<?php
// $Id: index.php 9057 2017-04-07 00:19:32Z chiming $
/* 取得設定檔 */
include "config.php";

sfs_check();

$year_seme=($_POST[year_seme])?$_POST[year_seme]:$_GET[year_seme];
$class_id=($_POST[class_id])?$_POST[class_id]: $_GET[class_id];
$student_sn=($_POST[student_sn])?$_POST[student_sn]:$_GET[student_sn];
$act=($_POST[act])?$_POST[act]:$_GET[act];
$act1=($_POST[act1])?$_POST[act1]:$_GET[act1];
$stu_num=($_POST[stu_num])?$_POST[stu_num]:$_GET[stu_num];
$stage = ($_POST[stage])?$_POST[stage]:$_GET[stage]; //階段
$yorn = findyorn();  //是否有平時成績
$start_date=($_POST[start_date])?$_POST[start_date]: $_GET[start_date];
$end_date=($_POST[end_date])?$_POST[end_date]: $_GET[end_date];
$avg=$_REQUEST['avg'];
$topmargin=$_REQUEST['topmargin'];
$buttommargin=$_REQUEST['buttommargin'];
$style_ss_num=$_REQUEST['style_ss_num'];
$inputstyle=$_REQUEST['inputstyle'];
$inputdata=($_REQUEST['inputdata']);
$inputdata=str_replace("<br /><text:line-break/>","\r\n",$inputdata);
$inputdatah=($inputdata);
$inputdata=nl2br($_REQUEST['inputdata']);
$inputdata=str_replace("\r\n","<text:line-break/>",$inputdata);


if ($stage=="") $stage=1;

$M_SETUP=get_module_setup('score_stage_chart');

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
	if ($class_id=="") {
		$sql="select seme_class,student_sn from stud_seme where seme_year_seme='$year_seme'";
		$rs=$CONN->Execute($sql);
		$class_num=$rs->fields['seme_class'];
		$student_sn=$rs->fields['student_sn'];
		$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($class_num,0,-2),substr($class_num,-2,2));
	}
} else {
	$sel_year=(substr($year_seme,0,1)=="0")?substr($year_seme,1,2):substr($year_seme,0,3);
	$sel_seme=substr($year_seme,3,1);
	$temp_class_ar = explode("_",$class_id);
	$class_id = sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$temp_class_ar[2],$temp_class_ar[3]);
}

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];

//執行動作判斷

if($act=="dlar"){
	//echo $stage.$start_date.$end_date;exit;
	downlod_ar($student_sn,$class_id,$interface_sn,$stu_num,$sel_year,$sel_seme,"",$stage,$start_date,$end_date,$avg);
	header("location: {$_SERVER['SCRIPT_NAME']}?stud_id=$stud_id");
}elseif($act=="dlar_all"){
	downlod_ar("",$class_id,$interface_sn,"",$sel_year,$sel_seme,"all",$stage,$start_date,$end_date,$avg);
	header("location: {$_SERVER['SCRIPT_NAME']}?stud_id=$stud_id");
}else{
	echo " ";
	$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$student_sn,$stage);
}


//秀出網頁
head("定期考查通知書");
// 您的程式碼由此開始
print_menu($menu_p);
echo $main;
foot();

