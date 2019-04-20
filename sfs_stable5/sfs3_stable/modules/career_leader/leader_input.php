<?php
include_once('config.php');

sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//目前學期
$c_curr_seme=($_POST['c_curr_seme']!="")?$_POST['c_curr_seme']:sprintf("%03d%d",curr_year(),curr_seme());

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

//取得所有班級
 $select_class=class_base(curr_year().curr_seme());  //取得目前的班級陣列  [701]一年一班 , [702]一年二班

//取得任教班級
$class_num=($_POST['class_num']=='')?get_teach_class():$_POST['class_num'];

//$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($class_num,-3,strlen($class_num)-2),substr($class_num,-2));

//該任教班級已在學的總學期數
$select_seme=get_class_seme_select($class_num);  													//array [1001]="100學年第1學期"
$select_seme_key=get_class_seme_key_select($select_seme,$class_num);			//array 如: [1001]="7-1"; [1012]="8-2";

//目前編輯的學期
$seme_key=$select_seme_key[$c_curr_seme]; 

//取班級學生名單
$query="select a.student_sn,a.seme_num,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='".curr_year().curr_seme()."' and a.seme_class='$class_num' and a.student_sn=b.student_sn order by a.seme_num";
$res_stud_list=$CONN->Execute($query);

//POST 後的動作
if ($_POST['act']=="save") {
 foreach ($_POST['ponder_array'] as $student_sn=>$v) {
	//檢查是否已有舊紀錄
	$query="select * from career_self_ponder where student_sn=$student_sn and id='3-2'";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	$sn=$res->fields['sn'];
	if($sn) {
	 //取出資料, 僅取代本學期	  		
		$ponder_array=unserialize($res->fields['content']); //解開成二維陣列
		//幹部
		$ponder_array[$seme_key][1][1]=$v[$seme_key][1][1];
		$ponder_array[$seme_key][1][2]=$v[$seme_key][1][2];
		//小老師
		$ponder_array[$seme_key][2][1]=$v[$seme_key][2][1];
		$ponder_array[$seme_key][2][2]=$v[$seme_key][2][2];
		//備註
		$ponder_array[$seme_key][memo]=$v[$seme_key][memo];
		//echo $ponder_array[$seme_key][1][1];
		//重建新陣列
    $new_ponder_array=array();
		foreach ($ponder_array as $K=>$V) {
		  if ($K!="") {
			//幹部
			$new_ponder_array[$K][1][1]=$ponder_array[$K][1][1];
			$new_ponder_array[$K][1][2]=$ponder_array[$K][1][2];
			//小老師
			$new_ponder_array[$K][2][1]=$ponder_array[$K][2][1];
			$new_ponder_array[$K][2][2]=$ponder_array[$K][2][2];
			//備註
			$new_ponder_array[$K][memo]=$ponder_array[$K][memo];
		  }
		}		
		
		$content=addslashes(serialize($new_ponder_array));
		$query="update career_self_ponder set id='3-2',content='$content' where sn=$sn";
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	} else {
		$content=serialize($v);
		$query="insert into career_self_ponder set student_sn=$student_sn,id='3-2',content='$content'";
		$res=$CONN->Execute($query) or die("SQL錯誤:$query");
  } // end if
 } // end foreach
 
 $INFO="己於".date("Y-m-d H:i:s")."進行儲存動作!";
 
}

