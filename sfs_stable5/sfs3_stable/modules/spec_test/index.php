<?php
//$Id: index.php 8975 2016-09-14 08:26:39Z smallduh $
include "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("特殊測驗");
print_menu($school_menu_p);

//主要內容
if ($_POST[year_seme]) {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
}

$main="<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>\n";
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$main.="<form name=\"menu_form\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
	<table>
	<tr>
	<td>$year_seme_menu</td>
	</tr>
	</table></form>\n";

//新增測驗或科目
if ($_POST[add] || $_POST[add_subject]) {
	$mode=($_POST[add])?"add":$_POST[mode];
	$compare_id=($mode=="edit")?$_POST[id]:$_POST[compare_id];
	$main.=edit_form($sel_year,$sel_seme,$_POST[c_year],$_POST[title],$compare_id,$mode,$_POST[subjects],$_POST[add_subject],$_POST[subject],$_POST[ratio]);
}

//確定新增
if ($_POST[sure_add]) {
	$c_year=$_POST[c_year];
	$title=addslashes($_POST[title]);
	$rt=$_POST[ratio];
    foreach ($_POST[subject] as $k=>$v) {
	//while(list($k,$v)=each($_POST[subject])) {
		if ($v) {
			$subject_str.=addslashes($v)."@@";
			$ratio_str.=$rt[$k].":";
		}
	}
	if ($subject_str) $subject_str=substr($subject_str,0,-2);
	if ($ratio_str) $ratio_str=substr($ratio_str,0,-1);
	if ($_POST[id]) {
		$query="update test_manage set title='$title',subject_str='$subject_str',ratio_str='$ratio_str',compare_id='$compare_id' where id='".$_POST[id]."'";
	} else {
		$query="insert into test_manage (year,semester,c_year,title,subject_str,ratio_str,compare_id) values ('$sel_year','$sel_seme','$c_year','$title','$subject_str','$ratio_str','$compare_id')";
	}
	$res=$CONN->Execute($query) or die($query);
}

//修改
if ($_POST[edit]) {
    foreach ($_POST[edit] as $k=>$v) {
	//while(list($k,$v)=each($_POST[edit])) {
		$query="select * from test_manage where id='$k'";
		$res=$CONN->Execute($query) or die($query);
	}
	$main.=edit_form($res->fields[year],$res->fields[semester],$res->fields[c_year],"",$res->fields[id],"edit");
}

//刪除
if ($_POST[del]) {
    foreach ($_POST[del] as $k=>$v) {
	//while(list($k,$v)=each($_POST[del])) {
		$query="delete from test_manage where id='$k'";
		$CONN->Execute($query);
	}
}

$main.="<form name=\"form1\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">\n
	<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc class=main_body>\n
	<tr bgcolor='#E1ECFF' align='center'><td>流水號</td><td>學年度</td><td>學期</td><td>年級</td><td>測驗名稱</td><td>測驗科目</td><td>科目加權</td><td>比較測驗</td><td>功能選項</td></tr>\n";
$query="select * from test_manage where year='$sel_year' and semester='$sel_seme' order by id desc";
$res=$CONN->Execute($query) or die($query);
$i=0;
while (!$res->EOF) {
	$subject_str=str_replace("@@","|",stripslashes($res->fields[subject_str]));
	$c_year=$res->fields[c_year];
	$c_year=(empty($c_year))?"無特定年級":$class_year[$c_year]."級";
	$t_id=$res->fields[id];
	$main.="<tr bgcolor='#ffffff' align='center'>
		<td>$t_id</td>
		<td>".$res->fields[year]."</td>
		<td>".$res->fields[semester]."</td>
		<td>".$c_year."</td>
		<td><a href=./score_input.php?id=$t_id>".stripslashes($res->fields[title])."</a></td>
		<td>".$subject_str."</td>
		<td>".$res->fields[ratio_str]."</td>
		<td>".$res->fields[compare_id]."</td>
		<td><input type='image' src='./images/edit.png' name='edit[".$res->fields[id]."]' alt='修改' align='middle'>/<input type='image' src='./images/del.png' name='del[".$res->fields[id]."]' alt='刪除' align='middle'></td>
		</tr>\n";
	$i++;
	$res->MoveNext();
}
if ($i==0) {
	$main.="<tr bgcolor='#E1ECFF' align='center'><td bgcolor='#ffffff' colspan='9'>尚無資料</tr>\n";
}
$main.="</table>";
if (!$_POST[add] && !$_POST[add_subject]) $main.="<input type='submit' name='add' value='新增一次測驗'>";
$main.="</form></tr></table>";
echo $main;

