<?php
// $Id: describe.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "../../include/config.php";
//引入函數
include "./my_fun.php";

//使用者認證
sfs_check();

// 不需要 register_globals

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}



//程式檔頭
head("成績列表");


//列出橫向的連結選單模組
$menu_p = array("normal.php"=>"平時成績", "manage.php"=>"管理學期成績", "describe.php"=>"評語管理","index.php"=>"顯示學期成績","test.php"=>"使用說明");
$Link = "teacher_course=$_GET[teacher_course]";
print_menu($menu_p,$Link);

$yorn=findyorn();
//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

if($send=="ok"){	
	foreach($stud_A as $key => $val){
		$memo=${"s".$val};
		//echo $key.$val.$ss_id.$seme_year_seme.$memo."<br>";
		$sql="select sss_id from stud_seme_score where ss_id='$ss_id' and seme_year_seme='$seme_year_seme' and student_sn='$val'";		
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$sss_id=$rs->fields['sss_id'];
		if($sss_id) $sql2="update stud_seme_score set ss_score_memo='$memo' where sss_id='$sss_id'";
		else $sql2="insert into stud_seme_score(seme_year_seme,student_sn,ss_id,ss_score_memo) values('$seme_year_seme','$val','$ss_id','$memo')";
		//echo $sql2."<br>";
		$CONN->Execute($sql2) or trigger_error($sql2);
	}	
}


if($need_allow!=""){
    $update_rs=$CONN->Execute("select  teacher_sn,class_id,ss_id from score_course where course_id='$teacher_course'") or die("help");
    $teacher_sn=$update_rs->fields['teacher_sn'];
    $class_id=$update_rs->fields['class_id'];
    $ss_id=$update_rs->fields['ss_id'];
    $CONN->Execute("UPDATE  score_course  SET  allow='$need_allow'  WHERE  teacher_sn='$teacher_sn' and class_id='$class_id' and ss_id='$ss_id'") or die("help");    
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期


$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
$col_name="teacher_course";
$id=$teacher_course;
$select_teacher_ss=&select_teacher_ss($id,$col_name,$teacher_id,$sel_year,$sel_seme);
for($i=1;$i<=3;$i++){
    $check_sort[$i]=$i;
}
for($i=1;$i<=3;$i++){
    $selected_sort[$i]=($check_sort[$i]==$curr_sort)?"selected":"";
}
$check_form[0]="1";
$check_form[1]="2";
for($i=0;$i<2;$i++){
    $form_selected[$i]=($check_form[$i]==$curr_form)?"selected":"";
}
$select_teacher_subject="
    <form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu1()'>
            $select_teacher_ss;
        </select>
    </form>
    ";
$sql="select performance_test_times from score_setup where year='$sel_year' and semester='$sel_seme'";
$rs=$CONN->Execute($sql);
$performance_test_times=$rs->fields['performance_test_times'];



echo "<table cellspacing=0 cellpadding=0><tr><td>$select_teacher_subject</td>";


$select_course_id_sql="select * from score_course where course_id=$teacher_course";
$rs_select_course_id=$CONN->Execute($select_course_id_sql);
$year= $rs_select_course_id->fields['year'];
$semester= $rs_select_course_id->fields['semester'];
$class_id=$rs_select_course_id->fields[class_id];
$class_year=$rs_select_course_id->fields[class_year];
$ss_id= $rs_select_course_id->fields['ss_id'];
$teacher_sn = $rs_select_course_id->fields['teacher_sn'];
$allow=$rs_select_course_id->fields['allow'];
    $select_class_num="select class_num from teacher_post where teacher_sn='{$_SESSION['session_tea_sn']}'";
    $rs_select_class_num=$CONN->Execute($select_class_num);
    $class_num = $rs_select_class_num->fields['class_num'];
    if($class_num) $class_num=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($class_num,0,-2),substr($class_num,-2));
    if(($teacher_course) && ($class_id!=$class_num)) {
        //if($need_allow=="") $need_allow=0;
        $checked=($allow=="1")?"checked":"";
        $uu=($allow=="1")?"0":"1";
        $check_allow="
        <form name='allow_form' method='post' action='{$_SERVER['PHP_SELF']}'>
            <input type='hidden' name='teacher_course' value='$teacher_course'>
            <input type='hidden' name='curr_sort' value='$curr_sort'>
            <input type='radio' name='need_allow' value='$uu' $checked onClick='jumpMenu_allowform()'>關閉導師管理權限
        </form>
        ";
        echo "<td>".$check_allow."</td>";
    }
