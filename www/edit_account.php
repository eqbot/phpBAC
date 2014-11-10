<h1>Edit account information</h1>
<?php
	require('../wwwincludes/common.php');
	if(!verifyLogin()){
		header('Location: login.php');
		die('Not logged in.');
	}
	if(!empty($_POST)){
		$change = true;
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			echo 'Invalid Email. <br />';
			$change = false;
		}
		if($_POST['email'] != $_SESSION['user']['email']){
			$query = "
				SELECT 1
				FROM users
				WHERE email = :email
				";
			$query_params = array(
				':email' => $_POST['email']
			);
			try{
				$stmt = $db->prepare($query);
				$result = $stmt->execute($query_params);
			}
			catch(PDOException $ex){
				die("Failed to execute statment : " . $ex->getMessage());
			}
			$row = $stmt->fetch();
			if($row){
				echo "E-Mail already in use! <br />";
				$change = false;
			}
		}
		if(!empty($_POST['password'])){
			list($salt,$password) = genPasswordRep($_POST['password']);
		}
		else{
			$password = null;
			$salt = null;
		}
		$query_params = array(
			':email' => $_POST['email'],
			':user_id' => $_SESSION['user']['id']
		);
		if($password !== null){
			$query_params[':password'] = $password;
			$query_params[':salt'] = $salt;
		}
		$query = "
			UPDATE users
			SET email = :email
			";
			if($password !== null){
				$query .= "
					, password = :password
					, salt = :salt
					";
			}
			$query .= "
				WHERE id = :user_id
				";
			try{
				$stmt = $db->prepare($query);
				$result = $stmt->execute($query_params);
			}
			catch(PDOException $ex){
				die("Failed to run query: " . $ex->getMessage());
			}
			//Logout user for security.
			header("Location: logout.php");
			die("Redirect");
	}
?>
<form action="edit_account.php" method="post">
	Username:<br />
	<b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b><br />
	<br />
	EMail Address:<br />
	<input type="text" name="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" /><br />
	<br />
	<b>WARNING : THIS LIKES TO BREAK ACCOUNTS. I'M WORKING ON FIXING IT.</b>
	Password: <br />
	<input type="password" name="password" value="" /><br />
	<i>(Leave blank if you do not want to change your password)</i><br />
	<br />
	<input type="submit" value="Update Account" />
</form>

