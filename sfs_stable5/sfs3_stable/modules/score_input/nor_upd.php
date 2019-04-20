<?php
// $Id: nor_upd.php 8876 2016-04-21 03:07:51Z infodaes $
include "../../include/config.php";
include "my_fun.php";

//使用者認證
sfs_check();

$teach_id=$_SESSION['session_log_id'];
$teacher_sn = $_SESSION['session_tea_sn'];
$nor_score="nor_score_".curr_year()."_".curr_seme();
$teacher_course=$_POST[teacher_course];
$act=$_POST['act'];
$stage=($_POST[curr_sort])?$_POST[curr_sort]:$_POST[stage];
if ($stage==254) {
	$stage_str="全學期";
} elseif ($stage==255) {
	$stage_str="不分階段";
} else {
	$stage_str="第".$stage."階段";
}

//取得正確任教課程
$course_arr_all=get_teacher_course(curr_year(),curr_seme(),$teacher_sn,$is_allow);
$course_arr = $course_arr_all['course'];

// 檢查課程權限是否正確
$cc_arr=array_keys($course_arr);
$err=(in_array($teacher_course,$cc_arr))?0:1;


if ($err==0) {
	//成績檔案匯入
	if($act=="file_in"){
		$main="
			<table bgcolor=#FDE442 border=0 cellpadding=2 cellspacing=1 align='center'>
			<tr><td colspan=2 height=50 align='center'><font size='+2'>成績檔案匯入</font></td></tr>
			<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
			<tr><td  nowrap valign='top' bgcolor='#E1ECFF' width=40%>
			<p>請按『瀏覽』選擇匯入檔案來源：</p>
			<input type=file name='scoredata'>
			<input type=hidden name='act' value='file_in'>
			<input type=hidden name='teacher_course' value='$teacher_course'>
			<input type=hidden name='class_subj' value='{$_POST['class_subj']}'>
			<input type=hidden name='stage' value='$stage'>
			<input type=hidden name='freq' value='{$_POST['freq']}'>
			<input type=hidden name='err_msg' value='檔案格式錯誤<a href=./normal.php?teacher_course={$_POST['teacher_course']}&stage={$_POST['stage']}> <<上一頁>></a>'>
			<p><input type=submit name='file_date' value='成績檔案匯入'></p>
			<b>".$_POST['err_msg']."</b></td>
			<td valign='top' bgcolor='#FFFFFF'>
			說明：<br>
			<ol>
			<li>成績csv檔，請勿寫上成績，程式會由第二行開始讀取</li>
			<li>成績csv檔的第一行key上標題，方便將來自己的查詢</li>
			<li>檔案內容格式如下：(由第二行開始的第一欄為座號，第二欄為分數)</li>
			</ol>
	    <pre>
	    #".curr_year()."學年第".curr_seme()."學期".$course_arr[$teacher_course].$stage_str."的第".$_POST[freq]."次平時成績
	    1,56
	    2,76
	    3,56
	    4,22
	    5,45
	    6,65
	    7,87
	    8,85
	    9,23
	    10,55
	    11,45
	    12,78
	    </pre>
			</td>
			</tr>
			</table>
			</form>";
		if($_POST['file_date']=="成績檔案匯入"){
			$path_str = "temp/score/";
			set_upload_path($path_str);
			$temp_path = $UPLOAD_PATH.$path_str;
			$temp_file= $temp_path."score_$teacher_course.csv";
			if ($_FILES['scoredata']['size']>0 && $_FILES['scoredata']['name']!=""){
				copy($_FILES['scoredata']['tmp_name'] , $temp_file);
				$fd = fopen ($temp_file,"r");
				$i =0;
				while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
					if ($i++ == 0) {//第一筆為抬頭                        
						$msg.="第一筆為抬頭，不要keyin成績<br>";
						continue ;
					}//第一筆為抬頭
					$stud_site_num= trim($tt[0]);
					$stud_score= (float) trim($tt[1]);
					/*
					if((strlen($stud_site_num)>3) || (strlen($stud_score)>3) ){
						echo $main;
						exit;
					}
					*/
					if($stud_score>100 || $stud_score<0 ){
						echo $main;
						exit;
					}
					
					

					//找出student_sn
					$class_subj_array=explode("_",$_POST['class_subj']);
					$seme_year_seme=$class_subj_array[0].$class_subj_array[1];
					$seme_class=intval($class_subj_array[2]).$class_subj_array[3];
					$seme_num=$stud_site_num;
					$rs_stid=$CONN->Execute("select stud_id from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' and seme_num='$seme_num'");
					$stud_id=$rs_stid->fields['stud_id'];
					$rs_stsn=$CONN->Execute("select student_sn from stud_base where stud_id='$stud_id' and stud_study_cond=0 ");
					$student_sn=$rs_stsn->fields['student_sn'];
					if($student_sn){
						if($stud_score=="") $stud_score="-100";
						$bobo=$CONN->Execute("update $nor_score SET test_score='$stud_score' where stud_sn='$student_sn' and class_subj='{$_POST['class_subj']}' and stage='{$_POST['stage']}' and freq='{$_POST['freq']}' and teach_id='$teach_id'") or trigger_error("匯入失敗",256);

						if($bobo) $msg.="--num ".$stud_site_num." --成功<br>";
						else $msg.="--num ".$stud_site_num." --失敗<br>";
					} else {
						$msg.="--num ".$stud_site_num." --不存在<br>";
					}
				}
				header("Location:./normal.php?teacher_course={$_POST['teacher_course']}&stage={$_POST['stage']}&msg=$msg");
			} else {
				echo $main;
				exit;
			}
		}
		echo $main;
	}

	//成績檔案匯出
	elseif($act=="file_out"){
		$filename="norscore_".$_POST['class_subj']."_".$_POST['stage']."_".$_POST['freq'].".csv";
		header("Content-disposition: filename=$filename");
		header("Content-type: application/octetstream ; Charset=Big5");
		//header("Pragma: no-cache");
						//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
		header("Expires: 0");
		$class_subj_name=explode("_",$_POST['class_subj']);
		$class_id=$class_subj_name[0]."_".$class_subj_name[1]."_".$class_subj_name[2]."_".$class_subj_name[3];
		$class_year_name=class_id_to_full_class_name($class_id);
		$subject_name=subject_id_to_subject_name($class_subj_name[4]);
		$dl_time=date("Y m d H:i:s");
		$file_info="#".curr_year()."學年第".curr_seme()."學期".$course_arr[$teacher_course].$stage_str."的第".$_POST[freq]."次平時成績，考試名稱：『".$_POST['test_name']."』，(".$dl_time.")\n";
		echo $file_info;
		$rs=&$CONN->Execute("select a.stud_sn,a.test_score,b.curr_class_num from $nor_score a,stud_base b where a.stud_sn=b.student_sn and a.class_subj='{$_POST['class_subj']}' and a.stage='$stage' and a.freq='{$_POST['freq']}' and a.enable=1 order by b.curr_class_num");
		$i=0;
		while(!$rs->EOF){
			$student_sn[$i]=$rs->fields['stud_sn'];
			$test_score[$i]=$rs->fields['test_score'];
			$student_info[$i]= substr($rs->fields[curr_class_num],-2);
			if($test_score[$i]=="-100") $test_score[$i]=" ";
			echo "\"".$student_info[$i]."\",\"".$test_score[$i]."\"\n";
			$i++;
			$rs->MoveNext();
		}
	}
} else
	header("Location:./normal.php?teacher_course={$_POST['teacher_course']}&stage=$stage");
?>
