<?php

// $Id: stud_kind2.php 7706 2013-10-23 08:59:03Z smallduh $


//���J�]�w��
require("config.php") ;
// --�{�� session 
sfs_check();

$teacher_sn=$_SESSION['session_tea_sn'];//���o�n�J�Ѯv��id
//��X���ЯZ��
$class_name=teacher_sn_to_class_name($teacher_sn);

//�Ӹ�O��
//�u�n�i�J�N�O��
$class_description=implode(",",$class_name);
$test=pipa_log("�S�������O\r\n�Ǧ~�G$sel_year\r\n�Ǵ��G$sel_seme\r\n�Z�šG$class_description\r\n");		

//-----------------------------------


  if ($_POST['Submit']=="�ץXEXCEL") {

             
	$filename ="score.xls" ;


	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
					//�t�X SSL�s�u�ɡAIE 6,7,8�U�������D�A�i��ק� 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");

$data_array = show_data($class_name , 0) ;

//�ϥμ˪�
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// �ϥ� smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$smarty->debugging = true;


$smarty->assign("data_array",$data_array); 


$smarty->assign("template_dir",$template_dir);

$smarty->display("$template_dir/kind_excel.htm");
	exit;
         
  }       


head("�S�������O�ǥͦW�U");
print_menu($menu_p);

$data_array = show_data($class_name ) ;

//�ϥμ˪�
$template_dir = $SFS_PATH."/".get_store_path()."/templates";
// �ϥ� smarty tag
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
//$smarty->debugging = true;


$smarty->assign("data_array",$data_array); 


$smarty->assign("template_dir",$template_dir);

$smarty->display("$template_dir/kind.htm");
foot() ;


function show_data($class_name , $view=1 ) {
  global $CONN ;     
  //$class_year_p = class_base(); //�Z��

 
    //���o�U���O�W��
    $sqlstr = "select d_id , t_name from sfs_text where  t_kind='stud_kind'  " ;
    $result =$CONN->Execute($sqlstr) or user_error("Ū�����ѡI<br>$sqlstr",256) ;
    while ($row = $result->FetchRow() ) {
        $d_id = $row["d_id"] ;
        $t_name = $row["t_name"] ;    
        $kind_name[$d_id] = "($d_id)$t_name"   ;
    }


    $sqlstr = "select b.* , d.*  from stud_base b ,stud_domicile d  where b.stud_study_cond = 0  and b.stud_kind <> '0' and (b.stud_kind <> ',0,') and b.stud_kind <> ''  and b.student_sn =d.student_sn  and b.curr_class_num like '$class_name[0]%' order by  b.curr_class_num " ;
    
    $result =$CONN->Execute($sqlstr) or user_error("Ū�����ѡI<br>$sqlstr",256) ; 
    //echo $sqlstr ;
    

    while ($row = $result->FetchRow() ) {
    	unset($row_data) ;
      $s_kind ="" ;
      $stud_kind = $row["stud_kind"];
    	$stud_kind_arr = split("," , $stud_kind) ;
    	foreach( $stud_kind_arr as  $tid =>$tval) {
    	  if ($tval > 0 )
    	     $s_kind .= $kind_name["$tval"]; 
    	}    

    	if ($s_kind) {
    		
        	$row_data[s_kind]=$s_kind ; 
        	    	
        	$row_data[s_addres] = $row["stud_addr_1"];
        	$row_data[s_home_phone] = $row["stud_tel_1"];	  //�a���q��
        	$row_data[s_offical_phone] = $row["stud_tel_2"];  	//�u�@�a�q��
        
        	$row_data[stud_id] = $row["stud_id"];
        	$row_data[stud_name] = $row["stud_name"];
        	$row_data[stud_person_id] = $row["stud_person_id"];
          
        
        	$class_num_curr = $row["curr_class_num"];
        	//$row_data[s_year_class] = $class_year_p[substr($class_num_curr,0,3)];   //���o�Z��
          $row_data[s_year_class] = $class_name[1] ;   //���o�Z��
          
        	$row_data[s_num] = intval(substr($class_num_curr,-2));	//�y��
        	$row_data[s_birthday] = $row["stud_birthday"]  ;
        	//�ഫ������
        	if ($view)
                $row_data[s_birthday] = DtoCh($row_data[s_birthday]) ; 
                
          $row_data[d_guardian_name] = $row["guardian_name"] ;
          $row_data[fath_name] = $row["fath_name"] ;
          $row_data[moth_name] = $row["moth_name"] ;
          $data_arr[]= $row_data ;
      }

    }
    
    return $data_arr ;
    
}    

?>