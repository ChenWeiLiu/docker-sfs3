<?php

// $Id: board_show.php 7779 2013-11-20 16:09:00Z smallduh $
// --系統設定檔
ini_set('memory_limit', '-1');

include "board_config.php";
include_once "../../include/sfs_case_dataarray.php";

//echo $_GET['api_key'].";".$_GET['act'];
//mysqli
$mysqliconn = get_mysqli_conn();

if ($_GET['api_key'] != $api_key) {
    $row[1] = "API Key 錯誤!";
}
//測試
if ($_GET['act'] == 'test') {
    $row = "恭喜! 連線成功!";
    $row = base64_encode(addslashes($row));
    //$row[1]="ok!";
}



if ($_GET['act'] == 'GetPages') {
    //把過期置頂取消
    $CONN->Execute("UPDATE `jboard_p` SET b_sort = '100',top_days='0' WHERE (to_days(b_open_date) + top_days) < to_days(now( )) and top_days>0");

    //應傳入的條件
    $bk_id = $_GET['bk_id'];
    $page_count = $_GET['page_count'];  //每頁幾筆 
    //檢查是否開放 jboard_kind 的 board_is_public=1
    //開始組合 sql
    //$sql_select = "select b_id from jboard_p  where bk_id='$bk_id' ";
    $sql_select = "select count(b_id) from jboard_p  where bk_id=? ";

    //同步呈現板區資料
    /*
      $sql_sync="select bk_id,synchronize_days from jboard_kind where synchronize='$bk_id' and bk_id<>'$bk_id'";
      $res=$CONN->Execute($sql_sync) or die ($sql_sync);
     */

///mysqli
//$sql_sync="select bk_id,synchronize_days from jboard_kind where synchronize=? and bk_id<>?";
    $sql_sync = "select count(*) from jboard_kind where synchronize=? and bk_id<>?";
    $stmt = "";
    $stmt = $mysqliconn->prepare($sql_sync);
    $stmt->bind_param('ss', $bk_id, $bk_id);
    $stmt->execute();
    $stmt->bind_result($cc);
    $stmt->fetch();
    $stmt->close();
///mysqli
    //if ($res->RecordCount()) {
    if ($cc) {

///mysqli
        $sql_sync = "select bk_id,synchronize_days from jboard_kind where synchronize=? and bk_id<>?";
        $stmt = "";
        $stmt = $mysqliconn->prepare($sql_sync);
        $stmt->bind_param('ss', $bk_id, $bk_id);
        $stmt->execute();
        $stmt->bind_result($bk_idx, $synchronize_days);
///mysqli
        //while ($row=$res->fetchRow()) {
        //$SYNC[]=array('bk_id'=>$row['bk_id'],'days'=>$row['synchronize_days']); //同步板區 id
        while ($stmt->fetch()) {
            $SYNC[] = array('bk_id' => $bk_idx, 'days' => $synchronize_days); //同步板區 id
        }
    }
    //同步呈現的板區
    if (count($SYNC) > 0) {
        foreach ($SYNC as $v) {
            $sql_select .=" or (bk_id='" . $v['bk_id'] . "' and to_days(b_open_date)+ " . $v['days'] . " > to_days(curdate()))";
        }
    }

    $sql_select.=" order by b_sort,b_open_date desc ,b_post_time desc ";

///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($sql_select);
    $stmt->bind_param('s', $bk_id);
    $stmt->execute();
    $stmt->bind_result($tol_num);
    $stmt->fetch();
    $stmt->close();
///mysqli
    //$result = $CONN->Execute($sql_select) or die ($sql_select);
    //$tol_num= $result->RecordCount($result);
    //計算頁數
    if ($tol_num % $page_count > 0)
        $tolpage = intval($tol_num / $page_count) + 1;
    else
        $tolpage = intval($tol_num / $page_count);

    $row = $tolpage;
}

