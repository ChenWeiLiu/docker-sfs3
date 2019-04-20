<?php
// $Id: class.php 9194 2018-01-12 03:59:09Z smallduh $

/*引入學務系統設定檔*/
include "config.php";
$year_seme=$_REQUEST['year_seme'];
$year_name=$_REQUEST['year_name'];
$me=$_REQUEST['me'];
$ss_id=$_REQUEST['ss_id'];
$test_sort=$_REQUEST['test_sort'];
$kind=$_REQUEST['kind'];
//使用者認證
sfs_check();
//取得學年學期
if ($year_seme) {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
}
//學期資料表名稱
$score_semester="score_semester_".$sel_year."_".$sel_seme;

if ($_POST[dokey]=='儲存') {
	save_semester_score($sel_year,$sel_seme);
	$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$year_name,$me);
	cal_seme_score($sel_year,$sel_seme,$class_id,$ss_id);
} else if ($_POST[file_out]<>'')
	download_score($sel_year,$sel_seme);
else if ($_POST[file_in]<>'')
	import_score($sel_year,$sel_seme);
elseif($_POST['file_date']=="成績檔案匯入")
	save_import_score('class.php');

//選單
$year_seme_menu=year_seme_menu($sel_year,$sel_seme);
$class_year_menu=class_year_menu($sel_year,$sel_seme,$year_name);
if($year_name)	$class_year_name_menu=class_name_menu($sel_year,$sel_seme,$year_name,$me);
if($year_name && $me)	$stage_menu=stage_menu($sel_year,$sel_seme,$year_name,$me,$curr_sort);

//領域科目名稱
$subject_arr = get_subject_name_arr();

//取得本學期班級陣列
$class_name_arr = class_base();

//下拉選單變數轉換
$curr_sort = $_POST[curr_sort];
if ($curr_sort=='')
	$curr_sort = $_GET[curr_sort];

//階段名稱陣列
$test_sort_name=array("","第一階段","第二階段","第三階段","第四階段","第五階段","第六階段","第七階段","第八階段","第九階段","第十階段",255 => "全學期");

// 建立學期成績資料表
//--------------------
$creat_table_sql="CREATE TABLE  if not exists $score_semester (
		  score_id bigint(10) unsigned NOT NULL auto_increment,
		  class_id varchar(11) NOT NULL default '',
		  student_sn int(10) unsigned NOT NULL default '0',
		  ss_id smallint(5) unsigned NOT NULL default '0',
		  score float unsigned NOT NULL default '0',
		  test_name varchar(20) NOT NULL default '',
		  test_kind varchar(10) NOT NULL default '定期評量',
		  test_sort tinyint(3) unsigned NOT NULL default '0',
		  update_time datetime NOT NULL default '0000-00-00 00:00:00',
		  sendmit enum('0','1') NOT NULL default '1',
 		  teacher_sn smallint(6) NOT NULL default '0',
		  PRIMARY KEY  (student_sn,ss_id,test_kind,test_sort),
		  UNIQUE KEY score_id (score_id))";
$CONN->Execute($creat_table_sql);

