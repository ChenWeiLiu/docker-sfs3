<!-- $Id: footer.php 5310 2009-01-10 07:57:56Z hami $ -->   
</td>
     </tr>
   <tr>
   <td>
     <BR>
   </td>
   </tr>
    </tbody>
  </table> 
</form>

<!------------------------FOOT BEGIN------------------------>
<table bgColor="green" border="0" cellPadding="0" cellSpacing="0" width="608">
  <tbody>       
    <tr>
      <td>       
        <p><img border="0" src="<?php echo $path_html ?>images/pixel_clear.gif" width="1" height="1" alt="背景圖"></p>
      </td>
    </tr>
  </tbody>  
</table>
<!------------------------FOOT END------------------------>

<table  border="0" cellPadding="0" cellSpacing="0" width="608"><tr><td>

<?php 
if ($man_name != ""){
	echo "<font size=2 color=green>系統管理：";
	if ($man_mail !="")
		echo"<a href=\"mailto:$man_mail\">$man_name</a>";
	else
		echo $man_name;
}
echo "&nbsp;&nbsp;&nbsp;";
if ($data_name != ""){
	echo "<font size=2 color=green>資料整理：";
	if ($data_mail !="")
		echo"<a href=\"mailto:$data_mail\">$data_name</a>";
	else
		echo $data_name;
}
?>
</td></tr></table>

<?php foot(); ?>
