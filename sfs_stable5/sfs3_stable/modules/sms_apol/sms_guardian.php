<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

$action='啟動傳送';
//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$my_sn=$_SESSION['session_tea_sn'];
$my_name=$_SESSION['session_tea_name'];

$selected_MDN=$_POST['selected_MDN'];
$selected_MDN_name=$_POST['selected_MDN_name'];
$Subject=iconv("Big5","UTF-8//IGNORE",$_POST['Subject']);
$my_name=$sign_name?"\r\n by $my_name":"";
$Message=iconv("Big5","UTF-8//IGNORE",$_POST['Message'].$my_name);
$class_id=$_POST['class_id'];

if($_POST['act']==$action)
if($selected_MDN and $Subject and $Message){	
	$fp = fsockopen("xsms.aptg.com.tw", 80, $errno, $errstr, 30);
	if (!$fp) echo '無法連接亞太電信伺服器！'; else
	{
		$MDNList='';
		foreach($selected_MDN as $key=>$MSISDN) $MDNList.="<MSISDN>$MSISDN</MSISDN>";
		$xmlpacket ="<soap-env:Envelope xmlns:soap-env='http://schemas.xmlsoap.org/soap/envelope/'>
		<soap-env:Header/>
		<soap-env:Body>
			<Request>
			<MDN>$MDN</MDN>
			<UID>$UID</UID>
			<UPASS>$UPASS</UPASS>
			<Subject>$Subject</Subject>
			<Message>$Message</Message>
			<MDNList>
				$MDNList
			</MDNList>
			</Request>
		</soap-env:Body>
		</soap-env:Envelope>";
		$contentlength = strlen($xmlpacket);
		$out = "POST /XSMSAP/api/APIRTRequest HTTP/1.1\r\n";
		$out .= "Host: 210.200.64.111\r\n";
		$out .= "Connection: close\r\n";
		$out .= "Content-type: text/xml;charset=utf-8\r\n";
		$out .= "Content-length: $contentlength\r\n\r\n";
		$out .= "$xmlpacket";
		fwrite($fp, $out);
		while (!feof($fp))
		{
			$theOutput .= fgets($fp, 128);
		}
		fclose($fp);
		$theOutput=iconv("UTF-8","Big5//IGNORE",$theOutput);
		
		//以字串方式抓取回應資料
		$pos1=strpos($theOutput,'<Code>',1)+6;
		$pos2=strpos($theOutput,'</Code>',1);
		$Code=substr($theOutput,$pos1,$pos2-$pos1);
		
		$pos1=strpos($theOutput,'<TaskID>',1)+8;
		$pos2=strpos($theOutput,'</TaskID>',1);
		$TaskID=substr($theOutput,$pos1,$pos2-$pos1);
		
		$pos1=strpos($theOutput,'<RtnDateTime>',1)+13;
		$pos2=strpos($theOutput,'</RtnDateTime>',1);
		$RtnDateTime=substr($theOutput,$pos1,$pos2-$pos1);
		
		//寫入task資料表
		$TotalRec=count($selected_MDN);
		$sql="INSERT INTO sms_apol_task (year_seme,ask_time,teacher_sn,MDN,Subject,Message,Code,TaskID,TotalRec,RtnDateTime) VALUES ('$curr_year_seme',now(),'$my_sn','$MDN','".stripslashes($_POST['Subject'])."','".stripslashes($_POST['Message'])."','$Code','$TaskID','$TotalRec','$RtnDateTime');";
		$res=$CONN->Execute($sql) or die("寫入資料表記錄失敗！<br>$sql");
		
		//寫入record資料表
		$sql="INSERT INTO sms_apol_record (TaskID,MSISDN,MSISDN_Name) VALUES ";
		foreach($selected_MDN as $key=>$MSISDN){
			$my_name=stripslashes($selected_MDN_name[$MSISDN]);
			$sql.="('$TaskID','$MSISDN','$my_name'),";
		}
		$sql=substr($sql,0,-1);
		$res=$CONN->Execute($sql) or die("寫入資料表記錄失敗！<br>$sql");
		//echo "<textarea cols=100 rows=10>$theOutput</textarea>";
	}
} else echo "輸入資料不完全，請檢查 1.傳送對象 2.主旨 3.簡訊內容<br>";

//秀出網頁
head("傳送簡訊至學生監護人");

