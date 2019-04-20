<?php

// $Id: board_config.php 8752 2016-01-13 12:38:48Z qfon $

/* 學務系統設定檔 */
require_once "../../include/config.php";
/* 學務系統函式庫 */
require_once "../../include/sfs_case_PLlib.php";

include "module-upgrade.php";

//取得模組設定
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

/* 上傳檔案目的目錄 */
$path_str = "school/board/";
set_upload_path($path_str);
$USR_DESTINATION = $UPLOAD_PATH.$path_str;

/*下載路徑 */
$download_path = $UPLOAD_URL.$path_str;


//公布日數設定
$days = array("1"=>"一天","2"=>"二天","3"=>"三天","4"=>"四天","5"=>"五天","6"=>"六天","7"=>"一星期","14"=>"二星期","21"=>"三星期","30"=>"一個月","92"=>"三個月","183"=>"半年","365"=>"一年");

//首頁地址設定
$school_addr = $SCHOOL_BASE[sch_cname_s]."地址：".$SCHOOL_BASE[sch_post_num].$SCHOOL_BASE[sch_addr];

//首頁電話設定
$school_tel = "電話：".$SCHOOL_BASE[sch_phone];

//首頁傳真設定
$school_fax = "傳真：".$SCHOOL_BASE[sch_fax];

//行事曆連結
$calendar_url ="/school/calendar/";


//檢查是否為內部 ip
$insite_arr = explode(",",$insite_ip);

$is_home_ip = check_home_ip($insite_arr);


// 檢查是否回簽
function CheckIsSigned($b_signs,$is_all=0){
		if (empty($b_signs))
		return false;
		$arr = explode(",",$b_signs);
		$temp_id_arr = array();
		$temp_time_arr = array();
		foreach($arr as $val){
			if ($val){
				$temp_arr = explode("^",$val);
				$temp_id_arr[] = $temp_arr[0];
				$temp_time_arr[$temp_arr[0]] = $temp_arr[1];
			}
		}
		if ($is_all){
			return $temp_time_arr;
		}
		else{
			if (in_array($_SESSION['session_tea_sn'],$temp_id_arr))
			return $temp_time_arr[$_SESSION['session_tea_sn']];
			else
			return false;
		}
	}


// 取得上傳文件
function board_getFileArray($b_id){
        global $USR_DESTINATION,$CONN;
        $res_arr=array();
        if ($b_id) {
                $sPath = $USR_DESTINATION.'/'.$b_id;
                if (!is_dir($sPath))
                        return false;

                $oHandle = opendir( $sPath );
                $res_arr = array();
                while ( $sFilename = readdir( $oHandle ) ) {
                        if ( $sFilename == "." || $sFilename == ".." )
                        continue;
                        $id_arr = explode('-',$sFilename);
                        $id = $id_arr[0];
                        $res_arr[$id]['new_filename'] = $sFilename;
                        $query="select id,org_filename from board_files where b_id='$b_id' and new_filename='$sFilename'";
                        $res=$CONN->Execute($query) or die($query);
                        if ($res->RecordCount()>0) {
                         $res_arr[$id]['id']=$res->fields['id'];
                         $res_arr[$id]['org_filename']=$res->fields['org_filename'];
                        } else {
                        	$res_arr[$id]['org_filename']="";
                        }
                }
        }
        return $res_arr;
}

function redir_str( $surl ,$str="",$sec) {
  //等 $sec 秒後，轉向到$sul 網頁，必須放在 header部份
  print("<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">");
  print("<meta http-equiv=\"refresh\" content=\"" . $sec . "; url=" . $surl ."\">  \n" ) ;
  print("</head><body>");
  print($str);
  print("</body></html>");
  //備註，php 中函數 Header("Location:cal2.php") ;會馬上轉址，無法出現訊息及等待
}

//檢查使用權
function board_checkid($chk){
	global $conID,$session_log_id ,$app_name,$session_tea_sn;
	$chkary= explode ("/",$chk);

	$pp	= $chkary[count($chkary)-2];
	$post_office = -1;
	$teach_title_id = -1;
	$teach_id = -1 ;
	$dbquery = " select a.teacher_sn,a.login_pass,a.name,b.post_office,b.teach_title_id ";
	$dbquery .="from teacher_base a ,teacher_post b  ";
	$dbquery .="where a.teacher_sn = b.teacher_sn and a.teacher_sn='$_SESSION[session_tea_sn]'";
	$result= mysql_query($dbquery,$conID)or ("<br>資料連結錯誤<br>\n $dbquery");

	if (mysql_num_rows($result) > 0){
		$row = mysql_fetch_array($result);
		$post_office = $row["post_office"];
		$teach_title_id	= $row["teach_title_id"];
		$teacher_sn =	$row["teacher_sn"];

		$dbquery = "select pro_kind_id from board_check where pro_kind_id ='$chk' and (post_office='$post_office' or post_office='99' or teach_title_id='$teach_title_id' or teacher_sn='$teacher_sn')";

		$result= mysql_query($dbquery,$conID)or die("<br>資料庫連結錯誤<br>\n $dbquery");
		if (mysql_num_rows ($result)>0)	{
			return true;
		}
		else
			return false;
	}
	else
		return false;
}

function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}
?>
