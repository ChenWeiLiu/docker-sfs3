<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

	//設定上傳圖片路徑
	$img_path = "photo/teacher";

?>
<head>
	 <title>校園MSN-教師教學網站列表</title>
</head>
<?php
if (!isset($_SESSION['MSN_LOGIN_ID'])) {
  echo "<Script language=\"JavaScript\">window.close();</Script>";
	exit();
}


//列出教職員id
// ====================================================================
$Subject_KIND=array("語文_國文","語文_英文","數學","自然與生活科技_理化","自然與生活科技_生物","自然與生活科技_地球科學","自然與生活科技_資訊","社會_地理","社會_歷史","社會_公民","健康與體育_健康","健康與體育_體育","藝術與人文_聽覺藝術","藝術與人文_視覺藝術","藝術與人文_表演藝術","綜合","特教");
foreach ($Subject_KIND as $k=>$kind) {
 $i=0; //紀錄本類別人數
 $master_subjects=iconv("UTF-8", "big5",$kind);
 $query="select teacher_sn,teach_id,name from teacher_base where master_subjects like '%".$master_subjects."%' and teach_condition=0 order by name";
 $result=$CONN->Execute($query);
 ?>
 <table border="0" width="700">
   <tr>
     <td style="color:#800000">領域-科別：<?php echo $kind;?></td>
   </tr>
 </table>
 <table border="0">
 	<?php
  while ($row=$result->fetchRow()) {
  	$teacher_sn=$row['teacher_sn'];
  	$selfweb="";
  	$sql_web="select selfweb from teacher_connect where teacher_sn='$teacher_sn'";
  	$res_web=$CONN->Execute($sql_web) or die ("Error! ".$sql_web);
  	
  	$selfweb=$res_web->fields['selfweb'];
  	
  	if ($selfweb=="") {
  	  $D=big52utf8($row['name']);
  	} else {
  		if (substr($selfweb,0,7)=="http://" or substr($selfweb,0,8)=="https://" ) {
  			$D="<a href=\"".$selfweb."\" target=\"_blank\">".big52utf8($row['name'])."</a>";
  		} else { 
  	   $D="<a href=\"http://".$selfweb."/\" target=\"_blank\">".big52utf8($row['name'])."</a>";
  	  }
  	}
  	$f_color=($selfweb=="")?"#CCCCCC":"blue";

  			$i++;  if ($i%10==1) echo "<tr>";
       ?>
        
        <td style="font-size:10pt" align="center">
        	<table border="1"  style="border-collapse:collapse">
           		<tr>
        			<td align="center" style="font-size:11pt;color:<?php echo $f_color;?>">
        				
        				<?php
        					echo $D;
        				?>
        				
        			</td>
        		</tr>
        	</table>
         </td>
        	<?php
      		if ($i%10==0) echo "</tr>";
 	}// end while
 ?>
</table>
共計 <?php echo $i;?> 位教師<br><br>
 <?php 
} // end foreach

?>  	
