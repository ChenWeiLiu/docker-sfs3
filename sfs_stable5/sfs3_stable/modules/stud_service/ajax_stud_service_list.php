<?php
header('Content-type: text/html;charset=big5');
//取得設定檔
include_once "config.php";

sfs_check();


 $sn=$_POST['sn'];
 
 
if (isset($sn)) {


	$S=getService_one($sn);
	//$query="select year_seme from stud_service where sn='$sn'";
	//$result=mysql_query($query);
	//list($c_curr_seme)=mysql_fetch_row($result);
  $c_curr_seme=$S['year_seme'];
	$class_array=class_base($c_curr_seme);
	//依班級列出名單
	
	$data="<table border='0' width='100%'  cellspacing='0' cellpadding='0'>";

   
		$query="select distinct b.seme_class from stud_service_detail a ,stud_seme b, stud_service c where a.item_sn='$sn'  and a.student_sn=b.student_sn and a.item_sn=c.sn and b.seme_year_seme=c.year_seme order by b.seme_class";
	   $result=mysql_query($query);
	   //開始依班級列出
	   while ($class_array=mysql_fetch_row($result)) {
	   	  list($classid)=$class_array;
		   $C=sprintf('%03d_%d_%02d_%02d',substr($c_curr_seme,0,3),substr($c_curr_seme,-1,1),substr($classid,0,1),substr($classid,1,2));
		   $class_base=class_id_2_old($C);
		   $class_name=$class_base[5]; //班名稱 一年1班, 一年2班....
		   $data.="<tr><td style='color:#0000FF'>".$class_base[5]."</td></tr><tr><td>";
		   //取出班級學生
		   $query="select a.*,b.stud_name,c.seme_num,c.seme_class from stud_service_detail a,stud_base b,stud_seme c where a.item_sn='$sn' and a.student_sn=b.student_sn and a.student_sn=c.student_sn and c.seme_year_seme='$c_curr_seme' and c.seme_class='$classid' and (b.stud_study_cond=0 or b.stud_study_cond=5) order by c.seme_num";
       $res_class=mysql_query($query);
       
       $data.="<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">";
       $i=0;
		   while ($row_class=mysql_fetch_array($res_class)) {
  	   	$i++;
	 	     if ($i%3==1) $data.="<tr>";
	   	 $data.="<td style=\"font-size:10pt\" valign=\"top\" width=\"113\">";
	   	 $data.=$row_class['seme_num'].$row_class['stud_name']."(".$row_class['minutes']."分)</td>";
	     if ($i%3==0) $data.="</tr>";
		   }	//while row_class		
		   	 if ($i%3>0) {
              for ($j=$i%3+1;$j<=3;$j++) { $data.="<td width='113'>&nbsp;</td>"; }
              $data.="</tr>";
    		 }
  
		   $data.="</table></td></tr>";
	   } // end while class_array	   
		     
		  $data.=" </td></tr></table>";

     echo $data;
}    
?>