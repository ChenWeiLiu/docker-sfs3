<?php
//require "config.php";
require "function.php";

//���ߺݤ䴩
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}

// �{��
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

//$posturl =  $SFS_PATH_HTML.'modules/eduxcachange/output_edu_new.php';//��Xbase64�����}
$set_data_url= $SFS_PATH_HTML.'modules/school_setup/';//�]�w�Ǯո�ƪ����}
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
$arr = get_defined_vars();
//print_r($arr);

// �s�� SFS3 �����Y
head("��e�pXML�W��");

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
	<legend>�N�Ǻ���ƥ[�K�W�Ǩt��</legend>
        <?php
        if(!strstr($check_server,"Hello")){
            echo "<font size='7' color='orange'>���ݥD����ɤ��A���𮧤@�U�a�I�I</font>";
        }else if(!$file_exist){
            echo "<font size='5' color='blue'>���������ɮסA�Х�<a href='output_xml.php'>�m����XML�ɡn</a></font>";
        }else{
            echo "<div id='javafx-app-placeholder'></div>";  
        }
	
                ?>
        <table>
            <table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#333354 bordercolordark=#FFFFFF  width=600>
                <TR bgcolor=#B7EBFF><TD width40%>�ǮզW��</TD><TD width=20%>�г��N�X</TD><TD width=20%>�Ҧb����</TD><TD width=20%>�����N�X</TD></TR>
                <TR><TD><?php echo $ex_school_name; ?></TD><TD><?php echo $ex_school_id; ?></TD><TD><?php echo $ex_school_city; ?></TD><TD><?php echo $ex_school_city_id;?></TD></TR>
        </table>
            <font size='5' color='red'>�Y�W�C��Ʀ��~�A�гs����T�խק�<�ҲհѼ�></font>
</fieldset>
�������W�ǫe�нT�{�z���q�����w�ˤU�C���󡹡���<br>
1.<a href="http://www.sfs.project.edu.tw/modules/mydownloads/visit.php?cid=2&lid=47">�O�������Ҥ���w�˵{��v0.5</a>(�D<b>�L�����A��</b>)<br>
2.<a href="http://moica.nat.gov.tw/download/File/HiCOS%20Client%20v2.1.9.6.zip">HiCOS�d���޲z�u��</a>


<?php
// SFS3 ������
foot();

?>
