<?php

// $Id: index.php 7768 2013-11-15 06:23:39Z smallduh $

/*引入學務系統設定檔*/
require "../../include/config.php";
require "../../include/sfs_case_score.php";
//引入函數
include "./my_fun.php";
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
$year_seme=($_GET['year_seme'])?$_GET['year_seme']:$_POST['year_seme'];
$year_name=($_GET['year_name'])?$_GET['year_name']:$_POST['year_name'];
$me=($_GET['me'])?$_GET['me']:$_POST['me'];
$scope_subject=($_GET['scope_subject'])?$_GET['scope_subject']:$_POST['scope_subject'];
$stage_score=($_GET['stage_score'])?$_GET['stage_score']:$_POST['stage_score'];
//$year_name=$_GET['year_name'];
//$me=$_GET['me'];
//$scope_subject=$_GET['scope_subject'];
//$stage_score=$_GET['stage_score'];
//$test_kind=$_GET['test_kind'];
$edit=$_GET['edit'];
$score1=$_POST['score1'];
$student_sn=$_POST['student_sn'];
$test_ratio0=$_POST['test_ratio0'];
$test_ratio1=$_POST['test_ratio1'];
$score2=$_POST['score2'];
$Submit5=($_GET['Submit5'])?$_GET['Submit5']:$_POST['Submit5'];
$Submit6=($_GET['Submit6'])?$_GET['Submit6']:$_POST['Submit6'];
//$Submit6=$_POST['Submit6'];
$score=$_POST['score'];
//$Submit5=$_POST['Submit5'];
$del=$_GET['del'];
//$test_kind=$_POST['test_kind'];
$is_man = checkid($_SERVER['SCRIPT_FILENAME'],1);

$year_temp = '';
$chk_year_seme = sprintf("%d_%d",curr_year(),curr_seme());
if ($year_seme == $chk_year_seme){
	$year_temp = "<input type='checkbox' name='all_student' value='1' ";
	if ($_REQUEST['all_student'])
		$year_temp .=" checked  ";
	$year_temp .="onClick='if(this.checked)document.form4.all_student.value=1;document.form4.submit()' > 顯示調出學生";
}

//程式檔頭
head("成績輸入");

//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor=#FFFFFF>";

