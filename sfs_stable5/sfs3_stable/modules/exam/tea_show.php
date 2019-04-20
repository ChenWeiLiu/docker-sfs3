<?php                                                                                                                             
// $Id: tea_show.php 8742 2016-01-08 13:57:14Z qfon $
//載入設定檔
include "exam_config.php";
//session_start();
include "header.php";
$grade_img = array("face_1.gif","face_2.gif","face_3.gif","face_4.gif","face_5.gif");
$exam_id = $_GET[exam_id];
if ($exam_id=='')
	$exam_id=intval($_POST[exam_id]);
$stud_id = $_GET[stud_id];
if ($stud_id=='')
	$stud_id = $_POST[stud_id];
//評量處理
include "header.php";
//mysqli
$mysqliconn = get_mysqli_conn();

if($_SESSION[session_log_id]<>'') {
	if($_POST[key] == '按下評量') {
		$_POST[exam_id]=intval($_POST[exam_id]);
		$query = "select stud_id from exam_stud where exam_id= '$_POST[exam_id]' ";
		$result = $CONN->Execute($query);
		while (!$result->EOF) {
			$stud_id = $result->fields["stud_id"];	
			$temp = "tea_comment_".$stud_id;
			$tea_comment = $_POST[$temp];
			$temp = "tea_grade_".$stud_id;
			$tea_grade = intval($_POST[$temp]);
		    /*
			$query2 = "update exam_stud set tea_comment='$tea_comment' ";
			if ($tea_grade !="")
				$query2 .= ",tea_grade=$tea_grade";
			$query2 .= " where exam_id= '$_POST[exam_id]' and stud_id ='$stud_id' ";
			$CONN->Execute($query2) ;
			*/
			
			$query2 = "update exam_stud set tea_comment=? ";
			if ($tea_grade !="")
				$query2 .= ",tea_grade=$tea_grade";
			$query2 .= " where exam_id= '$_POST[exam_id]' and stud_id ='$stud_id' ";
	
///mysqli	
$stmt = "";
$stmt = $mysqliconn->prepare($query2);
$stmt->bind_param('s', $tea_comment);
$stmt->execute();
$stmt->close();
///mysqli
			
			
			$result->MoveNext();
		}
		echo "評量已更新!!";
		redir( "$_SERVER[PHP_SELF]?exam_id=$_POST[exam_id]" ,1);
		exit;
	}
	
	if($_GET[key] == 'del'){
		
///mysqli	
$stmt = "";
if ($stud_id <> "") {
    $stmt = $mysqliconn->prepare("select f_name from exam_stud where stud_id=? and exam_id='$exam_id'");
    $stmt->bind_param('s', $stud_id);
}
$stmt->execute();
$stmt->bind_result($f_namex);
$stmt->fetch();
$stmt->close();
///mysqli
		$f_name= $upload_path."e_".$exam_id."/".$stud_id."_".$f_namex;

		/*
		$query = "select f_name from exam_stud where stud_id='$stud_id' and exam_id='$exam_id'";
		$res = $CONN->Execute($query);
		$f_name= $upload_path."e_".$exam_id."/".$stud_id."_".$res->fields[0];
		//echo $f_name;exit;
		*/
         $exam_id=intval($exam_id);
		//$query = "delete from exam_stud where stud_id='$stud_id' and exam_id='$exam_id'";		
		//$CONN->Execute($query);
		
///mysqli	
$query = "delete from exam_stud where stud_id=? and exam_id='$exam_id'";		
$stmt = "";
$stmt = $mysqliconn->prepare($query);
$stmt->bind_param('s', $stud_id);
$stmt->execute();
$stmt->close();
///mysqli
		
		if(file_exists ($f_name))
       	        	unlink($f_name);
	}		
		
}

//優良註記
if (isset($_GET[cool])){
	$exam_id=intval($exam_id);
	//$sql_update = "update exam_stud set cool = '$_GET[cool]' where exam_id= '$exam_id' and stud_id = '$stud_id' ";
	//$result = $CONN->Execute($sql_update) or die ($sql_update);
///mysqli	
$sql_update = "update exam_stud set cool = ? where exam_id= '$exam_id' and stud_id = ? ";
$stmt = "";
$stmt = $mysqliconn->prepare($sql_update);
$stmt->bind_param('ss', $_GET[cool],$stud_id);
$stmt->execute();
$stmt->close();
///mysqli
	
	
	
}

