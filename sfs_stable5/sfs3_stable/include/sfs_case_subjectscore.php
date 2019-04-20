<?php

// $Id: sfs_case_subjectscore.php 8596 2015-11-19 02:21:51Z qfon $
// 取代 subject_score.php


//取得科目名稱
function &get_subject_name($subject_id){
	global $CONN;
	if(empty($subject_id))	return "";

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select subject_name,enable from score_subject where subject_id=$subject_id";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	while (!$recordSet->EOF) {
		$subject_name= $recordSet->fields["subject_name"];
		$subject_enable= $recordSet->fields["enable"];
		$recordSet->MoveNext();
	}

	$name=($subject_enable=='1')?$subject_name:"<font color='red'>$subject_name</font>";

	return  $name;
}

//取得科目名稱陣列
function &get_subject_name_arr(){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select subject_id,subject_name,enable from score_subject ";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	// init $tmp_arr
	$temp_arr=array();

	while (!$recordSet->EOF) {
		$temp_arr[$recordSet->fields[subject_id]][subject_name] = $recordSet->fields[subject_name];
		$temp_arr[$recordSet->fields[subject_id]][enable] = $recordSet->fields[enable];
		$recordSet->MoveNext();
	}

//	$name=($subject_enable=='1')?$subject_name:"<font color='red'>$subject_name</font>";

	return  $temp_arr;
}


//找出科目編號，由$subject_name找出$subject_id
function get_subject_id($subject_name,$enable='1'){
	global $CONN;

	if (!$subject_name)  user_error("沒有傳入科目名稱！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$where_enable=($enable)?"and enable='1'":"";
	$sql_select = "select subject_id from score_subject where subject_name='$subject_name' $where_enable";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	while (!$recordSet->EOF) {
		$subject_id= $recordSet->fields["subject_id"];
		$recordSet->MoveNext();
	}

	return  $subject_id;
}

//取得年度課程名稱
function &get_ss_name($scope_id="",$subject_id="",$mode="長",$ss_id=""){
	if(!empty($ss_id)){
		$ss=get_one_ss($ss_id);
		$scope_id=$ss[scope_id];
		$subject_id=$ss[subject_id];
	}

	//取得領域名稱
	$scope_name=&get_subject_name($scope_id);

	//取得學科名稱
	$subject_name=(!empty($subject_id))?get_subject_name($subject_id):"";

	if($mode=="長"){
		$show_ss=(empty($subject_name))?$scope_name:$scope_name."-".$subject_name;
	}else{
		$show_ss=(empty($subject_name))?$scope_name:$subject_name;
	}
	
	$show_ss=($ss[enable]=='0')?"<font color='red'>$show_ss</font>":$show_ss;
	return $show_ss;
}

//取得年度課程名稱陣列
function &get_ss_name_arr($class,$mode="長"){
        global $CONN;
	//取得領域名稱
	$class_id=sprintf("%03d_%d_%02d_%02d",$class[0],$class[1],$class[3],$class[4]);
        $subject_name_arr=&get_subject_name_arr();
        $query = "select * from score_ss where class_id='".sprintf("%03s_%s_%02s_%02s",$class[0],$class[1],$class[3],$class[4])."' and enable='1' order by sort,sub_sort";
        $res = $CONN->Execute($query);
	if ($res->RecordCount() ==0){
		$query = "select * from score_ss where year='$class[0]' and semester='$class[1]' and class_year='$class[3]' and class_id='' and enable='1' order by sort,sub_sort";
		$res = $CONN->Execute($query);
	}
	while (!$res->EOF) {
		$ss_id=$res->fields[ss_id];
		$scope_id=$res->fields[scope_id];
                $subject_id=$res->fields[subject_id];

		//取得領域名稱
		$scope_name=$subject_name_arr[$scope_id][subject_name];
		//取得學科名稱
		$subject_name=(!empty($subject_id))?$subject_name_arr[$subject_id][subject_name]:"";

		if($mode=="長"){
                	$show_ss=(empty($subject_name))?$scope_name:$scope_name."-".$subject_name;
	        }else{
                $show_ss=(empty($subject_name))?$scope_name:$subject_name;
        	}
		$res_arr[$ss_id] = $show_ss;
		$res->MoveNext();
	}
        return $res_arr;
}



//取得科目節數陣列
function get_ss_num_arr($class_id){
        global $CONN;
                                                                                                                            
        $sql_select = "select ss_id ,count(*) as cc from score_course where  class_id='$class_id' and  day!='' and sector!=0 group by ss_id ";
        $recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while(!$recordSet->EOF){
		$res_arr[$recordSet->fields[ss_id]] = $recordSet->fields[cc];
		$recordSet->MoveNext();
	}
        return $res_arr;
}


//取得科目節數陣列(以課程設定的節數為主)
function get_ss_num_arr_from_score_ss($class_id){
        global $CONN;
		
		$aa=explode("_",$class_id);
		$aa[0]=intval($aa[0]);
		$aa[2]=intval($aa[2]);
                                                                                                                            
        $sql_select = "select ss_id ,sections as cc from score_ss where  year='$aa[0]' and semester='$aa[1]' and class_year='$aa[2]'";
        $recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while(!$recordSet->EOF){
		$res_arr[$recordSet->fields[ss_id]] = $recordSet->fields[cc];
		$recordSet->MoveNext();
	}
        return $res_arr;
}


//取得科目節數陣列(以課程設定的加權做為節數)
function get_ss_num_arr_from_score_ss_rate($class_id){
        global $CONN;
		
		$aa=explode("_",$class_id);
		$aa[0]=intval($aa[0]);
		$aa[2]=intval($aa[2]);
                                                                                                                            
        $sql_select = "select ss_id ,rate as cc from score_ss where  year='$aa[0]' and semester='$aa[1]' and class_year='$aa[2]'";
        $recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while(!$recordSet->EOF){
		$res_arr[$recordSet->fields[ss_id]] = $recordSet->fields[cc];
		$recordSet->MoveNext();
	}
        return $res_arr;
}

//算出某位學生的全學期成績及文字描述，並以甲乙丙丁為結果,傳回所有科目陣列
function get_ss_score_arr($class,$student_sn=""){
    global $CONN;
    $year=$class[0];
    $seme=$class[1];
	$seme_year_seme = sprintf("%03d%d",$year,$seme);
	$query = "select ss_id,ss_score,ss_score_memo from stud_seme_score where seme_year_seme = '$seme_year_seme'  and student_sn='$student_sn'";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$ss_id = $res->fields[ss_id];
		$ss_score = $res->fields[ss_score];
		$ss_score_memo = $res->fields[ss_score_memo];
        $score_name=score2str($ss_score,$class);
		$res_arr[$ss_id]['ss_score'] = $ss_score;
		$res_arr[$ss_id]['score_name'] = $score_name;
		$res_arr[$ss_id]['ss_score_memo'] = $ss_score_memo;
		$res->MoveNext();
	}
    return $res_arr;
}



