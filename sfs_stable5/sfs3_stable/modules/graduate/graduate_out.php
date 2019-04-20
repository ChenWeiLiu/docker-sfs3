<?php

// $Id: graduate_out.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";

if($_GET['class_year_b']) $class_year_b=$_GET['class_year_b'];
else $class_year_b=$_POST['class_year_b'];
if($_GET['select_seme_year']) $select_seme_year=$_GET['select_seme_year'];
else $select_seme_year=$_POST['select_seme_year'];
if($_GET['Cyear'] || $_GET['Cyear']=="0") $Cyear=$_GET['Cyear'];
else $Cyear=$_POST['Cyear'];
if($Cyear!="") $Cyear=intval($Cyear);
if($_GET['class_id']) $class_id=$_GET['class_id'];
else $class_id=$_POST['class_id'];
if($_GET['grad_all']) $grad_all=$_GET['grad_all'];
else $grad_all=$_POST['grad_all'];
if($_GET['date_all']) $date_all=$_GET['date_all'];
else $date_all=$_POST['date_all'];
if($_GET['word_all']) $word_all=$_GET['word_all'];
else $word_all=$_POST['word_all'];
if($_GET['num_start']) $num_start=$_GET['num_start'];
else $num_start=$_POST['num_start'];
//使用者認證
sfs_check();
//程式檔頭
head("畢業生作業");

//增加升學的學校名稱欄位
$rs01=$CONN->Execute("select new_school from grad_stud where 1");
if(!$rs01) $CONN->Execute("ALTER TABLE grad_stud ADD new_school varchar(40)");


print_menu($menu_p);
//設定主網頁顯示區的背景顏色
echo "
<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
<tr>
<td bgcolor='#FFFFFF'>";

//網頁內容請置於此處
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$new_sel_year=date("Y")-1911;//目前民國年

//儲存
if($_POST['Submit3']=='儲存'){
    for($i=0;$i<count($_POST['stud_id']);$i++){
		$chk_sql="select grad_sn from grad_stud where stud_id='{$_POST['stud_id'][$i]}' and stud_grad_year='$sel_year'";
		$chk_rs=$CONN->Execute($chk_sql) or die($chk_sql);
		$grad_sn[$i]=$chk_rs->fields['grad_sn'];
		if($_POST['P_date'][$i]!="") $P_date[$i]=ChtoD($dday="{$_POST['P_date'][$i]}", $st="-");
		else $P_date[$i]="0000-00-00";
		if($grad_sn[$i]){
			if($_POST['sure_grad'][$i]=="3") $upd_sql="update grad_stud set stud_grad_year='$sel_year',stud_id='{$_POST['stud_id'][$i]}',class_year='{$_POST['class_year'][$i]}',class_sort='{$_POST['class_sort'][$i]}',grad_kind='{$_POST['sure_grad'][$i]}',grad_date=null ,grad_word='',grad_num='' where grad_sn='$grad_sn[$i]'";
			else $upd_sql="update grad_stud set stud_grad_year='$sel_year',stud_id='{$_POST['stud_id'][$i]}',class_year='{$_POST['class_year'][$i]}',class_sort='{$_POST['class_sort'][$i]}',grad_kind='{$_POST['sure_grad'][$i]}',grad_date='$P_date[$i]',grad_word='{$_POST['P_word'][$i]}',grad_num='{$_POST['P_num'][$i]}' where grad_sn='$grad_sn[$i]'";
        	$CONN->Execute($upd_sql) or die($upd_sql);
		}
		else{
			$ins_sql="insert into  grad_stud (stud_grad_year,stud_id,class_year,class_sort,grad_kind,grad_date,grad_word,grad_num) values('$sel_year','{$_POST['stud_id'][$i]}','{$_POST['class_year'][$i]}','{$_POST['class_sort'][$i]}','{$_POST['sure_grad'][$i]}','$P_date[$i]','{$_POST['P_word'][$i]}','{$_POST['P_num'][$i]}')";
        	$CONN->Execute($ins_sql) or die($ins_sql);
		}
		//更新學籍資料表
		//if($_POST['sure_grad'][$i]=="1" || $_POST['sure_grad'][$i]=="2") $CONN->Execute("update stud_base set stud_study_cond='5' where stud_id='{$_POST['stud_id'][$i]}'");
		//elseif($_POST['sure_grad'][$i]=="3") $CONN->Execute("update stud_base set stud_study_cond='0' where stud_id='{$_POST['stud_id'][$i]}'");
		if($_POST['sure_grad'][$i]=="3") $CONN->Execute("update stud_base set stud_study_cond='0' where stud_id='{$_POST['stud_id'][$i]}'");
    }


}

