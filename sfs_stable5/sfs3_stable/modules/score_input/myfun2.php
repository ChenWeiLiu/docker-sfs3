<?php

//$Id: myfun2.php 9080 2017-06-07 05:05:28Z chiming $

//存檔
function save_semester_score($sel_year,$sel_seme) {
	global $CONN,$now;

	//學期資料表名稱
	$score_semester="score_semester_".$sel_year."_".$sel_seme;

	$temp_sn = substr($_POST[student_sn_hidden],0,-1);
	$temp_arr = explode(",",$temp_sn);
	if($_POST[test_sort] == 255)
		$test_kind='全學期';
	elseif($_POST[test_kind] == 's1')
		$test_kind='定期評量';
	else
		$test_kind='平時成績';
		
	if ($_POST[test_sort] == 254){
		
		//檢查有無成績, 106.0607 fix 無法修改問題 by 麒富
		for ($i=1;$i<=$_POST[performance_test_times];$i++) {
			
		$query = "select student_sn,test_sort from $score_semester where ss_id='$_POST[ss_id]' and test_sort='{$i}' and test_kind='$test_kind' and student_sn in($temp_sn)";
		$temp_sn_arr = array();
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$temp_sn_arr[$res->fields[1]][]=$res->fields[0];
			$res->MoveNext();
		}
		
			reset($temp_arr);
			while(list($id,$val) = each($temp_arr)) {
				$class_id=student_sn_2_class_id($sel_year,$sel_seme,$val);
				$score = trim($_POST["s_$val"]);
				if($score=='') $score= -100;
				if (in_array($val,$temp_sn_arr[$i]))
					$query = "UPDATE $score_semester set score='$score',update_time='$now',teacher_sn='$_SESSION[session_tea_sn]' where class_id='$class_id' and ss_id='$_POST[ss_id]' and test_sort='$i' and test_kind='$test_kind' and student_sn='$val'";
				else
					$query = "insert INTO $score_semester(class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$val','$_POST[ss_id]','$score','$test_kind','$test_kind','$i','$now','$_SESSION[session_tea_sn]')";
				$CONN->Execute($query) or die($query);
			}
		}
	}else{
		//檢查有無成績
		$query = "select student_sn from $score_semester where ss_id='$_POST[ss_id]' and test_sort='$_POST[test_sort]' and test_kind='$test_kind' and student_sn in($temp_sn)";
		$temp_sn_arr = array();
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		while(!$res->EOF){
			$temp_sn_arr[]=$res->fields[0];
			$res->MoveNext();
		}
		while(list($id,$val) = each($temp_arr)) {
			$class_id=student_sn_2_class_id($sel_year,$sel_seme,$val);
			$score = trim($_POST["s_$val"]);
			if($score=='') $score= -100;
			if (in_array($val,$temp_sn_arr))
				$query = "UPDATE $score_semester set score='$score',update_time='$now',teacher_sn='$_SESSION[session_tea_sn]' where class_id='$class_id' and ss_id='$_POST[ss_id]' and test_sort='$_POST[test_sort]' and test_kind='$test_kind' and student_sn='$val'";
			else
				$query = "insert INTO $score_semester(class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$val','$_POST[ss_id]','$score','$test_kind','$test_kind','$_POST[test_sort]','$now','$_SESSION[session_tea_sn]')";
			$CONN->Execute($query) or die($query);
		}
	}
}



