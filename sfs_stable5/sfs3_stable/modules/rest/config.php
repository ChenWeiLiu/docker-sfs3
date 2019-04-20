<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";

	//模組更新程式
	require_once "./module-upgrade.php";

//函式庫
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_dataarray.php";


//已開發的API設定 - POST
$api_post["year_seme"]="取得所有學年學期";
$api_post["curr_year_seme"]="取得目前學年學期";
$api_post["classroom"]="取得學期班級";
$api_post["class_table"]="取得班級課表";
$api_post["class_tuneup"]="查詢可調課節次";
$api_post["teacher_table"]="取得教師課表";
$api_post["class_students_list"]="取得班級學生名單";
$api_post["teachers_list"]="取得在職教師名單";
$api_post["teacher_title"]="取得在職稱陣列";
$api_post["person_id"]="依身分證sha2取得單一教師職務資料";
$api_post["stud_status"]="取得在籍學生數統計";
$api_post["room_office"]="取得處室陣列";
$api_post["teacher_auth"]="取得教師密碼雜湊值(慎用)";
$api_post["bridge_check"]="查詢轉出學生資訊";
$api_post["bridge_download"]="取得學生學籍XML";

//已開發的API設定 - GET
$api_get["year_seme"]="取得所有學年學期";
$api_get["curr_year_seme"]="取得目前學年學期";
$api_get["classroom"]="取得學期班級";
$api_get["teacher_title"]="取得在職稱陣列";
$api_get["check_link"]="檢測連線狀態";
$api_get["room_office"]="取得處室陣列";

// 以下為公用函式
/****************************************************************************/

//取得呼叫端 ip
function getClientIP () {
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $myip = $_SERVER['HTTP_CLIENT_IP'];
    }else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $myip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $myip= $_SERVER['REMOTE_ADDR'];
    }
    return $myip;

}

//證證 是否為合法 ip
function matchIP($requestIP,$allow_ip=array()) {
    $match=0;
    //print_r($allow_ip);

    foreach ($allow_ip as $allowIP) {
        //echo $allowIP;

        $ips=explode(".",$allowIP);
        $check_count=(end($ips)=='*')?count($ips)-1:count($ips);
        $check_ips=explode(".",$requestIP);
        $match_count=0;
        for($i=0;$i<$check_count;$i++) {
            if ($check_ips[$i]==$ips[$i]) $match_count++;
        }

        if ($match_count==$check_count) $match=1;

    } // end foreach

    return $match;

}  // end function


function array_big5_to_utf8(array $data){

    foreach($data as $key=>$value){
        if (is_array($value)){
            $data[$key] = array_big5_to_utf8($value);
        }else{
            $value = big5_to_utf8($value);
            //$data[$key] = htmlspecialchars($value);
            $data[$key] = $value;
        }

    } // end foreach

    return $data;
} // end function

//big5轉 utf8
function big5_to_utf8($str){
    $str = mb_convert_encoding($str, "UTF-8", "BIG5");

    $i=1;

    while ($i != 0){
        $pattern = '/&#\d+\;/';
        preg_match($pattern, $str, $matches);
        $i = sizeof($matches);
        if ($i !=0){
            $unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
            $str = preg_replace("/$matches[0]/",$unicode_char,$str);
        } //end if
    } //end wile

    return $str;

}

//base64編碼
function array_base64_encode($data) {
    foreach($data as $key=>$value){
        if (is_array($value)){
            $data[$key] = array_base64_encode($value);
        }else{
            $data[$key]= base64_encode($value);
        }
    } // end foreach

    return $data;

} // end function

function api_manage_form ($row,$act) {
    global $api_get,$api_post;
    ?>
    <form method="post" name="form1" action="<?php echo $_SERVER['PHP_SELF'] ?>">
        <input type="hidden" name="act" value="<?php echo $act ?>">
        <input type="hidden" name="sn" value="<?php echo $row['sn'] ?>">

        <table border="0">
        <tr>
            <td>授權帳號</td>
            <td><input type="text" name="s_id" value="<?php echo $row['s_id']?>" size="20"></td>
        </tr>
        <tr>
           <td>&nbsp;</td>
           <td style="display:none;color:#FF0000;font-size:10pt" id="m_sid"></td>
        </tr>
        <tr>
            <td>認證密碼</td>
            <td><input type="text" name="s_pwd" value="<?php echo $row['s_pwd']?>" size="20"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="display:none;color:#FF0000;font-size:10pt" id="m_spwd"></td>
        </tr>
        <tr>
            <td>允許連入的IP</td>
            <td><input type="text" name="allow_ip" value="<?php echo $row['allow_ip']?>" size="80"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="color:#FF0000" id="m_allowip">用 , 隔開, 可使用 * 表示所有 ip 　，如: 163.17.43.110,163.17.40.*</td>
        </tr>
        <tr>
            <td valign="top">GET授權</td>
            <td>
                <?php
                $priv_get = explode(",", $row['method_get']);
                foreach ($api_get as $k=>$v) {
                    ?>
                    <input type="checkbox" name="api_get[]" value="<?php echo $k ?>" <?php if (in_array($k,$priv_get)) echo "checked";?>>
                    <?php echo $k."(".$v.")" ?><br>
                <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="display:none;color:#FF0000" id="m_methodget"></td>
        </tr>
        <tr>
            <td valign="top">POST授權</td>
            <td>
                <?php
                $priv_post = explode(",", $row['method_post']);
                foreach ($api_post as $k=>$v) {
                ?>
                <input type="checkbox" name="api_post[]" value="<?php echo $k ?>" <?php if (in_array($k,$priv_post)) echo "checked";?>>
                    <?php echo $k."(".$v.")" ?><br>
                <?php
                }
                ?>
            </td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td style="display:none;color:#FF0000;font-size:10pt" id="m_methodpost"></td>
        </tr>
    </table>
        <input type="button" value="儲存" id="submit-form">
    </form>
    <script>
        $("#submit-form").click(function(){
           var save=1;
            if (document.form1.s_id.value=='') {
                $("#m_sid").html("帳號不能是空白");
                $("#m_sid").css("display","table-cell");
                save=0;
            } else {
                $("#m_sid").css("display","none");
            }

            if (document.form1.s_id.value!='') {

                var sn=document.form1.sn.value;
                var who=document.form1.s_id.value;
                var params = {
                    act: 'check_id',
                    sn: sn,
                    who: who
                };

                $.ajax({
                    type: 'post',
                    url: 'manage.php',
                    data: params,
                    dataType: 'text',
                    error: function(xhr) {
                        alert('something was wrong!');
                    },
                    success: function(response) {
                        if (response==1) {
                            $("#m_sid").html("帳號已經存在!");
                            $("#m_sid").css("display","table-cell");
                            save=0;
                        } else {
                            $("#m_sid").css("display","none");
                        }
                    }
                });

            }

            if (document.form1.s_pwd.value=='') {
                $("#m_spwd").html("密碼不能是空白");
                $("#m_spwd").css("display","table-cell");
                save=0;
            } else {
                $("#m_spwd").css("display","none");
            }
            if (document.form1.allow_ip.value=='') {
                $("#m_allowip").html("您必須限制可呼叫的 IP 來源!");
                $("#m_allowip").css("display","table-cell");
                save=0;
            } else {
                $("#m_allowip").html("用 , 隔開, 可使用 * 表示所有 ip 　，如: 163.17.43.110,163.17.40.*");
            }


            if (save==1) document.form1.submit();
        });

    </script>

    <?php
} // end function
?>

