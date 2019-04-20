<?php

// $Id: chart.php 7710 2013-10-23 12:40:27Z smallduh $

/* 取得設定檔 */
include "config.php";

sfs_check();

/* 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
*/

$year_seme=($_POST[year_seme])?$_POST[year_seme]:$_GET[year_seme];
$class_id=($_POST[class_id])?$_POST[class_id]: $_GET[class_id];
$stud_id=($_POST[stud_id])?$_POST[stud_id]:$_GET[stud_id];
$act=($_POST[act])?$_POST[act]:$_GET[act];
$stu_num=($_POST[stu_num])?$_POST[stu_num]:$_GET[stu_num];

//若為輸出檔案狀態, 算出正確學期
if (($class_id)&&($act)) {
	$c=explode("_",$class_id);
	$year_seme=$c[0].$c[1];
}

//更改學期
if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
} else {
	$sel_year=(substr($year_seme,0,1)=="0")?substr($year_seme,1,2):substr($year_seme,0,3);
	$sel_seme=substr($year_seme,3,1);
}

//更改班級
if ($stud_id != "") {
	$sql="select seme_class from stud_seme where stud_id='$stud_id' and seme_year_seme='$year_seme'";
	$rs=$CONN->Execute($sql);
	$stud_num=$rs->fields['seme_class'];
}
if ($class_id=="") {
	// 利用 $IS_JHORES 來 區隔 國中、國小、高中 的預設值
	if ($stud_num) $class_num=$stud_num;
	else {
		$year_name=$IS_JHORES+1;
		$sql="select seme_class,stud_id from stud_seme where seme_year_seme='$year_seme'";
		$rs=$CONN->Execute($sql);
		$class_num=$rs->fields['seme_class'];
		if ($class_num) {
			$stud_id=$rs->fields['stud_id'];
		} else {
			$sql="select seme_class,stud_id from stud_seme";
			$rs=$CONN->Execute($sql);
			$class_num=$rs->fields['seme_class'];
			$stud_id=$rs->fields['stud_id'];
		}
	}
} else {
	$temp_curr_class_arr = explode("_",$class_id); //091_1_02_03
	$class_num = $temp_curr_class_arr[2].$temp_curr_class_arr[3];
	if (substr($class_num,0,1)=="0") $class_num=substr($class_num,1,strlen($class_num)-1);
	$sql="select seme_class from stud_seme where seme_year_seme='$year_seme' and seme_class='$class_num' order by seme_class";
	$rs=$CONN->Execute($sql);
	if (!$rs->fields['seme_class']) {
		$sql="select seme_class from stud_seme where seme_year_seme='$year_seme' order by seme_class";
		$rs=$CONN->Execute($sql);
		$class_num=$rs->fields['seme_class'];
	}
	if ($stud_num != $class_num) $stud_id="";
}
//取得班級代號
$class_all=class_num_2_all($class_num);
$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];
if ($chknext)	$ss_temp = "&chknext=$chknext&nav_next=$nav_next";

//執行動作判斷
if($act=="dlar"){
	downlod_ar($stud_id,$class_id,$interface_sn,$stu_num,$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id");
}elseif($act=="dlar_all"){
	downlod_ar("",$class_id,$interface_sn,"",$sel_year,$sel_seme,"all");
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id");
}else{
	$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$stud_id);
}


//秀出網頁
head("製作成績單");

?>

<script language="JavaScript">
<!-- Begin
function jumpMenu(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&stud_id=" + document.col1.stud_id.options[document.col1.stud_id.selectedIndex].value;
}
//  End -->
</script>

<?php


echo $main;
foot();