//匯到教務處
function seme_score_input($sel_year,$sel_seme) {
	global $CONN,$now,$yorn;
	//學期資料表名稱
	$score_semester="score_semester_".$sel_year."_".$sel_seme;

	$temp_sn = substr($_POST[student_sn_hidden],0,-1);
	$temp_arr = explode(",",$temp_sn);
	// 將 score_semester 的 sendmit 設為 0
	$test_str=($_POST[test_sort] != 254)?"and test_sort='$_POST[test_sort]'":"and test_kind='平時成績'";
	$query= "UPDATE $score_semester set sendmit='0' where student_sn in ($temp_sn) and ss_id='$_POST[ss_id]' $test_str";
	$CONN->Execute($query) or die($query);
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$query = "select student_sn from stud_seme_score where ss_id='$_POST[ss_id]' and seme_year_seme='$seme_year_seme' and student_sn in($temp_sn)";
	$temp_sn_seme_arr = "";
	$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	while(!$res->EOF){
		$temp_sn_seme_arr.="'".$res->fields[0]."',";
		$res->MoveNext();
	}

	if ($temp_sn_seme_arr<>""){
		$temp_sn_seme_arr=substr($temp_sn_seme_arr,0,-1);
		//先將文字描述取出
		$rs=$CONN->Execute("select student_sn,ss_score_memo from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn in ($temp_sn_seme_arr) and ss_id='$_POST[ss_id]'");
		while (!$rs->EOF) {
			$val_arr[$rs->fields['student_sn']]=addslashes($rs->fields['ss_score_memo']);
			$rs->MoveNext();
		}
	}

	//階段成績 平時成績
	if ($_POST[test_sort]<255) {
		//將班級字串轉為陣列
		if($_POST[class_id]) {
			$class_arr=class_id_2_old($_POST[class_id]);
			$class_year=$class_arr[3];
		}
		else {//取得年級
			$class_year=ss_id_2_class_year($_POST[ss_id]);
		}

		$query = "select performance_test_times,score_mode,test_ratio from score_setup where  class_year=$class_year and year=$sel_year and semester='$sel_seme' and enable='1'";
		$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		//測驗次數
		$performance_test_times = $res->fields[performance_test_times];
		//成績配分比例相關設定
		$score_mode = $res->fields[score_mode];
		//比率
		$test_ratios = $res->fields[test_ratio];
		 //比率換算
	        if($score_mode=="all"){
        	        $test_ratio=explode("-",$test_ratios);
       		 }
		//每階段評量都是不同比率
       		elseif($score_mode=="severally"){
			$temp_arr=explode(",",$test_ratios);
			while(list($id,$val) = each($temp_arr)){
				$test_ratio_temp=explode("-",$val);
				$test_ratio[$id][0]=$test_ratio_temp[0];
				$test_ratio[$id][1]=$test_ratio_temp[1];
			}
		}
	        else{
        	        $test_ratio[0]=60;
			$test_ratio[1]=40;
		}
	
		//如果每學期只設定一次學期平時成績且每階段評量比率皆不同時,比率為 100 - 各階段評量比率
	        if ($yorn =='n' and $score_mode=="severally"){
        	        $temp_ratio=0;
			for($i=0;$i<$performance_test_times;$i++)
	        	                $temp_ratio += $test_ratio[$i][0];
                	$temp_ratio = (100-$temp_ratio);
			
	        }
                                                                                                                             

		//計算學期成績
		//全學期都是一種設定
		if($score_mode=="all"){
			if($yorn =='y')
				$query = "select student_sn,test_kind,sum(score) as cc from $score_semester where ss_id=$_POST[ss_id] and student_sn in ($temp_sn) and test_sort <= $performance_test_times and score <> '-100' group by student_sn,test_kind ";
			else
				$query = "select student_sn,test_kind,sum(score) as cc from $score_semester where ss_id=$_POST[ss_id] and student_sn in ($temp_sn) and test_sort <= $performance_test_times and score <> '-100' and (test_kind='定期評量' or test_kind='平時成績') group by student_sn,test_kind";
//			echo $query."<BR>";
			$res = $CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			$score_arr = array();
			$test_ratio_1  = $test_ratio[0]/100;
			$test_ratio_2  = $test_ratio[1]/100;
			
			while(!$res->EOF){
				$student_sn = $res->fields[student_sn];
				$test_kind = $res->fields[test_kind];
				$score = $res->fields[cc];
				if ($score=='') $score=0;
				if ($test_kind == "定期評量")
					$cc = ($score/$performance_test_times)*$test_ratio_1;
				else {
					$cc = $score * $test_ratio_2 / $performance_test_times;
					
				}
//				echo "$student_sn --  $test_kind -- $test_ratio_1 --  $test_ratio_2 -- $cc <BR>";
				$score_arr[$student_sn] += $cc;
				$res->MoveNext();
			}
		}
		//每次評量都不同設定
		else {
			if ($yorn=='y')
				$query = "select student_sn,test_kind,test_sort,score from $score_semester where ss_id='$_POST[ss_id]' and student_sn in ($temp_sn) and test_sort<255 ";
			else
				$query = "select student_sn,test_kind,test_sort,score from $score_semester where ss_id='$_POST[ss_id]' and student_sn in ($temp_sn) and (test_kind='定期評量' or test_kind='平時成績')";
			$res = $CONN->Execute($query) or die($query);
			$temp_score= array();
			while(!$res->EOF){
				$test_sort = $res->fields[test_sort];
				$student_sn = $res->fields[student_sn];
				$test_kind = $res->fields[test_kind];
				$score = $res->fields[score];
				if ($score=="-100") $score=0;
				$id = $test_sort-1;
				if ($test_kind=='定期評量')
					$cc =  $score*$test_ratio[$id][0]/100;
                                else{
					$cc =  $score*$test_ratio[$id][1]/100;
                                }
						
				$score_arr[$student_sn] += $cc;
				$res->MoveNext();
			}
		}
//將成績填入學期成績檔
                while(list($id,$val) = each($score_arr)){
			$query = "replace into stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo,teacher_sn)values('$seme_year_seme','$id','$_POST[ss_id]','$val','".addslashes($val_arr[$id])."','$_SESSION[session_tea_sn]')";
                        $CONN->Execute($query) or die($query);
                }
	}
	//全學期一次成績
	else if ($_POST[test_sort] == 255) {
		//將成績填入學期成績檔
		reset($temp_arr); 
		while(list($id,$val) = each($temp_arr)){
			$score = trim($_POST["avg_hidden_$val"]);
			$query = "replace into stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo,teacher_sn)values('$seme_year_seme','$val','$_POST[ss_id]','$score','".addslashes($val_arr[$val])."','$_SESSION[session_tea_sn]')";
			$CONN->Execute($query) or die($query);
		}
		
	}

	
}

