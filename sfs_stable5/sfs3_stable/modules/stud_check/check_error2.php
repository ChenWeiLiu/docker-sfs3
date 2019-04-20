<?php
                                                                                                                             
// $Id: check_error2.php 5310 2009-01-10 07:57:56Z hami $


//載入設定檔
include "stud_check_config.php";

//認證檢查
sfs_check();

//全域變數轉換
$act=($_POST['act'])?"{$_POST['act']}":"{$_GET['act']}";
$stud_id=($_POST['stud_id'])?"{$_POST['stud_id']}":"{$_GET['stud_id']}";
//$c_curr_class=($_POST['c_curr_class'])?"{$_POST['c_curr_class']}":"{$_GET['c_curr_class']}";
$curr_year = curr_year();
$curr_seme = curr_seme();
$upd_file=$UPLOAD_PATH."upgrade/include/2003-06-24.txt";

if($act=="edit"){
	$c_curr_class=($_POST['c_curr_class'])?"{$_POST['c_curr_class']}":"{$_GET['c_curr_class']}";
	header("Location: $SFS_PATH_HTML"."modules/stud_reg/stud_list.php?stud_id=$stud_id&c_curr_class=$c_curr_class");
	exit;
}
elseif($act=="upd_tb"){		
	upd_fail($upd_file,$act);
	
}
//系統自動修護
elseif($act=="auto_upd"){
	$main_del.="<table width='100%' bgcolor='#CDCDCD'><tr><td>";
	$seme_year_seme=sprintf("%03d%d",$curr_year,$curr_seme);
	$del_student_sn=($_POST['student_sn'])?"{$_POST['student_sn']}":"{$_GET['student_sn']}";
	//到stud_base找出學號為$stud_id的流水號
	$sql="select student_sn from stud_base where stud_id='$stud_id' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$new_student_sn=$rs->fields['student_sn'];
	//$new_curr_class_num=$rs->fields['curr_class_num'];
	//比對不同則以stud_base為主
	if($del_student_sn!=$new_student_sn) {
		$sql2="update stud_seme set student_sn='$new_student_sn' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' ";
		$CONN->Execute($sql2) or trigger_error($sql,256);
		$main_del.="調整『學號： $stud_id 』的流水號為『 $new_student_sn 』";
		$del_msg.="---執行日期：".date("l dS of F Y h:i:s A")."---執行人：".$_SESSION['session_tea_name'] ."(".$_SESSION['session_log_id'] .")\n";
		$del_msg.="調整『學號： $stud_id 』的流水號為『 $new_student_sn 』";
	}else{
		$main_del.="該生『學號： $stud_id 』不需進行調整，請調整另外一位！";
	}
	$main_del.="</td></tr></table><br>";
	$del_msg.="\n\n";
	//順便寫入紀錄檔YouKill.log
	$dir_name= $UPLOAD_PATH."/log";	
	if(!is_dir ($dir_name)) mkdir ("$dir_name", 0777);	
	$file_name= $dir_name."/YouKill.log";	
	$FD=fopen ($file_name, "a");
	fwrite ($FD, $del_msg);	
	fclose ($FD);

}
elseif($act=="del_seme"){
	$del_student_sn=($_POST['student_sn'])?"{$_POST['student_sn']}":"{$_GET['student_sn']}";
	//刪除前檢查該生的成績資料是否有存在，有則不准刪除
	if($del_student_sn!=""){
		$sql_score="select count(*) from stud_seme_score where student_sn='$del_student_sn' ";
		$rs_score=$CONN->Execute($sql_score) or trigger_error($sql,256);
		$count_score=$rs_score->fields[0];
	}	
	if($count_score==0){  		
		$main_del.="<table width='100%' bgcolor='#CDCDCD'><tr><td>";
		$del_msg.="---執行日期：".date("l dS of F Y h:i:s A")."---執行人：".$_SESSION['session_tea_name'] ."(".$_SESSION['session_log_id'] .")\n";
		//將該生stud_seme給刪除掉
		$seme_year_seme=sprintf("%03d%d",$curr_year,$curr_seme);
		if($stud_id!="") $sql_del2="delete from stud_seme where student_sn='$del_student_sn' and seme_year_seme='$seme_year_seme' ";
		elseif($del_student_sn) $sql_del2="delete from stud_seme where stud_id='$stud_id' and seme_year_seme='$seme_year_seme' ";		
		$rs_del2=$CONN->Execute($sql_del2) or trigger_error($sql_del2,256);
		if($rs_del2) {
			$main_del.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學期資料表<br>";
			$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學期資料表\n";
		}
		$main_del.="</td></tr></table><br>";
		$del_msg.="\n\n";
		//順便寫入紀錄檔YouKill.log
		$dir_name= $UPLOAD_PATH."/log";	
		if(!is_dir ($dir_name)) mkdir ("$dir_name", 0777);	
		$file_name= $dir_name."/YouKill.log";	
		$FD=fopen ($file_name, "a");
		fwrite ($FD, $del_msg);	
		fclose ($FD);
		
		//提供該生可以還原的sql檔案
		
	}
	else {
		$main_del="<table width='100%' bgcolor='#CDCDCD'><tr><td>該生成績資料已經存在，不允許刪除。";
		$main_del.="<font color='red'>無論如何都要</font><a href='{$_SERVER['PHP_SELF']}?act=sure_del_seme&student_sn=$del_student_sn&stud_id=$stud_id'><font class='button'>刪除</font></a></td></tr></table><br>";
	}
}
elseif($act=="del"){
	$del_student_sn=($_POST['student_sn'])?"{$_POST['student_sn']}":"{$_GET['student_sn']}";
	//刪除前檢查該生的成績資料是否有存在，有則不准刪除
	$sql_score="select count(*) from stud_seme_score where student_sn='$del_student_sn' ";
	$rs_score=$CONN->Execute($sql_score) or trigger_error($sql,256);
	$count_score=$rs_score->fields[0];
	if($count_score==0){  		
		$main_del.="<table width='100%' bgcolor='#CDCDCD'><tr><td>";
		//將該生stud_base給刪除掉
		/*****準備將來備份用的，還沒好
		$sql_bk = "select * from stud_base where student_sn='$del_student_sn' ";
		$rs_bk = $CONN->Execute($sql_bk);
		if ($rs_bk) {
			while( $ar_bk = $rs_bk->FetchRow() ) {
				$ar_bk[$i];
			}
		}
		*/
		$del_msg.="---執行日期：".date("l dS of F Y h:i:s A")."---執行人：".$_SESSION['session_tea_name'] ."(".$_SESSION['session_log_id'] .")\n";
		$sql_del="delete from stud_base where student_sn='$del_student_sn' ";
		$rs_del=$CONN->Execute($sql_del) or trigger_error($sql_del,256);
		if($rs_del) {
			$main_del.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學生資料表<br>";
			$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學生資料表\n";
		}

		//將該生stud_seme給刪除掉
		//$seme_year_seme=sprintf("%03d%d",$curr_year,$curr_seme);
		$sql_del2="delete from stud_seme where student_sn='$del_student_sn' ";
		$rs_del2=$CONN->Execute($sql_del2) or trigger_error($sql_del2,256);
		if($rs_del2) {
			$main_del.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學期資料表<br>";
			$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的學期資料表\n";
		}

		//將該生stud_domicile給刪除掉
		$sql_del3="delete from stud_domicile where stud_id='$stud_id' ";
		$rs_del3=$CONN->Execute($sql_del3) or trigger_error($sql_del3,256);
		if($rs_del3) {
			$main_del.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的戶籍資料表<br>";
			$del_msg.="刪除 『流水號：$del_student_sn 』，『學號：$stud_id 』 的戶籍資料表\n";
		}

		$main_del.="</td></tr></table><br>";
		$del_msg.="\n\n";


		//順便寫入紀錄檔YouKill.log
		$dir_name= $UPLOAD_PATH."/log";	
		if(!is_dir ($dir_name)) mkdir ("$dir_name", 0777);	
		$file_name= $dir_name."/YouKill.log";	
		$FD=fopen ($file_name, "a");
		fwrite ($FD, $del_msg);	
		fclose ($FD);
		
		//提供該生可以還原的sql檔案
		
	}
	else {
		$main_del="<table width='100%' bgcolor='#CDCDCD'><tr><td>該生成績資料已經存在，不允許刪除。";
		$main_del.="<font color='red'>無論如何都要</font><a href='{$_SERVER['PHP_SELF']}?act=sure_del&student_sn=$del_student_sn&stud_id=$stud_id'><font class='button'>刪除</font></a></td></tr></table><br>";
	}
}


