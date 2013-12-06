<?php
//
// Make thumbnail from an image
//
function makeThumb($iname,$thumbw,$thumbh,$quality,$suf="_small") {
        if (substr($iname,-4,1)==".") {
                $fn=substr($iname,0,-4);
                $ext=substr($iname,-3);
        } else {
                if (substr($iname,-5,1)==".") {
                        $fn=substr($iname,0,-5);
                        $ext=substr($iname,-4);
                } else {
                        return;
                }
        }
        $ext=trim(strtolower($ext));
        $func="";
        switch ($ext) {
                case "jpg":
                case "jpeg":
                        $src_img = imagecreatefromjpeg($iname);
                        $func="imagejpeg";
                        break;
                case "png":
                        $src_img=imagecreatefrompng($iname);
                        $func="imagepng";
                        break;
                case "gif":
                        $src_img=imagecreatefromgif($iname);
                        $func="imagegif";
                        break;
                default:
                        return;
                        break;
        }
        $size = getimagesize($iname);
        if ($size[0]<$thumbw) {
                system("cp '$iname' '$fn$suf.$ext'");
        } else {
                $scale = min($thumbw/$size[0], $thumbh/$size[1]);
                $width = (int)($size[0]*$scale);
                $height = (int)($size[1]*$scale);
                $dst_img = imagecreatetruecolor($width,$height); 
                imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $width, $height, $size[0], $size[1]); 
                if (strlen($func)) {
                        $func($dst_img, "$fn$suf.$ext", $quality); 
                }
                imagedestroy($dst_img);
        }
        imagedestroy($src_img); 
}
?>
