<?php

// $Id: reward_one.php 7062 2013-01-08 15:37:05Z smallduh $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_dataarray.php";

sfs_check();


//秀出網頁
head("轉學生個人獎懲登記");

	//相關功能表
$tool_bar=&make_menu($student_menu_p);
echo $tool_bar;

//目前學期
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());

//取得所有學期
$seme_list=get_class_seme();

//目前選定學期
$work_year_seme=$_POST['work_year_seme'];
if ($work_year_seme=='') $work_year_seme = $c_curr_seme;
$move_year_seme = intval(substr($work_year_seme,0,-1)).substr($work_year_seme,-1,1);

//刪除一筆
if ($_POST['act']=='del_reward') {
  //取得 stud_id
  $query="select stud_id from stud_base where student_sn='".$_POST['selected_student']."'";
  $res=mysql_query($query);
  list($stud_id)=mysql_fetch_row($res);
  //取得獎懲學期
  $query="select reward_year_seme from reward where reward_id='".$_POST['option1']."'";
  $res=mysql_query($query);
  list($reward_year_seme)=mysql_fetch_row($res);
  
  $query="delete from reward where reward_id='".$_POST['option1']."'";
  mysql_query($query);
  
	cal_rew(substr($reward_year_seme,0,strlen($reward_year_seme)-1),substr($reward_year_seme,-1),$stud_id); 

} // end if del_reward

//新增一筆
if ($_POST['act']=='add_reward') {
	//處理日期
	if ($_POST['temp_reward_date']) {
		$dd=explode("-",$_POST['temp_reward_date']);
		if ($dd[0]<1911) $dd[0]+=1911;
		$_POST['temp_reward_date']=implode("-",$dd);
	}
	
  $query="select stud_id from stud_base where student_sn='".$_POST['selected_student']."'";
  $res=mysql_query($query);
  list($stud_id)=mysql_fetch_row($res);

  if ($stud_id=='') {
    echo "找不到對應的學號!";
    exit();
  }
  
		$sel_year=sprintf('%d',substr($_POST['reward_year_seme'],0,strlen($_POST['reward_year_seme'])-1));
		$sel_seme=substr($_POST['reward_year_seme'],-1);

 $reward_year_seme=$sel_year.$sel_seme; //發生時的學期 ,小於100, 前面不加0
 $reward_kind=$_POST['reward_kind']; //獎懲種類
 $reward_reason=$_POST['reward_reason']."(他校獎懲)"; //獎懲理由
 $reward_base=$_POST['reward_base']; //獎懲依據
 $reward_date=$_POST['temp_reward_date']; //獎懲日期
 
 		$student_sn=$_POST['selected_student'];
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=date("Y-m-j");
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$reward_div','$stud_id','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn')";
		
		$res=$CONN->Execute($query);
		$dep_id=$CONN->Insert_ID();
		$query="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
		$CONN->Execute($query);
		cal_rew($sel_year,$sel_seme,$stud_id); 

 $_POST['act']='';

}



