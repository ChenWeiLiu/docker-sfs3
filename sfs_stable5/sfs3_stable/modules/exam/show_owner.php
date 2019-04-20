<?php                                                                                                                             
// $Id: show_owner.php 8743 2016-01-08 14:02:58Z qfon $

//載入設定檔
include "exam_config.php";

include "header.php";
$grade_img = array("face_1.gif","face_2.gif","face_3.gif","face_4.gif","face_5.gif");

include "header.php";

$stud_id = $_GET[stud_id];
if ($stud_id=='')
	$stud_id = $_POST[stud_id];
$e_kind_id = $_GET[e_kind_id];
if($e_kind_id == '')
	$e_kind_id = $_POST[e_kind_id];

//取得班級
$e_kind_id=intval($e_kind_id);
$query = "select substring(class_id,5,3)as cc from exam_kind where e_kind_id='$e_kind_id'";
$result = $CONN->Execute($query);
$cc = $result->fields[0];
//取得該班學生學號、姓名
$query = "select stud_id,stud_name,curr_class_num from stud_base where curr_class_num like'$cc%' and stud_base.stud_study_cond=0 order by curr_class_num";
$result = $CONN->Execute($query) ;
//echo $query;
$temp_stud = "<select name=stud_id onchange=\"document.chgform.submit()\">\n";
while (!$result->EOF){ 
	$sitnum = substr($result->fields[2],-2);
	if ($stud_id == $result->fields[0])		
		$temp_stud .= "<option value=\"".$result->fields[0]."\" selected>$sitnum--".$result->fields[1]."</option>\n";
	else
		$temp_stud .= "<option value=\"".$result->fields[0]."\">$sitnum--".$result->fields[1]."</option>\n";
	$result->MoveNext();
}
$temp_stud .= "</select>";
//取得學生年級班級
if (isset($session_curr_class_num))
	$curr_class = substr($session_curr_class_num,1,2 );

echo "<center>";
echo "<a href=\"exam_list.php\">回作業列表區</a></center>";
echo "<table  border=1 >\n";

