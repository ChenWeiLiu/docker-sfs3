<?php

// $Id: $

include "report_config.php";
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
$default_start=$IS_JHORES?11:16;
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
$test=pipa_log("印輔導訪談記錄貼條\r\n學期：$year_seme\r\n班級：$class_id $class_name\r\n");	

if (count($sel_stud)>0)
{
	foreach($sel_stud as $key=>$selected_student)
	{
		//取得stud_base基本資料
		$sql_basis="select stud_name,curr_class_num,stud_study_year from stud_base where stud_id='$selected_student'";
		$res_basis=$CONN->Execute($sql_basis) or user_error($sql_basis,256);
		$stud_name=$res_basis->fields['stud_name'];
		$curr_class_num=$res_basis->fields['curr_class_num'];
		$stud_study_year=$res_basis->fields['stud_study_year'];
		
		$data="( $curr_class_num )　　※學號：$selected_student 　　※姓名：$stud_name 　　※入學年：$stud_study_year";
		$data.="<table name='$data=' align=center width='100%' border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111'>";
		$data.="<tr align='center' bgcolor='#FFCCCC'><td>NO.</td><td>年級</td><td width=90>紀錄日期</td><td width=90>連絡對象</td><td width=90>連絡事項</td><td>內容要點</td><td width=90>輔導者</td></tr>";
		$sql="select a.*,b.name as teacher from stud_seme_talk a LEFT JOIN teacher_base b ON a.teach_id=b.teacher_sn WHERE a.stud_id='$selected_student' order by a.sst_date,a.seme_year_seme";
		$res=$CONN->Execute($sql) or user_error("讀取stud_seme_talk資料失敗！<br>$sql",256);
		$recno=0;
		while(!$res->EOF)
		{
			$recno++;
			if($recno>=$start)
			{
				//計算就學年級
				$year=substr($res->fields[seme_year_seme],0,3);
				$grade=$year-$stud_study_year+$IS_JHORES;
				$grade_array=array('一','二','三','四','五','六','七','八','九','十','十一','十二');
				
				//假使未找到輔導教師，則往轉學匯入的資料表尋找
				if(! $res->fields['teacher']) {
					$seme_year_seme=$res->fields['seme_year_seme'];
					$sql_teacher="select teacher_name from stud_seme_import WHERE stud_id='$stud_id' and seme_year_seme='$seme_year_seme';";
					$res_teacher=$CONN->Execute($sql_teacher);
					//替代原空值
					$res->fields[teacher]=$res_teacher->fields['teacher_name'];
				}
				$data.="<tr><td align='center'>{$recno}</td><td align='center'>{$grade_array[$grade]}</td><td align='center'>{$res->fields[sst_date]}</td><td align='center'>{$res->fields[sst_name]}</td><td align='center'>{$res->fields[sst_main]}</td><td>{$res->fields[sst_memo]}</td><td align='center'>{$res->fields[teacher]}</td></tr>";
			}	
			$res->movenext();	
		}
		//echo "<pre>";
		//print_r($res->getrows());
		//echo "</pre>";
		$data.="</table><BR>";
		if($key<count($sel_stud)-1)	if($newpage) $data.="<P STYLE='page-break-before: always;'>";
		echo $data;
	}
	exit;
}
}

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
echo "<hr><font color='#FF5555'><li>本功能主要在列印學生的輔導訪談記錄，解決95輔導紀錄表學生輔導訪談記錄超出定表筆數而無法顯示的問題！</li>";
echo "<li>國中預設自第11筆起列印，國小預設自第16筆起列印。唯使用者可自訂起始筆數。</li>";
echo "<li>本表採用HTML列印，勾取'強制個別學生分頁'，每位學生列印時皆會開啟新的分頁。</li></font><hr>";

echo "<form enctype='multipart/form-data' action=\"{$_SERVER['PHP_SELF']}\" method=\"post\" name=\"myform\">";
$sel1 = new drop_select();
$sel1->top_option =  "選擇學年";
$sel1->s_name = "year_seme";
$sel1->id = $_REQUEST[year_seme];
$sel1->is_submit = true;
$sel1->arr = get_class_seme();
$sel1->do_select();
 	 
echo "&nbsp;&nbsp;";
$sel1 = new drop_select();
$sel1->top_option =  "選擇班級";
$sel1->s_name = "class_id";
$sel1->id = $class_id;
$sel1->is_submit = true;
$sel1->arr = class_base($_REQUEST[year_seme]);
$sel1->do_select();

if($class_id<>'') {
 $query = "select a.student_sn,a.stud_id,a.stud_name,b.seme_num,a.stud_study_cond from stud_base a , stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[year_seme]' and seme_class='$_REQUEST[class_id]' order by b.seme_num";
	$result = $CONN->Execute($query) or die ($query);
	if (!$result->EOF) {
		//產生SELECT選項
		$start_select="<select name='start' onchange='this.form.submit();'>";
		for($i=1;$i<=30;$i++)
		{
			if($i==$start) $start_select.="<option selected>$i</option>"; else $start_select.="<option>$i</option>";
			
		}
		$start_select.="</select>";
 		
		echo '&nbsp;<input type="button" value="全選" onClick="javascript:tagall(1);">&nbsp;';
 		echo '<input type="button" value="取消全選" onClick="javascript:tagall(0);">';
		echo "　　◎自第 $start_select 筆起列印<hr>";
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
			
			if($counter>=$start)
			{
				$color='#FFCCCC';
				$input="<input id=\"c_$stud_id\" type=\"checkbox\" name=\"sel_stud[]\" value=\"$stud_id\" checked>";
			} else {
				$color='#CCCCCC'; $input="";
			}
		
			echo "<td bgcolor='$color'>$input<label for=\"c_$stud_id\">$curr_class_num. $stud_name $move_kind</label>($counter)</td>\n";
				
			if ($ii % 5 == 4)
				echo "</tr>";
			$ii++;
			$result->MoveNext();
		}
		echo"</table>";
	}
	echo "<BR><input type='checkbox' name='newpage' value='Y'>強制個別學生分頁　";
	echo "<input type='submit' name='go' value='按我開始列印'>";
}
echo "</form>";

foot();

?>
