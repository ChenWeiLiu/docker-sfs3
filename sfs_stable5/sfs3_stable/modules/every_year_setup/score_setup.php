<?php

// $Id: score_setup.php 7147 2013-02-25 07:11:21Z infodaes $

/* 取得基本設定檔 */
include "config.php";

sfs_check();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
if(empty($act))$act="";


//執行動作判斷
if($act=="儲存設定"){
	$setup_id=save_setup($setup_id,$sel_year,$sel_seme,$main_data,$Cyear,$all_same,$pt_times,$allow_modify);
	header("location: {$_SERVER['PHP_SELF']}?act=view&setup_id=$setup_id");
}elseif($act=="view" or $act=="觀看設定"){
	$main=&exam_form($sel_year,$sel_seme,"view",$Cyear,$setup_id);
}elseif($act=="開始設定" or $act=="修改設定" or $act=="setup"){
	$main=&exam_form($sel_year,$sel_seme,"edit",$Cyear,$setup_id);
}else{
	$main=&exam_form_1($sel_year,$sel_seme);
}


//秀出網頁
head("考試設定");
echo $main;
foot();

/*
函式區
*/
//基本設定表單
function &exam_form_1($sel_year,$sel_seme){
	global $school_menu_p;
	//相關功能表
	$tool_bar=&make_menu($school_menu_p);

	//說明
	$help_text="
	請選擇一個學年、學期以做設定。||
	<span class='like_button'>開始設定</span> 會開始進行該學年學期考試設定。||
	<span class='like_button'>觀看設定</span>會列出該學年學期的考試設定。
	";
	$help=&help($help_text);

	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu");
	
	//取得年級選單
	$class_year_list=&get_class_year_select($sel_year,$sel_seme,$Cyear);

	$main="
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&year_seme=\" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td>請選擇欲設定的學年度：</td><td>$date_select</td></tr>
		<tr><td>請選擇欲設定的年級：</td><td>$class_year_list</td></tr>
		<tr><td colspan='2'><input type='submit' name='act' value='開始設定' class='b1'>
		<input type='submit' name='act' value='觀看設定' class='b1'>
		</td></tr>
		</form>
		</table>
	</td></tr>
	</table>
	<br>
	$help
	";
	return $main;
}



