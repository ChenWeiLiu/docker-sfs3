<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("傳送簡訊至學生監護人行動電話");

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

$action='啟動傳送';
//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$my_sn=$_SESSION['session_tea_sn'];
$my_name=$_SESSION['session_tea_name'];


$type_id=$_POST['type_id'];
$selected_stud=$_POST['selected_stud'];
$smbody_posted=$_POST['smbody'];

//建立物件
$SendGet = new SocketHttpRequest();
$usr=$m_arr['usr'];
$pwd=$m_arr['pwd'];
$sign_name=$m_arr['sign_name'];



if($selected_stud AND $_POST['act']==$action){
	$smbody=str_replace("\r\n",chr(6),$smbody_posted);
	if($sign_name) $smbody.=chr(6).$my_name.' from SFS3';
	//抓取選擇的班級學生
	$batch_value="";
	$pre_data="<br>◎前次發送記錄：
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1'>
		<tr align='center' bgcolor='#ccccff'><td>NO.</td><td>簡訊序號</td><td>對象名稱</td><td>手機號碼</td><td>狀態</td></tr>";
	foreach($selected_stud as $key=>$dstaddr_combine)
	{
		$dstaddr_combine_arr=explode("\r",stripslashes($dstaddr_combine));
		$dstaddr=$dstaddr_combine_arr[0];
		$DestName=$dstaddr_combine_arr[1];
		$i++;
		$url="http://smexpress.mitake.com.tw:9600/SmSendGet.asp?username=$usr&password=$pwd&dstaddr=$dstaddr&DestName=$DestName&dlvtime=$dlvtime&vldtime=$vldtime&smbody=$smbody";
		$SendGet->HttpRequest($url); //呼叫成員方法
		$SendGet->sendRequest(); //發送		
		//寫入資料庫以供查詢
		$response_str=$SendGet->getResponseBody();
		$response_str_array=explode("\r\n",$response_str);
		foreach($response_str_array as $key=>$value){
			$value_array=explode('=',$value);
			$response_array[$value_array[0]]=$value_array[1];
		}
		$status_message=$statuscodeArray[$response_array['statuscode']];
		$pre_data.="<tr align='center'><td>$i</td><td>{$response_array[msgid]}</td><td>$DestName</td><td>$dstaddr</td><td>$status_message</td></tr>";
		$sql="INSERT sms_mitake_record SET year_seme='$curr_year_seme',msgid='{$response_array[msgid]}',statuscode='{$response_array[statuscode]}',AccountPoint='{$response_array[AccountPoint]}',Duplicate='{$response_array[Duplicate]}'";
		//foreach($response_array as $key=>$value) if($value) $sql.="$key='$value)',"; 
		$sql.=",ask_time=now(),teacher_sn=$my_sn,private=0,username='$usr',dstaddr='$dstaddr',DestName='$DestName',dlvtime='$dlvtime',smbody='$smbody',ClientID='$ClientID'";
		$recordSet=$CONN->Execute($sql) or user_error("寫入資料表記錄失敗！<br>$sql",256);
	}
	$pre_data.="</table>";
}


//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
$row_count=$recordSet->recordcount();
$radio_list='';
while (list($d_id,$t_name)=$recordSet->FetchRow()) {
		$checked=($type_id==$d_id)?'checked':'';
        $radio_list.="<input type='radio' name='type_id' value='$d_id' onclick='this.form.submit()' $checked>($d_id)$t_name<br>";	
}

$type_radio="<table border='2' cellpadding='6' cellspacing='0' style='border-collapse: collapse; font-size=9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr><td align='center' bgcolor='#ccccff'>選擇身分類別</td></tr><tr><td>$radio_list</td></tr></table><br>";


$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><table><tr valign='top'><td>$type_radio</td><td>";

