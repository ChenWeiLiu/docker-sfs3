<?php
//查詢簡訊傳送狀態
function get_task_result($TaskID)
{
	global $MDN,$UID,$UPASS;
	$fp = fsockopen("xsms.aptg.com.tw", 80, $errno, $errstr, 30);
	if (!$fp) echo '無法連接亞太電信伺服器，查詢傳送狀態失敗！'; else
	{
		$xmlpacket ="<soap-env:Envelope xmlns:soap-env='http://schemas.xmlsoap.org/soap/envelope/'>
		<soap-env:Header/>
		<soap-env:Body>
			<Request>
			<MDN>$MDN</MDN>
			<UID>$UID</UID>
			<UPASS>$UPASS</UPASS>
			<TaskID>$TaskID</TaskID>
			</Request>
		</soap-env:Body>
		</soap-env:Envelope>";
		$contentlength = strlen($xmlpacket);
		$out = "POST /XSMSAP/api/APIQueryRequest HTTP/1.1\r\n";
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
		//$theOutput=iconv("UTF-8","Big5//IGNORE",$theOutput);
		
		//去掉多餘頭尾標頭後  將字串轉為xml物件
		$pos1=strpos($theOutput,'<Response>',1);
		$pos2=strpos($theOutput,'</Response>',1)+11;
		$theOutput=substr($theOutput,$pos1,$pos2-$pos1);
		$xml = simplexml_load_string($theOutput);
		//echo "<textarea cols=100 rows=30>$theOutput</textarea>";
	}
	return $xml;	
}

//查詢使用與剩餘點數
function get_points($ctype=0)
{
	global $MDN,$UID,$UPASS;
	$resault=array();
	$fp = fsockopen("xsms.aptg.com.tw", 80, $errno, $errstr, 30);
	if (!$fp) echo '無法連接亞太電信伺服器，查詢剩餘點數失敗！'; else
	{
		$xmlpacket ="<soap-env:Envelope xmlns:soap-env='http://schemas.xmlsoap.org/soap/envelope/'>
		<soap-env:Header/>
		<soap-env:Body>
			<Request>
			<MDN>$MDN</MDN>
			<UID>$UID</UID>
			<UPASS>$UPASS</UPASS>
			<CType>$ctype</CType>
			</Request>
		</soap-env:Body>
		</soap-env:Envelope>";
		$contentlength = strlen($xmlpacket);
		$out = "POST /XSMSAP/api/APICPointRequest HTTP/1.1\r\n";
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
		//以字串方式解析回應資料
		$key_arr=array('PointsRemain','PointsUsed');		
		foreach($key_arr as $key){
			$target="<$key>";
			$pos1=strpos($theOutput,$target,1)+strlen($target);
			$target="</$key>";
			$pos2=strpos($theOutput,$target,1);
			
			$Code=substr($theOutput,$pos1,$pos2-$pos1);
			
			$result[$key]=$Code;
		}		
	}
	return $result;	
}

?>