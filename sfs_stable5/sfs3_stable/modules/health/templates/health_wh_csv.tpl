{{* $Id: health_wh_csv.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{foreach from=$health_data->stud_data item=sd key=seme_class}}
{{foreach from=$sd item=d key=seme_num}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_base.$sn}}
"{{$seme_class}}","{{$seme_num}}","{{$dd.stud_id}}","000.0","00.0"
{{/foreach}}
{{/foreach}}
