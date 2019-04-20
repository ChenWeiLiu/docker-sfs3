{{* $Id: health_stud_now.tpl 5831 2010-01-19 08:05:00Z hami $ *}}
<form name="myform" action="{{$smarty.post.PHP_SELF}}" method="post">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" class="small">
<tr>
<td colspan="2" style="color:white;text-align:center;">目前編輯學生</td>
</tr>
<tr bgcolor="#f4feff">
<td>統編</td><td>{{$health_data->stud_base.$sn.stud_person_id}}</td>
</tr>
<tr bgcolor="white">
<td>學生</td><td>{{$health_data->stud_base.$sn.stud_name}}</td>
</tr>
<tr bgcolor="#f4feff">
<td>學號</td><td>{{$health_data->stud_base.$sn.stud_id}}</td>
</tr>
<tr bgcolor="white">
<td>生日</td><td>{{$health_data->stud_base.$sn.stud_birthday}}</td>
</tr>
<tr style="background-color:white;">
<td>血型</td><td>{{$health_data->stud_base.$sn.stud_blood_type}}</td>
</tr>
<tr bgcolor="#f4feff">
<td>父親</td><td>{{$health_data->stud_base.$sn.fath_name}}</td>
</tr>
<tr bgcolor="white">
<td>母親</td><td>{{$health_data->stud_base.$sn.moth_name}}</td>
</tr>
<tr bgcolor="#f4feff">
<td>緊急連絡</td><td>{{$health_data->stud_base.$sn.stud_tel_2}}</td>
</tr>
</table>
