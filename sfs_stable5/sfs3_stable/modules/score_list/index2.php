<?php

// $Id: index2.php 7710 2013-10-23 12:40:27Z smallduh $

/*引入學務系統設定檔*/
include "../../include/config.php";
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
$year_seme=$_GET['year_seme'];
$year_name=$_GET['year_name'];
$me=$_GET['me'];
$stage=$_GET['stage'];
$fine_print=$_GET['fine_print'];
$sel_year=$_GET['sel_year'];
$sel_seme=$_GET['sel_seme'];
$doc=$_GET['doc'];
$sxw=$_GET['sxw'];
if(($doc=="1")||($sxw=="1")){
    $filename=($doc=="1")?"score_paper.doc":"score_paper.sxw";
    header("Content-disposition: filename=$filename");
    header("Content-type: application/octetstream ; Charset=Big5");
    //header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
    header("Expires: 0");
}
//秀出網頁
if($fine_print!="1") head("成績查詢");
$yorn=findyorn();
//列出橫向的連結選單模組
//$menu_p = array("index.php"=>"顯示學期成績",
//"manage.php"=>"*管理學期成績","normal.php"=>"*"."平時成績","test.php"=>"*"."使用說明");
//print_menu($menu_p);
//設定主網頁顯示區的背景顏色
if($fine_print!="1") echo "<table border=0 cellspacing=1 cellpadding=2
width=100% bgcolor=#cccccc><tr><td bgcolor=#FFFFFF>";
//if(empty($sel_year))$sel_year = curr_year(); //目前學年
//if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
//$score_semester="score_semester_".$sel_year."_".$sel_seme;
$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id

$col_name="year_seme";
$id=$year_seme;
$show_year_seme=select_year_seme($id,$col_name);
$year_seme_menu="
    <form name='form0' method='post' action='{$_SERVER['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu0()'>
            $show_year_seme
         </select>
     </form>";
$sel_year_seme=explode("_",$year_seme);
if($fine_print!="1") $sel_year=$sel_year_seme[0];
if($fine_print!="1") $sel_seme=$sel_year_seme[1];
$score_semester="score_semester_".$sel_year."_".$sel_seme;
//echo $sel_year."".$sel_seme;

if($year_seme){
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
    //echo $year_name;
}
//echo $class_year_menu;
if(($year_seme)&&($year_name)){
    $col_name="me";
    $id=$me;
    $show_class_year_name=select_school_class_name($year_name,$id,$col_name,$sel_year,$sel_seme);
    $class_year_name_menu="
    <form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu2()'>
            $show_class_year_name
        </select>
        <input type='hidden' name='year_seme' value='$year_seme'>
        <input type='hidden' name='year_name' value='$year_name'>
    </form>";

//echo $class_year_name_menu;
}
if(($year_seme)&&($year_name)&&($me)){
    $c_year=$year_name;
    $c_name=$me;
    $col_name="stage";
    $id=$stage;
    $show_stage=select_stage($c_year,$c_name,$id,$col_name,$sel_year,$sel_seme);
    $stage_menu="
    <form name='form3' method='post' action='{$_SERVER['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu3()'>
            $show_stage
        </select>
        <input type='hidden' name='year_seme' value='$year_seme'>
        <input type='hidden' name='year_name' value='$year_name'>
        <input type='hidden' name='me' value='$me'>
    </form>";


}
$menu="
    <table cellspacing=0 cellpadding=0>
        <tr>
            <td>$year_seme_menu</td><td>$class_year_menu</td><td>$class_year_name_menu</td><td>$stage_menu</td>
        </tr>
    </table>";
if($fine_print!="1") echo $menu;

//以上為選單bar

