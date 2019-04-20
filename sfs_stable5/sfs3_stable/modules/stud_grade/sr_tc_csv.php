<?php
//$Id:  $
//���J�]�w��
require ("config.php");

// �{���ˬd
sfs_check();

//smart card mini server
$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
if (!$curr_seme) {
    $sel_year = curr_year(); //��ܾǦ~
    $sel_seme = curr_seme(); //��ܾǴ�
    $curr_seme = curr_year() . curr_seme(); //�{�b�Ǧ~�Ǵ�
} else {
    $sel_year = substr($curr_seme, 0, 3);
    if (substr($sel_year, 0, 1) == "0")
        $sel_year = substr($sel_year, 1, 2);
    $sel_seme = substr($curr_seme, 3, 1);
    $curr_seme = $sel_year . $sel_seme;
}
$target_page = $SFS_PATH_HTML . 'modules/stud_grade/sr_tc_upload_bc.php';
if (function_exists('curl_init')) {
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
$cookie_sch_id = $_COOKIE['cookie_sch_id'];
if ($cookie_sch_id == null) {
    $cookie_sch_id = get_session_prot();
}

$UP_YEAR = ($IS_JHORES == 0) ? 6 : $UP_YEAR = 9; //�P�_�ꤤ�p
$do_upload_script = "var targeturi = encodeURI('" . $SFS_PATH_HTML . "modules/stud_grade/session_upload.php');window.open(targeturi);";
//�P�_�O�_�O�x�����Ǯ�
$isTaichung = substr($SCHOOL_BASE['sch_id'], 0, 2);
$postBtn = "�O�����N�Ǻޱ��t����Xcsv��";
$class_name = class_base();
if ($_POST[do_key] == $postBtn) {
    $curr_year = curr_year();
    $new_school_str = ($_POST[curr_grade_school]) ? "and g.new_school= '$_POST[curr_grade_school]'" : "";
    $str = "���~�Ǧ~��,�~��,�Z�ŦW��,���y,�����Ҧr��,�ǥͩm�W,�ʧO,�X�ͦ~,�X�ͤ�,�X�ͤ�,�J�Ǧ~,���~�r��,���@�H,�p���q��,���y�a�},�ɤJ�ꤤ,���O����\r\n";
    //��������~�͸�ƪ�
    $sql = "SELECT a.*,b.curr_class_num,b.stud_country,b.stud_person_id,b.stud_name,b.stud_sex,year(b.stud_birthday) as birth_year,month(b.stud_birthday) as birth_month,day(b.stud_birthday) as birth_day,b.stud_study_year,b.stud_addr_1,b.stud_tel_1,b.stud_addr_2,c.guardian_name FROM grad_stud a INNER JOIN stud_base b ON a.student_sn=b.student_sn INNER JOIN stud_domicile c ON a.student_sn=c.student_sn WHERE stud_grad_year='$curr_year' ORDER BY grad_num";
    $result = $CONN->Execute($sql) or user_error("Ū�����ѡI<br>$sql", 256);

    while (!$result->EOF) {
        //�Z��
        $c_name = $class_name[substr($result->fields[curr_class_num], 0, -2)];
        $str.="\"" . $curr_year . "\",";
        $str.="\"" . $result->fields['class_year'] . "\",";
        $str.="\"" . $c_name . "\",";
        $str.="\"" . $result->fields['stud_country'] . "\",";
        $str.="\"" . $result->fields['stud_person_id'] . "\",";
        $str.="\"" . $result->fields['stud_name'] . "\",";
        $str.="\"" . ($result->fields['stud_sex'] == '1' ? '�k' : '�k') . "\",";
        $str.="\"" . $result->fields['birth_year'] . "\",";
        $str.="\"" . $result->fields['birth_month'] . "\",";
        $str.="\"" . $result->fields['birth_day'] . "\",";
        $str.="\"" . $result->fields['stud_study_year'] . "\",";
        $str.="\"" . $result->fields['grad_word'] . '��' . $result->fields['grad_num'] . "��\",";
        $str.="\"" . $result->fields['guardian_name'] . "\",";
        $str.="\"" . ($result->fields['stud_tel_2'] ? $result->fields['stud_tel_2'] : $result->fields['stud_tel_1']) . "\",";
        $str.="\"" . $result->fields['stud_addr_1'] . "\",";
        $str.="\"" . $result->fields['new_school'] . "\",";
        $str.="\"\"\r\n";



        $result->MoveNext();
    }

    header("Content-disposition: attachment; filename=" . $SCHOOL_BASE[sch_cname_ss] . curr_year() . "�Ǧ~�ײ��~�͸����X-�O�����N�Ǻޱ��t��.csv");
    header("Content-type: text/x-csv");
    //header("Pragma: no-cache");
    //�t�X SSL�s�u�ɡAIE 6,7,8�U�������D�A�i��ק� 
    header("Cache-Control: max-age=0");
    header("Pragma: public");
    header("Expires: 0");

    echo $str;
    exit;
}

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
        
        var studgradeuploadsrurl = "https://localhost:8443/sr/upload/studgrade";
        console.log(studgradeuploadsrurl);
        $("#btnStudGradeUploadSR").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            console.log(studgradeuploadsrurl);
            if (!$("#pin").val()) {
                $.unblockUI();
                alert('�п�JPIN�X');
                $("#pin").focus();
            } else {
                console.log($("#pin").val());
                $.ajax({
                    url: studgradeuploadsrurl,
                    dataType: "json",
                    contentType: 'application/json',
                    method: "POST",
                    timeout:180000,
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
                        alert(JSON.stringify(data));
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
    <legend>
        �A�α��ΡG���~�͸�ƥ������O���bSFS3�����]�Ҧp�G�S�Х͡^
    </legend> 
    <form name ="myform" action="<?php echo $PHP_SELF ?>" method="post" >

        <BR><input type="submit" name="do_key" value="<?php echo $postBtn ?>">
    </form>
</fieldset><br/>

<?php
if ($isTaichung == '06' || $isTaichung == '19') {
    $auto = "<fieldset>
    <legend>
        �A�α��ΡG���~�͸�Ƨ����O���bSFS3����
    </legend>
    <button id='btnCheckCard'>�ˬd�O�_�v���J����</button>&nbsp;&nbsp;�п�J�d��PIN�X�G<input type='password' id='pin'>&nbsp;&nbsp;<button id='btnStudGradeUploadSR'>���~�͸�Ʀ۰ʶפJ�O�����N�Ǻޱ��t��</button><p>
    <a href='https://localhost:8443/checkcard/exists' target='_blank' style='-webkit-appearance: button;-moz-appearance: button;appearance: button;text-decoration: none;color: initial;'>���ګH���O�������ҷL�����A��</a>&nbsp;&nbsp;
    <a href='https://oidc.tanet.edu.tw/miniserver/DeskTopMiniServer.jnlp' target='_blank' style='-webkit-appearance: button;-moz-appearance: button;appearance: button;text-decoration: none;color: initial;'>���ڤU���O�������ҷL�����A��</a></p>
</fieldset>";
    echo $auto;
}
?>
<div id="domMessage" style="display:none;"> 
    <img src="<?php echo $SFS_PATH_HTML ?>/images/busy.gif" alt="PORCESSING" id="loader"/>&nbsp;&nbsp;����Ū����...�еy��...
</div>
<?php
foot();
?>

