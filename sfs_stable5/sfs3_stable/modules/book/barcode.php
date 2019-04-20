<?php
                                                                                                                             
// $Id: barcode.php 5310 2009-01-10 07:57:56Z hami $

include "book_config.php";

$text  =  $_GET[code]; 
$barcodeheight=40; 
$barcodethinwidth=1; 
$barcodethickwidth=$barcodethinwidth*3; 
$codingmap  =  Array(
        "0"=> "000110100", "1"=> "100100001", 
        "2"=> "001100001", "3"=> "101100000", "4"=> "000110001", 
        "5"=> "100110000", "6"=> "001110000", "7"=> "000100101", 
        "8"=> "100100100", "9"=> "001100100", "A"=> "100001001", 
        "B"=> "001001001", "C"=> "101001000", "D"=> "000011001", 
        "E"=> "100011000", "F"=> "001011000", "G"=> "000001101", 
        "H"=> "100001100", "I"=> "001001100", "J"=> "000011100", 
        "K"=> "100000011", "L"=> "001000011", "M"=> "101000010", 
        "N"=> "000010011", "O"=> "100010010", "P"=> "001010010",     
        "Q"=> "000000111", "R"=> "100000110", "S"=> "001000110", 
        "T"=> "000010110", "U"=> "110000001", "V"=> "011000001", 
        "W"=> "111000000", "X"=> "010010001", "Y"=> "110010000", 
        "Z"=> "011010000", " "=> "011000100", "$"=> "010101000", 
        "%"=> "000101010", "*"=> "010010100", "+"=> "010001010", 
        "-"=> "010000101", "."=> "110000100", "/"=> "010100010"); 
    $text  =  strtoupper($text); 
    $text  =  "*$text*";  //  add  start/stop  chars.  
    $textlen  =  strlen($text); 
    $barcodewidth  =  ($textlen)*(7*$barcodethinwidth + 3*$barcodethickwidth)-$barcodethinwidth; 
    $im  =  ImageCreate($barcodewidth,$barcodeheight); 
    $black  =  ImageColorAllocate($im,0,0,0); 
    $white  =  ImageColorAllocate($im,255,255,255); 
    imagefill($im,0,0,$white); 
    $xpos=0; 
    for  ($idx=0;$idx<$textlen;$idx++)  { 
        $char  =  substr($text,$idx,1); 
        //  make  unknown  chars  a  '-';  
        if  (!isset($codingmap[$char]))  $char  =  "-"; 
        for  ($baridx=0;$baridx<=8;$baridx++)  { 
            $elementwidth  =  (substr($codingmap[$char],$baridx,1))?$barcodethickwidth: $barcodethinwidth; 
            if  (($baridx+1)%2) 
                imagefilledrectangle($im,$xpos,0,$xpos+ $elementwidth-1,$barcodeheight,$black); 
            $xpos+=$elementwidth; 
        } 
        $xpos+=$barcodethinwidth; 
    } 
if ($barcore_type == "Gif"){
	Header( "Content-type: image/gif"); 
	ImageGif($im);
}
else {
	Header( "Content-type: image/png"); 
	ImagePng($im);
}	
ImageDestroy($im); 
    return;         
?>
