{{* $Id: cchk_body.htm 5522 2009-07-09 04:48:15Z infodaes $ *}}

{{$break_page}}

<P ALIGN=CENTER STYLE="font-size:16pt; font-family: 標楷體, cursive;">{{$school_name}} {{$class_info.ch_year}} {{$class_info.c_seme}}<br>{{$default_title}}</p>
<DIV STYLE="font-size: 14pt; font-family: 標楷體, cursive; text-align:center;">班級：{{$class_info.c_year2}}{{$class_info.c_name}}　　座號：{{$stud.seme_num}}　　姓名：{{$stud.stud_name}}</DIV>

<!-- 出缺席統計 -->
{{if $stud_absent=="checked"}}
<br>
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>個人出缺席統計</u></b></td>
	</tr>
	<tr>
	<td align="center">
  {{$absent_print}}
	</td>
	</tr>
</table>

{{/if}}

<!--個人獎懲 -->
{{if $stud_reward=="checked"}}
<br>
{{foreach from=$f item=d key=i}}
{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['i']]=0;{{/php}}
{{/foreach}}
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>個人獎懲資料</u></b><br><small>統計時間：{{$seme_start_date}}~{{$seme_end_date}}</small></td>
	</tr>
	<tr>
	<td align="center">
<table width="100%">
{{if $stud_reward_detail=="checked"}}
<!-- 明細標題 -->
<tr class="title_sbody2">
<td align="center" width="50"><span style="font-size:10pt;">學年</span></td>
<td align="center" width="50"><span style="font-size:10pt;">學期</span></td>
<td align="center"><span style="font-size:10pt;">獎懲事由</span></td>
<td align="center"><span style="font-size:10pt;">獎懲類別</span></td>
<td align="center"><span style="font-size:10pt;">獎懲依據</span></td>
<td align="center" width="80"><span style="font-size:10pt;">獎懲生效日期</span></td>
<td align="center" width="80"><span style="font-size:10pt;">銷過日期</span></td>
</tr>
<!-- 明細標題結束 -->
<tr><td colspan="7"><hr size="2"></tr>
{{/if}}
{{foreach from=$reward_rows item=d}}
{{assign var=r_id value=$d.reward_kind}}
{{assign var=sel_year value=$d.reward_year_seme|@substr:0:-1}}
{{assign var=sel_seme value=$d.reward_year_seme|@substr:-1:1}}
{{assign var=k value=$d.reward_kind|@abs}}
{{if $d.reward_kind>0}}{{assign var=j value=0}}{{else}}{{assign var=j value=3}}{{/if}}
{{if $k==1}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+3]++;{{/php}}{{/if}}
{{if $k==2}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+3]+=2;{{/php}}{{/if}}
{{if $k==3}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+2]++;{{/php}}{{/if}}
{{if $k==4}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+2]+=2;{{/php}}{{/if}}
{{if $k==5}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+1]++;{{/php}}{{/if}}
{{if $k==6}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+1]+=2;{{/php}}{{/if}}
{{if $k==7}}{{php}}$this->_tpl_vars['t'][$this->_tpl_vars['j']+1]+=3;{{/php}}{{/if}}

{{if $stud_reward_detail=="checked"}}
<!-- 明細部分 -->
<tr class="title_sbody1">
<td align="center"><span style="font-size:10pt;">{{$sel_year}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$sel_seme}}</span></td>
<td align="left"><span style="font-size:10pt;">{{$d.reward_reason}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$reward_kind.$r_id}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$d.reward_base}}</span></td>
<td align="center"><span style="font-size:10pt;">{{$d.reward_date}}</span></td>
<td align="center"><span style="font-size:10pt;">{{if $r_id>0}}---{{elseif $d.reward_cancel_date=="0000-00-00"}}未銷過{{else}}{{$d.reward_cancel_date}}{{/if}}</span></td>
</tr>
<!-- 明細部分截止 -->
{{/if}}
{{/foreach}}
<tr>
<td colspan="7"><hr size="2"></td>
</tr>
<tr>
<td colspan="7">
<table width="100%">
<tr>
<td align="center">大功</td>
<td align="center">小功</td>
<td align="center">嘉獎</td>
<td align="center">大過</td>
<td align="center">小過</td>
<td align="center">警告</td>
</tr>
<tr>
{{foreach from=$f item=d key=i}}
{{assign var=tt value=$t.$i}}
<td align="center">{{$tt|@intval}}次</td>
{{/foreach}}
</tr>
</table>
</td>
</tr>
<tr>
<td colspan="7"><hr size="2"></td>
</tr>
</table>
	
	</td>
	</tr>
</table>
{{/if}}

<!--個人學期資料列印開始 -->
<!--日常生活表現 -->
{{if $stud_chk_data=="checked"}}
<br>
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>日常生活表現</u></b></td>
	</tr>
</table>
{{$chk_data}}
{{/if}}
<!-- 社團活動 -->
{{if $stud_club=="checked"}}
<br>
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>社團活動記錄</u></b></td>
	</tr>
</table>
<table border='2' cellpadding='3' cellspacing='0' width='100%' style="border-collapse:collapse;font-size:10pt" bordercolor="#111111">
	<TR>
		<td align="center" width="120">參加社團</td>
		{{if $stud_club_score=="checked" }}
		<td align="center" width="30">成績</td>
		{{/if}}
		<td align="center">教師評語</td>
		<td align="center">自我省思</td>
	</TR>