//觀看模板
function &main_form($interface_sn="",$sel_year="",$sel_seme="",$class_id="",$stud_id=""){
	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next,$edit_mode,$submit;

	$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
	$c=explode("_",$class_id);
	$seme_class=$c[2].$c[3];
	if (substr($seme_class,0,1)=="0") $seme_class=substr($seme_class,1,strlen($seme_class)-1);
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	//假如沒有指定學生，取得第一位學生
	if(empty($stud_id)) {
		$sql="select stud_id from stud_seme where seme_year_seme='$year_seme' and seme_class='$seme_class' order by seme_num";
		$rs=$CONN->Execute($sql);
		$stud_id=$rs->fields['stud_id'];
	}

	//若仍是沒有$stud_id，則秀出錯誤訊息
	if(empty($stud_id))header("location:{$_SERVER['PHP_SELF']}?error=1");
	
	if ($chknext && $nav_next<>'')	$stud_id = $nav_next;
	
	//求得學生ID	
	$student_sn=stud_id2student_sn($stud_id);

	//取得該學生日常生活表現評量值
	$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
	
	//取得學生日常生活表現分數及導師評語建議
	$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme);

	//取得學生缺席情況
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
	
	//學生獎懲情況
	$reward_data = get_reward_value($stud_id,$sel_year,$sel_seme);	

	//取得學生成績檔
	$score_data = &get_score_value($stud_id,$student_sn,$class_id,$oth_data);

	//取得詳細資料
	$html=&html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn);
	
	$gridBgcolor="#DDDDDC";
	//已製作顯示顏色
	$over_color = "#223322";
	//左選單女生顯示顏色
	$non_color = "blue";

	//學年選單
	$class_seme_p = get_class_seme(); //學年度	
	$upstr = "<select name=\"year_seme\" onchange=\"this.form.submit()\">\n";
	while (list($tid,$tname)=each($class_seme_p)){
		if ($year_seme== $tid)
	      		$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
	      	else
	      		$upstr .= "<option value=\"$tid\">$tname</option>\n";
	}
	$upstr .= "</select><br>"; 
	//班級選單
	$tmp=&get_class_select($sel_year,$sel_seme,"","class_id","document.gridform.submit",$class_id);
	$upstr .= $tmp;

	$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單	   	
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->bgcolor = $gridBgcolor;
	$grid1->display_color = array("1"=>"$over_color","0"=>"$non_color");
	if ($stud_id_temp<>''){
		$stud_id_temp = ",stud_id in ($stud_id_temp) as tt ";
		$grid1->color_index_item ="tt" ; //顏色判斷值
	}
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num $stud_id_temp from stud_base a,stud_seme b where a.stud_id=b.stud_id  and (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$year_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令
	$grid1->do_query(); //執行命令 

	$stud_select = $grid1->get_grid_str($stud_id,$upstr,$downstr); // 顯示畫面

	//取得指定學生資料
	$stu=get_stud_base("",$stud_id);

	//座號
	$sql="select seme_num from stud_seme where seme_year_seme='$year_seme' and stud_id='$stud_id'";
	$rs=$CONN->Execute($sql);
	$stu_class_num=$rs->fields['seme_num'];

	//取得學校資料
	$s=get_school_base();

	$tool_bar=&make_menu($school_menu_p);

	
	$checked=($chknext)?"checked":"";
    			

	$main="
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar&stud_id=$stud_id&stu_num=$stu_class_num&class_id=$class_id'>下載".$stu[stud_name]."的成績單</a></p>
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar_all&class_id=$class_id'>下載全班的成績單</a></p>
	
	<input type='checkbox' name='chknext' value='1' $checked>自動跳下一位
	</td><td bgcolor='#FFFFFF' valign='top'>
	<p align='center'>
	<font size=3>".$s[sch_cname]." ".$sel_year."學年度第".$sel_seme."學期成績單</p>
	<table align=center cellspacing=4>
	<tr>
	<td>班級：<font color='blue'>$class[5]</font></td><td width=40></td>
	<td>座號：<font color='green'>$stu_class_num[num]</font></td><td width=40></td>
	<td>姓名：<font color='red'>$stu[stud_name]</font></td>
	</tr></table></font>
	$html
	</td></tr></table>
	";

	return $main;
}

//取得學生日常生活表現分數及導師評語建議
function get_nor_value($student_sn,$sel_year,$sel_seme) {
	global $CONN;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$query="select ss_score,ss_score_memo from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id=0";
	$res = $CONN->Execute($query) or trigger_error("執行錯誤 $query",E_USER_ERROR);
	$temp_arr = array();
	$temp_arr[ss_score]=$res->fields[ss_score];
	$temp_arr[ss_score_memo]=$res->fields[ss_score_memo];
	
	return $temp_arr;

}

