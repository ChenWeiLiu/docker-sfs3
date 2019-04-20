{{* $Id: health_wh_class_list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<script>
function chg_tb(){
	document.getElementById('tb').value=1-document.getElementById('tb').value;
	document.myform.submit();
}
</script>

<input type="submit" name="xls" value="匯出XLS檔">
<input type="submit" name="ods" value="匯出ODS檔">
<input type="submit" name="ods_all" value="匯出全年級ODS檔">
<input type="checkbox" {{if $smarty.post.table}}checked{{/if}} OnChange="chg_tb();"><span class="small">含課桌椅型號</span>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>身高</td>
<td>體重</td>
<td>BMI</td>
<td>體位判讀</td>
{{if $smarty.post.table}}<td>桌椅型號</td>{{/if}}
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;">{{$dd.height}}</td>
<td style="text-align:center;">{{$dd.weight}}</td>
<td>{{$dd.BMI}}</td>
{{assign var=Bid value=$dd.Bid}}
<td {{if $Bid!=1}}style="color:red;"{{/if}}>{{$Bid_arr.$Bid}}</td>
{{assign var=tb value=$dd.height-1}}
{{assign var=tb value=$tb/5|@ceil}}
{{if $smarty.post.table}}{{if $dd.height>0}}<td style="text-align:center">{{$tb*5}}</td>{{else}}<td></td>{{/if}}{{/if}}
</tr>
{{/foreach}}
{{/foreach}}
</table>
<input type="submit" name="xls" value="匯出XLS檔">
<input type="submit" name="ods" value="匯出ODS檔">
<input type="submit" name="ods_all" value="匯出全年級ODS檔">
<input type="checkbox" {{if $smarty.post.table}}checked{{/if}} OnChange="chg_tb();"><span class="small">含課桌椅型號</span>
<input type="hidden" name="table" id="tb" value="{{if $smarty.post.table}}1{{else}}0{{/if}}">