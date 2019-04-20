<?php
include "config.php";

sfs_check();

$SEX[1]="男";
$SEX[2]="女";

//秀出網頁
head("體適能管理 - 快貼全校身高體重資料");
$tool_bar=&make_menu($menu_p);
//列出選單
echo $tool_bar;

//程式開始
$seme_class=$_POST['seme_class'];
$stud_data=$_POST['stud_data'];

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
?>

<form name="myform" action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
<table border="0" width="100%">
 <tr>
  <td style="color:#0000FF"><br>本程式是為了能將健康中心的資料能快速匯入系統而設計，<br>若貼上的資料格式有誤，匯入的資料則不保證百分之百正確，較保險的方法還是一筆一筆慢慢輸入。</td>
 </tr>	
 <tr>
  <td>
  	方法:<br>
  	1.請健康中心將資料透過下列步驟匯出 : 首頁>報表清單>生長發育>全校身高體重視力清單<br>
  	<img src='./images/source.jpg'><br>
  	2.利用 EXCEL開啟, 檢查每一個欄位是否都有資料, 如下圖, 在體重之前, 每個欄位都要有資料.<br>
  	<img src='./images/source1.png'><br>
  	3.系統會比對【統編】、【年級】、【班級】、【座號】這四欄, 並截取【身高】、【體重】這兩欄資料, <br>
  若某筆資料的其他欄資料有缺漏，如：學號有缺漏, 會導致欄位順序不正確，而無法正確讀取資料，<br><font color=red>請乾脆將該欄資料整欄刪除.</font><br>
  	4.將資料全選複製/貼上，然後按送出即可，注意! 第一行標題欄也要包括!<br>
  	5.注意, 按下送出後, 匯入的資料會存入<font color="#FF0000"><?php echo curr_year()."學年第".curr_seme()."學期</font> 的學生資料";?><br>
  	6.送出後, <font color=red>處理資料可能會有點久(本校2400位學生，要處理5分鐘)</font>, 若有資料無法存入, 請手動調整.<br>
  	7.重覆貼資料只會 update ,不會產生新資料.
  	</td>
 </tr>
  <tr>
   <td>
    		<textarea name="stud_data" cols="100" rows="10"></textarea>
    	</td>
    </tr>
    </table>
    <input type="submit" value="送出資料">

	
