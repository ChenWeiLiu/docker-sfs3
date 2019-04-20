<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

$Allow_Read=0;
if($is_home_ip) {
	 $Allow_Read=1;
  } else {
  	if (isset($_SESSION['MSN_LOGIN_ID'])) {
     $Allow_Read=1;  		
  	}	
  }	

$cc[0]="red";
$cc[1]="blue";
$cc[2]="green";


//計算前7日公告
mysql_query("SET NAMES 'utf8'");

   $nowsec=date("U",mktime(0,0,0,date("n"),date("j"),date("Y")));
   $nowdate=date("Y-m-d 0:0:0");
   $query="select a.idnumber,a.teach_id,a.data,a.data_kind,b.name from sc_msn_data a,sc_msn_online b where a.to_id='' and to_days(curdate())<=(to_days(a.post_date)+last_date) and (a.data_kind=0 or a.data_kind=2) and a.teach_id=b.teach_id order by post_date desc";
   $result=mysql_query($query);
   $board_num=mysql_num_rows($result);
    
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
A:hover {font-size:9pt;color: #ff00ff; text-decoration: underline}
</style>
</head>
<body bgcolor="#FFFFFF" leftmargin="3" topmargin="0">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
 <tr>
  <td width="100%">
<div style="padding: 0px;">
  <fieldset style="line-height: 150%; margin-top: 0; margin-bottom: 0">
    <legend>校內訊息交流 -<font style="font-size:10pt;color:#FF0000">(僅供校內瀏覽)</font></legend>
    <div>
   <script language="JavaScript">
   <?php
 if ($Allow_Read) {
   if ($board_num>0) {	
   	  ?>
     	text1 = new Array(<?php echo $board_num;?>);
	   <?php
	  $i=0; $t=0;
	  while ($row=mysql_fetch_row($result)) {
	  	list($idnumber,$teach_id,$b_sub,$data_kind,$name)=$row;
	  	$b_sub=AddLink2Text($b_sub);
	  		  $query_file="select filename,filename_r from sc_msn_file where idnumber='".$idnumber."'";
  				$result_file=mysql_query($query_file);
	  	if (mysql_num_rows($result_file)) {
          $j=0;
          while($row_file=mysql_fetch_row($result_file)) {
          	$j++;
      	   list($filename,$filename_r)=$row_file;
	  		   $b_sub.=" (<a href=\"main_download.php?set=".$filename."\" style=\"color:#FF00FF\" title=\"".$filename_r."\">下載".$j."</a>)";
	  		  } // end while
	  	}
	  	$t+=floor(strlen($b_sub)/20)+1;
		?>
	  	text1[<?php echo $i;?>]="<font color=<?php echo $cc[$i%3];?>>◇<?php echo preg_replace("/\r\n/","<br>",addslashes($b_sub));?> <font size=2 color=#000000>(<?php echo $name;?> 提供)</font></font>";
		<?php
		  $i++;
	   }
	  }else{
      $i=1;
      $t=1;
		?>
		  text1 = new Array(1);
		  text1[0]="近日無人發佈新訊息!";
		<?php
	  } // end if $board_num>0
 } else {	  
     $i=1;
     $t=1;
		?>
		  text1 = new Array(1);
		  text1[0]="非校內網段，您必須登入才能閱讀校內訊息!";
 <?php
 }	// end if $Allow_Read  
	if ($t<5)  $t=5;
	?>
	text1[<?php echo $i;?>]="";
	var index = <?php echo $i;?>;
	document.write ("<marquee scrollamount='1' scrolldelay='60' direction= 'up' height='130' width='360' id=xiaoqing1  onmouseover=xiaoqing1.stop() onmouseout=xiaoqing1.start() bgcolor='#FFFFFF' style='color: #0000FF'>");
	for (i=0;i<index;i++){
		document.write (text1[i] + "<br>");
	}
	document.write ("</marquee>")
	//頁面,1頁5行, 每頁時間約7秒
  setTimeout("reloading();", <?php echo ((($t/5)*7)+1)*1000;?>);
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

 