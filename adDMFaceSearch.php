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

	$sim_file = 'data/adDMSimRes.txt';
	$file = fopen($sim_file, "r");
	$resultsArr = array();
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   	$dbh -> beginTransaction();
	$idx = 0;
	while (!feof($file)) {
	   $line = fgets($file);
		if($line=="") continue;
	   $line = str_replace("\n", "", $line);
	   $arr = explode("\t", $line);
	   $idvica_facetracking = $arr[0];
	    
	   $sql = 'SELECT * FROM vica_cntv_facetracking WHERE idvica_facetracking='.$idvica_facetracking.';';
	
	   $sth = $dbh -> prepare($sql);
	   $sth -> execute();
	   $result = $sth -> fetchAll();
	   $resultsArr[$idx]['idvica_facetracking'] = $result[0]["idvica_facetracking"];
	   $resultsArr[$idx]["personID"] = $result[0]["personID"];
	   $resultsArr[$idx]["start_frame"] = $result[0]["start_frame"];
	   $resultsArr[$idx]["end_frame"] = $result[0]["end_frame"];
	   $resultsArr[$idx]["face_path"] = $result[0]["face_path"];
	   
	   $cou = count($arr);
	   $data = array();
	   $index = 0;
	   for ($i=1; $i < $cou; $i+=2) { 
		   
		   $idvica_facetracking = $arr[$i];
		   $sql = 'SELECT * FROM vica_cntv_facetracking WHERE idvica_facetracking='.$idvica_facetracking.';';
		   $sth = $dbh -> prepare($sql);
		   $sth -> execute();
		   $result = $sth -> fetchAll();
		   $data[$index] = array('idvica_facetracking' => $result[0]["idvica_facetracking"],
		   'personID' => $result[0]["personID"],'start_frame' => $result[0]["start_frame"],
		   'end_frame' => $result[0]["end_frame"],'face_path' => $result[0]["face_path"],
		   'sim' => $arr[$i+1]);
		   $index+=1;
	   }
	   $resultsArr[$idx]['data'] = $data;
	  
	 	$idx+=1;	
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