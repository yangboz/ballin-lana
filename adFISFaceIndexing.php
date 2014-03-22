<?php

include("settings.php");
header("Access-Control-Allow-Origin: *");
	
	$page = intval($_POST['pageNum']);
	// $page = 0;
	$pageSize = 9; //每页显示数
	$total = 0;
	$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	$win_lin = '';
	$dir_path = '';
	$dir_info;
	$cluster = '';
	switch ($os) {
		case 'windows':
			$dir_path = 'data\\ExtractedFaces\\FacialImg\\';
		 	$origin_dir_path = 'data\\FaceIndexingAndSearchImg\\';
			$win_lin_suf = '\\';
			$dir_info = scandir($dir_path);
			$total = count($dir_info);	//总记录数
			$totalPage = ceil(($total-2)/$pageSize); //总页数
			$startPage = $page*$pageSize;
			$res = array();
			$res['total'] = $total-2;	//目录中的'.','..'
			$res['pageSize'] = $pageSize;
			$res['totalPage'] = $totalPage;
			$res['list'] = get_dir_file_name_win($dir_path,$origin_dir_path,$dir_info,$win_lin_suf,$startPage,$pageSize,$total);
			echo json_encode($res);
			break;
		
		case 'linux':
			$dir_path = '/var/www/html/vica_web/data/ExtractedFaces/FacialImg/';
			$origin_dir_path = 'http://15.125.94.250/vica_web/data/FaceIndexingAndSearchImg/';
			$dir_path_lin = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/';
			$win_lin_suf = '/';
			$dir_info = scandir($dir_path);
			$total = count($dir_info);	//总记录数
			$totalPage = ceil(($total-2)/$pageSize); //总页数
			$startPage = $page*$pageSize;
			$res = array();
			$res['total'] = $total-2;	//目录中的'.','..'
			$res['pageSize'] = $pageSize;
			$res['totalPage'] = $totalPage;
			$res['list'] = get_dir_file_name_lin($dir_path,$dir_path_lin,$origin_dir_path,$dir_info,$win_lin_suf,$startPage,$pageSize,$total);
			echo json_encode($res);
			break;
		default:
			
			break;
	}
	
	
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
	
	function findNum($str=''){
	$str=trim($str);
	if(empty($str)){return '';}
	$result='';
	$flag = FALSE;
	for($i=0;$i<strlen($str);$i++){
		
	if($str[$i]=='0'&&(!$flag)) continue;
	if($str[$i]!='0'&&(!$flag))
	{
		$flag = TRUE;
	}
	$result.=$str[$i];
	
	}
	return $result;
}

	
	function get_dir_file_name_win($dir_path,$origin_dir_path,$dir_info,$win_lin_suf,$startPage,$pageSize,$total)
	{
		$result = array();
		$index = 0;
		
		$startIdx = $startPage+2;
		$endIdx = $startIdx+$pageSize;
		if($endIdx>$total) $endIdx=$total;
		// echo $startIdx.'_'.$endIdx;
		while ($startIdx < $endIdx) 
		{
			$sub_dir_path = $dir_path.$dir_info[$startIdx].$win_lin_suf;
			$sub_dir_info = dir($sub_dir_path);
			$img_count = count(glob($sub_dir_path.'*.jpg'));
			$sim_result = array();
			$idx = 0;
			
			$slider_show_count = 0;
			
			while(false !== ($entry = $sub_dir_info -> read()))
			{
				if($entry=='.'||$entry=='..') continue;
				if($slider_show_count == 20) break;
				
				if(!isset($result[$index]['face_path']))
				{
					$img_path = $sub_dir_path.$entry;
					$result[$index]['face_path'] = $img_path;
				}
				
				$origin_path = get_the_origin_path($entry);
				$origin_img_path = $origin_dir_path.$origin_path;
			 	$sim_result[$idx++] = array('sim_path' => $origin_img_path);
				
				
				$slider_show_count++;
			}
			
			$result[$index]['sim_face_path'] = $sim_result;
			$result[$index]['count'] = $img_count;
			$startIdx++;
			$index++;
		}		
		return $result;	
	}




	function get_dir_file_name_lin($dir_path,$dir_path_lin,$origin_dir_path,$dir_info,$win_lin_suf,$startPage,$pageSize,$total)
	{
		$result = array();
		$index = 0;
		
		$startIdx = $startPage+2;
		$endIdx = $startIdx+$pageSize;
		if($endIdx>$total) $endIdx=$total;
		// echo $startIdx.'_'.$endIdx;
		while ($startIdx < $endIdx) 
		{
			$sub_dir_path = $dir_path.$dir_info[$startIdx].$win_lin_suf;
			$sub_dir_info = dir($sub_dir_path);
			$sub_dir_path_img = $dir_path_lin.$dir_info[$startIdx].$win_lin_suf;
			
			$img_count = count(glob($sub_dir_path.'*.jpg'));
			$sim_result = array();
			$idx = 0;
			
			$slider_show_count = 0;
			
			while(false !== ($entry = $sub_dir_info -> read()))
			{
				if($entry=='.'||$entry=='..') continue;
				if($slider_show_count == 20) break;
				
				if(!isset($result[$index]['face_path']))
				{
					$img_path = $sub_dir_path_img.$entry;
					$result[$index]['face_path'] = $img_path;
				}
				
				$origin_path = get_the_origin_path($entry);
				$origin_img_path = $origin_dir_path.$origin_path;
			 	$sim_result[$idx++] = array('sim_path' => $origin_img_path);
				
				
				$slider_show_count++;
			}
			
			$result[$index]['sim_face_path'] = $sim_result;
			$result[$index]['count'] = $img_count;
			$startIdx++;
			$index++;
			$sub_dir_info->close();
		}		
		return $result;	
	}


?>