//搜尋頁
if ($_GET['act'] == 'GetSearch') {
	$forbidden_key=array("'" => "&#39;", "\"" => "&quot;");
  $search_startday=strtr($_GET['search_startday'],$forbidden_key);
  $search_endday=strtr($_GET['search_endday'],$forbidden_key);
  $search_room=intval($_GET['search_room']);
  $search_teachertitle=intval($_GET['search_teachertitle']);
  $search_key=$_GET['search_key'];
  $search_limit=$_GET['search_limit'];
  $page_office=$_GET['page_office'];
  
  $sql_select="select * from jboard_p where b_open_date>='$search_startday' and b_open_date<='$search_endday'";
  
	//有限制處室
	if ($search_room!="") {
    $sql_select.=" and b_unit='$search_room'";	
	}
	//有限制職稱
	if ($search_teachertitle!="") {
    $sql_select.=" and b_title='$search_teachertitle'";	
	}		
	//有限制關鍵字
	if ($search_key!="") {
		//$search_key=preg_replace("/[\'\"]+/" , '' ,$search_key);
		
		$search_key=strtr($search_key,$forbidden_key); 
		
	 	$search_key=iconv("UTF-8","BIG5//IGNORE",$search_key);
	 	$sql_select.=" and b_sub like '%".addslashes($search_key)."%'";
	}
  //有限制搜尋範圍
	if ($search_limit=='1') {
	  $offices=explode(",",$page_office);
	  $sql_select2="";
		  foreach ($offices as $OFFICE) {
		   $sql_select2.="bk_id='".$OFFICE."' or ";
		  }
		  $sql_select2=substr($sql_select2,0,strlen($sql_select2)-4);
		  
		 $sql_select.=" and (".$sql_select2.")"; 
	}
 
 /*
  echo $sql_select;
  exit();
*/
  //完成 sql
  $sql_select.=" order by b_open_date desc limit 100";

  $result = $CONN->Execute($sql_select) or die ($sql_select);
  $ROW=$result->GetRows();
  //轉碼
  $row=array();
  foreach ($ROW as $k=>$v) {
        $row[$k]=array_base64_encode($v);
  }

	} // end if GetSearch
		 
		 
//取得所有處室
if ($_GET['act'] == 'GetRooms') {
    /* 處室陣列 */
    $ROOM = room_kind();
    $row = array_base64_encode($ROOM);
}

//取得所有職稱
if ($_GET['act'] == 'GetTeacherTitle') {
    /* 職稱陣列 */
    $TEACHER_TITLE = title_kind();
    $row = array_base64_encode($TEACHER_TITLE);
}


if ($_GET['act'] == 'GetMarquee') {
    $bk_id = $_GET['bk_id'];

    //跑馬燈 $html_link
    //$query = "select b_id,b_sub,b_is_intranet,b_title from jboard_p where bk_id='$bk_id' and";        
    $query = "select b_id,b_sub,b_is_intranet,b_title from jboard_p where bk_id=? and";
    $query.=" b_is_marquee = '1' and ((to_days(b_open_date)+b_days > to_days(current_date())) or (to_days(b_open_date)+" . $max_marquee_days . " > to_days(current_date())));";

    //$result = $CONN->Execute($query) or die($query);
///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($query);
    $stmt->bind_param('s', $bk_id);
    $stmt->execute();
    $stmt->bind_result($b_id, $b_sub, $b_is_intranet, $b_title);
///mysqli
    //$ROW=$result->GetRows();
///mysqli
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field()) {
        $params[] = &$row[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $params);
    while ($stmt->fetch()) {
        foreach ($row as $key => $val) {
            $c[$key] = $val;
        }
        $ROW[] = $c;
    }
    $stmt->close();
///mysqli
    //轉碼
    $row = array();
    foreach ($ROW as $k => $v) {
        $v['b_title'] = $TEACHER_TITLE[$v['b_title']];
        $row[$k] = array_base64_encode($v);
    }
}