echo "</tr></table>";
//以上為選單bar
/*****************************************************************************************************/
$subject_name=ss_id_to_subject_name($ss_id);
//echo $class_year;
/********************************************************************************/
$sql="select * from school_class where class_id='$class_id'";
$rs=$CONN->Execute($sql);

$stud_sn=class_id_to_student_sn($class_id);
$full_class_name=course_id_to_full_class_name($teacher_course);
/*********************************************************************************/
if($teacher_course){
	$main.="
		<script language=\"JavaScript\">		
		var remote=null;
		function OpenWindow(p,x){
		strFeatures =\"top=20,left=20,width=500,height=200,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
		remote = window.open(\"comment.php?cq=\"+p,\"MyNew\", strFeatures);
		if (remote != null) {
		if (remote.opener == null)
		remote.opener = self;
		}
		if (x == 1) { return remote; }
		}
		function show(thetext){
			thetext.style.background = '#B6C9FD';
		}
		</script>";
	$main.="
		<table bgcolor=#000000 border=0 cellpadding=2 cellspacing=1>
			<tr bgcolor=#ffffff>
				<td  colspan=4 align=center>".$full_class_name.$subject_name." 評語</td>
			</tr>
			<tr bgcolor=#ffffff align=center>
				<td>座號</td>
				<td>姓名</td>
				<td>學期成績</td>
				<td>評語</td>										
			</tr>";
	$stud_A=array();
	$main.="<form action='{$_SERVER['PHP_SELF']}' method='POST' name='des_form'>";
	foreach($stud_sn as $key => $stud_val){
		$key=$key+1;
		//找出評語	
		$seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
		$sql="select ss_score,ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and ss_id='$ss_id' and student_sn='$stud_val' ";
		//echo $sql;
		$rs=$CONN->Execute($sql);
		$ss_score=intval($rs->fields['ss_score']);
		$ss_score_memo=$rs->fields['ss_score_memo'];
		$color=($ss_score_memo!="")?"#1330B3":"#C11212";
		$s_stud_val="s".$stud_val;
		$button="<img src='".$SFS_PATH_HTML."images/comment.png' width=16 height=16 border=0 align='left' onClick=\"return OpenWindow('$s_stud_val')\">";
		$stud_data=student_sn_to_classinfo($stud_val);
		$main.="<tr bgcolor=#ffffff>
		<td>".$stud_data[2]."</td>
		<td>".$stud_data[4]."</td>
		<td align=center>".$ss_score."</td>
		<td>".$button."<input type='text' name='$s_stud_val'  value='$ss_score_memo' onChange='show(this)'></td>
		</tr>
		";
		$main.="<input type='hidden' name='stud_A[]'  value='$stud_val'>";

	}                                              
	$main.="<input type='hidden' name='ss_id' value='$ss_id'>";
	$main.="<input type='hidden' name='seme_year_seme' value='$seme_year_seme'>";
	$main.="<input type='hidden' name='send' value='ok'>";
	$main.="<tr bgcolor=#ffffff><td colspan='3' align='center'><input type='submit' name='submit' value='存檔'></td></tr>";
	$main.="</form></table>";
	echo $main;
}
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();
?>

<script language="JavaScript1.2">

<?php
//是否由平時成績匯入
	if ($_GET[is_ok]==1)
		echo "alert ('平時成績匯入成功 !! ');\n";
?>

<!-- Begin
function jumpMenu1(){
	    var str, classstr ;
        if (document.form1.teacher_course.options[document.form1.teacher_course.selectedIndex].value>0) {
	        location="<?php echo $_SERVER['PHP_SELF'] ?>?teacher_course=" + document.form1.teacher_course.options[document.form1.teacher_course.selectedIndex].value;
	    }
}

function jumpMenu9(){
	var str, classstr ;
    if ((document.form9.teacher_course.value!="") & (document.form9.curr_sort.options[document.form9.curr_sort.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?teacher_course=" + document.form9.teacher_course.value + "&curr_sort=" + document.form9.curr_sort.options[document.form9.curr_sort.selectedIndex].value;
    }
}

function jumpMenu_allowform(){
	var str, classstr ;
	location="<?PHP echo $_SERVER['$PHP_SELF'] ?>?need_allow=" + document.allow_form.need_allow.value +"&teacher_course="+document.allow_form.teacher_course.value + "&curr_sort=" + document.allow_form.curr_sort.value;
}

function confirmSubmit(){
	return confirm('確定要送到教務處？一旦送出之後您將無法在更改，如需更改請洽教務處');

}

function openwindow(url_str){
window.open (url_str,"成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}

	
//  End -->
</script>
