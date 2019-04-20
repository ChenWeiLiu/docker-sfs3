{{* $Id: score_nor_chk.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
function go(a,j,k) {
	if (a=='del')
		OK=confirm('確定刪除本項目?');
	else
		OK=true;
	if (OK) {
		b=document.base_form;
		b.act.value=a;
		b.main.value=j;
		b.sub.value=k;
		b.submit();
	}
}

</script>

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<tr><td bgcolor="#FFFFFF">
<table cellspacing="0" cellpadding="0">
<form name ="base_form" action="{{$smarty.server.PHP_SELF}}" method="post" >
<tr><td>
{{$year_seme_sel}} {{$item_sel}} {{if $current_records and ! $current}}<input type="submit" name="copy_to_cur" value="複製至本學期">{{/if}}
<input type="submit" name="default" value="使用所選之標準項目" {{if $rowdata}}disabled{{/if}}>
<input type="submit" name="del_all" value="刪除本學期設定" OnClick="return confirm('確定刪除所有設定?');"> 
<input type="submit" name="del_record" value="刪除本學期所有記錄" OnClick="return confirm('確定刪除所有記錄（所有老師將必須重新輸入）?');"><br>
{{$msg_str}}<br><br>
<table cellspacing="0" cellpadding="0"><tr><td>
<table cellspacing="1" cellpadding="4" bgcolor="#9EBCDD">
<tr class="title_sbody2">
<td align="center" bgcolor="#AECCED">項目 <input type="image" src="images/add.png" alt="加入主項目" OnClick="go('add_main','{{$maxnum}}',0);">
{{if $smarty.post.act=="add_main"}}<br><input type="text" name="item_value">{{/if}}
</td>
<td align="center" bgcolor="#AECCED">功能選項
{{if $smarty.post.act=="add_main"}}<br><input type="button" value="儲存" OnClick="go('insert','{{$maxnum}}',0);"> <input type="submit" value="取消">{{/if}}
</td>
</tr>
{{foreach from=$rowdata item=v key=i}}
{{assign var=j value=$rowdata.$i.main}}
{{assign var=k value=$rowdata.$i.sub}}
{{if $smarty.post.act=="edit" && $smarty.post.main==$j && $smarty.post.sub==$k}}
<tr class="title_sbody{{if $k==0}}2{{else}}1{{/if}}">
<td align="left">{{if $k!=0}}{{$k}}.{{/if}}<input type="text" name="item_value" value="{{$rowdata.$i.item}}" size="35"></td>
<td align="left"><input type="button" value="儲存" OnClick="go('save',{{$j}},{{$k}});this.form.submit();"> <input type="submit" value="取消"></td>
</tr>
{{else}}
<tr class="title_sbody{{if $k==0}}2{{else}}1{{/if}}">
<td align="{{if $k==0}}center{{else}}left{{/if}}">{{if $k==0}}<font color="blue">{{else}}{{$k}}.{{/if}}{{$rowdata.$i.item}}{{if $k==0}}</font>{{/if}}</td>
<td align="left">
{{if $k==1 || ($j==0 && $k==0)}}<font color="#AAAAAA">{{else}}<span OnClick="go('up',{{$j}},{{$k}});">{{/if}}▲{{if $k==1 || ($j==0 && $k==0)}}</font>{{else}}</span>{{/if}}
{{if ($rownum.$j.num-1)==$k || ($j==($maxnum-1) && $k==0)}}<font color="#AAAAAA">{{else}}<span OnClick="go('down',{{$j}},{{$k}});">{{/if}}▼{{if ($rownum.$j.num-1)==$k || ($j==($maxnum-1) && $k==0)}}</font>{{else}}</span>{{/if}}
<input type="image" src="images/edit2.png" alt="編輯本項目" OnClick="go('edit',{{$j}},{{$k}});">
<input type="image" src="images/del.png" alt="刪除本項目" OnClick="go('del','{{$j}}','{{$k}}');">
{{if $k==0}}<input type="image" src="images/add.png" alt="加入子項目" OnClick="go('add',{{$j}},0);">{{/if}}
</td>
</tr>
{{if $smarty.post.act=="add" && $smarty.post.main==$j && $k==$submax.$j.num-1}}<tr class="title_sbody1"><td align="left">{{$submax.$j.num}}.<input type="text" name="item_value"></td><td align="center"><input type="button" value="儲存" OnClick="go('insert',{{$j}},{{$k+1}});"> <input type="submit" value="取消"></td>{{/if}}
{{/if}}
{{foreachelse}}
<tr class="title_sbody1"><td colspan="2" style="text-align:center;color:red;">本學期尚未設定</td></tr>
{{/foreach}}
</table><td></tr></table>
</td></tr>
</table></td></tr>
<input type="hidden" name="act" value="">
<input type="hidden" name="main" value="">
<input type="hidden" name="sub" value="">
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