//網頁內容請置於此處--------------------------------------------------------------
	
	$year_seme_A=explode("_",$year_seme);
	$sel_year=$year_seme_A[0];
	$sel_seme=$year_seme_A[1];
    if(empty($sel_year))$sel_year = curr_year(); //取得目前學年
    if(empty($sel_seme))$sel_seme = curr_seme(); //取得目前學期
	
	//學期選單
	$col_name="year_seme";
    $id=$year_seme;    
	$show_year_seme=select_year_seme($id,$col_name);
    $year_seme_menu="
        <form name='form0' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu0()'>
                $show_year_seme
            </select>
        </form>";
	
	//年級選單
    if($year_seme){
	$year_seme_A=explode("_",$year_seme);
	$sel_year=$year_seme_A[0];
	$sel_seme=$year_seme_A[1];
	$col_name="year_name";
    $id=$year_name;
    $show_class_year=select_school_class($id,$col_name,$sel_year,$sel_seme);
    $class_year_menu="
        <form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu1()'>
                $show_class_year
            </select>
			<input type='hidden' name='year_seme' value='$year_seme'>
        </form>";
	}
	
    //班級選單
    if($year_seme && $year_name){
		$year_seme_A=explode("_",$year_seme);
		$sel_year=$year_seme_A[0];
		$sel_seme=$year_seme_A[1];        
		$col_name="me";
        $id=$me;
        $show_class_year_name=select_school_class_name($year_name,$id,$col_name,$sel_year,$sel_seme);
        $class_year_name_menu="
        <form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu2()'>
                $show_class_year_name
            </select>
            <input type='hidden' name='year_name' value='$year_name'>
			<input type='hidden' name='year_seme' value='$year_seme'>
        </form>";
    }

    //科目選單(列出該年級的所有課程)
    if($year_seme && ($year_name)&&($me)){
		$year_seme_A=explode("_",$year_seme);
		$sel_year=$year_seme_A[0];
		$sel_seme=$year_seme_A[1];        
		$col_name="scope_subject";
        $id=$scope_subject;
        $select_scope_subject=scope_subject($id,$col_name,$sel_year,$sel_seme,$year_name,$me);
        $scope_subject_menu="
        <form name='form3' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu3()'>
                $select_scope_subject
            </select>
			<input type='hidden' name='year_seme' value='$year_seme'>
            <input type='hidden' name='year_name' value='$year_name'>
            <input type='hidden' name='me' value='$me'>
        </form>";
    }

    //階段成績選單
    if($year_seme && ($year_name)&&($me)&&($scope_subject)){
		$year_seme_A=explode("_",$year_seme);
		$sel_year=$year_seme_A[0];
		$sel_seme=$year_seme_A[1];        
		$col_name="stage_score";
        $id=$stage_score;
        $select_stage_score=stage_score($id,$col_name,$sel_year,$sel_seme,$year_name,$me,$scope_subject);
        $stage_score_menu="
        <form name='form4' method='post' action='{$_SERVER['PHP_SELF']}'>
            <select name='$col_name' onChange='jumpMenu4()'>
                $select_stage_score
            </select>
			<input type='hidden' name='year_seme' value='$year_seme'>
            <input type='hidden' name='year_name' value='$year_name'>
            <input type='hidden' name='me' value='$me'>
            <input type='hidden' name='all_student' value=''>
            <input type='hidden' name='scope_subject' value='$scope_subject'>
        </form>";
    }
    $menu="
        <table cellspacing=0 cellpadding=0>
            <tr>
                <td>$year_seme_menu</td><td>$class_year_menu</td><td>$class_year_name_menu</td><td>$scope_subject_menu</td><td>$stage_score_menu</td>
            </tr>
        </table>";
    echo $menu;
    //echo $stage_score;
	//以上為選單bar
    //成績輸入表格------------------------------------------------------------------------
    if(($year_seme)&&($year_name)&&($me)&&($scope_subject)&&($stage_score)){
        //系統自動偵測本學期儲存成績的資料表是否存在若不存在則自動新增，命名規則：score_semester_091_1
		$year_seme_A=explode("_",$year_seme);
		$sel_year=$year_seme_A[0];
		$sel_seme=$year_seme_A[1];       
	   $score_semester="score_semester_".$sel_year."_".$sel_seme;
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
       $CONN->Execute($creat_table_sql_s)  or trigger_error("資料庫沒有建立，請自行以下列語法建立<br>".$creat_table_sql_s,256);
		
		
        $update_time=date("Y-m-d H:i:s");
        $class_id= sprintf ("%03d_%1d_%02d_%02d", $sel_year,$sel_seme,$year_name,$me);
        $A=explode("_",$scope_subject);
        $ss_id=$A[0];
        $print=$A[1];
        
		$url_str_1 = $SFS_PATH_HTML.get_store_path()."/quick_input_m.php?edit=s1&class_id=$class_id&ss_id=$ss_id&curr_sort=$stage_score";
		$url_str_2 = $SFS_PATH_HTML.get_store_path()."/quick_input_m.php?edit=s2&class_id=$class_id&ss_id=$ss_id&curr_sort=$stage_score";
		$url_str_a = $SFS_PATH_HTML.get_store_path()."/quick_input_m.php?class_id=$class_id&ss_id=$ss_id&curr_sort=255";
		if($stage_score=="all"){			
            if($Submit5=="儲存"){
                
				//寫入學期成績資料表
                for($i=0;$i<count($student_sn);$i++){
                    $rs=$CONN->Execute("select score_id from $score_semester where class_id='$class_id' and ss_id='$ss_id' and test_sort='255' and student_sn='$student_sn[$i]'");                    
                    $score_id[$i]=$rs->fields["score_id"];
                    //echo $score_id[$i];
                    if(($score[$student_sn[$i]]=="")||($score[$student_sn[$i]]>"255")) $score[$student_sn[$i]]="-100";
                    $new_score=$score[$student_sn[$i]];
                    if($score_id[$i]){//更新成績
                        $CONN->Execute("UPDATE $score_semester SET test_name='全學期',score='$new_score',update_time='$update_time',sendmit='0',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id[$i]'");
                    }
                    else{//新增成績
                        $CONN->Execute("INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,sendmit,teacher_sn) values('$class_id','$student_sn[$i]','$ss_id','$new_score','全學期','全學期','255','$update_time','0','$_SESSION[session_tea_sn]')");
                        //echo "INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time) values('$class_id','$student_sn[$i]','$ss_id','$new_score','全學期','全學期','255','$update_time')";
                    }
                }
                //重新計算學期總成績
                for($i=0;$i<count($student_sn);$i++){
                    //由於該科只需輸入總成績所以學期總成績等於其所輸入的成績
                    //echo $student_sn[$i]."領域成績：".$score[$student_sn[$i]]."<br>";
                    if(($score[$student_sn[$i]]=="")||($score[$student_sn[$i]]>"255")) $score[$student_sn[$i]]="-100";
                    $real_score=$score[$student_sn[$i]];//該領域或科目的學期總平均

                    $seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
                    $sss_id_qry="select sss_id from stud_seme_score where seme_year_seme='$seme_year_seme' and ss_id='$ss_id' and student_sn='$student_sn[$i]'";
                    $sss_id_rs=$CONN->Execute($sss_id_qry) or trigger_error("學期總成績資料表未建立",E_USER_ERROR);
                    $sss_id=$sss_id_rs->fields['sss_id'];
                    if($sss_id[$i]){//更新成績
                        $CONN->Execute("UPDATE stud_seme_score SET ss_score='$real_score',teacher_sn='$_SESSION[session_tea_sn]' WHERE  sss_id='$sss_id'");
                    }
                    else{//新增成績
                        $CONN->Execute("INSERT INTO stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,teacher_sn) values('$seme_year_seme','$student_sn[$i]','$ss_id','$real_score','$_SESSION[session_tea_sn]')");
                        //echo "INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time) values('$class_id','$student_sn[$i]','$ss_id','$new_score','全學期','全學期','255','$update_time')";
                    }                }

            }

            //找出該班的所有學生
            $student_sn=class_id_to_student_sn($class_id,$_REQUEST['all_student']);
            //找出該班所有學生該科的成績
            for($i=0;$i<count($student_sn);$i++){
                $sql="select score from $score_semester where student_sn='$student_sn[$i]' and ss_id='$ss_id' and test_sort='255'";
                $rs=$CONN->Execute($sql);
                $score[$i]=$rs->fields["score"];
                //echo $student_sn[$i]."-->".$score[$i]."<br>";
                $rs->MoveNext();
            }
	    
            $main="
            <table bgcolor=#000000 border=0 cellpadding=2 cellspacing=1>
            <tr bgcolor=#ffffff>
                <td  colspan=4 align=center>".$full_class_name.$subject_name.$test_sort_name[$curr_sort]."成績考查 &nbsp;&nbsp;$year_temp </td>
            </tr>
            <tr bgcolor=#ffffff align=center>
                <td>座號</td>
                <td>姓名</td>
                <td>學期成績<br>
                                <a onclick=\"openwindow('$url_str_a')\"><img src='./images/wedit.png' border='0'></a>
								<a href=\"{$_SERVER['PHP_SELF']}?edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score&all_student=$_REQUEST[all_student]\"><img src='./images/pen.png' border='0'></a>
                                <a href=\"{$_SERVER['PHP_SELF']}?del=ds2&edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>
                <td>平均</td>
            </tr>";
            $main.="<form name='form5' method='post' action='{$_SERVER['PHP_SELF']}?year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score'>";
            for($m=0;$m<count($student_sn);$m++){
                if($del=="ds2") $score[$m]="";
                $student_info[$m]=student_sn_to_classinfo($student_sn[$m]);
                if($score[$m]=="-100") $score[$m]="";
                if($edit=="s2"){
                    $main.="<tr bgcolor='#ffffff'><td>".$student_info[$m][2]."</td><td>".$student_info[$m][4]."</td><td><input type=\"text\" name=score[$student_sn[$m]] size=\"8\" maxlength=\"4\" value='".$score[$m]."' class='border_no' style='width: 100%'></td><td>".$score[$m]."</td></tr>";
                    $main.="<input type='hidden' name='student_sn[$m]' value='$student_sn[$m]'>";
                }
                else{
                    $main.="<tr bgcolor='#ffffff'><td>".$student_info[$m][2]."</td><td>".$student_info[$m][4]."</td><td>".$score[$m]."</td><td>".$score[$m]."</td></tr>";
                }
            }
            if($edit=="s2") $main.="<tr bgcolor='#ffffff'><td colspan=4 align=center><input type='submit' name='Submit5' value='儲存'></td></tr>";
            $main.="<input type='hidden' name='year_seme' value='$year_seme'></form>";
            echo $main;
            echo"</table>";

        }
        else{
            if($Submit6=="儲存"){               
				//寫入學期成績資料表
                for($i=0;$i<count($student_sn);$i++){
                    //定期評量的部份
                    if($_POST['test_kind']=="定期評量"){
                    $rs1=$CONN->Execute("select score_id from $score_semester where class_id='$class_id' and ss_id='$ss_id' and test_sort='$stage_score' and test_kind='定期評量' and student_sn='$student_sn[$i]'");
                    //echo "select score_id from $score_semester where class_id='$class_id' and ss_id='$ss_id' and test_sort='255' and student_sn='$student_sn[$i]'";
                    $score_id1[$i]=$rs1->fields["score_id"];
                    //echo $score_id[$i]."<br>";
                    if(($score1[$student_sn[$i]]=="")||($score1[$student_sn[$i]]>"255")) $score1[$student_sn[$i]]="-100";
                    $new_score1=$score1[$student_sn[$i]];
                    if($score_id1[$i]){//更新成績
                        $CONN->Execute("UPDATE $score_semester SET test_name='定期評量',score='$new_score1',update_time='$update_time',sendmit='0',teacher_sn='$_SESSION[teacher_sn]'  WHERE  score_id='$score_id1[$i]'");
                    }
                    else{//新增成績
                        $CONN->Execute("INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,sendmit,teacher_sn) values('$class_id','$student_sn[$i]','$ss_id','$new_score1','定期評量','定期評量','$stage_score','$update_time','0','$_SESSION[session_tea_sn]')");
                        //echo "INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time) values('$class_id','$student_sn[$i]','$ss_id','$new_score','全學期','全學期','255','$update_time')";
                    }
                    }

                    //平時成績的部份
                    if($_POST['test_kind']=="平時成績"){
                    $rs2=$CONN->Execute("select score_id from $score_semester where class_id='$class_id' and ss_id='$ss_id' and test_sort='$stage_score' and test_kind='平時成績' and student_sn='$student_sn[$i]'");
                    //echo "select score_id from $score_semester where class_id='$class_id' and ss_id='$ss_id' and test_sort='255' and student_sn='$student_sn[$i]'";
                    $score_id2[$i]=$rs2->fields["score_id"];
                    //echo $score_id2[$i]."<br>";
                    if(($score2[$student_sn[$i]]=="")||($score2[$student_sn[$i]]>"255")) $score2[$student_sn[$i]]="-100";
                    $new_score2=$score2[$student_sn[$i]];
                    if($score_id2[$i]){//更新成績
                        $CONN->Execute("UPDATE $score_semester SET test_name='平時成績',score='$new_score2',update_time='$update_time',sendmit='0',teacher_sn='$_SESSION[session_tea_sn]'  WHERE  score_id='$score_id2[$i]'");
                    }
                    else{//新增成績
                        $CONN->Execute("INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time,sendmit,teacher_sn) values('$class_id','$student_sn[$i]','$ss_id','$new_score2','平時成績','平時成績','$stage_score','$update_time','0','$_SESSION[session_tea_sn]')");
                        //echo "INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time) values('$class_id','$student_sn[$i]','$ss_id','$new_score','全學期','全學期','255','$update_time')";
                    }
                    }

                }
                //重新計算學期總成績
                $seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
                $ss_id_array=more_ss($ss_id);
                reset($ss_id_array);
                while(list($key , $val) = each($ss_id_array)) {
                    if(is_array($val)){
                    	reset($val);
                        while(list($key1 , $val1) = each($val)) {
                        //echo $key ."[". $key1 ."] => ".$val1."<br>";
                            if($key=="ss_id") $TTL_ss_id[]=$val1;
                             if($key=="rate") $TTL_rate[]=$val1;
                         }
                    }
                    else{
                        //echo "$key => $val<br>";
                        if($key=="ss_id") $TTL_ss_id[]=$val;
                        if($key=="rate") $TTL_rate[]=$val;
                    }
                }
                for($i=0;$i<count($student_sn);$i++){
                    for($m=0;$m<count($TTL_ss_id);$m++){
                        $total_score[$i]=$total_score[$i]+(seme_score($student_sn[$i],$TTL_ss_id[$m],$sel_year,$sel_seme)*$TTL_rate[$m]);
                        $weight[$i]=$weight[$i]+$TTL_rate[$m];
						//echo $sel_year."---".$sel_seme."---".$student_sn[$i]."---".$TTL_ss_id[$m]."---".$TTL_rate[$m]."<br>";
                    }
                    //echo "領域成績：".$real_score[$i]=$total_score[$i]/$weight[$i]."<br>";
                    $real_score[$i]=$total_score[$i]/$weight[$i];//該領域或科目的學期總平均
                    //判斷該成績是否已經存在並寫入
                    $sss_id_qry="select sss_id from stud_seme_score where seme_year_seme='$seme_year_seme' and ss_id='$ss_id' and student_sn='$student_sn[$i]'";
                    $sss_id_rs=$CONN->Execute($sss_id_qry) or trigger_error("學期總成績資料表未建立",E_USER_ERROR);
                    $sss_id[$i]=$sss_id_rs->fields['sss_id'];
                    if($sss_id[$i]){//更新成績
                        $CONN->Execute("UPDATE stud_seme_score SET ss_score='$real_score[$i]',teacher_sn='$_SEESION[session_tea_sn]' WHERE  sss_id='$sss_id[$i]'");
                    }
                    else{//新增成績
                        $CONN->Execute("INSERT INTO stud_seme_score (seme_year_seme,student_sn,ss_id,ss_score,teacher_sn) values('$seme_year_seme','$student_sn[$i]','$ss_id','$real_score[$i]','$_SESSION[session_tea_sn]')");
                        //echo "INSERT INTO $score_semester (class_id,student_sn,ss_id,score,test_name,test_kind,test_sort,update_time) values('$class_id','$student_sn[$i]','$ss_id','$new_score','全學期','全學期','255','$update_time')";
                    }
                }
            }
            //取出本學年本學期的學校成績共通設定
            $sql="select * from score_setup where year='$sel_year' and semester='$sel_seme' and class_year='$year_name' and enable=1";
            //echo $sql;
            $rs=$CONN->Execute($sql);
            $score_mode= $rs->fields['score_mode'];
            $test_ratio= $rs->fields['test_ratio'];
			//echo $test_ratio;
            if($score_mode=="all"){
                $test_ratio=explode("-",$test_ratio);
                if($test_ratio[0]=="") $test_ratio[0]=60;
                if($test_ratio[1]=="") $test_ratio[1]=40;
            }
            elseif($score_mode=="severally"){
                $test_ratio=explode(",",$test_ratio);
                $i=$stage_score-1;
                $test_ratio=explode("-",$test_ratio[$i]);
                if($test_ratio[0]=="") $test_ratio[0]=60;
                if($test_ratio[1]=="") $test_ratio[1]=40;
            }
            else{
                $test_ratio[0]=60;
                $test_ratio[1]=40;
            }
            //echo $test_ratio[0]."% ".$test_ratio[1]."%";
            //找出該班的所有學生
            $student_sn=class_id_to_student_sn($class_id,$_REQUEST['all_student']);
            //找出該班所有學生該科的成績
            $a=$b=0;
            for($i=0;$i<count($student_sn);$i++){
                $sql1="select score from $score_semester where student_sn='$student_sn[$i]'  and ss_id='$ss_id' and test_sort='$stage_score' and test_kind='定期評量'";
                //echo $sql1."<br>";
                $rs1=$CONN->Execute($sql1);
                $score1[$i]=$rs1->fields["score"];
                //echo $student_sn[$i]."-->".$score1[$i]."<br>";

                $sql2="select score from $score_semester where student_sn='$student_sn[$i]' and ss_id='$ss_id' and test_sort='$stage_score' and test_kind='平時成績'";
                //echo $sql;
                $rs2=$CONN->Execute($sql2);
                $score2[$i]=$rs2->fields["score"];
                //echo $student_sn[$i]."-->".$score1[$i]."<br>";

            }

            $main="
            <table bgcolor=#000000 border=0 cellpadding=2 cellspacing=1>
            <tr bgcolor=#ffffff>
                <td  colspan=5 align=center>".$full_class_name.$subject_name.$test_sort_name[$stage_score]."成績考查 &nbsp;&nbsp;$year_temp</td>
            </tr>
            <tr bgcolor=#ffffff align=center>
                <td>座號</td>
                <td>姓名</td>
                <td>定期評量*$test_ratio[0]%<br>
                                <a onclick=\"openwindow('$url_str_1')\"><img src='./images/wedit.png' border='0' style='cursor:pointer'></a>
								<a href=\"{$_SERVER['PHP_SELF']}?edit=s1&year_seme=$year_seme&year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score&test_kind=定期評量&all_student=$_REQUEST[all_student]\"><img src='./images/pen.png' border='0'></a>
                                <a href=\"{$_SERVER['PHP_SELF']}?del=ds1&edit=s1&year_seme=$year_seme&year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score&test_kind=定期評量\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>
                <td>平時成績*$test_ratio[1]%<br>
                                <a onclick=\"openwindow('$url_str_2')\"><img src='./images/wedit.png' border='0' style='cursor:pointer'></a>
								<a href=\"{$_SERVER['PHP_SELF']}?edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score&test_kind=平時成績&all_student=$_REQUEST[all_student]\"><img src='./images/pen.png' border='0'></a>
                                <a href=\"{$_SERVER['PHP_SELF']}?del=ds2&edit=s2&year_seme=$year_seme&year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score&test_kind=平時成績\" onClick=\"return confirm('確定刪除這次成績 ?');\"><img src='./images/del.png' border='0'></a></td>
                <td>平均</td>
            </tr>";
            $main.="<form name='form6' method='post' action='{$_SERVER['PHP_SELF']}?year_name=$year_name&me=$me&scope_subject=$scope_subject&stage_score=$stage_score'>";
            //echo "<br>".count($student_sn)."<br>";
            for($m=0;$m<count($student_sn);$m++){
                if($del=="ds2") $score2[$m]="";
                if($del=="ds1") $score1[$m]="";
                $student_info[$m]=student_sn_to_classinfo($student_sn[$m]);
                if($score1[$m]=="-100") $score1[$m]="";
                if($score2[$m]=="-100") $score2[$m]="";
                if(($score1[$m]!="")||($score2[$m]!=""))
                $ave_score[$m]=number_format((($score1[$m]*$test_ratio[0])+($score2[$m]*$test_ratio[1]))/($test_ratio[0]+$test_ratio[1]),2);
				else $ave_score[$m]="";
				
                if($edit=="s1"){
                    $main.="<tr bgcolor='#ffffff'><td>".$student_info[$m][2]."</td><td>".$student_info[$m][4]."</td><td><input type=\"text\" name=score1[$student_sn[$m]] size=\"8\" maxlength=\"4\" value='".$score1[$m]."' class='border_no' style='width: 100%'></td><td>".$score2[$m]."</td><td>".$ave_score[$m]."</td></tr>";
                    $main.="<input type='hidden' name='student_sn[$m]' value='$student_sn[$m]'>";
                    $main.="<input type='hidden' name='test_ratio0' value='$test_ratio[0]'>";
                    $main.="<input type='hidden' name='test_ratio1' value='$test_ratio[1]'>";
                    $main.="<input type='hidden' name='score2[$student_sn[$m]]' value='$score2[$m]'>";
                    $main.="<input type='hidden' name='test_kind' value='定期評量'>";
                }
                if($edit=="s2"){
                    $main.="<tr bgcolor='#ffffff'><td>".$student_info[$m][2]."</td><td>".$student_info[$m][4]."</td><td>".$score1[$m]."</td><td><input type=\"text\" name=score2[$student_sn[$m]] size=\"8\" maxlength=\"4\" value='".$score2[$m]."' class='border_no' style='width: 100%'></td><td>".$ave_score[$m]."</td></tr>";
                    $main.="<input type='hidden' name='student_sn[$m]' value='$student_sn[$m]'>";
                    $main.="<input type='hidden' name='test_ratio0' value='$test_ratio[0]'>";
                    $main.="<input type='hidden' name='test_ratio1' value='$test_ratio[1]'>";
                    $main.="<input type='hidden' name='score1[$student_sn[$m]]' value='$score1[$m]'>";
                    $main.="<input type='hidden' name='test_kind' value='平時成績'>";
                }
                if(($edit!="s1")&&($edit!="s2")){
                    $main.="<tr bgcolor='#ffffff'><td>".$student_info[$m][2]."</td><td>".$student_info[$m][4]."</td><td>".$score1[$m]."</td><td>".$score2[$m]."</td><td>".$ave_score[$m]."</td></tr>";
                }
            }
            if(($edit=="s2")||($edit=="s1")) $main.="<tr bgcolor='#ffffff'><td colspan=5 align=center><input type='submit' name='Submit6' value='儲存'></td></tr>";
            $main.="
				<input type='hidden' name='year_seme' value='$year_seme'>
				</form>";
            echo $main;
            echo"</table>";
        }

    }
