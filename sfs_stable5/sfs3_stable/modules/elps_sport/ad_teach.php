<?php
//$Id: ad_teach.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
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

#####################   新增授權  ###########################

if ($_POST[act]=='add_teacher'){
if ($_POST[tea]=='') backe("未選擇人員！");
if ($_POST[main_id]=='') backe("操作錯誤！");
if ($_POST[pa]=='') backe("未選擇授權項目！");
	foreach($_POST[tea] as $key=>$val) {
	$SQL="INSERT INTO sport_teach (  tmid,teacher_sn ,pa) VALUES ('$_POST[main_id]','$key','$_POST[pa]' )";
	$rs=$CONN->Execute($SQL) or backe("操作錯誤！單人多次授權！");
	}
	header("Location:$PHP_SELF?mid=".$_POST[main_id]);
	}
#####################   移除授權  ###########################
if ($_POST[act]=='del_teacher'){
if ($_POST[tea]=='') backe("未選擇人員！");
if ($_POST[main_id]=='') backe("操作錯誤！");
	foreach($_POST[tea] as $key=>$val) {
	$SQL="delete from  sport_teach where id ='$key' ";
	$rs=$CONN->Execute($SQL) or backe("操作錯誤！");
	}
	header("Location:$PHP_SELF?mid=".$_POST[main_id]);
	}




head("競賽管理");
include_once "menu.php";
include_once "chk.js";
#####################   選單  ###########################
if($_GET[mid]=='') { print_menu($school_menu_p3);}
else {$link2="mid=$_GET[mid]"; print_menu($school_menu_p3,$link2);}

if($_GET[mid]==''){ mmid2();}
else {mmid2($_GET[mid]);
mid_teach($_GET[mid]);}

foot();



