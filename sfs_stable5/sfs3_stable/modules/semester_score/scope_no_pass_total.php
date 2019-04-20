<?php
//$Id:
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

//全校有幾個年級
$all_years=($IS_JHORES==0)?6:3;
$curr_year=curr_year();

//取得所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);
$tool_bar=&make_menu($menu_p);

//目前選定學期
if (isset($_POST['c_curr_seme'])) {
	$seme_year_seme=$_POST['c_curr_seme'];
	$sel_year=substr($seme_year_seme,0,3);
	$sel_seme=substr($seme_year_seme,-1);

//取得學校日期設定
$db_date=curr_year_seme_day($sel_year,$sel_seme);  //$db_date['start'] , $db_date['end'] , $db_date['st_start'] , $db_date['st_end']
$st_end_line=date("U",mktime(0,0,0,substr($db_date['st_end'],5,2),substr($db_date['st_end'],8,2),substr($db_date['st_end'],0,4)));
$now=date("U",mktime(0,0,0,substr(date("Y-m-d"),5,2),substr(date("Y-m-d"),8,2),substr(date("Y-m-d"),0,4)));

//計算各年級要統計的學期 以 1001;1002;1011;1012.... 的形式傳入
$Year_scan=array();
for($i=1;$i<=$all_years;$i++) {
 $Y=$i+$IS_JHORES;
  switch ($IS_JHORES) {
  	case '0':
  	    if ($Y>1) {
  	     for ($j=$Y-1;$j>=1;$j--) {
  	      $chk_year=$sel_year-$j;
  	      $Year_scan[$Y].=",".$chk_year."1";
  	      $Year_scan[$Y].=",".$chk_year."2";
  	     }
  	    }
  	break;
  	case '6':
  	    if ($Y>7) {
  	     for ($j=$Y-1;$j>=7;$j--) {
  	      $chk_year=$sel_year-$j+6;
  	      $Year_scan[$Y].=",".$chk_year."1";
  	      $Year_scan[$Y].=",".$chk_year."2";
  	     }
  	    }
  	 
  	break;  
  }
  //如果是下學期，加1
 				if ($sel_seme==2) $Year_scan[$Y].=",".$sel_year."1";
 	//如果已過結業式，加1
				if ($now>$st_end_line) $Year_scan[$Y].=",".$sel_year.$sel_seme;
	$Year_scan[$Y]=substr($Year_scan[$Y],1);
}  				



//============================================================================================================================
$Start_Year_Class=($IS_JHORES==0)?1:7;
$End_Year_Class=($IS_JHORES==0)?6:9;

//抓取班級設定裡的班級名稱
$class_base= class_base($seme_year_seme);
/*
echo "<pre>";
echo $Start_Year_Class."=>".$End_Year_Class;

exit();
*/

//依年級去分析 國小 1-6 , 國中 7-9
for ($year_name=$Start_Year_Class;$year_name<=$End_Year_Class;$year_name++) {

//$year_name=8;

//各年級要檢查的學期別 $Year_scan[1]~$Year_scan[9]

//年級別, 國小一,二年級只有五個領域
 		if($year_name>3 or ($year_name==3 and ($sel_seme==2 or $now>$st_end_line))){
			//$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			//$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
			//$area_rowspan=9;
		  $ALL_areas=8;  //要把一、二年級時生活領域算進去, 所以是8 個
		} else {
			//$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			//$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
			//$area_rowspan=7;
		  $ALL_areas=5;
		} 	

  //確認本年級有幾個領域
   if ($IS_JHORES==6) $ALL_areas=7;
  //$ALL_areas=$area_rowspan-2;

	//不及格領域數的人數統計
			$NO_PASS[$year_name][1]=0;
			$NO_PASS[$year_name][2]=0;
			$NO_PASS[$year_name][3]=0;
			$NO_PASS[$year_name][4]=0;
			$NO_PASS[$year_name][5]=0;
			$NO_PASS[$year_name][6]=0;
			$NO_PASS[$year_name][7]=0;

	//各領域不及格人數
			$NO_PASS[$year_name][language]=0;
			$NO_PASS[$year_name][math]=0;
			$NO_PASS[$year_name][health]=0;
			$NO_PASS[$year_name][nature]=0;
			$NO_PASS[$year_name][art]=0;
			$NO_PASS[$year_name][social]=0;
			$NO_PASS[$year_name][life]=0;
			$NO_PASS[$year_name][complex]=0;


  //取出名單
  $query="select a.*,b.stud_name,b.stud_person_id,c.guardian_name from stud_seme a,stud_base b,stud_domicile c where a.student_sn=b.student_sn and b.student_sn=c.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','5','15') order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	
	//學生人數
	$Student_Num[$year_name]=$res->RecordCount();		
	
	if ($Year_scan[$year_name]!="") {
	
	$ALL_PASS[$year_name]=array(); //統過領域數的人數累計
	$sn=array();
	$student_data=array();
	$fin_score=array();
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$sn[]=$student_sn;
		$student_data[$student_sn]['seme_num']=$res->fields['seme_num'];
		$student_data[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
		$student_data[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data[$student_sn]['stud_id']=$res->fields['stud_id'];
		$seme_class=$res->fields['seme_class'];
		$student_data[$student_sn]['class_name']=$class_base[$seme_class];
		$res->MoveNext();
	}

	$semes=explode(',',$Year_scan[$year_name]);
  
	//抓取領域成績
	$fin_score=cal_fin_score($sn,$semes,"","",2);  //2015.12.09改
  

  //檢查所有學生的每科成績
   foreach ($student_data as $student_sn=>$chk_score) {
			
			
			//1個領域不及格 , 即通過的總領域 -1 ,2個領域不及格 , 即通過的總領域 -2 ....
	   /*
			if ($fin_score[$student_sn][succ]==$ALL_areas-1) $NO_PASS[$year_name][1]++; 
			if ($fin_score[$student_sn][succ]==$ALL_areas-2) $NO_PASS[$year_name][2]++; 
			if ($fin_score[$student_sn][succ]==$ALL_areas-3) $NO_PASS[$year_name][3]++; 
			if ($fin_score[$student_sn][succ]==$ALL_areas-4) $NO_PASS[$year_name][4]++; 
			if ($fin_score[$student_sn][succ]==$ALL_areas-5) $NO_PASS[$year_name][5]++; 
		  if ($fin_score[$student_sn][succ]==$ALL_areas-6) $NO_PASS[$year_name][6]++; 
		  if ($fin_score[$student_sn][succ]==$ALL_areas-7) $NO_PASS[$year_name][7]++; 
		  if ($fin_score[$student_sn][succ]==$ALL_areas-8) $NO_PASS[$year_name][8]++;
	   */
	   $fail=$fin_score[$student_sn][fail];
	   $NO_PASS[$year_name][$fail]++;

		  
		  $SUCC=$fin_score[$student_sn][succ];
		  $ALL_PASS[$year_name][$SUCC]++;
		  
			//語文不及格
			if ($fin_score[$student_sn][language][avg][score]<60 and $fin_score[$student_sn][language][avg][score]>0) $NO_PASS[$year_name][language]++;
			//數學不及格
			if ($fin_score[$student_sn][math][avg][score]<60 and $fin_score[$student_sn][math][avg][score]>0) $NO_PASS[$year_name][math]++;
			//健體
			if ($fin_score[$student_sn][health][avg][score]<60 and $fin_score[$student_sn][health][avg][score]>0) $NO_PASS[$year_name][health]++;
			
			//三年級以上
      //if ($year_name>3) {
				//自然
				if ($fin_score[$student_sn][nature][avg][score]<60 and $fin_score[$student_sn][nature][avg][score]>0) $NO_PASS[$year_name][nature]++;
				//藝文
				if ($fin_score[$student_sn][art][avg][score]<60 and $fin_score[$student_sn][art][avg][score]>0) $NO_PASS[$year_name][art]++;
				//社會
				if ($fin_score[$student_sn][social][avg][score]<60 and $fin_score[$student_sn][social][avg][score]>0) $NO_PASS[$year_name][social]++;
      //} else { 
				//生活 國小一二年級
				if ($fin_score[$student_sn][life][avg][score]<60 and $fin_score[$student_sn][life][avg][score]>0) $NO_PASS[$year_name][life]++;
			//}

			//綜合
			if ($fin_score[$student_sn][complex][avg][score]<60 and $fin_score[$student_sn][complex][avg][score]>0) $NO_PASS[$year_name][complex]++;

   } //end foreach $fin_score
   
   } // end if $Year_scan

} // end for year_name
} // end if (isset($_POST['c_curr_seme']))
//=======================================================================================================
//秀出網頁
head("各領域成績不及格人數統計表");

//列出選單
echo $tool_bar;

?>
<form method="post" action="<?php $_SERVER['PHP_SELF'];?>" name="myform" id="myform" target="">
	<select id="select_year" name="c_curr_seme">
	<option style="color:#FF00FF">請選擇學期</option>
	<?php
	while (list($tid,$tname)=each($class_seme_p)){
	  if (substr($tid,0,3)>$curr_year-3) {
    ?>
      		<option value="<?php echo $tid;?>" <?php if ($tid==$_POST['c_curr_seme']) echo "selected";?>><?php echo $tname;?></option>
   <?php
      }
    } // end while
    ?>
</select>
</form>

<?php
if ($_POST['c_curr_seme']) {
?>
 
<br>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse:collapse; font-size:<?php echo $m_arr['text_size'];?>;' bordercolor='#111111' width='100%'>
	<tr>
		<td rowspan="2" align="center">年級</td>
		<td colspan="9" align="center">各學習領域學生成績評量情形</td>
		<td colspan="<?php echo (($IS_JHORES==6)?7:8);?>" align="center">學生成績評量不及格領域數情形</td>
		<td rowspan="2" align="center">統計學期</td>
	</tr>
	  <td>該年級總學生人數</td>
	  <td>語文領域不及格人數</td>
	  <td>數學領域不及格人數</td>
	  <td>社會領域不及格人數</td>
	  <td>自然與生活科技領域不及格人數</td>
	  <td>藝術與人文領域不及格人數</td>
	  <td>健康與體育領域不及格人數</td>
	  <td>綜合活動領域不及格人數</td>
	  <td>生活領域不及格人數</td>
	  <td>1個學習領域不及格人數</td>
	  <td>2個學習領域不及格人數</td>
	  <td>3個學習領域不及格人數</td>
	  <td>4個學習領域不及格人數</td>
	  <td>5個學習領域不及格人數</td>
	  <td>6個學習領域不及格人數</td>
	  <td>7個學習領域不及格人數</td>
	  <?php
	  if ($IS_JHORES==0) echo "<td>8個學習領域不及格人數</td>";
	  ?>
	<tr>
 <?php
 //依年級列出人數
 for ($year_name=$Start_Year_Class;$year_name<=$End_Year_Class;$year_name++) {
 ?>
 <tr>
 	<td align="center"><?php echo $year_name;?></td>
 	<td align="center"><?php echo $Student_Num[$year_name];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][language];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][math];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][social];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][nature];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][art];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][health];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][complex];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][life];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][1];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][2];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][3];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][4];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][5];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][6];?></td>
  <td align="center"><?php echo $NO_PASS[$year_name][7];?></td>
  <?php
	  if ($IS_JHORES==0) echo "<td align=\"center\">".$NO_PASS[$year_name][8]."</td>";
	  ?>
  <td><?php echo $Year_scan[$year_name];?></td>
 </tr>
 <?php 
 } // end for
 ?>	
</table>
<?php
} // end if
?>
<table border="0">
 <tr>
 	<td>說明：</td>
 </tr>
 <tr>
  <td>
依據103年4月25日修正發布「國民小學及國民中學學生成績評量準則」第9條規定，直轄市、縣 (市)政府應於每學期結束後一個月內檢視所轄國民中小學學生之評量結果，作為其教育政策擬訂及推動之參據，並於每學年結束後二個月內連同補救教學實施成效報教育部備查。
</td>
 </tr>
</table>
<div id="wait" style="display:none">
<p style="color:#FF0000">資料處理中，請稍候....</p>	
</div>
<script>
	$("#select_year").change(function(){
		  
		  wait.style.display="block";
		  
		  document.myform.submit();
		  
		})
	

</script>



