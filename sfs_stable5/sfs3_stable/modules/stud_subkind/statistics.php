<?php
// $Id: statistics.php 8973 2016-09-12 08:14:48Z infodaes $

include_once "config.php";
//include_once "../../include/sfs_case_dataarray.php";
sfs_check();
//秀出網頁
head("人數分析統計");

//橫向選單標籤
echo print_menu($MENU_P,$linkstr);

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

//取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$class_num = $row["class_num"];

if( checkid($SCRIPT_FILENAME,1) OR $class_num) {


$clan_option=($_POST[clan_option]);
$area_option=($_POST[area_option]);
$memo_option=($_POST[memo_option]);
$note_option=($_POST[note_option]);
$ext1_option=($_POST[ext1_option]);
$ext2_option=($_POST[ext2_option]);
$sex_option=($_POST[sex_option]);
$grade_option=($_POST[grade_option]);
$family_option=($_POST[family_option]);

if($clan_option.$area_option.$memo_option.$note_option.$ext1_option.$ext2_option.$family_option=="") $clan_option="on";

//取得學生身份列表
$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";

$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
while (list($d_id,$t_name)=$recordSet->FetchRow()) {
        if ($type_id==$d_id)
                $typedata.="<option value='$d_id' selected>($d_id)$t_name</option>";
        else
                $typedata.="<option value='$d_id'>($d_id)$t_name</option>";
}

//取得學生子身份類別清單資料
$type_select="SELECT * FROM stud_subkind_ref WHERE type_id='$type_id'";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
$sunkind_data=$recordSet->FetchRow();

$clan_title=$sunkind_data[clan_title];
$area_title=$sunkind_data[area_title];
$memo_title=$sunkind_data[memo_title];
$note_title=$sunkind_data[note_title];
$ext1_title=$sunkind_data[ext1_title];
$ext2_title=$sunkind_data[ext2_title];
$sex_title='性別';
$grade_title='年級別';
$family_title='家庭類型';


$group_fields=(($clan_option)?"a.clan,":"").
              (($area_option)?"a.area,":"").
              (($memo_option)?"a.memo,":"").
              (($note_option)?"a.note,":"").
			  (($ext1_option)?"a.ext1,":"").
			  (($ext2_option)?"a.ext2,":"").
			  (($sex_option)?"if(b.stud_sex='1','男','女'),":"").
			  (($grade_option)?"left(b.curr_class_num,1),":"");
			  //(($family_option)?"c.family_kind,":"");

$fields_title=(($clan_option)?"<td align='center'>$clan_title</td>":"").
              (($area_option)?"<td align='center'>$area_title</td>":"").
              (($memo_option)?"<td align='center'>$memo_title</td>":"").
              (($note_option)?"<td align='center'>$note_title</td>":"").
			  (($ext1_option)?"<td align='center'>$ext1_title</td>":"").
			  (($ext2_option)?"<td align='center'>$ext2_title</td>":"").
			  (($sex_option)?"<td align='center'>$sex_title</td>":"").
			  (($grade_option)?"<td align='center'>$grade_title</td>":"");
			  //(($family_option)?"<td align='center'>$family_title</td>":"").
              "<td>人數統計</td>";

$group_fields=substr($group_fields,0,-1);

$sta_options=(($clan_title<>"")?"<input type='checkbox' name='clan_option' ".(($clan_option)?"checked":"")." onclick='this.form.submit()'>$clan_title ":"").
               (($area_title<>"")?"<input type='checkbox' name='area_option' ".(($area_option)?"checked":"")." onclick='this.form.submit()'>$area_title ":"").
               (($memo_title<>"")?"<input type='checkbox' name='memo_option' ".(($memo_option)?"checked":"")." onclick='this.form.submit()'>$memo_title ":"").
               (($note_title<>"")?"<input type='checkbox' name='note_option' ".(($note_option)?"checked":"")." onclick='this.form.submit()'>$note_title ":"").
			   (($ext1_title<>"")?"<input type='checkbox' name='ext1_option' ".(($ext1_option)?"checked":"")." onclick='this.form.submit()'>$ext1_title ":"").
			   (($ext2_title<>"")?"<input type='checkbox' name='ext2_option' ".(($ext2_option)?"checked":"")." onclick='this.form.submit()'>$ext2_title ":"").
			   (($sex_title<>"")?"<input type='checkbox' name='sex_option' ".(($sex_option)?"checked":"")." onclick='this.form.submit()'>$sex_title ":"").
			   (($grade_title<>"")?"<input type='checkbox' name='grade_option' ".(($grade_option)?"checked":"")." onclick='this.form.submit()'>$grade_title ":"");
			   //(($family_title<>"")?"<input type='checkbox' name='family_option' ".(($family_option)?"checked":"")." onclick='this.form.submit()'>$family_title ":"");
/*
// 取出班級陣列
$class_base = class_base($work_year_seme);
*/
//取得統計資料
$type_select="SELECT $group_fields,count(*) as `人數` FROM stud_subkind a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE b.stud_study_cond=0 and a.type_id='$type_id'"; // LEFT JOIN stud_seme_eduh c ON a.stud_id=c.stud_id   
$type_select.=(!checkid($SCRIPT_FILENAME,1) AND $class_num<>'')?" AND b.curr_class_num like '$class_num%'":"";
$type_select.=" GROUP BY $group_fields";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);

$listdata.="<table width='100%' cellspacing='1' cellpadding='3' bgcolor='#FFCCCC'>
             <form name=\"stud_subkind\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">
             <tr>
             <td><img border='0' src='images/pin.gif'>學生身份選項：<select name='type_id' onchange='this.form.submit()'>
             $typedata</select></td></tr><tr><td>※分組統計項目： $sta_options</td></tr>
             </form></table>";
//<input type='submit' value='依選定項目統計列示' name='replace'>
$data=$recordSet->GetRows();

$listdata.="<table bordercolor=#55AAAA border=1 cellspacing=0 cellpadding=5><tr bgcolor=#AAFFAA>$fields_title</tr>";
$total=0;
for($i=0;$i<count($data);$i++)
{
        $listdata.="<tr>";
        //產生資料表
        for($j=0;$j<=count($data[$i])/2-1;$j++) $listdata.="<td align='center'>".($data[$i][$j]?$data[$i][$j]:"---")."</td>";
        $listdata.="</tr>";
        $total+=$data[$i][$j-1];
        }
$listdata.="<tr bgcolor='#CCCCFF'><td colspan=".($j-1)." align='center'>合　　計</td><td align='center'>$total</td></table>";
echo $listdata;


//檢查是否有類別屬性尚未設定
$type_select="SELECT count(student_sn) as members FROM stud_base WHERE stud_kind like '%,$type_id,%' AND stud_study_cond=0";
$type_select.=($class_num<>'')?" AND curr_class_num like '$class_num%'":"";
$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
$data=$recordSet->FetchRow();
if($total<>$data['members'])
        echo "<BR><font color=#FF5555>PS.系統發現身份別設定 [ ".$data['members' ]."] 與類別屬性設定統計數據 [ $total ] 不同，<BR>　 您可能尚有[ ".($data['members']-$total)." ]位此類學生的類別屬性尚未設定！<a href='setsubkind.php?type_id=$type_id'>[<img src='./images/set.gif' border=0>按此設定]</a>";

} else { echo "<h2><center><BR><BR><font color=#FF0000>您並未被授權使用此模組(非導師或模組管理員)</font></center></h2>"; } 
        foot();
?>