if ($year_name && $me) {
	//階段下拉選單 ------------
	$class_id = sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$year_name,$me);
	$query = "select subject_id,subject_name from score_subject where enable='1'";
	$res = $CONN->Execute($query);
	while (!$res->EOF) {
		$subject_arr[$res->fields['subject_id']]=$res->fields['subject_name'];
		$res->MoveNext();
	}
	$query = "select distinct a.ss_id,b.scope_id,b.subject_id,b.print from score_course a, score_ss b where a.ss_id=b.ss_id and a.class_id='$class_id' and b.need_exam=1 order by b.sort,b.sub_sort";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	while (!$res->EOF) {
		$ssid=$res->fields['ss_id'];
		$s=$res->fields['subject_id'];
		if ($s==0) $s=$res->fields['scope_id'];
		$ss_arr[$ssid]=$subject_arr[$s];
		$print_arr[$ssid]=$res->fields['print'];
		$res->MoveNext();
	}

	//產生下拉選單
	$sel= new drop_select();
	$sel->s_name = "ss_id";
	$sel->id = $ss_id;
	$sel->is_submit = true;
	$sel->arr = $ss_arr;
	$sel->font_style="";
	$sel->top_option = "選擇科目";
	$select_ss_bar = $sel->get_select();	

	if ($ss_id) {
		$print=$print_arr[$ss_id];
		// 科目完整時(含階段及學期成績),才出現階段下拉選單
		if ($print) {
			//將班級字串轉為陣列
			$class_arr=class_id_2_old($class_id);
			$query = "select performance_test_times,score_mode,test_ratio from score_setup where class_year='$class_arr[3]' and year='$sel_year' and semester='$sel_seme' and enable='1'";
			$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			//測驗次數
			$performance_test_times = $res->fields[performance_test_times];
			//成績配分比例相關設定
			$score_mode = $res->fields[score_mode];
			//比率
			$test_ratios = $res->fields[test_ratio];

			if ($curr_sort <254 && $curr_sort> $performance_test_times)
				$curr_sort='';
			//如果未選擇階段時自動取得下個階段
			//除平時成績外,階段成績須逐次匯到教務處 
			$temp_script = '';
			if ($curr_sort=='' || ($_POST[curr_sort_hidden] <>'' and $curr_sort<>$_POST[curr_sort_hidden]) and $curr_sort<254) {
				//計算目前應在第幾階段 (sendmit = 0 表示已送至教務處成績)
				$query ="select max(test_sort) as mm from $score_semester where class_id='$class_id' and ss_id='$ss_id' and sendmit='0' and test_sort<254";
				$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
				$mm = $res->fields[0]+1;
				if ($curr_sort =='')
					$curr_sort = $mm;
				if ($curr_sort>$performance_test_times)
					$curr_sort = $performance_test_times;
			}
			//比率換算
			if($score_mode=="all"){
				$test_ratio=explode("-",$test_ratios);
			}elseif($score_mode=="severally"){
				$temp_arr=explode(",",$test_ratios);
				$i=$curr_sort-1;
				$test_ratio=explode("-",$temp_arr[$i]);
			}else{
				$test_ratio[0]=60;
				$test_ratio[1]=40;
			}

			//產生下拉選單項目陣列
			for($i=1;$i<= $performance_test_times;$i++)
				$test_times_arr[$i] = "第 $i 階段";

			//如果不是每一階段都有平時成績時,出現學期平時成績選項
			if ($yorn=='n')
				$test_times_arr[254] = "平時成績";

			//產生下拉選單
			$sel= new drop_select();
			$sel->s_name = "curr_sort";
			$sel->id = $curr_sort;
			$sel->is_submit = true;
			$sel->arr = $test_times_arr;
			$sel->font_style="";
			$sel->top_option = "選擇階段";
			$select_stage_bar = $sel->get_select();	
			//記住上次 curr_sort 值,做判別用
//			$select_stage_bar .= "<input type=\"hidden\" name=\"curr_sort_hidden\" value=\"$curr_sort\">";
		}
		//全學期只輸入一次成績
		else
			$curr_sort = 255;
	}
	$hchk=($_REQUEST[athome])?"checked":"";
	$study_str=($_REQUEST[athome])?"'0','15'":"'0'";
	$athome=($_REQUEST[athome])?1:0;
	$athome_chk="<input type='checkbox' name='athome' OnChange='this.form.submit()' $hchk>包含在家自學學生";
}

//--------------階段下拉選單 結束

// 上方選單
$top_str = "<form action=\"$_SERVER[PHP_SELF]\" name=\"myform\" method=\"post\">$year_seme_menu $class_year_menu $class_year_name_menu $select_ss_bar $select_stage_bar $athome_chk</form>";

