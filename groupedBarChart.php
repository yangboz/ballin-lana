<?php
/*
The solution minute detailed in detail by the side of PHP server as following:
#1. getPersonIDs();// Query DB(SQLite database), get all of unique personIDs as array;
#2. getStartTimeMin();// Query DB, get the minimum start time of any personID;
#3. getEndTimeMax();//  Query DB, get the maximum end time of any personID;
#4. getGapPoints($time_gap_get,$min_start_time,$max_end_time,$resultsArr_personIDs);// Get the gap points dataset, take the startTimeMin,endTimeMax,personIDs as parameters to computation;
#4.1. calculate the date period aka time interval, which comes from user�s URL input.
$interval = new DateInterval('PT' . $interval . 'M');
$period = new DatePeriod($startTime, $interval, $endTime);
#4.2. for each of time interval.
foreach ($period as $dt) { 
#4.3. for each of personID:
for ($i = 0; $i < $count_personIDs; $i++) {
#4.4.1 get each person appearance times
                                array_push($arr_total_personIDs_appearance_time,getPersonIDappearanceTimes($personIDs[$i]));
#4.4.2 get each person duration by the time diff calculation take the each personID appearance time and  time interval value(gapPoint)
                           $gapPoint[$personIDs[$i]]=getPersonIDduration($arr_total_personIDs_appearance_time[$i],$gapPoint['GapPoint']);
Backlog:
#1.DateTime type storage in SQLite database, currently we used the text string, where it comes from C++ code base;
#2.O(2) algorithm take space times huge, currently 35ms cost;
@see:http://15.185.109.31/vica_web/groupedBarChart.html
@author:zhou.yangbo@hp.com
*/
// //TimeZone setting.
// date_default_timezone_set('UTC');
// // Connect to an ODBC database using driver invocation
// define('DSN', 'sqlite:Uploads/vica_dev.db');
// define('USER_NAME', NULL);
// define('PASS_WORD', NULL);
include("settings.php");
 // header("Access-Control-Allow-Origin: *");
