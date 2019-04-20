<?php
//$Id: list.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_class_absent.php";

//認證
sfs_check();

$a=new absent("teacher");

$sort_style=intval($_POST['sort_style']);
$view2ok=intval($_POST['view2ok']);

if ($_POST[edit]) {
	foreach($_POST['edit'] as $id=>$val);
	header("Location: record.php?act=edit&id=$id");
} elseif ($_POST[del]) {
	foreach($_POST['del'] as $id=>$val);
	$act="del";
	$a->set_id($id);
	$a->del_absent();
	$query = "delete from teacher_absent_course where a_id ='$id'";
	$CONN->Execute($query);

} elseif ($_POST[supply]) {
	foreach($_POST['supply'] as $id=>$val);
	header("Location: supply.php?act=edit&id=$id");	
}

if ($_POST[class_t]) {
	foreach($_POST['class_t'] as $id=>$val);
	header("Location: class.php?id=$id");
}
if ($_POST[outlay]) {
	foreach($_POST['outlay'] as $id=>$val);
	header("Location: outlay.php?id=$id");
}


//新的更新機制
$target=$_POST['go'];
if ($_POST['act']) {
	foreach($_POST[$target] as $key=>$id){
		switch($target){
			case 'deputy':	$query="update teacher_absent set status='1',{$target}_date='".date("Y-m-d H:i:s")."' where id='$id'"; break;
			default: $query="update teacher_absent set {$target}_sn='$_SESSION[session_tea_sn]',{$target}_date='".date("Y-m-d H:i:s")."' where id='$id'";			
		}
		$CONN->Execute($query);		
	}	
}

//刪除簽核
if($_POST[deputy_c]) {
		foreach($_POST['deputy_c'] as $id=>$val);
		$query="update teacher_absent set status='0',deputy_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}

if($_POST[check1_c]) {
	foreach($_POST['check1_c'] as $id=>$val);
	$query="update teacher_absent set check1_sn='0',check1_date='".date("Y-m-d H:i:s")."' where id='$id'";
	$CONN->Execute($query);
}

if($_POST[check2_c]) {
	foreach($_POST['check2_c'] as $id=>$val);
	$query="update teacher_absent set check2_sn='0',check2_date='".date("Y-m-d H:i:s")."' where id='$id'";
	$CONN->Execute($query);
}

if($_POST[check3_c]) {
	foreach($_POST['check3_c'] as $id=>$val);
	$query="update teacher_absent set check3_sn='0',check3_date='".date("Y-m-d H:i:s")."' where id='$id'";
	$CONN->Execute($query);
}

if($_POST[check4_c]) {
	foreach($_POST['check4_c'] as $id=>$val);
	$query="update teacher_absent set check4_sn='0',check4_date='".date("Y-m-d H:i:s")."' where id='$id'";
	$CONN->Execute($query);
}


/*

if ($_POST[deputy]) {
	list($id,$v)=each($_POST[deputy]);
		$query="update teacher_absent set status='1',deputy_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
} elseif ($_POST[deputy_c]) {
	list($id,$v)=each($_POST[deputy_c]);
		$query="update teacher_absent set status='0',deputy_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}

if ($_POST[check1]) {
	list($id,$v)=each($_POST[check1]);
		$query="update teacher_absent set check1_sn='$_SESSION[session_tea_sn]',check1_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
} elseif ($_POST[check1_c]) {
	list($id,$v)=each($_POST[check1_c]);
		$query="update teacher_absent set check1_sn='0',check1_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}

if ($_POST[check2]) {
	list($id,$v)=each($_POST[check2]);
		$query="update teacher_absent set check2_sn='$_SESSION[session_tea_sn]',check2_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
} elseif ($_POST[check2_c]) {
	list($id,$v)=each($_POST[check2_c]);
		$query="update teacher_absent set check2_sn='0',check2_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}

if ($_POST[check3]) {
	list($id,$v)=each($_POST[check3]);
		$query="update teacher_absent set check3_sn='$_SESSION[session_tea_sn]',check3_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
} elseif ($_POST[check3_c]) {
	list($id,$v)=each($_POST[check3_c]);
		$query="update teacher_absent set check3_sn='0',check3_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}

if ($_POST[check4]) {
	list($id,$v)=each($_POST[check4]);
		$query="update teacher_absent set check4_sn='$_SESSION[session_tea_sn]',check4_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
} elseif ($_POST[check4_c]) {
	list($id,$v)=each($_POST[check4_c]);
		$query="update teacher_absent set check4_sn='0',check4_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}

*/

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","假單處理"); 
$smarty->assign("SFS_MENU",$school_menu_p); 




//選擇學期
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme));
//選擇教師
$smarty->assign("leave_teacher_menu",teacher_menu("teacher_sn",$_POST[teacher_sn])); 
//選擇教師
$smarty->assign("leave_deputy_menu",teacher_menu("deputy_sn",$_POST[deputy_sn])); 

