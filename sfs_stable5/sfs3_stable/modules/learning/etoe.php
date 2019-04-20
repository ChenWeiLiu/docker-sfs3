<?php
// $Id: etoe.php 5310 2009-01-10 07:57:56Z hami $
// --系統設定檔
include "config.php"; 
session_start();
if($_SESSION[session_log_id]==""){
	
	$go_back=1; //回到自已的認證畫面  
		include "header.php";
	include $SFS_PATH."/rlogin.php";  
	exit;
}

if ($unit ==""){
		$unit = 'a3101';
}
// 領域名稱
$m = substr ($unit, 0, 1); 
$t = substr ($unit, 1, 2); 
$u = trim (substr ($unit, 3, 4)); 

if ($entry =="")
	$entry = 'a'; 


//登出
if ($_GET[logout]== "yes"){
	session_start();
	$CONN -> Execute ("update pro_user_state set pu_state=0,pu_time_over=now() where teacher_sn='{$_SESSION['session_tea_sn']}'") or user_error("更新失敗！",256);
	session_destroy();
	$_SESSION[session_log_id]="";
	$_SESSION[session_tea_name]="";
	Header("Location: $_SERVER[PHP_SELF]?unit=$unit");
}
if ($_GET[logout]== "no" and $_SESSION[session_log_id] ==""){
//	$_SESSION[unit]=$unit;
	include $SFS_PATH."/rlogin.php";  
	exit();
}




$l_entry="｜";
foreach($entry_s as $key=>$value){
	$l_entry.="<a href=$PHP_SELF?entry=$key&unit=$unit>$value</a>｜";
}

//取得各領域冊別
$sqlstr = "select * from unit_tome where  unit_m='$m' and unit_t='$t' " ;
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
$c_tome = $row["unit_tome"];
//取得單元名稱
$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and u_s='$u'" ;
$result = mysql_query($sqlstr);
$row= mysql_fetch_array($result);
$c_unit = $row["unit_name"];
$u_id = $row["u_id"];
$exam_c="";
$exam = $row["exam"];
		if($exam==1){   // 如果有建立題庫的話 
			$exam_c="<a href=javascript:fullwin('test.php?unit=$unit')> 線上測驗 </a>";
		}

if($_SESSION[session_who]=="教師" ){   // 教師可以檢視題庫內容
		$l_entry.="<a href=test_edit.php?unit=$unit>檢視題庫</a>｜";
}



$s_title= $modules[$m] . $c_tome .$c_unit; 

if ($_SESSION[session_log_id] != ""){
	$login= "歡迎 $_SESSION[session_tea_name] 登入! 　<a href=\"$_SERVER[PHP_SELF]?logout=yes&unit=$unit\">登出</a></td>";
}else{
	$login= "<a href=\"$_SERVER[PHP_SELF]?logout=no&unit=$unit\">登入</a>";
}	
$c_title= "<font size=5 face=標楷體 color=#800000><b>$s_title</b> </font>";	

//項目標題
	$sqlstr = "select * from unit_c where  ( bk_id='$u_id' or  bk_id='$m') and b_kind='$entry' and b_days > 0 order by b_open_date desc" ;	
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	$s_unit="<form  method='post' action=board_c.php>" ;
	$s_unit.="<table align='center'  border='0' cellpadding='3' cellspacing='3' width='100%'  >";
	$s_unit.="<tr ><td bgcolor='#cccccc'><font size=5 face=標楷體 color=#800000> $entry_s[$entry] </font>";
	if($_SESSION[session_who]=="教師"){
		$s_unit.="<input type='submit'  value='新增'>";
	}

	
$s_unit.="</td></tr>";
	while ($row = $result->FetchRow() ) {    		
    		$b_sub = $row["b_sub"] ;   
    		$b_id = $row["b_id"] ;  
		$bgcolor="#ffCCFF";		
		$s_unit.="<tr ><td bgcolor='$bgcolor'><a href=$PHP_SELF?entry=$entry&unit=$unit&m_id=$b_id > $b_sub </a></td></tr>";
	}
	$s_unit.="</table>"; 

	$s_unit.="<input type='hidden' name='u_id' value= $u_id >
			<input type='hidden' name='entry' value=$entry>
			<input type='hidden' name='unit' value=$unit>
	
		<input type='hidden' name='s_title' value=$s_title-$entry_s[$entry]>

	</form>"; 


// 計算資料數
$sqlstr = "SELECT count(*)  as cou FROM `unit_c` WHERE bk_id='$u_id' and b_days > 0 and b_kind <>'' " ;
$result = mysql_query($sqlstr);
$row= mysql_fetch_array($result);
$total = $row["cou"];
$sql_update = "update unit_u set total='$total' where u_id='$u_id' ";
mysql_query($sql_update) or die ($sql_update);




//取得內容

	include "entry_show.php"; 



// 網頁開始
include "header_u.php";

?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
  <tr>
    <td width="25%" ><a href="index.php?m=<?=$m ?>&t=<?=$t ?>");">回目錄</a>　<a href="search.php?unit=<?=$unit ?>");">搜尋</a>　<?=$exam_c ?></td>
    <td width="65%" align="center"><?= $c_title ?></td>
    <td width="10%" align="right"><a href="javascript:fullwin('oyez.php');">螢幕肅靜</a></td>
  </tr></table>
  <table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" ><tr>
    <td width="70%" ><?= $l_entry ?></td>
    <td width="30%" align="right"><?=$login ?></td>
  </tr>
</table><p>
<table align="center"  border="0" cellpadding="0" cellspacing="0" width="95%"  >
  <tr>
    <td width="20%" valign="top"><?=$s_unit ?></td>
    <td width="80%" valign="top"><?=$main ?></td>
  </tr>
</table>

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

</body>
</html>
<script language="JavaScript">
<!--
function fullwin(curl)
{window.open(curl,"poke","fullscreen,scrollbars")}
// -->
</script>