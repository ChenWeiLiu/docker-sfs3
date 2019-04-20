<?php

/**
 * Created by PhpStorm.
 * User: smallduh
 * Date: 2018/3/28
 * Time: 下午 04:54
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
        <td align="right" style="width:150px">學生姓名</td>
        <td align="left" style="width:650px;color:#0000DD">{$value_array['stud_name']}</td>
    </tr>
    <tr>
        <td align="right" style="width:100px">成績來源</td>
        <td align="left" style="width:700px"><input type="text" name="score_source" value="{$value_array['score_source']}" size="10"></td>
    </tr>
    <tr>
        <td align="right">百分數</td>
        <td align="left"><input type="text" name="score" size="10" onBlur="unset_ower(this)"></td>
    </tr>
    <tr>
        <td align="right">等第</td>
        <td align="left">
            <select size="1" name="score_level">
                <option value=""></option>
                <option value="優">優</option>
                <option value="甲">甲</option>
                <option value="乙">乙</option>
                <option value="丙">丙</option>
                <option value="丁">丁</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">努力程度</td>
        <td align="left">
            <select size="1" name="hard_level">
                <option value=""></option>
                <option>表現優異</option>
                <option>表現良好</option>
                <option>表現尚可</option>
                <option>需再加油</option>
                <option>有待改進</option>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">文字描述</td>
        <td align="left"><input type="text" name="score_memo" value="{$value_array['score_memo']}" style="width:100%"></td>
    </tr>

    <tr>
        <td align="right">備註或附記說明</td>
        <td align="left">
            <textarea name="append_memo" rows="10" style="width:100%">{$value_array['append_memo']}</textarea>
        </td>
    </tr>
    <tr>
        <td align="right">附件檔案</td>
        <td align="left"><input type="file" name="append_file" accept="application/pdf" style="width:100%"></td>
    </tr>
  </table>
  <span style="float:right;margin-top:10px">
  <input type="submit" onclick="return check_before_save()" value="儲存資料">
  </span>
</form>

JS;

    return $data;

}

function get_stud_base($student_sn) {
 global $CONN;
    //取得學生基本資料
    $sql="select * from stud_base where student_sn='$student_sn'";
    $res=$CONN->Execute($sql) or trigger_error($sql,256);
    $data=$res->fetchrow();

    return $data;
}