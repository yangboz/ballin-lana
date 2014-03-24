<?php
	include("settings.php");
	header("Access-Control-Allow-Origin: *");
	
	$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	$page = intval($_POST['pageNum']);
	// $page = 130;
	$pageSize = 9; //每页显示数
	$total = 0;
	
	$dir_path = '';
	$res_file_path = '';
	$win_lin = '';
	$sub_dir_path = '';
	switch ($os) {
		case 'windows':
			$res_file_path = 'data\\face_indexing_and_search_simres.txt';
			$dir_path =  'data\\ExtractedFaces\\FacialImg\\Cluster_';
			$win_lin_suf = '\\';
			$dir_num_len = 4;	//类文件的标号长度
			$file = fopen($res_file_path, 'r');
			$line = array();
			$idx=0;
			while (!feof($file)) {
				$tmp = fgets($file);
				$line[$idx++] = trim($tmp);
			}
			$res = array();
			$total = $idx-1;	//类的个数
			$res['total'] = $total;	
			$res['pageSize'] = $pageSize;
			$totalPage = ceil($total/$pageSize); //总页数
			$res['totalPage'] = $totalPage;
			$startPage = $page*$pageSize;
			$res['list'] = get_data_win($dir_path,$line,$win_lin_suf,$startPage,$pageSize,$total,$dir_num_len);
			fclose($file);
			echo json_encode($res);
			break;
		
		case 'linux':
			$res_file_path = '/var/www/html/vica_web/data/face_indexing_and_search_simres.txt';
			$dir_path = '/var/www/html/vica_web/data/ExtractedFaces/FacialImg/Cluster_';
			// echo $dir_path;
			// $dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/Cluster_';
			$sub_dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/Cluster_';
			
			
			$win_lin_suf = '/';
			$dir_num_len = 4;	//类文件的标号长度
			$file = fopen($res_file_path, 'r');
			$line = array();
			$idx=0;
			while (!feof($file)) {
				$tmp = fgets($file);
				$line[$idx++] = trim($tmp);
			}
			$res = array();
			$total = $idx-1;	//类的个数
			$res['total'] = $total;	
			$res['pageSize'] = $pageSize;
			$totalPage = ceil($total/$pageSize); //总页数
			$res['totalPage'] = $totalPage;
			$startPage = $page*$pageSize;
			$res['list'] = get_data_lin($dir_path,$sub_dir_path,$line,$win_lin_suf,$startPage,$pageSize,$total,$dir_num_len);
			fclose($file);
			echo json_encode($res);
			break;
		default:
			
			break;
	}
	
	
	function get_data_win($dir_path,$line,$win_lin_suf,$startPage,$pageSize,$total,$dir_num_len)
	{
		$result = array();
		$index = 0;
		$count = 30;	//返回排在前$count=30个的相近类
		$startIdx = $startPage;
		$endIdx = $startIdx+$pageSize;
		if($endIdx>$total) $endIdx=$total;
		// echo $startIdx.'_'.$endIdx;
		while ($startIdx<$endIdx) {
			
			$row = $line[$startIdx];
			$res_array = explode("\t", $row);
			arsort($res_array);
			$dir_num = get_dir_num($startIdx, $dir_num_len);
			$img_dir = $dir_path.$dir_num.$win_lin_suf;
			$first_img_path = get_dir_first_img_path($img_dir,$img_dir);
			$result[$index]['face_path'] = $first_img_path;
			// $result[$index]['sim_count'] = count(glob($img_dir.'*.jpg'));
			$data = array();
			$idx = 0;
			foreach ($res_array as $key => $value) {
				if($idx == $count) break;
				
				$dir_num = get_dir_num($key, $dir_num_len);
				$img_dir = $dir_path.$dir_num.$win_lin_suf;
				$first_img_path = get_dir_first_img_path($img_dir,$img_dir);
				// $first_img_path = get_dir_first_img_path($img_dir);
				$img_count = count(glob($img_dir.'*.jpg'));
				
				$data[$idx]['face_path'] = $first_img_path;
				$data[$idx]['img_dir'] = $img_dir;
				// $data[$index]['origin_img_dir'] = $origin_dir_path;
				// $data[$idx]['sub_img_path'] = $sub_img_path;
				$data[$idx]['sim_count'] = $img_count;
				$data[$idx]['sim_value'] = $value;
				
				$idx++;
			}
			
			$result[$index]['data'] = $data;
			$index++;
			$startIdx++;
		}
		
		// print_r($result);
		return $result;
	}
	
	
	function get_data_lin($dir_path,$sub_dir_path,$line,$win_lin_suf,$startPage,$pageSize,$total,$dir_num_len)
	{
		$result = array();
		$index = 0;
		$count = 30;	//返回排在前$count=30个的相近类
		$startIdx = $startPage;
		$endIdx = $startIdx+$pageSize;
		if($endIdx>$total) $endIdx=$total;
		
		while ($startIdx<$endIdx) {
			
			$row = $line[$startIdx];
			$res_array = explode("\t", $row);
			arsort($res_array);
			$dir_num = get_dir_num($startIdx, $dir_num_len);
			$img_dir = $dir_path.$dir_num.$win_lin_suf;
			$sub_img_dir = $sub_dir_path.$dir_num.$win_lin_suf;
			$first_img_path = get_dir_first_img_path($img_dir,$sub_img_dir);
			$result[$index]['face_path'] = $first_img_path;
			// $result[$index]['sim_count'] = count(glob($img_dir.'*.jpg'));
			$data = array();
			$idx = 0;
			foreach ($res_array as $key => $value) {
				if($idx == $count) break;
				
				$dir_num = get_dir_num($key, $dir_num_len);
				$img_dir = $dir_path.$dir_num.$win_lin_suf;
				$sub_img_dir = $sub_dir_path.$dir_num.$win_lin_suf;
				$first_img_path = get_dir_first_img_path($img_dir,$sub_img_dir);
				// $first_img_path = get_dir_first_img_path($img_dir);
				$img_count = count(glob($img_dir.'*.jpg'));
				
				$data[$idx]['face_path'] = $first_img_path;
				$data[$idx]['img_dir'] = $img_dir;
				// $data[$index]['origin_img_dir'] = $origin_dir_path;
				// $data[$idx]['sub_img_path'] = $sub_img_path;
				$data[$idx]['sim_count'] = $img_count;
				$data[$idx]['sim_value'] = $value;
				
				$idx++;
			}
			
			$result[$index]['data'] = $data;
			$index++;
			$startIdx++;
		}
		
		return $result;
	}
	
	
	
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