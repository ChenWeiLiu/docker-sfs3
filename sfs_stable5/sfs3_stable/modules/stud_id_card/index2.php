<?php
//$Id: index2.php 9152 2017-09-29 04:14:07Z tuheng $
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
$act=$_REQUEST[act];

//主要內容
if($act=="make"){
	downlod_ar($_REQUEST[all_stud],$sel_year,$sel_seme,"ooo");
}elseif($act=="make2"){
	downlod_ar($_REQUEST[all_stud],$sel_year,$sel_seme,"ooo2");
}elseif($act=="make3"){
	downlod_ar($_REQUEST[all_stud],$sel_year,$sel_seme,"ooo3");
}else{
	$main=&main_form($sel_year,$sel_seme,$_REQUEST[all_stud]);
}

//秀出網頁布景標頭
head("學生證列印模組");

?>

<script language="JavaScript">
<!-- Begin
function submits(a){
	document.myform.act.value=a;
}
//  End -->
</script>

<?php

echo $main;

//佈景結尾
foot();

function &main_form($sel_year,$sel_seme,$stud_arr){
	global $CONN,$school_menu_p;

	if(count($stud_arr)!=0){
		//取得學生資料
		$all_stud=get_stud_data($stud_arr);
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
    
	$main="
	$tool_bar
	<table bgcolor='#c0c0c0' cellspacing=1 cellpadding=2 class='small'>
	<tr class='title_mbody' align='center'>
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
	<td colspan=6>
	學號<input type=text size=7 maxlength=7 name=all_stud[0] value=".$stud_arr[0]."> 學號<input type=text size=7 maxlength=7 name=all_stud[1] value=".$stud_arr[1]."> 學號<input type=text size=7 maxlength=7 name=all_stud[2] value=".$stud_arr[2]."><br>
	學號<input type=text size=7 maxlength=7 name=all_stud[3] value=".$stud_arr[3]."> 學號<input type=text size=7 maxlength=7 name=all_stud[4] value=".$stud_arr[4]."> 學號<input type=text size=7 maxlength=7 name=all_stud[5] value=".$stud_arr[5]."><br>
	學號<input type=text size=7 maxlength=7 name=all_stud[6] value=".$stud_arr[6]."> 學號<input type=text size=7 maxlength=7 name=all_stud[7] value=".$stud_arr[7]."> 學號<input type=text size=7 maxlength=7 name=all_stud[8] value=".$stud_arr[8]."><br>
	<input type='hidden' name='act' value=''>
	<input type='button' value='列出資料' class='b1' OnClick='this.form.submit();'>
	<input type='button' value='下載學生證(9張/頁)' class='b1' OnClick=\"submits('make');this.form.submit();\">
	<input type='button' value='下載學生證(6張/頁)' class='b1' OnClick=\"submits('make2');this.form.submit();\">
        <input type='button' value='下載全班含上傳照片的借書證(9張/頁)' class='b1' OnClick=\"submits('make3');this.form.submit();\">
	</form>
	</tr>
	<tr class='title_mbody'>
	<td valign=top align=center>學號</td>
	<td valign=top align=center>學生姓名</td>
	<td valign=top align=center>性別</td>
	<td valign=top align=center>生日</td>
	<td valign=top align=center>身份證號</td>
	<td valign=top align=center>監護人</td>
	</tr>
	$all	
	</table><br>
	<font color='red'>附註：若使用六張/頁套印，請每次最多輸入六位學生。</font>
	";
	return $main;
}

//取得學生資料
function get_stud_data($stud_arr){
	global $CONN;

	$all_id="'".implode("','",$stud_arr)."'";
	$sql_select = "select a.stud_id,a.stud_name,a.stud_sex,a.stud_birthday,a.stud_person_id, a.stud_study_year,b.guardian_name from stud_base a left join stud_domicile b on a.student_sn=b.student_sn where a.stud_id in ($all_id) and a.stud_study_cond=0 order by a.stud_id";
	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($stud_id,$stud_name,$stud_sex,$stud_birthday,$stud_person_id,$stud_study_year,$guardian_name) = $recordSet->FetchRow()){
		$d=explode("-",$stud_birthday);
		$dy=$d[0]-1911;
		$birthday="中華民國".$dy."年".$d[1]."月".$d[2]."日";
			
		$stud[$stud_id][stud_name]=$stud_name;
		$stud[$stud_id][stud_sex]=($stud_sex=='1')?"男":"女";
		$stud[$stud_id][stud_birthday]=$birthday;
		$stud[$stud_id][by]=$dy;
		$stud[$stud_id][bm]=$d[1];
		$stud[$stud_id][bd]=$d[2];
		$stud[$stud_id][stud_person_id]=$stud_person_id;
		$stud[$stud_id][guardian_name]=$guardian_name;
                $stud[$stud_id][stud_study_year]=$stud_study_year;
	}
	return $stud;
}

//下載學生證
function downlod_ar($stud_arr=array(),$sel_year="",$sel_seme="",$oo_path=""){
	global $CONN,$UPLOAD_PATH,$SFS_PATH_HTML;

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
        $ttt = new EasyZIP;

        // 設定 檔案目錄
        $ttt->setPath($oo_path);

        // 加入整個目錄
        $ttt->addDir("META-INF");

        // 加入檔案
        $ttt -> addFile("styles.xml");
        $ttt -> addFile("content.xml");
        $ttt -> addFile("meta.xml");
        $ttt -> addFile("settings.xml");

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
	
	
	//取得學校資料
	$s=get_school_base();
		
	//取得學生資料
	$all_stud=get_stud_data($stud_arr);
	
	
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
