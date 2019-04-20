<?php

if(!$CONN){
        echo "go away !!";
        exit;
}

// 建立師生帳號欄位 view
$SQL="ALTER VIEW student_view AS SELECT stud_name,stud_id,stud_sex, edu_key,stud_study_cond,curr_class_num, ldap_password,YEAR(stud_birthday)-1911 AS birth_year,MONTH(stud_birthday) AS birth_month,stud_mail FROM stud_base WHERE stud_study_cond in (0,15);";
$rs=$CONN->Execute($SQL);

?>
