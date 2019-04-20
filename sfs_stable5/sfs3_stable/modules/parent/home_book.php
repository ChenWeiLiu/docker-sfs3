<?php

// $Id: home_book.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
include "./config.php";
//引入函數

//使用者認證
sfs_check();

$month=($_GET[month])?$_GET[month]:$_POST[month];
$year=($_GET[year])?$_GET[year]:$_POST[year];
$day=($_GET[day])?$_GET[day]:$_POST[day];

$act=($_GET[act])?$_GET[act]:$_POST[act];
$link_sn=($_GET[link_sn])?$_GET[link_sn]:$_POST[link_sn];
$content=($_GET[content])?$_GET[content]:$_POST[content];
$use1=($_GET[use1])?$_GET[use1]:$_POST[use1];
$date=($_GET['date'])?$_GET['date']:$_POST['date'];
$today=($_GET['today'])?$_GET['today']:$_POST['today'];
$re_link=($_GET[re_link])?$_GET[re_link]:$_POST[re_link];
$stud_id=($_GET[stud_id])?$_GET[stud_id]:$_POST[stud_id];
$class_id=($_GET[class_id])?$_GET[class_id]:$_POST[class_id];
foreach($_POST['member'] as $K=> $V) $member[$K]=$V;


if(!empty($_GET[this_date]) or !empty($_POST[this_date])){
	$this_date=($_GET[this_date])?$_GET[this_date]:$_POST[this_date];
	$d=explode("-",$this_date);
	$year=$d[0];
	$month=$d[1];
	$day=$d[2];
}


if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//由stud_id找班級和導師流水號
//global $stud_id;
//echo $stud_id;
$classinfo=stud_id_to_classinfo($stud_id);
$class_id=$classinfo[class_id];
$teacher_sn=$classinfo[teacher_sn];
//echo $class_id;
//echo $teacher_sn;
if(empty($year))$year=date("Y");
if(empty($month))$month=date("m");
if(empty($day))$day=date("d");
$week=date ("w", mktime(0,0,0,$month,$day,$year));
$right=&getMonthView($year,$month,$day,$stud_id);
$this_date=$year."-".$month."-".$day;

if($act=="del"){//家長僅能刪除自己發佈的留言
	$sql_del="delete from parent_link where link_sn='$link_sn'";
	$CONN->Execute($sql_del);
	header("Location:{$_SERVER['PHP_SELF']}?this_date=$this_date&stud_id=$stud_id");

}
elseif($act=="edit"){
	//秀出網頁
	head("班級事務");

	echo print_menu($menu_p);
	
	$sql_select="select * from parent_link where link_sn='$link_sn'";
	$rs_select=$CONN->Execute($sql_select);
	$content=$rs_select->fields['content'];
	$parent_link=$rs_select->fields['parent_link'];	
	$content=br2nl($content);
	$parent_link_A=explode(",",$parent_link);
	$teacher_link=$rs_select->fields['teacher_link'];
	$author_sn==$rs_select->fields['author_sn'];
		
	$main="
		<form action='{$_SERVER['PHP_SELF']}?act=save_edit' method='POST' name='form_edit'>      
		<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#000000' class='small' valign='top'>
		<tr bgcolor='#FEFBDA'>
		<td>
		<table bgcolor='#D844EB' cellspacing='1' cellpadding='3' align='center'  width='100%' valign='top'>
		<tr bgcolor='#FFCAF8'>
		<td><textarea name='content' cols='60' rows='10' >$content</textarea></td>
		<td valign='top'><input type='submit' name='submit_edit' value='送出'></td>
		<input type='hidden' name='link_sn' value='$link_sn'>
		<input type='hidden' name='stud_id' value='$stud_id'>	
		<input type='hidden' name='this_date' value='$this_date'>
		</table>
		</td>
		</tr>
		</table>			
		</form>			
	";
}

elseif($act=="save_edit"){
	$time=date("Y-m-d H:i:s");
	$parent_link=implode(",",$member);
	$content=nl2br($content);
	$sql_edit="update parent_link set time='$time',content='$content' where link_sn='$link_sn'";
	$CONN->Execute($sql_edit);
	header("Location:{$_SERVER['PHP_SELF']}?this_date=$this_date&stud_id=$stud_id");
}


