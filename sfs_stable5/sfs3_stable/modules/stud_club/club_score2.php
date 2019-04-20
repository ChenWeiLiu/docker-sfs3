<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 成績補登");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();
//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);

//目前選定學期
$c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf('%03d%1d',$curr_year,$curr_seme);

//目前選定年級，100指未指定
$c_curr_class=($_POST['c_curr_class']!="")?$_POST['c_curr_class']:"100";

//取得學期社團設定
$SETUP=get_club_setup($c_curr_seme);


//預設為本學期社團
if ($CLUB['year_seme']=="") $CLUB['year_seme']=$c_curr_seme;

//按下儲存鈕後 , 利用 $_SESSION['club_sn'] 為儲存目標
    if ($_POST['mode']=="save") {
			foreach ($_POST['score'] as $student_sn=>$score) {	  		
	  		$query="update association set score='$score',description='".$_POST['description'][$student_sn]."',stud_post='".$_POST['stud_post'][$student_sn]."',update_sn='".$_SESSION['session_tea_sn']."' where student_sn='$student_sn' and club_sn='".$_SESSION['club_sn']."'";
	  		if (!mysql_query($query)) {
	   		 echo "Error! Query=$query";
	   		 exit();
	  	  }		
		}
  	$INFO="已於".date('Y-m-d H:i:s')."儲存成績資料";
  	$_POST['mode']="list";	
  	$_POST['club_sn']=$_SESSION['club_sn'];
		}

//檢查是否有選定社團
if ($_POST['club_sn']!="") $c_curr_class=get_club_class($_POST['club_sn']);

?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<!-- mode 參數 insert , update ,在 submit前進行 mode.value 值修改 -->
	<input type="hidden" name="mode" value="">
	<input type="hidden" name="club_sn" value="">
<table border="0" width="1000">
	<tr>
		<!--主功能表列(橫跨左右兩視窗), 提示 select 切換學期及其他功能 -->
		<td colspan="2" style="font-size:10pt;color:#FF0000">
		<select name="c_curr_seme" onchange="this.form.submit()">
			<?php
			while (list($tid,$tname)=each($class_seme_p)){
    	?>
    		<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   		<?php
    	} // end while
    	?>
    </select>
		

		<!--第一列左側, 功能表之後提示最後動作訊息 $INFO -->
		</td>
	</tr>
	  <!--左列視窗, 學期社團列表 -->
	  <td width="160" valign="top" style="color:#FF00FF;font-size:10pt">
	  	<select name='c_curr_class' onchange="document.myform.submit()">
	  		<option value="" style="color:#FF00FF">請選擇..</option>
	  	<?php
			    $class_year_array=get_class_year_array(sprintf('%d',substr($c_curr_seme,0,3)),sprintf('%d',substr($c_curr_seme,-1)));
                foreach ($class_year_array as $K=>$class_year_name) {
                	?>
                	<option value="<?php echo $K;?>" style="color:#FF00FF;font-size:10pt" <?php if ($c_curr_class==$K) echo "selected";?>><?php echo $school_kind_name[$K];?>級(<?php echo get_club_num($c_curr_seme,$K);?>)</option>
                	<?php
                }	
			?>
									<option value="100" style="color:#FF00FF;font-size:10pt" <?php if ($c_curr_class=='100') echo "selected";?>>跨年級(<?php echo get_club_num($c_curr_seme,100);?>)</option>
		</select>社團列表
			<?php
	  	//傳入參數 1001 , 1002 等, 年度學期
	  	list_club_select($c_curr_seme,$c_curr_class);
	  	?>
	  </td>
	  <!--左列視窗結尾 -->
	  <!--右列視窗, 主畫面 -->
		<td width="840" valign="top">
	  <?php
		
	  //顯示某社團 ================================================================
	  if ($_POST['mode']=="list") {
	  	if ($_POST['club_sn']!="") $club_base=get_club_base($_POST['club_sn']);
      $_SESSION['club_sn']=$_POST['club_sn']; //存入 SESSION 
			echo "<font color='#800000'>指導老師：".get_teacher_name($club_base['club_teacher'])."<br>";
			echo "社團名稱：".$club_base['club_name']."</font><br>";
			 ?>
<table border="1" style="border-collapse:collapse" bordercolor="#800000" width="100%">
 <tr bgcolor='#CCFFCC' style="font-size:10pt">
  <td align="center" style="color:#000000;font-size:10pt" width="40">序號</td>
 	<td align="center" style="color:#0000FF" width="70">班級</td>
 	<td align="center" style="color:#0000FF" width="50">座號</td>
 	<td align="center" style="color:#0000FF" width="80">姓名</td>
 	<td align="center" style="color:#0000FF" width="50">成績</td>
 	<td align="center" style="color:#0000FF" width="80">擔任職務</td>
 	<td align="center" style="color:#0000FF" width="280">學習描述</td>
 
  <td align="center" style="color:#0000FF">學生自我省思</td>
 	
 </tr>
 <?php
//取得學生成績
$query="select a.*,b.seme_class,b.seme_num,c.stud_name from association a,stud_seme b,stud_base c where a.seme_year_seme='$c_curr_seme' and a.club_sn='".$club_base['club_sn']."' and b.seme_year_seme='$c_curr_seme' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and (c.stud_study_cond=0 or c.stud_study_cond=5) order by seme_class,seme_num";
$res=mysql_query($query);

 $i=0;
  while ($row=mysql_fetch_array($res)) {
  	$i++;
  	$CLASS_name=$school_kind_name[substr($row['seme_class'],0,1)];
  	if ($row['score']=="0")  $row['score']='';
  ?>
  <tr style="font-size:10pt">
    <td align="center" style="color:#000000;font-size:10pt" width="50"><?php echo $i;?></td>
  	<td align="center"><?php echo $CLASS_name.sprintf('%d',substr($row['seme_class'],1,2))."班";?></td> 
  	<td align="center"><?php echo $row['seme_num'];?></td> 
  	<td align="center"><?php echo $row['stud_name'];?></td> 
  	<td align="center"><input type="text" name="score[<?php echo $row['student_sn'];?>]" value="<?php echo $row['score'];?>" size="3"></td> 
  	<td align="center"><input type="text" name="stud_post[<?php echo $row['student_sn'];?>]" value="<?php echo $row['stud_post'];?>" size="8"></td> 
  	<td ><textarea cols="36" rows="3" name="description[<?php echo $row['student_sn'];?>]"><?php echo $row['description'];?></textarea></td> 
   	<td style="font-size:8pt" width="150"><?php echo $row['stud_feedback']; ?></td>
 </tr>  
  <?php 
  } // end while
 ?>
</table>
<input type="button" value="儲存" onclick="document.myform.mode.value='save';document.myform.submit()">※註: 學生若未擔任職務，該欄可留空白。
<table width="100%" border="0">
	<tr><td style="color:#FF0000;font-size:10pt"><?php echo $INFO;?></td></tr>
</table>
<?php       
       
	  }

		?>
	  </td>
	  <!--右列視窗結尾 -->
	</tr>
</table>
</form>

