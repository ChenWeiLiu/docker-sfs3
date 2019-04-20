<?php
// $Id: print_tol_o.php 5310 2009-01-10 07:57:56Z hami $

/*入學務系統設定檔*/
include "config.php";
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
$year_name=$_GET['year_name'];
$me=$_GET['me'];
$stage=$_GET['stage'];

$yorn=findyorn();

//秀出網頁
head("成績顯示");

print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "<table border=0 cellspacing=0 cellpadding=2 width=100% bgcolor=#cccccc><tr><td>";

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$score_semester="score_semester_".$sel_year."_".$sel_seme;
$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
$col_name="year_name";
$id=$year_name;
$show_class_year=&select_school_class($id,$col_name,$sel_year,$sel_seme);
$class_year_menu="
    <form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu1()'>
            $show_class_year
        </select>
    </form>";
//echo $class_year_menu;
/*************************************************************************/
if($year_name){
    $col_name="me";
    $id=$me;
    $show_class_year_name=&select_school_class_name($year_name,$id,$col_name,$sel_year,$sel_seme);
    $class_year_name_menu="
    <form name='form2' method='post' action='{$_SESSION['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu2()'>
            $show_class_year_name
        </select>
        <input type='hidden' name='year_name' value='$year_name'>
    </form>";

//echo $class_year_name_menu;
}
if(($year_name)&&($me)){
    $c_year=$year_name;
    $c_name=$me;
    $col_name="stage";
    $id=$stage;
    $show_stage=&select_stage($c_year,$c_name,$id,$col_name,$sel_year,$sel_seme);
    $stage_menu="
    <form name='form3' method='post' action='{$_SESSION['PHP_SELF']}'>
        <select name='$col_name' onChange='jumpMenu3()'>
            $show_stage
        </select>
        <input type='hidden' name='year_name' value='$year_name'>
        <input type='hidden' name='me' value='$me'>
    </form>";


}
$menu="
    <table>
        <tr>
            <td>$class_year_menu</td><td>$class_year_name_menu</td><td>$stage_menu</td>
        </tr>
    </table>";
echo $menu;

//以上為選單bar
/******************************************************************************************/