elseif($act=="new"){
	//秀出網頁
	head("班級事務");

	echo print_menu($parent_menu_p);	
	//echo $student_sn_A[1];
	//$parent_A=&get_parent($student_sn_A);
	//echo "姓名".$parent_A[0][name];
	
	$main="
		<form action='{$_SERVER['PHP_SELF']}?act=save_new' method='POST' name='form_new'>      
		<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#000000' class='small' valign='top'>
		<tr bgcolor='#FEFBDA'>
		<td>
		<table bgcolor='#D844EB' cellspacing='1' cellpadding='3' align='center'  width='100%' valign='top'>
		<tr bgcolor='#FFCAF8'>
		<td><textarea name='content' cols='60' rows='10' ></textarea></td>
		<td valign='top'><input type='submit' name='submit_new' value='送出'></td>
		<input type='hidden' name='stud_id' value='$stud_id'>
		<input type='hidden' name='this_date' value='$this_date'>
		</table>
		</td>
		</tr>
		</table>
		</form>				
	";


}
elseif($act=="save_new"){	
	$date=date($this_date);
	$time=date("Y-m-d H:i:s");
	
	$teacher_link=$teacher_sn;		
	$parent_link=$parent_sn;
	$content=nl2br($content);
	$author_sn="p".$parent_sn;
	//echo $date.$time;
	$sql_new="insert into parent_link(author_sn,date,time,class_id,teacher_link,parent_link,content) values('$author_sn','$date','$time','$class_id','$teacher_link','$parent_link','$content')";		
	$CONN->Execute($sql_new);
	header("Location:{$_SERVER['PHP_SELF']}?this_date=$this_date&stud_id=$stud_id");
}
elseif($act=="respon"){
	//秀出網頁
	head("班級事務");

	echo print_menu($menu_p);
	
	$sql_select="select * from parent_link where link_sn='$link_sn'";
	$rs_select=$CONN->Execute($sql_select);
	$content=$rs_select->fields['content'];
	$parent_link=$rs_select->fields['parent_link'];	
	$date=$rs_select->fields['date'];
	$content=br2nl($content);
	$parent_link_A=explode(",",$parent_link);
	$teacher_link=$rs_select->fields['teacher_link'];
	$author_sn==$rs_select->fields['author_sn'];	
	$use_content=($use1==1)?"<div style='margin-left: 20px;'>$content</div><hr style='width: 98%; height: 1px;'  noshade='noshade'>":"";
	//這一篇文章的發言者是誰？回應者是誰？
	
		

	$main="
		<form action='{$_SERVER['PHP_SELF']}?act=save_respon' method='POST' name='form_respone'>      
		<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#000000' class='small' valign='top'>
		<tr bgcolor='#FEFBDA'>
		<td>
		<table bgcolor='#D844EB' cellspacing='1' cellpadding='3' align='center'  width='100%' valign='top'>
		<tr bgcolor='#FFCAF8'>
		<td><textarea name='content' cols='60' rows='10' >$use_content</textarea></td>
		<td valign='top'><span class='button'><a href='{$_SERVER['PHP_SELF']}?act=respon&use1=1&link_sn=$link_sn&stud_id=$stud_id'>引文</a></span><input type='submit' name='submit_respone' value='送出'  ></td>								
		<input type='hidden' name='date' value='$date'>
		<input type='hidden' name='re_link' value='$link_sn'>
		<input type='hidden' name='stud_id' value='$stud_id'>
		</table>
		</td>
		</tr>
		</table>
		</form>				
	";

}
elseif($act=="save_respon"){		
	$time=date("Y-m-d H:i:s");	
	$teacher_link=$teacher_sn;
	$parent_link=$parent_sn;
	$content=nl2br($content);
	$author_sn="p".$parent_sn;
	//echo $date.$time;
	$sql_respon="insert into parent_link(author_sn,date,time,class_id,teacher_link,parent_link,content,re_link) values('$author_sn','$date','$time','$class_id','$teacher_link','$parent_link','$content','$re_link')";		
	//echo $sql_respon;
	$CONN->Execute($sql_respon) or die($sql_respon);
	header("Location:{$_SERVER['PHP_SELF']}?this_date=$date&stud_id=$stud_id");
}