//觀看模板
function &main_form($interface_sn="",$sel_year="",$sel_seme="",$class_id="",$student_sn="",$stage){

	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next,$edit_mode,$submit,$stage,$start_date,$end_date,$year_seme,$inputstyle,$inputdata,$style_ss_num,$topmargin,$buttommargin;

	if (empty($style_ss_num))
	{
	$a1="checked";
	$a2="";
	$a3="";
	}
	if ($style_ss_num==1)
	{	
	$a1="";
	$a2="checked";
	$a3="";
	}
	
	if ($style_ss_num==2)
	{	
	$a1="";
	$a2="";
	$a3="checked";
	}
	
	$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
	$c=explode("_",$class_id);
	$seme_class=$c[2].$c[3];
	if (substr($seme_class,0,1)=="0") $seme_class=substr($seme_class,1,strlen($seme_class)-1);
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	//假如沒有指定學生，取得第一位學生
	if(empty($student_sn)) {
		$sql="select student_sn from stud_seme where seme_year_seme='$year_seme' and seme_class='$seme_class' order by seme_num";
		$rs=$CONN->Execute($sql);
		$student_sn=$rs->fields['student_sn'];
	}

	//日常生活表現統計時間
	$db_date=curr_year_seme_day($sel_year,$sel_seme);
    	if(!$start_date) $start_date=$db_date[start];	
	if(!$end_date) $end_date=date("Y-m-d");
	if($end_date>$db_date[end]) $end_date=$db_date[end];
		
	//求得學生ID	
	$stud_id=student_sn2stud_id($student_sn);

	//取得學生缺席情況
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"種類",$start_date,$end_date);

	//學生獎懲情況
	$reward_data = getOneM_good_bad_data($stud_id,$sel_year,$sel_seme,$start_date,$end_date);

	//取得學生成績檔
	$score_data = &get_score_value($stud_id,$student_sn,$class_id,"",$sel_year,$sel_seme,$stage);

	//取得詳細資料
	$html=&html2code2_stage($class,$sel_year,$sel_seme,$abs_data,$reward_data,$score_data,$student_sn);
	
	$gridBgcolor="#DDDDDC";
	//已製作顯示顏色
	$over_color = "#FF6633";
	//左選單女生顯示顏色
	$non_color = "blue";

	//學年選單
	$class_seme_p = get_class_seme(); //學年度
	
	$upstr = " <select name=\"year_seme\" onchange=\"this.form.submit()\">\n";
	while (list($tid,$tname)=each($class_seme_p)){
		if ($year_seme== $tid)
	      		$upstr .= "<option value=\"$tid\" selected>$tname</option>\n";
	      	else
	      		$upstr .= "<option value=\"$tid\">$tname</option>\n";
	}
	$upstr .= "</select><br>\n<input type='hidden' name='start_date' value='$start_date'>\n<input type='hidden' name='end_date' value='$end_date'>\n"; 
			
	$upstr .= "<input type='hidden' name='stage' value='$stage'>\n";
	$upstr .= "<input type='hidden' name='inputstyle' value='$inputstyle'>";
	$upstr .= "<input type='hidden' name='inputdata' value='$inputdata'>";
	
	$upstr .= "<input type='hidden' name='style_ss_num' value='$style_ss_num'>";
	$upstr .= "<input type='hidden' name='topmargin' value='$topmargin'>";
	$upstr .= "<input type='hidden' name='buttommargin' value='$buttommargin'>";
	
	//班級選單
	$tmp=&get_class_select($sel_year,$sel_seme,"","class_id","this.form.submit",$class_id);
	
	$upstr .= $tmp;

	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   	
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名 

	$grid1->bgcolor = $gridBgcolor;
	$grid1->display_color = array("2"=>"$over_color","1"=>"$non_color");
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值

	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.stud_id,a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num $stud_id_temp from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5 or a.stud_study_cond=8) and  b.seme_year_seme='$year_seme' and b.seme_class='$seme_class' order by b.seme_num ";   //SQL 命令
	$grid1->do_query(); //執行命令 

	$stud_select = $grid1->get_grid_str($student_sn,$upstr,$downstr); // 顯示畫面

	//取得指定學生資料
	$stu=get_stud_base($student_sn);

	//階段選單
	$stagestr .= "";
	$stagestr .= "<select name=\"stage\" onchange=\"this.form.submit()\";>\n";
        $show_stage = select_stage2($year_seme,$seme_class);
	$stagestr .= "<option value=\"\" >選擇階段</option>\n";
	while (list($tid,$tname)=each($show_stage)){
		if ($stage== $tid)
	      		$stagestr .= "<option value=\"$tid\" selected>$tname</option>\n";
	      	else
	      		$stagestr .= "<option value=\"$tid\">$tname</option>\n";
	}
        $stagestr .= "</select>";


	//座號
	$sql="select seme_num from stud_seme where seme_year_seme='$year_seme' and student_sn='$student_sn'";
	$rs=$CONN->Execute($sql);
	$stu_class_num=$rs->fields['seme_num'];


	//取得學校資料
	$s=get_school_base();

	 if($grid1->count_row!=0)
		 
		 $dlstr ="<hr>
	        
	        <p align='center'>●有加權平均●</p>
			<p>

			<form action=\"{$_SERVER['SCRIPT_NAME']}\" name=\"myForm\"method=post >
			<input type='hidden' name='act' value='$act' />
			<input type='hidden' name='student_sn' value='$student_sn' />
			<input type='hidden' name='stu_num' value='$stu_class_num' />
			<input type='hidden' name='class_id' value='$class_id' />
			<input type='hidden' name='stage' value='$stage' />
			<input type='hidden' name='start_date' value='$start_date' />
			<input type='hidden' name='end_date' value='$end_date' />
			<input type='hidden' name='sel_year' value='$sel_year' />
			<input type='hidden' name='sel_seme' value='$sel_seme' />
			<input type='hidden' name='inputstyle' value='$inputstyle' />
			<input type='hidden' name='inputdata' value='$inputdata' />
			<input type='hidden' name='topmargin' value='$topmargin' />
			<input type='hidden' name='buttommargin' value='$buttommargin' />
            <input type='hidden' name='style_ss_num' value='$style_ss_num' />
			<input type='hidden' name='avg' value='1' />
	
			<input type='BUTTON' value=\"下載 $stu[stud_name] 的成績單\" name=\"mySubmit\" 
			onclick='document.myForm.act.value=\"dlar\";
			document.myForm.student_sn.value=\"$student_sn\";
			document.myForm.stu_num.value=\"$stu_class_num\";
			document.myForm.class_id.value=\"$class_id\";
		    document.myForm.stage.value=\"$stage\";
			document.myForm.start_date.value=\"$start_date\";
			document.myForm.end_date.value=\"$end_date\";
			document.myForm.inputstyle.value=\"$inputstyle\";
			document.myForm.inputdata.value=\"$inputdata\";
			document.myForm.topmargin.value=\"$topmargin\";
			document.myForm.buttommargin.value=\"$buttommargin\";
			document.myForm.style_ss_num.value=\"$style_ss_num\";
			document.myForm.avg=\"1\";
			document.myForm.submit();
			'>
		
			<p>
			
			<input type='BUTTON' value=\"下載全班的成績單\" name=\"mySubmit\" 
			onclick='document.myForm.act.value=\"dlar_all\";
			document.myForm.class_id.value=\"$class_id\";
		    document.myForm.stage.value=\"$stage\";
			document.myForm.start_date.value=\"$start_date\";
			document.myForm.end_date.value=\"$end_date\";
			document.myForm.inputstyle.value=\"$inputstyle\";
			document.myForm.inputdata.value=\"$inputdata\";
			document.myForm.topmargin.value=\"$topmargin\";
			document.myForm.buttommargin.value=\"$buttommargin\";
			document.myForm.avg=\"1\";
            document.myForm.submit();			
			'></form>
			
			
			
			<hr><p align='center'>○無加權平均○</p>
			<form action=\"{$_SERVER['SCRIPT_NAME']}\" name=\"myForm1\"method=post >
			<input type='hidden' name='act' value='$act' />
			<input type='hidden' name='student_sn' value='$student_sn' />
			<input type='hidden' name='stu_num' value='$stu_class_num' />
			<input type='hidden' name='class_id' value='$class_id' />
			<input type='hidden' name='stage' value='$stage' />
			<input type='hidden' name='start_date' value='$start_date' />
			<input type='hidden' name='end_date' value='$end_date' />
			<input type='hidden' name='sel_year' value='$sel_year' />
			<input type='hidden' name='sel_seme' value='$sel_seme' />
			<input type='hidden' name='inputstyle' value='$inputstyle' />
			<input type='hidden' name='inputdata' value='$inputdata' />
			<input type='hidden' name='topmargin' value='$topmargin' />
			<input type='hidden' name='buttommargin' value='$buttommargin' />
            <input type='hidden' name='style_ss_num' value='$style_ss_num' />
			<input type='hidden' name='avg' value='0' />
			
		    <input type='BUTTON' value=\"下載 $stu[stud_name] 的成績單\" name=\"mySubmit\" 
			onclick='document.myForm1.act.value=\"dlar\";
			document.myForm1.student_sn.value=\"$student_sn\";
			document.myForm1.stu_num.value=\"$stu_class_num\";
			document.myForm1.class_id.value=\"$class_id\";
		    document.myForm1.stage.value=\"$stage\";
			document.myForm1.start_date.value=\"$start_date\";
			document.myForm1.end_date.value=\"$end_date\";
			document.myForm1.inputstyle.value=\"$inputstyle\";
			document.myForm1.inputdata.value=\"$inputdata\";
            document.myForm1.topmargin.value=\"$topmargin\";
			document.myForm1.buttommargin.value=\"$buttommargin\";
			document.myForm1.avg=\"0\";
			document.myForm1.submit();
			'>
		
			<p>
			
			<input type='BUTTON' value=\"下載全班的成績單\" name=\"mySubmit\" 
			onclick='document.myForm1.act.value=\"dlar_all\";
			document.myForm1.class_id.value=\"$class_id\";
		    document.myForm1.stage.value=\"$stage\";
			document.myForm1.start_date.value=\"$start_date\";
			document.myForm1.end_date.value=\"$end_date\";	
			document.myForm1.inputstyle.value=\"$inputstyle\";
			document.myForm1.inputdata.value=\"$inputdata\";
			document.myForm1.topmargin.value=\"$topmargin\";
			document.myForm1.buttommargin.value=\"$buttommargin\";
			document.myForm1.avg=\"0\";
            document.myForm1.submit();			
			'><hr>
			
			</form>";

	

	$main=" 
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select $dlstr

	
	</td><td bgcolor='#FFFFFF' valign='top'>
	<p align='center'>
	
	<form name=\"myform\" method=post >
	
	<input type=\"radio\" name=\"style_ss_num\" value=\"\" $a1 onclick=\"this.form.submit()\";>以課表的每週節數 | 
	<input type=\"radio\" name=\"style_ss_num\" value=\"1\" $a2 onclick=\"this.form.submit()\";>以課程設定的每週節數 | 
	<input type=\"radio\" name=\"style_ss_num\" value=\"2\" $a3 onclick=\"this.form.submit()\";>以課程設定的加權數作為每週節數<p>

	
	<font size=3>".$s[sch_cname]." ".$sel_year."學年度第".$sel_seme."學期".$stagestr."定期評量成績.</p>
	<table align=center cellspacing=4>
	<tr><td colspan=3>日常生活表現統計時間
		<input type=text name=\"start_date\" value=\"$start_date\" size='8'>
		~<input type=text name=\"end_date\" value=\"$end_date\" size='8'>
		<input type=hidden name=\"class_id\" value=\"$class_id\">		
		<input type=hidden name=\"year_seme\" value=\"$year_seme\">		
		<input type=hidden name=\"student_sn\" value=\"$student_sn\">
		<input type=hidden name=\"topmargin\" value=\"$topmargin\">
		<input type=hidden name=\"buttommargin\" value=\"$buttommargin\">
		
		<input type=hidden name=\"selstage\" value=\"2\">		
		<input type=submit value=\"修改時間\"></td></tr>
	<tr>
	
	<td>班級：<font color='blue'>$class[5]</font></td>
	<td>座號：<font color='green'>$stu_class_num</font></td>
	<td>姓名：<font color='red'>$stu[stud_name]</font></td>
	</tr></table></font>
	$html
	</td></tr></form></table>";

	return $main;
}

