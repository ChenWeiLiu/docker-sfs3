{{* $Id: health_input_fclass.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<script>
function eb(a,b) {
	var i =0;

	while (i < document.myform.elements.length)  {
		c=a.toString();
		d=c.length;
		e=document.myform.elements[i].id.substr(0,d);
		if (e==a) {
			if (b==0) {
				document.myform.elements[i].disabled=true;
				document.getElementById('s'+c).disabled=true;
				for(j=1;j<={{$maxd}};j++) document.getElementById(a+"_w"+j).value="";
			} else {
				document.myform.elements[i].disabled=false;
				document.getElementById('s'+c).disabled=false;
			}
		}
		i++;
	}
}

function chk_s(a) {
	for(i=0;i<=2;i++) {
		if (a!=null) {
			if (a!=i) document.getElementById("tm"+i).checked=false;
		} else {
			if (document.getElementById("tm"+i).checked) return i;
		}
	}
}

function fill(a,b) {
	c=chk_s();
	if (c==null) {
		alert("請先選實施狀況!");
	} else {
		if (b==1) for(i=1;i<={{$maxd}};i++) document.getElementById("w"+i+"_"+a).value=c;
		else {
			var i=0;
			if (a>9) d=2;
			else d=1;
			while (i < document.myform.elements.length) {
				e=document.myform.elements[i];
				if (e.id.substr(0,d+2)==("w"+a+"_") && e.disabled==false) e.value=c;
				i++;
			}
		}
		alert("離開本頁面前請記得儲存!");
	}
}

function cm() {
	if (confirm("確定要把全校學生狀態都設定為「參與」?")) {
		document.myform.all.value=1;
		document.myform.submit();
	}
}

function cm2() {
	if (confirm("確定要把全校參與學生的實施狀態都設定為「有漱口」?")) {
		document.myform.act.value=1;
		document.myform.submit();
	}
}
</script>

<fieldset class="small" style="width:50%;">
<legend style="color:blue;">實施狀況</legend>
<input type="checkbox" id="tm0" value="0" OnClick="chk_s(0);">缺席 (代碼:<span style="color:red;">0</span>)
<input type="checkbox" id="tm1" value="1" OnClick="chk_s(1);">未漱口 (代碼:<span style="color:red;">1</span>)
<input type="checkbox" id="tm2" value="2" OnClick="chk_s(2);">有漱口 (代碼:<span style="color:red;">2</span>)
</fieldset>

<input type="button" value="設定全校學生皆參與" OnClick="cm();">
<input type="button" value="設定全校參與學生都有漱口" OnClick="cm2();">
<input type="submit" name="save" value="確定儲存">
<input type="reset" value="放棄儲存">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">選</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td>週別</td>
{{foreach from=$date_arr item=d key=i}}
<td><input type="radio" id="w{{$d.week_no}}" name="w" OnClick="fill('{{$d.week_no}}',2);document.getElementById('w'+{{$d.week_no}}).checked=false;"><br>{{$d.week_no}}</td>
{{/foreach}}
<td colspan="3">合計</td>
<td rowspan="2">每位<br>學童<br>執行率</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>參與狀況＼日期</td>
{{foreach from=$date_arr item=d key=i}}
<td>{{$d.do_date|@substr:5:2}}<br>／<br>{{$d.do_date|@substr:8:2}}</td>
{{/foreach}}
<td>缺<br><br>席</td>
<td>未<br>漱<br>口</td>
<td>有<br>漱<br>口</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=h value=$health_data->health_data.$sn.$year_seme.frecord}}
{{assign var=agree value=$h.agree}}
<tr style="background-color:white;text-align:center;">
<td style="background-color:#f4feff;"><input type="radio" id="s{{$sn}}" name="s{{$sn}}" {{if $agree=="0"}}disabled{{/if}} OnClick="fill('{{$sn}}','1');document.getElementById('s{{$sn}}').checked=false;"></td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td>
<input type="radio" name="update[new][{{$sn}}][{{$year_seme}}][health_frecord][agree]" value="1" {{if $agree==1}}checked{{/if}} OnClick="eb({{$sn}},1);">參與
<input type="radio" name="update[new][{{$sn}}][{{$year_seme}}][health_frecord][agree]" value="0" {{if $agree=="0"}}checked{{/if}} OnClick="eb({{$sn}},0);">不參與
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][health_frecord][agree]" value="{{$agree}}">
</td>
{{assign var=unf value=0}}
{{assign var=nof value=0}}
{{assign var=yaf value=0}}
{{foreach from=$date_arr item=d key=i}}
<td>
{{assign var=n value=$d.week_no}}
{{assign var=col value=w$n}}
<input type="text" id="{{$col}}_{{$sn}}" name="update[new][{{$sn}}][{{$year_seme}}][health_frecord][{{$col}}]" maxlength="1" style="width:10pt;" {{if $agree=="0"}}disabled{{/if}} value="{{$h.$col}}">
<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][health_frecord][{{$col}}]" value="{{$h.$col}}">
{{if $h.$col==0 && $h.$col!=""}}
{{assign var=unf value=$unf+1}}
{{elseif $h.$col==1}}
{{assign var=nof value=$nof+1}}
{{elseif $h.$col==2}}
{{assign var=yaf value=$yaf+1}}
{{/if}}
</td>
{{/foreach}}
<td>{{if $agree}}{{$unf}}{{/if}}</td>
<td>{{if $agree}}{{$nof}}{{/if}}</td>
<td>{{if $agree}}{{$yaf}}{{/if}}</td>
<td>{{if $agree}}{{$yaf/$maxd*100|@round:2}}%{{/if}}</td>
{{/foreach}}
</tr>
</table>
<input type="submit" name="save" value="確定儲存">
<input type="reset" value="放棄儲存">
<input type="hidden" name="all" value="">
<input type="hidden" name="act" value="">
