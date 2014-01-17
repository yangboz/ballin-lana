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
	}
	rsort($end_frame);
	// print_r($end_frame);
	// echo "</br>";
	$max_end_frame=$end_frame[0];
	$dsn = null;
	
}catch(PDOException $ex){
	
	echo 'Connection failed: ' . $ex -> getMessage();

	$dsn = null;
	
}

$fps=25;
$interval_count=10;
$second_per_minute=60;
//15 seconds per interval
$time_span=30;
$labels=array();
$labels[0]=0;
for($i=1;$i<=$interval_count;$i++)
{
	$labels[$i]=$labels[$i-1]+$time_span/$second_per_minute;
}

$data=array();
for($i=0;$i<=$interval_count;$i++)
{
	$data[$i]=0;
}
$idx=0;
for($k=0;$k<count($start_frame);$k++)
{
	$idx=$start_frame[$k]/($fps*$time_span) + 1;
	$data[$idx]++;
}

$res=array();
for($i=0;$i<=$interval_count;$i++)
{
	$res[$i]['labels']=$labels[$i];
	$res[$i]['data']=$data[$i];
}

$json_string = json_encode($res);

echo "getData($json_string)";

?>