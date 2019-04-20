<?php
//require "config.php";
require "function.php";

//中心端支援
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}

// 認證
sfs_check();

$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
//get file name
$temp_dir=$UPLOAD_PATH."eduxcachange/";
$file_exist=exist_file_path($temp_dir);
$temp_ss= explode("eduxcachange/",$file_exist);
$file_name=$temp_ss[1];

//get server path
if($_SERVER['SERVER_PORT']==443){
 $http_port="https://";
}else{
 $http_port="http://";
}
$serv_name=$http_port.$_SERVER['HTTP_HOST'];

$posturl =  $SFS_PATH_HTML."modules/eduxcachange/output_edu_new.php?cookie_sch_id={$cookie_sch_id}&serv_name={$serv_name}&file_name={$file_name}&upload_url={$UPLOAD_URL}";

//$posturl =  $SFS_PATH_HTML.'modules/eduxcachange/output_edu_new.php';//輸出base64的網址
$set_data_url= $SFS_PATH_HTML.'modules/school_setup/';//設定學校資料的網址
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
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
        <?php
        if(!strstr($check_server,"Hello")){
            echo "<font size='7' color='orange'>遠端主機賴床中，先休息一下吧！！</font>";
        }else if(!$file_exist){
            echo "<font size='5' color='blue'>未偵測到檔案，請先<a href='output_xml.php'>《產生XML檔》</a></font>";
        }else{
            echo "<div id='javafx-app-placeholder'></div>";  
        }
	
                ?>
        <table>
            <table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#333354 bordercolordark=#FFFFFF  width=600>
                <TR bgcolor=#B7EBFF><TD width40%>學校名稱</TD><TD width=20%>教部代碼</TD><TD width=20%>所在縣市</TD><TD width=20%>縣市代碼</TD></TR>
                <TR><TD><?php echo $ex_school_name; ?></TD><TD><?php echo $ex_school_id; ?></TD><TD><?php echo $ex_school_city; ?></TD><TD><?php echo $ex_school_city_id;?></TD></TR>
        </table>
            <font size='5' color='red'>若上列資料有誤，請連絡資訊組修改<模組參數></font>
</fieldset>
★★★上傳前請確認您的電腦有安裝下列元件★★★<br>
1.<a href="http://www.sfs.project.edu.tw/modules/mydownloads/visit.php?cid=2&lid=47">臺中市憑證元件安裝程式v0.5</a>(非<b>微型伺服器</b>)<br>
2.<a href="http://moica.nat.gov.tw/download/File/HiCOS%20Client%20v2.1.9.6.zip">HiCOS卡片管理工具</a>


<?php
// SFS3 的版尾
foot();

?>
