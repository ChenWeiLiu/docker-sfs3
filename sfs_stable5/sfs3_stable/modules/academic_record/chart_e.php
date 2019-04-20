<?php

// $Id: chart_e.php 7727 2013-10-28 08:26:17Z smallduh $

/* 取得設定檔 */
include "config.php";
sfs_check();
$oth_arr_score = array("表現優異"=>5,"表現良好"=>4,"表現尚可"=>3,"需再加油"=>2,"有待改進"=>1);
$oth_arr_score_2 = array(5=>"表現優異",4=>"表現良好",3=>"表現尚可",2=>"需再加油",1=>"有待改進");

if ($IS_JHORES=='6') header("location: chart_j.php");

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
$sel_year = curr_year();
$sel_seme = curr_seme();
//取得任教班級代號
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);
$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);
$year_seme=sprintf("%03s%1s",$sel_year,$sel_seme);
if ($chart_kind=="") $chart_kind=3;
$chart_kind+=$none_text*3;

//取得本學期上課總日數
$query = "select days from seme_course_date where seme_year_seme='$year_seme' and class_year=".substr($class_num,0,1);
$res= $CONN->Execute($query) or trigger_error("您不是班級導師<br>".$query,256);
$TOTAL_DAYS = $res->fields[0];

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];
if ($chknext)	$ss_temp = "&chknext=$chknext&nav_next=$nav_next";

