
<?php
//$Id:  $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

//全校有幾個年級
$all_years=($IS_JHORES==0)?6:3;


if (empty($_POST['year_seme'])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$seme_year_seme=sprintf('%03d',$sel_year).$sel_seme;
} else {
	$seme_year_seme=$_POST['year_seme'];
	$sel_year=substr($_POST['year_seme'],0,3);
	$sel_seme=substr($_POST['year_seme'],3,1);
}

$class_seme_p = get_class_seme(); //學年度

//年級別, 國小一,二年級只有五個領域
$year_name=$_POST['year_name'];

 		if($year_name>2){
			$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
			$area_rowspan=9;
		} else {
			$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
			$area_rowspan=7;
		} 	



//POST後開始篩選 第一種
if ($_POST['search_mode']==1) {	
	//已POST之 subject 資料 ======================================
	foreach ($_POST['year_name'] as $k=>$v) {
  	$YEAR_NAME[$k]=$v;
	}
	//已POST之 subject 資料 ======================================
	foreach ($_POST['subject'] as $k=>$v) {
  	$subject[$k]=$v;
	}
	//比對方式
	foreach ($_POST['comp'] as $k=>$v) {
  	$comp[$k]=($v>0)?$v:0;
	}
	//分數限制
	foreach ($_POST['score'] as $k=>$v) {
  	$score[$k]=($v>0)?$v:0;
	}
	$_POST['filter_mode']=($_POST['filter_mode']=="")?"and":$_POST['filter_mode'];
}
  //============================================================
if ($_POST['act']=="Start1") {
//依勾選的年級 , 先讀取名單
//抓取班級設定裡的班級名稱
	$class_base= class_base($seme_year_seme);
//foreach ($_POST['year_name'] as $year_name) {
  $query="select a.*,b.stud_name,b.stud_person_id,b.curr_class_num from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$sn[]=$student_sn;
		$student_data[$student_sn]['seme_num']=$res->fields['seme_num'];
		$student_data[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
		$student_data[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data[$student_sn]['stud_id']=$res->fields['stud_id'];
		$seme_class=$res->fields['seme_class'];
		$student_data[$student_sn]['class_name']=$class_base[$seme_class];
		$student_data[$student_sn]['curr_class_num']=$res->fields['curr_class_num'];

		$res->MoveNext();
	}
//} // end foreach

	$semes[]=sprintf("%03d",$sel_year).$sel_seme;
	$show_year[]=$sel_year;
	$show_seme[]=$sel_seme;
	//抓取領域成績
	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$year_name),$percision);
  
  //全部先設不顯示
  foreach ($student_data as $student_sn=>$chk_score) {
    $student_data[$student_sn]['chk']=0;
  }
  
  //AND 模式
  $STUD_COUNT=0;
  if ($_POST['filter_mode']=="and") {
   //檢查所有學生的每科成績   
   foreach ($student_data as $student_sn=>$chk_score) {
    $total_subject=0;  //達條件的科目數
    //依勾選科目檢查學生的成績
   	foreach($_POST['subject'] as $k) {
   		//大於或小於
   	 	switch ($comp[$k]) {
    		case 1: //大於或等於時
          if ($fin_score[$student_sn][$k][$seme_year_seme]['score']>=$score[$k]) $total_subject++; 	  
    		break;
     		case 0: //小於
     	    if ($fin_score[$student_sn][$k][$seme_year_seme]['score']<$score[$k]) $total_subject++;
    		break; 
    	} // end switch
   	} // end foreach $_POST['subject']
     if ($total_subject==count($_POST['subject'])) { $student_data[$student_sn]['chk']=1; $STUD_COUNT++; } //登錄此生符合條件
   } //end foreach $fin_score
  } // end if and
  
  //OR 模式
  if ($_POST['filter_mode']=="or") {
   //檢查所有學生的每科成績   
   foreach ($student_data as $student_sn=>$chk_score) {
    //依勾選科目檢查學生的成績
   	foreach($_POST['subject'] as $k) {
   		//大於或小於
   	 	switch ($comp[$k]) {
    		case 1: //大於或等於時
          if ($fin_score[$student_sn][$k][$seme_year_seme]['score']>=$score[$k]) { $student_data[$student_sn]['chk']=1; $STUD_COUNT++;} 	  
    		break;
     		case 0: //小於
     	    if ($fin_score[$student_sn][$k][$seme_year_seme]['score']<$score[$k]) { $student_data[$student_sn]['chk']=1; $STUD_COUNT++;}
    		break; 
    	} // end switch
   	} // end foreach $_POST['subject']
   } //end foreach $fin_score
  } // end if and
  
} // end if ($_POST['act']=="Start1")

