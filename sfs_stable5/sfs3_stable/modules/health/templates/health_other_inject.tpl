{{* $Id: health_other_inject.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
"�ǮեN�X","�ǥͲνs","�ǥͩm�W","�ͤ�","���ئ~��","���ج̭]","���O","���ؤ��","���ئa�I","�̭]�帹"
{{foreach from=$rowdata item=d key=sn}}
"{{$s_arr.sch_id}}","{{$basedata.$sn.stud_person_id}}","{{$basedata.$sn.stud_name}}","{{$basedata.$sn.stud_birthday}}","{{$basedata.$sn.seme_year}}","{{$d.id}}","{{$d.no}}","{{$d.date}}","",""
{{/foreach}}