{{* $Id: health_base_stud_list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="csv" value="匯出CSV檔">
<input type="submit" name="xls" value="匯出XLS檔">
<input type="submit" name="ods" value="匯出ODS檔">
<input type="submit" name="xls_all" value="匯出全年級XLS檔">
<input type="submit" name="ods_all" value="匯出全年級ODS檔">
<table bgcolor="#7e9cbd" cellspacing="1" cellpadding="4" class="small">
<tr style="background-color:#9ebcdd;color:white;text-align:center;">
<td>班級</td><td>座號</td><td>學號</td><td>姓名</td><td>身分證字號</td><td>出生日期</td><td>連絡地址</td><td>連絡人</td><td>連絡電話</td>
</tr>
{{foreach from=$health_data->stud_data item=sd key=seme_class}}
{{foreach from=$sd item=d key=seme_num}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_base.$sn}}
<tr bgcolor="white">
<td>{{$seme_class}}</td><td>{{$seme_num}}</td><td>{{$dd.stud_id}}</td><td>{{$dd.stud_name}}</td><td>{{$dd.stud_person_id}}</td><td>{{$dd.stud_birthday}}</td><td>{{$dd.stud_addr_2}}</td><td>{{$dd.guardian_name}}</td><td>{{$dd.stud_tel_2}}</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>