//成績檔匯出
function download_score($sel_year,$sel_seme) {
	global $CONN;
	//require_once "../../include/sfs_case_studclass.php";

	//學期資料表名稱
	$score_semester="score_semester_".$sel_year."_".$sel_seme;
	$class_id = $_POST[class_id];
	$ss_id = $_POST[ss_id];
	$test_sort = $_POST[test_sort];
	if($test_sort==255){
		$test_kind_num="all";
		$test_kind = "全學期";
	}
	else if ($_POST[test_kind] == 's1'){
		$test_kind = "定期評量";
		$test_kind_num ="1";
	}
	else{
		$test_kind = "平時成績";
		$test_kind_num ="2";
	}
	$filename="semescore_".$class_id."_".$ss_id."_".$_POST[test_sort]."_".$test_kind_num.".csv";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	
	$class_id_name=explode("_",$class_id);
	
	//  取出班名
	$class_sql="select * from school_class where class_id='$class_id' and enable=1";
	$rs_class=$CONN->Execute($class_sql) or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
	$c_year= $rs_class->fields['c_year'];
	$c_name= $rs_class->fields['c_name'];
	$school_kind_name=array("幼稚園","一年","二年","三年","四年","五年","六年","一年","二年","三年","一年","二年","三年");
	$full_year_class_name=$school_kind_name[$c_year];
	$full_year_class_name.=$c_name."班";
	$class_year_name =  $full_year_class_name;
	
	//$class_year_name=class_id_to_full_class_name($class_id);
	$subject_name=ss_id_to_subject_name($ss_id);
	$dl_time=date("Y m d H:i:s");
	if($test_kind_num=="all")
		$file_info="#".intval($class_id_name[0])."學年第".intval($class_id_name[1])."學期".$class_year_name.$subject_name.$test_kind."成績(".$dl_time.")\n";
	else
		$file_info="#".intval($class_id_name[0])."學年第".intval($class_id_name[1])."學期".$class_year_name.$subject_name."第".$test_sort."階段的".$test_kind."(".$dl_time.")\n";
	
	echo $file_info;
	echo "學生流水號,座號,姓名,成績\n";
	if ($test_sort == 254) $test_sort=1;
	$query = "select student_sn,score from $score_semester where class_id='$class_id' and test_sort='$test_sort' and test_kind='$test_kind' and ss_id='$ss_id' ";
	$res = $CONN->Execute($query) or trigger_error("SQL 錯誤 $query",E_USER_ERROR);
	while(!$res->EOF){
		$stud_temp_arr[$res->fields[student_sn]] = $res->fields[score];
		$res->MoveNext();
	}

	//取得學生姓名座號
	$temp_sn = substr($_POST[student_sn_hidden],0,-1);
	$query = "select student_sn,stud_name,curr_class_num from stud_base where student_sn in ($temp_sn) order by curr_class_num";
	$res = $CONN->Execute($query)or trigger_error($query);
	while(!$res->EOF){
		$sit_num = intval(substr($res->fields[curr_class_num],-2));
		echo $res->fields[student_sn].",".$sit_num.",\"".$res->fields[stud_name]."\",".$stud_temp_arr[$res->fields[student_sn]]."\n";
		$res->MoveNext();
	}


	exit;
}

