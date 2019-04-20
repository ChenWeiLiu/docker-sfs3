<?php
	// $Id: teach_config.php 9179 2017-12-01 14:09:00Z smallduh $
	//�t�γ]�w��
	include_once "../../include/config.php";
	//�禡�w
	include_once "../../include/sfs_case_PLlib.php";
	
	include_once "../../include/sfs_case_dataarray.php";
	//�s�W���s�W��
	$newBtn = " �s�W��� ";
	//�ק���s�W��
	$editBtn = " �T�w�ק� ";
	//�R�����s�W��
	$delBtn = " �T�w�R�� ";
	//�T�w�s�W���s�W��
	$postBtn = " �T�w�s�W ";
	//�s�W�ɱҥάy�����\��
	$is_IDauto = 1 ; // 0 ������	

	//�j�M�N��
	$srchID = " �j�M�N�� ";
	//�j�M�m�W
	$srchName = " �j�M�m�W ";

	
	//�����]�w��ܵ���
	$gridRow_num = 16;
	//����橳��]�w
	$gridBgcolor="#DDDDDC";
	//�����k������C��
	$gridBoy_color = "blue";
	//�����k������C��
	$gridGirl_color = "#FF6633";
	//�Ӥ��e��
	$img_width = 120;	
	
	//�ؿ����{��
	$teach_menu_p = array("teach_list.php"=>"�򥻸��","teach_post.php"=>"��¾���","teach_connect.php"=>"�������","mteacher.php"=>"�פJ�Юv���","teach_list_photo.php"=>"�b¾�Юv�@����","teach_list_subject.php"=>"���Ф@����","teach_appraisal.php"=>"���ԦҮ�");
	
	//�]�w�W�ǹϤ����|
	$img_path = "photo/teacher";
	
	/* �W���ɮ׼Ȧs�ؿ� */
	$path_str = "temp/teacher";
	set_upload_path($path_str);
	$temp_path = $UPLOAD_PATH.$path_str;

	/*�~�B���O*/
	$salary_kind_array=array("teacher"=>"�Ш|�H��","worker"=>"�u��","skilled-worker"=>"�ޤu","nurse-4"=>"�h(��)��","nurse-3"=>"�v(�T)��","nurse-2"=>"�v(�G)��","nurse-1"=>"�v(�@)��","official-1"=>"�e��1¾��","official-2"=>"�e��2¾��","official-3"=>"�e��3¾��","official-4"=>"�e��4¾��","official-5"=>"�e��5¾��","official-6"=>"�˥�6¾��","official-7"=>"�˥�7¾��","official-8"=>"�˥�8¾��","official-9"=>"�˥�9¾��","official-10"=>"²��10","official-11"=>"²��11¾��","official-12"=>"²��12¾��","official-13"=>"²��13¾��","official-14"=>"²��14¾��","other"=>"���ͱԷF��");

	/* �~�B�}�C */
	$salary_array['teacher']=array(90,100,110,120,130,140,150,160,170,180,190,200,210,220,230,245,260,275,290,310,330,350,370,390,410,430,450,475,500,525,550,575,600,625,650,680,710,740,770);
	$salary_array['worker']=array(90,95,100,105,110,115,120,125,130,135,140,145,150,155,160,165,170);
	$salary_array['skilled-worker']=array(120,125,130,135,140,145,150,155,160,165,170);
	$salary_array['official-1']=array(160,170,180,190,200,210,220,230,240,250,260,270,280);
	$salary_array['official-2']=array(230,240,250,260,270,280,290,300,310,320,330);
	$salary_array['official-3']=array(280,290,300,310,320,330,340,350,360,370,385,400,415);
	$salary_array['official-4']=array(300,310,320,330,340,350,360,370,385,400,415,430,445);
	$salary_array['official-5']=array(330,340,350,360,370,385,400,415,430,445,460,475,490,505,520);
	$salary_array['official-6']=array(385,400,415,430,445,460,475,490,505,520,535);
	$salary_array['official-7']=array(415,430,445,460,475,490,505,520,535,550,590);
	$salary_array['official-8']=array(445,460,475,490,505,520,535,550,590,610,630);
	$salary_array['official-9']=array(490,505,520,535,550,590,610,630,650,670,690,710);
	$salary_array['official-10']=array(590,610,630,650,670,690,710,730,750,780);
	$salary_array['official-11']=array(610,630,650,670,690,710,730,750,780,790);
	$salary_array['official-12']=array(650,670,690,710,730,750,780,790,800);
	$salary_array['official-13']=array(710,730,750,780,790,800);
	$salary_array['official-14']=array(800);
	$salary_array['nurse-1']=array(590,610,630,650,670,690,710,730,750,780,790);
	$salary_array['nurse-2']=array(445,460,475,490,505,520,535,550,590,610,630,650,670,690,710);
	$salary_array['nurse-3']=array(385,400,415,430,445,460,475,490,505,520,535,550,590);
	$salary_array['nurse-4']=array(280,290,300,310,320,330,340,350,360,370,385,400,415,430,445,460,475,490,505,520,535);
	$salary_array['other']=array(90,100,110,120,130,140,150,160,170,180,190,200,210,220,230,245,260,275,290,310,330,350,370,390,410,430,450,475,500,525,550,575,600,625,650,680,710,740,770);

	/*�M�~�[��*/
	//�Юv   �ժ��@�ߥH�̰� 31320 , ��l���b�~�B(key)�H�U, �H�ӭȬ��[����
	$spec_array['teacher']=array("230"=>20130,"330"=>23160,"450"=>26290,"770"=>31320);
	//���ȤH��
	$spec_array['official']=array("1"=>17710,"2"=>17770,"3"=>17830,"4"=>18060,"5"=>18910,"6"=>20790,"7"=>21710,"8"=>24700,"9"=>25770,"10"=>29960,"11"=>32650,"12"=>36690,"13"=>37840,"14"=>40630);
	for ($i=1;$i<=14;$i++) {
		$key="official-".$i;
		$spec_array[$key]=$spec_array['official'];
	}

	//�u��
	$spec_array['worker']=array("1"=>15100);
	$spec_array['skilled-worker']=array("1"=>15390);
	//
	$spec_array['other']=$spec_array['official'];
	//�@�z�v
	$spec_array['nurse-1']=array("1"=>33195);
	$spec_array['nurse-2']=array("1"=>25250);
	$spec_array['nurse-3']=array("1"=>21220);
	$spec_array['nurse-4']=array("1"=>18920);

	/* �a��[�� */
    $area_allowance_array=array("�L"=>0,"�F�x�[��"=>0,"�����@��"=>10,"�����G��"=>20,"�����T��"=>30,"���s�@��"=>10,"���s�G��"=>10,"���s�T��"=>20,"���s�|��"=>30,"���q�@��"=>10,"���q�G��"=>20,"���q�T��"=>30);

  //���o�Ҳճ]�w
  $m_arr = get_sfs_module_set();
  extract($m_arr, EXTR_OVERWRITE);
  

	//���o�y����
	function get_sfs_id() {
		global $DEFAULT_TEA_ID_TITLE, $DEFAULT_TEA_ID_NUMS,$CONN;
		$sqlstr = "select max(teach_id) as mm from teacher_base ";
		if ($DEFAULT_TEA_ID_TITLE)
			$sqlstr .= " where teach_id like '$DEFAULT_TEA_ID_TITLE%'";
		$result = $CONN->Execute($sqlstr) or die ($sqlstr);
		
		$num = 1;
		for ($i=0;$i<strlen($DEFAULT_TEA_ID_NUMS);$i++)
			$num *= 10;
		
		if ($result->fields[0] == '' ) {//�Ĥ@��			
			$temp = $num+ intval($DEFAULT_TEA_ID_NUMS);
		}
		else {
			$temp = substr($result->fields[0],strlen($DEFAULT_TEA_ID_TITLE));
			$temp = $num + intval($temp)+1;		
		}
		$temp =  $DEFAULT_TEA_ID_TITLE.substr($temp,1);	
		return $temp;	
	}


	
?>
