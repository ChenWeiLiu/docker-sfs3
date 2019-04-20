<?php
// $Id: quick_input.php 8368 2015-03-26 02:29:31Z smallduh $
include "config.php";

//顯示欄數
$col_num = 3;
$signBtn = "登錄成績";

//使用者認證
sfs_check();
$session_tea_sn =  $_SESSION['session_tea_sn'] ;

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

//存入資料庫 
if($_POST[edit]<>'') {
	$temp_sn_arr = explode(",",$_POST[sn_hidden]);
	$up_date = mysql_date();
	while(list($id,$sn) = each($temp_sn_arr)) {
		if($sn) {
			$temp_sn = $_POST["t_$sn"];
			//如果是測驗年月
			if($t==7){
				$test_date=explode('-',$temp_sn);
				$test_y=$test_date[0];
				$test_m=$test_date[1];
				$query = "update fitness_data set test_y='$test_y',test_m='$test_m',teacher_sn='$session_tea_sn',up_date='$up_date' where student_sn='$sn' and c_curr_seme='$c_curr_seme'";
			} else {
				if($temp_sn=="")
					$query = "update fitness_data set $file[$t]=NULL,teacher_sn='$session_tea_sn',up_date='$up_date' where student_sn='$sn' and c_curr_seme='$c_curr_seme'";
				else {
					//如果是心肺適能 檢查是否以 a.b 的格式輸入  如果是的話  進行轉分轉秒的動作
					if($t==5){
						$tran=strpos($temp_sn,'.');
						if($tran){
							$temp_arr=explode('.',$temp_sn);
							$temp_sn=$temp_arr[0]*60+$temp_arr[1];			
						}	
					}
					if($t!=6) $temp_sn=floor($temp_sn);   //無條件捨去取整數, 以免多進一個百分比
					$query = "update fitness_data set $file[$t]='$temp_sn' ,teacher_sn='$session_tea_sn',up_date='$up_date' where student_sn='$sn' and c_curr_seme='$c_curr_seme'";
				}
			}	
			$CONN->Execute($query) or die($query);
		}
	}
	echo "<html><body>
	<script LANGUAGE=\"JavaScript\">\n
	window.opener.history.go(0);\n
        window.close();
	</script>
	</body>
	</html>";
	exit;
}
?>
<html>
<meta http-equiv="Content-Type" content="text/html; Charset=Big5">
<head>
<title>成績輸入</title>

</head>
<body>

<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<?php
$sql = "select a.student_sn,a.stud_name,a.stud_sex,b.seme_num,c.* from stud_base a,stud_seme b,fitness_data c where a.student_sn=b.student_sn and c.student_sn=a.student_sn and a.stud_study_cond in ($in_study) and b.seme_year_seme='$c_curr_seme' and c.c_curr_seme='$c_curr_seme' and b.seme_class='$class_num' order by b.seme_num"; 
$result=$CONN->Execute($sql) or trigger_error("SQL語法錯誤 ", E_USER_ERROR);
echo "$class_base_p[$class_num] $test[$t] 輸入";
if($t==5) echo "<font size=2 color='red'>　( 可以 分.秒 進行輸入，程式會自動計算 )</font>";
echo "<table border=1>\n";
$ii =0;
while ($row = $result->FetchRow()) {
	$num=$row["seme_num"];
	$sn= $row["student_sn"];
	$stud_name = $row["stud_name"];
	if($t==7) $test_score=$row["test_y"].'-'.$row["test_m"]; else $test_score = $row[$file[$t]];

	if($ii % $col_num == 0)
		echo "<tr>";
		echo "<td bgcolor=#e3f9ef>$num</td>";
		echo "<td bgcolor=#ffcbfb>$stud_name</td>";
		echo "<td><input type=\"text\" name=\"t_$sn\" size=10 maxlength=50 value=\"$test_score\" onFocus=\"set_ower(this,$ii)\" onBlur=\"unset_ower(this)\"  ></td>";
		
	if($ii++ % $col_num == ($col_num-1))
		echo "</tr>\n";
	$sn_hidden .="$sn,";	
}
echo "</table>";
$edit="edit";
?>
<input type="button" name="do_key" value="<?php echo $signBtn ?>" onClick="document.myform.submit()">
&nbsp;&nbsp;<input type="button" name="go_away" value="放棄" onClick="check_change()">
&nbsp;&nbsp;<input type="button" name="reset_allBtn" value="清空" onClick="reset_all()">
<input type="hidden" name="class_num" value="<?php echo $class_num ?>">
<input type="hidden" name="c_curr_seme" value="<?php echo $c_curr_seme ?>">
<input type="hidden" name="edit" value="<?php echo $edit ?>">
<input type="hidden" name="sn_hidden" value="<?php echo $sn_hidden ?>">
<input type="hidden" name="t" value="<?php echo $t ?>">
</form>

<script >
var ss=0;
var is_change = false;
function set_default(){
document.myform.elements[ss].focus();
}

function check_change(){
if(is_change){
	if (confirm('您已經更改資料是否要離開 ?'))
		window.close();
}
else
	window.close();
}


function set_ower(thetext,ower) {
ss=ower;
thetext.style.background = '#FFFF00';
//thetext.select();
return true;
}

function unset_ower(thetext) {
thetext.style.background = '#FFFFFF';
return true;
}

function reset_all() {
	for (var i=0;i<document.myform.elements.length;i++)
	 {
	    var e = document.myform.elements[i];
	    if (e.type == 'text')
        	       e.value = '';
	}
  document.myform.elements[0].focus();
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
   else if (navigator.appName == "Navigator"||navigator.appName == "Netscape")
	tmp = e.which;
   else if (navigator.appName == "Mozilla")
       tmp = e.keyCode;
  if( document.myform.elements[ss].type != 'text')
		return true;
        else if (tmp == 13){ 
		var tt = parseFloat(document.myform.elements[ss].value);
		
		if (isNaN(tt) || tt >1000 || tt < 0 ){			
			alert('錯誤的分數!');
			document.myform.elements[ss].value ='';
			return false;
		}
		else {			
			ss++;
			document.myform.elements[ss].focus();
			is_change = true;
			return true;
		}
	}
        else    return true;
}
</script>
</body>
</html>
