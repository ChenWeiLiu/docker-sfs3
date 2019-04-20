{{* $Id: health_checkinput_st_ora.tpl 5735 2009-11-03 06:56:49Z brucelyc $ *}}
{{if $smarty.post.student_sn}}
{{assign var=sn value=$smarty.post.student_sn}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=d value=$health_data->health_data.$sn.$year_seme}}
{{foreach from=$d item=dd key=k}}{{if ($k|@substr:0:1)=="T"}}{{$k|@substr:1:2}}{{$teesb.$dd}}{{/if}}{{/foreach}}
{{/if}}
