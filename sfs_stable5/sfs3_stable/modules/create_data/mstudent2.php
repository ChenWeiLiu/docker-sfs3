<?php

// $Id: mstudent2.php 9266 2018-08-13 14:19:46Z chiming $

// --系統設定檔
include "create_data_config.php";
//--認證 session
sfs_check();
//取得目前學年
$curr_year = curr_year();

//取得目前學期
$curr_seme =  curr_seme();

$newer_only=$_POST['newer_only'];

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//印出檔頭
head("批次建立學生資料");
print_menu($menu_p);

if ($do_key=="批次建立資料")
{

	//取得郵遞區號代表的縣市鄉鎮 陣列
	$zip_arr = get_zip_arr();
	$rst=-1;
	//目前月份
	$month = date("m");
	//目前學年
	$class_year = curr_year();
	//目前學期
	$curr_seme =curr_seme();
	


	//學年學期
	$seme_year_seme = sprintf("%04d", curr_year().curr_seme());

	
	//判斷是否隔年
//	if (curr_seme()==1 and $month < $SFS_SEME2)
//		$class_year++;
	//取出 csv 的值
	$temp_file= $temp_path."stud.csv";
	if ($_FILES['userdata']['size'] >0 && $_FILES['userdata']['name'] != ""){
//		copy($_FILES['userdata']['tmp_name'] , $temp_file);		
		$fd = fopen ($_FILES['userdata']['tmp_name'],"r");
  $tt_analyse = sfs_fgetcsv ($fd, 5000, ",");
			//進行抬頭欄位分析
			//學號,姓名,英文姓名,性別,入學年,班級,座號,生日(西元),身份證字號,父親姓名,母親姓名,郵遞區號,電話,住址(不含縣市鄉鎮),緊急聯絡方式,入學前國小名稱,戶籍遷入日期(西元),學生行動電話,連絡地址,監護人,監護人行動電話
	//變數定義 
	$tt_test[0]="學號";
	$tt_test[1]="姓名"; 
	$tt_test[2]="英文姓名";
	$tt_test[3]="性別";
	$tt_test[4]="入學年";
	$tt_test[5]="班級";
	$tt_test[6]="座號";
	$tt_test[7]="生日(西元)"; 
	$tt_test[8]="身份證字號";
	$tt_test[9]="父親姓名";
	$tt_test[10]="母親姓名";
	$tt_test[11]="郵遞區號";
	$tt_test[12]="電話"; 
	$tt_test[13]="住址(不含縣市鎮)";
	if ($tt_analyse[13]=="住址(不含縣市?鎮)")$tt_test[13]="住址(不含縣市?鎮)";
	$tt_test[14]="緊急聯方式"; 
	$tt_test[15]="入學前國小名稱"; 
	$tt_test[16]="戶籍遷入日期(西元)";	
	$tt_test[17]="學生行動電話"; 
	$tt_test[18]="連絡地址"; 
	$tt_test[19]="監護人"; 
	$tt_test[20]="監護人行動電話"; 
	
	$tt_test[21]="監護人身分證證照";
	$tt_test[22]="監護人服務單位";
	$tt_test[23]="監護人職稱";
	$tt_test[24]="監護人電話";
	$tt_test[25]="監護人地址";
	$tt_test[26]="監護人電子郵件";
	
	$tt_test[27]="父親身分證證照";
	$tt_test[28]="父親職業";
	$tt_test[29]="父親服務單位";
	$tt_test[30]="父親職稱";
	$tt_test[31]="父親行動電話";
	$tt_test[32]="父親電話(公)";
	$tt_test[33]="父親電話(宅)";
	$tt_test[34]="父親電子郵件";
	
	$tt_test[35]="母親身分證證照";
	$tt_test[36]="母親職業";
	$tt_test[37]="母親服務單位";
	$tt_test[38]="母親職稱";
	$tt_test[39]="母親行動電話";
	$tt_test[40]="母親電話(公)";
	$tt_test[41]="母親電話(宅)";
	$tt_test[42]="母親電子郵件";
	
	$tt_test[43]="祖父姓名";
	$tt_test[44]="祖母姓名";

	

	
	//預設只檢查是有存在此 21 個欄位, 先把每個欄位的序號( $tt_test[$i) , $i 即為序號)記下, 之後讀取資料後, 再依實際資料內容導正序號
	$trans_field=array();
	for ($i=0;$i<count($tt_test);$i++) { $trans_field[$i]=-1; }
	foreach ($tt_analyse as $user_field=>$v) {
	  foreach ($tt_test as $sql_field=>$V) {
	   if ($v==$V) {
	   	$trans_field[$sql_field]=$user_field;   //第幾欄的資料, 將來必須取用使用者輸入的第幾欄資料	    
	   }
	  }
	} 		
	
	//抬頭資料分析完畢, 稍後取得csv欄位資料後, 利用 trans_to_right_field_to_right_field 函式導正
		
		rewind($fd);
		for ($i=0;$i<2;$i++){
		    $tt_org = sfs_fgetcsv ($fd, 5000, ",");
		    // 只抓取匯入檔的第二列
		    if ($i==1) {
		      $tt=trans_to_right_field($tt_org);
		      $c_year = $class_year-$tt[4]+1+$IS_JHORES; // 計算年級，$IS_JHORES 使三種學制的年級計算正常
		      
		    }
		}
		$query = "select c_sort,c_name  from school_class where year='$class_year' and semester='$curr_seme' and c_year='$c_year' ";
		$res = $CONN->Execute($query)or die ($query) ;
		if ($res->EOF){
		  $con_temp =  "您的匯入檔中 $c_year 年級(入學年: $tt[4])，尚未設定班級數，請注意這個年級在貴校學制中是否有效？若屬有效年級範圍，請至教務處->學期初設定，將班級數設定好之後，再執行本程式。<br>本次執行中斷的查詢指令為： $query";
		}
		else {
			while (!$res->EOF) {
				$temp_class_name[$res->fields[0]] = $res->fields[1];
				$res->MoveNext();
			}
			
			//進行匯入資料的檢查			
			rewind($fd);
			$j =0;
			$stud_id_array=array();
			$curr_class_num_array=array();
			while ($ck_tt_org = sfs_fgetcsv ($fd, 5000, ",")) {
				if ($j++ == 0){ //第一筆為抬頭，不檢查
                    continue ;
                }
				/*  原來的程式碼
				if (substr($ck_tt[0],0,1)==0)
					$stud_id= substr($ck_tt[0],1);
				else
					$stud_id= trim($ck_tt[0]);
				*/
				//導正欄位序號 
				$ck_tt=trans_to_right_field($ck_tt_org);
				//修改的程式碼
				$stud_id= trim($ck_tt[0]);

				
				$rollin_year=trim($ck_tt[4]);

				//檢查學號是否存在
				if($stud_id=="") {
					$msg="學號（學生代號）不准空白，於第 ".$j." 筆學生資料";
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}
				
				//檢查學號是否重複
				if(in_array($stud_id,$stud_id_array))  {
					$msg="您所要匯入的學生資料中學號：$stud_id 重複"; 
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}
				
				//沒有重複則加入學號陣列
				$stud_id_array[$j]=$stud_id;
				
				
				$stud_name = trim (addslashes($ck_tt[1]));
				//檢查姓名
				if($stud_name=="") {
					$msg="學號：$stud_id 的學生沒有姓名";
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}
				
				$stud_sex = trim($ck_tt[3]);				
				//檢查性別				
				if($stud_sex!=1 && $stud_sex!=2) {
					$msg="學號：$stud_id  姓名：$stud_name 的學生性別錯誤"; 
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}
				
				$stud_study_year = chop ($ck_tt[4]);				
				// 引入 $IS_JHORES
				$year = $class_year-$stud_study_year+1+$IS_JHORES;
				$ck_query = "select c_sort,c_name  from school_class where year='$class_year' and semester='$curr_seme' and c_year='$year' and enable=1";
				$ck_res = $CONN->Execute($ck_query)or die ($ck_query) ;
				//檢查入學年度
				if ($ck_res->EOF) {
					$msg="學號：$stud_id  姓名：$stud_name 的入學年度（ $stud_study_year ）填寫錯誤或班級（ $year 年級） 尚未設定";
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}
				$k=0;
				while(!$ck_res->EOF){
					$c_sort[$k]=$ck_res->fields['c_sort'];					
					$ck_res->MoveNext();
					$k++;
				}
				//檢查班級
				$class=trim($ck_tt[5]);
				if(!in_array($class,$c_sort)){
					$msg="學號：$stud_id  姓名：$stud_name 的學生班級（ $year 年 $class 班 ）填寫錯誤或班級尚未設定"; 
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;									
					foot();	
					exit;
					}
								
				if($year==0) $class_name= sprintf("%03d",$ck_tt[5]);
				else $class_name = $year*100+$ck_tt[5];
				$class_name_id = $ck_tt[5];
				if($year==0) $curr_class_num=sprintf("%03d%02d",$ck_tt[5],$ck_tt[6]);
				else $curr_class_num= $class_name*100+$ck_tt[6];
				//檢查座號是否重複			
				if(in_array($curr_class_num,$curr_class_num_array))  {
					$msg= "您所要匯入的學生資料中座號（ $year 年 $class 班 ".substr($curr_class_num,-1,2)." 號 ） 重複"; 
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}
				
				//沒有重複則加入座號陣列
				$curr_class_num_array[$j]=$curr_class_num;
				
				$stud_birthday = trim ($ck_tt[7]);
				//檢查生日
				
				//$stud_birthday_array=explode("/",$stud_birthday);
				$stud_birthday_array=split("[/.-]",$stud_birthday);
				if($stud_birthday_array[0]<1900 || $stud_birthday_array[0]>2030 || $stud_birthday_array[1]<1 || $stud_birthday_array[1]>12 || $stud_birthday_array[2]<1 || $stud_birthday_array[2]>31) {
					$msg="學號：$stud_id  姓名：$stud_name 的生日（ $stud_birthday ）填寫錯誤"; 
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;				
					foot();
					exit;
				}
				
				$stud_person_id = trim ($ck_tt[8]);
				//檢查身份證
				if($stud_person_id=="") {
					$msg="身份證不准空白，於第 ".($j-1)." 筆學生資料";
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;					
					foot();
					exit;
				}				
				
				//檢查身份證是否重複
				if(in_array($stud_person_id,$stud_person_id_array))  {
					$msg="您所要匯入的學生資料中身份證號：$stud_person_id 重複"; 
					$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
					echo $alert;
					foot();
					exit;
				}
				
				//沒有重複則加入學號陣列
				$stud_person_id_array[$j]=$stud_person_id;
				
				//檢查現存資料庫中是否該身份證是否有其他的學號，或是該學號中已存有其他身份正號
				$sql="select stud_id from stud_base where stud_person_id='$stud_person_id' and stud_study_cond='0' ";
				$rs=$CONN->Execute($sql) or trigger_error($sql,256);
				$m=0;
				while(!$rs->EOF){
					$old_id[$m]=$rs->fields['stud_id'];
					if($stud_id!=$old_id[$m]) {					
						$msg="學號：$stud_id  姓名：$stud_name 的身份證字號已被學號： ".$old_id[$m]." 使用，請查明！";
						$alert="<p></p><table cellspacing=1 cellpadding=6 border=0 bgcolor='#FFF829' width='80%' align='center'><tr><td align='center'><h1><img src='images/caution.png' align='middle' border=0> 警告</h1></font></td></tr><tr><td align='center' bgcolor='#FFFFFF' width='90%'> $msg </td></tr></table>";
						echo $alert;
						foot();
						exit;
					}
					$rs->MoveNext();
					$m++;
				}
				
				//都沒問題了
				$check_pass="ok";
			}
			//檢查通過才放行，使之開始寫入資料庫
			if($check_pass=="ok"){			
				rewind($fd);
				$i =0;
				while ($tt_org = sfs_fgetcsv ($fd, 5000, ",")) {
					if ($i++ == 0){ //第一筆為抬頭
						$ok_temp .="<font color='red'>第一筆應為抬頭，若您的學生基本資料檔的第一筆是學生資料的話，該位學生將無法匯入！</font><br>";
						continue ;
					}
					/*  原來的程式碼
					if (substr($tt[0],0,1)==0)
						$stud_id= substr($tt[0],1);
					else
						$stud_id= trim($tt[0]);
					*/
					//導正欄位序號
					$tt=trans_to_right_field($tt_org);
					//修改的程式碼
					$stud_id= trim($tt[0]);
					$stud_name = trim (addslashes($tt[1]));
					
					$stud_name_eng = trim($tt[2]);
					
					//加入將全形空白替換的功能
					$stud_name=str_replace('　','',$stud_name); 
					
					$stud_sex = trim($tt[3]);
					$stud_study_year = chop ($tt[4]);
					
					$go=true;				
					if($newer_only and $stud_study_year<>$class_year) $go=false;
					if($go) {
				
						// 引入 $IS_JHORES
						$year = $class_year-$stud_study_year+1+$IS_JHORES;
						//幼稚班的年級為0
						if($year==0) $class= sprintf("%03d",$tt[5]);
						else $class = $year*100+$tt[5];
						$class_name_id = $tt[5];
						if($year==0) $curr_class_num=sprintf("%03d%02d",$tt[5],$tt[6]);
						else $curr_class_num= $class*100+$tt[6];
						$seme_num = sprintf("%02d",$tt[6]);
						$stud_birthday = trim ($tt[7]);
						$stud_person_id = trim ($tt[8]);
						$fath_name = trim (addslashes($tt[9]));
						$moth_name = trim (addslashes($tt[10]));

						$stud_tel_1 = trim ($tt[12]);
						$stud_tel_2 = trim ($tt[14]);
						$stud_mschool_name = trim ($tt[15]);
						$zip_id = trim($tt[11]);
						//若住址中,使用全形數字,將其轉換成半形,以利拆解 2015.09.14 修改 by smallduh.
						$search  = array('０', '１', '２', '３', '４','５', '６', '７', '８', '９');
						$replace = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
						$tt[13]=str_replace($search, $replace, $tt[13]);
						$addr = $zip_arr[$tt[11]].trim($tt[13]);
						$addr=trim(str_replace("&","",$addr));
						//20120825新增欄位   戶籍遷入日期、學生行動電話、連絡地址、監護人、監護人行動電話
						$addr_move_in=trim($tt[16]);
						$stud_tel_3 = trim ($tt[17]);
						$stud_addr_2 = trim(addslashes($tt[18]));
						$stud_addr_2=trim(str_replace("&","",$stud_addr_2));
						$stud_addr_2 = $stud_addr_2?$stud_addr_2:addslashes($addr);
						$guardian_name=trim(addslashes($tt[19]));
						$guardian_hand_phone=trim($tt[20]);
						

						//新加入戶政資料欄位
                        $guardian_p_id=trim($tt[21]);
                        $guardian_unit=trim(addslashes($tt[22]));
                        $guardian_work_name=trim(addslashes($tt[23]));
						$guardian_phone=trim($tt[24]);
                        $guardian_address=trim(addslashes($tt[25]));
						$guardian_address=trim(str_replace("&","",$guardian_address));
                        $guardian_email=trim($tt[26]);
						
                        $fath_p_id=trim($tt[27]);
						$fath_occupation=trim(addslashes($tt[28]));                       
                        $fath_unit=trim(addslashes($tt[29]));
                        $fath_work_name=trim(addslashes($tt[30]));						
						$fath_hand_phone=trim($tt[31]);                        
                        $fath_phone=trim($tt[32]);
                        $fath_home_phone=trim($tt[33]);
						$fath_email=trim($tt[34]);
                        
                        $moth_p_id=trim($tt[35]);
                        $moth_occupation=trim(addslashes($tt[36]));
						$moth_unit=trim(addslashes($tt[37]));
                        $moth_work_name=trim(addslashes($tt[38]));
                        $moth_hand_phone=trim($tt[39]);
                        $moth_phone=trim($tt[40]);
						$moth_home_phone=trim($tt[41]);
                        $moth_email=trim($tt[42]);

						$grandfath_name=trim(addslashes($tt[43]));
                        $grandmoth_name=trim(addslashes($tt[44]));						
						//新加入戶政資料欄位
						
						$edu_key =  hash('sha256', strtoupper($stud_person_id));
						//拆解地址
						$addr_arr = change_addr($addr);
						//加上跳脫字元，避免許功蓋問題  如：成功路，會出現亂碼 2015.09.14 修改 by smallduh.
						foreach ($addr_arr as $k=>$v) {
						  $addr_arr[$k]=addslashes($v);
						}
						$addr=addslashes($addr);
						$stud_kind =',0,';
						//空值NULL的判斷，修正未keyin戶籍遷入日期時，基本資料（stud_list.php）遷入日期-1911-00-00的錯置。修改 by chunkai 102.9.6
						$sql_insert1 = "replace into stud_base (stud_id,stud_name,stud_name_eng,stud_person_id,stud_birthday,stud_sex,stud_study_cond,
						curr_class_num,stud_study_year,stud_addr_a,stud_addr_b,stud_addr_c,stud_addr_d,stud_addr_e,stud_addr_f,
						stud_addr_g,stud_addr_h,stud_addr_i,stud_addr_j,stud_addr_k,stud_addr_l,stud_addr_m,stud_addr_1,stud_addr_2,
						stud_tel_1,stud_tel_2,stud_kind,stud_mschool_name,addr_zip,enroll_school,addr_move_in,stud_tel_3,edu_key) 
						values ('$stud_id','$stud_name','$stud_name_eng','$stud_person_id','$stud_birthday','$stud_sex','0','$curr_class_num','$stud_study_year',
						'$addr_arr[0]','$addr_arr[1]','$addr_arr[2]','$addr_arr[3]','$addr_arr[4]','$addr_arr[5]','$addr_arr[6]','$addr_arr[7]',
						'$addr_arr[8]','$addr_arr[9]','$addr_arr[10]','$addr_arr[11]','$addr_arr[12]','$addr','$stud_addr_2','$stud_tel_1',
						'$stud_tel_2','$stud_kind','$stud_mschool_name','$zip_id','$school_long_name','$addr_move_in','$stud_tel_3','$edu_key')";
						
						$sql_insert2 = "replace into stud_base (stud_id,stud_name,stud_name_eng,stud_person_id,stud_birthday,stud_sex,stud_study_cond,
						curr_class_num,stud_study_year,stud_addr_a,stud_addr_b,stud_addr_c,stud_addr_d,stud_addr_e,stud_addr_f,
						stud_addr_g,stud_addr_h,stud_addr_i,stud_addr_j,stud_addr_k,stud_addr_l,stud_addr_m,stud_addr_1,stud_addr_2,
						stud_tel_1,stud_tel_2,stud_kind,stud_mschool_name,addr_zip,enroll_school,addr_move_in,stud_tel_3,edu_key)
						 values ('$stud_id','$stud_name','$stud_name_eng','$stud_person_id','$stud_birthday','$stud_sex','0','$curr_class_num','$stud_study_year',
						'$addr_arr[0]','$addr_arr[1]','$addr_arr[2]','$addr_arr[3]','$addr_arr[4]','$addr_arr[5]','$addr_arr[6]','$addr_arr[7]',
						'$addr_arr[8]','$addr_arr[9]','$addr_arr[10]','$addr_arr[11]','$addr_arr[12]','$addr','$stud_addr_2','$stud_tel_1',
						'$stud_tel_2','$stud_kind','$stud_mschool_name','$zip_id','$school_long_name',NULL,'$stud_tel_3','$edu_key')";
						($addr_move_in == '')?$sql_insert=$sql_insert2:$sql_insert=$sql_insert1;
				//	echo $sql_insert."<BR>";

						$result2 = $CONN->Execute($sql_insert);
						if ($result2) {
							$stud_name=stripslashes($stud_name);
							$ok_temp .= "$stud_id -- $stud_name 新增成功!<br>";
							
							$guardian_name = $guardian_name?$guardian_name:$fath_name;
							$guardian_name = $guardian_name?$guardian_name:$moth_name;
							
							//取得 student_sn
							$query = "select student_sn from stud_base where stud_id='$stud_id' and stud_study_year=$stud_study_year";
							$resss = $CONN->Execute($query);
							$student_sn= $resss->fields[0];

							//加入家庭狀況資料
							//$query = "replace into stud_domicile (stud_id,fath_name,moth_name,guardian_name,guardian_hand_phone,student_sn) values('$stud_id','$fath_name','$moth_name','$guardian_name','$guardian_hand_phone','$student_sn')";

$query = "replace into stud_domicile
(stud_id,
 fath_name,
 moth_name,
 guardian_name,
 guardian_hand_phone,
 student_sn,
 guardian_p_id,
 guardian_unit,
 guardian_work_name,
 guardian_phone,
 guardian_address,
 guardian_email,
 fath_p_id,
 fath_occupation,                       
 fath_unit,
 fath_work_name,						
 fath_hand_phone,                        
 fath_phone,
 fath_home_phone,
 fath_email,
 moth_p_id,
 moth_occupation,
 moth_unit,
 moth_work_name,
 moth_hand_phone,
 moth_phone,
 moth_home_phone,
 moth_email,
 grandfath_name,
 grandmoth_name	
 
 )values(
 '$stud_id',
 '$fath_name',
 '$moth_name',
 '$guardian_name',
 '$guardian_hand_phone',
 '$student_sn',
 '$guardian_p_id',
 '$guardian_unit',
 '$guardian_work_name',
 '$guardian_phone',
 '$guardian_address',
 '$guardian_email',
 '$fath_p_id',
 '$fath_occupation',                       
 '$fath_unit',
 '$fath_work_name',						
 '$fath_hand_phone',                        
 '$fath_phone',
 '$fath_home_phone',
 '$fath_email',
 '$moth_p_id',
 '$moth_occupation',
 '$moth_unit',
 '$moth_work_name',
 '$moth_hand_phone',
 '$moth_phone',
 '$moth_home_phone',
 '$moth_email',
 '$grandfath_name',
 '$grandmoth_name'			
				
)";

							if (!$CONN->Execute($query))
								$con_temp .= "$stud_id - $stud_name 新增家庭狀況資料失敗! <br>";
							//加入學年學期資料
							$query = "replace into  stud_seme (seme_year_seme,stud_id,seme_class,seme_num,seme_class_name,student_sn) values('$seme_year_seme','$stud_id','$class','$seme_num','$temp_class_name[$class_name_id]','$student_sn')";
							if (!$CONN->Execute($query))
								$con_temp .= "$stud_id - $stud_name 新增學年資料失敗! <br>";
					//	echo $query."<BR>";

						}	else $con_temp .= "$stud_id - $stud_name 新增基本資料失敗! <br>";
					} else $con_temp .="學號: $stud_id - $stud_name 入學年( $rollin_year )匯入限定禁止!!<BR>";
				}
			}
		}
	}
	else
	{
		echo "檔案格式錯誤!";
		exit;
	}
	unlink($temp_file);
	
}
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0" >
<tr><td valign=top bgcolor="#CCCCCC">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >

