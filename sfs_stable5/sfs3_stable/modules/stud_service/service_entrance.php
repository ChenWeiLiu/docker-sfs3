<?php

// $Id: reward_one.php 7062 2013-01-08 15:37:05Z smallduh $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_dataarray.php";

sfs_check();
?>
<script type="text/javascript" src="./include/functions.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="./include/JSCal2-1.9/src/css/jscal2.css">

<?php


//秀出網頁
head("轉學生補登他校資料");

	//相關功能表
$tool_bar=&make_menu($school_menu_p);
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}

$C[0]="<font size=2 color=#c0c0c0>無</font>";
$C[1]="<font size=2 color=#0000FF><i>已認證</i></font>";


//讀取服務類別 $ITEM[0],$ITEM[1].....
$M_SETUP=get_module_setup('stud_service');
$ITEM=explode(",",$M_SETUP['item']);

//目前學期
$c_curr_seme=sprintf("%03d%d",curr_year(),curr_seme());

//取得所有學期
$seme_list=get_class_seme();

//取得資料庫中所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);


//目前選定學期
$work_year_seme=$_POST['work_year_seme'];
if ($work_year_seme=='') $work_year_seme = $c_curr_seme;
$move_year_seme = intval(substr($work_year_seme,0,-1)).substr($work_year_seme,-1,1);


  //已點選的學生 student_sn
  $selected_student=$_POST['selected_student'];


//增加一個服務記錄
if ($_POST['act']=='service_add') {
 $year_seme=$_POST['year_seme'];
 $service_date=$_POST['service_date'];
 $department=$_POST['department'];
 $item=$_POST['item'];
 $memo=$_POST['memo'];
 $update_sn=$_SESSION['session_tea_sn'];
 $sponsor=$_POST['sponsor'];
 $query="insert into stud_service (year_seme,service_date,department,item,memo,update_sn,input_sn,input_time,confirm,sponsor) values ('$year_seme','$service_date','$department','$item','$memo','$update_sn','$update_sn','".date('Y-m-d H:i:s')."','1','$sponsor')";

  //存入成功則再記錄學生
  if (mysql_query($query)) {
   	list($item_sn)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		 $minutes=$_POST['minutes'];
		 $studmemo="外校記錄";
		 $query="insert into stud_service_detail (student_sn,item_sn,minutes,studmemo) values ('$selected_student','$item_sn','$minutes','$studmemo')";	
     mysql_query($query);
   }
 $_POST['act']='';
} // end if service_add