if ($_GET['act'] == 'GetList') {

    //應傳入的條件
    $bk_id = $_GET['bk_id'];
    $post_page = $_GET['post_page'];    //第幾頁
    $page_count = $_GET['page_count'];  //每頁幾筆 
    $search_key = $_GET['search_key'];  //有沒有索引條件
    //檢查是否開放 jboard_kind 的 board_is_public=1
    //$sql_boardname="select board_name from jboard_kind where bk_id='$bk_id'";
    $sql_boardname = "select board_name from jboard_kind where bk_id=?";
    //$res=$CONN->Execute($sql_boardname);
///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($sql_boardname);
    $stmt->bind_param('s', $bk_id);
    $stmt->execute();
    $stmt->bind_result($board_name);
    $stmt->fetch();
    $stmt->close();
///mysqli
    //$board_name=$res->fields['board_name'];
    //開始組合 sql
    //$sql_select = "select a.*,b.board_name from jboard_p a,jboard_kind b where a.bk_id=b.bk_id and ( a.bk_id='$bk_id' ";
    $sql_select = "select a.b_id,a.bk_id,a.b_open_date,a.b_days,a.b_unit,a.b_title,a.b_name,a.b_sub,a.b_con,a.b_hints,a.b_url,a.b_post_time,a.b_own_id,a.b_is_intranet,a.b_is_marquee,a.b_signs,a.b_is_sign,a.teacher_sn,a.b_sort,a.top_days,b.board_name from jboard_p a,jboard_kind b where a.bk_id=b.bk_id and ( a.bk_id=? ";

    //同步呈現板區資料
    //$sql_sync="select bk_id,synchronize_days from jboard_kind where synchronize='$bk_id' and bk_id<>'$bk_id'";
    $sql_sync = "select count(*) from jboard_kind where synchronize=? and bk_id<>?";
///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($sql_sync);
    $stmt->bind_param('ss', $bk_id, $bk_id);
    $stmt->execute();
    $stmt->bind_result($cc1);
    $stmt->fetch();
    $stmt->close();
///mysqli
    //$res=$CONN->Execute($sql_sync) or die ($sql_sync);
    //if ($res->RecordCount()) {
    if ($cc1) {
        ///mysqli
        $sql_sync = "select bk_id,synchronize_days from jboard_kind where synchronize=? and bk_id<>?";
        $stmt = "";
        $stmt = $mysqliconn->prepare($sql_sync);
        $stmt->bind_param('ss', $bk_id, $bk_id);
        $stmt->execute();
        $stmt->bind_result($bk_idx, $synchronize_days);
        ///mysqli
        //while ($row=$res->fetchRow()) {		
        //$SYNC[]=array('bk_id'=>$row['bk_id'],'days'=>$row['synchronize_days']); //同步板區 id
        while ($stmt->fetch()) {
            $SYNC[] = array('bk_id' => $bk_idx, 'days' => $synchronize_days); //同步板區 id
        }
    }
    //同步呈現的板區
    if (count($SYNC) > 0) {
        foreach ($SYNC as $v) {
            $sql_select .=" or (a.bk_id='" . $v['bk_id'] . "' and to_days(a.b_open_date)+ " . $v['days'] . " > to_days(curdate()))";
        }
    }


    //有輸入條件	
    if ($search_key != "") {
        //$search_key=iconv("UTF-8","BIG5//IGNORE",$search_key);
        //$sql_select.=") and a.b_sub like '%".addslashes($search_key)."%'";
        $sql_select.=") and a.b_sub like ?";
    }

    $sql_select.=(($search_key == "") ? ")" : "") . " order by a.b_sort,a.b_open_date desc ,a.b_post_time desc ";

    //取出資料
    $sql_select .= " limit " . ($post_page * $page_count) . ", $page_count";

    //$result = $CONN->Execute($sql_select) or die ($sql_select);
    ///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($sql_select);
    $stmt->mbind_param('s', $bk_id);
    if ($search_key != "") {
        $search_key = iconv("UTF-8", "BIG5//IGNORE", $search_key);
        $search_key = "%" . addslashes($search_key) . "%";
        $stmt->mbind_param('s', $search_key);
    }
    $stmt->execute();
    $stmt->bind_result($b_id, $bk_id, $b_open_date, $b_days, $b_unit, $b_title, $b_name, $b_sub, $b_con, $b_hints, $b_url, $b_post_time, $b_own_id, $b_is_intranet, $b_is_marquee, $b_signs, $b_is_sign, $teacher_sn, $b_sort, $top_days, $board_name);

///mysqli			
    //$ROW=$result->GetRows();
///mysqli
    $meta = $stmt->result_metadata();
    while ($field = $meta->fetch_field()) {
        $params[] = &$row[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $params);
    while ($stmt->fetch()) {
        foreach ($row as $key => $val) {
            $c[$key] = $val;
        }
        $ROW[] = $c;
    }
    $stmt->close();
///mysqli	  
    //轉碼
    $row = array();
    foreach ($ROW as $k => $v) {
        $v['board_name'] = $board_name;
        $row[$k] = array_base64_encode($v);
    }
}



//讀取一篇文章
if ($_GET['act'] == 'GetOne' and $_GET['b_id'] != '') {
    $b_id = intval($_GET['b_id']);
    $query = "update jboard_p set b_hints = b_hints+1 where b_id='$b_id' ";
    $res = $CONN->Execute($query);
    $query = "select  a.*,b.board_name from jboard_p a,jboard_kind b where a.bk_id=b.bk_id  and a.b_id='$b_id'";
    $result = $CONN->Execute($query);
    $row = $result->fetchRow();
    $row = array_base64_encode($row);
}

//讀取一篇文章中的附件列表
if ($_GET['act'] == 'GetFileNameList' and $_GET['b_id'] != '') {
    $b_id = intval($_GET['b_id']);
    $query = "select new_filename,org_filename from jboard_files where b_id='$b_id'";
    $result = $CONN->Execute($query);
    $ROW = $result->GetRows();

    //轉碼
    $row = array();
    foreach ($ROW as $k => $v) {
        $row[$k] = array_base64_encode($v);
    }
}


//讀取圖 , 不用轉碼
if ($_GET['act'] == 'GetImage' and $_GET['b_id'] != "" and $_GET['name'] != "") {
    $name = $_GET['name'];
    $b_id = intval($_GET['b_id']);
    //$query="select filetype,content from jboard_images where b_id='".$b_id."' and filename='".$name."'";
    $query = "select filetype,content from jboard_images where b_id=$b_id and filename=?";
///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($query);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($filetype, $content);
    $stmt->fetch();
    $stmt->close();
///mysqli				
    //$res=$CONN->Execute($query) or die($query);
    //$row= $r:qexites->fetchRow();
    $row['filetype'] = $filetype;
    $row['content'] = $content;
}

//讀取檔案 , 不用轉碼
if ($_GET['act'] == 'GetFile' and $_GET['b_id'] != "" and $_GET['name'] != "") {
    $name = $_GET['name'];
    $b_id = intval($_GET['b_id']);
    //不再讀取資料 MySQL 資料庫內 content 值 , 2014.09.30起改為檔案方式
    //$query="select org_filename,filetype,content from jboard_files where b_id='".$b_id."' and new_filename='".$name."'";
    //$query="select org_filename,filetype from jboard_files where b_id='".$b_id."' and new_filename='".$name."'";
    $query = "select org_filename,filetype from jboard_files where b_id='" . $b_id . "' and new_filename=?";
///mysqli
    $stmt = "";
    $stmt = $mysqliconn->prepare($query);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($org_filename, $filetype);
    $stmt->fetch();
    $stmt->close();
///mysqli				


    $row['filetype'] = $filetype;

    //$res=$CONN->Execute($query) or die($query);
    //$row= $res->fetchRow();	
    $row['org_filename'] = base64_encode(addslashes($org_filename));

    // 2014.09.30 改為讀檔方式, 把檔案讀入, 編碼, 存入變數 $row['content'] , 不再讀取資料 MySQL 資料庫內 content 值
    $sFP = fopen($Download_Path . $name, "r");    //載入檔案
    $sFilesize = filesize($Download_Path . $name);  //檔案大小       		
    $sFile = addslashes(fread($sFP, $sFilesize));
    $row['content'] = base64_encode($sFile);
}


//送出
exit(json_encode($row));

//將陣列編碼
function array_base64_encode($arr) {
    $B_arr = array();
    foreach ($arr as $k => $v) {
        $B_arr[$k] = base64_encode(addslashes($v));
    }
    return $B_arr;
}
