{{* $Id: score_nor_award.tpl 8915 2016-06-22 03:41:24Z qfon $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor='#FFFFFF'>
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<table width="100%">
<tr>
<td>{{$year_seme_menu}} {{$class_year_menu}}</td>
</tr>
{{if $smarty.post.year_name}}
<tr><td>
<table border="0" cellspacing="1" cellpadding="4" width="100%" bgcolor="#cccccc" class="main_body">
<tr bgcolor="#E1ECFF" align="center">
<td>�Z��</td>
<td>�y��</td>
<td>�Ǹ�</td>
<td>�m�W</td>
</tr>
{{foreach from=$show_sn item=sc key=sn}}
<tr bgcolor="#ddddff" align="center">
<td>{{$sclass[$sn]}}</td>
<td>{{$snum[$sn]}}</td>
<td>{{$stud_id[$sn]}}</td>
<td>{{$stud_name[$sn]}}</td>
</tr>
{{/foreach}}
</table>
<br>
<font color="red">���G�Х���[�έp��`���Z]���Ҳέp���Ǵ��X�ʮu����;��ǥͤ��C�J�έp</font>
</td></tr>
{{/if}}
</tr>
</table>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}