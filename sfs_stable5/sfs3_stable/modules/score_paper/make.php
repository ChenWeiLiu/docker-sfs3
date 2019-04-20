<?php
// $Id: make.php 8599 2015-11-20 02:27:38Z qfon $

include "config.php";
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
$CHK_KIND=chk_kind();

/*
//取得任教班級代號
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);
if(empty($class_num)){
	$act="error";
	$error_title="無班級編號";
	$error_main="找不到您的班級編號，故您無法使用此功能。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的任教資料輸入系統中。
	</ol>";
}
*/

//主選單設定
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$act=$_REQUEST[act];

//執行動作判斷

if($act=="dlar"){
	downlod_ar($_POST[stud_id],$_POST[class_id],$_POST[sp_sn],$_POST[stu_num],$sel_year,$sel_seme);
	header("location: {$_SERVER['PHP_SELF']}?class_id=$_POST[class_id]&stud_id=$_POST[stud_id]");
}elseif($act=="dlar_all"){
	downlod_ar("",$_POST[class_id],$_POST[sp_sn],"",$sel_year,$sel_seme,"all");
	header("location: {$_SERVER['PHP_SELF']}?class_id=$_POST[class_id]");
}elseif($_REQUEST[error]==1){
	user_error("該班級無學生資料，無法繼續。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的學生資料輸入系統中。
	<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php'>".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php</a>)</ol>",256);
}else{
	$main=&main_form($sel_year,$sel_seme,$_REQUEST[class_id],$_REQUEST[stud_id]);
}


//秀出網頁
head("成績單製作");
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
foot();

function &main_form($sel_year,$sel_seme,$class_id,$stud_id){
	global $CONN,$sch_montain_p,$sch_mark_p,$sch_class_p,$UPLOAD_URL,$school_menu_p,$performance,$SFS_PATH_HTML,$CHK_KIND;
	
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu_seme_1",$_REQUEST['class_id']);
	
	//取得學生選單	
	if(empty($class_select) or empty($date_select))	header("location:{$_SERVER['PHP_SELF']}?error=1&year_seme=$_REQUEST[year_seme]");
	
	if(!empty($class_id)){
		//轉換班級代碼
		$class=class_id_2_old($class_id);
		//假如沒有指定學生，取得第一位學生
		if(empty($stud_id))$stud_id=get_no1($class_id);
		//若仍是沒有 $stud_id ，則秀出錯誤訊息
		if(empty($stud_id))header("location:{$_SERVER['PHP_SELF']}?error=1");
		
		
		$gridBgcolor="#DDDDDC";
		//已製作顯示顏色
		$over_color = "#223322";
		//左選單女生顯示顏色
		$non_color = "blue";
		
		$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單	   	
		$grid1->key_item = "stud_id";  // 索引欄名 
		$grid1->formname = "myform";
		$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
		$grid1->bgcolor = $gridBgcolor;
		$grid1->display_color = array("1"=>"blue","2"=>"red");
		$grid1->color_index_item ="stud_sex" ; //顏色判斷值
		$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
		$seme_class_seme = sprintf("%03s%d",$class[0],$class[1]);
		$class_num = $class[2];
		$grid1->sql_str = "select a.stud_id,a.stud_name,a.stud_sex,b.seme_num as sit_num from stud_base a,stud_seme b where a.stud_id=b.stud_id  and a.stud_study_cond=0 and  b.seme_year_seme='$seme_class_seme' and b.seme_class='$class_num' order by b.seme_num ";   //SQL 命令   
		//$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num  from stud_base where curr_class_num like '$class[2]%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令
		$grid1->do_query(); //執行命令 
	
		$stud_select = $grid1->get_grid_str($stud_id); // 顯示畫面
		
		
		if(!empty($stud_id)){
			
			if ($chknext && $nav_next<>'')	$stud_id = $nav_next;
		
			//求得學生ID	
			$student_sn=stud_id2student_sn($stud_id);
			
			//取得指定學生資料
			$stu=get_stud_baseB($student_sn,$stud_id);
			
			//座號
			$stu_class_num=curr_class_num2_data($stu['curr_class_num']);
			
			$score_paper_option=score_paper_option();
			$down_box="<div>
			<form action='{$_SERVER['PHP_SELF']}' method='post'>
			成績單格式：<select name='sp_sn'>
			$score_paper_option
			</select>
			<br>
			<input type='radio' name='act' value='dlar' checked>下載".$stu[stud_name]."的成績單<br>
			<input type='radio' name='act' value='dlar_all'>下載全班的成績單
			<input type='hidden' name='stud_id' value='$stud_id'>
			<input type='hidden' name='stu_num' value='$stu_class_num[num]'>
			<input type='hidden' name='class_id' value='$class_id'>
			<input type='hidden' name='year_seme' value='$_REQUEST[year_seme]'>
			<br>
			<input type='submit' value='下載'>
			</form>
			</div>";
		
	
			$stud_all="<b>".$stu[stud_name]."（".$stu_class_num[num]."號）</b>的成績資料如下：<a href='".$SFS_PATH_HTML."modules/score_paper/input.php?stud_id=$stud_id&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id'>輸入".$stu[stud_name]."的成績單</a><br>
			<table><tr><td valign=top>
			
			<table width=300 cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>";
			//取得學生資訊
			$studata=get_stud_base_arrayB($class_id,$stud_id,$student_sn);
			$stud_all.=make_list($studata,"學生資訊","","",false);
			
			//取得該學生日常生活表現評量值
			$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
			foreach($performance as $id=>$sk){
				$oth_array[$sk]=$oth_data['生活表現評量'][$id];
			}
			$stud_all.=make_list($oth_array,"生活表現評量","","",false);	
			
			//取得學期資訊
			$days=get_all_days($sel_year,$sel_seme,$class_id);
			$stud_all.=make_list($days,"學期資訊","","",false);
	
			//取得學生學期評語及分數
			$nor_value=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
			$stud_all.=make_list($nor_value,"學期總表現","","",false);

			//取得學生日常生活表現文字
			$nor_text=get_nor_text($student_sn,$sel_year,$sel_seme);
			$stud_all.=make_list($nor_text,"日常生活表現文字","","",false);

			//取得學生日常生活檢核文字
			$chk_text=get_chk_text($student_sn,$sel_year,$sel_seme,$CHK_KIND);
			$stud_all.=make_list($chk_text,"日常生活檢核文字","","",false);
			
			//取得學生缺席情況
			$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"標籤");
			$stud_all.=make_list($abs_data,"缺席情況","","",false);
			
			//取得學生缺席情況（成績單輸入版）
			$abs_data=get_abs_value($stud_id,$sel_year,$sel_seme,"標籤_成");
			$stud_all.=make_list($abs_data,"缺席情況（成績單輸入版）","","",false);
			
			//學生獎懲情況
			$reward_data=get_reward_value2($stud_id,$sel_year,$sel_seme);	
			$stud_all.=make_list($reward_data,"獎懲情況","","",false);
			
			//學生獎懲情況（成績單輸入版）
			$reward_data2=get_reward_value($stud_id,$sel_year,$sel_seme,"標籤_成");
			$stud_all.=make_list($reward_data2,"獎懲情況（成績單輸入版）","","",false);
			
			$stud_all.="</table></td><td valign=top>".$down_box."</td></tr></table><p>";
			
			//取得學生成績檔
			$stud_all.=get_score_value2($sel_year,$sel_seme,$stud_id,$student_sn,$class_id,$oth_data);
			
			
		}else{
			$stud_all="尚未選擇學生</td></tr><table>";
		}
		
	}
    $tool_bar=&make_menu($school_menu_p);
    
    //取得指定學生資料
	$stu=get_stud_baseB($student_sn,$stud_id);

	$main="
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing=0 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td bgcolor='#BDD3FF' valign=top>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td valign=top align=center>
  		$date_select<br>
  		$class_select<br>
  		$stud_select
  		</td></tr>
		</form>
		</table>
	</td><td valign=top>$stud_all</td></tr>
	</table>
	";
	return $main;
}

