<?php
include("settings.php");
 header("Access-Control-Allow-Origin: *");
//
try {
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';
	$dbh -> beginTransaction();
	$sth = $dbh -> prepare('SELECT idvica_facetracking,personID,start_frame,end_frame FROM vica_facetracking');
	$sth -> execute();
	//for JSON output
	$resultsArr = array();
	$result = $sth -> fetchAll();
	//printf(count($result));
	$count = count($result);
	//customize results assembling.
	for ($i = 0; $i < $count; $i++) {
		// print_r($result[$i]);
		$resultsArr[$i]["idvica_facetracking"] = $result[$i]["idvica_facetracking"];
		$resultsArr[$i]["personID"] = $result[$i]["personID"];
		$resultsArr[$i]["start_frame"] = $result[$i]["start_frame"];
		$resultsArr[$i]["end_frame"] = $result[$i]["end_frame"];
	}
	//Return vica_facetracking results.
	// ksort($resultsArr);
	// print_r($resultsArr);
	// echo json_encode($resultsArr);
	//
	$dsn = null;
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}

$resultsArr = array_sort($resultsArr,'start_frame');

// print_r($resultsArr);
function array_sort($arr,$keys,$type='asc')
{

	$keysvalue = $new_array = array();
	foreach ($arr as $k=>$v)
	{
		$keysvalue[$k] = $v[$keys];
	}
	
	if($type == 'asc')
	{
		asort($keysvalue);
	
	}else
	{
		arsort($keysvalue);
	}
	reset($keysvalue);
	foreach ($keysvalue as $k=>$v)
	{
		$new_array[$k] = $arr[$k];
	}
	return $new_array;
}

$count=count($resultsArr);
$data=array();
$idx=0;
$data[$idx]['start_frame']=$resultsArr[0]['start_frame'];
$tmp_end_frame=$resultsArr[0]['end_frame'];
$data[$idx]['end_frame']=$tmp_end_frame;
$data[$idx]['num']=1;
$data[$idx]['synop']=$idx+1;
for($i=0;$i<$count-1;$i++)
{
	if($resultsArr[$i+1]['start_frame']<=$tmp_end_frame)
	{
		if ($tmp_end_frame<=$resultsArr[$i+1]['end_frame']) 
		{
			$tmp_end_frame=$resultsArr[$i+1]['end_frame'];
		}
		$data[$idx]['end_frame']=$tmp_end_frame;
		$data[$idx]['num']++;
	}
	else {
		$idx++;
		$data[$idx]['start_frame']=$resultsArr[$i+1]['start_frame'];
		$tmp_end_frame=$resultsArr[$i+1]['end_frame'];
		$data[$idx]['end_frame']=$tmp_end_frame;
		$data[$idx]['num']=1;
		$data[$idx]['synop']=$idx+1;
	}
	
}

// print_r($data);
$tsv_file = 'data/synopsis.tsv';
$file = fopen($tsv_file,"w");
$title_line = "synop	num	start_frame	end_frame";
// $title_line = "letter	frequency";
$content = $title_line."\n";

for($i=0;$i<count($data);$i++)
{
	$tmp = $data[$i]['synop']."\t".$data[$i]['num']."\t".$data[$i]['start_frame']."\t".$data[$i]['end_frame'];
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