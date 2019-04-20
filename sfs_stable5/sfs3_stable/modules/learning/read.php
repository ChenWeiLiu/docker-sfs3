<?php
include "config.php"; 
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

session_start();
if( $size=='' ){
	$size='6';
	$spe=400;
	$bgcolor='#FFFFFF';
	$fcolor='#000000';
}else{	
	$_SESSION["siz"]=$size;
	$_SESSION["sp"]=$spe;
	$_SESSION["bgcolo"]=$bgcolor;
	$_SESSION["fcolo"]=$fcolor;
}
if($_SESSION["siz"]!=''){
	$size=$_SESSION["siz"];
	$spe=$_SESSION["sp"];
	$bgcolor=$_SESSION["bgcolo"];
	$fcolor=$_SESSION["fcolo"];
}


?>
<HTML><HEAD><TITLE></TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5"><LINK 
type=text/css rel=stylesheet>
<META content="Microsoft FrontPage 5.0" name=GENERATOR></HEAD>
<BODY >
<SCRIPT language=VBScript>
dim w,t,p,mm,c,mr,dy,tmp,vs:vs=5:dy=1:p=1:Set w = document.body

sub tp
	m=mid(t,p,1):if m="" then c=mr
	if m="`" then m="":mm=mid(mm,1,len(mm)-1)
	if m="<" then av=instr(mid(t,p),">"):m=mid(t,p,av):p=p+av-1
	if m="&" then av=instr(mid(t,p),";"):m=mid(t,p,av):p=p+av-1
	tmp=<?=$spe/4 ?>:if m="." then tmp=<?=$spe ?> else if (m="，"   or m="："  or m="；") then tmp=<?=$spe/2 ?> else if ( m="。" or  m="！" or m="？"  ) then tmp=<?=$spe ?> else if m="<br>" then tmp=800
	if w.scrollHeight-w.scrollTop>w.offsetHeight-120 then w.scrollTop=w.scrollTop+int(dy):dy=dy+dy/vs else dy=1
	mm=mm&m:w1.innerHTML=mm&c:p=p+1:if p<=len(t)+1 then SetTimeOut "tp",16+tmp else w.scroll="yes"
end sub

sub window_onload()
	w.scroll="yes"
	w.bgcolor="<?=$bgcolor ?>"
	w.style.cursor="crosshair"
	t=w0.innerHTML:tp
end sub
</SCRIPT>

<?
$d_size=array(
"4"=>"",
"5"=>"",
"6"=>"",
"7"=>"",
);
$d_size[$size]="selected";
$d_spe=array(
"100"=>"",
"200"=>"",
"400"=>"",
"600"=>"",
"800"=>"",
);
$d_spe[$spe]="selected";

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
  <option <?=$d_size['4']?>>4</option>
  <option <?=$d_size['5']?>>5</option>
  <option <?=$d_size['6']?>>6</option>
  <option <?=$d_size['7']?>>7</option>
  </select>　
速度：<select size="1" name="spe" style="font-size: 8 pt">
  <option <?=$d_spe[100] ?> value=100>最快</option>
  <option <?=$d_spe[200] ?> value=200>快</option>
  <option <?=$d_spe[400] ?> value=400>中等</option>
  <option <?=$d_spe[600] ?> value=600>慢</option>
  <option <?=$d_spe[800] ?> value=800>最慢</option>  
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




<input class="formButton" value="關閉" style="font-size: 8 pt" type="button" onclick="javascript:window.close();">
<DIV id=w0 style="DISPLAY: none">
<TABLE width="90%" align=center border=0>
<TBODY><TR><TD>
      <P><FONT color=<?=$fcolor ?> size=<?=$size ?> face="標楷體">
      		<?=nl2br($b_sub)?><br>
		<?=nl2br($b_con)?><br>
<?php
	if (substr($b_upload,-3)=='jpg' or substr($b_upload,-3)=='JPG' or substr($b_upload,-3)=='gif' or substr($b_upload,-3)=='GIF'or substr($b_upload,-3)=='png'){		
 		echo "<img  src=\"$download_path".$b_id."_".$b_upload."\">"; 
 	}
	
?>

</TD></TR></TBODY></TABLE></DIV>
<DIV id=w1></DIV>

</BODY></HTML>