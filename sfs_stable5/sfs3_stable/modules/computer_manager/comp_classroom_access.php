<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//�q�X����
head("�����޲z - �]�w�ҥ~����IP");

$tool_bar=&make_menu($school_menu_p);

//�C�X���
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
    echo "��p , �z�S���L�޲z�v��!";
    exit();
}


$telnet = new PHPTelnet();

//Ū��������b�K
if ($firewall_ip=="" or $firewall_user=="" or $firewall_pwd=="") {
    echo "�z�|���]�w�����������T, �ХѼҲ��ܼƶi��]�w!";
    exit();
}

//�Y���F�x�s
if ($_POST['act']!="") {

    //�n�J������
    //telnet����
    $telnet = new PHPTelnet();
    echo "�i�樾����s�u<br>";
    ob_flush();
    flush();
    //�i��s�u
    $result = $telnet->Connect($firewall_ip, $firewall_user, $firewall_pwd);
    //�s�u���\�~��i��
    if ($result == 0) {
        if ($VDOM) {
            echo "�Q�ը�����ҥ� VDOM ...<br>";
            ob_flush();
            flush();
            $telnet->DoCommand('config vdom', $result);
            $telnet->DoCommand('edit root', $result);
            echo str_replace("\n","<br>",$result);
            echo "<br>";
            ob_flush();
            flush();
        }
        //Ū�X�Ҧ� ip , ���ˬd�O�_�w���� ip �w�q
        $all_ip=explode("\n",$_POST['all_ip']);

        $access_address="\"sfs3_addrgrp_access_tag\"";
        foreach ($all_ip as $check_ip) {
            $check_ip=trim($check_ip);
            $check_ip=preg_replace('/\s(?=\s)/', '', $check_ip);
            $check_ip=preg_replace('/[\r\t]/', '', $check_ip);
            $check_ip=nf_to_wf($check_ip,0);
            $save_ip="";
            //�Y��J���D�ŭ�
            if ($check_ip!='') {
                echo $check_ip."<br>";
                $telnet->DoCommand('show firewall address '.$check_ip, $result);
                // NOTE: $result may contain newlines
                $RES=explode("\n",$result);
                // $RES[0] �|���аe�X�����O
                //���w�q
                if (trim($RES[1])=='config firewall address') {
                    echo "�� IP address �w���w�q<br>";
                    foreach ($RES as $k => $v) {
                        echo $v . "<br>";
                        ob_flush();
                        flush();
                    }
                    $save_ip=$check_ip;
                } else {
                    echo "�|���w�q�A�i��w�q...<br>";
                    $telnet->DoCommand('config firewall address', $result);
                    $telnet->DoCommand('edit '.$check_ip, $result);
                    $telnet->DoCommand('set subnet '.$check_ip." 255.255.255.255", $result);
                    $telnet->DoCommand('next', $result);
                    $telnet->DoCommand('end', $result);
                    $telnet->DoCommand('show firewall address '.$check_ip, $result);
                    $RES=explode("\n",$result);
                    if (trim($RES[1])=='config firewall address') {
                        echo "�]�w����<br>";
                        foreach ($RES as $k => $v) {
                            echo $v . "<br>";
                            ob_flush();
                            flush();
                        }
                        $save_ip=$check_ip;
                    } else {
                        echo "�]�w����! <br>";
                    }
                }

                $access_address.=" \"".$save_ip."\"";

            }

        } // end foreach

        echo "�N access ���Ҧ� IP �x�s�b access �s��... <br>";
        echo $access_address."<br>";
        ob_flush();
        flush();
        //�]�w firewall addrgrp ��
        $telnet->DoCommand('config firewall addrgrp', $result);
        $telnet->DoCommand('edit "'.$addrgrp_access.'"', $result);
        $telnet->DoCommand('set member '.$access_address, $result);
        $telnet->DoCommand('next', $result);
        $telnet->DoCommand('end', $result);
        //Ū�X�]�w
        echo "�˴��]�w��...<br>";
        $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_access, $result);
        $RES=explode("\n",$result);
        foreach ($RES as $k => $v) {
            echo $v . "<br>";
            ob_flush();
            flush();
            //���o�w�n����}
            if (substr(trim($v),0,10)=='set member')  $all_access_ip=get_all_ip(trim($v));
        }

        $telnet->Disconnect();
        echo "</div>";
        echo "<Script> $(\"#show_process\").css(\"display\",\"none\");</Script>";
    } else {
        echo "�L�k�n�J������I</div>";
        exit();
    } // end if result=0

} else {
  //�����n�J������,Ū���]�w
    $telnet = new PHPTelnet();
    echo "�i�樾����s�u<br>";
    ob_flush();
    flush();
    //�i��s�u
    $result = $telnet->Connect($firewall_ip, $firewall_user, $firewall_pwd);
    //�s�u���\�~��i��
    if ($result == 0) {
        if ($VDOM) {
            echo "�Q�ը�����ҥ� VDOM ...<br>";
            ob_flush();
            flush();
            $telnet->DoCommand('config vdom', $result);
            $telnet->DoCommand('edit root', $result);
            echo str_replace("\n", "<br>", $result);
            echo "<br>";
            ob_flush();
            flush();
        }
        $telnet->DoCommand('show firewall addrgrp ' . $addrgrp_access, $result);
        $RES=explode("\n",$result);
        if (trim($RES[1])=='config firewall addrgrp') {
            foreach ($RES as $k => $v) {
                echo $v . "<br>";
                ob_flush();
                flush();
                //���o�w�n����}
                if (substr(trim($v),0,10)=='set member')  $all_access_ip=get_all_ip(trim($v));
            }

        } else {
            echo "�䤣�� $addrgrp_access ��}�s�թw�q! <br>";
            echo "�ЦA����@���u������n�J���աv�C <br>";
            exit();
        }
    } else {
        echo "�L�k�n�J������I</div>";
        exit();
    }
} // end if $_POST['act']!='')


