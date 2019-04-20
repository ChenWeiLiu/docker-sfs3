<?php
// $Id: upgrade2-3.php 5310 2009-01-10 07:57:56Z hami $
include "../include/config.php";
require_once "../include/sfs_core_globals.php";
include "../include/sfs_case_PLlib.php";
include "update_function.php";
set_time_limit(180) ;

//密碼有誤
if(!$testCONN = mysql_pconnect ("$mysql_host","$mysql_user","$mysql_pass")){
	$str =read_file("pass_error.htm");
	echo $str;
	exit;
}
//未先建立 sfs3 資料庫
else if (!mysql_select_db($mysql_db,$testCONN)){
	$str =read_file("yet_create_newdb.htm");
	echo $str;
	exit;
}
//已升級
else if(check_field($mysql_db,$conID,'teacher_base','teacher_sn')){
	$str =read_file("has_import_newdb.htm");
	echo $str;
	exit;
}


else if(!mysql_query ("show columns from stud_base",$testCONN)){
	$str =read_file("yet_import_newdb.htm");
	echo $str;
	exit;
}

else if ($_POST[do_it] <>'執行升級') {
	include "do_memo.htm";
	exit;
}	

$btime = utime();
$sql_query = read_file("./3.0/stud_base_temp.sql");

if ($sql_query != '') {
    $pieces       = array();
    PMA_splitSqlFile_2($pieces, $sql_query, PMA_MYSQL_INT_VERSION);
    $pieces_count = count($pieces);

    // Copy of the cleaned sql statement for display purpose only (see near the
    // beginning of "db_details.php" & "tbl_properties.php")
    if ($sql_file != 'none' && $pieces_count > 10) {
         // Be nice with bandwidth...
       $sql_query_cpy = $sql_query = '';
    } else {
        $sql_query_cpy = implode(";\n", $pieces) . ';';
    }

    // Only one query to run
    if ($pieces_count == 1 && !empty($pieces[0]) ) {
        // sql.php will stripslash the query if get_magic_quotes_gpc
        if (get_magic_quotes_gpc() == 1) {
            $sql_query = addslashes($pieces[0]);
        } else {
            $sql_query = $pieces[0];
        }
        if (eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $sql_query)) {
            $reload = 1;
        }
       // include('./sql.php');
        exit();
    }

    // Runs multiple queries
    else  {
        for ($i = 0; $i < $pieces_count; $i++) {
            $a_sql_query = $pieces[$i];
            $result = mysql_query($a_sql_query,$testCONN);
            if ($result == FALSE) { // readdump failed
                $my_die = $a_sql_query;
		echo "錯誤的語法 :$my_die ";
                //break;
            }
            if (!isset($reload) && eregi('^(DROP|CREATE)[[:space:]]+(IF EXISTS[[:space:]]+)?(TABLE|DATABASE)[[:space:]]+(.+)', $a_sql_query)) {
                $reload = 1;
            }
        } // end for
    } // end else if
    unset($pieces);
} // end if



//資料改變
if (check_field ($mysql_db,$conID,'stud_domicile','addr_id')) {
	mysql_query ("ALTER TABLE `stud_domicile` DROP `addr_id`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `fath_country`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `fath_abroad`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `fath_note`");
	
	mysql_query ("ALTER TABLE `stud_domicile` DROP `moth_country`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `moth_abroad`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `moth_note`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `is_same_gua`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `grandfath_birthyear`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP `grandmoth_birthyear`");
	mysql_query ("ALTER TABLE `stud_domicile` DROP PRIMARY KEY, ADD PRIMARY KEY(`stud_id`) ");
	
}

//更改 stud_seme 資料表結構

mysql_query("ALTER TABLE `stud_seme` DROP `score_total`, DROP `score_total_t`, DROP `comment`, DROP `seme_cadre`, DROP `assist_total`, DROP `absen_thing`, DROP `absen_sick`, DROP `absen_none`");
mysql_query("ALTER TABLE `stud_seme` ADD `seme_class_year_s` INT NOT NULL AFTER `seme_num`");