//============================================================================================================================
if ($_POST['act']=="Start2") {
//依勾選的年級 , 先讀取名單
//抓取班級設定裡的班級名稱
	$class_base= class_base($seme_year_seme);
//foreach ($_POST['year_name'] as $year_name) {
  //$query="select a.*,b.stud_name,b.stud_person_id,b.stud_addr_2,b.addr_zip from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
  $query="select a.*,b.stud_name,b.stud_person_id,b.curr_class_num,b.stud_addr_2,b.addr_zip,c.guardian_name from stud_seme a,stud_base b,stud_domicile c where a.student_sn=b.student_sn and b.student_sn=c.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";

	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$student_sn=$res->fields['student_sn'];
		$sn[]=$student_sn;
		$student_data[$student_sn]['seme_num']=$res->fields['seme_num'];
		$student_data[$student_sn]['stud_person_id']=$res->fields['stud_person_id'];
		$student_data[$student_sn]['stud_name']=$res->fields['stud_name'];
		$student_data[$student_sn]['stud_id']=$res->fields['stud_id'];
		$seme_class=$res->fields['seme_class'];
		$student_data[$student_sn]['class_name']=$class_base[$seme_class];
		$student_data[$student_sn]['curr_class_num']=$res->fields['curr_class_num'];
		
		$student_data[$student_sn]['stud_addr_2']=$res->fields['stud_addr_2'];
		$student_data[$student_sn]['addr_zip']=$res->fields['addr_zip'];
		$student_data[$student_sn]['guardian_name']=$res->fields['guardian_name'];

		$res->MoveNext();
	}
//} // end foreach

	$semes[]=sprintf("%03d",$sel_year).$sel_seme;
	$show_year[]=$sel_year;
	$show_seme[]=$sel_seme;
	//抓取領域成績
	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$year_name),$percision);
  
  //全部先設不顯示
  foreach ($student_data as $student_sn=>$chk_score) {
    $student_data[$student_sn]['chk']=0;
  }
  
  //確認本年級有幾個領域
  $ALL_areas=$area_rowspan-2;
  $no_succ=$_POST['no_succ']; 
  $STUD_COUNT=0;  //符合的學生數
  
   //檢查所有學生的每科成績   
   foreach ($student_data as $student_sn=>$chk_score) {
   	
       if ($no_succ<=$ALL_areas-$fin_score[$student_sn][succ]) { $student_data[$student_sn]['chk']=1; $STUD_COUNT++; } //登錄此生符合條件

   } //end foreach $fin_score
  
  
} // end if ($_POST['act']=="Start2")
//=================================================================================================================================
if($_POST['option1']=='CSV'){
	$filename=$seme_year_seme.'_'.$school_id.$school_long_name."年級學期成績篩選.csv";
	if ($year_name>2) {
	$csv_data="班級,座號,學號,身份證字號,姓名,目前班級座號,本國語文,英文,本土語言,語文平均,數學,自然與生活科技,社會,健康與體育,藝術與人文,綜合活動,領域平均,監護人,聯絡地址\r\n";
		foreach($student_data as $student_sn=>$data) { 
			if ($data['chk']==1) {
		 	$csv_data.="{$data['class_name']},{$data['seme_num']},{$data['stud_id']},{$data['stud_person_id']},{$data['stud_name']},{$data['curr_class_num']},{$fin_score[$student_sn][chinese][$seme_year_seme][score]},{$fin_score[$student_sn][english][$seme_year_seme][score]},{$fin_score[$student_sn][local][$seme_year_seme][score]},{$fin_score[$student_sn][language][$seme_year_seme][score]},";
		 	$csv_data.="{$fin_score[$student_sn][math][$seme_year_seme][score]},{$fin_score[$student_sn][nature][$seme_year_seme][score]},{$fin_score[$student_sn][social][$seme_year_seme][score]},{$fin_score[$student_sn][health][$seme_year_seme][score]},{$fin_score[$student_sn][art][$seme_year_seme][score]},{$fin_score[$student_sn][complex][$seme_year_seme][score]},{$fin_score[$student_sn][avg][score]},";
		 	$csv_data.="{$data['guardian_name']},{$data['addr_zip']}{$data['stud_addr_2']}\r\n";
	  	}
		}
  } else {
   	$csv_data="班級,座號,學號,身份證字號,姓名,目前班級座號,本國語文,英文,本土語言,語文平均,數學,健康與體育,生活,綜合活動,領域平均,監護人,聯絡地址\r\n";
		foreach($student_data as $student_sn=>$data) { 
			if ($data['chk']==1) {
		 	$csv_data.="{$data['class_name']},{$data['seme_num']},{$data['stud_id']},{$data['stud_person_id']},{$data['stud_name']},{$data['curr_class_num']},{$fin_score[$student_sn][chinese][$seme_year_seme][score]},{$fin_score[$student_sn][english][$seme_year_seme][score]},{$fin_score[$student_sn][local][$seme_year_seme][score]},{$fin_score[$student_sn][language][$seme_year_seme][score]},";
		 	$csv_data.="{$fin_score[$student_sn][math][$seme_year_seme][score]},{$fin_score[$student_sn][health][$seme_year_seme][score]},{$fin_score[$student_sn][life][$seme_year_seme][score]},{$fin_score[$student_sn][complex][$seme_year_seme][score]},{$fin_score[$student_sn][avg][score]},";
		 	$csv_data.="{$data['guardian_name']},{$data['addr_zip']}{$data['stud_addr_2']}\r\n";
	  	}
		}
  
  }
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	echo $csv_data;
	exit;
}


