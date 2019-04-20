<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";

sfs_check();

//秀出網頁
head("傳送簡訊至教職員行動電話");

print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].name=='selected_teacher[]') {
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

$selected_teacher=$_POST['selected_teacher'];
$ClientID=$_POST['ClientID'];  //尚未處理, 此為避免重複發送檢查之用
$smbody_posted=$_POST['smbody'];
$room_id=$_POST['room_id'];

//建立物件
$SendGet = new SocketHttpRequest();
$usr=$m_arr['usr'];
$pwd=$m_arr['pwd'];
$sign_name=$m_arr['sign_name'];
$room_select=$m_arr['room_select'];

if($selected_teacher AND $_POST['act']==$action){	
	$smbody=str_replace("\r\n",chr(6),$smbody_posted);
	if($sign_name) $smbody.=chr(6).$my_name.' from SFS3';
	//抓取選擇的教職員
	$batch_value="";
	$pre_data="<br>◎前次發送記錄：
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' id='AutoNumber1'>
		<tr align='center' bgcolor='#ccccff'><td>NO.</td><td>簡訊序號</td><td>對象名稱</td><td>手機號碼</td><td>狀態</td></tr>";
	foreach($selected_teacher as $key=>$dstaddr_combine)
	{
		$dstaddr_combine_arr=explode("\r",stripslashes($dstaddr_combine));
		$dstaddr=$dstaddr_combine_arr[0];
		$DestName=$dstaddr_combine_arr[1];
		$i++;
		$url="http://smexpress.mitake.com.tw:9696/SmSendGet.asp?username=$usr&password=$pwd&dstaddr=$dstaddr&DestName=$DestName&dlvtime=$dlvtime&vldtime=$vldtime&smbody=$smbody";
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
		$sql="INSERT INTO sms_mitake_record SET year_seme='$curr_year_seme',msgid='{$response_array[msgid]}',statuscode='{$response_array[statuscode]}',AccountPoint='{$response_array[AccountPoint]}',Duplicate='{$response_array[Duplicate]}'";
		//foreach($response_array as $key=>$value) if($value) $sql.="$key='$value)',"; 
		$sql.=",ask_time=now(),teacher_sn=$my_sn,private=0,username='$usr',dstaddr='$dstaddr',DestName='$DestName',dlvtime='$dlvtime',smbody='$smbody',ClientID='$ClientID'";
		$recordSet=$CONN->Execute($sql) or user_error("寫入資料表記錄失敗！<br>$sql",256);
	}
	$pre_data.="</table>";
}

//顯示處室
$school_room=room_kind();
if($room_select){	
	$room_list="◎選擇處室◎<br><select name='room_id' onchange='this.form.submit()'><option value=''>*全體教職員*</option>";
	foreach($school_room as $key=>$value){
			$selected=($room_id==$key)?'selected':'';
			$room_list.="<option value=$key $selected>$value</option>";	
	}
	$room_list.="</select>";
} else {
	$room_list="<table border='2' cellpadding='6' cellspacing='0' style='border-collapse: collapse; font-size=9pt;' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr><td align='center' bgcolor='#ccccff'>選擇處室</td></tr><tr><td><input type='radio' name='room_id' value='' onclick='this.form.submit()' checked>*所有教職員工*<br>";
	foreach($school_room as $key=>$value){
			$selected=($room_id==$key)?'checked':'';
			$room_list.="<input type='radio' name='room_id' value='$key' onclick='this.form.submit()' $selected>$value<br>";	
	}
	$room_list.="</td></tr></table><br>";
}

$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'><table><tr valign='top'><td>$room_list</td><td>";

//if($class_id)
//{
	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	//抓取教師資料
	if($room_id) $room_limit=" and post_office=$room_id";
	$sql_select = "SELECT a.name,a.sex,a.cell_phone,d.title_name,b.class_num ,b.post_office
		FROM teacher_base a,teacher_post b,teacher_title d 
		where a.teacher_sn =b.teacher_sn  
		and b.teach_title_id=d.teach_title_id  
		and a.teach_condition=0 $room_limit order by class_num,post_kind,post_office";
	
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select,256);
	$col=6; //設定每一列顯示幾人
	$teacherdata="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
			<tr bgcolor='#ccffff'><td colspan=$col align='center'>
				<input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>傳送對象
			</td></tr>";
	while(list($name,$sex,$cell_phone,$title_name,$class_num,$post_office)=$recordSet->FetchRow()) {
		if($recordSet->currentrow() % $col==1) $teacherdata.="<tr align='center'>";
		$num=sprintf('%02d',$recordSet->currentrow());
		$sex_color=($sex==1)?"#CCFFCC":"#FFCCCC";
		$cell_phone=str_replace('-','',$cell_phone); //避免會輸入-做為分隔的習慣問題
		$cell_phone=intval($cell_phone);
		if(!$cell_phone) {
			$teacherdata.="<td bgcolor='#CCCCCC'>($num)$name<br><font size=2 color='gray'>[$class_num$title_name]</font></td>";
		} else {
			$cell_phone='0'.$cell_phone;
			$DestName='('.$num.')'.$class_num.$title_name.$name;
			$teacherdata.="<td bgcolor='$sex_color'><input type='checkbox' name='selected_teacher[]' value='{$cell_phone}\r{$DestName}'>($num)$name<br><font size=2 color='blue'>[$class_num$title_name][$cell_phone]</font></td>";
		}
		if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $teacherdata.="</tr>";
	}
	$col=$col-3;
	//查詢剩餘點數
	$url="http://smexpress.mitake.com.tw:9696/SmQueryGet.asp?username=$usr&password=$pwd";
	$SendGet->HttpRequest($url);
	$SendGet->sendRequest();
	$left_points=str_replace('AccountPoint=','可用點數：',$SendGet->getResponseBody());
	
	$teacherdata.="</table><br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width='100%'>
	<tr bgcolor='#ccffff'><td colspan=$col align='center'><tr align='center' bgcolor='#ccffff'><td colspan=3>使用說明</td><td colspan=$col'>簡訊內容</td></tr>
		<tr bgcolor='#ffffcc'>
		<td colspan=3>
		<font color='brown' size=2>
			<li>使用帳號：$usr ； $left_points 。</li>
			<li>每封簡訊長度：70個中文字或是160個英數字。</li>
			<li>發送前請先檢視前次發送記錄，避免重複發送</li>
			<li>發送的每封簡訊，都需要成本支出，請注意成本效益。</li>
			<li>底色顯示為灰黑色的學生，係其監護人資料未登記行動電話或登載有誤。</li>
		</font>
		</td>
		<td colspan=$col' align='center'>
		<textarea name='smbody' rows=4 cols=40>$smbody_posted</textarea>
		<input type='submit' value='$action' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#ff5555; font-size:20px; height=66'></td></tr>";
//}

echo $main.$teacherdata."<font size=2 color='green'>$pre_data</font></td></tr></table></form>";
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
            $this->iPort = 9696;
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