<?php
// 引入 SFS3 的函式庫
include "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

// 叫用 SFS3 的版頭
head(iconv("UTF-8","Big5","XML匯入"));
$tool_bar=make_menu($toxml_menu);
echo $tool_bar;

// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

if($_POST['go']=='Go'){
	if ($_FILES['xmlfile']['size'] >0 && $_FILES['xmlfile']['name'] != "") {
		$stud_kind_array=stud_kind();  //擷取學生身分別代碼以利後面判斷
		$xml = simplexml_load_file($_FILES['xmlfile']['tmp_name']);
		$students =$xml->學生基本資料;
		foreach($students as $student){
			$basis_data=$student->基本資料;
			$stud_name=trim($basis_data->學生姓名);
			if($stud_name<>'null'){
				$stud_sex=($basis_data->學生性別=="男")?"1":"2";
				$stud_birthday=$basis_data->學生生日;
				
				//以下這部分判斷轉入學校的班級資料，做為異動記錄的資料參考
				//$curr_class_num=sprintf("%d%02d%2d",$basis_data->現在年級,$basis_data->現在班級,$basis_data->現在座號);
				
				//身份註記
				$stud_types=$basis_data->學生身份註記->學生身份別_資料內容;
				foreach($stud_types as $temp_type){
					$temp_type_code=iconv("UTF-8","Big5",$temp_type->學生身份別_類別);
					$temp_type_code=array_search($temp_type_code,$stud_kind_array); //轉換學生身分別代碼
					$stud_type.=$temp_type->學生身份別_類別.',';
				}
				
				//原住民資料
				$stud_aborigine_area=$basis_data->原住民->原住民_居住地;
				$stud_aborigine_clan=$basis_data->原住民->原住民_族別;
				
				//身分證證照
				$stud_country=$basis_data->身分證證照->國籍;
				$stud_country_kind=iconv("UTF-8","Big5",$basis_data->身分證證照->證照種類);
				$stud_country_kind=array_search($stud_country_kind,stud_country_kind()); //轉換證照種類代碼
				
				$stud_person_id=strtoupper(trim($basis_data->身分證證照->證照號碼));
				$stud_country_name=$basis_data->身分證證照->僑居地;
				
				//連絡資料
				$stud_addr=$basis_data->連絡資料->戶籍地址;
				$stud_addr_1=$stud_addr->戶籍地址_縣市名;
				$stud_addr_1.=$stud_addr->戶籍地址_鄉鎮市區名;
				$stud_addr_1.=$stud_addr->戶籍地址_村里;
				$stud_addr_1.=$stud_addr->戶籍地址_鄰;
				$stud_addr_1.=$stud_addr->戶籍地址_路街;
				$stud_addr_1.=$stud_addr->戶籍地址_段;
				$stud_addr_1.=$stud_addr->戶籍地址_巷;
				$stud_addr_1.=$stud_addr->戶籍地址_弄;
				$stud_addr_1.=$stud_addr->戶籍地址_號;
				$stud_addr_1.=$stud_addr->戶籍地址_之;
				$stud_addr_1.=$stud_addr->戶籍地址_樓;
				$stud_addr_1.=$stud_addr->戶籍地址_樓之;
				$stud_addr_1.=$stud_addr->戶籍地址_其他;			
				$stud_addr_1=str_replace("null","",$stud_addr_1);
				
				$stud_addr=$basis_data->連絡資料->通訊地址;
				$stud_addr_2=$stud_addr->通訊地址_縣市名;
				$stud_addr_2.=$stud_addr->通訊地址_鄉鎮市區名;
				$stud_addr_2.=$stud_addr->通訊地址_村里;
				$stud_addr_2.=$stud_addr->通訊地址_鄰;
				$stud_addr_2.=$stud_addr->通訊地址_路街;
				$stud_addr_2.=$stud_addr->通訊地址_段;
				$stud_addr_2.=$stud_addr->通訊地址_巷;
				$stud_addr_2.=$stud_addr->通訊地址_弄;
				$stud_addr_2.=$stud_addr->通訊地址_號;
				$stud_addr_2.=$stud_addr->通訊地址_之;
				$stud_addr_2.=$stud_addr->通訊地址_樓;
				$stud_addr_2.=$stud_addr->通訊地址_樓之;
				$stud_addr_2.=$stud_addr->通訊地址_其他;
				$stud_addr_2=str_replace("null","",$stud_addr_2);
				
				$stud_tel_1=$basis_data->連絡資料->通訊電話;
				$stud_tel_2=$basis_data->連絡資料->通訊電話;
				$stud_te1_3=$basis_data->連絡資料->行動電話;
				
				/* 3.0 XML 已刪除
				$stud_addr=$basis_data->中輟時戶籍地址;
				$stud_addr_a=$stud_addr->縣市名;
				$stud_addr_b=$stud_addr->鄉鎮市區名;
				$stud_addr_c=$stud_addr->村里;
				$stud_addr_d=$stud_addr->鄰;
				$stud_addr_e=$stud_addr->路街;
				$stud_addr_f=$stud_addr->段;
				$stud_addr_g=$stud_addr->巷;
				$stud_addr_h=$stud_addr->弄;
				$stud_addr_i=$stud_addr->號;
				$stud_addr_j=$stud_addr->之;
				$stud_addr_k=$stud_addr->樓;
				$stud_addr_l=$stud_addr->樓之;
				$stud_addr_m=$stud_addr->其他;
				*/
				
				
				//班級性質
				$stud_class_kind=iconv("UTF-8","Big5",$basis_data->學生班級性質->班級性質);
				$stud_class_kind=array_search($stud_class_kind,stud_class_kind()); //轉換班級性質代碼
				
				$stud_spe_kind=iconv("UTF-8","Big5",$basis_data->學生班級性質->特教班類別);
				$stud_spe_kind=array_search($stud_spe_kind,stud_spe_kind()); //轉換特教班類別代碼
				
				$stud_spe_class_kind=iconv("UTF-8","Big5",$basis_data->學生班級性質->特教班班別);
				$stud_spe_class_kind=array_search($stud_spe_class_kind,stud_spe_class_kind());  //轉換特教班班別代碼
				
				$stud_spe_class_id=iconv("UTF-8","Big5",$basis_data->學生班級性質->特殊班上課性質);
				$stud_spe_class_id=array_search($stud_spe_class_id,stud_spe_class_id()); //轉換特殊班上課性質代碼
				
				//入學前教育資料
				$kindergarden=$basis_data->入學前教育資料->幼稚園入學;
				$stud_preschool_status=iconv("UTF-8","Big5",$kindergarden->幼稚園入學資格);
				$stud_preschool_status=array_search($stud_preschool_status,stud_preschool_status());  //轉換入學資格代碼
				$stud_preschool_id=$kindergarden->幼稚園_教育部學校代碼;
				$stud_preschool_name=$kindergarden->幼稚園_學校名稱;
				
				$elementary=$basis_data->入學前教育資料->國小入學;
				$stud_mschool_status=iconv("UTF-8","Big5",$elementary->國小入學資格);
				$stud_mschool_status=array_search($stud_mschool_status,stud_preschool_status());  //轉換入學資格代碼			
				$stud_mschool_id=$elementary->國小_教育部學校代碼;
				$stud_mseschool_name=$elementary->國小_學校名稱;
				
				
				//取得學生異動-轉入學生的 student_sn 和 stud_id
				$SQL="SELECT student_sn,stud_id FROM stud_base WHERE stud_study_cond=0 AND trim(stud_person_id)='$stud_person_id' AND stud_sex='$stud_sex' AND stud_birthday='$stud_birthday' ORDER BY student_sn DESC";  // AND trim(stud_name)='$stud_name'  不比對姓名  改比對出生年月日
				$SQL=iconv("UTF-8","Big5",$SQL);
				$result= $CONN->Execute($SQL) or user_error("無法擷取學生基本資料! <br><br>$SQL",256);
				//$row = $result->FetchRow();			
				$stud_id=$result->fields["stud_id"];
				$student_sn=$result->fields["student_sn"];
				$messages=iconv("UTF-8","Big5","<BR><FONT COLOR=#0000FF>#※學生編號:$student_sn 　※學號:$stud_id 　※姓名:$stud_name 　※身分證字號:$stud_person_id ※出生年月日:$stud_birthday</FONT>");
				echo $messages;
				switch ($result->recordcount()) {
					case 0:
						$messages.=iconv("UTF-8","Big5","<BR>　#找不到此生的在籍學籍紀錄, 系統無法匯入資料。 <BR><BR> 請比對兩者的[身分證字號]、[性別]與[出生年月日]是否正確！<BR><BR> 或 請<a href='../stud_move'>按此連結至 學生異動(stud_move)模組</a> 預先作業!!");
						break;
					case 1:
						//輔導資料參照陣列
						$sse_relation_arr=sfs_text(iconv("UTF-8","Big5","父母關係"));
						$sse_family_kind_arr=sfs_text(iconv("UTF-8","Big5","家庭類型"));
						$sse_family_air_arr=sfs_text(iconv("UTF-8","Big5","家庭氣氛"));
						$sse_teach_arr=sfs_text(iconv("UTF-8","Big5","管教方式"));
						$sse_live_state_arr=sfs_text(iconv("UTF-8","Big5","居住情形"));
						$sse_rich_state_arr=sfs_text(iconv("UTF-8","Big5","經濟狀況"));
		
						$sse_arr= array("1"=>"喜愛困難科目","2"=>"喜愛困難科目","3"=>"特殊才能","4"=>"興趣","5"=>"生活習慣","6"=>"人際關係","7"=>"外向行為","8"=>"內向行為","9"=>"學習行為","10"=>"不良習慣","11"=>"焦慮行為");
						while(list($id,$val)= each($sse_arr)){
							$temp_sse_arr = sfs_text(iconv("UTF-8","Big5","$val"));
							${"sse_arr_$id"} = $temp_sse_arr;
						}
						//echo iconv("UTF-8","Big5","※準備匯入.....※姓名:$stud_name 　※身分證字號:$stud_person_id<BR><BR>");
						include "import_basis.php";
						include "import_seme.php";
						include "import_move.php";
						include "import_inner.php";
						include "import_career.php";	//社團及服務學習記錄  2016.11.08 新增
						break;
					default:
						$messages.=iconv("UTF-8","Big5","<BR>#※姓名:$stud_name 　※身分證字號:$stud_person_id ※出生年月日:$stud_birthday 此生在籍紀錄有".$result->recordcount()."筆, 系統放棄匯入, 請檢查!!<BR><BR>");
						break;
				}
			}
		}
	} else { exit('無法讀取XML檔案!'); }
}

$main=iconv("UTF-8","Big5","<form action =\"{$_SERVER['PHP_SELF']}\" enctype=\"multipart/form-data\" method=post>
<BR><font size=2 color='red'>◎XML匯入是補登轉學生學籍資料最有效率的方式，若您未取得學生的XML交換檔案，請<a href='stud_data_patch.php'> 按此 </a>進行手動補登！</font><BR><BR>
※欲匯入的XML檔：<input type=file name=\"xmlfile\" size=60>
<input type=\"submit\" name=\"go\" value=\"Go\">
</form>");
echo ($messages?$messages:$main);

foot();
?> 