// 取得成績檔XML
function &get_score_xml_value($stud_id,$student_sn,$class_id,$sel_year,$sel_seme,$stage,$avg) {
	global $CONN,$style_ss_num;
	$class=class_id_2_old($class_id);
	// 取得本年級的課程陣列
	$ss_name_arr = &get_ss_name_arr($class);
	
	
	// 取得課程每週時數
	$ss_num_arr = get_ss_num_arr($class_id);

	
	if ($style_ss_num==1)//以課程設定每周節數為主
	{
	$ss_num_arr =get_ss_num_arr_from_score_ss($class_id);
	}
	
	if ($style_ss_num==2)//以課程設的加權數作為節數
	{
	$ss_num_arr =get_ss_num_arr_from_score_ss_rate($class_id);
	}
	
	
	
	// 取得學習成就

	$ss_score_arr =get_ss_score($student_sn,$sel_year,$sel_seme,$stage);
	
	//計算平均
$sectors=0;
foreach($ss_score_arr as $key=>$value){
	$ss_score_sum['定期評量']+=$value['定期評量']*$ss_num_arr[$key];
	$ss_score_sum['平時成績']+=$value['平時成績']*$ss_num_arr[$key];
	$sectors+=$ss_num_arr[$key];	
}
$ss_score_sum['定期評量']=sprintf("%3.2f",$ss_score_sum['定期評量']/$sectors);
$ss_score_sum['平時成績']=sprintf("%3.2f",$ss_score_sum['平時成績']/$sectors);

$ss_score_avg['平均']['定期評量']=$ss_score_sum['定期評量'];
$ss_score_avg['平均']['平時成績']=$ss_score_sum['平時成績'];

	$ss_sql_select = "select ss_id from score_ss where class_id='".sprintf("%03s_%s_%02s_%02s",$class[0],$class[1],$class[3],$class[4])."'and need_exam='1' and enable='1' and print='1' order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	if ($ss_recordSet->RecordCount() ==0){
		$ss_sql_select = "select ss_id from score_ss where year='$class[0]' and semester='$class[1]' and class_year='$class[3]' and class_id='' and need_exam='1' and enable='1' and print='1' order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	}
	$res_str = "";
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		if ($ss_score_arr[$ss_id][定期評量]=='') $ss_score_arr[$ss_id][定期評量]="--";
		if ($ss_score_arr[$ss_id][平時成績]=="") $ss_score_arr[$ss_id][平時成績]="--";
		$res_str.='<table:table-row><table:table-cell table:style-name="table2.A2" table:value-type="string"><text:p text:style-name="P6">'.$ss_name.'</text:p></table:table-cell><table:table-cell table:style-name="table2.B2" table:value-type="string"><text:p text:style-name="P7">'.$ss_num_arr[$ss_id].'</text:p></table:table-cell><table:table-cell table:style-name="table2.A2" table:value-type="string"><text:p text:style-name="P7">'.round($ss_score_arr[$ss_id][定期評量],0).'</text:p></table:table-cell><table:table-cell table:style-name="table2.D3" table:value-type="string"><text:p text:style-name="P7">'.round($ss_score_arr[$ss_id][平時成績],0).'</text:p></table:table-cell></table:table-row>';
	}
	if($avg)
	$res_str.='<table:table-row><table:table-cell table:style-name="table2.B2" table:number-columns-spanned="2" table:value-type="string"><text:p text:style-name="P7">加權平均</text:p></table:table-cell><table:table-cell table:style-name="table2.A2" table:value-type="string"><text:p text:style-name="P7">'.round($ss_score_avg['平均']['定期評量'],0).'</text:p></table:table-cell><table:table-cell table:style-name="table2.D3" table:value-type="string"><text:p text:style-name="P7">'.round($ss_score_avg['平均']['平時成績'],0).'</text:p></table:table-cell></table:table-row>';

	return $res_str;
}



