<?php
header("Content-type:image/png");
error_reporting(0);
mb_internal_encoding("UTF-8");
$score = $_GET['score'];
$nickname = $_GET['nickname'];
$avatar = './avatar/' . $_GET['openid'] . '.jpg';
if(!file_exists($avatar)) {
    $avatar = './avatar/' . $_GET['openid'] . '.png';
}
$avatar_path = $_GET['avatar_path'];
$str = '      ' . $_GET['content'];
$rank = $_GET['rank'];
$keep_days = $_GET['keep_days'];
$praise_times = $_GET['praise_times'];
$collect_count = $_GET['collect_count'];

$length = mb_strlen($str,"UTF-8");
if($length>200) {
    $str = mb_substr($str , 0, 100, "UTF-8") . '...';
}
// $str = mb_strimwidth($str , 0, 100, '...', "UTF-8");

$font = './PingFang-Regular.ttf';

//创建画布
$ban_width = 750;
$ban_height = 1209;
$img = imagecreatetruecolor($ban_width, $ban_height);
$white = imagecolorallocate($img, 255, 255,255);
imagefill($img, 0, 0, $white);

//添加背景图片
$src_header_path = './share.jpg';
$src_header = imagecreatefromstring(file_get_contents($src_header_path));
$src_header_arr = getimagesize($src_header_path);
$header_w = 750;
$header_h = 1209;
$header = imagecreatetruecolor($header_w, $header_h);
imagecopyresized($header, $src_header, 0, 0, 0, 0, $header_w, $header_h, $src_header_arr[0], $src_header_arr[1]);
imagecopymerge($img, $header, 0, 0, 0, 0, $header_w, $header_h, 100);


//处理文字
$content = autowrap(25, 0, $font, $str, 500);
//添加文字
$black = imagecolorallocate($img, 0, 0, 0);
$white = imagecolorallocate($img,255,255,255);
imagettftext($img, 25, 0, 125, 380, $white, $font, $content);

$str0 = $score . '分';
$str1 = "今日排行榜第".$rank."名";
$str2 = "表扬孩子".$praise_times."次";
$str3 = $collect_count."句表扬被收录";
$str4 = "已经坚持不吼娃第".$keep_days."天";
$str5 = "慧加，爱需要智慧";
$str6 = "长按去参加";

$b1 = imagettfbbox(40,0,$font,$str0);
$w1 = abs($b1[2] - $b1[0]);
$marginleft = (750 - $w1)/2;

imagettftext($img, 40, 0, $marginleft, 330, $white, $font, $str0);
imagettftext($img, 20, 0, 270, 770, $white, $font, $str1);
imagettftext($img, 20, 0, 40, 770, $white, $font, $str2);
imagettftext($img, 20, 0, 520, 770, $white, $font, $str3);
imagettftext($img, 24, 0, 50, 1120, $white, $font, $str4);
imagettftext($img, 20, 0, 50, 1160, $white, $font, $str5);
imagettftext($img, 16, 0, 565, 1110, $white, $font, $str6);

//添加头像
imagebackgroundmycard($avatar_path,$avatar,$img,50,920,100,100,50);
//添加昵称
imagettftext($img, 16, 0, 65 , 1060, $white, $font, $nickname);
//添加二维码
$qrcode_path = './qrcode.png';
$newqrcode_path = './newqrcode.png';
imageresize($qrcode_path,$newqrcode_path,150,150);
$qrcode = imagecreatefromstring(file_get_contents($newqrcode_path));
imagecopymerge($img, $qrcode, 550, 910, 0, 0, 150, 150, 100);

imagepng($img);
imagedestroy($img);