//取得學生出缺席狀況

function get_abs_value($stud_id,$sel_year,$sel_seme) {                                                              
	global $CONN;
	if ($sel_seme==1) {
		$abs_sdate=(string)(1911+(integer)$sel_year)."-07-31";
		$abs_edate=(string)(1912+(integer)$sel_year)."-02-01";
	} else {
		$abs_sdate=(string)(1912+(integer)$sel_year)."-01-31";
		$abs_edate=(string)(1912+(integer)$sel_year)."-08-01";
	}
	$year_seme=sprintf("%03s%01s",$sel_year,$sel_seme);
	$temp_arr=array();
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='事假' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$temp_arr['1']=$recordSet->fields[0];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='病假' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$temp_arr['2']=$recordSet->fields[0];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='曠課' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$temp_arr['3']=$recordSet->fields[0];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='曠課' and (section = 'uf' or section = 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$temp_arr['4']=$recordSet->fields[0];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and (section = 'uf' or section = 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$temp=$recordSet->fields[0];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and absent_kind='公假' and (section != 'uf' and section != 'df')";
	$recordSet = $CONN->Execute($sql_select);
	$temp_arr['5']=$recordSet->fields[0];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate')";
	$recordSet = $CONN->Execute($sql_select);
	$temp_arr['6']=$recordSet->fields[0]-$temp_arr['1']-$temp_arr['2']-$temp_arr['3']-$temp-$temp_arr['5'];
	$sql_select = "select count(sasn) from stud_absent where stud_id='$stud_id' and (date>'$abs_sdate' and date<'$abs_edate') and (absent_kind!='公假')";
	$recordSet = $CONN->Execute($sql_select);
	if ($recordSet->fields[0]==0) $abs_score=5; 
	$sql_select = "select * from seme_score_nor where stud_id='$stud_id' and seme_year_seme='$year_seme'";
	$recordSet = $CONN->Execute($sql_select);
	$score1=$recordSet->fields['score1'];
	$score2=$recordSet->fields['score2'];
	$score3=$recordSet->fields['score3'];
	$score4=$recordSet->fields['score4'];
	$score5=$recordSet->fields['score5'];
	$score6=$recordSet->fields['score6'];
	$score7=$recordSet->fields['score7'];
	$nor_score=80+$score1+($score2+$score3+$score4+$score5)/4+$score6+$score7;
	$abs_score=$abs_score-$temp_arr['1']/30-$temp_arr['2']/80-$temp_arr['3']/2-$temp_arr['4']/4;
	$nor_score=number_format($nor_score+$abs_score,2);
	$sql_select = "select student_sn from stud_base where stud_id='$stud_id'";
	$recordSet = $CONN->Execute($sql_select);
	$student_sn=$recordSet->fields['student_sn'];
	$sql_select = "select * from stud_seme_score_nor where student_sn='$student_sn' and seme_year_seme='$year_seme'";
	$recordSet = $CONN->Execute($sql_select);
	$ss_score=$recordSet->fields['ss_score'];
	$chk=$recordSet->fields['seme_year_seme'];
	if ($chk) {
		$sql_select = "update stud_seme_score_nor set ss_score='$nor_score' where student_sn='$student_sn' and seme_year_seme='$year_seme'";
		$recordSet = $CONN->Execute($sql_select);
	} else {
		$sql_select = "insert into stud_seme_score_nor (seme_year_seme,student_sn ss_id,ss_score,ss_score_memo) values ('$year_seme','$student_sn','$nor_score','NULL')";
		$recordSet = $CONN->Execute($sql_select);
	}
	return $temp_arr;
}

//取得將懲記錄 

function get_reward_value($stud_id,$sel_year,$sel_seme) {
	global $CONN;
	$reward_year_seme=$sel_year.$sel_seme;
	$year_seme=sprintf("%03s%01s",$sel_year,$sel_seme);
	$query = "select reward_kind from reward where stud_id='$stud_id' and move_year_seme='$reward_year_seme'";
	$res = $CONN->Execute($query) or trigger_error("SQL 指令錯誤 $query",E_USER_ERROR);
	$temp_arr=array();
	for ($i=1;$i<7;$i++) $temp_arr[$i]=0;
	while(!$res->EOF){
		switch ( $res->fields[reward_kind]) {
			case 1:
				$temp_arr[3]++;
				break;
			case 2:
				$temp_arr[3]+=2;
				break;
			case 3:
				$temp_arr[2]++;
				break;
			case 4:
				$temp_arr[2]+=2;
				break;
			case 5:
				$temp_arr[1]++;
				break;
			case 6:
				$temp_arr[1]+=2;
				break;
			case 7:
				$temp_arr[1]+=3;
				break;
			case -1:
				$temp_arr[6]++;
				break;
			case -2:
				$temp_arr[6]+=2;
				break;
			case -3:
				$temp_arr[5]++;
				break;
			case -4:
				$temp_arr[5]+=2;
				break;
			case -5:
				$temp_arr[4]++;
				break;
			case -6:
				$temp_arr[4]+=2;
				break;
			case -7:
				$temp_arr[4]+=3;
				break;
			default:
				break;
		}
		$res->MoveNext();
	}
	$sql_select = "select student_sn from stud_base where stud_id='$stud_id'";
	$recordSet = $CONN->Execute($sql_select);
	$student_sn=$recordSet->fields['student_sn'];
	$sql_select = "select * from stud_seme_score_nor where student_sn='$student_sn' and seme_year_seme='$year_seme'";
	$recordSet = $CONN->Execute($sql_select);
	$ss_score=$recordSet->fields['ss_score'];
	$re_score=$temp_arr[1]*9+$temp_arr[2]*3+$temp_arr[3]-$temp_arr[4]*7;
	if ($temp_arr[5])
		if ($temp_arr[5]==1) $re_score=$re_score-2;
		else $re_score=$re_score-4-($temp_arr[5]-2)*3;
	if ($temp_arr[6]) $re_score=$re_score-$temp_arr[6]+1;
	$nor_score=$ss_score+$re_score;
	$sql_select = "update stud_seme_score_nor set ss_score='$nor_score' where student_sn='$student_sn' and seme_year_seme='$year_seme'";
	$recordSet = $CONN->Execute($sql_select);
	return $temp_arr;
	
}

// 取得成績檔
function &get_score_value($stud_id,$student_sn,$class_id,$oth_data) {
	global $CONN;
	$class=class_id_2_old($class_id);
	// 取得本年級的課程陣列
	$ss_name_arr = &get_ss_name_arr($class);
	// 取得努力程度文字敘述
//	$arr_1 = sfs_text("努力程度");
	// 取得課程每週時數
	$ss_num_arr = get_ss_num_arr($class_id);
	// 取得學習成就
	$ss_score_arr =get_ss_score_arr($class,$student_sn);

	$temp_str = "<table bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"4\" width=\"100%\">
	<tr bgcolor=\"#c4d9ff\">
	<td>科目</td>
	<td align=\"center\">每週節數</td>
	<td align=\"center\">努力程度</td>
	<td align=\"center\">學習成就</td>
	<td align=\"center\">學習描述文字說明</td>
	</tr>
	";

	$ss_sql_select = "select  ss_id  from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and class_year='$class[3]'  and  need_exam='1' and enable=1 order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	$hidden_ss_id='';
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		$temp_sel = $oth_data["努力程度"]["$ss_id"];
		if ($temp_sel=='') $temp_sel = "--";
		if ($ss_score_arr["$ss_id"][ss_score_memo]=="") $ss_score_arr["$ss_id"][ss_score_memo]="--";
		$temp_str .= "<tr bgcolor='white'>
			<td>$ss_name</td>
			<td align='center'>$ss_num_arr[$ss_id]節</td>			
			<td nowrap align='center'>$temp_sel</td>
			<td align='center'>".$ss_score_arr[$ss_id][score_name]."</td>
			<td>".$ss_score_arr[$ss_id][ss_score_memo]."</td>
			</tr>";
		//將ss_id 放在 hidden 	
		$hidden_ss_id .="$ss_id,";
	}

	return $temp_str."<input type=\"hidden\" name=\"hidden_ss_id\" value=\"$hidden_ss_id\">\n";
}

// 取得成績檔XML
function &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data) {
	global $CONN;
	$class=class_id_2_old($class_id);
	// 取得本年級的課程陣列
	$ss_name_arr = &get_ss_name_arr($class);
	// 取得學習成就
	$ss_score_arr =get_ss_score_arr($class,$student_sn);
	// 取得努力程度文字敘述
	$ss_sql_select = "select  ss_id  from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and class_year='$class[3]'  and  need_exam='1' and enable=1 order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		if ($oth_data["努力程度"]["$ss_id"]=='') $oth_data["努力程度"]["$ss_id"] = "--";
		if ($ss_score_arr["$ss_id"][ss_score_memo]=="") $ss_score_arr["$ss_id"][ss_score_memo]="--";
	}

	// 取得課程每週時數
	$ss_num_arr = get_ss_num_arr($class_id);

	$ss_sql_select = "select  ss_id from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and class_year='$class[3]'  and  need_exam='1' and enable=1 order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	$res_str = "";
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		$res_str.="<table:table-row table:style-name=\"ss_table.1\"><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$ss_name</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$ss_num_arr[$ss_id]</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$oth_data["努力程度"]["$ss_id"]."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$ss_score_arr[$ss_id][score_name]."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.E2\" table:value-type=\"string\"><text:p text:style-name=\"P11\">".$ss_score_arr[$ss_id][ss_score_memo]."</text:p></table:table-cell></table:table-row>";
	}

	return $res_str;
}