//學生資料轉換
$query = "select * from stud_base order by stud_id";
$recordSet = $CONN->Execute($query);
while (!$recordSet->EOF) {

	$stud_id = $recordSet->fields["stud_id"];
	$stud_name = $recordSet->fields["stud_name"];
	$stud_person_id = $recordSet->fields["stud_person_id"];
	$stud_country = $recordSet->fields["stud_country"];
	$stud_abroad = $recordSet->fields["stud_abroad"];
	$addr_id = $recordSet->fields["addr_id"];
	$stud_birthday = $recordSet->fields["stud_birthday"];
	$stud_sex = $recordSet->fields["stud_sex"];
	$stud_blood_type = $recordSet->fields["stud_blood_type"];
	$stud_study_cond = $recordSet->fields["stud_study_cond"];
	$stud_study_year = $recordSet->fields["stud_study_year"];
	$condition = $recordSet->fields["condition"];
	$stud_row = $recordSet->fields["stud_row"];
	$sister_brother = $recordSet->fields["sister_brother"];
	$email_pass = $recordSet->fields["email_pass"];
	$create_date = $recordSet->fields["create_date"];
	$stud_kind = $recordSet->fields["stud_kind"];
	$stud_class_kind = $recordSet->fields["stud_class_kind"];
	$stud_spe_kind = $recordSet->fields["stud_spe_kind"];
	$stud_spe_class_kind = $recordSet->fields["stud_spe_class_kind"];
	$stud_preschool_id = $recordSet->fields["stud_preschool_id"];
	$stud_preschool_name = $recordSet->fields["stud_preschool_name"];
	$stud_preschool_status = $recordSet->fields["stud_preschool_status"];
	$stud_hospital = $recordSet->fields["stud_hospital"];
	$stud_graduate_kind = $recordSet->fields["stud_graduate_kind"];
	$stud_graduate_date = $recordSet->fields["stud_graduate_date"];
	$stud_graduate_word = $recordSet->fields["stud_graduate_word"];
	$stud_graduate_num = $recordSet->fields["stud_graduate_num"];
	$stud_graduate_school = $recordSet->fields["stud_graduate_school"];
	$class_num_1 = $recordSet->fields["class_num_1"];
	$class_num_2 = $recordSet->fields["class_num_2"];
	$class_num_3 = $recordSet->fields["class_num_3"];
	$class_num_4 = $recordSet->fields["class_num_4"];
	$class_num_5 = $recordSet->fields["class_num_5"];
	$class_num_6 = $recordSet->fields["class_num_6"];
	$class_num_7 = $recordSet->fields["class_num_7"];
	$class_num_8 = $recordSet->fields["class_num_8"];
	$class_num_9 = $recordSet->fields["class_num_9"];
	$class_num_10 = $recordSet->fields["class_num_10"];
	$class_num_11 = $recordSet->fields["class_num_11"];
	$class_num_12 = $recordSet->fields["class_num_12"];
	$update_id = $recordSet->fields["update_id"];
	$update_time = $recordSet->fields["update_time"];
	$curr_class_num = $recordSet->fields["curr_class_num"];
	
	
	$sql_select = "select addr_id,stud_id,stud_addr_h_a,stud_addr_h_b,stud_addr_h_c,stud_addr_h_d,stud_addr_h_e,stud_addr_h_f,stud_addr_h_g,stud_addr_h_h,stud_addr_h_i,stud_addr_h_j,stud_addr_h_k,stud_addr_h_l,stud_phone_h,stud_handphone_h from stud_addr where stud_id='$stud_id'";
	$recordSet2 = $CONN->Execute($sql_select);
	$stud_addr_a = $recordSet2->fields["stud_addr_h_a"];
	$stud_addr_b = $recordSet2->fields["stud_addr_h_b"];
	$stud_addr_c = $recordSet2->fields["stud_addr_h_c"];
	$stud_addr_d = $recordSet2->fields["stud_addr_h_d"];
	$stud_addr_e = $recordSet2->fields["stud_addr_h_e"];
	$stud_addr_f = $recordSet2->fields["stud_addr_h_f"];
	$stud_addr_g = $recordSet2->fields["stud_addr_h_g"];
	$stud_addr_h = $recordSet2->fields["stud_addr_h_h"];
	$stud_addr_i = $recordSet2->fields["stud_addr_h_i"];
	$stud_addr_j = $recordSet2->fields["stud_addr_h_j"];
	$stud_addr_k = $recordSet2->fields["stud_addr_h_k"];
	$stud_addr_l = $recordSet2->fields["stud_addr_h_l"];
	$stud_tel_1 = $recordSet2->fields["stud_phone_h"];
	$stud_tel_2 = $recordSet2->fields["stud_phone_h"];
	$stud_tel_3 = $recordSet2->fields["stud_handphone_h"];
	if ($stud_addr_d!='') $stud_addr_d .="鄰";
	if ($stud_addr_f!='') $stud_addr_f .="段";
	if ($stud_addr_g!='') $stud_addr_g .="巷";
	if ($stud_addr_h!='') $stud_addr_h .="弄";
	if ($stud_addr_i!='') $stud_addr_i .="號";
	
	$stud_addr_1 = $stud_addr_a .$stud_addr_b.$stud_addr_c.$stud_addr_d.$stud_addr_e.$stud_addr_f.$stud_addr_g.$stud_addr_h.$stud_addr_i.$stud_addr_j.$stud_addr_k.$stud_addr_l;
	
	
	
	$sql_insert = "insert into stud_base_temp (stud_id,stud_name,stud_sex,stud_birthday,stud_blood_type,stud_birth_place,stud_kind,stud_country,stud_country_kind,stud_person_id,stud_country_name,stud_addr_1,stud_addr_2,stud_tel_1,stud_tel_2,stud_tel_3,stud_mail,stud_addr_a,stud_addr_b,stud_addr_c,stud_addr_d,stud_addr_e,stud_addr_f,stud_addr_g,stud_addr_h,stud_addr_i,stud_addr_j,stud_addr_k,stud_addr_l,stud_addr_m,stud_class_kind,stud_spe_kind,stud_spe_class_kind,stud_spe_class_id,stud_preschool_status,stud_preschool_id,stud_preschool_name,stud_mschool_id,stud_mschool_name,stud_study_year,curr_class_num,stud_study_cond,email_pass) values ('$stud_id','$stud_name','$stud_sex','$stud_birthday','$stud_blood_type','$stud_birth_place','$stud_kind','$stud_country','$stud_country_kind','$stud_person_id','$stud_country_name','$stud_addr_1','$stud_addr_2','$stud_tel_1','$stud_tel_3','$stud_tel_2','$stud_mail','$stud_addr_a','$stud_addr_b','$stud_addr_c','$stud_addr_d','$stud_addr_e','$stud_addr_f','$stud_addr_g','$stud_addr_h','$stud_addr_i','$stud_addr_j','$stud_addr_k','$stud_addr_l','$stud_addr_m','$stud_class_kind','$stud_spe_kind','$stud_spe_class_kind','$stud_spe_class_id','$stud_preschool_status','$stud_preschool_id','$stud_preschool_name','$stud_mschool_id','$stud_mschool_name','$stud_study_year','$curr_class_num','$stud_study_cond','$email_pass')";
$sql_insert = str_replace("\\","\\\\",$sql_insert);

	$CONN->Execute($sql_insert) or die($sql_insert);

	
	//轉入學期資料
	$stud_temp = substr($stud_id,0,2);
	$temp_year =curr_year();			
	for($numi=0;$numi<($temp_year-$stud_temp+1);$numi++){
		$class_temp1 = "class_num_".(($numi)*2+1);
		$class_temp2 = "class_num_".(($numi+1)*2);				
				
		if (substr($$class_temp1,-1) !="") {//第一學期
			$seme_year_seme= sprintf("%03d%d",$stud_temp+$numi,1);
			$seme_class = substr($$class_temp1,0,3);
			$seme_class_name = $class_name [substr($$class_temp1,1,2)];
			$seme_num = substr($$class_temp1,-2);
			$sql_insert = "replace into stud_seme (stud_id,seme_year_seme,seme_class,seme_class_name,seme_num) values ('$stud_id','$seme_year_seme','$seme_class','$seme_class_name','$seme_num')";
			$CONN->Execute($sql_insert) or die ($sql_insert);
		}
		if (substr($$class_temp2,-1) !="") { //第二學期
			$seme_year_seme= sprintf("%03d%d",$stud_temp+$numi,2);
			$seme_class = substr($$class_temp2,0,3);
			$seme_class_name = $class_name [substr($$class_temp2,1,2)];
			$seme_num = substr($$class_temp2,-2);
			$sql_insert = "replace into stud_seme (stud_id,seme_year_seme,seme_class,seme_class_name,seme_num) values ('$stud_id','$seme_year_seme','$seme_class','$seme_class_name','$seme_num')";
			$CONN->Execute($sql_insert) or die ($sql_insert);

		}
	}
	
	$recordSet->MoveNext();
};

