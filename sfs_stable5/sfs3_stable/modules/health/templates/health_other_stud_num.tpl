{{* $Id: health_other_stud_num.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0">
	<tr>
	<td style="vertical-align:top;">
		<table style="background-color:#9EBCDD;" cellspacing="1" cellpadding="4">
			<tr style="background-color:#E1ECFF;">
			<td>年級</td><td>男學生</td><td>女學生</td><td>學生合計</td>
			</tr>
{{foreach from=$class_arr item=d key=i}}
			<tr style="background-color:#FFFFFF;text-align:center;">
			<td>{{$d}}</td>
			<td>{{$nums_arr.$i.1|@intval}} 人</td>
			<td>{{$nums_arr.$i.2|@intval}} 人</td>
			<td>{{$nums_arr.$i.all|@intval}} 人</td>
			</tr>
{{/foreach}}
		</table>
	</td>
	<td>&nbsp;</td>
	<td style="vertical-align:top;">
		<table style="background-color:#9EBCDD;" cellspacing="1" cellpadding="4">
			<tr style="background-color:#E1ECFF;">
			<td>年級</td><td>總班級數</td><td>男學生</td><td>女學生</td><td>學生合計</td>
			</tr>
{{foreach from=$year_arr item=d key=i}}
			<tr style="background-color:#FFFFFF;text-align:center;">
			<td>{{$d}}</td>
			<td>{{$nums_arr.$i.nums|@intval}}</td>
			<td>{{$nums_arr.$i.1|@intval}}</td>
			<td>{{$nums_arr.$i.2|@intval}}</td>
			<td>{{$nums_arr.$i.all|@intval}}</td>
			</tr>
{{/foreach}}
			<tr style="background-color:#FFFFFF;text-align:center;">
			<td>合計</td>
			<td>{{$nums_arr.all.nums|@intval}}</td>
			<td>{{$nums_arr.all.1|@intval}}</td>
			<td>{{$nums_arr.all.2|@intval}}</td>
			<td>{{$nums_arr.all.all|@intval}}</td>
			</tr>
		</table>
		<span style="font-size:10pt;">
		<br>含在家教育人數 : 2 人
{{foreach from=$pers_arr item=d key=i}}
{{foreach from=$d item=dd key=ii}}
		<br>{{$class_arr.$i}}{{$ii}}號 ({{if $dd.stud_sex==1}}男{{else}}女{{/if}}) {{$dd.stud_name}}
{{/foreach}}
{{/foreach}}
		</span>
	</td>
	</tr>
</table>
