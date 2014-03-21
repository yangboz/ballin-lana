<?php

include("settings.php");
	header("Access-Control-Allow-Origin: *");
	
	
	$os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	$win_lin = '';
	$dir_path = '';
	$dir_info;
	$cluster = '';
	// $flag;
	switch ($os) {
		case 'windows':
			$dir_path = 'data\\ExtractedFaces\\FacialImg\\';
			// echo $dir_path;
			$dir_info = dir($dir_path);
			$sub_dir_path = 'data\\ExtractedFaces\\FacialImg\\';
			$origin_dir_path = 'data\\FaceIndexingAndSearchImg\\';
			$cluster = 'Cluster_';
			$win_lin = '\\';
			// $flag = TRUE;
			break;
		
		case 'linux':
			$dir_path = '/var/www/html/vica_web/data/ExtractedFaces/FacialImg/';
			// echo $dir_path;
			$dir_info = dir($dir_path);
			$sub_dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/';
			$origin_dir_path = 'http://15.125.94.250/vica_web/data/FaceIndexingAndSearchImg/';
			$cluster = 'Cluster_';
			$win_lin = '/';
			// $flag=FALSE;
			break;
		default:
			
			break;
	}
	
	if($os == 'windows')
	{
		$res = array();
		$idx = 0;
		$tmp = '';
		while (false !== ($entry = $dir_info->read())) {
	   // echo $entry."\n";
			if($tmp == 10) break;
			if($entry=='.'||$entry=='..') continue;
			$tmp_dir = $dir_path.$entry.$win_lin;
			$dir_img = dir($tmp_dir);
			$img_count = count(glob($tmp_dir.'*.jpg'));
			// if($img_count<2) continue;
			$tmp_dir = $sub_dir_path.$entry.$win_lin;
			$count = 0;
			$index = 0;
			$data = array();
			while(false !== ($tmp_entry = $dir_img->read()))
			{
				// echo $entry."\n";
				if($tmp_entry=='.'||$tmp_entry=='..') continue;
				
				if(!isset($res[$idx]['face_path']))
				{
					$img_path = $tmp_dir.$tmp_entry;
					$res[$idx]['face_path'] = $img_path;
				}
				if($count == 10) break;
				$origin_path = get_the_origin_path($tmp_entry);
				$origin_img_path = $origin_dir_path.$origin_path;
				if(in_array($origin_img_path, $data)) continue;
			 	$data[$index++] = array('sim_path' => $origin_img_path);
				$count++;
				// echo $img_path."</br>";
					
			}
			$res[$idx]['sim_face_data'] = $data;
			
			$res[$idx]['count'] = $img_count;
			$idx++;
			
			$tmp++;
		}
		
	}


if($os == 'linux')
{
	$idx1=0;
	$res1=array();
	// $c = 0;
	while (false !== ($entry = $dir_info->read())) {
  	// if($c==100) continue;
		if($entry=='.'||$entry=='..') continue;
	 // echo $entry."</br>";
	// $tmp++;
		$tmp = explode('.', $entry);
		$tmp1 = explode('_', $tmp[0]);
		$entry = findNum($tmp1[1]);
		$res1[$idx1++] = (int)$entry;
		// $c++;
	}
	
	sort($res1);
	$count = count($res1);
	
	$res = array();
	$idx = 0;
	$len = 4;
	$mycount = 0;
	for($i=0;$i<$count;++$i)
	{
		if($mycount == 15) continue;
		$dir_num = get_dir_num($res1[$i], $len);
		$cluster_img_dir = $cluster.$dir_num;
		
		$tmp_dir = $dir_path.$cluster_img_dir.$win_lin;
		$dir_img = dir($tmp_dir);
		$img_count = count(glob($tmp_dir.'*.jpg'));
		$tmp_dir = $sub_dir_path.$cluster_img_dir.$win_lin;
			$count1 = 0;
			$index = 0;
			$data = array();
			while(false !== ($tmp_entry = $dir_img->read()))
			{
				// echo $entry."\n";
				if($tmp_entry=='.'||$tmp_entry=='..') continue;
				
				if(!isset($res[$idx]['face_path']))
				{
					$img_path = $tmp_dir.$tmp_entry;
					$res[$idx]['face_path'] = $img_path;
				}
				if($count1 == 10) break;
				$origin_path = get_the_origin_path($tmp_entry);
				$origin_img_path = $origin_dir_path.$origin_path;
				if(in_array($origin_img_path, $data)) continue;
			 	$data[$index++] = array('sim_path' => $origin_img_path);
				$count1++;
				// echo $img_path."</br>";
					
			}
		$res[$idx]['sim_face_data'] = $data;
		
		$res[$idx]['count'] = $img_count;
		$idx++;
		
		$mycount++;
		
	}
	
	
	
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
		// if(is_numeric($str[$i])&&($str[$i]!='0')){
			// $result.=$str[$i];
			// $flag = TRUE;
		// }
		// if(!$flag)
		// {
			// continue;
		// }
		// if($flag&&($str[$i]=='0'))
		// {
			// $result.=$str[$i];
		// }
		
		if($str[$i]=='0'&&(!$flag)) continue;
		if($str[$i]!='0'&&(!$flag))
		{
			$flag = TRUE;
		}
		$result.=$str[$i];
		
	}
	return $result;
}






	// include("settings.php");
	// header("Access-Control-Allow-Origin: *");
