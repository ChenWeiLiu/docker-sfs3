<?php

// $Id: list_all_teacher.php 8103 2014-08-31 16:38:02Z infodaes $

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





	

	$sql_select = "select max(sections) as ms  from score_setup where year=$sel_year and semester=$sel_seme  ";

	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (!$recordSet->EOF) {
		$sections= $recordSet->fields["ms"];
        $recordSet->MoveNext();
	}	
	    
	
	if ( $sections>7){ 
	  //$oo_path = "ooo_all_class8";
	  $sections = 8 ;
	}else{    
      //$oo_path = "ooo_all_class";
      $sections = 7 ;
    }  

  $oo_path = "ooo_self";
	$filename="teacher_all_".$sel_year.$sel_seme.".sxw";


	//新增一個 zipfile 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);
	$ttt->addDir('META-INF');	
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");



	//讀出 XML 檔頭

	$doc_head = $ttt->read_file(dirname(__FILE__)."/$oo_path/content-h.xml");
	//$doc_head =iconv("Big5","UTF-8",$doc_head);
	//讀出 XML 檔尾
	
	$doc_foot = $ttt->read_file(dirname(__FILE__)."/$oo_path/content-e.xml");
	//$doc_foot =iconv("Big5","UTF-8",$doc_foot);
	
	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content-c.xml");
	//$data =iconv("Big5","UTF-8",$data);
	
	
	/*
	$sql_select = "select teacher_sn ,year ,semester from score_course 
	   where year=$sel_year and semester=$sel_seme and teacher_sn <> 0
	   group by teacher_sn order by teacher_sn  ";
	
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($teacher_sn ,$year ,$semester)= $recordSet->FetchRow()) {	
	     $replace_data .= get_class_sect($teacher_sn  ,$year ,$semester ,$data ) ;
	     //加入換頁
		   $replace_data .="<text:p text:style-name=\"break_page\"/>" ;
	     
	} 
	*/
	
	$sql_select = "select DISTINCT teacher_sn from score_course 
	   where year=$sel_year and semester=$sel_seme and teacher_sn>0 order by teacher_sn";
	
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while (list($teacher_sn)= $recordSet->FetchRow()) {	
	     $replace_data .= get_class_sect($teacher_sn  ,$sel_year ,$sel_seme ,$data ) ;
	     //加入換頁
		   $replace_data .="<text:p text:style-name=\"break_page\"/>" ;
	     
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

  exit ;


function get_class_sect($teacher_sn  , $sel_year="",$sel_seme="" ,$data="" ){
	global $CONN,$weekN ,$sections,$midnoon;
    

	//每週的日數
	$dayn=sizeof($weekN)+1;
    
  $global_classname = get_global_class_name($sel_year,$sel_seme,"短") ;
	
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
	/*
		while (list($course_id,$teacher_sn,$day,$sector,$ss_id,$room)= $recordSet->FetchRow()) {
		$k=$day."_".$sector;
		$a[$k]=$ss_id;
		$b[$k]=$teacher_sn;
		$r[$k]=$room;
	}
	*/
	/*
	for ($w = 1; $w<= $dayn ; $w++) {
	    for ($s=1 ; $s<=$sections ; $s++) {
	        $k=$w ."_". $s;
	    	$tk=$w.$s;
	    	//先清空，再填值
	    	$temp_arr[$tk]="" ;
	    	$temp_arr[$tk] = substr(get_ss_name("","","短",$a[$k]),0,4) . "\n" .$c[$k] ;
	    }
	}     	
	*/
  //echo $teach_name ;
	if(!empty($teach_name)){
		//取得節次時間
		$section_table=section_table($sel_year,$sel_seme);
		//取得課表
		for ($j=1;$j<=$sections;$j++){
			//若是最後一列要用不同的樣式
			$ooo_style=($j==$sections)?"4":"2";
			
			if ($j==$midnoon){
				//預設的午休OpenOffice.org表格程式碼
				$all_class.= "<table:table-row table:style-name=\"course_tbl.3\"><table:table-cell table:style-name=\"course_tbl.A3\" table:number-columns-spanned=\"6\" table:value-type=\"string\"><text:p text:style-name=\"P12\">午間休息</text:p></table:table-cell><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/><table:covered-table-cell/></table:table-row>";
			}
			
			$all_class.="<table:table-row table:style-name=\"course_tbl.1\"><table:table-cell table:style-name=\"course_tbl.A".$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P8\">第 $j 節</text:p><text:p text:style-name=\"P15\">{$section_table[$j][0]}~{$section_table[$j][1]}</text:p></table:table-cell>";
			//列印出各節
			$wn=count($weekN);
			for ($i=1;$i<=$wn;$i++) {
				//若是最後一格要用不同的樣式
				$ooo_style2=($i==$wn)?"F":"B";
			
				$k2=$i."_".$j;
				
				$teacher_search_mode=(!empty($tsn) and $tsn==$b[$k2])?true:false;
				//科目
				$subject_sel=&get_ss_name("","","短",$a[$k2]);
				
				//班級
				$class_sel=$c[$k2];
				//每一格
 		    $all_class.="<table:table-cell table:style-name=\"course_tbl.".$ooo_style2.$ooo_style."\" table:value-type=\"string\"><text:p text:style-name=\"P9\">$subject_sel</text:p><text:p text:style-name=\"P10\">$class_sel</text:p></table:table-cell>";
			}
			$all_class.="</table:table-row>";
		}
		
	}else{
		$all_class="";
	}	

 // echo $all_class ;
	$temp_arr["city_name"] = "";  //$s[sch_sheng];
	$temp_arr["school_name"] = $s[sch_cname];
	//$temp_arr["Cyear"] = $stu[stud_name];
	//$temp_arr["stu_class"] = $class[5];
	$temp_arr["stu_class"] = $teach_name;
	$temp_arr["teacher_name"] = $class_man;
	$temp_arr["year"] = $sel_year;
	$temp_arr["seme"] = $sel_seme;
	$temp_arr["all_course"] = $all_class;
	
	
	//班級名
	//$temp_arr["class"] = $teach_name ;
	
    $ttt = new EasyZip;

    $class_data = $ttt->change_temp($temp_arr,$data,0);

	return  $class_data  ;
}	
	

?>