//匯入檔案
function import_score($sel_year,$sel_seme) {
	global $CONN,$menu_p;

	$main="
            <table bgcolor=#FDE442 border=0 cellpadding=2 cellspacing=1 align='center'>
            <tr><td colspan=2  height=50 align='center'><font size='+2'>成績檔案匯入</font></td></tr>
            <form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
            <tr><td  nowrap valign='top' bgcolor='#E1ECFF' width=40%>
            <p>請按『瀏覽』選擇匯入檔案來源：</p>
            <input type=file name='scoredata'>
            <input type=hidden name='score_semester' value='$score_semester'>
            <input type=hidden name='class_id' value='$_POST[class_id]'>
            <input type=hidden name='test_sort' value='$_POST[test_sort]'>
            <input type=hidden name='test_kind' value='$_POST[test_kind]'>
            <input type=hidden name='ss_id' value='$_POST[ss_id]'>
            <input type=hidden name='teacher_course' value='$_POST[teacher_course]'>
	    <input type=hidden name='student_sn_hidden' value='$_POST[student_sn_hidden]'>
	    <input type=hidden name='performance_test_times' value='$_POST[performance_test_times]'>
            <input type=hidden name='err_msg' value='檔案格式錯誤<a href=./manage.php?teacher_course=$_POST[teacher_course]&curr_sort=$_POST[test_sort]> <<上一頁>></a>'>
            <p><input type=submit name='file_date' value='成績檔案匯入'></p>
            <b>".$_POST['err_msg']."</b>
            </td>
            <td valign='top' bgcolor='#FFFFFF'>
            說明：<br>
            <ol>
                <li>成績csv檔，請勿寫上成績，程式會由第三行開始讀取</li>
                <li>成績csv檔的第一行key上標題，方便將來自己的查詢</li>
                <li>檔案內容格式如下：(由第三行開始的第一欄為學生流水號，第二欄為座號, 第三欄為姓名,第四欄為分數)</li>
            </ol>
    <pre>
    #91學年第1學期一年一班數學第2階段的定期評量
    2001,1,陳XX,56
    2002,2,李XX,76
    2003,3,張XX,56
    2004,4,吳XX,22
    1005,5,林XX,99
    1006,6,王XX,65
    1007,7,鐘XX,87
    1008,8,李XX,88
    </pre>
            </td>
            </tr>
            </table>
            </form>";
	
       	head("成績檔案匯入");
	print_menu($menu_p,"teacher_course=$_POST[teacher_course]");
        echo $main;
	foot();
	exit;
}

