<?php
	//Might be vulnerable to session hijacking.
	require('../wwwincludes/common.php');
	if(!verifyLogin()){
		header("Location: login.php");
		die("Not logged in.");
	}
//finished validating authorization
?>
<body background="http://rlv.zcache.com/upset_cloud_key_chain-r27102ec530714e54b0034aa17a23cd5f_x7j3z_8byvr_324.jpg">
Hello <?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?>, and welcome to the secret content!<br />
<a href="memberlist.php">Memberlist</a><br />
<a href="edit_account.php">Edit account</a><br />
<a href="logout.php">Logout</a>
</body>
