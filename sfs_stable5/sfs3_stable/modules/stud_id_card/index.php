<?php
//$Id: index.php 9152 2017-09-29 04:14:07Z tuheng $
include "config.php";

//認證
sfs_check();

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}else{
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期
}

//主選單設定
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$act=$_REQUEST[act];

//主要內容
if($_REQUEST[error]==1){
	user_error("該班級無學生資料，無法繼續。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的學生資料輸入系統中。
	<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php'>".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php</a>)</ol>",256);
}elseif($act=="make"){
	downlod_ar($_REQUEST[stud_id],$_REQUEST[class_id],$sel_year,$sel_seme,"ooo");
}elseif($act=="make2"){
	downlod_ar($_REQUEST[stud_id],$_REQUEST[class_id],$sel_year,$sel_seme,"ooo2");
}elseif($act=="make3"){
	downlod_ar($_REQUEST[stud_id],$_REQUEST[class_id],$sel_year,$sel_seme,"ooo3");
}else{
	$main=&main_form($sel_year,$sel_seme,$_REQUEST[class_id],$_REQUEST[stud_id]);
}

//秀出網頁布景標頭
head("學生證列印模組");
?>


<script language="JavaScript">
<!-- Begin
function jumpMenu_seme(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + "&class_id=<?php echo $_REQUEST[class_id]?>";
}

function jumpMenu_seme_1(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=<?php echo $_REQUEST[year_seme]?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
}
//  End -->
</script>


<?php
echo $main;

//佈景結尾
foot();

function &main_form($sel_year,$sel_seme,$class_id,$stud_id){
	global $CONN,$sch_montain_p,$sch_mark_p,$sch_class_p,$UPLOAD_URL,$school_menu_p,$performance,$SFS_PATH_HTML;
	
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu_seme_1",$_REQUEST['class_id']);
	
	//取得學生選單	
	if(empty($class_select) or empty($date_select))	header("location:{$_SERVER['PHP_SELF']}?error=1&year_seme=$_REQUEST[year_seme]");
	
	
	
	if(!empty($class_id)){
		//取得學生資料
		$all_stud=get_stud_data($class_id);
		$n=0;
		foreach($all_stud as $stud_id=>$stu){
			$all.="<tr bgcolor='#FFFFFF'>
			<td valign=top align=center>$stud_id</td>
			<td valign=top align=center>$stu[stud_name]</td>
			<td valign=top align=center>$stu[stud_sex]</td>
			<td valign=top align=center>$stu[stud_birthday]</td>
			<td valign=top align=center>$stu[stud_person_id]</td>
			<td valign=top align=center>$stu[guardian_name]</td>
			</tr>";
			$n++;
		}
		
	}
    $tool_bar=&make_menu($school_menu_p);
    
   //製作按鈕
	$make_button=" ◎印表後無法看到條碼，請<a href='../charge/IDAutomationHC39M.ttf'> 按此下載條碼字型 </a>後安裝至作業系統◎<br>
	<input type='button' value='下載全班 $n 人的學生證(9張/頁)' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=make&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'>	
	<input type='button' value='下載全班 $n 人的學生證(6張/頁)' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=make2&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'>
	<input type='button' value='下載全班含上傳照片的借書證(9張/頁)' onclick=\"window.location.href='{$_SERVER['PHP_SELF']}?act=make3&sel_year=$sel_year&sel_seme=$sel_seme&Cyear=$Cyear&class_id=$class_id'\" class='b1'>	
	";

	$main="
	$tool_bar
	<table bgcolor='#c0c0c0' cellspacing=1 cellpadding=2 class='small'>
	<tr class='title_mbody'>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<td valign=top colspan=6>$date_select $class_select $make_button</td>
	</form>
	</tr>
	<tr class='title_mbody'>
	<td valign=top align=center>學號</td>
	<td valign=top align=center>學生姓名</td>
	<td valign=top align=center>性別</td>
	<td valign=top align=center>生日</td>
	<td valign=top align=center>身分證號</td>
	<td valign=top align=center>監護人</td>
	</tr>
	$all	
	</table>
	";
	return $main;
}

//取得學生資料
function get_stud_data($class_id=""){
	global $CONN;
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	$sql_select = "select a.stud_id, a.stud_name, a.stud_sex, a.stud_birthday, a.stud_person_id, a.stud_study_year, b.guardian_name from stud_base a left join stud_domicile b on a.student_sn=b.student_sn where a.curr_class_num like '".$class[2]."%' and a.stud_study_cond=0 order by a.stud_id";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($stud_id,$stud_name,$stud_sex,$stud_birthday,$stud_person_id,$stud_study_year,$guardian_name) = $recordSet->FetchRow()){
		$d=explode("-",$stud_birthday);
		$dy=$d[0]-1911;
		$birthday="中華民國".$dy."年".$d[1]."月".$d[2]."日";
			
		$stud[$stud_id][stud_name]=$stud_name;
		$stud[$stud_id][stud_sex]=($stud_sex=='1')?"男":"女";
		$stud[$stud_id][stud_birthday]=$birthday;
		$stud[$stud_id][guardian_name]=$guardian_name;
		$stud[$stud_id][by]=$dy;
		$stud[$stud_id][bm]=$d[1];
		$stud[$stud_id][bd]=$d[2];
		$stud[$stud_id][stud_person_id]=$stud_person_id;
		$stud[$stud_id][stud_study_year]=$stud_study_year;
	}
	return $stud;
}


