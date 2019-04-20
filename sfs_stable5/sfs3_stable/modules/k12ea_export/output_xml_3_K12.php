<?php
// $Id: output_xml.php 8928 2016-07-20 18:11:45Z smallduh $

require "config.php";
require "class.php";
set_time_limit(0);
ini_set('memory_limit', '100M');
sfs_check();

//取得班級數
$year=curr_year();
$seme=curr_seme();
//目前學期
$c_curr_seme=sprintf('%03d%1d',$year,$seme);

/*
$sql="select distinct c_year from school_class where year='{$year}' and semester='$seme' order by c_year";
$res=$CONN->Execute($sql) or die($sql);
$row=$res->getRows();
$select_year=array();
foreach ($row as $v) {
	$select_year[$v[c_year]]=$v['c_year']."年級";
}
*/

$all_reward=$_POST['all_reward'];
$select_year[99]="全校學生";

$selected_year=($_POST['output_selected'])?$_POST['output_selected']:99;

$all_reward_checked=$all_reward?"checked":"";
//國中加入生涯輔導輸出選項
$checked=$IS_JHOES?'checked':'';
$career_checkbox="<input type='checkbox' name='career' value=1 $checked>輸出國中生涯輔導手冊資料(需有安裝相關模組)";
$smarty->assign("career_checkbox",$career_checkbox);
$smarty->assign("select_year",$select_year);
$smarty->assign("selected_year",$selected_year);
$smarty->assign("all_reward_checked",$all_reward_checked);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","資科司3.0 XML匯出(國前署)");
$smarty->assign("SFS_MENU",$toxml_menu);
if ($_POST[output_xml]) {
	$smarty->assign("output",1);
} else {
	$smarty->assign("output",0);
}
$smarty->display("toxml_output_school_xml.tpl");


//把buffer 先全部送出
ob_flush();
flush();

