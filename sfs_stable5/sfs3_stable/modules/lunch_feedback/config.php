<?php



//---------------------------------------------------

// 這裡請放上程式的識別 Id，寫法： $ + Id + $

// SFS 開發小組幫您放入 SFS 的 CVS Server 時

// 會自動維護此一變數，注意! 請放在註解範圍中，如下所示：

//

//---------------------------------------------------



// $Id: config.php 5310 2009-01-10 07:57:56Z hami $



//---------------------------------------------------

//

// 模組系統相關的設定檔，一定要引入，所以使用 require !!!

//

//---------------------------------------------------



require_once "./module-cfg.php";



//---------------------------------------------------

// 這裡請引入 SFS 學務系統的相關函式庫。

//

// 至於要引入那些呢？

//

// 1. sfs3/include/config.php 經常是需要的。

//

// 2. 其它，就視您的程式目的而定。

// 請注意!!!!! 這裡只能使用 include_once 或 include

//---------------------------------------------------





// 引入 SFS 設定檔，它會幫您載入 SFS 的核心函式庫

include_once "../../include/config.php";




//取得模組參數設定

$m_arr = &get_sfs_module_set("lunch_feedback");

extract($m_arr, EXTR_OVERWRITE);



//---------------------------------------------------

// 這裡請引入您自己的函式庫

$c_day=array('','一','二','三','四','五','六','日');


//取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$session_tea_name = $_SESSION['session_tea_name'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ;
$class_id=$row["class_num"];

if (checkid($_SERVER['SCRIPT_FILENAME'],1)) {
	$class_id_arr=class_base();
	$class_id=($_POST[class_id])?$_POST[class_id]:key($class_id_arr);
	$is_admin=1;
}

$not_allowed="<CENTER><BR><BR><H2>您並非班級導師<BR>或者<BR>系統管理員尚未開放導師操作此功能!</H2></CENTER>";


?>
