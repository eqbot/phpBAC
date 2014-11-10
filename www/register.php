<h1>Register</h1>
<?php
	require("../wwwincludes/common.php");
	$noreg = false;
	if(!empty($_POST)){
		if(empty($_POST['username'])){
			echo "Please enter a username.";
			$noreg = true;
		}
		if(empty($_POST['password'])){
			echo "Please enter a password.";
			$noreg = true;
		}
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
			echo "Invalid E-mail address.";
			$noreg = true;
		}
		//Check uniqueness of username
		$query = "SELECT 1 FROM users WHERE username = :username";
		$query_params = array(
			':username' => $_POST['username']
		);
		try{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		}
		catch(PDOException $ex){
			die("Failed to run query: " . $ex->getMessage());
		}
		$row = $stmt->fetch();
		if($row){
			echo "Username already taken! So sad. ;_;";
			$noreg = true;
		}
		//Check uniqueness of email.
		$query = "SELECT 1 FROM users WHERE email = :email";
		$query_params = array(
			':email' => $_POST['username']
		);
		try{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		}
		catch(PDOException $ex){
			die("Failed to run query: " . $ex->getMessage());
		}
		$row = $stmt->fetch();
		if($row){
			echo "Someone's already registered with that email. Probably you.";
			$noreg = true;
		}
		if(!$noreg){
		$query = "
			INSERT INTO users (
				username,
				password,
				salt,
				email)
			VALUES(
			:username,
			:password,
			:salt,
			:email)";
			list($salt,$password) = genPasswordRep($_POST['password']);
			$query_params = array(
				':username' => $_POST['username'],
				':password' => $password,
				':salt' => $salt,
				':email' => $_POST['email']);
			try{
				$stmt = $db->prepare($query);
				$result = $stmt->execute($query_params);
			}
			catch(PDOException $ex)
			{
				die("Failed to run query: " . $ex->getMessage());
			}
			header("Location: login.php");
			die("Redirect");
	}
}
?>
<form action="register.php" method="post">
	Username: <br />
	<input type="text" name="username" value="" /> <br />
	<br />
	E-Mail: <br />
	<input type="text" name="email" value="" /> <br />
	<br />
	Password: <br />
	<input type="password" name="password" value="" /> <br />
	<br />
	<input type="submit" value="Register" />
</form>