//取得科目的下拉選單
function &select_subject($id,$enable='1',$subject_kind="",$col_name="subject_id"){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select subject_id,subject_name from score_subject where enable='$enable' and subject_kind='$subject_kind'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$option="<option value='0'>科目選單</option>";
	while (!$recordSet->EOF) {
		$subject_id= $recordSet->fields["subject_id"];
		$subject_name = $recordSet->fields["subject_name"];
		$selected=($id==$subject_id)?"selected":"";
		$option.="<option value='$subject_id' $selected>$subject_name</option>\n";
		$recordSet->MoveNext();
	}

	$select_subject="
	<select name='$col_name'>
	$option</select>
	";
	return $select_subject;
}


//取得今年課程的下拉選單
function &select_ss($col_name="ss_id",$id,$enable='1',$sel_year,$sel_seme,$Cyear){
	global $CONN;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$where_enable=($enable)?"and enable='1'":"";
	$sql_select = "select ss_id,scope_id,subject_id,enable from score_ss where class_year='$Cyear' and year='$sel_year' and semester='$sel_seme' $where_enable";
	//die($sql_select);
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$option="<option value='0'></option>";
	while (!$recordSet->EOF) {
		$ss_id= $recordSet->fields["ss_id"];
		$scope_id = $recordSet->fields["scope_id"];
		$subject_id = $recordSet->fields["subject_id"];
		$enable= $recordSet->fields["enable"];

		$color=($enable)?"black":"red";
		$subject_name=&get_ss_name($scope_id,$subject_id,"短");

		$selected=($id==$ss_id)?"selected":"";
		$option.="<option value='$ss_id' $selected style='color:$color'>$subject_name</option>\n";
		$recordSet->MoveNext();
	}

	$select_ss="
	<select name='$col_name'>
	$option
	</select>";
	return $select_ss;
}


