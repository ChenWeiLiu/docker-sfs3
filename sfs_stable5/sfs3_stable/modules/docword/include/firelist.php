<?php 
//文件開頭
function content_header() {
global $school_long_name,$page,$last_page,$page_count,$num_record,$QueryBeginDate,$QueryEndDate;

$ptemp = $num_record - (($page) * $page_count);
if ( $ptemp > 0)
	$less_record = $page_count;
else
	$less_record = $page_count+$ptemp;
	
$temp = explode ("-",$QueryBeginDate);
$btemp = sprintf ("%d年%d月%d日", $temp[0]-1911,$temp[1],$temp[2]);
$temp = explode ("-",$QueryEndDate);
$etemp = sprintf ("%d年%d月%d日", $temp[0]-1911,$temp[1],$temp[2]);
?>
<?php echo $school_long_name ?>公文銷毀清冊</span><span lang="EN-US" style="font-size:14.0pt;mso-bidi-font-size:10.0pt;mso-fareast-font-family:&quot;MS Gothic&quot;"><o:p>
</o:p>
</span></b></p>
<p class="MsoNormal" align="right" style="text-align:right"><span style="font-family:
新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">列印日期：民國<?php echo $etemp ?></span><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;"><o:p>
</o:p>
</span></p>
<table border="1" cellspacing="0" cellpadding="0" width="636" style="width:477.0pt;
 margin-left:1.4pt;border-collapse:collapse;border:none;mso-border-alt:solid windowtext .75pt;
 mso-padding-alt:0cm 1.4pt 0cm 1.4pt">
  <tr>
    <td width="52" style="width:38.8pt;border:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" align="center" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:
  auto;text-align:center"><span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">系統號</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="71" style="width:53.3pt;border:solid windowtext .75pt;border-left:
  none;mso-border-left-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" align="center" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:
  auto;text-align:center"><span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">收文日期</span><span lang="EN-US"><br>
      </span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">單位</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="90" style="width:67.3pt;border:solid windowtext .75pt;border-left:
  none;mso-border-left-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" align="center" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:
  auto;text-align:center"><span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">收文號</span><span lang="EN-US"><br>
      </span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">文別</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="279" style="width:209.6pt;border:solid windowtext .75pt;border-left:
  none;mso-border-left-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" align="center" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:
  auto;text-align:center"><span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">文</span> <span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">件</span> <span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">主</span> <span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">旨</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="72" style="width:54.0pt;border:solid windowtext .75pt;border-left:
  none;mso-border-left-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" align="center" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:
  auto;text-align:center"><span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">辦理單位</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="72" style="width:54.0pt;border:solid windowtext .75pt;border-left:
  none;mso-border-left-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" align="center" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:
  auto;text-align:center"><span style="font-family:新細明體;mso-ascii-font-family:
  &quot;Times New Roman&quot;">備註</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
  </tr>
<?php
}


//一般內容部份
function content_normal() {
global $doc1_id,$doc1_date,$doc1_word,$doc1_main,$doc1_unit_num1,$doc1_unit,$doc1_kind;
?>
  
<tr>
    <td width="52" style="width:38.8pt;border:solid windowtext .75pt;border-top:
  none;mso-border-top-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">900001<O:P>
      </O:P>
      </span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="71" style="width:53.3pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">89.12.30<br>
      </span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">台中縣政府</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="90" style="width:67.3pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">89</span><span style="font-family:
  &quot;MS Gothic&quot;;mso-ascii-font-family:&quot;Times New Roman&quot;">府教社字第</span><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">365050</span><span style="font-family:&quot;MS Gothic&quot;;mso-ascii-font-family:&quot;Times New Roman&quot;">號函</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="279" style="width:209.6pt;border-top:none;border-left:none;
  border-bottom:solid windowtext .75pt;border-right:solid windowtext .75pt;
  mso-border-top-alt:solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;
  padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span style="font-family:
  新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">高雄燈會</span><span lang="EN-US">-</span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">全國花燈競賽</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="72" style="width:54.0pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span style="font-family:
  新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">教學組</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="72" style="width:54.0pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt"><span lang="EN-US" style="font-size:12.0pt;font-family:
  新細明體;mso-hansi-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;
  mso-font-kerning:1.0pt;mso-ansi-language:EN-US;mso-fareast-language:ZH-TW;
  mso-bidi-language:AR-SA"><O:P>
      </O:P>
      </span>
      <p class="MsoNormal" style="text-align:justify;text-justify:inter-ideograph">&nbsp;<span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>

  </tr>

<?php
}