else{
	//秀出網頁
	head("班級事務");	
	$class_name=class_id_to_full_class_name($class_id);
	echo print_menu($parent_menu_p);
	$sql_link="select * from parent_link where date='$this_date' and  class_id='$class_id' and (parent_link like '%,$parent_sn,%' or  parent_link like '%,$parent_sn' or parent_link like '$parent_sn,%' or parent_link='$parent_sn') order by link_sn DESC";		
	
	$rs_link=$CONN->Execute($sql_link) or die($sql_link);
	while(!$rs_link->EOF){
		$link_sn[$i]=$rs_link->fields['link_sn'];
		$author_sn[$i]=$rs_link->fields['author_sn'];
		$date[$i]=$rs_link->fields['date'];
		$time[$i]=$rs_link->fields['time'];
		$class_id[$i]=$rs_link->fields['class_id'];
		$teacher_link[$i]=$rs_link->fields['teacher_link'];
		$parent_link[$i]=$rs_link->fields['parent_link'];
		$content[$i]=$rs_link->fields['content'];
		$content[$i]="<small style='font-style: italic; background-color: rgb(255, 255, 153);'>聯$link_sn[$i]</small><br>".$content[$i];
		$re_link[$i]=$rs_link->fields['re_link'];	

		//作者轉換中文姓名
		$author_name[$i]=&get_author_name($author_sn[$i]);
		if($author_sn[$i]=="p".$parent_sn){		
		$data.="<br>
			<table bgcolor='#4A56A3' cellspacing='1' cellpadding='3' align='center'  width='100%' valign='top'>
				<tr bgcolor='#FFFFFF'><td colspan='5'>$content[$i]</td></tr>
				<tr bgcolor='#A3C7FD'><td>張貼者：$author_name[$i]</td><td>時間：$time[$i]</td><td><span class='button'><a href='$_SERVER[PHP_SELF]?link_sn=$link_sn[$i]&act=del&this_date=$this_date&stud_id=$stud_id'>刪除</a></span></td>
					<td><span class='button'><a href='$_SERVER[PHP_SELF]?link_sn=$link_sn[$i]&act=edit&this_date=$this_date&stud_id=$stud_id'>編修</a></span></td><td><span class='button'><a href='$_SERVER[PHP_SELF]?link_sn=$link_sn[$i]&act=respon&this_date=$this_date&stud_id=$stud_id'>回應</a></span></td>	
				</tr>
			</table>";
		}
		else{
		$data.="<br>
			<table bgcolor='#D844EB' cellspacing='1' cellpadding='3' align='center'  width='100%' valign='top'>
				<tr bgcolor='#FFFFFF'><td colspan='3'>$content[$i]</td></tr>
				<tr bgcolor='#FFCAF8'><td>張貼者：$author_name[$i]</td><td>時間：$time[$i]</td>
					<td><span class='button'><a href='$_SERVER[PHP_SELF]?link_sn=$link_sn[$i]&act=respon&this_date=$this_date&stud_id=$stud_id'>回應</a></span></td>	
				</tr>
			</table>";								
		}	
		$i++;
		$rs_link->MoveNext();	
	}	
	
	
	$main="
		<table width='100%' cellspacing='1' cellpadding='3' align='center' bgcolor='#000000' class='small' valign='top'>
		<tr bgcolor='#FEFBDA'>
		<td>
		<font class='dateStyle'>$year</font>
		年
		<font class='dateStyle'>$month</font>
		月
		<font class='dateStyle'>$day</font>（星期".$week_array[$week]."）<font class='dateStyle'>".$class_name."</font> 家庭聯絡簿
		<a href='$_SERVER[PHP_SELF]?act=&this_date=$today&stud_id=$stud_id' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
		<span class='button'><a href='$_SERVER[PHP_SELF]?act=new&this_date=$this_date&stud_id=$stud_id' class='box'>新增</a></span><br>
		$data
		</td>
		</tr>
		</table>
	";
}
//顯示在網頁上的畫面
echo "<table width='100%'><tr><td width='70%' valign='top'>".$main."</td><td align='right' valign='top'>".$right."</td></tr></table>";


//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";
//程式檔尾
foot();

