{{* $Id: health_wh_stunting.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<input type="submit" name="xls" value="匯出名冊">
<input type="submit" name="noti" value="列印通知單">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>身高</td>
<td>體重</td>
<td>體位判讀</td>
<td>GHD</td>
<td>BMI</td>
<td>實歲</td>
<td>診斷代號</td>
<td>診斷醫院</td>
</tr>
{{assign var=n value=0}}
{{assign var=ssn value=""}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $dd.stunting}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$dd.weight}}</td>
{{assign var=Bid value=$dd.Bid}}
<td {{if $Bid!=1}}style="color:red;"{{/if}}>{{$Bid_arr.$Bid}}</td>
<td>{{$dd.GHD}}</td>
<td>{{$dd.BMI}}</td>
<td style="text-align:center;">{{$dd.years}}</td>
<td></td>
<td></td>
</tr>
{{assign var=n value=$n+1}}
{{assign var=ssn value=$ssn,$sn}}
{{/if}}
{{/foreach}}
{{/foreach}}
{{if $n==0}}
<tr style="background-color:white;color:red;text-align:center;">
<td colspan="12">無資料</td>
</tr>
{{else}}
<input type="hidden" name="ssn" value="{{$ssn}}">
{{/if}}
</table>
