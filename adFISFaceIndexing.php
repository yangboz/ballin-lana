<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	$dir_path = 'data\\ExtractedFaces\\FacialImg\\';
	$dir_info = dir($dir_path);
	$res = array();
	$idx = 0;
	$tmp = 0;
	while (false !== ($entry = $dir_info->read())) {
   // echo $entry."\n";
		if($tmp == 30) break;
		if($entry=='.'||$entry=='..') continue;
		$tmp_dir = $dir_path.$entry.'\\';
		$dir_img = dir($tmp_dir);
		$count = 0;
		$index = 0;
		$data = array();
		while(false !== ($tmp_entry = $dir_img->read()))
		{
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