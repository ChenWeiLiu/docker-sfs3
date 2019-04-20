<?php
//$Id: record.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_class_absent.php";

//認證
sfs_check();

if ($_GET[act]=="edit") {
	if ($_GET[id]) {
		$query="select * from teacher_absent where id='".$_GET[id]."'";
		$res=$CONN->Execute($query);
		if ($res->fields[0]) {
			$_POST=$res->FetchRow();
			$sel_year=$_POST[year];
			$sel_seme=$_POST[semester];
			$_POST[agent_sn]=$_POST[deputy_sn];
			$_POST[act]="edit";
		}
	}
}

if ($_POST[teacher_sn]) {
	$a=new absent("teacher");
	$smarty->assign("abs_kind",tea_abs($_POST[abs_kind],$a->absent_kind_arr)); 
	$mor=date("Y-m-d H:i",mktime(8,0,0,date("m"),date("d"),date("Y")));
	$smarty->assign("morning",$mor); 
	$eve=date("Y-m-d H:i",mktime(16,0,0,date("m"),date("d"),date("Y")));
	$smarty->assign("evening",$eve); 
	$smarty->assign("agent_menu",teacher_menu("agent_sn",$_POST[agent_sn],$_POST[teacher_sn])); 
}

if ($_POST[sure]) {

	$day=$_POST[day];
	$hour=$_POST[hour];
	
	$sn=$_POST[teacher_sn];
	$abs_kind=$_POST[abs_kind];
	$reason=$_POST[reason];

	$note=$_POST[note];

	$start_date=$_POST[start_date];
	$end_date=$_POST[end_date];
	$agent_sn=$_POST[agent_sn];
	$class_dis=$_POST[class_dis];
	$status=$_POST[status];
	$locale=$_POST[locale];

 	$month = substr($start_date,5,2);   //月
	$post_k=teacher_post_k($sn);

    $fileName = '';
    if (is_file($_FILES['note_file']['tmp_name'])) {
        if (!check_is_php_file($_FILES['note_file']['name'])) {
            $temp = explode('.',$_FILES['note_file']['name']);
            $fileName = time().'.'.end($temp);
            $filePath = set_upload_path("/school/teacher_absent");

            if (!copy($_FILES['note_file']['tmp_name'],$filePath.$fileName)){
                echo "檔案上傳失敗!請重新送出!<br>";
                foot();
                exit;
            }
        }
        else{
            echo "警告：請勿上傳php檔！<br>";
            foot();
            exit;
        }
    }

	if ($_POST[act]=="add"){		
		$query="insert into teacher_absent (year,semester,month,teacher_sn,reason,abs_kind,start_date,
		end_date,class_dis,deputy_sn,record_id,record_date,day,hour,note,post_k,locale,note_file)
		values ('$sel_year','$sel_seme','$month','$sn','$reason','$abs_kind','$start_date','$end_date',
		'$class_dis','$agent_sn','$_SESSION[session_log_id]','".date("Y-m-d H:i:s")."','$day','$hour','$note',
		'$post_k','$locale','$fileName')";
		$CONN->Execute($query);
		header("Location: deputy.php?year_seme=$sel_year"."_"."$sel_seme");
	}
	else {

		$a->set_id($_POST[id]);
		$id=$_POST[id];
        // 如果上傳案先刪除舊檔
        if ($fileName){
            $query = "SELECT note_file FROM teacher_absent where id='$id'";
            $res = $CONN->execute($query);
            $row = $res->fetchRow();

            unlink($filePath.$row['note_file']);
			$note_file_SQL=" , note_file='$fileName' ";
        }else{$note_file_SQL='';}
		$query="update teacher_absent set year='$sel_year',semester='$sel_seme',month='$month',teacher_sn='$sn',
		reason='$reason',abs_kind='$abs_kind',start_date='$start_date',end_date='$end_date',
		class_dis='$class_dis',deputy_sn='$agent_sn',record_id='$_SESSION[session_log_id]',
		record_date='".date("Y-m-d H:i:s")."',day='$day',hour='$hour' ,note='$note',post_k='$post_k',
		locale='$locale' $note_file_SQL  where id='$id'";
		$CONN->Execute($query);		
		header("Location: deputy.php?year_seme=$sel_year"."_"."$sel_seme");
	}
}




$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","請假登錄"); 
$smarty->assign("SFS_MENU",$school_menu_p); 



$smarty->assign("id",$_POST[id]); 

$smarty->assign("course_menu",course_menu($_POST[class_dis])); 
$smarty->assign("status_menu",status_menu($_POST[status])); 


//選擇學期
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme));
//選擇教師
$smarty->assign("leave_teacher_menu",teacher_menu("teacher_sn",$_POST[teacher_sn])); 
//選擇假別
$smarty->assign("abs_kind",tea_abs($_POST[abs_kind],$a->absent_kind_arr)); 

$smarty->assign("tea_arr",my_teacher_array());
$smarty->assign("abs_kind_arr",$a->absent_kind_arr);
$smarty->assign("course_kind_arr",$course_kind);
$smarty->assign("status_kind_arr",$status_kind);



$smarty->display('record_a.tpl'); 

