<?php
session_start();

// $backvalue = '';
$backdata = array();
if(!isset($_SESSION['user_id'])){
 	// echo json_encode('true');
	$backdata['state'] = 'false';
	// exit();
}else{
	// echo json_encode('false');
	$backdata['state'] = 'true';
	$backdata['username'] = $_SESSION['username'];
	// exit();
}
echo json_encode($backdata);

?>