// 	
// 	
	// $os = (DIRECTORY_SEPARATOR=='\\')?"windows":'linux';
	// $win_lin = '';
	// $dir_path = '';
	// $dir_info;
	// $cluster = '';
	// // $flag;
	// switch ($os) {
		// case 'windows':
			// $dir_path = 'data\\ExtractedFaces\\FacialImg\\';
			// // echo $dir_path;
			// $dir_info = dir($dir_path);
			// $sub_dir_path = 'data\\ExtractedFaces\\FacialImg\\';
			// $origin_dir_path = 'data\\FaceIndexingAndSearchImg\\';
			// $cluster = 'Cluster_';
			// $win_lin = '\\';
			// // $flag = TRUE;
			// break;
// 		
		// case 'linux':
			// $dir_path = '/var/www/html/vica_web/data/ExtractedFaces/FacialImg/';
			// // echo $dir_path;
			// $dir_info = dir($dir_path);
			// $sub_dir_path = 'http://15.125.94.250/vica_web/data/ExtractedFaces/FacialImg/';
			// $origin_dir_path = 'http://15.125.94.250/vica_web/data/FaceIndexingAndSearchImg/';
			// $cluster = 'Cluster_';
			// $win_lin = '/';
			// // $flag=FALSE;
			// break;
		// default:
// 			
			// break;
	// }
// 	
	// if($os == 'windows')
	// {
		// $res = array();
		// $idx = 0;
		// $tmp = '';
		// while (false !== ($entry = $dir_info->read())) {
	   // // echo $entry."\n";
			// if($tmp == 10) break;
			// if($entry=='.'||$entry=='..') continue;
			// $tmp_dir = $dir_path.$entry.$win_lin;
			// $dir_img = dir($tmp_dir);
			// $img_count = count(glob($tmp_dir.'*.jpg'));
			// // if($img_count<2) continue;
			// $tmp_dir = $sub_dir_path.$entry.$win_lin;
			// $count = 0;
			// $index = 0;
			// $data = array();
			// while(false !== ($tmp_entry = $dir_img->read()))
			// {
				// // echo $entry."\n";
				// if($tmp_entry=='.'||$tmp_entry=='..') continue;
// 				
				// if(!isset($res[$idx]['face_path']))
				// {
					// $img_path = $tmp_dir.$tmp_entry;
					// $res[$idx]['face_path'] = $img_path;
				// }
				// if($count == 10) break;
				// $origin_path = get_the_origin_path($tmp_entry);
				// $origin_img_path = $origin_dir_path.$origin_path;
				// if(in_array($origin_img_path, $data)) continue;
			 	// $data[$index++] = array('sim_path' => $origin_img_path);
				// $count++;
				// // echo $img_path."</br>";
// 					
			// }
			// $res[$idx]['sim_face_data'] = $data;
