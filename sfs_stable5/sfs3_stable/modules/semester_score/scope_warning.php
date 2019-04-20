
<?php
//$Id:  $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

//全校有幾個年級
$all_years=($IS_JHORES==0)?6:3;

	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$seme_year_seme=sprintf('%03d',$sel_year).$sel_seme;

//取得學校日期設定
$db_date=curr_year_seme_day($sel_year,$sel_seme);  //$db_date['start'] , $db_date['end'] , $db_date['st_start'] , $db_date['st_end']
$st_end_line=date("U",mktime(0,0,0,substr($db_date['st_end'],5,2),substr($db_date['st_end'],8,2),substr($db_date['st_end'],0,4)));
$now=date("U",mktime(0,0,0,substr(date("Y-m-d"),5,2),substr(date("Y-m-d"),8,2),substr(date("Y-m-d"),0,4)));

//計算各年級要統計的學期
$Year_scan=array();
for($i=1;$i<=$all_years;$i++) {
 $Y=$i+$IS_JHORES;
  switch ($IS_JHORES) {
  	case '0':
  	    if ($Y>1) {
  	     for ($j=$Y-1;$j>=1;$j--) {
  	      $chk_year=curr_year()-$j;
  	      $Year_scan[$Y].=",".$chk_year."1";
  	      $Year_scan[$Y].=",".$chk_year."2";
  	     }
  	    }
  	break;
  	case '6':
  	    if ($Y>7) {
  	     for ($j=$Y-1;$j>=7;$j--) {
  	      $chk_year=curr_year()-$j+6;
  	      $Year_scan[$Y].=",".$chk_year."1";
  	      $Year_scan[$Y].=",".$chk_year."2";
  	     }
  	    }
  	 
  	break;  
  }
  //如果是下學期，加1
 				if (curr_seme()==2) $Year_scan[$Y].=",".curr_year()."1";
 	//如果已過結業式，加本學期
				if ($now>$st_end_line or $_POST['force_this_seme']) $Year_scan[$Y].=",".curr_year().curr_seme();
	$Year_scan[$Y]=substr($Year_scan[$Y],1);
}  				


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



//============================================================================================================================
if ($_POST['act']=="start") {
//依勾選的年級 , 先讀取名單
//抓取班級設定裡的班級名稱
	$class_base= class_base($seme_year_seme);
//foreach ($_POST['year_name'] as $year_name) {
  //$query="select a.*,b.stud_name,b.stud_person_id,b.stud_addr_2,b.addr_zip from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";
  $query="select a.*,b.stud_name,b.stud_person_id,b.stud_addr_2,b.addr_zip,c.guardian_name from stud_seme a,stud_base b,stud_domicile c where a.student_sn=b.student_sn and b.student_sn=c.student_sn and a.seme_year_seme='$seme_year_seme' and a.seme_class like '$year_name%' and b.stud_study_cond in ('0','15') order by a.seme_class,a.seme_num";

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
		
		$student_data[$student_sn]['stud_addr_2']=$res->fields['stud_addr_2'];
		$student_data[$student_sn]['addr_zip']=$res->fields['addr_zip'];
		$student_data[$student_sn]['guardian_name']=$res->fields['guardian_name'];

		$res->MoveNext();
	}
//} // end foreach

	//$semes[]=sprintf("%03d",$sel_year).$sel_seme;
	$semes=explode(',',$Year_scan[$year_name]);
/*
echo "<pre>";
print_r($semes);
echo "</pre>";
exit();
 */
  
	$show_year[]=$sel_year;
	$show_seme[]=$sel_seme;
	//抓取領域成績
	//echo "start<br>";
	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$year_name),$percision);
	//echo "end<br>";
	//echo "<pre>";
