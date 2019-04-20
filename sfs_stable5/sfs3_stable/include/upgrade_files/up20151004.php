<?php
	
//$Id:$

if(!$CONN){
        echo "go away !!";
        exit;
}

    $SQL="ALTER TABLE grad_stud ADD INDEX(stud_id)";
    $rs=$CONN->Execute($SQL);
	
	
	$SQL="select stud_id,stud_grad_year,student_sn from grad_stud ";
	$rs=$CONN->Execute($SQL);

		while (!$rs->EOF) 
		{
		$grad_stud_id=$rs->fields["stud_id"];
		$grad_stud_grad_year=$rs->fields["stud_grad_year"]-2;
		$grad_student_sn=$rs->fields["student_sn"];
		 $sql_select = "select student_sn,stud_study_year from stud_base where stud_study_year='$grad_stud_grad_year' and stud_id='$grad_stud_id' Limit 1";
         $recordSet=$CONN->Execute($sql_select);
		 $student_sn=$recordSet->fields['student_sn'];
         $stud_study_year=$recordSet->fields['stud_study_year']+2;
		if ($grad_student_sn !=$student_sn)
		{		
	
		$upsql = "UPDATE grad_stud SET student_sn='$student_sn' WHERE stud_id='$grad_stud_id' and stud_grad_year='$stud_study_year'";
	    $CONN->Execute($upsql) ;

		}
		$rs->MoveNext();
		}

	
?>