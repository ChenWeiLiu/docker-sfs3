<?php

// $Id: input.php 5310 2009-01-10 07:57:56Z hami $

// 取得設定檔
include "config.php";

sfs_check();

//取得學期
$year_seme=($_GET['year_seme'])?$_GET['year_seme']:$_POST['year_seme'];
$year_name=($_GET['year_name'])?$_GET['year_name']:$_POST['year_name'];
$me=($_GET['me'])?$_GET['me']:$_POST['me'];
$edit_one=$_GET['edit_one'];
$pact=$_POST['pact'];
$edit_score=$_POST['edit_score'];
$edit_comment=$_POST['edit_comment'];
$save_comment=$_POST['save_comment'];
$default=$_POST['default'];
if (!$default) $stud_score=$_POST['stud_score'];

//程式檔頭
head("日常成績管理");
print_menu($menu_p);

echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
} else {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
}

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

$menu="
	<table cellspacing=0 cellpadding=0>
	<tr>
	<td>$year_seme_menu</td><td>$class_year_menu</td><td>$class_year_name_menu</td>
	</tr>
	</table>";
echo $menu;

if(($year_seme)&&($year_name)&&($me)){
	$Create_db="CREATE TABLE if not exists class_comment_admin (
		ccm_id int(10) unsigned NOT NULL  auto_increment,
		teacher_sn smallint(6) unsigned NOT NULL default '' ,
		class_id varchar(11) NOT NULL default '',
		sel_year smallint(5) NOT NULL default '0',
		sel_seme enum('1','2') NOT NULL default '1',
		update_time datetime NOT NULL default '0000-00-00 00:00:00' ,
		update_teacher_sn smallint(6) unsigned NOT NULL default '' ,
		sendmit enum('0','1') NOT NULL default '1',
		PRIMARY KEY  (ccm_id))";
	mysql_query($Create_db);

	$delta_year=curr_year()-$sel_year;
	$class_id= sprintf ("%03d_%1d_%02d_%02d", curr_year(),curr_seme(),$year_name+$delta_year,$me);
	$seme_year_seme=sprintf("%03d%1d",$sel_year,$sel_seme);


	//取得學生名單
	$class_num= $year_name.sprintf("%02d",$me);
	$i=0;
	$sql="select a.student_sn,a.seme_num from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_num' and a.student_sn=b.student_sn and b.stud_study_cond=0 order by a.seme_num";
	$rs=$CONN->Execute($sql) or die($sql);;
	while (!$rs->EOF) {
		$sn=$rs->fields["student_sn"];
		$stud_sn[]=$sn;
		$stud_num[$sn]=$rs->fields["seme_num"];
		$i++;
		$rs->MoveNext();
	}
	$student_number=$i;

	//取得該成績單和成績無關的欄位欄位資料
	$i=0;
	$comm_num=0;
	if($edit_comment)
		$quick_input="<img src='../score_input/images/comment1.png'  border='0' onClick=\"return OpenWindow2('quick_input_memo.php?class_id=".$class_num."&seme_year_seme=".$seme_year_seme."')\">";
	
	$text_table="
		<td>日常考查成績</td>
		<td colspan='4' align='center'>團體活動成績</td>
		<td rowspan='2' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E5'>公共服務</a><br><font color='#ff0000'>(±5)</font></td>
		<td rowspan='2' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E6'>校外特殊表現</a><br><font color='#ff0000'>(+5)</font></td>
		<td rowspan='2' width='300'>日常行為表現描述文字說明$quick_input</td>";
	$text_table2="
	<tr>
	<td align='center' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E0'>導師評分</a><br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E1'>班級活動</a><br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E2'>社團活動</a><br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E3'>自治活動</a><br><font color='#ff0000'>(±5)</font></td>
	<td align='center' width='30'><a href='$_SERVER[PHP_SELF]?year_seme=$year_seme&year_name=$year_name&me=$me&edit_one=E4'>例行活動</a><br><font color='#ff0000'>(±5)</font></td></tr>";
 	
	//設定加減成績資料
	$score_sn=array("0"=>"1","1"=>"1","2"=>"1","3"=>"1","4"=>"1","5"=>"1","6"=>"0");

	$main="	<script language=\"JavaScript\">
		var remote=null;
		function OpenWindow(p,x){
		strFeatures =\"top=300,left=20,width=500,height=210,toolbar=0,resizable=no,scrollbars=yes,status=0\";
		remote = window.open(\"../academic_record/comment.php?cq=\"+p,\"MyNew\", strFeatures);
		if (remote != null) {
		if (remote.opener == null)
		remote.opener = self;
		}
		if (x == 1) { return remote; }
		}
		</script>
		<table bgcolor=#ffffff border=0 cellpadding=2 cellspacing=1>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='col1'>
		<tr bgcolor='#ffffff'><td>
		<input type='submit' name='edit_score' value='編輯日常成績'> 
		<input type='submit' name='edit_comment' value='編輯文字描述'>";
	if ($edit_score || $edit_comment || $edit_one)
		$main.="<input type='submit' name='default' value='回復原值'>
			<input type='submit' name='save_comment' value='儲存'>";
	$main.=	"<br>
		<table bgcolor='#9ebcdd' cellspacing='1' cellpadding='4' class='small'>
		<tr bgcolor='#c4d9ff'>
		<td align='center' rowspan='2'>座號</td>
		<td align='center' rowspan='2'>姓名</td>
		$text_table
		</tr>
		$text_table2
		";
		
	//顯示成績
	for ($m=0;$m<count($stud_sn);$m++){
		$rs=&$CONN->Execute("select stud_name,stud_id from stud_base where student_sn='$stud_sn[$m]'");

		//取得座號及姓名
		$stud_name=$rs->fields['stud_name'];
		$stud_id[$m]=$rs->fields['stud_id'];
		$site_num=$stud_num[$stud_sn[$m]];

		$table_score="";
		$table_temp="";
		
		//取得或儲存導師加減分
		if (($pact=="s")&&($save_comment)) {
			for ($i=0;$i<7;$i++) {
				settype($stud_score[$stud_id[$m]][$i],integer);
				$j=$stud_score[$stud_id[$m]][$i];
				if (($j < -5) or ($j > 5)) $j=0;
				$tp[$i]=$j;
				$stud_score[$stud_id[$m]][$i]=$j;
			}
			$rs_nor=&$CONN->Execute("select * from seme_score_nor where seme_year_seme='$seme_year_seme' and stud_id='$stud_id[$m]'");
			if ($rs_nor->RecordCount( )) {
				$rs_nor=&$CONN->Execute("update seme_score_nor set score1='$tp[0]',score2='$tp[1]',score3='$tp[2]',score4='$tp[3]',score5='$tp[4]',score6='$tp[5]',score7='$tp[6]' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id[$m]'");
			} else {
				$rs_nor=&$CONN->Execute("insert into seme_score_nor (seme_year_seme,stud_id,score1,score2,score3,score4,score5,score6,score7) values ('$seme_year_seme','$stud_id[$m]','$tp[0]','$tp[1]','$tp[2]','$tp[3]','$tp[4]','$tp[5]','$tp[6]')");
			}

			check_nor($seme_year_seme,$stud_id[$m],1,$nor_val[$nor_kind[$tp[0]+5]]);
			$i=round(($tp[1]+$tp[2]+$tp[3]+$tp[4])/4);
			check_nor($seme_year_seme,$stud_id[$m],2,$nor_val[$nor_kind[$i+5]]);
			check_nor($seme_year_seme,$stud_id[$m],3,$nor_val[$nor_kind[$tp[5]+5]]);
			check_nor($seme_year_seme,$stud_id[$m],4,$nor_val[$nor_kind[$tp[6]+5]]);
		} else {
			$rs_nor=&$CONN->Execute("select * from seme_score_nor where seme_year_seme='$seme_year_seme' and stud_id='$stud_id[$m]'");
			$stud_score[$stud_id[$m]][0]=$rs_nor->fields['score1'];
			$stud_score[$stud_id[$m]][1]=$rs_nor->fields['score2'];
			$stud_score[$stud_id[$m]][2]=$rs_nor->fields['score3'];
			$stud_score[$stud_id[$m]][3]=$rs_nor->fields['score4'];
			$stud_score[$stud_id[$m]][4]=$rs_nor->fields['score5'];
			$stud_score[$stud_id[$m]][5]=$rs_nor->fields['score6'];
			$stud_score[$stud_id[$m]][6]=$rs_nor->fields['score7'];
		}
		$j=0;
		while ($j<count($score_sn)) {
			$score_value=$stud_score[$stud_id[$m]][$j];
			if (!$edit_score and !$edit_one){
				$table_score.="<td align='center'>$score_value</td>";
			}elseif(!$edit_score and $edit_one){
				if(substr($edit_one,1,2)==$j)
					$table_score.="<td align='center'><input type='text' name='stud_score[".$stud_id[$m]."][".$j."]' value='$score_value' style='width: 100%' size='5' ></td>";
				else
					$table_score.="<td align='center'><input type='hidden' name='stud_score[".$stud_id[$m]."][".$j."]' value='$score_value'>$score_value</td>";
			}else{
				$table_score.="<td align='center'><input type='text' name='stud_score[".$stud_id[$m]."][".$j."]' value='$score_value' style='width: 100%'></td>";
			}
			$j++;
		}
		$rs=&$CONN->Execute("select student_sn,ss_score_memo from stud_seme_score_nor where student_sn='$stud_sn[$m]' and ss_id='0' and seme_year_seme='$seme_year_seme'");
		$have_value=($rs->fields['student_sn'])?"1":"0";
		if ($stud_score[$stud_id[$m]][$j]=="") $stud_score[$stud_id[$m]][$j]=addslashes($rs->fields['ss_score_memo']);
		if (!$edit_comment) {
			$table_temp.=$stud_score[$stud_id[$m]][$j];
		} else {
			$col_name="stud_score[".$stud_id[$m]."][".$j."]";
			$id_name="V".$stud_id[$m]."_".$j;
			$button="<img src='".$SFS_PATH_HTML."images/comment.png' width='16' height='16' border='0' align='left' onClick=\"return OpenWindow('$id_name')\">";
			$table_temp.=$button."<input type='text' name='$col_name' id='$id_name' value='".$stud_score[$stud_id[$m]][$j]."' style='width: 100%'>";
		}
		$main.="
			<tr bgcolor=#ffffff>
			<td align='right'>$site_num &nbsp;</td>
               		<td>&nbsp; $stud_name &nbsp;</td>
               		$table_score
               		<td>".stripslashes($table_temp)."</td>
               		</tr>
               		";
		$ss_score_memo=$stud_score[$stud_id[$m]][$j];
		if (($pact=="c")&&($save_comment)) {
			$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));
			if ($have_value=="0") {	
				$sql_data = "insert into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score_memo) values ('$seme_year_seme','$stud_sn[$m]','0','$ss_score_memo')";
			} else {
				$sql_data= "update stud_seme_score_nor set ss_score_memo='$ss_score_memo' where student_sn='$stud_sn[$m]' and ss_id='0' and seme_year_seme='$seme_year_seme'";
			}
			$CONN->Execute($sql_data) or die($sql_data);
		}
		if ($save_comment) {
			$teacher_sn=$_SESSION['session_tea_sn'];
			$sql_update="select ccm_id from class_comment_admin where teacher_sn='$teacher_sn' and class_id='$class_id' and sel_year='$sel_year' and sel_seme='$sel_seme'";
			$rs=$CONN->Execute($sql_update) or die($sql_update);
			$update_data=$rs->FetchRow();
			if (empty($update_data)) {
				$sql_update="insert into class_comment_admin (teacher_sn,class_id,sel_year,sel_seme,update_time,update_teacher_sn,sendmit) values ('$teacher_sn','$class_id','$sel_year','$sel_seme','$today','$teacher_sn','1')";
			} else {
				$ccm_id=$rs->$update_data[ccm_id];
				$sendmit=($send_comment)?'0':'1';
				$sql_update="update class_comment_admin set update_time='$today',update_teacher_sn='$teacher_sn',sendmit='$sendmit' where ccm_id='$ccm_id'";
			}
			$CONN->Execute($sql_update) or die($sql_update);
		}
	}
	if ($edit_score or $edit_one) $act="<input type='hidden' name='pact' value='s'>";
	elseif ($edit_comment) $act="<input type='hidden' name='pact' value='c'>";
	else $act="";
	$main.="</table>
		<input type='hidden' name='year_name' value='$year_name'>
		<input type='hidden' name='year_seme' value='$year_seme'>
		<input type='hidden' name='me' value='$me'>
		$act";
	if ($edit_score || $edit_comment || $edit_one) 
		$main.="<input type='submit' name='default' value='回復原值'>
			<input type='submit' name='save_comment' value='儲存'>";
	$main.="</form>";
}



