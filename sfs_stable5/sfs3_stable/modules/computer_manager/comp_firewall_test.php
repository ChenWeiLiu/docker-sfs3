<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//�q�X����
head("�q���ЫǺ޲z - �������˴�");

$tool_bar=&make_menu($school_menu_p);

//�C�X���
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "��p , �z�S���L�޲z�v��!";
 exit();
}


//Ū��������b�K
if ($firewall_ip=="" or $firewall_user=="" or $firewall_pwd=="" or $addrgrp_deny_tag=='' or $addrgrp_deny=='' or $addrgrp_access=='' or $addrgrp_comp_all=='') {
    echo "�z�������������T���]�w����, �ХѼҲ��ܼƶi��]�w!";
    exit();
}

  $set_addrgrp_deny=0;
  $set_addrgrp_access=0;

  echo "�}�l�i�樾����n�J����, �еy��... (�`�N�I�@�X�{�u�˴������v�r�ˡA�~�����}������!)<br>";
 ob_flush();
 flush();
 //����
 $telnet = new PHPTelnet();

 // if the first argument to Connect is blank,
 // PHPTelnet will connect to the local host via 127.0.0.1
 $result = $telnet->Connect($firewall_ip,$firewall_user,$firewall_pwd);

 if ($result == 0) { 

     echo "������n�J���\! <br>";
     ob_flush();
     flush();

     if ($VDOM) {
         echo "�Q�ը�����ҥ� VDOM ...<br>";

         $telnet->DoCommand('config vdom', $result);
         $telnet->DoCommand('edit root', $result);
         echo str_replace("\n","<br>",$result);
         ob_flush();
         flush();
     }
     echo "<br>-------------------------------------------------------------------------------<br>";
     echo "���Ҳժ��B�@��z�G<br>";
     echo "(1)�b�����𤤩w�q��Ӧ�}�s�աA���]�@�ӦW���uaccess�s�աv�A�@�ӦW���udeny�s�աv�C<br>";
     echo "(2)�b������w�]�w��������G(�����ѱz��ʦb������W�s�W)<br>";
     echo "�Ĥ@�������A���\�s�u�����A�ӷ����}���Ҧ��q���Ы�IP�A�ت����}�M�Ρuaccess�s�աv�F<br>";
     echo "�ĤG�������A�T��s�W�����A�ӷ����}�M�Ρudeny�s�աv�F<br>";
     echo "(3)�p�G�z�Q���Y�� IP ����W���A�u�n��o�� IP �[�J��udeny�s�աv���Y�i�C�o�ӥ\��Ѧ��ҲըӶi��ާ@�C<br>";
     echo "(4)�p�G�z�Q���q���Ыǥi�n�J���Ǻ����A�u�n�⤹�\�����}�[�J��uaccess�s�աv���Y�i�C�o�ӥ\��]��Ѧ��ҲըӶi��ާ@�C<br>";
     echo "-------------------------------------------------------------------------------<br>";
     echo "�{�b�A�{���N��U�z�b�����𤤩w�q�udeny�s�աv�Ρuaccess�s�աv�A<br>";
     echo "�z�b�Ҳ��ܼƤ��]�w�� �udeny�s�աv�W�٬� ".$addrgrp_deny."<br>";
     echo "�z�b�Ҳ��ܼƤ��]�w�� �uaccess�s�աv�W�٬� ".$addrgrp_access."<br>";
     ob_flush();
     flush();
     //
     $telnet->DoCommand('show firewall address "sfs3_addrgrp_access_tag"', $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall address') {
         echo "sfs3_addrgrp_access_tag �� ip address �w�q�w�s�b. <br>";
         ob_flush();
         flush();
     } else {
         $telnet->DoCommand('config firewall address', $result);
         $telnet->DoCommand('edit sfs3_addrgrp_access_tag', $result);
         $telnet->DoCommand('set subnet 127.0.0.1 255.255.255.255', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall address "sfs3_addrgrp_access_tag"', $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall address') {
             echo "�]�w sfs3_addrgrp_access_tag �� ip address �w�q ���� (���Ŧۦ��ʧR��)<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
         } else {
             echo "�]�w sfs3_addrgrp_access_tag ip address �w�q ����! <br>";
             ob_flush();
             flush();
         }
     }

     $telnet->DoCommand('show firewall addrgrp '.$addrgrp_access, $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall addrgrp') {
         echo "access �s�աG $addrgrp_access ���w�q�w�s�b. <br>";
         $set_addrgrp_access=1;
         ob_flush();
         flush();
     } else {
         echo "�w�q��}�s�� ".$addrgrp_access."...<br>";
         $telnet->DoCommand('config firewall addrgrp', $result);
         $telnet->DoCommand('edit '.$addrgrp_access, $result);
         $telnet->DoCommand('set member "sfs3_addrgrp_access_tag"', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall addrgrp '.$addrgrp_access, $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall addrgrp') {
             echo "�]�w access�s�� : $addrgrp_access ����<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
             $set_addrgrp_access=1;
         } else {
             echo "�]�w access�s�� : $addrgrp_access ����<br>";
         }

     }
        //�ˬd�O�_�w�ݩR
             while (trim(end($RES))=="--More--") {
                 $telnet->DoCommand(' ', $result);
                 $RES=explode("\n",$result);
             }

     $telnet->DoCommand('show firewall address "sfs3_addrgrp_deny_tag"', $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall address') {
         echo "sfs3_addrgrp_deny_tag �� ip address �w�q�w�s�b. <br>";
         ob_flush();
         flush();
     } else {
         $telnet->DoCommand('config firewall address', $result);
         $telnet->DoCommand('edit sfs3_addrgrp_deny_tag', $result);
         $telnet->DoCommand('set subnet '.$addrgrp_deny_tag." 255.255.255.255", $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall address "sfs3_addrgrp_deny_tag"', $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall address') {
             echo "�]�w sfs3_addrgrp_deny_tag �� ip address �w�q ���� (���Ŧۦ��ʧR��)<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
         } else {
             echo "�]�w sfs3_addrgrp_deny_tag ip address �w�q ����! <br>";
             ob_flush();
             flush();
         }
     }

     $telnet->DoCommand('show firewall addrgrp '.$addrgrp_deny, $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall addrgrp') {
         echo "deny �s�աG $addrgrp_deny ���w�q�w�s�b. <br>";
         $set_addrgrp_deny=1;
         ob_flush();
         flush();
     } else {
         echo "�w�q��}�s�� ".$addrgrp_deny."...<br>";
         ob_flush();
         flush();
         $telnet->DoCommand('config firewall addrgrp', $result);
         $telnet->DoCommand('edit '.$addrgrp_deny, $result);
         $telnet->DoCommand('set member "sfs3_addrgrp_deny_tag"', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall addrgrp '.$addrgrp_deny, $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall addrgrp') {
             echo "�]�w deny�s�� : $addrgrp_deny ����<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
             $set_addrgrp_deny=1;
         } else {
             echo "�]�w deny�s�� : $addrgrp_deny ����<br>";
         }

     }

    //�ˬd�O�_�w�ݩR  �]���s�դ��� ip address �ƶq�i��L�h�A�X�{ --More-- �n���@�U�ť�
     while (trim(end($RES))=="--More--") {
         $telnet->DoCommand(' ', $result);
         $RES=explode("\n",$result);
     }

     $telnet->DoCommand('show firewall addrgrp '.$addrgrp_comp_all, $result);
     $RES=explode("\n",$result);
     if (trim($RES[1])=='config firewall addrgrp') {
         echo "�q���Ы�ip�s�աG $addrgrp_comp_all ���w�q�w�s�b. <br>";
         $set_addrgrp_comp_all=1;
         ob_flush();
         flush();
     } else {
         foreach ($RES as $k => $v) {
             echo $v . "<br>";
             ob_flush();
             flush();
         }
         echo "�w�q�q���Ы�ip�s�� : $addrgrp_comp_all <br>";
         ob_flush();
         flush();
         $telnet->DoCommand('config firewall addrgrp', $result);
         $telnet->DoCommand('edit '.$addrgrp_comp_all, $result);
         $telnet->DoCommand('set member "sfs3_addrgrp_deny_tag"', $result);
         $telnet->DoCommand('next', $result);
         $telnet->DoCommand('end', $result);
         $telnet->DoCommand('show firewall addrgrp '.$addrgrp_comp_all, $result);
         $RES=explode("\n",$result);
         if (trim($RES[1])=='config firewall addrgrp') {
             echo "�]�w�q���Ы�ip�s�� : $addrgrp_comp_all ����<br>";
             foreach ($RES as $k => $v) {
                 echo $v . "<br>";
                 ob_flush();
                 flush();
             }
             $set_addrgrp_comp_all=1;
         } else {
             echo "�]�w�q���Ы�ip�s�� : sfs3_comp_all ����<br>";
         }

     }

     //���_ telnet �s�u
     $telnet->Disconnect();
     if ($set_addrgrp_access and $set_addrgrp_deny and $set_addrgrp_comp_all ) {
         echo "�t�Ωw�q�F��� IP��}: sfs3_addrgrp_access_tag�Bsfs3_addrgrp_deny_tag  <br>";
         echo "�t�Ωw�q�F�T�Ӧ�}�s��: $addrgrp_access �B $addrgrp_deny �B $addrgrp_comp_all <br>";
         echo "�H�W�����Ҳզb������W�B�@�ɥ��n���w�q�A�ФŦۦ��ʧR���C<br>";
         echo "<br>";
         echo "<p style='color:#FF0000'> �{�b, �Цb�Q�ժ�������A���m�w�q�H�U��������G <br> ";
         echo "�Ģ����G���\ $addrgrp_comp_all �s�ճs�u $addrgrp_access �s�աC<br>";
         echo "�Ģ����G�T�� $addrgrp_deny ��~�s�u�C</p>";
         echo "�`�N! ��1�������\�b��2�����e!";
     }

     echo "<br><br><span style='color:#FF0000'>�˴�����</span>";

 } else {
 	?>
  �L�k�n�J������I�ХѼҲ��ܼƭ��]�����𪺱b���αK�X�C
  <?php
 }

?>