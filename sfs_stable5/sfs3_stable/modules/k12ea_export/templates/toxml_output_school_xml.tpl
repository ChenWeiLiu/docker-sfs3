{{* $Id:$ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > XML輸出作業</td>
			</tr>
			<tr>
				<td class=title_sbody2>輸出資料</td>
				<td>
					{{ foreach from=$select_year key=k item=year }}
					<input type="radio" name="output_selected" value="{{ $k }}" {{ if $k==$selected_year }} checked{{/if}}>{{ $year }}
					{{ /foreach }}
				</td>
			</tr>

			<tr>
	    	<td width="100%" align="center" colspan="2">
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<BR>執行輸出檔案前，請先確認系統已安裝 1.學生獎懲(reward) 2.學生身分類別與屬性(stud_subkind) 模組！<BR><BR>
				<!--
				{{$career_checkbox}} 
				<input type="checkbox" name="all_reward" value='1' {{$all_reward_checked}}>不輸出非本學期的獎懲明細
				 -->
				<input type=submit name="output_xml" value =" 產生檔案 "></td>
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