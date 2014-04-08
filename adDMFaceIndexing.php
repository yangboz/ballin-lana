<?php
// //TimeZone setting.
// date_default_timezone_set('UTC');
// // Connect to an ODBC database using driver invocation
// $dsn = 'sqlite:Uploads/vica_dev.db';
// $user = null;
// $password = null;
include("settings.php");
 header("Access-Control-Allow-Origin: *");
//
try {
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';
	$dbh -> beginTransaction();
	$sth = $dbh -> prepare('SELECT * FROM vica_cntv_facetracking');
	$sth -> execute();
	//for JSON output
	$resultsArr = array();
	$result = $sth -> fetchAll();
	//printf(count($result));
	$count = count($result);
	//customize results assembling.
	$tmp = array();
	$personID = array();
	$start_frame = array();
	$end_frame = array();
	$idx = 0;
	// $thres = 0.60;
	for ($i = 0; $i < $count; $i++) {
		// print_r($result[$i]);
		// $tmp[$i] = $result[$i]["personID"];
		if(in_array($result[$i]["personID"], $tmp))
		{
			$personID[$result[$i]["personID"]]=$personID[$result[$i]["personID"]]+1;
			$start_frame[$result[$i]["personID"]].='_'.$result[$i]["start_frame"];
			$end_frame[$result[$i]["personID"]].='_'.$result[$i]["end_frame"];
			// $resultsArr[$idx]["start_frame"].'_'.$result[$i]["start_frame"];
			// $resultsArr[$idx]["end_frame"].'_'.$result[$i]["end_frame"];
			// echo $idx."</br>";
			continue;
		}
		else {
			$resultsArr[$idx]["idvica_facetracking"] = $result[$i]["idvica_facetracking"];
			$resultsArr[$idx]["personID"] = $result[$i]["personID"];
			// $resultsArr[$idx]["start_frame"] = $result[$i]["start_frame"];
			// $resultsArr[$idx]["end_frame"] = $result[$i]["end_frame"];
			$resultsArr[$idx]["face_path"] = $result[$i]["face_path"];
			$tmp[$i] = $result[$i]["personID"];
			$personID[$result[$i]["personID"]]=1;
			$start_frame[$result[$i]["personID"]]=$result[$i]["start_frame"];
			$end_frame[$result[$i]["personID"]]=$result[$i]["end_frame"];
			$idx++;
		}
		
		
	}
	
	// print_r($start_frame);
	
	$res = array();
	for ($i=0; $i < $idx; $i++) { 
		$res[$i]['idvica_facetracking'] = $resultsArr[$i]['idvica_facetracking'];
		$res[$i]['personID'] = $resultsArr[$i]['personID'];
		// $res[$i]['start_frame'] = $resultsArr[$i]['start_frame'];
		
		// $res[$i]['end_frame'] = $resultsArr[$i]['end_frame'];
		$res[$i]['start_frame'] = $start_frame[$res[$i]['personID']];
		$res[$i]['end_frame'] = $end_frame[$res[$i]['personID']];
		$res[$i]['face_path'] = $resultsArr[$i]['face_path'];
		$res[$i]['times'] = $personID[$res[$i]['personID']];
	}
	//Return vica_facetracking results.
	echo json_encode($res);
	// echo "</br>";
	// print_r($personID);
	//
	$dsn = null;
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}
?>