if($curr_sort){
	$main="<table bgcolor=#000000 border=0 cellpadding=2 cellspacing=1>
		<tr bgcolor=#ffffff>
			<td  colspan=5 align=center>".$full_class_name.$test_sort_name[$curr_sort]." 成績考查</td>
		</tr>
		<tr bgcolor=#ffffff align=center>
		<td>座號</td>
		<td>姓名</td>";
	//班級代號
	$curr_class_temp = sprintf("%d%02d",$class_arr[3],$class_arr[4]);
	//學生ID hidden 值
	$temp_hidden = "";
	//平均成績 hidden 值
	$avg_temp_hidden = "";
	//學年學期值
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

	//階段成績
	if ($curr_sort<254){
		if ($yorn=='n'){
			$main .="<td>定期評量*".$test_ratio[0]."%";
			$main.="<br><a href=\"{$_SERVER['PHP_SELF']}?edit=s1&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort&athome=$athome\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['PHP_SELF']}?del=ds1&edit=s1year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
		}
		else {
			if($test_ratio[0]!=0) {
				$main.="<td>定期評量*".$test_ratio[0]."%";
				$main.="<br><a href=\"{$_SERVER['PHP_SELF']}?edit=s1&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort&athome=$athome\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['PHP_SELF']}?del=ds1&edit=s1&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
			}
                        if($test_ratio[1]!=0) {
                        	$main.="<td>平時成績*".$test_ratio[1]."%";
                        	$main.="<br></a><a href=\"{$_SERVER['PHP_SELF']}?edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort&athome=$athome\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['PHP_SELF']}?del=ds2&edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
                        }
		}
		$main .="<td>平均</td></tr>\n";
		//評量成績
		if ($yorn=='n')
			$query = "select student_sn,test_kind,score from $score_semester where ss_id=$ss_id and test_sort='$curr_sort' and test_kind='定期評量' and  class_id='$class_id'";
		else
			$query = "select student_sn,test_kind,score from $score_semester where ss_id=$ss_id and test_sort='$curr_sort' and  class_id='$class_id'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$tt =1;
			if ($res->fields[test_kind] =="定期評量")
				$tt = 0;
			$score_arr[$tt][$res->fields[student_sn]] = $res->fields[score];
			$res->MoveNext();
		}
		
		//顯示學生成績
		$query = "select a.student_sn,a.stud_name,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' and b.seme_class='$curr_class_temp' and a.stud_study_cond in ($study_str) order by b.seme_num";
		$res = $CONN->Execute($query) or triger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$stud_num = $res->fields[seme_num];
			$stud_name = $res->fields[stud_name];
			$student_sn = $res->fields[student_sn];
			if ($_GET[del]=='ds1')
				$score_1=-100;
			else
				$score_1 = $score_arr[0][$student_sn];			
			if ($score_1 == -100 || $score_1=="" )
				$score_1_s='';
			else $score_1_s=$score_1;
			if ($_GET[del]=='ds2')
				$score_2 = -100;
			else
				$score_2 = $score_arr[1][$student_sn];
			if ($score_2 == -100 || $score_2=="")
				$score_2_s='';
			else $score_2_s=$score_2;
			$red_1 = ($score_1>=60)?"#000000":"#ff0000";
			$red_2 = ($score_2>=60)?"#000000":"#ff0000";
			$bred_1 = ($score_1<60 && $score_1<>'')?"#ffaabb":"#FFFFFF";
			$bred_2 = ($score_2<60 && $score_2<>'')?"#ffaabb":"#FFFFFF";
			if ($_GET[edit]=='s1')
				$score1_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" value=\"$score_1_s\" style='background-color: $bred_1;'></td>";
			else
				$score1_text = "<td align=center ><font color=$red_1>$score_1_s</font></td>";
			if ($_GET[edit]=='s2')
				$score2_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" value=\"$score_2_s\" style='background-color: $bred_2;' ></td>";
			else
				$score2_text = "<td align=center ><font color=$red_2>$score_2_s</font></td>";

			if ($score_1==-100 || $score_2==-100 || $score_1=="" || $score_2=="") {
				if ($score_1>0)
					$avg_score= $score_1_s;
				else
					$avg_score= $score_2_s;
			}
			//elseif(){
			
			//}
			else {
				$ratio_sum = $test_ratio[0]+$test_ratio[1];
				$avg_score = sprintf("%01.2f",($score_1*$test_ratio[0]+$score_2*$test_ratio[1])/$ratio_sum);
				//$avg_score = "";
			}
			$red_3 = ($avg_score>=60)?"#000000":"#ff0000";
			if ($yorn == 'n')
				$main .="<tr bgcolor=#FFFFFF ><td>$stud_num</td><td>$stud_name</td>$score1_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			else
				$main .="<tr bgcolor=#FFFFFF ><td>$stud_num</td><td>$stud_name</td>$score1_text $score2_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			$avg_temp_hidden .= "<input type=\"hidden\" name=\"avg_hidden_$student_sn\" value=\"$avg_score\">";
			$temp_hidden .="$student_sn,";
			$res->MoveNext();
		}
	}
	//學期成績
	elseif($curr_sort == 255){
		$main .="<td>學期成績";
		if ($is_send==0) $main.="<br><a href=\"{$_SERVER['PHP_SELF']}?edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['PHP_SELF']}?del=ds2&edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&ss_id=$ss_id&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>";
		$main.="<td>平均</td></tr>\n";
		
		$query = "select student_sn,score from $score_semester where  ss_id=$ss_id and test_sort=255 and test_kind='全學期' and class_id='$class_id'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$score_arr[$res->fields[student_sn]] = $res->fields[score];
			$res->MoveNext();
		}
			
		//將班級字串轉為陣列
		$class_arr=class_id_2_old($class_id);
		$curr_class_temp = sprintf("%d%02d",$class_arr[3],$class_arr[4]);
		//顯示學生成績
		$query = "select a.student_sn,a.stud_name,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' and b.seme_class='$curr_class_temp' and a.stud_study_cond in ($study_str) order by b.seme_num";
		$res = $CONN->Execute($query) or triger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			//$stud_num = intval(substr($res->fields[curr_class_num],-2));
			$stud_num = $res->fields[seme_num];
			$stud_name  = $res->fields[stud_name];
			$student_sn = $res->fields[student_sn];
			if ($_GET[del]=='ds2')
				$score_2 = -100;
			else
				$score_2 = $score_arr[$student_sn];
			if ($score_2 == -100)
				$score_2='';
			$red_2 = ($score_2>=60)?"#000000":"#ff0000";
			$bred_2 = ($score_2<60 && $score_2<>'')?"#ffaabb":"#FFFFFF";
			if ($_GET[edit]=='s2')
				$score2_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" value=\"$score_2\" style='background-color: $bred_2;' ></td>";
			else
				$score2_text = "<td align=center ><font color=$red_2>$score_2</font></td>";

				$avg_score= $score_2;
			$red_3 = ($avg_score>=60)?"#000000":"#ff0000";
			$main .="<tr bgcolor=#FFFFFF ><td>$stud_num</td><td>$stud_name</td> $score2_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			$avg_temp_hidden .= "<input type=\"hidden\" name=\"avg_hidden_$student_sn\" value=\"$avg_score\">";
			$temp_hidden .="$student_sn,";
			$res->MoveNext();
		}
	


                                                                                                                             
	}
	//平時成績
	elseif($curr_sort == 254) {
		$main .="<td>全學期平時成績<br><a onclick=\"openwindow('$url_str_2')\"><img src='./images/wedit.png' border='0'></a><a href=\"{$_SERVER['PHP_SELF']}?edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\"><img src='./images/pen.png' border='0'></a><a href=\"{$_SERVER['PHP_SELF']}?del=ds2&edit=s2&teacher_course=$teacher_course&curr_sort=$curr_sort\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td> <td>平均</td> </tr>\n";
	
		
		$query = "select student_sn,score from $score_semester where  ss_id=$ss_id and test_sort=254 and  class_id='$class_id'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$score_arr[$res->fields[student_sn]] = $res->fields[score];
			$res->MoveNext();
		}
			
		//將班級字串轉為陣列
		$class_arr=class_id_2_old($class_id);
		$curr_class_temp = sprintf("%d%02d",$class_arr[3],$class_arr[4]);
		//顯示學生成績
		$query = "select a.student_sn,a.stud_name,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' and b.seme_class='$curr_class_temp' and a.stud_study_cond in ($study_str) order by b.seme_num";
		$res = $CONN->Execute($query) or triger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$stud_num = intval(substr($res->fields[curr_class_num],-2));
			$stud_name  = $res->fields[stud_name];
			$student_sn = $res->fields[student_sn];
			if ($_GET[del]=='ds2')
				$score_2 = -100;
			else
				$score_2 = $score_arr[$student_sn];
			if ($score_2 == -100)
				$score_2='';
			$red_2 = ($score_2>=60)?"#000000":"#ff0000";
			$bred_2 = ($score_2<60 && $score_2<>'')?"#ffaabb":"#FFFFFF";
			if ($_GET[edit]=='s2')
				$score2_text = "<td align=center ><input type=\"text\" size=6 name=\"s_$student_sn\" value=\"$score_2\" style='background-color: $bred_2;' ></td>";
			else
				$score2_text = "<td align=center ><font color=$red_2>$score_2</font></td>";

				$avg_score= $score_2;
			$red_3 = ($avg_score>=60)?"#000000":"#ff0000";
			$main .="<tr bgcolor=#FFFFFF ><td>$stud_num</td><td>$stud_name</td> $score2_text <td><font color=$red_3>$avg_score</font></td></tr>\n";
			$avg_temp_hidden .= "<input type=\"hidden\" name=\"avg_hidden_$student_sn\" value=\"$avg_score\">";
			$temp_hidden .="$student_sn,";
			$res->MoveNext();
		}
	
	}

	$main .="</tr>";
	$main .="</table>";
}

