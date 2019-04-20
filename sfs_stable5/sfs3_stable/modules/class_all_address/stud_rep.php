<?php
//$Id: stud_rep.php 5617 2009-09-01 11:53:36Z hami $
/*引入學務系統設定檔*/
include "../../include/config.php";

require_once "./module-cfg.php";
include_once "../../include/sfs_case_PLlib.php";

//使用者認證
sfs_check();

$curr_year = curr_year();
$curr_seme = curr_seme();
$class_name_arr = class_base() ;
$class_name= $class_name_arr[$_REQUEST[sel_year]] ;


if ($_POST[Submit1]=='下載學生名冊'){
	// smarty template 路徑
	$template_dir = $SFS_PATH."/".get_store_path()."/templates";

	// 使用 smarty tag
	$smarty->left_delimiter="{{";
	$smarty->right_delimiter="}}";
	//學校全銜
	$smarty->assign("school_long_name",$school_long_name);
	$smarty->assign("curr_year",$curr_year);
	$smarty->assign("curr_seme",$curr_seme);
	$smarty->assign("class_name",$class_name);
	$smarty->assign("class_name_arr",$class_name_arr);
	$smarty->assign("today",sprintf("%d 年 %d 月 %d 日",date("Y")-1911,date("m"),date("d")));

	$seme_year_seme = sprintf("%03d%d",$curr_year,$curr_seme);


	//列出學生
	if ($_POST[allyear]==1){
		$year_base = year_base();
		$smarty->assign("title_class",$year_base[substr($_POST[sel_year],0,1)]."級");
		$sel_where = "seme_class like '".substr($_POST[sel_year],0,1)."%'";
	}
	else {
		$smarty->assign("title_class",$class_name_arr[$_POST[sel_year]]);
		$sel_where = "seme_class=$_POST[sel_year]";
	}
	$query = "select a.stud_id,a.stud_name,a.stud_sex,a.stud_birthday,a.stud_person_id,a.stud_mschool_name,a.stud_addr_1 ,b.seme_class,b.seme_num,a.stud_study_year from stud_base a ,stud_seme b where a.stud_study_cond=0 AND a.student_sn=b.student_sn and   b.seme_year_seme=$seme_year_seme and $sel_where order by seme_class,seme_num";

	$res = $CONN->Execute($query) or die($query);
	$seme_class_arr = array();
	while(!$res->EOF){
		$seme_class = $res->fields[seme_class];
		$stud_id = $res->fields[stud_id];
		$seme_class_arr[$seme_class][$stud_id] = $res->fields;
		$res->MoveNext();
	}
	$smarty->assign("rowdata",$seme_class_arr);

	//教師姓名
	$query="select  a.name ,b.class_num FROM teacher_base a , teacher_post b where a.teacher_sn  = b.teacher_sn  and  a.teach_condition = '0' and b.post_office = '8'";
	$res = $CONN->Execute($query) or die($query);
	while(!$res->EOF){
		$class_tea_arr[$res->fields[class_num]] = $res->fields[name];
		$res->MoveNext();
	}
	$smarty->assign("class_tea_arr",$class_tea_arr);
	if ($IS_JHORES==0)
		$smarty->display("$template_dir/body.tpl");
	else
		$smarty->display("$template_dir/body2.tpl");

	exit;

}

head();
print_menu($menu_p);

$sel1 = new drop_select(); //選單類別
$sel1->s_name = "sel_year"; //選單名稱
$sel1->id = $_POST[sel_year];
$sel1->has_empty = false;
$sel1->arr = $class_name_arr ; //內容陣列(六個學年)
$sel1->is_submit = true;
$sel1->bgcolor = "#DDFFEE";
$sel1->font_style ="font-size: 15px;font-weight: bold";
$class_select = "選擇班級:" . $sel1->get_select();
$menu=" <html><head><meta http-equiv=\"Content-Type\" content=\"text/html; Charset=Big5\"></head><body>
		<table cellspacing=2 cellpadding=2>
			<tr>
				<td>
					<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
					 $class_select
					<input name='allyear' type='checkbox' value='1' $chk_allyear >全學年
					<input type='submit' name='Submit1' value='下載學生名冊'>
					</form>
				</td>
			</tr>
		</table></body></html>";
	echo $menu;

foot();
?>