// 取得成績檔
function &get_score_value2($sel_year,$sel_seme,$stud_id,$student_sn,$class_id,$oth_data) {
	global $CONN,$ss9;

	$class=class_id_2_old($class_id);
	$cyear=$class[3];

	// 取得努力程度文字敘述
	//	$arr_1 = sfs_text("努力程度");
	// 取得課程每週時數
	$ss_num_arr = get_ss_num_arr($class_id);
	// 取得學習成就
	$ss_score_arr =get_ss_score_arr($class,$student_sn);
	
	$other_title="<td>節數</td><td>分數</td><td>加權</td><td>等第</td><td>努力程度</td><td>評語</td>";
	
	$main.="<p>";
	
	//自動偵測九年一貫科目標籤
	$ss9_array=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
	$yss9=array();
	
	
	//一個迴一個科目
	foreach($ss9 as $link_ss){
		//if($subject['need_exam']!='1')continue;
		$k="九_".$link_ss;
		$k1=$k."節數";
		$k2=$k."分數";
		$k3=$k."加權";
		$k4=$k."等第";
		$k5=$k."努力程度";
		$k6=$k."評語";
			
		$yss9[$k]=$link_ss;
		$other9[$k]=array(str_replace("{break_text}","<br>",$ss9_array[$k1]."節"),str_replace("{break_text}","<br>",$ss9_array[$k2]),str_replace("{break_text}","<br>",$ss9_array[$k3]),str_replace("{break_text}","<br>",$ss9_array[$k4]),str_replace("{break_text}","<br>",$ss9_array[$k5]),str_replace("{break_text}","<br>",$ss9_array[$k6]));
		//$other9[$k]=array($ss9_array[$k1]."節",$ss9_array[$k2],$ss9_array[$k3],$ss9_array[$k4],$ss9_array[$k5],$ss9_array[$k6]);
	}
	
	if(!empty($ss9_array)){
		$main.=make_list($yss9,"自動偵測九年一貫科目",$other_title,$other9)."<br>";
		
	}

	//九年一貫衍生標籤
	$otherm_title="<td>節數</td><td>分數</td><td>加權</td><td>等第</td>";
	$ssm_array=get_ssm_array($ss9_array,$class_id);
	$ssm=array("語文","學期總平均");
	foreach($ssm as $link_ss){
		$k="九_".$link_ss;
		$k1=$k."節數";
		$k2=$k."分數";
		$k3=$k."加權";
		$k4=$k."等第";

		$yssm[$k]=$link_ss;
		$otherm[$k]=array(str_replace("{break_text}","<br>",$ssm_array[$k1]."節"),str_replace("{break_text}","<br>",$ssm_array[$k2]),str_replace("{break_text}","<br>",$ssm_array[$k3]),str_replace("{break_text}","<br>",$ssm_array[$k4]));
	}
	$main.=make_list($yssm,"九年一貫衍生",$otherm_title,$otherm)."<br>";
	
	//取得科目陣列
	$ss_array=ss_array($sel_year,$sel_seme,$cyear,$class_id);
	$yss=array();
	
	foreach($ss_array as $ss_id=>$subject){
		if($subject[need_exam]!='1')continue;
		
		$k=$subject['name'];
		$yss[$k]=$subject['name'];
		$other[$k]=array($ss_num_arr[$ss_id]."節",$ss_score_arr[$ss_id]['ss_score'],$subject['rate'],$ss_score_arr[$ss_id]['score_name'],$oth_data["努力程度"]["$ss_id"],$ss_score_arr[$ss_id]['ss_score_memo']);
	}
	
	if(!empty($ss_array)){
		$main.=make_list($yss,"$cyear 年級科目",$other_title,$other)."<br>";
	}
	
	
	
	return $main;
}


