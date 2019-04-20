{{* $Id: fitness_input.tpl 8065 2014-06-13 06:18:06Z smallduh $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script language="JavaScript">
function openwindow(t){
	window.open ("quick_input.php?t="+t+"&class_num={{$class_num}}&c_curr_seme={{$smarty.post.year_seme}}","成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}
</script>

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4">
<form action="{{$smarty.server.PHP_SELF}}" method="post">
<input type="hidden" name="act" value="">
<tr>
<td bgcolor="#FFFFFF" valign="top">
<p>{{$seme_menu}} {{$class_menu}} <font size="3" color="blue">按下項目名稱即可輸入成績</font> {{if $admin}}<input type='submit' value='抓取本學期全校學生身高體重資料' name='copy_wh' onclick='return confirm("學生人數多的話可能會耗時很久，確定要這樣做嗎？")'>{{else}}<input type='submit' value='抓取本學期身高體重資料' name='copy_wh'>{{/if}}</p>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">學號</td>
<td align="center"><a onclick="openwindow('0');"><img src="./images/wedit.png" border="0" title="資料輸入">身高</a><br>(cm)</td>
<td align="center"><a onclick="openwindow('1');"><img src="./images/wedit.png" border="0" title="資料輸入">體重</a><br>(kg)</td>
<td align="center"><a onclick="openwindow('2');"><img src="./images/wedit.png" border="0" title="資料輸入">坐姿前彎</a><br>(cm)</td>
<td align="center"><a onclick="openwindow('4');"><img src="./images/wedit.png" border="0" title="資料輸入">立定跳遠</a><br>(cm)</td>
<td align="center"><a onclick="openwindow('3');"><img src="./images/wedit.png" border="0" title="資料輸入">仰臥起坐</a><br>(次)</td>
<td align="center"><a onclick="openwindow('5');"><img src="./images/wedit.png" border="0" title="資料輸入">心肺適能</a><br>(秒)</td>
<td align="center"><a onclick="openwindow('6');"><img src="./images/wedit.png" border="0" title="資料輸入">檢測單位</a></td>
<td align="center"><a onclick="openwindow('7');"><img src="./images/wedit.png" border="0" title="資料輸入">檢測年月</a><br>( 年-月 )</td>
</tr>
{{foreach from=$rowdata item=d key=i}}
{{assign var=sn value=$d.student_sn}}
<tr bgcolor="white">
<td class="small">{{$d.seme_num}}</td>
<td class="small"><font color="{{if $d.stud_sex==1}}blue{{elseif $d.stud_sex==2}}red{{else}}black{{/if}}">{{$d.stud_name}}</font></td>
<td style="text-align:right;">{{$d.stud_id}}</td>
<td style="text-align:right;">{{$fd.$sn.tall}}</td>
<td style="text-align:right;">{{$fd.$sn.weigh}}</td>
<td style="text-align:right;">{{$fd.$sn.test1}}</td>
<td style="text-align:right;">{{$fd.$sn.test3}}</td>
<td style="text-align:right;">{{$fd.$sn.test2}}</td>
<td style="text-align:right;">{{$fd.$sn.test4}}</td>
<td style="text-align:left;">{{$fd.$sn.organization}}</td>
<td style="text-align:center;">{{$fd.$sn.test_y}}-{{$fd.$sn.test_m}}</td>
</tr>
{{/foreach}}
</table>
</td></tr></table>
{{if $admin}}
<table border="2" cellpadding="3" cellspacing="0" style="border-collapse: collapse; font-size=9px;" bordercolor="#119911" width="100%">
		<tr><td align="center" bgcolor="#ccffff">測驗結果批次匯入</td></tr>
		<tr><td><font size=2>
			<li>本功能可匯入選定學期就學學生的體適能測驗紀錄，<a href='./xls_sample.xls'><img src='./images/pen.png' border=0 height=11>格式下載</a>。</li>
			<li>匯入的資料採教育部資料格式，欄位須為固定的順序：測驗日期、學校類別、年級、班級名稱、學號、性別、身分證字號、生日、身高、體重、坐姿體前彎、立定跳遠、仰臥起坐、心肺適能、<font color='red'>檢測單位</font>。</li>
			<li>匯入時免登載的欄位：學校類別、年級、性別、身分證字號、生日；<font color='red'>必有的欄位：班級名稱、學號；班級名稱請用序列代號表示，如六年甲班請填601、九年二班請填902。</font></li>
			<li>匯入後，程式會將資料內有效學生指定學期原有的紀錄刪除，再依據您貼上的資料重新記錄，請謹慎使用。</li>
			<li>複製貼上的資料無須包含欄位名稱或說明，僅需貼上學生紀錄列即可！</li>
			</font></td></tr>
		<tr><td>
		<textarea name="content" style="border-width:1px; color:blue; background:#ffeeee; font-size:11px;" cols=120 rows=5></textarea></td></tr>
		<tr><td align="center" bgcolor="#ccffff"><input type="submit" name="go" value="匯入"></td></tr>
		</table><font color="red">{{$msg}}</font>
{{/if}}
</form>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