//結束主網頁顯示區----------------------------------------------------------------
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();
?>


<script language="JavaScript1.2">
<!-- Begin
function jumpMenu0(){
	var str, classstr ;
 if (document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value!="") {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value;
	}
}

function jumpMenu1(){
	var str, classstr ;
 if ((document.form1.year_name.value!="") & (document.form1.year_name.options[document.form1.year_name.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form1.year_seme.value + "&year_name=" + document.form1.year_name.options[document.form1.year_name.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
 if ((document.form2.year_name.value!="") & (document.form2.me.options[document.form2.me.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form2.year_seme.value + "&year_name=" + document.form2.year_name.value + "&me=" + document.form2.me.options[document.form2.me.selectedIndex].value;
	}
}

function jumpMenu3(){
	var str, classstr ;
 if ((document.form3.year_name.value!="") & (document.form3.me.value!="") & (document.form3.scope_subject.options[document.form3.scope_subject.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form3.year_seme.value + "&year_name=" + document.form3.year_name.value + "&me=" +document.form3.me.value + "&scope_subject=" + document.form3.scope_subject.options[document.form3.scope_subject.selectedIndex].value;
	}
}

function jumpMenu4(){
	var str, classstr ;
 if ((document.form4.year_name.value!="") & (document.form4.me.value!="") & (document.form4.scope_subject.value!="") & (document.form4.stage_score.options[document.form4.stage_score.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form4.year_seme.value + "&year_name=" + document.form4.year_name.value + "&me=" +document.form4.me.value + "&scope_subject=" +document.form4.scope_subject.value + "&stage_score=" + document.form4.stage_score.options[document.form4.stage_score.selectedIndex].value;
	}
}

function openwindow(url_str){
window.open (url_str,"成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}


//  End -->
</script>
