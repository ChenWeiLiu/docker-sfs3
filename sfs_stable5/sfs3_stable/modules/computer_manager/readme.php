<?php
include_once('config.php');
include_once('include/PHPTelnet.php');
sfs_check();


//�q�X����
head("�����޲z - ���n����");

$tool_bar=&make_menu($school_menu_p);

//�C�X���
echo $tool_bar;

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);
if ($module_manager!=1) {
 echo "��p , �z�S���L�޲z�v��!";
 exit();
}
?>
<table border="0">
  <tr>
   <td style="color:#FF0000">�����A�������n���A���Ҳեثe�H���ժ��[�c���լO���`�i����A�����O�ҶQ�ժ������[�c�i�A�ΡC�p�z�L�k�Ӿᨾ����]�w�W�����I�A�ФűҥΡI�I�I</td>
  </tr>
  <tr>
    <td>���U�ӡA�i������[�c�W�������C</td>
  </tr>
  <tr>
   <td>1.���ժ�������A�� 2016.10.12�w�˧����A�����̷̳s�t�o�� FG-200D �A�z�פW�Y�z���]�Ƥ]�O�o�@�x�A���ӬO�i�H�ΡC<br>
  <br>
  <img src="./images/fg.png" border="0">
   </td>
  </tr>

  <tr>
   <td>
    2.�W�Ϥ�, ���ժ��[�c�O�q���ЫǬҬ��T�wIP�A�ҳ]�� 192.168.2.x�A�B�ϥΨ����� port2�A��F�������~�| NAT �ন�u��IP��~�s�u�C<br>
    <br>
   </td>
  </tr>
    <tr>
        <td>
            3.<span style="color:#FF0000">���ҲձĥΪ������A�O���� IPv4 �W���\��A�аO�o�n��ǥ͹q�����d�� IPv6 �\������C</span><br>
            <br>
            <img src="./images/ipv6_disable.png" border="0">
        </td>
    </tr>
  <tr>
    <td>
        ============================================================================<br>
        <br><font color=blue>���Ҳպި�q���Ыǹq����_�W����z�G</font><br><br>
        1.�b�����𤤩w�q��Ӧ�}�s�աA���]�@�ӦW���uaccess�s�աv�A�@�ӦW���udeny�s�աv�C<br>
        2.�b������w�]�w��������G(�����ѱz��ʦb������W�s�W)<br>
        �Ĥ@�������A���\�s�u�����A�ӷ����}���Ҧ��q���Ы�IP�A�ت����}�M�Ρuaccess�s�աv<br>
        �ĤG�������A�T��s�W�����A�ӷ����}�M�Ρudeny�s�աv�F<br>
        3.�p�G�z�Q���Y�� IP ����W���A�u�n��o�� IP �[�J��udeny�s�աv���Y�i�C�o�ӥ\��Ѧ��ҲըӶi��ާ@�C<br>
        4.�p�G�z�Q���q���Ыǥi�n�J���Ǻ����A�u�n�⤹�\�����}�[�J��uaccess�s�աv���Y�i�C�o�ӥ\��]��Ѧ��ҲըӶi��ާ@�C<br>
        ==============================================================================
    <br><br>
        <p style="color:#FF0000">�]���A�p�G�T�w�n�ϥΥ��ҲաA��ĳ�y�{�G</p>
        1.����Ҳ��ܼƶi��]�w�A�����ܼƫ�ĳ�Ĺw�]�ȡA�N�Ӥ���ݱo���C<br>
        2.���� ������n�J���աC<br>
            ���ɨt�η|��U�إߤT�Ӧ�}�s�թw�q�G <br>
        (1)�q���ЫǩҦ��q��ip�s��(�Ω�w�q�Ҧ��q��) <br>
        (2)deny�s�� (�Ω�w�q���ǹq������W��) <br>
        (3)access�s�� (�Ω�w�q�ҥ~����ip) <br>
        3.�b������]�w�������<br>
        �Ĥ@�������O���\�ҥ~�s�J��ip�A�]�N�O�Y�ϳQ����s�~�A�o��ip���M�i�H�h<br>
        <img src="./images/policy1.png" border="0"><br><br>
        �ĤG�������O�T�� deny�s�ճs�J�A�]�N�O�u�n�Q�[�J�o�s�ժ�ip, ������W��<br>
        <img src="./images/policy2.png" border="0"><br><br>
        �]�w����, �H�ڮժ��Ҥl, �|���O�o�˪��e��<br>
        <img src="./images/policy3.png" border="0"><br><br>

        4.�]�w�q���ЫǮy�� (�o�̷|�����[���ɶ�)<br>
        5.�i�H�}�l���v���q���Ѯv�ϥΤF, �b���v������, ��T�ժ��n���޲z�v (�i���樾����n�J���դγ]�w�ҥ~IP), �q���Ѯv�u�n�@���v��.

    </td>
  </tr>
</table>