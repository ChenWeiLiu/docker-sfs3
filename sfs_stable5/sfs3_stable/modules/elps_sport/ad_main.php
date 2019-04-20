<?php
//$Id: ad_main.php 7119 2013-02-08 07:44:45Z chiming $
include "config.php";
//認證
sfs_check();
#####################   權限檢查  ###########################
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])){
if ($_POST[mid] || $_GET[mid] || $_POST[main_id] ) {
	$bb='';
	($_POST[mid]!='' ) ? $bb=$_POST[mid]:$bb;
	($_GET[mid]!='' ) ? $bb=$_GET[mid]:$bb;
	($_POST[main_id]!='' ) ? $bb=$_POST[main_id]:$bb;
if (check_man($_SESSION[session_tea_sn],$bb ,2)!='YES'   ) backe("您無權限操作");
}}
#####################   權限檢查結束  ###########################

if ($_POST['act']=='main_add') {
	if (  $_POST['title']=='' || $_POST['memo']=='') {backinput("資料不齊！回上頁重填！");exit; }
//	$cratetime=date("Y-m-d");
//	$nowday=date("YmdHis");
	$title=strip_tags($_POST['title']);$memo=strip_tags($_POST['memo']);
	$signtime=$_POST[ya0]."-".$_POST[ya1]."-".$_POST[ya2]." ".$_POST[ya3].":".$_POST[ya4].":00";
	$stoptime=$_POST[yb0]."-".$_POST[yb1]."-".$_POST[yb2]." ".$_POST[yb3].":".$_POST[yb4].":00";
	$work_start=$_POST[yc0]."-".$_POST[yc1]."-".$_POST[yc2]." ".$_POST[yc3].":".$_POST[yc4].":00";
	$work_end=$_POST[yd0]."-".$_POST[yd1]."-".$_POST[yd2]." ".$_POST[yd3].":".$_POST[yd4].":00";
	$cratetime=$_POST[yy0]."-".$_POST[yy1]."-".$_POST[yy2];
// echo $crsql;
//////////////////將每一資料表名稱記錄到主資料表內/////////////////
	$sqlinsert =" INSERT INTO sport_main (  title , year , signtime , stoptime , work_start , work_end , memo ) VALUES ( '$title', '$cratetime', '$signtime', '$stoptime', '$work_start', '$work_end','$memo') ";
	$CONN->Execute($sqlinsert)or die($sqlinsert);
	$go=$PHP_SELF;
	header("location:$go");
	}
/////////////////處理比賽更新---開始//////////////////////////////////
if ($_POST[act]=='main_updata' && $_POST[title]!='' && $_POST[memo]!='' && $_POST[id]!='' ) {
	$signtime=$_POST[ya0]."-".$_POST[ya1]."-".$_POST[ya2]." ".$_POST[ya3].":".$_POST[ya4].":00";
	$stoptime=$_POST[yb0]."-".$_POST[yb1]."-".$_POST[yb2]." ".$_POST[yb3].":".$_POST[yb4].":00";
	$work_start=$_POST[yc0]."-".$_POST[yc1]."-".$_POST[yc2]." ".$_POST[yc3].":".$_POST[yc4].":00";
	$work_end=$_POST[yd0]."-".$_POST[yd1]."-".$_POST[yd2]." ".$_POST[yd3].":".$_POST[yd4].":00";
	$cratetime=$_POST[yy0]."-".$_POST[yy1]."-".$_POST[yy2];
	$sql_up="update sport_main set title='$_POST[title]' , memo='$_POST[memo]',year='$cratetime' , signtime='$signtime' , stoptime='$stoptime',work_start='$work_start' , work_end='$work_end' where id='$_POST[id]' ";
	$CONN->Execute($sql_up);
	header("location:$PHP_SELF");
	}
/////////////////處理比賽刪除---開始//////////////////////////////////
if ($_GET[act]=='del') {
//	$rs = $CONN->Execute(" select * from sport_main where id='$_GET[mid]' ");
//	$arr = $rs->GetArray();
	$chk_sqla="select id from sport_item where mid = '$_GET[mid]' ";
	$chk_sqlb="select id from sport_res  where mid = '$_GET[mid]' ";
//	echo($chk_sqlb);
	$rsa = $CONN->Execute($chk_sqla);
	$rsb = $CONN->Execute($chk_sqlb);
	$nua=$rsa->RecordCount();
	$nub=$rsb->RecordCount();
	if ($nua!=0 || $nub!=0 ) backe("已有資料記錄無法刪除");
		$SQL="DELETE FROM sport_main WHERE id='$_GET[mid]' ";
		$rs=$CONN->Execute($SQL);
	header("location:$PHP_SELF");
	}











