{{* $Id: stud_basic_test_distest3_print.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>99中投區高中職免試入學在校表現證明單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{foreach from=$student_sn item=d key=seme_class name=rows}}
{{foreach from=$d item=sn key=site_num}}
<TABLE style="border-collapse: collapse; margin: auto; font: 12pt Times New Roman,標楷體,標楷體; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 16pt Times New Roman,標楷體,標楷體;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 20pt;">
          <TD colSpan="8" style="font-size: 20pt; font-weight: bold;">中投區99學年度高中高職免試入學<br>在校表現證明單</TD>
		</TR>
		<TR>
		  <TD colSpan="8" style="height: 40pt; text-align: left;">一、學生基本資料</TD>
		</TR>
        <TR style="height: 30pt; font-size: 10pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >學生姓名</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; font-size: 18pt;" 
          colSpan="3">{{$stud_data.$sn.stud_name}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >性　別</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; font: 13pt Times New Roman,標楷體,標楷體;" 
          colSpan="3">{{if $stud_data.$sn.stud_sex==1}}<span style="font-size: 20pt;">■</span>男　<span style="font-size: 20pt;">□</span>女{{else}}<span style="font-size: 20pt;">□</span>男　<span style="font-size: 20pt;">■</span>女{{/if}}</TD>
		</TR>
        <TR style="height: 30pt;font-size: 10pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >就讀學校</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; font-size: 13pt;" 
          colSpan="3">{{$sch_arr.sch_cname}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >生　日</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; font: 13pt Times New Roman,標楷體,標楷體;" 
          colSpan="3">{{$stud_data.$sn.stud_birthday}}</TD>
		</TR>
        <TR style="height: 30pt;font-size: 10pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >就讀班級</TD>
{{assign var=y value=$seme_class|@substr:0:-2}}
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid; font: 13pt Times New Roman,標楷體,標楷體;" 
          colSpan="3"><span style="font-size: 18pt;">{{$y-6}} </span> 年 <span style="font-size: 18pt;"> {{$seme_class|@substr:-2:2|@intval}} </span> 班座號：<span style="font-size: 18pt;">{{$site_num}}</span></TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >身分證字號</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font: 16pt Times New Roman,標楷體,標楷體;" 
          colSpan="3">{{$stud_data.$sn.stud_person_id}}</TD>
		</TR>
		<TR>
		  <TD colSpan="8" style="height: 40pt; text-align: left;">二、在校表現</TD>
		</TR>
        <TR style="height: 27pt;font-size: 14pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; text-align: left;" 
          colSpan="3">　項目</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >國二<br>上學期</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >國二<br>下學期</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >國三<br>上學期</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          >平均</TD>
		  <TD style="width:12.5%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          >PR值</TD>
		</TR>
{{foreach from=$s_arr item=sl key=j}}
        <TR style="height: 28pt;font-size: 14pt;">
{{if $j==1}}
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; text-align: left;" 
          rowSpan="3">　語文<br>　領域</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; text-align: left;" 
          colSpan="2">　{{$sl}}</TD>
{{elseif $j==2 || $j==3}}
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; text-align: left;" 
          colSpan="2">　{{$sl}}</TD>
{{else}}
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; text-align: left;" 
          colSpan="3">　{{$sl}}</TD>
{{/if}}
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          >{{$rowdata.$sn.0.$j.score}}</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          >{{$rowdata.$sn.1.$j.score}}</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          >{{$rowdata.$sn.2.$j.score}}</TD>
          <TD style="width:12.5%; border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          >{{$rowdata.$sn.3.$j.score|string_format:"%.2f"}}</TD>
		  <TD style="width:12.5%; border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; font-size: 16pt; font-weight: bold;" 
          >{{$rowdata.$sn.3.$j.pr}}</TD>
		</TR>
{{/foreach}}
        <TR style="height: 0pt;">
          <TD style="border-top: windowtext 1.5pt solid;" colSpan="8"></TD>
		</TR>
		<TR>
		  <TD colSpan="8" style="height: 40pt; text-align: left;">三、在校表現採計方式</TD>
		</TR>
		<TR>
		  <TD colSpan="8" style="text-align: left; font-size: 12pt;">
		  <TABLE style="width:100%;">
		  <TR>
		    <TD style="vertical-align: top;" nowrap>1.採計方式：</TD>
		    <TD>採計三學期（國二上、下學期、國三上學期）七大領域之學期成績（取至小數第2位）。</TD>
		  </TR>
		  <TR>
		    <TD style="vertical-align: top;" nowrap>2.呈現方式：</TD>
		    <TD>各國中應分別呈現學生在語文領域、數學領域、自然與生活科技領域、社會領域、健康與體育領域、藝術與人文領域、綜合活動領域等八項百分等級（PR值）。</TD>
		  </TR>
		  </TABLE>
		</TR>
        <TR style="height: 40pt;font-size: 12pt;">
          <TD style="border-top: windowtext 0.75pt dashed; text-align: right;" colSpan="2">國中審核<br>人員核章</TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="3"></TD>
          <TD style="border-top: windowtext 0.75pt dashed; text-align: left;" colSpan="2">國　　　中<br>教務處核章</TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="3"></TD>
		</TR>
        <TR>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
          <TD width="12.5%">&nbsp;</TD>
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
