{{* $Id:$ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > XML��X�@�~</td>
			</tr>
			<tr>
				<td class=title_sbody2>��X���</td>
				<td>
					{{ foreach from=$select_year key=k item=year }}
					<input type="radio" name="output_selected" value="{{ $k }}" {{ if $k==$selected_year }} checked{{/if}}>{{ $year }}
					{{ /foreach }}
				</td>
			</tr>

			<tr>
	    	<td width="100%" align="center" colspan="2">
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<BR>�����X�ɮ׫e�A�Х��T�{�t�Τw�w�� 1.�ǥͼ��g(reward) 2.�ǥͨ������O�P�ݩ�(stud_subkind) �ҲաI<BR><BR>
				<!--
				{{$career_checkbox}} 
				<input type="checkbox" name="all_reward" value='1' {{$all_reward_checked}}>����X�D���Ǵ������g����
				 -->
				<input type=submit name="output_xml" value =" �����ɮ� "></td>
			</tr>
		</table><br></td>
	</tr>

	<TR>
		<TD></TD>
	</TR>
	</form>
</table>

{{ if $output==0 }}
{{include file="$SFS_TEMPLATE/footer.tpl"}}
{{ /if }}