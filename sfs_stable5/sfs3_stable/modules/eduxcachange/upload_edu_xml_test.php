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
//$posturl =  $SFS_PATH_HTML.'modules/eduxcachange/output_edu_test.php';
$posturl =  $SFS_PATH_HTML."modules/eduxcachange/output_edu_test.php?cookie_sch_id={$cookie_sch_id}&sfs_path_html={$SFS_PATH_HTML}";
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
$schoolname = trim($SCHOOL_BASE['sch_cname']);
$schoolid = trim($SCHOOL_BASE['sch_id']);


$arr = get_defined_vars();
//print_r($arr);

// 叫用 SFS3 的版頭
head("國前署XML上傳");

$tool_bar=make_menu($eduxcachange_menu);
echo $tool_bar;
if($loglevel==""){
    $loglevel="info";
}

//check remote server exist
if(function_exists('curl_ini')){
   $ch = curl_init();
   curl_setopt($ch , CURLOPT_URL , "http://140.114.67.144");
   curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
   $check_server = curl_exec($ch);
   curl_close($ch);
}else{
    $check_server = file_get_contents("http://140.114.67.144");
}
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
					datatype : 'studreg',
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
	<legend>就學網資料【上傳測試】</legend>
        <?php
        if(!strstr($check_server,"Hello")){
            echo "<font size='7' color='orange'>遠端主機賴床中，先休息一下吧！！</font>";
        }else{
            echo "<div id='javafx-app-placeholder'></div>";  
        }
        ?>
</fieldset>


<?php
// SFS3 的版尾
foot();

?>
