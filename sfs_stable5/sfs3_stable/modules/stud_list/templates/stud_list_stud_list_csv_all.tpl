{{* $Id: stud_list_stud_list_csv_all.tpl 8507 2015-09-01 13:37:18Z brucelyc $ *}}
"班級","座號","學號","姓名","性別"
{{foreach from=$class_arr item=d}}
{{assign var=site_num value=$smarty.section.i.index+1}}
"{{$d.seme_class}}","{{$d.seme_num}}","{{$d.stud_id}}","{{$d.stud_name}}","{{if $d.stud_sex==1}}男{{else}}女{{/if}}"
{{/foreach}}
