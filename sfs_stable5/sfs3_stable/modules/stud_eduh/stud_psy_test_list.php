<?php

// $Id: $
include "config.php";
include "../stud_report/report_config.php";
include_once "../../include/sfs_case_dataarray.php";
//認證檢查
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$sel_stud=$_POST[sel_stud];
$newpage=$_POST[newpage];
$default_start=$IS_JHORES?1:16;
$start=$_POST[start]?$_POST[start]:$default_start;


if($_REQUEST[year_seme]=='')
  	$_REQUEST[year_seme] = sprintf("%03d%d",curr_year(),curr_seme());

if($_POST['go']=='按我開始列印')
{
//個資記錄
//學期
$year_seme=$_POST['year_seme'];
//班級陣列
$class_arr = class_base();
//班級(先取得SFS3中的內定班級代碼例如101,再轉換成學校自訂名稱例一年甲班)
$class_id=$_POST['class_id'];
$class_name=$class_arr[$class_id];
//選取輸出資料的學生陣列
$sel_stud=$_POST[sel_stud];
$stud_id_list=implode(',',$sel_stud);
$test=pipa_log("印心理測驗記錄貼條\r\n學期：$year_seme\r\n班級：$class_id $class_name\r\n學生列表：$stud_id_list");
if (count($sel_stud)>0)
{
	foreach($sel_stud as $key=>$selected_student)
	{
		//取得stud_base基本資料
		$sql_basis="select a.student_sn,a.stud_name,a.curr_class_num,a.stud_study_year from stud_base a,stud_seme b where a.stud_id='$selected_student' and a.student_sn=b.student_sn and b.seme_year_seme='$year_seme'";
		$res_basis=$CONN->Execute($sql_basis) or user_error($sql_basis,256);
	  $student_sn=$res_basis->fields['student_sn'];
		$stud_name=$res_basis->fields['stud_name'];
		$curr_class_num=$res_basis->fields['curr_class_num'];
		$stud_study_year=$res_basis->fields['stud_study_year'];
		
		$data="( $curr_class_num )　　※學號：$selected_student 　　※姓名：$stud_name 　　※入學年：$stud_study_year";
		
		?>

		<table border="0" width="100%">
			<tr>
				<td><?php echo $data;?></td>
			</tr>
		</table>
		<table border="1" cellspacing="0" cellpadding="2" style="border-collapse: collapse" bordercolor="#000000" width="100%">
<tr><td>學期</td><td>測驗日期</td><td>心理測驗名稱</td><td>原始分數</td><td>常模樣本</td><td>標準分數</td><td>百分等級</td><td>解釋</td><td>建檔者</td><td>動作</td></tr>
<?php
$sql_select = "select * from stud_psy_test where student_sn='$student_sn' order by year,semester,student_sn desc  ";
$recordSet = $CONN->Execute($sql_select) or die($sql_select);

while (!$recordSet->EOF) {
	$sn = $recordSet->fields["sn"];
	//$student_sn = $recordSet->fields["student_sn"];
	$year = $recordSet->fields["year"];
	$semester = $recordSet->fields["semester"];
		
	$item = $recordSet->fields["item"];
	$score = $recordSet->fields["score"];
	$model = $recordSet->fields["model"];
	$standard = $recordSet->fields["standard"];
	$pr = $recordSet->fields["pr"];
	$explanation = $recordSet->fields["explanation"];
	$test_date = $recordSet->fields["test_date"];
	$teacher_sn = $recordSet->fields["teacher_sn"];
	
	$name = get_teacher_name($teacher_sn);
	$seme_str = $year."學年第".$semester."學期";
	$seme_year_seme  = sprintf("%03d%d",$year,$semester);
		
	echo "<td>$seme_str</td><td>$test_date</td><td>$item</td><td>$score</td><td>$model</td><td>$standard</td><td>$pr</td><td>$explanation</td><td>$name</td><td>";
	
	echo "</td></tr>";
	
    $recordSet->MoveNext();
} // end while

?>
</table>
		
<?php		
  if($newpage) echo "<P STYLE='page-break-before: always;'>";

	}// end if foreach ($sel_stud as $key=>$selected_student)
	exit;
 } 
} // end if 按我開始列印

