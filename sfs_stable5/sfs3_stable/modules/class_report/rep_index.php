<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//目前學期
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());
//目前選定學期
$the_seme=($_POST['the_seme']=="")?$c_curr_seme:$_POST['the_seme'];

switch ($_SESSION['session_who']) {
	//如果是老師, 取得所有學期
	case '教師':
		$select_seme = get_class_seme(); //學年度
		//取得目前學期的所有可讀取的成績單
		$select_report=get_report("list",$the_seme);
	break;

	//如果是學生, 取得就學學期
	case '學生':
	  //先取學生該學期就讀班級
		$sql="select seme_class from stud_seme where seme_year_seme='$the_seme' and student_sn='{$_SESSION['session_tea_sn']}'";
		$res=$CONN->execute($sql);
		$class_num=$res->fields['seme_class'];
		//該班級已在學的總學期數
		$select_seme=get_class_seme_select($class_num);
		//取得該學期的所有可讀取的成績單
		$select_report=get_report("list",$the_seme,$class_num);

	break;
} // end switch


//秀出 SFS3 標題
head("觀看成績單");

//列出選單
echo $tool_bar;

?>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="">
	<select size="1" name="the_seme" onchange="document.myform.submit()">
		<?php
		foreach ($select_seme as $k=>$v) {
		?>
			<option value="<?php echo $k;?>"<?php if ($the_seme==$k) echo " selected";?>><?php echo $v;?></option>
		<?php
		}
		?>
	</select>
	
	<select size="1" name="the_report" onchange="document.myform.submit()">
		<option value="">--請選擇成績單--</option>
		<?php
		foreach ($select_report as $k=>$v) {
		?>
			<option value="<?php echo $v['sn'];?>"<?php if ($_POST['the_report']==$v['sn']) echo " selected";?>><?php echo "[".$v['seme_class_cname']."]".$v['title'];?></option>
		<?php
		}
		?>
	</select>	
  <?php
  if ($_POST['the_report']) {
  	$REP_SETUP=get_report_setup($_POST['the_report']);
   	if (($REP_SETUP['open_read'] and $REP_SETUP['rep_classmates']) or $_SESSION['session_who']=='教師') { 
   			list_class_score($REP_SETUP,0,$REP_SETUP['rep_sum'],$REP_SETUP['rep_avg'],$REP_SETUP['rep_rank']); //列出全班
    }
   	if ($REP_SETUP['open_read'] and $REP_SETUP['rep_classmates']==0 and $_SESSION['session_who']=='學生') { 
   			list_class_score_personal($REP_SETUP,0,$REP_SETUP['rep_sum'],$REP_SETUP['rep_avg'],$REP_SETUP['rep_rank']); //列出個人
    }
  
  }
  ?>	
   	
   	<font color=red size=2>※注意! 單一成績的加權值愈高，該成績所佔總平均比例愈高。</font>



</form>



