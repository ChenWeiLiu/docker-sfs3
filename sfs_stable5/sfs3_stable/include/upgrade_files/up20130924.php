<?php

if(!$CONN){
        echo "go away !!";
        exit;
}
// 建立師生帳號欄位 view
$SQL="DROP VIEW IF EXISTS  teacher_course_view;";
$rs=$CONN->Execute($SQL);

$SQL="CREATE VIEW teacher_course_view AS SELECT b.edu_key, b.name,a.teacher_sn,a.class_year,a.class_name,a.class_id,a.year,a.semester,d.subject_name FROM score_course a , teacher_base b ,score_ss c,score_subject d WHERE b.teach_condition=0 AND a.teacher_sn=b.teacher_sn AND a.ss_id=c.ss_id AND c.subject_id=d.subject_id GROUP BY  a.teacher_sn,a.class_id ORDER BY a.teacher_sn;";
$rs=$CONN->Execute($SQL);

$SQL="DROP VIEW IF EXISTS  teacher_post_view;";
$rs=$CONN->Execute($SQL);

$SQL="CREATE VIEW teacher_post_view AS SELECT teacher_base.teach_id, teacher_base.name,teacher_base.edu_key,teacher_base.ldap_password, teacher_base.sex,teacher_post.class_num,teacher_title.title_name FROM teacher_base LEFT JOIN teacher_post ON  teacher_base.teacher_sn = teacher_post.teacher_sn LEFT JOIN teacher_title ON teacher_post.teach_title_id=teacher_title.teach_title_id WHERE  teacher_base.teach_condition=0;;";
$rs=$CONN->Execute($SQL);

$SQL="DROP VIEW IF EXISTS  student_view;";
$rs=$CONN->Execute($SQL);

$SQL="CREATE VIEW student_view AS SELECT stud_name,stud_id,stud_sex, edu_key,stud_study_cond,curr_class_num, ldap_password FROM stud_base WHERE  stud_study_cond in (0,15);";
$rs=$CONN->Execute($SQL);


?>