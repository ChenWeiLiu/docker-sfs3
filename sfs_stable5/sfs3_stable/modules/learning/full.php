<?php
include "config.php"; 
?>
<html>
<head>
<title>佈告欄</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5" >
<style type="text/css">
<!--
A:visited
{
    COLOR: #4433aa;    
}
A:link
{
    COLOR: #4433dd;    
}
A:active
{
    COLOR: #4433dd;    
}
A:hover
{
    COLOR: #ff6666;    
}
-->
</style>
</head>

<font face="Times New Roman">
<OBJECT name="Player" ID="Player" height="0" width="0"
  CLASSID="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6">
</object>
</font>

<?php

session_start();
if( $size=='' ){
	$size='32';
	$bgcolor='#FFFFFF';
	$fcolor='#000000';
}else{	
	$_SESSION["size"]=$size;
	$_SESSION["bgcolor"]=$bgcolor;
	$_SESSION["fcolor"]=$fcolor;
}
if($_SESSION["size"]!=''){
	$size=$_SESSION["size"];
	$bgcolor=$_SESSION["bgcolor"];
	$fcolor=$_SESSION["fcolor"];
}

echo " <body bgcolor=$bgcolor >";
$b_id=intval($b_id);
$query = "select  * from unit_c  where b_id='$b_id' ";
$result = mysql_query($query);
$row= mysql_fetch_array($result);
$b_id = $row["b_id"];
$bk_id = $row["bk_id"];
$b_open_date = $row["b_open_date"];
$b_days = $row["b_days"];
$b_unit = $row["b_unit"];
$b_title = $row["b_title"];
$b_name = $row["b_name"];
$b_sub = $row["b_sub"];
$b_con = $row["b_con"];
$b_hints = $row["b_hints"];
$b_upload = $row["b_upload"];
$b_own_id = $row["b_own_id"];
$b_url = $row["b_url"];
$b_post_time = $row["b_post_time"];
$b_is_intranet = $row["b_is_intranet"];
$teacher_sn = $row["teacher_sn"];


$d_size=array(
"16"=>"",
"24"=>"",
"32"=>"",
"40"=>"",
"48"=>"",
"56"=>"",
"64"=>"",
"72"=>"",
"80"=>"",
"96"=>"",
"120"=>""
);
$d_size[$size]="selected";

$d_f=array(
"#000000"=>"", 
"#FFFFFF"=>"",
"#008000"=>"",
"#800000"=>"",
"#808000"=>"",
"#000080"=>"",
"#800080"=>"",
"#808080"=>"",
"#FFFF00"=>"",
"#00FF00"=>"",
"#00FFFF"=>"",
"#FF00FF"=>"",
"#C0C0C0"=>"",
"#FF0000"=>"",
"#0000FF"=>"", 
"#008080"=>"" );
$d_f[$fcolor]="selected";

$d_b=array(
"#000000"=>"", 
"#FFFFFF"=>"",
"#008000"=>"",
"#800000"=>"",
"#808000"=>"",
"#000080"=>"",
"#800080"=>"",
"#808080"=>"",
"#FFFF00"=>"",
"#00FF00"=>"",
"#00FFFF"=>"",
"#FF00FF"=>"",
"#C0C0C0"=>"",
"#FF0000"=>"",
"#0000FF"=>"", 
"#008080"=>"" );
$d_b[$bgcolor]="selected";


?>
<font size=2>
<form method="POST" action="<?php echo $PHP_SELF ?>">
字型大小：<select size="1" name="size" style="font-size: 8 pt">
  <option <?=$d_size['16']?>>16</option>
  <option <?=$d_size['24']?>>24</option>
  <option <?=$d_size['32']?>>32</option>
  <option <?=$d_size['40']?>>40</option>
  <option <?=$d_size['48']?>>48</option>
  <option <?=$d_size['56']?>>56</option>
  <option <?=$d_size['64']?>>64</option>
   <option <?=$d_size['72']?>>72</option>
   <option <?=$d_size['80']?>>80</option>
   <option <?=$d_size['96']?>>96</option>
   <option <?=$d_size['120']?>>120</option>


  </select>　
  字型顏色：<select size="1" name="fcolor" style="font-size: 8 pt">
