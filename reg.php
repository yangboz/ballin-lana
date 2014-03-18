<?php
include("settings.php");
header("Access-Control-Allow-Origin: *");

if(!isset($_POST['submit'])){
	exit('Illegal Access!');
}
$username = $_POST['username'];
$password = $_POST['password'];
$password = MD5($password);

try {
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sth = $dbh -> prepare("select user_id from vica_user where username='$username' limit 1");
	$sth -> execute();
	$result = $sth -> fetchAll();
	if($result)
	{
		echo 'Username: ',$username,' has exist!<a href="javascript:history.back(-1);">back</a> Please use another username';
		exit;
		$dsn = null;
	}else{
		$insert = "INSERT INTO vica_user(username,password)VALUES('$username','$password')";
		$dbh->exec($insert);
		$dbh->lastInsertId();
		if($dbh->lastInsertId()){
			exit('User Register Success! Click to Login <a href="login.html">Login</a>');
			$dsn = null;
		} else {
		echo 'Add User Error!',mysql_error(),'<br />';
		echo 'Click <a href="javascript:history.back(-1);"> to return</a> and try again!';
		$dsn = null;
		}
	
	}
	
	
	
	
	
	
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}

?>