//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="存檔"){
	save_value();
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id$ss_temp");
}elseif($act=="儲存修改"){
	save_value();
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id$ss_temp");
}elseif($act=="dlar"){
	downlod_ar($text_cond,$stud_id,$class_id,$interface_sn,$stu_num,$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id");
}elseif($act=="dlar_all"){
	downlod_ar($text_cond,"",$class_id,$interface_sn,"",$sel_year,$sel_seme,"all");
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
	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next,$edit_mode,$submit,$chk_menu_arr,$chart_kind;

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
	$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme,"",($chk_menu_arr)?1:0);

	//取得學生缺席情況
	$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme);
	
	//取得學生成績檔
	$score_data = &get_score_value($stud_id,$student_sn,$class_id,$oth_data);

	
	if(count($oth_data['生活表現評量'])>0){
		$edit_mode="update";
		$submit="儲存修改";
	}else{
		$edit_mode="add";
		$submit="存檔";
	}


	//取得詳細資料
	$html=&html2code2($class,$sel_year,$sel_seme,$oth_data,$nor_data,$abs_data,$reward_data,$score_data,$student_sn,($chk_menu_arr)?1:0);
	
	$gridBgcolor="#DDDDDC";
	//已製作顯示顏色
	$over_color = "#223322";
	//左選單女生顯示顏色
	$non_color = "blue";

	//班級選單
	$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單	   	
	$grid1->key_item = "stud_id";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->bgcolor = $gridBgcolor;
	$grid1->display_color = array("1"=>"blue","2"=>"red");
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
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
	//if ($use_both) $tool_bar=&make_menu($school_menu_p);

	
	$checked=($chknext)?"checked":"";
    			

	$main="
	<script language=\"JavaScript\">
	var remote=null;
	function OpenWindow(p,x){
	strFeatures =\"top=300,left=20,width=500,height=200,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
	remote = window.open(\"comment.php?cq=\"+p,\"MyNew\", strFeatures);
	if (remote != null) {
	if (remote.opener == null)
	remote.opener = self;
	}
	if (x == 1) { return remote; }
	}
	function OpenWindow2(p,x){
	strFeatures =\"top=0,left=0,width=600,height=400,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
	remote = window.open(\"quick_input_memo.php?cq=\"+p,\"MyNew\", strFeatures);
	if (remote != null) {
	if (remote.opener == null)
	remote.opener = self;
	}
	if (x == 1) { return remote; }
	}

	function checkok() {
	document.col1.nav_next.value = document.gridform.nav_next.value;	
	return true;	
	}
	
	</script>
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select
	<hr>";
        $main.=($chk_menu_arr)?"
	<font color=red>網頁式成績單：</font>
	<hr>
	<p><a href='chart.php?chart_kind=".$chart_kind."' target='_balnk'>下載全班的成績單</a></p>
        ":"
	<font color=red>包含學習描述文字：</font>
	<hr>
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar&stud_id=$stud_id&stu_num=$stu_class_num&class_id=$class_id&text_cond=yes'>下載".$stu[stud_name]."的成績單</a></p>
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar_all&class_id=$class_id&text_cond=yes'>下載全班的成績單</a></p>
	<hr>
	<font color=red >不含學習描述文字：</font>
	<hr>
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar&stud_id=$stud_id&stu_num=$stu_class_num&class_id=$class_id'>下載".$stu[stud_name]."的成績單</a></p>
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar_all&class_id=$class_id'>下載全班的成績單</a></p>";
	$main.="
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='col1'>
	<input type='checkbox' name='chknext' value='1' $checked>自動跳下一位
	</td><td bgcolor='#FFFFFF' valign='top'>
	<p align='center'>
	<font size=3>".$s[sch_cname]." ".$sel_year."學年度第".$sel_seme."學期成績單</p>
	<table align=center cellspacing=4>
	<tr>
	<td>班級：<font color='blue'>$class[5]</font></td><td width=40></td>
	<td>座號：<font color='green'>$stu_class_num</font></td><td width=40></td>
	<td>姓名：<font color='red'>$stu[stud_name]</font></td>
	</tr></table></font>
	$html
	<input type='hidden' name='stud_id' value='$stud_id'>
	<input type='hidden' name='student_sn' value='$student_sn'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='class_id' value='$class_id'>
	<input type='hidden' name='nav_next' ><br><div align='center'>
	<input type='submit' name='act' value='$submit' onClick='return checkok();'>
	</div>
	</form>
	</td></tr></table>
	";

	return $main;
}


//儲存值
function save_value(){
	global $CONN;
	//日常生活表現存檔
	$seme_year_seme = sprintf("%03d%d",$_POST[sel_year],$_POST[sel_seme]);
	for ($i=1;$i<=4;$i++){
		$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$_POST[stud_id]','生活表現評量','$i','".$_POST["a_$i"]."')";
		$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);		
	}
	//科目努力程度存檔
	//$temp_ss_id_arr = explode(",",$_POST[hidden_ss_id]);
//	while(list($id,$val)=each($temp_ss_id_arr)){
//		if ($val<>''){
//			$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val)values('$seme_year_seme','$_POST[stud_id]','努力程度','$val','".$_POST["ss_$val"]."')";
//			$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);
//		}
//	}
	//導師評語及建議及等第
	$query = "replace into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo) values('$seme_year_seme','$_POST[student_sn]',0,'$_POST[nor_score]','$_POST[nor_score_memo]')";
	$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);
/*	改至 填寫勤惰記錄 處紀錄
	//學生出缺席
	$abs_kind_arr = stud_abs_kind();
	while(list($id,$val)=each($abs_kind_arr)) {
		$query = "replace into stud_seme_abs(seme_year_seme,stud_id,abs_kind,abs_days) values('$seme_year_seme','$_POST[stud_id]','$id','".$_POST["abs_$id"]."')";
		$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);

	}
*/	
	//其他設定
	$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$_POST[stud_id]','其他設定',0,'$_POST[oth_rep]')";
	$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);
	return true;

}

//更新值
function update_value(){
	return;	
}

