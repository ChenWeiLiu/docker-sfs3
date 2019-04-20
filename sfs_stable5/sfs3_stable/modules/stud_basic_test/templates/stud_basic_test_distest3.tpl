{{* $Id: stud_basic_test_distest3.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/jquery.progressbar.min.js"></script>
<script type="text/javascript">
<!--
var pp=0, d, acc, pass;
var arr=[0{{foreach from=$class_arr item=d key=k}}, {{$k}}{{/foreach}}];
function go() {
	$('#calBtn').attr('disabled', true);
	$('#calBtn').attr('value', ' 成績計算中, 請稍候... ');
	$("#calBtn").get(0).style.color = "red";
	$('#proc').show();
	$('#pb1').progressBar(0);
	d=100/{{$class_arr|@count}};
	$.each(arr,function(i, n){
		if (n>0) {
			$.post('{{$smarty.server.SCRIPT_NAME}}',{ class_no: n, year_seme: "{{$smarty.post.year_seme}}", year_name: "{{$smarty.post.year_name}}", cy: "{{$smarty.post.cy}}", act: "cal"},function(data){
				if (data!=''){
					pp+=d;
					$('#pb1').progressBar(pp);
					$('#msg').html(data);
					if (pp>99) {
						$('#calBtn').attr('value', '計算完畢');
						$("#calBtn").get(0).style.color = "blue";
						$('#showBtn').attr('disabled', false);
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
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>
{{if !$smarty.post.year_name}}區域：<input type="radio" name="city" {{if $smarty.post.cy=="" || $smarty.post.cy==1}}checked{{/if}} OnClick="document.menu_form.cy.value='1';">中投區 <input type="radio" name="city" {{if $smarty.post.cy==2}}checked{{/if}} OnClick="document.menu_form.cy.value='2';">彰化區 <input type="radio" name="city" {{if $smarty.post.cy==3}}checked{{/if}} OnClick="document.menu_form.cy.value='3';">臺南區 <input type="radio" name="city" {{if $smarty.post.cy==4}}checked{{/if}} OnClick="document.menu_form.cy.value='4';">竹苗區 <input type="radio" name="city" {{if $smarty.post.cy==5}}checked{{/if}} OnClick="document.menu_form.cy.value='5';">臺東區<br>{{/if}}
學期：{{$year_seme_menu}} 年級：{{$class_year_menu}} <input type="submit" id="cleanBtn" name="clean" value="清除暫存"> <input type="button" id="calBtn" value="開始計算" OnClick="go();" {{if !$smarty.post.clean || !$smarty.post.year_name}}disabled="true"{{/if}}> <input type="submit" id="showBtn" name="show" value="資料顯示" disabled="true">{{if $smarty.post.show}} <input type="submit" name="out" value="資料匯出"> <input type="submit" name="htm" value="證明單輸出"> <input type="submit" name="LOCK" value="成績封存">{{/if}}
{{if $smarty.post.year_name}}<br>區域：<span style="color:red;">《{{if $smarty.post.cy==2}}彰化{{elseif $smarty.post.cy==3}}臺南{{elseif $smarty.post.cy==4}}竹苗{{elseif $smarty.post.cy==5}}臺東{{else}}中投{{/if}}區》{{/if}}</span> <input type="hidden" name="cy" value="{{$smarty.post.cy}}">
{{if $smarty.post.show}}
<br>
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>班級</td>
<td>學號</td>
<td>姓名</td>
<td>身分證號</td>
<td>性別</td>
{{if $smarty.post.cy!=4}}
<td>生日</td>
{{/if}}
{{foreach from=$col_arr item=d}}
<td>{{$d}}</td>
{{/foreach}}
{{if $smarty.post.cy!=4}}
{{foreach from=$s_arr item=d}}
<td>{{$d}}平</td>
{{/foreach}}
{{/if}}
{{if $smarty.post.cy==2 || $smarty.post.cy==4 || $smarty.post.cy==5}}
<td>前百分</td>
{{elseif $smarty.post.cy==3}}
<td>排序</td>
{{else}}
{{foreach from=$s_arr item=d}}
<td>{{$d}}PR</td>
{{/foreach}}
{{/if}}
</tr>
{{foreach from=$student_sn item=d key=seme_class}}
{{foreach from=$d item=sn key=site_num}}
<tr bgcolor="#ddddff" align="center">
<td>{{$seme_class|@substr:-2:2|intval}}</td>
<td>{{$stud_data.$sn.stud_id}}</td>
<td>{{$stud_data.$sn.stud_name}}</td>
<td>{{$stud_data.$sn.stud_person_id}}</td>
<td>{{$stud_data.$sn.stud_sex}}</td>
{{if $smarty.post.cy!=4}}
<td>{{$stud_data.$sn.stud_birthday}}</td>
{{/if}}
{{foreach from=$semes item=si key=i}}
{{foreach from=$s_arr item=sl key=j}}
{{if $smarty.post.cy!=4 || $i!=5}}
<td>{{$rowdata.$sn.$i.$j.score}}</td>
{{/if}}
{{/foreach}}
{{/foreach}}
{{if $smarty.post.cy==2 || $smarty.post.cy==4 || $smarty.post.cy==5}}
{{if $j==10}}
<td>{{$rowdata.$sn.$pry.$j.pr}}％</td>
{{/if}}
{{elseif $smarty.post.cy==3}}
{{if $j==10}}
<td>{{$rowdata.$sn.$pry.$j.pr}}</td>
{{/if}}
{{else}}
{{foreach from=$s_arr item=sl key=j}}
<td>{{$rowdata.$sn.$pry.$j.pr}}</td>
{{/foreach}}
{{/if}}
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td></tr>
{{else}}
<div id="proc" style="display:none;">
<br>
成績計算進度 <span class="progressBar" id="pb1">0%</span>
<div id="msg">
&nbsp;
</div></div>
<br>請先檢查學期成績是否有多餘資料，以確保成績計算正確。<input type="submit" name="check" value="先檢查成績">
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol>
	<li>本資料目前為中投區、彰化區、臺南區、竹苗區、臺東區高中高職免試入學使用，其他地區若有不同資料格式，請再與<a href="http://www.sfs.project.edu.tw">程式開發小組</a>連絡。</li>
	<li>99學年在校表現採計方式：</li>
	<ol style="list-style-type: none;">
	<li>(1) 中投區：採計三學期（國二上、下學期、國三上學期）七大領域之學期成績（不加權取至小數第2位）。</li>
	<li>(2) 彰化區：採計五學期（國一上學期～國三上學期）七大領域之學期成績（不加權取至小數第2位）。</li>
	<li>(3) 臺南區：採計五學期（國一上學期～國三上學期）七大領域之學期成績（加權取至小數第2位）。</li>
	<li>(4) 竹苗區：採計五學期（國一上學期～國三上學期）七大領域之學期成績（加權取至小數第1位）。</li>
	</ol>
	<li>99學年在校表現呈現方式：</li>
	<ol style="list-style-type: none;">
	<li>(1) 中投區：七大領域PR值。</li>
	<li>(2) 彰化區：七大領域年級前百分比。</li>
	<li>(3) 臺南區：七大領域年級前百分比。</li>
	<li>(4) 竹苗區：七大領域年級前百分比。</li>
	</ol>
	<li>各區匯出檔案格式可能不同，請依區域選擇。</li>
	<li>若計算方式或匯出檔案格式與實際不符，請儘快與<a href="http://www.sfs.project.edu.tw">程式開發小組</a>連絡。</li>
	<li style="color:red;">列印前務必確認學生成績（含學期初成績設定）已完全正確不再修改，否則成績修改後重新計算的結果可能不只一位學生的百分比值變動而造成難以收拾的後果。</li>
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
