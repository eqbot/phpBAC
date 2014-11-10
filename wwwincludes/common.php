<?php
	//Ball config
	//pointer size, bytes
	Global $ballpath;
	$ballpath = '/var/wwwincludes/ball';
	$pointerbytes = strlen(pack('H*',base_convert(filesize($ballpath),10,16)));
	Global $ballpasses;
	$ballpasses = 10;
	

	//ball functions
	function ballLookup($pointer){
		global $ballpath;
		$ball = fopen($ballpath, 'r');
		fseek($ball, $pointer);
		$data = fread($ball, 1);
		//Be kind, please rewind!
		rewind($ball);
		return $data;
	}
	function genPasswordRep($password){
		global $ballpath;
		global $ballpasses;
		$salt = mcrypt_create_iv(mcrypt_enc_get_iv_size(mcrypt_module_open(MCRYPT_RIJNDAEL_256,"",'cfb',"")));
                $balldatas = '';
		$ballptrs = '';
                for($i = 1; $i <= $ballpasses; $i++){
                	$ptrdec = mt_rand(0,(filesize($ballpath)-1));
                        $ptrhex = rtrim(base_convert($ptrdec, 10, 16), '0');
                        $ptrascii = pack('H*', $ptrhex);
                        $ballptrs .= $ptrascii;
                        $data = ballLookup($ptrdec);
                        $balldatas .= $data;
		}
		echo $ballptrs;
		$balldatahash = hash('md5', $balldatas, true);
		$ciphertext = $ballptrs . $balldatahash;
		$key = hash('md5',$password);
		$passwordrep = rtrim(mcrypt_encrypt(MCRYPT_RIJNDAEL_256,$key,$ciphertext,'cfb',$salt));
		return array($salt,$passwordrep);
	}

	//SQL config
	$username = "ballchainuser";
	$password = "ben";
	$host = "localhost";
	$dbname = "ball&chain";
	Global $db;	

	//code stuff
	$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

	try
	{
		$db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);
	}
	catch(PDOException $except)
	{
		die("Failed to connect to the database: " . $except->getMessage());
	}
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	header('Content-Type: text/html; charset=utf-8');
	session_start();
	//functions requiring session/database
	function verifyLogin(){	
		global $db;
        	if(empty($_SESSION['user'])){
                	header('Location: login.php');
                	return false;
        	}
        	$query = "
	                SELECT username
        	        FROM users
                	WHERE username = :username
	                ";
        	$query_params = array(
                	':username' => $_SESSION['user']['username']
	                );
        	try{
	                $stmt = $db->prepare($query);
        	        $result = $stmt->execute($query_params);
	        }
        	catch(PDOException $ex){
                	return false;
	        }
        	$row = $stmt->fetch();
	        if(!$row){
        	        header('Location: login.php');
                	return false;
	        }
		return true;
	}