head("成績補登/修改");
//列出橫向的連結選單模組
print_menu($menu_p);
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";
echo $top_str;
echo $temp_script;
echo "<form name=\"form9\" method=\"post\" action=\"$_SERVER[PHP_SELF]\">";
echo $main;
echo "
<input type=\"hidden\" name=\"year_seme\" value=\"$year_seme\">
<input type=\"hidden\" name=\"year_name\" value=\"$year_name\">
<input type=\"hidden\" name=\"me\" value=\"$me\">
<input type=\"hidden\" name=\"class_id\" value=\"$class_id\">
<input type=\"hidden\" name=\"ss_id\" value=\"$ss_id\">
<input type=\"hidden\" name=\"test_kind\" value=\"$_GET[edit]\">
<input type=\"hidden\" name=\"test_sort\" value=\"$curr_sort\">
<input type=\"hidden\" name=\"curr_sort\" value=\"$curr_sort\">
<input type=\"hidden\" name=\"student_sn_hidden\" value=\"$temp_hidden\">
<input type=\"hidden\" name=\"athome\" value=\"$athome\">";

echo $avg_temp_hidden;

if($_GET[edit]<>''){
	if ($is_send==0) echo "<input type=\"submit\" name=\"dokey\" value=\"儲存\">";
	if ($curr_sort ==255)
		$io_test_name="學期成績";
	elseif($_GET[edit]=="s1")
		$io_test_name="定期評量";
	elseif($_GET[edit]=="s2")
		$io_test_name="平時成績";
	echo "
        <input type=\"submit\" name=\"file_in\" value=匯入".$io_test_name.">
        <input type=\"submit\" name=\"file_out\" value=匯出".$io_test_name.">";

}
echo "</td></tr></table>";
echo "</form>";
foot();
?> 
<script language="JavaScript1.2">
<!-- Begin
function openwindow(url_str){
window.open (url_str,"成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}
//  End -->
</script>
