<?php
//$Id: edu_txt.php 7711 2013-10-23 13:07:37Z smallduh $
//載入設定檔
require ("config.php");

// 認證檢查
sfs_check();

$curr_seme=$_POST[curr_seme];
if (!$curr_seme) $curr_seme = sprintf("%03d%d",curr_year(),curr_seme());
$sel_year=intval(substr($curr_seme,0,-1));
$sel_seme=substr($curr_seme,-1,1);
$stud_study_year=($IS_JHORES==0)?6:3;
$stud_study_year=$sel_year-$stud_study_year+1;
$s=get_school_base();
$sch_id=$s[sch_id];
$edu_id_arr=($IS_JHORES==0)?array("1"=>"01","2"=>"02"):array("1"=>"81","2"=>"81");

$postBtn = "教育程度資料檔匯出";
if ($_POST[do_key]==$postBtn){
	$query = "select a.*,b.grad_kind from stud_base a left join grad_stud b on a.student_sn=b.student_sn and b.stud_grad_year='$sel_year' where stud_study_year='$stud_study_year' and (stud_study_cond='0' or stud_study_cond='5') order by stud_id";
	$result =$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256);
	while ($row = $result->FetchRow()) {
		$stud_id = $row['stud_id'];
		$stud_name = mb_substr($row['stud_name']."　　　　　　",0,6);
		$stud_person_id = $row['stud_person_id'];
		$dd=explode("-",$row['stud_birthday']);
		$dd[0]=$dd[0]-1911;
		$stud_birthday = sprintf("%07d",implode("",$dd));
		$edu_id = $edu_id_arr[$row['grad_kind']];
		$cname = $class_name[substr($curr_class_num,0,-2)];
		$str.=$stud_name.strtoupper(sprintf("% 10s",$stud_person_id)).$stud_birthday.$edu_id.sprintf("%06d",$sch_id)."\r\n";
	}
	
	$filename = "edu.txt";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $str;	
	exit;
}

head();
print_menu($menu_p);
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr><td valign="top" bgcolor="#CCCCCC" align="center">
<table width="80%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td valign="top">  
  <form name ="myform" action="<?php echo $PHP_SELF ?>" method="post" >
   <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"   class=main_body >	
   <tr>
	
	<td class=title_mbody colspan=4 align=center >
	<?php 
	//列出年度別選單
		$class_seme_p = get_class_seme(); //學年度
		$sel1 = new drop_select();
		$sel1->s_name="curr_seme";
		$sel1->is_submit = true;
		$sel1->top_option = "選擇學期";
		$sel1->id = $curr_seme;
		$sel1->arr = $class_seme_p;
		$sel1->do_select();
		
	?><input type="submit" name="do_key" value="<?php echo $postBtn ?>">
</td>
   </tr>
</table>
</form>
   <table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"   class=main_body >	
   <tr class=title_sbody1 ><td align=center>姓　　　　名</td><td align=center>國民身分證<br>統一編號</td><td align=center>出生日期</td><td align=center>教育程度代碼</td><td align=center>學校代碼</td></tr>
<?php
	//列出十筆資料
	$query = "select a.*,b.grad_kind from stud_base a left join grad_stud b on a.student_sn=b.student_sn and b.stud_grad_year='$sel_year' where stud_study_year='$stud_study_year' and (stud_study_cond='0' or stud_study_cond='5') order by stud_id limit 0,10";
	$result =$CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;    	
	while ($row = $result->FetchRow()) {
		$stud_id = $row['stud_id'];
		$stud_name = substr($row['stud_name']."　　　　　　",0,12);
		$stud_person_id = $row['stud_person_id'];
		$dd=explode("-",$row['stud_birthday']);
		$dd[0]=$dd[0]-1911;
		$stud_birthday = sprintf("%07d",implode("",$dd));
		$edu_id = $edu_id_arr[$row['grad_kind']];
		$cname = $class_name[substr($curr_class_num,0,-2)];
		echo "<tr><td>$stud_name</td><td>$stud_person_id</td><td>$stud_birthday</td><td>$edu_id</td><td>$sch_id</td></tr>\n";
	}

?>
</table>
</td>
<td valign="top" width=300>
<!-- 說明 -->
<table
style="width: 100%; text-align: left; background-color: rgb(255, 255, 204);"
border="1" cellpadding="1" cellspacing="1">
<tbody>
<tr>
<td style="vertical-align: top;">
注意事項：
<ol>
<li>畢（結）業生及新生教育程度資料檔應儲存成純文字檔。</li>
<li style="color: red;">如果您直接以瀏覽器開啟文字檔時，請再「按滑鼠右鍵→檢視原始碼」後再另存新檔，即可看到正常格式。</li>
<li>檔案內容應包括學生姓名、國民身分證統一編號、出生日期、教育程度代碼及學校代碼等五項資料，資料檔不須登打表頭及列任何格線。</li>
<li>姓名為六個全形中文字，文字靠左依序排齊，未滿六個全形中文字部分應留全形空白，超過六個全形中文字部分則不必輸入，Big5碼所無之文字則留全形空白。</li>
<li>國民身分證統一編號為十位半形英數字，其中英文字應大寫。</li>
<li>出生日期為七位半形數字，其中出生年為三位數字靠右，如果只有二位則前面補0；至於出生月及出生日均為二位數字靠右，如只有一位則前面補0。</li>
<li>教育程度代碼為二位半形數字，新生與肄業之教育程度代碼相同，其代碼詳見代碼表。</li>
<li>學校代碼為六位半形數字，其代碼應依據教育部統計處彙編之「各級學校名錄」。大專院校及軍警學校之學校代碼只採用後四碼，前面兩碼補99。</li>
<li>登打完一筆學生資料後應按換行鍵再登打第二筆學生資料。</li>
<li>華僑或領有居留證的學生，因未具有戶籍登記之現任中華民國國民身分，不須報送教育程度資料。</li>
</ol>
</td>
</tr>
</tbody>
</table>
<!-- 說明結束 -->
</td>
</tr>
</table> 
</td>
</tr>
</table> 
<?php
foot();
?>
