{{* $Id: health_other_cdc.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{assign var=k value=2}}
"�ǮեN�X","�ǮզW��","�ǥͩm�W","�ʧO","�~��","�Z��","�y��"
{{foreach from=$rowdata item=d key=sn}}
"{{$s_arr.sch_id}}","{{$s_arr.sch_cname}}","{{$d.stud_name}}","{{$k-$d.stud_sex}}","{{$d.seme_class|@substr:0:1}}","{{$d.seme_class|@substr:-2:2}}","{{$d.seme_num}}"
{{/foreach}}