//主要的設定表格
function &exam_form($sel_year,$sel_seme,$mode="edit",$sel_Cyear="",$setup_id=""){
	global $act,$CONN,$school_kind_name,$school_kind_end,$school_kind_name_n,$ptt,$school_menu_p,$main_data;
 	
	$sm=&get_all_setup($setup_id,$sel_year,$sel_seme,$sel_Cyear);
	$setup_id=$sm[setup_id];
	$Cyear=(is_null($sel_Cyear))?$sm[class_year]:$sel_Cyear;
	$year=(empty($sm[year]))?$sel_year:$sm[year];
	$seme=(empty($sm[semester]))?$sel_seme:$sm[semester];

	//取得年級選單
	$class_year_list=&get_class_year_select($year,$seme,$Cyear,"jumpMenu");
	
	
	if($mode=="edit" and empty($setup_id)){
		//先建立一個設定檔（不論有無資料）
		$setup_id=save_setup($setup_id,$year,$seme,$main_data,$Cyear);
	}	

	
	//看該年級是否已經有資料
	$have_data=($mode=="view" and empty($sm[rule]) and empty($sm[sections]) and empty($sm[interface_sn]))?false:true;
	
	//預設考試次數
	$pttn=(!empty($ptt))?$ptt:$sm[performance_test_times];
	$pf_stand=(empty($pttn))?2:$pttn;

	if($mode=="edit"){
		//定期評量次數選單
		for($i=0;$i<=10;$i++){
			$selected=($i==$pf_stand)?"selected":"";
			$pf_options.="<option $selected>$i</option>";
		}
		$performance_test_options="<select name='pt_times' onChange='jumpMenu2()'>$pf_options</select>";

	}else{
		$performance_test_options="<font color='#FF0000'><b>$pf_stand</b></font>";
	}

	//假如之前成績比例模式是個別的，進行分割字串動作
	if($sm[score_mode]=="severally"){
		$test_ratio=explode(",",$sm[test_ratio]);
		for($i=0;$i<sizeof($test_ratio);$i++){
			$tr=explode("-",$test_ratio[$i]);
			$n=$i+1;
			$pf_index="pf_ratio_".$n;
			$pt_index="pt_ratio_".$n;
			$severally_ratio[$pf_index]=$tr[0];
			$severally_ratio[$pt_index]=$tr[1];
		}
	}else{
		$test_ratio=explode("-",$sm[test_ratio]);
		$all_ratio[pf]=$test_ratio[0]?$test_ratio[0]:50;
		$all_ratio[pt]=$test_ratio[1]?$test_ratio[1]:50;
	}

	//定期評量比例設定，採個別比例則計算不同次的比例為何
	for($i=1;$i<=$pf_stand;$i++){
		$pf_index="pf_ratio_".$i;
		$pt_index="pt_ratio_".$i;
		$severally_exam_ratio.=($mode=="edit")?"
		第 $i 次定期考查佔 <input type='text' name='main_data[pf_ratio][$i]' value='$severally_ratio[$pf_index]' size=2>%，
		平時成績佔 <input type='text' name='main_data[pt_ratio][$i]' value='$severally_ratio[$pt_index]' size=2>%<br>
		":"
		第 $i 次定期考查佔 <font color='#0000FF'>$severally_ratio[$pf_index]</font> %，
		平時成績佔 <font color='#0000FF'>$severally_ratio[$pt_index]</font> %<br>
		";
	}
	
	
	$checked_severally=($sm[score_mode]=="severally")?"checked":"";
	$checked_all=($sm[score_mode]=="all")?"checked":"";
	if(! $checked_severally) $checked_all='checked';

	//每次評量均採相同比例模式
	$all_mode=($mode=="edit")?"
	<tr bgcolor='#FFFFFF'><td valign='top' nowrap>
	<input type='radio' name='main_data[score_mode]' value='all' $checked_all>每次評量均採相同比例：</td>
	<td valign='top' class='small'>
	定期考查計分比例：<input type='text' name='main_data[performance_test_ratio]' value='$all_ratio[pf]' size=3>%<br>
	平時成績計分比例：<input type='text' name='main_data[practice_test_ratio]' value='$all_ratio[pt]' size=3>%
	</td></tr>":"
	<tr bgcolor='#FFFFFF'><td valign='top' nowrap>
	每次評量均採相同比例：</td>
	<td valign='top'>
	定期考查計分比例： <font color='#0000FF'>$all_ratio[pf]</font> %<br>
	平時成績計分比例： <font color='#0000FF'>$all_ratio[pt]</font> %
	</td></tr>
	";


	//每次評量採不同比例模式
	$severally_mode=($mode=="edit")?"
	<tr bgcolor='#FFFFFF'><td valign='top' nowrap>
	<input type='radio' name='main_data[score_mode]' value='severally' $checked_severally>每次評量採不同比例：</td>
	<td valign='top' class='small'>
	$severally_exam_ratio
	</td></tr>":"
	<tr bgcolor='#FFFFFF'><td valign='top' nowrap>
	每次評量採不同比例：</td>
	<td valign='top' class='small'>
	$severally_exam_ratio
	</td></tr>
	";
	
	//等第設定
	//若是第1次設定
	$rule_setup=((empty($setup_id) and $mode=="edit") or empty($sm[rule]))?"優_>=_90\n甲_>=_80\n乙_>=_70\n丙_>=_60\n丁_<_60":$sm[rule];
	
	$rule_now="<textarea cols='10' rows='6' name='main_data[rule]'>$rule_setup</textarea>";
	$tmp=&say_rule($sm[rule]);
	$rule_set=($mode=="edit")?$rule_now:$tmp;
	
	//如果是觀看模式，擇一秀出即可。
	if($mode=="view"){
		$test_ratio_set=($sm[score_mode]=="all")?$all_mode:$severally_mode;
		$submit="修改設定";
	}else{
		$test_ratio_set=$all_mode."\n".$severally_mode;
		$submit="儲存設定";
	}
	
	$semester_name=($seme=='2')?"下":"上";

	$date_text="<font color='#607387'>
	<font color='#000000'>$year</font> 學年
	<font color='#000000'>$semester_name</font>學期
	</font>";

	$all_exam_date=($mode=="view")?get_exam_year($year,$seme,$act,"Cyear=$Cyear"):"";

	
	$all_same_b=($mode=="view")?"":"<input type='checkbox' name='all_same' value=1><font color='blue'>所有年級採用相同設定（可個別再修改）</font>";
	
	$allow_modify_checked=($sm[allow_modify]=="true")?"checked":"";
	$allow_modify_txt=(($mode=="view" and $sm[allow_modify]=="true") or $mode=="edit")?"允許該年級之班級可以自行調整考試設定":"";
	$allow_modify_b=($mode=="view")?$allow_modify_txt:"<input type='checkbox' name='allow_modify' value='true' $allow_modify_checked><font color='red'>$allow_modify_txt</font>";

	//節數設定
	$sections_n=(empty($setup_id) or empty($sm[sections]))?7:$sm[sections];
	$sections_form=($mode=="view")?$sm[sections]:"<input type='text' name='main_data[sections]' value='$sections_n' size=3> 節";
	
	//成績單樣式設定
	//$tmp=&get_sc_list("option",$sm[interface_sn],"main_data[interface_sn]");
	//$ar_set=($mode=="view")?"本學期使用的成績單樣式為：『".get_interface_name($sm[interface_sn])."』":"請選擇一個成績單樣式：".$tmp;

	//相關功能表
	$tool_bar=&make_menu($school_menu_p);


	//說明
	$help_text="
	若是選擇「每次評量均採相同比例」，那算法如下：<br>
	<font color='blue'>該科學期成績=(
	<font color='black'>定期考查分數總平均</font>
	 *
	<font color='red'>定期考查計分比例</font>
	)+(
	<font color='black'>平時分數總平均</font>
	 *
	<font color='red'>平時成績計分比例</font>
	)</font>||
	若是選擇「每次評量採不同比例」，假設定期考查 2 次，「定期考查」和「平時考」成績比例依序為：30:10，40:20，那算法如下：<br>
	<font color='blue'>該科學期成績=(
	<font color='black'>第 1 次定期考查分數</font>
	 *
	<font color='red'>30%</font>
	)+(
	<font color='black'>第 1 次平時分數平均</font>
	 *
	<font color='red'>10%</font>
	)+(
	<font color='black'>第 2 次定期考查分數</font>
	 *
	<font color='red'>40%</font>
	)+(
	<font color='black'>第 2次平時分數平均</font>
	 *
	<font color='red'>20%</font>
	)</font>
	";
	$help=&help($help_text);
	
	if(is_null($Cyear)){
		$all_data="";
	}else{
	$all_data=($have_data)?"
	<tr bgcolor='#E1ECFF'><td valign='top' colspan=2 class='col_style'>成績繳交相關設定</td></tr>
	<tr bgcolor='#FFFFFF'><td valign='top' nowrap colspan=2>
	本學期定期考查次數： $performance_test_options 次
	</td></tr>
	<tr bgcolor='#E1ECFF'><td valign='top' colspan=2 class='col_style'>成績配分比例相關設定</td></tr>
	$test_ratio_set
	<tr bgcolor='#E1ECFF'><td valign='top' class='col_style'>等第設定</td></tr>
	<tr><td valign='top' rowspan=4>$rule_set</td></tr>
	<tr bgcolor='#E1ECFF'><td valign='top' colspan=2 class='col_style'>每日節數設定</td></tr>
	<tr bgcolor='#FFFFFF'><td valign='top' nowrap colspan=2 class='small'>
	每日上課節數有幾節？ $sections_form
	</td></tr>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='setup_id' value='$sm[setup_id]'>
	$hidden
	<tr bgcolor='#def7ce'><td valign='top' class='small'>
	$allow_modify_b<br>
	$all_same_b
	<p align='right'>
	<input type='submit' name='act' value='$submit' onclick='if(this.value==\"儲存設定\") { return confirm(\"按確定後，會將課表設定內多餘節次的排課刪除，而且不可回復。\\n\\n真的要這樣做嗎?\") }' class='b1'>
	</p></td></tr>
	</form>
	<tr bgcolor='#E1ECFF'><td valign='top' colspan=2>其他相關設定</td></tr>
	<tr bgcolor='#FFFFFF'><td colspan=2><a href='subject_setup.php?subject_kind=scope'>科目選擇清單設定</a>（不需特別去設定）&nbsp;</td></tr>
	<tr bgcolor='#FFFFFF'><td colspan=2><a href='subject_setup.php?subject_kind=subject'>分科選擇清單設定</a>（不需特別去設定）&nbsp;</td></tr>
	":"
	<tr bgcolor='#E1ECFF'><td valign='top' colspan=2>該年級尚未設定資料，<a href='{$_SERVER['PHP_SELF']}?act=setup&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear'>現在立即設定</a></td></tr>
	";
	}
	
	$main="
	<style type='text/css'>
	.col_style{
		background : #E1ECFF;
		color : #778899;
	}
	</style>
	<script language='JavaScript'>
	function jumpMenu(){
		if(document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=\" + document.myform.Cyear.options[document.myform.Cyear.selectedIndex].value;
		}
	}
	function jumpMenu2(){
		if(document.myform.pt_times.options[document.myform.pt_times.selectedIndex].value!=''){
			location=\"{$_SERVER['PHP_SELF']}?act=$act&setup_id=$setup_id&main_data=$main_data&ptt=\" + document.myform.pt_times.options[document.myform.pt_times.selectedIndex].value;
		}
	}
	</script>
	$tool_bar
	<table cellspacing=0 cellpadding=0><tr><td>
		<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
		<tr bgcolor='#FFFFFF'><td>
			<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
			<table cellpadding=4 width=500>
			<tr bgcolor='#ffffcc'>
			<td colspan=2>欲設定的學年度：".$date_text.$class_year_list."</td></tr>
			$all_data
			</table>
		</td></tr></table>
	</td><td valign='top'>$all_exam_date</td></tr></table>
	<p>
	$help
	</p>
	";
	return $main;
}

