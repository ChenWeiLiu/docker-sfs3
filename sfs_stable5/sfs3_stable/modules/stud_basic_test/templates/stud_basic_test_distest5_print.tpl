{{* $Id: stud_basic_test_distest5_print.tpl 8373 2015-03-30 06:44:32Z chiming $ *}}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>{{$curr_year}}學年度國民中學在校學習領域成績證明單</TITLE>
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
          <TD colSpan="2" style="font: 16pt 細明體; font-weight: bold; border: windowtext 3pt solid; letter-spacing: -0.1em;">一般身分學生</TD>
		  <TD colSpan="7"></TD>
		</TR>
        <TR style="height: 40pt;">
          <TD colSpan="9" style="font: 18pt 標楷體; font-weight: bold;"><span style="font-family: Times New Roman; font-weight: bold;">{{$curr_year}}</span>學年度國民中學在校學習領域成績證明單</TD>
		</TR>
        <TR style="height: 30pt; font-size: 12pt;">
          <TD style="text-align: left;" colSpan="5">就讀國中： <span style="font-size: 16pt; letter-spacing: -0.1em;">{{$sch_arr.sch_cname}}</span></TD>
		  <TD style="text-align: left;" colSpan="4">就讀國中代碼： <span style="font: 18pt Times New Roman;">{{$sch_arr.sch_id}}</span></TD>
		</TR>
{{assign var=y value=$seme_class|@substr:0:-2}}
        <TR style="height: 30pt;font-size: 12pt;">
          <TD style="text-align: left;" colSpan="5">班級： <span style="font: 18pt Times New Roman;">{{$y}}</span> 年 <span style="font: 18pt Times New Roman;">{{$seme_class|@substr:-2:2|@intval}}</span> 班　　姓名： <span style="font-size: 16pt;">{{$stud_data.$sn.stud_name}}</span></TD>
          <TD style="text-align: left;" colSpan="4">身分證統一編號： <span style="font: 18pt Times New Roman">{{$stud_data.$sn.stud_person_id}}</span></TD>
		</TR>
        <TR style="height: 27pt;font-size: 12pt; background-color: #EEEEEE;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; text-align: center;" 
          colSpan="2">領域（學科）</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >八年級<br>上學期<br><span style="font-family: Times New Roman;">(A)</span></TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >八年級<br>下學期<br><span style="font-family: Times New Roman;">(B)</span></TD>
          <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          >九年級<br>上學期<br><span style="font-family: Times New Roman;">(C)</span></TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2">領域(學科)學期<br>成績平均<span style="font-family: Times New Roman;">(A+B+C)/3</span></TD>
		  <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2">領域(學科)學期成<br>績全校排名百分比</TD>
		</TR>
{{foreach from=$s_arr item=sl key=j}}
{{if $j==10}}
        <TR style="height: 28pt; font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2" rowSpan="2">{{$sl}}</TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >八年級<br>上學期<br><span style="font-family: Times New Roman;">(D)</span></TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >八年級<br>下學期<br><span style="font-family: Times New Roman;">(E)</span></TD>
          <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          >九年級<br>上學期<br><span style="font-family: Times New Roman;">(F)</span></TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2" rowSpan="2">---</TD>
		  <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" 
          colSpan="2" rowSpan="2">---</TD>
		</TR>
{{/if}}
{{if $j<>3}}
        <TR style="height: 28pt;font-size: 14pt;">
          {{if $j<>10}}
		  <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; font-size: 12pt;" 
          colSpan="2">{{$sl}}</TD>
		  {{/if}}
		  {{assign var=sp_cal value=$stud_data.$sn.sp_cal}}
		  {{if $stud_data.$sn.stud_kind==2 || $stud_data.$sn.stud_kind==7}}{{assign var=sp_cal value=1}}{{/if}}
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          ><span style="font-family: Times New Roman;">{{if $sp_cal && $stud_data.$sn.enable0==""}}---{{else}}{{$rowdata.$sn.0.$j.score}}{{/if}}</span></TD>
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          ><span style="font-family: Times New Roman;">{{if $sp_cal && $stud_data.$sn.enable1==""}}---{{else}}{{$rowdata.$sn.1.$j.score}}{{/if}}</span></TD>
          <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;" 
          ><span style="font-family: Times New Roman;">{{if $sp_cal && $stud_data.$sn.enable2==""}}---{{else}}{{$rowdata.$sn.2.$j.score}}{{/if}}</span></TD>
          {{if $j<>10}}
          <TD style="width:11%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          colSpan="2"><span style="font-family: Times New Roman;">{{$rowdata.$sn.3.$j.score|string_format:"%.2f"}}</span></TD>
		  <TD style="width:11%; border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid;" 
          colSpan="2"><span style="font: 16pt Times New Roman; font-weight: bold;">{{$rowdata.$sn.3.$j.pr}}</span></TD>
		  {{/if}}
		</TR>
{{/if}}
{{/foreach}}
        <TR style="height: 28pt;font-size: 14pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; font-size: 12pt;" 
          colSpan="2">在校3學期成績<br>總平均<br><span style="font-family: Times New Roman;">(D+E+F)/3</span></TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="3"><span style="font-family: Times New Roman;">{{$rowdata.$sn.3.10.score|string_format:"%.2f"}}</span></TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; font-size: 12pt;" 
          colSpan="2">在校3學期成績<br>總平均<br>全校排名百分比</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2"><span style="font: 16pt Times New Roman; font-weight: bold;">{{$rowdata.$sn.3.10.pr}}</span></TD>
		</TR>
        <TR style="height: 40pt;font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="2">備註</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-left: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; text-align: left;" 
          colSpan="7">1.學期成績平均計算至小數點後第二位，小數點後第三位四捨五入。<br>2.所指「八年級上學期、八年級下學期、九年級上學期」即「國中二年級上學期、二年級下學期及三年級上學期」。<br>3.領域（學科）之排列順序由上至下依序為國語文、英語、數學、社會、自然與生活科技、藝術與人文、健康與體育及綜合活動，順序不得任意變動。</TD>
		</TR>
        <TR style="height: 0pt;">
          <TD style="border-top: windowtext 1.5pt solid;" colSpan="9"></TD>
		</TR>
		<TR>
		  <TD colSpan="9" style="height: 20pt; text-align: left;"></TD>
		</TR>
        <TR style="height: 40pt;font-size: 12pt;">
          <TD style="border-top: windowtext 0.75pt dashed; text-align: right; color: CCCCCC;" colSpan="2">(就讀國中戳章)</TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="3"></TD>
          <TD style="border-top: windowtext 0.75pt dashed; text-align: left;" colSpan="2"></TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="2"></TD>
		</TR>
        <TR>
          <TD>&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
          <TD width="11%">&nbsp;</TD>
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
