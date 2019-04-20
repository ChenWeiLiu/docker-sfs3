<?php
// $Id: upload.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
//include "stick/stick-cfg.php";
//include_once "stick/dl_pdf.php";

sfs_check();

//主選單設定
//$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;
//印出檔頭
head();

//模組選單
print_menu($menu_p,$linkstr);

//預設值設定
$act=$_REQUEST[act];

$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year]; //目前學年
$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme]; //目前學期
$curr_seme = $sel_year.$sel_seme; //現在學年學期
//echo $_SESSION['session_log_id'];
//echo $sel_year;
//echo $sel_seme;
//取得任教班級代號
$class_num = get_teach_class();
//echo $class_num;
if ($class_num == '') {
        head("權限錯誤");
        stud_class_err();
        foot();
        exit;
}
//$curr_seme = curr_year().curr_seme(); //現在學年學期
//$class_num=get_teach_class();
//$class_all=class_num_2_all($class_num);
//$class_id=old_class_2_new_id($class_num,$sel_year,$sel_seme);


if (!ini_get('register_globals')) {
        ini_set("magic_quotes_runtime", 0);
        extract( $_POST );
        extract( $_GET );
        extract( $_SERVER );
}

//echo $act;
//執行動作判斷
if($act=="insert"){
	$save_result = savefile($curr_seme,$class_num);
	if (isset($save_result)){
	        global $CONN;
        	$sql = "insert into score_paper_upload (spu_sn,curr_seme,class_num,file_name,log_id,time) values ('','$curr_seme','$class_num','$save_result','".$_SESSION['session_log_id']."',NOW())";
	        $CONN->Execute($sql) or user_error("新增失敗！<br>$sql",256);
	}else{
		echo "檔案傳送過程發生錯誤，請再試一次，若錯誤仍然發生請洽管理人員";
		exit;
	}
}elseif($act=="modify"){
        $save_result = savefile($curr_seme,$class_num);
        if (isset($save_result)){
		global $CONN;
	        $sql = "UPDATE score_paper_upload SET curr_seme = '$curr_seme', class_num = '$class_num', file_name = '$save_result', log_id = '".$_SESSION['session_log_id']."',time = NOW() WHERE spu_sn = '".$_POST[spu_sn]."'";
        	$CONN->Execute($sql) or user_error("修改失敗！<br>$sql",256);
		header("location: $_SERVER[PHP_SELF]");
	}else{
		echo "檔案傳送過程發生錯誤，請再試一次，若錯誤仍然發生請洽管理人員";
                exit;
	}
}else{
//        $main=&score_paper_upload_mainForm($curr_seme,$class_num);
}
$main=&score_paper_upload_mainForm($curr_seme,$class_num);

//秀出網頁
head("自訂成績單");
echo $main;
foot();


//主要輸入畫面
function &score_paper_upload_mainForm($curr_seme,$class_num){
//        global $school_menu_p;
	$dbData=get_score_paper_upload_data($curr_seme,$class_num);
//        if($mode=="modify" and !empty($spu_sn)){
//                $dbData=get_score_paper_upload_data($curr_seme,$class_num);
//        }

        if(is_array($dbData) and sizeof($dbData)>0){
                foreach($dbData as $a=>$b){
                        $DBV[$a]=(!is_null($b))?$b:"";
                }
		$submit="modify";
                $tran_warn="<p><font color=red>您已於".$DBV[time]."完成上傳成績單的動作，若有異動，請選擇正確檔案後，按下「上傳」按鈕覆寫之前所傳送的檔案</font></p>";
        }else{
                $submit="insert";
        }

//        $submit=($mode=="modify")?"update":"insert";

        //說明
//        $readme=readme();

        //相關功能表
//        $tool_bar=&make_menu($school_menu_p);

        $main="
        <form action='$_SERVER[PHP_SELF]' method='post' ENCTYPE='multipart/form-data'>
        $tran_warn
        <table cellspacing='1' cellpadding='4' bgcolor='#C0C0C0' class='small'>
        <input type='hidden' name='spu_sn' value='$DBV[spu_sn]'>
        <tr bgcolor='#FFFFFF'>
        <td>檔名：<input type='file' name='userfile' size='50'>
        <br>
        </td></tr>
        <tr bgcolor='#00659C'><td align='center'>
		<input type='hidden' name='act' value='$submit'>
                 <input type='submit' value='上傳' class='b1'></td>
        </tr>
        </table>
        </form>
        ";
        if ($DBV[printed] == 1){
		$main="<p><font color=red>您於".$DBV[time]."所傳送的成績單已被教務處歸檔，若需重傳請與註冊組聯繫</font></p>";
	}
        return $main;
}

//取得某一筆資料
function get_score_paper_upload_data($curr_seme,$class_num){
        global $CONN;
        $sql_select="select spu_sn,file_name,time,printed from score_paper_upload where curr_seme ='$curr_seme' and class_num ='$class_num'";
        $recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
        $theData=$recordSet->FetchRow();
        return $theData;
}


function savefile($curr_seme,$class_num){
        global $SFS_PATH,$UPLOAD_PATH;
        $ext = strtolower(strrchr(str_replace("'","",stripslashes($_FILES['userfile']['name'])),'.'));
        if (!(($ext == ".pdf") || ($ext == ".sxw") || ($ext == ".doc"))){
        	die("上傳成績單只接受sxw,doc,pdf格式");
		return;
        }else{
		$filename=$curr_seme."_".$class_num.$ext;
	}
        if($_FILES['userfile']['type'] == "application/vnd.sun.xml.writer"){
                $filename=$curr_seme."_".$class_num.".sxw";
        }
        if (!is_dir($UPLOAD_PATH)) {
                die("上傳目錄 $UPLOAD_PATH 不存在！");
        }

        //統一上傳目錄
        $upath=$UPLOAD_PATH."score_paper_upload";
        if (!is_dir($upath)) {
                mkdir($upath) or die($upath."建立失敗！");
        }

        //上傳目的地
        $todir=$upath."/";
        if (!is_dir($todir)) {
                mkdir($todir) or die($todir."目的目錄建立失敗！");
        }

        $the_file=$todir.$filename;

        copy($_FILES['userfile']['tmp_name'],$the_file);
        unlink($_FILES['userfile']['tmp_name']);

	if(file_exists($the_file)) {
		return $filename;
        }else{
                die("上傳成績單失敗，請洽管理人員");
		return;
        }
}

?>
