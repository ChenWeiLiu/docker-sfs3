<?php

// $Id: act_stu_data.php 7707 2013-10-23 12:13:23Z smallduh $

// --系統設定檔
include "act_data_config.php";

//--認證 session
sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//取得目前學期
$curr_seme =  curr_seme();
//-----------------------------------

if ($do_key =="CSV 輸出" OR $do_key =="XLS 輸出") {	
	
	if($curr_school_year and $do_key =="XLS 輸出")$filename = "year_".$curr_school_year.".xls";
	if($curr_class_year and $do_key =="XLS 輸出")$filename = "class".$curr_class_year.".xls";
	if($curr_school_year and $do_key =="CSV 輸出")$filename = "year_".$curr_school_year.".csv";
	if($curr_class_year and $do_key =="CSV 輸出")$filename = "class".$curr_class_year.".csv";
	
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream; Charset=Big5");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	
	if($do_key =="XLS 輸出")
		$ma .= "<table border=1>\n<tr><td>代號</td><td>姓名</td><td>性別</td><td>入學年</td><td>班級</td><td>座號</td><td>生日(西元)</td><td>身分證字號</td><td>父親姓名</td><td>母親姓名</td><td>郵遞區號</td><td>電話</td><td>住址</td><td>緊急聯絡方式</td></tr>\n";
	if($do_key =="CSV 輸出")
		$ma .= "代號,姓名,性別,入學年,班級,座號,生日(西元),身分證字號,父親姓名,母親姓名,郵遞區號,電話,住址,緊急聯絡方式\n";

	$s = $curr_school_year*100;
	$e = ($curr_school_year+1)*100;
	if($curr_class_year!="" and $curr_school_year=="")
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class='$_REQUEST[curr_class_year]'  and a.stud_study_cond in (0,5) order by b.seme_num ";
	if($curr_class_year=="" and $curr_school_year!="")
		$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class like '$curr_school_year%' and a.stud_study_cond in (0,5) order by b.seme_class";
		//$query = "select a.* from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_year_seme='$_REQUEST[curr_seme]' and b.seme_class>'$s' and b.seme_class<'$e' and a.stud_study_cond in (0,5) order by b.seme_class";
	$result = $CONN->Execute($query)or die($query);

	while (!$result->EOF) {
		$stud_id = $result->fields['stud_id'];
		$s_home_phone = $result->fields['stud_tel_2'];
		$s_offical_phone = $result->fields['stud_tel_3'];
		$stud_sex = $result->fields['stud_sex'];
		$stud_name = $result->fields['stud_name'];
		$curr_class_num = $result->fields['curr_class_num'];
		$stud_birthday = $result->fields['stud_birthday'];
		$stud_person_id = $result->fields['stud_person_id'];
		$stud_study_year = $result->fields['stud_study_year'];
		$addr_zip = $result->fields['addr_zip'];
		//$s_addres = $result->fields['stud_addr_2'];
		$addr = change_addr(addslashes($result->fields[stud_addr_2]),1);
		$s_addres = "";
		for ($i=2;$i<=12;$i++) $s_addres .= $addr[$i];
		$query2 = "select fath_name,moth_name from stud_domicile where stud_id ='$stud_id'";
		$result2 = $CONN->Execute($query2)or die ($query2) ;
		$fath_name = $result2->fields['fath_name'];
		$moth_name = $result2->fields['moth_name'];
		
		if($do_key =="XLS 輸出") $stud_num ="<tr><td>".$stud_id;
		if($do_key =="CSV 輸出") $stud_num = $stud_id;
		
		$arr = array($stud_num,$stud_name,$stud_sex,$stud_study_year,substr($curr_class_num,1,2),substr($curr_class_num,-2),$stud_birthday,$stud_person_id,$fath_name,$moth_name,$addr_zip,$s_home_phone,$s_addres,$s_offical_phone); 
			
		if($do_key =="XLS 輸出") $data[] = implode("<td>", $arr);
		if($do_key =="CSV 輸出") $data[] = implode(",", $arr);
		
		$result->MoveNext();
	}
	if($do_key =="XLS 輸出") $ma .= implode("</td>", $data);
	if($do_key =="CSV 輸出") $ma .= implode("\n", $data);
	if($do_key =="XLS 輸出") $ma .= "</table>";
	
	echo $ma;
	exit;
}


//印出檔頭
head("匯出健康中心資料");
print_menu($menu_p);

?>

<table border="0" width="100%" cellspacing="0" cellpadding="0" >
<tr><td valign=top bgcolor="#CCCCCC">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
<tr><td>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" name="pform">

<?php
	
	//列出學年度別
	if ($_REQUEST[curr_seme]=='')$_REQUEST[curr_seme] = sprintf("%03d%d",curr_year(),curr_seme());
	$class_seme_p = get_class_seme(); //學年度
	$sel = new drop_select();
	$sel->s_name= "curr_seme";
	$sel->has_empty = false;
	$sel->is_submit = true;
	$sel->arr = $class_seme_p;
	$sel->id = $_REQUEST[curr_seme];
	$sel->do_select();

	//列出年級別
	$school_year_p = year_base($_REQUEST[curr_year]);
	$main  = "<select name='curr_school_year'>";
	$main .= "<option value=''>請選擇年級</option>\n";
	while(list($ttkey,$ttvalue)= each ($school_year_p)) {
		if ($ttkey == $curr_school_year)	  
			 $main .= sprintf ("<option value=\"%s\" selected> %s級 </option>\n",$ttkey,$ttvalue);
		else
			 $main .= sprintf ("<option value=\"%s\"> %s級 </option>\n",$ttkey,$ttvalue);
	}             	 
	$main .= "</select>";
	
	//列出班級
	$class_year_p = class_base($_REQUEST[curr_seme]);
	$main .= "<select name='curr_class_year'>";
	$main .= "<option value=''>請選擇班級</option>\n";
	while(list($tkey,$tvalue)= each ($class_year_p)) {
		if ($tkey == $curr_class_year)	  
			 $main .= sprintf ("<option value=\"%s\" selected>%s</option>\n",$tkey,$tvalue);
		else
			 $main .= sprintf ("<option value=\"%s\">%s</option>\n",$tkey,$tvalue);
	}             	 
	$main .= "</select><p>";
	$main .= " 附註： 年級 ＆ 班級只能選一項下載！！";
	echo $main;
?>

</td>
<td width=65% rowspan="2" valign=top >
<p><b><font size="4">萬豐版資料轉出說明</font></b></p>
<p>萬豐版國民小學健康檢查資訊處理系統 為 台中縣萬豐國小張文育老師所開發，詳細說明請至下列網址查看<BR>
<a href= "http://health.wfes.tcc.edu.tw/">http://health.wfes.tcc.edu.tw/</a>

</td>
</tr>
<tr>
<td >
<input type=submit name="do_key" value="CSV 輸出">
<input type=submit name="do_key" value="XLS 輸出">
</td>
</tr>
</table>
</td></tr></table>

<?php foot() ?>
