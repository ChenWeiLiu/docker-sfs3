<?php
//取得設定檔
include_once "config.php";


sfs_check();

//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取目前操作的老師有沒有管理權
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

$R_select=explode(',',$rank_select);
$N_select=explode(',',$nature_select);

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期 , 若有選定則以選定的學期作為比對學生班級座號的依據, 否則以最新學期的個資為準
$c_curr_seme=sprintf('%03d%1d',$curr_year,$curr_seme);

//取得目前所有班級
$class_array=class_base();

//預設並未選定學生
$start=0;

/** submit 後的動作 **************************************************/
//儲存一筆
if ($_POST['act']=='save') {
	
 	//$student_sn=$_POST['student_sn'];
	$level=$_POST['level'];
	$squad=$_POST['squad'];
	$name=$_POST['r_name'];
	$rank=$_POST['rank'];
  $certificate_date=$_POST['certificate_date'];
	$sponsor=$_POST['sponsor'];
	$memo=$_POST['memo'];
	$word=strip_tags(trim($_POST['word']));
	$weight=$_POST['weight'];
	$weight_tech=$_POST['weight_tech'];
	$year=$_POST['year'];
	$nature=$_POST['nature'];
	$i=0;
	foreach ($_POST['selected_students'] as $student_sn) {
		$query="insert into career_race set student_sn='$student_sn',level='$level',squad='$squad',name='$name',
		rank='$rank',certificate_date='$certificate_date',sponsor='$sponsor',memo='$memo',
		word='{$word}', weight='{$weight}', weight_tech='{$weight_tech}',year='$year',nature='$nature' ,	update_sn='".$_SESSION['session_tea_sn']."'";
   		if (!mysql_query($query)) {
   		 $MSG="儲存資料失敗!";
   		  echo $query;die($MSG);
			} 
		$i++;
	} // end foreach
		
	$INFO="已於".date("Y-m-d H:i:s")."儲存".$i."筆記錄.";
	
}
//

/**************** 開始秀出網頁 ******************/
//秀出 SFS3 標題
head();

//列出選單
echo $tool_bar;

//print_r($class_array);

?>
<form name="myform" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
	<input type="hidden" name="act" value="">
	<input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">

<table border="0" width="100%" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC">
<tr>
  <td>
	<!-- 登錄表單  -->
	<table border="0" bgcolor="#ffffff" style="border-collapse:collapse" bordercolor="#800000">
		<tr>
			<td style="color:#800000">
				<fieldset style="line-height: 150%; margin-top: 0; margin-bottom: 0">
				<legend><font size=2 color=#0000dd>請輸入競賽細目</font></legend>
				<?php
					 	//預設值
					 	$race_record['year']=date("Y")-1911;
						$race_record['level']=5;	
						$race_record['squad']=1;
						$race_record['weight']=1;
						$race_record['weight_tech']=1;
						$race_record['certificate_date']=date("Y-m-d");
						form_race_record($race_record);
					?>
			<input type="button" value="儲存記錄" onclick="check_save()">
				<?php
					if (substr($sch_id,0,2)=='13') {
					 echo "<br><font size=\"2\">※說明：屏東區的「競賽類別」及「得獎名次」無法經由模組變數自行設定。</font>";
					}
					?>
			</fieldset>
			</td>
		</tr>
	</table>
	</td>	
</tr>
<tr>
   <td>◎預選學生名單(請進行最後勾選再按下「儲存記錄」)</td>
 </tr>
 <tr>
   <td>
   	<table border="2" style="border-collapse:collapse" bordercolor="#111111" bgcolor="#FFDDDD" width="100%">
   		<tr>
   			<td><span id="show_selected_students">目前無預選名單</span></td>
   		</tr>
   	</table>   
   </td>
 </tr>
 <tr>
  <td style="color:#FF0000;font-size:10pt"><?php echo $INFO;?></td>
 </tr>
</table>
</form>

