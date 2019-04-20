<?php

// $Id: write_memo.php 9067 2017-05-11 16:42:31Z smallduh $
include "config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";

sfs_check();
?>

<style>
.preMemo{font-size:12px;background:#ddf;cursor: pointer;}
</style>
<?php

if($_POST[act]=="存檔"){
	$sss_id_arr = explode(",",$_POST[temp_sss_id]);
	$stud_id_arr = explode(",",$_POST[temp_stud_id]);
	$seme_year_seme = sprintf("%03d%d",$_POST[sel_year],$_POST[sel_seme]);
	//學習描述評語
	while(list($id,$val) = each($sss_id_arr)){
		if ($val<>''){
			$temp_val ="C_$val";
			$ss_score_memo_val = $_POST[$temp_val];
			$query = "update stud_seme_score set ss_score_memo = '$_POST[$temp_val]',teacher_sn='$_SESSION[session_tea_sn]' where sss_id='$val'";
			$CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
		}
	}
	//努力程度
	while(list($id,$val) = each($stud_id_arr)){
		if ($val<>''){
			$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val)values('$seme_year_seme','$val','努力程度','$_POST[ss_id]','".$_POST["aa_$val"]."')";
			$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);
		}
	}
}

$sel_year = curr_year();
$sel_seme = curr_seme();

//取得排除名單
$student_out=get_manage_out($sel_year,$sel_seme);

//教師代號
$teacher_sn = $_SESSION[session_tea_sn];

//領域科目名稱
$subject_arr = get_subject_name_arr();

//判斷是幾年幾班的導師
$class_num = get_teach_class();
$class_year=substr($class_num,0,-2);
$class_name=intval(substr($class_num,-2));

//取得本學期班級陣列
$class_name_arr = class_base();

//2003-12-25新增，先取出分組課程的選單
$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1'";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$all_ss_id.="'".$res->fields[ss_id]."',";
	$res->MoveNext();
}
if ($all_ss_id) $all_ss_id=substr($all_ss_id,0,-1);
$sql_sub="select * from elective_tea where teacher_sn='$teacher_sn' and ss_id in ($all_ss_id)";
$rs_sub=$CONN->Execute($sql_sub) or creat_elective();
if($rs_sub){
	$sub=0;
	while(!$rs_sub->EOF){
		//一樣要找出科目名
		$group_id=$rs_sub->fields['group_id'];
		$group_name=$rs_sub->fields['group_name'];
		$ss_id=$rs_sub->fields['ss_id'];
		$cid=$rs_sub->fields['course_id'];

		//這個科目需要考試嗎？
		$query = "select need_exam from score_ss where ss_id='$ss_id' ";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$need_exam = $res->fields['need_exam'];
		if($need_exam){
			$class_subj=ss_id_to_class_subject_name($ss_id);
			$class_subj_group=$class_subj."-".$group_name;
			$gs_id=$group_id."g".$ss_id;
			$e_arr[$cid]=$gs_id;
			$course_arr[$gs_id]=$class_subj_group;
			$sub++;
		}
		$rs_sub->MoveNext();
	}
}

//取得任教科目陣列 $course_arr
//導師
if ($class_num && $is_allow=="y") {
	$class_year = substr($class_num,0,-2);
	$class_name = substr($class_num,-2);
	$query = "select a.course_id,a.class_id,b.ss_id,b.scope_id,b.subject_id,a.allow from score_course a,score_ss b where a.day<>'' and a.ss_id=b.ss_id and b.need_exam=1 and a.year=$sel_year and a.semester=$sel_seme and (a.teacher_sn='$teacher_sn' or (a.class_year='$class_year' and a.class_name='$class_name' and a.allow='0')) group by a.class_id,b.scope_id,b.subject_id";
}
//科任老師
else
	$query = "select a.course_id,a.class_id,b.ss_id,b.scope_id,b.subject_id,a.allow from score_course a,score_ss b where a.day<>'' and a.ss_id=b.ss_id and b.need_exam=1 and a.year=$sel_year and a.semester=$sel_seme and a.teacher_sn='$teacher_sn'  group by a.class_id,b.scope_id,b.subject_id";

