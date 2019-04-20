<?php
//申訴網頁開始
$s_unit="<form  method='post' action='$PHP_SELF'>" ;	
if( $key=="提出申訴1" or $key=="提出申訴2" or $key=="提出申訴3" ){
	$qid=$q_id[substr($key,8,1)];
	$sqlstr = "select * from test_data   where  qid =$qid " ;
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row= mysql_fetch_array($result);
	$s_unit.="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
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
	$note=$row["note"] ; 
	$beef=$row["beef"] ;
	$ques_up_c="";
	$ques_wav_c="";
	if($ques_up != ''){ 
		//if (substr($ques_up,-3)=='jpg' or substr($ques_up,-3)=='JPG'or substr($ques_up,-3)=='gif' or substr($ques_up,-3)=='png'){		
		$ques_up_c="<img  src='" . $downtest_path  .$qid. "_" .$ques_up . "'>";
	}
	if($ques_wav != ''){
		//if(substr($b_upload,-3)=='wav' or substr($b_upload,-3)=='WAV'or substr($b_upload,-3)=='mp3' or substr($b_upload,-3)=='MP3' or substr($b_upload,-3)=='mid' or substr($b_upload,-3)=='MID'){		
		$talk= $qid. "_" .$ques_wav;
		$ques_wav_c= "<a href=javascript:Play('$talk');><img  border=0 src='images/speak.gif'  width=22 height=18 align=middle ></a>" ;
	}
	$s_unit.="<tr><td width='60%'>$font_q $ques $ques_up_c $ques_wav_c </font></td><td width='40%' valign='top'>";
	switch ($bre){
	case 0:
		$s_unit.="$comment <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left'>";
		for($j=1;$j<=6;$j++){
			$myans[$j]="○";
			if($j==$answer)	
				$myans[$j]="●";
			if($ch[$j]!="")	
				$s_unit.="<tr><td ><font color=blue>$myans[$j]</font>$font_c  $ch[$j]</font></td></tr>";					
		}
		$s_unit.="</table>";
		break;
	case 1:
		$s_unit.="$comment<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left' >";
		for($j=1;$j<=6;$j++){
			$myans[$j]="□";				
			if(substr_count($answer,$j)>0)	
				$myans[$j]="■";
				if($ch[$j]!="")	
				$s_unit.="<tr><td ><font color=blue >$myans[$j]</font>$font_c $ch[$j]</font></td></tr>";					
		}
		$s_unit.="</table>";
		break;				
	case 2:
		$s_unit.="$comment<font color=blue size=5 face=標楷體>$answer </font>";
		break;
	}
	$s_unit.="</td></tr><tr><td colspan=2>$note </td></tr></table>";
	$s_unit.="申訴說明：<textarea rows=4 name=b_con  cols=50></textarea><br>";
	$subm.="<input type='submit' name='key' value='繼續next'>";
	$act="確定申訴";
	$msg_c="<font color=#FF00FF face=標楷體 size=5>每提一題申訴需消耗10分戰鬥力。<br>如果審核後確實需要訂正，可增加 3 分經驗值！<br></font>";

}else{
	if($b_con!=""){
		
			$b_post_time = mysql_date();	
			$note=$note. $_SESSION[session_who] . $_SESSION[session_log_id] . "於 $b_post_time 說道：" .  $b_con  . "<br>" ;
			$sql_update = "update test_data set note='$note' , beef='$s_id'   where qid='$qid' " ;
			mysql_query($sql_update) or die ($sql_update);
			$msg_c="<font color=#FF00FF face=標楷體 size=6>謝謝你寶貴的意見！</font>";

		if($beef==0){	
			$total=$total - 10 ;
			$sql_update = "update test_score set total='$total' where s_id='$s_id' " ;
			mysql_query($sql_update) or die ($sql_update);
		}else{
			$msg_c.="<font color=#FF00FF face=標楷體 size=6><br>本題已有人先提出申訴了！</font>";
		}
	}else{
		$msg_c="<font color=#FF00FF face=標楷體 size=6>如果發現問題要提出來喔！</font>";
	}
	$act="繼續next";
}

$s_unit.="<input type='hidden' name='paper' value='$paper'>			
	<input type='hidden' name='score' value='$score'>
	<input type='hidden' name='qid' value='$qid'>
	<input type='hidden' name='err' value='$err'>
	<input type='hidden' name='breed' value='$breed'>	
	<input type='hidden' name='unit' value='$unit'>
	<input type='hidden' name='righ' value='$righ'>	
	<input type='hidden' name='qid' value='$qid'>	
	<input type='hidden' name='note' value='$note'>		
	<input type='hidden' name='beef' value='$beef'>				
	$msg_c<input type='submit' name='key' value='$act' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>
	　$subm
	</form>"; 

//申訴網頁結束
?>
