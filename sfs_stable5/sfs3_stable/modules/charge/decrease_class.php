<?php
// $Id: list.php 5310 2009-01-10 07:57:56Z hami $

include "config.php";
include "my_fun.php";

sfs_check();

//秀出網頁
head("收費管理");

print_menu($menu_p);
echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='selected_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;



//學期別
$work_year_seme=$_REQUEST[work_year_seme];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

$item_id=$_REQUEST[item_id];
$stud_class=$_POST[stud_class];
$selected_stud=$_POST[selected_stud];
$detail_id=$_POST[detail_id];
$pay=$_POST[pay];
$decrease_dollars=$_POST[decrease_dollars];
$cause=$_POST[cause];


//print_r($selected_stud);


//取得目前班級id
$class_data=explode('_',$stud_class);
$class_id=$class_data[2]*100+$class_data[3];
$grade+=$class_data[2];

// 取出班級名稱陣列
$class_base = class_base($work_year_seme);



//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

// $_SESSION[session_tea_name]  取得教師姓名

if($selected_stud AND $_POST['act']=='確定減免'){
	if($decrease_dollars AND $detail_id AND $selected_stud)
	{
		//計算百分比
		$percent=$decrease_dollars/$pay*100;
		//抓取選擇的班級學生
		$batch_value="";
		foreach($selected_stud as $stud_datas)
		{
			$stud_data=explode(',',$stud_datas);
			$sn=$stud_data[0];
			$class_num=$stud_data[1];
	
			$batch_value.="('$detail_id',$sn,'$class_num',$percent,'$cause'),";
		}
		$batch_value=substr($batch_value,0,-1);
		//echo "===================<BR>$batch_value<BR>===================";
		
		$sql_select="REPLACE INTO charge_decrease(detail_id,student_sn,curr_class_num,percent,cause) values $batch_value";
		$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	} else echo "<script language=\"Javascript\"> alert (\"資訊不足, 無法身分別批次新增！\")</script>";
};



if($_POST['act']=='清空本班級此項目減免名單'){
	$sql_select="delete from charge_decrease where detail_id=$detail_id AND curr_class_num like '$class_id%'";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
};


//取得年度與學期的下拉選單
$seme_list=get_class_seme();
$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA' width='100%'><form name='myform' method='post' action='$_SERVER[PHP_SELF]'>
	<select name='work_year_seme' onchange='this.form.submit()'>";
foreach($seme_list as $key=>$value){
	$main.="<option ".($key==$work_year_seme?"selected":"")." value=$key>$value</option>";
}
$main.="</select><select name='item_id' onchange='this.form.submit()'><option></option>";

//取得年度項目
$sql_select="select * from charge_item where year_seme='$work_year_seme' order by end_date desc";
$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

while(!$res->EOF) {
	$main.="<option ".($item_id==$res->fields[item_id]?"selected":"")." value=".$res->fields[item_id].">".$res->fields[item]."(".$res->fields[start_date]."~".$res->fields[end_date].")</option>";
	$res->MoveNext();
}
$main.="</select>";

if($item_id>0)
{
	//顯示班級
	$class_list=get_class_select(curr_year(),curr_seme(),"","stud_class","this.form.submit",$stud_class);
	$class_data=explode('_',$stud_class);
	$class_id=$class_data[2]*100+$class_data[3];
	$curr_grade=substr($class_id,0,1)-1;
	$main.=$class_list;

	if($stud_class<>'')
	{
	
		//顯示指定項目詳細資料
		$sql_select="select * from charge_detail where item_id='$item_id' order by detail_sort";
		$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
		$main.="<select name='detail_id' onchange='this.form.submit()'><option></option>";
		while(!$res->EOF) {
			//取得各學年應收費金額
			$selected="";
			if($detail_id==$res->fields[detail_id])
			{
				$selected="selected";
				$grade_dollar=explode(',',$res->fields[dollars]);
				//print_r($grade_dollar);
			}
			$main.="<option $selected value=".$res->fields[detail_id].">".$res->fields[detail]."</option>";
			$res->MoveNext();
		}
		$dollars=$grade_dollar[$curr_grade];
		$main.="</select>";

		if($detail_id)
		{
			//取得前已開列"減免”的學生資料
			$sql_select="select * from charge_decrease where detail_id=$detail_id AND curr_class_num like '$class_id%' order by curr_class_num";

			$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			
			$listed=array();
			while(!$recordSet->EOF)
			{
				$listed[$recordSet->fields[student_sn]]=$recordSet->fields[percent];
				$recordSet->MoveNext();
			}

			
			//取得stud_base中班級學生列表並據以與前sql對照後顯示
	
			$stud_select="SELECT a.student_sn,b.curr_class_num,right(b.curr_class_num,2) as class_no,b.stud_name,b.stud_sex FROM charge_record a,stud_base b WHERE a.item_id=$item_id AND a.student_sn=b.student_sn AND b.curr_class_num like '$class_id%' ORDER BY b.curr_class_num";
			$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
			//以checkbox呈現
			$col=5; //設定每一列顯示幾人
			
			$studentdata="　◎應收金額：$dollars <input type='hidden' name='pay' value=$dollars>";
			while(list($student_sn,$curr_class_num,$class_no,$stud_name,$stud_sex)=$recordSet->FetchRow()) {
				if($recordSet->currentrow() % $col==1) $studentdata.="<tr>";
				if (array_key_exists($student_sn,$listed)) {
						$studentdata.="<td bgcolor=".($listed[$recordSet->fields[student_sn]-1]?"#CCCCCC":"#FFFF8D").">★($class_no)$stud_name ( $".round($dollars*$listed[$student_sn]/100)." )</td>";
				} else {
					$studentdata.="<td bgcolor=".($stud_sex==1?"#CCFFCC":"#FFCCCC")."><input type='checkbox' name='selected_stud[]' value='$student_sn,$curr_class_num' id='stud_selected'>($class_no)$stud_name</td>";
				}
				if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
				//echo "<BR>$curr_class_num === $stud_name";
			}
			
			$studentdata.="<tr height='50'><td align='center' colspan=$col><input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'><input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>　";
			$studentdata.="　◎減免金額：<input type='text' name='decrease_dollars' size=5>元　減免原因：<input type='text' name='cause' size=10>　<input type='submit' value='確定減免' name='act'>";
			$studentdata.="　<input type='submit' value='清空本班級此項目減免名單' name='act' onclick='return confirm(\"確定要\"+this.value+\"?\")'></td></tr>";
		}
	}
}
echo $main.$studentdata."</form></table>";
foot();
?>