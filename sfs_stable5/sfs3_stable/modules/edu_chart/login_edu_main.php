<?php
//$Id: login_edu_main.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

if ($_POST[sch_id] && $_POST[login_pass]) {
	$host="140.111.34.58";
	$fp=fsockopen($host, 80, $errno, $errstr, 10);
	if(!$fp) {
		$smarty->display("edu_chart_login_edu_err.tpl");
		exit;
	} else {
		//先取出 ASP.NET_SessionId
		$url="/SchoolLogin.aspx";
		sock_put($fp,"GET",$host,$url,"",$ASP_NET_SessionID."\r\n");
		$i=0;
		while(!feof($fp)){
			$message = fgets($fp,1024);
			$d=array();
			if ($i==0)
				$d=explode("ASP.NET_SessionId=",$message);
			elseif ($i==1)
				$d=explode('name="__VIEWSTATE" value="',$message);
			else
				$d=explode("</html>",strtolower($message));
			if ($d[1]!="") {
				if ($i==0) {
					$dd=array();
					$dd=explode(";",$d[1]);
					$ASP_NET_SessionID=$dd[0];
				} elseif ($i==1) {
					$dd=array();
					$dd=explode('"',$d[1]);
					$ViewState=urlencode($dd[0]);
				}
				$i++;
				if ($i==3) break; 
			}
		}
		//填入學校代碼
		$str="__EVENTTARGET=tbscode&__EVENTARGUMENT=&__VIEWSTATE=".$ViewState."&tbscode=".$_POST[sch_id]."&tbPwd=&ddlYear=".(1911+curr_year());
		sock_put($fp,"POST",$host,$url,$host,$ASP_NET_SessionID);
		sock_put_str($fp,$str);
		while(!feof($fp)){
			$message = fgets($fp,1024);
			$d=array();
			$d=explode("</html>",strtolower($message));
			if ($d[1]!="") break;
		}
		//填入密碼並進行登入
		$str="__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=".$ViewState."&tbscode=".$_POST[sch_id]."&tbPwd=".$_POST[login_pass]."&ddlYear=".(1911+curr_year())."&Button1=%BDT%A9w";
		sock_put($fp,"POST",$host,$url,$host.$url,$ASP_NET_SessionID);
		sock_put_str($fp,$str);
		while(!feof($fp)){
			$message = fgets($fp,1024);
			$d=array();
			$d=explode("</html>",strtolower($message));
			if ($d[1]!="") break;
		}
		$url2="/School/SchoolIndex.aspx";
		sock_put($fp,"GET",$host,$url2,$host.$url,$ASP_NET_SessionID."\r\n");
		$i=0;
		$content="";
		$fp2=fopen($temp_path."url.txt","w");
		while(!feof($fp)){
			$message = fgets($fp,1024);
			$d=array();
			if ($i==0)
				$d=explode("<!",$message);
			else {
				$d=explode("</html>",strtolower($message));
				$r=explode("window.location.replace('..",$message);
				if ($r[1]!="") {
					$rr=explode("')",$r[1]);
					fwrite($fp2,"http://".$host.$rr[0]."\r\n");
				}
			}
			if ($d[1]!="")
				if ($i==0) $i=1;
			else 
			  break;
			if ($i==1) $content.=str_replace("../","http://".$host."/",$message);
		}
	}
	echo $content;
	fclose($fp);
	fclose($fp2);
	exit;
}
?>
