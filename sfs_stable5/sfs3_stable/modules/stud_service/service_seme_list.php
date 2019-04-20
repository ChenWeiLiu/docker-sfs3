<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//秀出網頁
head("學期資料總表");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期 
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取出本學期所有名冊
$query="select a.student_sn,b.stud_name,b.curr_class_num from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$c_curr_seme'";
if ($_POST['to_class']!="" and $_POST['to_class']!="A") $query.=" and a.seme_class like '".$_POST['to_class']."%'";
$query.=" order by seme_class,seme_num";
$res=$CONN->Execute($query) or die("SQL錯誤:$query");

$res_sum=$res->RecordCount();

//if ($_POST['act']=="") {
	$class_year_array=get_class_year_array();
	//print_r($class_year_array);
	//$school_kind_name['A']="全年";
	//$class_year_array['A']="A";
	?>
	<form name="myform" method="POST" action="<?php echo $_SERVER['php_self'];?>">
	  <input type="hidden" name="act" value="">
	  請選擇要統計的年級：
	  <select name="to_class" size="1" onchange="document.myform.submit()">
	   <option value="">---</option>
	  
	  <?php
	  foreach ($class_year_array as $k=>$v) {
	   ?>
	    <option value="<?php echo $k;?>"<?php if ($_POST['to_class']==$k) echo " selected";?>><?php echo $school_kind_name[$k]."級";?></option>
	   <?php
	  }
	  ?>
	  </select>
	  <?php
	   if ($_POST['to_class']!="") {
	  ?>
	   ，共計 <?php echo $res_sum;?> 位學生. <br>
	  <?php
	   if ($_POST['act']!="start") {
	  ?>
	   統計時間會有點久, 請耐心等候, 您確定要開始統計? 
	  <input type="button" value="開始" onclick="document.myform.act.value='start';document.myform.submit()"> <br>
	  <?php
	 		} else {
	 		 echo "<hr>";
	 		}
		 }
	  ?>
	</form>
	
	<?php
//}

if ($_POST['act']=="start") {

//取得所有學期資料, 每年有兩個學期
$seme_num=$_POST['to_class']-6;
$class_seme_p = get_class_seme(); //學年度
$class_seme_p=array_reverse($class_seme_p,1);	
reset($class_seme_p);
while (list($tid,$tname)=each($class_seme_p)){
  //只列有就讀的年
  if (substr($tid,0,3)>$curr_year-$seme_num) {
  	$list_seme[]=$tid;    
  }
}


$SEME_REC=array();

while ($row=$res->FetchRow()) {
  //echo $row['student_sn'].",".$row['stud_name'].",".$row['curr_class_num']."<br>";
  $student_sn=$row['student_sn'];
  $SERVICE[$student_sn]['class']=$school_kind_name[substr($row['curr_class_num'],0,1)].sprintf('%d',substr($row['curr_class_num'],1,2))."班";
  $SERVICE[$student_sn]['num']=sprintf("%d",substr($row['curr_class_num'],-2));
  $SERVICE[$student_sn]['stud_name']=$row['stud_name'];
  
  //以陣列記下學生的資料
  $sql="select seme_year_seme from stud_seme where student_sn='$student_sn' order by seme_year_seme";
  $res_seme=$CONN->execute($sql);
  
  //2013.12.17修正, 以該年級有就讀的學期去搜尋,以免轉學生讀不到外校資料
  foreach ($list_seme as $seme_year_seme) {
    $min=getService_allmin($student_sn,$seme_year_seme);
    $SERVICE[$student_sn][$seme_year_seme]=$min;	//記下學生某學期的總服務分鐘
    $SEME_REC[$seme_year_seme]+=$min;
  }
  
  /*
  while ($row_seme=$res_seme->FetchRow()) {
    $seme_year_seme=$row_seme['seme_year_seme'];
    $min=getService_allmin($student_sn,$seme_year_seme);
    $SERVICE[$student_sn][$seme_year_seme]=$min;	//記下學生某學期的總服務分鐘
    $SEME_REC[$seme_year_seme]+=$min;
  } // end while
  */
} // end while


?>
<table border="1" style="border-collapse:collapse" bordercolor="#000000">
  <tr bgcolor="#FFCCFF">
    <td rowspan="2" align="center">班級</td>
    <td rowspan="2" align="center">座號</td>
    <td rowspan="2" align="center">姓名</td>
    <td colspan="<?php echo $seme_num*2;?>" align="center">學年-學期</td>
    <td rowspan="2" align="center">總時數</td>
  </tr>
	<tr bgcolor="#FFCCFF">
    <?php
  		foreach ($list_seme as $seme) {
 		?>
   		<td align="center" width="50"><?php echo sprintf("%d-%d",substr($seme,0,3),substr($seme,-1));?></td>
 		<?php
  		}    
    ?>
	</tr>

<?php

//列出
foreach($SERVICE as $student_sn=>$v) {
	?>
  <tr>
    <td align="center"><?php echo $v['class'];?></td>
    <td align="center"><?php echo $v['num'];?></td>
    <td align="center"><?php echo $v['stud_name'];?></td>
	<?php
  $ALL=0;
  foreach ($list_seme as $seme) {
	 $ALL+=$v[$seme];
	?>
    <td align="center"><?php echo round($v[$seme]/60,2);?></td>
   <?php
  }
  ?>
  <td align="center"><?php echo round($ALL/60,2);?></td>
  </tr>
  <?php
}
?>
 <tr>
    <td align="center" colspan="3">時數總計</td>
<?php
    $ALL=0;
		foreach ($list_seme as $seme) {
		$ALL+=$SEME_REC[$seme];
		?>
		<td align="center"><?php echo round($SEME_REC[$seme]/60,2);?></td>
		<?php
		}
  ?>
  <td align="cente"><?php echo round($ALL/60,2);?></td>
  </tr>
  <?php
} // end if start
?>