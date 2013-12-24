<?php
// //TimeZone setting.
// date_default_timezone_set('UTC');
// // Connect to an ODBC database using driver invocation
// $dsn = 'sqlite:Uploads/vica_dev.db';
// $user = null;
// $password = null;
include("settings.php");
//
try {
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';
	$dbh -> beginTransaction();
	$sth = $dbh -> prepare('SELECT idvica_facetracking,x,y,personID,width,height,appear_time FROM vica_facetracking');
	$sth -> execute();
	//for JSON output
	$resultsArr = array();
	$result = $sth -> fetchAll();
	//printf(count($result));
	$count = count($result);
	//customize results assembling.
	for ($i = 0; $i < $count; $i++) {
		// print_r($result[$i]);
		$resultsArr[$i]["appear_time"] = $result[$i]["appear_time"];
		$resultsArr[$i]["height"] = $result[$i]["height"];
		$resultsArr[$i]["idvica_facetracking"] = $result[$i]["idvica_facetracking"];
		$resultsArr[$i]["personID"] = $result[$i]["personID"];
		$resultsArr[$i]["width"] = $result[$i]["width"];
		$resultsArr[$i]["x"] = $result[$i]["x"];
		$resultsArr[$i]["y"] = $result[$i]["y"];
	}
	//Return vica_facetracking results.
	echo json_encode($resultsArr);
	//
	$dsn = null;
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}
?>