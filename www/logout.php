<?php
	require("../wwwincludes/common.php");
	unset($_SESSION['user']);
	header("Location: index.php");
	die("Redirect");

