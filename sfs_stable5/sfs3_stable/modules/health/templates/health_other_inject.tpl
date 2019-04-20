{{* $Id: health_other_inject.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
"學校代碼","學生統編","學生姓名","生日","接種年級","接種疫苗","劑別","接種日期","接種地點","疫苗批號"
{{foreach from=$rowdata item=d key=sn}}
"{{$s_arr.sch_id}}","{{$basedata.$sn.stud_person_id}}","{{$basedata.$sn.stud_name}}","{{$basedata.$sn.stud_birthday}}","{{$basedata.$sn.seme_year}}","{{$d.id}}","{{$d.no}}","{{$d.date}}","",""
{{/foreach}}
