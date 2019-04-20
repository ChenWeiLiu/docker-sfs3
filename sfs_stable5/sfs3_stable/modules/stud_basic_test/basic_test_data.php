<?php

// $Id: basic_test_data.php 7711 2013-10-23 13:07:37Z smallduh $

// --系統設定檔
include "select_data_config.php";

//--認證 session
sfs_check();

$curr_seme=$_POST['curr_seme'];
$curr_school_year=$_POST['curr_school_year'];
$area=$_POST['area'];
$parent=$_POST['parent'];
$phone=$_POST['phone'];
$address=$_POST['address']; 

if ($_POST['do_key'] =="TXT 輸出") {	
	$filename = "student.txt";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/plain");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	
	$s = $curr_school_year*100;
	$e = ($curr_school_year+1)*100;
	
	$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_POST[curr_seme]' and b.seme_class>$s and b.seme_class<$e and a.stud_study_cond in (0,5) order by b.seme_class,b.seme_num";
	$result = $CONN->Execute($query)or die($query);
	
	$i=1;
	while (!$result->EOF) {
		$stud_id = $result->fields['stud_id'];
		$student_sn = $result->fields['student_sn'];
		$s_register_phone = $result->fields['stud_tel_1'];
		$s_home_phone = $result->fields['stud_tel_2'];
		$s_offical_phone = $result->fields['stud_tel_3'];
		$stud_sex = $result->fields['stud_sex'];
		$stud_name = $result->fields['stud_name'];
		$curr_class_num = $result->fields['curr_class_num'];
		$stud_birthday = $result->fields['stud_birthday'];
		$stud_person_id = $result->fields['stud_person_id'];
		$addr_zip = $result->fields['addr_zip'];
		$s_addres_1 = $result->fields['stud_addr_1'];
		$s_addres_2 = $result->fields['stud_addr_2'];
		//判斷學生身份
		$stud_kind_arr = explode(",",$result->fields['stud_kind']);
		$stud_kind="";
		$spe_kind="";
		if (in_array("9",$stud_kind_arr)) $stud_kind="1";
		if (in_array("12",$stud_kind_arr)) $stud_kind="2";
		if (in_array("51",$stud_kind_arr)) $stud_kind="3";
		if (in_array("6",$stud_kind_arr)) $stud_kind="4";
		if (in_array("7",$stud_kind_arr)) $stud_kind="5";
		if (in_array("52",$stud_kind_arr)) $stud_kind="6";
		if (empty($stud_kind)) $stud_kind="0";
		if (in_array("41",$stud_kind_arr)) $spe_kind="1";
		if (in_array("42",$stud_kind_arr)) $spe_kind="2";
		if (in_array("43",$stud_kind_arr)) $spe_kind="3";
		if (in_array("44",$stud_kind_arr)) $spe_kind="4";
		if (in_array("45",$stud_kind_arr)) $spe_kind="5";
		if (in_array("46",$stud_kind_arr)) $spe_kind="6";
		if (in_array("47",$stud_kind_arr)) $spe_kind="7";
		if (in_array("48",$stud_kind_arr)) $spe_kind="8";
		if (in_array("49",$stud_kind_arr)) $spe_kind="9";
		if (empty($spe_kind)) $spe_kind="0";
		//查詢父母姓名
		$query2 = "select fath_name,moth_name,guardian_name from stud_domicile where student_sn ='$student_sn'";
		$result2 = $CONN->Execute($query2)or die ($query2) ;
		$fath_name = $result2->fields['fath_name'];
		$moth_name = $result2->fields['moth_name'];
		$guardian_name = $result2->fields['guardian_name'];
		//判斷家長姓名
		if($parent==1) $parent_name=$guardian_name;
		if($parent==2) $parent_name=$fath_name;
		if($parent==3) $parent_name=$moth_name;
		//判斷電話
		if($phone==1)$s_phone=$s_register_phone;
		if($phone==2)$s_phone=$s_home_phone;
		if($phone==3)$s_phone=$s_offical_phone;
		//判斷住址
		if($address==1)$s_address=$s_addres_1;
		if($address==2)$s_address=$s_addres_2;
		$birth = explode("-" , $stud_birthday);//出生日期
		$birth[0]=$birth[0]-1911;//轉換成民國
		$curr_class_num_c=substr($curr_class_num,1,2); //取班別
		$curr_class_num_n=substr($curr_class_num,-2); //取座號
		$now_curr_seme=substr($_REQUEST['curr_seme'],0,3); //取學年度
		$over_curr_seme = $now_curr_seme+1; //畢業年度
		$arr = array($curr_class_num_c,$curr_class_num_n,sprintf("%-20s",str_replace("　","",$stud_name)),sprintf("%-10s",$stud_person_id),$stud_sex,$birth[0],$birth[1],$birth[2],$stud_kind,$spe_kind," ",1,1,sprintf("%-20s",$parent_name),sprintf("%-10s",substr($s_phone,0,10)),sprintf("%-3s",$addr_zip),sprintf("%-80s",$s_address),sprintf("%-8s",$stud_id),sprintf("%-10s",$s_offical_phone));
		$data[] = implode("", $arr);
		$result->MoveNext();
		$i++;
	}
	
	$ma .= implode("\r\n", $data);
	echo $ma;
	exit;
}

