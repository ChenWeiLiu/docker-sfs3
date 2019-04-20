<?php

// $Id: basic_test_stu.php 7711 2013-10-23 13:07:37Z smallduh $

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
$do_key=$_POST['do_key'];

if ($do_key =="CSV 輸出") {	
	//if($curr_school_year)$filename = "year_".$curr_school_year.".csv";
	if($curr_school_year)$filename = "student.csv";
	if($curr_class_year)$filename = "student_".$curr_class_year.".csv";
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	
	$ma = "考區代碼,學校代碼,報名序號,學號,班級,座號,學生姓名,身分證號,性別,出生年,出生月,出生日,畢業學校,畢業年度,畢肄業,學生身分,身心障礙,分發區,低收入戶,失業給付者,資料授權,家長姓名,電話,郵遞區號,地址\n";

	$s = $curr_school_year*100;
	$e = ($curr_school_year+1)*100;
	
	$sql = "SELECT sch_id FROM school_base"; //查詢學校代碼
	$rs = $CONN->Execute($sql)or die($sql);
	$sch_id = $rs->fields['sch_id'];
	
	if($curr_class_year!="" and $curr_school_year=="")
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class='$_REQUEST[curr_class_year]'  and a.stud_study_cond in (0,5) order by b.seme_num ";
	if($curr_class_year=="" and $curr_school_year!="")
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class>$s and b.seme_class<$e and a.stud_study_cond in (0,5) order by b.seme_class, b.seme_num";
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
		$s_addres = $result->fields['stud_addr_2'];
		//判斷電話
		if($phone==1)$s_phone=$s_register_phone;
		if($phone==2)$s_phone=$s_home_phone;
		if($phone==3)$s_phone=$s_offical_phone;
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
		//判斷住址
		if($address==1)$s_address=$s_addres_1;
		if($address==2)$s_address=$s_addres_2;
		$birth = explode("-" , $stud_birthday);//出生日期
		$birth[0]=$birth[0]-1911;//轉換成民國
		$curr_class_num_c=substr($curr_class_num,1,2); //取班別
		$curr_class_num_n=substr($curr_class_num,-2); //取座號
		$now_curr_seme=substr($_REQUEST['curr_seme'],0,3); //取學年度
		$over_curr_seme = $now_curr_seme+1; //畢業年度
		$arr = array($area,$sch_id,$i,$stud_id,$curr_class_num_c,$curr_class_num_n,$stud_name,$stud_person_id,$stud_sex,$birth[0],$birth[1],$birth[2],$sch_id,$over_curr_seme,1,0,0,$area,0,0,1,$parent_name,$s_phone,$addr_zip,$s_addres);
		$data[] = implode(",", $arr);
		$result->MoveNext();
		$i++;
	}
	
	$ma .= implode("\n", $data);
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
<form action="<?php echo $_SERVER['SCRIPT_NAME'] ?>" method="post" name="pform">

