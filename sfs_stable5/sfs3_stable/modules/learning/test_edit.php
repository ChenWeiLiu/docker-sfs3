<?php
// $Id: test_edit.php 8705 2015-12-29 03:03:33Z qfon $
// --系統設定檔
include "config.php"; 
session_start();
//限教師進入
if($_SESSION['session_who']!='教師'){
	exit();
}
// $unit 唯一傳入的單元代號

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
// 限管理者使用
if (checkid($_SERVER[SCRIPT_FILENAME],1)){
$del="　<a href='test_edit.php?key=del&unit=$unit'>刪除解答空白題</a>
　<a href='test_edit.php?key=clean&unit=$unit'>答對率歸0</a>";

}

//取得各領域冊別
$sqlstr = "select * from unit_tome where  unit_m='$m' and unit_t='$t' " ;
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
$c_tome = $row["unit_tome"];
$tome_ver = $row["tome_ver"];
//取得單元名稱
$sqlstr = "select * from unit_u where  unit_m='$m'  and unit_t='$t' and u_s='$u' and tome_ver='$tome_ver' ";
$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
$row= mysql_fetch_array($result);
$c_unit = $row["unit_name"];
$u_id = $row["u_id"];
$msg_err="";
if($u_id==""){   //無此單元
	$s_unit="<font size=7 color=red>無此單元的題庫！</font>";
}
$s_title= $modules[$m] . $c_tome .$c_unit  ; 
$c_title= "<font size=5 face=標楷體 color=#800000><b>$s_title</b> </font>";	

if ($_SESSION[session_log_id] != ""){
	 $logout= "<a href=\"$_SERVER[PHP_SELF]?logout=yes&unit=$unit\">登出</a>";
}	

