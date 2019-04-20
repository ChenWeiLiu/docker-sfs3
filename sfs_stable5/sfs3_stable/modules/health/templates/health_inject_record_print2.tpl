<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>預防接種及應補種針劑清單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
<TABLE style="border-collapse: collapse; margin: auto; font: 10pt 標楷體,標楷體,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
{{assign var=year value=$smarty.post.year_seme|@substr:0:3}}
        <TR style="height: 25pt; text-align: center;">
          <TD style="font-size:12pt;" colSpan="20">{{$school_data.sch_cname}} {{$year|intval}}學年度 第{{$smarty.post.year_seme|@substr:-1:1}}學期 預防接種及應補種針劑清單</TD>
		</TR>
        <TR style="height: 15pt; text-align: center;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="3">班<br>級</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="3">座<br>號</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="3">姓名</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          rowSpan="2" colSpan="2">預防<br>接種<br>卡影<br>本</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="15">應補種及實際補種疫苗劑次</TD>
		</TR>
        <TR style="height: 15pt; text-align: center;">
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >卡<br>介<br>苗</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">Ｂ型肝<br>炎疫苗</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="4">小兒麻<br>痺疫苗</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">白喉、破<br>傷風混合<br>疫苗</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">日本腦<br>炎疫苗</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >Ｍ<BR>Ｍ<BR>Ｒ<BR>疫<br>苗</TD>
		</TR>
        <TR style="height: 15pt; text-align: center;">
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >已<br><br>繳</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >未<br><br>繳</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >一<br>　<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>一<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>二<br>劑</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>三<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>一<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>二<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>三<br>劑</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >追<br>　<br>加</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>一<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>二<br>劑</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >追<br>　<br>加</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>一<br>劑</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >第<br>二<br>劑</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >追<br>　<br>加</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >一<br>　<br>劑</TD>
		</TR>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.inject}}
        <TR style="height: 15pt; text-align: right;">
		  <TD style="border-left: windowtext 1.5pt solid; border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{$class_name}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{$seme_num}}</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{$health_data->stud_base.$sn.stud_name}}</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $dd.0.0.times>0}}ˇ{{/if}}</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $dd.0.0.times<1}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][1][$this->_tpl_vars['inject_arr']['times'][1]-$this->_tpl_vars['dd'][0][1]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][2][$this->_tpl_vars['inject_arr']['times'][2]-$this->_tpl_vars['dd'][0][2]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][2][$this->_tpl_vars['inject_arr']['times'][2]-$this->_tpl_vars['dd'][0][2]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][2][$this->_tpl_vars['inject_arr']['times'][2]-$this->_tpl_vars['dd'][0][2]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(4,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack4']=1;
else
	$this->_tpl_vars['lack4']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack4==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][4][$this->_tpl_vars['inject_arr']['times'][4]-$this->_tpl_vars['dd'][0][4]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][4][$this->_tpl_vars['inject_arr']['times'][4]-$this->_tpl_vars['dd'][0][4]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][4][$this->_tpl_vars['inject_arr']['times'][4]-$this->_tpl_vars['dd'][0][4]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][5][$this->_tpl_vars['inject_arr']['times'][5]-$this->_tpl_vars['dd'][0][5]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][5][$this->_tpl_vars['inject_arr']['times'][5]-$this->_tpl_vars['dd'][0][5]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][5][$this->_tpl_vars['inject_arr']['times'][5]-$this->_tpl_vars['dd'][0][5]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}ˇ{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][7][$this->_tpl_vars['inject_arr']['times'][7]-$this->_tpl_vars['dd'][0][7]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}ˇ{{/if}}</TD>
		</TR>
{{/foreach}}
{{/foreach}}
        <TR style="height: 30pt; text-align: right;">
          <TD style="font-size:12pt; border-top: windowtext 1.5pt solid; text-align: center;" colSpan="23">承辦人　　　　　　　組長　　　　　　　主任　　　　　　　校長　　　　　　　</TD>
		</TR>
  </TBODY>
</TABLE>
</BODY></HTML>
