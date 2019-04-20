<?php
//$Id: grade_data.php 8364 2015-03-23 07:28:21Z chiming $
//載入設定檔
require("config.php") ;

// 認證檢查
sfs_check();
($IS_JHORES==0) ? $UP_YEAR=6:$UP_YEAR=9;//判斷國中小

$class_year_p = class_base("",array($UP_YEAR) ); //班級

$key =$_POST['key'];
$curr_class_name =$_POST['curr_class_name'];
	
if ($key)
    Do_CSV($curr_class_name) ;
        	
else {
	head() ;
	//prog(5);
	print_menu($menu_p);
        echo show_menu() ;
	foot() ;

}

//主選單
function show_menu() { 
        global $UP_YEAR ,$PHP_SELF,$class_year_p ;
        
	$class_temp ='';		
        foreach($class_year_p  as $tkey=>$tvalue)
	 {
		  if ($tkey == $curr_class_name)	  
			 $class_temp .=  sprintf ("<option value='%d' selected>%s</option>\n",$tkey,$tvalue);
		   else
			 $class_temp .=   sprintf ("<option value='%d'>%s</option>\n",$tkey,$tvalue);
        }             	 
		         
	$main = "<table width=100% bgcolor='#CCCCCC' >
  		<tr><td align='center'>	 
  		<H2>畢業生資料匯出</H2>
  		<form action='$PHP_SELF' method='post' name='pform'> 
	        <table width=50%  cellspacing='0' cellpadding='2' bordercolorlight='#333354' bordercolordark='#FFFFFF' border='1' bgcolor='#99CCCC'>
	           <tr><td align=right>選擇班級</td>
	             <td><select name='curr_class_name'>
	             <option value='$UP_YEAR'>全學年</option>
	             $class_temp 
	             </select></td>
	           </tr>
	           <tr><td colspan=2 align=center><input type='submit' name='key' value='匯出 CSV '>
	           </td></tr>
	         </table>
	         </form>
	        </td></tr></table>" ;       
       return $main ; 	           
}
        
//-----------------------------------匯出csv

function Do_CSV($class_num) {
  global $UP_YEAR , $CONN  ;
  $class_name = class_name();
  
  /*
  $sqlstr  = " select s.stud_id, s.stud_name,year(s.stud_birthday) as TY , month(s.stud_birthday) as TM, DAYOFMONTH(s.stud_birthday) as TD,
               s.curr_class_num , g.grad_num  
               from stud_base  s
               LEFT JOIN grad_stud as g ON s.stud_id=g.stud_id 
               where  s.stud_study_cond =0  and s.curr_class_num like '$class_num%' 
               order by s.curr_class_num ";
  
  $sqlstr  = " select s.stud_id, s.stud_name, s.stud_name_eng,year(s.stud_birthday) as TY , month(s.stud_birthday) as TM, DAYOFMONTH(s.stud_birthday) as TD,
               s.curr_class_num , g.grad_num  
               from stud_base  s,grad_stud as g where  s.stud_id=g.stud_id 
               and  s.stud_study_cond =0  and s.curr_class_num like '$class_num%' 
               order by s.curr_class_num ";
  */             
  $sqlstr  = " select s.stud_id, s.stud_name, s.stud_name_eng,year(s.stud_birthday) as TY , month(s.stud_birthday) as TM, DAYOFMONTH(s.stud_birthday) as TD,
               s.curr_class_num , g.grad_num  
               from stud_base  s,grad_stud as g where  s.student_sn=g.student_sn 
               and  s.stud_study_cond in ('0','15')  and s.curr_class_num like '$class_num%' 
               order by s.curr_class_num ";
               //搜尋條件將s.stud_id=g.stud_id修改成 s.student_sn=g.student_sn，避免畢業生十年學號重複問題  modify by kai,103.4.30
               
  $result =$CONN->Execute($sqlstr) or user_error("讀取失敗！<br>$sqlstr",256) ; 

  $row_data[0] =  "班級,座號,學號,姓名,英文姓名,年,月,日,編號";
  
  while ($row =$result->FetchRow() ) {
	
	
	$stud_name = $row["stud_name"];
	$stud_name_eng = $row["stud_name_eng"];
	
	$stud_no=substr($row["curr_class_num"],-2);
	$stud_id=$row["stud_id"];

	$stud_TY = $row["TY"] -1911 ;
	$stud_TM = $row["TM"] ;
	$stud_TD = $row["TD"] ;
	$curr_class_num = $row["curr_class_num"];
	$grad_num = $row["grad_num"];	
	$curr_class_name = $class_name[substr($curr_class_num,0,3)];
        $line_data ="\"$curr_class_name\",\"$stud_no\",\"$stud_id\",\"$stud_name\",\"$stud_name_eng\",\"$stud_TY\",\"$stud_TM\",\"$stud_TD\",\"$grad_num\"" ;
        $row_data[] = $line_data ;
	
  }

	$main=implode("\r\n",$row_data);
	$s=get_school_base();
	$filename =$s['sch_cname'].curr_year()."學年度畢業生名冊.csv";
	
	//以串流方式送出 ooo.csv

	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: text/x-csv");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");

	echo $main;
	
	exit;
	return;

}
  
?>