// $abs_data -- 缺曠課記錄
// $reward_data -- 將懲記錄
// $score_data -- 成績記錄
function &html2code2_stage($class,$sel_year,$sel_seme,$abs_data,$reward_data,$score_data,$student_sn) {
	global $SFS_PATH_HTML,$CONN,$REWARD_KIND,$year_seme,$IS_JHORES,$dlstr,$inputstyle,$inputdatah,$topmargin,$buttommargin;
	
	//列印上邊界選單
	$select_topmargin = "";
	$select_topmargin .= "上邊界:<select name=\"topmargin\">\n";
	for($ti=1;$ti<=30;$ti=$ti+1)
	{
		$tii=($ti/10)."cm";
		   if ($topmargin==$ti)
		   {
	      		$select_topmargin .= "<option value=\"$ti\" selected>$tii</option>\n";
		   }
			else
			{
	      		$select_topmargin .= "<option value=\"$ti\">$tii</option>\n";
			}
	}
     $select_topmargin .= "</select>";
	
	//列印下邊界選單
	$select_buttommargin = "";
	$select_buttommargin .= "下邊界:<select name=\"buttommargin\">\n";
	for($ti=1;$ti<=30;$ti=$ti+1)
	{
		$tii=($ti/10)."cm";
		   if ($buttommargin==$ti)
		   {
	      		$select_buttommargin .= "<option value=\"$ti\" selected>$tii</option>\n";
		   }
			else
			{
	      		$select_buttommargin .= "<option value=\"$ti\">$tii</option>\n";
			}
	}
     $select_buttommargin .= "</select>";	
	
	
	if (empty($inputstyle))$inputstyle="導師評語及建議";
	
	//假別
	$abs_kind_arr = stud_abs_kind();

	//獎懲
	$rep_kind_arr = stud_rep_kind();


	$temp_str ="
	<table cellspacing=\"0\" cellpadding=\"0\">
	<tr>
	<td>
	<table bgcolor=\"#9ebcdd\" cellspacing=\"1\" cellpadding=\"4\" width=\"100%\">
	<tr bgcolor=\"white\">

	<td colspan=\"13\" nowrap>日常生活表現評量</td>
	</tr>

	<tr bgcolor=\"white\">
	<td nowrap>學生缺席情況<br>
	</td>";
	while(list($id,$val)=each($abs_kind_arr)){
		$ttt = "節數";
		if ($id==4)
			$ttt= "次數";
		$temp_str .="<td nowrap>$val<br>$ttt</td>\n<td width=\"30\" align=\"center\">$abs_data[$id]</td>\n";
	}
	
	$temp_str.= "</tr>
	<tr bgcolor=\"white\">
	<td nowrap>獎懲<br>
	</td>";
	//列出獎懲
	foreach($REWARD_KIND as $key=>$gbkind)
		$temp_str .= "<td nowrap>$gbkind<br>次數</td>\n<td width=\"30\" align=\"center\">$reward_data[$key]</td>\n";

	$temp_str .= "</tr>";
	
	$col_num=count($REWARD_KIND)*2;
	
	
	if ($IS_JHORES>0) {
  	$ALL_SERV=getService_allmin($student_sn,$year_seme);
		$service = round($ALL_SERV/60,2);
  	$temp_str .= "	
  	 <tr bgcolor=\"white\"><td nowrap>服務學習</td><td colspan=\"$col_num\" nowrap>本學期迄今已服務 $service 小時</td></tr>   
  	";	
  } // end if 是否為國中
	$temp_str .= "</table>
	</td></tr>
	<tr><td>
	$score_data
	</td></tr>
	<tr><td colspan=4>
	<form name=\"myForm\" method=post >
	成績單備註抬頭:<input type='text' name='inputstyle' value='$inputstyle'><br>
	<textarea name='inputdata' rows='5' style='width:100%'>$inputdatah</textarea>
    $select_topmargin $select_buttommargin
	<input type=submit value=\"設定確定\">
	</form>
	</td></tr>
	
	
	</table>
	</td>
	</tr>
	</table>";
	return $temp_str;
}


