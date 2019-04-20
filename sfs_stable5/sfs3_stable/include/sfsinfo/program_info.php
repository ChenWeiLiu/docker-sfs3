<?php

// $Id: program_info.php 8131 2014-09-23 07:58:12Z smallduh $

include "sfsinfo_config.php";

//  --程式檔頭
head("模組程式說明");
print_menu($menu_p);


$root_dir = $SFS_PATH;
$curr_dir = $root_dir.$dir;
?>
<table  width=100% class=main_body >
  <tr > 
    <td rowspan="2" valign=top height=100%>
		<table cellspacing="0" cellpadding="0" bgcolor="#999999" border="0" align="center">
		<tr>
		<td>
		<table  border="0" cellspacing="1" cellpadding="3" align="center">
		<tr bgcolor="#f8feed"><td  valign=top >
	<?php 
	if ($dir !=""){
		$name = updir ($dir);
		$ffile = substr("$dir",1);
		$query = "SELECT * FROM pro_kind where store_path='$ffile' ";
		$result = mysql_query ($query);
		if (mysql_num_rows($result)>0)	{
			$row = mysql_fetch_array($result);
			$upthis_path = $row["pro_kind_id"];
			$upname = $row["pro_kind_name"];
		}
		echo  "<font size=2><a href=$PHP_SELF?get=$get&dir=$name&curr_file=$dir&this_path=$upthis_path>$upname</a></font><br>"; 
	}
	else
		echo "<font size=2><B><a href=\"$PHP_SELF\">系統說明</a></b>&nbsp; 版本：sfs$SFS_VERSION($SFS_DATE)</font>";

	// 取得目錄名
	get_dirname($dir); 

	?>
		<br><img border="0" src="<?php echo $SFS_PATH_HTML ?>images/pixel_clear.gif" width="220" height="1" alt="背景圖">
		</td></tr>	
		</table>
	</td></tr></table>
	</td>
	</tr>
	<tr>
	<td width="100%" valign=top >
	<?php 			
	//echo "$do_path 說明<p>";
	echo "<font size=2>";
	if ($pathname != "")
		$fpath_str = "$SFS_PATH/$pathname"."_README.txt";
	else
		$fpath_str = $path."/Readme.txt";
	if (is_file ($fpath_str)){
		$fd = fopen($fpath_str, "r");
		while ($buffer = fgets($fd, 4096))
			echo $buffer."<BR>";
		fclose($fd);
	}
	echo "</font>";
	?>
	
	</td>
	</tr>
</table>

<?php
foot();

?>
