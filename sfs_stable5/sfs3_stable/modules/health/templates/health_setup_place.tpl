{{* $Id: health_setup_place.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

{{assign var=id value=$smarty.post.third_menu_id}}
{{assign var=cname value=$third_menu_arr.$id}}
<input type="submit" name="act" value="新增{{$cname}}">
<table bgcolor="#7e9cbd" cellspacing="1" cellpadding="4" class="small">
<tr style="background-color:#9ebcdd;color:white;text-align:center;">
<td>{{$cname}}</td><td>功能選項</td>
</tr>
{{foreach from=$item_arr item=d key=i}}
<tr bgcolor="white">
{{if $smarty.post.edit_item_id==$i}}
<td><input type="text" name="item_name" value="{{$d}}"></td><td><input type="button" name="sure" value="確定修改" OnClick="this.form.item_id.value='{{$i}}';this.form.submit();"> <input type="reset" value="回復"> <input type="submit" value="放棄"></td>
{{else}}
<td>{{$d}}</td><td style="text-align:center;"><input type="image" src="images/edit.gif" name="edit_item_id" value="{{$i}}" alt="編修這筆資料"><input type="image" src="images/delete.gif" name="del_item_id" value="{{$i}}" alt="刪除這筆資料" OnClick="this.form.submit();"></td>
{{/if}}
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="2" style="color:red;text-align:center;">尚未設定任何{{$cname}}</td>
</tr>
{{/foreach}}
{{assign var=chkname value=新增$cname}}
{{if $smarty.post.act==$chkname}}
<tr style="background-color:yellow;">
<td><input type="text" name="new_item"></td><td><input type="submit" name="act" value="確定新增"></td>
</tr>
{{/if}}
</table>
<input type="submit" name="act" value="新增{{$cname}}">
<input type="hidden" name="item_id" value="">

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>如果同時有記錄要新增及刪除，請勿直接把要刪除的資料項目改成要新增的資料項目，否則將造成資料記錄錯亂。</li>
	<li>如果有常使用的資料項目，請直接新增為獨立項目，勿以「其他」記錄，否則將無法進行較精確的資料分析及統計。</li>
	</ol>
</td></tr>
</table>
