<?php
// ���J�]�w��
require "stud_reg_config.php";
include_once "../../include/sfs_case_dataarray.php";



// �{���ˬd
sfs_check();


$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$curr_seme = $_GET['curr_seme'];
$target_page = $SFS_PATH_HTML . 'modules/stud_reg/stud_base_upload_bc.php';
if (function_exists('curl_ini')) {
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => "https://oidc.tc.edu.tw/api/real-ip",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => "api:oidcuser"
    );
    curl_setopt_array($ch, $options);
    $real_ip = curl_exec($ch);
    curl_close($ch);
} else {
    if (!$real_ip) {
        $real_ip = file_get_contents('http://phihag.de/ip/');
    }
}


if (!$curr_seme) {
    $sel_year = curr_year(); //��ܾǦ~
    $sel_seme = curr_seme(); //��ܾǴ�
    $curr_seme = curr_year() . curr_seme(); //�{�b�Ǧ~�Ǵ�
} else {
    $sel_year = substr($curr_seme, 0, 3);
    $sel_seme = substr($curr_seme, 3, 1);
    $curr_seme = $sel_year . $sel_seme;
}
$cookie_sch_id = $_COOKIE['cookie_sch_id'];
if ($cookie_sch_id == null) {
    $cookie_sch_id = get_session_prot();
}
//�P�_�O�_�O�x�����Ǯ�
$isTaichung = substr($SCHOOL_BASE['sch_id'], 0, 2);

head();
print_menu($menu_p);
?>
<script type="text/javascript">
    $(document).ready((function () {
        $(document).ajaxComplete($.unblockUI);

        var checkcardurl = "https://localhost:8443/checkcard/exists";
        $("#btnCheckCard").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            console.log(checkcardurl);

            $.get(checkcardurl,
                    function (data) {
                        console.log(JSON.stringify(data));
                        obj = JSON.parse(JSON.stringify(data));
                        alert(obj.status);
                    }
            ).error(
                    function (err) {
                        alert('�нT�w�L�����A���v�Ұ�');
                    });
        });

        var studbaseuploadrurl = "https://localhost:8443/sr/upload/studbase";
        console.log(studbaseuploadrurl);
        $("#btnStudBaseUploadSR").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            if (!$("#pin").val()) {
                $.unblockUI();
                alert('�п�JPIN�X');
                $("#pin").focus();
            } else {
                console.log($("#pin").val());
                $.ajax({
                    url: studbaseuploadrurl,
                    dataType: "json",
                    contentType: 'application/json',
                    method: "POST",
                    data: JSON.stringify({
                        "password": $("#pin").val(),
                        "cookieschid": <?php echo json_encode($cookie_sch_id) ?>,
                        "eduid": <?php echo json_encode(trim($SCHOOL_BASE['sch_id'])) ?>,
                        "currseme": <?php echo json_encode($curr_seme) ?>,
                        "sessionid": <?php echo json_encode($session_id) ?>,
                        "useragent": <?php echo json_encode($useragent) ?>,
                        "targetpage": <?php echo json_encode($target_page) ?>,
                        "submitip": <?php echo json_encode($real_ip) ?>,
                        "uploadid": <?php echo json_encode(trim($_SESSION['session_log_id'])) ?>,
                        "uploadname": <?php echo json_encode(trim(iconv("BIG5", "UTF-8", $_SESSION['session_tea_name']))) ?>
                    }),
                    success: function (data, textStatus, jqXHR) {
                        console.log(JSON.stringify(data));
                        obj = JSON.parse(JSON.stringify(data));
                        alert(obj.status);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        alert('�нT�w�L�����A���v�Ұ�');
                    }
                });
            }
        });
    }));
</script>


<fieldset>
    <legend>���Ǧ~�צb�y�͸�Ʀ۰ʤW��(�O�����N�Ǻޱ��t��)</legend>
    <?php
    if ($isTaichung == '06' || $isTaichung == '19') {
        echo "<button id='btnCheckCard'>�ˬd�O�_�v���J����</button>&nbsp;&nbsp;�п�J�d��PIN�X�G<input type='password' id='pin'>&nbsp;&nbsp;<button id='btnStudBaseUploadSR'>���Ǧ~�צb�y�͸�Ʀ۰ʶפJ�O�����N�Ǻޱ��t��</button><p>
    <a href='https://localhost:8443/checkcard/exists' target='_blank' style='-webkit-appearance: button;-moz-appearance: button;appearance: button;text-decoration: none;color: initial;'>���ګH���O�������ҷL�����A��</a>&nbsp;&nbsp;
    <a href='https://oidc.tanet.edu.tw/miniserver/DeskTopMiniServer.jnlp' target='_blank' style='-webkit-appearance: button;-moz-appearance: button;appearance: button;text-decoration: none;color: initial;'>���ڤU���O�������ҷL�����A��</a></p>";
    } else {
        echo '<h3>�ܩ�ۡA���\��ȨѻO�������ݾǮըϥ�</h3>';
    }
    ?>

</fieldset>
<div id="domMessage" style="display:none;"> 
    <img src="<?php echo $SFS_PATH_HTML ?>/images/busy.gif" alt="PORCESSING" id="loader"/>&nbsp;&nbsp;����Ū����...�еy��...
</div>

<?php
foot();
?>