//儲存考試設定
function save_setup($setup_id="",$sel_year="",$sel_seme="",$main_data="",$Cyear="",$all_same="",$pt_times="",$allow_modify=""){
	global $CONN;
	$main_data[performance_test_times]=$pt_times;
	$main_data[allow_modify]=$allow_modify;
	
	//假如所有年級採相同設定
	if($all_same=="1"){
		$class_year_array=get_class_year_array($sel_year,$sel_seme);
		if(!is_array($class_year_array))$class_year_array=array();

		//取得年級陣列
		while(list($i,$v)=each($class_year_array)){
			$setup_id=save_one("",$sel_year,$sel_seme,$main_data,$v);
			if($v==$Cyear)$curr_setup_id=$setup_id;
		}
		return $curr_setup_id;
	}else{
		$setup_id=save_one($setup_id,$sel_year,$sel_seme,$main_data,$Cyear);
		return $setup_id;
	}

	return false;
}

//儲存某一個年級的成績設定
function save_one($setup_id="",$sel_year="",$sel_seme="",$main_data="",$Cyear=""){
	global $CONN;
	//假如無考試設定id，去取得
	$sm=&get_all_setup($setup_id,$sel_year,$sel_seme,$Cyear);
	$setup_id=$sm[setup_id];

	//如果仍沒有，表示該學期還未有資料，那麼就新增一筆資料。
	if(empty($setup_id)){
		$setup_id=add_setup($main_data,$sel_year,$sel_seme,$Cyear);

		//刪除多餘節數的課表資料
		$sql = "delete from score_course where year='$sel_year' and semester='$sel_seme' and class_year='$Cyear' and sector>{$main_data[sections]}";
		$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);

		if(!empty($setup_id))	return $setup_id;
	}elseif(is_array($main_data)){
		if(update_setup($setup_id,$main_data,$sel_year,$sel_seme,$Cyear))
		
		//刪除多餘節數的課表資料
		$sql = "delete from score_course where year='$sel_year' and semester='$sel_seme' and class_year='$Cyear' and sector>{$main_data[sections]}";
		$CONN->Execute($sql) or trigger_error("SQL語法錯誤： $sql", E_USER_ERROR);

		return $setup_id;
	}

	return false;
}