function save_import_score($targetFile = 'manage2.php') {
	global $CONN;	
	
	$ss_id = $_POST[ss_id];
	$test_sort = $_POST[test_sort];
	$teacher_course = $_POST[teacher_course];
	$class_id = $_POST[class_id];
	$class_id_array=explode("_",$_POST[class_id]);
	$seme_year_seme=$class_id_array[0].$class_id_array[1];
	$seme_class=intval($class_id_array[2]).$class_id_array[3];
	$score_semester = "score_semester_".intval($class_id_array[0])."_".intval($class_id_array[1]);
	$update_time = date("Y-m-j H:m:s");
	if ($test_sort == 255){
		$test_kind="全學期";
		$update_str=" test_sort=255 ";
		$test_kind_num = "all";
	}
	elseif($_POST[test_kind] =='s1'){
		$test_kind="定期評量";
		$update_str = " test_kind='定期評量'";
		$test_kind_num = "1";
	}
	elseif($_POST[test_kind] =='s2'){
		$test_kind="平時成績";
		$update_str = " test_kind='平時成績'";
		$test_kind_num = "2";
	}
	
	//檢查檔名題否相符
	$filename="semescore_".$class_id."_".$ss_id."_".$test_sort."_".$test_kind_num.".csv";
	if (strcmp($filename,$_FILES['scoredata']['name'])!= 0){
		echo "匯入檔名錯誤 !! ,請找尋 $filename 檔名匯入!!";
		exit;
	}
		
	//檢查有無成績記錄
	$student_sn_hidden = substr($_POST[student_sn_hidden],0,-1);
	if ($_POST[test_sort] == 254){
		$query ="select student_sn,test_sort from $score_semester where student_sn in ($student_sn_hidden) and class_id='$class_id' and ss_id='$ss_id' and test_kind='$test_kind'";
		$res = $CONN->Execute($query);
		$student_sn_arr = array();
		while(!$res->EOF){
			$student_sn_arr[$res->fields[1]][]=$res->fields[0];
			$res->MoveNext();
		}
		if ($_FILES['scoredata']['size'] >0 && $_FILES['scoredata']['name'] != ""){
			$fd = fopen ($_FILES['scoredata']['tmp_name'] ,"r");
			$i =0;
			while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
				if ($i++ < 2){//第一筆為抬頭
					$msg="第一筆為抬頭，不要keyin成績<br>";
					continue ;
				}
				$student_sn= trim($tt[0]);
				$stud_score = trim($tt[3]);
			//	if(strlen($stud_score)>3){
			//		echo $main;
			//		exit;
			//	}
				if($student_sn){
					if($stud_score=="")
						$stud_score="-100";
					echo $_POST[performance_test_times];
					for ($j=1;$j<=$_POST[performance_test_times];$j++) {
						if (in_array($student_sn,$student_sn_arr[$j]))
							$bobo= "update $score_semester SET score='$stud_score',update_time='$update_time',teacher_sn='$_SESSION[session_tea_sn]' where student_sn='$student_sn' and class_id='$class_id' and ss_id='$ss_id' and $update_str and test_sort='$j'";
						else
							$bobo="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$student_sn','$ss_id','$stud_score','$test_kind','$test_kind','$j','$update_time','$_SESSION[session_tea_sn]')";
//	     	     				echo $bobo."<BR>";              
						if($CONN->Execute($bobo))
							$msg.="--num ".$stud_site_num." --成功<br>";
						else
							$msg.="--num ".$stud_site_num." --失敗<br>";
					}
				}else{
					$msg.="--num ".$stud_site_num." --不存在<br>";
				}
			}
			header("Location: $targetFile?teacher_course=$teacher_course&curr_sort=$test_sort&msg=$msg");
		}
		else{
			echo $main;
			exit;
		}
	}else{
		$query ="select student_sn from $score_semester where student_sn in ($student_sn_hidden) and class_id='$class_id' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort'";
		$res = $CONN->Execute($query);
		$student_sn_arr = array();
		while(!$res->EOF) {
			$student_sn_arr[]=$res->fields[0];
			$res->MoveNext();
		}

		if ($_FILES['scoredata']['size'] >0 && $_FILES['scoredata']['name'] != ""){
			$fd = fopen ($_FILES['scoredata']['tmp_name'] ,"r");
			$i =0;
			while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
				if ($i++ < 2){//第一筆為抬頭
					$msg="第一筆為抬頭，不要keyin成績<br>";
					continue ;
				}
				$student_sn= trim($tt[0]);
				$stud_score = trim($tt[3]);
				//if(strlen($stud_score)>3){
				//	echo $main;
				//	exit;
				//}
				if($student_sn){
					if($stud_score=="")
						$stud_score="-100";
					if (in_array($student_sn,$student_sn_arr))
						$bobo= "update $score_semester SET score='$stud_score',update_time='$update_time',teacher_sn='$_SESSION[session_tea_sn]' where student_sn='$student_sn' and class_id='$class_id' and ss_id='$ss_id' and $update_str and test_sort='$test_sort'";
					else
						$bobo="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$student_sn','$ss_id','$stud_score','$test_kind','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')";
	//     	     			echo $bobo."<BR>";              
					if($CONN->Execute($bobo))
						$msg.="--num ".$stud_site_num." --成功<br>";
					 else
						$msg.="--num ".$stud_site_num." --失敗<br>";
				}
				else{
					$msg.="--num ".$stud_site_num." --不存在<br>";
				}
			}
			header("Location: $targetFile?teacher_course=$teacher_course&curr_sort=$test_sort&msg=$msg");
		}
		else{
			echo $main;
			exit;
		}
	}

}




