<?php
include("settings.php");
header("Access-Control-Allow-Origin: *");
//read the database
try{
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';
	$dbh -> beginTransaction();
	$sth = $dbh -> prepare('SELECT * FROM vica_facetracking');
	$sth -> execute();
	//for JSON output
	$resultsArr = array();
	$result = $sth -> fetchAll();
	//printf(count($result));
	$count = count($result);
	//customize results assembling.
	for ($i = 0; $i < $count; $i++) {
		// print_r($result[$i]);
		$resultsArr[$i]["start_frame"] = $result[$i]["start_frame"];
		$resultsArr[$i]["end_frame"] = $result[$i]["end_frame"];
		$resultsArr[$i]["idvica_facetracking"] = $result[$i]["idvica_facetracking"];
		$resultsArr[$i]["personID"] = $result[$i]["personID"];
	}
	
	foreach ($resultsArr as $key => $row) {
		$start_frame[$key]=$row['start_frame'];
    	$end_frame[$key]  = $row['end_frame'];
		$personID[$key] = $row['personID'];
	}
	
}catch(PDOException $ex){
	
	echo 'Connection failed: ' . $ex -> getMessage();

	$dsn = null;
	
}

$fps=25;
$count=count($start_frame);
$labels=array();
for($i=0;$i<$count;$i++)
{
	$labels[$i]=$personID[$i];
}

$data=array();
for($i=0;$i<$count;$i++)
{
	$duration=ceil(($end_frame[$i]-$start_frame[$i])/$fps);
	$data[$i]=$duration;
}

$res=array();
$res[0]['labels']=0;
$res[0]['data']=0;
$res[0]['start_frame']=0;
$res[0]['end_frame']=0;
for($i=0;$i<$count;$i++)
{
	$res[$i+1]['labels']=$labels[$i];
	$res[$i+1]['data']=$data[$i];
	$res[$i+1]['start_frame']=$start_frame[$i];
	$res[$i+1]['end_frame']=$end_frame[$i];
}

$json_string = json_encode($res);

echo "getData($json_string)";

?>