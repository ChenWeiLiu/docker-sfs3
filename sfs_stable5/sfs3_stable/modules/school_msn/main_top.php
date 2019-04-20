<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

//讀取近日公告 , 儲存在 $ann_data 陣列 , $ann_num 變數表公告數
$BOARD_P=$SOURCE."_p";
$BOARD_KIND=$SOURCE."_kind";
if ($IS_UTF8==0) {
 $CONN->Execute("SET NAMES 'latin1'"); 
} else {
 $CONN->Execute("SET NAMES 'utf8'"); 
}
$query="select a.* from $BOARD_P a,$BOARD_KIND b where to_days(a.b_open_date)+$LAST_DAYS > to_days(curdate()) and a.bk_id = b.bk_id order by a.b_open_date desc ,a.b_post_time desc ";
$res=$CONN->Execute($query) or die("Error! query=".$query);
$ann_num=-1;
if ($res->RecordCount()>0) {
 while ($row=$res->FetchRow()) {
  $ann_num++;
  if ($IS_UTF8==0) {
   $ann_data[$ann_num]=big52utf8($row['b_open_date']." ".$row['b_sub']." <font size=1>(".$row['b_unit']."_".$row['b_title'].")</font>");
  } else {
   $ann_data[$ann_num]=$row['b_open_date']." ".$row['b_sub']." <font size=1>(".$row['b_unit']."_".$row['b_title'].")</font>";
  }
 }
} else {
 $ann_num=0;
 $ann_data[0]="近期內無新公告!";
}


$cc[0]="#FFFFFF";
$cc[1]="#FFFF00";
$cc[2]="#00FFFF";
$cc[3]="#00FF00";

   $board_num=$ann_num+1;
?>
<html>
<head>
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<title>訊息</title>
<style>
A:link {font-size:9pt;color:#ff0000; text-decoration: none}
A:visited {font-size:9pt;color: #ff0000; text-decoration: none;}
A:hover {font-size:9pt;color: #ffff00; text-decoration: underline}
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="3" topmargin="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
 <tr>
  <td width="100%">
<div style="padding: 0px;">
  <fieldset style="line-height: 150%; margin-top: 0; margin-bottom: 0">
    <legend>首頁最新公告 -<font style="font-size:10pt;color:#FF0000">(詳細內容請瀏覽首頁公告)</font></legend>
    <div>
   <script language="JavaScript">
   	text1 = new Array(<?php echo $board_num;?>);
	<?php
	$i=0;
	for ($ii=0;$ii<$board_num;$ii++) {
		$b_sub=$ann_data[$ii];
		$t+=floor(strlen($b_sub)/20)+1;
		?>
		text1[<?php echo $i;?>]="<font color=<?php echo $cc[$i%4];?>>◆ <?php echo addslashes($b_sub);?></font>";
		<?php
		$i++;
	}
	
	//畫面延遲時間
	$delay_time=((($t/11)*15)+1)*1000;
	$delay_time=($delay_time<15000)?15000:$delay_time;
	?>
	text1[<?php echo $i;?>]="";
	var index = <?php echo $i;?>;
	document.write ("<marquee scrollamount='1' scrolldelay='60' direction= 'up' height='260' width='360' id=xiaoqing1  onmouseover=xiaoqing1.stop() onmouseout=xiaoqing1.start() bgcolor='#000000' style='color: #FFFF00'>");
	for (i=0;i<index;i++){
		document.write (text1[i] + "<br>");
	}
	document.write ("</marquee>")
	//頁面,1頁11行, 每頁時間約15秒
  setTimeout("reloading();", <?php echo $delay_time;?>);

    </script>    	
  </div>
  </fieldset>
</div> 
  </td>
 </tr>
</table>
</body>
</html>
<Script language="JavaScript">
	function reloading() {
		window.location.reload();
	}
</Script>

 