head("競賽管理");
include_once "menu.php";
include_once "chk.js";
#####################   選單  ###########################
if($_GET[mid]=='') { print_menu($school_menu_p3);}
else {$link2="mid=$_GET[mid]"; print_menu($school_menu_p3,$link2);}
//print_menu($school_menu_p2);

mmid2($_GET[mid]);
if($_GET[act]=='edit'&& $_GET[mid]!='' ) midtb($_GET[mid]);
if($_GET[act]=='tb') midtb();



foot();



#####################   列示主要項目  ###########################
function mmid2($mid) {
			global $CONN; //left join sport_res c (on b.mid=a.id ) 
	$SQL="select * from sport_main order by year desc ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();

	$SQL1="select mid, count(id) as bnu  from sport_item  group by mid ";
	$rsa=$CONN->Execute($SQL1) or die($SQL1);
	$arr1=$rsa->GetArray();
	$SQL2="select mid, count(id) as cnu  from sport_res  group by mid ";
	$rsb=$CONN->Execute($SQL2) or die($SQL2);
	$arr2=$rsb->GetArray();
//print_r($arr1);
//print_r($arr2);

echo "<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD><TR align=center bgcolor='#9EBCDD'><TD width=5%>項次</TD>
	<TD width=25% >名稱&nbsp;<img src='images/21.gif'><A HREF='$PHP_SELF?act=tb'>新增主項目</A></TD>
	<TD width=6%>日期</TD>
	<TD width=19%>報名與截止</TD>
	<TD width=19%>操作與截止</TD>
	<TD width=5%>項目</TD>
	<TD width=5%>人次</TD>
	<TD width=16%>選項 </TD>
</TR>";
for($i=0; $i<$rs->RecordCount(); $i++) {
	$nu1=0;$nu2=0;
	for($x=0; $x<$rsa->RecordCount(); $x++) {
		($arr[$i][id]==$arr1[$x][mid]) ? $nu1=$arr1[$x][bnu]: $nu1=$nu1;
		}
	for($x=0; $x<$rsb->RecordCount(); $x++) {
		($arr[$i][id]==$arr2[$x][mid]) ? $nu2=$arr2[$x][cnu]: $nu2=$nu2;
		}

echo "<TR align=center bgcolor='#FFFFFF'><TD>".$arr[$i][id]."</TD>
	<TD align=left><A HREF='$PHP_SELF?mid=".$arr[$i][id]."'>".$arr[$i][title]."</A></TD>
	<TD>".$arr[$i][year]."</TD>
	<TD style='font-size:9pt;' >".substr($arr[$i][signtime],0,13)."<BR>".substr($arr[$i][stoptime],0,13)."</TD>
	<TD style='font-size:9pt;'>".substr($arr[$i][work_start],0,13)."<BR>".substr($arr[$i][work_end],0,13)."</TD>
	<TD>".$arr[$i][bnu]."$nu1</TD>
	<TD>".$arr[$i][bnu]."$nu2</TD>
	<TD><A HREF='$PHP_SELF?act=edit&mid=".$arr[$i][id]."'>修改</A>
		<A HREF='$PHP_SELF?act=del&mid=".$arr[$i][id]."'>刪除</A></TD></TR>";
}
echo "</TABLE>";
}

