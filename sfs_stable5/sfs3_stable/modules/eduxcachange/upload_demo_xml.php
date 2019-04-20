<?php

include "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// 撘????刻?芸楛?? config.php 瑼~T
require "config.php";

// 認證
sfs_check();

//中心端支援
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}

$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$posturl =  $SFS_PATH_HTML.'modules/eduxcachange/output_edu_demo.php';//輸出base64的網址
$set_data_url= $SFS_PATH_HTML.'modules/school_setup/';//設定學校資料的網址
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
$arr = get_defined_vars();
//print_r($arr);

// 叫用 SFS3 的版頭
head("國前署XML上傳");

$tool_bar=make_menu($eduxcachange_menu);
echo $tool_bar;

?>

  <SCRIPT src="./web-files/dtjava.js"></SCRIPT>


<script>
    function javafxEmbed() {
        dtjava.embed(
            {
                url : 'dist/EDUXCAFileUpload.jnlp',
                placeholder : 'javafx-app-placeholder',
                width : 480,
                height : 120,
                params:{
					dhkey : 'false',
					posturl : '<?php echo $posturl?>',
					sessionid : '<?php echo $session_id?>',
                                        cookie_sch_id:'<?php echo $cookie_sch_id?>',
					useragent : '<?php echo $useragent?>',
					datatype : 'studupdate',
					filetype : 'xml',
					cityno : '<?php echo $ex_school_city_id?>',
					schoolsn : '<?php echo $ex_school_id?>',
					schoolname : '<?php echo $ex_school_name?>',
					deliverschoolsn : '<?php echo $schoolid?>',
					deliverschoolname : '<?php echo $schoolname?>',
					studentid : 'A098765435',
 	                urlprefix : 'http://140.114.67.144',
                    port : '80',
                    loglevel : '<?php echo $loglevel?>'
				}
	    },
            {
                javafx : '8.0+',
		jvmargs : '-Xmx512m '
            },
            {}
        );
    }
    <!-- Embed FX application into web page once page is loaded -->
    dtjava.addOnloadCallback(javafxEmbed);
</script>

<fieldset>
	<legend>就學網資料加密上傳系統</legend>
	<div id='javafx-app-placeholder'></div>
        <table>
            <table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#333354 bordercolordark=#FFFFFF  width=600>
                <TR bgcolor=#B7EBFF><TD width40%>學校名稱</TD><TD width=20%>教部代碼</TD><TD width=20%>所在縣市</TD><TD width=20%>縣市代碼</TD></TR>
                <TR><TD><?php echo $ex_school_name; ?></TD><TD><?php echo $ex_school_id; ?></TD><TD><?php echo $ex_school_city; ?></TD><TD><?php echo $ex_school_city_id;?></TD></TR>
        </table>
            <font size='5' color='red'>若上列資料有誤，請連絡資訊組修改<模組參數></font>
</fieldset>


<?php
// SFS3 的版尾
foot();

?>