//匯出本班名單
if ($_POST['act']=="output") {
  $data_output="
	<table>
		<tr>
		 <td colspan=\"6\">".$select_class[$class_num]."幹部名單</td>
		</tr>
		<tr>
			<td>座號</td>
			<td>姓名</td>
			<td>幹部1</td>
			<td>幹部2</td>
			<td>小老師1</td>
			<td>小老師2</td>
		</tr>";
	//開始列出學生及本學期明細
	$stud_count=0;
  while ($row=$res_stud_list->FetchRow()) {
  	$stud_count++;
  	$student_sn=$row['student_sn'];
  	$seme_num=$row['seme_num'];
  	$stud_name=$row['stud_name'];
		//取出該生幹部相關資料(包含所有學期的陣列資料, 以下只顯示本學期, 其他學期則以 hidden 方式)
		$query="select * from career_self_ponder where student_sn='$student_sn' and id='3-2'";
 		$res_ponder=$CONN->Execute($query);
 		$ponder_array=unserialize($res_ponder->fields['content']); //二維陣列
 		/***
 		陣列資料說明:
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][1幹部][1,2] 兩欄
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][2小老師][1,2] 兩欄
 		*/
  	$data_output.="
  	<tr>
  		<td>".$seme_num."</td>
  		<td>".$stud_name."</td>";
  		
  		foreach ($select_seme as $the_seme=>$V) {
  		  if ($the_seme==$c_curr_seme) {
			    $data_output.="
					<td>".stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][1][1])."</td>
					<td>".stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][1][2])."</td>
		  		<td>".stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][2][1])."</td>
		  		<td>".stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][2][2])."</td>";		  		
   		  } // end if
  		} // end foreach
  		
 		$data_output.="</tr>";
  } // end while
	
	$data_output.="</table>";

  $filename=$class_num."_leader.xls";
  header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/octetstream");
	header("Cache-Control: max-age=0");
	header("Pragma: public");
	header("Expires: 0");
	echo $data_output;

	exit;

} // end if output
//匯出全校名單



//秀出 SFS3 標題
head();

if ($class_num==0 and $module_manager==0) { echo "抱歉, 您不具導師身分!"; exit(); };

