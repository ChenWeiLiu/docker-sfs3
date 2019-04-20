{{* $Id: stud_basic_test_distest2.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<form name="menu_form" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td style="vertival-align:top;background-color:#CCCCCC;">
<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF" width="100%" class="main_body">
<tr><td>學期：{{$year_seme_menu}} 年級：{{$class_year_menu}} {{$menu2}} <br>
輸出：<input type="submit" name="txt" value="中區TXT檔" {{if !$smarty.post.year_name}}disabled="true"{{/if}}> <input type="submit" name="xls" value="北區XLS檔" {{if !$smarty.post.year_name}}disabled="true"{{/if}}> <input type="submit" name="chart" value="成績證明" {{if !$smarty.post.year_name}}disabled="true"{{/if}}>
{{if $smarty.post.year_name}}
<br>
<table border="0" width="100%" style="font-size:12px;" bgcolor="#C0C0C0" cellpadding="3" cellspacing="1">
<tr bgcolor="#FFFFCC" align="center">
<td>班<br>級</td>
<td>座<br>號</td>
<td>學生姓名</td>
<td>身分證字號</td>
<td>性<br>別</td>
<td>出<br>生<br>年</td>
<td>出<br>生<br>月</td>
<td>出<br>生<br>日</td>
<td>學<br>生<br>身<br>分</td>
<td>身<br>心<br>障<br>礙</td>
<td>畢<br>肄<br>業</td>
<td>資<br>料<br>授<br>權</td>
<td>家長姓名</td>
<td>電　　話</td>
<td>郵<br>遞<br>區<br>號</td>
<td>地　　址</td>
<td>學號</td>
<td>國文</td>
<td>英文</td>
<td>數學</td>
<td>社會</td>
<td>自然</td>
<td>健康<br>與<br>體育</td>
<td>藝術<br>與<br>人文</td>
<td>綜合<br>活動</td>
<td>七大<br>學習<br>領域<br>平均</td>
</tr>
{{foreach from=$student_sn item=d key=seme_class}}
{{foreach from=$d item=sn key=site_num}}
<tr bgcolor="#ddddff" align="center">
<td>{{$seme_class|@substr:-2:2}}</td>
<td>{{$site_num|string_format:"%02d"}}</td>
<td>{{$stud_data.$sn.stud_name}}</td>
<td>{{$stud_data.$sn.stud_person_id}}</td>
<td>{{$stud_data.$sn.stud_sex}}</td>
<td>{{$stud_data.$sn.stud_birthday|@substr:0:2}}</td>
<td>{{$stud_data.$sn.stud_birthday|@substr:2:2}}</td>
<td>{{$stud_data.$sn.stud_birthday|@substr:4:2}}</td>
<td>0</td>
<td>00</td>
<td>1</td>
<td>1</td>
<td>{{$stud_data.$sn.parent_name}}</td>
<td>{{$stud_data.$sn.stud_tel}}</td>
<td>{{$stud_data.$sn.addr_zip|@substr:0:3}}</td>
<td>{{$stud_data.$sn.stud_addr_1}}</td>
<td>{{$stud_data.$sn.stud_id}}</td>
{{foreach from=$ss_link item=sl}}
<td>{{s2n score=$fin_score.$sn.$sl semes=$semes}}</td>
{{/foreach}}
<td>{{tavg score=$fin_score.$sn semes=$semes ss_link=$ss_link}}</td>
</tr>
{{/foreach}}
{{/foreach}}
</table>
</td></tr>
{{else}}
<br>請先檢查學期成績是否有多餘資料，以確保成績計算正確。<input type="submit" name="check" value="先檢查成績">
{{*說明*}}
<table class="small" width="100%">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;background-color:white;">
	<ol style="list-style-type: none;">
	<li>1. 本資料為中區、北區五專聯招免試入學使用。</li>
	<li>2. 成績計算規則：</li>
	<li> &nbsp; (1) 各科目級分換算規則：</li>
	<li> &nbsp; &nbsp; (i) 各科目五學期原始成績不四捨五入，直接換算為級分。</li>
	<li> &nbsp; &nbsp; (ii) 各科目五學期之級分加總後，得到各科目級分合計。</li>
	<li> &nbsp; (2) 七大領域（八大科）平均換算規則：</li>
	<li> &nbsp; &nbsp; (i) 各科目五學期原始成績不四捨五入，分別算出各科目平均分數後，予以四捨五入取至小數點後第二位。</li>
	<li> &nbsp; &nbsp; (ii) 以各科目平均分數（取至小數點後第二位）算出各科目總平均後，予以四捨五入取至小數點後第一位。</li>
	<li> &nbsp; &nbsp; (iii) 以各科目總平均（取至小數點後第一位）換算為等第。</li>
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
