{{* $Id: every_year_setup_course_setup_import_mapping_course.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap style="text-align:left;">《對應課程資料》<br><font color="red">（請先點選「匯入課程資料」再點選「系統課程資料」）</font><br>
{{foreach from=$class_year item=d key=i}}{{if $snum.$i>0}}<input type="radio" name="c_year" value="{{$i}}" {{if $smarty.post.c_year==$i}}checked{{/if}} OnClick="this.form.submit();">{{$d}}級<font color="red">({{$snum.$i}}節)</font> {{/if}}{{/foreach}}<br>
<br>
<table><tr class="title_sbody1">
<td align="left" valign="top">
<fieldset>
<legend>匯入課程資料</legend>
{{assign var=k value=0}}
{{foreach from=$s_data item=d key=i name=s_data}}
{{if $unmappings.os_id.$i && $k==0}}
{{assign var=chk value=1}}
{{assign var=k value=1}}
{{else}}
{{assign var=chk value=0}}
{{/if}}
<input type="radio" name="in_sel" value="{{$i}}" {{if $chk==1}}checked{{/if}} {{if $unmappings.os_id.$i==""}}disabled{{/if}}><font color="{{if $unmappings.os_id.$i}}#003366{{else}}#66CCFF{{/if}}">{{$d}}({{$i}})</font><font color="red">(共{{if $so_data.$i==""}}0{{else}}{{$so_data.$i}}{{/if}}節)</font><input type="image" name="clean_os_id[{{$i}}]" src="images/del.png" alt="刪除{{$d}}的對應"><br>
{{/foreach}}
</fieldset>
</td>
<td align="left" valign="top">
<fieldset>
<legend>系統課程資料</legend>
{{foreach from=$ss_data item=d key=i}}
{{assign var=m value=$d.scope_id}}
{{assign var=n value=$d.subject_id}}
<input type="radio" name="map_sel" value="{{$i}}" OnClick="this.form.submit();">{{if $d.class_id==""}}<font color="blue">[{{$d.class_year}}年級全年級課程]</font>{{else}}<font color="red">[{{$d.class_year}}年級{{$d.class_id|@substr:-2:2}}班課程]</font>{{/if}} {{$sb_data.$m}}{{if $n}}-{{$sb_data.$n}}{{/if}}<font color="red">({{if $sm_data.$i==""}}0{{else}}{{$sm_data.$i}}{{/if}}節)</font><input type="image" name="clean_ss_id[{{$i}}]" src="images/del.png" alt="刪除{{$d}}的對應"><br>
{{/foreach}}
</fieldset>
</td>
<td align="left" valign="top">
<fieldset>
<legend>功能選項</legend>
<input type="submit" name="clean" value="清除所有對應資料"><br>
<input type="submit" name="status" value="回到對應狀態"><br>
</fieldset>
<fieldset>
<legend>資料{{if $unmappings>0 || $mappings==0}}未{{else}}已{{/if}}完成對應</legend>
<font color="red">尚未完全對應課程：{{$unmappings.subject}}科</font><br>
<font color="red">尚未對應節數：{{$unmappings.sector}}節</font><br>
</fieldset>
</td></tr></table>
</td></tr>
<input type="hidden" name="act" value="進行課程對應">
<input type="hidden" name="year_seme" value="{{$sel_year}}-{{$sel_seme}}">
<input type="hidden" name="import" value="1">
</table>