//殺無赦之二
elseif($act=="sure_del" or $act=="sure_del_seme"){
	$del_student_sn=($_POST['student_sn'])?"{$_POST['student_sn']}":"{$_GET['student_sn']}";
	$sql_sfs3="SHOW TABLES FROM sfs3";
	$rs_sfs3=$CONN->Execute($sql_sfs3) or trigger_error($sql_sfs3,256);
	if ($rs_sfs3) {
		$i=0;
		$main_del.="<table width='100%' bgcolor='#CDCDCD'><tr><td>";
		$del_msg.="---執行日期：".date("l dS of F Y h:i:s A")."---執行人：".$_SESSION['session_tea_name'] ."(".$_SESSION['session_log_id'] .")\n";
		while( $ar_sfs3 = $rs_sfs3->FetchRow() ) {			
			//判斷該資料表是否有stud_id，student_sn，stud_sn的欄位，有的話就刪除這一筆資料
			$sql_fields="show fields from {$ar_sfs3[0]}";
			$rs_fields=$CONN->Execute($sql_fields) or trigger_error($sql_fields,256);
			if($rs_fields){								
				while( $ar_fields = $rs_fields->FetchRow() ) {
					//echo $ar_fields[0];
					if($ar_fields[0]=="stud_id") {
						$a=$CONN->Execute("delete from $ar_sfs3[0] where stud_id='$stud_id'") or trigger_error("刪除 $ar_sfs3[0] 失敗！",256);
						if($a){
							$main_del.="刪除 $ar_sfs3[0] 資料表中學號為 $stud_id 的資料錄！<br>";
							$del_msg.="刪除 $ar_sfs3[0] 資料表中學號為 $stud_id 的資料錄！\n";
						}		  
						break;
					}	
					elseif($ar_fields[0]=="student_id") {
						$a=$CONN->Execute("delete from $ar_sfs3[0] where student_id='$stud_id'") or trigger_error("刪除 $ar_sfs3[0] 失敗！",256);
						if($a) {
							$main_del.="刪除 $ar_sfs3[0] 資料表中學號為 $stud_id 的資料錄！<br>";
							$del_msg.="刪除 $ar_sfs3[0] 資料表中學號為 $stud_id 的資料錄！\n";
						}
						break;
					}	
					elseif($ar_fields[0]=="stud_sn") {
						$a=$CONN->Execute("delete from $ar_sfs3[0] where stud_sn='$del_student_sn'") or trigger_error("刪除 $ar_sfs3[0] 失敗！",256);
						if($a) {
							$main_del.="刪除 $ar_sfs3[0] 資料表中學生流水號為 $del_student_sn 的資料錄！<br>";
							$del_msg.="刪除 $ar_sfs3[0] 資料表中學生流水號為 $del_student_sn 的資料錄！\n";	
						}	
						break;
					}
					elseif($ar_fields[0]=="student_sn") {
						$a=$CONN->Execute("delete from $ar_sfs3[0] where student_sn='$del_student_sn'") or trigger_error("刪除 $ar_sfs3[0] 失敗！",256);
						if($a) {
							$main_del.="刪除 $ar_sfs3[0] 資料表中學生流水號為 $del_student_sn 的資料錄！<br>";
							$del_msg.="刪除 $ar_sfs3[0] 資料表中學生流水號為 $del_student_sn 的資料錄！\n";
						}	
						break;
					}
				}
			}
		}
		$main_del.="</td></tr></table><br>";
		$del_msg.="\n\n";
	}
	//順便寫入紀錄檔YouKill.log
	$dir_name= $UPLOAD_PATH."/log";	
	if(!is_dir ($dir_name)) mkdir ("$dir_name", 0777);	
	$file_name= $dir_name."/YouKill.log";	
	$FD=fopen ($file_name, "a");
	fwrite ($FD, $del_msg);	
	fclose ($FD);
	
	//提供該生可以還原的sql檔案
}



