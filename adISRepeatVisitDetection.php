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
	$sth = $dbh -> prepare('SELECT * FROM vica_facerevisittracking');
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
		$resultsArr[$i]["idvica_facerevisittracking"] = $result[$i]["idvica_facerevisittracking"];
		$resultsArr[$i]["personID"] = $result[$i]["personID"];
		$resultsArr[$i]["face_path"] = $result[$i]["face_path"];
	}
	
	// foreach ($resultsArr as $key => $row) {
		// $personID[$key] = $row['personID'];
	// }
	
}catch(PDOException $ex){
	
	echo 'Connection failed: ' . $ex -> getMessage();

	$dsn = null;
	
}

$count=count($resultsArr);
$data=array();
for($i=0;$i<$count;$i++)
{
	$k = $resultsArr[$i]['personID'];
	if(isset($data[$k]['s_e_frame']))
	{
		$data[$k]['s_e_frame'] = $data[$k]['s_e_frame']."_".($resultsArr[$i]['start_frame']."_".$resultsArr[$i]['end_frame']);
	}
	else 
	{
		$data[$k]['s_e_frame'] = $resultsArr[$i]['start_frame']."_".$resultsArr[$i]['end_frame'];
		$data[$k]['face_path'] = $resultsArr[$i]['face_path'];
	}
	
	if(isset($data[$k]['num']))
	{
		$data[$k]['num']++; 
	}
	else {
		$data[$k]['num']=1;
	}
	
}

// print_r($data);
// echo "</br>";

$tsv_file = 'data/repeatVisitDetection.tsv';
$file = fopen($tsv_file,"w");
$title_line = "personID	start_end_frame	face_path	num";
// $title_line = "letter	frequency";
$content = $title_line."\n";

for($i=1;$i<=count($data);$i++)
{
	
	$tmp = $i."\t".$data[$i]['s_e_frame']."\t".$data[$i]['face_path']."\t".$data[$i]['num'];
	// $tmp = $personID[$i]."\t".$apperanceDuration[$i];
	
	$content=$content.$tmp."\n";
}
echo $content."</br>";
// $json_string = json_encode($res);
// 
// echo "getData($json_string)";
fwrite($file, $content);
fclose($file);
echo "ok";

?>


<!-- $cou=max($personID);
$count=count($resultsArr);
$data=array();
for($i=1;$i<=$cou;$i++)
{
	$data[$i]['s_e_frame']="";
	// $data[$i]['face_path']="";
	$data[$i]['num']=0;
}
for($i=0;$i<$count;$i++)
{
	$k = $resultsArr[$i]['personID'];
	$data[$k]['s_e_frame'] = $data[$k]['s_e_frame']."_".($resultsArr[$i]['start_frame']."_".$resultsArr[$i]['end_frame']);
	$data[$k]['num']++; 
	if(!isset($data[$k]['face_path']))
	{
		$data[$k]['face_path'] = $resultsArr[$i]['face_path'];	
	}
} -->
