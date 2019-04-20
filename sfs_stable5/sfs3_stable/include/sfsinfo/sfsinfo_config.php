<?php

// $Id: sfsinfo_config.php 5310 2009-01-10 07:57:56Z hami $

include "../config.php";
//目錄內程式
$menu_p = array("aboutsfs.php"=>"關於SFS學務系統 ","program_info.php"=>"模組程式說明");


/* 取得子目錄數 */

function get_dircount($dir)
{
	global $root_dir;
	$curr_dir = "$root_dir$dir";
	$handle=opendir($curr_dir);
	while ($file = readdir($handle))
	{ 
		if ($file != "." and $file != ".." and  is_display_path($file))
			{
				chdir($curr_dir); 
				if (!is_dir($file))
					continue;
				$i++;
			}
	}
	return $i;
}




/* 重整目錄名稱 */

function get_dir($dir)
{
	global $root_dir,$this_path,$curr_file,$get,$ap_array;
	$curr_dir = "$root_dir$dir";
	$handle=opendir($curr_dir);
	while ($file = readdir($handle)){
		if ($file != "." and $file != ".." and is_display_path($file)){
			chdir($curr_dir); 
			if (!is_dir($file))
				continue;
			$ap_array[] = "$dir/$file";
			get_dir("$dir/$file");
		}
	}
	closedir($handle); 
	return $ap_array;
}

/* 取得目錄名稱 */

function get_dirname($dir)
{
	global $root_dir,$do_path,$curr_file,$SFS_PATH;
	$curr_dir = "$root_dir$dir";
	$handle=opendir($curr_dir);
	while ($file = readdir($handle)){
		if ($file != "." and $file != ".." and is_display_path($file)){
			chdir($curr_dir); 
			if (!is_dir($file))
				continue;
			$ffile = substr("$dir/$file",1);

			echo "<DL>";
			$query = "SELECT * FROM pro_kind where store_path='$ffile' ";
			$result = mysql_query ($query);
			if (mysql_num_rows($result)>0){
				$row = mysql_fetch_array($result);
				$pro_kind_id = $row["pro_kind_id"];
				$pro_kind_name = $row["pro_kind_name"];
				$pro_kind_order = $row["pro_kind_order"];
				$store_path = $row["store_path"];
				if ($this_path =='')
					$this_path == $pro_kind_id;
					
				$fpath_str = "$SFS_PATH/$store_path/$file"."_README.txt";	
				$go_gif = "";
				if (is_file ($fpath_str))
					$go_gif= "<a href=\"{$_SERVER['PHP_SELF']}?curr_file=$dir/$file&pathname=$store_path/$file\">$pro_kind_name($file)</a>";
				else
					$go_gif= "$pro_kind_name($file)";
					
				if ($curr_file == "$dir/$file" ){
					$go_gif = "<DT><font size=2>◢ $go_gif</font>";
					$do_path = $pro_kind_name;
				}
				else
					$go_gif = "<DT><font size=2>$go_gif</font>";

				echo "$go_gif";
				get_dirname("$dir/$file");
				echo "</DL>";
			}
			else
				echo "</DL>";
		}
	}
	closedir($handle); 
}



?>
