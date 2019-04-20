<?php
                                                                                                                             
// $Id: show_zip.php 5310 2009-01-10 07:57:56Z hami $

if (!$isload)
{
include "exam_config.php";
}
include "header.php";


echo "<h2>­ì©lÀÉ¦Cªí</h2>\n";
$m_path=$upload_path."/e_".$exam_id."/".$stud_id;

exec("ls ".$m_path." -l *.phps" , $result, $id);
             
             $i = 1;

             while (isset($result[$i])) {
             $result[$i] = eregi_replace(" +", ",", $result[$i]);

	     $line = explode(",", $result[$i]);
             $f_temp = explode(".", $line[8]);
	     if (!ereg("^d", $line[0]) && $f_temp[count($f_temp)-1] == 'phps' )  {

            	
	      echo "<a href=\"".$uplaod_url."/e_".$exam_id."/".$stud_id."/".$line[8]."\">". $line[8]."</a><br>\n";
	      }	
             
		$i++;
            
  	  	 	
	}    
	
include "footer.php";
?>	
	