//print_r($fin_score);
//echo "</pre>";
//exit();
  
  //全部先設不顯示
  foreach ($student_data as $student_sn=>$chk_score) {
    $student_data[$student_sn]['chk']=0;
  }
  
  //確認本年級有幾個領域
  $ALL_areas=$area_rowspan-2;
  //$no_succ=$_POST['no_succ']; 
  $STUD_COUNT=0;  //符合的學生數
  
   //檢查所有學生的每科成績   
   foreach ($student_data as $student_sn=>$chk_score) {
   	
       if ($fin_score[$student_sn][succ]<4) { $student_data[$student_sn]['chk']=1; $STUD_COUNT++; } //少於４科及格

   } //end foreach $fin_score
  
  
} // end if ($_POST['act']=="Start")
//=================================================================================================================================
if($_POST['option1']=='CSV'){
	$filename=$seme_year_seme.'_'.$school_id.$school_long_name."年級修業示警名單篩選.csv";
	if ($year_name>2) {
	$csv_data="班級,座號,學號,身份證字號,姓名,本國語文,英文,本土語言,語文平均,數學,自然與生活科技,社會,健康與體育,藝術與人文,綜合活動,領域平均,監護人,聯絡地址\r\n";
		foreach($student_data as $student_sn=>$data) { 
			if ($data['chk']==1) {
		 	$csv_data.="{$data['class_name']},{$data['seme_num']},{$data['stud_id']},{$data['stud_person_id']},{$data['stud_name']},{$fin_score[$student_sn][chinese][avg][score]},{$fin_score[$student_sn][english][avg][score]},{$fin_score[$student_sn][local][avg][score]},{$fin_score[$student_sn][language][avg][score]},";
		 	$csv_data.="{$fin_score[$student_sn][math][avg][score]},{$fin_score[$student_sn][nature][avg][score]},{$fin_score[$student_sn][social][avg][score]},{$fin_score[$student_sn][health][avg][score]},{$fin_score[$student_sn][art][avg][score]},{$fin_score[$student_sn][complex][avg][score]},{$fin_score[$student_sn][avg][score]},";
		 	$csv_data.="{$data['guardian_name']},{$data['addr_zip']}{$data['stud_addr_2']}\r\n";
	  	}
		}
  } else {
   	$csv_data="班級,座號,學號,身份證字號,姓名,本國語文,英文,本土語言,語文平均,數學,健康與體育,生活,綜合活動,領域平均,監護人,聯絡地址\r\n";
		foreach($student_data as $student_sn=>$data) { 
			if ($data['chk']==1) {
		 	$csv_data.="{$data['class_name']},{$data['seme_num']},{$data['stud_id']},{$data['stud_person_id']},{$data['stud_name']},{$fin_score[$student_sn][chinese][avg][score]},{$fin_score[$student_sn][english][avg][score]},{$fin_score[$student_sn][local][avg][score]},{$fin_score[$student_sn][language][avg][score]},";
		 	$csv_data.="{$fin_score[$student_sn][math][avg][score]},{$fin_score[$student_sn][health][avg][score]},{$fin_score[$student_sn][life][avg][score]},{$fin_score[$student_sn][complex][avg][score]},{$fin_score[$student_sn][avg][score]},";
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
head("學期成績名冊 - 修業預警名單篩選");

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
	<font color=blue>※篩選截至<?php echo curr_year();?>學年度第<?php echo curr_seme();?>學期止可能無法畢業的名單：</font>
　<br>
	<table border="1" width="600" style="border-collspae:collapse">
  	<tr bgcolor="#FFFFDD" style="color:#800000;font-size:10pt" >
  		<td>
  依據教育部研擬「國中小學生成績評量準則」明定核發畢業證書的統一條件，學生必須七大學習領域有四項以上畢業總平均在六十分以上
	。本功能協助篩選目前在學期間學習領域總平均已有四科不及格的學生，方便學校善盡提醒責任。<br>
	注意！若有學生為中途休學再復學著，請確認該生的就學期間成績為連續學期，否則可能會有統計誤差情況發生。
		 </td>
		</tr>
	</table>
<br>
<font color=blue>※請勾選要篩選的年級：</font><br>
<?php
  			for($i=1;$i<=$all_years;$i++) {
  				$Y=$i+$IS_JHORES;
  				if ($Year_scan[$Y]) {
  					
  				//$semes=($i-1)*2+1;
  			 ?>
  			  <input type="radio" name="year_name" value="<?php echo $Y;?>"<?php if ($_POST['year_name']==$Y) echo " checked";?> onclick="document.myform.act.value='';document.myform.option1.value='';document.myform.submit();"><?php echo $school_kind_name[$Y]."級"; ?>
  			  (統計就學 <?php echo $Year_scan[$Y];?> 學期的平均)
  			  <br>
  			  <?php
  			  }
  		  } // end for
//強制計算本學期
if ($now<=$st_end_line) {
	?>
		<br><input type="checkbox" name="force_this_seme" value="1" <?php if ($_POST['force_this_seme']) echo " checked";?> onclick="document.myform.act.value='';document.myform.option1.value='';document.myform.submit();">強制計算至本學期 <?= curr_year().curr_seme()?> ( <span style="color:#FF0000">注意! 本學期成績若未完整，所列名單並非是正確資料，請謹慎使用！</span>)<br>
	<?php
}
 if (!$year_name)  exit();
?>
<br>
<input type="button" value="開始篩選" name="btn" onclick="document.myform.act.value='start';check_select()">
<br><br>
<?php
 if ($_POST['act']=='') {
?>
<table border="1" width="500" style="border-collspae:collapse">
  <tr bgcolor="#000000" style="color:#FFFFCC">
  	<td>注意！由於本程式運算需要大量記憶體，針對大型學校，程式執行到一半可能會發生中斷情況（畫面呈現空白），此時請網管人員調整 php.ini 中 memory_limit 的設定值，將預設值 128MB 改為 256MB 即可。</td>
  </tr>
</table>
<?php 
 }
  if ($STUD_COUNT>0) {
    ?>
    <input type="button" value="CSV輸出" onclick="document.myform.act.value='start';document.myform.option1.value='CSV';document.myform.submit()">
    共計 <?php echo $STUD_COUNT."位學生";
  }

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
$smarty->display("score_report_warning.tpl");
}

?>


<Script Language="JavaScript">

   function check_select() {
     var year_name=0;
     var i=0;
     while (i < document.myform.elements.length) {
       
       if (document.myform.elements[i].name.substr(0,9)=='year_name') {
         if (document.myform.elements[i].checked==true) {
           year_name=1;
         }
       }
       i++;
     } // end while
     if (year_name==1) {
     	document.myform.submit();
     } else {
     	if (year_name==0) alert ('未勾選年級!');
      
     }
   }
   
 </Script>