//作品展示
///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
    $stmt = $mysqliconn->prepare("select exam.exam_id,exam.exam_name,exam.exam_memo,exam.exam_source_isopen,exam.teach_name,exam_kind.class_id,exam_stud.cool,exam_stud.memo,exam_stud.stud_name,exam_stud.stud_id,exam_stud.f_name,exam_stud.tea_comment,exam_stud.tea_grade,exam_stud.stud_num
               from exam_kind,exam_stud,exam where exam_kind.e_kind_id = exam.e_kind_id and exam.exam_id = exam_stud.exam_id and exam_kind.e_kind_id='$e_kind_id'
               and exam_stud.stud_id= ? order by exam_stud.exam_id");
    $stmt->bind_param('s', $stud_id);


$stmt->execute();
$stmt->bind_result($exam_id,$exam_name,$exam_memo,$exam_source_isopen,$teach_name,$class_id,$cool,$memo,$stud_name,$stud_id,$f_name,$tea_comment,$tea_grade,$stud_num);

$temp_flag = true; //暫存變數

while ($stmt->fetch()) {
	
	//顯示圖示
	$temp_score = "&nbsp;";
	if ($tea_grade >= 90) 
		$temp_score = "<img src=\"images/$grade_img[0]\" alt=\"優\">";
	else if ($tea_grade >=80) 
		$temp_score = "<img src=\"images/$grade_img[1]\" alt=\"甲\">";
	else if ($tea_grade >=70) 
		$temp_score = "<img src=\"images/$grade_img[2]\" alt=\"乙\">";
	else if ($tea_grade >=60) 
		$temp_score = "<img src=\"images/$grade_img[3]\" alt=\"丙\">";
	else if ($tea_grade >0 and $tea_grade < 60) 
		$temp_score = "<img src=\"images/$grade_img[4]\" alt=\"丁\">";
		
	$temp = explode(".", $f_name);	
	$pp = $temp[count($temp)-1];
	//註解
	
	if ($tea_grade == 0 && $session_log_id=="")
		$tea_grade ="&nbsp;";
	if ($tea_comment == "" && $session_log_id=="")
		$tea_comment ="&nbsp;";
	
	//圖示
	if ($cool == "1")
		$cool_img ="<img src=\"images/cool.gif\">";
	else
		$cool_img = "";
		
	//印出學生名字
	if ($temp_flag) {
		//if(substr($stud_id,0,4) != "demo" )
	 	//	$stud_name_temp = "$stud_num 號 -- $stud_name";
	 	//else
	 	//	$stud_name_temp = "教師示範";
	 	echo "<form name=\"chgform\" method=post action=\"$_SERVER[PHP_SELF]\">";
	 	echo "<input type=hidden name=e_kind_id value=\"$e_kind_id\">";
	 	echo "<tr><td valign=top align=center>";
	 	echo intval(substr($class_id,0,3))."學年度";
	 	echo "第 ". substr($class_id,3,1)."學期--";
	 	echo get_class_name($class_id)." $temp_stud </form><table bordercolorlight=#ACBAF9 border=1 cellspacing=0 cellpadding=0 bgcolor=#C6FBCE bordercolordark=#DFE2FD width=535>\n";
		echo "<tr bgColor=\"#80ffff\"><td colspan=4 align=center ><font size=4><b>$e_kind_name </b></font></td></tr>\n";
		echo "<tr ><td width=165 align=\"center\">作業名稱</td><td  align=\"center\" nowrap>指導老師</td><td  align=\"center\" nowrap>作者說明</td><td width=220 align=\"center\" >教師評語</td><td width=70 align=\"center\" >得分</td></tr>\n";
		
		$temp_flag = false; //暫存變數
	}
	
	if ($color_i++ % 2 == 0 )	 
		echo "<tr bgcolor=\"#E3FEDE\"><td>";
	else
		echo "<tr ><td>";
	
	//壓縮檔處理
	if ( $pp== "zip" ) {
		echo "<a href=\"".$uplaod_url."e_".$exam_id."/".$stud_id."/ \" >$exam_name</a>";
	}
	else { 
		echo "<a href=\"".$uplaod_url."e_".$exam_id."/".$stud_id."_".$f_name."\" >$exam_name</a>"; 
	}
	echo "</td>";
	echo "<td align=center> $teach_name</td>";
	
	echo "<td   valign=middle>$memo &nbsp;</td>";
	echo "<td >$tea_comment &nbsp;</td>";			
	if ($is_scroe_img) //顯示等第(在 exam_config.php 中設定)
		echo "<td align=\"center\">$temp_score &nbsp;</td>"; 
	else
		echo "<td align=\"center\">$tea_grade &nbsp;</td>"; 
	
	
	if ($exam_source_isopen =="1" && (($pp== "php" )|| ($pp == "php3"))) {
		echo "&nbsp;｜&nbsp;<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."_".$temp[0].".phps\">查看原始檔</a>";
	}
	
	echo"</tr>";

}

/*
$sql_select = "select exam_stud.*,exam.exam_id,exam.exam_name,exam.exam_memo,exam.exam_source_isopen,exam.teach_name,exam_kind.class_id
               from exam_kind,exam_stud,exam where exam_kind.e_kind_id = exam.e_kind_id and exam.exam_id = exam_stud.exam_id and exam_kind.e_kind_id='$e_kind_id'
               and exam_stud.stud_id= '$stud_id' order by exam_stud.exam_id";
$result = $CONN->Execute($sql_select) or die ($sql_select);

$temp_flag = true; //暫存變數
while (!$result->EOF) {
	$exam_id = $result->fields["exam_id"];
	$exam_name = $result->fields["exam_name"];
	$stud_name = $result->fields["stud_name"];
	$f_name = $result->fields["f_name"];
	$tea_comment = $result->fields["tea_comment"];
	$tea_grade = $result->fields["tea_grade"];
	$teach_name = $result->fields["teach_name"];
	$stud_num = $result->fields["stud_num"];
	$memo = $result->fields["memo"];	
	$stud_id = $result->fields["stud_id"];
	$class_id = $result->fields["class_id"];
	$cool = $result->fields["cool"];
	$exam_source_isopen =$result->fields["exam_source_isopen"]; 
		
	//顯示圖示
	$temp_score = "&nbsp;";
	if ($tea_grade >= 90) 
		$temp_score = "<img src=\"images/$grade_img[0]\" alt=\"優\">";
	else if ($tea_grade >=80) 
		$temp_score = "<img src=\"images/$grade_img[1]\" alt=\"甲\">";
	else if ($tea_grade >=70) 
		$temp_score = "<img src=\"images/$grade_img[2]\" alt=\"乙\">";
	else if ($tea_grade >=60) 
		$temp_score = "<img src=\"images/$grade_img[3]\" alt=\"丙\">";
	else if ($tea_grade >0 and $tea_grade < 60) 
		$temp_score = "<img src=\"images/$grade_img[4]\" alt=\"丁\">";
		
	$temp = explode(".", $f_name);	
	$pp = $temp[count($temp)-1];
	//註解
	
	if ($tea_grade == 0 && $session_log_id=="")
		$tea_grade ="&nbsp;";
	if ($tea_comment == "" && $session_log_id=="")
		$tea_comment ="&nbsp;";
	
	//圖示
	if ($cool == "1")
		$cool_img ="<img src=\"images/cool.gif\">";
	else
		$cool_img = "";
		
	//印出學生名字
	if ($temp_flag) {
		//if(substr($stud_id,0,4) != "demo" )
	 	//	$stud_name_temp = "$stud_num 號 -- $stud_name";
	 	//else
	 	//	$stud_name_temp = "教師示範";
	 	echo "<form name=\"chgform\" method=post action=\"$_SERVER[PHP_SELF]\">";
	 	echo "<input type=hidden name=e_kind_id value=\"$e_kind_id\">";
	 	echo "<tr><td valign=top align=center>";
	 	echo intval(substr($class_id,0,3))."學年度";
	 	echo "第 ". substr($class_id,3,1)."學期--";
	 	echo get_class_name($class_id)." $temp_stud </form><table bordercolorlight=#ACBAF9 border=1 cellspacing=0 cellpadding=0 bgcolor=#C6FBCE bordercolordark=#DFE2FD width=535>\n";
		echo "<tr bgColor=\"#80ffff\"><td colspan=4 align=center ><font size=4><b>$e_kind_name </b></font></td></tr>\n";
		echo "<tr ><td width=165 align=\"center\">作業名稱</td><td  align=\"center\" nowrap>指導老師</td><td  align=\"center\" nowrap>作者說明</td><td width=220 align=\"center\" >教師評語</td><td width=70 align=\"center\" >得分</td></tr>\n";
		
		$temp_flag = false; //暫存變數
	}
	
	if ($color_i++ % 2 == 0 )	 
		echo "<tr bgcolor=\"#E3FEDE\"><td>";
	else
		echo "<tr ><td>";
	
	//壓縮檔處理
	if ( $pp== "zip" ) {
		echo "<a href=\"".$uplaod_url."e_".$exam_id."/".$stud_id."/ \" >$exam_name</a>";
	}
	else { 
		echo "<a href=\"".$uplaod_url."e_".$exam_id."/".$stud_id."_".$f_name."\" >$exam_name</a>"; 
	}
	echo "</td>";
	echo "<td align=center> $teach_name</td>";
	
	echo "<td   valign=middle>$memo &nbsp;</td>";
	echo "<td >$tea_comment &nbsp;</td>";			
	if ($is_scroe_img) //顯示等第(在 exam_config.php 中設定)
		echo "<td align=\"center\">$temp_score &nbsp;</td>"; 
	else
		echo "<td align=\"center\">$tea_grade &nbsp;</td>"; 
	
	
	if ($exam_source_isopen =="1" && (($pp== "php" )|| ($pp == "php3"))) {
		echo "&nbsp;｜&nbsp;<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."_".$temp[0].".phps\">查看原始檔</a>";
	}
	
	echo"</tr>";

	$result->MoveNext();
}

*/
?>
</table>
</td></tr>
</table>
</form>
圖示：<img src="images/cool.gif"> -- <font size =+2><i>贊 </i></font>喔！
<hr width=300 size=1>
<a href="exam_list.php">回作業列表區</a>
<?php include "footer.php"; ?>