//選擇年級
$class_year_menu=&get_class_year_select($sel_year,$sel_seme,$Cyear,$jump_fn="jumpMenu1",$col_name="Cyear");
echo "
	<table><tr><td align='left' colspan='2'>
	<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
	請選擇要進行畢業轉出的年級$class_year_menu</form></td>";

//選擇班級

$class_select_menu=&get_class_select($sel_year,$sel_seme,$Cyear,$col_name="class_id",$jump_fn="jumpMenu2",$class_id,$mode="長");
echo"
	<td  align='left' colspan='2'>
	<form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
	$class_select_menu
	<input type='hidden' name='Cyear' value='$Cyear'>
	</form></td></tr>
";

//列出該年級所有學生提供選擇畢業轉出者

if($Cyear=="" && $class_id=="") echo "</table>";
else{
	//工具列
	echo "<tr><td><a href='{$_SERVER['PHP_SELF']}?Cyear=$Cyear&class_id=$class_id&grad_all=1&date_all=$date_all&word_all=$word_all&num_start=$num_start'><span class='button'>全部畢業</span></a></td>";
	echo "<td><form name='f_date' method='post' action='{$_SERVER['PHP_SELF']}'>
      			  <input type='hidden' name='Cyear' value='$Cyear'>
				  <input type='hidden' name='class_id' value='$class_id'>
				  <input type='hidden' name='grad_all' value='$grad_all'>
      			  <input type='hidden' name='word_all' value='$word_all'>
				  <input type='hidden' name='num_start' value='$num_start'>
				  <input type='text' name='date_all' size='12' maxlength='40' value='$date_all'>
      			  <input type='submit' name='Sdate' value='日期複製'>
				  </form>
			  </td>";
    echo " <td><form name='f_word' method='post' action='{$_SERVER['PHP_SELF']}'>
      			  <input type='hidden' name='Cyear' value='$Cyear'>
				  <input type='hidden' name='class_id' value='$class_id'>
				  <input type='hidden' name='grad_all' value='$grad_all'>
				  <input type='hidden' name='date_all' value='$date_all'>
				  <input type='hidden' name='num_start' value='$num_start'>
				  <input type='text' name='word_all' size='12' maxlength='40' value='$word_all'>
				  <input type='submit' name='Sword' value='字複製'>
				  </form>
			  </td>";
    echo " <td><form name='f_num' method='post' action='{$_SERVER['PHP_SELF']}'>
      			  <input type='hidden' name='Cyear' value='$Cyear'>
				  <input type='hidden' name='class_id' value='$class_id'>
				  <input type='hidden' name='grad_all' value='$grad_all'>
				  <input type='hidden' name='date_all' value='$date_all'>
				  <input type='hidden' name='word_all' value='$word_all'>
				  <input type='text' name='num_start' size='12' maxlength='40' value='$num_start'>
				  <input type='submit' name='Snum' value='號起始值'>
				  </form>
			  </td>";
echo " <td><form name='form3' method='post' action='{$_SERVER['PHP_SELF']}'>
      			  <input type='hidden' name='Cyear' value='$Cyear'>
				  <input type='hidden' name='class_id' value='$class_id'>
				  <input type='submit' name='Submit3' value='儲存'>
			  </td>";


	$seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);
	if($class_id) {
		$class_id_arr=explode("_",$class_id);
		$seme_class_a=sprintf("%d%02d",$class_id_arr[2],$class_id_arr[3]);
		$sql="select a.stud_id,a.seme_class,a.seme_num,a.seme_class_name, b.stud_study_cond from stud_seme a, stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class_a' and b.stud_study_cond=0 and a.student_sn=b.student_sn order by a.seme_class,a.seme_num";
	}
	else $sql="select a.stud_id,a.seme_class,a.seme_num,a.seme_class_name, b.stud_study_cond from stud_seme a, stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$Cyear%' and b.stud_study_cond=0 and a.student_sn=b.student_sn order by a.seme_class,a.seme_num";
	$rs=$CONN->Execute($sql);
	$i=0;
	while(!$rs->EOF){
		$stud_id[$i]=$rs->fields['stud_id'];
		$rs_grad=$CONN->Execute("select grad_kind,grad_date,grad_word,grad_num,grad_score from grad_stud where stud_id='$stud_id[$i]' and stud_grad_year='$sel_year'");
		$grad_kind[$i]=$rs_grad->fields['grad_kind'];
		$grad_date[$i]=$rs_grad->fields['grad_date'];
		//if($grad_date[$i]) $grad_date[$i]=DtoCh($dday="$grad_date[$i]", $st="-");
		$grad_word[$i]=$rs_grad->fields['grad_word'];
		$grad_num[$i]=$rs_grad->fields['grad_num'];				
		if($rs_grad->fields['grad_score']!="")$grad_score[$i]=number_format($rs_grad->fields['grad_score'],2);
		$seme_class[$i]=$rs->fields['seme_class'];
		$class_year[$i]=substr($seme_class[$i],0,-2);
		$class_sort[$i]=substr($seme_class[$i],-2);
		$seme_class_name[$i]=$rs->fields['seme_class_name'];
		$class_year_name[$i]=substr($seme_class[$i],0,-2)."年".(substr($seme_class[$i],1,2)+0)."班";
		$seme_num[$i]=$rs->fields['seme_num'];		
		$rs->MoveNext();
		$i++;
	}
	echo "
	<tr><td colspan='5'><table bgcolor='black' border='0' cellpadding='2' cellspacing='1'>
		<tr bgcolor='#FFEC6E'>
		<td><a href='{$_SERVER['PHP_SELF']}'>班級</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>座號</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>姓名</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>成績</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>畢業</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>肄業</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>留級</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>證書日期</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>證書字</a></td>
		<td><a href='{$_SERVER['PHP_SELF']}'>證書號</a></td>
		</tr>";
	$date_all=trim($date_all);
	$word_all=trim($word_all);
	$L=strlen($num_start);

	for($j=0;$j<count($stud_id);$j++){
		//由stud_id找出stud_name
		$sql_stud_name = "select stud_name from stud_base where stud_id='$stud_id[$j]' ";
		$rs_stud_name=$CONN->Execute($sql_stud_name) or die($sql_stud_name);
		$stud_name[$j] = $rs_stud_name->fields['stud_name'];
		$bgc=($j%2==1)?"#C4FAAE":"#B6C9FD";
		if($grad_all=="1" || $grad_kind[$j]=="1") $ck1[$j]="checked";
		elseif($grad_kind[$j]=="2") $ck2[$j]="checked";
		elseif($grad_kind[$j]=="3") $ck3[$j]="checked";
		if($date_all) $date_all_a[$j]=$date_all;
		else { $G=DtoCh($dday="$grad_date[$j]", $st="-"); $date_all_a[$j]=($grad_date[$j]=="0000-00-00" || $grad_date[$j]=="")?"":"$G";}
		if($word_all) $word_all_a[$j]=$word_all;
		else $word_all_a[$j]=$grad_word[$j];
		if($num_start) $num_start_a[0]=$num_start;
		else $num_start_a[$j]=$grad_num[$j];
		if($L!="0") $num_start_a[$j]=sprintf("%0".$L."d",$num_start_a[$j]);
		$k=$j+1;
		if($L!="0" && $ck3[$j]!="checked") $num_start_a[$k]=$num_start_a[$j]+1;
		elseif($L!="0" && $ck3[$j]=="checked") $num_start_a[$k]=$num_start_a[$j];		
		if($sure_grad[$j]=="3") {$date_all_a[$j]=""; $word_all_a[$j]=""; $num_start_a[$j]="";}
		//1:畢業 2:肄業 3:留級
		if($ck3[$j]=="checked") {$date_all_a[$j]=""; $word_all_a[$j]=""; $num_start_a[$j]="";}		
		$score_color=($grad_score[$j]<60)?"#FF0000":"";
		echo "
			<tr bgcolor='$bgc'>
			<td>$class_year_name[$j]</td>
			<td>$seme_num[$j]</td>
			<td>$stud_name[$j]</td>
			<td><font color='$score_color'>$grad_score[$j]</font></td>
			<input type='hidden' name='stud_id[$j]' value='$stud_id[$j]'>
			<input type='hidden' name='class_year[$j]' value='$class_year[$j]'>
			<input type='hidden' name='class_sort[$j]' value='$class_sort[$j]'>
			<td><input type='radio' name='sure_grad[$j]' value='1' $ck1[$j]></td>
			<td><input type='radio' name='sure_grad[$j]' value='2' $ck2[$j]></td>
			<td><input type='radio' name='sure_grad[$j]' value='3' $ck3[$j]></td>
			<td><input type='text' name='P_date[$j]' size='10' maxlength='40' value='$date_all_a[$j]'></td>
			<td><input type='text' name='P_word[$j]' size='16' maxlength='40' value='$word_all_a[$j]'></td>
			<td><input type='text' name='P_num[$j]' size='16' maxlength='40' value='$num_start_a[$j]'></td>
			</tr>";

	}
	echo "</table></td></tr></table></form>";
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
 if (document.form1.Cyear.options[document.form1.Cyear.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?Cyear=" + document.form1.Cyear.options[document.form1.Cyear.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
    if (document.form2.class_id.options[document.form2.class_id.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?Cyear=" + document.form2.Cyear.value + "&class_id=" + document.form2.class_id.options[document.form2.class_id.selectedIndex].value;
	}
}
//  End -->
</script>