// $dsn = 'sqlite:Uploads/vica_dev.db';
// $user = null;
// $password = null;
$dbh = NULL;
//$_GET['']
$time_gap_get = null;
if (isset($_GET["gap"])) {
	$time_gap_get = $_GET["gap"];
	// echo $time_gap_get;
}
$resultsArr = array();
$resultsArr_personIDs = array();
$resultsArr_gapPoints = array();
$arr_gapPoints_min_max = array();
//
function getRandomDuration()
{
	$min=0;
  	$max=20;
  return rand($min,$max);
}
//
function getDurationInSeconds($timeDuration)
{
	$timeDurationInSec = (int)$timeDuration -> h * 3600 + (int)$timeDuration -> m * 60 + (int)$timeDuration -> s;
	return $timeDurationInSec;
}
//
function timeStringParse($value = '') {
	$timeArr = date_parse_from_format("d/m/Y-H:i:s", $value);
	$dateTime = new DateTime($timeArr['year'] . '-' . $timeArr['month'] . '-' . $timeArr['day'] . $timeArr['hour'] . ':' . $timeArr['minute'] . ':' . $timeArr['second']);
	return $dateTime;
}
//@see:http://www.sqlite.org/lang_datefunc.html
//select end_time
// from
// (
// SELECT end_time
// , substr(end_time,7,4)||'/'||substr(end_time,4,2)||'/'||substr(end_time,1,2)||'-'||substr(end_time,12) as new_end_time
// FROM vica_people_counting
// ) tmp
// order by tmp.new_end_time desc
// limit 1
function getEndTimeMax() {
	$max_end_time = null;
	//
	try {
		//
		$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
		$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo 'PDO Connection  OK!','';

		$dbh -> beginTransaction();

		$sth = $dbh -> prepare("SELECT end_time
			FROM 
			(
			SELECT end_time
			           , substr(end_time,7,4)||' / '||substr(end_time,4,2)||' / '||substr(end_time,1,2)||' - '||substr(end_time,12) as new_end_time
			 FROM vica_people_counting
			 ) tmp
			 ORDER BY tmp.new_end_time desc
			 LIMIT 1;");
		//Get all of personIDs avoid duplication;

		$sth -> execute();

		$result = $sth -> fetchAll();
		//printf(count($result));
		$count = count($result);
		//
		$min_start_time = null;
		$max_end_time = null;
		for ($i = 0; $i < $count; $i++) {
			// print_r($result[$i]);
			$max_end_time = $result[$i]['end_time'];
		}
		//
		$dsn = null;

	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e -> getMessage();
		$dsn = null;
	}
	return $max_end_time;
}

//
// select start_time
// from
// (
// SELECT start_time
// , substr(start_time,7,4)||'/'||substr(start_time,4,2)||'/'||substr(start_time,1,2)||'-'||substr(start_time,12) as new_start_time
// FROM vica_people_counting
// ) tmp
// order by tmp.new_start_time
// limit 1
function getStartTimeMin() {
	$min_start_time = null;
	//
	try {
		//
		$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
		$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo 'PDO Connection  OK!','';

		$dbh -> beginTransaction();

		$sth = $dbh -> prepare("SELECT start_time 
			FROM ( SELECT start_time, 
				substr(start_time,7,4)||' / '||substr(start_time,4,2)||' / '||substr(start_time,1,2)||' - '||substr(start_time,12) AS new_start_time 
				FROM vica_people_counting) tmp 
				ORDER BY tmp.new_start_time 
				LIMIT 1;");
		//Get all of personIDs avoid duplication;

		$sth -> execute();

		$result = $sth -> fetchAll();
		//printf(count($result));
		$count = count($result);
		//
		for ($i = 0; $i < $count; $i++) {
			// print_r($result[$i]);
			$min_start_time = $result[$i]['start_time'];
		}
		//
		$dsn = null;

	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e -> getMessage();
		$dsn = null;
	}
	return $min_start_time;
}
//
function getPersonIDappearanceTimes($personID)
{
	$appearanceTime = array();
	//
	try {
		//
		$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
		$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo 'PDO Connection  OK!','';

		$dbh -> beginTransaction();

		$sth = $dbh -> prepare("SELECT personID,start_time,end_time FROM vica_people_counting WHERE personID=".$personID.";");
		//Get all of personIDs avoid duplication;

		$sth -> execute();

		$result = $sth -> fetchAll();
		//printf(count($result));
		$count = count($result);
		//
		for ($i = 0; $i < $count; $i++) {
			// print_r($result[$i]);
			array_push($appearanceTime,array($result[$i]['start_time'],$result[$i]['end_time']));
		}
		// print_r($appearanceTime);
		//
		$dsn = null;

	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e -> getMessage();
		$dsn = null;
	}
	//
	return $appearanceTime;
}

//
function getPersonIDduration($personIDappearanceTimes,$gapPoint)
{
	$data_time_gapPoint = timeStringParse($gapPoint);
	// print_r($data_time_gapPoint);
	$duration = 0;//in seconds
	//
	$count = count($personIDappearanceTimes);
	// print_r($totalPersonIDappearanceTimes[0]);
	for ($i = 0; $i < $count; $i++) {
		$person_start_time = timeStringParse($personIDappearanceTimes[$i][0]);
		// print_r($person_start_time);
		$person_end_time = timeStringParse($personIDappearanceTimes[$i][1]);
		// print_r($person_end_time);
		if($person_start_time < $data_time_gapPoint)
		{
			if($person_end_time < $data_time_gapPoint)
			{
				$duration += getDurationInSeconds($person_start_time -> diff($person_end_time));
			}else
			{
				$duration += getDurationInSeconds($person_start_time -> diff($data_time_gapPoint));
			}
		}
	}
	//
	// echo "duration:".$duration;
	return $duration;
}

//SELECT max(end_time) as MAX_end_time,min(start_time) as MIN_start_time FROM vica_people_counting;
function getGapPoints($interval,$min_start_time,$max_end_time,$personIDs) {
	$arr_gapPoints = array();
//print_r($arr_gapPoints_min_max);
	//Secondly,Time duration calculate.
	//@see: http://www.php.net/manual/en/function.date-parse-from-format.php
	$startTime = timeStringParse($min_start_time);
	//print_r($startTime);
	$endTime = timeStringParse($max_end_time);
	//print_r($endTime);
	//
	$timeDuration = $startTime -> diff($endTime);
	//print_r($timeDuration);
	$timeDurationInSec = (int)$timeDuration -> d * 86400 + (int)$timeDuration -> h * 3600 + (int)$timeDuration -> m * 60 + (int)$timeDuration -> s;
	//print_r($timeDurationInSec);
	if(!($interval)) $interval = 20000;
	$interval = new DateInterval('PT' . $interval . 'M');
	//print_r($interval);
	$period = new DatePeriod($startTime, $interval, $endTime);
	//
	$arr_total_personIDs_appearance_time = array();
	// print_r($period);
	foreach ($period as $dt) {
		// do something
		$gapPoint = array('GapPoint'=>$dt -> format('d/m/Y-H:i'));
		//print_r($gapPoint);
		$count_personIDs = count($personIDs);
		// print_r($count_personIDs);
		//
		for ($i = 0; $i < $count_personIDs; $i++) {
			//Get total personIDs' appearance time.
			array_push($arr_total_personIDs_appearance_time,getPersonIDappearanceTimes($personIDs[$i]));
			//$gapPoint[$personIDs[$i]]=getRandomDuration();//with real date time comparation;
			$gapPoint[$personIDs[$i]]=getPersonIDduration($arr_total_personIDs_appearance_time[$i],$gapPoint['GapPoint']);
		}
		// print_r($gapPoint);
		array_push($arr_gapPoints, $gapPoint);
	}
	//print_r($personIDs[0]);
	//print_r($arr_total_personIDs_appearance_time);
	return $arr_gapPoints;
}

//
function getPersonIDs() {
	$arr_personIDs = array();
	//
	try {
		//
		$dbh = new PDO(DSN, USER_NAME, PASS_WORD);
		$dbh -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		//echo 'PDO Connection  OK!','';

		$dbh -> beginTransaction();

		$sth = $dbh -> prepare('SELECT personID FROM vica_people_counting GROUP BY personID;');
		//Get all of personIDs avoid duplication;

		$sth -> execute();

		$result = $sth -> fetchAll();
		//printf(count($result));
		$count = count($result);
		$arr_personIDs = array();
		for ($i = 0; $i < $count; $i++) {
			array_push($arr_personIDs, $result[$i]['personID']);
		}
		//Echo JSON results
		// $resultsArr = array($resultsArr_personIDs, $resultsArr_gapPoints);
		// echo json_encode($resultsArr);
		$dsn = null;
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e -> getMessage();
		$dsn = null;
	}
	return $arr_personIDs;
}

//Subroutine
$resultsArr_personIDs = getPersonIDs();
//min/max time
$min_start_time = getStartTimeMin();
$max_end_time = getEndTimeMax();
array_push($arr_gapPoints_min_max, $min_start_time);
array_push($arr_gapPoints_min_max, $max_end_time);
//print_r($arr_gapPoints_min_max);
$resultsArr_gapPoints = getGapPoints($time_gap_get,$min_start_time,$max_end_time,$resultsArr_personIDs);
// print_r($resultsArr_gapPoints);
//Echo JSON results
echo json_encode($resultsArr_gapPoints);
?>