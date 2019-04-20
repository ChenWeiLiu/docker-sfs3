<?php
// $Id: my_fun.php 5830 2010-01-15 13:37:49Z hami $

function sub_menu($sub_menu_arr=array(),$id="",$s_name="") {
	global $CONN;

	$sm = new drop_select();
	$sm->s_name =($s_name=="")?"sub_menu_id":$s_name;
	$sm->has_empty = false;
	$sm->id = $id;
	$sm->arr = $sub_menu_arr;
	$sm->is_submit = true;
	if ($s_name=="kmenu_id" && $id) $sm->other_script = "document.myform.target='';document.getElementById('act').value='';document.getElementById('sn').value=''";
	return $sm->get_select();
}

function year_menu($sel_year,$other_script="") {
	global $CONN;

	$s = new drop_select();
	$s->s_name ="sel_year";
	$s->top_option = "選擇學年";
	$s->id = sprintf("%03d",$sel_year);
	$s->arr = get_class_year();
	$s->is_submit = true;
	$s->other_script = $other_script;
	return $s->get_select();
}

function year_seme_menu($sel_year,$sel_seme,$other_script="") {
	global $CONN;

	$scys = new drop_select();
	$scys->s_name ="year_seme";
	$scys->top_option = "選擇學期";
	$scys->id = sprintf("%03d",$sel_year).$sel_seme;
	$scys->arr = get_class_seme();
	$scys->is_submit = true;
	$scys->other_script = $other_script;
	return $scys->get_select();
}

function class_menu($sel_year,$sel_seme,$id,$other_script="",$mode=0) {
	global $school_kind_name,$class_year,$CONN;

	$scy = new drop_select();
	$scy->s_name ="class_name";
	$scy->top_option = ($mode==2)?"選擇年級":"選擇班級";
	$scy->id = $id;
	if ($mode!=2) $tmp_arr = class_base(sprintf("%03d",$sel_year).$sel_seme);
	if ($mode!=0) {
		foreach($class_year as $k=>$v) if (intval($k)>0) $tmp_arr[$k] = $v."級";
		if ($mode!=2) $tmp_arr["all"] = "全校";
	}
	$scy->arr = $tmp_arr;
	$scy->is_submit = true;
	$scy->other_script = $other_script;
	return $scy->get_select();
}

function stud_menu($sel_year,$sel_seme,$sel_class,$id,$other_script="",$mode=0) {
	global $CONN;

	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$s = new drop_select();
	$s->s_name ="student_sn";
	$s->top_option = "選擇學生";
	$s->id = $id;
	$tmp_arr=array();
	$tmp_str="";
	$query = "select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$sel_class' order by seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$tmp_arr[$res->fields['student_sn']]="(".$res->fields['seme_num'].") ";
		$tmp_str.="'".$res->fields['student_sn']."',";
		$res->MoveNext();
	}
	if ($tmp_str) {
		$tmp_str = substr($tmp_str,0,-1);
		$query="select * from stud_base where student_sn in ($tmp_str)";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			switch ($res->fields['stud_sex']) {
				case 1: $c=1; break;
				case 2: $c=0; break;
				default: $c=3;
			}
			$tmp_arr[$res->fields['student_sn']] .= $res->fields['stud_name'];
			$c_arr[$res->fields['student_sn']] = $c;
			$res->MoveNext();
		}
	}
	$s->arr = $tmp_arr;
	$s->is_display_color = true;
	$s->color_index_arr = $c_arr;
	$s->is_submit = true;
	$s->other_script = $other_script;
	return $s->get_select();
}

function get_stu_arr($sel_year,$sel_seme,$id) {
	global $CONN,$study_str;

	$query = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='".sprintf("%03d",$sel_year).$sel_seme."' and b.seme_class='$id' and a.stud_study_cond in ($study_str) order by b.seme_num";
	$res=$CONN->Execute($query);
	return $res->GetRows();
}

function arr_to_str($temp_arr=array()) {

	$temp_str="";
	while(list($k,$v)=each($temp_arr)) {
		$temp_str.=$k.".".$v.",";
	}
	if ($temp_str) $temp_str=substr($temp_str,0,-1);
	return $temp_str;
}

