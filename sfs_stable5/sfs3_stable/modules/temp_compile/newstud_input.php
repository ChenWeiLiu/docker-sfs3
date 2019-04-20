<?php

// $Id: newstud_input.php 8118 2014-09-15 05:35:09Z hami $

/*引入學務系統設定檔*/
require "config.php";
$class_year_b=$_REQUEST['class_year_b'];
if (empty($class_year_b)) $class_year_b=$IS_JHORES+1;

//使用者認證
sfs_check();

//程式檔頭
head("新生編班");
print_menu($menu_p,"class_year_b=$class_year_b");

//設定主網頁顯示區的背景顏色
echo "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";

//網頁內容請置於此處
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
if ($_POST['act']=="批次建立資料"){
    $main=import($_FILES['newstuddata']['tmp_name'],$_FILES['newstuddata']['name'],$_FILES['newstuddata']['size']);
}else{
	$main=main_form();
}
echo $main;

//主要表格
function main_form(){
  global $CONN,$class_year,$IS_JHORES;
  if ($IS_JHORES == 0) { $tmpsel1=' selected'; $tmpsel2=''; } else { $tmpsel1=''; $tmpsel2=' selected'; }
	$main="
	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' class=main_body >
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#E1ECFF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		<select name='class_year_b'>";
	while (list($k,$v)=each($class_year)) {
		$checked=($IS_JHORES+1==$k)?"selected":"";
		$main.="<option value='$k' $checked>$v</option>\n";
	}
	$main.="</select>
        <input type=file name='newstuddata'>
		<p><input type=submit name='act' value='批次建立資料'></p>
		</td>
		<td valign='top' bgcolor='#FFFFFF'>
		<p><b><font size='4'>新生資料批次建檔說明</font></b></p>
		<ol>
		<li>本程式提供正式編班前之新生基本資料匯入，若是已編班完成之學生資料請由<a href=\"../create_data/\">「匯入資料」</a>模組進行處理。</li>
		<li>匯入的新生基本資料csv檔格式，請參考<a href='newstud.csv'>『newstud.csv』</a>。</li>
		<li>匯入之後請到『新生管理』進行相關資料檢查。</li>
		<li>「國中臨時班級」、「國中臨時座號」欄是給手動臨時編班匯入用，若直接以學務系統進行臨時編班，則請留白。</li>
		</ol>
		</td>
		</tr>
		</table>
	</form>
	</td></tr></table>
	";
	return $main;
}


