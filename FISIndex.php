<?php
include("settings.php");
header("Access-Control-Allow-Origin: *");
	$dir_path = 'data\\FaceIndexingAndSearchImg\\';
	$res = array();
	
	$count = 50;		//控制首页随机显示图片的张数
	
	$dir_info = dir($dir_path);
	// print_r($dir_info);
	$dir_array=array();
	$idx=0;
	 while (false !== ($entry = $dir_info->read())) {
   // echo $entry."\n";
		if($entry=='.'||$entry=='..') continue;
		$dir_array[$idx++] = $dir_path.$entry;
	}
	 // print_r($dir_array);
	 
	 for($i=0;$i<$count;++$i)
	 {
	 	$num = rand(0, $idx-1);
		$res[$i]['face_path']=$dir_array[$num];
	 }
	 
	 echo json_encode($res);
	 
?>