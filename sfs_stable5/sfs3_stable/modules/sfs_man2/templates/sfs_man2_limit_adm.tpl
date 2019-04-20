{{* $Id: sfs_man2_limit_adm.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table cellspacing="1" cellpadding="3" bgcolor="#C6D7F2">
<form action="{{$smarty.server.SCRIPT_NAME}}" method="post">
<tr bgcolor="#FFFFFF">
<td>{{$kind_menu}}</td>
</tr></form></table>
{{if $rowdata}}
<table cellspacing="0" cellpadding="1" bgcolor="#0000FF">
<form name="data_form" action="{{$smarty.server.SCRIPT_NAME}}" method="post">
<tr><td>
<table width='100%' cellspacing='2' cellpadding='2' class='small' style="background-color:white;">
<tr><td>授權對象</td><td>模組名稱(權限)
{{foreach from=$rowdata item=d}}
{{assign var=id value=$d.id_sn}}
{{if $id!=$oid}}</td></tr>{{assign var=i value=0}}
<tr><td>
{{if $t_arr}}{{$t_arr[$d.id_sn]}}
{{else}}{{$d.id_sn}}{{/if}}
</td><td>{{/if}}
{{assign var=i value=$i+1}}
<a href="#" OnClick="document.data_form.del.value='{{$d.p_id}}';document.data_form.submit();"><img src="images/del.png" border="0"></a> {{$d.showname}} (<span style="color:{{if $d.is_admin==1}}red{{else}}blue{{/if}};">{{$l_arr[$d.is_admin]}}</span>), &nbsp; 
{{if $i % 5 == 0}}<br>{{/if}}
{{assign var=oid value=$d.id_sn}}
{{/foreach}}
</td></tr></table></td></tr>
<input type="hidden" name="kind" value="{{$smarty.post.kind}}">
<input type="hidden" name="del" value="">
</form></table>
{{/if}}
{{include file="$SFS_TEMPLATE/footer.tpl"}}
