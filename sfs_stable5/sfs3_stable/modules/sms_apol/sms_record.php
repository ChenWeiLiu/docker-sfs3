<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();


$target_id=$_POST['taskid'];
if($target_id){
	//抓取此ID的收訊者名稱
	$sql="SELECT * FROM sms_apol_record WHERE TaskID='$target_id'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	while(!$rs->EOF){
		$MSISDN=$rs->fields['MSISDN'];
		$name_arr[$MSISDN]=$rs->fields['MSISDN_Name'];
		$rs->MoveNext();
	}

	//查詢各個號碼傳送狀態
	$xml=get_task_result($target_id);	

	$RtnDateTime=$xml->RtnDateTime;
	$CreateTime=$xml->CreateTime;
	$Code=$xml->Code;
	$Reason=iconv("UTF-8","Big5//IGNORE",$xml->Reason);
	$TotalRec=$xml->TotalRec;
	$TaskStatus=iconv("UTF-8","Big5//IGNORE",$xml->TaskStatus);
	$description=$statuscodeArray[$TaskStatus];
	//更新原紀錄狀態
	$sql="UPDATE sms_apol_task SET Code='$TaskStatus',TotalRec='$TotalRec' WHERE TaskID='$target_id'";
	$rs=$CONN->Execute($sql) or user_error("更新狀態失敗！<br>$sql",256);	
	
	//進行HTML輸出
	$result="<table><tr valign='top'><td><font size=1 color='#FF8888'>■查詢時刻： $RtnDateTime<br>■交易代號： $target_id<br>■交易時刻： $CreateTime<br>■交易狀態碼： $Code $Reason<br>■發送對象數： $TotalRec<br>■傳送狀態碼： $TaskStatus $description<br></font></td>
		<td><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr align='center' bgcolor='#ffffcc'><td>No.</td><td>手機號碼</td><td>對象名稱</td><td>傳送結果</td><td>手機收到簡訊時間</td></tr>";
	$MDNList=(array)$xml->MDNList;

	if($TotalRec==1){
			$MSISDN=$MDNList[MDNUnit]->MSISDN;
			$MSISDN_NAME=$name_arr["$MSISDN"];
			$Status=$MDNList[MDNUnit]->Status;
			$DrDateTime=$MDNList[MDNUnit]->DrDateTime;
			$DrDateTime=substr($DrDateTime,0,4).'/'.substr($DrDateTime,4,2).'/'.substr($DrDateTime,6,2).' '.substr($DrDateTime,8,2).':'.substr($DrDateTime,10,2);
			$bgcolor=($Status=='99')?'#FFEEEE':'#FFFFFF';
			$result.="<tr align='center' bgcolor='$bgcolor'><td>1</td><td align='center'>$MSISDN</td><td>$MSISDN_NAME</td><td>$Status {$statuscodeArray["$Status"]}</td><td>$DrDateTime</td></tr>";
	} else {
		$i=0;
		foreach($MDNList[MDNUnit] as $MDNUnit) {
			$i++;		
			$MSISDN=$MDNUnit->MSISDN;
			$MSISDN_NAME=$name_arr["$MSISDN"];
			$Status=$MDNUnit->Status;
			$bgcolor=($Status=='99')?'#FFEEEE':'#FFFFFF';
			$DrDateTime=$MDNUnit->DrDateTime;
			$DrDateTime=substr($DrDateTime,0,4).'/'.substr($DrDateTime,4,2).'/'.substr($DrDateTime,6,2).' '.substr($DrDateTime,8,2).':'.substr($DrDateTime,10,2);
			$result.="<tr align='center' bgcolor='$bgcolor'><td>$i</td><td align='center'>$MSISDN</td><td>$MSISDN_NAME</td><td>$Status</td><td>{$statuscodeArray["$Status"]}</td><td>$DrDateTime</td></tr>";
		}
	}
	$result.="</table></td></tr></table>";
}


//$action='更新傳送結果';

//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$curr_year=curr_year();
$curr_month=date("m");
$session_tea_sn=$_SESSION['session_tea_sn'];

//秀出網頁
head("發送記錄");
print_menu($menu_p);

$year_month=$_POST['year_month'];	
//抓取發送年月
$sql="SELECT DISTINCT DATE_FORMAT(ask_time,'%Y-%m') FROM sms_apol_task WHERE teacher_sn='$session_tea_sn' AND year_seme='$curr_year_seme'";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$rs->EOF){
	$this_year_month=$rs->fields[0];
	$checked=($this_year_month==$year_month)?'checked':'';
	$year_month_radio.="<input type='radio' value='$this_year_month' name='year_month' $checked onclick=\"document.myform.taskid.value=''; this.form.submit();\">{$year}$this_year_month ";
	$rs->MoveNext();
}

$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='hidden' name='taskid' value=''><font size=2 color='brown'>$year_month_radio<br>$msg</font>
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr align='center' bgcolor='#ffcccc'><td>No.</td><td>請求時刻</td><td>企業代表號</td><td>主旨</td><td>簡訊內容</td><td>交易代號</td><td>筆數</td><td>簡訊狀態</td><td>狀態回報時刻</td></tr>";

//取得傳送記錄
$sql="SELECT * FROM sms_apol_task WHERE teacher_sn='$session_tea_sn' AND DATE_FORMAT(ask_time,'%Y-%m')='$year_month' ORDER BY RtnDateTime DESC";
$recordSet=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$recordSet->EOF){
	$i++;
	$ask_time=$recordSet->fields['ask_time'];
	$MDN=$recordSet->fields['MDN'];
	$Subject=$recordSet->fields['Subject'];
	$Message=str_replace("\r\n",'<br>',$recordSet->fields['Message']);
	$Code=$statuscodeArray[$recordSet->fields['Code']];
	$TaskID=$recordSet->fields['TaskID'];
	$TotalRec=$recordSet->fields['TotalRec'];
	$RtnDateTime=$recordSet->fields['RtnDateTime'];	
	
	$java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ffffcc';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.taskid.value=\"$TaskID\"; document.myform.submit();'";
	
	$my_num=$target_id==$TaskID?'■':$i;
	$my_color=$target_id==$TaskID?'#FF0000':'#000000';
	
	$main.="<tr align='center' $java_script  style='Color:$my_color'><td>$my_num</td><td>$ask_time</td><td>$MDN</td><td align='left'>$Subject</td><td align='left'>$Message</td><td>$TaskID</td><td>$TotalRec</td><td>$Code</td><td>$RtnDateTime</td></tr>";
	$recordSet->MoveNext();
}
$main.="</table></form><br>$result";

echo $main;
foot();

?>