#####################   列示主要項目  ###########################
function midtb($mid='') {
			global $CONN;
if($mid!='') {
	$SQL="select * from sport_main where id='$mid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	}
echo"<FORM METHOD=POST ACTION='$PHP_SELF' name='aa'>
<table  border='0' borderColor='#b1cbe4' cellPadding='0' cellSpacing='0' width=100%>
<tr><td align='center' bgColor='#b1cbe4'  colspan='2'>
開辦比賽
</td></tr>
<tr><INPUT TYPE='hidden'>
<td align='right'>資料編號</td>
<td><input name='id' size='5' value='".$arr[0][id]."'  readonly style='background-color:silver;'></td>
</tr>
<tr>
<td align='right' >主題名稱</td>
<td><input name='title' size='40' value='".$arr[0][title]."'  class=ipmei></td>
</tr>
<tr>
<td align='right' >比賽開始日期</td>
<td>";
if($arr=='') {
	$yy=array(date("Y"),date("m"),date("d"));
	$ya=array(date("Y"),date("m"),date("d"),date("G"),date("i"));
	$yb=array(date("Y"),date("m"),date("d"),date("G"),date("i"));
	$yc=array(date("Y"),date("m"),date("d"),date("G"),date("i"));
	$yd=array(date("Y"),date("m"),date("d"),date("G"),date("i"));
}
else {
	$yy=split("[- :]",$arr[0][year]);
	$ya=split("[- :]",$arr[0][signtime]);
	$yb=split("[- :]",$arr[0][stoptime]);
	$yc=split("[- :]",$arr[0][work_start]);
	$yd=split("[- :]",$arr[0][work_end]);
}

echo set_time_select("yy0",date("Y")-5,date("Y")+5,$yy[0])."年&nbsp;";//年度
echo set_time_select("yy1",1,13,$yy[1])."月&nbsp;";//月月
echo set_time_select("yy2",1,32,$yy[2])."日</tr>";//日日

echo"<tr><td align='right' >報名開始時間</td><td>";
echo set_time_select("ya0",date("Y")-5,date("Y")+5,$ya[0])."年&nbsp;";//年度年
echo set_time_select("ya1",1,13,$ya[1])."月&nbsp;";//月月
echo set_time_select("ya2",1,32,$ya[2])."日&nbsp;";//日日
echo set_time_select("ya3",0,24,$ya[3])."時&nbsp;";//時
echo set_time_select("ya4",0,60,$ya[4])."分";//日分
echo"</tr><tr><td align='right' >報名結束時間</td><td>";
echo set_time_select("yb0",date("Y")-5,date("Y")+5,$yb[0])."年&nbsp;";//年度
echo set_time_select("yb1",1,13,$yb[1])."月&nbsp;";//月月
echo set_time_select("yb2",1,32,$yb[2])."日&nbsp;";//日日
echo set_time_select("yb3",0,24,$yb[3])."時&nbsp;";//日時
echo set_time_select("yb4",0,60,$yb[4])."分</td></tr>";//分

echo"<tr><td align='right' >大會人員操作開始時間</td><td>";
echo set_time_select("yc0",date("Y")-5,date("Y")+5,$yc[0])."年&nbsp;";//年度年
echo set_time_select("yc1",1,13,$yc[1])."月&nbsp;";//月月
echo set_time_select("yc2",1,32,$yc[2])."日&nbsp;";//日日
echo set_time_select("yc3",0,24,$yc[3])."時&nbsp;";//時
echo set_time_select("yc4",0,60,$yc[4])."分";//日分
echo"</tr><tr><td align='right' >大會人員操作結束時間</td><td>";
echo set_time_select("yd0",date("Y")-5,date("Y")+5,$yd[0])."年&nbsp;";//年度
echo set_time_select("yd1",1,13,$yd[1])."月&nbsp;";//月月
echo set_time_select("yd2",1,32,$yd[2])."日&nbsp;";//日日
echo set_time_select("yd3",0,24,$yd[3])."時&nbsp;";//日時
echo set_time_select("yd4",0,60,$yd[4])."分</td></tr>";//分
echo"

<tr>
<td align='right' >前言說明</td>
<td><textarea cols='40' name='memo' rows='5' wrap='virtual' class=ipmei>".$arr[0][memo]."</textarea></td>
</tr>
<tr><td align='center'   colspan='2'>";
if($arr) {echo "<INPUT TYPE='hidden' name=act value='main_updata'>";}
	else {echo "<INPUT TYPE='hidden' name=act value='main_add'>";}
echo"
<input name='B1' type='submit' value='輸入完成'>
<input name='B2' type='reset' value='重新設定'>
<input name='B3' type='button' value='返回' onclick=\"location='$PHP_SELF'\">
</td></tr>
</table>
</FORM>";
}




?>