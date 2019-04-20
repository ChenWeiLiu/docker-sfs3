<?php
//$Id: login_edu_chart.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

if ($_GET[ReportName]!="") {
	$str="";
	while(list($k,$v)=each($_GET)) {
		if ($k=="ReportNum" || $k=="ReportName")
			$str.="&".$k."=".urlencode($v);
		else
			$str.="&".$k."=".$v;
	}
	$s=explode("140.111.34.58",$str);
	$ss=explode("&ASP_NET_SessionID=",$s[1]);
	$str=$ss[0];
	$ASP_NET_SessionID=$ss[1];
	$host="140.111.34.58";
	$fp=fsockopen($host, 80, $errno, $errstr, 10);
	if(!$fp) {
		$smarty->display("edu_chart_login_edu_err.tpl");
		exit;
	} else {
		$url="/School/SchoolIndex.aspx";
		//先取出 ASP.NET_SessionId
		sock_put($fp,"GET",$host,$str,$host.$url,$ASP_NET_SessionID);
		sock_put_str($fp,$str);
		$i=0;
		while(!feof($fp)){
			$message = fgets($fp,1024);
			$d=array();
			if ($i==0)
				$d=explode("<!",$message);
			else
				$d=explode("</html>",strtolower($message));
			if ($d[1]!="")
				if ($i==0) $i=1;
			else 
			  break;
			if ($i==1) echo str_replace("../","http://".$host."/",$message);
		}
	}
	exit;
}
?>