#####################   列示主要項目  ###########################
function mmid2($gmid='') {
			global $CONN;
	($gmid=='') ? $SQL="select * from sport_main order by year desc ": $SQL="select * from sport_main where id='$gmid' ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
echo "<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=1 cellpadding=0 bgcolor=#9EBCDD><TR align=center bgcolor='#9EBCDD'><TD width=6%>項次</TD>
	<TD width=30% >名稱 <img src='images/21.gif'><A HREF='$_SERVER[PHP_SELF]'>列示主項目</A></TD>
	<TD width=10%>日期</TD>
	<TD width=27%>班級報名與截止</TD>
	<TD width=27%>大會操作與截止</TD>
</TR>";
for($i=0; $i<$rs->RecordCount(); $i++) {

echo "<TR align=center bgcolor='#FFFFFF'><TD>".$arr[$i][id]."</TD>
	<TD align=left><A HREF='$PHP_SELF?mid=".$arr[$i][id]."'>".$arr[$i][title]."</A></TD>
	<TD>".$arr[$i][year]."</TD>
	<TD style='font-size:9pt;' >".substr($arr[$i][signtime],0,13)." -- ".substr($arr[$i][stoptime],0,13)."</TD>
	<TD style='font-size:9pt;'>".substr($arr[$i][work_start],0,13)." -- ".substr($arr[$i][work_end],0,13)."</TD>
</TR>";
}
echo "</TABLE>";
}
#####################   列示主要項目  ###########################
function mid_teach($mid) {
			global $CONN;
	$SQL="select a.*,b.name  from sport_teach a left join teacher_base b on a.teacher_sn=b.teacher_sn  where tmid='$mid' ";
	$rs0=$CONN->Execute($SQL) or die($SQL);
	$arr0=$rs0->GetArray();
echo "<TABLE border=0 width=100% style='font-size:11pt;'  cellspacing=0 cellpadding=0 bgcolor=#9EBCDD>
<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>\n<INPUT TYPE='hidden' name='main_id' value='$mid'><INPUT TYPE='hidden' name='act' value=''>
<TR  bgcolor='#FFFFFF'><TD><img src='images/21.gif'><B>授權人員列表</B>
<INPUT TYPE='text' NAME='ifo' value='' size='20' disabled
style=' border-width: 0px; background-color:White; font-size:12pt;color:red;'>
<input TYPE='image' align='top' border=0 SRC='images/ch_back2.gif' 
onclick=\"this.form.reset();return false;\" alt='重新選擇' 
onmouseover=\"f1.ifo.value='重新選擇';\" onmouseout=\"f1.ifo.value='';\">

<input TYPE='image' align='top' border=0 SRC='images/ch_cancel.gif' 
onclick=\" if (window.confirm('將鉤選者移除授權？')){this.form.act.value='del_teacher';this.form.sumit();}return false;\" alt='將鉤選者移除授權' 
onmouseover=\"f1.ifo.value='將鉤選者移除授權';\" onmouseout=\"f1.ifo.value='';\">


</TD><TR bgcolor='#FFFFFF'><TD>
";
$tea1='';$tea2='';$cname='';
for($i=0; $i<$rs0->RecordCount(); $i++) {
	$cname="<INPUT TYPE='checkbox' NAME='tea[".$arr0[$i][id]."]' value='".$arr0[$i][name]."'>";
	$cname.=$arr0[$i][name]."&nbsp;\n";

($arr0[$i][pa]==1) ? $tea1.=$cname:$tea1;
($arr0[$i][pa]==2) ? $tea2.=$cname:$tea2;

	}

echo "
<div style='margin-left: 20pt;'><B style='font-size:12pt;color:#800000'>系統管理人員：</B>$tea2 <BR>
<B style='font-size:12pt;color:#800000'>會務操作人員：</B>$tea1 
</div>
<TD></TR></FORM>";

	$SQL="select teacher_sn,name  from  teacher_base   where teach_condition=0 order by  birthday ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
	echo "<FORM METHOD=POST ACTION='$PHP_SELF' name='f2'>\n<INPUT TYPE='hidden' name='main_id' value='$mid'><INPUT TYPE='hidden' name='act' value=''>";

echo "<TR  bgcolor='#FFFFFF'><TD><img src='images/21.gif'><B>全校人員列表</B>
<INPUT TYPE='text' NAME='ifo' value='' size='20' disabled
style=' border-width: 0px; background-color:White; font-size:12pt;color:red;'>
<input TYPE='image' align='top' border=0 SRC='images/ch_back2.gif' 
onclick=\"this.form.reset();return false;\" alt='重新選擇' 
onmouseover=\"f2.ifo.value='重新選擇';\" onmouseout=\"f2.ifo.value='';\">

<input TYPE='image' align='top' border=0 SRC='images/ch_save.gif' 
onclick=\" if (window.confirm('將鉤選者加入授權？')){this.form.act.value='add_teacher';this.form.sumit();}return false;\" alt='將鉤選者加入授權' 
onmouseover=\"f2.ifo.value='將鉤選者加入授權';\" onmouseout=\"f2.ifo.value='';\">
</TD><TR bgcolor='#FFFFFF'><TD>
授權項目：
<SELECT NAME='pa'><option value=''>未選擇</option>
<option value='1'>會務操作員</option><option value='2'>系統管理員</option></SELECT>
</TD><TR bgcolor='#FFFFFF'><TD>";

for($i=0; $i<$rs->RecordCount(); $i++) {
	$bb='';$cname='';
	for($x=0; $x<$rs0->RecordCount(); $x++) {
	($arr[$i][teacher_sn]==$arr0[$x][teacher_sn]) ? $bb=" disabled ":$bb;
	}
	$cname="<INPUT TYPE='checkbox' NAME='tea[".$arr[$i][teacher_sn]."]' value='".$arr[$i][name]."' $bb>";
	$cname.=$arr[$i][name]."&nbsp;\n";
	echo $cname;
if ($i%8==7 && $i!=0 ) echo "<BR>\n";
	}
echo "</FORM><TD></TR></TABLE>";
}




?>