$res = $CONN->Execute($query)or trigger_error($query,E_USER_ERROR);
while(!$res->EOF){
	$temp_arr = explode("_",$res->fields[class_id]);
	$temp_id = sprintf("%d%02d",$temp_arr[2],$temp_arr[3]);
	$temp_ss_id = $res->fields[subject_id];
	$cid=$res->fields[course_id];
	if ($res->fields[subject_id]==0)  $temp_ss_id = $res->fields[scope_id];
	if (empty($e_arr[$cid]))  $course_arr[$cid] = $class_name_arr[$temp_id].$subject_arr[$temp_ss_id][subject_name];
	$subject_id_arr[$cid] = $res->fields['ss_id'];
	//關閉導師功能判別
	if ($is_allow=='y') $allow_arr[$res->fields[course_id]] = $res->fields[allow];
	$res->MoveNext();
}

$teacher_course = $_REQUEST[teacher_course];

// 檢查課程權限是否正確
$cc_arr=array_keys($course_arr);
$err=(in_array($teacher_course,$cc_arr) || $teacher_course=="")?0:1;

//科目下拉選單 -------------
$sel= new drop_select();
$sel->s_name = "teacher_course";
$sel->id = $teacher_course;
$sel->is_submit = true; 
$sel->arr = $course_arr;
$sel->top_option = "選擇班級科目";
$sel->font_style="";
$sel->font_color = "#3f33f3";
$sel->is_bgcolor_list = true;
$course_sel = $sel->get_select();
//------------- 科目下拉選單 結束

// 上方選單
$top_str = "<form action=\"$_SERVER[PHP_SELF]\" name=\"myform\" method=\"post\" id=\"myform\">$course_sel</form>
	
	<input type='button' id='showPrevMemo' value='顯示上學期評語' disabled='disabled'>";

