{{* $Id: health_web_wh_csv.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
"PID","GradeID","Sem","Weight","Height"
{{foreach from=$rowdata item=d}}
"{{$d.stud_person_id}}","{{$d.year-$d.stud_study_year+$IS_JHORES+1}}","{{$d.semester}}","{{$d.weight}}","{{$d.height}}"
{{/foreach}}