if($year_name && $me && $stage){
    //取出本學年本學期的學校成績共通設定
    $sql="select * from score_setup where class_year=$year_name and year='$sel_year' and semester='$sel_seme'";
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
        $test_ratio_new=array("$m1","$m2");
        for($i=0;$i<$performance_test_times;$i++){
            $test_ratio_all[$i]=$test_ratio_new;
        }

        //$test_ratio_all_1=$test_ratio_all_2=$test_ratio_all_3=$test_ratio_all_4=$test_ratio_all_5=$test_ratio;
    }
    elseif($score_mode=="severally"){
		//echo $test_ratio;
        $test_ratio=explode(",",$test_ratio);
        for($i=0;$i<count($test_ratio);$i++){
            $test_ratio_all[$i]=explode("-",$test_ratio[$i]);
            //$test_ratio_all[$i][0]=(60/$performance_test_times);
            //$test_ratio_all[$i][1]=(40/$performance_test_times);
            if($stage==($i+1)) $test_ratio_new=$test_ratio_all[$i];

			//先清空

			elseif($stage==255) {
				//全學期定期評量總比率				
				$test_ratio_new[0]=$test_ratio_new[0]+intval($test_ratio_all[$i][0]);
				
				//全學期平時成績總比率
				$test_ratio_new[1]=$test_ratio_new[1]+intval($test_ratio_all[$i][1]);
				
			}	
        }
		$test_ratio_all[$i]=$test_ratio_new;
		//echo $test_ratio_all[$i][0];
		//echo $test_ratio_new[1];
    }
    else{
        $test_ratio_new[0]=(60/$performance_test_times);
        $test_ratio_new[1]=(40/$performance_test_times);
        for($i=0;$i<$performance_test_times;$i++){
            $test_ratio_all[$i]=$test_ratio_new;
        }
    }

    $class_id=sprintf ("%03d_%d_%02d_%02d", $sel_year,$sel_seme,$year_name, $me);
    if($stage==255){
        $sql="select * from $score_semester where class_id='$class_id'";
        $rs=$CONN->Execute($sql);
        $i=0;
        while(!$rs->EOF){
            $student_sn[$i]= $rs->fields["student_sn"];
                //如果該位學生目前不在學的話
                $st_rs=$CONN->Execute("select stud_study_cond from stud_base where student_sn='$student_sn[$i]'") or die("help");
                //echo "select stud_study_cond from stud_base where student_sn='$student_sn[$i]'";
                $stud_study_cond[$i]=$st_rs->fields["stud_study_cond"];
                //echo $stud_study_cond[$i]." ";
                if($stud_study_cond[$i]!="0") {$rs->MoveNext(); continue;}
            $ss_id[$i]=$rs->fields["ss_id"];
            $test_kind[$i]=$rs->fields["test_kind"];
            $score[$i]=$rs->fields["score"];
            if($score[$i]==-100) $score[$i]="";
            $test_sort[$i]=$rs->fields["test_sort"];
            if($test_sort[$i]==255)
                $Sscore[$student_sn[$i]][$ss_id[$i]][$test_kind[$i]]=$score[$i];
            else{
                $c_ts=$test_sort[$i]-1;
                //echo $c_ts."<br>";
                $RR=($test_ratio_all[$c_ts][0]+$test_ratio_all[$c_ts][1])/100;
                //echo $score[$i]."*".$RR."<br>";
                $Sscore[$student_sn[$i]][$ss_id[$i]][$test_kind[$i]]=$Sscore[$student_sn[$i]][$ss_id[$i]][$test_kind[$i]]+($score[$i]*$RR);
                //echo $Sscore[$student_sn[$i]][$ss_id[$i]][$test_kind[$i]]."<br>";
            }
            $i++;
            $rs->MoveNext();
        }
    }
    else{
			$sql="select * from $score_semester where class_id='$class_id' and test_sort='$stage'";
			$rs=$CONN->Execute($sql);
			$i=0;
			while(!$rs->EOF){
				$student_sn[$i]= $rs->fields["student_sn"];
					$st_rs=$CONN->Execute("select stud_study_cond from stud_base where student_sn='$student_sn[$i]'") or die("help");
					//echo "select stud_study_cond from stud_base where student_sn='$student_sn[$i]'";
					$stud_study_cond[$i]=$st_rs->fields["stud_study_cond"];
					//echo $stud_study_cond[$i]." ";
					if($stud_study_cond[$i]!="0") {$rs->MoveNext(); continue;}
				$ss_id[$i]=$rs->fields["ss_id"];
				$test_kind[$i]=$rs->fields["test_kind"];
				$score[$i]=$rs->fields["score"];
				if($score[$i]==-100) $score[$i]="";
				$Sscore[$student_sn[$i]][$ss_id[$i]][$test_kind[$i]]=$score[$i];
				$i++;
				$rs->MoveNext();
        	}			
    }
        $test_kind=deldup($test_kind);
        $ss_id=deldup($ss_id);
        $student_sn=deldup($student_sn);
        for($i=0;$i<count($ss_id);$i++){
            $subject_name[$i]=ss_id_to_subject_name($ss_id[$i]);
            $subject_list.="
                <td width='80'>$subject_name[$i]</td>
            ";
            for($j=0;$j<count($student_sn);$j++){
                $SS1[$j]=$Sscore[$student_sn[$j]][$ss_id[$i]][定期評量];
                $SS2[$j]=$Sscore[$student_sn[$j]][$ss_id[$i]][平時成績];
                $SS3[$j]=$Sscore[$student_sn[$j]][$ss_id[$i]][全學期];
				if($SS3[$j]!=""){
                    $SSav[$j]=$SS3[$j];
                }
                else{
                   // echo $SS1[$j]."*".$test_ratio_new[0]."+".$SS2[$j]."*".$test_ratio_new[1]."/".$test_ratio_new[0]."+".$test_ratio_new[1]."<br>";
					if(($SS1[$j]!="-100")&&($SS2[$j]!="-100")){
                        if($yorn=='y') {
							 //if($score_mode=="severally")  $SSav[$j]=number_format(($SS1[$j]+$SS2[$j]),2);
							 //else $SSav[$j]=number_format(($SS1[$j]*$test_ratio_new[0]+$SS2[$j]*$test_ratio_new[1])/($test_ratio_new[0]+$test_ratio_new[1]),2);	
							 $SSav[$j]=number_format(($SS1[$j]*$test_ratio_new[0]+$SS2[$j]*$test_ratio_new[1])/($test_ratio_new[0]+$test_ratio_new[1]),2);							 
						}	 
						else $SSav[$j]=number_format($SS1[$j],2);
                    }
                    elseif(($SS1[$j]!="-100")&&($SS2[$j]=="-100")){
                        if($yorn=='y') $SSav[$j]=number_format($SS1[$j],2);
						else $SSav[$j]=number_format($SS1[$j],2);
                    }
                    elseif(($SS1[$j]=="-100")&&($SS2[$j]!="-100")){
                        if($yorn=='y') $SSav[$j]=number_format($SS2[$j],2);
						else $SSav[$j]="";
                    }
                    else{
                        $SSav[$j]="";
                }
                }
                $score_list[$j].="<td width='80'>$SSav[$j]</td>";
                $one_student_total[$j]=$one_student_total[$j]+$SSav[$j];
                $statistics_average[$i]=$statistics_average[$i]+$SSav[$j];
                if($SSav[$j]==100) $statistics_100[$i]++;
                elseif(($SSav[$j]<100)&&($SSav[$j]>=90)) $statistics_90[$i]++;
                elseif(($SSav[$j]<90)&&($SSav[$j]>=80)) $statistics_80[$i]++;
                elseif(($SSav[$j]<80)&&($SSav[$j]>=70)) $statistics_70[$i]++;
                elseif(($SSav[$j]<70)&&($SSav[$j]>=60)) $statistics_60[$i]++;
                elseif(($SSav[$j]<60)&&($SSav[$j]>=50)) $statistics_50[$i]++;
                elseif(($SSav[$j]<50)&&($SSav[$j]>=40)) $statistics_40[$i]++;
                elseif(($SSav[$j]<40)&&($SSav[$j]>=30)) $statistics_30[$i]++;
                elseif(($SSav[$j]<30)&&($SSav[$j]>=20)) $statistics_20[$i]++;
                elseif(($SSav[$j]<20)&&($SSav[$j]>=10)) $statistics_10[$i]++;
                else $statistics_0[$i]++;
            }

            $statistics_average[$i]=number_format($statistics_average[$i]/count($student_sn),2);
            $statistics_list_average.="<td width='80'>$statistics_average[$i]</td>";
            $statistics_list_100.="<td width='80' bgcolor='#FFFFFF'>$statistics_100[$i]</td>";
            $statistics_list_90.="<td width='80' bgcolor='#FFFFFF'>$statistics_90[$i]</td>";
            $statistics_list_80.="<td width='80' bgcolor='#FFFFFF'>$statistics_80[$i]</td>";
            $statistics_list_70.="<td width='80' bgcolor='#FFFFFF'>$statistics_70[$i]</td>";
            $statistics_list_60.="<td width='80' bgcolor='#FFFFFF'>$statistics_60[$i]</td>";
            $statistics_list_50.="<td width='80' bgcolor='#FFFFFF'>$statistics_50[$i]</td>";
            $statistics_list_40.="<td width='80' bgcolor='#FFFFFF'>$statistics_40[$i]</td>";
            $statistics_list_30.="<td width='80' bgcolor='#FFFFFF'>$statistics_30[$i]</td>";
            $statistics_list_20.="<td width='80' bgcolor='#FFFFFF'>$statistics_20[$i]</td>";
            $statistics_list_10.="<td width='80' bgcolor='#FFFFFF'>$statistics_10[$i]</td>";
            $statistics_list_0.="<td width='80' bgcolor='#FFFFFF'>$statistics_0[$i]</td>";
        }
        //$student_sn=deldup($student_sn);
        for($i=0;$i<count($student_sn);$i++){
            $student_info[$i]=student_sn_to_classinfo($student_sn[$i]);
            $student_sitenum[$i]=$student_info[$i][2];
            $student_name[$i]=$student_info[$i][4];
            $many_ss=count($ss_id);
            $one_student_average[$i]=number_format(($one_student_total[$i]/$many_ss),2);
            $seniority[$i]=how_big($one_student_total[$i],$one_student_total);
            $student_and_score_list.="
                <tr bgcolor=#ffffff>
                    <td width='40' bgcolor='#B8FF91'>$student_sitenum[$i]</td>
                    <td width='80' bgcolor='#CFFFC4'>$student_name[$i]</td>
                    $score_list[$i]
                    <td width='80' bgcolor='#B4BED3'>$one_student_total[$i]</td>
                    <td width='80' bgcolor='#CBD6ED'>$one_student_average[$i]</td>
                    <td width='80' bgcolor='#D8E4FD'>$seniority[$i]</td>
                </tr>
            ";
        }
        $main="
            <table bgcolor=#0000fff border='0' cellpadding='6' cellspacing='1'>
                <tr bgcolor=#FDC3F5>
                    <td width='40'>座號</td>
                    <td width='80'>姓名</td>
                    $subject_list
                    <td width='80'>總分</td>
                    <td width='80'>平均</td>
                    <td width='40'>名次</td>
                </tr>
                    $student_and_score_list
                <tr bgcolor=#FDC3F5>
                    <td width='40'>&nbsp;</td>
                    <td width='80'>各科平均</td>
                    $statistics_list_average
                    <td width='80'>&nbsp;</td>
                    <td width='80'>&nbsp;</td>
                    <td width='40'>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan=2><font color=#FFFFFF>成績分佈表</font></td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>100分</td>
                    $statistics_list_100
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>90-100分</td>
                    $statistics_list_90
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>80-90分</td>
                    $statistics_list_80
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>70-80分</td>
                    $statistics_list_70
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>60-70分</td>
                    $statistics_list_60
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>50-60分</td>
                    $statistics_list_50
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>40-50分</td>
                    $statistics_list_40
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>30-40分</td>
                    $statistics_list_30
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>20-30分</td>
                    $statistics_list_20
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>10-20分</td>
                    $statistics_list_10
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
                <tr>
                    <td width='40' bgcolor='#B8FF91'>&nbsp;</td>
                    <td width='80' bgcolor='#CFFFC4'>0-10分</td>
                    $statistics_list_0
                    <td width='80' bgcolor='#B4BED3'>&nbsp;</td>
                    <td width='80' bgcolor='#CBD6ED'>&nbsp;</td>
                    <td width='40' bgcolor='#D8E4FD'>&nbsp;</td>
                </tr>
            </table>";

        echo $main;
}
//結束主網頁顯示區
echo "</td>";
echo "</tr>";
echo "</table>";

//程式檔尾
foot();

?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu1(){
	var str, classstr ;
 if (document.form1.year_name.options[document.form1.year_name.selectedIndex].value!="") {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_name=" + document.form1.year_name.options[document.form1.year_name.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
 if ((document.form2.year_name.value!="") & (document.form2.me.options[document.form2.me.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_name=" + document.form2.year_name.value + "&me=" + document.form2.me.options[document.form2.me.selectedIndex].value;
	}
}

function jumpMenu3(){
	var str, classstr ;
 if ((document.form3.year_name.value!="") & (document.form3.me.value!="") & (document.form3.stage.options[document.form3.stage.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_name=" + document.form3.year_name.value + "&me=" +document.form3.me.value + "&stage=" + document.form3.stage.options[document.form3.stage.selectedIndex].value;
	}
}
//  End -->
</script>
