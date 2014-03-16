<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	
	$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	$dir_path = '';
	$res_file_path = '';
	$win_lin = '';
	$sub_dir_path = '';
	switch ($os) {
		case 'windows':
			$res_file_path = 'data\\face_indexing_and_search_simres.txt';
			$dir_path =  'data\\ExtractedFaces\\FacialImg\\Cluster_';
			// echo $dir_path;
			$sub_dir_path = 'data\\ExtractedFaces\\FacialImg\\Cluster_';
			$win_lin = '\\';
			break;
		
		case 'linux':
			$res_file_path = '/var/www/html/vica_web/data/face_indexing_and_search_simres.txt';
			$dir_path = '/var/www/html/vica_web/data/ExtractedFaces/FacialImg/Cluster_';
			// echo $dir_path;
			// $dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/Cluster_';
			$sub_dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/Cluster_';
			$win_lin = '/';
			break;
		default:
			
			break;
	}
	
	$file = fopen($res_file_path, 'r');
	// echo $file;
	$c = 0;
	$dir_num_len = 4;
	$idx=0;
	$count=50;
	$res = array();
	$img_dir = '';
	$sub_img_path = '';
	$first_img_path = '';
	while(!feof($file))
	{
		if($c==50) break;
		$line = fgets($file);
		$line = trim($line);
		$res_array = explode("\t", $line);
		arsort($res_array);
		$dir_num = get_dir_num($idx, $dir_num_len);
		$img_dir = $dir_path.$dir_num.$win_lin;
		$sub_img_path = $sub_dir_path.$dir_num.$win_lin;
		$first_img_path = get_dir_first_img_path($img_dir,$sub_img_path);
		$res[$idx]['face_path'] = $first_img_path;
		$res[$idx]['sim_count'] = count(glob($img_dir.'*.jpg'));
		$data = array();
		$index = 0;
		foreach ($res_array as $key => $value) {
			if($index == $count) break;
			$dir_num = get_dir_num($key, $dir_num_len);
			$img_dir = $dir_path.$dir_num.$win_lin;
			$sub_img_path = $sub_dir_path.$dir_num.$win_lin;
			$first_img_path = get_dir_first_img_path($img_dir,$sub_img_path);
			// $first_img_path = get_dir_first_img_path($img_dir);
			$img_count = count(glob($img_dir.'*.jpg'));
			
			$data[$index]['face_path'] = $first_img_path;
			$data[$index]['img_dir'] = $img_dir;
			$data[$index]['sub_img_path'] = $sub_img_path;
			$data[$index]['sim_count'] = $img_count;
			$data[$index]['sim_value'] = $value;
			$index++;
		}
		$res[$idx]['data'] = $data;
		$idx++;
		$c++;
	}

	fclose($file);
	echo json_encode($res);
	
	function get_dir_num($num,$len)
	{
		$dir_num = '';
		for($i=0;$i<$len;++$i)
		{
			if(($tmp = $num%10)!=0)
			{
				$dir_num = $dir_num.$tmp;
				$num = floor($num/10);
			}else{
				$dir_num = $dir_num.'0';
				$num = floor($num/10);
			}
		}
		return strrev($dir_num);
	}
	
	function get_dir_first_img_path($img_dir,$sub_img_path)
	{
		$dir_info = dir($img_dir);
		$first_img_path = '';
		while (false !== ($entry = $dir_info->read())) {
			if($entry=='.'||$entry=='..') continue;
			$first_img_path = $sub_img_path.$entry;
			break;
		}
		return $first_img_path;
	}
?>