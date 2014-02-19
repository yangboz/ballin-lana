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
	//
	$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';
	$dbh -> beginTransaction();
	$sth = $dbh -> prepare('SELECT * FROM vica_facetracking');
	$sth -> execute();
	//for JSON output
	$result = $sth -> fetchAll();
	//printf(count($result));
	$count = count($result);
	//customize results assembling.
	$filename = 'data/simres.txt';
	$file = fopen($filename, "w");
	for ($i = 0; $i < $count; $i++) {
		$content='';
		$content=$result[$i]["personID"]."\t";
		
		$sim_count = rand(3, 10);	
		$sim = 1;
		for ($j=0; $j < $sim_count; $j++) {
			 
			$id = rand(1,$count);
			$sim-=0.05;
			if( $j==$sim_count-1)
			{
				$content=$content.$id."\t".$sim;
			}
			else
			{
				$content=$content.$id."\t".$sim."\t";
			}
				
			
		}
		$content=$content."\n";
		echo $content."</br>";
		fwrite($file, $content);
	}
	fclose($file);
	//Return vica_facetracking results.
	//
	$dsn = null;
} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}
echo "ok";
?>