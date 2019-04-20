<?php
                                                                                                                             
// $Id: tol_score.php 8743 2016-01-08 14:02:58Z qfon $

//載入設定檔
include "exam_config.php";
//session_start();
include "header.php";
$grade_img = array("face_1.gif","face_2.gif","face_3.gif","face_4.gif","face_5.gif");
//評量處理
include "header.php";
if($key =='按下評量') {
	$exam_id=intval($exam_id);
	$query = "select stud_id from exam_stud where exam_id= '$exam_id' ";
	$result = mysql_query($query);
	while ($row = mysql_fetch_array($result)) {
		$stud_id = $row["stud_id"];	
		$temp = "tea_comment_".$stud_id;
		$tea_comment = $$temp;
		$temp = "tea_grade_".$stud_id;
		$tea_grade = $$temp;
		
		$query2 = "update exam_stud set tea_comment='$tea_comment' ";
		if ($tea_grade !="")
		$query2 .= ",tea_grade=$tea_grade";
		$query2 .= " where exam_id= '$exam_id' and stud_id ='$stud_id' ";
		mysql_query ($query2) ;
	}
	redir_str( "$PHP_SELF?exam_id=$exam_id" ,"評量已更新!!",1);
	exit;
}

//優良註記
if (isset($cool)){
	$exam_id=intval($exam_id);
	/*
	$sql_update = "update exam_stud set cool = '$cool' where exam_id= '$exam_id' and stud_id = '$stud_id' ";
	$result = mysql_query ($sql_update,$conID) or die ($sql_update);
     */
//mysqli
$mysqliconn = get_mysqli_conn();	
$sql_update = "update exam_stud set cool =?  where exam_id= '$exam_id' and stud_id = ? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('ss', $cool,$stud_id);
$stmt->execute();
$stmt->close();
///mysqli		
	
}

//作業名稱處理
$exam_id=intval($exam_id);
$query = "select exam.exam_name,exam.exam_memo ,exam.teach_id,exam.teach_name,exam.exam_isupload,exam_kind.class_id,exam_kind.e_upload_ok from exam,exam_kind where exam.e_kind_id = exam_kind.e_kind_id and exam.exam_id='$exam_id' ";
$result = mysql_query ($query) or die($query);
$row = mysql_fetch_array($result);
$teach_id = $row["teach_id"]; //教師代號
$teach_name = $row["teach_name"]; //教師姓名
$exam_name = $row["exam_name"];
$exam_memo = $row["exam_memo"];
$e_upload_ok = $row["e_upload_ok"];
$class_id = $row["class_id"];
$exam_isupload = $row["exam_isupload"];
//顯示作業班級
echo "<h3>班級：".get_class_name($class_id)." 指導教師：$teach_name</h3>";

//取得學生年級班級
if (isset($session_curr_class_num))
	$curr_class = substr($session_curr_class_num,1,2 );


//判斷是否開始上傳作業 exam_isupload == 1
//判斷是否為該班學生或指導教師，再給予上傳權限		
echo "<center>";
if ($exam_isupload == '1' && (($e_upload_ok == '1' && substr($class_id,-2) == $curr_class)||($teach_id == $session_log_id)) )
echo "<a href=\"tea_upload.php?exam_id=$exam_id&exam_name=$exam_name\">上傳作業</a>&nbsp;&nbsp;";
echo "<a href=\"$PHP_SELF?e_kind_id=$e_kind_id&exam_id=$exam_id\">重新整理</a>&nbsp;&nbsp;";
echo "<a href=\"exam_list.php\">回作業列表區</a></center>";
echo "<form method=\"post\" action=\"tea_show.php\">";
echo "<table  border=1 >\n";
echo "<tr><td valign=top><table bordercolorlight=#ACBAF9 border=1 cellspacing=0 cellpadding=0 bgcolor=#C6FBCE bordercolordark=#DFE2FD width=535>\n";
echo "<tr bgColor=\"#80ffff\"><td colspan=4 align=center ><font size=4><b>$e_kind_name </b></font></td></tr>\n";
echo "<tr><td colspan=4><font color=red size=3>作業名稱：$exam_name<hr size=1>說明： $exam_memo</font></td></tr>\n";
echo "<tr><td width=165 align=\"center\" >姓名</td><td width=65 align=\"center\" >成果</td><td width=220 align=\"center\" >評語</td><td width=70 align=\"center\" >得分</td></tr>\n";

