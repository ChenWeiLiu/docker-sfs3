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
?>

<br>
�H�U���H POST ��k�� SFS3 �Ǧ^���Ǵ��Z�Ű}�C���d�ҡA�Ѽ� character=UTF-8 ��ܧ����ন UTF-8 �榡�A�Ǧ^ <br><br>

<p style="font-size:10pt;border-style: solid;border-width: thin;border-color: #000000">
        $ch = curl_init();<br>
        $url="<span style="color:#FF0000"><?= $SFS_PATH_HTML ?>modules/rest/api.php</span>";<br>
        //�n POST �� �U�ӰѼƪ���T<br>
        $POST_DATA=array( "<span style="color:#FF0000">search</span>"=>base64_encode("<span style="color:#FF0000">classroom</span>"),"<span style="color:#FF0000">character</span>"=>base64_encode("<span style="color:#FF0000">UTF-8</span>"));<br>
    <br>
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  '<span style="color:#FF0000">POST</span>');    //�i�� GET , PUT , POST , DELETE �� method<br>
        curl_setopt($ch, CURLOPT_POSTFIELDS,$POST_DATA);     //��ǻ����ѼƱa�J<br>
    <br>
        curl_setopt($ch, CURLOPT_HEADER, 0);<br>
        //��b header �̪��{�Ҹ�T<br>
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('<span style="color:#FF0000">S_ID:�b��</span>','<span style="color:#FF0000">S_PWD:�K�X</span>'));<br>
        curl_setopt($ch, CURLOPT_URL, $url);<br>
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);<br>
    <br>
        $receive=curl_exec($ch);<br>
        curl_close($ch);<br>
    <br>
        //�ন�}�C<br>
        $SERVICE=json_decode($receive,true); <br>
        $SERVICE=array_base64_decode($SERVICE);<br>
    <br>
        if ($SERVICE['result']) {<br>
        &nbsp;&nbsp;&nbsp;    $data=$SERVICE['data'];    //���\���o�����<br>
        } else {<br>
        &nbsp;&nbsp;&nbsp;    //�o�Ϳ��~!<br>
        &nbsp;&nbsp;&nbsp;        echo  $SERVICE['message']; //�C�X���~�T��<br>
        }<br>
    <br>
    <br>
     <br>
    //���ư� base64_decode<br>
    function array_base64_decode($data) { <br>
    �@foreach($data as $key=>$value){<br>
    �@if (is_array($value)){<br>
    �@�@�@$data[$key] = array_base64_decode($value);<br>
    �@}else{<br>
    �@�@�@$data[$key]= base64_decode($value);<br>
    �@}<br>
    } // end foreach<br>
    <br>
    return $data;<br>
    <br>

</p>

