<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//秀出網頁
head("網路管理 - 設定電腦教室");

$tool_bar=&make_menu($school_menu_p);

//列出選單
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "抱歉 , 您沒有無管理權限!";
 exit();
}

$comproom=$_POST['comproom'];

//儲存
if ($_POST['act']=='save') {
 foreach ($_POST['site_num'] as $net_edit=>$site_num) {
  $net_ip=$_POST['net_ip'][$net_edit];
  
  $query="replace into net_roomsite (net_edit,net_ip,site_num) values ('$net_edit','$net_ip','$site_num')";
  mysql_query($query);
    
 } // end foreach
 
} // end if save


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
 <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
 <input type="hidden" name="option1" value="<?php echo $_POST['option1'];?>">
 
 <select name="comproom" size="1" onchange="document.myform.act.value='';document.myform.submit()">
  <option value="">請選擇要設定的電腦教室...</option>
  <option value="comproom1"<?php if ($comproom=='comproom1') echo " selected";?>>第一電腦教室</option>
  <option value="comproom2"<?php if ($comproom=='comproom2') echo " selected";?>>第二電腦教室</option>
  <option value="comproom3"<?php if ($comproom=='comproom3') echo " selected";?>>第三電腦教室</option>
 </select>
<?php
$net_ip='';
$site_num='';
$ipmac='';
if ($comproom!="") {

 $COMP_INT=substr($comproom,-1);
 $COMP[1]=100;
 $COMP[2]=200;
 $COMP[3]=300;
 //讀取現有設定
 $query="select * from net_roomsite where net_edit like '".$COMP_INT."%' and site_num>'0' and net_ip!=''";
 $res=mysql_query($query);
 while ($row=mysql_fetch_array($res,1)) {
   	 	$net_ip[$row['net_edit']]=$row['net_ip'];
      $site_num[$row['net_edit']]=$row['site_num'];
      $ipmac[$row['net_edit']]=$row['ipmac'];
 } // end while


?> 
 <table border="0" width="600">
   <tr><td align="center" style="color:#0000FF">第<?php echo $COMP_INT;?>電腦教室配置圖</td></tr> 
 </table>
 <table width="600" style="border-collapse:collapse" bordercolor="#000000" border="0">
 	<tr>
  	<td width="150" align="center">編號　-=電腦 IP =-　</td><td width="150" align="center">編號　-=電腦 IP =-　</td><td width="150" align="center">編號　-=電腦 IP =-　</td><td width="150" align="center">編號　-=電腦 IP =-　</td>
 	</tr>
  <tr>
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=10;$i>0;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=20;$i>10;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=30;$i>20;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
    <td>
      <table border="0" width="100%">
        <?php
         for ($i=40;$i>30;$i--) {
         ?>
          <tr>
           <td>
            <?php
             pc_site($COMP[$COMP_INT]+$i);
            ?>
           </td>
          </tr>
         <?php
         }        
        ?>       
      </table>
    </td>	
  </tr>
 </table>
  <br>
 <table width="600" border="0" style="border-collapse:collapse" bordercolor="#000000">
 	<tr>
 		<td align="center">
 			<table width="80" border="1" style="border-collapse:collapse" bordercolor="#000000">
 			 <tr><td align="center">教師位置</td></tr>
 			</table>
 		</td>
 </tr>
 </table>

<input type="button" value="儲存" onclick="document.myform.act.value='save';document.myform.submit()">
<br><font color=red size=2>※注意！已鎖定狀態的電腦請勿更動 IP</font>。
<?php
} // end if comproom
?>

</form>

<?php

//顯示每個座位的顯況
function pc_site ($edit_num) {
 global $net_ip,$site_num,$ipmac;
 ?>
 <table border="1" style="border-collapse:collapse" bordercolor="#000000" width="100%">
  <tr>
   <td bgcolor="<?php if ($ipmac[$edit_num]==0) { echo "#CCFFCC"; } else { echo "#FFCCCC"; } ?>">
   	<input type="text" name="site_num[<?php echo $edit_num;?>]" value="<?php echo $site_num[$edit_num];?>" size="3">
   	<input type="text" name="net_ip[<?php echo $edit_num;?>]" value="<?php echo $net_ip[$edit_num];?>" size="12">
   </td>
  </tr>
 </table>
 
 <?php
}

?>
<Script Language="JavaScript">
function check_tag(SOURCE,STR) {
	var j=0;
	while (j < document.myform.elements.length)  {
	 if (document.myform.elements[j].name==SOURCE) {
	  if (document.myform.elements[j].checked) {
	   k=1;
	  } else {
	   k=0;
	  }	
	 }
	 	j++;
	}
	
  var i =0;
  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name.substr(0,STR.length)==STR) {
      document.myform.elements[i].checked=k;
    }
    i++;
  }
 } // end function
</Script>