<?php
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
	$sth = $dbh -> prepare('SELECT idvica_people,name,hasFeature,image,feature FROM vica_people');
	$sth -> execute();
	$sth->bindColumn(1, $id);  
	$sth->bindColumn(2, $name);  
	$sth->bindColumn(3, $hasFeature);  
	$sth->bindColumn(4, $image, PDO::PARAM_LOB);  
	//for JSON output
	$resultsArr = array();
	$resultStep = 0;
	//
	while($sth->fetch())  
	{  
		$resultsArr[$resultStep]['name'] = $name;
		file_put_contents($id.".jpg",$image);
		echo "$id,$name, <img src='".$id.".jpg'> <br/>";
		$resultStep++;
	} 
	//Return people results.
	echo json_encode($resultsArr);
	//
	$dsn = null;
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}
?>