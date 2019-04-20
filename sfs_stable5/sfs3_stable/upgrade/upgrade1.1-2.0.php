<?php	
// $Id: upgrade1.1-2.0.php 6818 2012-06-22 08:28:22Z smallduh $
	// 升級時去除下列選項
	//---------------
	echo "<h1>請參考 ".$_SERVER['PHP_SELF'].' 程式碼說明</h1>';
	exit;
	//--------------
	
	$UPDATE_MODE = 1.1;
	/***
	session_start();
	session_register("session_mysql_host");
	session_register("session_mysql_user");
	session_register("session_mysql_db");
	session_register("session_mysql_password");
	session_register("session_sfs_path");		
	***/
	include "../include/config.php";
	include "update_function.php";
	if ($dostep != "4")
		include "header.php";
	if (!isset($step))
		$step = 1 ;

	if ($dostep > 1) {
		$conID = mysql_pconnect ("$session_mysql_host","$session_mysql_user","$session_mysql_password");		
		$conID2 = mysql_pconnect ("$mysql_host","$mysql_user","$mysql_pass");
		$new_mysql_db = $mysql_db;
	}
	//第一步 	
	if ($dostep == "1") {
	
		if (!file_exists("$sfs_path/include/config.php") ) {
			echo "$sfs_path 目錄不存在!! 請檢查!!";
			exit;
		}
		$checkID = @mysql_connect ("$host","$user","$password");	
		@mysql_select_db($database,$checkID);
		if (!$checkID) {
			echo "<center><b><h3>失敗!!<br>檢查mysql的設定是否正確?!<b></h3></center>";
			exit;
		}
		$session_mysql_user = $user;
		$session_mysql_password = $password ;
		$session_mysql_host = $host ;
		$session_mysql_db = $database;
		$session_sfs_path = $sfs_path;		
		
		echo "<h3>登入成功!!</h3>";
		echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"1; url=$PHP_SELF?step=2#this_step2\">";
		exit;
	}	
	
	//第二步 	
	else if ($dostep == "2") {
			
		$sql_fd = fopen("sfs2.sql", "r");   
 		while ($buffer = fgets($sql_fd, 4096))   
 		   $create_sql .= $buffer;
 		fclose($sql_fd);
 		$pieces = split_sql($create_sql);
 		for ($i=0; $i<count($pieces); $i++)
		{
    			$pieces[$i] = stripslashes(trim($pieces[$i]));
    			if(!empty($pieces[$i]) && $pieces[$i] != "#")
    				{
        				$result = mysql_db_query ($new_mysql_db, $pieces[$i],$conID2) or die($pieces[$i]);
    				}
		}
 				
		include "$session_sfs_path/include/config.php";
		showtitle("轉換處室中");
		$query = "delete from  school_room ";
			mysql_db_query($new_mysql_db,$query,$conID2)or die ("err: 處室資料");
		while (list($tid, $tname) = each($post_office_p)){
			if (substr($tname,0,1)=='*')
				$tname = substr($tname,1);			
			$query = "insert into school_room values('$tid','$tname','','')";
			mysql_db_query($new_mysql_db,$query,$conID2) or die ("新增錯誤： $query");
			$iii++;
		};
		echo "計轉入 $iii 筆資料!<br>";
		
		showtitle("轉換學生資料中");		
		$result_o = mysql_db_query($mysql_db,"select * from stud_base",$conID) or die("錯誤:轉換學生資料中");
		while ($row = mysql_fetch_array($result_o)) {
			$stud_id = ltrim($row["stud_id"]);
			$stud_name = addcslashes($row["stud_name"],"'");
			$stud_person_id = $row["stud_person_id"];
			$stud_birthday = $row["stud_birthday"];
			$stud_sex = $row["stud_sex"];
			$stud_birthplace = $row["stud_birthplace"];
			$stud_blood_type = $row["stud_blood_type"];
			$stud_phone_h = $row["stud_home_phone"];
			$stud_phone_c = $row["stud_offical_phone"];
			$stud_domicile_addres = $row["stud_domicile_addres"];
			$stud_inhabit_address = $row["stud_inhabit_address"];
			$stud_study_cond = $row["stud_study_cond"];
			$stud_graduate_num = $row["stud_graduate_num"];
			$condition = $row["condition"];
			$stud_row = $row["stud_row"];
			$sister_brother = $row["sister_brother"];
			$class_num_1 = $row["class_num_1"];
			$class_num_2 = $row["class_num_2"];
			$class_num_3 = $row["class_num_3"];
			$class_num_4 = $row["class_num_4"];
			$class_num_5 = $row["class_num_5"];
			$class_num_6 = $row["class_num_6"];
			$class_num_7 = $row["class_num_7"];
			$class_num_8 = $row["class_num_8"];
			$class_num_9 = $row["class_num_9"];
			$class_num_10 = $row["class_num_10"];
			$class_num_11 = $row["class_num_11"];
			$class_num_12 = $row["class_num_12"];	
			$curr_class_num = $row["curr_class_num"];
			$email_pass = $row["email_pass"];
			if ($row["create_date"]== 0)
				$create_date = date("Y-m-d");
			else
				$create_date = $row["create_date"];		
			$stud_graduate_school = $row["stud_graduate_school"];
			
			//轉換住址
			$addr = $row[stud_inhabit_address];
			$res = change_addr($addr);
					
			$stud_addr_h_a = $res[0];
			if ($stud_addr_h_a =="")
				$stud_addr_h_a = $default_sheng ;
				
			$stud_addr_h_b = $res[1];
			if ($stud_addr_h_b =="")
				$stud_addr_h_b = $default_coun;
				
			$stud_addr_h_c = $res[2];
			$stud_addr_h_d = $res[3];
			$stud_addr_h_e = $res[4];
			$stud_addr_h_f = $res[5];
			$stud_addr_h_g = $res[6];
			$stud_addr_h_h = $res[7];
			$stud_addr_h_j = $res[8];
			$stud_addr_h_i = $res[9];
			$stud_addr_h_m = $res[10];
			$stud_addr_h_l = $res[11];
			$addr_id++;
			$sql_insert = "insert into stud_addr (addr_id,stud_id,stud_addr_h_a,stud_addr_h_b,stud_addr_h_c,stud_addr_h_d,stud_addr_h_e,stud_addr_h_f,stud_addr_h_g,stud_addr_h_h,stud_addr_h_i,stud_addr_h_j,stud_addr_h_k,stud_addr_h_l,stud_phone_h,stud_handphone_h,stud_addr_c_a,stud_addr_c_b,stud_addr_c_c,stud_addr_c_d,stud_addr_c_e,stud_addr_c_f,stud_addr_c_g,stud_addr_c_h,stud_addr_c_i,stud_addr_c_j,stud_addr_c_k,stud_addr_c_l,stud_phone_c,stud_handphone_c) values ($addr_id,'$stud_id','$stud_addr_h_a','$stud_addr_h_b','$stud_addr_h_c','$stud_addr_h_d','$stud_addr_h_e','$stud_addr_h_f','$stud_addr_h_g','$stud_addr_h_h','$stud_addr_h_i','$stud_addr_h_j','$stud_addr_h_k','$stud_addr_h_l','$stud_phone_h','$stud_handphone_h','$stud_addr_c_a','$stud_addr_c_b','$stud_addr_c_c','$stud_addr_c_d','$stud_addr_c_e','$stud_addr_c_f','$stud_addr_c_g','$stud_addr_c_h','$stud_addr_c_i','$stud_addr_c_j','$stud_addr_c_k','$stud_addr_c_l','$stud_phone_c','$stud_handphone_c')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
					
			$sql_insert = "replace into stud_base (stud_id,stud_name,stud_person_id,stud_country,stud_abroad,addr_id,stud_birthday,stud_sex,stud_blood_type,stud_study_cond,stud_study_year,condition,stud_row,sister_brother,email_pass,create_date,stud_kind,stud_class_kind,stud_spe_kind,stud_spe_class_kind,stud_preschool_id,stud_preschool_name,stud_preschool_status,stud_hospital,stud_graduate_kind,stud_graduate_date,stud_graduate_word,stud_graduate_num,stud_graduate_school,class_num_1,class_num_2,class_num_3,class_num_4,class_num_5,class_num_6,class_num_7,class_num_8,class_num_9,class_num_10,class_num_11,class_num_12,update_id,curr_class_num) values ('$stud_id','$stud_name ','$stud_person_id','$stud_country','$stud_abroad','$addr_id','$stud_birthday','$stud_sex','$stud_blood_type','$stud_study_cond','$stud_study_year','$condition','$stud_row','$sister_brother','$email_pass','$create_date','$stud_kind','$stud_class_kind','$stud_spe_kind','$stud_spe_class_kind','$stud_preschool_id','$stud_preschool_name','$stud_preschool_status','$stud_hospital','$stud_graduate_kind','$stud_graduate_date','$stud_graduate_word','$stud_graduate_num','$stud_graduate_school','$class_num_1','$class_num_2','$class_num_3','$class_num_4','$class_num_5','$class_num_6','$class_num_7','$class_num_8','$class_num_9','$class_num_10','$class_num_11','$class_num_12','$update_id','$curr_class_num')";
			
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			
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
					mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
				}
				if (substr($$class_temp2,-1) !="") { //第二學期
					$seme_year_seme= sprintf("%03d%d",$stud_temp+$numi,2);
					$seme_class = substr($$class_temp2,0,3);
					$seme_class_name = $class_name [substr($$class_temp2,1,2)];
					$seme_num = substr($$class_temp2,-2);
					$sql_insert = "replace into stud_seme (stud_id,seme_year_seme,seme_class,seme_class_name,seme_num) values ('$stud_id','$seme_year_seme','$seme_class','$seme_class_name','$seme_num')";
					mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);

				}
			}
			
			$iii++;
		};
		
		//加入入學年
		$sql_insert = "update stud_base set stud_study_year= substring(ltrim(stud_id),1,2)  ";
		mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
		
		
		//更新學期班級記錄	
		$query = "select seme_year_seme,substring(seme_class,1,1)as cc from stud_seme  group by seme_year_seme,seme_class order by seme_year_seme";
		$result = mysql_db_query($new_mysql_db,$query,$conID2) or die ($query);
			while ($row = mysql_fetch_row($result)) {
				$year = $row[0];			
				$class = $row[1];
				$arr[$year][$class] += 1;
			}			
			if (count($arr)>0) {
				reset($arr);
				while(list($tid1,$arr1)= each ($arr)) {		
					while(list($tid2,$arr2) = each($arr1)) {				
							$curr_class_year = $tid1;
							$c_year = $tid2;
							$c_num = $arr2;
							if ($c_year) {
								$query = "insert into school_class_num (curr_class_year,c_year,c_num)values('$curr_class_year','$c_year','$c_num')";
								mysql_db_query($new_mysql_db,$query,$conID2) or die ($query);
							}
				}
			}
		}
		
		echo "計轉入 $iii 筆資料!<br>";
		
		
		echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"2; url=$PHP_SELF?step=3#this_step3\">";
				
		include "footer.php";
		exit;
	}
	
	//第三步 	
	else if ($dostep == "3") {
		 				
		include "$session_sfs_path/include/config.php";
		$occu_kindp =  occu_kind(); //家長職業
		
		//學生戶口資料
				
		showtitle("轉換學生戶口資料中");		
		$sql_select = "select a.* from stud_domicile a,stud_base b where a.stud_id=b.stud_id ";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die("err: 轉換學生戶口資料中");
		while ($row = mysql_fetch_array($result)) {
			$stud_id = $row["stud_id"];
			$fath_name = addcslashes($row["fath_name"],"'");
			if ($row["fath_birthyear"] !="0000-00-00")
				$fath_birthyear = substr($row["fath_birthyear"],0,4)-1911;
			$fath_alive = $row["fath_alive"];
			$fath_education = "1".$row["fath_education"];
			$fath_occupation = $occu_kindp[$row["fath_occupation"]];
			$fath_unit = addcslashes($row["fath_unit"],"'");
			$fath_work_name = addcslashes($row["fath_work_name"],"'");
			$fath_phone = $row["fath_phone"];
			$fath_note = addcslashes($row["fath_note"],"'");
			$moth_name = addcslashes($row["moth_name"],"'");
			if ($row["moth_birthyear"] !="0000-00-00")
				$moth_birthyear = substr($row["moth_birthyear"],0,4)-1911;
			
			$moth_alive = $row["moth_alive"];
			$moth_education = "1".$row["moth_education"];
			$moth_occupation = $occu_kindp[$row["moth_occupation"]];
			$moth_unit = $row["moth_unit"];
			$moth_work_name = addcslashes($row["moth_work_name"],"'");
			$moth_phone = $row["moth_phone"];
			$moth_note = $row["moth_note"];
			$guardian_name = addcslashes($row["guardian_name"],"'");
			$guardian_phone = $row["guardian_phone"];
			$guardian_address = addcslashes($row["guardian_address"],"'");
			$guardian_native = $row["guardian_native"];
			$guardian_relation = $row["guardian_relation"];
			$grandfath_name = addcslashes($row["grandfath_name"],"'");			
			$grandfath_alive = $row["grandfath_alive"];
			$grandmoth_name = addcslashes($row["grandmoth_name"],"'");			
			$grandmoth_alive = $row["grandmoth_alive"];			
			$update_time = $row["update_time"];			
						
			
			
			$sql_insert = "insert into stud_domicile (addr_id,stud_id,fath_name,fath_birthyear,fath_alive,fath_relation,fath_country,fath_p_id,fath_abroad,fath_education,fath_occupation,fath_unit,fath_work_name,fath_phone,fath_home_phone,fath_hand_phone,fath_email,fath_note,moth_name,moth_birthyear,moth_alive,moth_relation,moth_country,moth_p_id,moth_abroad,moth_education,moth_occupation,moth_unit,moth_work_name,moth_phone,moth_home_phone,moth_hand_phone,moth_email,moth_note,guardian_name,guardian_phone,guardian_address,guardian_relation,guardian_p_id,guardian_unit,guardian_work_name,guardian_hand_phone,guardian_email,grandfath_name,grandfath_alive,grandmoth_name,grandmoth_alive,update_time,update_id) values ('$stud_id','$stud_id','$fath_name ','$fath_birthyear','$fath_alive','$fath_relation','$fath_country','$fath_p_id','$fath_abroad','$fath_education','$fath_occupation','$fath_unit ','$fath_work_name ','$fath_phone','$fath_home_phone','$fath_hand_phone','$fath_email','$fath_note','$moth_name ','$moth_birthyear','$moth_alive','$moth_relation','$moth_country','$moth_p_id','$moth_abroad','$moth_education','$moth_occupation','$moth_unit ','$moth_work_name ','$moth_phone','$moth_home_phone','$moth_hand_phone','$moth_email','$moth_note','$guardian_name ','$guardian_phone','$guardian_address','$guardian_relation','$guardian_p_id','$guardian_unit ','$guardian_work_name ','$guardian_hand_phone','$guardian_email','$grandfath_name ','$grandfath_alive','$grandmoth_name ','$grandmoth_alive','$update_time','$update_id')";					
			
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			
			
			
			// 加入地址資料
			$sql = "select addr_id from stud_base where stud_id='$stud_id'";			
			$result_addr = mysql_db_query($new_mysql_db,$sql,$conID2) or die ($sql);
			$row_addr = mysql_fetch_row($result_addr);
			$sql = "update stud_domicile set addr_id = '$row_addr[0]' where  stud_id='$stud_id'";
			$result_addr = mysql_db_query($new_mysql_db,$sql,$conID2) or die ($sql);
			$iii++;
			
		};
		
		//更改學歷設定 
		//舊設定("1"=>"不識字","2"=>"識字(未就學)","3"=>"小學","4"=>"中學","5"=>"高中職","6"=>"大學","7"=>"碩士","8"=>"博士","9"=>"專科");
		//新設定("1"=>"博士","2"=>"碩士","3"=>"大學","4"=>"專科","5"=>"高中","6"=>"國中","7"=>"國小畢業","8"=>"國小肄業","9"=>"識字(未就學)","10"=>"不識字");
		//改變陣列
		$chgarr = array("11"=>"10","12"=>"9","13"=>"7","14"=>"6","15"=>"5","16"=>"3","17"=>"2","18"=>"1","19"=>"4");
		while (list($old_id,$new_id)= each($chgarr)) {
			$sql = "update  stud_domicile set fath_education='$new_id' where fath_education='$old_id' ";
			mysql_db_query($new_mysql_db,$sql,$conID2) or die ($sql);
			$sql = "update  stud_domicile set moth_education='$new_id' where moth_education='$old_id' ";
			mysql_db_query($new_mysql_db,$sql,$conID2) or die ($sql);
		}
		echo "計轉入 $iii 筆資料!<br>";
		//學生兄弟姐妹資料
		showtitle("轉換學生兄弟姐妹資料中");		
		$sql_select = "select bs_id,stud_id,bs_name,bs_calling,bs_gradu,bs_birthyear from stud_brother_sister";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID) or die ("err:轉換學生兄弟姐妹資料");

		while ($row = mysql_fetch_array($result)) {

			$bs_id = $row["bs_id"];
			$stud_id = $row["stud_id"];
			$bs_name = $row["bs_name"];
			$bs_calling = $row["bs_calling"];
			$bs_gradu = $row["bs_gradu"];
			$bs_birthyear = $row["bs_birthyear"];			
			$bs_name = ereg_replace("'","",$bs_name); 			
			
			$sql_insert = "insert into stud_brother_sister (bs_id,stud_id,bs_name,bs_calling,bs_gradu,bs_birthyear) values ($bs_id,'$stud_id','$bs_name ',$bs_calling,'$bs_gradu',$bs_birthyear)";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換教師資料中");
		$sql_select = "select teach_id,teach_person_id,name,sex,age,birthday,birth_place,marriage,address,home_phone,cell_phone,office_home,teach_condition,teach_memo,login_pass from teacher_base";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID);
		while ($row = mysql_fetch_array($result)) {

			$teach_id = $row["teach_id"];
			$teach_person_id = $row["teach_person_id"];
			$name = addcslashes($row["name"],"'");
			$sex = $row["sex"];
			$age = $row["age"];
			$birthday = $row["birthday"];
			$birth_place = $row["birth_place"];
			$marriage = $row["marriage"];
			$address = addcslashes($row["address"],"'");
			$home_phone = $row["home_phone"];
			$cell_phone = $row["cell_phone"];
			$office_home = $row["office_home"];
			$teach_condition = $row["teach_condition"];
			$teach_memo = addcslashes($row["teach_memo"],"'");
			$login_pass = $row["login_pass"];
			
			$sql_insert = "insert into teacher_base (teach_id,teach_person_id,name,sex,age,birthday,birth_place,marriage,address,home_phone,cell_phone,office_home,teach_condition,teach_memo,login_pass,teach_edu_kind,teach_edu_abroad,teach_sub_kind,teach_check_kind,teach_check_word,teach_is_cripple) values ('$teach_id','$teach_person_id','$name','$sex','$age','$birthday','$birth_place','$marriage','$address','$home_phone','$cell_phone','$office_home','$teach_condition','$teach_memo','$login_pass','$teach_edu_kind','$teach_edu_abroad','$teach_sub_kind','$teach_check_kind','$teach_check_word','$teach_is_cripple')";			
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;

		};
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換教師網路資料中");		
		$sql_select = "select teach_id,email,email2,email3,selfweb,selfweb2,classweb,classweb2,icq from teacher_connect";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID);

		while ($row = mysql_fetch_array($result)) {

			$teach_id = $row["teach_id"];
			$email = $row["email"];
			$email2 = $row["email2"];
			$email3 = $row["email3"];
			$selfweb = $row["selfweb"];
			$selfweb2 = $row["selfweb2"];
			$classweb = $row["classweb"];
			$classweb2 = $row["classweb2"];
			$icq = $row["icq"];
			$sql_insert = "insert into teacher_connect (teach_id,email,email2,email3,selfweb,selfweb2,classweb,classweb2,icq) values ('$teach_id','$email','$email2','$email3','$selfweb','$selfweb2','$classweb','$classweb2','$icq')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換教師任職資料中");		
		$sql_select = "select teach_id,post_kind,post_office,post_level,official_level,post_class,post_num,bywork_num,salay,appoint_date,arrive_date,approve_date,approve_number,teach_title_id,class_num from teacher_post";

		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err:轉換教師任職資料");

		while ($row = mysql_fetch_array($result)) {

			$teach_id = $row["teach_id"];
			$post_kind = $row["post_kind"];
			$post_office = $row["post_office"];
			$post_level = $row["post_level"];
			$official_level = $row["official_level"];
			$post_class = $row["post_class"];
			$post_num = $row["post_num"];
			$bywork_num = $row["bywork_num"];			
			$salay = $row["salay"];
			$appoint_date = $row["appoint_date"];
			$arrive_date = $row["arrive_date"];
			$approve_date = $row["approve_date"];
			$approve_number = $row["approve_number"];
			$teach_title_id = $row["teach_title_id"];
			$class_num = $row["class_num"];

			$sql_insert = "insert into teacher_post (teach_id,post_kind,post_office,post_level,official_level,post_class,post_num,bywork_num,salay,appoint_date,arrive_date,approve_date,approve_number,teach_title_id,class_num) values ('$teach_id','$post_kind','$post_office','$post_level','$official_level','$post_class','$post_num','$bywork_num','$salay','$appoint_date','$arrive_date','$approve_date','$approve_number','$teach_title_id','$class_num')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換教師任教科目中");		
		$sql_select = "select subject_id,subject_name,subject_year from teacher_subject";

		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err:轉換教師科目 資料");;

		while ($row = mysql_fetch_array($result)) {

			$subject_id = $row["subject_id"];
			$subject_name = addcslashes($row["subject_name"],"'");;
			$subject_year = $row["subject_year"];

			$sql_insert = "insert into teacher_subject (subject_id,subject_name,subject_year) values ($subject_id,'$subject_name',$subject_year)";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換教師職稱中");		
		$sql_select = "select teach_title_id,title_name,title_kind,title_short_name from teacher_title";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err:轉換教師職稱資料");;

		while ($row = mysql_fetch_array($result)) {

			$teach_title_id = $row["teach_title_id"];
			$title_name = addcslashes($row["title_name"],"'");
			$title_kind = $row["title_kind"];
			$title_short_name = $row["title_short_name"];
			$sql_insert = "insert into teacher_title (teach_title_id,title_name,title_kind,title_short_name) values ($teach_title_id,'$title_name',$title_kind,'$title_short_name')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換系統資料中 (pro_check) ");		
		$sql_select = "select pc_id,pro_kind_id,post_office,teach_id,teach_title_id,is_admin from pro_check";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err: pro_check");

		while ($row = mysql_fetch_array($result)) {

			$pc_id = $row["pc_id"];
			$pro_kind_id = $row["pro_kind_id"];
			$post_office = $row["post_office"];
			$teach_id = $row["teach_id"];
			$teach_title_id = $row["teach_title_id"];
			$is_admin = $row["is_admin"];
			$sql_insert = "insert into pro_check (pc_id,pro_kind_id,post_office,teach_id,teach_title_id,is_admin) values ($pc_id,'$pro_kind_id','$post_office','$teach_id','$teach_title_id','$is_admin')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert) ;
			$iii++;
		};
		
		echo "計轉入 $iii 筆資料!<br><br>";
		
		showtitle("轉換系統資料中 (pro_check_stu) ");	
			
		$sql_select = "select pc_id,pro_kind_id,stud_id,teach_id,use_date,use_last_date,class_num from pro_check_stu";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err: pro_check_stu");;

		while ($row = mysql_fetch_array($result)) {

			$pc_id = $row["pc_id"];
			$pro_kind_id = $row["pro_kind_id"];
			$stud_id = $row["stud_id"];
			$teach_id = $row["teach_id"];
			$use_date = $row["use_date"];
			$use_last_date = $row["use_last_date"];
			$class_num = $row["class_num"];
			$sql_insert = "insert into pro_check_stu (pc_id,pro_kind_id,stud_id,teach_id,use_date,use_last_date,class_num) values ('$pc_id','$pro_kind_id','$stud_id','$teach_id','$use_date','$use_last_date','$class_num')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		
		echo "計轉入 $iii 筆資料!<br><br>";
		
		if (! check_field($new_mysql_db,$conID2,'pro_kind','pro_isopen')) {
			$query = "ALTER TABLE pro_kind ADD pro_isopen TINYINT not null";
			mysql_db_query($new_mysql_db,$query,$conID2) or die ($query);
		}
		
		if (! check_field($new_mysql_db,$conID2,'board_p','b_is_intranet')) {
			$query = "ALTER TABLE board_p ADD b_is_intranet CHAR(1) DEFAULT '0' NOT NULL";
			mysql_db_query($new_mysql_db,$query,$conID2) or die ($query);
		}		
		  


		//加入顯示程式判別欄位
		$query = "ALTER TABLE pro_kind ADD pro_islive TINYINT DEFAULT '1' NOT NULL ";
		mysql_db_query($new_mysql_db,$query,$conID2) or die ($query);
		
		showtitle("轉換系統資料中 (pro_kind) ");	
			
		$sql_select = "select * from pro_kind";

		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err: pro_kind");

		while ($row = mysql_fetch_array($result)) {

			$pro_kind_id = $row["pro_kind_id"];
				$pro_kind_name = addcslashes($row["pro_kind_name"],"'");
			$pro_kind_order = $row["pro_kind_order"];
			$home_index = $row["home_index"];
			if ($row["home_index"]=="none")
				$home_index ="";
			$store_path = $row["store_path"];
			$pro_author = $row["pro_author"];
			$pro_parent = $row["pro_parent"];
			$pro_isopen = $row["pro_isopen"];
			$sql_insert = "insert into pro_kind (pro_kind_id,pro_kind_name,pro_kind_order,home_index,store_path,pro_author,pro_parent,pro_isopen) values ('$pro_kind_id','$pro_kind_name','$pro_kind_order','$home_index','$store_path','$pro_author','$pro_parent','$pro_isopen')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		
		echo "計轉入 $iii 筆資料!<br><br>";
		
		
		
		showtitle("轉換學期資料中 (seme_class) ");
					
		$sql_select = "select current_school_year,teach_id,teach_title_id,class_num,subject_id1,subject_id2,subject_id3 from seme_class";
		$result = mysql_db_query ($session_mysql_db,$sql_select,$conID)or die ("err: seme_class");

		while ($row = mysql_fetch_array($result)) {

			$current_school_year = $row["current_school_year"];
			$teach_id = $row["teach_id"];
			$teach_title_id = $row["teach_title_id"];
			$class_num = $row["class_num"];
			$subject_id1 = $row["subject_id1"];
			$subject_id2 = $row["subject_id2"];
			$subject_id3 = $row["subject_id3"];
			$sql_insert = "insert into seme_class (current_school_year,teach_id,teach_title_id,class_num,subject_id1,subject_id2,subject_id3) values ('$current_school_year','$teach_id',$teach_title_id,'$class_num','$subject_id1','$subject_id2','$subject_id3')";
			mysql_db_query($new_mysql_db,$sql_insert,$conID2) or die ($sql_insert);
			$iii++;
		};
		
		echo "計轉入 $iii 筆資料!<br><br>";
		
		echo "<meta HTTP-EQUIV=\"REFRESH\" content=\"5; url=$PHP_SELF?step=4#this_step4\">";
				
		include "footer.php";
		exit;
	}
	
//尚未建立資料庫
if (!@mysql_select_db($mysql_db,$conID) && $dostep=='') {
	include "ustep0.php";
	include "footer.php";
	exit;
}
include "ustep1.php";
include "ustep2.php";
include "ustep3.php";
if ($step==4)
	include "ustep4.php";
	
include "footer.php";
?>