function get_whs($h="",$year_seme="",$kind="whs",$mode=0) {
	global $Bid_arr,$_POST;

	if ($h) {
		$temp_arr=array();
		while(list($seme_class,$v)=each($h->stud_data)) {
			while(list($seme_num,$vv)=each($v)) {
				$a=array();
				$seme_year=substr($seme_class,0,-2);
				$seme_name=substr($seme_class,-2,2);
				$sn=$vv[student_sn];
				$hh=$h->health_data[$sn][$year_seme];
				if ($_POST['table']) {
					if ($hh[height])
					$tb=ceil(($hh[height]-1)/5)*5;
					else
					$tb="";
					$kind="wht";
				}
				if ($kind=="whs")
				$a=array($seme_year,$seme_name,$seme_num,$h->stud_base[$sn][stud_name],$hh[height],$hh[weight],$hh[BMI],$Bid_arr[$hh[Bid]],$hh[r][sight_o],$hh[l][sight_o],$hh[r][sight_r],$hh[l][sight_r]);
				elseif ($kind=="whb")
				$a=array($seme_year,$seme_name,$seme_num,$h->stud_base[$sn][stud_name],$hh[height],$hh[weight],$hh[BMI],$Bid_arr[$hh[Bid]]);
				elseif ($kind=="wh")
				$a=array($seme_year,$seme_name,$seme_num,$h->stud_base[$sn][stud_name],$hh[height],$hh[weight]);
				elseif ($kind=="wht")
				$a=array($seme_year,$seme_name,$seme_num,$h->stud_base[$sn][stud_name],$hh[height],$hh[weight],$hh[BMI],$Bid_arr[$hh[Bid]],$tb);
				else
				$a=array($seme_year,$seme_name,$seme_num,$h->stud_base[$sn][stud_name],$hh[r][sight_o],$hh[l][sight_o],$hh[r][sight_r],$hh[l][sight_r]);
				if ($mode)
				$temp_arr[$seme_class][]=$a;
				else
				$temp_arr[]=$a;
			}
		}
	}
	return $temp_arr;
}

function get_utee($h,$year_seme="",$mode=0) {

	if ($h) {
		$temp_arr=array();
		while(list($seme_class,$v)=each($h->stud_data)) {
			while(list($seme_num,$vv)=each($v)) {
				$a=array();
				$seme_year=substr($seme_class,0,-2);
				$seme_name=substr($seme_class,-2,2);
				$sn=$vv[student_sn];
				if ($h->health_data[$sn][$year_seme]['chkOra']!=1) {
					$a=array($seme_year,$seme_name,$seme_num,$h->stud_base[$sn][stud_name]);
					if ($mode)
					$temp_arr[$seme_class][]=$a;
					else
					$temp_arr[]=$a;
				}
			}
		}
	}
	return $temp_arr;
}

function get_stud_list($h="",$year_seme="",$mode=0) {

	if ($h) {
		$temp_arr=array();
		while(list($seme_class,$v)=each($h->stud_data)) {
			while(list($seme_num,$vv)=each($v)) {
				$a=array();
				$seme_year=substr($seme_class,0,-2);
				$seme_name=substr($seme_class,-2,2);
				$sn=$vv[student_sn];
				$hh=$h->health_data[$sn][$year_seme];
				$d=$h->stud_base[$sn];
				$stud_sex=($d[stud_sex]==1)?"男":"女";
				if ($d[guardian_name])
				$n=$d[guardian_name];
				elseif ($d[fath_name])
				$n=$d[fath_name];
				else
				$n=$d[moth_name];
				$a=array($seme_class,$seme_num,$d[stud_id],$d[stud_name],$stud_sex,$d[stud_person_id],$d[stud_birthday],$d[stud_addr_2],$d[stud_tel_2],$n);
				if ($mode)
				$temp_arr[$seme_class][]=$a;
				else
				$temp_arr[]=$a;
			}
		}
	}
	return $temp_arr;
}

