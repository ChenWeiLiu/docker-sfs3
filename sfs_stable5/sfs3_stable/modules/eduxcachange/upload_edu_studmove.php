<?php

include "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// 撘????刻?芸楛?? config.php 瑼~T
require "config.php";

// 認證
sfs_check();



$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$posturl =  $SFS_PATH_HTML.'modules/eduxcachange/output_edu_new.php';
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
$schoolname = trim($SCHOOL_BASE['sch_cname']);
$schoolid = trim($SCHOOL_BASE['sch_id']);


$arr = get_defined_vars();
//print_r($arr);

// 叫用 SFS3 的版頭
head("國教署XML上傳");

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
					useragent : '<?php echo $useragent?>',
					datatype : 'studreg',
					filetype : 'xml',
					cityno : 'pp1234',
					schoolsn : '<?php echo $schoolid?>',
					schoolname : '<?php echo $schoolname?>',
					deliverschoolsn : '<?php echo $schoolid?>',
					deliverschoolname : '<?php echo $schoolname?>',
					studentid : 'A098765435',
 	                urlprefix : 'http://140.114.67.144',
                    port : '80',
                    loglevel : 'debug'
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
</fieldset>


<?php
// SFS3 的版尾
foot();

?>