if ($key == "stud"){
if ($topage !="")
	$page = $topage; 
$page_count=20;
if ($order =="")
	$order = "curr_class_num"; 
$page_count=20;
$u_id=intval($u_id);
$sql_select = "select  a.* ,b.u_id ,c.stud_name,c.curr_class_num,c.stud_study_cond from test_score a,unit_u b,stud_base c where ";
$sql_select .= " a.u_id='$u_id'  and  ";   
$sql_select .= " poke >0 and who='學生'  and  c.stud_study_cond='0' and ";   
$sql_select .= " a.u_id=b.u_id and a.teacher_sn = c.student_sn order by  $order"; 
$result = mysql_query ($sql_select,$conID)or die ($sql_select);
$tol_num= mysql_num_rows($result);

if ($tol_num % $page_count > 0 )
	$tolpage = intval($tol_num / $page_count)+1;
else
	$tolpage = intval($tol_num / $page_count);

$e_unit="<form action= $PHP_SELF method=get name=bform>";
$e_unit.="　　　第 <select name=topage  onchange=document.bform.submit()>";
	for ($i= 0 ; $i < $tolpage ;$i++){
		$j=$i+1;
		if ($page == $i){
			$e_unit.=" <option value=$i  selected>$j</option>";
		}else{
			$e_unit.=" <option value=$i >$j</option>";
		}
	}
		$e_unit.="</select>頁 /共 $tolpage 頁";

$sql_select = "select  a.* ,b.u_id ,c.stud_name,c.curr_class_num,c.stud_study_cond from test_score a,unit_u b,stud_base c where ";
$sql_select .= " a.u_id='$u_id'  and  ";   
$sql_select .= " poke >0 and who='學生'  and c.stud_study_cond='0' and ";   
$sql_select .= " a.u_id=b.u_id and a.teacher_sn = c.student_sn order by  $order"; 
$sql_select .= " limit ".($page * $page_count).", $page_count";
$result = mysql_query ($sql_select,$conID)or die ($sql_select);

$e_unit.="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
$e_unit.="<tr <tr  align='center'><td><a href='$PHP_SELF?key=stud&unit=$unit'>年班座號</a></td><td>姓名</td><td>神奇寶貝等級</td><td>經驗值</td><td>戰鬥力</td><td><a href='$PHP_SELF?key=stud&unit=$unit&order=up_date desc'>最新訓練時間</a></td></tr>";
$i=1;
while ($row = mysql_fetch_array($result)){
	$stud_name = $row["stud_name"];
	$curr_class_num=$row["curr_class_num"];
	$poke = $row["poke"];
	$exper = $row["exper"];
	$total = $row["total"];
	$top = $row["top"];
	$up_date = $row["up_date"];
//	$unit= $row["unit_m"]. $row["unit_t"]. $row["u_s"];
//	$unit_name= $row["unit_name"];
	$poke_type=ceil($poke/50);
	if($top==1)
		$poke_type=6;

	$e_unit.="<tr  align='center'><td>$curr_class_num </td><td> $stud_name </td><td>$poke_type ($poke) </td><td>$exper </td><td>$total </td><td>$up_date </td></tr>";

//	$i++;
}
$e_unit.="</table>
		<input type='hidden' name='key' value='$key'>
		<input type='hidden' name='unit' value='$unit'>
<input type='hidden' name='order' value='$order'>
</form>";

}else{
//答對率歸０
if ($key == "clean"){
	$sql_update = "update test_data set total_r='0', total_e='0' where u_id='$u_id' ";  //答對率	
	mysql_query($sql_update) or die ($sql_update);
}
//刪除解答空白者
if ($key == "del"){
	$sql_update = "delete  FROM test_data WHERE answer='' and u_id='$u_id' ";
	mysql_query($sql_update) or die ($sql_update);
}
if ($key == "修改"){
	$qid=intval($qid);
	$sqlstr = "select * from test_data   where  qid='$qid' " ;	
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row = mysql_fetch_array($result);
	$ques = $row["ques"] ;  
	$ch[1] = $row["ch1"] ;  
	$ch[2] = $row["ch2"] ;  
	$ch[3] = $row["ch3"] ;  
	$ch[4] = $row["ch4"] ;  
	$ch[5] = $row["ch5"] ;  
	$ch[6] = $row["ch6"] ;  
	$breed= $row["breed"] ; 
	$answer= $row["answer"] ; 
	$ques_wav= $row["ques_wav"] ; 
	$ques_up= $row["ques_up"] ; 
	$note= $row["note"] ; 
	if($ques_up != ''){ 
		$ques_up_c="<img  src='" . $downtest_path  .$qid. "_" .$ques_up . "'>"; 
	}
	if($ques_wav != ''){
		$talk= $qid. "_" .$ques_wav;
		$ques_wav_c= "<a href=javascript:Play('$talk');><img  border=0 src='images/speak.gif'  width=22 height=18 align=middle ></a>" ;
	}


	$e_unit="<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>";
	$e_unit.="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
	$e_unit.="<tr><td width='60%' valign='top'>$i. 題目 ：<textarea name='ques' cols=50 rows=5 wrap=virtual>$ques</textarea> <br>";
	$e_unit.="解答：<input type='text' size='15' maxlength='40' name=answer  value=$answer >　　題型：<input type='text' size='3' maxlength='3' name=breed  value=$breed > 0:選擇,1:複選,2:填充<br>";
	$e_unit.="圖片檔：$ques_up_c <input type='file' size='30' maxlength='50' name='ques_up' >　<font size=2><input type=checkbox name='del_img' value='1'> 刪除圖片</font><br>";
	$e_unit.="語音檔：$ques_wav_c <input type='file' size='30' maxlength='50' name='ques_wav' >　<font size=2><input type=checkbox name='del_wav' value='1'> 刪除語音</font>";
	$e_unit.="</td><td width='40%' valign='top'>";
	$e_unit.="<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left'>";
		for($j=1;$j<=6;$j++){
			$val='"'. $ch[$j] .'"';	
			$e_unit.="<tr><td >選項$j ： <input type='text' size='30' maxlength='60' name=ch[$j]  value=". $val ."></td></tr>";
			//$e_unit.="<tr><td >選項$j ： <input type='text' size='30' maxlength='60' name=ch[$j]  value=$ch[$j] ></td></tr>";	
			//$main .= "<td><input type='text' name='food".$md."[".$j."]'  size='".$INPUT_SIZE."' value=". $val ."></td>\n";				
		}
	$e_unit.="</table></td></tr>
		<tr><td colspan=2>備註：<textarea name='note' cols=80 rows=5 wrap=virtual>$note </textarea> </td></tr></table>";
	$e_unit.="　　<input type='submit' name='key' value='確定修改'>
		<input type='submit' name='key' value='取消'>　	
		<input type='hidden' name='unit' value='$unit'>
		<input type='hidden' name='qid' value='$qid'>
		<input type='hidden' name='old_up' value='$ques_up'>
		<input type='hidden' name='old_wav' value='$ques_wav'>";
	$e_unit.="</form>";

}elseif($key == "確定修改"){
	$b_edit_time = mysql_date();
	$sql_update = "update test_data set ques='$ques',ch1='$ch[1]',ch2='$ch[2]',ch3='$ch[3]',ch4='$ch[4]',ch5='$ch[5]',ch6='$ch[6]',up_date='$b_edit_time',answer='$answer',note='$note',breed='$breed',teacher_sn='$_SESSION[session_tea_sn]'";
	$b_store = $qid."_".$_FILES[ques_up][name];
	$b_old_store = $b_id."_".$old_up;
	if($del_img==1){
		$sql_update .= ", ques_up=''";
		if(file_exists($TES_DESTINATION.$b_old_store))
			unlink($TES_DESTINATION.$b_old_store);
	}elseif (is_file($_FILES[ques_up][tmp_name])){
		$sql_update .= ", ques_up='".$_FILES[ques_up][name]."' ";
		if(file_exists($TES_DESTINATION.$b_old_store))
			unlink($TES_DESTINATION.$b_old_store);
		//檢查是否上傳 php 程式檔
		if  (check_is_php_file($_FILES[ques_up][name]))
			$error_flag = true;
		else{	
			copy($_FILES[ques_up][tmp_name] , ($TES_DESTINATION.$b_store));
		}
	}
	$b_store = $qid."_".$_FILES[ques_wav][name];
	$b_old_store = $b_id."_".$old_up;
	if($del_wav==1){
		$sql_update .= ", ques_wav=''";
		if(file_exists($TES_DESTINATION.$b_old_store))
			unlink($TES_DESTINATION.$b_old_store);
	}elseif (is_file($_FILES[ques_wav][tmp_name])){
		$sql_update .= ", ques_wav='".$_FILES[ques_wav][name]."' ";
		if(file_exists($TES_DESTINATION.$b_old_store))
			unlink($TES_DESTINATION.$b_old_store);
		//檢查是否上傳 php 程式檔
		if  (check_is_php_file($_FILES[ques_wav][name]))
			$error_flag = true;
		else{	
			copy($_FILES[ques_wav][tmp_name] , ($TES_DESTINATION.$b_store));
		}
	}

	$sql_update .= " where qid='$qid' " ;
	mysql_query($sql_update) or die ($sql_update);
}
//顯示題庫
$u_id=intval($u_id);
$sqlstr = "select * from test_data   where  u_id='$u_id' order by qid " ;
$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
$s_unit="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
$s_unit.="<tr><td width='50%'>題目 (如答案為空白，則該題不會出現在測驗上)</td><td width='40%'>選項</td><td width='10%' align='center'>對/錯<br>答對率</td></tr>";

$i=0;
while ($row = $result->FetchRow() ) {    	
	$i=$i+1;
	$qid = $row["qid"] ;	
	$ques = $row["ques"] ;  
	$ch[1] = $row["ch1"] ;  
	$ch[2] = $row["ch2"] ;  
	$ch[3] = $row["ch3"] ;  
	$ch[4] = $row["ch4"] ;  
	$ch[5] = $row["ch5"] ;  
	$ch[6] = $row["ch6"] ;  
	$breed[$i] = $row["breed"] ; 
	$bre = $row["breed"] ; 
	$answer= $row["answer"] ; 
	$ques_wav= $row["ques_wav"] ; 
	$ques_up= $row["ques_up"] ; 
	$total_r= $row["total_r"] ; 
	$total_e= $row["total_e"] ; 
	$teacher_sn= $row["teacher_sn"] ; 
	$up_date= $row["up_date"] ; 
	$note= $row["note"] ; 
	$avg_r=round($total_r/($total_r+$total_e),2)*100;
	$ques_up_c="";
	$ques_wav_c="";
	if($teacher_sn>0){ 
		$edit_c="<font size=1> $teacher_sn - $up_date </font>"; 
	}

	if($ques_up != ''){ 
		$ques_up_c="<img  src='" . $downtest_path  .$qid. "_" .$ques_up . "'>"; 
	}
	if($ques_wav != ''){
		$talk= $qid. "_" .$ques_wav;
		$ques_wav_c= "<a href=javascript:Play('$talk');><img  border=0 src='images/speak.gif'  width=22 height=18 align=middle ></a>" ;
	}

		$ans_c="<br><font color=red size=3 face=新細明體>解答：$answer</font>　<a href=$PHP_SELF?key=修改&unit=$unit&qid=$qid&i=$i>修改</a>　<a href=test_view.php?qid=$qid&unit=$unit>展示</a>";
	
		$s_unit.="<tr><td valign='top'>$i. ( $qid ) $edit_c <br> $ques $ques_up_c $ques_wav_c $ans_c</font></td><td  valign='top'>";
	       switch ($bre){
		case 0:
			$s_unit.="$comment <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left'>";
			for($j=1;$j<=6;$j++){
				$myans[$j]="○";
				if($ch[$j]!="")
					$s_unit.="<tr><td ><font color=blue>$myans[$j]</font>$font_c  $ch[$j]</font></td></tr>";					
			}
			$s_unit.="</table>";
			break;
		case 1:
			$s_unit.="$comment<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left' >";
			for($j=1;$j<=6;$j++){
				$myans[$j]="□";				
				if($ch[$j]!="")	
					$s_unit.="<tr><td ><font color=blue >$myans[$j]</font>$font_c $ch[$j]</font></td></tr>";					
			}
			$s_unit.="</table>";
			break;				
		case 2:
			$s_unit.="$comment<font color=blue size=5 face=標楷體></font>";
			break;
		}
		$bgcolor="";
	
		if($avg_r<90)
			$bgcolor="yellow";
		if($avg_r<50)
			$bgcolor="red";
	$s_unit.="</td ><td align='center' valign='top' bgcolor='$bgcolor' >$total_r / $total_e <br>$avg_r ％</td></tr>";
		$s_unit.="<tr><td colspan=3 bgcolor=CCCCFF>$note </td></tr>";

}
$s_unit.="</table>"; 

}


// 網頁開始
include "header_u.php";


?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
  <tr> <td width="60%" align="center"><?= $c_title ?></td><tr>
<tr>
    <td width="30%" ><a href="etoe.php?unit=<?=$unit ?>" >回教學資源</a>　
		<a href="test_edit.php?unit=<?=$unit ?>">檢視題庫</a>　
		<a href="test_up.php?unit=<?=$unit ?>">上傳(新增)題庫</a>　
		<a href="test_edit.php?key=stud&unit=<?=$unit?>">訓練家名單</a>　	
		<a href="act_test_data.php?unit=<?=$unit?>">匯出題庫</a>
		<?=$del ?>
         <td width="10%" align="center"><?= $logout ?></td>
</tr></table>
<?=$e_unit ?>
<?=$s_unit ?>
</body>
</html>



<script language="JavaScript">
<!--
function fullwin(curl){
window.open(curl,'alone','fullscreen=yes,scrollbars=yes');
}
	
// -->
</script>
<script language="JavaScript">
function Play(mp){ 
mp="<?=$SFS_PATH_HTML?>data/test/" + mp ;
Player.URL = mp;
}	

</script>

