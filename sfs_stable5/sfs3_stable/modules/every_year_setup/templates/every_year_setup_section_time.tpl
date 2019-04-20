{{* $Id: every_year_setup_section_time.tpl 6688 2012-02-08 02:27:53Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<table cellspacing="0" cellpadding="0" border="0">
<tr valign="top">
<form name="menu_form" method="post" action="{{$smarty.server.PHP_SELF}}">
<td>
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
{{if $smarty.post.act==""}}
<tr bgcolor="#FFFFFF"><td>
<table>
<tr>
<td>請選擇欲設定的學期：{{$year_seme_menu}}</td>
</tr>
<tr><td>
<input type="submit" name="act" value="開始設定" class="b1"> <input type="submit" name="act" value="觀看設定" class="b1">
</td></tr>
</table>
</td></tr>
{{else}}
<tr bgcolor="#E1ECFF" class="small" align="center">
<td colspan="2"><font color="#607387"><font color="black">{{$sel_year}}</font>學年度<font color="black">{{$sel_seme}}學期各節上課時間表</font>
{{if !$year_seme_data}}<br><br><font color='red'>本學期各節上課時間尚未設定，表列資料係前一學期資料！</font>{{/if}}
</td>
</tr>
<tr bgcolor="#E1ECFF" class="small" align="center">
<td>節次<td>起迄時間</td>
</tr>
{{foreach from=$section_table item=v key=i}}
<tr bgcolor="white" class="small" align="center">
<td>{{$i}}<td bgcolor="{{$bg.$i}}">{{if $smarty.post.act=="開始設定"}}<input type="text" size="5" name="st[{{$i}}][0]" value="{{$section_table.$i.0}}"> - <input type="text" size="5" name="st[{{$i}}][1]" value="{{$section_table.$i.1}}">{{else}}{{$section_table.$i.0}} - {{$section_table.$i.1}}{{/if}}</td>
</tr>
{{/foreach}}
{{/if}}
</table>
{{if $smarty.post.act=="開始設定"}}
<td><input type="submit" name="save" value="儲存設定">
{{elseif $smarty.post.act=="觀看設定" || $smarty.post.act=="show"}}
<td><input type="submit" name="act" value="開始設定">
{{/if}}
</td>
</form>
</tr></table>
<br>
<table>
<tr bgcolor="#FBFBC4"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td></tr>
<tr><td style="line-height: 150%;">
<ol>
{{if $smarty.post.act==""}}
<li class="small">請選擇一個學年、學期以做設定。</li>
<li class="small"><span class="like_button">開始設定</span>會開始進行該學期各節上課時間的設定。</li>
<li class="small"><span class="like_button">觀看設定</span>會列出該學期各節上課時間的設定。
{{else}}
<li class="small">時間的格式為hh:mm。例：08:00。</li>
<li class="small">顯示紅色底色的區域表示時間設定上有問題。例如下課時間比上課時間早，或是後面節次的時間比前面節次的時間早，請務必更正。</li>
{{/if}}
</li>
</ol>
</td></tr>
</table>
{{include file="$SFS_TEMPLATE/footer.tpl"}}