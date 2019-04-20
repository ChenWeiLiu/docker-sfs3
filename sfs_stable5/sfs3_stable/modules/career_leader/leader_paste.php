<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//目前學期
$c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf("%03d%d",curr_year(),curr_seme());

//取得所有班級
 $select_class=class_base(curr_year().curr_seme());  //取得目前的班級陣列  [701]一年一班 , [702]一年二班

//取得任教班級
$class_num=get_teach_class();
$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($class_num,-3,strlen($class_num)-2),substr($class_num,-2));

//該任教班級已在學的總學期數
$select_seme=get_class_seme_select($class_num);  													//array [1001]="100學年第1學期"
$select_seme_key=get_class_seme_key_select($select_seme,$class_num);			//array 如: [1001]="7-1"; [1012]="8-2";

//取班級學生名單
$query="select a.student_sn,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='".curr_year().curr_seme()."' and a.seme_class='$class_num' and a.student_sn=b.student_sn order by a.seme_num";
$res_stud_list=$CONN->Execute($query);

//POST 後的動作
if ($_POST['act']=="save") {
	$data_array=explode("\n",$_POST['data_array']);
 	$save_ok=0;
 	$seme_key=$select_seme_key[$c_curr_seme]; 
 foreach ($data_array as $a) {
 	$data_arr=explode("\t",$a);
  $seme_num=$data_arr[0];
  $stud_name=$data_arr[1];
  //取得學生的 student_sn
  $sql="select a.student_sn from stud_seme a,stud_base b where a.seme_year_seme='".curr_year().curr_seme()."' and a.seme_class='$class_num' and a.seme_num='$seme_num' and b.stud_name='$stud_name' and a.student_sn=b.student_sn";
  $res=$CONN->Execute($sql);
  $student_sn=$res->fields['student_sn'];
  if ($student_sn) {
  //取出資料, 並置換目標學期的陣列資料, 再回存
   	/***
 		陣列資料說明:
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][1幹部][1,2] 兩欄
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][2小老師][1,2] 兩欄
 		*/
 		//檢查是否已有舊紀錄
		$query="select * from career_self_ponder where student_sn=$student_sn and id='3-2'";
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
		$sn=$res->fields['sn'];
		if($sn) {
			$ponder_array=unserialize($res->fields['content']); //解開成二維陣列
			//幹部
			$ponder_array[$seme_key][1][1]=$data_arr[2];
			$ponder_array[$seme_key][1][2]=$data_arr[3];
			//小老師
			$ponder_array[$seme_key][2][1]=$data_arr[4];
			$ponder_array[$seme_key][2][2]=$data_arr[5];
			//備註
			$ponder_array[$seme_key]['memo']=$data_arr[6];
			
			$content=serialize($ponder_array);	
			
			$query="update career_self_ponder set id='3-2',content='$content' where sn=$sn";
		}else{
			//幹部
			$ponder_array[$seme_key][1][1]=$data_arr[2];
			$ponder_array[$seme_key][1][2]=$data_arr[3];
			//小老師
			$ponder_array[$seme_key][2][1]=$data_arr[4];
			$ponder_array[$seme_key][2][2]=$data_arr[5];
			//備註
			$ponder_array[$seme_key]['memo']=$data_arr[6];
			
			$ponder_array[$seme_key][data]="";
			$content=serialize($ponder_array);
			
			$query="insert into career_self_ponder set student_sn=$student_sn,id='3-2',content='$content'";
		
		} // end if else
		
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	} // end if student_sn
 } // end foreach	
 
 $INFO="己於".date("Y-m-d H:i:s")."進行儲存動作!";
}




//秀出 SFS3 標題
head();

if ($class_num==0) echo "抱歉, 您不具導師身分! 快貼功能僅提供導師快速貼上全班資料!";


//列出選單
echo $tool_bar;
?>
<form name="myform" method="post" act="<?php echo $_SEVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<?php
	echo $select_class[$class_num]; 
	?>
	<select size="1" name="c_curr_seme" onchange="document.myform.submit()">
		<?php
		foreach ($select_seme as $k=>$v) {
			?>
			<option value="<?php echo $k;?>"<?php if ($k==$c_curr_seme) echo " selected";?>><?php echo $v;?></option>
			<?php
		}
		?>
	</select>
	<table border="0" width="100%">
	 <tr>
	 	<td style="color:#0000FF"><b>年級-學期：<?php echo $select_seme_key[$c_curr_seme];?></b>
	 	 <font size="2" color="red"><?php echo $INFO;?></font>
	 	</td>
	 </tr>
	 <tr>
	 	<td>
	 			<textarea name="data_array" cols="80" rows="10"></textarea>
	 	</td>
	 </tr>
	 <tr>
	 	 	<td>
	 	 		<input type="button" value="貼上資料" onclick="document.myform.act.value='save';document.myform.submit();">
	 	 		<input type="button" value="快貼說明" onclick="readme();">
	 	 	</td>
	 </tr>
	</table>
	<table id="readme_show" style="display:none">
	 <tr>
	    <td style="font-size:10pt;color:#0000dd">
   			說明：請下載Excel填寫範例﹝<a href="demo.xls" style="color:#FF0000">範例</a>﹞，依圖示僅選擇內容複製/貼上即可(資料內容不包括標題列)。
      </td>
	 </tr>
	 <tr>
		 <td><img src="images/paste_demo.png" border="0"></td>
		</tr>
	</table>
	<font color="#800000">※<?php echo $class_num;?>班學生名單，請注意貼上的資料中，座號與姓名用於對照，務必完全正確。</font>
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
	<?php
	$i=0;
	while ($row=$res_stud_list->FetchRow()) {
  	$i++;
  	$student_sn=$row['student_sn'];
  	$seme_num=$row['seme_num'];
  	$stud_name=$row['stud_name'];
	 if ($i%10==1) {
			echo "<tr>";
		}
	?>
			<td align="center"><?php echo $seme_num." ".$stud_name;?></td>
	<?php
	 if ($i%10==0) {
			echo "</tr>";
		}
	?>	 
  	<?php
  } // end while	
	?>
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