//因為要輸出 CSV 檔, 所以檔頭資料不能先送出 , 資料處理完再處理畫面
//秀出網頁
head("學期成績名冊 - 名單篩選");

$tool_bar=&make_menu($menu_p);

//列出選單
echo $tool_bar;

?>
<style>
 .bg_select { background-color:#FFFF00  }
 .bg_noselect { background-color:#FFFFFF  }
</style>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="">
	<font color=blue>※請選擇學期：</font>
  <select size="1" name="year_seme" onchange="document.myform.option1.value='';document.myform.submit()">
    <?php
    foreach ($class_seme_p as $k=>$v) {
    ?>
     <option value="<?php echo $k;?>"<?php if ($k==$seme_year_seme) echo " selected";?>><?php echo $v;?></option>
    <?php
    }
    ?>
  </select><br>
  <font color=blue>※請選擇篩選的條件：</font>
  <input type="radio" name="search_mode" value="1" onclick="document.myform.option1.value='';document.myform.submit();" <?php if ($_POST['search_mode']=="1") echo " checked";?>>領域成績 
  <input type="radio" name="search_mode" value="2" onclick="document.myform.option1.value='';document.myform.submit();" <?php if ($_POST['search_mode']=="2") echo " checked";?>>未達標準的「領域總數」
<?php	



if ($_POST['search_mode']==1) {	

?>
<br>
<font color=blue>※請勾選要篩選年級及領域：</font>
<?php
  			for($i=1;$i<=$all_years;$i++) {
  				$Y=$i+$IS_JHORES;
  			 ?>
  			  <input type="radio" name="year_name" value="<?php echo $Y;?>"<?php if ($_POST['year_name']==$Y) echo " checked";?> onclick="document.myform.option1.value='';document.myform.submit();"><?php echo $school_kind_name[$Y]."級"; ?>
  			  <?php
  		  } // end for

 if (!$year_name)  exit();

if ($IS_JHORES==0) echo "<br>(※注意! 篩選國小低年級時, 請勿勾選「社會」和「藝術與人文」領域)";

?>
<table border="1" style="border-collapse:collapse" bordercolor="#800000">
  <tr bgcolor="#FFCCFF">
  <td align="center">勾選</td>
  <td align="center">領域別</td>
  <td align="center">單科篩選條件</td>
	<td align="center">分數</td>
 </tr>
 <?php
  foreach ($link_ss as $k=>$v) {
    $score[$k]=($score[$k]=="")?60:$score[$k];
    $v=str_replace("鄉土語文","本土語言",$v);
 ?>
  <tr id="<?php echo $k;?>" class="bg_noselect">
  <td align="center"><input type="checkbox" name="subject[<?php echo $k;?>]" value="<?php echo $k;?>"<?php if ($subject[$k]==$k) echo " checked";?> onclick="check_select_bg()"></td>
  <td><?php echo $v;?></td>
  <td><input type="radio" name="comp[<?php echo $k;?>]" value="1"<?php if ($comp[$k]==1) echo " checked";?>>＞＝ <input type="radio" name="comp[<?php echo $k;?>]" value="0" <?php if ($comp[$k]==0) echo " checked";?>>＜</td>
	<td><input type="text" name="score[<?php echo $k;?>]" value="<?php echo $score[$k];?>" size="5"></td>
 </tr>
 <?php
 } // end foreach
 ?> 
</table>
==> 科目間的條件：<input type="radio" name="filter_mode" value="and"<?php if ($_POST['filter_mode']=="and") echo " checked";?>>AND<input type="radio" name="filter_mode" value="or"<?php if ($_POST['filter_mode']=="or") echo " checked";?>>OR
<input type="button" value="開始篩選" name="btn" onclick="document.myform.option1.value='';check_select()">
<?php 
  if ($STUD_COUNT>0) {
    ?>
    <input type="button" value="CSV輸出" onclick="document.myform.act.value='Start1';document.myform.option1.value='CSV';document.myform.submit()">
    <?php
  }
} // end if search_mode==1


if ($_POST['search_mode']==2) {
	$no_succ=(isset($_POST['no_succ']))?$_POST['no_succ']:0;
	?>
	<br>
<font color=blue>※請勾選年級：</font>
<?php 
  			for($i=1;$i<=$all_years;$i++) {
  				$Y=$i+$IS_JHORES;
  			 ?>
  			  <input type="radio" name="year_name" value="<?php echo $Y;?>"<?php if ($_POST['year_name']==$Y) echo " checked";?> onclick="document.myform.option1.value='';document.myform.submit();"><?php echo $school_kind_name[$Y]."級"; ?>
  			  <?php
  		  } // end for
  		?>
<?php
 if (!$year_name)  exit();
?>
<br><br>
 條件：篩選學期成績中 <input type="text" name="no_succ" value="<?php echo $_POST['no_succ'];?>" size="3">個領域(含)以上未達及格標準(60分)的學生。<br><br>
 
<input type="button" value="開始篩選" name="btn" onclick=" if (document.myform.no_succ.value>0) { document.myform.option1.value='';document.myform.act.value='Start2';document.myform.submit(); }">
<?php 
  if ($STUD_COUNT>0) {
    ?>
    <input type="button" value="CSV輸出" onclick="document.myform.act.value='Start2';document.myform.option1.value='CSV';document.myform.submit()">
    <?php
  }


}	 // end if search_mode==2

?>
</form>
<?php
//畫面呈現
if ($STUD_COUNT>0) {
$smarty->assign("show_year",$show_year);
$smarty->assign("show_seme",$show_seme);
$smarty->assign("semes",$semes);
$smarty->assign("curr_seme",$semes[0]);
$smarty->assign("fin_score",$fin_score);
$smarty->assign("student_data_nor",$student_data_nor);
$smarty->assign("ss_link",$ss_link);
$smarty->assign("link_ss",$link_ss);
$smarty->assign("rule",$rule_all);
$smarty->assign("year_name",$year_name);
$smarty->assign("percision_radio",$percision_radio);
$smarty->assign("student_data",$student_data);
$smarty->assign("m_arr",$m_arr);
$smarty->assign("school_long_name",$school_long_name);
$smarty->display("score_report_filter.tpl");
}

?>


<Script Language="JavaScript">
	
	check_select_bg();
	
   function check_select() {
     var start=0;
     var year_name=0;
     var i=0;
     while (i < document.myform.elements.length) {
       if (document.myform.elements[i].name.substr(0,7)=='subject') {
         if (document.myform.elements[i].checked==true) {
           start=1;
         }
       }
       if (document.myform.elements[i].name.substr(0,9)=='year_name') {
         if (document.myform.elements[i].checked==true) {
           year_name=1;
         }
       }
       i++;
     } // end while
     if (start==1 && year_name==1) {
     	document.myform.act.value="Start1";
     	document.myform.submit();
     } else {
     	if (year_name==0) alert ('未勾選年級!');
      if (start==0) alert ('未勾選科目!');
     }
   }
   
   function check_select_bg() {
   	var i=0;
     while (i < document.myform.elements.length) {
       if (document.myform.elements[i].name.substr(0,7)=='subject') {
       	   wl=document.myform.elements[i].name.length-9;
         	 w=document.myform.elements[i].name.substr(8,wl);
         if (document.myform.elements[i].checked==true) {
         	 document.getElementById(w).className = 'bg_select';           
         } else {
         	 document.getElementById(w).className = 'bg_noselect';           
         }
       }
       i++;
     } // end while
   }
 </Script>