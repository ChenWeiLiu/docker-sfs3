{{* $Id: stud_list_stud_list_csv.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
"座號","學號","姓名","{{$oth_str}}","座號","學號","姓名","{{$oth_str}}","座號","學號","姓名","{{$oth_str}}"
{{section name=i start=0 loop=$max_num}}
{{assign var=site_num value=$smarty.section.i.index+1}}
"{{$site_num}}","{{$data_arr.1.$site_num.stud_id}}","{{$data_arr.1.$site_num.stud_name}}","{{$data_arr.1.$site_num.oth}}","{{$site_num}}","{{$data_arr.2.$site_num.stud_id}}","{{$data_arr.2.$site_num.stud_name}}","{{$data_arr.2.$site_num.oth}}","{{$site_num}}","{{$data_arr.3.$site_num.stud_id}}","{{$data_arr.3.$site_num.stud_name}}","{{$data_arr.3.$site_num.oth}}"
{{/section}}