<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();



//$action='更新傳送結果';

//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$curr_year=curr_year();
$curr_month=date("m");
$session_tea_sn=$_SESSION['session_tea_sn'];

$usr=$m_arr['usr'];
$pwd=$m_arr['pwd'];

if($_POST['act']=='更新簡訊狀態'){
	$SendGet = new SocketHttpRequest();
	$url="http://www.smsgo.com.tw/sms_gw/queryBulk.asp?username=$usr&password=$pwd&msgid=";
	//更新操作者發送的簡訊狀態
	//統計需更新數量
	$sql="SELECT DISTINCT msgid FROM sms_smsgo_record WHERE year_seme='$curr_year_seme' AND teacher_sn='$session_tea_sn' AND statusstr='OK' ORDER BY sn DESC;";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	//取得所有statusstr='OK'的msgid
	$all_msgid='';
	while(!$res->EOF) {
		$all_msgid.=$res->fields['msgid'].',';		
		$res->MoveNext();
	}
	$all_msgid=substr($all_msgid,0,-1);

	if($all_msgid){
//echo "$all_msgid<br>";
//echo $url.$all_msgid."<br>";			
		$SendGet->HttpRequest($url.$all_msgid);
		$SendGet->sendRequest();
		$response_str=$SendGet->getResponseBody();
//echo $response_str.'<br>';
		//更新資料庫
		$response_str_array=explode("\r\n",$response_str);
//print_r($response_str_array);
		foreach($response_str_array as $key=>$value){
			$value_array=explode('|',$value);
			$statuscode=$value_array[0];
			$statusstr=$value_array[1];
			$dstaddr=$value_array[2];
			$msgid=$value_array[3];			
			if($msgid and $statusstr){			
				$sql="UPDATE sms_smsgo_record SET statuscode='$statuscode',statusstr='$statusstr',donetime=NOW() WHERE msgid='$msgid' AND dstaddr='$dstaddr';";
//echo $sql.'<br>';
				$rs=$CONN->Execute($sql) or user_error("更新記錄失敗！<br>$sql",256);
			}
		}
	}

}

//秀出網頁
head("發送記錄");
print_menu($menu_p);

$year_month=$_POST['year_month'];	
//抓取發送年月
$sql="SELECT DISTINCT DATE_FORMAT(ask_time,'%Y-%m') FROM sms_smsgo_record WHERE teacher_sn='$session_tea_sn' AND year_seme='$curr_year_seme'";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$rs->EOF){
	$this_year_month=$rs->fields[0];
	$checked=($this_year_month==$year_month)?'checked':'';
	$year_month_radio.="<input type='radio' value='$this_year_month' name='year_month' $checked onclick=\"this.form.submit();\">{$year}$this_year_month ";
	$rs->MoveNext();
}
	
$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><input type='submit' name='act' value='更新簡訊狀態'> <font size=2 color='brown'>$year_month_radio<br>$msg</font>
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr align='center' bgcolor='#ffcccc'><td>No.</td><td>請求時間</td><td>使用帳號</td><td>對象名稱</td><td>手機號碼</td><td>簡訊內容</td><td>發送序號</td><td>簡訊狀態</td><td>代發狀態</td><td>狀態查詢時間</td></tr>";

	
//取得傳送記錄
$sql="SELECT * FROM sms_smsgo_record WHERE teacher_sn='$session_tea_sn' AND DATE_FORMAT(ask_time,'%Y-%m')='$year_month' ORDER BY ask_time DESC";
$recordSet=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$recordSet->EOF){
	$i++;
	$ask_time=$recordSet->fields['ask_time'];
	$username=$recordSet->fields['username'];
	$dstaddr=$recordSet->fields['dstaddr'];
	$DestName=$recordSet->fields['DestName'];
	$smbody=str_replace("\r\n",'<br>',$recordSet->fields['smbody']);
	$msgid=$recordSet->fields['msgid'];
	$donetime=$recordSet->fields['donetime'];	
	$statuscode=$recordSet->fields['statuscode'];
	$statusstr=$recordSet->fields['statusstr'];
	$statuscode_str=$statuscodeArray[$statuscode];
	$main.="<tr align='center'><td>$i</td><td>$ask_time</td><td>$username</td><td>$DestName</td><td>$dstaddr</td><td align='left'>$smbody</td><td>$msgid</td><td>$statuscode_str</td><td>$statusstr</td><td>$donetime</td></tr>";
	$recordSet->MoveNext();
}	

$main.="</table></form>";
echo $main;
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
            $this->iPort = 80;
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