<form action ="<?php echo $_SERVER['PHP_SELF'] ?>" enctype="multipart/form-data" method=post>
<tr><td  nowrap>檔案：<input type=file name=userdata><BR><BR><font color='red'>PS.歷年度已有的學生資料請勿重覆匯入！</font></td>
<td width=65% rowspan="2" valign=top>
<?php
if ($con_temp<>''){
	echo "<b>新增錯誤<b><p>";
	echo "<font size=4>$con_temp</font>";
}
else
	echo '
<p><b><font size="4">學生資料批次建檔說明</font></b></p>
<p>1.本程式只能建立學生基本資料，其他如學生戶口資料等，需至學籍管理程式建立。<br>
2.利用 excel 或其他工具鍵入學生資料，存成 csv 檔，並保留第一列標題檔，如 
<a href=studdemo1.csv target=new>範例檔</a><BR>
3.本範例檔為萬豐版健康系統匯出之學生資料檔 Sheet1.xls 須轉存成 .csv 格式檔。<br>
4.出生日期以西元為準。<br>
5.地址順序:按下列方式排列，程式才可正常拆解。<br>
<span style="background-color: #FFFF00"><font color="#0000FF">縣(市)</font>鄉(鎮區)<font color="#0000FF">村(里)</font>鄰<font color="#0000FF">路(街)</font>段<font color="#0000FF">巷</font>弄<font color="#0000FF">號</font><font color="#000000">之</font><font color="#0000FF">樓</font>之</span></p>
例:
  <li>台中縣外埔鄉中山村11鄰中山路34之6號</li>
  <li>台中縣外埔鄉大同村甲后路9號</
';

?>

</td>

</tr>
<tr><td nowrap><input type='checkbox' name='newer_only' value='checked' checked>限定只能匯入本學年度新生 ( 入學年為 <?php echo $curr_year; ?> )<br><br>
<input type=submit name="do_key" value="批次建立資料"></td></tr>

</form>
</table>
</td></tr></table>

<?php
echo $ok_temp;
foot();

//將實際欄位順序導正為配合資料庫欄位資料
function trans_to_right_field($tt_org) {
  global $trans_field,$tt_test;
  
  $tt=array();
  for ($i=0;$i<count($tt_test);$i++) {  	
  	if ($trans_field[$i]>-1) {
  		$tt[$i]=$tt_org[$trans_field[$i]];
    } else {
  	  $tt[$i]=""; 
    }
  } // end for
  
  return $tt;
  
}
?> 
