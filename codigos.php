<?php
	session_start();
	
	$_SESSION['codigos'] = $_POST['loscodigos'];
	echo $_SESSION['codigos'];
?>