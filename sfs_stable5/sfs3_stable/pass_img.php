<?php
//$Id: pass_img.php 6800 2012-06-22 07:48:38Z smallduh $

//session_start();
include "include/config.php";
$img=new pass_img();//«Ø¥ßª«¥ó
$img->show();

class pass_img {
	var $pass;//Åçµý½X
	var $height=30;//¹Ï¤ù°ª«×
	var $weight=110;//¹Ï¤ù’i«×
	var $font_file="harvey.ttf"; //from http://font101.com/	

	function pass_img() {
		$t1=range('A','Z');
		$t2=range('a','z');

		mt_srand((double)microtime()*1000000);
		$this->pass=$t1[mt_rand(0,25)].$t2[mt_rand(0,25)].sprintf("%04d",mt_rand(1,9999));

		//Åçµý½X¼g¤Jsession
		unset($_SESSION["Login_img"]);	
		//session_register("Login_img");
		$_SESSION["Login_img"]=$this->pass;
	}

	function show($font_no=0) {
		//¨ú±o³]©w­È
		$c=chk_login_img("","",2);

		$f_name = dirname(__file__)."/images/pass1.png"; //¹ÏÀÉ¦WºÙ
		if ($c['FONT_NO']==1) {
			$this->font_file="sir.ttf";
			$fs = 20; // ¦rÅé¤j¤p
			$fx = 6; //¦r¶}ÀY x ®y¼Ð
			$fy = 20; //¦r¶}ÀY y ®y¼Ð
			$xoffset = 3; //¨C­Ó¦rªº¶ZÂ÷
		} elseif ($c['FONT_NO']==2) {
			$this->font_file="epilog.ttf";
			$fs = 26; // ¦rÅé¤j¤p
			$fx = 0; //¦r¶}ÀY x ®y¼Ð
			$fy = 24; //¦r¶}ÀY y ®y¼Ð
			$xoffset = 3; //¨C­Ó¦rªº¶ZÂ÷
		} elseif ($c['FONT_NO']==3) {
			$this->font_file="hotshot.ttf";
			$fs = 20; // ¦rÅé¤j¤p
			$fx = 0; //¦r¶}ÀY x ®y¼Ð
			$fy = 22; //¦r¶}ÀY y ®y¼Ð
			$xoffset = 2; //¨C­Ó¦rªº¶ZÂ÷
		} elseif ($c['FONT_NO']==4) {
			$this->font_file="arial.ttf";
			$fs = 18; // ¦rÅé¤j¤p
			$fx = 0; //¦r¶}ÀY x ®y¼Ð
			$fy = 22; //¦r¶}ÀY y ®y¼Ð
			$xoffset = 2; //¨C­Ó¦rªº¶ZÂ÷

		} else {
			$fs = 24; // ¦rÅé¤j¤p
			$fx = -4; //¦r¶}ÀY x ®y¼Ð
			$fy = 32; //¦r¶}ÀY y ®y¼Ð
			$xoffset = 2; //¨C­Ó¦rªº¶ZÂ÷
		}
		$this->font_file="fonts/".$this->font_file;

		//²£¥Í¹Ï¤ù
		$origImg = @imagecreate($this->weight,$this->height);
		$backgroundcolor = ImageColorAllocate($origImg,255,255,255);
	
		//¼v¹³³B²z
		$font_box=array();
		for($i=0;$i<strlen($this->pass);$i++) {
			//³v¦r³B²z
			$w=substr($this->pass,$i,1);
			//¶Ã¼Æ²£¥Í¤å¦rÃC¦â
			$textcolor=ImageColorAllocate($origImg,rand(0,255*$c['COLOR']),rand(0,128*$c['COLOR']),rand(0,255*$c['COLOR']));
			//¶Ã¼Æ²£¥Í¨¤«×
			$fa=($i*$c['SLOPE']>2)?(rand(-20,-10)):-20;
			//µe¥X¤å¦r
			ImageTTFText($origImg,$fs,$fa,$fx,$fy,$textcolor,$this->font_file,$w);
			//­pºâ¤U¤@­Ó¦rªºx®y¼Ð
			$font_box=array();
			$font_box=imagettfbbox($fs,0,$this->font_file,$w);
			$fx+=$font_box[4]+$xoffset;
		}

		//¥[¤J¤zÂZ¹³¯À
		if ($c['DOT']) {
			for($i=0;$i<300;$i++)	{
				$randcolor = ImageColorallocate($origImg,rand(0,255),rand(0,255),rand(0,255));
				imagesetpixel($origImg,rand()%$this->weight,rand()%$this->height,$randcolor);
			}
		}

		// ²£¥Í³Ì²×PNG¹Ï¤ù¨Ã¥BÄÀ©ñ°O¾ÐÅé
		ImagePNG($origImg);

	//ÄÀ©ñ¥ô¦ó©M¹Ï§ÎorigImgÃöÁpªº°O¾ÐÅé
		ImageDestroy($origImg);
	}
}
?>
