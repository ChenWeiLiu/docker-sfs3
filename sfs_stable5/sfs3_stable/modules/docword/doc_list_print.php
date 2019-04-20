<?php

// $Id: doc_list_print.php 6805 2012-06-22 08:00:32Z smallduh $

//載入設定檔
include "docword_config.php";
// session 認證
//session_start();
//session_register("session_log_id");
$page_count = 15 ; //每頁公文數
if(!checkid($PHP_SELF)){
	$go_back=1; //回到自已的認證畫面
	include "header.php";
	include $path."/rlogin.php";
	include "footer.php";
	exit;
}
else
	$ischecked = true;
//-----------------------------------

//註銷公文處理
if ($key == "確定註銷") {
	$query ="update sch_doc1 set doc_stat='9' where doc1_year_limit < 99 and doc1_k_id=0 and TO_DAYS(DATE_ADD(doc1_date,INTERVAL doc1_year_limit YEAR)) <= TO_DAYS('$DelDate') ";
	mysql_query ($query);
}

//銷毀參考日期
if ($DelDate == "") 
	$DelDate = date("Y-m-j");

//取得承辦處室
$doc_unit_p = doc_unit();


$query ="select *  from sch_doc1 where doc_stat<>'9' and doc1_year_limit < 99 and doc1_k_id=0 and TO_DAYS(DATE_ADD(doc1_date,INTERVAL doc1_year_limit YEAR)) <= TO_DAYS('$DelDate') ";

$result = mysql_query ($query) or die ($query);
$num_record= mysql_num_rows($result); 

if ($key == "列印銷毀清冊") {	
	include "include/firelist2.php";
	
	//計算最後一頁
	if ($num_record % $page_count > 0 )
		$last_page = floor($num_record / $page_count)+1;
	else
		$last_page = floor($num_record / $page_count);
		
		
	$i = 1 ;
	$page = 1;	//頁數
	content_header();
	//公文類別(在 docword_config.php 中設定)
	$doc_kind_p = doc_kind();
	while($row = mysql_fetch_array( $result ) ) {
        	$doc1_id = $row["doc1_id"];
        	$doc1_main = $row["doc1_main"];
        	$doc1_word = $row["doc1_word"];        	        	
        	$doc1_kind = $doc_kind_p[$row["doc1_kind"]];
        	$doc1_date = $row["doc1_date"];
        	$doc1_date_sign = $row["doc1_date_sign"];        	
        	$doc1_unit = $row["doc1_unit"];        	
        	$doc1_unit_num1 = $doc_unit_p[$row["doc1_unit_num1"]] ;
        	
        	$temp = explode ("-",$doc1_date);
        	$doc1_date = sprintf("%d.%d.%d",$temp[0]-1911,$temp[1],$temp[2]);
        	
        	if ( ($i>1) && ($i % $page_count == 0) ) {        		
        		//content_end(); //印出內容
        		content_normal(); //印出內容
        		content_footer();
        		if ($num_record - ($page * $page_count) > 0) {
        			$page++;
        			page_break();
        			content_header();
        		}
        	}
//        	else if ($num_record == $i)
//        		content_end(); //印出內容
        	else
        		content_normal(); //印出內容
        	        	
        	$i++;
        }
        content_footer();
	exit;
}
else {
	while($row = mysql_fetch_array( $result ) ) {
        	$doc1_id = $row["doc1_id"];
        	$doc1_main = $row["doc1_main"];
        	$doc1_year_limit = $row["doc1_year_limit"];
        	$doc1_kind = $row["doc1_kind"];
        	$doc1_date = $row["doc1_date"];
        	$doc1_date_sign = $row["doc1_date_sign"];
        	$doc1_infile_date = $row["doc1_infile_date"];
        	$doc1_unit = $row["doc1_unit"];
        	$doc1_unit_num1 = $row["doc1_unit_num1"];        
        	$con.= "<tr><td>$doc1_year_limit</td><td>$doc1_date</td><td>$doc1_id</td><td>$doc1_unit</td><td>$doc1_main</td><td>$doc_unit_p[$doc1_unit_num1]</td><td>&nbsp;</td></tr>";
        }
}
include "header.php";
prog(4); //上方menu (在 docword_config.php 中設定)


?>
<table border="0"  width="100%" >
  <tr>

       <td align="center" width="100%" colspan=2>
       <form method="POST" action="<?php echo $PHP_SELF ?>">
	<?php 

	if ($key == "註銷過期公文") {
		echo "<H2><font color=\"red\">下列公文將被註銷，請確認銷毀清冊已列印完畢</font></H2>";
		echo "<input type=\"hidden\" name=\"DelDate\" value=\"$DelDate\">";
		echo "<input type=\"submit\" name=\"key\" value=\"確定註銷\">";
	}
	else
		echo "銷毀參考日期:<input type=\"text\" name=\"DelDate\" size=\"10\" value=\"$DelDate\"> <input type=\"submit\" value=\"查詢\" name=\"B1\"> &nbsp;<input type=\"submit\" value=\"列印銷毀清冊\" name=\"key\">&nbsp;<input type=\"submit\" value=\"註銷過期公文\" name=\"key\"> </form>";
	?>
</tr>
</table>
<?php echo "<center><font size=4>以 $DelDate 查詢，超過保存年限公文計 $num_record 件</font></center>";?>
<table border=1 width="100%" >
<tr><td>保存年限</td><td>收文日期</td><td>收文號</td><td>來文單位</td><td>文件主旨</td><td>辦理單位</td><td width="120">簽收</td></tr>
<?php echo $con ?>
</table>
<?php
include "footer.php";
?>
