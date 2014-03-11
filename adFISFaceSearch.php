<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	$res_file_path = 'E:\\WorkSpace\\HP\\from.group\\from.yongqiang\\ParseAffinMat\\ParseAffinMat\\res.txt';
	$file = fopen($res_file_path, 'r');
	$c = 0;
	$file_dir = 'data\\ExtractedFaces\\FacialImg\\Cluster_';
	$dir_num_len = 4;
	$idx=0;
	$count=10;
	$res = array();
	while(!feof($file))
	{
		if($c==11) break;
		$line = fgets($file);
		$line = trim($line);
		// $res_array = array_filter(explode("\t", $line));
		$res_array = explode("\t", $line);
		arsort($res_array);
		// // $res_array = array_sort($res_array,'start_frame');
// 		
		// print_r($res_array);
		// $c++;
		// echo get_dir_num($idx, $dir_num_len)."</br>";
		$dir_num = get_dir_num($idx, $dir_num_len);
		$img_dir = $file_dir.$dir_num.'\\';
		$first_img_path = get_dir_first_img_path($img_dir);
		$res[$idx]['face_path'] = $first_img_path;
		$res[$idx]['sim_count'] = count(glob($img_dir.'*.jpg'));
		// $data = array();
		
		
		$data = array();
		$index = 0;
		foreach ($res_array as $key => $value) {
			if($index == $count) break;
			// if($index == $key) continue;
			
			$dir_num = get_dir_num($key, $dir_num_len);
			$img_dir = $file_dir.$dir_num.'\\';
			$first_img_path = get_dir_first_img_path($img_dir);
			$img_count = count(glob($img_dir.'*.jpg'));
			
			$data[$index]['face_path'] = $first_img_path;
			$data[$index]['img_dir'] = $img_dir;
			$data[$index]['sim_count'] = $img_count;
			$data[$index]['sim_value'] = $value;
			$index++;
		}
		$res[$idx]['data'] = $data;
		$idx++;
		$c++;
	}

	echo json_encode($res);
	fclose($file);
	// echo "</br>"."ok";
	
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
	
	function get_dir_first_img_path($img_dir)
	{
		$dir_info = dir($img_dir);
		$first_img_path = '';
		while (false !== ($entry = $dir_info->read())) {
			if($entry=='.'||$entry=='..') continue;
			$first_img_path = $img_dir.$entry;
			break;
		}
		return $first_img_path;
	}
?>