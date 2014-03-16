<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	$postValue=$_POST['trans_data'];
	$postValue1=$_POST['trans_data1'];
     $dir_info = dir($postValue);
	 
	 $count = 50;
	 $res = array();
	 $idx = 0;
	 
 	$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	switch ($os) {
		case 'windows':
			$postValue1 = str_replace("\\\\", "/", $postValue1);
			break;
		
		case 'linux':
			break;
		default:
			
			break;
	}
	 
	 
	 while(false !== ($entry=$dir_info->read()))
	 {
	 	if($idx == $count) break;
	 	if($entry=='.'||$entry=='..') continue;
		$img_path = $postValue1.$entry;
		$res[$idx]['face_path'] = $img_path;
		$idx++;
	 }
     
	 echo json_encode($res);
?>