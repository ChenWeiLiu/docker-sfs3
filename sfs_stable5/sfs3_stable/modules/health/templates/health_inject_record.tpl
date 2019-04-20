{{* $Id: health_inject_record.tpl 5689 2009-10-19 04:33:57Z brucelyc $ *}}
{{if $smarty.post.class_name}}<input type="submit" name="print" value="友善列印">{{/if}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="3">班級</td>
<td rowspan="3">座號</td>
<td rowspan="3">姓名</td>
<td colspan="2" rowspan="2">預防<br>接種<br>卡影<br>本　</td>
<td colspan="18">卡上記錄</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="1">卡<br>介<br>苗</td>
<td colspan="3">B型肝<br>炎疫苗</td>
<td colspan="4">小兒麻痺<br>口服疫苗</td>
<td colspan="4">白喉破傷<br>風百日咳<br>混合疫苗</td>
<td colspan="1">麻疹<br>疫苗</td>
<td colspan="3">日本腦<br>炎疹苗</td>
<td colspan="1" nowrap>MMR<br>疫苗</td>
<td colspan="1" nowrap>水痘<br>疫苗</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>已<br>　<br>交</td>
<td nowrap>未<br>　<br>交</td>
<td nowrap>一<br>　<br>劑</td>
<td nowrap>第<br>一<br>劑</td>
<td nowrap>第<br>二<br>劑</td>
<td nowrap>第<br>三<br>劑</td>
<td nowrap>第<br>一<br>劑</td>
<td nowrap>第<br>二<br>劑</td>
<td nowrap>第<br>三<br>劑</td>
<td nowrap>第<br>四<br>劑</td>
<td nowrap>第<br>一<br>劑</td>
<td nowrap>第<br>二<br>劑</td>
<td nowrap>第<br>三<br>劑</td>
<td nowrap>第<br>四<br>劑</td>
<td nowrap>一<br>　<br>劑</td>
<td nowrap>第<br>一<br>劑</td>
<td nowrap>第<br>二<br>劑</td>
<td nowrap>第<br>三<br>劑</td>
<td nowrap>一<br>　<br>劑</td>
<td nowrap>一<br>　<br>劑</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.inject}}
<tr style="background-color:{{cycle values="white,#f4feff"}};">
<td>{{$class_name}}</td>
<td>{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>{{if $dd.0.0.times>0}}v{{/if}}</td>
<td>{{if $dd.0.0.times<1}}v{{/if}}</td>
<td>{{if $dd.0.1.times>0}}v{{/if}}</td>
<td>{{if $dd.0.2.times>0}}v{{/if}}</td>
<td>{{if $dd.0.2.times>1}}v{{/if}}</td>
<td>{{if $dd.0.2.times>2}}v{{/if}}</td>
<td>{{if $dd.0.3.times>0}}v{{/if}}</td>
<td>{{if $dd.0.3.times>1}}v{{/if}}</td>
<td>{{if $dd.0.3.times>2}}v{{/if}}</td>
<td>{{if $dd.0.3.times>3}}v{{/if}}</td>
<td>{{if $dd.0.4.times>0}}v{{/if}}</td>
<td>{{if $dd.0.4.times>1}}v{{/if}}</td>
<td>{{if $dd.0.4.times>2}}v{{/if}}</td>
<td>{{if $dd.0.4.times>3}}v{{/if}}</td>
<td>{{if $dd.0.6.times>0}}v{{/if}}</td>
<td>{{if $dd.0.5.times>0}}v{{/if}}</td>
<td>{{if $dd.0.5.times>1}}v{{/if}}</td>
<td>{{if $dd.0.5.times>2}}v{{/if}}</td>
<td>{{if $dd.0.7.times>0}}v{{/if}}</td>
<td>{{if $dd.0.8.times>0}}v{{/if}}</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td></tr></table>