//本校目前該年級該班級目前已有階段成績的選單
function select_stage2($year_seme,$year_name){
	global $CONN,$score_semester,$yorn;
	$sel_year = substr($year_seme,0,3);
	$sel_seme = substr($year_seme,-1);
	$c_year = substr($year_name,0,-2);
	$c_name = substr($year_name,-2);
	$score_semester="score_semester_".intval($sel_year)."_".$sel_seme;
	$class_id = sprintf("%03s_%s_%02s_%02s",$sel_year,$sel_seme,$c_year,$c_name);
	if ($yorn=='n')
		$sql="select DISTINCT test_sort from $score_semester where class_id='$class_id'  order by test_sort";
	else
		$sql="select DISTINCT test_sort from $score_semester where class_id='$class_id' and test_sort<>254  order by test_sort";

	$rs=&$CONN->Execute($sql) or die($sql);
        while (!$rs->EOF) {
		if($rs->fields[0]==255)
			$temp_name="全學期";
		elseif($rs->fields[0]==254)
			$temp_name="平時成績";
		else
			$temp_name="第".$rs->fields[0]."階段";
		$test_sort[$rs->fields[0]]= $temp_name;
		$rs->MoveNext();
        }
	return $test_sort;
}

//取得所有科目陣列
function get_all_subject_arr(){
	global $CONN;
	$query = "select subject_id,subject_name from score_subject";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$res_arr[$res->fields[0]] = $res->fields[1];
		$res->MoveNext();
	}
	return $res_arr;
}
//算出這個值是陣列中第幾大的，a是一個數，b是一個陣列
function  how_big2($a,$b){
	$sort=1;
	while(list($id,$val)=each($b)){
        	if($a<$val) $sort++;
    }
    return  $sort;
}

//是否每一次月考要配合一次平時成績
function  findyorn(){
	global $CONN;
	$rs_yorn=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='score_input' AND pm_item='yorn'");
	$yorn=$rs_yorn->fields['pm_value'];
	return $yorn;
}

function ss_id_2_class_year($ss_id){
	global $CONN;
	$sql="select class_year from score_ss where ss_id='$ss_id' and enable=1";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$class_year=$rs->fields['class_year'];
	return $class_year;
}

function student_sn_2_class_id($sel_year,$sel_seme,$student_sn){
	global $CONN;
	$sql="select seme_class from stud_seme where student_sn='$student_sn' and seme_year_seme='".sprintf("%03d",$sel_year).$sel_seme."'";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$seme_class=$rs->fields['seme_class'];
	$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,substr($seme_class,0,-2),substr($seme_class,-2,2));
	return $class_id;
}


?>
