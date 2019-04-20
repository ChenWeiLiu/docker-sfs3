{{* $Id: stud_query_stud_kind_explode_csv.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
"身份別","學號","班級","座號","姓名","性別","身份證號","生日","地址","電話","父親","母親","監護人"
	{{section loop=$data_arr name=arr_key}}
		{{assign var=cid value=$data_arr[arr_key].stud_class}}
		{{assign var=sex value=$data_arr[arr_key].stud_sex}}
"{{$stud_kind}}","{{$data_arr[arr_key].stud_id}}","{{$class_arr[$cid]}}","{{$data_arr[arr_key].stud_site}}","{{$data_arr[arr_key].stud_name}}","{{$sex_arr[$sex]}}","{{$data_arr[arr_key].stud_person_id}}","{{$data_arr[arr_key].stud_birthday}}","{{$data_arr[arr_key].stud_addr_2}}","{{$data_arr[arr_key].stud_tel_2}}","{{$data_arr[arr_key].fath_name}}","{{$data_arr[arr_key].moth_name}}","{{$data_arr[arr_key].guardian_name}}"
	{{/section}}