//刪除一個服務記錄
if ($_POST['act']=='service_delete') {
 $query="delete from stud_service where sn='".$_POST['option1']."'";
 mysql_query($query);
 $query="delete from stud_service_detail where item_sn='".$_POST['option1']."'";
 mysql_query($query);
 $_POST['act']='';
} // end if service_delete


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
			//取得目前登錄者所在部門
				if ($department=='') {
					$sql_select = "select post_office from teacher_post where teacher_sn='{$_SESSION['session_tea_sn']}'";
					$recordSet = $CONN->Execute($sql_select);
					$department= $recordSet->fields["post_office"];
				}

    ?>
		  <font color='#800000'>※補登服務學習記錄</font>
  <table border="0" width="100%" style="border-collapse:collapse" bordercolor="#800000" cellpadding="3">
   <tr>
    <td>發生學期</td>
    <td>
  				<select name="year_seme">
					<?php
					 foreach ($class_seme_p as $tid=>$tname) {
	  					if (substr($tid,0,3)>$curr_year-3) {
			    ?>
      				<option value="<?php echo $tid;?>" <?php if ($_POST['year_seme']==$tid) echo "selected";?>><?php echo $tname;?></option>
   				<?php
      				} // end if
    				} // end foreach
		    ?>
				</select> 

    
    </td>
   </tr>
   <tr>
    <td width="70">服務日期</td><td>
    	<input type="text" name="service_date" id="service_date" size="10" value=""> (格式 : YYYY-MM-DD)<input type="button" id="date_select" value="...">
    	 		<script type="text/javascript">
		new Calendar({
  		    inputField: "service_date",
   		    dateFormat: "%Y-%m-%d",
    	    trigger: "date_select",
    			max: <?php echo date(Y-m-d);?>,
    	    bottomBar: true,
    	    weekNumbers: false,
    	    showTime: 24,
    	    onSelect: function() {this.hide();}
		    });
		</script>

    </td>
   </tr>
   <tr>
    <td width="70">登錄單位</td><td>
    <select name="department" size="1" onchange="document.myform.sponsor.value=this.options[ this.selectedIndex ].text ">
   	<?php
   	
    $sql_select = "select room_id,room_name from school_room where enable='1'";
	  $result = mysql_query($sql_select);
	
	while ($row=mysql_fetch_row($result)) {
		list($room_id,$room_name)=$row;
		?>
		<option value="<?php echo $room_id;?>" <?php if ($room_id==$department) echo " selected";?>><?php echo $room_name;?></option>
	<?php
	} // end while
	?>
	</select>
    </td>
   </tr>
   <tr>
    <td width="70">主辦單位</td>
    <td><input type="text" name="sponsor" value=""></td>
   </tr>
   <tr>
    <td width="70">服務類型</td><td>
    	    <select name="item" size="1">
						<?php 
						  $c=0;
						  foreach ($ITEM as $K) {
						   ?>
									<option value="<?php echo $K;?>" <?php if ($K==$item) { echo " selected"; $c=1;}?>><?php echo $K;?></option>
						   <?php
						  }
						    if ($c==0 and $item!='') {
						    	?>
						    	<option value="<?php echo $item;?>"  selected><?php echo $item;?></option>
						    	<?php
						    }

						?>
					</select>	
   	</td>
   </tr>
   <tr>
    <td width="70">服務內容</td><td><input type="text" name="memo" size="40"  value=""></td>
   </tr>
   <tr>
    <td width="70">服務時間</td><td><input type="text" name="minutes" size="5" value="">分鐘</td>
   </tr>

  </table>
   <input type="button" value="新增一筆記錄" onclick="check_insert();">
   	<table border="0">
	 	<tr bgcolor="#FFCCFF">
	 		  <td width="100" style="font-size:9pt" align="center">學期</td>
	  		<td width="70" style="font-size:9pt" align="center">日期</td>
	  		<td width="100" style="font-size:9pt" align="center">主辦單位</td>
	  		<td width="70" style="font-size:8pt" align="center">服務類型</td>
	  		<td width="200" style="font-size:9pt" align="center">服務內容</td>
	  		<td width="60" style="font-size:9pt" align="center">時數(分)</td>
	  		<td width="50" style="font-size:9pt" align="center">認證</td>
	  		<td width="70" style="font-size:9pt" align="center">操作</td>
	 	</tr>
	
   <?php
		//列出該生的所有記錄
   	$query="select a.*,b.student_sn,b.item_sn,b.minutes,b.studmemo from stud_service a,stud_service_detail b where a.sn=b.item_sn and b.student_sn='$selected_student' order by service_date";
    
    $res=mysql_query($query);
    while ($row=mysql_fetch_array($res,1)) {
    ?>
	 	<tr>
	 		  <td style="font-size:9pt" align="center"><?php echo sprintf("%d",substr($row['year_seme'],0,3));?>學年度第<?php echo substr($row['year_seme'],-1);?>學期</td>
	  		<td style="font-size:9pt" align="center"><?php echo $row['service_date'];?></td>
	  		<td style="font-size:9pt"><?php echo $row['sponsor'];?></td>
	  		<td style="font-size:8pt" align="center"><?php echo $row['item'];?></td>
	  		<td style="font-size:9pt"><?php echo $row['memo'];?></td>
	  		<td style="font-size:9pt" align="center"><?php echo $row['minutes'];?></td>
	  		<td style="font-size:9pt" align="center"><?php echo $C[$row['confirm']];?></td>
	  		<td style="font-size:9pt" align="center"><?php if ($row['studmemo']=='外校記錄') { ?> <input type="button" value="刪除" onclick="if (confirm('您確定要:\n刪除<?php echo $row['service_date'];?>的一筆資料?')) { document.myform.option1.value='<?php echo $row['item_sn'];?>';document.myform.act.value='service_delete';document.myform.submit(); } "> <?php }else{ echo "<font color=red size=2><i>校內記錄</i></font>";} ?></td>
	 	</tr>
    
    
    <?php   
    } // end while  
    ?>
    </table>
    <?php
	} // end if selected_student
    
    
 } // end if ($work_year_seme!='')
?>
	
	

</form>



<?php
foot();
?>
<Script language="JavaScript">
 function check_insert() {
 var save=1;
	if (document.myform.service_date.value=='') {
		alert('未輸入服務日期');
		save=0;
	}
	
	if (document.myform.sponsor.value=='') {
	 alert('未輸入主辦單位');
   save=0;
  }	

	if (document.myform.memo.value=='') {
		alert('未輸入服務內容');
		save=0;
	}

	if (document.myform.minutes.value=='') {
		alert('未輸入服務時間');
		save=0;
	}
	if (save==1) {
	 document.myform.act.value='service_add';
	 document.myform.submit();
	} else {
	 return false;
	}
  
 }
</Script>