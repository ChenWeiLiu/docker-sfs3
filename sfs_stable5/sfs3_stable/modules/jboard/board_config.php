<?php
// $Id: board_config.php 7779 2013-11-20 16:09:00Z smallduh $

/* 學務系統設定檔 */
require_once "../../include/config.php";

/* 學務系統函式庫 */
require_once "../../include/sfs_case_PLlib.php";
require_once "../../include/sfs_case_dataarray.php";

include "module-upgrade.php";

//取得模組設定
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

//2014.11.4 跑馬燈最長期限，若未設定，預設30日
if ($max_marquee_days==0) $max_marquee_days=30;

/* 上傳檔案目的目錄 */
$path_str = "school/jboard/";
set_upload_path($path_str);
$USR_DESTINATION = $UPLOAD_PATH.$path_str;

//附檔存放路徑 2014.08.08
$path_str = "school/jboard/files/";
set_upload_path($path_str);
/*附檔下載路徑 URL */
$download_file_path = $UPLOAD_URL.$path_str;
/*下載路徑 */
$Download_Path = $UPLOAD_PATH.$path_str;

$path_str = "school/jboard/tmp/";
set_upload_path($path_str);
/*圖檔暫存目錄*/
$USR_IMG_TMP = $UPLOAD_PATH.$path_str;



/*處室陣列*/
$ROOM=room_kind();

/*職稱陣列*/
$TEACHER_TITLE=title_kind();

//公布日數設定
$days = array("0"=>"永久保存","7"=>"一星期","14"=>"二星期","21"=>"三星期","30"=>"一個月","92"=>"三個月","183"=>"半年","365"=>"一年");

//首頁地址設定
$school_addr = $SCHOOL_BASE[sch_cname_s]."地址：".$SCHOOL_BASE[sch_post_num].$SCHOOL_BASE[sch_addr];

//首頁電話設定
$school_tel = "電話：".$SCHOOL_BASE[sch_phone];

//首頁傳真設定
$school_fax = "傳真：".$SCHOOL_BASE[sch_fax];

//行事曆連結
$calendar_url ="/school/calendar/";

//層級顏色
$position_color=array("0"=>"#0000FF","1"=>"#D200B4","2"=>"#1E4B00","3"=>"#7800B4","4"=>"#782D00","5"=>"#A50000","6"=>"#D200B4","7"=>"#003CB4","8"=>"#1E5A00","9"=>"#3C2D00");

//檢查是否為內部 ip
$insite_arr = explode(",",$insite_ip);

$is_home_ip = check_home_ip($insite_arr);

	//讀取可上傳附件大小的值 '' 
	$query="SELECT @@global.max_allowed_packet";
	$res=$CONN->Execute($query);
	$M1=$res->fields(0);  //MySQL
	$M1=floor($M1/(1024*1024));
	
	$M2=ini_get('post_max_size');
	$M2=substr($M2,0,strlen($M2)-1);
	
	$M3=ini_get('upload_max_filesize');
	$M3=substr($M3,0,strlen($M3)-1);
	
	$Max_upload=round(min($M1,$M2,$M3)/1.34,2);
	
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
        global $CONN;
        $res_arr=array();
        $i=0;
		
        if ($b_id) {
			        $b_id=intval($b_id);
        			$query="select org_filename,new_filename from jboard_files where b_id='$b_id'";
              $res=$CONN->Execute($query) or die($query);
              if ($res->RecordCount()>0) {
              	while($row=$res->fetchRow()) {
        					$i++;
        					$res_arr[$i]['org_filename']=$row['org_filename'];
        					$res_arr[$i]['new_filename']=$row['new_filename'];
        				}
        			}
               
        }
        return $res_arr;
}

