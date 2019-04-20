<!-- $Id: prn_all_score.tpl 5310 2009-01-10 07:57:56Z hami $  -->

{{if $break_page != ''}}{{$break_page}}{{/if}}
<table cellPadding='0' border=0 cellSpacing='0' width='96%' align=center>
	<tr>
<td rowspan=2 class=empty style='font-size:24pt;line-height:26pt;font-family:標楷體'>{{$school_name}} 學生學籍記錄表</td>
		<td class=empty align=left style='font-size:14pt;line-height:16pt'>學號：{{$base.stud_id}}</td>
	</tr>
	<tr>
		<td class=empty align=left style='font-size:14pt;line-height:16pt'>姓名：{{$base.stud_name}}</td>
	</tr>
</table>
<table cellPadding='0' border=0 cellSpacing='0' width='96%' align=center  >
	<tr>
		<td class=top_left>身分證字號</td>
		<td class=top>{{$base.stud_person_id}}</td>
		<td class=top>出生年月日</td>
		<td class=top>{{$base.stud_birthday}}</td>
		<td class=top>性別</td>
		<td class=top>{{$base.stud_sex}}</td>
		<td class=top>出生地</td>
		<td class=top>
		{{if $base.stud_birth_place==''}}------
		{{else}}{{$base.stud_birth_place}}
		{{/if}}
		</td>
		<td class=top rowspan=5 style='width:3.8cm;height:4.6cm'>{{$base.stud_photo_src}}</td>
	</tr>
	<tr>
		<td class=left>家長或監護人</td>
		<td >{{$base.guardian_name}}</td>
		<td >關係</td>
		<td >{{$guar_kind[$base.guardian_relation]}}</td>
		<td >電話</td>
		<td colspan=3>{{$base.phone}}</td>
	</tr>
	<tr>
		<td class=left>戶籍地址</td>
		<td colspan=7 align=left>{{$base.stud_addr_1}}</td>
	</tr>
	<tr>
		<td class=left>入學資格</td>
		<td >{{$base.stud_mschool_name}}</td>
		<td >畢(修)業證書字號</td>
		<td colspan=5>{{$base.grade_word_num}}</td>
	</tr>
	<tr>
<td class=left>異動情形</td>
<td colspan=7 style='vertical-align:top'>{{include file=$prn_move_tpl}}</td></tr>
</table>
<br>
<table cellPadding='0' border=0 cellSpacing='0' width='96%' align=center  >
	<tr>
		<td colspan=2 class=top_left align=center>學年 學期</td>
		{{foreach from=$seme_ary key=grade_seme item=data}}
		<td class=top colspan=3 width={{$seme_width}}%>{{$data.seme_title}}</td>
		{{/foreach}}
	</tr>
	<tr>
		<td colspan=2 class=left>年 班 號</td>
		{{foreach from=$seme_ary key=grade_seme item=data}}
		<td colspan=3>{{$data.class_title}}</td>
		{{/foreach}}
	</tr>
	<tr>
		<td class=left>學習領域</td>
		<td>科目</td>
		{{foreach from=$seme_ary key=grade_seme item=data}}
		<td>節數</td>
		<td>百分制</td>
		<td>等第</td>
		{{/foreach}}
	</tr>
	{{foreach from=$all_score key=scope_name item=scope}}
		{{assign var=first value=1}}
		{{foreach from=$scope.sub_arys key=sub_name item=subs}}
			{{if $scope_name !='日常生活表現'}}
			<tr>
				{{if $first==1}}
					{{assign var=first value=10}}
					<td rowspan={{$scope.items}} class=left>{{$scope_name}}</td>
				{{/if}}
				<td>{{$sub_name}}</td>
				{{foreach from=$subs key=grade_seme item=sub}}
					<td>{{$sub.rate}}</td>
					<td>{{$sub.score}}</td>
					<td>{{$sub.level}}</td>
				{{/foreach}}
			</tr>
			{{/if}}
		{{/foreach}}
	{{/foreach}}
	{{foreach from=$all_score key=scope_name item=scope}}
		{{assign var=first value=1}}
		{{foreach from=$scope.sub_arys key=sub_name item=subs}}
			{{if $scope_name =='日常生活表現'}}
			<tr>
				{{if $first==1}}
					{{assign var=first value=10}}
					<td rowspan={{$scope.items}} class=left>{{$scope_name}}</td>
				{{/if}}
				<td></td>
				{{foreach from=$subs key=grade_seme item=sub}}
					<td>{{$sub.rate}}</td>
					<td>{{$sub.score}}</td>
					<td>{{$sub.level}}</td>
				{{/foreach}}
			</tr>
<tr>
<td colspan=2 class=left>導師</td>
{{foreach from=$seme_ary key=grade_seme item=data}}
<td colspan=3>{{$data.teacher_1}}</td>
{{/foreach}}										</tr>
<tr >
<td colspan=2 class=left>導師評語</td>
{{foreach from=$subs key=grade_seme item=sub}}
<td colspan=3 class=memo>{{$sub.memo}}</td>
{{/foreach}}
</tr>
			{{/if}}
		{{/foreach}}
	{{/foreach}}
</table>
<br>
<table cellPadding='0' border=0 cellSpacing='0' width='96%' style='text-align:center'  >
	<tr>
		<td class=empty width=25%>承辦人員</td>
		<td class=empty width=25%>註冊組長</td>
		<td class=empty width=25%>教務主任</td>
		<td class=empty width=25%>校長</td>
	</tr>
</table>

