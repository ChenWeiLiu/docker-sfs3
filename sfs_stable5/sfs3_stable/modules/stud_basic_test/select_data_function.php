<?php
// $Id: select_data_function.php 6714 2012-03-06 06:01:06Z brucelyc $

function change_addr($addr,$mode=0) {
	//縣市
	$temp_str = split_str($addr,"縣",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

    //鄉鎮	
	$temp_str = split_str($addr,"鄉",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"鎮",1);

	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"市",1);
	
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"區",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//村里
	$temp_str = split_str($addr,"村",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"里",1);

	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//鄰
	$temp_str = split_str($addr,"鄰",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//路
	$temp_str = split_str($addr,"路",1);
	if ($temp_str[0] =="")
		$temp_str = split_str($addr,"街",1);
	
	$res[] = $temp_str[0];
	$addr=$temp_str[1];

      	//段
	$temp_str = split_str($addr,"段",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

      	//巷
	$temp_str = split_str($addr,"巷",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//弄
	$temp_str = split_str($addr,"弄",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//號
	$temp_str = split_str($addr,"號",$mode);
	$temp_arr = explode("-",$temp_str);
	if (sizeof($temp_arr)>1){
		$res[]=$temp_arr[0];
		$res[]=$temp_arr[1];
	}else {
		$res[]=$temp_str[0];
		$res[]="";
	}
	$addr=$temp_str[1];
	
	//樓
	$temp_str = split_str($addr,"樓",$mode);
	$res[]=$temp_str[0] ;
	$addr=$temp_str[1];

	//樓之
	if ($addr != "") {
		if ($mode)
			$temp_str = $addr;
		else
			$temp_str = substr(chop($addr),2);
	} else
		$temp_str ="";
		
	$res[]=$temp_str ;
      	return $res;
}

function split_str($addr,$str,$last=0) {
      	$temp = explode ($str, $addr);
	if (count($temp)<2 ){
		$t[0]="";
		$t[1]=$addr;
	}else{
		$t[0]=(!empty($last))?$temp[0].$str:$temp[0];
		$t[1]=$temp[1];
	}
	return $t;
}

function menu_sel($arr,$id,$sel) {
	$menu_sel = "<select name='$id' onchange=this.form.submit();>\n";
	while(list($k,$v)=each($arr)) {
		$selected=($k==$sel)?"selected":"";
		$menu_sel .= "<option value='$k' $selected>$v</option>\n";
	}
	$menu_sel .= "</select>\n";
	return $menu_sel;
}

function year_seme_menu($sel_year,$sel_seme) {
	global $CONN;

	$sql="select year,semester from school_class where enable='1' order by year,semester";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$year=$rs->fields["year"];
		$semester=$rs->fields["semester"];
		if ($year!=$oy || $semester!=$os)
			$show_year_seme[$year."_".$semester]=$year."學年度第".$semester."學期";
		$oy=$year;
		$os=$semester;
		$rs->MoveNext();
	}
	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = $sel_year."_".$sel_seme;
	$scys->arr = $show_year_seme;
	$scys->is_submit = true;
	return $scys->get_select();
}

function class_year_menu($sel_year,$sel_seme,$id) {
	global $school_kind_name,$CONN;

	$sql="select distinct c_year from school_class where year='$sel_year' and semester='$sel_seme' and enable='1' order by c_year";
	$rs=$CONN->Execute($sql);
	while (!$rs->EOF) {
		$show_year_name[$rs->fields["c_year"]]=$school_kind_name[$rs->fields["c_year"]]."級";
		$rs->MoveNext();
	}
	$scy = new drop_select();
	$scy->s_name ="year_name";
	$scy->top_option = "選擇年級";
	$scy->id = $id;
	$scy->arr = $show_year_name;
	$scy->is_submit = true;
	return $scy->get_select();
}

function get_stud_study_year($seme_year_seme,$year_name) {
	global $CONN;

	$query="select count(a.student_sn) as num,b.stud_study_year from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name"."%' and b.stud_study_cond in ('0','5','15') group by b.stud_study_year order by num desc";
	$res=$CONN->Execute($query);
	return $res->fields['stud_study_year'];
}

function chk_tbl() {
	global $CONN;

	$creat_table_sql="CREATE TABLE if not exists `stud_seme_dis` (
	`seme_year_seme` varchar(6) NOT NULL default '',
	`student_sn` int(10) unsigned NOT NULL default '0',
	`seme_class` varchar(10) default NULL,
	`seme_num` tinyint(3) unsigned default NULL,
	`area1` varchar(2) default NULL,
	`area2` varchar(2) default NULL,
	`stud_kind` tinyint(3) unsigned default NULL,
	`hand_kind` varchar(1) default NULL,
	`lowincome` varchar(1) default NULL,
	`unemployed` varchar(1) default NULL,
	`datalic` varchar(1) default NULL,
	`addr` varchar(60) default NULL,
	`zip` varchar(5) default NULL,
	`parent` varchar(20) NOT NULL default '',
	`tel` varchar(20) default NULL,
	`cell` varchar(20) default NULL,
	`cal` varchar(1) default NULL,
	`enable0` varchar(1) default '1',
	`enable1` varchar(1) default '1',
	`enable2` varchar(1) default '1',
	`sp_kind` varchar(1) default NULL,
	`sp_cal` varchar(1) default NULL,
	PRIMARY KEY (seme_year_seme,student_sn)
	)";
	$CONN->Execute($creat_table_sql);
}

//判斷資料封存狀態
function chk_dis($sel_year=0,$sel_seme_arr=array()) {
	global $CONN;

	$temp_arr=array();
	$sel_year=intval($sel_year);
	if ($sel_year>0) {
		$query="select count(student_sn) as num from dis_score_fin where year='$sel_year'";
		$res=$CONN->Execute($query);
		if ($res->fields['num']>0) $temp_arr[2]=1;
	}
	if (count($sel_seme_arr)>0) {
		$sel_str="'".implode("','",$sel_seme_arr)."'";
		$query="select count(student_sn) as num from dis_stage_fin where concat(year,semester) in ($sel_str)";
		$res=$CONN->Execute($query);
		if ($res->fields['num']>0) $temp_arr[3]=1;
	}

	return $temp_arr;
}

function chk_fin() {
	global $CONN;
	
	$creat_table_sql="CREATE TABLE if not exists `dis_score_fin` (
			`student_sn` int(10) unsigned NOT NULL default '0',
			`year` smallint(5) unsigned NOT NULL default '0',
			`seme` varchar(4) NOT NULL default '',
			`ss_no` int(6) unsigned NOT NULL default '0',
			`score` float NOT NULL default '0.0',
			`pr` int(6) unsigned NOT NULL default '0',
			`sp_score` float NOT NULL default '0.0',
			`sp_pr` int(6) unsigned NOT NULL default '0',
			PRIMARY KEY (student_sn,seme,ss_no)
	)";
	$CONN->Execute($creat_table_sql);
}
?>
