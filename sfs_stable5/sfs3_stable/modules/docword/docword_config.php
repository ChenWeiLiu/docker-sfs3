<?php

// $Id: docword_config.php 5310 2009-01-10 07:57:56Z hami $

/* 載入學務系統設定 */
require_once "../../include/config.php";
//載入函式庫
require_once "../../include/sfs_case_PLlib.php";

//設為全域變數狀態
require_once "../../include/sfs_core_globals.php";
//取得模組設
$m_arr = get_sfs_module_set("docword");
extract($m_arr, EXTR_OVERWRITE);

/* 程式項目 */
$prob = array("doc1_list.php"=>"收文區","doc_out_list.php"=>"發文區","doc_unit.php"=>"*單位管理","doc_list_print.php"=>"*銷毀清冊列印");

/* 公文狀態 */
$doc_stat_array = array("1" =>"未歸檔公文","2" =>"已歸檔公文","9"=>"已銷毀公文");

/* 子程式項目--收文 */
$prob_doc1 = array("doc_in.php"=>"新增收文","doc_print.php"=>"列印簽收單");

/* 子程式項目--發文 */
$prob_doc2 = array("doc_out.php"=>"新增發文","doc_print.php"=>"統計");

/*檢查碼 勿改*/
$ischecked = false;

/*! @function doc1_unit()
    @abstract 處室單位名稱
    @result 回傳陣列
*/
function doc_unit(){
	$temp = array();
	$query = "select * from sch_doc1_unit order by doc1_unit_num1 ";
	$result = mysql_query ($query);
	while ($row = mysql_fetch_array ($result))
		$temp["$row[doc1_unit_num1]"] = $row[doc1_unit_name];
	return $temp;
}

/*! @function doc_kind()
    @abstract 處室單位名稱
    @result 回傳陣列
*/
function doc_kind(){
	return array("1"=>"函","2"=>"書函","3"=>"令","4"=>"公告","5"=>"開會通知單","6"=>"簽","7"=>"文辦案件通知單","8"=>"文議案件通知單","9"=>"催辦案件通知單","10"=>"移交單");
}
/*! @function doc_kind()
    @abstract 保存年限
    @result 回傳陣列
*/
function doc_life(){
	return array("1"=>"1","3"=>"3","5"=>"5","10"=>"10","15"=>"15","30"=>"30","99"=>"永久");
}


//程式主項
function prog($key_prob){
	global $stud_id ,$prob,$ischecked;	
	
	echo "<table cellSpacing=0 cellPadding=0  align=center bgColor=#000000 border=0>
  <tbody>
    <tr>
      <td>
        <table cellSpacing=1 cellPadding=3 width=100% border=0>
          <tbody>
          <tr>";
          
	$i =1;
	while ( list( $key, $val ) = each( $prob ) ){
		if (substr($val,0,1) != "*" or ($ischecked)){
			if ($key_prob == $i++)
				echo "<td bgcolor=yellow ><a href=\"$key\">$val</a></td>"; 
			else
				echo "<td bgcolor=#CCCCCC><a href=\"$key\">$val</a></td>";   
		}
	}	
	if ($ischecked) //通過認證
		echo "<td bgcolor=#CCCCCC><a href=\"log.php?sel=out\">登出系統</a></td>";
	else
		echo "<td bgcolor=#CCCCCC><a href=\"log.php?sel=in\">登入系統</a></td>";
	
	echo "</tr></tbody>
        </table>
      </td>
    </tr>
  </tbody>
</table>";
}

//收文子項
function prog_doc1($key_prob){
	global $stud_id ,$prob_doc1;	
	echo "<table align=center  bgcolor=#D0DCE0  ><tr><td><img src=\"images/tree_end.gif\"></td>";
	$i =1;
	while ( list( $key, $val ) = each( $prob_doc1 ) ){
		if ($key_prob == $i++)
			echo "<td bgcolor=yellow ><a href=\"$key\">$val</a></td>"; 
		else
			echo "<td><a href=\"$key\">$val</a></td>";   
	}
	echo "</tr></table>";
}

//發文子項
function prog_doc2($key_prob){
	global $stud_id ,$prob_doc2;	
	echo "<table align=center  bgcolor=#D0DCE0 ><tr><td><img src=\"images/tree_end.gif\"></td>";
	$i =1;
	while ( list( $key, $val ) = each( $prob_doc2 ) ){
		if ($key_prob == $i++)
			echo "<td bgcolor=yellow ><a href=\"$key\">$val</a></td>"; 
		else
			echo "<td><a href=\"$key\">$val</a></td>";   
	}
	echo "</tr></table>";
}

?>