// 取得成績檔XML
function &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data,$text_cond) {
	global $CONN,$oth_arr_score,$oth_arr_score_2;
	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	
	$class=class_id_2_old($class_id);
	// 取得本年級的課程陣列
	$ss_name_arr = &get_ss_name_arr($class);
	// 取得努力程度文字敘述
//	$arr_1 = sfs_text("努力程度");
	// 取得課程每週時數
	$ss_num_arr = get_ss_num_arr($class_id);
	// 取得學習成就
	$ss_score_arr =get_ss_score_arr($class,$student_sn);

	$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and class_id='$class_id' and need_exam='1' order by scope_id,subject_id";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	if ($ss_recordSet->RecordCount() ==0){
		$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and need_exam='1' and class_id='' and class_year='$class[3]' order by scope_id,subject_id";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	}
	$hidden_ss_id='';
	$temp_9_arr = array();
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$link_ss=$SS[link_ss];
		$rate=$SS[rate];
		$temp_9_arr[$link_ss][ss_hours] += $ss_num_arr[$ss_id];
		$temp_9_arr[$link_ss][ss_score] += $ss_score_arr[$ss_id][ss_score]*$rate;
		$temp_9_arr[$link_ss][rate] += $rate;
		$oth_data_rate = 0;
		$temp_9_arr[$link_ss][oth_data] += $oth_arr_score[$oth_data["努力程度"]["$ss_id"]]*$rate;
		$temp_9_arr[$link_ss][ss_name][] = $ss_name_arr[$ss_id];
		$temp_9_arr[$link_ss][ss_score_memo][] = $ss_score_arr[$ss_id][ss_score_memo];
	}
	reset($temp_9_arr);
	while(list($id,$val)=each($temp_9_arr)){			
		$score_temp = $val[ss_score]/$val[rate];
		$score_oth = $oth_arr_score_2[round($val[oth_data]/$val[rate],0)];
		$score_temp_str ='';
		$score_memo = score2str($score_temp,$class);
		$ss_score_memo ='';
		 if (count($val[ss_score_memo])>1){
		 										reset($val[ss_score_memo]);
                        while(list($id2,$temp_mm) = each($val[ss_score_memo]))
				if ($temp_mm<>'')
                                	$ss_score_memo .= "[".$val[ss_name][$id2]."] $temp_mm ";
                        $ss_score_memo = substr($ss_score_memo,0,-1);
                }
                else
                        $ss_score_memo = $val[ss_score_memo][0];
		
		$ss_score_memo = $ttt->xml_reference_change($ss_score_memo);
		//是否印出文字描述
		if ($text_cond == 'yes'){
			//含文字描述
			$res_str.="<table:table-row table:style-name=\"ss_table.1\"><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$id</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$val[ss_hours]</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_oth."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_memo."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.E2\" table:value-type=\"string\"><text:p text:style-name=\"P11\">$ss_score_memo</text:p></table:table-cell></table:table-row>";
		}else{
			//不含文字描述		
			$res_str.="<table:table-row table:style-name=\"ss_table.1\"><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$id</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$val[ss_hours]</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_oth."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.D2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_memo."</text:p></table:table-cell></table:table-row>";
		}
	}
	return $res_str;
}