//內容結尾
function content_end() {
global $doc1_id,$doc1_date,$doc1_word,$doc1_main,$doc1_unit_num1,$doc1_unit;
?>
<tr>
    <td width="52" style="width:38.8pt;border:solid windowtext .75pt;border-top:
  none;mso-border-top-alt:solid windowtext .75pt;padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">900018<O:P>
      </O:P>
      </span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="71" style="width:53.3pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">89.12.30<br>
      </span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">台中縣政府</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="90" style="width:67.3pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">89</span><span style="font-family:
  &quot;MS Gothic&quot;;mso-ascii-font-family:&quot;Times New Roman&quot;">府教學字第</span><span lang="EN-US" style="mso-fareast-font-family:&quot;MS Gothic&quot;">365188</span><span style="font-family:&quot;MS Gothic&quot;;mso-ascii-font-family:&quot;Times New Roman&quot;">號函</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="279" style="width:209.6pt;border-top:none;border-left:none;
  border-bottom:solid windowtext .75pt;border-right:solid windowtext .75pt;
  mso-border-top-alt:solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;
  padding:0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span style="font-family:
  新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">八十九</span><span lang="EN-US">(</span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">二</span><span lang="EN-US">)</span><span style="font-family:新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">國小審定本議價流程，同意書、訂購數</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="72" style="width:54.0pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt">
      <p class="MsoNormal" style="mso-margin-top-alt:auto;mso-margin-bottom-alt:auto;
  text-align:justify;text-justify:inter-ideograph"><span style="font-family:
  新細明體;mso-ascii-font-family:&quot;Times New Roman&quot;">教科書</span><span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
    <td width="72" style="width:54.0pt;border-top:none;border-left:none;border-bottom:
  solid windowtext .75pt;border-right:solid windowtext .75pt;mso-border-top-alt:
  solid windowtext .75pt;mso-border-left-alt:solid windowtext .75pt;padding:
  0cm 1.4pt 0cm 1.4pt"><span lang="EN-US" style="font-size:12.0pt;font-family:
  新細明體;mso-hansi-font-family:&quot;Times New Roman&quot;;mso-bidi-font-family:&quot;Times New Roman&quot;;
  mso-font-kerning:1.0pt;mso-ansi-language:EN-US;mso-fareast-language:ZH-TW;
  mso-bidi-language:AR-SA"><O:P>
      </O:P>
      </span>
      <p class="MsoNormal" style="text-align:justify;text-justify:inter-ideograph">&nbsp;<span lang="EN-US" style="font-family:新細明體"><o:p>
      </o:p>
      </span></p>
    </td>
</tr>
<?php
}

//文件結尾
Function content_footer() {
?>
</table>

</body>
</html>
<?php
}

//分頁
function page_break() {
?>
<p class="MsoNormal"><span lang="EN-US" style="mso-bidi-font-size:12.0pt">&nbsp;<o:p>
</o:p>
</span></p>
<span lang="EN-US" style="font-size:12.0pt;font-family:&quot;Times New Roman&quot;;
mso-fareast-font-family:新細明體;mso-font-kerning:1.0pt;mso-ansi-language:EN-US;
mso-fareast-language:ZH-TW;mso-bidi-language:AR-SA"><br clear="all" style="mso-special-character:line-break;page-break-before:always">
</span>
<p class="MsoNormal">&nbsp;<span lang="EN-US" style="mso-bidi-font-size:12.0pt"><o:p>
</o:p>
</span></p>
<?php
}
?>