<?php
//有資料的話先分析身高體重
if ($stud_data) {
	$data_arr=explode("\n",$stud_data);
	//第1欄為標題, 驗證欄位名稱
	//先去除先後空白
	 $data_arr[0]=trim($data_arr[0]);
	//去掉跟隨別的擠在一塊的空白
   $data_arr[0] = preg_replace('/\s(?=\s)/','', $data_arr[0]);
   $data_arr[0] = preg_replace('/[\n\r\t]/', ' ', $data_arr[0]);
  //切割, 放入 $T array()
	$T=explode(" ",$data_arr[0]); 
	//求身分證, 學號, 年級	班級	座號	學生 身高, 體重 分別是第幾欄
	for ($i=0; $i < count($T) ;$i++) {
	 if ($T[$i]=='統編') $STUD_PERSON_ID=$i;
	 if ($T[$i]=='學號') $STUDENT_SN=$i;
	 if ($T[$i]=='年級') $YEAR_CLASS=$i;
	 if ($T[$i]=='班級') $CLASS=$i;
	 if ($T[$i]=='座號') $NUM=$i;
	 if ($T[$i]=='學生') $STUD_NAME=$i;
	 if ($T[$i]=='身高') $TALL=$i;
	 if ($T[$i]=='體重') $WEIGH=$i;
	}
	
 //開始處理第2行後的資料
 $E=0; //錯誤
 $INPUT_NUM=0; //處理成功
	for ($i = 1 ; $i < count($data_arr); $i++ ) {
		//去掉前後空白
	 $data_arr[$i] = trim($data_arr[$i]);
	 //去掉跟隨別的擠在一塊的空白
   $data_arr[$i] = preg_replace('/\s(?=\s)/','', $data_arr[$i]);
   $data_arr[$i] = preg_replace('/[\n\r\t]/', ' ', $data_arr[$i]);

   //變成二維陣列
   $student=explode(" ",$data_arr[$i]);  //某筆學生的資料
   //班級
   switch ($student[$YEAR_CLASS]) {
   	case '一':
   	  $seme_class="1".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '二':
   	  $seme_class="2".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '三':
   	  $seme_class="3".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '四':
   	  $seme_class="4".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '五':
   	  $seme_class="5".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '六':
   	  $seme_class="6".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '七':
   	  $seme_class="7".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '八':
   	  $seme_class="8".sprintf("%02d",$student[$CLASS]);
   	break;
   	case '九':
   	  $seme_class="9".sprintf("%02d",$student[$CLASS]);
   	break;    
   }
   
   //以身分證及班級為主要索引, 取得學生的 student_sn
		$query="select a.student_sn from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.stud_person_id='".$student[$STUD_PERSON_ID]."' and b.seme_year_seme='$c_curr_seme' and b.seme_class='$seme_class'";
    $result=mysql_query($query);
   	$row=mysql_fetch_row($result);
   	list($student_sn)=$row;
    $ERROR=1;
    if ($student_sn and $student[$TALL]>0 and $student[$WEIGH]>0) {
    	  $query="select student_sn from fitness_data where student_sn='".$student_sn."' and c_curr_seme='".$c_curr_seme."'";
   			$result_chk=mysql_query($query);
   			//如果有資料
   			if (mysql_num_rows($result_chk)) {
   			  $query="update `fitness_data` set tall='".$student[$TALL]."',weigh='".$student[$WEIGH]."' where student_sn='".$student_sn."' and c_curr_seme='".$c_curr_seme."'";
   			} else {
   				$query="insert into `fitness_data` (c_curr_seme,student_sn,tall,weigh) values ('$c_curr_seme','".$student_sn."','".$student[$TALL]."','".$student[$WEIGH]."')";
				}	  
				//echo $query."<br>";
        if (mysql_query($query)) {
        	$ERROR=0;
          $INPUT_NUM++;
        } else {
          $ERROR=1;
        }    
    } 
    if ($ERROR==1) {
    	$E++;
      $ERR[$E]['PERSON_ID']=$student[$STUD_PERSON_ID];
      $ERR[$E]['YEAR_CLASS']=$student[$YEAR_CLASS];
      $ERR[$E]['CLASS']=$student[$CLASS];
      $ERR[$E]['NUM']=$student[$NUM];
      $ERR[$E]['STUD_NAME']=$student[$STUD_NAME];
      $ERR[$E]['TALL']=$student[$TALL];
      $ERR[$E]['WEIGH']=$student[$WEIGH];
    } // end if ($student_sn)


   
	} // end for
	echo "<br>共成功更新 ".$INPUT_NUM. "位學生的身高體重資料!<br> 以下為存入失敗的資料.<br>";
 ?>
 
 <table border="1" style="border-collapse:collapse" bordercolor="#CCCCCC">
	<tr  bgcolor="#FFCCFF">
		<td width="100" style="font-size:10pt" align="center">統編</td>
		<td width="50" style="font-size:10pt" align="center">年級</td>
		<td width="50" style="font-size:10pt" align="center">班級</td>
		<td width="50" style="font-size:10pt" align="center">座號</td>
		<td width="100" style="font-size:10pt" align="center">姓名</td>
		<td width="50" style="font-size:10pt" align="center">身高</td>
		<td width="50" style="font-size:10pt" align="center">體重</td>
	</tr>
 <?php
 for ($i=1;$i<=$E;$i++) {
 ?>
 	<tr>
		<td width="100" style="font-size:10pt" align="center"><?php echo $ERR[$i]['PERSON_ID'];?></td></td>
		<td width="50" style="font-size:10pt" align="center"><?php echo $ERR[$i]['YEAR_CLASS'];?></td>
		<td width="50" style="font-size:10pt" align="center"><?php echo $ERR[$i]['CLASS'];?></td>
		<td width="50" style="font-size:10pt" align="center"><?php echo $ERR[$i]['NUM'];?></td>
		<td width="100" style="font-size:10pt" align="center"><?php echo $ERR[$i]['STUD_NAME'];?></td>
		<td width="50" style="font-size:10pt" align="center"><?php echo $ERR[$i]['TALL'];?></td>
		<td width="50" style="font-size:10pt" align="center"><?php echo $ERR[$i]['WEIGH'];?></td>
	</tr>
 <?php 
 }
 ?>
</table>
 <?php
}
?>