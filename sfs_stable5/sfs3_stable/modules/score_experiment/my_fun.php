<?php

/**
 * Created by PhpStorm.
 * User: smallduh
 * Date: 2018/3/28
 * Time: �U�� 04:54
 */

function make_form($value_array=array(),$title) {

$data = <<< JS
<p style="text-align:center;font-size:16pt">{$title}</p>
<form method="post" name="form_block" action="{$_SERVER['PHP_SELF']}" enctype="multipart/form-data">
    <input type="hidden" name="act" value="save">
    <input type="hidden" name="sn" value="{$value_array['sn']}">
    <input type="hidden" name="student_sn" value="{$value_array['student_sn']}">
  <table border="0" width="100%">
  <tr>
        <td align="right" style="width:150px">�ǥͩm�W</td>
        <td align="left" style="width:650px;color:#0000DD">{$value_array['stud_name']}</td>
    </tr>
    <tr>
        <td align="right" style="width:100px">���Z�ӷ�</td>
        <td align="left" style="width:700px"><input type="text" name="score_source" value="{$value_array['score_source']}" size="10"></td>
    </tr>
    <tr>
        <td align="right">�ʤ���</td>
        <td align="left"><input type="text" name="score" size="10" onBlur="unset_ower(this)"></td>
    </tr>
    <tr>
        <td align="right">����</td>
        <td align="left">
            <select size="1" name="score_level">
                <option value=""></option>
                <option value="�u">�u</option>
                <option value="��">��</option>
                <option value="�A">�A</option>
                <option value="��">��</option>
                <option value="�B">�B</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">�V�O�{��</td>
        <td align="left">
            <select size="1" name="hard_level">
                <option value=""></option>
                <option>��{�u��</option>
                <option>��{�}�n</option>
                <option>��{�|�i</option>
                <option>�ݦA�[�o</option>
                <option>���ݧ�i</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">��r�y�z</td>
        <td align="left"><input type="text" name="score_memo" value="{$value_array['score_memo']}" style="width:100%"></td>
    </tr>

    <tr>
        <td align="right">�Ƶ��Ϊ��O����</td>
        <td align="left">
            <textarea name="append_memo" rows="10" style="width:100%">{$value_array['append_memo']}</textarea>
        </td>
    </tr>
    <tr>
        <td align="right">�����ɮ�</td>
        <td align="left"><input type="file" name="append_file" accept="application/pdf" style="width:100%"></td>
    </tr>
  </table>
  <span style="float:right;margin-top:10px">
  <input type="submit" onclick="return check_before_save()" value="�x�s���">
  </span>
</form>

JS;

    return $data;

}

function get_stud_base($student_sn) {
 global $CONN;
    //���o�ǥͰ򥻸��
    $sql="select * from stud_base where student_sn='$student_sn'";
    $res=$CONN->Execute($sql) or trigger_error($sql,256);
    $data=$res->fetchrow();

    return $data;
}