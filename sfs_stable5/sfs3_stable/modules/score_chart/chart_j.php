<?php

// $Id: chart_j.php 7972 2014-04-01 08:22:02Z smallduh $

/* 取得設定檔 */
include "config.php";

sfs_check();

if (($IS_JHORES=='0')&&($use_both=='0')) header("location: chart_e.php");

//努力程度
$oth_arr_score = array("表現優異"=>5,"表現良好"=>4,"表現尚可"=>3,"需再加油"=>2,"有待改進"=>1);
$oth_arr_score_2 = array(5=>"表現優異",4=>"表現良好",3=>"表現尚可",2=>"需再加油",1=>"有待改進",0=>"--");

$year_seme=($_POST[year_seme])?$_POST[year_seme]:$_GET[year_seme];
$class_id=($_POST[class_id])?$_POST[class_id]: $_GET[class_id];
$student_sn=($_POST[student_sn])?$_POST[student_sn]:$_GET[student_sn];
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
	$sel_year=intval(substr($year_seme,0,-1));
	$sel_seme=substr($year_seme,-1,1);
}

//更改班級
if ($student_sn != "") {
	$sql="select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='$year_seme'";
	$rs=$CONN->Execute($sql);
	$stud_num=$rs->fields['seme_class'];
}

if ($class_id=="") {
	// 利用 $IS_JHORES 來 區隔 國中、國小、高中 的預設值
	if ($stud_num) $class_num=$stud_num;
	else {
		$year_name=$IS_JHORES+1;
		$sql="select seme_class,student_sn from stud_seme where seme_year_seme='$year_seme' order by seme_class, seme_num";
		$rs=$CONN->Execute($sql);
		while (!$rs->EOF) {
			$class_num=$rs->fields['seme_class'];
			if ($class_num) {
				$student_sn=$rs->fields['student_sn'];
				if ($student_sn) {
					$res=$CONN->Execute("select * from stud_base where student_sn='$student_sn'");
					if ($res->RecordCount()>0) break;
				}
			}
			$rs->MoveNext();
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
	if ($stud_num != $class_num) $student_sn="";
}

//確定有學生名單
if(!$class_num) die("<font color='red' size=4><BR>系統未找到本學期的班級學生名單，<BR>請<a href='../stud_year/'>按此連結至[編班作業]模組</a>進行處理!</font>");


//取得班級代號
$class_all=class_num_2_all($class_num);
$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);

//取得本學期上課總日數
$query = "select days from seme_course_date where seme_year_seme='$year_seme' and class_year=".substr($class_num,0,1);
$res= $CONN->Execute($query) or die($query);
$TOTAL_DAYS = $res->fields[0];

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];

//執行動作判斷
if($act=="dlar"){
	downlod_ar($student_sn,$class_id,$interface_sn,$stu_num,$sel_year,$sel_seme);
	exit;
}elseif($act=="dlar_all"){
	downlod_ar("",$class_id,$interface_sn,"",$sel_year,$sel_seme,"all");
	exit;
}

$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$student_sn);

//秀出網頁
head("製作成績單");
print_menu($school_menu_p);

?>

<script language="JavaScript">
<!-- Begin
function jumpMenu(){
	location="<?php echo $_SERVER['SCRIPT_NAME']?>?act=<?php echo $act;?>&stud_id=" + document.col1.stud_id.options[document.col1.stud_id.selectedIndex].value;
}
//  End -->
</script>

<?php

echo $main;
foot();

//觀看模板
function &main_form($interface_sn="",$sel_year="",$sel_seme="",$class_id="",$student_sn=""){
	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next,$edit_mode,$submit;

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

	//若仍是沒有$stud_id，則秀出錯誤訊息
	if(empty($student_sn))header("location:{$_SERVER['SCRIPT_NAME']}?error=1");
	
	if ($chknext && $nav_next<>'')	$student_sn = $nav_next;
	
	//求得學生ID
	$query="select stud_id from stud_base where student_sn='$student_sn'";
	$res=$CONN->Execute($query);
	$stud_id=$res->fields['stud_id'];

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

	$grid1 = new ado_grid_menu($_SERVER['SCRIPT_NAME'],$URI,$CONN);  //建立選單	   	
	$grid1->key_item = "student_sn";  // 索引欄名  	
	$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
	$grid1->bgcolor = $gridBgcolor;
	$grid1->display_color = array("1"=>"blue","2"=>"red");
	$grid1->color_index_item ="stud_sex" ; //顏色判斷值
	if ($stud_id_temp<>''){
		$stud_id_temp = ",stud_id in ($stud_id_temp) as tt ";
		$grid1->color_index_item ="tt" ; //顏色判斷值
	}
	$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
	$grid1->sql_str = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num as sit_num $stud_id_temp from stud_base a,stud_seme b where a.student_sn=b.student_sn and (a.stud_study_cond=0 or a.stud_study_cond=5) and b.seme_year_seme='$year_seme' and b.seme_class='$seme_class' order by b.seme_num";   //SQL 命令
	$grid1->do_query(); //執行命令 

	$stud_select = $grid1->get_grid_str($student_sn,$upstr,$downstr); // 顯示畫面

	//取得指定學生資料
	$stu=get_stud_base($student_sn,"");

	//座號
	$sql="select seme_num from stud_seme where seme_year_seme='$year_seme' and student_sn='$student_sn'";
	$rs=$CONN->Execute($sql);
	$stu_class_num=$rs->fields['seme_num'];

	//取得學校資料
	$s=get_school_base();

	if ($use_both) $tool_bar=&make_menu($school_menu_p);

	
	$checked=($chknext)?"checked":"";
    			

	$main="
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select
	<p><a href='{$_SERVER['SCRIPT_NAME']}?act=dlar&student_sn=$student_sn&stu_num=$stu_class_num&class_id=$class_id'>下載".$stu[stud_name]."的成績單</a></p>
	<p><a href='{$_SERVER['SCRIPT_NAME']}?act=dlar_all&class_id=$class_id'>下載全班的成績單</a></p>
	
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
	</td></tr></table>
	";

	return $main;
}

