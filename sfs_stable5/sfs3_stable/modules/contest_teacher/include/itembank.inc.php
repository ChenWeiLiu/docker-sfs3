<?php

//表單_新增題庫
function form_additem($IB) {
?>
   <table border="0" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
  	<tr>
  		<td width="80" align="right">題目</td>
  		<td><input type="text" name="question" size="100" value="<?php echo $IB['question'];?>"></td>
  	</tr>
  	<tr>
  		<td width="80" align="right">參考答案</td>
  		<td><input type="text" name="ans" size="70" value="<?php echo $IB['ans'];?>"></td>
  	</tr>
  	<tr>
  		<td width="80" align="right">參考網址</td>
  		<td><input type="text" name="ans_url" size="70" value="<?php echo $IB['ans_url'];?>"></td>
  	</tr>
  </table>
<?php
}

//列出題庫試題
function listitembank($PAGE) {
	global $PHP_PAGE;
	?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
  	<tr>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="20" align="center"><input type='checkbox' name="tag_chk" onclick="tag_all('tag_chk','tag_it');"</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="40" align="center">編號</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" >題目內容</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="20%">參考解答</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="10%">網址</td>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="50" align="center">操作</td>
  	</tr>

   	<?php
   	 $row=mysql_fetch_row(mysql_query("select count(*) as num from contest_itembank"));
   	 list($ALL)=$row; 
   	 $PAGEALL=ceil($ALL/$PHP_PAGE); //無條件進位
   	 $st=($PAGE-1)*$PHP_PAGE;
   	 $query="select * from contest_itembank limit ".$st.",".$PHP_PAGE;
   	 $result=mysql_query($query);

     if (mysql_num_rows($result)) {
     
   	 while ($IB=mysql_fetch_array($result)) {   	 	
   	 	$ans_url=($IB['ans_url']=='')?"無":"<a href='".$IB['ans_url']."' target='_blank'>".瀏覽."</a>";
   	 	
   	?>
  	<tr>
  		<td bgcolor="#CCFFCC" style="font-size:10pt;color:#000000" width="20" align="center"><input type='checkbox' name="tag_it[]" value="<?php echo $IB['ibsn'];?>"</td>
  		<td style="font-size:10pt;color:#000000" width="40" align="center"><?php echo $IB['id'];?></td>
  		<td style="font-size:10pt;color:#000000" ><?php echo $IB['question'];?></td>
  		<td style="font-size:10pt;color:#000000" width="20%"><?php echo $IB['ans'];?></td>
  		<td style="font-size:10pt;color:#000000" width="10%"><?php echo $ans_url;?></td>
  		
  		<td style="font-size:10pt;color:#000000" width="50" align="center">
  			<img src="./images/edit.png" border="0" style="cursor:hand" onclick="document.myform.option1.value='<?php echo $IB['ibsn'];?>';document.myform.act.value='update';document.myform.submit();">&nbsp;
  			<img src="./images/del.png"  border="0" style="cursor:hand" onclick="del_itembank('<?php echo $IB['ibsn'];?>');">
  		</td>
  	</tr>
  	<?php
  	} // end while  	
   } // end if mysql_num_rows > 0
  	?>
  	</table>
  	<table border="0" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
  	<tr>
  	 <td style="font-size:10pt">換頁 
  	 <?php
  	 //頁碼
  	  for($i=1;$i<=$PAGEALL;$i++) {
  	  	if ($i==$PAGE) {
  		  	   echo "<font color=#FF00FF size=3><b><u>".$i."</u></b></font>&nbsp;";
				 }else{
  	   ?>
  	    <a href="javascript:page(<?php echo $i;?>)"><?php echo $i;?></a>&nbsp;
  	   <?php
  	     } // end if
  	  } //end for
  	 ?>
  	 </td>
  	</tr>
  </table>
  <font size="2" color="#FF0000">※提示：可以由「系統管理/模組管理」，調整模組變數，改變每頁呈現筆數。</font>
<?php
} // end function

function get_item($ibsn) {
 $query="select * from contest_itembank where ibsn='$ibsn'";
 $res=mysql_query($query);
 
 return mysql_fetch_array($res);
 
}

?>