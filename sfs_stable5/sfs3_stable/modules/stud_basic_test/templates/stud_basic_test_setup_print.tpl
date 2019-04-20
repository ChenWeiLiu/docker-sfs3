{{* $Id: stud_basic_test_setup_print.tpl 7176 2013-03-01 03:54:11Z chiming $ *}}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>102學年度國民中學在校學習領域成績證明單-特殊身分學生</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{foreach from=$student_sn item=d key=seme_class name=rows}}
{{foreach from=$d item=sn key=site_num}}
<TABLE style="border-collapse: collapse; margin: auto; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 16pt 標楷體;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 30pt;">
          <TD colSpan="3" style="font: 16pt 細明體; font-weight: bold; border: windowtext 3pt solid; letter-spacing: -0.1em;">特殊身分學生</TD>
		  <TD colSpan="8"></TD>
		</TR>
        <TR style="height: 30pt;">
          <TD colSpan="11" style="font: 18pt 標楷體; font-weight: bold;"><span style="font-family: Times New Roman; font-weight: bold;">102</span>學年度國民中學在校學習領域成績證明單</TD>
		</TR>
        <TR style="height: 18pt; font-size: 12pt;">
          <TD style="text-align: left;" colSpan="6">就讀國中： <span style="font-size: 16pt; letter-spacing: -0.1em;">{{$sch_arr.sch_cname}}</span></TD>
		  <TD style="text-align: left;" colSpan="5">就讀國中代碼： <span style="font: 18pt Times New Roman;">{{$sch_arr.sch_id}}</span></TD>
		</TR>
{{assign var=y value=$seme_class|@substr:0:-2}}
        <TR style="height: 18pt;font-size: 12pt;">
          <TD style="text-align: left;" colSpan="6">班級： <span style="font: 18pt Times New Roman;">{{$y}}</span> 年 <span style="font: 18pt Times New Roman;">{{$seme_class|@substr:-2:2|@intval}}</span> 班　　姓名： <span style="font-size: 16pt;">{{$stud_data.$sn.stud_name}}</span></TD>
          <TD style="text-align: left;" colSpan="5">身分證統一編號： <span style="font: 18pt Times New Roman">{{$stud_data.$sn.stud_person_id}}</span></TD>
		</TR>
		<TR style="height: 6pt;"><TD colSpan="11"></TD></TR>
        <TR style="font-size: 12pt;">
          <TD style="text-align: left; vertical-align: top; letter-spacing: -0.1em;" colSpan="2">考生特殊身分別：</TD>
		  <TD style="text-align: left; letter-spacing: -0.1em;" colSpan="9">{{if $stud_data.$sn.sp_kind==2}}■{{else}}□{{/if}} 原住民(持文化及語言能力證明)　{{if $stud_data.$sn.sp_kind==1}}■{{else}}□{{/if}} 原住民(未持文化及語言能力證明)<br>{{if $stud_data.$sn.kind==7}}■{{else}}□{{/if}} 境外優秀科學技術人才子女　{{if $stud_data.$sn.kind==2}}■{{else}}□{{/if}} 政府赴國外工作人員子女　{{if $stud_data.$sn.kind==3}}■{{else}}□{{/if}} 蒙藏生<br>{{if $stud_data.$sn.sp_kind=='C'}}■{{else}}□{{/if}} 身心障礙生　(以上請就考生最有利之特殊身分勾選並限勾選一項)</TD>
		</TR>
        <TR style="height: 27pt;font-size: 12pt; background-color: #EEEEEE;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; text-align: center;" 
          colSpan="2" nowrap>領域（學科）</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >八上<br><span style="font-family: Times New Roman;">(A)</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >八下<br><span style="font-family: Times New Roman;">(B)</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          >九上<br><span style="font-family: Times New Roman;">(C)</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; letter-spacing: -0.1em;" 
          colSpan="2">領域(學科)<br>學期成績平均<br><span style="font-family: Times New Roman;">( A + B + C ) / 3</span></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; letter-spacing: -0.1em;" 
          colSpan="2">領域(學科)<br>學期成績全校<br>排名百分比</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; letter-spacing: -0.1em;" 
          colSpan="2" nowrap>特殊身分加後之<br>領域(學科)學期<br>成績全校排名百分比</TD>
		</TR>
{{foreach from=$s_arr item=sl key=j}}
{{if $j==10}}
        <TR style="height: 20pt; font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2" rowSpan="2">{{$sl}}</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;" 
          >八上<span style="font-family: Times New Roman;">(D)</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;" 
          >八下<span style="font-family: Times New Roman;">(E)</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;" 
          >九上<span style="font-family: Times New Roman;">(F)</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="4" rowSpan="2">---</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" 
          colSpan="2" rowSpan="3">---</TD>
		</TR>
{{/if}}
{{if $j<>3}}
        <TR style="height: 18pt;font-size: 12pt;">
          {{if $j<>10}}
		  <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; font-size: 12pt;" 
          colSpan="2">{{$sl}}</TD>
		  {{/if}}
		  {{assign var=sp_cal value=$stud_data.$sn.sp_cal}}
		  {{if $stud_data.$sn.kind==2 || $stud_data.$sn.kind==7}}{{assign var=sp_cal value=1}}{{/if}}
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          ><span style="font-family: Times New Roman;">{{if $sp_cal && $stud_data.$sn.enable0==""}}---{{else}}{{$rowdata.$sn.0.$j.score}}{{/if}}</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          ><span style="font-family: Times New Roman;">{{if $sp_cal && $stud_data.$sn.enable1==""}}---{{else}}{{$rowdata.$sn.1.$j.score}}{{/if}}</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;" 
          ><span style="font-family: Times New Roman;">{{if $sp_cal && $stud_data.$sn.enable2==""}}---{{else}}{{$rowdata.$sn.2.$j.score}}{{/if}}</span></TD>
          {{if $j<>10}}
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          colSpan="2"><span style="font-family: Times New Roman;">{{$rowdata.$sn.3.$j.score|string_format:"%.2f"}}</span></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;" 
          colSpan="2"><span style="font: 14pt Times New Roman; font-weight: bold;">{{$rowdata.$sn.3.$j.pr}}</span></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;" 
          colSpan="2"><span style="font: 14pt Times New Roman; font-weight: bold;">{{$rowdata.$sn.3.$j.ppr}}</span></TD>
		  {{/if}}
		</TR>
{{/if}}
{{/foreach}}
        <TR style="height: 28pt;font-size: 14pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 12pt; letter-spacing: -0.1em;" 
          colSpan="2">在校3學期成績<br>總平均<br><span style="font-family: Times New Roman;">( D + E + F ) / 3</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="3"><span style="font-family: Times New Roman;">{{$rowdata.$sn.3.10.score|string_format:"%.2f"}}</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 12pt; letter-spacing: -0.1em;" 
          colSpan="2">在校3學期成績<br>總平均<br>全校排名百分比</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2"><span style="font: 16pt Times New Roman; font-weight: bold;">{{$rowdata.$sn.3.10.pr}}</span></TD>
		</TR>
        <TR style="height: 20pt; font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2" rowSpan="2">特殊身分加分後<br>之學期成績總平均</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;" 
          >八上<span style="font-family: Times New Roman;">(G)</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;" 
          >八下<span style="font-family: Times New Roman;">(H)</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;" 
          >九上<span style="font-family: Times New Roman;">(I)</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 10pt;"
          colSpan="6" rowSpan="2">---</TD>
		</TR>
		<TR style="height: 18pt;font-size: 12pt;">
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">{{$rowdata.$sn.0.10.pscore|string_format:"%.2f"}}</span></TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">{{$rowdata.$sn.1.10.pscore|string_format:"%.2f"}}</span></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;"><span style="font-family: Times New Roman;">{{$rowdata.$sn.2.10.pscore|string_format:"%.2f"}}</span></TD>
		</TR>
        <TR style="height: 28pt;font-size: 14pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 12pt; letter-spacing: -0.1em;" 
          colSpan="2">特殊身分<span style="text-decoration: underline;">加分後</span><br>之在校3學期成<br>績總平均<br><span style="font-family: Times New Roman;">( G + H + I ) / 3</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="3"><span style="font-family: Times New Roman;">{{$rowdata.$sn.3.10.pscore|string_format:"%.2f"}}</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 12pt; letter-spacing: -0.1em;" 
          colSpan="4">特殊身分<span style="text-decoration: underline;">加分後</span><br>之在校3學期成<br>績總平均<br>全校排名百分比</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2"><span style="font: 16pt Times New Roman; font-weight: bold;">{{$rowdata.$sn.3.10.ppr}}</span></TD>
		</TR>
        <TR style="height: 40pt;font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2">備註</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-left: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; text-align: left; letter-spacing: -0.1em;" 
          colSpan="9">1.學期成績平均計算至小數點後第二位，小數點後第三位四捨五入。<br>2.所指「八年級上學期、八年級下學期、九年級上學期」即「國中二年級上學期、二年級下學期及三年級上學期」。<br>3.特殊身分生加分優待依各特殊身分加分法規規定辦理，本成績證明單係以考生特殊身分別為原住民持有文化及語言能力證明(加分比率35％)為範例：即 G = D * (1+0.35)， H = E * (1+0.35)， I = F * (1+0.35)。<br>4.領域（學科）之排列順序由上至下依序為國語文、英語、數學、社會、自然與生活科技、藝術與人文、健康與體育及綜合活動，順序不得任意變動。</TD>
		</TR>
        <TR style="height: 0pt;">
          <TD style="border-top: windowtext 1.5pt solid;" colSpan="11"></TD>
		</TR>
		<TR>
		  <TD colSpan="11" style="height: 5pt; text-align: left;"></TD>
		</TR>
        <TR style="height: 20pt;font-size: 12pt;">
          <TD style="border-top: windowtext 0.75pt dashed; text-align: right; color: CCCCCC;" colSpan="2">(就讀國中戳章)</TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="3"></TD>
          <TD style="border-top: windowtext 0.75pt dashed; text-align: left;" colSpan="3"></TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="3"></TD>
		</TR>
        <TR style="height: 1pt;">
          <TD>&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
          <TD width="9%">&nbsp;</TD>
		</TR>
		</TBODY>
	  </TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/foreach}}
{{/foreach}}
</BODY></HTML>
