<?php
//
// 取得班級資訊 , 最後把資料存在 data 陣列中
//
$c_year=(isset($params['c_year']))?"and c_year='".$params['c_year']."'":"";

$sel_year=($params['sel_year']=='')?curr_year():$params['sel_year'];
$sel_seme=($params['sel_seme']=='')?curr_seme():$params['sel_seme'];

$sql_select = "select class_id,c_year,c_name from school_class where year='$sel_year' and semester='$sel_seme' $c_year and enable='1' order by c_year,c_sort";

$res=$CONN->Execute($sql_select);
$row=$res->getRows();

foreach ($row as $V) {
    $data[$V['class_id']]=$school_kind_name[$V['c_year']].$V['c_name']."班";
}