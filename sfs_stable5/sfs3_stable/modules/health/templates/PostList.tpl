<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>視力不良通知單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->stud_base.$sn}}
{{assign var=year_name value=$seme_class|@substr:0:-2}}
{{assign var=i value=$i+1}}
{{if $i % 12==1}}
{{assign var=j value=0}}
<TABLE style="border-collapse: collapse; margin: auto; font: 14pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 12pt 標楷體,標楷體,serif;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
{{/if}}
{{assign var=j value=$j+1}}
{{if $j % 2==1}}
        <TR>
{{/if}}
			<TD style="text-align:left;font-size:10pt;">{{$school_data.sch_addr}}<br>{{$school_data.sch_cname}}健康中心　寄<br>學生：{{$class_data.$seme_class}}班{{$seme_num}}號　{{$dd.stud_name}}<span style="font-size:12pt;"><br><br>{{if $smarty.post.re==2}}{{$dd.stud_name}}{{else}}{{$dd.guardian_name}}{{/if}}　啟<br><br>{{$dd.stud_addr_2}}<br><br></span></TD>
{{if $j % 2==0}}
		</TR>
{{/if}}
{{if $i % 12==0 || $i==count($smarty.post.student_sn)}}
		</TBODY>
	  </TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/if}}
{{/foreach}}
</BODY></HTML>
