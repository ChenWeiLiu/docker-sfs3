<?php

// $Id: list_teach_sum.php 8103 2014-08-31 16:38:02Z infodaes $

/* 取得基本設定檔 */
include "config.php";
include "../../include/sfs_oo_zip2.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$year_seme=$_GET['year_seme'];

//若有選擇學年學期，進行分割取得學年及學期
if(!empty($year_seme)){
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

function get_class_sect($teacher_sn  , $sel_year="",$sel_seme="" ,$data="" ){
	global $CONN,$weekN ,$sections;
    

	//每週的日數
	$dayn=sizeof($weekN)+1;
    
    $global_classname = get_global_class_name($sel_year,$sel_seme,"短") ;
	
	/*
	$sql_select = "select c.course_id,c.teacher_sn,c.day,c.sector,c.ss_id,c.room ,c.class_id , t.name
	      from score_course c , teacher_base t where c.teacher_sn = t.teacher_sn
          and c.year='$sel_year' and c.semester = '$sel_seme'  and c.teacher_sn  = '$teacher_sn' order by day,sector";
    //echo $sql_select . "<br> " ;
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room ,$class_id , $t_name)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$c[$k] = $global_classname[$class_id] ;
		$r[$k]=$room;
		$teach_name=$t_name ;
	}
	*/
	$sql= "select name from teacher_base where teacher_sn = $teacher_sn";
	$rs=$CONN->Execute($sql) or trigger_error("錯誤訊息： $sql", E_USER_ERROR);
	$teach_name=$rs->fields['name'];
	
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room,class_id
	      from score_course where year='$sel_year' and semester = '$sel_seme' and (teacher_sn=$teacher_sn or cooperate_sn=$teacher_sn) order by day,sector";
    //echo $sql_select . "<br> " ;
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room ,$class_id , $t_name)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$c[$k] = $global_classname[$class_id] ;
		$r[$k]=$room;
		
	}
	
	for ($w = 1; $w<= $dayn ; $w++) {
	    for ($s=1 ; $s<=$sections ; $s++) {
	        $k=$w ."_". $s;
	    	$tk=$w.$s;
	    	//先清空，再填值
	    	$temp_arr[$tk]="" ;
	    	$temp_arr[$tk] = substr(get_ss_name("","","短",$a[$k]),0,4) . "\n" .$c[$k] ;
	    }
	}     	
	
	//班級名
	$temp_arr["class"] = $teach_name ;
	
    $ttt = new EasyZip;

    $class_data .= $ttt->change_temp($temp_arr,$data);
	return  $class_data  ;
}	
	
	


	$sql_select = "select max(sections) as ms  from score_setup where year=$sel_year and semester=$sel_seme  ";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (!$recordSet->EOF) {
		$sections= $recordSet->fields["ms"];
        $recordSet->MoveNext();
	}	
	    
	
	if ( $sections>7){ 
	  $oo_path = "ooo_all_class8";
	  $sections = 8 ;
	}else{    
      $oo_path = "ooo_all_class";
      $sections = 7 ;
    }  
    

	$filename="teacher_all_".$sel_year.$sel_seme.".sxw";
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');	
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//讀出 XML 檔頭

	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head");
	$doc_head =iconv("Big5","UTF-8",$doc_head);
	//讀出 XML 檔尾
	
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot");
	$doc_foot =iconv("Big5","UTF-8",$doc_foot);
	
	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content");
	$data =iconv("Big5","UTF-8",$data);
	
	$sql_select = "select teacher_sn  ,year ,semester   from score_course 
	   where year=$sel_year and semester=$sel_seme and teacher_sn <> 0
	   group by teacher_sn   order by teacher_sn  ";
	
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($teacher_sn ,$year ,$semester)= $recordSet->FetchRow()) {	
	     $replace_data .= get_class_sect($teacher_sn  ,$year ,$semester ,$data ) ;
	}     
	//將 content.xml 的 tag 取代



	// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
	//$replace_data = $ttt->change_temp($tmp_arr,$data);
	
	//結合頭尾
    $replace_data = $doc_head . $replace_data . $doc_foot;
        
	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");
	
	//產生 zip 檔
	$sss = & $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	//header("Content-type: application/octetstream");
	header("Content-type: application/vnd.sun.xml.writer");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

	header("Expires: 0");

	echo $sss;

?>