?>
<form method="post" name="myform" action="<?php echo $_SERVER['PHP_SELF'];?>">
    <input type="hidden" name="act" value="<?php echo $_POST['act'];?>">
    <textarea name="all_ip" style="width:500px;background-color: #CCCCCC" rows="10" ><?php echo $all_access_ip;?></textarea>
    <br>
    <input type="button" value="�x�s�]�w" onclick="document.myform.act.value='save';document.myform.submit()">
    <br>
    <p style="color:#FF0000">
    ���п�J IPv4 �榡���X�z IP �A�C�� IP ���@��A �p : 163.17.39.33 �C <br>
    �����ҲչB�@�覡�������s�J������i��]�w�A�Фp�߾ާ@�A�H�K�M�ήն�����C<br>
    �����Ǻ����A�P�@�ӭ����i��|�s���h�Ӻ����A�ҥH���D�O�ܲM���Ӻ����T��u����@IP���O(�Ҧp�G���������v�ɥ��x)�A�_�h�ɥi�ण�n�ϥΥ��\��C<br>
    </p>
</form>


<?php
function get_all_ip($ip_member) {
   // echo "�I�� <br>";
    $all_ip=explode(" ",$ip_member);
    $i=0;
    $save_ip="";
    foreach ($all_ip as $IP) {
      $IP=trim($IP);
      $i++;
        if ($i>3) {
            $save_ip.=substr($IP,1,strlen($IP)-2)."\n";
        }
    }

    return $save_ip;

}  // end function


function nf_to_wf($strs, $types){  //���Υb���ഫ
    $nft = array(
        "(", ")", "[", "]", "{", "}", ".",
        ",", ";", ":",
        "-",  "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~",
        "`", "'", "\"","?",
        "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
    );
    $wft = array(
        "�]", "�^", "�e", "�f", "�a", "�b", "�O",
        "�A", "�F", "�G",
        "��",  "�I", "�I", "��", "�C", "�H", "��", "�U", "�B",
        "�A", "��", "��", "��", "?",
        "�B", "�B", "?","�H",
        "��", "��",
        "�s", "��",
        "��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
        "��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
        "��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
        "��", "��", "�@", "�A", "�B", "�C",
        "��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
        "��", "��", "��", "��", "��", "��", "��", "��", "��", "��",
        "��", "��", "��", "��", "��", "��",
        "�@"
    );

    if ($types == '1'){
        // �����
        $strtmp = str_replace($nft, $wft, $strs);
    }else{
        // ��b��
        $strtmp = str_replace($wft, $nft, $strs);
    }
    return $strtmp;
}
?>