function autowrap($fontsize, $angle, $fontface, $string, $width) {
// 这几个变量分别是 字体大小, 角度, 字体名称, 字符串, 预设宽度
    $content = "";
    // 将字符串拆分成一个个单字 保存到数组 letter 中
    for ($i=0;$i<mb_strlen($string);$i++) {
        $letter[] = mb_substr($string, $i, 1);
    }
    foreach ($letter as $l) {
        $teststr = $content." ".$l;
        $testbox = imagettfbbox($fontsize, $angle, $fontface, $teststr);
        // 判断拼接后的字符串是否超过预设的宽度
        if (($testbox[2] > $width) && ($content !== "")) {
            $content .= "\n";
        }
        $content .= $l;
    }
    return $content;
}

function imageresize($filename,$newname,$newwidth,$newheight){

    if(!empty($filename) && file_exists($filename)){
        list($width, $height) = getimagesize($filename);
        $thumb = imagecreatetruecolor($newwidth, $newheight);

        $suffix = strrchr($filename,'.');
        switch($suffix){
            case '.gif':
                $source = imagecreatefromgif($filename);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagegif($thumb,$newname);
                break;
            case '.png':
                $source = imagecreatefrompng($filename);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagepng($thumb,$newname);
                break;
            case '.jpg':
                $source = imagecreatefromjpeg($filename);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagejpeg($thumb,$newname);
                break;
            case '.bmp':
                $source = imagecreatefromwbmp($filename);
                imagecopyresized($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
                imagewbmp($thumb,$newname);
                break;
        }

        imagedestroy($thumb);
    }else {
        echo 'INVALID IMAGE';
    }
}

function imagebackgroundmycard($avatar_path,$avatar ,$im, $dst_x, $dst_y, $image_w, $image_h, $radius) {
    $imghttp = get_headers($avatar_path,true);
    if($imghttp['Content-Type'] == "image/jpeg") {
        $source = imagecreatefromjpeg($avatar);
    }
    if($imghttp['Content-Type'] == "image/png") {
        $source = imagecreatefrompng($avatar);
    }
    if(!$source){
        $source  = imagecreatetruecolor($image_w, $image_h);
        $bg = imagecolorallocate($source, 0, 0, 0);
        imagefill($source, 0, 0, $bg);
        imagecopyresized($im, $source, $dst_x, $dst_y, 0, 0, $image_w, $image_h, $image_w, $image_h);
    }else {
        $avatar_size = getimagesize($avatar);
        $source = radius_img($avatar,$avatar_size[0]/2);
        imagecopyresized($im, $source, $dst_x, $dst_y, 0, 0, $image_w, $image_h, $avatar_size[0], $avatar_size[1]);
    }
}

function radius_img($imgpath = './t.png', $radius = 15) {
    $ext     = pathinfo($imgpath);
    $src_img = null;
    switch ($ext['extension']) {
        case 'jpg':
            $src_img = imagecreatefromjpeg($imgpath);
            break;
        case 'png':
            $src_img = imagecreatefrompng($imgpath);
            break;
    }
    $wh = getimagesize($imgpath);
    $w  = $wh[0];
    $h  = $wh[1];
    // $radius = $radius == 0 ? (min($w, $h) / 2) : $radius;
    $img = imagecreatetruecolor($w, $h);
    //这一句一定要有
    imagesavealpha($img, true);
    //拾取一个完全透明的颜色,最后一个参数127为全透明
    $bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
    imagefill($img, 0, 0, $bg);
    $r = $radius; //圆 角半径
    for ($x = 0; $x < $w; $x++) {
        for ($y = 0; $y < $h; $y++) {
            $rgbColor = imagecolorat($src_img, $x, $y);
            if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
                //不在四角的范围内,直接画
                imagesetpixel($img, $x, $y, $rgbColor);
            } else {
                //在四角的范围内选择画
                //上左
                $y_x = $r; //圆心X坐标
                $y_y = $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                //上右
                $y_x = $w - $r; //圆心X坐标
                $y_y = $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                //下左
                $y_x = $r; //圆心X坐标
                $y_y = $h - $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
                //下右
                $y_x = $w - $r; //圆心X坐标
                $y_y = $h - $r; //圆心Y坐标
                if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
                    imagesetpixel($img, $x, $y, $rgbColor);
                }
            }
        }
    }
    return $img;
}
?>