//列出選單
echo $tool_bar;
?>
<form name="myform" method="post" act="<?php echo $_SEVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<?php
	if ($module_manager) {
	?>
		<select size="1" name="class_num" onchange="document.myform.act.value='';document.myform.submit()">
		<?php
		foreach ($select_class as $k=>$v) {
			?>
			<option value="<?php echo $k;?>"<?php if ($k==$class_num) echo " selected";?>><?php echo $v;?></option>
			<?php
		}
		?>
	</select>
	<?php
	} else {
		echo $select_class[$class_num]; 
	}// end if $module_manager	
	?>

	<select size="1" name="c_curr_seme" onchange="document.myform.act.value='';document.myform.submit()">
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
	 	<td width="50%" align="right">
	 		<?php
	 		/*
	 		if ($module_manager) {
	 		?>
			<input type="button" value="匯出全校幹部名單" onclick="document.myform.act.value='output_all';document.myform.submit();">	 		
			<?php
			}
			*/
			?>
			<input type="button" value="匯出本班幹部名單" onclick="document.myform.act.value='output';document.myform.submit();">
	 		<input type="button" value="儲存資料" onclick="chk_max_input();document.myform.act.value='save';document.myform.submit();">
	 	
	 	</td>
	 </tr>
	</table>
	<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'>
			<td width="30" align="center">座號</td>
			<td width="60" align="center">姓名</td>
			<td width="230" align="center">擔任幹部</td>
			<td width="230" align="center">擔任小老師</td>
			<td width="100" align="center">備註</td>
			<td>自我省思</td>
		</tr>
	
	<?php
	//開始列出學生及本學期明細
	$stud_count=0;
  while ($row=$res_stud_list->FetchRow()) {
  	$stud_count++;
  	$student_sn=$row['student_sn'];
  	$seme_num=$row['seme_num'];
  	$stud_name=$row['stud_name'];
		//取出該生幹部相關資料(包含所有學期的陣列資料, 以下只顯示本學期, 其他學期則以 hidden 方式)
		$query="select * from career_self_ponder where student_sn='$student_sn' and id='3-2'";
 		$res_ponder=$CONN->Execute($query);
 		$ponder_array=unserialize($res_ponder->fields['content']); //二維陣列
 		/***
 		陣列資料說明:
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][1幹部][1,2] 兩欄
 		  $ponder_array[學期7-1,7-2,8-,8-2,9-1,9-2等][2小老師][1,2] 兩欄
 		*/
  	?>
  	<tr>
  		<td align="center"><?php echo $seme_num;?></td>
  		<td align="center"><?php echo $stud_name;?></td>
  		<?php
  		foreach ($select_seme as $the_seme=>$V) {
  		  if ($the_seme==$c_curr_seme) {
			if($input_method){ //get_name_list_select
			?>	
					<td align='left'>1. <?php echo get_name_list_select("ponder_array[$student_sn][$select_seme_key[$c_curr_seme]][1][1]",$name_list_arr,$ponder_array[$select_seme_key[$c_curr_seme]][1][1]) ?>&nbsp;&nbsp;2. <?php echo get_name_list_select("ponder_array[$student_sn][$select_seme_key[$c_curr_seme]][1][2]",$name_list_arr,$ponder_array[$select_seme_key[$c_curr_seme]][1][2]) ?></td>
		  		<td align='left'>1. <?php echo get_name_list_select("ponder_array[$student_sn][$select_seme_key[$c_curr_seme]][2][1]",$name_list2_arr,$ponder_array[$select_seme_key[$c_curr_seme]][2][1]) ?>&nbsp;&nbsp;2. <?php echo get_name_list_select("ponder_array[$student_sn][$select_seme_key[$c_curr_seme]][2][2]",$name_list2_arr,$ponder_array[$select_seme_key[$c_curr_seme]][2][2]) ?></td>
				<td align='center'><input type="text" size="12" name="ponder_array[<?php echo $student_sn;?>][<?php echo $select_seme_key[$c_curr_seme];?>][memo]" value="<?php echo $ponder_array[$select_seme_key[$c_curr_seme]][memo];?>"<?php if ($ponder_array[$select_seme_key[$c_curr_seme]][memo]) echo " STYLE=\"background-color:#FFFF00\"";?>></td>
 					<td align='left'><?php echo $ponder_array[$select_seme_key[$c_curr_seme]][data];?></td>
			<?php } else { ?>
					<td align='left'>1. <input type="text" size="10" name="ponder_array[<?php echo $student_sn;?>][<?php echo $select_seme_key[$c_curr_seme];?>][1][1]" value="<?php echo stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][1][1]);?>"<?php if ($ponder_array[$select_seme_key[$c_curr_seme]][1][1]) echo " STYLE=\"background-color:#FFFF00\"";?>>&nbsp;／&nbsp;
													 2. <input type="text" size="10" name="ponder_array[<?php echo $student_sn;?>][<?php echo $select_seme_key[$c_curr_seme];?>][1][2]" value="<?php echo stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][1][2]);?>"<?php if ($ponder_array[$select_seme_key[$c_curr_seme]][1][2]) echo " STYLE=\"background-color:#FFFF00\"";?>></td>
		  		<td align='left'>1. <input type="text" size="10" name="ponder_array[<?php echo $student_sn;?>][<?php echo $select_seme_key[$c_curr_seme];?>][2][1]" value="<?php echo stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][2][1]);?>"<?php if ($ponder_array[$select_seme_key[$c_curr_seme]][2][1]) echo " STYLE=\"background-color:#FFFF00\"";?>>&nbsp;／&nbsp;
		  										 2. <input type="text" size="10" name="ponder_array[<?php echo $student_sn;?>][<?php echo $select_seme_key[$c_curr_seme];?>][2][2]" value="<?php echo stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][2][2]);?>"<?php if ($ponder_array[$select_seme_key[$c_curr_seme]][2][2]) echo " STYLE=\"background-color:#FFFF00\"";?>></td>
 				<td align='center'><input type="text" size="12" name="ponder_array[<?php echo $student_sn;?>][<?php echo $select_seme_key[$c_curr_seme];?>][memo]" value="<?php echo stripslashes($ponder_array[$select_seme_key[$c_curr_seme]][memo]);?>"<?php if ($ponder_array[$select_seme_key[$c_curr_seme]][memo]) echo " STYLE=\"background-color:#FFFF00\"";?>></td>
					<td align='left'><?php echo $ponder_array[$select_seme_key[$c_curr_seme]][data];?></td>
  		  <?php }
  		  } 
  		} // end foreach
  		?>
 		</tr>
  	<?php
  } // end while
	
	
	?>
	</table>
	※最多填入幹部 <?php echo $max_leader1;?> 人，最多填入小老師 <?php echo $max_leader2;?> 人
