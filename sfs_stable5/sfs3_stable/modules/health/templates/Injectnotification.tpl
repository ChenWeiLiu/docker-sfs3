<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>國小學童常規疫苗接種同意書及評估單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=year_name value=$seme_class|@substr:0:-2}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{php}}
$this->_tpl_vars['bd']=explode("-",$this->_tpl_vars['health_data']->stud_base[$this->_tpl_vars['sn']]['stud_birthday']);
{{/php}}
<TABLE style="border-collapse: collapse; margin: auto; letter-spacing: -0.1em; font: 12pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 20pt;">
          <TD colSpan="10" style="font-size:16pt;"><strong>國小學童常規疫苗接種同意書及評估單</strong></TD>
		</TR>
		<TR style="height: 15pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">親愛的家長：<br>　　本校近日將安排貴子弟完成下列疫苗接種，請先協助完成下列評估項目並簽名後，交給小朋友帶回學校，以便確認貴子弟可於當日順利完成接種。</TD>
		</TR>
		<TR style="height: 15pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">■基本資料<br><span style="font-size:12pt;">&nbsp;<strong><u> {{$school_data.sch_cname}} </u></strong>，學童姓名：<strong><u> {{$health_data->stud_base.$sn.stud_name}} </u></strong>，班級：<strong><u> {{$year_data.$year_name|@substr:0:2}} </u></strong>年<strong><u> {{$class_data.$seme_class}} </u></strong>班<strong><u> {{$seme_num}} </u></strong>號 <br>出生日期：<u> <strong>{{$bd.0-1911}}</strong> </u>年<u> <strong>{{$bd.1}}</strong> </u>月<u> <strong>{{$bd.2}}</strong> </u>日， 聯絡電話：<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u></span></TD>
		</TR>
		<TR style="height: 15pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">■擬接種疫苗種類<br>
		  <TABLE BORDER="0" style="WIDTH:100%; font-size: 14pt;"><TR><TD>
		  &nbsp;{{if $smarty.post.inj.4}}■{{else}}□{{/if}}減量破傷風白喉非細胞性百日咳混合疫苗（Tdap）<br>&nbsp;{{if $smarty.post.inj.7}}■{{else}}□{{/if}}麻疹、腮腺炎、德國麻疹混合疫苗<br>&nbsp;{{if $smarty.post.inj.8}}■{{else}}□{{/if}}流感疫苗<br>&nbsp;□其他：<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u>
		  </TD><TD style="vertical-align: top;">
		  {{if $smarty.post.inj.3}}■{{else}}□{{/if}}小兒麻痺口服疫苗<br>{{if $smarty.post.inj.5}}■{{else}}□{{/if}}日本腦炎疫苗
		  </TD></TR></TABLE>
		  </TD>
		</TR>
		<TR style="height: 15pt; font-size: 14pt;">
		  <TD colSpan="10" style="text-align: left;">■健康評估</TD>
		</TR>
        <TR style="height: 15pt">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          rowSpan="2">評估內容</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="2" nowrap>請勾選有或無</TD>
		</TR>
        <TR style="height: 15pt">
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" >有</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" >無</TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >1.以前預防接種後是否有嚴重特殊反應，如發高燒（40.5  ℃以上）、抽痙、昏迷、休克、哭鬧3小時以上…等。</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >2.是否曾對同一類疫苗或對疫苗的任何成分(如雞蛋、明膠及新黴素)有過敏反應。</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
 		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >3.是否有嚴重心臟、肝臟、腎臟、白血病、癌症…等病史。</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >4.一年內有否抽痙狀況。</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >5.現在身體有無任何病徵，如發燒（38.5℃以上）、嘔吐、呼吸困難…等或正服用八寶粉、驚風散、水楊酸（阿斯匹靈）等藥物及最近三天內有無就醫、吃藥等情形。</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >6.最近三個月曾否肌肉注射免疫球蛋白(免疫血清)或免疫抑制劑。<br> &nbsp; 最近六個月是否曾輸過血或接受靜脈注射血液製品。<br> &nbsp; 最近十一個月內是否曾靜脈注射高劑量免疫球蛋白。</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid;" ></TD>
		</TR>
        <TR style="HEIGHT: 25pt">
          <TD style="border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid; border-right: windowtext 0.75pt solid; text-align: left;"
		  >7.接種當日之體溫：額溫<u> &nbsp; &nbsp; &nbsp; &nbsp; </u>℃ 或 耳溫<u> &nbsp; &nbsp; &nbsp; &nbsp; </u>℃</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" ></TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" ></TD>
		</TR>
		</TBODY>
	  </TABLE><br>
	  <span style="font-size: 14pt;">■備註</span><br><span style="font-size: 12pt;">※服用未經衛生署核准及醫師處方之八寶粉、驚風散等含重金屬之藥物，容易發生慢性鉛中毒導致腦症及死亡，應告知家長勿服用。<br>※患有心血管疾病、腎臟、肝臟等重大傷病者，請醫師檢查後再決定是否接種，但接種要有醫師醫囑。<br>※以上評估結果請按各項疫苗之禁忌，決定是否給予接種。<br>※本評估表紀錄後請妥善保存六年。</span>
	  <p style="font-size: 14pt;">醫師評估後是否接種：□是 &nbsp; □否</p>
	  <TABLE BORDER="0" style="WIDTH:100%;"><TR><TD>
	  <p style="font-size: 14pt;">醫師簽名：<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u></p>
	  <p style="font-size: 14pt;">接種單位：<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u></p><br>
	  </TD><TD style="text-align: right;">
	  <p style="font-size: 14pt;">同意接種家長簽名：<u> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </u></p>
	  <p style="font-size: 14pt;">接種日期：<u> &nbsp; &nbsp; &nbsp; </u>年<u> &nbsp; &nbsp; </u>月<u> &nbsp; &nbsp; &nbsp;</u>日</p><br>
	  </TD></TR></TABLE>
	</TD>
  </TR>
  </TBODY>
</TABLE>
{{/foreach}}
</BODY></HTML>
