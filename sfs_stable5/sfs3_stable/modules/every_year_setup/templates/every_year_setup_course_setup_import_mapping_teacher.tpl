{{* $Id: every_year_setup_course_setup_import_mapping_teacher.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap style="text-align:left;">《對應教師資料》<br><font color="red">（請先點選「匯入教師資料」再點選「現有教師資料」）</font><br>
<input type="checkbox" name="hide" {{if $smarty.post.hide}}checked{{/if}} OnChange="this.form.submit();">隱藏已對應的「匯入教師資料」<br><br>
<table><tr class="title_sbody1">
<td align="left" valign="top">
<fieldset>
<legend>匯入教師資料</legend>
{{foreach from=$t_data item=d key=i}}
<input type="radio" name="in_sel" value="{{$d.ot_id}}" {{if $i==0}}checked{{/if}}><font color="#336699">{{$d.ot_name}}({{$d.ot_id}})</font> =&gt; ({{if $d.teacher_sn>0}}<font color="{{if $d.sex==1}}blue{{else}}red{{/if}}">{{$d.teacher_name}}</font>{{else}}<font color="hotpink">未對應</font>{{/if}}) <input type="image" name="clean_one[{{$d.ot_id}}]" src="images/del.png" alt="刪除{{$d.name}}的對應"><br>
{{/foreach}}
</fieldset>
</td>
<td align="left" valign="top">
<fieldset>
<legend>現有教師資料</legend>
{{foreach from=$tb_data item=d key=i}}
{{assign var=k value=$d.teach_title_id}}
<input type="radio" name="map_sel" value="{{$d.teacher_sn}}" OnClick="this.form.submit();"><font color="{{if $d.sex==1}}blue{{else}}red{{/if}}">{{$d.name}}</font>({{if $d.class_num!=""}}{{$d.class_num}}{{/if}}{{$tt_data[$k]}})<br>
{{/foreach}}
</fieldset>
</td>
<td align="left" valign="top">
<fieldset>
<legend>功能選項</legend>
<input type="submit" name="auto" value="自動依姓名對應"><br>
<input type="submit" name="clean_teacher" value="清除所有對應資料"><br>
<input type="submit" name="status" value="回到對應狀態"><br>
</fieldset>
<fieldset>
<legend>資料{{if $unmappings>0 || $mappings==0}}未{{else}}已{{/if}}完成對應</legend>
<font color="red">尚未對應教師數：{{$unmappings}}</font><br>
</fieldset>
</td></tr></table>
</td></tr>
<input type="hidden" name="act" value="進行教師對應">
<input type="hidden" name="year_seme" value="{{$sel_year}}-{{$sel_seme}}">
<input type="hidden" name="import" value="1">
</table>