//規則口語化
function &say_rule($rule=""){
	$r=explode("\n",$rule);
	while(list($k,$v)=each($r)){
		$str=explode("_",$v);
		$main.="學期分數
		<font color='#FF0000'>$str[1]</font>
		<font color='#0000FF'>$str[2]</font>
		時，等第為『<font color='#008000'>$str[0]</font>』<br>";
	}
	return $main;
}

//新增考試設定
function add_setup($main_data="",$sel_year="",$sel_seme="",$Cyear=""){
	global $CONN;

	if(empty($main_data[score_mode]))$main_data[score_mode] = all;
	//$rule=make_rule($main_data);
	$rule=$main_data[rule];
	if($main_data[score_mode]=="all"){
		$test_ratio=$main_data[performance_test_ratio]."-".$main_data[practice_test_ratio];
	}elseif($main_data[score_mode]=="severally"){
		$test_ratio=&ratio_2_string($main_data[pf_ratio],$main_data[pt_ratio]);
	}

	$sql_insert = "insert into score_setup (year,semester,class_year,allow_modify,performance_test_times,practice_test_times,test_ratio,rule,score_mode,sections,interface_sn,update_date,enable) values ($sel_year,'$sel_seme','$Cyear','$main_data[allow_modify]','$main_data[performance_test_times]','1','$test_ratio','$rule','$main_data[score_mode]','$main_data[sections]','$main_data[interface_sn]',now(),'1')";
	$CONN->Execute($sql_insert) or trigger_error("SQL語法錯誤： $sql_insert", E_USER_ERROR);

	return mysql_insert_id();
}