//佈景結尾
foot();

function edit_form($sel_year,$sel_seme,$c_year,$title,$compare_id,$mode,$subjects=0,$add_subject="",$subject=array(),$ratio=array()) {
	global $CONN,$class_year;

	$query="select distinct c_year from school_class where year='$sel_year' and semester='$sel_seme' order by c_year";
	$res=$CONN->Execute($query) or die($query);
	$sel_class="";
	while (!$res->EOF) {
		$selected=($c_year==$res->fields[c_year])?"selected":"";
		$sel_class.="<option value='".$res->fields[c_year]."' $selected>".$class_year[$res->fields[c_year]]."級</option>\n";
		$res->MoveNext();
	}
	if (empty($sel_class)) {
		$sel_class="無年級資料";
	} else {
		$sel_class="<select name='c_year' size='1'>\n<option value=''>無特定年級</option>\n".$sel_class."</select>\n";
	}

	$subject_str="";
	$ratio_str="";
	$button_str="確定新增";
	if ($mode=="edit") {
		$query="select * from test_manage where id='$compare_id'";
		$res=$CONN->Execute($query) or die($query);
		$id=$res->fields['id'];
		$compare_id=$res->fields['compare_id'];
		$button_str="確定修改";
	}
	if ($mode=="add" || $add_subject) {
		//科目數
		if ($add_subject) $subjects++;
		$subjects=($subjects<1)?5:$subjects;
		for ($i=1;$i<=$subjects;$i++) {
			$subject_str.="<input type='text' name='subject[".$i."]' value='".$subject[$i]."' size='6'> ";
			$ratio_str.="<input type='text' name='ratio[".$i."]' value='".$ratio[$i]."' size='6'> ";
		}
	} else {
		$title=stripslashes($res->fields['title']);
		$subject=explode("@@",stripslashes($res->fields['subject_str']));
		$ratio=explode(":",$res->fields['ratio_str']);
		foreach ($subject as $k=>$v) {
        //while(list($k,$v)=each($subject)) {
			$subject_str.="<input type='text' name='subject[".($k+1)."]' value='$v' size='6'> ";
			$ratio_str.="<input type='text' name='ratio[".($k+1)."]' value='".$ratio[$k]."' size='6'> ";
		}
		$subjects=count($subject);
	}
	$form_str.="<form name=\"form2\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">\n
		<table border=0 cellspacing=1 cellpadding=2 bgcolor=#cccccc class=main_body>\n
		<tr bgcolor='#E1ECFF' align='center'><td>年級</td><td bgcolor='#ffffff' align='left'>$sel_class</td></tr>\n
		<tr bgcolor='#E1ECFF' align='center'><td>測驗名稱</td><td bgcolor='#ffffff' align='left'><input type='text' name='title' value='".$title."' size='40'></td></tr>\n
		<tr bgcolor='#E1ECFF' align='center'><td>測驗科目</td><td bgcolor='#ffffff' align='left'>$subject_str</td></tr>\n
		<tr bgcolor='#E1ECFF' align='center'><td>科目加權</td><td bgcolor='#ffffff' align='left'>$ratio_str</td></tr>\n
		<tr bgcolor='#E1ECFF' align='center'><td>比較測驗</td><td bgcolor='#ffffff' align='left'><input type='text' name='compare_id' value='$compare_id'></td></tr>\n
		</table><input type='submit' name='sure_add' value='$button_str'> <input type='submit' name='add_subject' value='增加科目'> <input type='submit' value='取消'><input type='hidden' name='subjects' value='$subjects'><input type='hidden' name='id' value='$id'><input type='hidden' name='mode' value='$mode'></form>\n";
	return $form_str;
}
?>
