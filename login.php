<?php
include("settings.php");
header("Access-Control-Allow-Origin: *");
error_reporting(0);
session_start();

if($_GET['action'] == "logout"){
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	echo 'Logout success! Click <a href="login.html">Login</a>';
	// header("Location:login.html");
	exit;
}

if(!isset($_POST['submit'])){
	exit('Illegal Access!');
}
$username = htmlspecialchars($_POST['username']);
$password = MD5($_POST['password']);

try {
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$dbh -> beginTransaction();
	$sth = $dbh -> prepare("select user_id from vica_user where username='$username' and password='$password' limit 1");
	$sth -> execute();
	$resultsArr = array();
	$result = $sth -> fetch();
	if($result){
		$_SESSION['username'] = $username;
		$_SESSION['user_id'] = $result['user_id'];
		$dsn = null;
		header("Location:index.html");
		exit();
		
	} else {
		exit('Login failed <a href="javascript:history.back(-1);">Return</a>Try again!');
		$dsn = null;
	}
	
	
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}

?>