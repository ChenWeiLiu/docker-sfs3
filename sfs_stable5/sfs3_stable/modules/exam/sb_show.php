<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<meta name="title" content="Scratch: Imagine, Program, Share"/>
<meta  name="description" content="Scratch: a programming language for everyone. Create interactive stories, games, music and art - and share it online."/>
<meta  name="author" content="Lifelong Kindergarten Group at the MIT Media Laboratory"/>
<meta  name="keywords" content="programming, youth, children, kids, novices, visual programming, animated stories, video games, interactive art, sharing, online communities, user-generated content, media lab, mit, creativity, learning, education, llk, lifelong kindergarten, beginners, beginning, easy, learn, draw, fun, program, games, animation, educators, play, playful "/>

<title>scratch 展示</title>

<style type="text/css">
<!--
.title1 {
	background-color: #FFFF99;
	width: 240px;
	height: 450px;
	float: left;
	padding:30px 20px 0px 20px;
}
.con1 {
	background-color: #FFCC00;
	width: 500px;
	height: 450px;
	float: left;
	text-align: center;
	padding-top:30px;
}
-->
</style>
</head>

<body>

<div class="con1">
Scratch 作品秀
<p align="center">
<applet code="ScratchApplet" codebase="./" name="ProjectApplet" width="482" height="387" align="middle" archive="ScratchApplet.jar" id="ProjectApplet" style="display:block">
<param name="project" value="../../../<?php echo $_GET["uu"] ?>">
</applet>
</p>
</div>

<div class="title1">
<?php
  echo $_GET["name"];
  echo " 的 ".$_GET["tn"]." 作業<br /> ";
  echo "<a href=\"".$_GET["uu"]."\">原始檔案下載</a>";
	if ($_GET["memo"]!=""){
  		echo "<br /><div><br />作業說明：<blockquote><p>";
		echo $_GET["memo"]."</p></blockquote></div>";
	}
?>
</div>

</body>
</html>
