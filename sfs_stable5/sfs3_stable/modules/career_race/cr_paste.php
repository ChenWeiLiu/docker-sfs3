<?php
//取得設定檔
include_once "config.php";

sfs_check();

//取得系統中所有學期資料, 每一學年有二個學期
$class_seme_p = get_class_seme(); 

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期 , 若有選定則以選定的學期作為比對學生班級座號的依據, 否則以最新學期的個資為準
$c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf('%03d%1d',$curr_year,$curr_seme);

 //計算該學期的日期區間
 $year=sprintf("%d",substr($c_curr_seme,0,3));
 $seme=substr($c_curr_seme,-1);
 //起始日
 $sql="select day from school_day where year='$year' and seme='$seme' and day_kind='start'";
 $res=mysql_query($sql);
 list($st_date)=mysql_fetch_row($res);
 
 //結束日
 $sql="select day from school_day where year='$year' and seme='$seme' and day_kind='end'";
 $res=mysql_query($sql);
 list($end_date)=mysql_fetch_row($res);



//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** 資料處理 **********************/
//儲存
if ($_POST['act']=='save') {
 $data_array=explode("\n",$_POST['data_array']);
 $save_ok=0; 
 foreach ($data_array as $a) {
 	$data_arr=explode("\t",$a);
   
  switch ($_POST['data_mode']) {	
   
   //第一種 學年學期	班級	座號	姓名 {年度 競賽類別}	競賽範圍	競賽性質	競賽名稱	得獎名次	證書日期	主辦單位	備註
   case '1':
   	$year_seme=$data_arr[0];
   	$seme_class=$data_arr[1];
   	$seme_num=$data_arr[2];
   	$stud_name=$data_arr[3];
   	$sql="select a.student_sn from stud_seme a,stud_base b where a.seme_year_seme='$year_seme' and a.student_sn=b.student_sn and a.seme_class='$seme_class' and a.seme_num=$seme_num and  b.stud_name='$stud_name'";
   	$res=mysql_query($sql);
   	list($student_sn)=mysql_fetch_row($res);
   	if ($student_sn) {
   	//證書日期 , 檢查各式並進行修正, 若格式不對, 填上今天日期
   	$c_date=explode("-",$data_arr[10]);
   	 if (count($c_date)!=3) {
        	$c_date=explode("/",$data_arr[10]);
   				if (count($c_date)!=3) {
        			$c_date=explode(".",$data_arr[10]);
  	   				if (count($c_date)!=3) {
  	   					$data_arr[10]=sprintf("%d",$c_date[0])."-".sprintf("%02d",$c_date[1])."-".sprintf("%02d",$c_date[2]);
	        	  } else {
	        	   $data_arr[10]=date("Y-m-d");
	        	  }       	  		
   				} else {
   					$data_arr[10]=sprintf("%d",$c_date[0])."-".sprintf("%02d",$c_date[1])."-".sprintf("%02d",$c_date[2]);   				  
   				}
   	 } else {
			$data_arr[10]=sprintf("%d",$c_date[0])."-".sprintf("%02d",$c_date[1])."-".sprintf("%02d",$c_date[2]); 
   	 }
   	
   	//寫入一筆資料
   		//$query="insert into career_race set student_sn='$student_sn',level=".$data_arr[4].",squad=".$data_arr[5].",name='".$data_arr[6]."',rank='".$data_arr[7]."',certificate_date='".$data_arr[8]."',sponsor='".$data_arr[9]."',memo='".$data_arr[10]."',update_sn='".$_SESSION['session_tea_sn']."'";
   		$query="insert into career_race set student_sn='{$student_sn}',year='{$data_arr[4]}',nature='{$data_arr[5]}',level='{$data_arr[6]}',
   		squad='{$data_arr[7]}' ,`name`='{$data_arr[8]}', rank='{$data_arr[9]}',	certificate_date='{$data_arr[10]}',sponsor='{$data_arr[11]}',memo='{$data_arr[12]}', update_sn='{$_SESSION['session_tea_sn']}', `word`='{$data_arr[13]}', `weight`='{$data_arr[14]}' ";
   		if (mysql_query($query)) {
   		 $save_ok+=1;
   		} else {
   		 echo "Error! query=$query";
   		 exit();
   		}
    } else {
    	if (trim($a)!="") $Err_info.="<font color=blue>無法辨識資料列:</font><font size=2>".$a."</font><br>";    
    }// end if $student_sn
    break;

    //第二種 學號 姓名 {年度 競賽類別} 競賽範圍	競賽性質	競賽名稱	得獎名次	證書日期	主辦單位	備註
    case '2': 
   	$stud_id=$data_arr[0];
   	$stud_name=$data_arr[1];
   	$sql="select student_sn from stud_base where stud_study_cond='0' and stud_id='$stud_id' and stud_name='$stud_name'";
   	$res=mysql_query($sql);
   	list($student_sn)=mysql_fetch_row($res);
   	if ($student_sn) {
   	//證書日期 , 檢查各式並進行修正, 若格式不對, 填上今天日期
   	$c_date=explode("-",$data_arr[8]);
   	 if (count($c_date)!=3) {
        	$c_date=explode("/",$data_arr[8]);
   				if (count($c_date)!=3) {
        			$c_date=explode(".",$data_arr[8]);
  	   				if (count($c_date)!=3) {
  	   					$data_arr[8]=sprintf("%d",$c_date[0])."-".sprintf("%02d",$c_date[1])."-".sprintf("%02d",$c_date[2]);
	        	  } else {
	        	   $data_arr[8]=date("Y-m-d");
	        	  }       	  		
   				} else {
   					$data_arr[8]=sprintf("%d",$c_date[0])."-".sprintf("%02d",$c_date[1])."-".sprintf("%02d",$c_date[2]);   				  
   				}
   	 } else {
			$data_arr[8]=sprintf("%d",$c_date[0])."-".sprintf("%02d",$c_date[1])."-".sprintf("%02d",$c_date[2]); 
   	 }
   	
   	//寫入一筆資料
   		//$query="insert into career_race set student_sn='$student_sn',level=".$data_arr[2].",squad=".$data_arr[3].",name='".$data_arr[4]."',rank='".$data_arr[5]."',certificate_date='".$data_arr[6]."',sponsor='".$data_arr[7]."',memo='".$data_arr[8]."',update_sn='".$_SESSION['session_tea_sn']."'";
   		$query="insert into career_race set student_sn='{$student_sn}',year='{$data_arr[2]}',nature='{$data_arr[3]}',level='{$data_arr[4]}',
   		squad='{$data_arr[5]}',name='{$data_arr[6]}',rank='{$data_arr[7]}',
   		certificate_date='{$data_arr[8]}',sponsor='{$data_arr[9]}',memo='{$data_arr[10]}' ,
   		update_sn='{$_SESSION['session_tea_sn']}' , `word`='{$data_arr[11]}', `weight`='{$data_arr[12]}' ";
   		if (mysql_query($query)) {
   		 $save_ok+=1;
   		} else {
   		 echo "Error! query=$query";
   		 exit();
   		}
    } else {
    	if (trim($a)!="") $Err_info.="<font color=blue>無法辨識資料列=></font><font size=2 color=red>".$a."</font><br>";    
    }// end if $student_sn


    
    break;
  }
  
  
 } // end foreach

} // end if $_POST['save']
//刪除
if ($_POST['act']=='delete') {
 foreach ($_POST['check_it'] as $v) {
 	$query="delete from career_race where sn='$v'";
 	mysql_query($query); 
 }
} // end if delete

