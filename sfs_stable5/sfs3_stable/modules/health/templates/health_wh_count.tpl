{{* $Id: health_wh_count.tpl 5648 2009-09-17 08:11:29Z brucelyc $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">年級</td>
<td colspan="3">男生</td>
<td colspan="3">女生</td>
</tr>
{{php}}$this->_tpl_vars['v'][9][9]=$this->_tpl_vars['v'][1][9]+$this->_tpl_vars['v'][2][9];{{/php}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td>身高(cm)</td>
<td>體重(kg)</td>
<td>BMI(kg/m<sup>2</sup>)</td>
<td>身高(cm)</td>
<td>體重(kg)</td>
<td>BMI(kg/m<sup>2</sup>)</td>
</tr>
{{foreach from=$data_arr item=dd key=i}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$i}}</td>
<td>{{$dd.1.havg}}</td>
<td>{{$dd.1.wavg}}</td>
<td>{{$dd.1.bavg}}</td>
<td>{{$dd.2.havg}}</td>
<td>{{$dd.2.wavg}}</td>
<td>{{$dd.2.bavg}}</td>
</tr>
{{/foreach}}
</table>
</td></tr></table>
<input type="submit" name="print" value="列印">