</form>

<Script language="JavaScript">
//幹部數
var MAX1=<?php echo $max_leader1;?>;
//小老師數
var MAX2=<?php echo $max_leader2;?>;

var Lead1=new Array(<?php echo $stud_count;?>);
var Lead2=new Array(<?php echo $stud_count;?>);

function chk_max_input() {
	var intMax1=0;
	var intMax2=0;
	var L1=0;
	var L2=0;
	var i=0;
	var double_leader1_txt='';
	var double_leader2_txt='';
	var double_chk=0;
   		while (i < document.myform.elements.length)  {
   			
    		if (document.myform.elements[i].name.substr(0,12)=='ponder_array') {

   				str_len=document.myform.elements[i].name.length;
   				st=str_len-6;
   				//幹部
      		if (document.myform.elements[i].name.substr(st,3)=='[1]') {
     				if (document.myform.elements[i].value!='') { 
     				double_chk=0;
     				//檢查有沒有重覆
     				if (intMax1>0 && L1>0) {
     					for (j=1;j<=L1;j++) {
     							if (document.myform.elements[i].value==Lead1[j]) { 
     								double_chk=1;
     								double_leader1_txt=double_leader1_txt+','+document.myform.elements[i].value;     								
     							}
     					}     						
     				}
     				//沒有重覆, 記下這筆資料
     				if (double_chk==0) {
     					L1=L1+1;
     					Lead1[L1]=document.myform.elements[i].value;
     				}
     				intMax1+=1;     					
     					
     				}
     			}
     			//小老師
      		if (document.myform.elements[i].name.substr(st,3)=='[2]') {
     				if (document.myform.elements[i].value!='') {
     				double_chk=0;
     				//檢查有沒有重覆
     				if (intMax2>0 && L2>0) {
     					for (j=1;j<=L2;j++) {
     							if (document.myform.elements[i].value==Lead2[j]) { 
     								double_chk=1;
     								double_leader2_txt=double_leader2_txt+','+document.myform.elements[i].value;     								
     							}
     					}     						
     				}
     				//沒有重覆, 記下這筆資料
     				if (double_chk==0) {
     					L2=L2+1;
     					Lead2[L2]=document.myform.elements[i].value;
     				}
     				intMax2+=1;     					
     					
     				}
     			}

    		}
    		i++;
    		
			}
	if (intMax1>MAX1) alert('登錄的幹部人數(共 '+intMax1+' 人)已超過允許人數(每班最多'+MAX1+'人)\n您填寫的資料仍會存入系統，但請務必確認是否符合資格認定之規定。');
	if (intMax2>MAX2) alert('登錄的小老師人數(共 '+intMax2+' 人)已超過允許人數(每班最多'+MAX2+'人)\n您填寫的資料仍會存入系統，但請務必確認是否符合資格認定之規定。');
	if (double_leader1_txt!='') alert('友善提醒：\n您登錄的幹部資料重覆了：'+double_leader1_txt.substr(1,double_leader1_txt.length-1));
	if (double_leader2_txt!='') alert('友善提醒：\n您登錄的小老師資料重覆了：'+double_leader2_txt.substr(1,double_leader2_txt.length-1));

}


</Script>
