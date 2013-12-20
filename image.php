<?php
//@see:http://stackoverflow.com/questions/6282351/in-php-how-i-show-various-images-from-a-blob-field-in-the-database-with-the-htm
//TimeZone setting.
date_default_timezone_set('UTC');
// Connect to an ODBC database using driver invocation
$dsn = 'sqlite:Uploads/vica_dev.db';
$user = null;
$password = null;
//
try {
	//
	$dbh = new PDO($dsn, $user, $password);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';

	$dbh -> beginTransaction();

	$stmt = $dbh -> prepare('SELECT idvica_people,name,hasFeature,image,feature FROM vica_people WHERE idvica_people='.$_GET['pid']);

	$stmt -> execute();
	$stmt->bindColumn(4, $lob, PDO_PARAM_LOB);
	$stmt->fetch(PDO_FETCH_BOUND);
	echo $lob;
	//
	$dsn = null;

} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}
?>