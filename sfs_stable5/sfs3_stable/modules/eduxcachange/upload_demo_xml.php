<?php

include "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";

// �????��?�己?? config.php �~T
require "config.php";

// �{��
sfs_check();

//���ߺݤ䴩
$cookie_sch_id=$_COOKIE['cookie_sch_id'];
if($cookie_sch_id==null){
    $cookie_sch_id= get_session_prot();
}

$session_id = session_id();
$useragent = $_SERVER['HTTP_USER_AGENT'];
$posturl =  $SFS_PATH_HTML.'modules/eduxcachange/output_edu_demo.php';//��Xbase64�����}
$set_data_url= $SFS_PATH_HTML.'modules/school_setup/';//�]�w�Ǯո�ƪ����}
//$schoolname = iconv("Big5","UTF-8",trim($SCHOOL_BASE['sch_cname']));
$arr = get_defined_vars();
//print_r($arr);

// �s�� SFS3 �����Y
head("��e�pXML�W��");

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
	<legend>�N�Ǻ���ƥ[�K�W�Ǩt��</legend>
	<div id='javafx-app-placeholder'></div>
        <table>
            <table border=1 cellspacing=0 cellpadding=2 bordercolorlight=#333354 bordercolordark=#FFFFFF  width=600>
                <TR bgcolor=#B7EBFF><TD width40%>�ǮզW��</TD><TD width=20%>�г��N�X</TD><TD width=20%>�Ҧb����</TD><TD width=20%>�����N�X</TD></TR>
                <TR><TD><?php echo $ex_school_name; ?></TD><TD><?php echo $ex_school_id; ?></TD><TD><?php echo $ex_school_city; ?></TD><TD><?php echo $ex_school_city_id;?></TD></TR>
        </table>
            <font size='5' color='red'>�Y�W�C��Ʀ��~�A�гs����T�խק�<�ҲհѼ�></font>
</fieldset>


<?php
// SFS3 ������
foot();

?>
