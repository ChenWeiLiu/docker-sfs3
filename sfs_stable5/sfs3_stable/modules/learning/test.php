<?php
// $Id: test.php 8705 2015-12-29 03:03:33Z qfon $
// --系統設定檔
include "config.php"; 
session_start();
if($_SESSION[session_log_id]==""){	
	$go_back=1; //回到自已的認證畫面  
		include "header.php";
	include $SFS_PATH."/rlogin.php";  
	exit;
}

$con=3;	//預設每次題數
$canon=30;	//前面幾題預設為選擇題
$pass=100;	//過關題數，可得神奇寶貝
$font_q="<font <font size=6 face=標楷體>";   // 題目字型
$font_c="<font <font size=5 face=標楷體>";   // 選項字型

// $unit 唯一傳入的單元代號
if($unit=='')
	$unit='a3121';

$m = substr ($unit, 0, 1); 
$t = substr ($unit, 1, 2); 
$u = trim (substr ($unit, 3, 4)); 

//登出
if ($_GET[logout]== "yes"){
	session_start();
	$CONN -> Execute ("update pro_user_state set pu_state=0,pu_time_over=now() where teacher_sn='{$_SESSION['session_tea_sn']}'") or user_error("更新失敗！",256);
	session_destroy();
	$_SESSION[session_log_id]="";
	$_SESSION[session_tea_name]="";
	Header("Location: index.php?m=$m&t=$t");
}

//取得各領域冊別
$sqlstr = "select * from unit_tome where  unit_m='$m' and unit_t='$t' " ;
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
$c_tome = $row["unit_tome"];
$tome_ver = $row["tome_ver"];
//取得單元名稱
$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and u_s='$u' and tome_ver='$tome_ver' and exam='1'";
$result = mysql_query($sqlstr);
$row= mysql_fetch_array($result);
$c_unit = $row["unit_name"];
$u_id = $row["u_id"];
$msg_err="";
if($u_id==""){   //無此單元
	$s_unit="<font size=7 color=red>無此單元的題庫！</font>";
}
$s_title= $modules[$m] . $c_tome .$c_unit  ; 
$c_title= "<font size=5 face=標楷體 color=#800000><b>$s_title</b> </font>";	

//if ($_SESSION[session_log_id] != ""){
//	 $logout= "<a href=\"$_SERVER[PHP_SELF]?logout=yes&unit=$unit\">登出</a>";
//}	

//取得神奇寶貝資料
$sqlstr = "select * from poke_base   " ;
$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
$i=1;
while ($row = $result->FetchRow() ) { 
	//$p_sn = $row["p_sn"] ;	
	$p_name = $row["p_name"] ; 
	$poke_a[$i]['p_name']=$p_name ;
	$poke_a[$i]['1']=$row["p_s1"] ;
	$poke_a[$i]['2']=$row["p_s2"] ; 
	$poke_a[$i]['3']=$row["p_s3"] ; 

	$i++;
}
//取得舊資料
$u_id=intval($u_id);
$sqlstr = "select * from test_score where  u_id='$u_id' and teacher_sn='$_SESSION[session_tea_sn]' " ;
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
if($row['s_id']=="" and $s_unit==""){  //新資料
	$sql_insert = "insert into test_score (u_id,stud_id,who,stud_name,teacher_sn) values ('$u_id','$_SESSION[session_log_id]','$_SESSION[session_who]','$_SESSION[session_tea_name]','$_SESSION[session_tea_sn]')";
	mysql_query($sql_insert) or die ($sql_insert); 
	$sqlstr = "select * from test_score where  u_id='$u_id' and stud_id='$_SESSION[session_log_id]' " ;
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row= mysql_fetch_array($result);
}
$total = $row["total"];
$s_id = $row["s_id"];
//$type = $row["type"];
$poke = $row["poke"];
$exper = $row["exper"];
$top = $row["top"];
$up_date = mysql_date();
$poke_type=ceil($poke/50);
//已達終極進化
if($top==1)
	$poke_type=6;

$poke_n="經驗值：<font size=5 color=red> " . $exper . " </font><font size=2> (5分以上可以進化)</font>"  ;


if( $top==1){
	$poke_n="經驗值：<font size=5 color=red> " . $exper . " </font><font size=2> (★已達終極進化)</font>"  ;
}
	
if($total>=100){
	$power_msg="<font size=2>(100分以上可以戰鬥)</font>";
}

if($key=="確定申訴" or $key=="提出申訴1" or $key=="提出申訴2" or $key=="提出申訴3" ){
	include "test_1.php"; 
}
if($key=="進化" ){
	include "test_2.php"; 
}
if($key=="戰鬥" or $key=="剪刀╳" or $key=="石頭●" or $key==" 布 □"){
	include "test_3.php"; 
} 
if($key=="我的神奇寶貝" or $key=='依編號'or $key=='依戰鬥力' or $key=='依課程'){
	include "test_4.php"; 
} 
if($key=="" or $key=="確定ok" or $key=="繼續next"){ 
	include "test_5.php"; 
} 
if($key=="我的徽章" or $key=='依序號'or $key=='依日期'){
	include "test_6.php"; 
} 

// onSelectStart="event.returnValue=false"  禁足選取

// 網頁開始
?>
<html>
<head>
<title><?=$s_title ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=big5" >
</head>
<body style="border: 10pt #808080 outset" bgcolor="CCFFCC" background="images-a/b<?=$exper?>.gif"  bgproperties="fixed"  
      ONDRAGSTART="window.event.returnValue=false" ONCONTEXTMENU="window.event.returnValue=false"  >
<script language="JavaScript">
<!--
  //if (history.length==0 ) window.location="";  //禁止直接輸入網址    OnLoad="namosw_init_animation()
  if(name!="poke") window.location="";       //禁止直接輸入網址
 
-->
</script>

<font face="Times New Roman">
<OBJECT name="Player" ID="Player" height="0" width="0"
  CLASSID="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6">
</object>
</font>

<?php

$login= "<font size=5 color='9933ff' face=標楷體>訓練家：$_SESSION[session_tea_name]</font>";
$power="戰鬥力：<font size=5 color=red> " . $total . " </font>".$power_msg ;   //即時更新成績　
if($poke>0 ){
	$poke_alt=$poke . "_" . $poke_a[$poke]['p_name'];
	$poke_gif="<img src=pokemon/$poke" . ".gif  alt=$poke_alt >" .$poke_n;
}else{
	$poke_gif="$pass 分以上就可收服神奇寶貝喔！";
}
?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
  <tr>			
    <td width="25%" ><a href=javascript:close()><font size=5 face=標楷體>離開(EXIT)</font></a></td>
    <td width="65%" align="center"><?= $c_title ?></td>
         <td width="10%" align="center"><?= $logout ?></td>
</tr></table>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
<tr> 
<td width="30%" align="right" valign="bottom"><?=$login ?>　<a href=online_con.php><font size=3 face=標楷體>修行之路</font></a></td>
<td width="30%" align="right" valign="bottom"><?=$power ?></td>
<td width="40%" align="right" valign="bottom"><?=$poke_gif ?></td>
</tr></table>
<?=$s_unit ?>
</body>
</html>



<script language="JavaScript">
function Play(mp){ 
mp="<?=$SFS_PATH_HTML?>data/test/" + mp ;
Player.URL = mp;
}	
</script>

