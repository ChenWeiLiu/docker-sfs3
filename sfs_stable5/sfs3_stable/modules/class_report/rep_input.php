<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//目前學期
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());


//POST後的動作
//儲存一份成績
if ($_POST['act']=='insert') {
	//成績單設定值$_POST['the_report'];	
	$report_sn=$_POST['the_report'];  					//成績單sn

	//存入考試成績設定
	$subject=$_POST['subject'];
	$test_date=$_POST['test_date'];
	$memo=$_POST['memo'];
	$real_sum=1;
	$update_sn=$_SESSION['session_tea_sn'];
	$rate=$_POST['rate'];
	$sql="insert into `class_report_test` set report_sn='$report_sn',subject='$subject',test_date='$test_date',real_sum='$real_sum',memo='$memo',update_sn='$update_sn',rate='$rate'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	
	//取回自動新增的 sn  值, 以登錄個別學生分數的對應
	$res=$CONN->Execute("SELECT LAST_INSERT_ID()");
	$test_sn=$res->fields[0];	
	
	//存入所有登錄的成績 $_POST['score'];
	foreach ($_POST['score'] as $student_sn=>$score) {
		if ($score!="") {
		 $sql="insert into `class_report_score` set test_sn='$test_sn',student_sn='$student_sn',score='$score',update_sn='$update_sn'";
		 $res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	  }
	}
	
	//存入成績單更動記錄
	$last_edit_sn=($_SESSION['session_who']=='教師')?"t".$_SESSION['session_tea_sn']:"s".$_SESSION['session_tea_sn']; 	// 最後更動成績的sn 
	$last_edit_time=date("Y-m-d H:i:s"); 				// 最後更動成績的時間
	$sql="update `class_report_setup` set last_edit_sn='$last_edit_sn',last_edit_time='$last_edit_time' where sn='$report_sn'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");

	$_POST['act']='';

} // end if insert

//更新一份成績
if ($_POST['act']=='update') {
	//成績單設定值$_POST['the_report'];	
	$report_sn=$_POST['the_report'];  					//成績單sn

	//存入考試成績設定
	$subject=$_POST['subject'];
	$test_date=$_POST['test_date'];
	$memo=$_POST['memo'];
	$real_sum=1;
	$rate=$_POST['rate'];
	$update_sn=$_SESSION['session_tea_sn'];
	//教師才能更改 rate (加權)
	$sql=($_SESSION['session_who']=='教師')?"update `class_report_test` set report_sn='$report_sn',subject='$subject',test_date='$test_date',real_sum='$real_sum',memo='$memo',update_sn='$update_sn',rate='$rate' where sn='{$_POST['option1']}'":"update `class_report_test` set report_sn='$report_sn',subject='$subject',test_date='$test_date',real_sum='$real_sum',memo='$memo',update_sn='$update_sn' where sn='{$_POST['option1']}'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
	
	//存入所有登錄的成績 $_POST['score'];
	foreach ($_POST['score'] as $student_sn=>$score) {
		$sql="select sn from `class_report_score` where test_sn='{$_POST['option1']}' and student_sn='$student_sn'";
		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
		if ($res) {
		  $sn=$res->fields[0];
			if ($sn>0) {
		  	$sql="update `class_report_score` set score='$score',update_sn='$update_sn' where sn='$sn'";
				$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
			} else {
		  //新的資料
				if ($score!="") {
		 			$sql="insert into `class_report_score` set test_sn='{$_POST['option1']}',student_sn='$student_sn',score='$score',update_sn='$update_sn'";
					$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
				}
		 	} // end if $>0
		} // end if $res
	}  // end foreach
	
	//存入成績單更動記錄
	$last_edit_sn=($_SESSION['session_who']=='教師')?"t".$_SESSION['session_tea_sn']:"s".$_SESSION['session_tea_sn']; 	// 最後更動成績的sn 
	$last_edit_time=date("Y-m-d H:i:s"); 				// 最後更動成績的時間
	$sql="update `class_report_setup` set last_edit_sn='$last_edit_sn',last_edit_time='$last_edit_time' where sn='$report_sn'";
	$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");

	$_POST['act']='';

} // end if update

//刪除一份成績
if ($_POST['act']=='DeleteOne') {
  //成績單設定值$_POST['the_report'];	
	$report_sn=$_POST['the_report'];  					//成績單sn
	
	//刪除所有分數
		$sql="delete from `class_report_score` where test_sn='{$_POST['option1']}'";
		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");

	//刪除考試設定
		$sql="delete from `class_report_test` where sn='{$_POST['option1']}' and report_sn='$report_sn'";
		$res=$CONN->Execute($sql) or die("SQL錯誤:$sql");
		
		$_POST['act']='';
}


switch ($_SESSION['session_who']) {
	//如果是老師, 取得所有學期
	case '教師':
		$select_seme = get_class_seme(); //學年度
		//取得目前學期的所有可讀取的成績單
		$select_report=get_report("input",$c_curr_seme);
	break;

	//如果是學生, 取得就學學期
	case '學生':
		$sql="select seme_class from stud_seme where seme_year_seme='$c_curr_seme' and student_sn='{$_SESSION['session_tea_sn']}'";
		$res=$CONN->execute($sql) or die("SQL錯誤:$sql");
		$class_num=$res->fields[0];
		//該班級已在學的總學期數
		$select_seme=get_class_seme_select($class_num);
		//取得目前學期的所有可讀取的成績單
		$select_report=get_report("input",$c_curr_seme,$class_num);
	break;
} // end switch


//成績單匯出
if ($_POST['act']=='output') {
	  $REP_SETUP=get_report_setup($_POST['the_report']);
    $filename =  $REP_SETUP['title'].".xls"; 	
    header("Content-disposition: filename=$filename");
    header("Content-type: application/octetstream");	  
    //header("Pragma: no-cache");
	  				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	  header("Expires: 0"); 

 list_class_score($REP_SETUP,0,1,1,1);
 exit();
}


