<?php
// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
require_once "./module-cfg.php";
require "../../include/config.php";

head('不再維護的模組');
echo "<div style='font-size:20px;margin:20px ;'>此模組已不再維護!! <br />畢業生作業相關功能,請改由<a href='../stud_grade/'>畢業生升學資料</a> 操作</div>";
foot();
exit;

require "../../include/sfs_case_studclass.php";
require "../../include/sfs_case_subjectscore.php";
require "../../include/sfs_case_PLlib.php";
require "../../include/sfs_oo_zip2.php";
require "my_fun.php";

//傳回畢業資料表的學年陣列
function get_grad_year() {
	global $CONN;	
	$query = "select  stud_grad_year from grad_stud order by stud_grad_year ";
	$result = $CONN->Execute($query) or trigger_error("SQL語法錯誤： $query", E_USER_ERROR);
	$i=0;
	while(!$result->EOF){ 
		$index[$i] = $result->fields[0];
		$i++;
		$result->MoveNext();
	}
	//去除重複值
	$rr=deldup($index);
	//$rr[$index_temp] = $result->fields[0]."學年第度";
	// return $rr;	
	return (!$rr) ? array() : $rr; 

	// 判斷 $rr 是否存在? 若不存在則傳回為空陣列	
}

//一個比較兩個陣列，然後去除重複的值的函數
function  deldup($a){

        $i=count($a);
        for  ($j=0;$j<=$i;$j++){
                      for  ($k=0;$k<$j;$k++){
                                    if($a[$k]==$a[$j]){
                                            $a[$j]="";
                                    }
                      }
        }
        $q=0;
        for($r=0;$r<=$i;$r++){
                      if($a[$r]!=""){
                                      $d[$q]=$a[$r];
                                      $q++;
                      }
          }

return  $d;
}

//算出這個值是陣列中第幾大的，a是一個數，b是一個陣列
function  how_big($a,$b){
    $sort=1;
    for($i=0;$i<count($b);$i++){
        if($a<$b[$i]) $sort++;
    }
    return  $sort;
}
?>
