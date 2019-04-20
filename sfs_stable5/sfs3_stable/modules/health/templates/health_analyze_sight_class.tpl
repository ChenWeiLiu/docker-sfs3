{{* $Id: health_analyze_sight_class.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td rowspan="2">性別</td>
<td colspan="4">裸視視力</td>
<td colspan="4">矯正視力</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>右眼</td>
<td>左眼</td>
<td>正常</td>
<td>不良</td>
<td>右眼</td>
<td>左眼</td>
<td>正常</td>
<td>不良</td>
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
<td style="text-align:center;">{{$dd.r.sight_o}}</td>
<td style="text-align:center;">{{$dd.l.sight_o}}</td>
{{assign var=ok value=0}}
<td style="text-align:center;">{{if $dd.r.sight_o>0.8 && $dd.l.sight_o>0.8}}＊{{assign var=ok value=1}}{{/if}}</td>
<td style="text-align:center;">{{if $ok==0 && $dd.r.sight_o!="" && $dd.l.sight_o!=""}}<span style="color:red;">＊</span>{{/if}}</td>
<td style="text-align:center;">{{$dd.r.sight_r}}</td>
<td style="text-align:center;">{{$dd.l.sight_r}}</td>
{{assign var=ook value=0}}
<td style="text-align:center;">{{if $dd.r.sight_r>0.8 && $dd.l.sight_r>0.8}}＊{{assign var=ook value=1}}{{/if}}</td>
<td style="text-align:center;">{{if $ok==0 && $ook==0 && $dd.r.sight_r!="" && $dd.l.sight_r!=""}}<span style="color:red;">＊</span>{{/if}}</td>
{{php}}
if ($this->_tpl_vars['dd']['r']['sight_o']!="" && $this->_tpl_vars['dd']['l']['sight_o']!="") {
	$this->_tpl_vars['v'][$this->_tpl_vars['sex']][o][$this->_tpl_vars['ok']]+=1;
	$this->_tpl_vars['v'][9][o][$this->_tpl_vars['ok']]+=1;
}
if ($this->_tpl_vars['ok']==0 && $this->_tpl_vars['dd']['r']['sight_r']!="" && $this->_tpl_vars['dd']['l']['sight_r']!="") {
	$this->_tpl_vars['v'][$this->_tpl_vars['sex']][r][$this->_tpl_vars['ook']]+=1;
	$this->_tpl_vars['v'][9][r][$this->_tpl_vars['ook']]+=1;
}
{{/php}}
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">　</td>
<td rowspan="2">檢查<br>人數</td>
<td colspan="4">裸視視力</td>
<td colspan="4">矯正視力</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>正常<br>人數</td>
<td>正常<br>比率</td>
<td>不良<br>人數</td>
<td>不良<br>比率</td>
<td>正常<br>人數</td>
<td>正常<br>比率</td>
<td>不良<br>人數</td>
<td>不良<br>比率</td>
</tr>
<tr style="background-color:white;text-align:center;">
{{php}}
$this->_tpl_vars['v'][1][nums]=$this->_tpl_vars['v'][1][o][0]+$this->_tpl_vars['v'][1][o][1];
$this->_tpl_vars['v'][2][nums]=$this->_tpl_vars['v'][2][o][0]+$this->_tpl_vars['v'][2][o][1];
$this->_tpl_vars['v'][9][nums]=$this->_tpl_vars['v'][1][nums]+$this->_tpl_vars['v'][2][nums];
{{/php}}
<td>男生</td>
<td>{{$v.1.nums}}</td>
<td>{{$v.1.o.1}}</td>
<td>{{$v.1.o.1/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.1.o.0}}</td>
<td>{{$v.1.o.0/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.1.r.1}}</td>
<td>{{$v.1.r.1/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.1.r.0}}</td>
<td>{{$v.1.r.0/$v.9.nums*100|string_format:"%.1f"}}%</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>女生</td>
<td>{{$v.2.nums}}</td>
<td>{{$v.2.o.1}}</td>
<td>{{$v.2.o.1/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.2.o.0}}</td>
<td>{{$v.2.o.0/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.2.r.1}}</td>
<td>{{$v.2.r.1/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.2.r.0}}</td>
<td>{{$v.2.r.0/$v.9.nums*100|string_format:"%.1f"}}%</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>合計</td>
<td>{{$v.9.nums}}</td>
<td>{{$v.9.o.1}}</td>
<td>{{$v.9.o.1/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.9.o.0}}</td>
<td>{{$v.9.o.0/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.9.r.1}}</td>
<td>{{$v.9.r.1/$v.9.nums*100|string_format:"%.1f"}}%</td>
<td>{{$v.9.r.0}}</td>
<td>{{$v.9.r.0/$v.9.nums*100|string_format:"%.1f"}}%</td>
</tr>
</table>
<span class="small">PS:各項比率皆以總人數為分母所得之百分率。</span>
</td></tr></table>