//選擇班級

head();

print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='sel_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;
echo "<hr><font color='#FF5555'><li>本功能主要在列印學生的心理測驗記錄貼條！</li>";
echo "<li>國中預設自第11筆起列印，國小預設自第16筆起列印。唯使用者可自訂起始筆數。</li>";
echo "<li>本表採用HTML列印，勾取'強制個別學生分頁'，每位學生列印時皆會開啟新的分頁。</li></font><hr>";

echo "<form enctype='multipart/form-data' action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" name=\"myform\" target=\"\">";
$sel1 = new drop_select();
$sel1->top_option =  "選擇學年";
$sel1->s_name = "year_seme";
$sel1->id = $_REQUEST[year_seme];
$sel1->is_submit = true;
$sel1->arr = get_class_seme();
$sel1->other_script = "this.form.target=''";
$sel1->do_select();

 	 
echo "&nbsp;&nbsp;";
$sel1 = new drop_select();
$sel1->top_option =  "選擇班級";
$sel1->s_name = "class_id";
$sel1->id = $class_id;
$sel1->is_submit = true;
$sel1->arr = class_base($_REQUEST[year_seme]);
$sel1->other_script = "this.form.target=''";
$sel1->do_select();



if($class_id<>'') {

 $query = "select a.student_sn,a.stud_id,a.stud_name,b.seme_num,a.stud_study_cond from stud_base a , stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[year_seme]' and seme_class='$_REQUEST[class_id]' order by b.seme_num";
	$result = $CONN->Execute($query) or die ($query);
	if (!$result->EOF) {
 		
		echo '&nbsp;<input type="button" value="全選" onClick="javascript:tagall(1);">&nbsp;';
 		echo '<input type="button" value="取消全選" onClick="javascript:tagall(0);">';
		
		echo "<table border=1>";
		$ii=0;
		while (!$result->EOF) {
			$student_sn= $result->fields[student_sn];
			$stud_id = $result->fields[stud_id];
			$stud_name = $result->fields[stud_name];
			$curr_class_num = $result->fields[seme_num];
			$stud_study_cond = $result->fields[stud_study_cond];
			$move_kind ='';
			if ($stud_study_cond >0)
				$move_kind= "<font color='red'>(".$move_kind_arr[$stud_study_cond].")</font>";

			if ($ii %2 ==0)
				$tr_class = "class=title_sbody1";
			else
				$tr_class = "class=title_sbody2";
			
			if ($ii % 5 == 0)
				echo "<tr $tr_class >";
				
			//抓取輔導訪談記錄筆數
			//$sql="select count(*) from stud_seme_talk where student_sn=$student_sn";   ---> student_sn 尚未有資料欄位
			$sql="select count(*) as counter from stud_seme_talk where stud_id=$stud_id";
			$rs=$CONN->Execute($sql) or die($sql);
			$counter=$rs->fields[counter];
			
				$color='#FFCCCC';
				$input="<input id=\"c_$stud_id\" type=\"checkbox\" name=\"sel_stud[]\" value=\"$stud_id\" checked>";

			echo "<td bgcolor='$color'>$input<label for=\"c_$stud_id\">$curr_class_num. $stud_name $move_kind</label>($counter)</td>\n";
				
			if ($ii % 5 == 4)
				echo "</tr>";
			$ii++;
			$result->MoveNext();
		}
		echo"</table>";
	}
	echo "<BR><input type='checkbox' name='newpage' value='Y'>強制個別學生分頁　";
	echo "<input type='submit' name='go' value='按我開始列印' onclick='this.form.target=\"_blank\"'>";

}
echo "</form>";

foot();

?>
