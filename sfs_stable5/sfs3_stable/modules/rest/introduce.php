<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//���o�]�w��
include_once "config.php";
//���ҬO�_�n�J
sfs_check(); 
//�s�@��� ( $school_menu_p�}�C�]�w�� module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//Ū���ثe�ާ@���Ѯv���S���޲z�v , �f�t module-cfg.php �̪��]�w
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** �}�l�q�X���� ******************/
//�q�X SFS3 ���D
head();
//�C�X���
echo $tool_bar;

$URL='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<div>
 <p style="font-size:12pt">SFS RESTful API �A�Ȼ����G</p>
 <ol>
     <li>�A�����b�Ҳ��ܼƤ��]�w���\�I�s�� API �� IP�A�Y���]�w�A��ܤ����\�I�s�C</li>
     <li>�A�����b�Ҳ��ܼƤ��]�w S_ID �� S_PWD�A�� client �ݩI�s�ɡA�N�o����ܼƩ�b Header ���A�Ҧp�G<br>
         �� S_IP=api �AS_PWD=12356�ɡA�H php �� curl �I�s�ɡA�i�H�o��]�w <br>
         <p style="border-color: #000000;border-style: solid;border-width: thin;padding: 10px">curl_setopt($ch, CURLOPT_HTTPHEADER, array('S_ID:api','S_PWD:123456'));</p>
     </li>
     <li>�I�s���}�G<?php //= $SFS_PATH_HTML."modules/rest/api.php"; ?>
         <?= substr($URL,0,strlen($URL)-13)."api.php" ?>

     </li>
     <li> sfs3 �� http �ں��}�G<?= $SFS_PATH_HTML ?></li>
     <li> sfs3 �D�� IP ��}�G<?php echo $_SERVER['SERVER_ADDR'];?></li>
 </ol>
</div>

<br>
�I�s�\�໡���G
<table border="1" style="font-size:10pt;border-collapse: collapse" width="100%">
    <tr style="background-color: #00aa00">
        <td>�\��</td>
        <td>�I�s��k</td>
        <td>�Ѽ�</td>
        <td>�������</td>
    </tr>
    <tr>
        <td>���o�Ǧ~�Ǵ�</td>
        <td>POST�BGET</td>
        <td>
            search=year_seme (���n)<br>
        </td>
        <td>�}�C<br>
            �p: $data['1051']='105�Ǧ~��1�Ǵ�'
        </td>
    </tr>
    <tr>
        <td>���o�ثe�Ǧ~�ξǴ�</td>
        <td>POST�BGET</td>
        <td>
            search=curr_year_seme (���n)<br>
        </td>
        <td>�}�C, �p:<br>
            $data['curr_year']=105<br>
            $data['curr_seme']=1
        </td>
    </tr>
    <tr>
        <td>���o���Ǵ����Z�Ÿ��</td>
        <td>POST�BGET</td>
        <td>
            search=classroom (���n)<br>
            c_year=int (���w�~��, �D���n,�ꤤ�� 7-9,��p��1-6)<br>
            curr_year=int (���w�Ǧ~, �D���n, �p 105)<br>
            curr_seme=int (���w�Ǵ�, �D���n, 1 �� 2)
        </td>
        <td>�}�C, �p:<br>
            $data['104_2_07_01'] = �@�~1�Z
        </td>
    </tr>
    <tr>
        <td>���o�Z�ŽҪ�</td>
        <td>POST</td>
        <td>
            search=class_table (���n)<br>
            class_id=string (���n, �p: 104_2_07_01)
        </td>
        <td>�}�C:<br>
            $data[$key]['subject']=���<br>
            $data[$key]['teacher']=�Юv<br>
            $data[$key]['co_teacher']=��P�Юv<br>
            $data[$key]['room']=�W�Ҧa�I<br>
        </td>
    </tr>
    <tr>
        <td>���o�Юv�Ҫ�</td>
        <td>POST</td>
        <td>
            search=teacher_table (���n)<br>
            teacher_sn=int (���n)
        </td>
        <td>�}�C:<br>
            $data[$key]['subject']=���<br>
            $data[$key]['class_name']=�Z��<br>
            $data[$key]['room']=�W�Ҧa�I<br>
        </td>
    </tr>
    <tr>
        <td>���o�Z�ŦW��</td>
        <td>POST</td>
        <td>
            search=class_students_list (���n)<br>
            class_id=string (���n, �p: 104_2_07_01)
        </td>
        <td>�}�C:<br>
            $data[$key]['student_sn']=�y����<br>
            $data[$key]['stud_id']=�Ǹ�<br>
            $data[$key]['stud_name']=�m�W<br>
            $data[$key]['stud_class']=�Z��<br>
            $data[$key]['stud_sex']=�ʧO (1�k,2�k)<br>
            $data[$key]['stud_sitenum']=�y��
        </td>
    </tr>
    <tr>
        <td>���o�b¾�Юv�W��</td>
        <td>POST</td>
        <td>
            search=teachers_list (���n)<br>
            key=teacher_sn (�Hteacher_sn��@�}�C�� key ,�ٲ��۰ʥH�y�����@�� key)<br>
        </td>
        <td>�}�C:<br>
            $data[$key]['teacher_sn']=�y����<br>
            $data[$key]['teacher_id']=�b��<br>
            $data[$key]['teacher_name']=�m�W<br>
            $data[$key]['teacher_sex']=�ʧO (1�k,2�k)<br>
            $data[$key]['room_name']=�Ҧb�B��<br>
            $data[$key]['title_name']=¾��
        </td>
    </tr>
    <tr>
        <td>���o�ǥͤH�Ʋέp</td>
        <td>POST</td>
        <td>
            search=stud_status (���n)<br>
            year=int (�Ǧ~, �D���n , �ٲ����q�{����Ǧ~)<br>
            semester=int (�Ǵ�, �D���n , �ٲ����q�{����Ǵ�)

        </td>
        <td>�}�C:<br>
            $data[$key][$class_id]=�Z�ŦW��<br>
            $data[$key]['boy']=�k�ͼ�<br>
            $data[$key]['girl']=�k�ͼ�<br>
            $data[$key]['stud_all']=�`��
        </td>
    </tr>
    <!-- 2017.04.30 �W�[ -->
    <tr>
        <td>���o�Юv¾�ٰ}�C</td>
        <td>POST�BGET</td>
        <td>
            search=teacher_title (���n)<br>
        </td>
        <td>�}�C:<br>
            $data[$key]['title_name']=¾��<br>
            $data[$key]['title_short_name']=²��<br>
            $data[$key]['title_kind']=¾�����O<br>
            $data[$key]['room_name']=�B��
        </td>
    </tr>
    <!-- 2017.06.09 �W�[ -->
    <tr>
        <td>���o�B�Ǹ��</td>
        <td>POST�BGET</td>
        <td>
            search=room_office (���n)<br>
        </td>
        <td>�}�C:<br>
            $data[$key]['room_id']=�y����<br>
            $data[$key]['room_name']=�B�ǦW��
        </td>
    </tr>
</table>

 
<?php
//  --�{���ɧ�
foot();
?>