//下載成績單
function downlod_ar($student_sn="",$class_id="",$interface_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode="",$stage,$start_date,$end_date,$avg){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$M_SETUP,$REWARD_KIND,$year_seme,$inputstyle,$inputdata,$topmargin,$buttommargin;
  
  global $IS_JHORES;

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
	
	//取得學校資料
	$s=get_school_base();
	
	
	//換頁 tag
	$break ='<text:p text:style-name="P10"/>';

	if ($M_SETUP['hborder']=="") $M_SETUP['hborder'] = 1.27;
	if ($M_SETUP['wborder']=="") $M_SETUP['wborder'] = 1.27;
	if ($draw_img_width=='') $draw_img_width=$M_SETUP['hborder']."cm";
	if ($draw_img_height=='') $draw_img_height=$M_SETUP['wborder']."cm";
	$img_title=get_title_pic();//讀取職稱圖章
	
	//校長簽章檔
//	if (is_file($UPLOAD_PATH."school/title_img/title_1")){
// 	$title_img = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."school/title_img/title_1";
	if ($img_title["校長"]!=''){
		$title_img = $img_title["校長"];
		//$title_img = $SFS_PATH_HTML."data/school/title_img/title_1";
		//$title_img = $SFS_PATH_HTML.$UPLOAD_URL."school/title_img/title_1";
		$sign_1 ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"0\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
	}
	//教務主任簽章檔	
//	if (is_file($UPLOAD_PATH."school/title_img/title_2")){
//	$title_img = "http://".$_SERVER["SERVER_ADDR"].$UPLOAD_URL."school/title_img/title_2";
	if ($img_title["教務主任"]!=''){
		 $title_img = $img_title["教務主任"];
		//$title_img = $SFS_PATH_HTML."data/school/title_img/title_2";
		//$title_img = $SFS_PATH_HTML.$UPLOAD_URL."school/title_img/title_2";
		$sign_2 = "<draw:image draw:style-name=\"fr2\" draw:name=\"bbbb1\" text:anchor-type=\"paragraph\" svg:x=\"0.727cm\" svg:y=\"0.344cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"1\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
		}

	//假別
	$abs_kind_arr = stud_abs_kind();

	//獎懲
	$rep_kind_arr = stud_rep_kind();

	
	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);	
	//讀出 xml 檔案
	//$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/META-INF/manifest.xml");

	$styles = $ttt->read_file(dirname(__FILE__)."/$oo_path/styles.xml");
    if (empty($topmargin))$topmargin=20;
	if (empty($buttommargin))$buttommargin=20;
	$styles_arr["topmargin"] = $topmargin/10;
	$styles_arr["buttommargin"] = $buttommargin/10;
	$styles = $ttt->change_temp($styles_arr,$styles,0);
	$ttt->add_file($styles,"styles.xml");

	$ttt->addDir("META-INF");
	$ttt->addfile("settings.xml");
