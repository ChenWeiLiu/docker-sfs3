<?php

// $Id: home_count.php 7711 2013-10-23 13:07:37Z smallduh $

include "config.php";
//取得選擇學年度及學期，如:0921
$select_year_seme=$_POST["select_year_seme"];

$year=get_curr_year($select_year_seme);//取得選擇之學年，如92
$semester=get_curr_seme($select_year_seme);//取得選擇之學期，如1

if(empty($year))$year=curr_year();//預設為本年度
if(empty($semester))$semester=curr_seme();//預設為本學期

//echo "ys=".$select_year_seme."<br>"."y=".$year."<br>"."s=".$semester;

if (!empty($_POST["look"])) save_csv($year,$semester);//串流送出資料

head("家庭狀況統計");

//列出統計

$main=list_class_stu($year,$semester);
echo $main  ;

foot();
//列出所有班級的資料
function list_class_stu($year,$semester){
	global $menu_p,$CONN ,$school_kind_name ,$class_year;
	$toolbar=&make_menu($menu_p);

        //底下使用select_yearseme_form 顯示學年度學期下拉選單
        $select_yearseme_form="<form method='post' action='".basename($_SERVER["PHP_SELF"])."'>";
        $class_seme_p = get_class_seme(); //學年度
        $upstr = "<select name='select_year_seme' onchange='this.form.submit()'>";
        while (list($tid,$tname)=each($class_seme_p)){
        	if ((strlen($year.$semester)==3 and $tid=="0".$year.$semester) or (strlen($year.$semester)==4 and $tid==$year.$semester))
              		$upstr .= "<option value='".$tid."' selected>".$tname."</option>";//$tid如"0921"
              	else
              		$upstr .= "<option value='".$tid."'>".$tname."</option>";
        }
        $upstr .= "</select><br>";
        $select_yearseme_form.= $upstr;
        //select_yearseme_form 結束

        //下載資料按鈕
        $download_data.="<form method='post' action='".basename($_SERVER["PHP_SELF"])."'>";
        $download_data.="<input type='submit' name='look' value='下載詳細資料(xls)'></form>";

        if (strlen($year)==2)//將選擇年度轉成0921格式
        $select_year_seme="0".$year.$semester;
          else
           $select_year_seme=$year.$semester;

        //找出家庭種類
        $record_home=SFS_TEXT("家庭類型");

        $title1.="<table border=1 cellspacing=0><tr bgcolor=yellow><td>年級</td><td>性別</td>";
        //找出年級統計
        reset($record_home);
        //開始秀出欄位(家庭類型)
        while ($array_home = each($record_home)) {
              $title1.="<td width='60' align='center'>".$array_home[value]."</td>";
        }
        $title1.="</tr>";

	$sql_select = "select DISTINCT c_year from school_class where year='$year' and semester='$semester' order by c_year,c_sort";
	$record_year_class=$CONN->Execute($sql_select) or die($sql_select);
        while ($array_class = $record_year_class->FetchRow()) {
              reset($record_home);
              $title1.="<tr align='center'><td>".$class_year[$array_class[c_year]]."</td><td>男</td>";
              while ($array_home = each($record_home)) {
                      //從班級表中找出當年度男性學生座號
                      $sql_select="select b.stud_id from stud_base a, stud_seme b where a.student_sn=b.student_sn and a.stud_sex='1'and a.stud_study_cond=0 and b.seme_year_seme='$select_year_seme' and substring(b.seme_class,1,1)='$array_class[c_year]'";
                      $record_class_st=$CONN->Execute($sql_select) or die($sql_select);
                      $count_st=0;
                      //開始比對並統計人數
                      while ($array_student = $record_class_st->FetchRow()) {
                            $sql_select="select * from stud_seme_eduh where seme_year_seme='$select_year_seme' and stud_id='$array_student[stud_id]' and sse_family_kind='$array_home[key]'";
                            $record_select=$CONN->Execute($sql_select) or die($sql_select);
                            if ($record_select->RecordCount()!=0) $count_st++;
                      }
                      $all_boy[$array_home[key]]+=$count_st;//男生小計
                      $title1.="<td>".$count_st."</td>";
              }
              $title1.="</tr>";
              $title1.="<tr align='center'><td>".$class_year[$array_class[c_year]]."</td><td>女</td>";
              reset($record_home);
              while ($array_home = each($record_home)) {
                      //從班級表中找出當年度女性學生座號
                      $sql_select="select b.stud_id from stud_base a, stud_seme b where a.student_sn=b.student_sn and a.stud_sex='2' and a.stud_study_cond=0 and b.seme_year_seme='$select_year_seme' and substring(b.seme_class,1,1)='$array_class[c_year]'";
                      $record_class_st=$CONN->Execute($sql_select) or die($sql_select);
                      $count_st=0;
                      //開始比對並統計人數
                      while ($array_student = $record_class_st->FetchRow()) {
                            $sql_select="select * from stud_seme_eduh where seme_year_seme='$select_year_seme' and stud_id='$array_student[stud_id]' and sse_family_kind='$array_home[key]'";
                            $record_select=$CONN->Execute($sql_select) or die($sql_select);
                            if ($record_select->RecordCount()!=0) $count_st++;
                      }
                      $all_girl[$array_home[key]]+=$count_st;//女生小計
                      $title1.="<td>".$count_st."</td>";
              }
        }
        //列出全校男女生統計結果(按家庭總類)
        $title1.="</tr><tr align='center'><td rowspan='2'>全校</td><td>男</td>";
        reset($all_boy);
        while(list($id,$val)=each($all_boy)){
             $title1.="<td>$val</td>";
             $all[$id]+=$val;
        }
        $title1.="</tr><tr align='center'><td>女</td>";
        reset($all_girl);
        while(list($id,$val)=each($all_girl)){
             $title1.="<td>$val</td>";
             $all[$id]+=$val;
        }
        reset($all);
        $title1.="</tr><tr align='center' bgcolor='#bb8833'><td colspan='2'>合計</td>";
        while(list($id,$val)=each($all)){
             $title1.="<td>$val</td>";
        }

        $title1.="</tr></table>";
	$main="$toolbar";

	$help_text="
	本程式主要依據學生之[學期輔導]中[家庭類型]欄位為分類統計之依據。||按下載詳細資料，可取得更多資訊。||下載檔案之[家庭狀況]欄位值，係依據學生[輔導訪談]資料中，尋找[聯絡事項]欄位以[通報]二字開頭之該筆訪談紀錄內容所列。若該學生[訪談紀錄]並未有此筆資料，則該欄位值留空。";
	$help=&help($help_text);
	$main.=$help;

	$main.="<table><tr><td width='40%'>$select_yearseme_form</td><td>$download_data</td></tr></table>";
        $main.=$title1;
        
	return $main;
}