//下載成績單
function downlod_ar($stud_id="",$class_id="",$sp_sn="",$stu_num="",$sel_year="",$sel_seme="",$mode=""){
	global $CONN,$UPLOAD_PATH,$UPLOAD_URL,$SFS_PATH_HTML,$line_color,$line_width,$draw_img_width,$draw_img_height;
	
	//轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_num=$class[2];
	
	
	//求得學生ID	
	$student_sn=stud_id2student_sn($stud_id);
	if($mode=="all"){
		//取得該班學生
		$all_stud_array=get_stud_array($sel_year,$sel_seme,$class[3],$class[4],"sn","id");
		make_ooo($sel_year,$sel_seme,$class_id,$sp_sn,$all_stud_array);
		
	}else{
		make_ooo($sel_year,$sel_seme,$class_id,$sp_sn,array($student_sn=>$stud_id));
	}
	
	return;
	
}



function make_ooo($sel_year,$sel_seme,$class_id,$sp_sn,$data_arr){
	global $CONN,$UPLOAD_PATH,$CHK_KIND;
	
	//Openofiice的路徑	
	$oo_path=$UPLOAD_PATH."score_paper/".$sp_sn;
	
	//檔名種類
	if($mode=="one"){
		$filename="score".$class_id.".sxw";
	}else{
		$filename="score".$stud_id.".sxw";
	}
	
	
	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
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
	
	
	//讀出 content.xml 
	$data = $ttt->read_file($oo_path."/content.xml");
	// 加入換頁 tag

	$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="sfs_break_page" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-before="page"/></style:style>',$data);
	
	//拆解 content.xml
	$arr1 = explode("<office:body>",$data);
	//檔頭
	$con_head = $arr1[0]."<office:body>";
	$arr2 = explode("</office:body>",$arr1[1]);
	//資料內容
	$con_body = $arr2[0];
	//檔尾
	$con_foot = "</office:body>".$arr2[1];
	$i=0;
	$replace_data ='';
	foreach($data_arr as $student_sn=>$stud_id){
		$i++;		
		//將 content.xml 的 tag 取代
	
		$temp = array();
		//取得學校資料
            $temp_arr = get_school_base_array();
		 
		//班級個人資料
		$temp[]=get_stud_base_arrayB($class_id,$stud_id,$student_sn);
		//出缺席資料
		$temp[]=get_abs_value($stud_id,$sel_year,$sel_seme,"標籤");
		//出缺席資料（成績單輸入版）
		$temp[]=get_abs_value($stud_id,$sel_year,$sel_seme,"標籤_成");
		//獎勵資料
		$temp[]=get_reward_value2($stud_id,$sel_year,$sel_seme);
		//獎勵資料（成績單輸入版）
		$temp[]=get_reward_value($stud_id,$sel_year,$sel_seme,"標籤_成");
		//總評與分數
		$temp[]=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
		//生活表現文字
		$temp[]=get_nor_text($student_sn,$sel_year,$sel_seme);
		//檢核表文字
		$temp[]=get_chk_text($student_sn,$sel_year,$sel_seme,$CHK_KIND);
		//生活表現評量
		$temp[]=get_performance_value($stud_id,$sel_year,$sel_seme);
		//取得學期資訊
		$temp[]=get_all_days($sel_year,$sel_seme,$class_id);
		//成績資料
		$temp[]=get_score_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		//九年一貫成績資料
		$ss9_array=get_ss9_array($sel_year,$sel_seme,$stud_id,$student_sn,$class_id);
		$temp[]=$ss9_array;
		//九年一貫衍生成績資料
		$temp[]=get_ssm_array($ss9_array,$class_id);

		foreach($temp as $t_arr){
			if (count($t_arr))
				$temp_arr = array_merge($temp_arr,$t_arr);
		}
		
		// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
		$replace_data .= $ttt->change_temp($temp_arr,$con_body);

		//換頁處理
		if ($i<count($data_arr))
			$replace_data .='<text:p text:style-name="sfs_break_page"/>';

	}

	$replace_data = $ttt->change_temp2(array("break_text"=>"<text:line-break/>"),$replace_data);	
	$replace_data = $con_head.$replace_data.$con_foot;

	//把一些多餘的標籤以空白取代
	$pattern[]="/\{([^\}]*)\}/";
	$replacement[]="";
	
	$replace_data=preg_replace($pattern, $replacement, $replace_data);
	
	
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");
	echo $sss;	
	exit;
}
?>
