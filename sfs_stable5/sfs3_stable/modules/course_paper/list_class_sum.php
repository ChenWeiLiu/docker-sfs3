<?php

// $Id: list_class_sum.php 7705 2013-10-23 08:58:49Z smallduh $

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


function get_class_sect($class_id , $sel_year="",$sel_seme="" ,$data="" ){
	global $CONN,$weekN ,$sections ;

	//取得班級資料
	$the_class=get_class_all($class_id);
	//取得考試所有設定
	$sm=&get_all_setup("",$sel_year,$sel_seme,$the_class[year]);
	//$sections=$sm[sections];	
	
	//每週的日數
	$dayn=sizeof($weekN)+1;

	//轉換班級代碼
	$class=class_id_2_old($class_id);
	$class_teacher=get_class_teacher($class[2]);
	$class_man=$class_teacher[name];
	$class_man_sn = $class_teacher[sn];
	
	$sql_select = "select course_id,teacher_sn,day,sector,ss_id,room  from score_course where year='$sel_year' and semester = '$sel_seme'  and class_id = '$class_id' order by day,sector";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
	}
	
	for ($w = 1; $w<= $dayn ; $w++) {
	    for ($s=1 ; $s<=$sections ; $s++) {
	        $k=$w ."_". $s;
	    	$tk=$w.$s;
	    	//先清空，再填值
	    	$temp_arr[$tk]="" ;
	    	$sectname= substr(get_ss_name("","","短",$a[$k]),0,4) ;
	    	if ($b[$k]<>0 and $class_man_sn<>$b[$k] ) $sectname = "($sectname)" ; 
	    	$temp_arr[$tk] =$sectname ;
	    }
	}     	
	
	//班級名
	$temp_arr["class"] = $the_class[name] ."\n" . $class_man;
	
    $ttt = new EasyZip;

    $class_data .= $ttt->change_temp($temp_arr,$data);
	return  $class_data  ;
}	
	
	


	$sql_select = "select max(sections) as ms  from score_setup where year=$sel_year and semester=$sel_seme ";

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
    

	$filename="course_班級總表_".$sel_year.$sel_seme.".sxw";


	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');	
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_head");
	$doc_head =iconv("Big5","UTF-8",$doc_head);
	//讀出 XML 檔尾
	
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content_foot");
	$doc_foot =iconv("Big5","UTF-8",$doc_foot);
	
	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content");
	$data =iconv("Big5","UTF-8",$data);
	
	$sql_select = "select class_id ,year ,semester   from score_course 
	   where year=$sel_year and semester=$sel_seme
	   group by class_id  order by class_id ";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($class_id ,$year ,$semester)= $recordSet->FetchRow()) 	
	     $replace_data .= get_class_sect($class_id ,$year ,$semester ,$data ) ;
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
