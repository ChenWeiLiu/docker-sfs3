{{* $Id: health_setup_wh.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table border="0">
<tr><td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<form name="v" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">身高體重輸入方式</td>
</tr>
{{assign var=c value=$smarty.post.chk-1}}
{{assign var=d value=$smarty.post.dot-1}}
{{assign var=s value=$smarty.post.slope-1}}
{{assign var=r value=$smarty.post.color-1}}
<tr style="background-color:white;text-align:center;">
<td><select name="wh_input"><option value="">先身高後體重</option><option value="1" {{if $smarty.post.wh_input=="1"}}selected{{/if}}>先體重後身高</option></select></td>
</tr>
<input type="hidden" name="chk" value="{{$smarty.post.chk}}">
</table>
<input type="submit" name="sure" value="確定儲存">
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">為配合全自動身高體重測量儀送出數據方式，可由此設定改變身高體重輸入欄位排列順序。</li>
</ol>
</td></tr>
</form>
</table>
</td></tr></table>
