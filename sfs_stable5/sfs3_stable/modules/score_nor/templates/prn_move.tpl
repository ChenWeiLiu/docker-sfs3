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
{{foreach from=$move_data  item=move_data}}
<TR><TD>{{$move_data.move_date}}</TD>
{{assign var=mid value=$move_data.move_kind}}
<TD>{{$move_kind.$mid}}</TD>
<TD>{{$move_data.move_c_unit}}</TD>
<TD>{{$move_data.move_c_date}}</TD>
<TD {{if $smarty.post.type==1}}class="empty_right"{{/if}}>{{$move_data.move_c_word}}字<br>{{if $smarty.post.type==1}}<br>{{else}} {{/if}}第{{$move_data.move_c_num}}號
</TD></TR>
{{/foreach}}
</TABLE>
{{/strip}}
