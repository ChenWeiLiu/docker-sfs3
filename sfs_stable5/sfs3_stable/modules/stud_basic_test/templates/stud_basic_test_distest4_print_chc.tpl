{{* $Id: stud_basic_test_distest4_print_chc.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>免試入學在校表現證明單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{foreach from=$student_sn item=d key=seme_class}}
{{foreach from=$d item=sn key=site_num}}
<TABLE style="border-collapse: collapse; margin: auto; font: 12pt Times New Roman,標楷體,標楷體; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 16pt Times New Roman,標楷體,標楷體;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 20pt;">
          <TD colSpan="20" style="font-size: 20pt; font-weight: bold;">免試入學在校表現證明單</TD>
		</TR>
		<TR>
		  <TD colSpan="20" style="height: 40pt; text-align: left;">一、學生基本資料</TD>
		</TR>
        <TR style="height: 30pt; font-size: 10pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">學生姓名</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; font-size: 18pt;" 
          colSpan="7">{{$stud_data.$sn.stud_name}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">性　別</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; font: 13pt Times New Roman,標楷體,標楷體;" 
          colSpan="7">{{if $stud_data.$sn.stud_sex==1}}<span style="font-size: 20pt;">■</span>男　<span style="font-size: 20pt;">□</span>女{{else}}<span style="font-size: 20pt;">□</span>男　<span style="font-size: 20pt;">■</span>女{{/if}}</TD>
		</TR>
        <TR style="height: 30pt;font-size: 10pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">就讀學校</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; font-size: 13pt;" 
          colSpan="7">{{$sch_arr.sch_cname}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">生　日</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; font: 13pt Times New Roman,標楷體,標楷體;" 
          colSpan="7">{{$stud_data.$sn.stud_birthday}}</TD>
		</TR>
        <TR style="height: 30pt;font-size: 10pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          colSpan="3">就讀年班座號</TD>
{{assign var=y value=$seme_class|@substr:0:-2}}
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid; font: 13pt Times New Roman,標楷體,標楷體;" 
          colSpan="7"><span style="font-size: 18pt;">{{$y-6}} </span> 年 <span style="font-size: 18pt;"> {{$seme_class|@substr:-2:2|@intval}} </span> 班座號：<span style="font-size: 18pt;">{{$site_num}}</span></TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          colSpan="3">身分證字號</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font: 16pt Times New Roman,標楷體,標楷體;" 
          colSpan="7">{{$stud_data.$sn.stud_person_id}}</TD>
		</TR>
		<TR>
		  <TD colSpan="20" style="height: 40pt; text-align: left;">二、在校表現 &nbsp;( 三學期五大科及平均 )</TD>
		</TR>
        <TR style="height: 27pt;font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; text-align: center;" 
          colSpan="7">項目</TD>
          <TD style="width:10%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="3">國二<br>上學期</TD>
          <TD style="width:10%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="3">國二<br>下學期</TD>
          <TD style="width:10%; border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="3">國三<br>上學期</TD>
		  <TD style="width:10%; border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid;" 
          colSpan="4">平均</TD>
		</TR>
{{foreach from=$s_arr item=sl key=j}}
        <TR style="height: 28pt;font-size: 12pt;">
          <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; text-align: left;" 
          colSpan="7">　{{$sl}}</TD>
{{foreach from=$seme_arr item=si key=i}}
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid;" 
          colSpan="3">{{$rowdata.$sn.$i.avg.$j.score}}</TD>
{{/foreach}}
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; font-size: 16pt; font-weight: bold;" 
          colSpan="4">{{$rowdata.$sn.9991.avg.$j.score}}</TD>
		</TR>
{{/foreach}}
        <TR style="height: 30pt;font-size: 12pt;">
{{if $stud_data.$sn.stud_sex==1}}{{assign var=num value=$sex1}}{{else}}{{assign var=num value=$sex2}}{{/if}}
		  <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" colSpan="2">總平均</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" colSpan="2">{{$rowdata.$sn.9991.avg.avg.score}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" colSpan="2">全三年<br>級總人<br>數</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" colSpan="2">{{$sex0}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" colSpan="2">全三年<br>級前百<br>分比</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" colSpan="2">{{$rowdata.$sn.9991.avg.avg.pr}}%</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" colSpan="2">三年級<br>{{if $stud_data.$sn.stud_sex==1}}男{{else}}女{{/if}}生總<br>人數</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" colSpan="2">{{$num}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" colSpan="2">三年級<br>{{if $stud_data.$sn.stud_sex==1}}男{{else}}女{{/if}}生前<br>百分比</TD>
{{assign var=t value=$rowdata.$sn.5.10.score*7+$rowdata.$sn.5.3.score}}
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; font-size: 16pt; font-weight: bold;" colSpan="2">{{$rowdata.$sn.9991.avg.avg.pr2}}%</TD>
	</TR>
        <TR style="height: 0pt;">
          <TD colSpan="20"></TD>
		</TR>
		<TR>
		  <TD colSpan="20" style="height: 40pt; text-align: left;">三、在校表現採計方式</TD>
		</TR>
		<TR>
		  <TD colSpan="20" style="text-align: left; font-size: 12pt;">
		  <TABLE style="width:100%;">
		  <TR>
		    <TD style="vertical-align: top;" nowrap>1.採計方式：</TD>
		    <TD>採計三學期（國二上、下學期、國三上學期）五大科目定期評量之各自平均（取至小數第2位）以及平均總分。</TD>
		  </TR>
		  <TR>
		    <TD style="vertical-align: top;" nowrap>2.呈現方式：</TD>
		    <TD>各國中應分別呈現學生在本國語文、英語、數學、自然與生活科技、社會等五大科目總成績所占同年級百分比。</TD>
		  </TR>
		  </TABLE>
		</TR>
        <TR style="height: 40pt;font-size: 12pt;">
          <TD style="border-top: windowtext 0.75pt dashed; text-align: right;" colSpan="4">國中審核<br>人員核章</TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="8"></TD>
          <TD style="border-top: windowtext 0.75pt dashed; text-align: left;" colSpan="4">國　　　中<br>教務處核章</TD>
          <TD style="border-top: windowtext 0.75pt dashed;" colSpan="4"></TD>
		</TR>
        <TR>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
          <TD width="5%">&nbsp;</TD>
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