print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='selected_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;
//顯示班級
if($class_select){
	$class_base=class_base($curr_year_seme);
	$class_list="◎選擇班級◎<br><select name='class_id' onchange='this.form.submit()'><option value=''></option>";
	foreach($class_base as $key=>$value){
			$selected=($class_id==$key)?'selected':'';
			$class_list.="<option value=$key $selected>$value</option>";	
	}
	$class_list.="</select>";
} else {
	$class_base=class_base($curr_year_seme);
	$class_list="<table border='2' cellpadding='6' cellspacing='0' style='border-collapse: collapse; font-size=9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr><td align='center' bgcolor='#ccccff'>選擇班級</td></tr><tr><td>";
	foreach($class_base as $key=>$value){
			$selected=($class_id==$key)?'checked':'';
			$class_list.="<input type='radio' name='class_id' value='$key' onclick='this.form.submit()' $selected>$value<br>";	
	}
	$class_list.="</td></tr></table><br>";
}

$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><table><tr valign='top'><td>$class_list</td><td>";

if($class_id)
{
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.stud_name,a.curr_class_num,a.stud_sex,b.guardian_name,b.guardian_hand_phone FROM stud_base a left join stud_domicile b on a.student_sn=b.student_sn WHERE a.stud_study_cond=0 and a.curr_class_num like '$class_id%' ORDER BY a.curr_class_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以checkbox呈現
	$col=6; //設定每一列顯示幾人
	
	$studentdata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
				<tr bgcolor='#ccffff'><td colspan=$col align='center'>
				<input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>傳送對象
				</td></tr>";
	while(list($student_sn,$stud_name,$curr_class_num,$stud_sex,$guardian_name,$guardian_hand_phone)=$recordSet->FetchRow()) {
		if($recordSet->currentrow() % $col==1) $studentdata.="<tr align='center'>";
		$seme_num=sprintf('%02d',substr($curr_class_num,-2));
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$guardian_hand_phone=str_replace('-','',$guardian_hand_phone);  //避免會輸入-做為分隔的習慣問題
		$guardian_hand_phone=intval($guardian_hand_phone);
		if(!$guardian_hand_phone) {
			$studentdata.="<td bgcolor='#CCCCCC'>($seme_num)$stud_name<br><font size=2 color='gray'>[$guardian_name]</font></td>";
		} else {
			$guardian_hand_phone='0'.$guardian_hand_phone;
			$DestName=$curr_class_num.$stud_name.'的監護人';
			$studentdata.="<td bgcolor='$stud_sex_color'><input type='checkbox' name='selected_MDN[]' value='$guardian_hand_phone'>($seme_num)$stud_name<input type='hidden' name='selected_MDN_name[$guardian_hand_phone]' value='$DestName'><br><font size=2 color='blue'>[$guardian_name][$guardian_hand_phone]</font></td>";
		}
		if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
	}
	$col=$col-3;
	//查詢剩餘點數
	$lefted=get_points();
	
	$studentdata.="</table><br>
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr align='center' bgcolor='#ccffff'><td colspan=3>使用說明</td><td colspan=$col'>簡訊內容</td></tr>
		<tr bgcolor='#ffffcc'>
		<td colspan=3>
		<font color='brown' size=2>
			<li>企業代表號：$MDN ；使用帳號：$UID 。</li>
			<li>已發送：{$lefted['PointsUsed']} ；可發送：{$lefted['PointsRemain']} 。</li>
			<li>每封簡訊長度：70個中文字或是160個英數字。</li>
			<li>發送前請先檢視前次發送記錄，避免重複發送</li>
			<li>發送的每封簡訊，都需要成本支出，請注意成本效益。</li>
			<li>底色顯示為灰黑色的學生，係其監護人資料未登記行動電話或登載有誤。</li>
		</font>
		</td>
		<td colspan=$col' align='left'>
		主旨紀錄：<input type='text' maxlength=20 size=26 name='Subject' value='{$_POST['Subject']}'><br>傳送內容：<br>
		<textarea name='Message' rows=5 cols=40>{$_POST['Message']}</textarea>
		<input type='submit' value='$action' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:20px;'></td></tr></table>";
}

echo $main.$studentdata."<font size=2 color='green'>$pre_data</font></td></tr></table></form>";
foot();

?>