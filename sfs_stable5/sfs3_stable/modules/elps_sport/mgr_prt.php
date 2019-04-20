<?php
//$Id: mgr_prt.php 8769 2016-01-13 14:16:55Z qfon $
include "config.php";
//認證
sfs_check();
//if ($_POST){
//	echo "<PRE>";print_r($_POST);print_r($_GET);echo "</PRE>";
//	die();
//	}

#####################   權限檢查與時間  ###########################
if($_GET[mid] || $_POST[mid] ) {
	($_GET[mid] == '') ? $cmid=$_POST[mid]: $cmid=$_GET[mid];
	if(ch_mid_t($cmid)!=3 ) backe("非操作時間");
	}
$ad_array=who_is_root();
if (!is_array($ad_array[$_SESSION[session_tea_sn]])){
if ($_POST[mid] || $_GET[mid] || $_POST[main_id] ) {
	$bb='';
	($_POST[mid]!='' ) ? $bb=$_POST[mid]:$bb;
	($_GET[mid]!='' ) ? $bb=$_GET[mid]:$bb;
	($_POST[main_id]!='' ) ? $bb=$_POST[main_id]:$bb;
if (check_man($_SESSION[session_tea_sn],$bb ,1)!='YES'   ) backe("您無權限操作");
	if(ch_mid_t($bb)!=3) backe("非操作時間");

}}


//秀出網頁布景標頭
head("競賽報名");


include_once "menu.php";
include_once "chk.js";

if($_GET[mid]=='') { print_menu($school_menu_p2);}
else {$link2="mid=$_GET[mid]&item=$_GET[item]&sclass=$_GET[sclass]"; print_menu($school_menu_p2,$link2);}

mmid($_GET[mid]);
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function add(n) {
	var str1=document.f1.allitem.value;
	var str2=new String(n);
	if (str1.indexOf(str2,0)==-1) {
	document.f1.allitem.value=str1+n;}
	else {
	document.f1.allitem.value=str1.replace(n,'');}
}
function add2(n) {
	var str1=document.f1.allclass.value;
	var str2=new String(n);
	if (str1.indexOf(str2,0)==-1) {
	document.f1.allclass.value=str1+n;}
	else {
	document.f1.allclass.value=str1.replace(n,'');}
}

//-->
</SCRIPT>
<?php

//echo "<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>\n<INPUT TYPE='hidden' name='act' value=''>";
if ($_GET[mid]!='') echo item_list($_GET[mid]);
//if ($_GET[item]!='') stud_list($_GET[mid],$_GET[item]);
// if ($class_num!='' && $_GET[mid]!='') stud_list($class_num);
//$color_sex[$arr[$i][stud_sex]]

//echo "</FORM>";









//佈景結尾
foot();




#####################  列示項目   #############################
function item_list($mid){
		global $CONN,$sportclass,$sportname,$itemkind;
		$class_name_arr = class_base() ;
		$class_numa=substr($class_num,0,1);
	$SQL="select *  from sport_item   where  mid='$mid' and  skind=0   order by  kind, enterclass ";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$arr=$rs->GetArray();
//	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' group by itemid ";
//	$arr_1=initArray("itemid,nu",$SQL);//全部人數
//	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportnum!='' group by itemid ";
//	$arr_2=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and  sportorder!=0 group by itemid ";
	$arr_3=initArray("itemid,nu",$SQL);//己檢錄人數(編排順序)
//	$SQL="select itemid ,count(id) as nu from  sport_res   where  mid='$mid' and (results!=0 or results!='' ) group by itemid ";
//	$arr_4=initArray("itemid,nu",$SQL);//有成績人數
//$_GET[mid] && $_GET[item] && $_GET[Spk] && $_GET[kitem]
	$ss="<table border=0 width='100%' style='font-size:10pt;'  cellspacing=1 cellpadding=0 bgcolor=silver>
	<FORM METHOD=POST ACTION='$PHP_SELF' name='f1'>
	<tr bgcolor=white><td><INPUT TYPE='hidden' name='act' value=''><INPUT TYPE='reset' value='重選項目' class=bur><INPUT TYPE='button' value='印出鉤選的比賽項目人員' onclick=\"window.open('mgr_prt.1.php?mid=$mid&item=all&kitem=item&Spk='+this.form.allitem.value,'','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur>
	<INPUT TYPE='text' NAME='allitem' value='' size='50' disabled class=bur>
	<BR>";
for($i=0; $i<$rs->RecordCount(); $i++) {
		($arr_3[$arr[$i][id]]=='') ? $Nu3=0:$Nu3=$arr_3[$arr[$i][id]];
		( $arr[$i][sportkind]==1)  ? $color="#d0691e":$color="#696969";
		$ss.="<INPUT TYPE='checkbox' NAME='sel_item' value='a".$arr[$i][id]."_' onclick=\"add(this.value);\"><FONT COLOR='$color'>".$sportclass[$arr[$i][enterclass]].$sportname[$arr[$i][item]].$itemkind[$arr[$i][kind]].
		"</FONT>&nbsp;($Nu3)\n";
	($i%5==4 && $i!=0) ? $ss.="<BR>":$ss;
	}
#####################  列示班級   #############################
$ss.="<hr color=#800000 size=1><FONT COLOR='#696969'><INPUT TYPE='reset' value='重選班級' class=bur><INPUT TYPE='button' value='印出鉤選的班別的參賽人員' onclick=\"window.open('mgr_prt.1.php?mid=$mid&item=all&kitem=sclass&Spk='+this.form.allclass.value,'','scrollbars=yes,resizable=yes,height=500,width=600');\" class=bur>
<INPUT TYPE='text' NAME='allclass' value='' size='50' disabled class=bur>

<BR>";
$y=0;
	foreach($class_name_arr as $key=>$val) {
		$ss.="<INPUT TYPE='checkbox' NAME='sclass' value='a".$key."_' onclick=\"add2(this.value);\">$val\n";
		($y%8==7 && $y!=0) ? $ss.="<BR>":$ss;
		$y++;
		}
$ss.="</FONT></td></tr></FORM></table>";

Return $ss;
}



#####################  列示項目   #############################

function link_a($sclass){
	$class_name_arr = class_base() ;
	$ss="<FORM name=p2>選擇班級：<select name='link2' size='1' class='small' onChange=\"if(document.p2.link2.value!='')change_link(document.p2.link2.value);\"> ";
	foreach($class_name_arr as $key=>$val) {
		($sclass==$key) ? $cc=" selected":$cc="";
		$ss.="<option value='$PHP_SELF?mid=$_GET[mid]&sclass=$key'$cc>$val</option>\n";
	}
	$ss.="</select></FORM>";
Return $ss;
}


?>