<form method="post" name="myform2" action="<?php echo $_SERVER['php_self'];?>">
<table border="0">
 <tr>
  <td>◎選擇班級
  	<select name="the_class" size="1" id="the_class">
  	 <option value="">請選擇班級</option>
					<?php
					 foreach ($class_array as $k=>$v) {
					 ?>
					 <option value="<?php echo $k;?>" ><?php echo $v;?></option>
					 <?php
					 }
					?>  	 
  	</select> <input type="button" id="chk_all" value="全選"><input type="button" id="chk_all_clear" value="全不選">
  	</td>	
 </tr>
 <tr>
 	<td>
 	
 		<span id="the_students"></span>
 	</td>
 </tr>
 <tr>
   <td><input type="button" value="預選這些學生" id="btn_select_students"></td>
 </tr>
	<tr>
	<td style="font-size:10pt;color:blue">※使用說明：</td>
	</tr>
	<tr>
	<td style="font-size:10pt;color:blue">1.本程式僅適用於登錄團體記錄，欲進行個別記錄的增刪或修改，請利用<a href='cr_input.php'>個別登錄功能</a>。</td>
	</tr>
	<tr>
	<td style="font-size:10pt;color:blue">2.使用時請先輸入競賽細目，接著利用「◎選擇班級／勾選學生」功能，選擇要登錄此筆記錄的學生。</td>
	</tr>
	<tr>
	<td style="font-size:10pt;color:blue">3.按下「預選這些學生」，被勾選的名單會放入「預選名單」視窗。</td>
	</tr>
	<tr>
	<td style="font-size:10pt;color:blue">4.確認要登錄的學生名單都在「預選名單」視窗中，即可按下「儲存記錄」，替這些學生建立此筆記錄。</td>
	</tr>

</table>

</form>
<?php
//若為屏東
if (substr($sch_id,0,2)=='13') {
?>
<script type='text/javascript' src='select_race_option.js'></script>
<?php
 }
?>
<Script>
 //檢測資料是否完整
 function check_save() {
 	var ok=1;
 	if (document.myform.r_name.value=='') {
 		ok=0;
 		alert('請輸入競賽名稱');
 		document.myform.r_name.focus();
 		return false;
 	}
 	if (document.myform.rank.value=='') {
 		ok=0;
 		alert('請輸入得獎名次, 如「第1名」、「優等」....等。');
 		document.myform.rank.focus();
 		return false;
 	}
 	if (document.myform.certificate_date.value=='') {
 		ok=0;
 		alert('請輸入證書日期');
 		document.myform.certificate_date.focus();
 		return false;
 	}
 	if (document.myform.sponsor.value=='') {
 		ok=0;
 		alert('請輸入舉辦單位');
 		document.myform.sponsor.focus();
 		return false;
 	}
 	
 	if (ok==1) {
 		document.myform.act.value='save';
 		document.myform.submit();
 	}
 	
 }

 $("#the_class").change(function(){
    $.ajax({
   	type: "post",
    url: 'ajax_return_students.php',
    data: { the_class: $('#the_class').val() , pre_selected:$('#pre_selected').val() },
    error: function(xhr) {
      alert('ajax request 發生錯誤, 無法取得學生名單!');
    },
    success: function(response) {
    	$('#the_students').html(response);
      $('#the_students').fadeIn();      
    }
   });   // end $.ajax
 }); // end #the_class

 $("#btn_select_students").click(function(){
 	//處理勾選的名單, 做成陣列
 	var selectedItems = new Array();
 		$("input[name*='chk_student[]']:checked").each(function() {
 					selectedItems.push($(this).val());
 		});

 if (selectedItems .length == 0)
     alert("請勾選學生");
 else
 	
 	　//傳送被勾選的名單(轉成以;隔開的字申)及已預選(pre_selected)的 hidden 值
    $.ajax({
   	type: "post",
    url: 'ajax_select_students.php',
    data: { items:selectedItems.join(';') , pre_selected:$('#pre_selected').val() },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤, 無法取得學生名單!');
    },
    success: function(response) {
    	$('#show_selected_students').html(response);
      $('#show_selected_students').fadeIn(); 
      //最後傳回名單的 table  及 <input type="hidden" name="pre_selected">
    }
   });   // end $.ajax
 }); // end #the_class

//全選
$("#chk_all").click(function(){
  $(".chk_student").attr("checked","true");
});

//全不選
$("#chk_all_clear").click(function(){
  $(".chk_student").attr("checked","");
});


</Script>
