<?php
// $Id: output_xml.php 7713 2013-10-25 02:29:35Z smallduh $
//require "config.php";
include "class.php";
include "function.php";

//中心端支援
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}

sfs_check();

$toxml_menu=make_menu($eduxcachange_menu);

//國中加入生涯輔導輸出選項
$checked=$IS_JHORES?'checked':'';

$career_checkbox="<input type='checkbox' name='career' value=1 $checked>輸出國中生涯輔導手冊資料(需有安裝相關模組)";
$smarty->assign("career_checkbox",$career_checkbox);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","XML交換作業");
$smarty->assign("SFS_MENU",$toxml_menu);
$smarty->display("toxml_output_xml.tpl");

$temp_dir=$UPLOAD_PATH."eduxcachange/";

//check file dir exist and permission
if (!is_dir($temp_dir)) {
    mkdir($temp_dir, 0777, ture);
}
if(0777 !== (fileperms($temp_dir) & 0777)){
    chmod($temp_dir, 0777);
}
$f_list=check_files_list($temp_dir);
print "<script type='text/javascript'>document.getElementById(\"process_res\").innerHTML ='$f_list';</script>";
//如果確定輸出XML檔案
if ($_POST[output_xml]!="") {
    unlink(exist_file_path($temp_dir));
    if (!$IS_JHORES) {
       if(!$SFS_IS_CENTER_VER){
        $process1 = new Process('php output_xml_cli_1.php');//auto start the same time
        $process2 = new Process('php output_xml_cli_2.php');
        $process3 = new Process('php output_xml_cli_3.php');
        $process4 = new Process('php output_xml_cli_4.php');
        $process5 = new Process('php output_xml_cli_5.php');
        $process6 = new Process('php output_xml_cli_6.php');
      }else{
        $process1 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_1.php");\'');
        $process2 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_2.php");\'');
        $process3 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_3.php");\'');
        $process4 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_4.php");\'');
        $process5 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_5.php");\'');
        $process6 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_6.php");\'');
      }
      //$process.start();
      //$process.stop();
	while ($process6->status()){
		   echo "<script type='text/javascript'>document.getElementById('process_res').innerHTML = '<img src=\"images/running.gif\">';</script>";
		}
    }else{
      if($class_year[1]==null){
		  //判別中心端版本
		  if(!$SFS_IS_CENTER_VER){
          $process7 = new Process('php output_xml_cli_7.php');
          $process8 = new Process('php output_xml_cli_8.php');
          $process9 = new Process('php output_xml_cli_9.php');
	  }else{
		  $process7 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_7.php");\'');
		  $process8 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_8.php");\'');
		  $process9 = new Process('php -r \'$_COOKIE["cookie_sch_id"]="'.$cookie_sch_id.'";require("output_xml_cli_9.php");\'');
		  }
	  while ($process9->status()){
		   echo "<script type='text/javascript'>document.getElementById('process_res').innerHTML = '<img src=\"images/running.gif\">';</script>";
		}  
      }else{
          $process1 = new Process('php output_xml_cli_1.php');
          $process2 = new Process('php output_xml_cli_2.php');
          $process3 = new Process('php output_xml_cli_3.php');
	  while ($process3->status()){
		   echo "<script type='text/javascript'>document.getElementById('process_res').innerHTML = '<img src=\"images/running.gif\">';</script>";
		}
      }
    }
	unset($_POST[output_xml]);
	$filename_zip=$SCHOOL_BASE['sch_id']."_XML_".date('YmdHis').".zip";
	$filename_xml=$SCHOOL_BASE['sch_id']."_XML_".date('YmdHis').".xml";
    $content=mb_convert_encoding("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<!DOCTYPE 學籍交換資料 SYSTEM \"http://sfscvs.tc.edu.tw/student_3_0_tcc.dtd\" >\r\n<學籍交換資料>\r\n", "UTF-8", "BIG5");
    if (!$IS_JHORES) {
       $run_start=1;
	while($run_start<7){
	$old_file=$temp_dir.$SCHOOL_BASE['sch_id']."_XML_".$run_start.".xml";//各年級檔案
	$content.=file_get_contents($old_file)."\r\n";//讀取檔案內容
	unlink($old_file);
	$run_start++;
        }
    }else{
        if($class_year[1]==null){
            $run_start=7;
	    while($run_start<10){
	       $old_file=$temp_dir.$SCHOOL_BASE['sch_id']."_XML_".$run_start.".xml";//各年級檔案
	       $content.=file_get_contents($old_file)."\r\n";//讀取檔案內容
	       unlink($old_file);
	       $run_start++;
            }
        }else{
            $run_start=1;
	    while($run_start<4){
	       $old_file=$temp_dir.$SCHOOL_BASE['sch_id']."_XML_".$run_start.".xml";//各年級檔案
	       $content.=file_get_contents($old_file)."\r\n";//讀取檔案內容
	       unlink($old_file);
	       $run_start++;
            }
        }
    
    }
	$content.=mb_convert_encoding("</學籍交換資料>", "UTF-8", "BIG5");//file footer
	file_put_contents($temp_dir.$filename_xml,$content);//寫入合併檔
	//產生zip檔
	if($xml_file === FALSE){
      die("產生檔案失敗");
    }else{
	  $zip = new ZipArchive;
      $xml_zip = $zip->open($temp_dir.$filename_zip,ZipArchive::CREATE);
      if($xml_zip === TRUE) {
        $zip->addFile($temp_dir.$filename_xml, $filename_xml);
        $zip->close();
        unlink($temp_dir.$filename_xml);
      }else{
        unlink($temp_dir.$filename_xml);
        die("產生壓縮檔失敗");
      }
    }
    $f_list=check_files_list($temp_dir);
	echo "<script type='text/javascript'>document.getElementById('process_res').innerHTML ='$f_list';</script>";
}
?>
