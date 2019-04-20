<?php

// $Id: stud_move_out.php 7285 2013-05-10 06:51:37Z smallduh $
// 載入設定檔
include "stud_move_config.php";
include_once "../../include/sfs_case_dataarray.php";



// 認證檢查
sfs_check();

//中心端支援
$cookie_sch_id = $_COOKIE['cookie_sch_id'];
if ($cookie_sch_id == null){
        $cookie_sch_id = get_session_prot();
}

?>
<script>
var applet =
'<applet code="org.apache.pivot.wtk.BrowserApplicationContext$HostApplet" archive="XCVXMLexChange.jar, lib/commons-codec-1.8.jar, lib/commons-lang-2.6.jar, lib/commons-logging-1.1.3.jar, lib/fluent-hc-4.3.5.jar, lib/httpclient-4.3.5.jar, lib/httpclient-cache-4.3.5.jar, lib/httpcore-4.3.2.jar, lib/httpmime-4.3.5.jar, lib/log4j-1.2.16.jar, lib/pivot-charts-2.0.3.jar, lib/pivot-core-2.0.3.jar, lib/pivot-web-2.0.3.jar, lib/pivot-web-server-2.0.3.jar, lib/pivot-wtk-2.0.3.jar, lib/pivot-wtk-terra-2.0.3.jar, lib/derby.jar, lib/derbynet.jar, lib/derbyclient.jar, lib/schooldb.jar, lib/log4j-1.2.16.jar, lib/commons-configuration-1.9.jar"  width="600" height="200">' +
'<param name="application_class_name" value="XCAGUI_Upload">' +
'<param name="startup_properties" value="' + 
'sessionid=<?php echo session_id() ?>&cookie_sch_id=<?php echo $cookie_sch_id ;?>&useragent=' +
escape("<?php echo  $_SERVER['HTTP_USER_AGENT'] ?>") +
'&arguments=' + "<?php echo utf8_decode($_GET['para']) ?>" +
'&targetpage=<?php echo $SFS_PATH_HTML;?>modules/toxml/output_xml2.php'
+ '">'
+'</applet>';
document.write(
applet
);
</script>

