<?php

// $Id: upd_semester.php 7710 2013-10-23 12:40:27Z smallduh $

    /*引入學務系統設定檔*/
      include "../../include/config.php";
    //使用者認證
      sfs_check();

// 不需要 register_globals
/*
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
*/
$class_id=$_POST['class_id'];
$ss_id=$_POST['ss_id'];
$test_sort=$_POST['test_sort'];
$test_form=$_POST['test_form'];
$teacher_course=$_POST['teacher_course'];
$Submit2=$_POST['Submit2'];
$test_kind=$_POST['test_kind'];
$submit2_check=$_POST['submit2_check'];
$score1=$_POST['score1'];
$score2=$_POST['score2'];
$stud_sn_array=$_POST['stud_sn_array'];
$file_in=$_POST['file_in'];

    //引入函數
    include "./my_fun.php";
    //$stud_sn=explode(",",$stud_sn_array);
    $yorn=findyorn();
    $stud_sn=class_id_to_student_sn($class_id);
    if(empty($sel_year))$sel_year = curr_year(); //目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
    $score_semester="score_semester_".$sel_year."_".$sel_seme;
    $update_time=date("Y-m-d H:i:s");
    //成績檔案匯入
    if($file_in){
        $main="
            <table bgcolor=#FDE442 border=0 cellpadding=2 cellspacing=1 align='center'>
            <tr><td colspan=2  height=50 align='center'><font size='+2'>成績檔案匯入</font></td></tr>
            <form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method=post>
            <tr><td  nowrap valign='top' bgcolor='#E1ECFF' width=40%>
            <p>請按『瀏覽』選擇匯入檔案來源：</p>
            <input type=file name='scoredata'>
            <input type=hidden name='file_in' value='yes'>
            <input type=hidden name='score_semester' value='$score_semester'>
            <input type=hidden name='class_id' value='$class_id'>
            <input type=hidden name='test_sort' value='$test_sort'>
            <input type=hidden name='test_kind' value='$test_kind'>
            <input type=hidden name='ss_id' value='$ss_id'>
            <input type=hidden name='teacher_course' value='$teacher_course'>
            <input type=hidden name='err_msg' value='檔案格式錯誤<a href=./manage.php?teacher_course=$teacher_course&curr_sort=$test_sort> <<上一頁>></a>'>
            <p><input type=submit name='file_date' value='成績檔案匯入'></p>
            <b>".$_POST['err_msg']."</b>
            </td>
            <td valign='top' bgcolor='#FFFFFF'>
            說明：<br>
            <ol>
                <li>成績csv檔，請勿寫上成績，程式會由第二行開始讀取</li>
                <li>成績csv檔的第一行key上標題，方便將來自己的查詢</li>
                <li>檔案內容格式如下：(由第二行開始的第一欄為座號，第二欄為分數)</li>
            </ol>
    <pre>
    #91學年第1學期一年一班數學第2階段的定期評量
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
            //$update_time=date("Y m d H:i:s");
            $path_str = "temp/score/";
            set_upload_path($path_str);
            $temp_path = $UPLOAD_PATH.$path_str;
            $temp_file= $temp_path."score.csv";
            if ($_FILES['scoredata']['size'] >0 && $_FILES['scoredata']['name'] != ""){

                copy($_FILES['scoredata']['tmp_name'] , $temp_file);
                $fd = fopen ($temp_file,"r");
                $i =0;
                while ($tt = sfs_fgetcsv ($fd, 2000, ",")) {
                    if ($i++ == 0){//第一筆為抬頭
                        $msg.="第一筆為抬頭，不要keyin成績<br>";
                        continue ;
                    }
                    $stud_site_num= trim($tt[0]);
                    $stud_score= trim($tt[1]);
                    //echo $stud_site_num." ".$stud_score."<br>";
                    if((strlen($stud_site_num)>3) || (strlen($stud_score)>3) ){
                        echo $main;
                        exit;
                    }
                    //echo $score_nor." ".$class_subj." ".$stud_site_num." ".$stage." ".$freq." ".$test_name." ".$teach_id." ".$weighted." ".$stud_score."<br>";
                    //找出student_sn
                    $class_id_array=explode("_",$class_id);
                    //$curr_class_num=intval($class_id_array[2]).$class_id_array[3].sprintf("%02d",$stud_site_num);
                    //echo $curr_class_num."<br>";
                    //$rs_stsn=$CONN->Execute("select student_sn from stud_base where curr_class_num='$curr_class_num' and stud_study_cond=0 ");
                    //$student_sn=$rs_stsn->fields['student_sn'];
                    //echo $student_sn."<br>";
                    $seme_year_seme=$class_id_array[0].$class_id_array[1];
                    $seme_class=intval($class_id_array[2]).$class_id_array[3];
                    $seme_num=$stud_site_num;
                    $rs_stid=$CONN->Execute("select stud_id,student_sn from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' and seme_num='$seme_num' order by seme_num");
                    $stud_id=$rs_stid->fields['stud_id'];
                    $student_sn=$rs_stid->fields['student_sn'];
                    if($student_sn){
                        if($stud_score=="") $stud_score="-100";
                        $rs_ck=$CONN->Execute("select score_id from $score_semester where student_sn='$student_sn' and class_id='$class_id' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort'");
                        $ck=$rs_ck->fields['score_id'];
                        if($ck) $bobo=$CONN->Execute("update $score_semester SET score='$stud_score',update_time='$update_time',teacher_sn='$_SESSION[session_tea_sn]' where student_sn='$student_sn' and class_id='$class_id' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort'");
                        else $bobo=$CONN->Execute("INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$student_sn','$ss_id','$stud_score','$test_kind','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')");
                        //echo "update $score_semester SET score='$stud_score',update_time='$update_time' where student_sn='$student_sn' and class_id='$class_id' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort'";
                        if($bobo) $msg.="--num ".$stud_site_num." --成功<br>";
                        else $msg.="--num ".$stud_site_num." --失敗<br>";
                    }
                    else{
                        $msg.="--num ".$stud_site_num." --不存在<br>";
                    }
                }
                header("Location:manage.php?teacher_course=$teacher_course&curr_sort=$test_sort&msg=$msg");
            }
            else{
                echo $main;
                exit;
            }

        }
        echo $main;
    }
    //成績檔案匯出
    elseif($_POST['file_out']){
        //echo $test_kind;
        if($test_kind=="全學期") $test_kind_num="all";
        if($test_kind=="定期評量") $test_kind_num="1";
        if($test_kind=="平時成績") $test_kind_num="2";
        $filename="semescore_".$class_id."_".$ss_id."_".$test_sort."_".$test_kind_num.".csv";
        header("Content-disposition: filename=$filename");
        header("Content-type: application/octetstream ; Charset=Big5");
        //header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
        header("Expires: 0");
        $class_id_name=explode("_",$class_id);
        $class_year_name=class_id_to_full_class_name($class_id);
        $subject_name=ss_id_to_subject_name($ss_id);
        $dl_time=date("Y m d H:i:s");
        if($test_kind=="全學期") $file_info="#".intval($class_id_name[0])."學年第".intval($class_id_name[1])."學期".$class_year_name.$subject_name.$test_kind."成績(".$dl_time.")\n";
        else $file_info="#".intval($class_id_name[0])."學年第".intval($class_id_name[1])."學期".$class_year_name.$subject_name."第".$test_sort."階段的".$test_kind."(".$dl_time.")\n";
        echo $file_info;
        $rs=&$CONN->Execute("select  student_sn,score  from  $score_semester where class_id='$class_id' and test_sort='$test_sort' and test_kind='$test_kind' and ss_id='$ss_id' ");
        $i=0;
        while(!$rs->EOF){
            $student_sn[$i]=$rs->fields['student_sn'];
            $score[$i]=$rs->fields['score'];
            if($score[$i]=="-100") $score[$i]=" ";
            $student_info[$i]=student_sn_to_classinfo($student_sn[$i]);
            echo $student_info[$i][2].",".$score[$i]."\n";
            $i++;
            $rs->MoveNext();
        }
    }
    else{
        if($yorn=="n"){
			if($test_sort!=255 && $test_sort!=254){
				for($i=0;$i<count($stud_sn);$i++){
					//echo $stud_sn[$i]." ".$score1[2]." ";
					$test_kind="定期評量";
					$sql1="select score_id from $score_semester where class_id='$class_id' and student_sn='$stud_sn[$i]' and ss_id='$ss_id' and test_kind='定期評量' and test_sort='$test_sort' ";
					$rs1=&$CONN->Execute($sql1) ;
					$score_id=$rs1->fields['score_id'];
					$score1[$i]=$score1[$stud_sn[$i]];
					if(eregi("[[:alpha:]]|[!@#$%^&*()_+=-?></,|\~`:;'{}]",$score1[$i])) $score1[$i]='-100';
					if(($score1[$i]=="")||($score1[$i]<0)||($score1[$i]>100)) $score1[$i]='-100';
					if($score_id){
						//echo $score1[$i]." ";
						$sql2="UPDATE  $score_semester SET test_name='定期評量',test_sort='$test_sort',score='$score1[$i]',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id'";
						$CONN->Execute($sql2);

					}
					else{
						//echo $score1[$i]." ";
						$sql3="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$stud_sn[$i]','$ss_id','$score1[$i]','$test_name','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')";
						$CONN->Execute($sql3) ;

					}
					//echo $score1[2]." ";
					//$show.=$class_id.">>>".$stud_sn[$i].">>>".$ss_id.">>>".$score1[$stud_sn[$i]].">>>".$score2[$stud_sn[$i]].">>>".$test_sort."<br>";
				}
			}
			else{				
				$class=class_id_2_old($class_id);
				$times_qry="select performance_test_times from score_setup where class_year=$class[3] and year=$sel_year and semester='$sel_seme' and enable='1'";    	
				$times_rs=&$CONN->Execute($times_qry);
				$performance_test_times=$times_rs->fields["performance_test_times"];  		
					for($i=0;$i<count($stud_sn);$i++){
						$test_kind=($test_sort==255)?"全學期":"平時成績";
						//$test_kind="平時成績";              
						if($test_sort!=255){
							for($g=1;$g<=$performance_test_times;$g++){
								$sql1="select score_id from $score_semester where class_id='$class_id' and student_sn='$stud_sn[$i]' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$g' ";								
								$rs1=&$CONN->Execute($sql1) ;
								$score_id=$rs1->fields['score_id'];
								$score2[$i]=$score2[$stud_sn[$i]];
								if(($score2[$i]=="")||($score2[$i]<0)||($score2[$i]>100)) $score2[$i]='-100';					
								if($score_id){
									$sql2="UPDATE  $score_semester SET test_name='$test_kind',test_sort='$g',score='$score2[$i]',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id'";
									$CONN->Execute($sql2);

								}
								else{
									$sql3="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$stud_sn[$i]','$ss_id','$score2[$i]','$test_kind','$test_kind','$g','$update_time','$_SESSION[session_tea_sn]')";
									$CONN->Execute($sql3) ;
								}
							}
						}
						else{
							$sql1="select score_id from $score_semester where class_id='$class_id' and student_sn='$stud_sn[$i]' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort' ";
							$rs1=&$CONN->Execute($sql1) ;
							$score_id=$rs1->fields['score_id'];
							$score2[$i]=$score2[$stud_sn[$i]];
							if(($score2[$i]=="")||($score2[$i]<0)||($score2[$i]>100)) $score2[$i]='-100';					
							if($score_id){
								$sql2="UPDATE  $score_semester SET test_name='$test_kind',test_sort='$test_sort',score='$score2[$i]',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id'";
								$CONN->Execute($sql2);
							}
							else{
								$sql3="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$stud_sn[$i]','$ss_id','$score2[$i]','$test_kind','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')";
								$CONN->Execute($sql3) ;
							}								
						}
					}
				}		
		}
		else{		
			//echo "$test_sort";
			if($test_sort!=255 && $test_sort!=254){
				for($i=0;$i<count($stud_sn);$i++){
					//echo $stud_sn[$i]." ".$score1[2]." ";
					$test_kind="定期評量";
					$sql1="select score_id from $score_semester where class_id='$class_id' and student_sn='$stud_sn[$i]' and ss_id='$ss_id' and test_kind='定期評量' and test_sort='$test_sort' ";
					$rs1=&$CONN->Execute($sql1) ;
					$score_id=$rs1->fields['score_id'];
					$score1[$i]=$score1[$stud_sn[$i]];
					if(eregi("[[:alpha:]]|[!@#$%^&*()_+=-?></,|\~`:;'{}]",$score1[$i])) $score1[$i]='-100';
					if(($score1[$i]=="")||($score1[$i]<0)||($score1[$i]>100)) $score1[$i]='-100';
					if($score_id){
						//echo $score1[$i]." ";
						$sql2="UPDATE  $score_semester SET test_name='定期評量',test_sort='$test_sort',score='$score1[$i]',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id'";
						$CONN->Execute($sql2);

					}
					else{
						//echo $score1[$i]." ";
						$sql3="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$stud_sn[$i]','$ss_id','$score1[$i]','$test_name','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')";
						$CONN->Execute($sql3) ;

					}
					//echo $score1[2]." ";
					//$show.=$class_id.">>>".$stud_sn[$i].">>>".$ss_id.">>>".$score1[$stud_sn[$i]].">>>".$score2[$stud_sn[$i]].">>>".$test_sort."<br>";
				}
			}
			//$class=class_id_2_old($class_id);
			//$times_qry="select performance_test_times from score_setup where class_year=$class[3] and year=$sel_year and semester='$sel_seme' and enable='1'";    	
			//$times_rs=&$CONN->Execute($times_qry);
			//$performance_test_times=$times_rs->fields["performance_test_times"];  		
				//echo "$test_sort";
				for($i=0;$i<count($stud_sn);$i++){
					$test_kind=($test_sort==255)?"全學期":"平時成績";
					//$test_kind="平時成績";              
					if($test_sort!=255){
						//for($g=1;$g<=$performance_test_times;$g++){
							$sql1="select score_id from $score_semester where class_id='$class_id' and student_sn='$stud_sn[$i]' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort' ";							
							$rs1=&$CONN->Execute($sql1) ;
							$score_id=$rs1->fields['score_id'];
							$score2[$i]=$score2[$stud_sn[$i]];
							if(($score2[$i]=="")||($score2[$i]<0)||($score2[$i]>100)) $score2[$i]='-100';					
							if($score_id){
								$sql2="UPDATE  $score_semester SET test_name='$test_kind',test_sort='$test_sort',score='$score2[$i]',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id'";
								$CONN->Execute($sql2);

							}
							else{
								$sql3="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$stud_sn[$i]','$ss_id','$score2[$i]','$test_kind','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')";
								$CONN->Execute($sql3) ;
							}
						//}
					}
					else{
						$sql1="select score_id from $score_semester where class_id='$class_id' and student_sn='$stud_sn[$i]' and ss_id='$ss_id' and test_kind='$test_kind' and test_sort='$test_sort' ";
						$rs1=&$CONN->Execute($sql1) ;
						$score_id=$rs1->fields['score_id'];
						$score2[$i]=$score2[$stud_sn[$i]];
						if(($score2[$i]=="")||($score2[$i]<0)||($score2[$i]>100)) $score2[$i]='-100';					
						if($score_id){
							$sql2="UPDATE  $score_semester SET test_name='$test_kind',test_sort='$test_sort',score='$score2[$i]',teacher_sn='$_SESSION[session_tea_sn]' WHERE  score_id='$score_id'";
							$CONN->Execute($sql2);
						}
						else{
							$sql3="INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,teacher_sn) values('$class_id','$stud_sn[$i]','$ss_id','$score2[$i]','$test_kind','$test_kind','$test_sort','$update_time','$_SESSION[session_tea_sn]')";
							$CONN->Execute($sql3) ;
						}								
					}
				}
		}		
        //}
        //echo $show;
        header("Location:manage.php?teacher_course=$teacher_course&curr_sort=$test_sort&curr_form=$test_form&submit2_check=$submit2_check");
    }
?>

