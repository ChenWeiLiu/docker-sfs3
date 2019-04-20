<?php
// $Id: test_admin.php 5310 2009-01-10 07:57:56Z hami $
// --系統設定檔
include "config.php"; 
session_start();
sfs_check();
$att_time = mysql_date();
if($key == "修改設定"){
	$sql_update ="UPDATE `test_setup` SET `mat`  ='$match',`open` = '$open' ,`unit` = '$unit',`n_games` ='$n_games',`content` = '$content' ";
	mysql_query($sql_update) or die ($sql_update);	
	$key='setup';	
}


if ($key == "修改"){
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
	if($ques_up != ''){ 
		$ques_up_c="<img  src='" . $downtest_path  .$qid. "_" .$ques_up . "'>"; 
	}
	if($ques_wav != ''){
		$talk= $qid. "_" .$ques_wav;
		$ques_wav_c= "<a href=javascript:Play('$talk');><img  border=0 src='images/speak.gif'  width=22 height=18 align=middle ></a>" ;
	}


	$note= $row["note"] ; 
	$beef= $row["beef"] ; 
	$e_unit="<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>";
	$e_unit.="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
	$e_unit.="<tr><td width='60%' valign='top'>題目$qid ：<textarea name='ques' cols=50 rows=5 wrap=virtual>$ques</textarea> <br>";
	$e_unit.="解答：<input type='text' size='15' maxlength='40' name=answer  value=$answer >　　題型：<input type='text' size='3' maxlength='3' name=breed  value=$breed > 0:選擇,1:複選,2:填充<br>";
	$e_unit.="圖片檔：$ques_up_c <input type='file' size='30' maxlength='50' name='ques_up' >　<font size=2><input type=checkbox name='del_img' value='1'> 刪除圖片</font><br>";
	$e_unit.="語音檔：$ques_wav_c  <input type='file' size='30' maxlength='50' name='ques_wav' >　<font size=2><input type=checkbox name='del_wav' value='1'> 刪除語音</font>";
	$e_unit.="</td><td width='40%' valign='top'>";
	$e_unit.="<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left'>";
		for($j=1;$j<=6;$j++){	
			$e_unit.="<tr><td >選項$j ： <input type='text' size='30' maxlength='60' name=ch[$j]  value=". $ch[$j]."></td></tr>";
			//$e_unit.="<tr><td >選項$j ： <input type='text' size='30' maxlength='60' name=ch[$j]  value=". $ch[$j] ." ></td></tr>";					
		}
	$e_unit.="</table></td></tr>
		<tr><td colspan=2>備註：<textarea name='note' cols=50 rows=5 wrap=virtual>$note </textarea> 
	$beef 申訴中 <input type='submit' name='key' value='加分'>
	<input type='submit' name='key' value='無效'>
	</td></tr></table>";
	$e_unit.="　　<input type='submit' name='key' value='確定修改'>
		<input type='submit' name='key' value='取消'>　	
		<input type='hidden' name='unit' value='$unit'>
		<input type='hidden' name='qid' value='$qid'>
		<input type='hidden' name='old_up' value='$ques_up'>
		<input type='hidden' name='beef' value='$beef'>
		<input type='hidden' name='old_wav' value='$ques_wav'>";
	$e_unit.="</form>";

}
if($key == "確定修改"){
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
if($key=='加分'){
	$sqlstr = "select * from test_score   where  s_id='$beef'";
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row = mysql_fetch_array($result);
	$exper = $row["exper"]+3 ;  
	$sql_update = "update test_score set exper='$exper'  where  s_id='$beef' " ;	
	mysql_query($sql_update) or die ($sql_update);
	$sql_update = "update test_data set ques='$ques',ch1='$ch[1]',ch2='$ch[2]',ch3='$ch[3]',ch4='$ch[4]',ch5='$ch[5]',ch6='$ch[6]',up_date='$b_edit_time',answer='$answer',note='$note',breed='$breed',teacher_sn='$_SESSION[session_tea_sn]',beef='0' where  qid='$qid'";
	mysql_query($sql_update) or die ($sql_update);
}
if($key=='無效'){
	$sql_update = "update test_data set ques='$ques',ch1='$ch[1]',ch2='$ch[2]',ch3='$ch[3]',ch4='$ch[4]',ch5='$ch[5]',ch6='$ch[6]',up_date='$b_edit_time',answer='$answer',note='$note',breed='$breed',teacher_sn='$_SESSION[session_tea_sn]',beef='0' where  qid='$qid'";
	mysql_query($sql_update) or die ($sql_update);
}


//處理申訴

	$sqlstr = "select * from test_data   where  beef >0 " ;
$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
$s_unit="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
$s_unit.="<tr><td width='50%'>題目</td><td width='40%'>選項</td><td width='10%' align='center'>對/錯<br>答對率</td></tr>";

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
	$unit= $row["unit_m"] .  $row["unit_t"] .  $row["u_s"]  ; 

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

		$ans_c="<br><font color=red size=3 face=新細明體>解答：$answer</font>　<a href=$PHP_SELF?key=修改&qid=$qid&i=$i>修改</a>　<a href=test_view.php?qid=$qid>展示</a>";
	
		$s_unit.="<tr><td valign='top'>題號: $qid ($unit) $edit_c  <br> $ques $ques_up_c $ques_wav_c $ans_c</font></td><td  valign='top'>";
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


if($key=='全部清除'){
	$sql_update = "update test_online set h_who='' ,h_stud_id='',h_sid=0,h_sid1=0,h_sid2=0,h_sid3=0,h_sid4=0,h_sid5=0 ,h_name='',h_games='0' , g_who='' ,g_stud_id='',g_sid=0,g_sid1=0,g_sid2=0,g_sid3=0,g_sid4=0,g_sid5=0 ,g_name='',g_games='0' ,p_games='0' "; 	
	mysql_query($sql_update) or die ($sql_update);	
	$key='setup';	
}
if($key=='對戰設定'){
	$sqlstr = "select * from test_setup " ;
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row= mysql_fetch_array($result);
	$match = $row["mat"] ;    //競賽中
 	$open = $row["open"] ;  	//開放的道館
	$n_games = $row["n_games"] ;  //以多少決定勝負
	$content = $row["content"] ;  //每隔更新秒數
	$unit_m = $row["unit"] ;  //限制領域
      $who='學生';
      for($p=1;$p<=16;$p++){
	if($stud_h[$p]!=''){
		$stud_id=$stud_h[$p];
		//選手的神奇寶貝
		$today=date("Y-m-d");	
		$unit_t=stud_ye($stud_id)-1;
		$cond=" and online_date !='$today' ";
		if($unit_m!='')
				$cond.=" and  unit_m='$unit_m' ";
		if($unit_t!='')
				$cond.=" and  unit_t>'$unit_t' ";

		$sqlstr = "select a.*,b.unit_m,b.unit_t  from test_score a,unit_u b WHERE  a.u_id=b.u_id and stud_id= '$stud_id'  and who='$who' and poke>0 and a.total>=100 $cond order by total desc" ;
		$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
		$i=0;
		while ($row = $result->FetchRow() ) {    	
			$i++;
			if($i==1){
				$sid= $row["poke"];
				$h_name="小" . substr($row["stud_name"],2,2);
			}
			$a_sid[$i]= $row["s_id"] ;
		}
		if($i>=($n_games*2-1)){
			$sql_update = "update test_online set h_who='$who' ,h_stud_id='$stud_id',h_sid='$sid',h_sid1='$a_sid[1]',h_sid2='$a_sid[2]',h_sid3='$a_sid[3]',h_sid4='$a_sid[4]',h_sid5='$a_sid[5]' ,h_name='$h_name',h_games='0' ,h_win='0',att_time='$att_time',h_attack='1',p_games='1'   where p_sn='$p' "; 	
			mysql_query($sql_update) or die ($sql_update);	
		}else{
			$pk=$n_games*2-1;
			$msg="$stud_id 沒有 $pk 隻以上可戰鬥的神奇寶貝！(目前只有 $i 隻)";
			?>			
			<script language="JavaScript">
				alert("<?=$msg ?>")	
			</script>
			<?
		}	
	}
	if($stud_g[$p]!=''){
		$stud_id=$stud_g[$p];
		//選手的神奇寶貝
		$today=date("Y-m-d");	
		$unit_t=stud_ye($stud_id)-1;
		$cond=" and online_date !='$today' ";
		if($unit_m!='')
				$cond.=" and  unit_m='$unit_m' ";
		if($unit_t!='')
				$cond.=" and  unit_t>'$unit_t' ";

		$sqlstr = "select a.*,b.unit_m,b.unit_t  from test_score a,unit_u b WHERE  a.u_id=b.u_id and stud_id= '$stud_id'  and who='$who' and poke>0 and a.total>=100 $cond  order by total desc" ;
		$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
		$i=0;
		while ($row = $result->FetchRow() ) {    	
			$i++;
			if($i==1){
				$sid= $row["poke"];
				$h_name="小" . substr($row["stud_name"],2,2);
			}
			$a_sid[$i]= $row["s_id"] ;
		}
		if($i>=($n_games*2-1)){
			$sql_update = "update test_online set g_who='$who' ,g_stud_id='$stud_id',g_sid='$sid',g_sid1='$a_sid[1]',g_sid2='$a_sid[2]',g_sid3='$a_sid[3]',g_sid4='$a_sid[4]',g_sid5='$a_sid[5]' ,g_name='$h_name',g_games='0' ,g_win='0',att_time='$att_time',g_attack='1',p_games='1'   where p_sn='$p' "; 	
			mysql_query($sql_update) or die ($sql_update);	
		}else{
			$pk=$n_games*2-1;
			$msg="$stud_id 沒有 $pk 隻以上可戰鬥的神奇寶貝！(目前只有 $i 隻)";
			?>			
			<script language="JavaScript">
				alert("<?=$msg ?>")	
			</script>
			<?
		}	
	}

      }
	$key='setup';	
	
}
if ($key == "setup"){
	$sqlstr = "select * from test_setup " ;
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row= mysql_fetch_array($result);
	$match = $row["mat"] ;    //競賽中
 	$open = $row["open"] ;  	//開放的道館
	$n_games = $row["n_games"] ;  //以多少決定勝負
	$content = $row["content"] ;  //每隔更新秒數
	$unit = $row["unit"] ;  //限制領域
	$e_unit="<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>";
	$e_unit.="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'><tr><td>";
	$e_unit.="對戰形式：<input type='text' size='2' maxlength='2' name=match  value=$match > 0:修行中,1:競賽中　　開放的道館數：<input type='text' size='3' maxlength='3' name=open  value=$open > 預設8個，最多16個。<br>";
	$e_unit.="決勝場數：<input type='text' size='2' maxlength='2' name=n_games   value=$n_games  > 先勝 $n_games 場者贏　　更新頻率：<input type='text' size='3' maxlength='3' name=content value=$content > 建議每10秒更新
　　學習領域(代號)：<input type='text' size='3' maxlength='3' name=unit value=$unit > 預設不限制<br>";
	$e_unit.="</td></tr></table>";
	$e_unit.="　　<input type='submit' name='key' value='修改設定'>
		<input type='submit' name='key' value='取消'>";	
	$e_unit.="</form>";


	//取得各道館資料
	$sqlstr = "select * from test_online where  p_sn <= $open  " ;
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	$s_unit="<font size=5 color=red>　　設定參賽選手(請分別輸入各道館參賽選手的學號)</font>";
	$s_unit.="<table align='center'  border='0' cellpadding='3' cellspacing='3' width='100%' >";
	$s_unit.="<form action= $PHP_SELF method=post name=bform>";
	$s_unit.="<tr>";
	$t=0;	
	$now_time = mysql_date();
	while ($row = $result->FetchRow() ) {    
		$t++;		
    		$p_sn = $row["p_sn"] ;   
	    	$p_name = $row["p_name"] ;  
		$h_name=$row["h_name"] ; 
		$h_stud_id=$row["h_stud_id"] ; 
		$g_name=$row["g_name"] ; 
		$g_stud_id=$row["g_stud_id"] ; 


		$cp_name="<font size=5 face=標楷體 >$p_name</font>";
 	
		$s_unit.="<td width='25%'  align='center' valign='top'  >
		<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='98%' >
  		<tr><td width='100%' height='32'colspan='2' align='center'>$cp_name</td></tr>
  		<tr><td width='50%' height='32'align='center'>館主</td>
    		<td width='50%' height='32' align='center'>挑戰者</td></tr>
  		<tr><td width='50%' height='32' align='center'><input type='text' name='stud_h[$p_sn]' size=10 ></td>
		<td width='50%' height='32' align='center'><input type='text'  name='stud_g[$p_sn]' size=10 ></td></tr>
 		<tr><td width='50%' height='32' align='center'>$h_name</td>
		<td width='50%' height='32' align='center'>$g_name</td></tr>
  		<tr><td width='50%' height='32' align='center'>$h_stud_id</td>
		<td width='50%' height='32' align='center'>$g_stud_id</td></tr> 
  	
		</table></td>";
		if($t==4){
			$s_unit.="</tr><tr>"; 
			$t=0;
		}
	}
	$s_unit.="</tr></font></table><input type='submit' value='對戰設定' name='key'>　<input type='submit' value='全部清除' name='key'>"; 

}


// 網頁開始
include "header_u.php";
?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
  <tr>
    <td  ><a href="index.php?m=<?=$m ?>&t=<?=$t ?>">回目錄</a>　
		<a href="<?=$PHP_SELF?>">處理申訴</a>　
		<a href="<?=$PHP_SELF?>?key=setup">連線對戰管理</a>　
		<a href="test_score.php?key=stud">訓練家名單</a>　
 </td>
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

