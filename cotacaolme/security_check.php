<?php
	// security check
	session_start(); 
	if(!isset($_SESSION["upwd"])) {  $_SESSION["upwd"]=""; }
	if(!isset($GLOBALS["upwd"])) {  $GLOBALS["upwd"]="yvy2018"; }
	if(isset($_POST["upwd"])) {
		$_SESSION["upwd"]=$_POST["upwd"];
	}
	if( $_SESSION["upwd"]!=$GLOBALS["upwd"]) { 
		header("location:login.php");
		die();
	} else {
		// logged on - start page
	}
?>