mysql_query("drop table if exists stud_base ");
mysql_query("ALTER TABLE `stud_base_temp` RENAME `stud_base` ");

//改變班級記錄
$temp_arr = "class_name_kind_$class_kind";
$c_name_arr = $$temp_arr;
$query = "select * from school_class_num order by curr_class_year,c_year";
$res = $CONN->Execute($query);
while (!$res->EOF) {
	$c_num = $res->fields[c_num];
	$c_year = $res->fields[c_year];
	$curr_class_year = $res->fields[curr_class_year];
	$class_year = substr($curr_class_year,0,3);
	$semester = substr($curr_class_year,-1);
	for($i=1;$i<=$c_num;$i++) {
		$class_id = sprintf("%03s_%s_%02s_%02s",$class_year,$semester,$c_year,$i);
		 $sql_insert = "insert into school_class (class_id,year,semester,c_year,c_sort,enable,c_name) values ('$class_id',$class_year,'$semester','$c_year',$i,'1','$c_name_arr[$i]')";
		$CONN->Execute($sql_insert) or die($sql_insert);
	}

	$res->MoveNext();
}
//更改 teacher_sn 的值
//教師任職資料:
up_teacher_sn("teacher_post");
up_teacher_sn("teacher_connect");

//更改權限
//$query = " update pro_kind set store_path= CONCAT('administrator',substring(store_path,6))  where store_path like 'admin%'";
//$CONN->Execute($query) or die ($query);
$query = "select a.* from pro_check a ,pro_kind b where a.pro_kind_id=b.pro_kind_id and b.store_path like 'admin%'";
$res = $CONN->Execute($query) or trigger_error("升級失敗!!",E_USER_ERROR);
while(!$res->EOF) {
	$pro_kind_id = $res->fields[pro_kind_id];
        $teach_title_id = $res->fields[teach_title_id];
        $post_office = $res->fields[post_office];
        $teach_id = $res->fields[teach_id];
        $is_admin = $res->fields[is_admin];
	if ($post_office<>'' and $post_office<>0)
		 $CONN->Execute("INSERT INTO pro_check_new VALUES ('', 1, '處室',$post_office, '$is_admin')") or trigger_error("處室新增錯誤",E_USER_ERROR);
	if ($teach_title_id<>'' and $teach_title_id<>0)
		$CONN->Execute("INSERT INTO pro_check_new VALUES ('', 1, '職稱',$teach_title_id, '$is_admin')") or trigger_error("處室新增錯誤",E_USER_ERROR);

	if ($teach_id<>'') {
		$query = "select teacher_sn from teacher_base where teach_id='$teach_id'";
		$res2 = $CONN->Execute($query) or trigger_error("SQL 錯誤<br>$query",E_USER_ERROR);
		$teacher_sn = $res2->fields[0];
		$CONN->Execute("INSERT INTO pro_check_new VALUES ('', 1, '教師',$teacher_sn, '$is_admin')") or trigger_error("處室新增錯誤",E_USER_ERROR);
	}

	$res->MoveNext();
}

$CONN->Execute("ALTER TABLE `stud_seme` CHANGE `seme_class_year_s` `seme_class_year_s` INT UNSIGNED DEFAULT '0' NOT NULL");

// 加入系統選項函式

include "join_sfstest.php";

 $del = 0;
        if($delkey=="刪除記錄")
                $del = 1;

        //檢查不存在的目錄
        $err_path = check_err_set($del);

        if (count($err_path)>0){
		echo "找出錯誤的目錄設定共 ".count($err_path)." 筆記錄<HR>\n";
                foreach($err_path as $temp)
			echo " $temp <br>";
	}

//重新整理程式
reset_pro(0);

$etime = utime();
$runtime = ($etime - $btime) ;
echo "Completed in $runtime miliseconds<BR>\n";
?>
<script>
if(confirm('您已經完成系統升級動作 \n按確定鍵進行其他模組升級')){
	window.location.href='<?php echo $SFS_PATH_HTML."upgrade/up_module.php"; ?>';
	}
</script>

