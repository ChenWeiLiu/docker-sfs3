{{* $Id: stud_basic_test_distest4.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/jquery.progressbar.min.js"></script>
<script type="text/javascript">
<!--
function go(a) {
	var i =0;
	while (i < document.menu_form.elements.length)  {
		b="sel"+a;
		c=document.menu_form.elements[i].id.substr(0,4);
		if (b==c) document.menu_form.elements[i].checked=!document.menu_form.elements[i].checked;
		i++;
	}
}

var pp=0, d, acc, pass;
var arr=[0{{foreach from=$class_arr item=d key=k}}, {{$k}}{{/foreach}}];
function cal() {
	$('#calBtn').attr('disabled', true);
	$('#calBtn').attr('value', ' 成績計算中, 請稍候... ');
	$("#calBtn").get(0).style.color = "red";
	$('#proc').show();
	$('#pb1').progressBar(0);
	d=100/{{$class_arr|@count}};
	$.each(arr,function(i, n){
		if (n>0) {
			$.post('{{$smarty.server.SCRIPT_NAME}}',{ class_no: n, year_seme: "{{$smarty.post.year_seme}}", year_name: "{{$smarty.post.year_name}}", act: "cal", step: 4},function(data){
				if (data!=''){
					pp+=d;
					$('#pb1').progressBar(pp);
					$('#msg').html(data);
					if (pp>99) {
						$('#calBtn').attr('value', '計算完畢');
						$("#calBtn").get(0).style.color = "blue";
						$('#nextBtn').attr('disabled', false);
						$('#proc').hide();
					}
				}
			});
		}
	});
}
//-->
</script>

<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<input type="hidden" name="step" value="{{$smarty.post.step}}">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>學期：{{$year_seme_menu}} 年級：{{$class_year_menu}} {{if $smarty.post.year_name}}{{$step_str}} <input type="submit" id="nextBtn" name="next" value="進行下個步驟" {{if $smarty.post.step>=4}}disabled=true{{/if}}>{{/if}}
{{if $smarty.post.step==3}}<br>快選：<input type="button" value="轉入前反選" OnClick="go(1);"> <input type="button" value="轉入學期反選" OnClick="go(2);"> <input type="button" value="轉入後反選" OnClick="go(3);" disabled> &nbsp; 刪除轉學生多餘成績：<input type="submit" name="del" value="刪除">{{/if}}
{{if $smarty.post.step==5}}<br>區域：<input type="radio" name="cy" value="1" {{if $smarty.post.cy=="" || $smarty.post.cy==1}}checked{{/if}}>中投版 <input type="radio" name="cy" value="2" {{if $smarty.post.cy==2}}checked{{/if}}>彰縣版 <input type="submit" name="CRT" value="列印證明單"> <input type="submit" name="LOCK" value="成績封存">{{/if}}
{{if $smarty.post.step==1}}
<br>
<table border="0" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>選</td>
<td>學期</td>
</tr>
{{foreach from=$year_arr item=d key=i}}
<tr bgcolor="white" align="center">
<td><input type="checkbox" name="seme[{{$i}}]" value="{{$i}}" {{if $seme_arr.$i}}checked{{/if}}></td>
<td>{{$d}}</td>
</tr>
{{/foreach}}
</table>
{{elseif $smarty.post.step==2}}
<br>
<table border="0" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>學年</td>
<td>學期</td>
<td>科目代碼</td>
<td>科目名</td>
<td>成績數</td>
<td>對應科目</td>
<td>班級課程</td>
</tr>
{{foreach from=$ss_arr item=d key=i}}
{{assign var=year value=$d.year}}
{{assign var=semester value=$d.semester}}
<tr bgcolor="{{cycle values="white,#f0f0f0"}}" align="center">
<td>{{$year}}</td>
<td>{{$semester}}</td>
<td>{{$i}}</td>
<td style="text-align:left;">&nbsp; &nbsp;{{$d.name}}&nbsp; &nbsp;</td>
<td>{{$d.num}}</td>
<td>{{html_radios name=sel[$i] options=$m_arr selected=$subj_arr.$i|intval}}</td>
<td>{{if $d.class_id}}是{{else}}否{{/if}}</td>
</tr>
{{/foreach}}
</table>
{{foreach from=$seme_arr item=d name=s}}
<input type="hidden" name="seme[{{$smarty.foreach.s.iteration}}]" value="{{$d}}">
{{/foreach}}
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>只需對應要採計的科目，不採計的科目則無需對應。</li>
	<li>若為班級課程，亦需進行對應，否則將不會被採計。</li>
	</ol>
</td></tr>
</table>
{{elseif $smarty.post.step==3}}
<br>
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>班級</td>
<td>座號</td>
<td>學生姓名</td>
<td>轉入日</td>
{{foreach from=$semes item=d}}
<td>{{$d}}</td>
{{/foreach}}
</tr>
{{foreach from=$sn_arr item=sn}}
<tr bgcolor="white" align="center">
<td>{{$rowdata.$sn.seme_class}}</td>
<td>{{$rowdata.$sn.seme_num}}</td>
<td style="color:{{if $rowdata.$sn.stud_sex==1}}blue{{else}}red{{/if}};">{{$rowdata.$sn.stud_name}}<input type="hidden" name="sn[{{$sn}}]" value="{{$sn}}"></td>
<td>{{$rowdata.$sn.move_date}}</td>
{{foreach from=$semes item=d key=s}}
{{if $rowdata.$sn.move_year_seme>$s}}{{assign var=ss value=1}}{{elseif $rowdata.$sn.move_year_seme==$s}}{{assign var=ss value=2}}{{else}}{{assign var=ss value=3}}{{/if}}
<td style="background-color:{{if $ss==1}}white{{elseif $ss==2}}#FFFF80{{else}}#E0E0E0{{/if}};">{{t2c times=$times.$s semes=$s sn=$sn kind=$ss enable=$rowdata.$sn.testdata.$s}}</td>
{{/foreach}}
</tr>
{{/foreach}}
</table>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>黃底色代表該學期為轉入學期。</li>
	<li>轉入後的各學期（當學期請自行依情況做人工判斷）階段成績自動（強制）採計。</li>
	<li>需先補登成績後，所選項目才能儲存。</li>
	</ol>
</td></tr>
</table>
{{elseif $smarty.post.step==4}}
<div id="proc" style="display:none;">
<br>
成績計算進度 <span class="progressBar" id="pb1">0%</span>
<div id="msg">
&nbsp;
</div></div>
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>按下「開始計算」鈕將開始進行成績計算。</li>
	</ol>
</td></tr>
</table>
{{elseif $smarty.post.step==5}}
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>班級</td>
<td>學號</td>
<td>姓名</td>
<td>身分證號</td>
<td>性別</td>
<td>生日</td>
{{foreach from=$col_arr item=d}}
<td>{{$d}}定</td>
{{/foreach}}
{{foreach from=$s_arr item=d}}
<td>{{$d}}定平</td>
{{/foreach}}
<td>總定平</td>
{{foreach from=$s_arr item=d}}
<td>{{$d}}定PR</td>
{{/foreach}}
<td>總定PR</td>
</tr>
{{foreach from=$student_sn item=d key=seme_class}}
{{foreach from=$d item=sn key=site_num}}
<tr bgcolor="#ddddff" align="center">
<td>{{$seme_class|@substr:-2:2|intval}}</td>
<td>{{$stud_data.$sn.stud_id}}</td>
<td>{{$stud_data.$sn.stud_name}}</td>
<td>{{$stud_data.$sn.stud_person_id}}</td>
<td>{{$stud_data.$sn.stud_sex}}</td>
<td>{{$stud_data.$sn.stud_birthday}}</td>
{{foreach from=$semes item=si}}
{{foreach from=$s_arr item=sl key=j}}
<td>{{$rowdata.$sn.$si.$j.score}}</td>
{{/foreach}}
{{/foreach}}
{{foreach from=$s_arr item=sl key=j}}
<td>{{$rowdata.$sn.9991.$j.score}}</td>
{{/foreach}}
<td>{{$rowdata.$sn.9991.6.score}}</td>
{{foreach from=$s_arr item=sl key=j}}
<td>{{$rowdata.$sn.9991.$j.pr}}</td>
{{/foreach}}
<td>{{$rowdata.$sn.9991.6.pr}}</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
{{else}}
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>本資料目前為台中縣免試入學使用，其他地區若有不同資料格式，請再與程式開發小組連絡。</li>
	<li>本作業將依下列步驟進行：</li>
	<ol style="list-style-type: lower-roman;">
	<li>選擇所要處理成績的學年學期。</li>
	<li>設定各學年學期中所要處理的對應科目。</li>
	<li>設定各轉學生要處理成績的學年學期與階段。</li>
	<li>成績計算。</li>
	<li>顯示結果。</li>
	</ol>
	</ol>
</td></tr>
</table>
{{/if}}
</tr>
</table>
</td></tr>
</table>
</form>
{{include file="$SFS_TEMPLATE/footer.tpl"}}