if($year_name && $me && $stage){

//取出本學年本學期的學校成績共通設定
$sql="select * from score_setup where class_year=$year_name and
year='$sel_year' and semester='$sel_seme'";
$rs=$CONN->Execute($sql);
$score_mode= $rs->fields['score_mode'];
$test_ratio= $rs->fields['test_ratio'];
$performance_test_times=  $rs->fields['performance_test_times'];
if($score_mode=="all"){
    $test_ratio=explode("-",$test_ratio);
    if ($test_ratio[0]=="") $test_ratio[0]=60;
    if ($test_ratio[1]=="") $test_ratio[1]=40;
    $m1=$test_ratio[0]/$performance_test_times;
    $m2=$test_ratio[1]/$performance_test_times;
    $test_ratio=array("$m1","$m2");
    for($i=0;$i<$performance_test_times;$i++){
        $test_ratio_all[$i]=$test_ratio;
    }

    //$test_ratio_all_1=$test_ratio_all_2=$test_ratio_all_3=$test_ratio_all_4=$test_ratio_all_5=$test_ratio;
}
elseif($score_mode=="severally"){
    $test_ratio=explode(",",$test_ratio);
    for($i=0;$i<count($test_ratio);$i++){
        $test_ratio_all[$i]=explode("-",$test_ratio[$i]);
        if($test_ratio_all[$i][0]=="")
$test_ratio_all[$i][0]=(60/$performance_test_times);
        if($test_ratio_all[$i][1]=="")
$test_ratio_all[$i][1]=(40/$performance_test_times);
        if($stage==($i+1)) $test_ratio=$test_ratio_all[$i];

    }
}
else{
    $test_ratio[0]=(60/$performance_test_times);
    $test_ratio[1]=(40/$performance_test_times);
    for($i=0;$i<$performance_test_times;$i++){
        $test_ratio_all[$i]=$test_ratio;
    }
}
//該年級該班級該階段的成績
        $score_edu_adm="score_edu_adm_".$sel_year."_".$sel_seme;
        if(strlen($sel_year)==2) $sel_year="0".$sel_year;
        if(strlen($year_name)==1) $year_name="0".$year_name;
        if(strlen($me)==1) $me="0".$me;
        $class_id=$sel_year."_".$sel_seme."_".$year_name."_".$me;
        if($stage==255){
            $sql="select * from $score_edu_adm where class_id='$class_id' and
enable='1' order by student_sn";
            $rs=$CONN->Execute($sql);
            if($rs){
                $i=0;
                while(!$rs->EOF){
                    $edu_adm_id[$i]=$rs->fields['edu_adm_id'];
                    $student_sn[$i]=$rs->fields['student_sn'];
                    //echo $student_sn[$i]." ";
                    $ss_id[$i]=$rs->fields['ss_id'];
                    $score[$i]=$rs->fields['score'];
                    if($score[$i]=="-100") $score[$i]="";
                    $test_sort[$i]=$rs->fields['test_sort'];
                    //$Sscore[$student_sn[$i]][$ss_id[$i]][$test_sort[$i]]=$score[$i];
                    if($test_sort[$i]==255)
$Sscore[$student_sn[$i]][$ss_id[$i]]=$score[$i];
                    else{
                        if($yorn=='y'){
							$c_ts=$test_sort[$i]-1;
							//echo $c_ts."<br>";
							$RR=($test_ratio_all[$c_ts][0]+$test_ratio_all[$c_ts][1])/100;
							//echo $RR."<br>";
							$Sscore[$student_sn[$i]][$ss_id[$i]]=$Sscore[$student_sn[$i]][$ss_id[$i]]+($score[$i]*$RR);
						}
						else{
							$c_ts=$test_sort[$i]-1;
							if($test_sort[$i]==254)
{																							
								$scoreA[$i]=$score[$i]*$test_ratio_all[0][1]*$performance_test_times/100;
							}
							else
$scoreA[$i]=$score[$i]*$test_ratio_all[$c_ts][0]/100;
							$Sscore[$student_sn[$i]][$ss_id[$i]]=$Sscore[$student_sn[$i]][$ss_id[$i]]+$scoreA[$i];
						
						}                    
					}

                    //$mscore[$i]=$student_sn[$i]."_".$ss_id[$i]."_".$test_sort[$i]."_".$Sscore[$student_sn[$i]][$ss_id[$i]][$test_sort[$i]]."<br>";
                    $i++;
                    $rs->MoveNext();
                }
            }
        }
        else{
            $sql="select * from $score_edu_adm where test_sort='$stage' and
class_id='$class_id' and enable='1'";
            $rs=$CONN->Execute($sql);
            if($rs){
            $i=0;
                while(!$rs->EOF){
                    $edu_adm_id[$i]=$rs->fields['edu_adm_id'];
                    $student_sn[$i]=$rs->fields['student_sn'];
                    //echo $student_sn[$i]." ";
                    $ss_id[$i]=$rs->fields['ss_id'];
                    $score[$i]=$rs->fields['score'];
                    if($score[$i]=="-100") $score[$i]="";
                    $Sscore[$student_sn[$i]][$ss_id[$i]]=$score[$i];
                    $i++;
                    $rs->MoveNext();
                }
            }
        }

        //該年級的共通設定
        if($stage==255)
            $sql="select * from score_ss where year='$sel_year' and
semester='$sel_seme' and class_year='$year_name' and enable='1' and
need_exam='1' order by scope_id";
        else
            $sql="select * from score_ss where year='$sel_year' and
semester='$sel_seme' and class_year='$year_name' and enable='1' and
need_exam='1' and print='1' order by scope_id";
        //echo $sql;
		$rs=$CONN->Execute($sql);
        $i=0;
        while(!$rs->EOF){
            $scope_ss_id[$i]=$rs->fields['ss_id'];
			//echo $scope_ss_id[$i];
            $exam_ss_id.=$rs->fields['ss_id']." ";
            $scope_id[$i]=$rs->fields['scope_id'];
            $ii=$i-1;
            if("$scope_id[$i]"=="$scope_id[$ii]") {$rs->MoveNext(); continue;}
			//if(in_array($scope_id[$i],$scope_id)){$rs->MoveNext();
			//continue;}

			$subject_id[$i]=$rs->fields['subject_id'];
            $rate_scope[$scope_ss_id[$i]]=$rs->fields['rate'];
            //$total_rate=$total_rate+$rate_scope[$scope_ss_id[$i]];
            $scope_name[$i]=ss_id_to_scope_name($scope_ss_id[$i]);
            $one_L[$i]=$scope_name[$i];

			if($subject_id[$i]!='0'){                
				if($stage==255)
                    $sql1="select * from score_ss where year='$sel_year' and
semester='$sel_seme' and class_year='$year_name' and enable='1' and
need_exam='1' and scope_id='$scope_id[$i]' order by ss_id";
                else
                    $sql1="select * from score_ss where year='$sel_year' and
semester='$sel_seme' and class_year='$year_name' and enable='1' and
need_exam='1' and print='1' and scope_id='$scope_id[$i]' order by ss_id";
               //echo $sql1;
			   $rs1=$CONN->Execute($sql1);
                $j=0;
                while(!$rs1->EOF){
                    $subject_ss_id[$i][$j]=$rs1->fields['ss_id'];
					//echo $subject_ss_id[$i][$j];
                    $Ssubject_ss_id.=$rs1->fields['ss_id']." ";
                    $s_subject_id[$i][$j]=$rs1->fields['subject_id'];
                    $rate_subject[$i][$j]=$rs1->fields['rate'];
                    $Rrate_subject[$subject_ss_id[$i][$j]]=$rate_subject[$i][$j];
                    //$total_rate=$total_rate+$Rrate_subject[$subject_ss_id[$i][$j]];
                    $subject_name[$i][$j]=ss_id_to_subject_name($subject_ss_id[$i][$j]);
                    $two_L[$i][$j].=$subject_name[$i][$j]."*".$rate_subject[$i][$j];
                    $j++;
                    $rs1->MoveNext();
                }
            }
            else{ $one_L[$i].="*".$rate_scope[$scope_ss_id[$i]]; }
            //$total_rate=$total_rate+$rate_scope[$scope_ss_id[$i]];
            $i++;
            $rs->MoveNext();
        }
        //echo $total_rate;
		
        $ss_title="<tr bgcolor='#F2E18E'><td rowspan='2' width='40'
align='center'>座號</td><td rowspan='2' width='80' align='center'>姓名</td>";
        for($i=0;$i<count($scope_name);$i++){
            $colspan=count($subject_name[$i]);
            
			if($subject_name[$i]){
                $ss_title.="<td rowspan='1' colspan=$colspan width='80'
align='center'>".$one_L[$i]."</td>";
            }
            else{
                $ss_title.="<td rowspan='2' width='80'>".$one_L[$i]."</td>";
            }
        }
        $ss_title.="<td align='center' rowspan='2' width='40'>總分</td><td
align='center' rowspan='2' width='40'>平均</td><td align='center' rowspan='2'
width='40'>名次</td></tr>";
        $ss_title.="<tr bgcolor='#FDF1B5'>";
        for($i=0;$i<count($scope_name);$i++){
            if($subject_name[$i]){
                for($j=0;$j<count($subject_name[$i]);$j++){
                    $ss_title.="<td width='80'
align='center'>".$two_L[$i][$j]."</td>";
                }
            }
        }
        $ss_title.="</tr>";

//學生的成績
        $student_sn=class_id_to_student_sn($class_id);
        //echo $class_id;
        $exam_ss_id=trim($exam_ss_id);
        //echo $exam_ss_id;
		$exam_ss_id=explode(" ",$exam_ss_id);
        $Ssubject_ss_id=trim($Ssubject_ss_id);
        //echo $Ssubject_ss_id;
        $Ssubject_ss_id=explode(" ",$Ssubject_ss_id);        
	   for($i=0;$i<count($student_sn);$i++){
            $Rrate[$i]=0;
            $SSscore_total=0;
            $student_name[$i]=student_sn_to_stud_name($student_sn[$i]);
            //echo $student_name[$i]." ";
            $site_num[$i]=student_sn_to_site_num($student_sn[$i]);
            //$student_score_list.="<tr><td
            //bgcolor='#B8FF91'>$student_sn[$i]</td><td
            //bgcolor='#CFFFC4'>$student_name[$i]</td>";
            for($j=0;$j<count($exam_ss_id);$j++){
                $SSscore[$i][$j]=$Sscore[$student_sn[$i]][$exam_ss_id[$j]];
                //判斷exam_ss_id[$j]是領域還是分科
				$sql_s="select subject_id from score_ss where
ss_id='$exam_ss_id[$j]'";
				//echo $sql_s;
				$rs_s=$CONN->Execute($sql_s) or die($sql_s);
				$WD[$j]=$rs_s->fields['subject_id'];
				//echo $WD[$j]."<br>";
				$rate12=($WD[$j]=="0")?"{$rate_scope[$exam_ss_id[$j]]}":"{$Rrate_subject[$exam_ss_id[$j]]}";
				//echo $rate12;
				//echo
				//$exam_ss_id[$j]."---".$SSscore[$i][$j]."*".$rate12."<br>";
                $SSSscore=$SSscore[$i][$j]*$rate12;
                if($SSscore[$i][$j]!="") $Rrate[$i]=$Rrate[$i]+($rate12);
                //echo $Rrate." ";
                $SSSSscore=$SSscore[$i][$j];
                //$student_score_list.="<td
                //bgcolor='#ffffff'>$SSSSscore</td>";
                $SSscore_total=$SSscore_total+$SSSscore;
            }
            $SSSscore_total[$i]=round($SSscore_total,2);
            //echo "<br>";
            //$student_score_list.="<td
            //bgcolor='#B4BED3'>$SSSscore_total[$i]</td><td
            //bgcolor='#CBD6ED'>$SSscore_total/$Rrate</td><td
            //bgcolor='#D8E4FD'>$how_big</td></tr>";
			//echo  $SSSscore_total[$i]."/".$Rrate[$i]." ";       
		}
        for($i=0;$i<count($student_sn);$i++){
            $student_name[$i]=student_sn_to_stud_name($student_sn[$i]);
            $how_big=how_big($SSSscore_total[$i],$SSSscore_total);
            $student_score_list.="<tr><td
bgcolor='#B8FF91'>$site_num[$i]</td><td
bgcolor='#CFFFC4'>$student_name[$i]</td>";
            for($j=0;$j<count($exam_ss_id);$j++){
                $SSSSscore=$SSscore[$i][$j];
                if($SSSSscore) $SSSSscore=round($SSSSscore,2);
				if($SSSSscore<0) $SSSSscore="";
                $student_score_list.="<td bgcolor='#ffffff'>$SSSSscore</td>";
            }

			//echo $SSSscore_total[$i]."/".$Rrate[$i];			
			if($Rrate)
$one_student_average[$i]=round($SSSscore_total[$i]/$Rrate[$i],2);
            if($SSSscore_total[$i]<0) $SSSscore_total[$i]="";
			if($one_student_average[$i]<0)
$one_student_average[$i]="";			
			$student_score_list.="<td
bgcolor='#B4BED3'>$SSSscore_total[$i]</td><td
bgcolor='#CBD6ED'>$one_student_average[$i]</td><td
bgcolor='#D8E4FD'>$how_big</td></tr>";
        }
        settype($sel_year,"integer");
        if($fine_print!="1") echo "<a
href='{$_SERVER['PHP_SELF']}?fine_print=1&sel_year=$sel_year&sel_seme=$sel_seme&year_name=$year_name&me=$me&stage=$stage'>友善列印</a>&nbsp;&nbsp;";
        $score_bar=score_head($sel_year,$sel_seme,$year_name,$me,$stage);
        if($fine_print!="1") echo "<a
href='{$_SERVER['PHP_SELF']}?fine_print=1&sel_year=$sel_year&sel_seme=$sel_seme&year_name=$year_name&me=$me&stage=$stage&sxw=1'>轉成sxw檔</a>&nbsp;&nbsp;";
        if($fine_print!="1") echo "<a
href='{$_SERVER['PHP_SELF']}?fine_print=1&sel_year=$sel_year&sel_seme=$sel_seme&year_name=$year_name&me=$me&stage=$stage&doc=1'>轉成doc檔</a>";
        if($fine_print==1) echo "<font size=+2>$score_bar</font>" ;
        if(($doc=="1")||($sxw=="1")){
            echo "<table bgcolor=#0000ff border='1' cellpadding='6'
cellspacing='0'>";
        }
        else{
            echo "<table bgcolor=#0000ff border='0' cellpadding='6'
cellspacing='1'>";
        }
        echo $ss_title;
        echo "$student_score_list";
        echo "</table>";
}

