<?php

include "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// 引入?�自己�? config.php �~T
require "config.php";

// 認�?
sfs_check();

// �{��
sfs_check();



$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$posturl =  $SFS_PATH_HTML.'modules/toxml/input_edu_studmove.php';
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
$schoolname = trim($SCHOOL_BASE['sch_cname']);
$schoolid = trim($SCHOOL_BASE['sch_id']);


$arr = get_defined_vars();
//print_r($arr);

// �s�� SFS3 �����Y
head("XML�洫�@�~");

$tool_bar=make_menu($toxml_menu);
echo $tool_bar;

?>

  <SCRIPT src="./web-files/dtjava.js"></SCRIPT>


<script>
    function javafxEmbed() {
        dtjava.embed(
            {
                url : 'dist2/EDUXCAExchangeGet.jnlp',
                placeholder : 'javafx-app-placeholder',
                width : 480,
                height : 120,
                params:{
					dhkey : 'false',
					posturl : '<?php echo $posturl?>',
					sessionid : '<?php echo $session_id?>',
					useragent : '<?php echo $useragent?>',
					datatype : 'studmove',
					filetype : 'xml',
					cityno : 'pp1234',
					cookie_sch_id : '<?php echo $_COOKIE['cookie_sch_id']?>',
					schoolsn : '<?php echo $schoolid?>',
					schoolname : '<?php echo $schoolname?>',
					studentid : 'A098765435',
                    transferschoolsn : '<?php echo $schoolid?>',
					transferschoolname : '<?php echo $schoolname?>',
 	                urlprefix : 'http://140.114.135.91',
					port : "8080",
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
	<legend>�N�Ǻ���ƥ[�K�U���t��</legend>
	<div id='javafx-app-placeholder'></div>
</fieldset>


<?php
// SFS3 ������
foot();

?>