//刪除單筆
if ($_POST['act']=='DeleteOne') {
	$sn=$_POST['option1'];
	$query="delete from career_race where sn='$sn'";
	mysql_query($query);
}

//讀取本學年度使用者已登錄的所有競賽
if ($c_curr_seme!="") $race_record=get_race_record($c_curr_seme,$_SESSION['session_tea_sn']);	



/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();


//列出選單
echo $tool_bar;


?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
   <font color="#800000"><u><b>※請貼上要登錄的資料</b></u></font>
   <input type="button" value="使用說明" onclick="readme();">
   <table border="0" id="readme_show" style="display:none">
     <tr>
      <td style="font-size:10pt;color:#0000dd">
   			說明：請下載Excel填寫範例﹝<a href="demo1.xls" style="color:#FF0000">範例1</a>﹞﹝<a href="demo2.xls" style="color:#FF0000">範例2</a>﹞，依圖示僅選擇內容複製/貼上即可(資料內容不包括標題列)。
      </td>
     </tr>	
		<tr>
		 <td><img src="images/paste_demo.png" border="0"></td>
		</tr>
   </table>
   <table border="0">
   	<tr>
   		<td>
   	<textarea cols="80" rows="10" name="data_array"></textarea>
   	<br>
   	<font color="#800000">欄位順序格式：</font><br>
   	<input type="radio" name="data_mode" value="1" checked>[範例1]學期學年、班級、座號、姓名....<br>
   	<input type="radio" name="data_mode" value="2">[範例2]學號、姓名....
   	<br><br>
   	<input type="button" value="送出資料" onclick="document.myform.act.value='save';document.myform.submit()">
   	</td>
   	</tr>
   </table>
   <?php
 //訊息提示
  if ($_POST['act']=="save") {
   ?>
    <table border="0" width="100%">
      <tr>
        <td style="color:#FF0000"><?php echo "本次共存入".$save_ok."筆資料!";?></td>
      </tr>
      <tr><td><?php echo $Err_info;?></td></tr>
    </table>
  <?php 
  } 
  ?>
   <table border="0" width="100%">
     <tr>
      <td style="color:#800000">
      	<u><b>※資料顯示</b></u>
				<select name="c_curr_seme" onchange="this.form.submit()">
					<?php
					foreach ($class_seme_p as $tid=>$tname) {
    			?>
    				<option style="color:#FF00FF" value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   				<?php
    			} // end while
    			?>
    		</select>    
      	<font size=2>日期：<?php echo $st_date;?>~<?php echo $end_date;?></font>(僅列出您登錄的資料)</td>
     </tr>
   </table>
<?php
	list_race_record($race_record,1,1,'cr_input.php'); 
 ?>
 <table border="0">
 	<tr>
 	      <td><input type="button" value="刪除勾選的資料" onclick="if (confirm('您確定要刪除勾選的資料?')) { document.myform.act.value='delete';document.myform.submit(); } "></td>
 	</tr>
 </table>
</form>
<Script Language="JavaScript">
function readme() {
	var dis=readme_show.style.display;	
	if (dis=='none') {
		readme_show.style.display="block";
	} else {
		readme_show.style.display="none";
	}
}
</Script>