//結束主網頁顯示區
if($fine_print!="1") echo "</td>";
if($fine_print!="1") echo "</tr>";
if($fine_print!="1") echo "</table>";

//程式檔尾
if($fine_print!="1") foot();

if(($doc!="1")&&($sxw!="1")){
?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu0(){
	var str, classstr ;
 if
(document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value!="")
{
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" +
document.form0.year_seme.options[document.form0.year_seme.selectedIndex].value;
	}
}

function jumpMenu1(){
	var str, classstr ;
 if ((document.form1.year_seme.value!="") &
(document.form1.year_name.options[document.form1.year_name.selectedIndex].value!=""))
{
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" +
document.form1.year_seme.value + "&year_name=" +
document.form1.year_name.options[document.form1.year_name.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
 if ((document.form2.year_seme.value!="") &
(document.form2.year_name.value!="") &
(document.form2.me.options[document.form2.me.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" +
document.form2.year_seme.value + "&year_name=" +
document.form2.year_name.value + "&me=" +
document.form2.me.options[document.form2.me.selectedIndex].value;
	}
}

function jumpMenu3(){
	var str, classstr ;
 if ((document.form3.year_seme.value!="") &
(document.form3.year_name.value!="") & (document.form3.me.value!="") &
(document.form3.stage.options[document.form3.stage.selectedIndex].value!=""))
{
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" +
document.form3.year_seme.value + "&year_name=" +
document.form3.year_name.value + "&me=" +document.form3.me.value + "&stage=" +
document.form3.stage.options[document.form3.stage.selectedIndex].value;
	}
}
//  End -->
</script>
<?php
}
?>
