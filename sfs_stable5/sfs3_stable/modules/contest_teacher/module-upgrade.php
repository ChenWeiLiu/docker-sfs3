<?php
//$Id: module-upgrade.php 6737 2012-04-06 12:25:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
// reward_reason�Mreward_base ����ݩʬ�text

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//�H�W�O�d--------------------------------------------------------


$up_file_name =$upgrade_str."2013-03-02.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_setup` ADD `password` varchar(4) NULL" ; //�v�ɱK�X�]�p
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�s�W���, �v�ɮɥi�]�p�K�X�~��i�J-- by smallduh (2013-03-02)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


$up_file_name =$upgrade_str."2013-03-03.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_setup` ADD `delete_enable` tinyint(1) not NULL default '0'" ; //���\�R��
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�s�W���, �O�_���\�R��-- by smallduh (2013-03-03)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//�ק��ƪ�A�W�[�v�ɱK�X�]�p
$up_file_name =$upgrade_str."2013-03-04.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_setup` CHANGE `qtext` `qtext` text not NULL" ; //
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�ק��D�����榡����r text -- by smallduh (2013-03-04)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//�ק��ƪ�A�d��Ƥ��ɼW�[�O�������Ѯv�\��
$up_file_name =$upgrade_str."2013-03-08.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_record1` ADD `teacher_sn` int(10) NULL" ; //
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�d��Ƥ��ɼW�[�O�������Ѯv�\�� -- by smallduh (2013-03-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//�ק��ƪ�A�W�[���r����
$up_file_name =$upgrade_str."2017-02-03.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "create TABLE `contest_typebank` ("; 	//���r�D�w
	$query[0].="id int(5) not null auto_increment,";  	//�y����
	$query[0].="kind tinyint(1) not null,";				//���O 1���� 2�^��
	$query[0].="article varchar(100) not null,";		//�峹���D
	$query[0].="content text not null,";				//�峹���e
	$query[0].="open tinyint(1) not null,";				//�O�_�}��m��
	$query[0].="primary key (id)";
	$query[0].=") ENGINE=MyISAM ";

	$query[1] = "create TABLE `contest_typerec` ("; 	//���r���ɰO��
	$query[1].="id int(5) not null auto_increment,";  	//�y����
	$query[1].="race_id int(5) not null,";				//�v�ɬy���� , �C���v�ɦ��⦸���r���|
	$query[1].="type_id_1 int(5) not null,";			//�ϥ��D�w�y����1
	$query[1].="type_id_2 int(5) not null,";			//�ϥ��D�w�y����2
	$query[1].="student_sn int(10) not null,";			//�ǥ�
	$query[1].="sttime_1 datetime not null,";			//�Ģ����}�l�ɶ�
	$query[1].="endtime_1 datetime not null,";			//�Ģ��������ɶ�
	$query[1].="answer_1 text not null,";				//�Ģ����@�����e
	$query[1].="correct_1 decimal(3,2) not null,";		//�Ģ����@������v
	$query[1].="speed_1 int(5) not null,";				//�Ģ����t�� (���T�r��)
	$query[1].="sttime_2 datetime not null,";			//��2���}�l�ɶ�
	$query[1].="endtime_2 datetime not null,";			//��2�������ɶ�
	$query[1].="answer_2 text not null,";				//��2���@�����e
	$query[1].="correct_2 decimal(3,2) not null,";		//��2���@������v
	$query[1].="speed_2 int(5) not null,";				//��2���t�� (���T�r��)
	$query[1].="score_correct decimal(3,2) not null,";	//�̲׵���v
	$query[1].="score_speed int(5) not null,";			//�̲׳t�� (���T�r��)
	$query[1].="primary key (id)";
	$query[1].=") ENGINE=MyISAM ";

	$art1 = "�@�@�B�ͶR�F�@���ơA��⪺���l�a�զ���A��o�����ڭ̬ݮɡA�@����ѤQ���P����\r\n";
	$art1.= "���P�ǻ��G�u�ڡA�n���ѽL�����C�v\r\n";
	$art1.= "�@�@�u�ڬݭ˦��I���Z�ȡC�v�ڻ��C\r\n";
	$art1.= "�@�@�u�u���@�����񨧿|�C�v�@��~���s�u�j���ȡv���P�Ǻ򱵵ۻ��C\r\n";
	$art1.= "�ڭ̤��T����j���A�P�˪��@���ơA�C�ӤH�o�����P���Pı�C����B�ͳs�����ƥίȥ]�n\r\n";
	$art1.= "�A�oı�o��ƴN�O��ơA���O�ѽL�A�]���O�Z�ȡA�󤣬O�񨧿|�C\r\n";
	$art1.= "�@�@�H�H���Y���[�I���ɬۦP�A���O�M�ӤH���ʮ�P�ͬ����Ҧ����C\r\n";
	$art1.= "�@�@�p�G�g�`�}�������ܡA�K�|�o�{�ܤ֦��@�ǥ��S���H���ʹL�F���y�ܻ��A�����a�Ϊ��\r\n";
	$art1.= "����ơA�����H�Y�०�C�@��c�������󴿫����o���̤@�����˲@���}�G���c�l���G�u�L�׫�\r\n";
	$art1.= "�����ݪ��ˤl�A�٬O���H���w�A�ҥH���Ƚ椣�X�h�C�v\r\n";
	$art1.= "�@�@�N�H�u�H�v�ӻ��A�S������O�p���H�]�\�ڭ̬ݬY�H�������A���O�b�L���k�ͩM�k�ͤߤ�\r\n";
	$art1.= "�A�����{���L�p�u�ѥP�v�Ρu�հ����l�v��a�����L�ʡC\r\n";
	$art1.= "�@�@�H�`�|�h�M�D�ۤv���w���ƪ��A�C�ӤH���ݪk���[�I���P�A�èS���������Y�A���n���O�H\r\n";
	$art1.= "�P�H�����A���Ӧ������e�ԩM�L����誺�ݪk�P�[�I�����q�C\r\n";
	$art1.= "�@�@�p�G�L��q�o�����樣��X�����A���A�S�󥲭n�L���V�������h��ť����O�H�Ať�A���Q\r\n";
	$art1.= "��A�L�ݥL����X�A�������|�����q�������P���C�H�P�H�������T����j���A�P�˪��@����\r\n";
	$art1.= "�A�C�ӤH�o�����P�������A�������O�ѩ�ʥF�������q���t�G�F�]���A���F��ּ����A�W�i�M\r\n";
	$art1.= "�ӡA�ڭ̥����V�O���i���q�C";


	$art2 ="As President Donald Trump's White House attempts to embark on a period of order and discipline, many in Washington \r\n";
	$art2.="are greeting the news with a collective eye roll.\r\n";
	$art2.="\r\n";
	$art2.="At the start of Trump's third week in office, top advisers are trying to move beyond the infighting and feuds inside\r\n";
	$art2.="the West Wing, which have alarmed Republicans and official Washington far more than the President himself.\r\n";
	$art2.="\r\n";
	$art2.="White House chief of staff Reince Priebus is asserting more authority to run things, administration officials say,\r\n";
	$art2.="in hopes of trying to \"keep things running smoothly\" after a rocky -- and active -- first two weeks.\r\n";
	$art2.="\r\n";
	$art2.="The administration has privately pledged to do a better job of keeping relevant government agencies and congressional\r\n";
	$art2.="allies in the loop when rolling out executive actions and legislative priorities -- a far cry from the sloppy implementation\r\n";
	$art2.="of Trump's travel ban. That experience left aides cringing at the public beating they were taking, and personally\r\n";
	$art2.="irritated Trump.\r\n";
	$art2.="\r\n";
	$art2.="\"The first 10 days there's a bit of learning the ropes for any incoming administration,\" said Jason Miller, a \r\n";
	$art2.="former spokesman for Trump's presidential campaign. \"They're going to be finding their sea legs and getting \r\n";
	$art2.="everything nailed down.\"\r\n";
	$art2.="\r\n";
	$art2.="Privately, lobbyists, congressional staffers and other GOP political operatives said they're dubious that an\r\n";
	$art2.="orderly White House is on the horizon.\r\n";
	$art2.="\r\n";
	$art2.="\"I just don't see how the leopard changes his spots,\" said one GOP operative, who declined to be named because this \r\n";
	$art2.="person didn't want to appear to be rooting against the President. \"He got to the job by drinking rocket fuel, and \r\n";
	$art2.="now people are wondering if he can sit down and delegate and be a responsible executive.\"\r\n";
	$art2.="\r\n";
	$art2.="Within the White House, Trump's team has been more intent on quashing stories about turf wars and internal conflict \r\n";
	$art2.="than actually resolving them, said a top Republican close to the administration.\r\n";
	$art2.="\r\n";
	$art2.="This Republican, who spoke on condition of anonymity to frankly discuss internal workings of the administration,\r\n";
	$art2.="said any suggestion that all conflicts between Priebus and chief strategist Steve Bannon have been eliminated are mistaken.\r\n";
	$art2.="\r\n";
	$art2.="And that doesn't much matter to Trump. He operates easily in tumultuous environments. When disagreements arise, staffers \r\n";
	$art2.="tend to duke it out before they head to the Oval Office, keeping most of the discord from Trump's view.\r\n";
	$art2.="\r\n";
	$art2.="The turmoil surrounding Trump has often been ascribed to whichever aide has his ear at the time. Priebus's style is more \r\n";
	$art2.="cautious; he cares about the details. Bannon favors disruptive action and isn't fazed by a little public outcry if it's \r\n";
	$art2.="in pursuit of sweeping change.\r\n";
	$art2.="\r\n";
	$art2.="But the reality is the frenzied pace -- and now the cycle of chaos to calm -- is mostly driven by Trump, according to people \r\n";
	$art2.="close to him.\r\n";
	$art2.="\r\n";
	$art2.="The President's priority was to move quickly to deliver on bold promises he made on the campaign trail. When he saw the backlash \r\n";
	$art2.="over the travel ban, he aimed to correct the process by tapping Priebus to run point going forward.\r\n";
	$art2.="\r\n";
	$art2.="It's a cyclical pattern that Republicans close to the White House predict will dominate at least the first year of his administration.\r\n";
	$art2.="\r\n";
	$art2.="\"We've been punked enough times,\" said one Republican operative in Washington, who spoke anonymously because this person works\r\n";
	$art2.="with the White House. \"The only thing that can change him is the weight of the office. And hopefully it begins to weigh on him.\"\r\n";
	$art2.="\r\n";
	$art2.="Trump may be largely immune to this kind of volatility, but everyone surrounding him is not. A number of former campaign staffers are \r\n";
	$art2.="seeking job opportunities within government agencies -- even as positions within the White House remain unfilled -- to distance \r\n";
	$art2.="themselves from the \"West Wing circus,\" according to a person familiar with the situation.";

	$query[2]="insert into contest_typebank (kind,article,content,open) VALUES ('1','���q','".addslashes($art1)."','1')";
	$query[3]="insert into contest_typebank (kind,article,content,open) VALUES ('2','White House turmoil rankles Washington more than Trump','".addslashes($art2)."','1')";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�W�[���r���ɥθ�ƪ� -- by smallduh (2017-02-03)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//�ק��ƪ�A�W�[���r�����D�����
$up_file_name =$upgrade_str."2017-02-10.txt";
if (!is_file($up_file_name)) {
	$query[0] = "ALTER TABLE `contest_setup` ADD `type_id_1` int(5) not NULL" ; //���r���ɲ�1�g
	$query[1] = "ALTER TABLE `contest_setup` ADD `type_id_2` int(5) not NULL" ; //���r���ɲ�2�g
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�W�[���r�����D����� -- by smallduh (2017-02-10)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}

//�ק��ƪ����
$up_file_name =$upgrade_str."2017-02-11.txt";
if (!is_file($up_file_name)) {
	$query[0] = "ALTER TABLE `contest_typerec` CHANGE `correct_1` `correct_1` DECIMAL( 5, 2 ) NOT NULL ;";
	$query[1] = "ALTER TABLE `contest_typerec` CHANGE `correct_2` `correct_2` DECIMAL( 5, 2 ) NOT NULL ;";
	$query[2] = "ALTER TABLE `contest_typerec` CHANGE `score_correct` `score_correct` DECIMAL( 5, 2 ) NOT NULL ;";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n ��s���\ ! \n";
		else
			$temp_str .= "$query[$i]\n ��s���� ! \n";
	}
	$temp_query = "�ק勵�T�v��� -- by smallduh (2017-02-11)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}

?>