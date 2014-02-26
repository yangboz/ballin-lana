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
	$sth = $dbh -> prepare('SELECT * FROM vica_cntv_facetracking');
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

$res=array();
$tmp_start_frame = "";
$tmp_end_frame = "";
// $sum=0;
for($i=0;$i<count($data);$i++)
{
	$tmp_start_frame = $tmp_start_frame.$data[$i]['start_frame']."_";
	$tmp_end_frame = $tmp_end_frame.$data[$i]['end_frame']."_";
	// $sum+=($data[$i]['end_frame']-$data[$i]['start_frame']);
	// echo $data[$i]['start_frame'].'_'.$data[$i]['end_frame']."</br>";
	// echo floor($data[$i]['start_frame']/25).'_'.ceil($data[$i]['end_frame']/25)."</br>";
}
$res[0]['start_frame'] = $tmp_start_frame;
$res[0]['end_frame'] = $tmp_end_frame;
// $time=floor($sum/25);

$json_string = json_encode($res);

echo $json_string;
// echo $time;
?>