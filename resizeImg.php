<?php

ini_set('max_execution_time', 2000);	//设置执行超时时间

// $src_dir = "E:\\WorkSpace\\HP\\from.group\\from.yongqiang\\test-2000\\test-2000\\";
// $dir_info = dir($src_dir);
// $dest_dir = "E:\\WorkSpace\\HP\\from.group\\from.yongqiang\\FaceIndexingAndSearchImg\\";
// 
// $c = 0;
// $flag = TRUE;
// $suffix = '';
// while(false !== ($entry = $dir_info->read()))
// {
	// // if($c==10) break;
	// if($entry=="."||$entry=="..") continue;
	// if($flag)
	// {
// 		
		// $file_part  = pathinfo($entry);  
		// $extendname = $file_part["extension"];  
		// $flag = FALSE;
	// }
// 	
	// $src_img_path = $src_dir.$entry.".".$suffix;
	// $dest_img_path = $dest_dir.$entry.".".$suffix;
	 // thumn($src_img_path, 480, 320,$dest_img_path);
	// $c++;
// }
// 
// 
// echo "ok";


$src_dir_pre = "E:\\WorkSpace\\HP\\from.group\\from.yongqiang\\cluster_result\\cluster_result\\FacialImg\\Cluster_";
$sIdx = 0;
$eIdx = 1172;
$dest_dir_pre = "E:\\WorkSpace\\HP\\from.group\\from.yongqiang\\FacialImg1\\Cluster_";

$c = 0;
$len = 4;	//因为有1173个文件夹，所以位数为4

for($i=0; $i<=1172; ++$i)
{
	$flag = TRUE;
	$suffix = '';
	$dir_num = get_dir_num($i, $len);
	$src_dir = $src_dir_pre.$dir_num."\\";
	$dest_dir = $dest_dir_pre.$dir_num."\\";
	mkdir($dest_dir);
	$dir_info = dir($src_dir);
	while(false !== ($entry = $dir_info->read()))
	{
		// if($c==10) break;
		if($entry=="."||$entry=="..") continue;
		if($flag)
		{
			
			$file_part  = pathinfo($entry);  
			$extendname = $file_part["extension"];  
			$flag = FALSE;
		}
		
		$src_img_path = $src_dir.$entry.".".$suffix;
		$dest_img_path = $dest_dir.$entry.".".$suffix;
		 thumn($src_img_path, 320, 320,$dest_img_path);
		// $c++;
	}	
	
}




echo "ok";

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


function thumn($background, $width, $height, $newfile) {
 list($s_w, $s_h)=getimagesize($background);//获取原图片高度、宽度
 // if ($width && ($s_w < $s_h)) {
 // $width = ($height / $s_h) * $s_w;
 // } else {
 // $height = ($width / $s_w) * $s_h;
 // }
 $new=imagecreatetruecolor($width, $height);
 $img=imagecreatefromjpeg($background);
 imagecopyresampled($new, $img, 0, 0, 0, 0, $width, $height, $s_w, $s_h);
 imagejpeg($new, $newfile);
 imagedestroy($new);
 imagedestroy($img);
 }
?>

