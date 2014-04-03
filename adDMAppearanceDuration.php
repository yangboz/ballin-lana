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
	// $personid = '0000';
	// $personid = '0004';
	// $personid = '0007';
	// $personid = '0008';
	$personid = '0009';
	// $personid = '0006';
	$sql = "SELECT * FROM vica_cntv_facetracking WHERE personID='".$personid."'";
	// echo $sql;
	$sth = $dbh -> prepare($sql);
	$sth -> execute();
	//for JSON output
	$resultsArr = array();
	$result = $sth -> fetchAll();
	
	// print_r($result);
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
		$idvica_facetracking[$key] = $row['idvica_facetracking'];
	}
	
}catch(PDOException $ex){
	
	echo 'Connection failed: ' . $ex -> getMessage();

	$dsn = null;
	
}

$fps=25;
$count=count($start_frame);
$apperanceDuration=array();
$start_time=array();
for($i=0;$i<$count;$i++)
{
	$duration=ceil(($end_frame[$i]-$start_frame[$i])/$fps);
	$apperanceDuration[$i]=$duration;
	$start_time[$i]=ceil($start_frame[$i]/$fps);
}

$tsv_file = 'data/adDMAppearanceDuration9.tsv';
$file = fopen($tsv_file,"w");
$title_line = "idvica_facetracking	personID	apperanceDuration	start_time	start_frame	end_frame";
// $title_line = "letter	frequency";
$content = $title_line."\n";

for($i=0;$i<$count;$i++)
{
	$tmp = $idvica_facetracking[$i]."\t".$personID[$i]."\t".$apperanceDuration[$i]."\t".date('H:i:s',$start_time[$i])."\t".$start_frame[$i]."\t".$end_frame[$i];
	// $tmp = $personID[$i]."\t".$apperanceDuration[$i];
	
	$content=$content.$tmp."\n";
}
echo $content."</br>";
// print_r($personID);
// $json_string = json_encode($res);
// 
// echo "getData($json_string)";
fwrite($file, $content);
fclose($file);
echo "ok";

?>