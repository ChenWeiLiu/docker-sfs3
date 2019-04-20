<?php

// $Id: index_old.php 7727 2013-10-28 08:26:17Z smallduh $

/* 取得設定檔 */
include "config.php";
include "../../include/sfs_oo_zip.php";
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//取得任教班級代號
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);

if(empty($class_num)){
	$act="error";
	$error_title="無班級編號";
	$error_main="找不到您的班級編號，故您無法使用此功能。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的任教資料輸入系統中。
	</ol>";
}elseif($error==1){
	$act="error";
	$error_title="該班級無學生資料";
	$error_main="找不到您的班級學生，故您無法使用此功能。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的學生資料輸入系統中。
	<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php'>".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php</a>)</ol>";
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//取得考試樣板編號
$exam_setup=&get_all_setup("",$sel_year,$sel_seme,$class_all[year]);
$interface_sn=$exam_setup[interface_sn];


$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);

if ($chknext)	$ss_temp = "&chknext=$chknext&nav_next=$nav_next";



//執行動作判斷
if($act=="error"){
	$main=&error_tbl($error_title,$error_main);
}elseif($act=="存檔"){
	$sc_sn=save_value($C,$interface_sn,$sel_year,$sel_seme,$stud_id,$class_id);
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id$ss_temp");
}elseif($act=="儲存修改"){
	update_value($sc_sn,$C,$interface_sn,$sel_year,$sel_seme,$stud_id,$class_id);
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id$ss_temp");
}elseif($act=="dlar"){
	downlod_ar($stud_id,$class_id,$interface_sn,$stu_num,$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id");
}elseif($act=="dlar_all"){
	downlod_ar("",$class_id,$interface_sn,"",$sel_year,$sel_seme,"all");
	header("location: {$_SERVER['PHP_SELF']}?stud_id=$stud_id");
}else{
	$main=&main_form($interface_sn,$sel_year,$sel_seme,$class_id,$stud_id);
}


//秀出網頁
head("填寫成績單");

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
	global $CONN,$input_kind,$school_menu_p,$cq,$comm,$chknext,$nav_next;
	
	

	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	//假如沒有指定學生，取得第一位學生
	if(empty($stud_id))$stud_id=get_no1($class_id);

	//若仍是沒有$stud_id，則秀出錯誤訊息
	if(empty($stud_id))header("location:{$_SERVER['PHP_SELF']}?error=1");
	
	if ($chknext && $nav_next<>'')	$stud_id = $nav_next;
	
	//取得該學生一些和科目無關的值
	$data=get_value($stud_id,$sel_year,$sel_seme);


	if(!empty($data)){
		$val=explode("^^",$data[value]);
		reset($val);
		while(list($k,$v)=each($val)){
			$all_value=explode("：",$v);
			$sn=$all_value[0];
			$value[$sn]=$all_value[1];
		}
		$sc_sn=$data[sc_sn];
		$edit_mode="update";
		$submit="儲存修改";
	}else{
		$value="";
		$edit_mode="add";
		$submit="存檔";
	}

	//找出目前已設定過的學生
	$query = "select a.stud_id,b.stud_name from score_input_value a , stud_base b  where a.stud_id=b.stud_id and a.class_id='$class_id' order by b.curr_class_num";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤",E_USER_ERROR);
	if ($res->RecordCount()>0) {
		while (!$res->EOF){
			$memo_str .= $res->fields[1].",";
			$stud_id_temp .=$res->fields[0].",";
			$res->MoveNext();
		}
		$stud_id_temp = substr($stud_id_temp,0,-1);
		$upstr = "已完成 <font color=red>".$res->RecordCount()."</font> 份成績單<br><br>";
	}
	//學生選單
	//$stud_select=get_stud_select($class_id,$stud_id,"stud_id","jumpMenu");
	$gridBgcolor="#DDDDDC";
	//已製作顯示顏色
	$over_color = "#223322";
	//左選單女生顯示顏色
	$non_color = "blue";
	
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
	$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num $stud_id_temp from stud_base where curr_class_num like '$class[2]%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令
	$grid1->do_query(); //執行命令 
//	echo $grid1->sql_str;exit;

	//求得學生ID	
	$student_sn=stud_id2student_sn($stud_id);

	$stud_select = $grid1->get_grid_str($stud_id,$upstr,$downstr); // 顯示畫面



	//取得指定學生資料
	$stu=get_stud_base("",$stud_id);

	//座號
	$stu_class_num=curr_class_num2_data($stu['curr_class_num']);
	
	//取得學校資料
	$s=get_school_base();

	$tool_bar=&make_menu($school_menu_p);

	
	//取得模板
	$C=&get_sc($interface_sn);

	$html=&html2code($interface_sn,$C[html],$C[sshtml],$class_id,false,$stud_id,$value,$student_sn);
	
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
	function checkok() {
	document.col1.nav_next.value = document.gridform.nav_next.value;	
	return true;	
	}
	
	</script>
	$tool_bar
	<table bgcolor='#DFDFDF' cellspacing=1 cellpadding=4>
	<tr class='small'><td valign='top'>$stud_select
	<p><a href='{$_SERVER['PHP_SELF']}?act=dlar&stud_id=$stud_id&stu_num=$stu_class_num[num]'>下載".$stu[stud_name]."的成績單</a></p>
	
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='col1'>
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
	<input type='hidden' name='sc_sn' value='$sc_sn'>
	<input type='hidden' name='stud_id' value='$stud_id'>
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
function save_value($C,$interface_sn,$sel_year,$sel_seme,$stud_id,$class_id){
	global $CONN;
	reset($C);
	while(list($k,$v)=each($C)){
		$array[]=$k."：".$v;
	}
	$value = implode("^^", $array);
	$sql_insert = "insert into score_input_value (interface_sn,date,stud_id,class_id,value,sel_year,sel_seme) values ($interface_sn,now(),'$stud_id','$class_id','$value',$sel_year,'$sel_seme')";
	if($CONN->Execute($sql_insert))	return mysql_insert_id();
	die($sql_insert);
	return  false;
}

//更新值
function update_value($sc_sn,$C,$interface_sn,$sel_year,$sel_seme,$stud_id,$class_id){
	global $CONN;
	if(empty($sc_sn))return;
	reset($C);
	while(list($k,$v)=each($C)){
		$array[]=$k."：".$v;
	}
	$value = implode("^^", $array);
	$sql_update= "update score_input_value set interface_sn=$interface_sn,date=now(),value='$value',class_id='$class_id' where sc_sn=$sc_sn";
	$CONN->Execute($sql_update) or die($sql_update);
	return  true;
}

//取得該生該學期的值〈注意：這並非成績，而是如評語之類的東西。〉
function get_value($stud_id,$sel_year,$sel_seme){
	global $CONN;
	$sql_select = "select * from score_input_value where stud_id='$stud_id' and sel_year='$sel_year' and sel_seme='$sel_seme'";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	$data=$recordSet->FetchRow();
	return $data;
}

//下載成績單
function downlod_ar($stud_id="",$class_id="",$interface_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height;
	
	//Openofiice的路徑
	$oo_path = "ooo/".$interface_sn;
	
	//檔名種類
	if($mode=="all"){
		$filename="score_".$class_id.".sxw";
	}else{
		$filename="score_".$class_id."_".$stu_num.".sxw";
	}
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_num=$class[2];

	//取得備註說明
	$memo_temp_str = &say_rule_2($class);
	
	//取得學校資料
	$s=get_school_base();
	
	//取得該樣板資料
	$SC=&get_sc($interface_sn);
	
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
	$where=($mode=="all")?"where curr_class_num like '$class_num%' order by curr_class_num":"where stud_id='$stud_id'";
	
	$query = "select stud_id,stud_name,student_sn,right(curr_class_num,2)  from stud_base $where";
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
		
		
		//取得該生該學期的成績值
		$value=array();
		$data=get_value($stud_id,$sel_year,$sel_seme);
		if(!empty($data)){
			$val=explode("^^",$data[value]);
			reset($val);
			while(list($k,$v)=each($val)){
				$all_value=explode("：",$v);
				$sn=$all_value[0];
				$value[$sn]=$all_value[1];
			}
		}
		
		//把和科目無關的欄位值填入
		reset($value);
		while(list($sn,$val)=each($value)){
			$temp_arr[$sn] = $val;
		}
		
		//取得科目資料
		$ss_xml_col="";
		$name_long=($SC[all_ss]=="y")?"長":"短";
		$group=($SC[all_ss]=="y")?"":"group by scope_id";
		$ss_sql_select = "select  * from score_ss where enable='1' and  year='$sel_year' and semester='$sel_seme' and class_year='$class[3]'  and  need_exam='1' $group order by scope_id,subject_id";
		$ss_recordSet=$CONN->Execute($ss_sql_select);
		while ($SS=$ss_recordSet->FetchRow()) {
			$ss_id=$SS[ss_id];
			$ss_name=&get_ss_name("","",$name_long,$ss_id);
			
			//和科目相關的欄位值填入
			$xml_col=$SC[xml];
			$xml_col=str_replace("{ss_sn}",$ss_id,$xml_col);
			$ss_xml_col.=str_replace("{ss_name}",$ss_name,$xml_col);
			
			//先取得該成績單和成績有關的欄位欄位資料
			$sql_select = "select *  from score_input_col where interface_sn=$interface_sn and col_ss='y'";
			$recordSet=$CONN->Execute($sql_select) or die($sql_select);
			
			while($C=$recordSet->FetchRow()){
				//若是和科目有關的欄位，在其name加上{ss_id}以便之後替換。
				$col_name="C[".$C[col_sn]."_".$ss_id."]";
				$sn=$C[col_sn]."_".$ss_id;
				$id="C".$sn;
				
				//如果由函數取值，暫不換成HTML
				if(empty($C[col_fn])){
					$ss_v=$value[$sn];
				}else{
					$ss_v=call_user_func_array($C[col_fn], array($class_id,$stud_id,$student_sn,$ss_id));
				}
				$ss_xml_col=str_replace("{".$sn."}",$ss_v,$ss_xml_col);
			}
		}
	
		$temp_arr["SIGN_1"] = $sign_1;
		$temp_arr["SIGN_2"] = $sign_2;
		$temp_arr["MEMO"] = $memo_temp_str;
		$temp_arr["ss_table"] = $ss_xml_col;
		
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
	//因應 SSL 連線時，IE 6,7,8 會發生下載的問題
	header("Cache-Control: max-age=0");
	header("Pragma: public");

	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}

?>