// 			
			// $res[$idx]['count'] = $img_count;
			// $idx++;
// 			
			// $tmp++;
		// }
// 		
	// }
// 
// 
// if($os == 'linux')
// {
	// $idx1=0;
	// $res1=array();
	// // $c = 0;
	// while (false !== ($entry = $dir_info->read())) {
  	// // if($c==100) continue;
		// if($entry=='.'||$entry=='..') continue;
	 // // echo $entry."</br>";
	// // $tmp++;
		// $tmp = explode('.', $entry);
		// $tmp1 = explode('_', $tmp[0]);
		// $entry = findNum($tmp1[1]);
		// $res1[$idx1++] = (int)$entry;
		// // $c++;
	// }
// 	
	// sort($res1);
	// $count = count($res1);
// 	
	// $res = array();
	// $idx = 0;
	// $len = 4;
	// $mycount = 0;
	// for($i=0;$i<$count;++$i)
	// {
		// if($mycount == 15) continue;
		// $dir_num = get_dir_num($res1[$i], $len);
		// $cluster_img_dir = $cluster.$dir_num;
// 		
		// $tmp_dir = $dir_path.$cluster_img_dir.$win_lin;
		// $dir_img = dir($tmp_dir);
		// $img_count = count(glob($tmp_dir.'*.jpg'));
		// $tmp_dir = $sub_dir_path.$cluster_img_dir.$win_lin;
			// $count1 = 0;
			// $index = 0;
			// $data = array();
			// while(false !== ($tmp_entry = $dir_img->read()))
			// {
				// // echo $entry."\n";
				// if($tmp_entry=='.'||$tmp_entry=='..') continue;
// 				
				// if(!isset($res[$idx]['face_path']))
				// {
					// $img_path = $tmp_dir.$tmp_entry;
					// $res[$idx]['face_path'] = $img_path;
				// }
				// if($count1 == 10) break;
				// $origin_path = get_the_origin_path($tmp_entry);
				// $origin_img_path = $origin_dir_path.$origin_path;
				// if(in_array($origin_img_path, $data)) continue;
			 	// $data[$index++] = array('sim_path' => $origin_img_path);
				// $count1++;
				// // echo $img_path."</br>";
// 					
			// }
		// $res[$idx]['sim_face_data'] = $data;
// 		
		// $res[$idx]['count'] = $img_count;
		// $idx++;
// 		
		// $mycount++;
// 		
	// }
// 	
// 	
// 	
// }
// 	
	// echo json_encode($res);
// 	
	// function get_the_origin_path($path)
	// {
		// $tmp = explode("_", $path);
		// $origin_path = '';
		// $count = count($tmp);
		// for($i=0;$i<$count-2;++$i)
		// {
			// $origin_path = $origin_path.$tmp[$i].'_';
		// }
		// $origin_path = $origin_path.$tmp[$i].'.JPG';
		// return $origin_path;		
	// }
// 	
// 	
	// function get_dir_num($num,$len)
	// {
		// $dir_num = '';
		// for($i=0;$i<$len;++$i)
		// {
			// if(($tmp = $num%10)!=0)
			// {
				// $dir_num = $dir_num.$tmp;
				// $num = floor($num/10);
			// }else{
				// $dir_num = $dir_num.'0';
				// $num = floor($num/10);
			// }
		// }
		// return strrev($dir_num);
	// }
// 	
		// function findNum($str=''){
	// $str=trim($str);
	// if(empty($str)){return '';}
	// $result='';
	// $flag = FALSE;
	// for($i=0;$i<strlen($str);$i++){
		// // if(is_numeric($str[$i])&&($str[$i]!='0')){
			// // $result.=$str[$i];
			// // $flag = TRUE;
		// // }
		// // if(!$flag)
		// // {
			// // continue;
		// // }
		// // if($flag&&($str[$i]=='0'))
		// // {
			// // $result.=$str[$i];
		// // }
// 		
		// if($str[$i]=='0'&&(!$flag)) continue;
		// if($str[$i]!='0'&&(!$flag))
		// {
			// $flag = TRUE;
		// }
		// $result.=$str[$i];
// 		
	// }
	// return $result;
// }
?>