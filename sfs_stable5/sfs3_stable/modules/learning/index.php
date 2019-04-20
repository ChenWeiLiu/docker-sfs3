<?php
// $Id: index.php 5310 2009-01-10 07:57:56Z hami $
// --系統設定檔
include "config.php"; 
session_start();

//登出
if ($_GET[logout]== "yes"){
//	session_start();
//	$CONN -> Execute ("update pro_user_state set pu_state=0,pu_time_over=now() where teacher_sn='{$_SESSION['session_tea_sn']}'") or user_error("更新失敗！",256);
	session_destroy();
	$_SESSION[session_log_id]="";
	$_SESSION[session_tea_name]="";
	Header("Location: $_SERVER[PHP_SELF]");
}
if ($_GET[logout]== "no" and $_SESSION[session_log_id] ==""){
	include $SFS_PATH."/rlogin.php";  
	exit();
}

if ($m =="")
	$m = 'a'; 
if ($t=='')
	$t=31;
// 領域名稱
if ($se=='')
	$se=substr($t,1,1);


$l_modules="｜";
foreach($modules_s as $key=>$value){
	$l_modules.="<a href=$PHP_SELF?m=$key&t=$t>$value</a>｜";
}
$c_modules=$modules[$m];
//取得各領域冊別

if($_SESSION['session_who']=='教師'){
	$testadmin="<a href=test_score.php??key=stud>訓練家名單</a>";
}

$admin="";    // 只有管理者才可進行管理

if (checkid($_SERVER[SCRIPT_FILENAME],1)){
$admin="<input type='submit'  value='管理'>";
$testadmin="<a href=test_admin.php>線上測驗管理</a>";
}
	$sqlstr = "select * from unit_tome where  unit_m='$m' and tome_ver <>''  order by seme,unit_t" ;
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	$s_tome="<table align='center'  border='0' cellpadding='3' cellspacing='3' width='95%'  >";
	$s_tome.="<form  method='post' action=tome_edit.php>
			<tr><td bgcolor='#cccccc'><font size=5 face=標楷體 color=#800000> $c_modules </font>　
			$admin </td>
			</tr><input type='hidden' name='m' value= $m ></form>";
	while ($row = $result->FetchRow() ) {    		
    		$unit_tome = $row["unit_tome"] ;    
    		$unit_t = $row["unit_t"] ;  
		$tome_ver = $row["tome_ver"] ;
		$seme = $row["seme"] ;
		$bgcolor="#ffCCFF";
		if($seme==2)
			$bgcolor="#11CCFF";
		
		$s_tome.="<tr ><td bgcolor='$bgcolor'><a href=$PHP_SELF?m=$m&t=$unit_t&se=$seme > $unit_tome : $tome_ver </a></td></tr>";
		if($unit_t==$t){
			$c_tome=$unit_tome;     //冊別
			$c_tome_ver=$tome_ver; //本學期版本
			$l_var="<a href=$PHP_SELF?m=$m&t=$t&se=$se&oth=oth>其它版</a>";
			if($oth=="oth"){
				$o_tome_ver=$tome_ver;
				$c_tome_ver="其它版";
				$l_var="<a href=$PHP_SELF?m=$m&t=$t&se=$se>$tome_ver </a>";
			}
		}

	}
	$s_tome.="</table>"; 
//取得單元名稱
	$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and tome_ver ='$c_tome_ver'  order by u_s" ;
	if($oth=="oth"){
		$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and tome_ver !='$o_tome_ver'  and tome_ver !=''  order by u_s" ;
	}
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	$s_unit="<table align='center'  border='0' cellpadding='2' cellspacing='2' width='95%'  bgcolor='#ccFFFF'>";
	$bgcolor="#ffCCFF";
		if($se==2)
			$bgcolor="#11CCFF";

	$s_unit.="<form  method='post' action=unit_edit.php>
			<tr bgcolor='$bgcolor'><td width='80%'><font size=5 face=標楷體 color=#800000> $c_tome : $c_tome_ver </font> 
			$l_var 　<a href=search.php>搜尋</a>　
		$admin $testadmin</td><td width='10%' align='center'>資料數</td><td width='10%' align='center'><a href=javascript:fullwin('online_con.php')>修行<br>之路</a></td></tr><input type='hidden' name='m' value= $m ><input type='hidden' name='t' value= $t ></form>";
	while ($row = $result->FetchRow() ) {    		
    		$unit_name = $row["unit_name"] ;    
    		$u_s = $row["u_s"] ;  		
		$u=$m.$t.$u_s;
		$total = $row["total"] ;
	
		if($total==0){
			$total="";
		}

		$exam = $row["exam"] ; 
		$exam_c="";
		if($exam==1){
			$exam_c="<a href=javascript:fullwin('test.php?unit=$u')>測驗</a>";
		}
		
		$s_unit.="<tr bgcolor='#a1c1a1'><td ><a href=etoe.php?unit=$u >  $unit_name </a></td><td align='center'>$total</td><td align='center'>$exam_c</td></tr>";
	}
	$s_unit.="</table>"; 


if ($_SESSION[session_log_id] != ""){
	$login= "歡迎 $_SESSION[session_tea_name] 登入! 　<a href=\"$_SERVER[PHP_SELF]?logout=yes&bk_id=$bk_id\">登出</a></td>";
}else{
	$login= "<a href=\"$_SERVER[PHP_SELF]?logout=no&bk_id=$bk_id\">登入</a>";
}	
	$c_title= "<font size=5 face=標楷體 color=#800000><b>$school_short_name 教學資源網 </b> </font>";	

// 網頁開始
include "header.php";

?>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
  <tr>
   <td width="15%" ><a href=<?=$HOME_URL ?>>HOME</a>　<a href='http://woa.mlc.edu.tw/index.jsp?unitid=000004' target='_blank'><img  border=0 src='pokemon/new.gif'  alt='神奇寶貝聯盟'  ></a>
</td>
    <td width="60%" align="center"> <?= $c_title ?> </td>
    <td width="30%" align="center"><?=$login ?></td>
  </tr>

      <tr>
    <td width="100%" colspan="3" > <?= $l_modules ?></td>
  </tr>

</table><p>
<table align="center"  border="0" cellpadding="0" cellspacing="0" width="95%"  >
  <tr>
    <td width="25%" valign="top"><?=$s_tome ?></td>
    <td width="75%" valign="top"><?=$s_unit ?></td>
  </tr>
</table>
</body>
</html>
<script language="JavaScript">
<!--
function fullwin(curl)
{window.open(curl,"poke","fullscreen,scrollbars")}
// -->
</script>