//作品展示
$exam_id=intval($exam_id);
$sql_select = "select exam_stud.*,exam.exam_id,exam.exam_name,exam.exam_memo,exam.exam_source_isopen
               from exam_stud,exam where exam.exam_id = exam_stud.exam_id              
               and exam_stud.exam_id= '$exam_id' order by exam_stud.stud_num";
$result = mysql_query ($sql_select) or die ($sql_select);

while ($row = mysql_fetch_array($result)) {
	$exam_id = $row["exam_id"];
	$stud_name = $row["stud_name"];
	$f_name = $row["f_name"];
	$tea_comment = $row["tea_comment"];
	$tea_grade = $row["tea_grade"];
	$stud_num = $row["stud_num"];
	$memo = $row["memo"];	
	$stud_id = $row["stud_id"];
	$cool = $row["cool"];
	$exam_source_isopen =$row["exam_source_isopen"]; 
	
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
	if (chop($memo) != "")
		$memo=" alt=\"$memo\" ";
	
	if ($tea_grade == 0 && $session_log_id=="")
		$tea_grade ="&nbsp;";
	if ($tea_comment == "" && $session_log_id=="")
		$tea_comment ="&nbsp;";
	if(substr($stud_id,0,4) != "demo" )
	 	$stud_name_temp = "$stud_num 號 -- $stud_name";
	 else
	 	$stud_name_temp = "教師示範";
	//圖示
	if ($cool == "1")
		$cool_img ="<img src=\"images/cool.gif\">";
	else
		$cool_img = "";
		
	//  管理者 	
	if ($session_log_id == $teach_id){
		if ($cool == "1")
			$set_cool = "<a href=\"$PHP_SELF?cool=0&exam_id=$exam_id&stud_id=$stud_id\"><font color=red>Uncool</font></a>";
		else
			$set_cool = "<a href=\"$PHP_SELF?cool=1&exam_id=$exam_id&stud_id=$stud_id\"><font size=+2><i>c</i></font>ool</a>";	              
	}
	
	echo "<tr><td  align=center>$set_cool $cool_img $stud_name_temp </td>";
	echo "<td  align=center valign=middle>";
	//壓縮檔處理
	if ( $pp== "zip" ) {
		echo "<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."/ \" >展示</a>&nbsp;<img src=\"images/memo.gif\" border=0 $memo >";
	}
	else { 
		echo "<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."_".$f_name."\" >展示</a>&nbsp;<img src=\"images/memo.gif\" border=0 $memo >"; 
		//name 需加入學生代號
		if ($session_log_id == $teach_id){
			echo "<td><input type=text size=30 name=tea_comment_".$stud_id." value=\"$tea_comment\"></td>";
			echo "<td align=center><input type=text size=8 name=tea_grade_".$stud_id." value=\"$tea_grade\" > $temp_score</td>";
		}
		else {
			if(substr($stud_id,0,4) == "demo" ) //教師
				echo "<td colspan=2>&nbsp;</td>";
			else {				
				echo "<td >$tea_comment</td>";
				if ($is_scroe_img) //顯示等第(在 exam_config.php 中設定)
					echo "<td align=\"center\">$temp_score</td>"; 
				else
					echo "<td align=\"center\">$tea_grade</td>"; 
			}
		}
	
		if($exam_source_isopen =="1" && (($pp== "php" )|| ($pp == "php3"))) {
			echo "&nbsp;｜&nbsp;<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."_".$temp[0].".phps\">查看原始檔</a>";
		}

	}

	echo"</td></tr>";
};

if ($session_log_id == $teach_id){
	echo "<br><tr><td colspan=4 align=right>
		<input type=\"hidden\" name=\"exam_id\" value=\"$exam_id\">
		<input type=\"submit\" name=\"key\" value=\"按下評量\"></td></tr>";
}
?>
</table>
</td></tr>
</table>
</form>
圖示：<img src="images/cool.gif"> -- <font size =+2><i>贊 </i></font>喔！
<hr width=300 size=1>
<a href="exam_list.php">回作業列表區</a>
<?php include "footer.php"; ?>
