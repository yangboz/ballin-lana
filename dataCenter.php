<?php
//TimeZone setting.
date_default_timezone_set('UTC');
// Connect to an ODBC database using driver invocation
$dsn = 'sqlite:Uploads/vica_dev.db';
$user = null;
$password = null;
//
function timeStringParse($value = '') {
	$timeArr = date_parse_from_format("d/m/Y-H:i:s", $value);
	$dateTime = new DateTime($timeArr['year'] . '-' . $timeArr['month'] . '-' . $timeArr['day'] . $timeArr['hour'] . ':' . $timeArr['minute'] . ':' . $timeArr['second']);
	return $dateTime;
}
//$_GET['']
$start_get = null;
$end_get = null;
$people_counter = 0;
if (isset($_GET["start"]) && isset($_GET["end"])) {
	$start_get = timeStringParse($_GET["start"]);
	$end_get = timeStringParse($_GET["end"]);
	// echo $_GET["start"].",".$_GET["end"];
}
try {
	//
	$dbh = new PDO($dsn, $user, $password);
 	$dbh ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//echo 'PDO Connection  OK!','';

	$dbh -> beginTransaction();

	$sth = $dbh -> prepare('SELECT personID,start_time,end_time FROM vica_people_counting');

	$sth -> execute();

	$result = $sth -> fetchAll();
	// printf(count($result));
	$count = count($result);
	$resultsArr = array();
	for ($i = 0; $i < $count; $i++) {
		$resultsArr[$i]['personID'] = $result[$i]['personID'];
		$rawStart = $result[$i]['start_time'];
		$rawEnd = $result[$i]['end_time'];
		//Time duration calculate.
		//@see: http://www.php.net/manual/en/function.date-parse-from-format.php
		$startTime = timeStringParse($rawStart);
		$endTime = timeStringParse($rawEnd);
		//People counting here.
		if ($start_get && $end_get) {
			if ( ($startTime > $start_get) && ($endTime < $end_get)) {
				$people_counter++;
			}
		}
		//
		$timeDuration = $startTime -> diff($endTime);
		//print_r($timeDuration->y);
		$timeDurationInSec = (int)$timeDuration -> h * 3600 + (int)$timeDuration -> m * 60 + (int)$timeDuration -> s;
		// print_r($timeDurationInSec);
		$resultsArr[$i]['start_time'] = $rawStart;
		$resultsArr[$i]['end_time'] = $rawEnd;
		$resultsArr[$i]['duration'] = $timeDurationInSec;
	}
	//Return people counting results.
	if ($start_get && $end_get) {
		$peopel_count_result = array();
		$peopel_count_result['count'] = $people_counter;
		echo json_encode($peopel_count_result);
	} else {
		echo json_encode($resultsArr);
	}
	$dsn = null;

} catch (PDOException $e) {

	echo 'Connection failed: ' . $e -> getMessage();

	$dsn = null;

}
?>