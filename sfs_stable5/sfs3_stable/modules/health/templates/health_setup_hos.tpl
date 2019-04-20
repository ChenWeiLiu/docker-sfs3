{{* $Id: health_setup_hos.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="act" value="新增醫院或診所">
<table bgcolor="#7e9cbd" cellspacing="1" cellpadding="4" class="small">
<tr style="background-color:#9ebcdd;color:white;text-align:center;">
<td>醫院或診所名稱</td><td>功能選項</td>
</tr>
{{foreach from=$hos_arr item=d key=i}}
<tr bgcolor="white">
{{if $smarty.post.edit_hos_id==$i}}
<td><input type="text" name="hos_name" value="{{$d}}"></td><td><input type="button" name="sure" value="確定修改" OnClick="this.form.hos_id.value='{{$i}}';this.form.submit();"> <input type="reset" value="回復"> <input type="submit" value="放棄"></td>
{{else}}
<td>{{$d}}</td><td style="text-align:center;"><input type="image" src="images/edit.gif" name="edit_hos_id" value="{{$i}}" alt="編修這筆資料"><input type="image" src="images/delete.gif" name="del_hos_id" value="{{$i}}" alt="刪除這筆資料" OnClick="this.form.submit();"></td>
{{/if}}
</tr>
{{foreachelse}}
<tr bgcolor="white">
<td colspan="2" style="color:red;text-align:center;">尚未設定任何醫院或診所</td>
</tr>
{{/foreach}}
{{if $smarty.post.act=="新增醫院或診所"}}
<tr style="background-color:yellow;">
<td><input type="text" name="new_hos"></td><td><input type="submit" name="act" value="確定新增"></td>
</tr>
{{/if}}
</table>
<input type="submit" name="act" value="新增醫院或診所">
<input type="hidden" name="hos_id" value="">

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>如果同時有記錄新增及刪除，請勿直接把要刪除的資料改成要新增的資料，<br>否則將造成資料錯亂。</li>
	</ol>
</td></tr>
</table>
