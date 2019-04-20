<?php
//$Id: chi_class_obj.php 8526 2015-09-14 06:41:06Z infodaes $
$question_kind=array("未填寫",
	1=>"犯案、嚴重行為偏差行為",
	2=>"低成就、中輟學生",
	3=>"升學、就業問題",
	4=>"家庭、人際、適應困難等因素",
	5=>"春暉學生(濫用藥物)",
	6=>"自傷學生(自我傷害傾向)",
	7=>"災後需特別輔導關懷之學生",
	
	9=>"拒學中輟",
	10=>"自傷/自殺",
	11=>"網路(3C)成癮",
	12=>"性侵/性騷擾/性霸凌",
	13=>"家暴/兒虐",
	14=>"哀傷/失落",
	15=>"家庭/親子",
	16=>"情緒困擾",
	17=>"人際困擾",
	18=>"學習困擾",
	19=>"一般精神疾患",
	20=>"特教",
	21=>"偏差行為",
	22=>"情感/性別困擾",
	23=>"志願選填特殊個案",
	24=>"藥物濫用(物質使用)",
	25=>"組織幫派行為",
	
	8=>"其他",
	);
$come_from=array("未填寫",1=>"級任老師",2=>"家長",3=>"社會局",4=>"其他");
$guid_over=array("否","是");
$talk_gui_stud=array("未填寫",1=>"晤談",2=>"電話",3=>"家訪",4=>"其他");
$size=15;

