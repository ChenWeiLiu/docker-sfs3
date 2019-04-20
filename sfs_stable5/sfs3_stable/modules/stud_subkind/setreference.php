<?php
// $Id: setreference.php 8973 2016-09-12 08:14:48Z infodaes $
include_once "config.php";
sfs_check();
//秀出網頁
head("選單參照設定");

//橫向選單標籤
echo print_menu($MENU_P,$linkstr);

//取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$class_num = $row["class_num"];

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

if( checkid($SCRIPT_FILENAME,1) OR $class_num) {


$can_edit=true;
if($class_num and !checkid($SCRIPT_FILENAME,1) ) { $m_arr = get_sfs_module_set("stud_subkind"); if($m_arr['set_ref']<>'Y') $can_edit=false; }

if($can_edit){
$clan_title=$_POST[clan_title];
$area_title=$_POST[area_title];
$memo_title=$_POST[memo_title];
$note_title=$_POST[note_title];
$ext1_title=$_POST[ext1_title];
$ext2_title=$_POST[ext2_title];
$clan=$_POST[clan];
$area=$_POST[area];
$memo=$_POST[memo];
$note=$_POST[note];
$ext1=$_POST[ext1];
$ext2=$_POST[ext2];



if($clan)
{
   //替換原來的資料
   $replace_Sql="REPLACE stud_subkind_ref(type_id,clan_title,area_title,memo_title,note_title,ext1_title,ext2_title,clan,area,memo,note,ext1,ext2) VALUES ('$type_id','$clan_title','$area_title','$memo_title','$note_title','$ext1_title','$ext2_title','$clan','$area','$memo','$note','$ext1','$ext2')";
   $recordSetYear=$CONN->Execute($replace_Sql) or user_error("寫入失敗！<br>$replace_Sql",256);
   $updatetime=date('m-d h:m:s');
   echo "\n<script language=\"Javascript\"> alert (\"$updatetime 已經將參照設定更新 !!'\")</script>";
}


//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";

$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
while (list($d_id,$t_name)=$recordSet->FetchRow()) {
        if ($type_id==$d_id)
                $typedata.="<option value='$d_id' selected>($d_id)$t_name</option>";
        else
                $typedata.="<option value='$d_id'>($d_id)$t_name</option>";
}


//取得參照資料
$type_select="SELECT * FROM stud_subkind_ref WHERE type_id='$type_id'";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
$data=$recordSet->FetchRow();
$listdata.="<table width='100%' cellspacing='1' cellpadding='3' bgcolor='#FFCCCC'>
<form name=\"sel_stud_kind\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
<tr>
<td><img border='0' src='images/pin.gif'>學生身份：<select name='type_id' onchange='this.form.submit()'>$typedata</select>　
</td></tr>
</form></table>
             <table border=1 cellspacing='1' cellpadding='3' bordercolor='#CCCCFF'>
             <form name=\"clan\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
             <tr bgcolor='#CCCCFF'>
             <td><input type='text' name='clan_title' value='$data[clan_title]'></td>
             <td><input type='text' name='area_title' value='$data[area_title]'></td>
             <td><input type='text' name='memo_title' value='$data[memo_title]'></td>
             <td><input type='text' name='note_title' value='$data[note_title]'></td>
			 <td><input type='text' name='ext1_title' value='$data[ext1_title]'></td>
			 <td><input type='text' name='ext2_title' value='$data[ext2_title]'></td>
             </tr>";
$listdata.="<tr>
         <td><textarea rows=15 name='clan' cols='20'>$data[clan]</textarea></td>
         <td><textarea rows=15 name='area' cols='20'>$data[area]</textarea></td>
         <td><textarea rows=15 name='memo' cols='20'>$data[memo]</textarea></td>
         <td><textarea rows=15 name='note' cols='20'>$data[note]</textarea></td>
		 <td><textarea rows=15 name='ext1' cols='20'>$data[ext1]</textarea></td>
		 <td><textarea rows=15 name='ext2' cols='20'>$data[ext2]</textarea></td>
         </tr>";
$listdata.="<tr><td colspan=6><center><input type='hidden' name='type_id' value='$type_id'>
<input type='submit' value='更改寫入' name='replace'>
<input type='reset' value='回復原值' name='recover'>
　　　<center></td></tr>
</form></table>";
echo $listdata;

} else { echo "<center><BR><BR><h2><a href='setsubkind.php?type_id=$type_id'>系統管理員<BR><BR>並未開放班級導師可以設定參照選單!</a></h2></center>"; }

} else { echo "<h2><center><BR><BR><font color=#FF0000>您並未被授權使用此模組(非導師或模組管理員)</font></center></h2>"; } 
foot();
?>