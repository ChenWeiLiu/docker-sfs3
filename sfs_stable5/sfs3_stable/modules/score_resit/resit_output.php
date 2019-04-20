<?php	
header('Content-type: text/html;charset=big5');
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";
include_once "../makeup_exam/my_fun.php";
//驗證是否登入
sfs_check(); 
//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取補考學期別設定
$sql="select * from resit_seme_setup limit 1";
$res=$CONN->Execute($sql);
$SETUP=$res->fetchrow();
$C_year_seme=substr($SETUP['now_year_seme'],0,3)."學年度 第 ".substr($SETUP['now_year_seme'],-1)." 學期";


//目前處理的學年學期
$sel_year = substr($SETUP['now_year_seme'],0,3);
$sel_seme = substr($SETUP['now_year_seme'],-1);

//已選定的年級
$Cyear=$_POST['Cyear'];
 		if($Cyear>2){
			$ss_link=array("語文"=>"language","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
		} else {
			$ss_link=array("語文"=>"language","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
		}

//確認可補考的年級
//例如: 以國中而言, 現今學年 103 , 若啟用 102學年, 只能考該年的一年級和二年級, 因為三年級已畢業
// 國中或國小判定 $IS_JHORES=6 (國中) , $IS_JHORES=0 (國小)
if ($IS_JHORES==6) {
	$SY=$curr_year-3;   //以103為例, 基準點為 100
} else {
	$SY=$curr_year-6;   //以103為例, 基準點為 97
}

//製作年級選單
$sy_circle=$sel_year-$SY;	
$now_cy=3-$sy_circle;

// ajax 檢視已補考成績
if ($_POST['act']=='output_resit_score') {
 	//領域別
 	// $Cyesr : 年級
 	$i=0;
	$scope=$_POST['scope'];
	$seme_year_seme=$SETUP['now_year_seme'];
  //抓取班級設定裡的班級名稱
	$class_base= class_base($curr_year_seme);
	$sql="select a.*,c.stud_id,c.stud_name,c.curr_class_num from resit_exam_score a,resit_paper_setup b,stud_base c where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.student_sn=c.student_sn and complete='1' order by curr_class_num";
	$res=$CONN->Execute($sql) or die($sql);
	while ($row=$res->FetchRow()) {
		$i++;
		$student_sn=$row['student_sn'];
		$curr_class_num=$row['curr_class_num'];
		$seme_class=substr($curr_class_num,0,3);
		$seme_num=substr($curr_class_num,-2);
		
		$main.="
			<tr>
	     <td style='font-size:10pt' align='center'><input type='checkbox' name='score_sn[]' value='".$row['sn']."' checked></td>
	     <td style='font-size:10pt' align='center'>".$class_base[$seme_class]."</td>
	     <td style='font-size:10pt' align='center'>".$seme_num."</td>
	     <td style='font-size:10pt' align='center'>".$row['stud_name']."</td>
	     <td style='font-size:10pt' align='center'>".$row['org_score']."</td>
	     <td style='font-size:10pt".(($row['score']<60)?";color:red":"")."' align='center'>".$row['score']."</td>
	     <td style='font-size:9pt'>".$row['entrance_time']."</td>		
	     <td style='font-size:9pt'>".$row['complete_time']."</td>		
			</tr>
		";

	}
	  $main="	  
	 <input type='hidden' name='scope' value='$scope'>
	 <table border=\"0\" width=\"100%\" cellspacing=\"3\" cellpadding=\"2\">
  	<tr>
   	  <td colspan='5' style='color:#800000'><b>".$link_ss[$scope]."領域</b> - [<font color=blue>已補考</font>]名單 , 共計 $i 位</td>
   	</tr>
	   <tr bgcolor=\"#FFCCCC\">
	   	 <td style='font-size:10pt'>勾選</td>
	     <td style='font-size:10pt'>班級</td>
	     <td style='font-size:10pt'>座號</td>
	     <td style='font-size:10pt'>姓名</td>
	     <td style='font-size:10pt'>原成績</td>
	     <td style='font-size:10pt'>補考成績</td>
	     <td style='font-size:10pt'>領卷時間</td>
	     <td style='font-size:10pt'>完成時間</td>
	   </tr>
	 ".$main."
	 </table>"; 
 
  echo $main;
  exit();

}

//匯出分數
if ($_POST['act']=='output_resit_score_submit') {
	$scope=$_POST['scope'];
	$seme_year_seme=$SETUP['now_year_seme'];
	$paper_setup=get_paper_sn($seme_year_seme,$Cyear,$scope);
	$data_arr=array();
	//陣列內容 data_arr[$student_sn][$seme_year_seme][$scope_ename]=$score
	foreach ($_POST['score_sn'] as $score_sn) {
	 $sql="select * from resit_exam_score where sn='$score_sn'";
   $res=$CONN->Execute($sql) or die ("讀取分數資料發生錯誤! SQL=".$sql);
   while ($row=$res->fetchRow()) {	 
	  $student_sn=$row['student_sn'];
	  $org_score=$row['org_score'];
	 //檢查 makeup_exam 裡的 makeup_exam_scope 有沒有確認名單
		$sql_check="select * from makeup_exam_scope where student_sn='$student_sn' and seme_year_seme='$seme_year_seme' and scope_ename='$scope'";
		$res_check=$CONN->Execute($sql_check);
		if ($res_check->recordCount()==0 and $_POST['auto_insert_makeup_exam_list']==1) {
			$query="insert into makeup_exam_scope (seme_year_seme,student_sn,scope_ename,class_year,oscore) values ('".$seme_year_seme."','".$student_sn."','".$scope."','".$Cyear."','$org_score')";
			$res_insert=$CONN->Execute($query) or die("自動於 makeup_exam 模組建立評量名冊失敗！SQL=".$query);
		}
    $data_arr[$student_sn][$seme_year_seme][$scope]=($row['score']>100)?100:$row['score'];
	 } // end while
	} // end foreach
	
	$SUCC=import_makeup_exam($data_arr);
  $INFO="已成功匯入 ".$SUCC." 筆".$link_ss[$scope]."領域的補考成績資料！";
}

$class_year_list="
  <select size='1' name='Cyear' onchange='this.form.submit()'>
   <option value=''>請選擇年級</option>";
   for ($i=1;$i<=$sy_circle;$i++) {
    $CY=$i+$IS_JHORES;
    $NCY=$CY+$now_cy;
    $class_year_list.="<option value='$CY'".(($CY==$Cyear)?" selected":"").">".$school_kind_name[$CY]."級 (目前就讀".$school_kind_name[$NCY]."級)</option>";
   }    
$class_year_list.="
  </select>
";

//計算各領域不及格人數
if ($Cyear!="") {
		if ($_POST['act']=='get_all_resit_name') {
		 $all_students=count_scope_fail($Cyear,$SETUP['now_year_seme'],$ss_link,$link_ss);
		 $INFO="該學年學生總數 $all_students 人，已自學期成績資料庫中更新補考名單!";		 
	  } 
	  	$seme_year_seme=$SETUP['now_year_seme'];
	   foreach ($ss_link as $scope) {
	   	//不及格人數
	     $sql="select count(*) as num from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope'";
			 $res=$CONN->Execute($sql) or die ("讀取人數發生錯誤！SQL=".$sql);
			 $fail['still'][$scope]=$res->fields['num'];
			//已補考人數 
	     $sql="select count(*) as num from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.complete='1'";
			 $res=$CONN->Execute($sql) or die ("讀取人數發生錯誤！SQL=".$sql);
			 $fail['tested'][$scope]=$res->fields['num'];
			//待補考人數
	     $sql="select count(*) as num from resit_exam_score a,resit_paper_setup b where a.paper_sn=b.sn and b.seme_year_seme='$seme_year_seme' and b.class_year='$Cyear' and b.scope='$scope' and a.complete='0'";
			 $res=$CONN->Execute($sql) or die ("讀取人數發生錯誤！SQL=".$sql);
			 $fail['ready'][$scope]=$res->fields['num'];			 
	   } // end foreach	   
		
} // end if $Cyear!="";


/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();
//列出選單
echo $tool_bar;
?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="opt1" value="">
	<input type="hidden" name="opt2" value="">
<?php
 echo "<font color=red>補考學期別：".$C_year_seme."</font><br>";
 echo "請選擇要匯出成績的年級：".$class_year_list;
 
 if ($Cyear!="") { 
 	?>
 <table border="0">
  <tr>
  	<!--左畫面 -->
    <td valign="top">
 	  <table border="1" style="border-collapse:collapse;font-size:10pt" bordercolor="#111111" cellpadding="3">
 		<tr bgcolor="#FFCCFF">
 			<td>領域別</td>
 			<td>不及格</td>
 			<td>已補考</td>
 			<td>待補考</td>
 			<td>匯出操作</td>
 		</tr>
 		<?php
 		foreach ($ss_link as $k=>$v) {
 		  ?>
 		  <tr>
 		    <td><?php echo $k;?></td>
 		    <td><?php echo $fail['still'][$v];?></td>
 		    <td><?php echo $fail['tested'][$v];?></td>
 				<td><?php echo $fail['ready'][$v];?></td>
 				<td>
 					<input type="button" value="匯出成績" class="output_resit_score" id="<?php echo $v;?>">
 				</td>
 		  </tr>
 		  <?php
 		} 		
 		?>
 	  </table>
 		<font size='2' color='#0000cc'>
      <img src='./images/filefind.png'>說明:<br>
   1.本處匯出成績並非將成績直接匯入學期成績資料庫。<br>
   2.您必須安接「補行評量成績作業(makeup_exam)」模組，<br>
   本系統會將成績匯出至該模組，請您再利用該模組處理成績。<br>
	 3.請放心，重復匯入僅將成績覆寫，並不會有其他錯誤。
   </font>
   <br>
   <br>
   <font color=red><?php echo $INFO;?></font>
   <br>
   	 <div id="output_submit" style="display:none">
   	 	<input type="button" style="color:#FF0000" value="確認無誤，匯出勾選的成績" id="output_resit_score_submit">
		 <br><input type="checkbox" name="auto_insert_makeup_exam_list" value="1" checked>當補行評量名冊中無此名單時自動新增
     </div>
    </td>
  	<!--右畫面 -->
    <td valign="top">
		<span id="show_right"></span>
    </td>
 </table> 	
 	<?php
 } //end if $Cyear 
?>
</form>
<?php
//  --程式檔尾
foot();
?>

<Script>

//匯出已補考成績
$(".output_resit_score").click(function(){
	var scope=$(this).attr("id");
	var act='output_resit_score';
	var Cyear='<?php echo $_POST['Cyear'];?>';
  
    $.ajax({
   	type: "post",
    url: 'resit_output.php',
    data: { act:act,scope:scope,Cyear:Cyear },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤!');
    },
    success: function(response) {
    	$('#show_right').html(response);
      $('#show_right').fadeIn(); 
			output_submit.style.display='block';
    } // end success
	});   // end $.ajax

})

//匯出已補考成績
$("#output_resit_score_submit").click(function(){

	document.myform.act.value='output_resit_score_submit';
	document.myform.submit();
	
})


</Script>