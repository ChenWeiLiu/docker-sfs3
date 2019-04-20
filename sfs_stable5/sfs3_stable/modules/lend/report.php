<?php

//$Id: report.php 6731 2012-03-28 01:50:11Z infodaes $
include "config.php";
sfs_check();

//秀出網頁
if(!$remove_sfs3head) head("物品管理統計分析");

//橫向選單標籤
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);

$selection=$_POST['selection']?$_POST['selection']:'類別統計';

$status_arr=array();
$status_arr['類別統計']="SELECT nature as `類別`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY nature";
$status_arr['廠牌統計']="SELECT maker as `廠牌`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY maker";
$status_arr['位置統計']="SELECT position as `位置`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY position";
$status_arr['經銷商統計']="SELECT saler as `經銷商`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY saler";
$status_arr['購買日期統計']="SELECT sign_date as `購買日期`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY sign_date";
$status_arr['機能狀態統計']="SELECT healthy as `機能狀態`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY healthy";
$status_arr['外借日數統計']="SELECT days_limit as `外借日數`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY sign_date";
$status_arr['保固期限統計']="SELECT warranty as `保固期限`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY warranty";
$status_arr['風險評估統計']="SELECT importance as `風險評估`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY importance";
$status_arr['使用年限統計']="SELECT usage_years as `使用年限`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY usage_years";
$status_arr['報廢日期統計']="SELECT crash_date as `報廢日期統計`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY crash_date";
$status_arr['報廢依據統計']="SELECT crashed_reason as `報廢依據`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY crashed_reason";
$status_arr['外借與否統計']="SELECT opened as `外借與否`,count(*) as `數量` FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY opened";
$status_arr['物品借用次數排行']="SELECT a.equ_serial as `物品編號`,b.item as `物品名稱`,count(a.equ_serial) as `次數` FROM equ_record a,equ_equipments b WHERE a.manager_sn=$session_tea_sn AND a.equ_serial=b.serial GROUP BY a.equ_serial,b.item ORDER BY `次數` DESC";
$status_arr['類別借用次數排行']=$sql="SELECT b.nature as `物品類別`,count(a.equ_serial) as `次數` FROM equ_record a,equ_equipments b WHERE a.manager_sn=$session_tea_sn AND a.equ_serial=b.serial GROUP BY b.nature ORDER BY `次數` DESC";
$status_arr['借用者排行']="SELECT b.name as `借用者`,count(a.equ_serial) as `次數` FROM equ_record a,teacher_base b WHERE a.manager_sn=$session_tea_sn AND a.teacher_sn=b.teacher_sn GROUP BY b.name ORDER BY `次數` DESC";
$status_arr['借用日期排行']="SELECT DATE_FORMAT(lend_date,'%Y/%m/%d %W')  as `借用日期`,count(equ_serial) as `次數` FROM equ_record WHERE manager_sn=$session_tea_sn GROUP BY `借用日期` ORDER BY `次數` DESC";
$status_arr['借用日別排行']="SELECT DATE_FORMAT(lend_date,'%W')  as `借用日別`,count(equ_serial) as `次數` FROM equ_record WHERE manager_sn=$session_tea_sn GROUP BY `借用日別` ORDER BY `次數` DESC";
$status_arr['歸還日期排行']="SELECT DATE_FORMAT(refund_date,'%Y/%m/%d %W')  as `歸還日期`,count(equ_serial) as `次數` FROM equ_record WHERE manager_sn=$session_tea_sn GROUP BY `歸還日期` ORDER BY `次數` DESC";
$status_arr['歸還日別排行']="SELECT DATE_FORMAT(refund_date,'%W')  as `歸還日別`,count(equ_serial) as `次數` FROM equ_record WHERE manager_sn=$session_tea_sn GROUP BY `歸還日別` ORDER BY `次數` DESC";
$status_arr['單一物品借用排行']="SELECT a.equ_serial as `編號`,b.item as `物品名稱`,count(a.equ_serial) as `次數` FROM equ_record a,equ_equipments b WHERE a.manager_sn=$session_tea_sn AND a.equ_serial=b.serial GROUP BY a.equ_serial,b.item ORDER BY `次數` DESC";
$status_arr['物品名稱借用排行']="SELECT b.item as `物品名稱`,count(a.equ_serial) as `次數` FROM equ_record a,equ_equipments b WHERE a.manager_sn=$session_tea_sn AND a.equ_serial=b.serial GROUP BY b.item ORDER BY `次數` DESC";
$status_arr['借用者排行']="SELECT b.name as `姓名`,count(a.equ_serial) as `次數` FROM equ_record a,teacher_base b WHERE a.manager_sn=$session_tea_sn AND a.teacher_sn=b.teacher_sn GROUP BY b.name ORDER BY `次數` DESC";
$status_arr['學期借用次數列表']="SELECT year_seme as `學期`,count(equ_serial) as `物品數` FROM equ_record WHERE manager_sn=$session_tea_sn GROUP BY year_seme ORDER BY year_seme DESC";
$status_arr['附記說明統計']="SELECT memo as `附記說明`,count(equ_serial) as `物品數` FROM equ_record WHERE manager_sn=$session_tea_sn GROUP BY memo ORDER BY memo";
$status_arr['借用日數統計']="SELECT TO_DAYS(refund_date)-TO_DAYS(lend_date) as `借用日數`,count(equ_serial) as `物品數` FROM equ_record WHERE manager_sn=$session_tea_sn AND NOT ISNULL(refund_date) GROUP BY `借用日數` ORDER BY `借用日數`";
$status_arr['未歸還物品應歸還日期統計']="SELECT refund_limit as `應歸還日期`,count(equ_serial) as `物品數` FROM equ_record WHERE manager_sn=$session_tea_sn AND ISNULL(refund_date) GROUP BY `應歸還日期` ORDER BY `應歸還日期`";
$status_arr['經管人開放借用物品統計']="SELECT b.name as `姓名`,count(serial) as `物品數` FROM equ_equipments a,teacher_base b WHERE a.opened='Y' AND a.manager_sn=b.teacher_sn GROUP BY `姓名` ORDER BY `物品數`";
$status_arr['經管人物品借用統計']="SELECT b.name as `姓名`,count(equ_serial) as `借用次數` FROM equ_record a,teacher_base b WHERE a.manager_sn=b.teacher_sn GROUP BY `姓名` ORDER BY `借用次數`";

foreach($status_arr as $key=>$value){
	$menu.="<input type='radio' value='$key' name='selection' onclick='this.form.submit()'".($selection==$key?' checked':'').">$key<BR>";
}

$sql=$status_arr[$selection];
$res=$CONN->Execute($sql) or user_error("執行統計分析失敗！<br>$sql",256);
$showdata="<table border='2' cellpadding='8' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>";
$showdata.="<tr bgcolor=$Tr_BGColor>";
for($i=0;$i<$res->FieldCount();$i++){
	$r=$res->fetchfield($i);
	$showdata.="<td align='center'>".$r->name."</td>";
}
$showdata.="</tr>";

while(!$res->EOF) {
	$showdata.="<tr align='center'>";
	for($i=0;$i<$res->FieldCount();$i++){
		$showdata.='<td>'.$res->fields[$i].'</td>';
	}
	$showdata.="</tr>";
	$res->MoveNext();
}
$showdata.="</table>";

$main="<table cellpadding='5' cellspacing='5'>
	<form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	<tr><td valign='top'>$menu</td><td valign='top'>$showdata</td></tr></table></form>";
echo $main;
if(!$remove_sfs3head) foot();

?>