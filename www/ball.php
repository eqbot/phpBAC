<h1>Password representation lookup</h1>
<?php
	require('../wwwincludes/common.php');
	if(!empty($_POST))
	{
		$query = "SELECT password from users where username = :username";
		$query_params = array(':username' => $_POST['username']);
		try
		{
			$stmt = $db->prepare($query);
			$result = $stmt->execute($query_params);
		}
		catch(PDOException $ex)
		{
			die("Something went wrong.");
		}
		$row = $stmt->fetch();
		echo $row['password'];
	}
?>
<form action='ball.php' method="post">
	Username : <br />
	<input type="text" name="username" value="" />
</form>
