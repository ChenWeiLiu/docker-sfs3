{{* $Id: reward_reward_stud_all_print.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<table border="0">
<tr>
<td colspan="6"><font size="4">{{$school_base.sch_cname}}學生個人獎懲明細表</font></td>
</tr>
<tr>
<td colspan="6">學生學號：{{$smarty.post.stud_id}}</td>
</tr>
<tr>
<td colspan="2">學生姓名：{{$stud_base.stud_name}}</td>
</tr>
<tr>
<td colspan="6">列表日期：{{$smarty.now|date_format:"%Y-%m-%d"}}</td>
</tr>
<tr>
<td colspan="7"><hr size="2"></td>
</tr>
<tr class="title_sbody2">
<td align="center"><span style="font-size:10pt;">學年</span></td>
<td align="center"><span style="font-size:10pt;">學期</span></td>
<td align="center"><span style="font-size:10pt;">獎懲事由</span></td>
<td align="center"><span style="font-size:10pt;">獎懲類別</span></td>
<td align="center"><span style="font-size:10pt;">獎懲依據</span></td>
<td align="center" width="80"><span style="font-size:10pt;">獎懲生效日期</span></td>
<td align="center" width="80"><span style="font-size:10pt;">銷過日期</span></td>
</tr>
<tr><td colspan="7"><hr size="2"></tr>
{{foreach from=$reward_rows item=d}}
{{assign var=r_id value=$d.reward_kind}}
{{assign var=sel_year value=$d.reward_year_seme|@substr:0:-1}}
{{assign var=sel_seme value=$d.reward_year_seme|@substr:-1:1}}
{{assign var=k value=$d.reward_kind|@abs}}
{{if $d.reward_kind>0}}{{assign var=j value=0}}{{else}}{{assign var=j value=3}}{{/if}}
{{if $k==1}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+3]++;{{/php}}{{/if}}
{{if $k==2}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+3]+=2;{{/php}}{{/if}}
{{if $k==3}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+2]++;{{/php}}{{/if}}
{{if $k==4}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+2]+=2;{{/php}}{{/if}}
{{if $k==5}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+1]++;{{/php}}{{/if}}
{{if $k==6}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+1]+=2;{{/php}}{{/if}}
{{if $k==7}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+1]+=3;{{/php}}{{/if}}
<tr class="title_sbody1">
<td align="center"><span style="font-size:10pt;">{{$sel_year}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$sel_seme}}</span></td>
<td align="left"><span style="font-size:10pt;">{{$d.reward_reason}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$reward_kind.$r_id}}</span></td>
<td align="left"><span style="font-size:10pt;">{{$d.reward_base}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$d.reward_date}}</span></td>
<td align="center"><span style="font-size:10pt;">{{if $r_id>0}}---{{elseif $d.reward_cancel_date=="0000-00-00"}}未銷過{{else}}{{$d.reward_cancel_date}}{{/if}}</span></td>
</tr>
{{/foreach}}
<tr>
<td colspan="7"><hr size="2"></td>
</tr>
<tr>
<td colspan="7">
<table width="100%">
<tr>
<td align="center">大功</td>
<td align="center">小功</td>
<td align="center">嘉獎</td>
<td align="center">大過</td>
<td align="center">小過</td>
<td align="center">警告</td>
</tr>
<tr>
{{foreach from=$f item=d key=i}}
{{assign var=tt value=$t.$i}}
<td align="center">{{$tt|@intval}}次</td>
{{/foreach}}
</tr>
</table>
</td>
</tr>
<tr>
<td colspan="7"><hr size="2"></td>
</tr>
</table>
