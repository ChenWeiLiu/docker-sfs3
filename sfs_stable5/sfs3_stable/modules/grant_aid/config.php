<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
include_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// 0.名稱  1.顯示底色  2.印領清冊處理程式  3.申請表處理程式  4.csv格式輸出  5.關鍵字  6.預設金額 ˙7.預設type_id
$menudata = array (
  array("原住民學用費","#FFCCCC","html_export.php","ask_export.php","csv_export.php","原住民",400,9)
 ,array("原住民獎學金","#CCFFCC","html_export2.php","ask_export2.php","csv_export.php","原住民",2000,9)
 ,array("學產助學金","#FFFFAA","html_export3.php","ask_export3.php","csv_export.php","低收入",2000,3)
 ,array("清寒獎學金","#CCCCFF","html_export4.php","ask_export4.php","csv_export.php","低收入",800,3)
 );

//取得身份子別定義
//$type_menu=stud_clan();

//獎助類別
$type=($_REQUEST[type]);
if($type=="") $type=$menudata[0][0];

for($i=0; $i<=count($menudata)-1;$i++)
{
        if($type==$menudata[$i][0]) {
                $menu_id=$i;
                $hint_color=$menudata[$i][1];
                $keyword=$menudata[$i][5];
                $dollars=$menudata[$i][6];
                $type_id=$menudata[$i][7];


                //取得學生子身份類別清單資料
                $type_select="SELECT * FROM stud_subkind_ref WHERE type_id='$type_id'";
                $recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
                $sunkind_data=$recordSet->FetchRow();

                $clan_title=$sunkind_data[clan_title];
                $area_title=$sunkind_data[area_title];
                $memo_title=$sunkind_data[memo_title];
                $note_title=$sunkind_data[note_title];

                $clan_list=explode("\n",$sunkind_data[clan]);
                $area_list=explode("\n",$sunkind_data[area]);
                $memo_list=explode("\n",$sunkind_data[memo]);
                $note_list=explode("\n",$sunkind_data[note]);

                $clan_list=$type_menu[$type_id][$clan_list_title];
                $area_lis=$type_menu[$type_id][$clan_area_title];
                }

        $menu.="<td bgcolor=".$menudata[$i][1]."><font face='標楷體'><a href='./index.php?type=".$menudata[$i][0]."'>".$menudata[$i][0]."</a></td>";
        }
$menu="<table border=0 cellspacing=0 cellpadding=5><tr>".$menu."</tr></table>";

?>