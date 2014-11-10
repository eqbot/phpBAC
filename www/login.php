<h1>Login</h1>
<?php
	require('../wwwincludes/common.php');
	$username_cache = '';
	if(!empty($_POST)){
		$query = "
			SELECT id, username, password, salt, email
			FROM users
			WHERE username = :username
			";
		$query_params = array(
			':username' => $_POST['username']
		);
		//Execute
		try{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		}
		catch(PDOException $ex){
			die('Could not run query : ' . $ex->getMessage());
		}
		$login_ok = false;
		$row = $stmt->fetch();
		if($row){
			$passwordencrypted = $row['password'];
			echo $passwordencrypted;
			echo '<br />';
			$passworddata = mcrypt_decrypt(MCRYPT_RIJNDAEL_256,hash('md5', $_POST['password']),$passwordencrypted,'cfb',$row['salt']);
			echo htmlentities($passworddata, ENT_QUOTES, 'UTF-8');
			echo '<br />';
			$pointers = substr($passworddata,0,($ballpasses*$pointerbytes));
			echo $pointers;
			echo '<br />';
			$datahash = substr($passworddata,($ballpasses*$pointerbytes));
			echo $datahash;
			echo '<br />';
			$datas = '';
			for($i = 1; $i<=$ballpasses;$i++){
				echo 'Pointer ';
				echo $i;
				echo '<br />';
				$pointerraw = substr($pointers,($i-1)*$pointerbytes,$pointerbytes);
				echo $pointerraw;
				echo '<br />';
				$pointerhex = rtrim(implode(unpack('H*',$pointerraw)), '0');
				echo $pointerhex;
				echo '<br />';
				$ptrdec = base_convert($pointerhex,16,10);
				echo $ptrdec;
				echo '<br />';
				$data = ballLookup($ptrdec);
				$datas .= $data;
				echo 'Corresponds to data : ';
				echo $data;
				echo '<br />';
			}
			echo $datas;
			echo '<br />';
			echo hash('md5',$datas);
			echo '<br />';
			if(hash('md5',$datas, true) === $datahash){
				//Data gotten from decrypted pointers == hash from decryption
				$login_ok = true;
			}
		}
		if($login_ok){
			$_SESSION['user'] = $row;
			header("Location: private.php");
			die("Redirect");
		}
		else{
			echo "Login Failed. <br />";
			$username_cache = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
		}
	}
?>
<form action='login.php' method='post'>
	Username:<br />
	<input type='text' name='username' value="<?php echo $username_cache; ?>" /> <br />
	<br />
	Password:<br />
	<input type="password" name='password' value='' /> <br />
	<br />
	<input type='submit' value='Login' />
</form>
<a href='register.php'>Register</a>
