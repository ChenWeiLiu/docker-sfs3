<?php

include "config.php";
sfs_check();

//�q�X����
head("�Ҳըϥλ���");

//��V������
echo print_menu($MENU_P,$linkstr);

$help_doc="
<ol>
	<li>�ҲչϥܡG<img src='./images/new_icon.png'></li>
	<li>�}�o�ӥ�</li>
		<ul>
			<li>�Ǯթ�ǰȳB��߷|��A���X���榳��������榡����ƽլd�����A�H�W�[²��B�P��ʸ�ƽլd���K�Q�ʡC</li>
			<li>�g�d�J���Ҳզb��ƽլd�W�A����ϥμu�ʡA�]���䭭��A�}�o�M�μҲսT������W�i�e�ݽլd�P��ݶ׾�Ĳv�C</li>
		</ul>
	<li>�w�˦�m��ĳ�G��¾��</li>
	<li>�Ҳտ��</li>
		<ul>	
			<li>��ƶ��</li>
				<ul>
					<li>�v���ݨD</li>
						<ul>
							<li>�ɮv�G�ȯ������ЯZ�žǥͪ��լd����</li>
							<li>�Ҳպ޲z���G�i������Ǵ����w�Z�žǥͪ��լd���ءC</li>
						</ul>
					<li>�\�໡���G��C���ЯZ�ũο�w�Z�žǥͦW��A���ѿ���I���� ( ���P�ﶵ�ۡu�լd���غ޲z�v�B���� ) �C</li>
				</ul>
			<li>�լd���غ޲z</li>
				<ul>
					<li>�v���ݨD�G�����Ҳպ޲z�v���C</li>
					<li>�\�໡���G�}�]�լd���ءC</li>
					<li>��컡��</li>
						<ul>
							<li>�լd���G�H�h���r��z�n�լd���Ƕ���</li>
							<li>���ﶵ�G�H�h���r��z�լd���ت��ﶵ ( �Ҧ����س��O�P�@�ӿﶵ ) �C</li>
							<li>�ơ@�@���G�޲z�̶}�]���լd���������O���� ( �ɮv�L�k�ݨ� ) �C</li>
							<li>�A�γB�ǡG�����e�|�۰��^���A�s�ئs�ɮɷ|�۰��k���A���ȸӳB�Ǫ��Ҳպ޲z���i�i����@ ( �R���B�ק�B��ƶץX ) �C</li>
							<li>�լd���ئW�١G�լd���ؼ��D�C</li>
							<li>�ɮv�i�ݨ쥻���ءG��ܡu�O�v�A�ɮv����ɤ~�|�X�{�b��椺�C</li>
							<li>�ɮv��������G�ɮv�i���������϶��C</li>
						</ul>
					<li>�p����</li>
						<ul>
							<li>�ɮv�n�����A��������ӭn��G 1.�u�ɮv�i�ݨ쥻���ءv�]���u�O�v 2.�ɮv��������G������b�]�w�������C</li>
						</ul>
				</ul>
			<li>�լd��ƶץX</li>	
				<ul>
					<li>�v���ݨD�G�����Ҳպ޲z�v���C</li>
					<li>�\�໡��</li>
						<ul>
							<li>�Z�Ÿ�ƦC��G�H�}�s�������覡�A��C��w�Z��(�i�h��)�ǥͪ��լd���G�C</li>
							<li>�W�浲�G�׾�G�H�}�s�������覡�A��C���լd���ؿ�w�Z�žǥͪ��լd�׾�W�� ( �����C�ӧO�Z�šA�̥���ܩҦ���w�Z�� )�C</li>
						</ul>
				</ul>
			</ul>
		</ul>
	<li>�S�O����</li>	
		<ul>
			<li>�C�ӽլd���ت����A�ﶵ�����ۦP�C</li>
			<li>�լd��A�ק����ﶵ�A�Y�ﶵ��r�Q���ܡA��լd��ƱN�L�k��ܡC</li>
		</ul>
</ol>
";

echo $help_doc;
foot();
?>