function file_menu($path_name="",$id="",$s_name="file_name",$ff_name="",$ext_name="") {
	global $CONN;

	$file_arr=array();
	$flen=strlen($ff_name);
	$elen=strlen($ext_name);
	$fp=opendir($path_name);
	while(gettype($file=readdir($fp))!=boolean){
		if (is_file("$path_name/$file")) {
			if (($ff_name=="" || substr($file,0,$flen)==$ff_name) && ($ext_name=="" || substr($file,($elen*(-1)),$elen)==$ext_name)){
				$file_arr[$file]=$file;
			}
		}
	}
	closedir($fp);

	$obj = new drop_select();
	$obj->s_name = $s_name;
	$obj->top_option = "請選擇檔案";
	$obj->id = $id;
	$obj->arr = $file_arr;
	$obj->is_submit = true;
	return $obj->get_select();
}

function chk_headline($h=array()) {

	$temp_arr=array();
	while(list($k,$v)=each($h)) {
		switch($v) {
			case "PID":
				$wh++;
				$temp_arr['data'][$v]=$k;
				break;
			case "GradeID":
				$wh++;
				$temp_arr['data'][$v]=$k;
				break;
			case "Sem":
				$wh++;
				$temp_arr['data'][$v]=$k;
				break;
			case "Weight":
				$wh++;
				$temp_arr['data'][$v]=$k;
				break;
			case "Height":
				$wh++;
				$temp_arr['data'][$v]=$k;
				break;
		}
	}
	if ($wh==5) {
		$temp_arr['chart']="wh";
		$temp_arr['chart_name']="身高體重表";
	}

	return $temp_arr;
}

//取得醫院或診所資料
//$mode=0:回傳醫院陣列, $mode=1:回傳新增的醫院id
function get_hospital($mode=0) {
	global $CONN;

	if ($_POST['new_hos']) {
		$_POST['new_hos']=trim($_POST['new_hos']);
		$query="select * from health_hospital where name='".$_POST['new_hos']."'";
		$res=$CONN->Execute($query);
		if ($res->RecordCount()==0) {
			$CONN->Execute("insert into health_hospital (name) values ('".$_POST['new_hos']."')");
			$new_id=$CONN->Insert_ID();
		} else {
			if ($res->fields[enable]==0) $CONN->Execute("update health_hospital set enable='1' where name=('".$_POST['new_hos']."')");
		}
	}
	$_POST['hos_name']=trim($_POST['hos_name']);
	$_POST['hos_id']=intval($_POST['hos_id']);
	if ($_POST['hos_name'] && $_POST['hos_id']!=0) {
		$CONN->Execute("update health_hospital set name='".$_POST['hos_name']."' where id='".$_POST['hos_id']."'");
	}
	if ($mode==0) {
		if ($_POST['del_hos_id']) {
			$CONN->Execute("update health_hospital set enable='0' where id='".intval($_POST['del_hos_id'])."'");
		}
		$query="select * from health_hospital where enable='1' order by id";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$temp_arr[sprintf("%02d",$res->fields['id'])]=$res->fields['name'];
			$res->MoveNext();
		}
		return $temp_arr;
	} else {
		return $new_id;
	}
}

//取得保險類別資料
function get_insurance($mode=0) {
	global $CONN;
	if ($_POST['new_insurance']) {
		$_POST['new_insurance']=trim($_POST['new_insurance']);
		$query="select * from health_insurance where name='".$_POST['new_insurance']."'";
		$res=$CONN->Execute($query);
		if ($res->RecordCount()==0) {
			$CONN->Execute("insert into health_insurance (name) values ('".$_POST['new_insurance']."')");
			$new_id=$CONN->Insert_ID();
		} else {

			if ($res->fields[enable]==0) $CONN->Execute("update health_insurance set enable='1' where name=('".$_POST['new_insurance']."')");
		}
	}
	$_POST['insurance_name']=trim($_POST['insurance_name']);
	$_POST['insurance_id']=intval($_POST['insurance_id']);
	if ($_POST['insurance_name'] && $_POST['insurance_id']!=0) {
		$CONN->Execute("update health_insurance set name='".$_POST['insurance_name']."' where id='".$_POST['insurance_id']."'");
	}
	if ($mode==0) {
//
//		if ($_POST['del_insurance_id']) {
//			$CONN->Execute("update health_insurance set enable='0' where id='".intval($_POST['del_insurance_id'])."'");
//		}

		$query="select * from health_insurance where enable='1' order by id";
		$temp_arr = array();
		$res=$CONN->Execute($query) or die($query);
		while(!$res->EOF) {
			$temp_arr[sprintf("%d",$res->fields['id'])]=$res->fields['name'];
			$res->MoveNext();
		}
		return $temp_arr;
	} else {
		return $new_id;
	}
}