<!------ 顯示社團迴圈 ---->
{{foreach from=$club_detail item=club}}
  <tr>
    <td align="center">{{$club.association_name}}</td>
    {{if $stud_club_score=="checked" }}
    <td align="center">{{$club.score}}</td>
    {{/if}}
    <td>{{$club.description}}</td>
    <td>{{$club.stud_feedback}}</td>
  </tr>
{{/foreach}}
<!------ 結束顯示社團迴圈 ---->
</TABLE>
{{/if}}
<!-- end if $stud_club -->
{{if $stud_service=="checked"}}
<br>
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>服務學習記錄</u></b></td>
	</tr>
</table>
<table border='2' cellpadding='3' cellspacing='0' width='100%' style="border-collapse:collapse;font-size:10pt" bordercolor="#111111">
	<TR>
		<td align="center" width="80">日期</td>
		<td align="center">參加校內外公共服務學習事項及活動項目</td>
		<td align="center" width="70">時數</td>
		<td align="center" width="100">主辦單位</td>
		<td align="center">自我省思</td>
	</TR>
<!------ 顯示服務學習迴圈 ---->
{{foreach from=$service_detail item=service key=sn}}
  <tr>
  	<td align="center">{{$service.service_date}}</td>
    <td>【{{$service.item}}】{{$service.memo}}</td>
    <td align="center">{{$service.hour}}</td>
    <td align="center">{{$service.sponsor}}</td>
    <td>{{$service.feedback}}</td>
  </tr>
{{/foreach}}
<!------ 結束顯示服務學習迴圈 ---->
</TABLE>
<table border="0" width="100%">
 <tr>
 	<td>	本學期服務學習總時數共計<b> {{$HOURS}} </b>小時</td>
 	</tr>
</table>
{{/if}}
<!-- end if $stud_service -->
<!-- 幹部資料 -->
{{if $stud_leader=="checked"}}
<br>
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>幹部資料</u></b></td>
	</tr>
	<tr>
	<td align="center">
  {{$leader_print}}
	</td>
	</tr>
</table>
{{/if}}

<!-- 競賽記錄 -->
{{if $stud_race=="checked"}}
<br>
<table border="0" width="100%">
	<tr>
	 <td align="center"><b><u>個人競賽記錄</u></b></td>
	</tr>
	<tr>
	<td align="center">
  {{$race_print}}
	</td>
	</tr>
</table>
{{/if}}

<br>
<TABLE WIDTH=100% BORDER=0 CELLPADDING=4 CELLSPACING=0>
	<THEAD>
		<TR>
			<TD COLSPAN=3 style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 1.5pt;">
				<DIV ALIGN=CENTER><FONT SIZE=3>審核簽章</FONT></DIV>
			</TD>
			<TD style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;">
				<DIV ALIGN=CENTER><FONT SIZE=3>家長意見：</FONT></DIV>
			</TD>
		</TR>
	</THEAD>
	<TBODY>
		<TR>
			<TD WIDTH=17% style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 1.5pt;line-height: 8pt;">
				<DIV ALIGN=CENTER><FONT SIZE=1 STYLE="font-size: 10pt;">導 師</FONT></DIV>
			</TD>
			<TD WIDTH=17% style="border-style: solid; border-width: 1.5pt 0.75pt 0pt 0pt;line-height: 8pt;">
				<DIV ALIGN=CENTER><FONT SIZE=1 STYLE="font-size: 10pt;">{{$sign_3_title}}</FONT></DIV>
			</TD>
			<TD WIDTH=17% style="border-style: solid; border-width: 1.5pt 1.5pt 0pt 0pt;line-height: 8pt;">
				<DIV ALIGN=CENTER><FONT SIZE=1 STYLE="font-size: 10pt;">校 長</FONT></DIV>
			</TD>
			<TD ROWSPAN=2 WIDTH=49% VALIGN=BOTTOM style="border-style: solid; border-width: 1.5pt 1.5pt 1.5pt 0pt;">
				<DIV ALIGN=RIGHT><FONT SIZE=3>家長簽章</FONT></DIV>
			</TD>
		</TR>
		<TR style="height:60pt;">
			<TD style="border-style: solid; border-width: 0.75pt 0.75pt 1.5pt 1.5pt;">
				<DIV ALIGN=CENTER><BR></DIV>
			</TD>
			<TD style="border-style: solid; border-width: 0.75pt 0.75pt 1.5pt 0pt;">
				<DIV ALIGN=CENTER VALIGN=MIDDLE STYLE="margin-bottom: 0cm">
            {{if $img_3}}<IMG SRC="{{$img_3}}" ALIGN=CENTER WIDTH=48 HEIGHT=48 BORDER=0><BR CLEAR=LEFT>{{else}}<BR>{{/if}}
				</DIV>
			</TD>
			<TD style="border-style: solid; border-width: 0.75pt 1.5pt 1.5pt 0pt;">
				<DIV ALIGN=CENTER VALIGN=MIDDLE STYLE="margin-bottom: 0cm">
				{{if $img_1}}<IMG SRC="{{$img_1}}" ALIGN=CENTER WIDTH=48 HEIGHT=48 BORDER=0><BR CLEAR=LEFT>{{else}}<BR>{{/if}}
				</DIV>
			</TD>
		</TR>
	</TBODY>
</TABLE>
{{$default_txt}}