//加到該生目前已補登獎懲紀錄中
if ($_POST['act_join']=='join' || $_POST['act_joinp']=='joinp') {

    list($stud_id)=mysql_fetch_row(mysql_query("select stud_id from stud_base where student_sn='".$_POST['selected_student']."'"));

    if ($stud_id=='') {
        echo "找不到對應的學號!";
        exit();
    }

if ($_POST['act_join']=='join')$joinx=$_POST['join'];
if ($_POST['act_joinp']=='joinp')$joinx=$_POST['joinp'];

for($k=0;$k<count($joinx);$k++)
{	
if ($_POST['act_join']=='join') $query="select * from reward where student_sn='$joinx[$k]' Limit 1";
if ($_POST['act_joinp']=='joinp') $query="select * from reward_exchange where sn='$joinx[$k]' Limit 1";
$res4=$CONN->Execute($query);
while (!$res4->EOF) 
{

$sel_year=reward_date_year($res4->fields['reward_date']);//由獎懲紀錄時間取出學年
$sel_seme=reward_date_seme($res4->fields['reward_date']);//由獎懲紀錄時間取出學期

$reward_year_seme=$sel_year.$sel_seme; //發生時的學期 ,小於100, 前面不加0
 
 if ($_POST['act_joinp']=='joinp')
 {
	 $reward_numbers=$res4->fields['reward_numbers']; //獎懲次數
	 if ($reward_numbers==1)$reward_numbers="一";
	 if ($reward_numbers==2)$reward_numbers="二";
	 if ($reward_numbers==3)$reward_numbers="三";
	 
	 $reward_kind=$res4->fields['reward_kind'].$reward_numbers."次";
 }	 
 else
 {
	$reward_kind=$res4->fields['reward_kind']; 
 }

 //將獎懲中文轉變成數字代碼, 正數為獎, 負數為懲
 foreach ($reward_arr as $i=>$v) {
     if ($v==$reward_kind) {
         $reward_kind=$i;
         break;
     }
 }

/*
 for($i=0;$i<count($reward_arr);$i++)
 {   
  if ($reward_kind==$reward_arr[$i]){
      $reward_kind=$i;
      break;
  }
 }
 */
 if ($_POST['act_join']=='join')$reward_reason=$res4->fields['reward_reason']; //獎懲理由 
 if ($_POST['act_joinp']=='joinp')$reward_reason=$res4->fields['reward_reason']."(他校獎懲)"; //獎懲理由
 $reward_base=$res4->fields['reward_base']; //獎懲依據
 $reward_date=$res4->fields['reward_date']; //獎懲日期
 
 		$student_sn=$_POST['selected_student'];
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=$reward_date;
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$reward_div','$stud_id','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn')";
		
		$res=$CONN->Execute($query);
		$dep_id=$CONN->Insert_ID();
		$query="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
		$CONN->Execute($query);
		cal_rew($sel_year,$sel_seme,$stud_id); 

 
 $res4->MoveNext();
}
	
}


 $_POST['act_join']="";
 $_POST['act_joinp']="";


}




	//獎懲選單
	$sel1 = new drop_select(); //選單類別
	$sel1->s_name = "reward_kind"; //選單名稱	
	$sel1->id = $reward_kind; //預設選項	
	$sel1->arr = $reward_arr; //內容陣列		
	$sel1->top_option = "-- 選擇獎懲 --";
	$reward_select=$sel1->get_select();

	// 日期函式
	if ($reward_date=="") $reward_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	$seldate=new date_class("myform");
	$seldate->demo="";

	//獎懲日期
	$date_input=$seldate->date_add("reward_date",$reward_date);

  //已點選的學生 student_sn
  $selected_student=$_POST['selected_student'];

  if ($reward_base=='') $reward_base='學生獎懲輔導實施辦法';

?>

<form method="post" name="myform" act="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">	
		※選擇轉入的學期：
	<select name="work_year_seme" onchange="document.myform.submit();">
  <?php
		foreach($seme_list as $key=>$value) {
	?>		
	 <option value="<?php echo $key;?>" <?php if ($key==$work_year_seme) echo " selected";?>><?php echo $value;?></option>
	 <?php
	 }
	 ?>
	</select><br>
	