//選擇月份
$smarty->assign("month",month_menu($_POST[month])); 

//選擇是否確定
$smarty->assign("d_check4_menu",d_make_menu("是否確定",$_POST[d_check4] , $check_arr,"d_check4",1)); 

// 判斷是否為管理權限
$isAdmin=(int)checkid($_SERVER[SCRIPT_FILENAME],1);
//若無管理權，判斷是否具有核章權
if (!$isAdmin)$isAdmin=(int)checkid_sign($_SERVER[SCRIPT_FILENAME],2);

$smarty->assign("abs_kind_arr",$a->absent_kind_arr);
$smarty->assign("tea_arr",my_teacher_array2());
$smarty->assign("course_kind_arr",$course_kind);
$smarty->assign("status_kind_arr",$status_kind);
$smarty->assign("session_tea_sn",$_SESSION[session_tea_sn]);
$smarty->assign("check1",$check1);
$smarty->assign("check2",$check2);
$smarty->assign("check3",$check3);
$smarty->assign("check4",$check4);
$smarty->assign("isAdmin",$isAdmin);
$smarty->assign("view2ok",$view2ok);

$query1.=" a.year='$sel_year' and a.semester='$sel_seme' AND a.teacher_sn = b.teacher_sn   ";

// 登入者所在處室
if (!$isAdmin) {
	$query = "SELECT * FROM teacher_post WHERE teacher_sn={$_SESSION['session_tea_sn']}";
	$res=$CONN->Execute($query);
	$user_post_office = $res->fields['post_office'];
// 	echo $user_post_office;
	$query1 .= " AND b.post_office=$user_post_office ";

}



if ($_POST[teacher_sn]) {
$query1 .=" and a.teacher_sn='$_POST[teacher_sn]'";
}

if ($_POST[deputy_sn]) {
$query1 .=" and a.deputy_sn='$_POST[deputy_sn]'";
}

if ($_POST[d_check4]==1) {
	$query1 .=" and a.check4_sn > 0 ";
}else{
	$query1 .=" and a.check4_sn = 0 ";
}

if ( $_POST[month] ) {
$query1 .=" and a.month='$_POST[month]'";
}


if ( $_POST[abs_kind] ) {
$query1 .=" and a.abs_kind='$_POST[abs_kind]'";
}

//取得所有人請假資料
$query="select a.*,c.title_kind from teacher_absent a , teacher_post b , teacher_title c  where " .$query1.
" AND b.teach_title_id=c.teach_title_id and  (a.class_dis='2' or a.class_dis='4') ";

$query .=" order by a.class_dis, a.start_date  desc ";

$res=$CONN->Execute($query);

$TT=$res->GetRows();

$smarty->assign("absent",$TT);
//選擇假別

foreach($TT as $v) 
{
$tid=$v["id"];
$tsn=$v["teacher_sn"];	
$query2="select * from teacher_post c,pro_check_new d where c.teacher_sn='$tsn' and d.id_sn=c.teach_title_id and d.is_admin='3'";
$rs=$CONN->Execute($query2);
$cb[$tsn]=$rs->RecordCount()?3:0;

 if ($cb[$tsn]==3)
 {
 $query="update teacher_absent set check2_sn='1',check2_date='".date("Y-m-d H:i:s")."' where id='$tid' and status=1";
 $CONN->Execute($query);
 }

}
$smarty->assign("isnotteacher",$cb);

$smarty->assign("abs_kind",tea_abs($_POST[abs_kind],$a->absent_kind_arr));
$smarty->assign('upload_url',$UPLOAD_URL);
$smarty->display('deputy_a.tpl'); 



