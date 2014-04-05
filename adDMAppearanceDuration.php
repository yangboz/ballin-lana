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
	// $personid = '0003';
	$personid = '0004';
	// $personid = '0007';
	// $personid = '0008';
	// $personid = '0009';
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

$tsv_file = 'data/adDMAppearanceDuration44.tsv';
$file = fopen($tsv_file,"w");
$title_line = "idvica_facetracking	personID	apperanceDuration	start_time	start_frame	end_frame";
// $title_line = "letter	frequency";
$content = $title_line."\n";


$total_seconds = 226;
$interval_time = 20;
$interval = ceil($total_seconds/$interval_time);
$t_count = $interval+$count;
$s_second = 0;
$e_second = 20;
$e_second1 = 20;
$s_idx = 0;
$flag = array();
for($k=0;$k<$count;++$k)
{
	$flag[$k]=1;
}

$i=0;
while($i<$interval)
{
	
	
	for($j=0;$j<$count;++$j)
	{
		if($flag[$j]>0)
		{
			if(($s_second<=$start_time[$j])&&($start_time[$j]<=$e_second))
			{
				$tmp = $idvica_facetracking[$j]."\t".$personID[$j]."\t".$apperanceDuration[$j]."\t".date('H:i:s',$start_time[$j])."\t".$start_frame[$j]."\t".$end_frame[$j];
					
				$content=$content.$tmp."\n";
				$flag[$j]=0;
				$i++;
				$e_second1+=$interval_time;
				// echo $e_second1."</br>";
			}
		}
		
	}
	$tmp = $idvica_facetracking[0]."\t".$personID[0]."\t".'0'."\t".date('H:i:s',$e_second1)."\t".'0'."\t".'0';
				
	$content=$content.$tmp."\n";
	
	$s_second+=$interval_time;
	$e_second+=$interval_time;
	$e_second1+=$interval_time;
	$i++;
	// echo $i."</br>";
}



// echo $content."</br>";



fwrite($file, $content);
fclose($file);
echo "ok";

?>