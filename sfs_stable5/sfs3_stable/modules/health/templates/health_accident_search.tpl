{{* $Id: health_accident_search.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

{{dhtml_calendar_init src="`$SFS_PATH_HTML`javascripts/calendar.js" setup_src="`$SFS_PATH_HTML`javascripts/calendar-setup.js" lang="`$SFS_PATH_HTML`javascripts/calendar-tw.js" css="`$SFS_PATH_HTML`javascripts/calendar-brown.css"}}
<script>
function del(a) {
	if (confirm('確定刪除此筆資料?')) {
		document.getElementById('del').value=1;
		document.getElementById('del_id').value=a;
		document.myform.submit();
	}
}
</script>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small" style="font-size:10pt;">
<tr style="color:white;text-align:center;line-height:18pt;font-size:14pt;">
<td colspan="2">查詢條件指定</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td nowrap>起迄時間</td>
<td style="background-color:#f4feff;text-align:left;">
<input type="text" id="start_date" name="health_accident_record[start_date]" value="{{$smarty.post.start_date}}" style="width:70px;"><input type="button" id="sdate" value="選擇時間"> ～ <input type="text" id="end_date" name="health_accident_record[end_date]" value="{{$smarty.post.end_date}}" style="width:70px;"><input type="button" id="edate" value="選擇時間">(空白代表不限定)
</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>地　　點</td>
<td style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$aplace item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="radio" id="sel_pla_{{$k}}" name="health_accident_record[place_id]" value="{{$k}}">{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table>
</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>原　　因</td>
<td style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$areason item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="radio" id="sel_rea_{{$k}}" name="health_accident_record[reason_id]" value="{{$k}}">{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table>
</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>部　　位</td>
<td style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$apart item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="checkbox" id="sel_par_{{$k}}" name="health_accident_part_record[part_id][{{$k}}]">{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table></td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>狀　　況</td>
<td style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$astatus item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="checkbox" id="sel_sta_{{$k}}" name="health_accident_status_record[status_id][{{$k}}]">{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table></td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>處置方式</td>
<td style="background-color:#f4feff;">
<table style="width:100%;">{{assign var=i value=0}}{{foreach from=$aattend item=d key=k}}{{if $i % 5 == 0}}<tr>{{/if}}<td><input type="checkbox" id="sel_att_{{$k}}" name="health_accident_attend_record[attend_id][{{$k}}]">{{$d}}</td>{{assign var=i value=$i+1}}{{if $i % 5 == 0}}</tr>{{/if}}{{/foreach}}</table></td>
</tr>
</table>

<input type="submit" name="start_search" value="開始查詢">
<input type="hidden" id="del" name="del">
<input type="hidden" id="del_id" name="update[del][0]">
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small">
<tr style="color:white;text-align:center;"><td colspan="13">查詢記錄</td></tr>
<tr style="background-color:white;text-align:center;">
<td>年級</td>
<td>班級</td>
<td>座號</td>
<td>姓名</td>
<td>處理時間</td>
<td>觀察時間</td>
<td>體溫</td>
<td>地點</td>
<td>原因</td>
<td>部位</td>
<td>狀況</td>
<td>處置方式</td>
<td>功能選項</td>
</tr>
{{foreach from=$rowdata item=d}}
<tr style="background-color:{{cycle values="#C0CAEC,white"}};text-align:center;">
<td>{{$d.year}}</td>
<td>{{$d.class}}</td>
<td>{{$d.num}}</td>
<td>{{$d.stud_name}}</td>
<td>{{$d.sign_time}}</td>
<td>{{if $d.obs_min>0}}{{$d.obs_min}}分{{else}}未記錄{{/if}}</td>
<td>{{if $d.temp>0}}{{$d.temp}}<sup>o</sup>C{{else}}未記錄{{/if}}</td>
{{assign var=i value=$d.place_id}}
<td>{{$aplace.$i}}</td>
{{assign var=i value=$d.reason_id}}
<td>{{$areason.$i}}</td>
<td>{{foreach from=$d.part_id item=dd}}{{$apart.$dd}}<br>{{/foreach}}</td>
<td>{{foreach from=$d.status_id item=dd}}{{$astatus.$dd}}<br>{{/foreach}}</td>
<td>{{foreach from=$d.attend_id item=dd}}{{$aattend.$dd}}<br>{{/foreach}}</td>
<td>編修 <a href="#" OnClick="del({{$d.id}});">刪除</a> <span title="{{if $d.memo}}{{$d.memo}}{{else}}無說明內容{{/if}}" style="cursor:pointer;color:blue;">說明</span></td>
</tr>
{{/foreach}}
</table>
</td></tr></table>
{{dhtml_calendar inputField="start_date" button="sdate" singleClick=false}}
{{dhtml_calendar inputField="end_date" button="edate" singleClick=false}}