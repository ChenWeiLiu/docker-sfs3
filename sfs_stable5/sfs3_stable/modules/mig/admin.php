<?php
                                                                                                                             
// $Id: admin.php 6810 2012-06-22 08:17:27Z smallduh $

// 系統認證
// 載入校務系統設定檔
include "mig_config.php";

//判斷 convert 路徑
if(!file_exists($convert_path."convert")){
	head();
	trigger_error($convert_path."convert 為錯誤的路徑,本程式利用 convert 程式進行圖像壓縮,下列的指令可以幫助了解 convert 的路徑<br><span style=\"background-color: #FFFF00\"> whereis convert </span><br>將正確的路徑設定在  系統管理 / 模組權限管理 中數位相本的<b>變數調整</b>",E_USER_ERROR);
}


//session_register("session_log_id");
//session_register("session_tea_name");
if(!checkid(substr($_SERVER[PHP_SELF],1)))
 {
  $go_back=1; //回到自已的認證畫面  
  include $templateDir."/header.php";
  include "$SFS_PATH/rlogin.php";  
  include $templateDir."/footer.php"; 
  exit;
 }


//$currDir = $_POST[currDir];
//$cpath = $_POST[cpath];
$cpath= ereg_replace (" ", "", $cpath);
$cpath = ereg_replace("^.*/", "", $cpath);

$uppath= ereg_replace("^.", "", $currDir);
$uppath= ereg_replace("^/", "", $uppath);
if ($uppath != "")
	$gopath = "./".$uppath;
else
	$gopath = ".";
    
$gopath = stripslashes($gopath);           
$filelist_path = addslashes($albumDir."/".$uppath."/filelist.txt");
//目錄處理

if ($_POST[key] == "開始")
{
	
	if ($_POST[sel] == 1) /** 建立目錄 **/
	{
 		if (!is_dir($albumDir))
			mkdir($albumDir, 0700);
		$e_path = addslashes($albumDir."/".$uppath."/".$cpath);
		if (!is_dir($e_path))            
			mkdir($e_path, 0700);  
	}
	else if ($_POST[sel]==2 && $_POST[cpath] !="") /** 刪除目錄 **/
	{
	      
		$e_name = addslashes($albumDir."/".$uppath."/".$cpath);   
		$str ="";
		for ($i=0;$i< strlen($e_name);$i++)
		{
			if (ord($e_name[$i]) == 96 || ord($e_name[$i]) == 124)
		   		$str .= chr(92).$e_name[$i];
		  	else $str .= $e_name[$i];
		}
      		$e_name = $str;
		
		system("rm -rf $e_name"); 

	}
}    

