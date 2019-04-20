{{* $Id: list_rep.tpl 5677 2009-10-02 08:06:18Z hami $ *}}
{{assign var=weeks value=$this->get_week($smarty.request.year_seme)}}
{{if $smarty.request.week_num}}
{{assign var=week_num value=$smarty.request.week_num}}
{{else}}
{{assign var=week_num value=$this->getCurrweek()}}
{{/if}}
{{assign var=year_seme_arr value=$this->get_year_seme()}}
{{if $smarty.request.year_seme}}
{{assign var=year_seme value=$smarty.request.year_seme}}
{{else}}
{{assign var=year_seme value=$year_seme_arr|@array_keys|@end}}
{{/if}}

{{assign var=title_arr value=$this->get_title()}}
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=Big5">
</HEAD>
<BODY LANG="zh-TW" DIR="LTR">
<P ALIGN=CENTER STYLE="margin-bottom: 0cm"><FONT FACE="AR PL ShanHeiSun Uni, serif"><FONT SIZE=4 STYLE="font-size: 16pt"><SPAN LANG="en-US"><B><FONT SIZE=4 STYLE="font-size: 16pt"><B>{{$SCHOOL_BASE.sch_cname_ss}}</B></FONT>
<FONT SIZE=4 STYLE="font-size: 16pt"><B>{{$smarty.request.open_date|@addslashes}}</B></FONT>
</B></SPAN></FONT></FONT><FONT SIZE=4 STYLE="font-size: 16pt"><B>報告事項列表</B></FONT></P>
<P STYLE="margin-bottom: 0cm"><BR>
</P>
<ul>
{{foreach from=$this->get_all($year_seme,$smarty.request.open_date,1) item=arr}}
	<li><p>{{$title_arr[$arr.teacher_sn].title_name}}  {{$arr.name}} -- {{$arr.title}}</P>
	<P>{{$arr.content}}</P>
	</li>
	{{/foreach}}
</ul>
</BODY>
</HTML>