<?php

	//列出年度別選單
	if (!$curr_seme) $curr_seme = sprintf("%03d%d",curr_year(),curr_seme());
	$class_seme_p = get_class_seme(); //學年度
	$main.="學期：".menu_sel($class_seme_p,"curr_seme",$curr_seme);
	//列出年級別選單
	$school_year_p = year_base($_REQUEST[curr_year]);
	$main.="年級：".menu_sel($school_year_p,"curr_school_year",$curr_school_year);
	$main .= "<input type=submit name='do_key' value='CSV 輸出'>\n";
	$parent_sel="家長：".menu_sel($parent_arr,"parent",$parent);
	$phone_sel="電話：".menu_sel($phone_arr,"phone",$phone);
	$address_sel="住址：".menu_sel($address_arr,"address",$address);
	//輸出選單
	$main .= "輸出欄位： ".$area_sel.$parent_sel.$phone_sel.$address_sel;
	echo $main;
	
	/*
	//列出年度別選單
	if (!$curr_seme)$curr_seme = sprintf("%03d%d",curr_year(),curr_seme());
	$class_seme_p = get_class_seme(); //學年度
	$main .= "<select name='sel_curr_seme' onchange=location.href=this.options[this.selectedIndex].value;>\n";
	while(list($tttkey,$tttvalue)= each ($class_seme_p)) {
		if ( $tttkey == $curr_seme )	  
			 $main .= "<option value=$_SERVER[PHP_SELF]?curr_seme=$tttkey selected>".$tttvalue."</option>\n";
		else
			 $main .= "<option value=$_SERVER[PHP_SELF]?curr_seme=$tttkey>".$tttvalue."</option>\n";
	} 
	$main .= "</select><input type=hidden name='curr_seme' value='$curr_seme'>\n";
	//列出年級別選單
	$school_year_p = year_base($_REQUEST[curr_year]);
	$main .= "\n<select name='sel_curr_school_year' onchange=location.href=this.options[this.selectedIndex].value;>";
	$main .= "<option value=''>請選擇年級</option>\n";
	while(list($ttkey,$ttvalue)= each ($school_year_p)) {
		if ( $ttkey == $year )	  
			 $main .= "<option value=$_SERVER[PHP_SELF]?curr_seme=$curr_seme&year=$ttkey selected>".$ttvalue."級</option>\n";
		else
			 $main .= "<option value=$_SERVER[PHP_SELF]?curr_seme=$curr_seme&year=$ttkey>".$ttvalue."級</option>\n";
	} 
	$main .= "</select><input type=hidden name=curr_school_year value='$year'> OR \n";
	//列出班級選單
	$class_year_p = class_base($_REQUEST[curr_seme]);
	$main .= "<select name='sel_curr_class_year' onchange=location.href=this.options[this.selectedIndex].value;>\n";
	$main .= "<option value=''>請選擇班級</option>\n";
	while(list($tkey,$tvalue)= each ($class_year_p)) {
		if ( $tkey == $class )	  
			 $main .= "<option value=$_SERVER[PHP_SELF]?curr_seme=$curr_seme&class=$tkey selected>".$tvalue."</option>\n";
		else
			 $main .= "<option value=$_SERVER[PHP_SELF]?curr_seme=$curr_seme&class=$tkey>".$tvalue."</option>\n";
	}             	 
	$main .= "</select>\n";
	$main .= "<input type=hidden name=curr_class_year value='$class'><input type=submit name='do_key' value='CSV 輸出'>\n";
	//輸出選單
	$parent_sel="家長：".menu_sel($parent_arr,"parent",$parent);
	$phone_sel="電話：".menu_sel($phone_arr,"phone",$phone);
	$address_sel="住址：".menu_sel($address_arr,"address",$address);
	$main .= "輸出欄位： ".$area_sel.$parent_sel.$phone_sel.$address_sel;

	echo $main;
	
	*/
	
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
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class>$s and b.seme_class<$e and a.stud_study_cond in (0,5) order by b.seme_class, b.seme_num";
	$result = $CONN->Execute($query)or die($query);

	$i=1;
	$list .= "<table border='0' width='100%' style='font-size:12px;' bgcolor='#C0C0C0' cellpadding='3' cellspacing='1'>";
	$list .= "<tr bgcolor='#FFFFCC' align='center'><td>考<br>區<br>代<br>碼<td>學<br>校<br>代<br>碼<td>報<br>名<br>序<br>號<td>學<br>號<td>班<br>級<td>座<br>號<td>學<br>生<br>姓<br>名<td>身<br>分<br>證<br>號<td>性<br>別<td>出<br>生<br>年<td>出<br>生<br>月
				<td>出<br>生<br>日<td>畢<br>業<br>學<br>校<td>畢<br>業<br>年<br>度<td>畢<br>肄<br>業<td>學<br>生<br>身<br>分<td>身<br>心<br>障<br>礙<td>分<br>發<br>區<td>低<br>收<br>入<br>戶<td>失<br>業<br>給<br>付<br>者<td>資<br>料<br>授<br>權
				<td>家<br>長<br>姓<br>名<td>電<br>話<td>郵<br>遞<br>區<br>號<td>地<br>址</td></tr><tr bgcolor='#FFFFFF'>";
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
		$s_addres = $result->fields['stud_addr_2'];
		//判斷電話
		if($phone==1)$s_phone=$s_register_phone;
		if($phone==2)$s_phone=$s_home_phone;
		if($phone==3)$s_phone=$s_offical_phone;
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
		//判斷住址
		if($address==1)$s_address=$s_addres_1;
		if($address==2)$s_address=$s_addres_2;
		//查詢父母姓名
		$query2 = "select fath_name,moth_name,guardian_name from stud_domicile where student_sn ='$student_sn'";
		$result2 = $CONN->Execute($query2)or die ($query2) ;
		$fath_name = $result2->fields['fath_name'];
		$moth_name = $result2->fields['moth_name'];
		$guardian_name = $result2->fields['guardian_name'];
		$birth = explode("-" , $stud_birthday);//出生日期
		$birth[0]=$birth[0]-1911;//轉換成民國
		$curr_class_num_c=substr($curr_class_num,1,2); //取班別
		$curr_class_num_n=substr($curr_class_num,-2); //取座號
		$now_curr_seme=substr($_REQUEST['curr_seme'],0,3); //取學年度
		$over_curr_seme = $now_curr_seme+1;
		$arr = array("<td>".$area,$sch_id,$i,$stud_id,$curr_class_num_c,$curr_class_num_n,$stud_name,$stud_person_id,$stud_sex,$birth[0],$birth[1],$birth[2],$sch_id,$over_curr_seme,1,0,0,$area,0,0,1,$parent_name,$s_phone,$addr_zip,$s_addres);
	
		$data[] = implode("<td>", $arr);
		$result->MoveNext();
		$i++;
	}
	
	$list .= implode("<tr bgcolor=#FFFFFF>", $data);
	$list .= "</table>";
	echo $list;

?>

</td>
</tr>
<tr>
<td >

</td>
</tr>
</table>
</td></tr></table>

<?php foot() ?>
