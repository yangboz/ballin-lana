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
	arsort($end_frame);
	$max_end_frame=$end_frame[0];
	$dsn = null;
	
}catch(PDOException $ex){
	
	echo 'Connection failed: ' . $ex -> getMessage();

	$dsn = null;
	
}

//compute the axis ration and get the data
$fps=25;
$interval=10;
$time_span_second=floor($max_end_frame/$fps);
$time_span_minute=floor($time_span_second/60);
$people_count=count($resultsArr);
$time_interval=floor($time_span_minute/$interval);
$time_interval=$time_interval+1;
//echo "$time_interval"."</br>";
$labels=array();
$labels[0]=0;
for($i=1;$i<=$interval;$i++)
{
	$labels[$i]=$labels[$i-1]+$time_interval;
}

$data=array();
for($i=0;$i<=$interval;$i++)
{
	$data[$i]=0;
}
// asort($start_frame);
$idx=0;
for($k=0;$k<count($start_frame);$k++)
{
	$idx=$start_frame[$k]/($fps*60*$time_interval) + 1;
	$data[$idx]++;
}

$res=array();
for($i=0;$i<=$interval;$i++)
{
	$res[$i]['labels']=$labels[$i];
	$res[$i]['data']=$data[$i];
}

$json_string = json_encode($res);

echo "getData($json_string)";

?>