<?php
  //針對學期列出學生
  if ($work_year_seme!='') {
  	$check_student=0;
  	//取得該學期轉入學生清單
		$sql="SELECT a.*,b.stud_id,b.stud_name,b.stud_sex,b.stud_study_year FROM stud_move a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.move_kind in (2,3,14) AND move_year_seme='$move_year_seme' ORDER BY move_date DESC";
		$recordSet=$CONN->Execute($sql) or user_error("讀取stud_move、stud_base資料失敗！<br>$sql",256);
		$col=3; //設定每一列顯示幾人
		$studentdata="※選擇欲補登的學生：<table>";
		while(!$recordSet->EOF) {
			$currentrow=$recordSet->currentrow()+1;
			if($currentrow % $col==1) $studentdata.="<tr>";
			$student_sn=$recordSet->fields['student_sn'];
			$stud_id=$recordSet->fields['stud_id'];
			$stud_name=$recordSet->fields['stud_name'];
			$stud_move_date=$recordSet->fields['move_date'];
			if($recordSet->fields['stud_sex']=='1') $color='#CCFFCC'; else  $color='#FFCCCC';
			if($student_sn==$selected_student) {
				$color='#FFFFAA';
				$stud_study_year=$recordSet->fields['stud_study_year'];
				$selected_student_id=$stud_id;
			}
	    
	    if ($student_sn==$selected_student) {
			  $student_radio="<input type='radio' value='$student_sn' name='selected_student' checked onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
			  $check_student=1;
			} else {
			  $student_radio="<input type='radio' value='$student_sn' name='selected_student' onclick='document.myform.submit()'>( $student_sn - $stud_id ) $stud_name - $stud_move_date";	
			}
			$studentdata.="<td bgcolor='$color' align='center'> $student_radio </td>";

			if( $currentrow % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
			$recordSet->movenext();
	  } // end while
			$studentdata.='</table><hr>';
		
    echo $studentdata;
    
    //若已點選學生, 列出該生的資料及新增表單
    if ($check_student) {
    ?>
    <table>
    	<tr class='title_sbody2'>
				<td>獎懲事實之學年度/學期</td>
				<td align='left' bgcolor='white'>
					<?php
				 $year_seme_select = "<select name='reward_year_seme'>\n";
				foreach($seme_list as $k=>$v) {
				if ($reward_year_seme==$k)
	      		$year_seme_select.="<option value='$k' selected>$v</option>\n";
	      	else
	      		$year_seme_select.="<option value='$k'>$v</option>\n";
				}
				$year_seme_select.= "</select>"; 
				
				echo $year_seme_select;

					?>
				</td>
			</tr>

    	<tr class='title_sbody2'>
				<td>獎懲類別</td>
				<td align='left' bgcolor='white'><?php echo $reward_select;?></td>
			</tr>
			<tr class='title_sbody2'>
				<td>獎懲事由</td>
				<td align='left' bgcolor='white'><input type='text' name='reward_reason' value='<?php echo $reward_reason;?>' size='30' maxlength='30'></td>
			</tr>
			<tr class='title_sbody2'>
				<td>獎懲依據</td>
				<td align='left' bgcolor='white'><input type='text' name='reward_base' value='<?php echo $reward_base;?>' size='30' maxlength='30'></td>
			</tr>
			<tr class='title_sbody2'>
				<td>獎懲日期</td>
				<td align='left' bgcolor='white'><?php echo $date_input;?>
			</tr>
	</table>
	<input type="button" value="新增一筆" onclick="check_data();"><br><font size=2 color=red>※注意：<br>1.請務必確認獎懲事實發生的時間，與該生在學之學期是否吻合，以免造成獎懲總表統計錯誤的問題。<br>2.不慎輸入錯誤，將該筆記錄刪除重新輸入即可。<br>3.系統將自動於獎懲事由加註「(他校獎懲)」字樣。</font>
  <hr>
    <?php
      //列出該生所有明細
      
      //以學生流水號處理資料

		
		$sn=$_POST['selected_student'];
		
		//列出
		?>
<table bgcolor=yellow width=100%><tr><td align="center">該生目前已補登獎懲紀錄</td></tr></table>		
<center><table width=100%>
<tr class="title_sbody2">
<td align="center">學年</td>
<td align="center">學期</td>
<td align="center">獎懲事由</td>
<td align="center" width="70">獎懲類別</td>
<td align="center">獎懲依據</td>
<td align="center" width="80">獎懲生效日期</td>
<td align="center" width="80">銷過日期</td>
</tr>
<?php
$query="select * from reward where student_sn='$sn' order by reward_div,reward_date desc";
$res=$CONN->Execute($query);
while (!$res->EOF) 
{
$sel_year=substr($res->fields['reward_year_seme'],0,strlen($res->fields['reward_year_seme'])-1);
$sel_seme=substr($res->fields['reward_year_seme'],-1);
 
?>
		<tr class="title_sbody1">
				<td align="center"><?php echo $sel_year;?></td>
				<td align="center"><?php echo $sel_seme;?></td>
				<td align="left"><?php echo $res->fields['reward_reason'];?></td>
				<td align="center"><?php echo $reward_arr[$res->fields['reward_kind']];?></td>
				<td align="left"><?php echo $res->fields['reward_base'];?></td>
				<td align="center"><?php echo $res->fields['reward_date'];?></td>
				<td align="center"><?php if ($res->fields['reward_kind']>0) { echo "---"; } else { if ($res->fields['reward_cancel_date']=="0000-00-00") { echo "未銷過"; } else { echo $res->fields['reward_cancel_date']; } } ?> </td>
				<td align="left">
				  <input type="image" src="images/del.png"  alt="刪除" onclick="if (confirm('您碓定要:\n刪除一筆<?php echo $res->fields['reward_date'];?>的記錄?')) { document.myform.option1.value='<?php echo $res->fields['reward_id'];?>'; document.myform.act.value='del_reward';document.myform.submit(); }">
				</td>
		</tr>
		<?php
   $res->MoveNext();
} // end while
		?>
</table></center><p>

<?php

echo "<table border=1 width=100%><tr>";
echo "<td valign=top width=50%>";	
echo "<table bgcolor=yellow width=100%><tr><td align='center'>身分證號相同的學生獎懲紀錄</td></tr></table>";		
	
//由流水號和身分證比對是否該生由該校轉出又轉回
$res0=$CONN->Execute("select DISTINCT b.student_sn as ssn ,b.stud_person_id as spid ,b.stud_name as sname from stud_base a,stud_base b where a.student_sn='$sn' and a.stud_person_id=b.stud_person_id and a.student_sn<>b.student_sn");
while(!$res0->EOF) 
{
$ssn=$res0->fields['ssn'];
$spid=$res0->fields['spid'];
$sname=$res0->fields['sname'];

echo "序號:".$ssn."身分證:".$spid." 姓名:".$sname."<br>"; 

?>
<table width=100%>
<tr class="title_sbody2">
<td align="center">學年</td>
<td align="center">學期</td>
<td align="center">獎懲事由</td>
<td align="center" width="70">獎懲類別</td>
<td align="center">獎懲依據</td>
<td align="center" width="80">獎懲生效日期</td>
<td align="center" width="80">銷過日期</td>

</tr>
<?php
 
$query="select * from reward where student_sn='$ssn' order by reward_div,reward_date desc";
$res2=$CONN->Execute($query);
while (!$res2->EOF) 
{
$sel_year=reward_date_year($res2->fields['reward_date']);//由獎懲紀錄時間取出學年
$sel_seme=reward_date_seme($res2->fields['reward_date']);//由獎懲紀錄時間取出學期

?>
		<tr class="title_sbody1">
				<td align="center"><?php echo $sel_year;?></td>
				<td align="center"><?php echo $sel_seme;?></td>
				<td align="left"><?php echo $res2->fields['reward_reason'];?></td>
				<td align="center"><?php echo $reward_arr[$res2->fields['reward_kind']];?></td>
				<td align="left"><?php echo $res2->fields['reward_base'];?></td>
				<td align="center"><?php echo $res2->fields['reward_date'];?></td>
				<td align="center"><?php if ($res2->fields['reward_kind']>0) { echo "---"; } else { if ($res2->fields['reward_cancel_date']=="0000-00-00") { echo "未銷過"; } else { echo $res2->fields['reward_cancel_date']; } } ?> </td>
				<td align="left">
                  <input type="checkbox" name="join[]" value="<?php echo $res2->fields['student_sn'];?>">				
				</td>
		</tr>
		<?php
   $res2->MoveNext();
} // end while
		?>
</table>



<?php
   
	$res0->MoveNext();
}
?>
<input type="hidden" name="act_join" value="<?php echo $_POST['act_join'];?>">
<center><input type="submit" value="加到該生目前獎懲紀錄中" onclick="check_join();"></center>

<?php
 echo "</td>";
 
 echo "<td valign=top width=50%><table bgcolor=yellow width=100%><tr><td align='center'>XML匯入的期中獎懲紀錄</td></tr></table>";
 ?>
 
<table width=100%>
<tr class="title_sbody2">
<td align="center">學年</td>
<td align="center">學期</td>
<td align="center">獎懲事由</td>
<td align="center" width="70">獎懲類別</td>
<td align="center">獎懲次數</td>
<td align="center">獎懲依據</td>
<td align="center" width="80">獎懲生效日期</td>
<td align="center" width="80">銷過日期</td>
<td align="left" width="80"><input type="checkbox" value='全選' onclick='chkall("myform",this)'>全選</td>

</tr>
<?php
 
$query="select * from reward_exchange where student_sn='$sn' order by reward_date desc";
$res3=$CONN->Execute($query);
while (!$res3->EOF) 
{
	
$sel_year=reward_date_year($res3->fields['reward_date']);//由獎懲紀錄時間取出學年
$sel_seme=reward_date_seme($res3->fields['reward_date']);//由獎懲紀錄時間取出學期


?>
		<tr class="title_sbody1">
				<td align="center"><?php echo $sel_year;?></td>
				<td align="center"><?php echo $sel_seme;?></td>
				<td align="left"><?php echo $res3->fields['reward_reason'];?></td>
				<td align="center"><?php echo $res3->fields['reward_kind'];?></td>
				<td align="center"><?php echo $res3->fields['reward_numbers'];?></td>
				<td align="left"><?php echo $res3->fields['reward_base'];?></td>
				<td align="center"><?php echo $res3->fields['reward_date'];?></td>
				<td align="center"><?php if ($res3->fields['reward_kind']>0) { echo "---"; } else { if ($res3->fields['reward_cancel_date']=="0000-00-00") { echo "未銷過"; } else { echo $res3->fields['reward_cancel_date']; } } ?> </td>
				<td align="left">
				  <input type="checkbox" name="joinp[]" value="<?php echo $res3->fields['sn'];?>">
				</td>
		</tr>
		<?php
        $res3->MoveNext();
} // end while
		?>
</table>
<input type="hidden" name="act_joinp" value="<?php echo $_POST['act_joinp'];?>">
<center><input type="submit" value="加到該生目前獎懲紀錄中" onclick="check_joinp();"></center>

 <?php
 echo "</td></tr></table>";	
  
	} // end if selected_student
    
    
 } // end if
?>
	
	

</form>



<?php
foot();

//由日期取得學年
function reward_date_year($date="",$sp="-"){
	global $SFS_SEME1,$CONN;
	$query = "select year from school_day where  day<='$date' and day_kind='start' order by day desc limit 1";
	$res = $CONN->Execute($query);
	return $res->fields[year];
}
//由日期取得學期
function reward_date_seme($date="",$sp="-"){
	global $SFS_SEME1,$CONN;
	$query = "select seme from school_day where  day<='$date' and day_kind='start' order by day desc limit 1";
	$res = $CONN->Execute($query);
	return $res->fields[seme];
}


?>
<Script Language="JavaScript">
function check_data() {
 if (document.myform.reward_kind.value=='') { 
 	 alert('未選擇獎懲類別!');
 	 return false;
 	}
 if (document.myform.reward_reason.value=='') { 
 	 alert('未輸入獎懲事由!');
 	 document.myform.reward_reason.focus(); 	 
 	 return false;
 	}
 if (document.myform.reward_base.value=='') { 
 	 alert('未輸入獎懲依據!');
 	 document.myform.reward_base.focus(); 	 
 	 return false;
 	}
 if (document.myform.temp_reward_date.value=='') { 
 	 alert('未輸入獎懲日期!');
 	 document.myform.temp_reward_date.focus(); 	 
 	 return false;
 	}
  document.myform.act.value='add_reward';
  document.myform.submit();
}

function check_join() {
	
if (confirm('您碓定要加到目前已補登獎懲紀錄中?')) 
{ 
  document.myform.act_join.value='join';
  
}
else
{
  document.myform.act_join.value='';

}
document.myform.submit();
}

function check_joinp() {

if (confirm('您碓定要加到目前已補登獎懲紀錄中?')) 
{ 
  document.myform.act_joinp.value='joinp';
  
}
else
{
	document.myform.act_joinp.value='';
}

  document.myform.submit();
}

function chkall(input1,input2)
{
    var objForm = document.forms[input1];
    var objLen = objForm.length;
    for (var iCount = 0; iCount < objLen; iCount++)
    {
        if (input2.checked == true)
        {
            if (objForm.elements[iCount].type == "checkbox")
            {
                objForm.elements[iCount].checked = true;
            }
        }
        else
        {
            if (objForm.elements[iCount].type == "checkbox")
            {
                objForm.elements[iCount].checked = false;
            }
        }
    }
}

</Script>