echo $main;
echo "</tr></table></tr></table>";

function score($type=""){
	if ($type=="1") 
		$end_num=-5;
	else
		$end_num=0;
	$default_selected=0;
	for ($i=5;$i>=$end_num;$i--) {
		$selected=($i==$default_selected)?"selected":"";
		$j=($i>0)?"+".$i:$i;
		$option_temp.="<option value='$i' $selected>$j</option>";
	}
	return $option_temp;
}

function check_nor($seme_year_seme,$stud_id,$ss_id,$ss_val) {
	global $CONN;
	
	$sql_chk="select * from stud_seme_score_oth where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$ss_id'";
	$rs_chk=$CONN->Execute($sql_chk);
	$chk_ss_id=$rs_chk->fields['ss_id'];
	$chk_ss_val=$rs_chk->fields['ss_val'];
       	if (($chk_ss_id!="")&&($ss_val!="")) {
     		$sql_chk="update stud_seme_score_oth set ss_val='$ss_val' where seme_year_seme='$seme_year_seme' and stud_id='$stud_id' and ss_id='$ss_id'";
      		$rs_chk=$CONN->Execute($sql_chk);
       	} else {
       		$sql_chk="insert into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values ('$seme_year_seme','$stud_id','生活表現評量','$ss_id','$ss_val')";
       		$rs_chk=$CONN->Execute($sql_chk);
      	}
}
?>

<script language="JavaScript">
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
 if ((document.form3.year_name.value!="") & (document.form3.me.value!="") & (document.form3.stud_id.options[document.form3.stud_id.selectedIndex].value!="")) {
	location="<?php echo $_SERVER['PHP_SELF'] ?>?year_seme=" + document.form3.year_seme.value + "&year_name=" + document.form3.year_name.value + "&me=" +document.form3.me.value + "&stud_id=" + document.form3.stud_id.options[document.form3.stud_id.selectedIndex].value;
	}
}

function OpenWindow2(url_str){
window.open (url_str,"成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}
//  End -->
</script>

<?php
foot();
?>