//取出該學年、該學期的不隱藏（有效的）學科
function &get_all_ss($sel_year,$sel_seme,$Cyear="",$class_id=""){
	global $CONN;
	if(!empty($class_id)){
		$filter="and class_id='$class_id'";
	}elseif(!is_null($Cyear)){
		$filter="and class_year='$Cyear' and class_id=''";
	}else{
		user_error("沒有年級或班級代碼，無法取得課程。", 256);
	}

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select * from score_ss where enable='1' and year='$sel_year' and semester='$sel_seme' $filter order by sort,sub_sort";
	$recordSet=$CONN->Execute($sql_select) or user_error("SQL語法執行錯誤： $sql_select", 256);
	$i=0;

	// init $id
	$id=array();

	while($array = $recordSet->FetchRow()){
		$id[$i]=$array;
		$i++;
	}
		
	return $id;
}

//列出課程設定中有設定年級與班級
function get_ss_yc($sel_year,$sel_seme){
	global $CONN;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select class_id,class_year from score_ss where year=$sel_year and semester='$sel_seme' and enable='1' group by class_year,class_id";

	$recordSet=$CONN->Execute($sql_select) or user_error("SQL語法執行錯誤： $sql_select", 256);
	$i=0;
	while(list($class_id,$class_year)= $recordSet->FetchRow()){
		$id[$i][Cyear]=$class_year;
		$id[$i][class_id]=$class_id;
		$i++;
	}
		
	return $id;
}




