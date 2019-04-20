<?php
if (!isset($_GET['text'])) die('錯誤的連結');
header("Content-type: image/png");
// Your email address which will be shown in the image
$email    =    $_GET['text'];
$length    =    (strlen($email)*8);
$im = @ImageCreate ($length, 20)
     or die ("請安裝 GD 模組");
$background_color = ImageColorAllocate ($im, 255, 255, 255); // White: 255,255,255
$text_color = ImageColorAllocate ($im, 55, 103, 200);
imagestring($im, 3,5,2,$email, $text_color);
imagepng ($im);
?>