//上傳處理
else if ($_POST[key] == "上傳"){
	// filelist.txt 記錄檔名		
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}	
	$file_list = fopen($filelist_path, 'a+');
	
	//建立目錄
	if (!is_dir($albumDir))
		mkdir($albumDir, 0711);   //上傳相片目錄
	
	$e_path = $albumDir."/".$uppath;
	$e_path_temp = $albumDir."/".$uppath."temp";
	
	if (!is_dir($e_path))            
		mkdir($e_path, 0711);  //相片目錄
       
  	//判斷檔名
	$f_name = $_FILES[infile][name];
	$f_temp = explode(".", $f_name);  	
	if ($f_temp[count($f_temp)-1] == 'zip')
	{                      
		if (is_dir($e_path))                     	
		{
			$e_path_temp = addslashes($e_path_temp);			
			exec("unzip ".$_FILES[infile][tmp_name]." -d $e_path_temp",$val);			
		}
            
	//列出檔案
		exec("ls -l ".$e_path_temp , $result, $id);
		$i = 1;
		while (isset($result[$i]))
		{
			$result[$i] = eregi_replace(" +", ",", $result[$i]);
			$line = explode(",", $result[$i]);
			if (!ereg("^d", $line[0]))
			{
				$imgname = $e_path_temp."/".$line[8];
				$temp_name = explode(".", $line[8]);
				$th_image = addslashes($e_path."/".$temp_name[0].".jpg");
				$th_name = addslashes($e_path."/".$temp_name[0]."_th.jpg");
				//system("djpeg -pnm $imgname | pnmscale -xscale 0.15 -yscale 0.15 | cjpeg > $th_name ");
				//system("/usr/X11R6/bin/convert -geometry 600 pictures/$name pictures/$name");
				system("$convert_path"."convert -geometry $indexImgWidth $imgname $th_name &");
				system("$convert_path"."convert -geometry $ImgWidth $imgname $th_image &");
				
//		echo $line[8]."上傳 ok!<br>\n";
				//$f_temp = explode(".", $line[8]);
				fputs($file_list,"$temp_name[0].jpg::$_SESSION[session_log_id]::$_SESSION[session_tea_name] \n");
				$i++;
				
			}
		}
		exec( "rm -rf $e_path_temp", $val );
	}
	else 
	{

		$temp_name = explode(".",$f_name);		
		
		$e_path = addslashes($e_path);
		//用圖案說明代替檔名
		if ($t1 !== "") {$temp_name[0] = $t1;}
		$temp_file_name = $temp_name[0].".jpg";

		$e_path .="/".stripslashes($temp_name[0]);

		$str ="";

		for ($i=0;$i< strlen($e_path);$i++)
		{
			if (ord($e_path[$i]) == 96 || ord($e_path[$i]) == 124 || ord($e_path[$i]) == 64|| ord($e_path[$i]) == 60)
		   		$str .= chr(92).$e_path[$i];
		  	else $str .= $e_path[$i];
		}
      		$e_path = $str;  

		$th_image = $e_path.".jpg";			
		$th_name = $e_path."_th.jpg";
		fputs($file_list,"$temp_file_name::$_SESSION[session_log_id]::$_SESSION[session_tea_name]\n");
		system($convert_path."convert -geometry 96 ".$_FILES[infile][tmp_name]." $th_name &");
		system($convert_path."convert -geometry 500 ".$_FILES[infile][tmp_name]." $th_image &");	
//		echo $convert_path."convert -geometry 500 ".$_FILES[infile][tmp_name]." $th_image &";
//		exit;

	}
fclose($file_list);
}
else if ($_POST[key] =="建立說明") {
	$line = $mig_memo;
	if (!ereg('<bulletin>', $_POST[$mig_memo]))
		$line = "<bulletin>\n$mig_memo";	
	if (!eregi('</bulletin>$', $mig_memo))
		$line .= "\n</bulletin>";
		
	$mig_file = addslashes($albumDir."/".$uppath."/mig.cf");
	$file = fopen("$mig_file", 'w');
	fputs($file,"$line");
	fclose($file);	
}	
//刪除圖檔處理
else if ($_GET[sel]== 'del')
{
	$e_name = addslashes($albumDir."/".$_GET[currDir]."/".$_GET[image]);   
	$str ="";	
	for ($i=0;$i< strlen($e_name);$i++)
	{
		if (ord($e_name[$i]) == 96 || ord($e_name[$i]) == 124 || ord($e_path[$i]) == 64 )
			$str .= chr(92).$e_name[$i];
		else $str .= $e_name[$i];
	}
      	$e_name = $str;     			
	unlink("$e_name"); 
	//更新 filelist.txt
	if(!file_exists($filelist_path)){
		@$createFile = fopen($filelist_path, "w") or die ("Can't create file ".$filelist_path."");
		fwrite ($createFile,"",0);
		@chmod($filelist_path, 0666);
		fclose($createFile);
	}
	else {
		@$openFile = fopen($filelist_path,"r") or die ("Access is denied. Set permission to ".$filelist_path." by command in console \"chmod 666 ".$filelist_path."\"");
		$str = "";
		while ($line = fgets($openFile, 4096)) {			
			if (!($line == "$image::$_SESSION[session_log_id]::$session_tea_name\n"))
				$str .= $line ;	
		}
		@$openFile = fopen($filelist_path,"w");
		fwrite($openFile,$str);
	}
	fclose($openFile);
}

header ("Location: admin_index.php?pageType=$_GET[pageType]&currDir=$gopath");
?>