//下載成績單
function downlod_ar($text_cond="",$stud_id="",$class_id="",$interface_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height,$TOTAL_DAYS,$sign_1_form,$sign_2_form,$sign_1_name,$sign_2_name;
	
	//Openofiice的路徑
	if ($text_cond == 'yes')
		$oo_path = "ooo/e";
	else
		$oo_path = "ooo/e/notext";
	
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
	$memo_temp_str = &say_rule_2($class);
	
	//取得學校資料
	$s=get_school_base();
	
        $memo_temp_str = "<text:p text:style-name=\"P11\">本學期上課日數: $TOTAL_DAYS 天 </text:p>".$memo_temp_str;


	//換頁 tag
	$break ="<text:p text:style-name=\"break_page\"/>";
	if ($draw_img_width=='') $draw_img_width="1.27cm";
	if ($draw_img_height=='') $draw_img_height="1.27cm";
	//校長簽章檔
	if ($sign_1_form=="" || $sign_1_form==1) {
		if (is_file($UPLOAD_PATH."school/title_img/title_1")){
			$title_img = "http://".$_SERVER["SERVER_ADDR"]."/".$UPLOAD_URL."school/title_img/title_1";
			$sign_1 ="<draw:image draw:style-name=\"fr1\" draw:name=\"aaaa1\" text:anchor-type=\"paragraph\" svg:x=\"0.73cm\" svg:y=\"0.161cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"0\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
		}
	} elseif ($sign_1_form==2) {
		$sign_1=$sign_1_name;
	}
	//教務主任簽章檔
	if ($sign_2_form=="" || $sign_2_form==1) {
		if (is_file($UPLOAD_PATH."school/title_img/title_2")){
			$title_img = "http://".$_SERVER["SERVER_ADDR"]."/"."$UPLOAD_URL"."school/title_img/title_2";
			$sign_2 = "<draw:image draw:style-name=\"fr2\" draw:name=\"bbbb1\" text:anchor-type=\"paragraph\" svg:x=\"0.727cm\" svg:y=\"0.344cm\" svg:width=\"$draw_img_width\" svg:height=\"$draw_img_height\" draw:z-index=\"1\" xlink:href=\"$title_img\" xlink:type=\"simple\" xlink:show=\"embed\" xlink:actuate=\"onLoad\"/>";
		}
	} elseif ($sign_2_form==2) {
		$sign_2=$sign_2_name;
	}
	
	$arr_1 = sfs_text("日常行為表現");
        $arr_2 = sfs_text("團體活動表現");
        $arr_3 = sfs_text("公共服務表現");
        $arr_4 = sfs_text("校外特殊表現");
	//假別
	$abs_kind_arr = stud_abs_kind();
	
	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);

	//讀出 xml 檔案
	$data = $ttt->addDir("META-INF");

	$data = $ttt->addFile("settings.xml");
	$data = $ttt->addFile("styles.xml");
	$data = $ttt->addFile("meta.xml");

//班級資料〈若是單人，則秀單人資料〉
	$where=($mode=="all")?"where (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$year_seme' and b.seme_class='$class_num' order by b.seme_num ":"where a.stud_id='$stud_id' and b.seme_year_seme='$year_seme'";
	$query = "select a.stud_id,a.stud_name,a.student_sn,b.seme_num from stud_base a left join stud_seme b on a.stud_id=b.stud_id $where";
	$res = $CONN->Execute($query)or trigger_error($query, E_USER_ERROR);
	while (list($stud_id,$stud_name,$student_sn,$stu_num)=$res->FetchRow()) {
		//讀出 content.xml 
		$content_body = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_body.xml");

		$sign2_arr["SIGN_1"] = $sign_1;
		$sign2_arr["SIGN_2"] = $sign_2;
		$content_body = $ttt->change_temp($sign2_arr,$content_body,0);
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
			($abs_data[$id]==0)?$temp_i="0":$temp_i=$abs_data[$id];
			$temp_arr["$i"] = $temp_i;
			$i++;
		}

		reset($rep_kind_arr);

		//取得學生日常生活表現分數及導師評語建議
		$nor_data=get_nor_value($student_sn,$sel_year,$sel_seme);
		$temp_arr["6"] = $ttt->change_str($nor_data[ss_score_memo],1,0);
		$temp_arr["7"] = score2str($nor_data[ss_score],$class);
		//取得其他字串
		$temp_arr[22] = $oth_data['其他設定'][0];
	
		//取得學生成績檔
		$temp_arr_score["ss_table"] = &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data,$text_cond);
	
		$temp_arr["MEMO"] = $memo_temp_str;
		
		//換行
		if($mode=="all")	$content_body .= $break;
		$content_body = $ttt->change_temp($temp_arr_score,$content_body,0);
		// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
		$replace_data .= $ttt->change_temp($temp_arr,$content_body,0);
	
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
	
	//以串流方式送出 sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
	//因應 SSL 連線時，IE 6,7,8 會發生下載的問題
	header("Cache-Control: max-age=0");
	header("Pragma: public");
	header("Expires: 0");

	echo $ttt->file();
	exit;
	return;
}
?>
