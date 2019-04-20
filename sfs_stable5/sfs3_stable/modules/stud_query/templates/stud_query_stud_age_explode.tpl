{{* $Id: stud_query_stud_age_explode.tpl 6767 2012-05-23 08:45:54Z hami $ *}}

<style type="text/css">
<!--
table.formdata {
	border: 1px solid #5F6F7E;
	border-collapse:collapse ;
}

table.formdata th {
	border: 1px solid #5F6F7E;
	background-color:#E2E2E2;
	color:#000000 ;
	text-align:left;
	font-weight:normal;
	padding:2px 4px 2px 4px ;
	margin:0;
}

table.formdata td {
	border: 1px solid #5F6F7E;
	padding:2px 4px 2px 4px ;
	margin:0;
	font-size:11pt;
}

table.formdata tr.altrow {
	background-color: #DFE7F2;
	color: #000000;
}

table.formdata  tr:hover {
	background-color: #CCCCCC;
	color: #000000;
}

table.formdata tr.altrow:hover {
	background-color: #CCCCCC;
	color: #000000;
}

table.formdata th.out {
	background-color:#99CCCC;
}
-->
</style>

<table class="formdata" >
	<tr> 
		<th>#</th>
		<th>學號</th>
		<th>班級</th>
		<th>座號</th>
		<th>姓名</th>
		<th>性別</th>
		<th>生日</th>
	</tr>
	{{section loop=$data_arr name=arr_key}}
		<tr>
			<td>{{counter}}</td>
			<td>{{$data_arr[arr_key].stud_id}}
			{{assign var=cid value=$data_arr[arr_key].stud_class}}
			<td>{{$class_arr[$cid]}}
			<td>{{$data_arr[arr_key].stud_site}}
			<td>{{$data_arr[arr_key].stud_name}}
			{{assign var=sex value=$data_arr[arr_key].stud_sex}}
			<td>{{$sex_arr[$sex]}}
			<td>{{$data_arr[arr_key].stud_birthday}}
		</tr>
	{{/section}}
</table>