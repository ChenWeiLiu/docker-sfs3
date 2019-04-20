{{* $Id: health_sight_ntu_list.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>立體感異常學生名冊</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=i value=1}}
{{foreach from=$health_data->stud_base item=d key=sn name=rows}}
{{assign var=year_name value=$d.seme_class|@substr:0:1}}
{{assign var=class_name value=$d.seme_class|@substr:-2:2}}
{{assign var=class_name value=$class_name|@intval}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $i % 35 == 1}}
<TABLE style="border-collapse: collapse; margin: auto; font: 14pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 10pt 新細明體,新細明體,serif;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
		<TR style="font: 16pt 新細明體,新細明體,serif;">
		  <TD colSpan="9">立體感檢查異常學生名冊</TD>
		</TR>
		<TR>
		  <TD colSpan="3" style="text-align: center;">{{$school_data.sch_cname}} {{$school_data.sch_id}}</TD>
		  <TD colSpan="6" style="text-align: right;">列印時間：{{$smarty.now|date_format:"%Y-%m-%d %H:%M:%S"}}</TD>
		</TR>
        <TR style="height: 14pt;text-align: left;">
          <TD style="border-top: windowtext 1.5pt solid;">年級</TD>
          <TD style="border-top: windowtext 1.5pt solid;">班級</TD>
          <TD style="border-top: windowtext 1.5pt solid;">座號</TD>
          <TD style="border-top: windowtext 1.5pt solid;">姓名</TD>
          <TD style="border-top: windowtext 1.5pt solid;">性別</TD>
          <TD style="border-top: windowtext 1.5pt solid;">身份證字號</TD>
          <TD style="border-top: windowtext 1.5pt solid;">診斷</TD>
          <TD style="border-top: windowtext 1.5pt solid;">其他診斷</TD>
          <TD style="border-top: windowtext 1.5pt solid;">就診醫院</TD>
		</TR>
{{/if}}
        <TR style="height: 14pt;text-align: left;">
          <TD style="border-top: windowtext 0.75pt solid;">{{$year_data[$year_name]}}</TD>
          <TD style="border-top: windowtext 0.75pt solid;">{{$class_data[$d.seme_class]}}</TD>
          <TD style="border-top: windowtext 0.75pt solid;">{{$d.seme_num}}</TD>
          <TD style="border-top: windowtext 0.75pt solid;">{{$d.stud_name}}</TD>
          <TD style="border-top: windowtext 0.75pt solid;">{{if $d.stud_sex==1}}男{{else}}女{{/if}}</TD>
          <TD style="border-top: windowtext 0.75pt solid;">{{$d.stud_person_id}}</TD>
          <TD style="border-top: windowtext 0.75pt solid;"></TD>
          <TD style="border-top: windowtext 0.75pt solid;"></TD>
          <TD style="border-top: windowtext 0.75pt solid;"></TD>
		</TR>
{{if $i % 35 == 0}}
        <TR style="height: 20pt;text-align: left;">
          <TD colSpan="9" style="border-top: windowtext 1.5pt solid;"></TD>
		</TR>
        <TR style="height: 60pt;text-align: left;">
          <TD colSpan="9" style="border-top: windowtext 1.5pt solid; vertical-align: top;">
		  <TABLE WIDTH=100% style="margin:auto;"><TR style="font-size: 10pt;">
		  <TD WIDTH=25%>承辦人</TD>
		  <TD WIDTH=25%>組長</TD>
		  <TD WIDTH=25%>主任</TD>
		  <TD WIDTH=25%>校長</TD>
		  </TR></TABLE>
		  </TD>
		</TR>
		</TBODY>
	  </TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/if}}
{{assign var=i value=$i+1}}
{{/foreach}}
{{if $i % 35 != 1}}
        <TR style="height: 20pt;text-align: left;">
          <TD colSpan="9" style="border-top: windowtext 1.5pt solid;"></TD>
		</TR>
        <TR style="height: 60pt;text-align: left;">
          <TD colSpan="9" style="border-top: windowtext 1.5pt solid; vertical-align: top;">
		  <TABLE WIDTH=100% style="margin:auto;"><TR style="font-size: 10pt;">
		  <TD WIDTH=25%>承辦人</TD>
		  <TD WIDTH=25%>組長</TD>
		  <TD WIDTH=25%>主任</TD>
		  <TD WIDTH=25%>校長</TD>
		  </TR></TABLE>
		  </TD>
		</TR>
		</TBODY>
	  </TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/if}}
</BODY></HTML>