if ($teacher_course) {
	//經由快貼傳入文字陣列===================================================
	if ($_POST['thepaste']) {
		$seme_year_seme = sprintf("%03d%d",$_POST[sel_year],$_POST[sel_seme]);
	  $data_arr=explode("\n",$_POST['paste_arr']);
	  $ss_id=$_POST['ss_id'];
	  foreach($data_arr as $a) {
	   
	   $p_data=explode("\t",$a);  //座號，姓名，資料
	   $curr_class_num=$_POST['class_id'].sprintf("%02d",$p_data[0]);
	   $stud_name=$p_data[1]; 	//姓名
	   $score_memo=$p_data[2];
	   $sql="select a.sss_id from stud_seme_score a ,stud_base b where a.student_sn=b.student_sn and a.ss_id ='$ss_id' and a.seme_year_seme='$seme_year_seme' and b.curr_class_num='$curr_class_num' and b.stud_study_cond=0";
	   list($stud_sss_id)=mysql_fetch_row(mysql_query($sql));
	   
	   if ($stud_sss_id) {
			$query = "update stud_seme_score set ss_score_memo = '$score_memo',teacher_sn='$_SEESION[session_tea_sn]' where sss_id='$stud_sss_id'";
	    mysql_query($query);
	   } // end if $stud_sss_id
	  
	  } // end foreach	
	  
	} // end if ($_POST['thepaste'])
	//=======================================================================
	
	$top_str.="<input type=\"button\" id=\"showMainPaste\" value=\"使用快貼輸入學習描述文字\" onclick=\"show_main_paste()\">";
if(strstr ($teacher_course, 'g')){
	$group_arr=explode("g",$teacher_course);
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$ss_id=$group_arr[1];
	$sql="select student_sn from elective_stu where group_id='{$group_arr[0]}' ";
}else{
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$temp_id_arr = explode("_",$class_id_arr[$teacher_course]);
	$course_id_sql="select * from score_course where course_id='$teacher_course'";
	$rs_course_id=$CONN->Execute($course_id_sql);
	$temp_id_arr= explode("_",$rs_course_id->fields['class_id']);
	$class_id = sprintf("%d%02d",$temp_id_arr[2],$temp_id_arr[3]);
	$ss_id = $subject_id_arr[$teacher_course];
	$sql="select student_sn from stud_base where curr_class_num like '$class_id%'and stud_study_cond='0'";
}
$rs=$CONN->Execute($sql);
$i=0;
while (!$rs->EOF) {
	$all_sn.=$rs->fields[student_sn].",";
	$i++;
	$rs->MoveNext();
}
$all_sn=substr($all_sn,0,-1);
$stud_numbers=$i;
$query = "select count(sss_id) as cc from stud_seme_score where student_sn in ($all_sn) and ss_id='$ss_id' and seme_year_seme='$seme_year_seme'";
$res = $CONN->Execute($query);
if ($res->fields[0]<$stud_numbers) {
	if(strstr ($teacher_course, 'g'))
		$query = "select student_sn from elective_stu where group_id='{$group_arr[0]}' ";
	else
		$query = "select student_sn from stud_base where curr_class_num like '$class_id%' and stud_study_cond=0";
	$res = $CONN->Execute($query);
	while (!$res->EOF){
		$sst = $res->fields[0];
		$sql="select student_sn from stud_seme_score where student_sn='$sst' and ss_id='$ss_id' and seme_year_seme='$seme_year_seme'";
		$rs=$CONN->Execute($sql);
		if (empty($rs->fields[student_sn])) {
			$query = "INSERT INTO stud_seme_score(seme_year_seme,student_sn,ss_id,teacher_sn)values('$seme_year_seme','$sst','$ss_id','$_SESSION[session_tea_sn]')";
			$CONN->Execute($query);
		}
		$res->MoveNext();
	}
}
$query = "select a.sss_id,a.student_sn,a.ss_score,a.ss_score_memo,b.stud_id,b.stud_name,b.curr_class_num,b.stud_study_year from stud_seme_score a ,stud_base b where a.student_sn=b.student_sn and a.ss_id ='$ss_id' and a.seme_year_seme='$seme_year_seme' and b.student_sn in ($all_sn) and b.stud_study_cond=0 order by b.curr_class_num ";
$res = $CONN->Execute($query) or trigger_error("SQL 指令錯誤", E_USER_ERROR);
$is_has_data = $res->RecordCount();
$site_str=(strstr($teacher_course, 'g'))?"班級-座號":"座號";
$html ="
<input type='hidden' name='thepaste' value=''>
<table border=\"0\">
  <tr id=\"main_paste\" style=\"display:none\">
    <td>
     <table border=\"0\">
     <tr>
      <td><textarea cols=\"80\" rows=\"10\" name=\"paste_arr\"></textarea></td>
     </tr>
     <tr>
      <td><input type=\"button\" value=\"送出資料\" onclick=\"document.col1.thepaste.value='1';document.col1.submit()\"><input type=\"button\" value=\"返回\" onclick=\"show_main_input()\"></td>
     </tr>
     <tr>
       <td>說明：如果您平常有利用如 Execl 軟體整理成績的習慣，此處方便您快速把學習文字描述鍵入系統，請依圖示操作。<br>
        <img src=\"images/paste_demo.png\" border=\"0\">
       </td>
     </tr>
    </table>
    </td>
  </tr>
  <tr id=\"main_input\" style=\"display:block\">
    <td>

	<table bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"4\" width=\"100%\">
	<tr bgcolor=\"#c4d9ff\">
	<td>學號</td>
	<td>$site_str</td>
	<td>姓名</td>".($pic_checked?'<td>大頭照</td>':'')."
	<td>學期分數</td>
	<td>努力程度</td>
	<td>學習描述文字<img src='images/comment1.png'  border='0' onclick=openwindow(\"quick_input_memo.php?class_id=$class_id&teacher_course=$teacher_course&ss_id=$ss_id&seme_year_seme=$seme_year_seme\")></td>
	</tr>
";
$temp_sss_id = '';
// 取得努力程度文字敘述
$arr_1 = sfs_text("努力程度");
// 取得學生努力程度值
$oth_data=&get_class_oth_value($class_id,$sel_year,$sel_seme,$ss_id,"努力程度");
$sel1 = new drop_select();
$sel1->use_val_as_key = true;

while(!$res->EOF) {
	$sss_id = $res->fields[sss_id];
	$stud_id = $res->fields[stud_id];
	$ss_score_memo = $res->fields[ss_score_memo];
	$ss_score= round($res->fields[ss_score],1);
	$student_sn = $res->fields['student_sn'];
	$stud_name= $res->fields[stud_name];
	//排除名單加註*
  $stud_name.=($student_out[$student_sn])?"<font color=red>*</font>":"";

	$curr_class_num = $res->fields[curr_class_num];
	$stud_study_year=$res->fields[stud_study_year];
	$sit_num = substr($curr_class_num,-2);
	if(strstr ($teacher_course, 'g')) {
		$sit_num = intval(substr($curr_class_num,-4,2))."-".$sit_num;
	}
	$sel1->s_name = "aa_$stud_id";
	$sel1->id = $oth_data["$stud_id"];
	$sel1->arr= $arr_1;
	$temp_sel = $sel1->get_select();


	$C = "C".$sss_id;
	$Cs = "C".$sss_id."s";
	
	if($pic_checked) {
		//印出照片
		$img=$UPLOAD_PATH."photo/student/".$stud_study_year."/".$stud_id; 
		$img_link=$UPLOAD_URL."photo/student/".$stud_study_year."/".$stud_id;			
		if (file_exists($img)) $pic_data="<td><img src='$img_link' width=$pic_width></td>"; else $pic_data="<td></td>";
	} else $pic_data="";
	$html .= "<tr bgcolor='white'><td align='center'>$stud_id</td><td align='center'>$sit_num</td><td align=center>$stud_name</td>$pic_data<td align=center>$ss_score</td>
	<td>$temp_sel</td>
	<td><img src='$SFS_PATH_HTML"."images/comment.png' width=16 height=16 border=0 align='left' name='$Cs' value='$Cs' onClick=\"return OpenWindow('$C')\">
	<input name='C_$sss_id' id='$C' size='60' value='$ss_score_memo' >
	<span class='preMemo' sid='$C' id='pre-$student_sn'></span>
	</td>
	</tr>\n";
	$temp_sss_id .= $sss_id.",";
	$temp_stud_id .= $stud_id.",";

	$res->MoveNext();
}
$html .="<input type='hidden' name='temp_sss_id' value='$temp_sss_id'>";
$html .="<input type='hidden' name='temp_stud_id' value='$temp_stud_id'>";
$html .="</table>\n";
} // end if teacher_course





$main="
	<script language=\"JavaScript\">
	var remote=null;
	function OpenWindow(p,x){
	strFeatures =\"top=10,left=20,width=550,height=250,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
	remote = window.open(\"../academic_record/comment.php?cq=\"+p,\"MyNew\", strFeatures);
	if (remote != null) {
	if (remote.opener == null)
	remote.opener = self;
	}
	if (x == 1) { return remote; }
	}
	function checkok() {
	return true;
	}
  
	</script>
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'>

	$top_str
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='col1'>
	<td bgcolor='#FFFFFF' valign='top'>";
	if ($is_has_data){
	$main .= "
	$html
	<input type='hidden' name='teacher_course' value='$teacher_course'>
	<input type='hidden' name='class_id' value='$class_id'>
	<input type='hidden' name='ss_id' value='$ss_id'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='nav_next' ><br><div align='center'>
	<input type='submit' name='act' value='存檔' onClick='return checkok();'>
	";
	}
	$main .="
	</div>
	</form>
	</td>
	</tr></table>
	</td> </tr></table>
	";

head("學習描述文字編修");
$link ="teacher_course=$teacher_course";
print_menu($menu_p,$link);
if ($err==1) {
	$main="<table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%'><tr><td align='center'><h1><img src='../../images/warn.png' align='middle' border=0>操作權限不符</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'>您並無本課程的操作權限！<br></td></tr><tr><td align=center><br></td></tr></table>";
}
echo $main;
foot();

?>
<script language="JavaScript1.2">
<!-- Begin
function openwindow(url_str){
window.open (url_str,"成績處理","toolbar=no,locaalert('test');tion=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}

function show_main_input() {
    main_input.style.display="block";
    main_paste.style.display="none";
 }

function show_main_paste() {
    main_input.style.display="none";
    main_paste.style.display="block";
 }

$(document).ready(function(){

	if ($("select[name='teacher_course']").val())
		$("#showPrevMemo").attr('disabled',false);
	else
		$("#showPrevMemo").attr('disabled',true);



	$("#showPrevMemo").click(function(){
		var id =$("select[name='teacher_course']").val();


		$.getJSON('write_memo_prev.php',{course_id:id},function(response){
			$.each(response,function(i,v){
				$("#pre-"+i).html(v);
			});
		});

/*
		$.ajax({
			type: 'POST',
			url: 'write_memo_prev.php',
			data:{ course_id:id },
			dataType: 'text',
			error: function (xhr) {
				alert('發生錯誤!');
			},
			success: function(response) {
				//alert(response);
				var res_data = JSON.parse(response);  //把傳入的資料轉為 json 格式再分析
				//alert('ok');
				$.each(res_data, function (i, v) {
					$("#pre-" + i).html(v);
				});
			}
		});
*/
	});
	// copy to memo
	$(".preMemo").click(function(){
		var id = $(this).attr('id').substr(4);
		var sid = $(this).attr('sid');
		$("#"+sid).attr('value',$(this).html());
	});

});

//  End -->


</script>
