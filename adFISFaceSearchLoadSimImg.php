<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	$postValue=$_POST['trans_data'];
     // echo $backValue."xhr";
     $dir_info = dir($postValue);
	 
	 $count = 5;
	 $res = array();
	 $idx = 0;
	 $postValue = str_replace("\\\\", "/", $postValue);
	 while(false !== ($entry=$dir_info->read()))
	 {
	 	if($idx == $count) break;
	 	if($entry=='.'||$entry=='..') continue;
		$img_path = $postValue.$entry;
		$res[$idx]['face_path'] = $img_path;
		$idx++;
	 }
     
	 echo json_encode($res);
?>