#####################   CSS  ###########################
function myheader(){
?>
<style type="text/css">
body{background-color:#f9f9f9;font-size:12pt}
.ip12{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;}
.ipmei{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;}
.ipme2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:14pt;color:red;font-family:標楷體 新細明體;}
.ip2{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:11pt;color:red;font-family:新細明體 標楷體;}
.ip3{border-style: solid; border-width: 0px; background-color:#E6ECF0; font-size:12pt;color:blue;font-family:新細明體 標楷體;}
.bu1{border-style: groove;border-width:1px: groove;background-color:#CCCCFF;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.bub{background-color:#FFCCCC;font-size:14pt;}
.bur2{border-style: groove;border-width:1px: groove;background-color:#FFCCCC;font-size:12px;Padding-left:0 px;Padding-right:0 px;}
.f8{font-size:9pt;color:blue;}
.f9{font-size:9 pt;}
.tth{ text-align: center; white-space: nowrap; background-color:#9EBCDD;}
.ttd{  white-space: nowrap; background-color:#FFFFFF;font-size:10pt }
A:link  {text-decoration:none;color:blue; }
A:visited {text-decoration:none;color:blue; }
A:hover {background-color:FF8000;color: #000000;  }
</style><?php
}

#####################   班級選單  ###########################
function link_a($Seme,$Sclass=''){
//		global $PHP_SELF;//$CONN,
	$class_name_arr = class_base($Seme) ;
	$ss="選擇班級：<select name='Sclass' size='1' class='small' onChange=\"location.href='$_SERVER[PHP_SELF]?Seme='+p2.Seme.value+'&Sclass='+this.options[this.selectedIndex].value;\">
	<option value=''>未選擇</option>\n ";
	foreach($class_name_arr as $key=>$val) {
	//$key1=substr($Seme,0,3)."_".substr($Seme,3,1)."_".sprintf("%02d",substr($key,0,1))."_".substr($key,1,2);
	$key1=$Seme."_".$key;
	($Sclass==$key1) ? $cc=" selected":$cc="";
	$ss.="<option value='$key1' $cc>$val </option>\n";
	}
	$ss.="</select>";
Return $ss;
}


##################  基本工具 initArray轉化資料為索引與帶值函式 #######################
## 選取資料的欄A為索引,欄B為值,欄A須是唯一
## 使用時 傳入 $F1為字串==>subject_id,subject_name
## 使用時 傳入 $SQL為資料庫語法
##################  基本工具 initArray轉化資料為索引與帶值函式 #######################

function initArray($F1,$SQL){
	global $CONN ;
	$col=split(",",$F1);
	$key_field=$col[0];
	$value_field=$col[1];

	$rs = $CONN->Execute($SQL) or die($SQL);
	$sch_all = array();
	if (!$rs) {
		Return $CONN->ErrorMsg(); 
	} else {
		while (!$rs->EOF) {
		$sch_all[$rs->fields[$key_field]]=$rs->fields[$value_field]; 
		$rs->MoveNext(); // 移至下一筆記錄
		}
	}
	Return $sch_all;
}
###########################################################
##抓取教師資料，包括〈teach_person_id,name,birthday,address,home_phone,title_name,class_num〉//
function get_tea_sel(){
	global $CONN;
	//抓取教師資料
	if($_GET['view']=='old') $teach_cond=''; else $teach_cond='and a.teach_condition =0'; 
	$sql_select = "SELECT a.teacher_sn, a.name, a.birthday, a.address, a.home_phone, a.cell_phone,a.teach_condition , d.title_name ,b.class_num FROM  teacher_base a , teacher_post b, teacher_title d where  a.teacher_sn =b.teacher_sn  and b.teach_title_id = d.teach_title_id $teach_cond order by a.teach_condition,class_num, post_kind , post_office , a.teach_id ";
	$rs=$CONN->Execute($sql_select) or die($sql_select);
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		if($ro->teach_condition) $ro->title_name='*非在職*'; 
		if($ro->class_num=='' || $ro->class_num=='0'){
			$word=$ro->title_name." - ".$ro->name;}
		else {
			$word=$ro->title_name.$ro->class_num." - ".$ro->name;
		}
		$key=$ro->teacher_sn;
		$arys[$key]=$word;
	}
	return $arys;
}

###########################################################
##抓取教師資料，包括〈teach_person_id,name,birthday,address,home_phone,title_name,class_num〉//
function get_tea_data(){
	global $CONN;
	//抓取教師資料
	if($_GET['view']=='old') $teach_cond=''; else $teach_cond='and a.teach_condition =0';
	$sql_select = "SELECT a.teacher_sn, a.name, a.birthday, a.address, a.home_phone, a.cell_phone , d.title_name ,b.class_num FROM  teacher_base a , teacher_post b, teacher_title d where  a.teacher_sn =b.teacher_sn  and b.teach_title_id = d.teach_title_id $teach_cond order by class_num, post_kind , post_office , a.teach_id ";
	$rs=$CONN->Execute($sql_select) or die($sql_select);
	$arys=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$key=$ro->teacher_sn;
		$arys[$key] = get_object_vars($ro);
		}
	return $arys;
}
###########################################################
##  傳出學生資料
function get_stu_data($st_sn,$seme){
	global $CONN ,$IS_JHORES;
		$sql = "select a.* ,b.seme_class,b.seme_num from stud_base a ,stud_seme b where a.student_sn = '$st_sn' and b.seme_year_seme='$seme' and b.student_sn=a.student_sn";
		$sql="select stud_base.*,stud_seme.seme_class,stud_seme.seme_num, stud_domicile.guardian_name,stud_domicile.guardian_unit,stud_domicile.guardian_hand_phone,stud_domicile.guardian_address,stud_domicile.guardian_phone  from stud_base left join stud_seme on(stud_base.student_sn=stud_seme.student_sn and stud_seme.seme_year_seme='$seme') left join stud_domicile on(stud_base.stud_id=stud_domicile.stud_id)  where stud_base.student_sn='{$st_sn}'  ";
		$rs = $CONN->Execute($sql) or die($sql);
		$arys=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$arys = get_object_vars($ro);
		($IS_JHORES==6) ? $grade=substr($ro->seme_class,0,1)-6:$grade=substr($ro->seme_class,0,1);
		$class1=substr($ro->seme_class,1,2)+0;
		$arys[cgrade]=num_tw($grade)."年".num_tw($class1)."班";
		}
	return $arys;
}
###########################################################
##  傳出學生資料
function get_stu_list($seme,$t_sn=''){
	global $CONN ,$IS_JHORES;
	if ($t_sn!='') { $add=" and a.guid_tea_sn='$t_sn' " ;}else { $add=" ";}
	$sql = "select a.guid_c_id,a.st_sn,a.guid_c_from,a.begin_date,a.guid_tea_sn,a.guid_c_kind,a.end_date,a.guid_c_isover,b.stud_name,b.stud_sex,c.seme_class,c.seme_num from stud_guid a,stud_base b,stud_seme c where a.st_sn = b.student_sn and b.student_sn=c.student_sn and c.seme_year_seme='$seme'  $add  order by a.begin_date desc ,c.seme_class ,c.seme_num ";
		$rs = $CONN->Execute($sql) or die($sql);
		$arys=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$arys[$ro->st_sn] = get_object_vars($ro);
		($IS_JHORES==6) ? $grade=substr($ro->seme_class,0,1)-6:$grade=substr($ro->seme_class,0,1);
		$class1=substr($ro->seme_class,1,2)+0;
		$arys[$ro->st_sn][cgrade]=num_tw($grade)."年".num_tw($class1)."班";
		}
	return $arys;
}
###########################################################
##  傳出學生資料
function get_stu_list2($type,$t_sn=''){
	global $CONN ,$IS_JHORES;
switch ($type){
	case 'now':$add1=" and a.guid_c_isover=0 ";
		break;
	case 'old':$add1=" and a.guid_c_isover=1 ";break;
 default:
}
	$seme=sprintf("%03d",curr_year()).curr_seme();
	if ($t_sn!='') { $add=" and a.guid_tea_sn='$t_sn' " ;}else { $add=" ";}
	$sql = "select a.guid_c_id,a.st_sn,a.guid_c_from,a.begin_date,a.guid_tea_sn,a.guid_c_kind,a.end_date,a.guid_c_isover,b.stud_name,b.stud_sex,c.seme_class,c.seme_num from stud_guid a,stud_base b,stud_seme c where a.st_sn = b.student_sn and b.student_sn=c.student_sn and c.seme_year_seme='$seme'  $add $add1 order by a.begin_date desc ,c.seme_class,c.seme_num  ";
		$rs = $CONN->Execute($sql) or die($sql);
		$arys=array();
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$arys[$ro->st_sn] = get_object_vars($ro);
		($IS_JHORES==6) ? $grade=substr($ro->seme_class,0,1)-6:$grade=substr($ro->seme_class,0,1);
		$class1=substr($ro->seme_class,1,2)+0;
		$arys[$ro->st_sn][cgrade]=num_tw($grade)."年".num_tw($class1)."班";
		}
	return $arys;
}


#########################################################
####  傳出學生資料3

function get_stu_list3($type,$page=0,$t_sn=''){
	global $CONN ,$IS_JHORES,	$size;
switch ($type){
	case 'now':$add1="  a.guid_c_isover=0 ";
		break;
	case 'old':$add1="  a.guid_c_isover=1 ";break;
 default:
}

//	$seme=sprintf("%03d",curr_year()).curr_seme();//目前學期

if ($t_sn!='') { $add=" and a.guid_tea_sn='$t_sn' " ;}else { $add=" ";}

	$sql = "select a.guid_c_id,a.st_sn,a.guid_c_from,a.begin_date,a.guid_tea_sn,a.guid_c_kind,a.end_date,a.guid_c_isover,a.guid_over_reason,b.stud_name,b.stud_sex,b.curr_class_num  from stud_guid a  left join stud_base b on( a.st_sn = b.student_sn)  where  $add1  $add  order by a.begin_date desc ,b.curr_class_num  limit   ".$page*$size." ,  $size";
	$rs = $CONN->Execute($sql) or die($sql);
	$obj=array();
	$obj=$rs->GetArray();
for($i=0;$i<$rs->RecordCount();$i++){
	($IS_JHORES==6) ? $obj[$i][grade]=substr($obj[$i][curr_class_num],0,1)-6:$obj[$i][grade]=substr($obj[$i][curr_class_num],0,1);
	$obj[$i][class1]=substr($obj[$i][curr_class_num],1,2)+0;
	$obj[$i][seme_num]=substr($obj[$i][curr_class_num],3,2)+0;
	$obj[$i][cgrade]=num_tw($obj[$i][grade])."年".num_tw($obj[$i][class1])."班";
	$obj[$i][guid_all_kind]=explode(",",$obj[$i][guid_c_kind]);

	}
	return $obj;
}


###########################################################
##  傳出學生資料
function get_stu_gui($id,$t_sn){
	global $CONN ;

		$sql = "select * from stud_guid where guid_c_id ='$id' and guid_tea_sn='$t_sn' ";
		$rs = $CONN->Execute($sql) or die($sql);
	if ($rs->RecordCount()==0) return "沒有相關資料！";
	$obj_stu=$rs->GetArray();
	$obj_stu[0][guid_all_kind]=explode(",",$obj_stu[0][guid_c_kind]);
	return $obj_stu[0];
}
###########################################################
##  傳出學生資料輔導紀錄--全部----傳入案號與教師碼
function get_event_all($id,$t_sn){
	global $CONN ;
		$sql = "select * from stud_guid_event where guid_c_id ='$id' and tutor='$t_sn' order by guid_l_date desc ";
		$rs = $CONN->Execute($sql) or die($sql);
	if ($rs->RecordCount()==0) return "";
	$obj_stu=$rs->GetArray();
	return $obj_stu;
}
###########################################################
##  傳出學生資料輔導紀錄--個人單筆----傳入資料號與教師碼
function get_event_one($id,$t_sn){
	global $CONN ;
		$sql = "select * from stud_guid_event where guid_l_id  ='$id' and tutor='$t_sn' ";
		$rs = $CONN->Execute($sql) or die($sql);
	if ($rs->RecordCount()==0) return "沒有相關資料！";
	$obj_stu=$rs->GetArray();
	return $obj_stu[0];
}

###########################################################
##  檢查記錄是否由本人負責
function check_gui($id,$t_sn){
	global $CONN ;
		$sql = "select * from stud_guid where guid_c_id ='$id' and guid_tea_sn='$t_sn' ";
		$rs = $CONN->Execute($sql) or die($sql);
	if ($rs->RecordCount()==0) {return "No";} else {return "Yes";}
}
###########################################################
##  檢查記錄是否由本人填寫
function check_event($id,$t_sn){
	global $CONN ;
		$sql = "select * from stud_guid_event where guid_l_id ='$id' and tutor ='$t_sn' ";
		$rs = $CONN->Execute($sql) or die($sql);
	if ($rs->RecordCount()==0) {return "No";} else {return "Yes";}
}



###########################################################
##  傳出中文數字函數

function num_tw($num, $type=0) {
 $num_str[0] = "十百千";
        $num_str[1] = "拾佰仟";
        $num_type[0]='零一二三四五六七八九';
        $num_type[1]='零壹貳參肆伍陸柒捌玖';
        $num = sprintf("%d",$num);
        while ($num) {
                $num1 = substr($num,0,1);
                $num = substr($num,1);
                $target .= substr($num_type[$type], $num1*2, 2);
                if (strlen($num)>0) $target .= substr($num_str[$type],(strlen($num)-1)*2,2);
 }
 return $target;
}

function backinput($st="未填妥!按下後回上頁重填!") {
echo"<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	}
function backe($st="未填妥!按下後回上頁重填!") {
echo "<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"history.back()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}
function backend($st="未填妥!按下後回上頁重填!") {
echo "<BR><BR><BR><CENTER><form>
	<input type='button' name='b1' value='$st' onclick=\"window.close()\" style='font-size:16pt;color:red'>
	</form></CENTER>";
	exit;
	}


function get_date_seme() {
	global $CONN ;
	$sql = "select * from school_day where day_kind='start' order by year ,seme ";
	$arys=array();
	$rs = $CONN->Execute($sql) or die($sql);
	while ($rs and $ro=$rs->FetchNextObject(false)) {
		$key=$ro->year."_".$ro->seme;
		$arys[$key][start] = $ro->day;
		$arys[$key][year]=$ro->year;
		$arys[$key][seme]=$ro->seme;
		}
	$sql = "select * from school_day where day_kind='end' order by year ,seme ";
	$rs = $CONN->Execute($sql) or die($sql);
	$all_day=$rs->GetArray();
	for ($i=0;$i<$rs->RecordCount();$i++){
		$ckey=$all_day[$i][year]."_".$all_day[$i][seme];
		$arys[$ckey][end]=$all_day[$i][day];
		}
	return $arys;
}


function alter_table(){
		global  $CONN;
	$res = $CONN->Execute("show columns from stud_guid like 'guid_over_reason'");
if ($res->EOF) {
	$UPSQL = 'ALTER TABLE `stud_guid` CHANGE `guid_c_over_reason` `guid_over_reason` TEXT NOT NULL';
	$rs=$CONN->Execute($UPSQL) or die($UPSQL."更新失敗");
	}
}


?>
