<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	
	
	$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	$win_lin = '';
	$dir_path = '';
	$dir_info;
	switch ($os) {
		case 'windows':
			$dir_path = 'data\\ExtractedFaces\\FacialImg\\';
			// echo $dir_path;
			$dir_info = dir($dir_path);
			$sub_dir_path = 'data\\ExtractedFaces\\FacialImg\\';
			$win_lin = '\\';
			break;
		
		case 'linux':
			$dir_path = '/var/www/html/vica_web/data/ExtractedFaces/FacialImg/';
			// echo $dir_path;
			$dir_info = dir($dir_path);
			$sub_dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/';
			$win_lin = '/';
			break;
		default:
			
			break;
	}
	
	$res = array();
	$idx = 0;
	$tmp = '';
	while (false !== ($entry = $dir_info->read())) {
   // echo $entry."\n";
		if($tmp == 100) break;
		if($entry=='.'||$entry=='..') continue;
		$tmp_dir = $dir_path.$entry.$win_lin;
		$dir_img = dir($tmp_dir);
		$tmp_dir = $sub_dir_path.$entry.$win_lin;
		$count = 0;
		$index = 0;
		$data = array();
		while(false !== ($tmp_entry = $dir_img->read()))
		{
			// echo $entry."\n";
			if($tmp_entry=='.'||$tmp_entry=='..') continue;
			$img_path = $tmp_dir.$tmp_entry;
			if(!isset($res[$idx]['face_path']))
			{
				$res[$idx]['face_path'] = $img_path;
			}
			if($count == 10) break;
		 	$data[$index++] = array('sim_path' => $img_path);
			$count++;
				
		}
		$res[$idx]['sim_face_data'] = $data;
		$idx++;
		
		$tmp++;
	}
	
	echo json_encode($res);
?>