//下載學生證
function downlod_ar($stud_id="",$class_id="",$sel_year="",$sel_seme="",$oo_path=""){
	global $CONN,$SFS_PATH_HTML;
	global $CONN,$UPLOAD_PATH;

	if ($oo_path=="ooo")
		$nums=9;
	elseif ($oo_path=="ooo2")
		$nums=6;
	elseif ($oo_path=="ooo3")
		$nums=9;
	//檔名種類
	if(!empty($stud_id)){
		$filename="STUD_ID_CARD_".$class_id."_".$stud_id.".sxw";
	}else{
		$filename="STUD_ID_CARD_".$class_id.".sxw";
	}
	
	//新增一個 zipfile 實例
	$ttt = new EasyZip;

	//加入 xml 檔案到 zip 中，共有五個檔案 
	//第一個參數為原始字串，第二個參數為 zip 檔案的目錄和名稱
	
	if (is_dir($oo_path)) { 
		if ($dh = opendir($oo_path)) { 
			while (($file = readdir($dh)) !== false) { 
				if($file=="." or $file==".." or $file=="content.xml" or $file=="Configurations2" or $file=="Thumbnails" or strtoupper(substr($file,-4))=='.SXW') {
					continue;
				}elseif(is_dir($oo_path."/".$file)){
					if ($dh2 = opendir($oo_path."/".$file)) { 
						while (($file2 = readdir($dh2)) !== false) { 
							if($file2=="." or $file2==".."){
								continue;
							}else{
								$data = $ttt->read_file($oo_path."/".$file."/".$file2);
								$ttt->add_file($data,$file."/".$file2);
							}
						} 
						closedir($dh2); 
					} 
				}else{
					$data = $ttt->read_file($oo_path."/".$file);
					$ttt->add_file($data,$file);
				}
			} 
			closedir($dh); 
		} 
	} 
	
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	
	//取得學校資料
	$s=get_school_base();
		
	//取得學生資料
	$all_stud=get_stud_data($class_id);
	
	
	//讀出 content.xml 
	$data = $ttt->read_file($oo_path."/content.xml");
	// 加入換頁 tag

	$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="BREAK_PAGE" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-before="page"/></style:style>',$data);
	
	//拆解 content.xml
	$arr1 = explode("<office:body>",$data);
	//檔頭
	$doc_head = $arr1[0]."<office:body>";
	$arr2 = explode("</office:body>",$arr1[1]);
	//資料內容
	$content_body = $arr2[0];
	//檔尾
	$doc_foot = "</office:body>".$arr2[1];
	$replace_data ="";
    
	$temp_arr["school_name"] = $s[sch_cname];

	$i=1;
	foreach($all_stud as $stud_id=>$stu){
		$temp_arr["stud_id".$i] = $stud_id;	
		$temp_arr["name".$i] = $stu[stud_name];
		$temp_arr["i".$i] = $stu[stud_sex];
		$temp_arr["birthday".$i] = $stu[stud_birthday];
		$temp_arr["by".$i] = $stu[by];
		$temp_arr["bm".$i] = $stu[bm];
		$temp_arr["bd".$i] = $stu[bd];
		$myphoto="../../data/photo/student/".$stu[stud_study_year]."/".$stud_id;
		$temp_arr["stud_id".$i."_photo"] = $SFS_PATH_HTML."/data/photo/student/".$stu[stud_study_year]."/".$stud_id;

		$temp_arr["stud_pid".$i] = $stu[stud_person_id];
		$temp_arr["parent".$i] = $stu[guardian_name];
        
		//彰化縣學校不顯示學生身份證字號及監護人姓名  98.05.22修正
		$pos=strpos($temp_arr["school_name"], "彰化縣");
		if($pos!==false){
            $temp_arr["stud_pid".$i]="**********";
            $temp_arr["parent".$i]="******";
        }
		if($i%$nums==0){
			// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
			$replace_data.= $ttt->change_temp($temp_arr,$content_body);
			$i=1;
		}else{
			$i++;
		}
	}
	
	if(($i-1)%$nums!=0){
		for("";$i<=$nums;$i++){
			$temp_arr["stud_id".$i] ="";	
			$temp_arr["name".$i] = "";
			$temp_arr["i".$i] = "";
			$temp_arr["birthday".$i] = "";
			$temp_arr["by".$i] = "";
			$temp_arr["bm".$i] = "";
			$temp_arr["bd".$i] = "";
			$temp_arr["stud_pid".$i] = "";
			$temp_arr["parent".$i] = "";
			$temp_arr["stud_id".$i."_photo"] = "";
		}
		$replace_data.= $ttt->change_temp($temp_arr,$content_body);
	}
	$replace_data =$doc_head.$replace_data.$doc_foot;
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = $ttt->file();

	//以串流方式送出 sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $sss;
	
	exit;
	return;
}
?>
