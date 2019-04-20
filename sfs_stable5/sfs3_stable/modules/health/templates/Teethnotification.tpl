<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>口腔檢查通知單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=year_name value=$seme_class|@substr:0:-2}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<TABLE style="border-collapse: collapse; margin: auto; letter-spacing: -0.1em; font: 12pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 20pt;">
          <TD colSpan="10" style="font-size:16pt;"><span style="font-size: 14pt;">{{$school_data.sch_cname}}</span>　　<strong>口腔檢查通知單</strong></TD>
		</TR>
		<TR style="height: 15pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">親愛的家長：</TD>
		</TR>
		<TR style="height: 15pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">貴子女　<strong>{{$year_data.$year_name}}{{$class_data.$seme_class}}班 {{$seme_num}} 號 {{$health_data->stud_base.$sn.stud_name}}</strong></TD>
		</TR>
		<TR style="height: 30pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">經本次口腔檢查發現下列問題</TD>
		</TR>
        <TR style="height: 30pt">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="8">牙齒狀況</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="2">檢查結果</TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;"
		  >　</TD>
          <TD style="border-left: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;"
		  >齲齒數</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >缺牙數</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >已矯治</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >待拔牙</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >阻生牙</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >贅生牙</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;"
		  >總　數</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; text-align: left; vertical-align: top;"
		  colSpan="2" rowSpan="4">
		  <br>　{{if $dd.C1}}■{{else}}□{{/if}} 齲齒
		  <br>　{{if $dd.C2}}■{{else}}□{{/if}} 缺牙
		  <br>　{{if $dd.C4}}■{{else}}□{{/if}} 待拔牙
		  <br>　{{if $dd.C5}}■{{else}}□{{/if}} 阻生牙
		  <br>　{{if $dd.C6}}■{{else}}□{{/if}} 贅生牙
		  <br>　{{if $dd.checks.Ora1}}■{{else}}□{{/if}} 口腔衛生不良
		  <br>　{{if $dd.checks.Ora4}}■{{else}}□{{/if}} 齒列咬合不正
		  <br>　{{if $dd.checks.Ora5}}■{{else}}□{{/if}} 牙齦炎
		  <br>　{{if $dd.checks.Ora6}}■{{else}}□{{/if}} 口腔黏膜異常<br><br></TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;"
		  >恆　齒</TD>
          <TD style="border-left: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.N1|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.N2|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.N3|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.N4|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.N5|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.N6|@intval}}</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.NTotal|@intval}}</TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;"
		  >乳　齒</TD>
          <TD style="border-left: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.n1|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.n2|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.n3|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.n4|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.n5|@intval}}</TD>
          <TD style="border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.n6|@intval}}</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;"
		  >{{$dd.nTotal|@intval}}</TD>
		</TR>
        <TR style="HEIGHT: 50pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;"
		  >其他連<br>絡事項</TD>
          <TD style="border-left: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
          <TD style="border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
          <TD style="border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
          <TD style="border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
          <TD style="border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
          <TD style="border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;"
		  >　</TD>
		</TR>
		</TBODY>
	  </TABLE><br>
	  <span style="font-size: 14pt;">為維護貴子弟之健康，請帶至合格牙醫師處做進一步檢查，儘快做好矯治工作<br>，並輔導其注意口腔保健，養成餐後潔牙的好習慣。<br>
	  順頌　時祺<br>
	  </span>
	  <p style="font-size: 12pt; text-align:right;">台中縣立潭秀國民中學 健康中心　　敬啟　　{{$smarty.now|date_format:"%Y"}} 年 {{$smarty.now|date_format:"%m"}} 月 {{$smarty.now|date_format:"%d"}} 日</p>
	  <p style="border-bottom: dashed 4px;"></p>
	  <p style="font-size: 14pt; text-align:center;">口腔檢查及矯治狀況（回條）</p>
	  <p style="font-size: 14pt;"><strong>{{$year_data.$year_name}}{{$class_data.$seme_class}}班 {{$seme_num}} 號 {{$health_data->stud_base.$sn.stud_name}}</strong></p>
	  <p style="font-size: 14pt;">醫師檢查結果：</p><br>
	  <p style="font-size: 14pt;">醫師建議事項：</p><br>
	  <p style="font-size: 14pt;">家長連絡事項：</p><br><br>
	  <p style="font-size: 14pt; text-align: right;">家長簽名：　　　　　　　　日期：　　　　</p>
	  <p style="font-size: 12pt; text-align: center;">（請在牙齒矯治完成後，將本回條交給級任老師(導師)轉還健康中心彙整。）</p>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/foreach}}
</BODY></HTML>
