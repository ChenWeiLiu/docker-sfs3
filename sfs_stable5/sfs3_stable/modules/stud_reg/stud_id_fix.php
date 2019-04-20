<?php

// 載入設定檔
include "stud_reg_config.php";


//印出檔頭
head();

$sql_sfs3="SHOW TABLES FROM $mysql_db";
$rs_sfs3=$CONN->Execute($sql_sfs3) or trigger_error($sql_sfs3,256);
while(!$rs_sfs3->EOF) {
        $tbname=$rs_sfs3->fields[0];

        $sql="SHOW COLUMNS FROM `$tbname`";
        $rs=$CONN->Execute($sql) or trigger_error($sql,256);
        while(!$rs->EOF) {
                $colname=$rs->fields[0];
                if($colname == 'stud_id') {
                        //echo "<li>$tbname</li>";
                        $tables[]=$tbname;
                }

                $rs->MoveNext();
        }
        $rs_sfs3->MoveNext();
}

if(!$IS_JHORES) {
        //要處理的學生
        $students=array(
                '1020541'=>'102051',
                '1020542'=>'102052',
                '1020543'=>'102053',
                '1020544'=>'102054',
        );
        foreach($students as $bad => $right) {
                //檢查有無學號重複
                $sql="SELECT COUNT(*) FROM stud_base WHERE stud_id='$bad'";
                $rs=$CONN->Execute($sql) or trigger_error($sql,256);
                if($rs->fields[0]>1) echo "學生基本資料表(stud_base)學號 $bad 有重複現象，為了避免衍生其他錯誤，程式不予校正處理
！"; else {
                        echo "<li>$bad --> $right</li>";
                        foreach($tables as $k => $v) {
                                $sql="UPDATE `$v` SET stud_id='$right' WHERE stud_id='$bad'";
                                $CONN->Execute($sql,$affected_rows);
                                echo "$affected_rows ： $sql<br>";
                        }
                }
        }
} else echo "本程式只適用於國小！";


foot();

?>