//印出檔頭
head("批次建立學生資料");
print_menu($menu_p);

?>

<table border="0" width="100%" cellspacing="0" cellpadding="0" >
<tr><td valign=top bgcolor="#CCCCCC">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="pform">

<?php
	//列出年度別選單
	if (!$curr_seme) $curr_seme = sprintf("%03d%d",curr_year(),curr_seme());
	$class_seme_p = get_class_seme(); //學年度
	$main.="學期：".menu_sel($class_seme_p,"curr_seme",$curr_seme);
	//列出年級別選單
	$school_year_p = year_base($_REQUEST[curr_year]);
	$main.="年級：".menu_sel($school_year_p,"curr_school_year",$curr_school_year);
	$main .= "<input type=submit name='do_key' value='TXT 輸出'>\n";
	$parent_sel="家長：".menu_sel($parent_arr,"parent",$parent);
	$phone_sel="電話：".menu_sel($phone_arr,"phone",$phone);
	$address_sel="住址：".menu_sel($address_arr,"address",$address);
	//輸出選單
	$main .= "輸出欄位： ".$parent_sel.$phone_sel.$address_sel;

	echo $main;

	
	//列出名冊
	$year=$curr_school_year;
	$s = $year*100;
	$e = ($year+1)*100;
	
	$sql = "SELECT sch_id FROM school_base"; //查詢學校代碼
	$rs = $CONN->Execute($sql)or die($sql);
	$sch_id = $rs->fields['sch_id'];
	
	if($class!="" and $year=="")
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class='$class'  and a.stud_study_cond in (0,5) order by b.seme_num ";
	if($class=="" and $year!="")
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class>$s and b.seme_class<$e and a.stud_study_cond in (0,5) order by b.seme_class";
	$result = $CONN->Execute($query)or die($query);

	$i=1;
	$list .= "<table border='0' width='100%' style='font-size:12px;' bgcolor='#C0C0C0' cellpadding='3' cellspacing='1'>";
	$list .= "<tr bgcolor='#FFFFCC' align='center'><td>班<br>級<td>座<br>號<td>學生姓名<td>身分證字號<td>性<br>別<td>出<br>生<br>年<td>出<br>生<br>月<td>出<br>生<br>日<td>學<br>生<br>身<br>分<td>身<br>心<br>障<br>礙<td>畢<br>肄<br>業<td>資<br>料<br>授<br>權<td>家長姓名<td>電　　話<td>郵<br>遞<br>區<br>號<td>地　　址<td>學號<td>緊急聯<br>絡手機</td></tr><tr bgcolor='#FFFFFF'>";
	while (!$result->EOF) {
		$stud_id = $result->fields['stud_id'];
		$student_sn = $result->fields['student_sn'];
		$s_register_phone = $result->fields['stud_tel_1'];
		$s_home_phone = $result->fields['stud_tel_2'];
		$s_offical_phone = $result->fields['stud_tel_3'];
		$stud_sex = $result->fields['stud_sex'];
		$stud_name = $result->fields['stud_name'];
		$curr_class_num = $result->fields['curr_class_num'];
		$stud_birthday = $result->fields['stud_birthday'];
		$stud_person_id = $result->fields['stud_person_id'];
		$addr_zip = $result->fields['addr_zip'];
		$s_addres_1 = $result->fields['stud_addr_1'];
		$s_addres_2 = $result->fields['stud_addr_2'];
		//判斷學生身份
		$stud_kind_arr = explode(",",$result->fields['stud_kind']);
		$stud_kind="";
		$spe_kind="";
		if (in_array("9",$stud_kind_arr)) $stud_kind="1";
		if (in_array("12",$stud_kind_arr)) $stud_kind="2";
		if (in_array("51",$stud_kind_arr)) $stud_kind="3";
		if (in_array("6",$stud_kind_arr)) $stud_kind="4";
		if (in_array("7",$stud_kind_arr)) $stud_kind="5";
		if (in_array("52",$stud_kind_arr)) $stud_kind="6";
		if (empty($stud_kind)) $stud_kind="0";
		if (in_array("41",$stud_kind_arr)) $spe_kind="1";
		if (in_array("42",$stud_kind_arr)) $spe_kind="2";
		if (in_array("43",$stud_kind_arr)) $spe_kind="3";
		if (in_array("44",$stud_kind_arr)) $spe_kind="4";
		if (in_array("45",$stud_kind_arr)) $spe_kind="5";
		if (in_array("46",$stud_kind_arr)) $spe_kind="6";
		if (in_array("47",$stud_kind_arr)) $spe_kind="7";
		if (in_array("48",$stud_kind_arr)) $spe_kind="8";
		if (in_array("49",$stud_kind_arr)) $spe_kind="9";
		if (empty($spe_kind)) $spe_kind="0";
		//查詢父母姓名
		$query2 = "select fath_name,moth_name,guardian_name from stud_domicile where student_sn ='$student_sn'";
		$result2 = $CONN->Execute($query2)or die ($query2) ;
		$fath_name = $result2->fields['fath_name'];
		$moth_name = $result2->fields['moth_name'];
		$guardian_name = $result2->fields['guardian_name'];
		//判斷家長姓名
		if($parent==1) $parent_name=$guardian_name;
		if($parent==2) $parent_name=$fath_name;
		if($parent==3) $parent_name=$moth_name;
		//判斷電話
		if($phone==1)$s_phone=$s_register_phone;
		if($phone==2)$s_phone=$s_home_phone;
		if($phone==3)$s_phone=$s_offical_phone;
		//判斷住址
		if($address==1)$s_address=$s_addres_1;
		if($address==2)$s_address=$s_addres_2;
		$birth = explode("-" , $stud_birthday);//出生日期
		$birth[0]=$birth[0]-1911;//轉換成民國
		$curr_class_num_c=substr($curr_class_num,1,2); //取班別
		$curr_class_num_n=substr($curr_class_num,-2); //取座號
		$now_curr_seme=substr($_REQUEST['curr_seme'],0,3); //取學年度
		$over_curr_seme = $now_curr_seme+1;
		$arr = array("<td>".$curr_class_num_c,$curr_class_num_n,$stud_name,$stud_person_id,$stud_sex,$birth[0],$birth[1],$birth[2],$stud_kind,$spe_kind,1,1,$parent_name,$s_phone,$addr_zip,$s_address,$stud_id,$s_offical_phone);
		$data[] = implode("<td>", $arr);
		$result->MoveNext();
		$i++;
	}
	
	$list .= implode("<tr bgcolor=#FFFFFF>", $data);
	$list .= "</table>";
	echo $list;

?>

</form></td>
</tr>
<tr>
<td >

</td>
</tr>
</table>
</td></tr></table>

<?php foot() ?>
