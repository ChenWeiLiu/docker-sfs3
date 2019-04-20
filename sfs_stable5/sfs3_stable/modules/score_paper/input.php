<?php
// $Id: input.php 7028 2012-12-04 05:44:50Z chiming $

include "config.php";
sfs_check();

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($_REQUEST[year_seme])){
	$ys=explode("-",$_REQUEST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}else{
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期
}

/*
//取得任教班級代號
$class_num=get_teach_class();
$class_all=class_num_2_all($class_num);
if(empty($class_num)){
	$act="error";
	$error_title="無班級編號";
	$error_main="找不到您的班級編號，故您無法使用此功能。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的任教資料輸入系統中。
	</ol>";
}
*/

//主選單設定
$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

$act=$_REQUEST[act];


if($_REQUEST[chknext]){
	$ss_temp = "&chknext=$_REQUEST[chknext]&nav_next=$_REQUEST[nav_next]";
}

$stud_id=$_REQUEST[stud_id];


//執行動作判斷

if($act=="save"){
	save_score_nor($sel_year,$sel_seme,$_REQUEST[student_sn],$_REQUEST[nor_score],$_REQUEST[nor_score_memo]);
	save_score_oth($sel_year,$sel_seme,$stud_id);
	header("location: {$_SERVER['PHP_SELF']}?sel_year=$sel_year&sel_seme=$sel_seme&class_id=$_REQUEST[class_id]&stud_id=$stud_id".$ss_temp);
}elseif($_REQUEST[error]==1){
	user_error("該班級無學生資料，無法繼續。<ol>
	<li>請確認您有任教班級。
	<li>請確認教務處已經將您的學生資料輸入系統中。
	<li>匯入學生資料：『學務系統首頁>教務>註冊組>匯入資料』(<a href='".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php'>".$SFS_PATH_HTML."school_affairs/student_reg/create_data/mstudent2.php</a>)</ol>",256);
	}else{
	if($_REQUEST[chknext]){$stud_id=$_REQUEST[nav_next];}
	$main=&main_form($sel_year,$sel_seme,$_REQUEST[class_id],$stud_id);
}


//秀出網頁
head("成績單製作");
?>


<script language="JavaScript">
<!-- Begin
function jumpMenu_seme(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=" + document.myform.year_seme.options[document.myform.year_seme.selectedIndex].value + "&class_id=<?php echo $_REQUEST[class_id]?>";
}

function jumpMenu_seme_1(){
	location="<?php echo $_SERVER['PHP_SELF']?>?act=<?php echo $act;?>&year_seme=<?php echo $_REQUEST[year_seme]?>&class_id=" + document.myform.class_id.options[document.myform.class_id.selectedIndex].value;
}
//  End -->
</script>

<?php
echo $main;
foot();

function &main_form($sel_year,$sel_seme,$class_id,$stud_id){
	global $CONN,$performance_option,$school_menu_p,$performance,$SFS_PATH_HTML;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	//取得年度與學期的下拉選單
	$date_select=&class_ok_setup_year($sel_year,$sel_seme,"year_seme","jumpMenu_seme");
	//年級與班級選單
	$class_select=&get_class_select($sel_year,$sel_seme,"","class_id","jumpMenu_seme_1",$_REQUEST['class_id']);
	
	//取得學生選單	
	if(empty($class_select) or empty($date_select))	header("location:{$_SERVER['PHP_SELF']}?error=1");
	
	if(!empty($class_id)){
		//轉換班級代碼
		$class=class_id_2_old($class_id);
		//假如沒有指定學生，取得第一位學生
		if(empty($stud_id))$stud_id=get_no1($class_id);
		//若仍是沒有 $stud_id ，則秀出錯誤訊息
		if(empty($stud_id))header("location:{$_SERVER['PHP_SELF']}?error=1");
		
		
		$gridBgcolor="#DDDDDC";
		//已製作顯示顏色
		$over_color = "#223322";
		//左選單女生顯示顏色
		$non_color = "blue";
		
		$grid1 = new ado_grid_menu($_SERVER['PHP_SELF'],$URI,$CONN);  //建立選單	   	
		$grid1->key_item = "stud_id";  // 索引欄名 
		$grid1->formname = "myform";
		$grid1->display_item = array("sit_num","stud_name");  // 顯示欄名   
		$grid1->bgcolor = $gridBgcolor;
		$grid1->display_color = array("1"=>"blue","2"=>"red");
		$grid1->color_index_item ="stud_sex" ; //顏色判斷值
		$grid1->class_ccs = " class=leftmenu";  // 顏色顯示
		$grid1->sql_str = "select stud_id,stud_name,stud_sex,substring(curr_class_num,4,2)as sit_num  from stud_base where curr_class_num like '$class[2]%' and stud_study_cond=0 order by curr_class_num";   //SQL 命令
		$grid1->do_query(); //執行命令 
	
		$stud_select = $grid1->get_grid_str($stud_id); // 顯示畫面
		
		
		if(!empty($stud_id)){
			
			if ($_REQUEST[chknext] && $_REQUEST[nav_next]<>'')	$stud_id = $_REQUEST[nav_next];
		
			//求得學生ID	
			$student_sn=stud_id2student_sn($stud_id);
			
			//取得指定學生資料
			//$stu=get_stud_base("",$stud_id);
			$stu=get_stud_baseB($student_sn,$stud_id);
			
			//座號
			$stu_class_num=curr_class_num2_data($stu['curr_class_num']);
			
			
			//取得該學生日常生活表現評量值
			$oth_data=&get_oth_value($stud_id,$sel_year,$sel_seme);
			$oth_array=array();
			foreach($performance as $id=>$sk){
				$oth_array[$id]=$oth_data['生活表現評量'][$id];	
			}
			
			$form="<table>";			
			$i=1;
			foreach($performance as $pf){
				//製作選項
				$form_option="<option value=''></option>";
				foreach($performance_option as $po){			
					$selected=($oth_array[$i]==$po)?"selected":"";
					$form_option.="<option value='$po' $selected>$po</option>";
				}
				$form.="<tr><td>$pf</td><td><select name='a_$i'>$form_option</select></td></tr>";
				$i++;
			}
			$form.="</table>";
			
			
			//取得學生學期評語及分數
			$nor_value=get_nor_value($student_sn,$sel_year,$sel_seme,$class_id);
			
			//評分選項
			$nor_array=&score2str_arr($class);
			$nor_score="<select name='nor_score'><option value=''></option>";
			foreach($nor_array as $nc=>$ns){				
				$selected=($nor_value['分數等第']==$nc)?"selected":"";
				$nor_score.="<option value='$nc' $selected>$ns</option>";
			}
			$nor_score.="</select>";
			
			$checked=($_REQUEST[chknext])?"checked":"";
			
			$stud_all="請輸入<b>".$stu[stud_name]."（".$stu_class_num[num]."號）</b>的成績單相關資料：<a href='".$SFS_PATH_HTML."modules/score_paper/make.php?stud_id=$stud_id&sel_year=$sel_year&sel_seme=$sel_seme&class_id=$class_id'>製作".$stu[stud_name]."的成績單</a>
			<form action='{$_SERVER['PHP_SELF']}' method='post' name='col1' id='col1'>
			<table width=300 cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'><tr bgcolor='#FFFFE7'><td valign=top>
			
			等第： $nor_score 
			<p>
			導師評語及建議 <br>
			<img src='$SFS_PATH_HTML/images/comment.png' width=16 height=16 border=0 align='left' onClick=\"return OpenWindow('nor_score_memo')\">
			<textarea name='nor_score_memo' id='nor_score_memo' cols=30 rows=5>".$nor_value['導師評語及建議']."</textarea>
			
			$form
			<br>
			<input type='hidden' name='act' value='save'>
			<input type='hidden' name='stud_id' value='$stud_id'>
			<input type='hidden' name='student_sn' value='$student_sn'>
			<input type='hidden' name='class_id' value='$class_id'>
			<input type='hidden' name='nav_next' >
			<p align='center'><input type='checkbox' name='chknext' value='1' $checked>自動跳下一位<br>
			<input type='submit' value='輸入' onClick='return checkok();'></p>
			</form>
			</td></tr></table>";
			//<img src='../score_input/images/comment1.png'  border='0' onclick=openwindow(\"../score_input/quick_input_memo.php?class_id=".$class[2]."&teacher_course=3899&ss_id=125&seme_year_seme=".$seme_year_seme."\")>
			
		}else{
			$stud_all="尚未選擇學生</td></tr><table>";
		}
		
	}
    $tool_bar=&make_menu($school_menu_p);
    
    //取得指定學生資料
	//$stu=get_stud_base("",$stud_id);
	$stu=get_stud_baseB($student_sn,$stud_id);

	
	
	$main="
	$tool_bar
	<script language=\"JavaScript\">
	var remote=null;
	function OpenWindow(p,x){
		strFeatures =\"top=300,left=20,width=500,height=150,toolbar=0,resizable=yes,scrollbars=yes,status=0\";
		remote = window.open(\"comment.php?cq=\"+p,\"MyNew\", strFeatures);
		if (remote != null) {
		if (remote.opener == null)
			remote.opener = self;
		}
		if (x == 1) { return remote; }
	}
	function checkok() {
		document.col1.nav_next.value = document.myform.nav_next.value;	
		return true;	
	}
	
	function openwindow(url_str){
	window.open (url_str,\"評語輸入\",\"toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420\");
	}
	</script>
	<table bgcolor='#9EBCDD' cellspacing=0 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td bgcolor='#BDD3FF' valign=top>
		<table>
		<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform'>
  		<tr><td valign=top align=center>
  		$date_select<br>
  		$class_select<br>
  		$stud_select
  		
  		</td></tr>
		</form>
		</table>
	</td><td valign=top>$stud_all</td></tr>
	</table>
	";
	return $main;
}



//導師評語及建議及等第
function save_score_nor($sel_year,$sel_seme,$student_sn,$nor_score,$nor_score_memo){
	global $CONN;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	$query = "replace into stud_seme_score_nor (seme_year_seme,student_sn,ss_id,ss_score,ss_score_memo) values('$seme_year_seme','$student_sn',0,'$nor_score','$nor_score_memo')";
	$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);
}
	
//日常生活表現存檔
function save_score_oth($sel_year,$sel_seme,$stud_id){
	global $CONN;
	$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
	for ($i=1;$i<=4;$i++){
		$query = "replace into stud_seme_score_oth (seme_year_seme,stud_id,ss_kind,ss_id,ss_val) values('$seme_year_seme','$stud_id','生活表現評量','$i','".$_REQUEST["a_$i"]."')";
		$CONN->Execute($query) or trigger_error("sql 錯誤 $query",E_USER_ERROR);		
	}
}
?>