// $oth_data -- 與科目無關資料
// $abs_data -- 缺曠課記錄
// $reward_data -- 將懲記錄
// $score_data -- 成績記錄
function &html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn) {
	global $SFS_PATH_HTML,$CONN;
	$arr_1 = sfs_text("日常行為表現");
	$arr_2 = sfs_text("團體活動表現");
	$arr_3 = sfs_text("公共服務表現");
	$arr_4 = sfs_text("校外特殊表現");
	//假別
	$abs_kind_arr = stud_abs_kind();
	//獎懲
	$rep_kind_arr = stud_rep_kind();
	$sel1 = new drop_select();
	$sel1->use_val_as_key = true;
	for($i=1;$i<=4;$i++) {
		${"sel_str_$i"} = $oth_data['生活表現評量'][$i];
	}
	//日常生活表現評量
	$score_str_arr = &score2str_arr($class);
	$sel1->s_name="nor_score";
	$sel1->id = $nor_data[ss_score];
	$sel1->top_option="選擇等第";
	$sel1->use_val_as_key = false;
	$sel1->arr = $score_str_arr;
	$nor_score_sel = $sel1->get_select();
	$year_seme=sprintf("%03s%01s",$sel_year,$sel_seme);
	$sql_select = "select * from stud_seme_score_nor where student_sn='$student_sn' and seme_year_seme='$year_seme'";
	$recordSet = $CONN->Execute($sql_select);
	$final_nor_score=$recordSet->fields['ss_score'];
	if ($final_nor_score>=85) $final_nor="甲";
	elseif ($final_nor_score>=70) $final_nor="乙";
	elseif ($final_nor_score>=60) $final_nor="丙";
	elseif ($final_nor_score>=50) $final_nor="丁";
	else $final_nor="戊";
	if ($final_nor_score=="") $final_nor="";

	$temp_str ="
	<table cellspacing=\"0\" cellpadding=\"0\">
	<tr>
	<td>
	<table bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"4\" width=\"100%\">
	<tr bgcolor=\"white\">

	<td colspan=\"13\" nowrap>日常生活表現評量</td>
	</tr>
	<tr bgcolor=\"white\">
	<td nowrap>日常行為表現</td>
	<td colspan=\"3\">$sel_str_1
	</td>
	<td colspan=\"8\" nowrap>導師評語及建議</td>
	<td nowrap>等第</td>
	</tr>
	<tr bgcolor=\"white\">
	<td nowrap>團體活動表現</td>

	<td colspan=\"3\">$sel_str_2
	</td>
	<td rowspan=\"3\" colspan=\"8\">".$nor_data[ss_score_memo]."</td>
	<td rowspan=\"3\" colspan=\"1\" align='center'>$final_nor</td>
	</tr>

	<tr bgcolor=\"white\">
	<td nowrap>公共服務</td>
	<td colspan=\"3\">$sel_str_3
	</td>

	</tr>
	<tr bgcolor=\"white\">
	<td nowrap>校外特殊表現</td>
	<td colspan=\"3\">$sel_str_4
	</td>
	</tr>
	<tr bgcolor=\"white\">
	<td nowrap>學生缺席情況<br>
	</td>";
	while(list($id,$val)=each($abs_kind_arr)){
		$ttt = "節數";
		if ($id==4)
			$ttt= "次數";
		$temp_str .="<td nowrap>$val<br>$ttt</td>\n<td>$abs_data[$id]</td>\n";
	}
	
	$temp_str.= "</tr>
	<tr bgcolor=\"white\">
	<td nowrap>獎懲<br>
	</td>";
	//列出獎懲
	while(list($id,$val)= each($rep_kind_arr))
		$temp_str .= "<td nowrap>$val<br>次數</td>\n<td>$reward_data[$id]</td>\n";

	$temp_str .= "</tr>
	<tr bgcolor=\"white\">
	<td nowrap>其他</td>
	<td colspan=\"12\"><input type='text' name='oth_rep' id='oth_rep' value='".$oth_data['其他設定'][0]."' style='width:100%'></td>
	</tr>
	</table>
	</td></tr>
	<tr><td>
	$score_data
	</td></tr>
	</table>
	</td>
	</tr>
	</table>";
	return $temp_str;
}


