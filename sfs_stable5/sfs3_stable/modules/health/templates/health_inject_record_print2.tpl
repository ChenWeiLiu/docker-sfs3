<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>�w�����ؤ����ɺذw���M��</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
<TABLE style="border-collapse: collapse; margin: auto; font: 10pt �з���,�з���,serif; page-break-after: always;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
{{assign var=year value=$smarty.post.year_seme|@substr:0:3}}
        <TR style="height: 25pt; text-align: center;">
          <TD style="font-size:12pt;" colSpan="20">{{$school_data.sch_cname}} {{$year|intval}}�Ǧ~�� ��{{$smarty.post.year_seme|@substr:-1:1}}�Ǵ� �w�����ؤ����ɺذw���M��</TD>
		</TR>
        <TR style="height: 15pt; text-align: center;">
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-left: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="3">�Z<br>��</TD>
          <TD style="border-right: windowtext 0.75pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="3">�y<br>��</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          rowSpan="3">�m�W</TD>
          <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          rowSpan="2" colSpan="2">�w��<br>����<br>�d�v<br>��</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-top: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="15">���ɺؤι�ڸɺج̭]����</TD>
		</TR>
        <TR style="height: 15pt; text-align: center;">
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >�d<br>��<br>�]</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">�Ы��x<br>���̭]</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="4">�p���<br>���̭]</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">�ճ�B�}<br>�˭��V�X<br>�̭]</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          colSpan="3">�饻��<br>���̭]</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid;" 
          >��<BR>��<BR>��<BR>��<br>�]</TD>
		</TR>
        <TR style="height: 15pt; text-align: center;">
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >�w<br><br>ú</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br><br>ú</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >�@<br>�@<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�@<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�G<br>��</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�T<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�@<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�G<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�T<br>��</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >�l<br>�@<br>�[</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�@<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�G<br>��</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >�l<br>�@<br>�[</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�@<br>��</TD>
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 1.5pt solid;" 
          >��<br>�G<br>��</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >�l<br>�@<br>�[</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 1.5pt solid;" 
          >�@<br>�@<br>��</TD>
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
          >{{if $dd.0.0.times>0}}��{{/if}}</TD>
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $dd.0.0.times<1}}��{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][1][$this->_tpl_vars['inject_arr']['times'][1]-$this->_tpl_vars['dd'][0][1]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}��{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][2][$this->_tpl_vars['inject_arr']['times'][2]-$this->_tpl_vars['dd'][0][2]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}��{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][2][$this->_tpl_vars['inject_arr']['times'][2]-$this->_tpl_vars['dd'][0][2]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}��{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][2][$this->_tpl_vars['inject_arr']['times'][2]-$this->_tpl_vars['dd'][0][2]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}��{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}��{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}��{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}��{{/if}}</TD>
{{php}}
if (!in_array(4,$this->_tpl_vars['inject_arr']['lack'][3][$this->_tpl_vars['inject_arr']['times'][3]-$this->_tpl_vars['dd'][0][3]['times']]))
	$this->_tpl_vars['lack4']=1;
else
	$this->_tpl_vars['lack4']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack4==0}}��{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][4][$this->_tpl_vars['inject_arr']['times'][4]-$this->_tpl_vars['dd'][0][4]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}��{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][4][$this->_tpl_vars['inject_arr']['times'][4]-$this->_tpl_vars['dd'][0][4]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}��{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][4][$this->_tpl_vars['inject_arr']['times'][4]-$this->_tpl_vars['dd'][0][4]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}��{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][5][$this->_tpl_vars['inject_arr']['times'][5]-$this->_tpl_vars['dd'][0][5]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}��{{/if}}</TD>
{{php}}
if (!in_array(2,$this->_tpl_vars['inject_arr']['lack'][5][$this->_tpl_vars['inject_arr']['times'][5]-$this->_tpl_vars['dd'][0][5]['times']]))
	$this->_tpl_vars['lack2']=1;
else
	$this->_tpl_vars['lack2']=0;
{{/php}}
		  <TD style="border-right: windowtext 0.75pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack2==0}}��{{/if}}</TD>
{{php}}
if (!in_array(3,$this->_tpl_vars['inject_arr']['lack'][5][$this->_tpl_vars['inject_arr']['times'][5]-$this->_tpl_vars['dd'][0][5]['times']]))
	$this->_tpl_vars['lack3']=1;
else
	$this->_tpl_vars['lack3']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack3==0}}��{{/if}}</TD>
{{php}}
if (!in_array(1,$this->_tpl_vars['inject_arr']['lack'][7][$this->_tpl_vars['inject_arr']['times'][7]-$this->_tpl_vars['dd'][0][7]['times']]))
	$this->_tpl_vars['lack1']=1;
else
	$this->_tpl_vars['lack1']=0;
{{/php}}
		  <TD style="border-right: windowtext 1.5pt solid; border-bottom: windowtext 0.75pt solid; text-align: center;" 
          >{{if $lack1==0}}��{{/if}}</TD>
		</TR>
{{/foreach}}
{{/foreach}}
        <TR style="height: 30pt; text-align: right;">
          <TD style="font-size:12pt; border-top: windowtext 1.5pt solid; text-align: center;" colSpan="23">�ӿ�H�@�@�@�@�@�@�@�ժ��@�@�@�@�@�@�@�D���@�@�@�@�@�@�@�ժ��@�@�@�@�@�@�@</TD>
		</TR>
  </TBODY>
</TABLE>
</BODY></HTML>