//取出某學科（ss）的資料
function &get_one_ss($ss_id){
	global $CONN;

	if (!$ss_id)  user_error("沒有傳入學科！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// init $array
	$array=array();

	$sql_select = "select * from score_ss where ss_id='$ss_id' ";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$array = $recordSet->FetchRow();
	return $array;
}


//取得課程表中所有年度
function &get_ss_year($sel_year,$sel_seme,$other_link=""){
	global $CONN;

	if (!$sel_year)  user_error("沒有傳入sel_year！請檢查！",256);
	if (!$sel_seme)  user_error("沒有傳入sel_seme！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$other_link=(empty($other_link))?"":"&$other_link";

	//找出該表中所有的年度與學期，要拿來作選單
	$sql_select = "select year,semester from score_ss where enable='1' order by year,semester";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$other_ss=array();
	while (!$recordSet->EOF) {
		$year = $recordSet->fields["year"];
		$semester = $recordSet->fields["semester"];
		$bgcolor=($year==$sel_year and $semester==$sel_seme)?"#FBEC8C":"white";
		$semester_name=($semester=='2')?"下":"上";
		$other_ss_name="<a href='{$_SERVER['SCRIPT_NAME']}?sel_year=$year&sel_seme=$semester".$other_link."'>&nbsp;".$year."學年".$semester_name."學期</a><br>";

		//製作其他學年學期的選單
		if(!in_array($other_ss_name,$other_ss)){
			$other_ss[$i]=$other_ss_name;
			$other_ss_text.="<tr bgcolor='$bgcolor'><td class='tab_all' nowrap>$other_ss_name</td></tr>";
			$i++;
		}

		$recordSet->MoveNext();
	}
	return "<table>".$other_ss_text."</table>";
}


//取得考試設定中所有年度
function &get_exam_year($sel_year,$sel_seme,$act,$other=""){
	global $CONN;

	if (!$sel_year)  user_error("沒有傳入sel_year！請檢查！",256);
	if (!$sel_seme)  user_error("沒有傳入sel_seme！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//額外的連結字串
	$other_link=(!empty($other))?"&".$other:"";
	
	//找出該表中所有的年度與學期，要拿來作選單
	$sql_select = "select year,semester from score_setup where enable='1' order by year,semester";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$other_ss=array();
	while (!$recordSet->EOF) {
		$year = $recordSet->fields["year"];
		$semester = $recordSet->fields["semester"];
		$bgcolor=($year==$sel_year and $semester==$sel_seme)?"#FBEC8C":"white";
		$semester_name=($semester=='2')?"下":"上";
		$other_ss_name="<a href='{$_SERVER['SCRIPT_NAME']}?act=$act&sel_year=$year&sel_seme=$semester".$other_link."'>&nbsp;".$year."學年".$semester_name."學期</a><br>";

		//製作其他學年學期的選單
		if(!in_array($other_ss_name,$other_ss)){
			$other_ss[$i]=$other_ss_name;
			$other_ss_text.="<tr bgcolor='$bgcolor'><td class='tab_all' nowrap>$other_ss_name</td></tr>";
			$i++;
		}

		$recordSet->MoveNext();
	}
	return "<table>".$other_ss_text."</table>";
}


//取出本學年本學期的學校成績共通設定
function ratio($year,$seme,$stage="1"){
	global $CONN;

	if (!$year)  user_error("沒有傳入year！請檢查！",256);
	if (!$seme)  user_error("沒有傳入seme！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql="select * from score_setup where year='$year' and semester='$seme'";
	$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$score_mode= $rs->fields['score_mode'];
	$test_ratio= $rs->fields['test_ratio'];
	if($score_mode=="all"){
		$test_ratio=explode("-",$test_ratio);
		$test_ratio_all_1=$test_ratio_all_2=$test_ratio_all_3=$test_ratio;
	}
	elseif($score_mode=="severally"){
		$test_ratio_all=explode(",",$test_ratio);
		$test_ratio_all_1=explode("-",$test_ratio_all[0]);
		$test_ratio_all_2=explode("-",$test_ratio_all[1]);
		$test_ratio_all_3=explode("-",$test_ratio_all[2]);
		if($stage=="1") {$test_ratio=$test_ratio_all_1;}
		elseif($stage=="2"){$test_ratio=$test_ratio_all_2;}
		else{$test_ratio=$test_ratio_all_3;}
	}
	else{
		$test_ratio[0]=60;
		$test_ratio[1]=40;
		$test_ratio_all_1=$test_ratio_all_2=$test_ratio_all_3=$test_ratio;
	}
	$test_ratio[3]=$score_mode;
	return $test_ratio;
}


//由班級序號class_id查出年級[year]，班級[sort]，班名[name]
function get_class_all($class_id=""){
	global $CONN,$school_kind_name;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// init $the_class
	$the_class=array();

	$sql_select = "select c_year,c_name,c_sort from school_class where class_id='$class_id'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	list($the_class[year],$name,$the_class[sort]) = $recordSet->FetchRow();
	$y=$the_class[year];
	$the_class[name]=$school_kind_name[$y]."".$name."班";
	return $the_class;
}


//取得一個模板的設定值
function &get_sc($interface_sn=""){
	global $CONN,$input_kind;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	// init $C
	$C=array();

	if(empty($interface_sn)) return false;
	$sql_select = "select * from score_input_interface where interface_sn='$interface_sn'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	$C=$recordSet->FetchRow();
	return $C;
}


//所有現存樣板
function &get_sc_list($mode="",$interface_sn="",$name="interface_sn"){
	global $CONN,$input_kind;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = "select interface_sn,title,text,html from score_input_interface";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);

	while($C=$recordSet->FetchRow()){
		if($mode=="text"){
			$data.="<li><a href='{$_SERVER['SCRIPT_NAME']}?interface_sn=$C[interface_sn]'>".$C[title]."</a>： $C[text]</li>";
		}elseif($mode=="option"){
			$selected=($interface_sn==$C[interface_sn])?"selected":"";
			$data.="<option value='$C[interface_sn]' $selected>$C[title]</option>\n";
		}else{
			$data.="<a href='{$_SERVER['SCRIPT_NAME']}?interface_sn=$C[interface_sn]'>".$C[title]."</a><br>";
		}
	}
	
	if($mode=="option"){
	$data="<select name='$name'>$data</select>";
	}

	return $data;
}




//給種類，做語法（mode=html秀<html>，mode=0秀&lt;html&gt;）
function &get_col_html($col_name="col",$type="",$value="",$comment="",$mode="html",$have_value="",$id=""){
	global $SFS_PATH_HTML,$cq,$comm;
	$html_mark=($mode=="html")?"<":"&lt;";
	$html_mark2=($mode=="html")?">":"&gt;";	
	if($comment=="y" && $mode=="html"){
		$button="<img src='".$SFS_PATH_HTML."images/comment.png' width=16 height=16 border=0 align='left' name='".$id."s' value='".$id."s' onClick=\"return OpenWindow('$id')\">";
	}else{
		$button="";
	}

	$v=(is_null($have_value))?$value:$have_value;

	if($type=="text"){
		if($cq=='$col_name')$v=$comm;
		$form=$button.$html_mark."input type='text' name='$col_name' id='$id' value='$v' style='width:100%'".$html_mark2;
	}elseif($type=="password"){
		$form=$html_mark."input type='password' name='$col_name' value='$v'".$html_mark2;
	}elseif($type=="select"){
		$curr_v=explode(",",$value);
		for($i=0;$i<sizeof($curr_v);$i++){
			$select=($curr_v[$i]==$v)?"selected":"";
			$option.=$html_mark."option value='$curr_v[$i]' $select".$html_mark2.$curr_v[$i].$html_mark."/option".$html_mark2."<br>\n";
		}
		$form=$html_mark."select name='$col_name' ".$html_mark2."<br>".$option.$html_mark."/select".$html_mark2;
	}elseif($type=="textarea"){
	  	if($cq=='$col_name')$v=$comm;
		$form=$button.$html_mark."textarea name='$col_name' id='$id' style='width: 100%;height: 100%'".$html_mark2.$v.$html_mark."/textarea".$html_mark2;
	}elseif($type=="checkbox"){
		$curr_v=explode(",",$value);
		for($i=0;$i<sizeof($curr_v);$i++){
			$checked=($curr_v[$i]==$v)?"checked":"";
			$form.=$html_mark."input type='checkbox' name='$col_name' value='$curr_v[$i]' $checked".$html_mark2.$v[$i]."<br>\n";
		}
	}elseif($type=="radio"){
		$curr_v=explode(",",$value);
		for($i=0;$i<sizeof($curr_v);$i++){
			$checked=($curr_v[$i]==$v)?"checked":"";
			$form.=$html_mark."input type='radio' name='$col_name'value='$curr_v[$i]' $checked".$html_mark2.$v[$i]."<br>\n";
		}
	}
	return $form;
}


//把樣板換成HTML碼
function &html2code($interface_sn="",$all_html="",$all_sshtml="",$class_id="",$design_mode=false,$stud_id="",$val="",$student_sn=""){
	global $CONN,$input_kind;
	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	//取得班級相關資料
	$classdata=class_id_2_old($class_id);
	
	//取得該樣板資料
	$SC=&get_sc($interface_sn);

	//先取得該成績單和成績無關的欄位欄位資料
	$sql_select = "select *  from score_input_col where interface_sn='$interface_sn' and col_ss='n'";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while($C=$recordSet->FetchRow()){
		//欄位名稱。
		$col_name="C[".$C[col_sn]."]";
		$sn=$C[col_sn];
		$id="C".$sn;
		//如果由函數取值，暫不換成HTML
		if(empty($C[col_fn])){
			$html=&get_col_html($col_name,$C[col_type],$C[col_value],$C[col_comment],"html",$val[$sn],$id);
		}else{
			$fn_v=($design_mode)?$C[col_fn]:"";

			$html=($design_mode)?"由".$C[col_fn]."取值":"{函數".$sn."}";
			$all_fn[$sn]=$C[col_fn];
		}
		//如果HTML中有那一欄，替換掉{}
		$all_html=str_replace("{".$C[col_sn]."_輸入欄}", $html, $all_html);
		//$all_sshtml=str_replace("{".$C[col_sn]."_輸入欄}", $html, $all_sshtml);

 	}

	//若非設計模式下，則讀出所有科目
	if(!$design_mode){

		//先取得該成績單和成績有關的欄位欄位資料
		$sql_select = "select *  from score_input_col where interface_sn='$interface_sn' and col_ss='y'";
		$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
			$name_long=($SC[all_ss]=="y")?"長":"短";
		$group=($SC[all_ss]=="y")?"":"group by scope_id";
		$ss_sql_select = "select  * from score_ss where enable='1' and  year='$classdata[0]' and semester='$classdata[1]' and class_year='$classdata[3]'  and  need_exam='1' $group order by scope_id,subject_id";
		$ss_recordSet=$CONN->Execute($ss_sql_select) or user_error("讀取失敗！<br>$ss_sql_select",256);
		while ($SS=$ss_recordSet->FetchRow()) {
			$ss_id=$SS[ss_id];
			$ss_name=&get_ss_name("","",$name_long,$ss_id);
			$sshtml_tmp=str_replace("{科目名稱}",$ss_name,$all_sshtml_tmp);
			@reset($all_fn);
			while(list($col_sn,$fn_name)=@each($all_fn)){
				$v=call_user_func_array($fn_name, array($class_id,$stud_id,$student_sn,$ss_id));
				$sshtml_tmp=str_replace("{函數".$col_sn."}",$v,$sshtml_tmp);
			}
	$all_sshtml_tmp=$all_sshtml;
		while($C=$recordSet->FetchRow()){
			//若是和科目有關的欄位，在其name加上{ss_id}以便之後替換。
			$col_name="C[".$C[col_sn]."_".$ss_id."]";
			$sn=$C[col_sn]."_".$ss_id;
			$id="C".$sn;

			//如果由函數取值，暫不換成HTML
			if(empty($C[col_fn])){
				$html=&get_col_html($col_name,$C[col_type],$C[col_value],$C[col_comment],"html",$val[$sn],$id);
			}else{
				$fn_v=($design_mode)?$C[col_fn]:"";
				$html=($design_mode)?"由".$C[col_fn]."取值":"{函數".$sn."}";
				$all_fn[$sn]=$C[col_fn];
			}
			//如果HTML中有那一欄，替換掉{}
			$all_sshtml_tmp=str_replace("{".$C[col_sn]."_輸入欄}", $html, $all_sshtml_tmp);

		}


			$sshtml.=$sshtml_tmp;

		}


	}else{
		$sshtml=$all_sshtml;
	}
	
	//把科目相關資料加到成績單中
	$all_html=str_replace("<!--此處會自動加入下方『和科目相關欄位』的設定-->",$sshtml,$all_html);

	return $all_html;
}

//找出今年度每日課堂最多的節數
function get_most_class($sel_year="",$sel_seme=""){
	global $CONN;

	$sql_select = "select max(sections) from score_setup where year = '$sel_year' and semester='$sel_seme' ";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("SQL語法錯誤： $sql_select", E_USER_ERROR);
	list($main) = $recordSet->FetchRow();
	return $main;
}

//由ss_id找出科目名稱的函數
function  ss_id_to_subject_name($ss_id){
    global $CONN;

	if (!$ss_id)  user_error("沒有傳入學科！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1) or user_error("讀取失敗！<br>$sql1",256);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
    return $subject_name;
}

//由ss_id找出學年學期的函數
function  ss_id_to_year_seme($ss_id){
    global $CONN;

	if (!$ss_id)  user_error("沒有傳入學科！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    $sql="select year,semester,class_year from score_ss where ss_id='$ss_id'";
    $rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
    $year = $rs->fields["year"];
	$semester = $rs->fields["semester"];
	$class_year = $rs->fields["class_year"];
	$uord=($semester=="1")?"上":"下";
	$cname=$year."學年度第".$semester."學期（".$class_year.$uord."）";
    return $cname;
}

//由ss_id找出class_year
function  ss_id_to_class_year($ss_id){
    global $CONN;
	if (!$ss_id)  user_error("沒有傳入學科！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
    $sql="select class_year from score_ss where ss_id='$ss_id'";
    $rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$class_year = $rs->fields["class_year"];
    return $class_year;
}

//取得取得某一科目的分組課程名稱陣列
function &get_group_name_arr($ss_id){
	global $CONN;
	if (!$ss_id)  user_error("沒有傳入科目代號！請檢查！",256);
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	$sql="select * from elective_tea where ss_id='$ss_id' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$i=0;
	while(!$rs->EOF){
		$group_id[$i]=$rs->fields['group_id'];
		$group_name[$i]=$rs->fields['group_name'];
		$teacher_sn[$i]=$rs->fields['teacher_sn'];
		$res_arr[$group_id[$i]]=array($group_name[$i],$teacher_sn[$i]);
		$i++;
		$rs->MoveNext();
	}
	return $res_arr;
}

//由ss_id傳回年級科目中文名稱
function ss_id_to_class_subject_name($ss_id){
    global $CONN;

	if (!$ss_id)  user_error("沒有傳入學科！請檢查！",256);

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

    $sql="select class_year from score_ss where ss_id='$ss_id'";
    $rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$class_year = $rs->fields["class_year"];

    $sql1="select subject_id from score_ss where ss_id=$ss_id";
    $rs1=$CONN->Execute($sql1) or user_error("讀取失敗！<br>$sql1",256);
    $subject_id = $rs1->fields["subject_id"];
    if($subject_id!=0){
        $sql2="select subject_name from score_subject where subject_id=$subject_id";
        $rs2=$CONN->Execute($sql2);
        $subject_name = $rs2->fields["subject_name"];
    }
    else{
        $sql3="select scope_id from score_ss where ss_id=$ss_id";
        $rs3=$CONN->Execute($sql3);
        $scope_id = $rs3->fields["scope_id"];
        $sql4="select subject_name from score_subject where subject_id=$scope_id";
        $rs4=$CONN->Execute($sql4);
        $subject_name = $rs4->fields["subject_name"];
    }
	$class_subject_name=$class_year."年級".$subject_name;
	return $class_subject_name;
}

//取出各節上課時間陣列
function section_table($sel_year,$sel_seme){
	global $CONN;

	$query="CREATE TABLE if not exists section_time (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		sector tinyint(2) unsigned default '0',
		stime varchar(11) NOT NULL default '00:00-00:01',
		PRIMARY KEY (year,semester,sector)) ;";
	$CONN->Execute($query);
	$query="select * from section_time where year='$sel_year' and semester='$sel_seme' order by sector";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$section_table[$res->fields[sector]]=explode("-",$res->fields[stime]);
		$res->MoveNext();
	}
	$query = "select MAX(sections) from score_setup where year = '$sel_year' and semester='$sel_seme'";
	$res=$CONN->Execute($query);
	$max_sector=$res->fields[0];
	for ($i=1;$i<=$max_sector;$i++) {
		if ($section_table[$i][0]=="") {
			$section_table[$i][0]=" ";
			$section_table[$i][1]=" ";
		}
	}
	return $section_table;
}


function get_teacher_course($sel_year="",$sel_seme="",$teacher_sn="",$is_allow="")
{
	global $CONN;

	//領域科目名稱
	$subject_arr = get_subject_name_arr();

	//取得本學期班級陣列
	$class_name_arr = class_base();

	$class_num = get_teach_class();
	
	//先取出分組課程的選單
	$query="select * from score_ss where year='$sel_year' and semester='$sel_seme' and enable='1'";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$all_ss_id.="'".$res->fields[ss_id]."',";
		$res->MoveNext();
	}
	if ($all_ss_id) $all_ss_id=substr($all_ss_id,0,-1);
	$sql_sub="select * from elective_tea where teacher_sn='$teacher_sn' and ss_id in ($all_ss_id)";
	$rs_sub=$CONN->Execute($sql_sub);
	if($rs_sub){
		$sub=0;
		while(!$rs_sub->EOF){
			//一樣要找出科目名
			$group_id=$rs_sub->fields['group_id'];
			$group_name=$rs_sub->fields['group_name'];
			$ss_id=$rs_sub->fields['ss_id'];
			$cid=$rs_sub->fields['course_id'];
	
			//這個科目需要考試嗎？
			$query = "select need_exam from score_ss where ss_id='$ss_id' ";
			$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			$need_exam = $res->fields['need_exam'];
			if($need_exam){
				$class_subj=ss_id_to_class_subject_name($ss_id);
				$class_subj_group=$class_subj."-".$group_name;
				$gs_id=$group_id."g".$ss_id;
				$e_arr[$cid]=$gs_id;
				$course_arr[$gs_id]=$class_subj_group;
				$sub++;
			}
			$rs_sub->MoveNext();
		}
	}

	
	//取得任教科目陣列 $course_arr
	//導師
	if ($class_num && $is_allow=="y") {
		$class_year = substr($class_num,0,-2);
		$class_name = substr($class_num,-2);
		$query = "select a.course_id,a.class_id,b.scope_id,b.subject_id,b.print,a.allow from score_course a,score_ss b where a.day<>'' and a.ss_id=b.ss_id and b.need_exam=1 and a.year='$sel_year' and a.semester='$sel_seme' and b.year='$sel_year' and b.semester='$sel_seme' and  b.enable='1' and  (a.teacher_sn='$teacher_sn' or (a.class_year='$class_year' and a.class_name='$class_name' and a.allow='0')) group by a.class_id,b.scope_id,b.subject_id";
		
	}
	//科任老師
	else
		$query = "select a.course_id,a.class_id,b.scope_id,b.subject_id,b.print,a.allow from score_course a,score_ss b where a.day<>'' and a.ss_id=b.ss_id and b.need_exam=1 and a.year='$sel_year' and a.semester='$sel_seme' and a.teacher_sn='$teacher_sn' and b.year='$sel_year' and b.semester='$sel_seme' and  b.enable='1' group by a.class_id,b.scope_id,b.subject_id";
	
	$res = $CONN->Execute($query)or trigger_error($query,E_USER_ERROR);
	while(!$res->EOF){
		$temp_arr = explode("_",$res->fields[class_id]);
		$temp_id = sprintf("%d%02d",$temp_arr[2],$temp_arr[3]);
		$temp_ss_id = $res->fields[subject_id];
		$cid=$res->fields[course_id];
		$print[$cid]=$res->fields['print'];
		if ($res->fields[subject_id]==0)  $temp_ss_id = $res->fields[scope_id];
		if ($cid==$teacher_course) $subj_id=$temp_ss_id;
		if (empty($e_arr[$cid]))  $course_arr[$cid] = $class_name_arr[$temp_id].$subject_arr[$temp_ss_id][subject_name];
		//關閉導師功能判別
		if ($is_allow=='y') $allow_arr[$res->fields[course_id]] = $res->fields[allow];	
		$res->MoveNext();
	}
	
	return array('course'=>$course_arr,'allow'=>$allow_arr);
}

#######################################
## JavaScript上下移動表單中的TEXT輸入格
## 使用時配合您表單的名稱傳入 formname 呼叫
## 使用流程:
## 1.在程式中加入include_once "../../include/sfs_case_subjectscore.php";
## 2.您程式的表單名稱為 aform 則以 moveit2("aform")呼叫
## 3.在每個須移動的text欄位中加入  onkeydown="moveit2(this,event);"  
## 如<INPUT TYPE='text' name=xxx value='xxx'   onkeydown="moveit2(this,event);" >
#######################################

function moveit2($f1){
?>
<script language="JavaScript">
<!--
function moveit2(chi,event) {
	var pKey = event.keyCode;//十字鍵 38向上 40向下;37向左;39向右
	if (pKey==40 || pKey==38  ) {
//	if (pKey==40 || pKey==38 || pKey==37 || pKey==39 ) {
	var max=document.<?=$f1?>.elements.length ;//所有元件數量
	var Go=0;//要移動位置
	TText= new Array ; //文字欄位陣列
	var Tin=0; //文字欄位陣列索引
	var Tin_now=0; //文字欄位陣列索引目前位置
	for(i=0; i<max; i++) {
	var obj = document.<?=$f1?>.elements[i];
	if (obj.type == 'text')
	{
	TText[Tin]=i; //記下它在所有元表中的第幾個
if(obj.name==chi.name ) {Tin_now=Tin;} //如果是傳進來的欄位,就記下該欄位在文字欄位陣列索引值
	Tin=Tin+1;
	}
	}
if (Tin==1 ) return false;//僅一個就不要移了
// if (pKey==40 || pKey==39 ) KK=40;
// if (pKey==38 || pKey==37 ) KK=38;
switch (pKey){ //循迴
	case 40://向下
		Tin=Tin-1;//取得索引值
		(Tin_now == Tin ) ? Go=TText[0] : Go=TText[Tin_now+1];
		document.<?=$f1?>.elements[Go].focus();
		return false;
		break;
	case 38://向上
		Tin=Tin-1;//取得索引值
		(Tin_now == 0 ) ? Go=TText[Tin] : Go=TText[(Tin_now-1)];
		document.<?=$f1?>.elements[Go].focus();
		return false;
		break;
		default:
	return false;
	}
	}
}

//-->
</script>
<?php
	}
########## End moveit2() ###############

?>