// 取得成績檔XML
function &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data) {
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

	$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and class_id='$class_id' and need_exam='1' order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	if ($ss_recordSet->RecordCount() ==0){
		$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and class_year='$class[3]' and need_exam='1' and class_id='' order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	}
	$temp_9_arr = array();
	while (!$ss_recordSet->EOF) {
		$link_ss=$ss_recordSet->fields['link_ss'];
		if ($link_ss!="彈性課程") $temp_9_arr[$link_ss][num]=$ss_recordSet->fields['cc'];
		$ss_recordSet->MoveNext();
	}
	$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and class_id='$class_id' and need_exam='1' order by sort,sub_sort";
	$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	if ($ss_recordSet->RecordCount() ==0){
		$ss_sql_select = "select ss_id,rate,link_ss from score_ss where enable='1' and  year='$class[0]' and semester='$class[1]' and class_year='$class[3]' and need_exam='1' and class_id='' order by sort,sub_sort";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
	}
	$hidden_ss_id='';
	while ($SS=$ss_recordSet->FetchRow()) {		
		$ss_id=$SS[ss_id];
		$link_ss=$SS[link_ss];
		$rate=$SS[rate];
		$ss_name= $ss_name_arr[$ss_id];
		$sub_link=0;
		if ($link_ss=="彈性課程" or $link_ss=='') {
			$link_ss="彈性課程-".$ss_name;
			$sub_link=1;
		}
		$temp_9_arr[$link_ss][ss_hours] += $ss_num_arr[$ss_id];
		$temp_9_arr[$link_ss][ss_score] += $ss_score_arr[$ss_id][ss_score]*$rate;
		$temp_9_arr[$link_ss][rate] += $rate;
		$oth_data_rate = 0;
		$temp_9_arr[$link_ss][oth_data] += $oth_arr_score[$oth_data["努力程度"]["$ss_id"]]*$rate;
		if ($ss_score_arr[$ss_id][ss_score_memo]<>'') {
			if ($sub_link==0 && $temp_9_arr[$link_ss][num]>1) $temp_9_arr[$link_ss][ss_score_memo] .= "$ss_name :";
			$temp_9_arr[$link_ss][ss_score_memo] .= $ss_score_arr[$ss_id][ss_score_memo]."<br/>";
		}
		//if ($temp_sel=='')
		//	$temp_sel = "--";
	}
	reset($temp_9_arr);
	while(list($id,$val)=each($temp_9_arr)){			
		if ($id=='') continue;
		$score_temp = $val[ss_score]/$val[rate];
		$score_oth = $oth_arr_score_2[round($val[oth_data]/$val[rate],0)];
		if ($score_temp>0)
			$score_temp_str = round($score_temp,0);
		else
			$score_temp_str ='';
		$score_memo = score2str($score_temp,$class);
		$ss_id=$SS[ss_id];
		$ss_name= $ss_name_arr[$ss_id];
		$memo_str=$ttt->xml_reference_change(substr($val[ss_score_memo],0,-5));
		if ($memo_str=="") $memo_str="--";
		$res_str.="<table:table-row table:style-name=\"ss_table.1\"><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$id</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">$val[ss_hours]</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_oth."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.A2\" table:value-type=\"string\"><text:p text:style-name=\"P5\">".$score_memo."</text:p></table:table-cell><table:table-cell table:style-name=\"ss_table.E2\" table:value-type=\"string\"><text:p text:style-name=\"P11\">".$memo_str."</text:p></table:table-cell></table:table-row>";
	}

	return $res_str;
}

//下載成績單
function downlod_ar($student_sn="",$class_id="",$interface_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height,$sign_1_form,$sign_2_form,$sign_1_name,$sign_2_name;
	
	//Openofiice的路徑
	$oo_path = "ooo/j";
	
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
	//獎懲
	$rep_kind_arr = stud_rep_kind();

	
	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);	
	$ttt->addDir("META-INF");
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");
	
	
	//班級資料〈若是單人，則秀單人資料〉
	$where=($mode=="all")?"where (a.stud_study_cond=0 or a.stud_study_cond=5) and  b.seme_year_seme='$year_seme' and b.seme_class='$class_num' order by b.seme_num ":"where a.student_sn='$student_sn' and b.seme_year_seme='$year_seme'";
	$query = "select a.stud_id,a.stud_name,a.student_sn,b.seme_num from stud_base a RIGHT join stud_seme b on a.student_sn=b.student_sn $where";
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
		$temp_arr["stu_name"] = $ttt->change_str($stud_name,1,0);
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
		$temp_arr["6"] = $ttt->change_str($nor_data[ss_score_memo],1,0);
		$temp_arr["7"] = score2str($nor_data[ss_score],$class);	

		//取得其他字串
		$temp_arr[22] = $oth_data['其他設定'][0];

		//取得學生成績檔
		$temp_arr_score["ss_table"] = &get_score_xml_value($stud_id,$student_sn,$class_id,$oth_data);
	
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
	$sss = & $ttt->file();

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