//取得傷病地點資料
//$mode=0:回傳傷病陣列, $mode=1:回傳新增的傷病id, $mode=2:回傳傷病反陣列
function get_accident_item($mode=0,$tbl_name="") {
	global $CONN;

	if ($tbl_name) {
		switch ($tbl_name) {
			case "health_accident_place":
			case "health_accident_reason":
			case "health_accident_part":
			case "health_accident_status":
			case "health_accident_attend":
				break;
			default:
				return;
		}
		$post_data=addslashes(trim($_POST['new_item']));
		$edit_name=addslashes(trim($_POST['item_name']));
		$edit_id=intval($_POST['item_id']);
		$del_id=intval($_POST['del_item_id']);
		if ($post_data) {
			$query="select * from $tbl_name where name='".$post_data."'";
			$res=$CONN->Execute($query);
			if ($res->RecordCount()==0) {
				$res=$CONN->Execute("select max(id) as mid from $tbl_name where id <> 999");
				$maxid=$res->fields['mid']+1;
				$CONN->Execute("insert into $tbl_name (id,name) values ('$maxid','".$post_data."')");
				$new_id=$CONN->Insert_ID();
			} else {
				if ($res->fields[enable]==0) $CONN->Execute("update $tbl_name set enable='1' where name=('".$post_data."')");
			}
		}
		if ($edit_name && $edit_id!=0) {
			$CONN->Execute("update $tbl_name set name='".$edit_name."' where id='".$edit_id."'");
		}
		if ($mode==0 || $mode==2) {
			if ($del_id) {
				$CONN->Execute("update $tbl_name set enable='0' where id='$del_id'");
			}
			$query="select * from $tbl_name where enable='1' order by id";
			$res=$CONN->Execute($query);
			while(!$res->EOF) {
				if ($mode==0)
				$temp_arr[$res->fields['id']]=$res->fields['name'];
				else
				$temp_arr[$res->fields['name']]=$res->fields['id'];
				$res->MoveNext();
			}
			return $temp_arr;
		} else {
			return $new_id;
		}
	}
}

function get_accident($id=0) {
	global $CONN;

	if ($id) {
		$temp_arr=array();
		$query="select * from health_accident_record where id='$id'";
		$res=$CONN->Execute($query);
		$t=$res->GetRows();
		$temp_arr=$t[0];
		$query="select * from health_accident_part_record where id='$id'";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$temp_arr[part_id][]=$res->fields['part_id'];
			$res->MoveNext();
		}
		$query="select * from health_accident_status_record where id='$id'";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$temp_arr[status_id][]=$res->fields['status_id'];
			$res->MoveNext();
		}
		$query="select * from health_accident_attend_record where id='$id'";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$temp_arr[attend_id][]=$res->fields['attend_id'];
			$res->MoveNext();
		}
		return $temp_arr;
	}
}

//取得預防接種資料
//$mode=0:回傳預防接種陣列, $mode=1:回傳新增的預防接種id
function get_inject_item($mode=0) {
	global $CONN;

	$post_data=addslashes(trim($_POST['new_item']));
	$edit_name=addslashes(trim($_POST['item_name']));
	$edit_id=intval($_POST['item_id']);
	$del_id=intval($_POST['del_item_id']);
	if ($post_data) {
		$query="select * from $tbl_name where name='".$post_data."'";
		$res=$CONN->Execute($query);
		if ($res->RecordCount()==0) {
			$res=$CONN->Execute("select max(id) as mid from $tbl_name where id <> 999");
			$maxid=$res->fields['mid']+1;
			$CONN->Execute("insert into $tbl_name (id,name) values ('$maxid','".$post_data."')");
			$new_id=$CONN->Insert_ID();
		} else {
			if ($res->fields[enable]==0) $CONN->Execute("update $tbl_name set enable='1' where name=('".$post_data."')");
		}
	}
	if ($edit_name && $edit_id!=0) {
		$CONN->Execute("update $tbl_name set name='".$edit_name."' where id='".$edit_id."'");
	}
	if ($mode==0) {
		if ($del_id) {
			$CONN->Execute("update $tbl_name set enable='0' where id='$del_id'");
		}
		$query="select * from health_inject_item where enable='1' order by id";
		$res=$CONN->Execute($query);
		$temp_arr['item'][0]="黃卡";
		while(!$res->EOF) {
			$temp_arr['item'][$res->fields['id']]=$res->fields['name'];
			if ($res->fields['lname']) $temp_arr['litem'][$res->fields['id']]=$res->fields['lname'];
			$temp_arr['times'][$res->fields['id']]=$res->fields['times'];
			$temp_arr['ltimes'][$res->fields['id']]=$res->fields['times'];
			for($i=0;$i<=4;$i++) {
				$temp_arr['lack'][$res->fields['id']][$i]=explode(',',$res->fields['lack'.$i]);
				if ($temp_arr['lack'][$res->fields['id']][$i][0]>0)$temp_arr['show'][$res->fields['id']]=$i;
			}
			$res->MoveNext();
		}
		return $temp_arr;
	} else {
		return $new_id;
	}
}

