{{* $Id: teacher_self_teach_login.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td>�Ǧ�</td><td>�n�J�ɶ�</td><td>�n�JIP</td>
</tr>
{{foreach from=$rowdata item=v key=i}}
<tr bgcolor="white">
<td>{{$v.no+1}}</td><td>{{$v.login_time}}</td><td>{{$v.ip}}</td>
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="3" style="text-align:center;color:blue;">�d�L���</td>
</tr>
{{/foreach}}
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