//作業名稱處理
$exam_id=intval($exam_id);
$query = "select exam.exam_name,exam.exam_memo ,exam.teach_id,exam.teach_name,exam.exam_isupload,exam_kind.class_id,exam_kind.e_upload_ok from exam,exam_kind where exam.e_kind_id = exam_kind.e_kind_id and exam.exam_id='$exam_id' ";
$result = $CONN->Execute($query) or die($query);
$teach_id = $result->fields["teach_id"]; //教師代號
$teach_name = $result->fields["teach_name"]; //教師姓名
$exam_name = $result->fields["exam_name"];
$exam_memo = $result->fields["exam_memo"];
$e_upload_ok = $result->fields["e_upload_ok"];
$class_id = $result->fields["class_id"];
$exam_isupload = $result->fields["exam_isupload"];
//顯示作業班級
echo "<h3>班級：".get_class_name($class_id)." 指導教師：$teach_name</h3>";

//取得學生年級班級
if (isset($_SESSION[session_curr_class_num]))
	$curr_class = substr($_SESSION[session_curr_class_num],0,3 );


//判斷是否開始上傳作業 exam_isupload == 1
//判斷是否為該班學生或指導教師，再給予上傳權限		
echo "<center>";

$temp_class = sprintf("%03d%d%d",curr_year(),curr_seme(),substr($_SESSION[session_curr_class_num],0,3));
if ($exam_isupload == '1' && (($e_upload_ok == '1' && $class_id == $temp_class)||($teach_id == $_SESSION[session_log_id])) )
echo "<a href=\"tea_upload.php?exam_id=$exam_id&exam_name=$exam_name\">上傳作業</a>&nbsp;&nbsp;";
echo "<a href=\"$_SERVER[PHP_SELF]?e_kind_id=$e_kind_id&exam_id=$exam_id\">重新整理</a>&nbsp;&nbsp;";
echo "<a href=\"exam_list.php\">回作業列表區</a></center>";
echo "<form method=\"post\" action=\"tea_show.php\">";
echo "<table  border=1 >\n";
echo "<tr><td valign=top><table bordercolorlight=#ACBAF9 border=1 cellspacing=0 cellpadding=0 bgcolor=#C6FBCE bordercolordark=#DFE2FD width=100%>\n";
echo "<tr bgColor=\"#80ffff\"><td colspan=5 align=center ><font size=4><b>$e_kind_name </b></font></td></tr>\n";
echo "<tr><td colspan=5><font color=red size=3>作業名稱：$exam_name<hr size=1>說明： $exam_memo</font></td></tr>\n";
echo "<tr><td width=165 align=\"center\" >姓名</td><td width=65 align=\"center\" >成果</td><td width=220 align=\"center\" >評語</td><td width=70 align=\"center\" >得分</td>";

if ($_SESSION[session_log_id] == $teach_id){
echo "<td>刪除</td>";
}

echo "</tr>\n";

//作品展示
$exam_id=intval($exam_id);
$sql_select = "select exam_stud.*,exam.exam_id,exam.exam_name,exam.exam_memo,exam.exam_source_isopen,exam.e_kind_id
               from exam_stud,exam where exam.exam_id = exam_stud.exam_id              
               and exam_stud.exam_id= '$exam_id' order by exam_stud.stud_num";
$result = $CONN->Execute($sql_select) or die ($sql_select);

