<?php
//戰鬥網頁開始

$s_unit="<form  method='post' action=$PHP_SELF >" ;	
if($my_sco==5){
	$s_id=intval($s_id);
	$sqlstr = "select * from test_score where s_id='$s_id' " ;
	$result = mysql_query($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256);
	$row= mysql_fetch_array($result);
	$type = $row["type"];
	if($type!=999){
		$exper= $exper+1;  
		$msg_c="<img src='images/win.gif'><font color=red face=標楷體 size=7>恭喜你獲勝了，繼續努力吧！</font>";
	}else{
		$exper= 0 ;  
		$msg_c="<img src='images/win.gif'><font color=red face=標楷體 size=7>請不要按上一頁！</font>";
	}
	$sql_update = "update test_score set exper='$exper',type='999' ";
	$sql_update .= " where s_id='$s_id' " ;
	mysql_query($sql_update) or die ($sql_update);
	$act="繼續next";
	$s_unit.="<input type='hidden' name='paper' value='$paper'>			
	<input type='hidden' name='score' value='$score'>
	<input type='hidden' name='breed' value='$breed'>	
	<input type='hidden' name='unit' value='$unit'>
	<input type='hidden' name='righ' value='$righ'>
	　$msg_c<input type='submit' name='key' value='$act' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>
	　$subm
	</form>"; 
		$poke_n="經驗值：<font size=5 color=red> " . $exper . " </font><font size=2> (5分以上可以進化)</font>"  ;
}elseif($he_sco==5){
	$msg_c="<img src='images/loss.gif'><font color=bule face=標楷體 size=6>很遺憾你輸了，再加油吧！</font>";
	$act="繼續next";
	$s_unit.="<input type='hidden' name='paper' value='$paper'>			
	<input type='hidden' name='score' value='$score'>
	<input type='hidden' name='breed' value='$breed'>	
	<input type='hidden' name='unit' value='$unit'>
	<input type='hidden' name='righ' value='$righ'>	
	　$msg_c<input type='submit' name='key' value='$act' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>
	　$subm
	</form>"; 

		
}else{
	 //第一次進入
	if($poke_p==""){
		$peo=rand(1,18);
		$poke_p=rand(1,251);
	}	



	$power_msg="<font size=2>(每招需消耗1分戰鬥力)</font>";

	// $poke=1;
	$poke_alt=$poke . "_" . $poke_a[$poke]['p_name'];
	$poke_gif="<img src=poke_b/$poke" . ".gif  alt=$poke_alt width=130 height=130>";
	$rou_a[1]=$poke_a[$poke]['1'];
	$rou_a[2]=$poke_a[$poke]['2'];
	$rou_a[3]=$poke_a[$poke]['3'] ;	
	$r_a[1]='╳' ;
	$r_a[2]='●';
	$r_a[3]=' □' ;	
	if($_SESSION[session_who]=='學生'){
		$yearc = substr ($_SESSION[session_log_id], 0, 2);
		$img ="photo/student/". $yearc . "/". $_SESSION[session_log_id];
	}elseif($_SESSION[session_who]=='教師'){
		$img ="photo/teacher/". $_SESSION[session_tea_sn];
	}
	$img="<img src=$UPLOAD_URL" .$img ." width=130 height=130 alt=我的照片>";  		
	if($key=="戰鬥"){
		if($he_block < ($poke_type+1)){  //被對方限制不能出某個招式
			$block_ch2=rand(1,10);
			if($block_ch2<=3){
				$he_block ++;
			}
		}
		$rough_a[1]="<input type='submit' name='key' value='剪刀╳' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體' title=$rou_a[1]>";
		$rough_a[2]="<input type='submit' name='key' value='石頭●' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體' title=$rou_a[2]>";
		$rough_a[3]="<input type='submit' name='key' value=' 布 □' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體' title=$rou_a[3]>";
		$rough="";
		for($i=1;$i<=3;$i++){
			if($i<>$block_ch2  ){
				$rough.=$rough_a[$i]."　";
			}else{
				$rough.="<font color=blue size=5>$r_a[$i] </font><font color=red size=5>已被封鎖</font>　";
			}
		}


		$block_tal=($poke_type+1)-$my_block;
		$block="可封鎖對方絕招(<font color=red>$block_tal</font>)：";

		if($my_block<($poke_type+1)){  //可限制對方不能出某個招式
			for($i=1;$i<=3;$i++){
				$block.="<input type='radio' value=$i  name='block_ch'> $r_a[$i] 　";
			}
			$block.="<input type='radio' value=0  name='block_ch'> 取消 　";

		}
	}else{

		//每招扣1分
		$total= $total - 1 ;  
		$s_id=intval($s_id);
		$sql_update = "update test_score set total='$total' ";
		$sql_update .= " where s_id='$s_id' " ;
		mysql_query($sql_update) or die ($sql_update);
		$rough="<font color=#FF00FF face=標楷體 size=6>先贏5局者獲勝喔！</font>";
		$rough.="<input type='submit' name='key' value='戰鬥' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>";
		 switch ($key){
		case '剪刀╳' :
			$my_rou=1;
			break;
		case '石頭●' :
			$my_rou=2;
			break;
		case ' 布 □' :
			$my_rou=3;
			break;
		}
		$my_rough=$rou_a[$my_rou].$r_a[$my_rou];
		
		$he_rou=rand(1,3);
		if($block_ch>0){   //封鎖對方招式
			$my_block ++;
			while ($block_ch==$he_rou){
				$he_rou=rand(1,3);
			}				
		}
		$he_rough=$poke_a[$poke_p][$he_rou].$r_a[$he_rou];	
		// 判斷勝負
		$judge=0;
		 switch ($my_rou){
			case 1:
			switch ($he_rou){
				case 2:$judge=1;break;
				case 3:$judge=2;break;
			}break;
			case 2:
			switch ($he_rou){
				case 1:$judge=2;break;
				case 3:$judge=1;break;
			}break;
			case 3:
			switch ($he_rou){
				case 1:$judge=1;break;
				case 2:$judge=2;break;
			}break;
		}
	}

	if($judge==1){
		$he_sco++;
		$he_rough ="<font  size=7 face=標楷體 color=red>$he_rough </font>";
		$my_rough="<font size=4 color=blue>$my_rough </font>";
	}
	if($judge==2){
		$my_sco++;
		$my_rough="<font  size=7 face=標楷體 color=red>$my_rough </font>";
		$he_rough="<font size=4 color=blue>$he_rough </font>";
	}

	$my_ima="";
	for($i=0;$i<$my_sco;$i++){
		$my_ima.="<img src=poke_b/ball.gif>　";
	}

	$s_unit.="<table align='center' border='0' cellpadding='0' cellspacing='0' width='95%' >
			<tr><td rowspan='3' width=300 >$img $poke_gif </td>
			<td height=30> $my_ima </td></tr>
			<tr><td> $rough </td>
			<tr><td> $block </td></tr></table>";
	$s_unit.="<table align='center' border='1' cellpadding='0' cellspacing='0' width='95%' height=100>
		<tr align='center'><td width='40%'  bgcolor='66CCFF'> $my_rough </td><td  width='20%'><font size=7  >vs</font></td><td width='40%'  bgcolor='FFFF66'> $he_rough </td></tr></table>";

	// $poke_p=1;
	$poke_alt=$poke_p . "_" . $poke_a[$poke_p]['p_name'];
	$poke_gif="<img src=poke_b/$poke_p" . ".gif  alt=$poke_alt width=130 height=130>";
		
	
	$img="<img src=poke_b/people" . $peo .".jpg alt='對手' width=130 height=130>";  
	
	$he_ima="";
	for($i=0;$i<$he_sco;$i++){
		$he_ima.="<img src=poke_b/ball.gif  align=center >　";
	}
	$s_unit.="<table align='center' border='0' cellpadding='0' cellspacing='0' width='95%' >
	<tr><td  align='right'>$he_ima  $poke_gif $img <input type='submit' name='key' value='繼續next'></td></tr>";
	$s_unit.="</table>";
	$s_unit.="<input type='hidden' name='paper' value='$paper'>	
	<input type='hidden' name='my_block' value='$my_block'>	
	<input type='hidden' name='he_block' value='$he_block'>		
	<input type='hidden' name='score' value='$score'>
	<input type='hidden' name='breed' value='$breed'>	
	<input type='hidden' name='unit' value='$unit'>
	<input type='hidden' name='righ' value='$righ'>
	<input type='hidden' name='my_sco' value='$my_sco'>
	<input type='hidden' name='he_sco' value='$he_sco'>	
	<input type='hidden' name='peo' value='$peo'>	
	<input type='hidden' name='poke_p' value='$poke_p'>				
	
	</form>"; 
}
//戰鬥網頁結束

?> 
