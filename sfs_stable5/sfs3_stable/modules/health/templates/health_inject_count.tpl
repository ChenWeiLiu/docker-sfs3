{{* $Id: health_inject_count.tpl 5690 2009-10-19 06:20:51Z brucelyc $ *}}

{{if $smarty.post.class_name}}<input type="submit" name="print" value="友善列印">{{/if}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">班別</td>
<td colspan="2">查卡結果</td>
<td colspan="1" nowrap>卡介苗</td>
<td colspan="3">B型肝炎疫苗</td>
<td colspan="4">小兒麻痺口服疫苗</td>
<td colspan="4">白喉破傷風百日咳<br>混合疫苗</td>
<td colspan="1">麻疹<br>疫苗</td>
<td colspan="1" nowrap>麻疹腮<br>腺炎德<br>國麻疹<br>混合疫<br>苗</td>
<td colspan="3">日本腦炎疹苗</td>
</tr>
{{php}}$this->_tpl_vars['v'][9][9]=$this->_tpl_vars['v'][1][9]+$this->_tpl_vars['v'][2][9];{{/php}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>學生<br>人數</td>
<td nowrap>持卡<br>人數</td>
<td nowrap>一劑</td>
<td nowrap>第一劑</td>
<td nowrap>第二劑</td>
<td nowrap>第三劑</td>
<td nowrap>第一劑</td>
<td nowrap>第二劑</td>
<td nowrap>第三劑</td>
<td nowrap>第四劑</td>
<td nowrap>第一劑</td>
<td nowrap>第二劑</td>
<td nowrap>第三劑</td>
<td nowrap>第四劑</td>
<td nowrap>一劑</td>
<td nowrap>一劑</td>
<td nowrap>第一劑</td>
<td nowrap>第二劑</td>
<td nowrap>第三劑</td>
</tr>
{{foreach from=$rowdata item=d key=i}}
{{if $i!="total"}}
<tr style="background-color:{{cycle values="white,#f4feff"}};">
<td>{{$i}}</td>
<td>{{$d.total}}</td>
<td>{{$d.y|intval}}</td>
<td>{{$d.1.1|intval}}</td>
<td>{{$d.2.1|intval}}</td>
<td>{{$d.2.2|intval}}</td>
<td>{{$d.2.3|intval}}</td>
<td>{{$d.3.1|intval}}</td>
<td>{{$d.3.2|intval}}</td>
<td>{{$d.3.3|intval}}</td>
<td>{{$d.3.4|intval}}</td>
<td>{{$d.4.1|intval}}</td>
<td>{{$d.4.2|intval}}</td>
<td>{{$d.4.3|intval}}</td>
<td>{{$d.4.4|intval}}</td>
<td>{{$d.6.1|intval}}</td>
<td>{{$d.7.1|intval}}</td>
<td>{{$d.5.1|intval}}</td>
<td>{{$d.5.2|intval}}</td>
<td>{{$d.5.3|intval}}</td>
</tr>
{{/if}}
{{/foreach}}
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>合計</td>
<td>{{$rowdata.total.total}}</td>
<td>{{$rowdata.total.y|intval}}</td>
<td>{{$rowdata.total.1.1|intval}}</td>
<td>{{$rowdata.total.2.1|intval}}</td>
<td>{{$rowdata.total.2.2|intval}}</td>
<td>{{$rowdata.total.2.3|intval}}</td>
<td>{{$rowdata.total.3.1|intval}}</td>
<td>{{$rowdata.total.3.2|intval}}</td>
<td>{{$rowdata.total.3.3|intval}}</td>
<td>{{$rowdata.total.3.4|intval}}</td>
<td>{{$rowdata.total.4.1|intval}}</td>
<td>{{$rowdata.total.4.2|intval}}</td>
<td>{{$rowdata.total.4.3|intval}}</td>
<td>{{$rowdata.total.4.4|intval}}</td>
<td>{{$rowdata.total.6.1|intval}}</td>
<td>{{$rowdata.total.7.1|intval}}</td>
<td>{{$rowdata.total.5.1|intval}}</td>
<td>{{$rowdata.total.5.2|intval}}</td>
<td>{{$rowdata.total.5.3|intval}}</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>持卡率<br>或<br>接種率</td>
<td>{{$rowdata.total.total}}</td>
<td>{{$rowdata.total.y/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.1.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.2.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.2.2/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.2.3/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.3.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.3.2/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.3.3/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.3.4/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.4.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.4.2/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.4.3/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.4.4/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.6.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.7.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.5.1/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.5.2/$rowdata.total.total*100|round:2}}%</td>
<td>{{$rowdata.total.5.3/$rowdata.total.total*100|round:2}}%</td>
</tr>
</table>
</td></tr></table>
