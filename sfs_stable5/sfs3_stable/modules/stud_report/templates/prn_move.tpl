{{* $Id: *}}
{{strip}}
<TABLE width=100% valign="top">
<TR>
<TD>日期</TD>
<TD>類別</TD>
<TD>核准機關</TD>
<TD>核准日期</TD>
<TD {{if $smarty.post.type==1}}class="empty_right"{{/if}}>核准文號</TD>
</TR>
{{if $move_data!="查無資料"}}
{{foreach from=$move_data  item=move_data}}
<TR><TD>{{$move_data.move_date}}</TD>
<TD>{{if $move_data.c_move_kind=="畢業"}}{{if $graduate_kind=="2"}}修業{{else}}畢業{{/if}}{{else}}{{$move_data.c_move_kind}}{{/if}}</TD>
<TD>{{$move_data.move_c_unit}}</TD>
<TD>{{$move_data.move_c_date}}</TD>
<TD {{if $smarty.post.type==1}}class="empty_right"{{/if}}>{{$move_data.move_c_word}}字{{if $smarty.post.type==1}}<br>{{else}} {{/if}}第{{$move_data.move_c_num}}號
</TD></TR>
{{/foreach}}
{{/if}}
</TABLE>
{{/strip}}
