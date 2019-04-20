<?php

// 載入設定檔
include "teach_config.php";

// 認證檢查
sfs_check();

//印出檔頭
head("教師基本資料-任教一覽表");

//職稱類別
$POST_KIND = post_kind();
$display_color = array("1"=>"$gridBoy_color","2"=>"$gridGirl_color");
//印出選單
$tool_bar=&make_menu($teach_menu_p);
//列出選單
echo $tool_bar;

echo "全校教師依任教科目條列一覽表:<br>
<hr size='1'>";
//列出教職員id
// ====================================================================
$subject_list=($subject_list!="")?$subject_list:"語文,數學,自然與生活科技,社會,健康與體育,藝術與人文,綜合,特教";
$Subject_KIND=explode(",",$subject_list);

//$Subject_KIND=array("語文_國文","語文_英文","數學","自然與生活科技_理化","自然與生活科技_生物","自然與生活科技_地球科學","自然與生活科技_資訊","社會_地理","社會_歷史","社會_公民","健康與體育_健康","健康與體育_體育","藝術與人文_聽覺藝術","藝術與人文_視覺藝術","藝術與人文_表演藝術","綜合","特教");
foreach ($Subject_KIND as $k=>$master_subjects) {
 $i=0; //紀錄本類別人數
 
 $query="select a.teacher_sn,a.teach_id,a.name,a.sex,b.post_kind,b.class_num,c.rank,c.title_name from teacher_base a,teacher_post b,teacher_title c where a.teacher_sn=b.teacher_sn and a.master_subjects like '%".$master_subjects."%' and a.teach_condition=0 and b.teach_title_id=c.teach_title_id order by c.rank,b.class_num";
 
 $result=$CONN->Execute($query) or die($query);
 ?>
 <table border="0" width="700">
   <tr>
     <td style="color:#800000">領域-科別：<?php echo $master_subjects;?></td>
   </tr>
 </table>
 <table border="1" style="border-collapse:collapse;border-color:#000000">
 	<?php
  while ($row=$result->fetchRow()) {
  	$teacher_sn=$row['teacher_sn'];
  	$selfweb="";
  	$sql_web="select selfweb from teacher_connect where teacher_sn='$teacher_sn'";
  	$res_web=$CONN->Execute($sql_web) or die ("Error! ".$sql_web);
  	$post_kind=$row['post_kind'];
		$title_name=$row['title_name'];
  	$sex=$row['sex'];
  	$selfweb=$res_web->fields['selfweb'];
  	
  	if ($selfweb=="") {
  	  $D=$row['name']."<br><font size=2>".$title_name."</font>";
  	} else {
  		if (substr($selfweb,0,7)=="http://" or substr($selfweb,0,8)=="https://" ) {
  			$D="<a href=\"".$selfweb."\" style='color:".$display_color[$sex]."' target=\"_blank\"><u>".$row['name']."</u></a><br><font size=2>".$title_name."</font>";
  		} else { 
  	   $D="<a href=\"http://".$selfweb."/\" style='color:".$display_color[$sex]."' target=\"_blank\"><u>".$row['name']."</u></a><br><font size=2>".$title_name."</font>";
  	  }
  	}
  	
  	if (false !== ($rst = strpos($title_name,"代課"))) { 
			$D="<font color=red>".$D."</font>";
    } elseif (false !== ($rst = strpos($title_name,"代理"))) { 
			$D="<font color=red>".$D."</font>";
    } elseif (false !== ($rst = strpos($title_name,"兼任"))) {
			$D="<font color=red>".$D."</font>";
    }
 	
  	//$f_color=($selfweb=="")?"#CCCCCC":"#000000";
  			$i++;  if ($i%10==1) echo "<tr>";
       ?>
        
        <td style="font-size:9pt" align="center" width="80">
        	<table border="0"  style="border-collapse:collapse">
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
<hr size="1">
說明: <br>
1.本程式依「教師管理/基本資料」中《學習領域任教專門科目》中所註記的資料加以分類後條列教師名單。<br>
2.請由模組變數調整要條列的索引條件。<br>
3.請由「學校設定/<a href='/school_setup/school_title.php'>職稱資料</a>」調整列表中各職稱列出的順序。<br>