if($type_id){
	//取得班級列表
	$class_base=class_base();
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.stud_name,a.curr_class_num,a.stud_sex,b.guardian_name,b.guardian_hand_phone FROM stud_base a left join stud_domicile b on a.student_sn=b.student_sn WHERE a.stud_study_cond=0 and a.stud_kind like '%,$type_id,%' ORDER BY a.curr_class_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//以checkbox呈現
	$col=5; //設定每一列顯示幾人
	
	$studentdata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
				<tr bgcolor='#ccffff'><td colspan=$col align='center'>
				<input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>傳送對象
				</td></tr>";
	while(list($student_sn,$stud_name,$curr_class_num,$stud_sex,$guardian_name,$guardian_hand_phone)=$recordSet->FetchRow()) {
		if($recordSet->currentrow() % $col==1) $studentdata.="<tr align='center'>";
		$class_id=substr($curr_class_num,0,3);
		$class_name=$class_base[$class_id];
		$seme_num=sprintf('%02d',substr($curr_class_num,-2));
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFCCCC";
		$guardian_hand_phone=str_replace('-','',$guardian_hand_phone);  //避免會輸入-做為分隔的習慣問題
		$guardian_hand_phone=intval($guardian_hand_phone);
		if(!$guardian_hand_phone) {
			$studentdata.="<td bgcolor='#CCCCCC'>$class_name ($seme_num)$stud_name<br><font size=2 color='gray'>[$guardian_name]</font></td>";
		} else {
			$guardian_hand_phone='0'.$guardian_hand_phone;
			$DestName=$curr_class_num.$stud_name.'的監護人';
			$studentdata.="<td bgcolor='$stud_sex_color'>$class_name ($seme_num)$stud_name<br><font size=2 color='blue'><input type='checkbox' name='selected_stud[]' value='{$guardian_hand_phone}\r{$DestName}'>[$guardian_name][$guardian_hand_phone]</font></td>";
		}
		if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
	}
	$col=$col-3;	
	//查詢剩餘點數
	$url="http://smexpress.mitake.com.tw:9600/SmQueryGet.asp?username=$usr&password=$pwd";
	$SendGet->HttpRequest($url);
	$SendGet->sendRequest();
	$left_points=str_replace('AccountPoint=','可用點數：',$SendGet->getResponseBody());
	
	$studentdata.="</table><br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'><tr align='center' bgcolor='#ccffff'><td colspan=3>使用說明</td><td colspan=$col>簡訊內容</td></tr>
		<tr bgcolor='#ffffcc'>
		<td colspan=3>
		<font color='brown' size=2>
			<li>使用帳號：$usr ； 可用點數： $left_points 。</li>
			<li>每封簡訊長度：70個中文字或是160個英數字。</li>
			<li>發送前請先檢視前次發送記錄，避免重複發送。</li>
			<li>發送的每封簡訊，都需要成本支出，請注意成本效益。</li>
			<li>底色顯示為灰黑色的學生，係其監護人資料未登記行動電話或登載有誤。</li>
		</font>
		</td>
		<td colspan=$col' align='center'>
		<textarea name='smbody' rows=4 cols=30>$smbody_posted</textarea>
		<input type='submit' value='$action' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:16px; height=66'></td></tr></table>";
}

echo $main.$studentdata."<font size=2 color='green'>$pre_data</font></td></tr></table></form>";
foot();

class SocketHttpRequest
{
    var $sHostAdd;
    var $sUri;
    var $iPort;  
    var $sRequestHeader; 
    var $sResponse;
   
    function HttpRequest($sUrl)
    {
        $sPatternUrlPart = '/http:\/\/([a-z-\.0-9]+)(:(\d+)){0,1}(.*)/i';
        $arMatchUrlPart = array();
        preg_match($sPatternUrlPart, $sUrl, $arMatchUrlPart);
       
        $this->sHostAdd = gethostbyname($arMatchUrlPart[1]);
        if (empty($arMatchUrlPart[4]))
        {
            $this->sUri = '/';
        }
        else
        {
            $this->sUri = $arMatchUrlPart[4];
        }
        if (empty($arMatchUrlPart[3]))
        {
            $this->iPort = 9600;
        }
        else
        {
            $this->iPort = $arMatchUrlPart[3];
        }
       
        $this->addRequestHeader('Host: '.$arMatchUrlPart[1]);
        $this->addRequestHeader('Connection: Close');

    }
   
    function addRequestHeader($sHeader)
    {
        $this->sRequestHeader .= trim($sHeader)."\r\n";
    }
   
    function sendRequest($sMethod = 'GET', $sPostData = '')
    {
        $sRequest = $sMethod." ".$this->sUri." HTTP/1.1\r\n";
        $sRequest .= $this->sRequestHeader;
        if ($sMethod == 'POST')
        {
            $sRequest .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $sRequest .= "Content-Length: ".strlen($sPostData)."\r\n";
            $sRequest .= "\r\n";
            $sRequest .= $sPostData."\r\n";
        }
        $sRequest .= "\r\n";
       
        $sockHttp = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!$sockHttp)
        {
            die('socket_create() failed!');
        }
       
        $resSockHttp = socket_connect($sockHttp, $this->sHostAdd, $this->iPort);
        if (!$resSockHttp)
        {
            die('socket_connect() failed!');
        }
       
        socket_write($sockHttp, $sRequest, strlen($sRequest));
       
        $this->sResponse = '';
        while ($sRead = socket_read($sockHttp, 4096))
        {
            $this->sResponse .= $sRead;
        }
       
        socket_close($sockHttp);
    }
   
    function getResponse()
    {
        return $this->sResponse;
    }
   
    function getResponseBody()
    {
        $sPatternSeperate = '/\r\n\r\n/';
        $arMatchResponsePart = preg_split($sPatternSeperate, $this->sResponse, 2);
        return $arMatchResponsePart[1];
    }
}
?>