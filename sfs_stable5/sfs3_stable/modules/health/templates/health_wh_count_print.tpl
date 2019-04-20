<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>生長發育統計表</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
<TABLE style="border-collapse: collapse; margin: auto; font: 12pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
{{assign var=year value=$smarty.post.year_seme|@substr:0:3}}
        <TR style="height: 30pt; text-align: center;">
          <TD style="font-size:12pt;" colSpan="7">{{$school_data.sch_cname}} {{$year|intval}}學年度 第{{$smarty.post.year_seme|@substr:-1:1}}學期 生長發育統計表</TD>
		</TR>
        <TR style="height: 30pt; text-align: center;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="2">年級</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">男　　生</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">女　　生</TD>
		</TR>
{{php}}$this->_tpl_vars['v'][9][9]=$this->_tpl_vars['v'][1][9]+$this->_tpl_vars['v'][2][9];{{/php}}
        <TR style="height: 30pt; text-align: center;">
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >身高(cm)</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >體重(kg)</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >BMI(kg/m<sup>2</sup>)</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >身高(cm)</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >體重(kg)</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >BMI(kg/m<sup>2</sup>)</TD>
		</TR>
{{foreach from=$data_arr item=dd key=i}}
        <TR style="height: 30pt; text-align: right;">
		  <TD style="border-right: windowtext 0.75pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{$i}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >{{$dd.1.havg}}　</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >{{$dd.1.wavg}}　</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >{{$dd.1.bavg}}　</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >{{$dd.2.havg}}　</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" 
          >{{$dd.2.wavg}}　</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >{{$dd.2.bavg}}　</TD>
		</TR>
{{/foreach}}
        <TR style="height: 90pt; text-align: right;">
          <TD style="font-size:12pt; border-top: windowtext 1.5pt solid; text-align: center;" colSpan="7">承辦人　　　　　　　組長　　　　　　　主任　　　　　　　校長　　　　　　　</TD>
		</TR>
  </TBODY>
</TABLE>
</BODY></HTML>