//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

$SS=0;  //記下共幾個欄位, 用於檢驗輸入

moveit2("myform");

?>
<script type="text/javascript" src="../../javascripts/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="../../javascripts/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="../../javascripts/JSCal2-1.9/src/css/jscal2.css">
<style type="text/css">
 .bg_0 { background-color:#FFFFFF;font-size:9pt  }
 .bg_Over { background-color:#CCFFCC;font-size:10pt;color:#FF0000  }
</style>

<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
	要輸入的成績單
	<select size="1" name="the_report" onchange="document.myform.option1.value='';document.myform.submit()">
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
	//若有選定成績單, 列出名單
	if ($_POST['the_report']!="") {
	  $REP_SETUP=get_report_setup($_POST['the_report']);
   //列出成績
   if ($_POST['act']=='') {
   	?>
   	<input type="button" value="新增一筆成績" onclick="document.myform.act.value='InsertOne';document.myform.submit()"<?php if ($REP_SETUP['locked']) echo " disabled";?>>
   	<?php
   	if ($REP_SETUP['locked']) echo "<br><font color=red size=2><i>本成績已匯至教務處, 無法再進行編輯!!</i></font>";
   	if ($_SESSION['session_who']=='教師') {
   		?>
   		<input type="button" value="匯出成績" onclick="document.myform.act.value='output';document.myform.submit()">
   		<?php
   		list_class_score($REP_SETUP,1,1,1,1);
  	} else {
   		list_class_score($REP_SETUP,1,$REP_SETUP['rep_sum'],$REP_SETUP['rep_avg'],$REP_SETUP['rep_rank']);
  	}
   	?>
   	<input type="button" value="新增一筆成績" onclick="document.myform.act.value='InsertOne';document.myform.submit()"<?php if ($REP_SETUP['locked']) echo " disabled";?>>
   	<BR>
   	<font color=red size=2>※注意! 單一成績的加權值愈高，該成績所佔總平均比例愈高。</font>
   	<?php
   }
   //新增成績
   if ($_POST['act']=='InsertOne') {
   	//傳入成績單設定,科目設定,學生成績
   	$TEST_SETUP['test_date']=date("Y-m-d");
   	$TEST_SETUP['rate']=1;
   	form_class_score($REP_SETUP,$TEST_SETUP,$SCORE); 
   	?>
   		  <input type="button" value="儲存資料" onclick="check_save('insert')">
   <?php
   }
   //修改某筆成績
   if ($_POST['act']=='edit') {
    $TEST_SETUP=get_report_test($_POST['option1']);
   	$SCORE=get_report_score($_POST['option1']);
  	//列出表單
    $SS=form_class_score($REP_SETUP,$TEST_SETUP,$SCORE);  
   	?>
   		  <input type="button" value="儲存資料" onclick="check_save('update')">
   <?php
 
   }
	

	} // end if ($_POST['the_report'])
	?>

</form>
<Script Language="JavaScript">
	
	var SS=<?php echo $SS;?>;
	var ss=0;
	
 function check_save(ACT) {
   	var ok=1;
 	if (document.myform.test_date.value=='') {
 		ok=0;
 		alert('請輸入考試日期');
 		document.myform.test_date.focus();
 		return false;
 	}
 	if (document.myform.subject.value=='') {
 		ok=0;
 		alert('請輸入考試科目');
 		document.myform.subject.focus();
 		return false;
 	}
 	
 	if (ok==1) {
 		document.myform.act.value=ACT;
 		document.myform.submit();
 	}
 
 }
 
 //科目選擇, 滑鼠停在上面時
 function OverLine(w) {
   document.getElementById(w).className = 'bg_over';  
 }
 
 //科目選擇, 滑鼠移開時
 function OutLine(w) {
   document.getElementById(w).className = 'bg_0';
 } 
 
 
 //輸入成績, 取得焦點時
 function set_ower(thetext,ower) {
	ss=ower;
	thetext.style.background = '#FFFF00';
	thetext.select();
	return true;
}

//輸入成績, 離開焦點時
function unset_ower(thetext) {
	if(thetext.value>100 && ss>2){ thetext.style.background = '#FF0000'; alert("輸入成績高於100分");}
	else if(thetext.value<0 && ss>2){ thetext.style.background = '#AA5555'; alert("輸入成績為負數"); }
	else if(thetext.value<60 && ss>2){ thetext.style.background = '#FFCCCC'; }
	else { thetext.style.background = '#FFFFFF'; }
	return true;
}
 
// handle keyboard events
if (navigator.appName == "Mozilla")
	document.addEventListener("keyup",keypress,true);
else if (navigator.appName == "Netscape")
	document.captureEvents(Event.KEYPRESS);
if (navigator.appName != "Mozilla")
	document.onkeypress=keypress;

function keypress(e) {
	if (navigator.appName == "Microsoft Internet Explorer")
		tmp = window.event.keyCode;
	else if (navigator.appName == "Navigator")
		tmp = e.which;
	else if (navigator.appName == "Navigator" || navigator.appName == "Netscape")
		tmp = e.keyCode;
		
	if (tmp == 13){
		var GG='SS_'+ss;
	  var TT = document.getElementById(GG).value;
	  var tt = parseFloat(document.getElementById(GG).value);
		if (isNaN(tt) && ss>2 && TT!=''){
			alert('錯誤的分數!');
			document.getElementById(GG).value ='';
			return false;
		} else {
			if (ss<SS) ss++;	
			GG='SS_'+ss;
			document.getElementById(GG).focus();
		} 
	} // end if tmp==13
		return true;
}
</script>



