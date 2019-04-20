<?php

// $Id: import_score.php 7324 2013-07-01 09:20:36Z chiming $

// 引入您自己的 config.php 檔
require_once "config.php";

// 認證
sfs_check();

// 您的程式碼由此開始
//全域變數轉換區*****************************************************
$act=($_GET['act'])?$_GET['act']:$_POST['act'];


//********************************************************************

if ($act=="批次建立資料"){	
	$main=import($_FILES['scoredata']['tmp_name'],$_FILES['scoredata']['name'],$_FILES['scoredata']['size']);    
}else{
	$main=&main_form();	 
}

// 叫用 SFS3 的版頭
head("學期成績補匯");	
echo $main;
foot();


//主要表格
function &main_form(){
	global $CONN,$MENU_P;
	$toolbar=&make_menu($MENU_P); 	
	$main="
	$toolbar	
	<table border='0' cellspacing='0' cellpadding='0' >
	<tr><td valign=top>
		<table cellspacing='1' cellpadding='10' border='0' bgcolor='#D08CD9'>
		<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
		<tr><td  nowrap valign='top' bgcolor='#FACDEF'>
		<p>請按『瀏覽』選擇匯入檔案來源：</p>
		<input type=file name='scoredata'>
		<p><input type=submit name='act' value='批次建立資料'></p>
		</td>
		<td valign='top' bgcolor='#FFFFFF'>
		<p><b><font size='4'>學期總成績匯入說明</font></b></p>
		<ol>
		<li>請先確定已經做匯出空白成績csv檔，並且完成成績欄輸入。</li>
		<li>匯出的成績csv檔，只能輸入成績欄，請勿更改檔名或變動其他欄位。</li>
		<li>成績csv檔的第一列是標題，第二列是欄名，程式會由第三列開始讀取 。</li>
		<li>檔案內容格式如下：(由第三列開始的第一欄為學生流水號，第二欄為座號, 第三欄為姓名,第四欄為分數) </li>
		<li>如果班級或科目不對，會出現錯誤訊息。</li>
		<table BORDER=\"0\" BGCOLOR=\"#E0E0E0\" WIDTH=\"100%\">
		<tr><td>
<pre>
#92學年第1學期四年一班電腦全學期成績(2004 01 06 11:31:38),,,
學生流水號,座號,姓名,成績    
2001,1,陳XX,56
2002,2,李XX,76
2003,3,張XX,56
2004,4,吳XX,22
1005,5,林XX,99
1006,6,王XX,65
1007,7,鐘XX,87
</pre>
		</td>
		</tr>
		</table>
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
function import($scoredata,$scoredata_name,$scoredata_size){
	if (!ereg ("([0-9]{4})_([0-9]{3})[._]", $scoredata_name, $regs)) {
		trigger_error("$scoredata_name  檔名不合法,請檢查後重新上傳!!",E_USER_ERROR);
		exit;	
	}
	global $CONN,$temp_path;
	$oth_arr_score = array(5=>"表現優異",4=>"表現良好",3=>"表現尚可",2=>"需再加油",1=>"有待改進");
    if ($scoredata_size>0 && $scoredata_name!=""){
		$seme_year_seme_A=explode("_",$scoredata_name);
		$cc = count ($seme_year_seme_A);
		// $cc 為3 表示文字敘述檔案
		$seme_year_seme=$seme_year_seme_A[0];
		
		$temp_file= $temp_path.$scoredata_name;
		copy($_FILES['scoredata']['tmp_name'] , $temp_file);		
		$fd = fopen ($temp_file,"r");
		//重置檔案指標
		rewind($fd);
		$j=0;
		while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
			if ($j++ == 0 ){ //第一筆為抬頭
				$MM=count($tt)+2;
				for($N=1;$N<$MM;$N++) {					
					$ss_id_A[$N]=trim($tt[$N]);
				}	
       			$ok_temp .="<font color='red'>".trim($tt[0])."</font><br>";				
				continue ;
			}
			elseif ($j++ == 2 ){ //第二筆為抬頭       			
				$M=count($tt);							
				$ok_temp .="<font color='blue'>";
				for($N=0;$N<$M;$N++) {
					$ok_temp .=trim($tt[$N])."---";					
				}	
				$ok_temp .="</font></br>";
				continue ;
			}
			for($N=0;$N<$M;$N++) {							
				$ok_temp.=trim($tt[$N])."---";
				$stud_id=trim($tt[0]);//學號
				//$student_sn=stud_id_to_student_sn($stud_id);//轉成流水號
				$student_sn=stud_id_to_student_sn($stud_id,$seme_year_seme);//轉成流水號
				//生活評量成績評語
				if ($seme_year_seme_A[2]=='nor.csv') {
					if($N==2) {//大於二的原因是前面三欄非成績
						$ss_score=trim($tt[3]);
						$ss_score_memo= AddSlashes(trim($tt[4]));
						if($ss_score=="") continue;//若無成績則繼續下一筆
						$query = "replace into stud_seme_score_nor(seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo)values('$seme_year_seme_A[0]','$student_sn',0,'$ss_score','$ss_score_memo')";
						$CONN->Execute($query) or die($query);
					}
				}
				//努力程度
				elseif ($seme_year_seme_A[2]=='study.csv') {
					if($N>2) {//大於二的原因是前面三欄非成績
						$ss_score=trim($tt[$N]);
						if($ss_score=="") continue;//若無成績則繼續下一筆
						$NN=$N-2;
						$ss_score = $oth_arr_score[$ss_score]; 
						$query = "replace into stud_seme_score_oth(seme_year_seme,stud_id,ss_kind,ss_id,ss_val)values('$seme_year_seme_A[0]','$stud_id','努力程度','$ss_id_A[$NN]','$ss_score')";
						$CONN->Execute($query) or die($query);
					}
				}

				else {
					//寫入學期總成績資料表stud_seme_score
					if($N>2) {//大於二的原因是前面三欄非成績
						$ss_score=trim($tt[$N]);
						if($ss_score=="") continue;//若無成績則繼續下一筆
						$NN=$N-2;
						$sql_sss="select sss_id from stud_seme_score where seme_year_seme='$seme_year_seme' and student_sn='$student_sn' and ss_id='$ss_id_A[$NN]' ";
						$rs_sss=&$CONN->Execute($sql_sss) ;
						$sss_id=$rs_sss->fields['sss_id'];					
						if($sss_id){
							if ($seme_year_seme_A[2]=='memo.csv'){
								$ss_score = addslashes(trim($ss_score));
								$update_sql="update  stud_seme_score set ss_score_memo='$ss_score',teacher_sn='$_SESSION[session_tea_sn]' where sss_id=$sss_id";
							}
							else
								$update_sql="update  stud_seme_score set ss_score='$ss_score',teacher_sn='$_SESSION[session_tea_sn]' where sss_id=$sss_id";
							$CONN->Execute($update_sql) or die($update_sql);
						}	
						else{
							if ($seme_year_seme_A[2]=='memo.csv'){
								$ss_score = addslashes($ss_score);
								$insert_sql="insert into stud_seme_score(seme_year_seme,student_sn,ss_id,ss_score_memo) values('$seme_year_seme','$student_sn','$ss_id_A[$NN]','$ss_score')";
							}
							else
								$insert_sql="insert into stud_seme_score(seme_year_seme,student_sn,ss_id,ss_score) values('$seme_year_seme','$student_sn','$ss_id_A[$NN]','$ss_score')";
							$CONN->Execute($insert_sql) or die($update_sql);
						}	
//					echo $insert_sql."<br>";
//					echo $update_sql."<br>";
					}
				}


					//系統自動偵測本學期儲存成績的資料表是否存在若不存在則自動新增，命名規則：score_semester_91_1
					$y1=intval(substr($seme_year_seme,0,3));
					$y2=substr($seme_year_seme,-1);		
		
					$score_semester="score_semester_".$y1."_".$y2;
					$creat_table_sql_s="CREATE TABLE  if not exists  $score_semester (
					  score_id bigint(10) unsigned NOT NULL auto_increment,
					  class_id varchar(11) NOT NULL default '',
					  student_sn int(10) unsigned NOT NULL default '0',
					  ss_id smallint(5) unsigned NOT NULL default '0',
					  score float unsigned NOT NULL default '0',
					  test_name varchar(20) NOT NULL default '',
					  test_kind varchar(10) NOT NULL default '定期評量',
					  test_sort tinyint(3) unsigned NOT NULL default '0',
					  update_time datetime NOT NULL default '0000-00-00 00:00:00',
					  sendmit enum('0','1') NOT NULL default '1',
				 	  teacher_sn smallint(6) NOT NULL default '0',
					  PRIMARY KEY  (student_sn,ss_id,test_kind,test_sort),
					  UNIQUE KEY score_id (score_id)
					  )";
					$CONN->Execute($creat_table_sql_s);
				
				if ($cc<3){

					//寫入學期階段成績資料表score_semester
					//$score_semester="score_semester_".$y1."_".$y2;
					$sql_sss="select score_id from $score_semester where student_sn='$student_sn' and ss_id='$ss_id_A[$NN]' and test_name='全學期' and test_kind='全學期'";					
					$rs_sss=&$CONN->Execute($sql_sss) ;
					$score_id=$rs_sss->fields['score_id'];					
					if($score_id){
						$update_sql="update  $score_semester set score='$ss_score' where score_id='$score_id'";
						$CONN->Execute($update_sql);
					}	
					else{
						if ($student_sn!=''){
							$class_id=student_sn_to_class_id($student_sn,$y1,$y2);
							$update_time=date ("Y-m-d H:i:s");
							$insert_sql="insert into $score_semester(class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,sendmit,teacher_sn) values('$class_id','$student_sn','$ss_id_A[$NN]','$ss_score','全學期','全學期','255','$update_time','0','$_SESSION[session_tea_sn]')"; 
							$CONN->Execute($insert_sql);
						}
					}	
				 }

			 }				
												
			$ok_temp.="<br>";
		}
	}
	unlink($temp_file);
	return $ok_temp;
}

//由stud_id找出student_sn
function  stud_id_to_student_sn($stud_id,$seme_year_seme){
    global $CONN;
    $SQL="select  a.student_sn  from  stud_base a ,stud_seme b where a.stud_id=b.stud_id and   a.student_sn=b.student_sn and b.seme_year_seme ='$seme_year_seme' and   a.stud_id='$stud_id' ";
    $rs=&$CONN->Execute($SQL);
    $student_sn=$rs->fields['student_sn'];
    return $student_sn;
}