function save_csv($year,$semester){
       	global $CONN;
        $select_year_seme=(strlen($year.$semester)==4) ? $year.$semester : "0".$year.$semester;//設定欲搜尋之學年度及學期

        //找出選擇年度學期之班級
	$sql_select = "select c_year,class_id,c_name,c_sort from school_class where year='$year' and semester='$semester' order by c_year,c_sort";
	$record_year_class=$CONN->Execute($sql_select) or die($sql_select);
        $num_class=$record_year_class->RecordCount();//班級小計
        if ($num_class<1){
           echo "錯誤，找不到班級設定！";
           exit;
        }
        //輸出
   	$filename="home_".$select_year_seme.".xls";
    	header("Content-disposition: filename=$filename");
    	header("Content-type: application/octetstream");
    	//header("Content-type: application/octetstream");
    	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
    	header("Expires: 0");


        //秀出各班符合資料
        echo "<table border='1'><tr><td colspan='15'>";
        echo $year."年度 第".$semester."學期 學生家庭類型關係資料一覽</td></tr>";
        echo "<tr>";
        echo "<td>年</td><td>班</td><td>班級</td><td>學號</td><td>座號</td><td>姓名</td><td>性別</td><td>父母關係</td><td>家庭類型</td><td>家庭氣氛</td><td>父管教方式</td><td>母管教方式</td><td>居住情形</td><td>經濟狀況</td><td>其他缺漏</td><td>家庭狀況</td></tr>";
        while ($array_class = $record_year_class->FetchRow()) {
              //挑出符合資料
              if (strlen($year)==2)//將選擇年度轉成0921格式
                 $select_year_seme="0".$year.$semester;
                else
                 $select_year_seme=$year.$semester;
              if (strlen($array_class[c_sort])==1)//將班級轉成701格式
                 $select_class=$array_class[c_year]."0".$array_class[c_sort];
                else
                 $select_class=$array_class[c_year].$array_class[c_sort];
              //從班級表中找出當年度該班學生資料
              $sql_select="select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_num from stud_base a, stud_seme b where a.student_sn=b.student_sn and a.stud_study_cond=0 and b.seme_year_seme='$select_year_seme' and b.seme_class='$select_class'";
              $record_class_st=$CONN->Execute($sql_select) or die($sql_select);
              //開始比對家庭狀況紀錄
              while ($array_student = $record_class_st->FetchRow()) {
                    //送出資料
                    $array_class[c_year]=($array_class[c_year]>6)?$array_class[c_year]-6:$array_class[c_year];
                    echo "<tr>";
                    echo "<td>$array_class[c_year]</td><td>$array_class[c_name]</td><td>$array_class[c_year]".substr(strrchr($array_class[class_id],"_"),1)."</td>";
                    echo "<td>$array_student[stud_id]</td><td>$array_student[seme_num]</td><td>$array_student[stud_name]</td>";
                    if ($array_student[stud_sex]==1) echo "<td>男</td>";
                      else echo "<td>女</td>";
                    $sql_select="select * from stud_seme_eduh where seme_year_seme='$select_year_seme' and stud_id='$array_student[stud_id]'";
                    $record_select=$CONN->Execute($sql_select) or die($sql_select);
                    //找到學生家庭紀錄
                    $f_flag=0;//尋找標記
                    while ($array_stud=$record_select->FetchRow()){
                          echo "<td>".get_sfs_text("父母關係",$array_stud[sse_relation])."</td>";
                          echo "<td>".get_sfs_text("家庭類型",$array_stud[sse_family_kind])."</td>";
                          echo "<td>".get_sfs_text("家庭氣氛",$array_stud[sse_family_air])."</td>";
                          echo "<td>".get_sfs_text("管教方式",$array_stud[sse_farther])."</td>";
                          echo "<td>".get_sfs_text("管教方式",$array_stud[sse_mother])."</td>";
                          echo "<td>".get_sfs_text("居住情形",$array_stud[sse_live_state])."</td>";
                          echo "<td>".get_sfs_text("經濟狀況",$array_stud[sse_rich_state])."</td>";
                          $nothing="";
                          if ($array_stud[sse_s1]=="") $nothing.="*最喜愛科目";
                          if ($array_stud[sse_s2]=="") $nothing.="*最困難科目";
                          if ($array_stud[sse_s3]=="") $nothing.="*特殊才能";
                          if ($array_stud[sse_s4]=="") $nothing.="*興趣";
                          if ($array_stud[sse_s5]=="") $nothing.="*生活習慣";
                          if ($array_stud[sse_s6]=="") $nothing.="*人際關係";
                          if ($array_stud[sse_s7]=="") $nothing.="*外向行為";
                          if ($array_stud[sse_s8]=="") $nothing.="*內向行為";
                          if ($array_stud[sse_s9]=="") $nothing.="*學習行為";
                          if ($array_stud[sse_s10]=="") $nothing.="*不良習慣";
                          if ($array_stud[sse_s11]=="") $nothing.="*焦慮行為";
                          echo "<td>".$nothing."</td>";
                          $f_flag=1;
                    }
                   if ($f_flag==0) echo "<td></td><td></td><td></td><td></td><td></td><td><td></td></td><td></td>";
                   
                   $sql_select="select sst_memo from stud_seme_talk where seme_year_seme + 90 ".">"." '$select_year_seme' and stud_id='$array_student[stud_id]' and sst_main like '通報%'";
                   $record_talk=$CONN->Execute($sql_select) or die($sql_select);
                   //從訪談紀錄表中找到學生家庭狀況
                   $f_flag=0;//尋找標記
                   while ($array_talk=$record_talk->FetchRow()){
                         echo "<td>$array_talk[sst_memo]</td>";
                         $f_flag=1;
                   }
                   if ($f_flag==0) echo "<td></td>";
                   echo "</tr>";
              }
        }     echo "</table>";
	exit;
}


?>
