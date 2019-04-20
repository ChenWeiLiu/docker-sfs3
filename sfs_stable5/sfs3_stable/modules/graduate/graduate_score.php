<?php

// $Id: graduate_score.php 5310 2009-01-10 07:57:56Z hami $

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
if($_GET['Sweight']) $Sweight=$_GET['Sweight'];
else $Sweight=$_POST['Sweight'];
if($_GET['Wyear']) $SWyear=$_GET['Wyear'];
else $Wyear=$_POST['Wyear'];
if($_GET['Wclass']) $Wclass=$_GET['Wclass'];
else $Wclass=$_POST['Wclass'];
if($_GET['view']) $view=$_GET['view'];
else $view=$_POST['view'];
while(list($key , $val) = each($_POST['ss_id'])) {
	$w_ss_id[$key]=$val;
}

//使用者認證
sfs_check();
//程式檔頭
head("畢業生作業");

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

if($Sweight=="確定"){
	if($Wyear){
		$seme_year_seme=sprintf("%03d%d",$sel_year,$sel_seme);		
		//先來找stud_id由stud_seme
		$sql_stid="select stud_id from stud_seme where seme_year_seme='$seme_year_seme' and seme_class like '$Wyear%'";
		//echo $sql_stid."<br>";
		$rs_stid=$CONN->Execute($sql_stid);
		$a=0;
		while(!$rs_stid->EOF){
			$stud_id[$a]=$rs_stid->fields['stud_id'];//現在先用ss_id將來可能會改用link_ss，以更加通用
			//轉換stud_id為student_sn
			 $student_sn[$a]=stud_id2student_sn($stud_id[$a]);
			//echo $student_sn[$a]."<br>";
			$stud_name[$a]=stud_name($stud_id[$a]);			
			echo "<br>開始算 ".$stud_name[$a].$stud_id[$a]." 的成績嘍！<br>";
			reset($w_ss_id);
			$b=0;
			while(list($key[$a] , $val[$a]) = each($w_ss_id)) {
				if($val[$a]=="0") continue;
				//echo $key."=>".$val."<br>" ;
				//到stud_seme_score找該生每一學期的總成績
				$sql_seme_score="select ss_score from stud_seme_score where student_sn='$student_sn[$a]' and ss_id='$key[$a]'";
				//echo $sql_seme_score."<br>";
				$rs_seme_score=$CONN->Execute($sql_seme_score);
				$ss_score[$a][$b]=$rs_seme_score->fields['ss_score'];
				$grad_score[$stud_id[$a]]=$grad_score[$stud_id[$a]]+$ss_score[$a][$b]*$val[$a];
				$Weight[$a]=$Weight[$a]+$val[$a];
				if($ss_score[$a][$b]=="") $ss_score[$a][$b]="0";
				echo ss_id_to_year_seme($key[$a]).ss_id_to_subject_name($key[$a])."：".$ss_score[$a][$b]."*".$val[$a]."=".$ss_score[$a][$b]*$val[$a]."<br>";
			}
			$average_score[$a]=$grad_score[$stud_id[$a]]/$Weight[$a];
			echo "總分：".$grad_score[$stud_id[$a]]."<br>";
			echo "平均：".$average_score[$a]."<br>";
			echo "將平均寫入畢業生資料表grad_stud(如果該畢業生存在的話).............<br>";
			$CONN->Execute("UPDATE grad_stud SET grad_score ='$average_score[$a]' WHERE stud_id= '$stud_id[$a]' ");
			$rs_stid->MoveNext();
			$a++;
		}		

	}
	elseif($Wclass){
		while(list($key , $val) = each($w_ss_id)) {
			echo $key."=>".$val."<br>" ;		
		}			
	}
}
else{
	//選擇年級
	$class_year_menu=&get_class_year_select($sel_year,$sel_seme,$Cyear,$jump_fn="jumpMenu1",$col_name="Cyear");
	echo "
		<table cellspacing=0 cellpadding=2><tr><td align='left' width='10%' nowrap>
		<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
		請選擇要計算畢業成績的$class_year_menu</form></td>";

	//選擇班級

	$class_select_menu=&get_class_select($sel_year,$sel_seme,$Cyear,$col_name="class_id",$jump_fn="jumpMenu2",$class_id,$mode="長");
	echo "
		<td  align='left' width='10%' nowrap>
		<form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
		或$class_select_menu
		<input type='hidden' name='Cyear' value='$Cyear'>
		</form></td>
	";
	echo "<td><a href='view_grad_score.php'><button>觀看畢業成績</button></a></td></tr>";
	//列出該年級所有學生

	if($Cyear=="" && $class_id=="") echo "</table>";
	else{
		echo "<tr><td colspan='2'><form name='weight_f' method='post' action='{$_SERVER['PHP_SELF']}'></td></tr>";
		if($class_id) {
			$class_id_A=explode("_",$class_id);
			$Cyear=intval($class_id_A[2]);
			$ben=$class_id_A[3];
		}	
		for($i=$Cyear;$i>0;$i--,$sel_year--){//學年
			for($j=2;$j>0;$j--){//學期
				//echo $sel_year."---".$j."---".$i."<br>";
				if($class_id) $sql_ss="select ss_id,rate from score_ss where year='$sel_year' and semester='$j' and class_year='$i'  and class_id like '%$ben' and enable='1' and  need_exam='1'";
				else $sql_ss="select ss_id,rate from score_ss where year='$sel_year' and semester='$j' and class_year='$i' and enable='1' and  need_exam='1'";				
				//目前選擇班級可能還無作用但是為了因應將來課程可能是以班級為單位來做設定故留下此一選項
				$rs_ss=$CONN->Execute($sql_ss);
				$m=$rs_ss->fields['ss_id'];				
				$bgcolor=($j==1)?"bgcolor='#FFF589'":"bgcolor='#FCA4FF'";
				if($m!="") echo "<tr $bgcolor><td colspan='3'>".$i."年級第".$j."學期：&nbsp;&nbsp;&nbsp;&nbsp;";
				$k=0;
				while(!$rs_ss->EOF){
					$ss_id[$k]=$rs_ss->fields['ss_id'];//現在先用ss_id將來可能會改用link_ss，以更加通用
					$R[$k]=$rs_ss->fields['rate'];//該課程的學分數
					echo "&nbsp;&nbsp;".ss_id_to_subject_name($ss_id[$k])."*";
					echo "<input type='text' name='ss_id[$ss_id[$k]]' value='$R[$k]' size='2' maxlength='2'>\n";
					$rs_ss->MoveNext();
					$k++;
				}
				if($m!="") {echo "</td></tr>"; $mm++;}

			}
			//echo $i."年級<br>";
		}
		if($mm>0) {
			echo "<tr><td colspan='2'><input type='hidden' name='Wclass' value='$class_id'><input type='hidden' name='Wyear' value='$Cyear'><input type='submit' name='Sweight' value='確定'></form></td></tr>";
			echo "<tr><td colspan='2'><br>操作說明：<br>
									<li>在您想要加入計算畢業成績的課程右方欄位輸入加權數，若加權數為0表示此一課程不列入畢業成績的計算。</li>
									<li>程式預設所有課程軍列入畢業成績的計算且加權數為1</li></td></tr>";
		}	
		else    { echo "</table></table>";  trigger_error("您所選的年級或班級沒有任何課程設定，請重新操作！<br>".$sql_ss,E_USER_ERROR); }
		echo "</table>";
	}
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
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?class_id=" + document.form2.class_id.options[document.form2.class_id.selectedIndex].value;
	}
}
//  End -->
</script>
