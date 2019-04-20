<?php

// $Id: reward_one.php 6735 2012-04-06 08:14:54Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();
?>
<script type="text/javascript" src="./include/functions.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/jscal2.js"></script>
<script type="text/javascript" src="./include/JSCal2-1.9/src/js/lang/b5.js"></script>
<link type="text/css" rel="stylesheet" href="./include/JSCal2-1.9/src/css/jscal2.css">

<?php

//秀出網頁
head("認證服務學習時數");

$tool_bar=&make_menu($school_menu_p);

//讀取服務類別 $ITEM[0],$ITEM[1].....
$M_SETUP=get_module_setup('stud_service');
$ITEM=explode(",",$M_SETUP['item']);

//列出選單
echo $tool_bar;
	
//取得資料庫中所有學期資料, 每年有兩個學期
$class_seme_p = get_class_seme(); //學年度	
$class_seme_p=array_reverse($class_seme_p,1);
//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$c_curr_seme=($_POST['c_curr_seme']=='')?$curr_year.$curr_seme:$_POST['c_curr_seme'];

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}
//預設列出未認證的資料
$_POST['listmode']=($_POST['listmode']==0)?2:$_POST['listmode'];

//按下確認
if ($_POST['confirm']==1 or $_POST['confirm']==-1) {
 $confirm=($_POST['confirm']+1)/2;
 foreach ($_POST['confirm_check'] as $sn) {
  $query="update stud_service set confirm='$confirm',confirm_sn='".$_SESSION['session_tea_sn']."' where sn='$sn'";
  mysql_query($query); 
 } 
}

?>
<!--- 輸入表單 --->
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>" name="myform">
	<input type="hidden" name="confirm" value="0">
<table border="0" width="100%">
				<tr>
					<td>
						<font color="#800000">請選擇學期：</font>
				<select name="c_curr_seme" onchange="this.form.submit()">
					<option value="" style="color:#FF00FF">請選擇學期</option>
					<?php
					 foreach($class_seme_p as $tid=>$tname) {
						if (substr($tid,0,3)>$curr_year-3) {
			    ?>
      				<option value="<?php echo $tid;?>" <?php if ($c_curr_seme==$tid) echo "selected";?>><?php echo $tname;?></option>
   				<?php
      				} // end if
    				} // end while
		    ?>
				</select> 
				<Input Type="Radio" Name="listmode" Value="3" <?php if ($_POST['listmode']==3) echo "checked";?> onclick="this.form.submit()">列出全部
				<Input Type="Radio" Name="listmode" Value="1" <?php if ($_POST['listmode']==1) echo "checked";?> onclick="this.form.submit()">列出已認證
				<Input Type="Radio" Name="listmode" Value="2" <?php if ($_POST['listmode']==2) echo "checked";?> onclick="this.form.submit()">列出未認證
				
		</td>
	</tr>
</table>
<table border="1" style="border-collapse:collapse" bordercolor="#800000" cellpadding="3" width="820">
	<tr bgcolor="#FFCCFF" style="font-size:10pt">
	 <td width="70" align="center">服務日期</td>
	 <td width="70" align="center">登錄者</td>
	 <td width="200" align="center">服務內容</td>
	 <td width="350" align="center">學生名單(時間:分)</td>
	 <td width="70" align="center"><input type="checkbox" name="init_check" value="1" onclick="checkall();">狀態</td>
	 <td width="60" align="center">認證者</td>
	</tr>
<?php

$C[0]="<font style='color:#FF0000;font-size:9pt'>未認證</font>";
$C[1]="<font style='color:#0000FF;font-size:9pt'>已認證</font>";

if ($c_curr_seme!="") {
 $query="select * from stud_service where year_seme='$c_curr_seme'";
 
 switch ($_POST['listmode']) {
 	case '3':
 	  break;
 	case '1':
 	   $query.=" and confirm='1'";
 	  break;
 	case '2':
 	   $query.=" and confirm='0'";
 	  break;
 }
  $query.=" order by service_date desc";
 
 
 $res=mysql_query($query);
 if (mysql_num_rows($res)>0) {
 	while ($S=mysql_fetch_array($res)) {
 		$INPUT=($S['input_sn']==0)?$S['update_sn']:$S['input_sn'];
 ?>	
	<tr style="font-size:10pt">
	 <td width="70" align="center"><?php echo $S['service_date'];?></td>
	 <td width="70" align="center"><?php echo "<font style='font-size:8pt'>【".getPostRoom($S['department'])."】</font><br>".get_teacher_name($INPUT);?></td>
	 <td width="200"><?php echo '【'.$S['item'].'】<br>'.$S['memo'];?></td>
	 <td width="350"><span class="show_students" id="<?php echo $S['sn'];?>">共登錄 <?php echo getService_num($S['sn']);?>人。(顯示名單)</span>
	  <?php
	  //list_service_stud_noedit($S['sn']);
	  ?>	
	 </td>
	 <td width="70" align="center">
	 	<?php
	 	 if ($S['confirm']==0) {
	 	 ?>
	 	  <input type="checkbox" name="confirm_check[]" value="<?php echo $S['sn'];?>">
	 	 <?php
	 	 } else {
	 	 	if ($_POST['listmode']==1) {
	 	 	?>
	 	 	<input type="checkbox" name="confirm_check[]" value="<?php echo $S['sn'];?>">
	 	 	<?php
	 	 	}
	 	  echo $C[$S['confirm']];
	   }
	 	?>
	 
	 </td>
	 <td width="60" align="center"><?php echo get_teacher_name($S['confirm_sn']);?></td>
	</tr>	
 	
 	<?php
 	} // end while
 }
}
?>
</table>
<?php
 if ($_POST['listmode']==2 or $_POST['listmode']==3) {
?>
<table border="0" width="100%">
 <tr>
  <input type="button" value="認證以上勾選項目的服務時數" onclick="document.myform.confirm.value=1;document.myform.submit()" style="color:#0000FF">
 </tr>
</table>
<?php
}
?>
<?php
 if ($_POST['listmode']==1) {
?>
<table border="0" width="100%">
 <tr>
  <input type="button" value="註消以上勾選項目的服務時數" onclick="document.myform.confirm.value=-1;document.myform.submit()" style="color:#FF0000">
 </tr>
</table>
<?php
}
?>
</form>
<Script>
function checkall() {
	
	var j=0;
	if (document.myform.init_check.checked) { j=1; }
	
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,13)=='confirm_check') {
      document.myform.elements[i].checked=j;
    }
    i++;
  }
}	

$(".show_students").click(function(){
   
	   var show=$(this);
	   var sn=$(this).attr("id");
	   
    $.ajax({
   	type: "post",
    url: 'ajax_stud_service_list.php',
    data: { sn:sn },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤, 無法取得學生名單!');
    },
    success: function(response) {
    	//alert('ajax request 成功!');
    	show.html(response);
      show.fadeIn();
    }
   });   // end $.ajax
   
});

</Script>