//	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");



        //找出該班的所有學生
	$sn_arr=array();
	if($mode=="all") {
		if ($sel_year==curr_year() && $sel_seme==curr_seme())
			$sn_arr=class_id_to_student_sn($class_id);
		else {
			$query="select student_sn from stud_seme where seme_year_seme='".sprintf("%03d%d",$sel_year,$sel_seme)."' and seme_class='".$class[2]."' order by seme_num";
			$res=$CONN->Execute($query);
			$i=0;
			while(!$res->EOF) {
				$sn_arr[$i]=$res->fields[0];
				$i++;
				$res->MoveNext();
			}
		}
	} else
		$sn_arr[]=$student_sn;

        for($m=0;$m<count($sn_arr);$m++){		
		//讀出 content.xml 
		$content_body = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");


		//$stu_num= intval (substr($stu_num,-2));

		$student_info=student_sn_to_classinfo($sn_arr[$m],$sel_year,$sel_seme);
		//將 content.xml 的 tag 取代

		$temp_arr["school_name"] = $s[sch_cname];
		$temp_arr["stu_class"] = $class[5];
		$temp_arr["year"] = $sel_year;
		$temp_arr["seme"] = $sel_seme;

		$temp_arr["stage"] = $stage;
		$temp_arr["stu_name"] = $student_info[4];
		$temp_arr["stu_num"] = $student_info[2];
		$temp_arr["start_date"] = $start_date;
		$temp_arr["end_date"] = $end_date;		
		$temp_arr["avg"] = $avg;	
		
		$temp_arr["inputstyle"] = $inputstyle;
		$temp_arr["inputdata"] = $inputdata;
		
		
		$stud_id = student_sn2stud_id($sn_arr[$m]);

		//取得學生缺席情況
		$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"種類",$start_date,$end_date);
		reset($abs_kind_arr);
		$i=1;	
		while(list($id,$val)=each($abs_kind_arr)){
			$temp_arr["$i"]=$abs_data[$id];
			$i++;
		}

	
		//學生獎懲情況
		$reward_data = getOneM_good_bad_data($stud_id,$sel_year,$sel_seme,$start_date,$end_date);
		$i=7;
		foreach($REWARD_KIND as $key=>$gbkind){
			$temp_i=$reward_data[$key];
			$temp_arr["$i"] = $temp_i;
			$i++;			
		}

		//取得學生成績檔{ss_table}
		$temp_arr_score["ss_table"] = &get_score_xml_value($stud_id,$sn_arr[$m],$class_id,$sel_year,$sel_seme,$stage,$avg);
		$temp_arr_score["SIGN_1"] = $sign_1;
		$temp_arr_score["SIGN_2"] = $sign_2;
		
		
		
		
		if ($IS_JHORES>0) {
		 $ALL_SERV=getService_allmin($sn_arr[$m],$year_seme);
		 $SERV = round($ALL_SERV/60,2);
		
		 $temp_arr_score["SERVICE"]="<table:table-row><table:table-cell table:style-name=\"table1.A2\" table:value-type=\"string\"><text:p text:style-name=\"P4\">服務學習</text:p>";
		 $temp_arr_score["SERVICE"].="</table:table-cell><table:table-cell table:style-name=\"table1.B4\" table:number-columns-spanned=\"12\" table:value-type=\"string\"><text:p text:style-name=\"P4\">";
		 $temp_arr_score["SERVICE"].="本學期迄今已服務 ".$SERV." 小時</text:p></table:table-cell><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/></table:table-row>";
	  } else {
	    $temp_arr_score["SERVICE"]="";
	  } //判斷國中或國小, 是否有服務學習
		
		$content_body = $ttt->change_temp($temp_arr_score,$content_body,0);
		
	
		
		//換行
		if($mode=="all") $content_body .= $break;
		
		// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
		$replace_data .= $ttt->change_temp($temp_arr,$content_body,0);
	}

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
	$sss = & $ttt->file();

	//以串流方式送出 sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");

  //header("Pragma: no-cache");
  //因應 IE 6,7,8 在 SSL 模式下無法下載，取消 no-cache 改為以下
	header("Cache-Control: max-age=0");
	header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}

//增加服務學習資料 2013/06/05 by smallduh
function getService_allmin($student_sn,$year_seme) {
 $query="select sum(minutes) from stud_service_detail a,stud_service b where a.student_sn='$student_sn' and b.year_seme='$year_seme' and a.item_sn=b.sn and b.confirm=1";
 $result=mysql_query($query);
 list($min)=mysql_fetch_row($result);
 return $min;
}

//讀取職稱圖章,傳回URL陣列,無圖章者不會在陣列中
function get_title_pic() {
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML;
	$URL = parse_url($SFS_PATH_HTML);
	$sql = "select * from teacher_title where enable='1' ";
	$rs = $CONN->Execute($sql);
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		if (!empty($ro->title_name)) {
			//---檢查是否有圖章存在
			$pic_file = $UPLOAD_PATH."school/title_img/title_{$ro->teach_title_id}";
			if (file_exists($pic_file)) {
				$arys[$ro->title_name]="{$URL['scheme']}://{$URL['host']}{$UPLOAD_URL}school/title_img/title_{$ro->teach_title_id}";
			}
		}
	}
	return $arys;
}

