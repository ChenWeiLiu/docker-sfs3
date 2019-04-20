{{* $Id: every_year_setup_setup_schoolday.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
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
<tr><td>請選擇欲設定的學年度：</td><td>民國 <input type="text" name="sel_year" size="3" value="{{$sel_year}}"> 學年度，<select name="sel_seme"><option value="1" {{if $sel_seme==1}}selected{{/if}}>上</option><option value="2" {{if $sel_seme==2}}selected{{/if}}>下</option></select>學期</td></tr>
<tr><td colspan="2"><input type="submit" name="act" value="設定" class="b1"></td></tr>
</table>
</td></tr>
</table>
<br>
<table>
	<tr bgcolor="#FBFBC4"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td></tr>
	<tr><td style="line-height:150%;">
	<ol>
	<li class="small">
	請選擇一個學年、學期來設定該學期的開始日期、結束日期、開學日及結業日。</li><li class="small">
	開學日及結業日的設定主要與學期週次及全勤加分的計算有關。
	</li>
	</ol>
</td></tr>
</table>
</td></tr>
{{else}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="2">
	<tr bgcolor="#FFFFFF"><td style="vertical-align:top;">
		<table cellspacing="0" cellpadding="2">
			<tr bgcolor="#FadFeF"><td>欲設定的學年度：</td><td>{{$sel_year}} 學年度 第 {{$sel_seme}} 學期</td></tr>
			<tr><td>請填入「學期開始日期」：</td><td><input type="text" name="data[start]" value="{{$data.start}}" size="10">（格式：{{$now}}）</td></tr>
			<tr><td>請填入「學期結束日期」：</td><td><input type="text" name="data[end]" value="{{$data.end}}" size="10"></td></tr>
			<tr><td>請填入「開學日」：</td><td><input type="text" name="data[st_start]" value="{{$data.st_start}}" size="10"></td></tr>
			<tr><td>請填入「結業日」：</td><td><input type="text" name="data[st_end]" value="{{$data.st_end}}" size="10"></td></tr>
			<tr><td colspan="2">
			<input type="hidden" name="sel_year" value="{{$sel_year}}">
			<input type="hidden" name="sel_seme" value="{{$sel_seme}}">
			<input type="hidden" name="act" value="edit">
			<input type="submit" value="儲存" class="b1" OnClick="this.form.act.value='insert';">
			</td></tr>
			<tr bgcolor="#FadFeF"><td colspan="2">&nbsp;</td></tr>
		</table>
	</td><td style="vertical-align:top;">
		{{assign var=w value=$smarty.post.week_setup}}
		<table cellspacing="0" cellpadding="3" style="color:{{if $w}}black{{else}}#AAAAAA{{/if}};">
			<tr bgcolor="#FadFeF"><td colspan="3" align="center"><input type="checkbox" name="week_setup" value="1" OnChange="{{if $w}}this.form.mode.value='disable';{{/if}}this.form.submit();" {{if $w}}checked{{/if}}>啟用本學期週次設定</td></tr>
			<tr><td colspan="3" style="color:red;text-align:center;">(特殊狀況才需設定)</td></tr>
			<tr bgcolor="#FFF1B8" style="text-align:center;"><td>跳過</td><td>週次別</td><td>開始日期</td></tr>
			{{assign var=j value=0}}
			{{foreach from=$week_data item=wd key=i}}
			{{if !$smarty.post.week_enable.$i}}{{assign var=j value=$j+1}}{{/if}}
			<tr style="text-align:center;{{if $smarty.post.week_enable.$i==1}}color:#AAAAAA;{{/if}}"><td><input type="checkbox" name="week_enable[{{$i}}]" value="1" OnChange="this.form.mode.value='edit';this.form.submit();" {{if $smarty.post.week_enable.$i==1}}checked{{/if}} {{if !$w}}disabled=true{{/if}}></td><td>{{if $smarty.post.week_enable.$i==1}}跳過{{else}}第 {{$j}} 週{{/if}}</td><td>{{$wd}}</td></tr>
			{{/foreach}}
			<tr><td colspan="3">
			<input type="hidden" name="mode" value="{{$smarty.post.mode}}">
			<input type="submit" value="儲存" class="b1" {{if !$w}}disabled=true{{/if}} OnClick="this.form.act.value='insert';">
		</td></tr>
		</table>
	</td></tr>
</table>
{{/if}}
</td></tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
