<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<!-- $Id: body2.tpl 5310 2009-01-10 07:57:56Z hami $ -->
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=big5">
	<TITLE>{{$school_long_name}}{{$curr_year}}
學年度 第 {{$curr_seme}} 學期學生一覽表</TITLE>
	<META NAME="GENERATOR" CONTENT="OpenOffice.org 1.1.2  (Linux)">
	<META NAME="CREATED" CONTENT="20041004;20402000">
	<META NAME="CHANGED" CONTENT="20041004;20542300">
	<STYLE>
	<!--
		@page { size: 21cm 29.7cm; margin: 2cm }
		P { margin-bottom: 0.21cm }
		TD P { margin-bottom: 0.21cm }
		TH P { margin-bottom: 0.21cm; font-style: italic }
	-->
	</STYLE>
</HEAD>
<BODY LANG="zh-TW" DIR="LTR">
<P ALIGN=CENTER STYLE="margin-bottom: 0cm"><FONT FACE="標楷體"><FONT SIZE=3>{{$school_long_name}}{{$curr_year}}
學年度 第 {{$curr_seme}} 學期 {{$title_class}} 學生一覽表</FONT></FONT>
</P>
<P ALIGN=RIGHT STYLE="margin-bottom: 0cm"><FONT FACE="標楷體"><FONT SIZE=3>填報日期 : 中華民國
{{$today}}</FONT></FONT></P>
<TABLE WIDTH=100% BORDER=1 BORDERCOLOR="#000000" CELLPADDING=4 CELLSPACING=0>
	<COL WIDTH=18*>
	<COL WIDTH=31*>
	<COL WIDTH=13*>
	<COL WIDTH=31*>
	<COL WIDTH=32*>
	<COL WIDTH=24*>
	<COL WIDTH=20*>
	<COL WIDTH=87*>
	<THEAD>
		<TR>
			<TH WIDTH=7%>
				<P><FONT FACE="標楷體"><FONT SIZE=2>學號</FONT></FONT></P>
			</TH>
			<TH WIDTH=12%>
				<P><FONT FACE="標楷體"><FONT SIZE=2>姓名</FONT></FONT></P>
			</TH>
			<TH WIDTH=5%>
				<P><FONT FACE="標楷體"><FONT SIZE=2>性別</FONT></FONT></P>
			</TH>
			<TH WIDTH=12%>
				<P><FONT SIZE=2><FONT FACE="標楷體">身分證字號</FONT></FONT></P>
			</TH>
			<TH WIDTH=12%>
				<P><FONT FACE="Times New Roman"><FONT SIZE=2><SPAN LANG="en-US">(</SPAN></FONT></FONT><FONT FACE="標楷體"><FONT SIZE=2>西元</FONT></FONT><FONT FACE="Times New Roman"><FONT SIZE=2><SPAN LANG="en-US">)</SPAN></FONT></FONT><FONT FACE="標楷體"><FONT SIZE=2>出生</FONT></FONT><FONT FACE="Times New Roman"><FONT SIZE=2><SPAN LANG="en-US"><BR></SPAN></FONT></FONT><FONT FACE="標楷體"><FONT SIZE=2>年月日</FONT></FONT></P>
			</TH>
			<TH WIDTH=9%>
				<P><FONT FACE="標楷體"><FONT SIZE=2>入學</FONT></FONT><FONT FACE="Times New Roman"><FONT SIZE=2><SPAN LANG="en-US"><BR></SPAN></FONT></FONT><FONT FACE="標楷體"><FONT SIZE=2>時間</FONT></FONT></P>
			</TH>
			<TH WIDTH=8%>
				<P><FONT FACE="標楷體"><FONT SIZE=2>入學</FONT></FONT><FONT FACE="Times New Roman"><FONT SIZE=2><SPAN LANG="en-US"><BR></SPAN></FONT></FONT><FONT FACE="標楷體"><FONT SIZE=2>資格</FONT></FONT></P>
			</TH>
			<TH WIDTH=34%>
				<P><FONT FACE="標楷體"><FONT SIZE=2>戶籍地址</FONT></FONT></P>
			</TH>
		</TR>
	</THEAD>
	<TBODY>
	{{foreach from=$rowdata key=seme_class item=seme_class_data}}
	{{foreach from=$seme_class_data item=data}}
		<TR VALIGN=TOP>
			<TD WIDTH=7%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_id}}</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=12%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_name}}</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=5%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{if $data.stud_sex==1}}男{{else}}女{{/if}}</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=12%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_person_id}}</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=12%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_birthday}}</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=9%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_study_year}}-9-1</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=8%>
				<P ALIGN=CENTER><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_mschool_name}}</FONT></FONT>
				</P>
			</TD>
			<TD WIDTH=34%>
				<P ALIGN=LEFT><FONT FACE="標楷體"><FONT SIZE=2>{{$data.stud_addr_1}}</FONT></FONT>
				</P>
			</TD>
		</TR>
	{{/foreach}}
	{{/foreach}}
	</TBODY>
</TABLE>
<P ALIGN=LEFT STYLE="margin-bottom: 0cm"><BR>
</P>
<P ALIGN=LEFT STYLE="margin-bottom: 0cm"><FONT SIZE=2><FONT FACE="標楷體">注意事項</FONT></FONT><FONT FACE="Times New Roman"><SPAN LANG="en-US"><FONT SIZE=2><FONT FACE="標楷體">:
</FONT></FONT></SPAN></FONT>
</P>
<OL>
	<LI><P ALIGN=LEFT STYLE="margin-bottom: 0cm"><FONT FACE="標楷體"><FONT SIZE=2>本名冊應填造一式二份，一份存校，一份函報縣市政府。</FONT></FONT></P>
	<LI><P ALIGN=LEFT STYLE="margin-bottom: 0cm"><FONT FACE="標楷體"><FONT SIZE=2>入學資格欄應填明畢業或肄業學校名稱及年級。</FONT></FONT></P>
	<LI><P ALIGN=LEFT STYLE="margin-bottom: 0cm"><FONT FACE="標楷體"><FONT SIZE=2>轉入學生亦用本表並分年級別裝訂。</FONT></FONT></P>
	<LI><P ALIGN=LEFT STYLE="margin-bottom: 0cm"><FONT FACE="標楷體"><FONT SIZE=2>本名冊應於學生入學後一個月內函報。</FONT></FONT></P>
</OL>
</BODY>
</HTML>