//取得月行事曆
function &getMonthView($year="",$month="",$day="",$stud_id,$mode=""){
	global $today;
	$cal = new MyCalendar;
	$cal->setStartDay(1);	
	$mc=($mode=="viewThing")?$cal->getMonthThingView($month,$year,$day):$cal->getMonthView_with_stud_id($month,$year,$day,$stud_id);
	$main="
	<table cellspacing='1' cellpadding='2' bgcolor='#000000' class='small'>
	<tr bgcolor='#FEFBDA'><td align='center'>
	<a href='$_SERVER[PHP_SELF]?act=$act&this_date=$today&stud_id=$stud_id' class='box'><img src='images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
	</td></tr>
	<tr bgcolor='#FFFFFF'><td>$mc</td></tr>
	</table>
	";
	return $main;
}


//轉換<br>為換行字元\n
function br2nl($message=""){
	$message=str_replace ("<br>","",$message);
	$message=str_replace ("<br/>","",$message);
	$message=str_replace ("<br />","",$message);
	$message=str_replace ("<BR>","",$message);
	$message=str_replace ("<BR/>","",$message);
	$message=str_replace ("<BR />","",$message);
	return $message;
}

//由聯絡簿parent_link作者的流水號找出他的姓名
function &get_author_name($author_sn){
	global $CONN;
	$A=substr($author_sn,0,1);
	$sn=substr($author_sn,1);
	//$A=t老師，$A=p家長
	if($A=="t"){//老師
		$sql="select name from teacher_base where teacher_sn='$sn'";		
		$rs=$CONN->Execute($sql);
		$name=$rs->fields['name'];		
	}
	elseif($A=="p"){//家長
		//找出家長的身份正號
		$sql="select parent_id from parent_auth where parent_sn='$sn'";
		$rs=$CONN->Execute($sql);
		$parent_id=$rs->fields['parent_id'];
		//找出家長名單
		$sql_name="select guardian_name from stud_domicile where  guardian_p_id='$parent_id'";
		$rs_name=$CONN->Execute($sql_name);
		$name=$rs_name->fields['guardian_name'];			
	}			
	return $name;
}

//刪除聯絡簿parent_link該link_sn和下層的有關link_sn
function search_and_sn($link_sn){
	global $CONN;
	
	$sql_del="delete from parent_link where link_sn='$link_sn'";
	$CONN->Execute($sql_del);
	
	$sql_select="select link_sn from parent_link where re_link='$link_sn'";
	$rs_select=$CONN->Execute($sql_select);	
	$link_sn=$rs_select->fields['link_sn'];
	if($link_sn!=""){
		search_and_sn($link_sn);
	}
}	

//由stud_id找出class_id班級和teacher_sn導師
function  stud_id_to_classinfo($stud_id){
    global $CONN,$sel_year,$sel_seme;
	$rs=&$CONN->Execute("select  curr_class_num  from  stud_base where stud_id='$stud_id'");
    //echo "select  curr_class_num  from  stud_base where stud_id='$stud_id'";
	$curr_class_num=$rs->fields['curr_class_num'];
	$class_year=substr($curr_class_num,0,-4);	
	$class_sort=substr($curr_class_num,-4,-2);
	$class_year_sort=$class_year.$class_sort;
	//echo $class_year_sort;
    $class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$class_year,$class_sort);	
	$rs=&$CONN->Execute("select  teacher_sn  from  teacher_post where class_num='$class_year_sort'");
    $teacher_sn=$rs->fields['teacher_sn'];	
	$classinfo[class_id]=$class_id;
	$classinfo[teacher_sn]=$teacher_sn;
	return $classinfo;
}
/*
//由class_id找出幾年幾班
function  class_id_to_full_class_name($class_id){
    global $CONN;
    $class_sql="select * from school_class where class_id='$class_id'";
    $rs_class=$CONN->Execute($class_sql);
    $c_year= $rs_class->fields['c_year'];
    $c_name= $rs_class->fields['c_name'];
    $school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
    $full_year_class_name=$school_kind_name[$c_year];
    $full_year_class_name.=$c_name."班";
    return $full_year_class_name;
}
*/
?>



<style type="text/css">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial, Helvetica, sans-serif;}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
