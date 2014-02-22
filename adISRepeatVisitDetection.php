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
	
	if(!isset($data[$k]['personID']))
	{
		$data[$k]['personID'] = $k;
	}
}

$tsv_file = 'data/repeatVisitDetection.tsv';
$file = fopen($tsv_file,"w");
$title_line = "personID	start_end_frame	face_path	num";
// $title_line = "letter	frequency";
$content = $title_line."\n";
// print_r($data);
foreach ($data as $key => $row) {
	
	$tmp = $data[$key]['personID']."\t".$data[$key]['s_e_frame']."\t".$data[$key]['face_path']."\t".$data[$key]['num'];
	// echo $data[$key]['personID']."--".$data[$key]['s_e_frame']."--".$data[$key]['face_path']."--".$data[$k]['num']."</br>";
	$content=$content.$tmp."\n";
}

echo $content."</br>";
fwrite($file, $content);
fclose($file);
echo "ok";

?>

