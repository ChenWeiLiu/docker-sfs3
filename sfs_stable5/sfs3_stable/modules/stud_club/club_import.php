<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();


//秀出網頁
head("社團活動 - 手動匯入");

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


$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}

//按下儲存鈕後 , 利用 $_SESSION['club_sn'] 為儲存目標
if ($_POST['act']=="save") {
 $insert_num=0;
 $data_array=explode("\n",$_POST['data_array']);
  /* 一列一個學生資料 */
  /* 0學年學期 1學號	2年級	3班級	4座號	5姓名	6社團名稱	7擔任幹部	8成績	9教師評語	10自我省思 */
 foreach ($data_array as $line) {

 	$student_sn="";
  $S=explode("\t",$line);

  if (trim($S[0])!='') {
 	 $c_curr_seme=$S[0];
   //利用學號取得 student_sn
   if (trim($S[1])!='') {
     $query="select student_sn from stud_seme where stud_id='".$S[1]."' and seme_year_seme='$c_curr_seme'";
     $res=mysql_query($query);
     if (mysql_num_rows($res)==1) {
     list($student_sn)=mysql_fetch_row($res);
     } else {
      $student_sn="";
     }
   } 
   //沒取到, 利用班級座號取得 studend_sn
   if ($student_sn=="") {
    $seme_class=sprintf("%d%02d",trim($S[2]),trim($S[3]));
    $seme_num=sprintf("%d",trim($S[4]));
    $query="select student_sn from stud_seme where seme_year_seme='$c_curr_seme' and seme_class='$seme_class' and seme_num='$seme_num'";
    $res=mysql_query($query);
     if (mysql_num_rows($res)==1) {
      list($student_sn)=mysql_fetch_row($res);
     } else {
      $student_sn="";
     }
   } // end if 利用班級座號取得
   
   //有這個學生
   if ($student_sn!="") {
   	$association_name=trim($S[6]);		//社團名稱
   	$score=trim($S[8]);							//成績
   	$description=trim($S[9]); 				//教師評語
   	$stud_post=trim($S[7]);
   	$stud_feedback=trim($S[8]);
   	
   	if (trim($association_name)=='') continue;
   	
  	//檢查有沒有重覆資料 , 不允許重覆
  	switch ($_POST['no_double_score']) {
  		//資料重覆時，略過
  		case '1': 
	  		$query="select student_sn from association where seme_year_seme='$c_curr_seme' and student_sn='$student_sn'";
  			$res=mysql_query($query);
  			if (mysql_num_rows($res)>0) {
  				$sql="";  //資料重覆
  			} else {        
  		    $sql="insert into association (student_sn,seme_year_seme,association_name,score,description,update_sn,stud_post,stud_feedback) values ('$student_sn','$c_curr_seme','$association_name','$score','$description','".$_SESSION['session_tea_sn']."','$stud_post','$stud_feedback')"; 	 
  			}
  		break;
  		//資料重覆時覆寫
  		case '2':
  			$query="delete from association where seme_year_seme='$c_curr_seme' and student_sn='$student_sn'";
  			$res=$CONN->Execute($query) or die('SQL Error! query='.$query);
  		  $sql="insert into association (student_sn,seme_year_seme,association_name,score,description,update_sn,stud_post,stud_feedback) values ('$student_sn','$c_curr_seme','$association_name','$score','$description','".$_SESSION['session_tea_sn']."','$stud_post','$stud_feedback')"; 	 
  		break;
  		//一律新增
  		case '3':
  		  $sql="insert into association (student_sn,seme_year_seme,association_name,score,description,update_sn,stud_post,stud_feedback) values ('$student_sn','$c_curr_seme','$association_name','$score','$description','".$_SESSION['session_tea_sn']."','$stud_post','$stud_feedback')"; 	 
  		break;  		
  		
    }
  	
   	if ($sql!="") {
   		 $res=$CONN->Execute($sql) or die('SQL Error! query='.$sql);
       $insert_num++;
   	}
   } // end if student_sn!=''   
  } // end if 學年學期  
 } // end foreach
} // end if 



?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<!-- mode 參數 insert , update ,在 submit前進行 mode.value 值修改 -->
	<input type="hidden" name="act" value="">

<?php
$query="select seme_year_seme,count(*) as num from association group by seme_year_seme order by seme_year_seme";
$res=$CONN->Execute($query);
?>
<font color=blue size=4>※列表為社團資料庫中已有的各學期資料數：</font>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='300'>
	<tr bgcolor='#FFDDDD' ALIGN='CENTER'>
		<td>學年學期</td><td>資料數</td>
	</tr>
<?php
 while ($row=$res->fetchRow()) {
 ?>
	<tr ALIGN='CENTER'>
		<td><a href='dup_del.php?ys=<?php echo $row['seme_year_seme'] ?>' target='_BLANK'><?php echo substr($row['seme_year_seme'],0,3);?>學年第<?php echo substr($row['seme_year_seme'],-1);?>學期</a></td><td><?php echo $row['num'];?> 筆</td>
	</tr>  
 <?php
 }
?>
</table>
<table>
	<tr>
		<td style='color:#800000'>請貼上資料：</td>
	</tr>
	 <tr>
	  <td><textarea cols="100" rows="6" name="data_array"></textarea></td>
	</tr>
</table>
<table border="0">
 <tr>
 	<td><font color='#800000'>若發現某學期資料庫中已存在某學生資料時，如何處理？</font><br/>
 		。<input type="radio" name="no_double_score" value="1" checked>略過該筆不處理<br/>
 		。<input type="radio" name="no_double_score" value="2">一律覆寫，僅保留最近的這一筆	<br/>
 		。<input type="radio" name="no_double_score" value="3">替該生增加這筆新記錄(該生將有多筆社團記錄)<br/>
 	</td>
 </tr>
 <tr>
  <td><input type="button" value="送出資料" onclick="if (document.myform.data_array.value!='') { document.myform.act.value='save';document.myform.submit(); }"></td>
 </tr>
</table>
<table border="0">
 <tr>
  <td style="color:#FF0000"><?php if ($_POST['act']=='save') echo "本次已更動".$insert_num."筆記錄";?></td>
 </tr>
</table>
<table border="0">
 <tr>
  <td style="color:#0066FF">匯入方式說明：<br>
  	1.本程式匯入的社團成績，僅供成績單輸出時使用。<br>
  	2.匯入方式為將 Excel 成績直接複製／貼上即可，但請注意欄位順序(如圖示)，每一筆資料必須包含「學年學期」。<br>
  	3.「學號」或「班級、座號」，這兩部分至少必須有其中一項，以便識別學生身分。<br>
  	4.當按下「送出資料」後，系統會先進行資料分析，每一列視為一筆資料。<br>
  	<font size=2>
  	(1)若該筆資料有輸入「學年學期」及「學號」，系統據此優先作為該資料的身分識別，並將社團記錄存入該生對應的學年學期社團記錄。<br>
  	(2)若該筆資料無學號，但有輸入「學年學期」、「班級」及「座號」，則系統會以<font color=red>該學年度的班級座號</font>做為身分識別條件，存入該生對應的學年學期社團記錄。<br>
  	(3)學生姓名僅供建檔者識別，系統不予採用，但欄位仍必須保留。<br/>
  	(4)若欠缺社團名稱，該筆資料不予處理。<br/>
  	</font>
  	<img src="./images/demo.png" border="0"><br>
  	
  </td>
 </tr>
 <tr>
  <td>【<a href="./images/demo.xls" style="color:#FF0000">按我</a>】可下載 EXCEL demo 檔案。</td>
 </tr>
</table>
</form>