//更新考試設定
function update_setup($setup_id,$main_data,$sel_year,$sel_seme,$Cyear=""){
	global $CONN;
	//$rule=make_rule($main_data);
	$rule=$main_data[rule];
	if($main_data[score_mode]=="all"){
		$test_ratio=$main_data[performance_test_ratio]."-".$main_data[practice_test_ratio];
	}elseif($main_data[score_mode]=="severally"){
		$test_ratio=&ratio_2_string($main_data[pf_ratio],$main_data[pt_ratio]);
	}

	$sql_update = "update score_setup set allow_modify='$main_data[allow_modify]',performance_test_times='$main_data[performance_test_times]',test_ratio='$test_ratio',rule='$rule',score_mode='$main_data[score_mode]',sections='$main_data[sections]',interface_sn='$main_data[interface_sn]',update_date=now() where setup_id = '$setup_id'";
	$CONN->Execute($sql_update) or trigger_error("SQL語法錯誤： $sql_update", E_USER_ERROR);
	return true;
}


//刪除考試設定
function del_setup($setup_id){
	global $CONN,$sel_year,$sel_seme;
	$sql_update = "update score_setup set enable='0' where setup_id = '$setup_id'";
	if($CONN->Execute($sql_update))		return true;
	return  false;
}



//把考試比例的部份以逗點的字串呈現
function &ratio_2_string($pf_ratio,$pt_ratio){
	for($i=1;$i<=sizeof($pf_ratio);$i++){
		$main.=$pf_ratio[$i]."-".$pt_ratio[$i].",";
	}
	$main=substr($main,0,-1);
	return $main;
}

//成績單的樣式名稱
function get_interface_name($interface_sn=1){
	$sc=&get_sc($interface_sn);
	return $sc[title];
}


?>
