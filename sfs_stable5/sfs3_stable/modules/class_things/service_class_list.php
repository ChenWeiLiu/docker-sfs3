<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";
include_once "../stud_service/my_functions.php";

sfs_check();

//秀出網頁
head("班級事務");

//列出選單
print_menu($menu_p);

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期 
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

$teacher_sn=$_SESSION['session_tea_sn'];//取得登入老師的id
//找出任教班級
$class_name=teacher_sn_to_class_name($teacher_sn);

$S=substr($class_name[0],0,1)-6;

//取出本學期所有名冊
$query="select a.student_sn,b.stud_name,b.curr_class_num from stud_seme a,stud_base b where a.student_sn=b.student_sn and a.seme_year_seme='$c_curr_seme'";
$query.=" and a.seme_class='".$class_name[0]."'";
$query.=" order by seme_num";
$res=$CONN->Execute($query) or die("SQL錯誤:$query");

$res_sum=$res->RecordCount();


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
  while ($row_seme=$res_seme->FetchRow()) {
    $seme_year_seme=$row_seme['seme_year_seme'];
    $min=getService_allmin($student_sn,$seme_year_seme);
    $SERVICE[$student_sn][$seme_year_seme]=$min;	//記下學生某學期的總服務分鐘
    $SEME_REC[$seme_year_seme]+=$min;
  } // end while
} // end while

//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度
$class_seme_p=array_reverse($class_seme_p,1);	
while (list($tid,$tname)=each($class_seme_p)){
  //只列就學年
  if (substr($tid,0,3)>$curr_year-$S) {
  	$list_seme[]=$tid;    
  }
}
?>
<table border="1" style="border-collapse:collapse" bordercolor="#000000">
  <tr bgcolor="#FFCCFF">
    <td rowspan="2" align="center">班級</td>
    <td rowspan="2" align="center">座號</td>
    <td rowspan="2" align="center">姓名</td>
    <td colspan="<?php echo count($list_seme); ?>" align="center">學年-學期</td>
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
</table>