//如果確定輸出XML檔案
if ($_POST[output_xml]) {
	if( ini_get('safe_mode') ){
		echo "Safe Mode 打開了! 請調整 php.ini 把它關閉!<br>";
	}else{
		// it's not
	}
	//取出所有選定的 sn
	switch ($_POST['output_selected']) {
		case '99':
			$sql="select count(*) from stud_base where stud_study_cond=0 or stud_study_cond=15";
			$res=$CONN->Execute($sql) or die($sql);
			$total=$res->fields[0];
			//計算要處理的班級數
			//$total = count($selected_student);
			$sql="select class_id from school_class where year='{$year}' and semester='$seme' order by class_id";
			$res=$CONN->Execute($sql) or die($sql);
			$class_row=$res->getRows();
			break;

	}


	//?示的?度??度，?位 px
	$width = 500;
	//每???的操作所占的?度??位?度
	$pix = $width / $total;
	//默??始的?度?百分比
	$progress = 0;
	echo "
         <script language=\"JavaScript\">
         <!--
         function updateProgress(sMsg, iWidth)
         {
          document.getElementById(\"status\").innerHTML = sMsg;
          document.getElementById(\"progress\").style.width = iWidth + \"px\";
          document.getElementById(\"percent\").innerHTML = parseInt(iWidth / ".$width." * 100) + \"%\";
          }
         -->
         </script>
    ";
	?>
	<div style="width:100%" id="process_show">
			<div style="margin:50px auto; padding: 8px; border: 1px solid gray; background: #EAEAEA; width: <?php echo $width+16; ?>px">
				<div style="padding: 0; background-color: white; border: 1px solid navy; width: <?php echo $width; ?>px">
					<div id="progress" style="padding: 0; background-color: #FFCC66; border: 0; width: 0px; text-align: center; height: 20px"></div>
				</div>
				<div id="status"></div>
				<div id="percent" style="position: relative; top: -34px; text-align: center; font-weight: bold; font-size: 8pt">0%</div>
			</div>
	</div>

	<?php
	ob_flush();
	flush();  //將資料 buffer 先輸出到瀏覽器
	//exit();

	$start_time=date("Y-m-d H:i:s");

	$ini_val=ini_get('upload_tmp_dir');
	$tmp_path = $ini_val ? $ini_val : sys_get_temp_dir();
	//檔名
	$filename=md5($SCHOOL_BASE['sch_id'].time());
	if (file_exists($tmp_path.'/'.$filename)) unlink($tmp_path.'/'.$filename);
	$i=$j=0;
	$out_xml_file = fopen($tmp_path.'/'.$filename, "a") or die("Unable to open file! 檔案：".$tmp_path.$filename);
	foreach ($class_row as $class) {

		$class_id=$class['class_id'];
		$seme_class=sprintf('%d%02d',substr($class_id,6,2),substr($class_id,9,2));

		$query="select b.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and b.seme_class='$seme_class' and b.seme_year_seme='$c_curr_seme' and (a.stud_study_cond=0 or a.stud_study_cond=15) order by seme_num";
		$res=$CONN->Execute($query) or die ($query);
		$row_stud=$res->getRows();
		$stud_arr=array();
		foreach ($row_stud as $v) {
			if ($v['student_sn']>0) {
				$stud_arr[$v['student_sn']]=$v['student_sn'];
			}
		}

		$xml_obj=new sfsxmlfile();
		$xml_obj->student_sn=$stud_arr;

		$xml_obj->output();

		//學校代碼 $school_edu_id
		$smarty->assign("school_edu_id",$SCHOOL_BASE['sch_id']);
		//學籍資料
		$smarty->assign("data_arr",$xml_obj->out_arr);
		//性別陣列
		$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
		//身份別陣列 (備註暫不產生)
		$smarty->assign("stud_kind_arr",stud_kind());
		//證照類別陣列
		$smarty->assign("id_kind_arr",stud_country_kind());
		//學生班級性質陣列
		$smarty->assign("class_kind_arr",stud_class_kind());

		//學生特殊班類別陣列
		$smarty->assign("spe_kind_arr",stud_spe_kind());
		//學生特殊班上課性質陣列
		$smarty->assign("spe_class_id_arr",stud_spe_class_id());
		//學生特殊班班別陣列
		$smarty->assign("spe_class_kind_arr",stud_spe_class_kind());
		//國中小判定 SFS 4.0 必須修正
		$smarty->assign("jhores",$IS_JHORES);
		//入學資格陣列
		$smarty->assign("preschool_status_arr",stud_preschool_status());

		//畢修業陣列
		$smarty->assign("grad_kind_arr",grad_kind());

		//存歿陣列
		$smarty->assign("is_live_arr",is_live());
		//與父關係陣列
		$smarty->assign("f_rela_arr",fath_relation());
		//與母關係陣列
		$smarty->assign("m_rela_arr",moth_relation());
		//與監護人關係陣列
		$smarty->assign("g_rela_arr",guardian_relation());
		//學歷陣列
		$smarty->assign("edu_kind_arr",edu_kind());
		//兄弟姐妹陣列
		$smarty->assign("bs_calling_kind_arr",bs_calling_kind());

		//生涯輔導考慮因素陣列
		$factor_items=array('self'=>'個人因素','env'=>'環境因素','info'=>'資訊因素');
		foreach($factor_items as $item=>$title){
			$factors[$item]=SFS_TEXT($title);
		}
		$smarty->assign("factors",$factors);

		//抓取各學期應出席日數
		$query="select * from seme_course_date order by seme_year_seme,class_year";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$current_seme_year_seme=$res->fields[seme_year_seme];
			$row_data=$res->FetchRow();
			$seme_course_date_arr[$current_seme_year_seme][$row_data['class_year']]=$row_data['days'];
		}
		$smarty->assign("seme_course_date_arr",$seme_course_date_arr);

		//處理進度
		$i+=count($stud_arr);
		$progress = $pix*$i;
		if ($i==$total) $progress=500;
		?>
		<script language="JavaScript">
			updateProgress("已處理 <?php echo $i; ?> 位....",<?php echo min($width, intval($progress)); ?>);
		</script>
		<?php

		ob_flush();
		flush(); //將資料 buffer 先輸出到瀏覽器
		ob_clean();


		//將smarty輸出的資料先cache住
		ob_start();
		$smarty->display("student_3_K12.tpl");
		$xmls=ob_get_contents();
		ob_end_clean();
		//ob_clean();
		//將空值以null取代
		$xmls=str_replace("><",">null<",$xmls);
		$xmls=str_replace("> <",">null<",$xmls);
		$xmls=str_replace("&","＆",$xmls);
		$xmls=str_replace("＆amp;","&amp;",$xmls);
		$xmls=str_replace("＆gt;","&gt;",$xmls);
		$xmls=str_replace("＆lt;","&lt;",$xmls);
		$xmls=str_replace("＆quot;","&quot;",$xmls);
		$xmls=str_replace("＆apos;","&apos;",$xmls);

		/* 依清大國前署需求, 取代某些該系統不接受字串 */
		//取代
		$xmls=str_replace("<證照種類>身分證字號</證照種類>","<證照種類>身分證</證照種類>",$xmls);
		$xmls=str_replace("<證照種類>null</證照種類>","<證照種類>身分證</證照種類>",$xmls);
		$xmls=str_replace("<國籍>null</國籍>","<國籍>中華民國</國籍>",$xmls);
		$xmls=str_replace("<班級性質>一般班</班級性質>","<班級性質>普通班</班級性質>",$xmls);
		$xmls=str_replace("<父管教方式>民主</父管教方式>","<父管教方式>民主式</父管教方式>",$xmls);
		$xmls=str_replace("<母管教方式>民主</母管教方式>","<母管教方式>民主式</母管教方式>",$xmls);
		$xmls=str_replace("<父管教方式>權威</父管教方式>","<父管教方式>權威式</父管教方式>",$xmls);
		$xmls=str_replace("<母管教方式>權威</母管教方式>","<母管教方式>權威式</母管教方式>",$xmls);
		$xmls=str_replace("<父管教方式>放任</父管教方式>","<父管教方式>放任式</父管教方式>",$xmls);
		$xmls=str_replace("<母管教方式>放任</母管教方式>","<母管教方式>放任式</母管教方式>",$xmls);
		$xmls=str_replace(" </學生姓名>","</學生姓名>",$xmls);

		//除去特殊符號造成的錯誤
		$xmls=preg_replace('/[\b]/', ' ', $xmls);   //Backspace鍵



		//處理第幾班
		$j++;
		//大於一個班再做去頭去尾合併動作
		if (count($class_row)>1) {
			if ($j==1) {
				//第一班去尾
				$s=strpos($xmls,"</學籍交換資料>");
				$xmls=substr($xmls,0,$s);
			} elseif ($j==count($class_row)) {
				//最末班去頭
				$s=strpos($xmls,"	<學生資料>");
				$xmls=substr($xmls,$s);
			} else {
				//中間班
				//去頭
				$s=strpos($xmls,"	<學生資料>");
				$xmls=substr($xmls,$s);
				//去尾
				$s=strpos($xmls,"</學籍交換資料>");
				$xmls=substr($xmls,0,$s);
			}

		}

		fwrite($out_xml_file, big5_to_utf8($xmls));
		//fwrite($out_xml_file, $xmls);
	}

	ob_end_clean();

	fclose($out_xml_file);

	$end_time=date("Y-m-d H:i:s");

	echo "<div style='text-align: center'>執行時間 ".substr($start_time,11)." -> ".substr($end_time,11)."

	 <span id='download_button'><input type='button' value='下載檔案' id='download'></div></span>
	</div>

	<Script>
	   $(\"#download\").click(function(){
	   	d = new Date();
	   	var now_time=d.toLocaleTimeString();
	    $(\"#download_button\").html('( 已於本機時間 '+now_time+' 下載，暫存檔已從伺服器系統中刪除! )')
	     window.location='output_xml_download.php?set=$filename';
	   });
	</Script>

	";




}



?>