//匯入資料
function import($newstuddata,$newstuddata_name,$newstuddata_size){
	global $temp_path,$CONN,$ok_temp,$con_temp;
	
	//變數定義 入學年,舊校名,身分證字號,姓名,英文姓名,性別(男生:1，女生:2),電話,生日（西元）,家長姓名,戶籍住址,原班級,戶籍遷入日期,聯絡住址,聯絡手機,臨時班級,臨時座號
	$tt_test[0][0]="入學年"; $tt_test[0][1]="stud_study_year";
	$tt_test[1][0]="舊校名"; $tt_test[1][1]="old_school";
	$tt_test[2][0]="身分證字號"; $tt_test[2][1]="stud_person_id";
	$tt_test[3][0]="姓名"; $tt_test[3][1]="stud_name";
	$tt_test[4][0]="英文姓名"; $tt_test[4][1]="stud_name_eng";
	$tt_test[5][0]="性別(男生:1，女生:2)"; $tt_test[5][1]="stud_sex";
	$tt_test[6][0]="電話"; $tt_test[6][1]="stud_tel_1";
	$tt_test[7][0]="生日（西元）"; $tt_test[7][1]="stud_birthday";
	$tt_test[8][0]="家長姓名"; $tt_test[8][1]="guardian_name";
	$tt_test[9][0]="戶籍住址"; $tt_test[9][1]="stud_address";
	$tt_test[10][0]="原班級"; $tt_test[10][1]="old_class";
	$tt_test[11][0]="戶籍遷入日期"; $tt_test[11][1]="addr_move_in";
	$tt_test[12][0]="聯絡住址"; $tt_test[12][1]="stud_addr_2";
	$tt_test[13][0]="郵遞區號"; $tt_test[13][1]="addr_zip";
	$tt_test[14][0]="聯絡手機"; $tt_test[14][1]="stud_tel_3";
	$tt_test[15][0]="臨時班級"; $tt_test[15][1]="temp_class";
	$tt_test[16][0]="臨時座號"; $tt_test[16][1]="temp_site";
	
	$tt_test[17][0]="身份證字號"; $tt_test[17][1]="stud_person_id";
	$tt_test[18][0]="住址"; $tt_test[18][1]="stud_address";
	$tt_test[19][0]="國小班級"; $tt_test[19][1]="old_class";
	$tt_test[20][0]="國中臨時班級"; $tt_test[20][1]="temp_class";
	$tt_test[21][0]="國中臨時座號"; $tt_test[21][1]="temp_site";
	
	//echo $_FILES['newstuddata']['tmp_name'].$_FILES['newstuddata']['name'].$_FILES['newstuddata']['size'];
	if ($_FILES['newstuddata']['size'] >0 && $_FILES['newstuddata']['name'] != ""){
		$path_str = "temp/newstud";
		$UPLOAD_PATH=set_upload_path($path_str);
		$temp_path = $UPLOAD_PATH;
		$temp_file= $temp_path."newstud.csv";

		copy($_FILES['newstuddata']['tmp_name'] , $temp_file);
		$fd = fopen ($temp_file,"r");
		//$fd = fopen ($_FILES['newstuddata']['tmp_name'],"r");
		rewind($fd);
		$i=0;
		while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
			if ($i++ == 0)	continue ;
			$stud_study_year = trim($tt[0]);
			if ($stud_study_year && $i>1) break;
		}
		$query="select max(temp_id) from new_stud where stud_study_year='$stud_study_year'";
		$res=$CONN->Execute($query);
		$temp_id=$res->fields[0];
		$start_id=intval(substr($temp_id,1));
		rewind($fd);
		$fd = fopen ($temp_file,"r");
		$i =0;
		$class_year = $_POST['class_year_b'];
		while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
			//第一筆為抬頭 , 分析欄位 入學年,舊校名,身分證字號,姓名,英文姓名,性別(男生:1，女生:2),電話,生日（西元）,家長姓名,戶籍住址,原班級,戶籍遷入日期,聯絡住址,聯絡手機,臨時班級,臨時座號
			if ($i++ == 0){ 
			$ok_temp .="<font color='red'>第一筆應為抬頭，若您的新生基本資料檔的第一筆是新生資料的話，該位學生將無法匯入！</font><br>";
			 for ($ii=0;$ii<=count($tt);$ii++) {
			 	$chk=0;
			   for ($jj=0;$jj<=count($tt_test);$jj++) {
			    if (trim($tt[$ii])==$tt_test[$jj][0]) {
			     $chk=1;
			     $T[$ii]=$tt_test[$jj][1]; //$T[] 為變數名稱 , 如 $T[0]='stud_study_year'; 使用時互相對應 $$T[0]=trim(addslashes($tt[0]))
			    } // end if
			   } // end for $jj
			  if ($chk==0) {
			   echo "欄位名稱[".$tt[$ii]."]無法識別 , 請重新修訂!";
			   exit();
			  } // end if $chk==0
			 }	// end for $ii==0 
			 //分析完畢, 略過, 從 $i==1 開始
				continue ;
			}
			

			//將對應欄位的資料填入正確變數中
			for ($ii=0;$ii<=count($tt);$ii++) {
			  $$T[$ii]=trim(addslashes($tt[$ii]));
			  $$T[$ii]=preg_replace('/,/',' ',$tt[$ii]);
			}
			
			/***
			$stud_study_year = trim($tt[0]);  								//入學年
			$old_school = trim (addslashes($tt[1])); 					//舊學校
			$stud_person_id = trim($tt[2]); 									//身份證字號
			$stud_name = trim(addslashes($tt[3]));						//姓名
			$stud_name_eng = trim(addslashes($tt[4]));				//英文姓名
			$stud_sex = trim($tt[5]);													//性別
			
			$stud_tel_1 = trim($tt[6]);												//電話
			$stud_birthday = trim($tt[7]);										//生日
			$guardian_name = trim(addslashes($tt[8]));				//家長姓名
			$stud_address = trim(addslashes($tt[9]));					//戶籍地址
			$old_class = trim(addslashes($tt[10]));						//舊班級
			$addr_move_in=trim($tt[11]);						//戶籍遷入日期
			$stud_addr_2= trim(addslashes($tt[12]));			//聯絡住址
			$addr_zip = trim($tt[13]);												//郵遞區號
			$stud_tel_3= trim($tt[14]);							//聯絡手機
			$temp_class = trim($tt[15]);											//臨時班級
			$temp_site = ($temp_class)?trim($tt[16]):0;				//臨時座號
			***/
			
			
			if ($temp_class) $temp_class = $class_year.sprintf("%02d",$temp_class);
	    //if ($temp=="" or $temp==0) $temp_site=0;
	    if ($temp_class=="" or $temp_class==0) $temp_site=0;
			
			
			$id="A".sprintf("%04d",$start_id+$i-1);
			
			//生日處理
			$sb=explode("/",$stud_birthday);
			if (count($sb)<3) {
				$sb=explode("-",$stud_birthday);
				$stud_birthday=$sb[0]."/".$sb[1]."/".$sb[2];
			}
			//戶籍遷入日期
			if ($addr_move_in!="") {
			  $sb=explode("/",$addr_move_in);
			  if (count($sb)<3) {
				  $sb=explode("-",$addr_move_in);
				  $addr_move_in=$sb[0]."/".$sb[1]."/".$sb[2];
			  }
		  }
			
			//檢查該筆資料是否存在
			$sql_select = "select newstud_sn from new_stud where stud_study_year='$stud_study_year' and old_school='$old_school' and stud_person_id='$stud_person_id' and stud_name='$stud_name'";
			$result_s = $CONN->Execute($sql_select) or die($sql_select);
			$newstud_sn=$result_s->fields['newstud_sn'];
			if ($newstud_sn!="") { 
				$ok_temp .= "$old_school -- $stud_name 已經存在!<br>"; 
				continue;
			} elseif (empty($stud_name))
				continue;
			else 
				$sql = "INSERT INTO new_stud (stud_study_year,old_school,stud_person_id,stud_name,stud_sex,stud_tel_1,stud_birthday,guardian_name,stud_address,sure_study,class_year,temp_id,old_class,addr_zip,temp_class,temp_site,stud_name_eng,addr_move_in,stud_addr_2,stud_tel_3) values ('$stud_study_year','$old_school','$stud_person_id','$stud_name','$stud_sex','$stud_tel_1','$stud_birthday','$guardian_name','$stud_address','1','$class_year','$id','$old_class','$addr_zip','$temp_class','$temp_site','$stud_name_eng','$addr_move_in','$stud_addr_2','$stud_tel_3')";
			$result = $CONN->Execute($sql) or die($sql);
			if ($result) {
				$stud_name=stripslashes($stud_name);
				$ok_temp .= "$old_school -- $stud_name 新增成功!<br>";
			} else
				$con_temp = "資料新增失敗!$sql_insert<br>";
		}
	} else {
		echo "檔案格式錯誤!";
		exit;
	}
    unlink($temp_file);
}

//結束主網頁顯示區
echo $ok_temp.$con_temp;
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();
?>