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
			$origin_dir_path = 'data\\FaceIndexingAndSearchImg\\';
			$origin_dir_path = str_replace("\\\\", "/", $origin_dir_path);
			break;
		
		case 'linux':
			$origin_dir_path = 'http://15.125.94.250/vica_web/data/FaceIndexingAndSearchImg/';
			break;
		default:
			
			break;
	}
	 
	 
	 while(false !== ($entry=$dir_info->read()))
	 {
	 	if($idx == $count) break;
	 	if($entry=='.'||$entry=='..') continue;
		$origin_img_path = get_the_origin_path($entry);
		$img_path = $origin_dir_path.$origin_img_path;
		$res[$idx]['face_path'] = $img_path;
		$idx++;
	 }
     
	 echo json_encode($res);
	 
	 function get_the_origin_path($path)
	{
		$tmp = explode("_", $path);
		$origin_path = '';
		$count = count($tmp);
		for($i=0;$i<$count-2;++$i)
		{
			$origin_path = $origin_path.$tmp[$i].'_';
		}
		$origin_path = $origin_path.$tmp[$i].'.JPG';
		return $origin_path;		
	}
?>