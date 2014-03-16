<?php
include("settings.php");
header("Access-Control-Allow-Origin: *");

$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
$dir_path = '';
$dir_info;
switch ($os) {
	case 'windows':
		$dir_path = 'data\\FaceIndexingAndSearchImg\\';
		// echo $dir_path;
		$dir_info = dir($dir_path);
		break;
	
	case 'linux':
		$dir_path = '/var/www/html/vica_web/data/FaceIndexingAndSearchImg/';
		// echo $dir_path;
		$dir_info = dir($dir_path);
		$dir_path = 'http://15.125.94.250/vica_web/data/FaceIndexingAndSearchImg/';
		break;
	default:
		
		break;
}
	
	$res = array();
	
	$count = 30;		//控制首页随机显示图片的张数
	
	
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
	 
	 // print_r($res);
	 echo json_encode($res);
	 
?>