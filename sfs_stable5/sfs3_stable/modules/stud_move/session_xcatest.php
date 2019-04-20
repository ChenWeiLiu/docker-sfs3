<?php

// 載入設定檔
include "stud_move_config.php";
include_once "../../include/sfs_case_dataarray.php";



// 認證檢查
sfs_check();


$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$target_page =  $SFS_PATH_HTML.'modules/stud_move/stud_move_xcatest.php';

$cookie_sch_id = $_COOKIE['cookie_sch_id'];
if ($cookie_sch_id == null){
        $cookie_sch_id = get_session_prot();
}
$para = array(
	'session_id'    => $session_id,
	'useragent'     => $useragent,
	'target_page'   => $target_page,
	'cookie_sch_id' => $cookie_sch_id
	);
//echo json_encode($para);
?>

<script type="text/javascript" src="http://java.com/js/dtjava.js"></script>
<script>
    function javafxEmbed() {
        dtjava.embed(
            {
                url : 'XCATest.jnlp',
                placeholder : 'javafx-app-placeholder',
                width : 360,
                height : 100,
		params: {
			webparams : '<?php echo json_encode($para)?>'
		}
            },
            {
                javafx : '8.0+'
            },
            {}
        );
    }
    <!-- Embed FX application into web page once page is loaded -->
    dtjava.addOnloadCallback(javafxEmbed);
</script>
<div id='javafx-app-placeholder'></div>