//訊息顯示區
head("學籍資料檢查");
print_menu($menu_p);

//升級檢查---stud_seme的student_sn是否升級成功
if(is_file($upd_file)){//在已經紀錄升級的情況下檢查stud_seme的student_sn欄位是否加入成功
	$sql_ckupd="SELECT student_sn FROM stud_seme";
	$CONN->Execute($sql_ckupd) or upd_fail($upd_file);
}
else{
	echo "尚未升級，是否升級學生學期記錄表 stud_seme , 加入 student_sn 欄位<br>
	<a href='{$_SERVER['PHP_SELF']}?act=upd_tb'><font class='button'>執行升級</font></a>";	
	exit;
}


//資料檢查區
//-------------------------------------------------------------------------------
	$sql="select * from stud_base where stud_study_cond='0' order by curr_class_num";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$i=0;
	$err_1=0;
	$error_1=array();
	$stud_id=array();
	$student_sn=array();
	while(!$rs->EOF){
		$student_sn=$rs->fields['student_sn'];
		$stud_id[$student_sn]=$rs->fields['stud_id'];		
		//echo $student_sn[$i]."<br>";
		$stud_name[$student_sn]=$rs->fields['stud_name'];
		$stud_sex[$student_sn]=$rs->fields['stud_sex'];
		$stud_birthday[$student_sn]=$rs->fields['stud_birthday'];
		$st=$rs->fields['stud_person_id'];
		$stud_person_id[$student_sn]=$st;
		if (!isIDnum($st)){
			$error_1[$err_1]=$student_sn;
			$err_1++;
		}
		$stud_study_year[$student_sn]=$rs->fields['stud_study_year'];
		$curr_class_num[$student_sn]=$rs->fields['curr_class_num'];
		$stud_study_cond[$student_sn]=$rs->fields['stud_study_cond'];						
		$c_curr_class[$student_sn] = sprintf("%03d_%d_%02d_%02d",$curr_year,$curr_seme,substr($curr_class_num[$student_sn],0,-4),substr($curr_class_num[$student_sn],-4,2));
		$rs->MoveNext();
		$i++;
	}
	
	$class_name_array=class_base();
	
	//列出身分證為空或不正確的人員
	$main0.= "<font class='title1'>以下為身分證不正確或是未填的在籍學生</font><br>";
	$main0.= "<ul>";
	$j=0;
	while(list($k,$v)=each($error_1)) {
		$class_name_str[$k]=substr($curr_class_num[$v],0,-2);
		$class_name[$k]=$class_name_array[$class_name_str[$k]];
		if($class_name[$k]=="") $class_name[$k]="<font color='#D61414'>無班級</font>";
		if (empty($stud_person_id[$v])){
			$main0.="<li>身分證未填 ： ".$class_name[$k]." / 學號：".$stud_id[$v]." / 姓名：".$stud_name[$v]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$v]}&c_curr_class={$c_curr_class[$v]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$v&stud_id={$stud_id[$v]}'><font class='button'>刪除</font></a></li>";
			$j++;
		} else {
			$main0.="<li>身分證 <font color='#E66661'>$stud_person_id[$v] </font>錯誤 ： ".$class_name[$k]." / 學號：".$stud_id[$v]." / 姓名：".$stud_name[$v]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$v]}&c_curr_class={$c_curr_class[$v]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$v&stud_id={$stud_id[$v]}'><font class='button'>刪除</font></a></li>";
			$j++;
		}
	}
	$main0.= "</ul>";
	if($j=="0") $main0.="無身分證錯誤的在籍學生！";
		
	//檢查是否有同一身分證而有兩個以上學號的在籍學生	
	$main1.= "<hr><font class='title1'>以下為同一身分證而有兩個以上學號的在籍學生</font><br>";	
	$sql="select student_sn,stud_person_id from stud_base where stud_study_cond='0' order by stud_person_id,curr_class_num";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$j=0;
	$stsn="";
	$k=$rs->recordcount();
	$m=0;
	$err=0;
	if ($k>0) {
		$pid[0]=$rs->fields['stud_person_id'];
		$stsn[0]=$rs->fields['student_sn'];
		$rs->MoveNext();
		for ($i=1;$i<$k;$i++) {
			$pid[$i]=$rs->fields['stud_person_id'];
			$stsn[$i]=$rs->fields['student_sn'];
			if ($pid[$i]==$pid[$i-1] && $pid[$i]!=""){
				if ($j>0) $j++;
				else {
					$j=2;
					$m=$i;
					$main1.="<br><ul>";
					$err=1;
				}
			}
			if ($j>0) {
				$sn=$stsn[$i-1];
				$class_name_str[$sn]=substr($curr_class_num[$sn],0,-2);
				$class_name[$sn]=$class_name_array[$class_name_str[$sn]];
				if($class_name[$sn]=="") $class_name[$sn]="<font color='#D61414'>無班級</font>";
				$main1.= "<li>".$class_name[$sn]." / 學號：".$stud_id[$sn]." / 姓名：".$stud_name[$sn]."  <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$sn]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$sn&stud_id={$stud_id[$sn]}'><font class='button'>刪除</font></a></li>";
				$j--;
			}
			if ($j==0 && $m!=0) {
				$main1.= "</ul>　同時擁有身分證號：<font class='title2'>".$stud_person_id[$stsn[$i-1]] ."</font>的在籍學生有以上<font class='title2'>".($i-$m+1)."</font>人，建議進行調整<br><br>";
				$m=0;
			}
			$rs->MoveNext();
		}
	}
	if($err==0) $main1.="<br>無同一身分證而有兩個以上學號的在籍學生！";
	
	
	//在stud_base存在的情況下與stud_seme做配對，兩者是以student_sn和stud_id做聯繫的，針對本學期
	$main2.="<hr><font class='title1'>以下為同一個學生流水號在本學期擁有兩個以上學期資料者</font><br>";
	$seme_year_seme=sprintf("%03d%d",$curr_year,$curr_seme);
	$sql="select * from stud_seme where seme_year_seme='$seme_year_seme' order by stud_id,seme_class,seme_num";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$i=0;
	$stsn="";
	$err=0;
	while(!$rs->EOF){
		$stsn[$i]=$rs->fields['student_sn'];
		$stid[$i]=$rs->fields['stud_id'];
		$new_stsn_array=array_slice($stsn,0, -1);
		//$main2.=$stsn[$i]."<br>";
		if(in_array($stsn[$i],$new_stsn_array)){
			$river[$stsn[$i]][]=$stid[$i];
			foreach($new_stsn_array as $k => $v){
				if(($stsn[$k]==$stsn[$i]) && (!in_array($stid[$k],$river[$stsn[$i]]))) $river[$stsn[$i]][]=$stid[$k];
			}
			$err=1;
		}
		$rs->MoveNext();
		$i++;
	}
	
	if($err==1) {
		foreach($river as $k1 => $v1){
			//echo $k1."=>".$v1."<br>";
			$main2.="<ul>以下為流水號：『 $k1 』的學生，建議先進行系統自動修正，若系統無法自動修正成功的話，建議自行刪除至僅保留一筆<br>";
			foreach($v1 as $k2 => $v2){
				$main2.="<li>學（代）號：".$v2."<a href='{$_SERVER['PHP_SELF']}?act=auto_upd&stud_id=$v2&student_sn=$k1'><font class='button'>系統自動修正</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del_seme&stud_id=$v2&student_sn=$k1'><font class='button'>刪除</font></a></li>";
				//echo $k2."=>".$v2."<br>";
			}
			$main2.="</ul>";
		}
	}
	else $main2.="<br>無重覆在籍學生！";
	
	//列出姓名為空的人員
	$main3.= "<hr><font class='title1'>以下為姓名未填的在籍學生</font><br>";
	$main3.= "<ul>";
	$err=0;
	while (list($k,$v)=each($stud_name)){
		if (empty($v)){
			$class_name_str[$k]=substr($curr_class_num[$k],0,-2);
			$class_name[$k]=$class_name_array[$class_name_str[$k]];
			if($class_name[$k]=="") $class_name[$k]="<font color='#D61414'>無班級</font>";
			$main3.="<li>姓名空白 ： ".$class_name[$k]." / 學號：".$stud_id[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";
			$err=1;
		}
	}
	$main3.= "</ul>";
	if($err=="0") $main3.="無姓名空白的在籍學生！";

	
	//列出性別未填或不正確的人員
	$main4.= "<hr><font class='title1'>以下為性別未填或不正確的在籍學生</font><br>";
	$err=0;
	$main4.= "<ul>";
	while (list($k,$v)=each($stud_sex)){		
		if(!IsSex($v)){
			$class_name_str[$k]=substr($curr_class_num[$k],0,-2);
			$class_name[$k]=$class_name_array[$class_name_str[$k]];
			if($class_name[$k]=="") $class_name[$k]="<font color='#D61414'>無班級</font>";
			if (empty( $v)){
				$main4.="<li>性別未填 ： ".$class_name[$k]." / 學號：".$stud_id[$k]." / 姓名：".$stud_name[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";
			}
			else{
				$main4.="<li>性別錯誤 ： ".$class_name[$k]." / 學號：".$stud_id[$k]." / 姓名：".$stud_name[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";
			}
			$err=1;
		}
	}	
	$main4.= "</ul>";
	if($err=="0") $main4.="無性別未填或不正確的在籍學生！";
	
	//列出出生日期未填或不正確的人員
	$main5.= "<hr><font class='title1'>以下為出生日期未填或不正確的在籍學生</font><br>";
	$err=0;
	$main5.= "<ul>";
	while (list($k,$v)=each($stud_birthday)){		
		if(!IsBirthday($v)){
			$class_name_str[$k]=substr($curr_class_num[$k],0,-2);
			$class_name[$k]=$class_name_array[$class_name_str[$k]];
			if($class_name[$k]=="") $class_name[$k]="<font color='#D61414'>無班級</font>";
			if (empty($v)){
				$main5.="<li>出生日期未填 ： ".$class_name[$k]." / 學號：".$stud_id[$k]." / 姓名：".$stud_name[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";			
			}
			else{
				$main5.="<li>出生日期錯誤 ： ".$class_name[$k]." / 學號：".$stud_id[$k]." / 姓名：".$stud_name[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";	
			}
			$err=1;
		}
	}	
	$main5.= "</ul>";
	if($err=="0") $main5.="無出生日期未填或不正確的在籍學生！";
	
	//列出班級，座號錯誤的人員
	$main6.= "<hr><font class='title1'>以下為班級，座號錯誤的在籍學生</font><br>";
	$err=0;
	$main6.= "<ul>";
	while (list($k,$v)=each($curr_class_num)){
		$msg[$k]=IsClassNum($v,$stud_id[$k]);
		if($msg[$k]){			
			$class_name_str[$k]=substr($curr_class_num[$k],0,-2);
			$class_name[$k]=$class_name_array[$class_name_str[$k]];
			if($class_name[$k]=="") $class_name[$k]="<font color='#D61414'>無班級</font>";		
			if (empty($v)){
				$main6.="<li>".$curr_class_num[$k]."班級，座號未填 ： ".$class_name[$k]." / 學號：".$stud_id[$k]." / 姓名：".$stud_name[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";			
			}
			else{
				$main6.="<li>$msg[$k] ： ".$class_name[$k]." / ".$stud_id[$k]." / 學號：姓名：".$stud_name[$k]." <a href='{$_SERVER['PHP_SELF']}?act=edit&stud_id={$stud_id[$k]}&c_curr_class={$c_curr_class[$k]}'><font class='button'>修改</font></a> <a href='{$_SERVER['PHP_SELF']}?act=del&student_sn=$k&stud_id={$stud_id[$k]}'><font class='button'>刪除</font></a></li>";	
			}
			$err=1;
		}
	}	
	$main6.= "</ul>";
	if($err=="0") $main6.="無班級，座號錯誤的在籍學生！";	
	
	//列出班級錯誤的人員
		
	$main= $main_del.$main0.$main1.$main2.$main3.$main4.$main5.$main6;
	//設定主網頁顯示區的背景顏色
	$back_ground="
		<table cellspacing=1 cellpadding=6 border=0 bgcolor='#B0C0F8' width='100%'>
			<tr bgcolor='#FFFFFF'>
				<td>
					$main
				</td>
			</tr>
		</table>";
	echo $back_ground;


foot();


function IsSex($sex){
	if(!$sex) return 0;
	if($sex==1 || $sex==2) return 1;
	else return 0;

}

function IsBirthday($birthday){
	if((!$birthday) || ($birthday=="0000-00-00") ) return 0;
	$BA=explode("-",$birthday);
	if(checkdate ($BA[1], $BA[2], $BA[0])) return 1;
	else return 0;
}


function IsClassNum($curr_class_num,$stud_id){
	global $CONN,$curr_year,$curr_seme;
	if(!$curr_class_num || !$stud_id){
		$msg="缺班級資料";
		return $msg;
	}		
	//檢查$curr_class_num的正確性
	//取出年級，班級，座號
	$c_year=substr($curr_class_num,0,-4);
	$c_sort=substr($curr_class_num,-4,-2);
	$num=substr($curr_class_num,-2);	
	if(!$num || $num==0){
		$msg="該生無座號或座號為0";
		return $msg;
	}		
	$sql="select count(*) from school_class where enable=1 and c_year='$c_year' and c_sort='$c_sort' and year='$curr_year' and semester='$curr_seme' ";	
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$c=$rs->fields[0];
	if($c==0) {
		$msg="該生所在的班級不存在";
		return $msg;
	}
	
	//除了在stud_base正確之外還要比對stud_seme是否一致
	$seme_year_seme=sprintf("%03d%d",$curr_year,$curr_seme);
	$seme_class=substr($curr_class_num,0,-2);
	$sql2="select stud_id from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' and seme_num='$num' and stud_id='$stud_id'";
	$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
	if(!$rs2){
		$msg="『學生基本資料』與『學期資料』不一致";
		return $msg;
	}
}

function upd_fail($upd_file,$act=""){
	global $CONN;	
	if($act=="upd_tb"){		
		//刪除紀錄檔
		if(is_file($upd_file)) unlink ($upd_file);
		//進行升級
		include "../../include/sfs_upgrade_list.php";		
		header("Location:{$_SERVER['PHP_SELF']}");
	}
	echo "更新學生學期記錄表 stud_seme , 加入 student_sn 欄位失敗<br>
	<a href='{$_SERVER['PHP_SELF']}?act=upd_tb'><font class='button'>執行升級</font></a>";
	exit;	
}
?>
