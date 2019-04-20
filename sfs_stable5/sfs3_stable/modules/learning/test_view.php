<?php
// $Id: test_view.php 8705 2015-12-29 03:03:33Z qfon $
// --系統設定檔
include "config.php"; 
session_start();
if($_SESSION[session_log_id]==""){	
	$go_back=1; //回到自已的認證畫面  
		include "header.php";
	include $SFS_PATH."/rlogin.php";  
	exit;
}

$con=1;	//預設每次題數
$font_q="<font <font size=7 face=標楷體>";   // 題目字型
$font_c="<font <font size=6 face=標楷體>";   // 選項字型
if($key=="確定ok"){  //核對答案
	
	for($i=1;$i<=$con;$i++){	
		if($breed==1)
			$ans[$i]=implode("",$ans[$i]);
		elseif($breed==2)
			$ans[$i]=trim($ans[$i]);		
	}
	
}

if($key=="下一題>>"){	
	$qid=$qid+1;
}
if($key=="<<上一題"){	
	$qid=$qid-1;
}
$qid=intval($qid);
$sqlstr = "select * from test_data   where  qid='$qid' " ;
$result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ;
$s_unit="<form  method='post' action=$PHP_SELF >" ;
$s_unit.="　　($qid)" ;
$s_unit.="<table border='1' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='95%'  align='center'>";
while ($row = $result->FetchRow() ) {   
	$i=1; 	
//	$qid = $row["qid"] ;	
	$ques = $row["ques"] ;  
	$ch[1] = $row["ch1"] ;  
	$ch[2] = $row["ch2"] ;  
	$ch[3] = $row["ch3"] ;  
	$ch[4] = $row["ch4"] ;  
	$ch[5] = $row["ch5"] ;  
	$ch[6] = $row["ch6"] ;  
	$breed = $row["breed"] ; 
	$bre = $row["breed"] ; 
	$answer= $row["answer"] ; 
	$ques_wav= $row["ques_wav"] ; 
	$ques_up= $row["ques_up"] ; 
	$total_r= $row["total_r"] ; 
	$total_e= $row["total_e"] ; 
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
	//　對答案		
	if($key=="確定ok"){	
			
	    	if($ans[$i]==$answer){			
			$ans_c="";
			$sc=1;			
			$comment="<img src='images/right.gif' align='left'  alt='+ $sc 分'>";
		}else{ 			
			$ans_c="<br><font color=red size=3 face=新細明體>解答：$answer</font>";
			$sc=0;
			$comment="<img src='images/error.gif' align='left' alt='- $sc 分'>";	
		}
		$s_unit.="<tr><td width='60%'>$font_q $ques $ques_up_c $ques_wav_c $ans_c</font></td><td width='40%' >";
	       switch ($bre){
		case 0:
			$s_unit.="$comment <table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left'>";
			for($j=1;$j<=6;$j++){
				$myans[$j]="○";
				if($j==$ans[$i])	
					$myans[$j]="●";
				if($ch[$j]!="")	
					$s_unit.="<tr><td ><font color=blue size=5>$myans[$j]</font>$font_c  $ch[$j]</font></td></tr>";					
			}
			$s_unit.="</table>";
			break;
		case 1:
			$s_unit.="$comment<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' align='left' >";
			for($j=1;$j<=6;$j++){
				$myans[$j]="□";				
				if(substr_count($ans[$i],$j)>0)	
					$myans[$j]="■";

				if($ch[$j]!="")	
					$s_unit.="<tr><td ><font color=blue size=7>$myans[$j]</font>$font_c $ch[$j]</font></td></tr>";					
			}
			$s_unit.="</table>";
			break;				
		case 2:
			$s_unit.="$comment<font color=blue size=7 face=標楷體>$ans[$i]</font>";
			break;
		}
		
//出題
	}else{
		$s_unit.="<tr><td width='60%'>$font_q $ques $ques_up_c $ques_wav_c</font></td><td width='40%' >";
		switch ($bre){
		case 0:
			$s_unit.="<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' >";
			for($j=1;$j<=6;$j++){
				if($ch[$j]!="")	
					$s_unit.="<tr><td ><input type='radio' value=$j  name='ans[$i]'>$font_c $ch[$j]</font></td></tr>";					
			}
			$s_unit.="</table>";
			break;
		case 1:
			$s_unit.="<table border='0' cellpadding='0' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' width='100%' >";
			for($j=1;$j<=6;$j++){
				if($ch[$j]!="")	
					$s_unit.="<tr><td ><input type='checkbox'  value=$j  name='ans[$i][]'>$font_c $ch[$j]</font></td></tr>";					
			}
			$s_unit.="</table>";

			break;				
		case 2:
			$s_unit.="<input type='text' name='ans[$i]'  size='20' style='font-size: 18 pt' >";
			break;
		}
	}
	
	$s_unit.="</td></tr>";
}
if($key=="確定ok"){	
	//答對回應
	$act="繼續next";

}else{
	$act="確定ok";
}
$s_unit.="</table><br>"; 

$s_unit.="<input type='hidden' name='qid' value='$qid'>	
<input type='hidden' name='unit' value='$unit'>		
<input type='hidden' name='breed' value='$breed'>		
	<font color=#FF00FF face=標楷體 size=6>
	　$msg_c</font><input type='submit' name='key' value='$act' style='font-size: 16 pt; color: #0000FF; font-family: 標楷體'>　
<input type='submit' name='key' value='<<上一題' style='font-size: 12 pt; color: #0000FF; font-family: 標楷體'>
<input type='submit' name='key' value='下一題>>' style='font-size: 12 pt; color: #0000FF; font-family: 標楷體'>

	　$subm
	</form>"; 



//測驗網頁結束




// 網頁開始

if($unit=="")
	$back="test_admin.php";
else
	$back="test_edit.php?unit=$unit";

include "header_u.php";
?>

<table align="center" border="0" cellpadding="0" cellspacing="0" width="95%" >
  <tr>
    <td width="20%" ><a href="<?=$back ?>">回上一頁</a></td>
</tr></table>
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

