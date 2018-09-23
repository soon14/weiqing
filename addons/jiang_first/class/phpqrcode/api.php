<?php
header("content-Type: text/html; charset=utf-8");
	$data =getdata('data');if(empty($data)){
	   $str="
       参数说明<br />
       data:要转码的数据<br />
       level:默认L 纠错级别：L、M、Q、H<br />
       size:默认4 点的大小：1到10,用于手机端4就可以了 <br />
       margin:默认1 边距 1到10 <br />
       logo:默认为空 中间logo 的文件名需要放到logo/目录中 <br /> 
       filename:默认为空 生成的文件名 生成后放到temp/目录中 <br />
       ";echo $str;exit;       
	}
    $level =getdata('level','L');// 纠错级别：L、M、Q、H    
    $size =getdata('size','4');;// 4;// 点的大小：1到10,用于手机端4就可以了
    // 下面注释了把二维码图片保存到本地的代码,如果要保存图片,用$fileName替换第二个参数false
    $path = 'temp/';
    $margin =getdata('margin','1');//边距
    $logo = getdata('logo');;//logo
    $filename=getdata('filename');// 生成的文件名
    if(!empty($filename)){$filename = $path.$filename.'_'.$size.'.png';
    }else{$filename=false;}
    if(!empty($logo) && empty($filename)){$filename = $path.$logo.'_'.$size.'.png';}$QR=false;
    if(!empty($filename)){$QR=$path.basename($filename);}
    include "qrlib.php";
    $QRcode = new QRcode();
    $QRcode->png($data,$QR,$level,$size,$margin);
    if($logo){
        if(strpos($logo,'http://')===false)$logo='logo/'.$logo;
        $QR = imagecreatefromstring(file_get_contents($QR));
        $logo = imagecreatefromstring(file_get_contents($logo));
        $QR_width = imagesx($QR);
        $QR_height = imagesy($QR);
        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);
        $logo_qr_width = $QR_width /5;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;
        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        header("Content-Type:image/jpg");
        imagepng($QR);exit;
    }
    if(!empty($filename))return $filename;
    
    
 function getdata($name,$default=''){
    $getdata=$_GET;
    return empty($getdata[$name])?$default:$getdata[$name];
 }
    
?>