while (!$result->EOF) {
	$exam_id = $result->fields["exam_id"];
	$e_kind_id = $result->fields["e_kind_id"];
	$stud_name = $result->fields["stud_name"];
	$f_name = $result->fields["f_name"];
	$tea_comment = $result->fields["tea_comment"];
	$tea_grade = $result->fields["tea_grade"];
	$stud_num = $result->fields["stud_num"];
	$memo = $result->fields["memo"];
	$stud_id = $result->fields["stud_id"];
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
	$pp = trim(strtolower($temp[count($temp)-1]));
	//註解
	if (chop($memo) != ""){
		$memo2 = nl2br($memo);
		$memo=" alt=\"$memo\" ";
	}
	
	if ($tea_grade == 0 && $_SESSION[session_log_id]=="")
		$tea_grade ="&nbsp;";
	if ($tea_comment == "" && $_SESSION[session_log_id]=="")
		$tea_comment ="&nbsp;";
	if(substr($stud_id,0,4) != "demo" )
	 	$stud_name_temp = "$stud_num 號 -- <a href=\"show_owner.php?stud_id=$stud_id&e_kind_id=$e_kind_id\">$stud_name</a>";
	 else
	 	$stud_name_temp = "教師示範";
	//圖示
	if ($cool == "1")
		$cool_img ="<img src=\"images/cool.gif\">";
	else
		$cool_img = "";
		
	//  管理者 	
	if ($_SESSION[session_log_id] == $teach_id){
		if ($cool == "1")
			$set_cool = "<a href=\"$_SERVER[PHP_SELF]?cool=0&exam_id=$exam_id&stud_id=$stud_id\"><font color=red>Uncool</font></a>";
		else
			$set_cool = "<a href=\"$_SERVER[PHP_SELF]?cool=1&exam_id=$exam_id&stud_id=$stud_id\"><font size=+2><i>c</i></font>ool</a>";	              
	}
	
	if ($color_i++ % 2 == 0 )	 
		echo "<tr bgcolor=\"#E3FEDE\">";
	else
		echo "<tr>";
	echo "<td  align=center>$set_cool $cool_img $stud_name_temp </td>";
	echo "<td  >";
	//壓縮檔處理
	if ( $pp== "zip" ) {
		echo "<a href=\"".$uplaod_url."e_".$exam_id."/".$stud_id."/ \" target=\"_blank\">展示</a>&nbsp;<img src=\"images/memo.gif\" border=0 $memo >";
	}
	else { 
	    if (($pp=="jpg")and ($_SESSION[session_log_id] == $teach_id)) { //出現小圖
	       echo "<img src=\"" . $uplaod_url . "e_" . $exam_id . "/" . $stud_id . "_".$f_name."\" width=\"160\" height=\"120\"> \n" ; 
	    }
		//附帶線上播放 mm=freemind sb=scratch
		$urlte = urlencode($uplaod_url."e_".$exam_id."/".$stud_id."_".trim($f_name));
		if($pp=="mm"){	
			echo "<a href=\"mm_show.php?name=".urlencode($stud_name_temp)."&tn=".urlencode($exam_name)."&uu=".$urlte."\" target=\"_blank\">展示</a>&nbsp;<img src=\"images/memo.gif\" border=0 $memo >";
		}elseif($pp=="sb"){
			echo "<a href=\"sb_show.php?name=".urlencode($stud_name_temp)."&tn=".urlencode($exam_name)."&uu=".$urlte."&memo=".$memo2."\" target=\"_blank\">展示</a>&nbsp;<img src=\"images/memo.gif\" border=0 $memo >";
		}else{
			echo "<a href=\"".$uplaod_url."e_".$exam_id."/".$stud_id."_".$f_name."\" target=\"_blank\">展示</a>&nbsp;<img src=\"images/memo.gif\" border=0 $memo >";
		}   
		
		//name 需加入學生代號
		if ($_SESSION[session_log_id] == $teach_id){
			echo "<td><input type=text size=30 name=tea_comment_".$stud_id." value=\"$tea_comment\"></td>";
			echo "<td align=center><input type=text size=8 name=tea_grade_".$stud_id." value=\"$tea_grade\" > $temp_score</td>";
		}
		else {
			if(substr($stud_id,0,4) == "demo" ) //教師
				echo "<td colspan=2>&nbsp;</td>";
			else {				
				echo "<td >$tea_comment</td>";
				if ($is_score_img) //顯示等第(在 exam_config.php 中設定)
					echo "<td align=\"center\">$temp_score</td>"; 
				else
					echo "<td align=\"center\">$tea_grade</td>"; 
			}
		}
	
		if($exam_source_isopen =="1" && (($pp== "php" )|| ($pp == "php3"))) {
			echo "&nbsp;｜&nbsp;<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."_".$temp[0].".phps\">查看原始檔</a>";
		}

	}

	echo"</td>";
	if ($_SESSION[session_log_id] == $teach_id){
		echo "<td><a href=\"$_SERVER[PHP_SELF]?key=del&f_name=e_$exam_id/$stud_id"."_$f_name&stud_id=$stud_id&e_kind_id=$e_kind_id&exam_id=$exam_id\">刪除</a></td>";
	}
	echo "</tr>";

	$stud_id_arr[]=$stud_id	;
	$result->MoveNext();
};


if ($_SESSION[session_log_id] == $teach_id){
	//未交作業列表
	$class_id_temp = substr($class_id,-3);
	$study_year_temp = intval(substr($class_id,0,3));
	$query = "select stud_id,stud_name,curr_class_num from stud_base where curr_class_num like '$class_id_temp%' and stud_study_cond=0  order by curr_class_num ";
	$result = $CONN->Execute($query) or die ($query);
	while (!$result->EOF) {
		$class_num = intval(substr($result->fields[2],-2));
		if (!in_array ($result->fields[0], $stud_id_arr))
		echo "<tr><td align=center>".$class_num." 號 -- ".$result->fields[1]."</td><td colspan=4>尚未完成</td></tr>";
		$result->MoveNext();
	}

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
