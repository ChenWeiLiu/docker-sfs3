{{* $Id: health_analyze_sight_class2.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<fieldset class="small" style="width:30%;">
<legend style="color:blue;font-size:12pt;">視力狀況</legend>
<select name=status_id>
{{html_options options=$sight_chk_status selected=$smarty.post.status_id}}
</select>
</fieldset>
<fieldset class="small" style="width:30%;">
<legend style="color:blue;font-size:12pt;">篩選視力</legend>
裸視
<select name="o_value">
{{html_options options=$sight_value selected=$smarty.post.o_value}}
</select>　
矯正
<select name="r_value">
{{html_options options=$sight_value selected=$smarty.post.r_value}}
</select>
</fieldset>
<input type="hidden" name="sel" id="sel" value="{{$smarty.post.sel}}">
<input type="button" value="開始篩選" OnClick="document.getElementById('sel').value='1';this.form.submit();">