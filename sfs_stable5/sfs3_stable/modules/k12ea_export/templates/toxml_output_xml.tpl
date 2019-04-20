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
				<td class=title_sbody2>異動類別</td>
				<td>{{$move_kind_sel}}</td>	      
			</tr>
			{{if $smarty.post.move_kind}}
				{{if $year_seme_sel!=""}}
				<tr>
					<td class=title_sbody2>異動學期</td>
					<td>{{$year_seme_sel}}</td>	      
				</tr>
				{{/if}}
			<tr>
	    	<td width="100%" align="center" colspan="2">
	    	<input type="hidden" name="update_id" value="{{$smarty.session.session_log_id}}">
				<BR>執行輸出檔案前，請先確認系統已安裝 1.學生獎懲(reward) 2.學生身分類別與屬性(stud_subkind) 模組！<BR><BR>
				{{$career_checkbox}} 
				<input type="checkbox" name="all_reward" value='1'>不輸出非本學期的獎懲明細 <input type=submit name="output_xml" value =" 輸出檔案 "></td>
			</tr>
		</table><br></td>
	</tr>
	<TR>
		<TD>
			<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body><tr><td colspan="8" class=title_top1 align=center>本學期<Script>document.write(document.base_form.move_kind.options[document.base_form.move_kind.selectedIndex].text+'記錄')</Script></td></tr>
				<TR class=title_mbody >				
					{{if $form_kind=="1"}}
						<TD>選擇</TD>
						<TD>學號</TD>
						<TD>姓名</TD>
						<TD>異動時間</TD>
						<TD>新就讀學校</TD>
					{{else}}
						<TD>選擇</TD>
						<TD>學年度</TD>				
						<TD>新就讀學校</TD>
						<TD>筆數</TD>
					{{/if}}
				</TR>
				{{section loop=$stud_move name=arr_key}}
					<TR class=nom_2>		
					{{if $form_kind=="1"}}
						<TD><input type="checkbox" name="choice[{{$stud_move[arr_key].student_sn}}]"></TD>
						<TD>{{$stud_move[arr_key].stud_id}}</TD>
						<TD>{{$stud_move[arr_key].stud_name}}</TD>					
						<TD>{{$stud_move[arr_key].move_date}}</TD>
						<TD>{{$stud_move[arr_key].school}}　</TD>
					{{else}}
						<TD><input type="radio" name="choice[]" value="{{$stud_move[arr_key].move_year_seme}}_{{$stud_move[arr_key].move_c_unit}}_{{$stud_move[arr_key].move_c_date}}_{{$stud_move[arr_key].move_c_num}}" {{if $smarty.post.move_kind==13}}OnClick="ss={{$stud_move[arr_key].move_year}}"{{/if}}></TD>
						<TD>{{$stud_move[arr_key].move_year}}</TD>					
						<TD>{{$stud_move[arr_key].move_date}}</TD>
						<TD>{{if $stud_move[arr_key].move_c_unit}}{{$stud_move[arr_key].move_c_unit}}{{else}}<font color="red">尚未輸入</font>{{/if}}</TD>
						<TD>{{$stud_move[arr_key].move_c_date}}</TD>
						<TD>{{$stud_move[arr_key].move_c_word}}字第{{$stud_move[arr_key].move_c_num}}號</TD>
						<TD>{{$stud_move[arr_key].num}}</TD>
					{{/if}}
					</TR>
				{{/section}}
			{{/if}}
			</table></TD>
	</TR>
	<TR>
		<TD></TD>
	</TR>
	</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}