function read_health_conf($temp_file="") {
	if ($temp_file) {
		if (is_file($temp_file)) {
			$fp=fopen($temp_file,"r");
			while(!feof($fp)) {
				$temp_str=fgets($fp,50);
				if ($temp_str) {
					$temp_arr=explode("=",$temp_str);
					if (!empty($temp_arr[0])) $temp[strtoupper($temp_arr[0])]=$temp_arr[1];
				}
			}
			fclose($fp);
		}
		return $temp;
	}
}

function get_fday($sel_year="",$sel_seme="") {
	global $CONN;

	if ($sel_year=="") $sel_year=curr_year();
	if ($sel_seme=="") $sel_seme=curr_seme();
	$query="select * from health_fday where year='$sel_year' and semester='$sel_seme' order by week_no";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$temp_arr[$res->fields['week_no']]=$res->fields['do_date'];
		$res->MoveNext();
	}
	return $temp_arr;
}

function get_checks_item() {
	global $CONN;

	$query="select * from health_checks_item";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$temp_arr[$res->fields['subject']][$res->fields['no']]=$res->fields['item'];
		$res->MoveNext();
	}
	return $temp_arr;
}

function make_key($password){
	if (function_exists ('date_default_timezone_set')){
		//PHP5設定時區, 在PHP4無法使用
		date_default_timezone_set('Asia/Taipei');
	} else {
		//PHP4設定時區的用法
		putenv("TZ=Asia/Taipei");
	}
	return $password.date("Ymd");
}

function encrypt($key, $plain_text) {
	$plain_text = serialize($plain_text);
	$c_t = $plain_text;
	//	$iv = substr(md5($key), 0, mcrypt_get_iv_size(MCRYPT_CAST_256, MCRYPT_MODE_CFB));
	//	$c_t = mcrypt_cfb(MCRYPT_CAST_256, $key, $plain_text, MCRYPT_ENCRYPT, $iv);
	return base64_encode($c_t);
}

function postdata($host, $uri, $data){
	while (list($k,$v) = each($data)) {
		$post .= rawurlencode($k)."=".rawurlencode($v)."&";
	}
	$post = substr($post, 0, -1);
	$len = strlen($post);
	$fp = @fsockopen( $host, 80, $errno, $errstr, 3000000);
	//$fp = @fsockopen( "ssl://".$host , 443, $errno, $errstr, 3000000);
	if (!$fp) {
		echo "$errstr ($errno)\n";
	} else {
		$receive = '';
		$out = "POST $uri HTTP/1.1\r\n";
		$out .= "Host: $host\r\n";
		$out .= "Content-type: application/x-www-form-urlencoded\r\n";
		$out .= "Connection: Close\r\n";
		$out .= "Content-Length: $len\r\n";
		$out .="\r\n";
		$out .= $post."\r\n";
		fwrite($fp, $out);
		while (!feof($fp)) {
			if(fgets($fp, 128)=="\r\n")
			break;
		}
		while (!feof($fp)) {
			$receive .= fgets($fp, 128);
		}
		fclose($fp);
	}
	return $receive;
}

function br2nl($string)
{
	return preg_replace('/<br\\s*?\/??>/i', '', $string);
}
?>
