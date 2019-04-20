{{* $Id: health_analyze_wh_class.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td rowspan="2">性別</td>
<td rowspan="2">身高</td>
<td rowspan="2">體重</td>
<td rowspan="2">BMI</td>
<td colspan="4">體重狀況</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>過輕</td>
<td>適中</td>
<td>過重</td>
<td>肥胖</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
{{assign var=sex value=$health_data->stud_base.$sn.stud_sex}}
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#f4feff;text-align:center;">{{if $health_data->stud_base.$sn.stud_sex==1}}男{{elseif $health_data->stud_base.$sn.stud_sex==2}}女{{else}}--{{/if}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$dd.weight}}</td>
<td>{{$dd.BMI}}</td>
{{assign var=Bid value=$dd.Bid}}
{{php}}
$this->_tpl_vars['v'][$this->_tpl_vars['sex']][$this->_tpl_vars['dd']['Bid']]+=1;
$this->_tpl_vars['v'][$this->_tpl_vars['sex']][9]+=1;
$this->_tpl_vars['v'][9][$this->_tpl_vars['dd']['Bid']]+=1;
{{/php}}
<td style="text-align:center;">{{if $Bid==0 && $dd.height!=0 && $dd.weight!=0}}＊{{/if}}</td>
<td style="text-align:center;">{{if $Bid==1}}＊{{/if}}</td>
<td style="text-align:center;">{{if $Bid==2}}＊{{/if}}</td>
<td style="text-align:center;">{{if $Bid==3}}＊{{/if}}</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="5">合計</td>
</tr>
{{php}}$this->_tpl_vars['v'][9][9]=$this->_tpl_vars['v'][1][9]+$this->_tpl_vars['v'][2][9];{{/php}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="2">體重狀況</td>
<td>男生</td>
<td>女生</td>
<td>合計</td>
</tr>
{{foreach from=$Bid_arr item=dd key=i}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;" rowspan="2">{{$dd}}</td>
<td style="background-color:#f4feff;">人數</td>
<td>{{$v.1.$i}}</td>
<td>{{$v.2.$i}}</td>
<td>{{php}}$this->_tpl_vars['v'][3][$this->_tpl_vars['i']]=$this->_tpl_vars['v'][1][$this->_tpl_vars['i']]+$this->_tpl_vars['v'][2][$this->_tpl_vars['i']];{{/php}}{{$v.3.$i}}</td>
</tr>
<tr style="background-color:white;">
<td style="background-color:#f4feff;">比例</td>
<td style="text-align:right;">{{$v.1.$i/$v.1.9*100|string_format:"%.1f"}}%</td>
<td style="text-align:right;">{{$v.2.$i/$v.2.9*100|string_format:"%.1f"}}%</td>
<td style="text-align:right;">{{$v.9.$i/$v.9.9*100|string_format:"%.1f"}}%</td>
</tr>
{{/foreach}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;" rowspan="2">合計</td>
<td style="background-color:#f4feff;">人數</td>
<td>{{$v.1.9}}</td>
<td>{{$v.2.9}}</td>
<td>{{$v.9.9}}</td>
</tr>
<tr style="background-color:white;">
<td style="background-color:#f4feff;">比例</td>
<td style="text-align:right;">{{$v.1.9/$v.9.9*100|string_format:"%.1f"}}%</td>
<td style="text-align:right;">{{$v.2.9/$v.9.9*100|string_format:"%.1f"}}%</td>
<td style="text-align:right;">100%</td>
</tr>
</table>
</td></tr></table>