<?php
//進化網頁開始
$s_unit="<form  method='post' action='$PHP_SELF' >" ;	
$subm="<input type='submit' name='key' value='我的神奇寶貝' >";
	// $poke_up=100;
	if($poke_up==""){
		$s_unit.="<table align='center' border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%' bgcolor='#FFFFFF'><tr>";
		$t=0;
		if($top==0 and $poke_type<5){
		for($i=1;$i<=50;$i++){
			$poke_p=50*$poke_type+$i;
			$poke_alt=$poke_p . "_" . $poke_a[$poke_p]['p_name'];
			$poke_gif="<img src=pokemon/$poke_p" . ".gif  alt=$poke_alt >" ;
			$s_unit.="<td align='center' width='10%'>$poke_gif <input type='radio' value=$poke_p  name='poke_up'></td>";
			$t++;
			if($t==10){
				$s_unit.="</tr><tr>";
				$t=0;
			}
		}
		}elseif($top==0 and $poke_type==5){
		for($i=1;$i<=251;$i++){
			$poke_p=$i;
			$poke_alt=$poke_p . "_" . $poke_a[$poke_p]['p_name'];
			$poke_gif="<img src=pokemon/$poke_p" . ".gif  alt=$poke_alt >" ;
			if($i!=25){
			$s_unit.="<td align='center' width='4%'>$poke_gif </td>";
			$t++;
			}
			if($t==25){
				$s_unit.="</tr><tr>";
				$t=0;
			}
			
		}
		}
		$s_unit.="</table>";
		$subm.="<input type='submit' name='key' value='繼續next'>";
		$act="進化";
		$msg_c="<font color=#FF00FF face=標楷體 size=6>你要進化為哪一隻神奇寶貝？</font>";
	}else{
		$poke=$poke_up;
		$top=0;
		if($old_top==5)
			$top=1;
		$u_id=intval($u_id);
		$sql_update = "update test_score set poke='$poke_up',up_date='$up_date',exper='0',top='$top'   where u_id='$u_id' and teacher_sn='$_SESSION[session_tea_sn]' ";  	
		mysql_query($sql_update) or die ($sql_update);	
		$poke_alt=$poke_up . "_" . $poke_a[$poke_up]['p_name'];
		$poke_gif="<img src=poke_b/$poke_up" . ".gif  alt=$poke_alt >";

		$act="繼續next";
		$msg_c="<font color=#FF00FF face=標楷體 size=6>你的神奇寶貝已經進化為</font>$poke_gif";

	}

$s_unit.="<input type='hidden' name='paper' value='$paper'>	
	<input type='hidden' name='old_top' value='$poke_type'>
	<input type='hidden' name='score' value='$score'>
	<input type='hidden' name='err' value='$err'>
	<input type='hidden' name='breed' value='$breed'>	
	<input type='hidden' name='unit' value='$unit'>
	<input type='hidden' name='righ' value='$righ'>				
	$msg_c<input type='submit' name='key' value='$act' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>
	　$subm
	</form>"; 

//進化網頁結束

?> 