//處理圖片的 <img src=""> ,傳回檔名
function GetImgFromHTML() {
	global $b_con,$sPath,$b_id,$CONN; //內容	
	$files_arr=array();
    if (stripos($b_con, '<img') !== false) {    	
        $imgsrc_regex = '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im';
        //由於系統在POST後有 addslashes , 所以要先 
        preg_match_all($imgsrc_regex, stripslashes($b_con), $matches);
        unset($imgsrc_regex);
        $i=0;
        if (is_array($matches) && !empty($matches)) {
        	//echo "<pre>";
        	//echo $b_con;
        	//print_r($matches[2]);
        	//echo "</pre>";
        	//exit();
          foreach ($matches[2] as $pic_key) {
   					$K=explode("/",$pic_key);
   					if (count($K)<=1) continue;
   					if (!is_file($sPath.$K[count($K)-1])) continue; //檔案不存在則不處理 				
   					$i++;
   					$oldNameKey=$K[count($K)-1];  									//原檔名					
   					$newNameKey=time().$i.date("ymd");							//新檔案   					   					  					
   					$files_arr[$i][1]=$oldNameKey; 									//用於刪除檔案
   					$files_arr[$i][2]=$newNameKey;			//用於載入檔案
   					//檢查, 若此檔案與前面重覆使用, 則把 $newNameKey 設為與前一個相同, 避免資料庫重覆存相同檔案
   					if ($i>1) {
   						for ($j=1;$j<$i;$j++) {
   					  	if ($files_arr[$j][1]==$files_arr[$i][1]) $files_arr[$i][2]=$files_arr[$j][2];
   						}  					
						}
   					$new_pic="img_show.php?b_id=$b_id&name=".$newNameKey;
   					$b_con=ereg_replace($pic_key,$new_pic, $b_con);						

 					} // end foreach 
            //return $files_arr;
          if (count($files_arr)) {
							foreach ($files_arr as $sFilename) {
								//檢查這個檔案有沒有重覆使用, 若有, 處理一次即可.
								$sql="select id from jboard_images where b_id='$b_id' and filename='".$sFilename[2]."'";
								$res=$CONN->Execute($sql);
								if ($res->RecordCount()>0) continue;
   								$sFP=fopen($sPath.$sFilename[1],"r");							//載入檔案
   								$sFilesize=filesize($sPath.$sFilename[1]); 				//檔案大小
   								$sFiletype=filetype($sPath.$sFilename[1]);  				//檔案屬性
       		
   								//轉碼 , 把檔案內容存入
   								$sFile=addslashes(fread($sFP,$sFilesize));
   								$sFile=base64_encode($sFile);
		  						$query="insert into jboard_images (b_id,filename,filesize,filetype,content) values ('$b_id','".$sFilename[2]."','$sFilesize','$sFiletype','$sFile')";
		  						$CONN->Execute($query) or die ($query);				  
   						} // end foreach
  						foreach ($files_arr as $sFilename) {
  							unlink($sPath.$sFilename[1]);
    					}
  						$query="update jboard_p set b_con='$b_con' where b_id='$b_id'";
  						$CONN->Execute($query) or die ($query);
  				} // end if
        } else {
            return false;
        }
    } else {    	 
        return false;
		}
}

//處理已不使用的圖
function DelImgNotInHTML() {
	global $b_con,$sPath,$b_id,$CONN; //內容
	
	//先取得所有的圖檔
	$b_id=intval($b_id);
	$sql="select filename from jboard_images where b_id='$b_id'";
	$res=$CONN->Execute($sql);
	$files_check=$res->GetRows();
	
  if ($res->RecordCount()>0) {
    if (stripos($b_con, '<img') !== false) {    	
        $imgsrc_regex = '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im';
        preg_match_all($imgsrc_regex, stripslashes($b_con), $matches);
        unset($imgsrc_regex);
        $i=0;
        if (is_array($matches) && !empty($matches)) {
          foreach ($matches[2] as $pic_key) {
   					$K=explode("=",$pic_key);
   					if (count($K)==0) continue;
   					$chk_pic=$K[count($K)-1];
   					//echo $chk_pic;
						foreach ($files_check as $k=>$v) {
							//echo $v['filename'];
						  if ($v['filename']==$chk_pic) $files_check[$k]['filename']="";
						}
 					} // end foreach             
        } // end if (is_array($matches) && !empty($matches))
    }
    
 		//若檔案未驗證存在, 刪除
 		foreach ($files_check as $v) {
 		 if ($v['filename']!="") {
			 $b_id=intval($b_id);
 		    $sql="delete from jboard_images where b_id='$b_id' and filename='".$v['filename']."'";
 		    $CONN->Execute($sql) or die ($sql);
 		 }
 		}
  } // end if $res->RecordCount()
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

		$dbquery = "select pro_kind_id from jboard_check where pro_kind_id ='$chk' and (post_office='$post_office' or post_office='99' or teach_title_id='$teach_title_id' or teacher_sn='$teacher_sn')";

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

function board_checksort($chk) {
   global $CONN;
   $sql="select board_is_sort from jboard_kind where bk_id='$chk'";
   $res=$CONN->Execute($sql);
   $board_is_sort=$res->fields[0];
   
   return $board_is_sort;
}

function get_board_kind_setup($chk) {
   global $CONN;
   $sql="select * from jboard_kind where bk_id='$chk'";
   $res=$CONN->Execute($sql);
   $setup=$res->fetchRow();
   
   return $setup;
}

function check_mysqli_param($param){
	if (!isset($param))$param="";
	return $param;
}

?>