<option <?=$d_f['#000000']?> value="#000000" style="color: #000000" >██黑色</option>
<option <?=$d_f['#FFFFFF']?> value="#FFFFFF" style="color: #FFFFFF">██白色</option>
<option <?=$d_f['#008000']?> value="#008000" style="color: #008000">██綠色</option>
<option <?=$d_f['#800000']?> value="#800000" style="color: #800000">██暗紅</option>
<option <?=$d_f['#808000']?> value="#808000" style="color: #808000">██深黃</option>
<option <?=$d_f['#000080']?> value="#000080" style="color: #000080">██海藍</option>
<option <?=$d_f['#800080']?> value="#800080" style="color: #800080">██紫色</option>
<option <?=$d_f['#808080']?> value="#808080" style="color: #808080">██灰色</option>
<option <?=$d_f['#FFFF00']?> value="#FFFF00" style="color: #FFFF00">██黃色</option>
<option <?=$d_f['#00FF00']?> value="#00FF00" style="color: #00FF00">██亮綠</option>
<option <?=$d_f['#00FFFF']?> value="#00FFFF" style="color: #00FFFF">██青色</option>
<option <?=$d_f['#FF00FF']?> value="#FF00FF" style="color: #FF00FF">██桃紅</option>
<option <?=$d_f['#C0C0C0']?> value="#C0C0C0" style="color: #C0C0C0">██銀色</option>
<option <?=$d_f['#FF0000']?> value="#FF0000" style="color: #FF0000">██紅色</option>
<option <?=$d_f['#0000FF']?> value="#0000FF" style="color: #0000FF">██藍色</option>
<option <?=$d_f['#008080']?> value="#008080" style="color: #008080">██藍綠</option>
  </select>　
  背景顏色：<select size="1" name="bgcolor" style="font-size: 8pt">
<option <?=$d_f['#000000']?> value="#000000" style="color: #000000" >██黑色</option>
<option <?=$d_b['#FFFFFF']?> value="#FFFFFF" style="color: #FFFFFF">██白色</option>
<option <?=$d_b['#008000']?> value="#008000" style="color: #008000">██綠色</option>
<option <?=$d_b['#800000']?> value="#800000" style="color: #800000">██暗紅</option>
<option <?=$d_b['#808000']?> value="#808000" style="color: #808000">██深黃</option>
<option <?=$d_b['#000080']?> value="#000080" style="color: #000080">██海藍</option>
<option <?=$d_b['#800080']?> value="#800080" style="color: #800080">██紫色</option>
<option <?=$d_b['#808080']?> value="#808080" style="color: #808080">██灰色</option>
<option <?=$d_b['#FFFF00']?> value="#FFFF00" style="color: #FFFF00">██黃色</option>
<option <?=$d_b['#00FF00']?> value="#00FF00" style="color: #00FF00">██亮綠</option>
<option <?=$d_b['#00FFFF']?> value="#00FFFF" style="color: #00FFFF">██青色</option>
<option <?=$d_b['#FF00FF']?> value="#FF00FF" style="color: #FF00FF">██桃紅</option>
<option <?=$d_b['#C0C0C0']?> value="#C0C0C0" style="color: #C0C0C0">██銀色</option>
<option <?=$d_b['#FF0000']?> value="#FF0000" style="color: #FF0000">██紅色</option>
<option <?=$d_b['#0000FF']?> value="#0000FF" style="color: #0000FF">██藍色</option>
<option <?=$d_b['#008080']?> value="#008080" style="color: #008080">██藍綠</option>
  </select>　

  <input type="submit" value="確定" name="B1" style="font-size: 8 pt">
  <input type="hidden" name="b_id" value="<?= $b_id ?>">
  <input type="hidden" name="n" value="<?= $n ?>">
  <input class="formButton" value="關閉" type="button" style="font-size: 8 pt" onclick="javascript:window.close();">



</form>
</font>



<table align="center" border="0" cellPadding="3" cellSpacing="0" width="95%" >
	<tr><td ><p style='line-height: 150%; font-size:<?=$size?> pt; font-family: 標楷體 ;color: <?=$fcolor ?>'>
		<?=nl2br($b_sub)?></td></tr>
	<tr><td><p style='line-height: 150%; font-size:<?=$size?> pt; font-family: 標楷體;color: <?=$fcolor ?>'>
		<?=nl2br($b_con)?></td></tr>
<?php
	$ft=substr($b_upload,-3);
	if ($ft=='jpg' or $ft=='JPG'or $ft=='gif' or $ft=='png'){		
?>
		<tr >
		<td ><?php echo "<img  src=\"$download_path".$b_id."_".$b_upload."\">"; ?></td>
		</tr>
<?php
 	}
	
	if($ft=='wav' or $ft=='WAV'or $ft=='mp3' or $ft=='MP3' or $ft=='mid' or $ft=='MID'){		
		$talk= $b_id. "_" .$b_upload;
		echo "<tr><td >
			<a href=javascript:Play('$talk');><img  border=0 src='images/speak.gif'  width=22 height=18 align=middle ></a>
		</td></tr>";

 	}
?>
</table>
</body>
<script language="JavaScript">
<!--
function fullwin(curl){
window.open(curl,'alone','fullscreen=yes,scrollbars=yes');
}
	
// -->
</script>
<script language="JavaScript">
function Play(mp){ 
mp="<?=$SFS_PATH_HTML?>data/unit/" + mp ;
Player.URL = mp;
}	

</script>


</html>
