<?php
//查看網頁開始

	$s_unit="<form  method='post' action=$PHP_SELF >" ;	
	// 已收集的徽章
	$sqlstr = "select * from`test_badge`  WHERE   teacher_sn= '$_SESSION[session_tea_sn]'  and who='$_SESSION[session_who]'  order by up_date desc " ;

	if($key=='依序號'){
	$sqlstr = "select * from`test_badge`  WHERE    teacher_sn= '$_SESSION[session_tea_sn]'  and who='$_SESSION[session_who]'  order by badge " ;
	}
	$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
	$s_unit.="<table align='center' border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%' ><tr>";
	$t=0;
	$poke_sum=0;
	while ($row = $result->FetchRow() ) {  
		$up_date= $row["up_date"] ;	
		$badge= $row["badge"] ;	
		$poke_alt=$badge . "_" . $poke_a[$badge]['p_name']  ;
		$poke_alt.= "　$up_date";
		$poke_gif="<img src=badge/$badge" . ".gif  alt=$poke_alt >" ;
		$s_unit.="<td align='center' width='20%'>$poke_gif </td>";
		$t++;
		$poke_sum++;
		if($t==5){
			$s_unit.="</tr><tr>";
			$t=0;
		}
	}
	for($j=5;$j>$t;$j--){
		$s_unit.="<td width='20%'><br></td>";
	}
	if($t<5){
		$s_unit.="</tr>";		
	}
	$s_unit.="</table>";	
	$subm.="<input type='submit' name='key' value='依序號' >
	<input type='submit' name='key' value='依日期' >
	<input type='submit' name='key' value='我的神奇寶貝' >


";
	$msg_c="<font color=#FF00FF face=標楷體 size=6>共收隻了 $poke_sum 個！</font>";
	
$s_unit.="<input type='hidden' name='paper' value='$paper'>			
	<input type='hidden' name='score' value='$score'>
	<input type='hidden' name='breed' value='$breed'>	
	<input type='hidden' name='unit' value='$unit'>
	<input type='hidden' name='righ' value='$righ'>				
	<font color=#FF00FF face=標楷體 size=6>
	　$msg_c</font><input type='submit' name='key' value='繼續next' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>
	$subm
	</form>"; 
//查看網頁結束
?> 