//下載成績單
function downlod_ar($stud_id="",$class_id="",$interface_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height;
	
	//Openofiice的路徑
	$oo_path = "ooo/1";
	
	//檔名種類
	if($mode=="all"){
		$filename="score_".$class_id.".sxw";
	}else{
		$filename="score_".$class_id."_".$stu_num.".sxw";
	}
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_num=$class[2];
	$year_seme=sprintf("%03s%1s",$class[0],$class[1]);

	//取得備註說明
//	$memo_temp_str = &say_rule_2($class);
	
	//取得學校資料
	$s=get_school_base();
	
	
	//換頁 tag
	$break ="<text:p text:style-name=\"break_page\"/>";
	if ($draw_img_width=='') $draw_img_width="1.27cm";
	if ($draw_img_height=='') $draw_img_height="1.27cm";
	//校長簽章檔
	if (is_file($UPLOAD_PATH."school/title_img/title_1")){
		$title_img = "http://".$_SERVER["SERVER_ADDR"]."/".$UPLOAD_URL."school/title_img/title_1";
		$sign_1 ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"0\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
	}
	//教務主任簽章檔	
	if (is_file($UPLOAD_PATH."school/title_img/title_2")){
			$title_img = "http://".$_SERVER["SERVER_ADDR"]."/"."$UPLOAD_URL"."school/title_img/title_2";
			$sign_2 = "<draw:image draw:style-name=\"fr2\" draw:name=\"bbbb1\" text:anchor-type=\"paragraph\" svg:x=\"0.727cm\" svg:y=\"0.344cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"1\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
		}
	$arr_1 = sfs_text("日常行為表現");
	$arr_2 = sfs_text("團體活動表現");
	$arr_3 = sfs_text("公共服務表現");
	$arr_4 = sfs_text("校外特殊表現");
	//假別
	$abs_kind_arr = stud_abs_kind();
	//獎懲
	$rep_kind_arr = stud_rep_kind();

	
	//新增一個 zipfile 實例
	$ttt = new zipfile;
	
	//讀出 xml 檔案
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/META-INF/manifest.xml");

	//加入 xml 檔案到 zip 中，共有五個檔案 
	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
	$ttt->add_file($data,"/META-INF/manifest.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/settings.xml");
	$ttt->add_file($data,"settings.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/styles.xml");
	$ttt->add_file($data,"styles.xml");

	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/meta.xml");
	$ttt->add_file($data,"meta.xml");


	//班級資料〈若是單人，則秀單人資料〉
	$where=($mode=="all")?"where (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$year_seme' and b.seme_class='$class_num' order by b.seme_num ":"where a.stud_id='$stud_id' and b.seme_year_seme='$year_seme'";
	$query = "select a.stud_id,a.stud_name,a.student_sn,b.seme_num from stud_base a left join stud_seme b on a.stud_id=b.stud_id $where";
	$res = $CONN->Execute($query)or trigger_error($query, E_USER_ERROR);
	while (list($stud_id,$stud_name,$student_sn,$stu_num)=$res->FetchRow()) {
		
		//讀出 content.xml 
		$content_body = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");
		
		//$stu_num= intval (substr($stu_num,-2));
		
		//將 content.xml 的 tag 取代
		$temp_arr["city_name"] = $s[sch_sheng];	
		$temp_arr["school_name"] = $s[sch_cname];
		$temp_arr["stu_class"] = $class[5];
		$temp_arr["year"] = $sel_year;
		$temp_arr["seme"] = $sel_seme;
		$temp_arr["stu_name"] = $stud_name;
		$temp_arr["stu_num"] = $stu_num;

		//取得該學生日常生活表現評量值
		$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
		$temp_arr["2"] = $oth_data['生活表現評量'][1];
		$temp_arr["3"] = $oth_data['生活表現評量'][2];
		$temp_arr["4"] = $oth_data['生活表現評量'][3];
		$temp_arr["5"] = $oth_data['生活表現評量'][4];
		
		//取得學生缺席情況
		$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
		reset($abs_kind_arr);
		$i=9;	
		while(list($id,$val)=each($abs_kind_arr)){
			$temp_i=$abs_data[$id];
//			($abs_data[$id]==0)?$temp_i="":$temp_i=$abs_data[$id];
			$temp_arr["$i"] = $temp_i;
			$i++;
		}

		reset($rep_kind_arr);
		//學生獎懲情況
		$reward_data = get_reward_value($stud_id,$sel_year,$sel_seme);
		$i=16;
		while(list($id,$val)=each($reward_data)){
			$temp_i=$reward_data[$id];
//			($reward_data[$id]==0)?$temp_i="":$temp_i=$reward_data[$id];
			$temp_arr["$i"] = $temp_i;
			$i++;
		}


		//取得學生日常生活表現分數及導師評語建議
		$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme);
		$temp_arr["6"] = $nor_data[ss_score_memo];
		$temp_arr["7"] = score2str($nor_data[ss_score],$class);	

		//取得其他字串
		$temp_arr[22] = $oth_data['其他設定'][0];

		//取得學生成績檔
//		$temp_arr["ss_table"] = &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data);
//		$temp_arr["ss_table"] = $temp_arr["ss_table"];
	
		$temp_arr["SIGN_1"] = $sign_1;
		$temp_arr["SIGN_2"] = $sign_2;
		$temp_arr["MEMO"] = $memo_temp_str;
		
		//換行
		if($mode=="all")	$content_body .= $break;
		
		// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
		$replace_data .= $ttt->change_temp($temp_arr,$content_body);
	}
	//echo $replace_data;
	//exit;
	//讀出 XML 檔頭
	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head.xml");
	if ($line_width<>'') {
		$sign_arr["0.002cm solid #000000"] = "$line_width solid $line_color";
		//改換格線寬度
		$doc_head = $ttt->change_sigle_temp($sign_arr,$doc_head);
	}
	//讀出 XML 檔尾
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot.xml");

	$replace_data =$doc_head.$replace_data.$doc_foot;
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = $ttt->file();

	//以串流方式送出 sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}

?>
