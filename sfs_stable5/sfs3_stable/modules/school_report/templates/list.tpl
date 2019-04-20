{{* $Id: list.tpl 7567 2013-09-24 03:09:46Z hami $ *}}
{{popup_init src="$SFS_PATH_HTML/javascripts/overlib/overlib.js"}}
{{assign var=room_arr value=$this->getRoomArr()}}
{{assign var=open_date value=''}}
{{foreach from=$this->get_all($year_seme,$week_num) item=arr}}
{{if $open_date neq $arr.open_date}}
<tr bgcolor='#FFFFFF'>
<td colspan=5><span style="background-color:#dfe">{{$arr.open_date}}</span> &nbsp;<a href='{{$smarty.server.PHP_SELF}}?act=print&amp;year_seme={{$arr.year_seme}}&amp;open_date={{$arr.open_date}}' target="_blank">列印匯整表</a>
&nbsp;<a href='{{$smarty.server.PHP_SELF}}?act=big_print&amp;year_seme={{$arr.year_seme}}&amp;open_date={{$arr.open_date}}' target="_blank">顯示大字版</a>

</td>
</tr>
<tr align=center  style='font-size:11pt' bgcolor='#E5E5E5'>
<td>日期</td>
<td>處室</td>
<td>主旨</td>
<td>報告人</td>
<td>編修</td>
</tr>
{{assign var=open_date value=$arr.open_date}}
{{/if}}
<tr align=center  style='background-color:#ffe; font-size:14px'>
{{assign var=weekno value=$arr.open_date|date_format:"%a"}}
<td>{{$arr.open_date}} (<span style='color:blue'>{{'utf-8'|iconv:'big5':$weekno}}</span>) </td>
<td>{{$room_arr[$arr.room_id]}} </td>
<td>{{$arr.title}}</td>
<td>{{$arr.name}} </td>
<td>
{{if $smarty.session.session_tea_sn eq $arr.teacher_sn}}
<a href="{{$smarty.server.PHP_SELF}}?edit={{$arr.id}}&page={{$this->page}}">修改</a>
<a href="{{$smarty.server.PHP_SELF}}?form_act=del&id={{$arr.id}}&page={{$this->page}}" onclick="return window.confirm('真的刪除嗎？');">刪除</a>
{{else}}
--
{{/if}}
</td></tr>
{{/foreach}}

