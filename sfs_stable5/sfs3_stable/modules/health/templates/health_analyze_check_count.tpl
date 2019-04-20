{{* $Id: health_analyze_check_count.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
{{assign var=h value=100}}
{{assign var=anum value=$bnum+$gnum}}
<table cellspacing="0" cellpadding="0"><tr><td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="3">科別</td>
<td rowspan="3">檢查名稱 \ 統計</td>
<td colspan="6">檢查項目結果發現異狀</td>
<td colspan="5">複檢就醫矯治追蹤情形</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="2">男</td>
<td colspan="2">女</td>
<td colspan="2">合計＊</td>
<td>複檢正<br>常☆</td>
<td>複檢異<br>常△</td>
<td>未就醫╳</td>
<td>就醫率</td>
<td>備註﹝及其他<br>異常項目﹞</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>人數</td>
<td>％</td>
<td>人數</td>
<td>％</td>
<td>人數</td>
<td>％</td>
<td>人數</td>
<td>人數</td>
<td>人數</td>
<td>％</td>
<td></td>
</tr>
<tr style="background-color: white;">
<td rowspan="11" style="text-align: center;">眼</td>
<td>視力不良</td>
{{if $bnum!=$rowdata.1.Oph.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Oph.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Oph.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Oph.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Oph.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Oph.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Oph.1.0+$rowdata.2.Oph.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Oph.1.0-$rowdata.2.Oph.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td style="text-align: right;">近視</td>
</tr>
<tr style="background-color: white;">
<td style="text-align: right;">遠視</td>
</tr>
<tr style="background-color: white;">
<td style="text-align: right;">散光</td>
</tr>
<tr style="background-color: white;">
<td style="text-align: right;">弱視</td>
</tr>
<tr style="background-color: white;">
<td>辨色力異常</td>
{{if $bnum!=$rowdata.1.Oph.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Oph.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Oph.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Oph.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Oph.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Oph.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Oph.2.0+$rowdata.2.Oph.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Oph.2.0-$rowdata.2.Oph.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>斜視</td>
{{if $bnum!=$rowdata.1.Oph.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Oph.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Oph.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Oph.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Oph.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Oph.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Oph.3.0+$rowdata.2.Oph.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Oph.3.0-$rowdata.2.Oph.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>睫毛倒插</td>
{{if $bnum!=$rowdata.1.Oph.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Oph.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Oph.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Oph.4.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Oph.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Oph.4.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Oph.4.0+$rowdata.2.Oph.4.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Oph.4.0-$rowdata.2.Oph.4.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>眼球震顫</td>
{{if $bnum!=$rowdata.1.Oph.5.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Oph.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Oph.5.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Oph.5.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Oph.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Oph.5.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Oph.5.0+$rowdata.2.Oph.5.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Oph.5.0-$rowdata.2.Oph.5.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>眼瞼下垂</td>
{{if $bnum!=$rowdata.1.Oph.6.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Oph.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Oph.6.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Oph.6.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Oph.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Oph.6.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Oph.6.0+$rowdata.2.Oph.6.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Oph.6.0-$rowdata.2.Oph.6.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: #F0F0F0;">
<td rowspan="11" style="text-align: center;">耳鼻喉</td>
<td>聽力異常</td>
{{if $bnum!=$rowdata.1.Ent.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.1.0+$rowdata.2.Ent.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.1.0-$rowdata.2.Ent.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>疑似中耳炎</td>
{{if $bnum!=$rowdata.1.Ent.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.2.0+$rowdata.2.Ent.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.2.0-$rowdata.2.Ent.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>耳道畸形</td>
{{if $bnum!=$rowdata.1.Ent.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.3.0+$rowdata.2.Ent.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.3.0-$rowdata.2.Ent.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>唇顎裂</td>
{{if $bnum!=$rowdata.1.Ent.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.4.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.4.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.4.0+$rowdata.2.Ent.4.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.4.0-$rowdata.2.Ent.4.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td></tr>
<tr style="background-color: #F0F0F0;">
<td>構音異常</td>
{{if $bnum!=$rowdata.1.Ent.5.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.5.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.5.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.5.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.5.0+$rowdata.2.Ent.5.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.5.0-$rowdata.2.Ent.5.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>耳前　管</td>
{{if $bnum!=$rowdata.1.Ent.6.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.6.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.6.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.6.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.6.0+$rowdata.2.Ent.6.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.6.0-$rowdata.2.Ent.6.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>耵聹栓塞</td>
{{if $bnum!=$rowdata.1.Ent.7.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.7.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.7.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.7.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.7.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.7.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.7.0+$rowdata.2.Ent.7.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.7.0-$rowdata.2.Ent.7.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>慢性鼻炎</td>
{{if $bnum!=$rowdata.1.Ent.8.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.8.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.8.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.8.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.8.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.8.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.8.0+$rowdata.2.Ent.8.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.8.0-$rowdata.2.Ent.8.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>過敏性鼻炎</td>
{{if $bnum!=$rowdata.1.Ent.9.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.9.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.9.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.9.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.9.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.9.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.9.0+$rowdata.2.Ent.9.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.9.0-$rowdata.2.Ent.9.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td></tr>
<tr style="background-color: #F0F0F0;">
<td>扁桃腺腫大</td>
{{if $bnum!=$rowdata.1.Ent.10.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ent.10.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ent.10.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ent.10.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ent.10.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ent.10.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ent.10.0+$rowdata.2.Ent.10.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ent.10.0-$rowdata.2.Ent.10.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: white;">
<td rowspan="4" style="text-align: center;">頭頸</td>
<td>斜頸</td>
{{if $bnum!=$rowdata.1.Hea.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Hea.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Hea.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Hea.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Hea.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Hea.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Hea.1.0+$rowdata.2.Hea.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Hea.1.0-$rowdata.2.Hea.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>甲狀腺腫</td>
{{if $bnum!=$rowdata.1.Hea.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Hea.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Hea.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Hea.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Hea.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Hea.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Hea.2.0+$rowdata.2.Hea.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Hea.2.0-$rowdata.2.Hea.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>淋巴腺腫大</td>
{{if $bnum!=$rowdata.1.Hea.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Hea.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Hea.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Hea.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Hea.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Hea.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Hea.3.0+$rowdata.2.Hea.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Hea.3.0-$rowdata.2.Hea.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: #F0F0F0;">
<td rowspan="5" style="text-align: center;">胸部</td>
<td>胸廓異常</td>
{{if $bnum!=$rowdata.1.Pul.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Pul.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Pul.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Pul.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Pul.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Pul.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Pul.1.0+$rowdata.2.Pul.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Pul.1.0-$rowdata.2.Pul.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>

</tr>
<tr style="background-color: #F0F0F0;">
<td>心雜音</td>
{{if $bnum!=$rowdata.1.Pul.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Pul.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Pul.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Pul.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Pul.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Pul.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Pul.2.0+$rowdata.2.Pul.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Pul.2.0-$rowdata.2.Pul.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>心律不整</td>
{{if $bnum!=$rowdata.1.Pul.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Pul.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Pul.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Pul.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Pul.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Pul.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Pul.3.0+$rowdata.2.Pul.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Pul.3.0-$rowdata.2.Pul.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>呼吸聲異常</td>
{{if $bnum!=$rowdata.1.Pul.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Pul.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Pul.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Pul.4.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Pul.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Pul.4.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Pul.4.0+$rowdata.2.Pul.4.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Pul.4.0-$rowdata.2.Pul.4.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: white;">
<td rowspan="6" style="text-align: center;">脊柱四肢</td>
<td>脊柱側彎</td>
{{if $bnum!=$rowdata.1.Spi.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Spi.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Spi.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Spi.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Spi.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Spi.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Spi.1.0+$rowdata.2.Spi.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Spi.1.0-$rowdata.2.Spi.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>多併指</td>
{{if $bnum!=$rowdata.1.Spi.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Spi.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Spi.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Spi.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Spi.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Spi.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Spi.2.0+$rowdata.2.Spi.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Spi.2.0-$rowdata.2.Spi.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>青蛙肢</td>
{{if $bnum!=$rowdata.1.Spi.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Spi.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Spi.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Spi.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Spi.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Spi.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Spi.3.0+$rowdata.2.Spi.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Spi.3.0-$rowdata.2.Spi.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>關節變形</td>
{{if $bnum!=$rowdata.1.Spi.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Spi.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Spi.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Spi.4.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Spi.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Spi.4.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Spi.4.0+$rowdata.2.Spi.4.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Spi.4.0-$rowdata.2.Spi.4.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>水腫</td>
{{if $bnum!=$rowdata.1.Spi.5.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Spi.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Spi.5.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Spi.5.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Spi.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Spi.5.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Spi.5.0+$rowdata.2.Spi.5.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Spi.5.0-$rowdata.2.Spi.5.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: #F0F0F0;">
<td rowspan="5" style="text-align: center;">泌尿生殖</td>
<td>隱睪</td>
{{if $bnum!=$rowdata.1.Uro.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
<td></td>
<td></td>
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>陰囊腫大</td>
{{if $bnum!=$rowdata.1.Uro.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
<td></td>
<td></td>
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>包皮異常</td>
{{if $bnum!=$rowdata.1.Uro.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
<td></td>
<td></td>
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>精索靜脈曲張</td>
{{if $bnum!=$rowdata.1.Uro.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
<td></td>
<td></td>
<td>{{if $b}}{{$bnum-$rowdata.1.Uro.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Uro.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: white;">
<td rowspan="7" style="text-align: center;">皮膚</td>
<td>癬</td>
{{if $bnum!=$rowdata.1.Der.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Der.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Der.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Der.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Der.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Der.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Der.1.0+$rowdata.2.Der.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Der.1.0-$rowdata.2.Der.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>疣</td>
{{if $bnum!=$rowdata.1.Der.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Der.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Der.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Der.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Der.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Der.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Der.2.0+$rowdata.2.Der.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Der.2.0-$rowdata.2.Der.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>紫斑</td>
{{if $bnum!=$rowdata.1.Der.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Der.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Der.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Der.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Der.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Der.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Der.3.0+$rowdata.2.Der.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Der.3.0-$rowdata.2.Der.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>疥瘡</td>
{{if $bnum!=$rowdata.1.Der.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Der.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Der.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Der.4.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Der.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Der.4.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Der.4.0+$rowdata.2.Der.4.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Der.4.0-$rowdata.2.Der.4.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>溼疹</td>
{{if $bnum!=$rowdata.1.Der.5.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Der.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Der.5.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Der.5.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Der.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Der.5.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Der.5.0+$rowdata.2.Der.5.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Der.5.0-$rowdata.2.Der.5.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>異位性皮膚炎</td>
{{if $bnum!=$rowdata.1.Der.6.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Der.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Der.6.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Der.6.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Der.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Der.6.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Der.6.0+$rowdata.2.Der.6.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Der.6.0-$rowdata.2.Der.6.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: white;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: #F0F0F0;">
<td rowspan="9" style="text-align: center;">口腔</td>
<td>齲齒</td>
{{if $bnum!=$rowdata.1.Ora.7.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.7.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.7.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.7.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.7.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.7.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.7.0+$rowdata.2.Ora.7.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.7.0-$rowdata.2.Ora.7.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>缺牙</td>
{{if $bnum!=$rowdata.1.Ora.8.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.8.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.8.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.8.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.8.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.8.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.8.0+$rowdata.2.Ora.8.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.8.0-$rowdata.2.Ora.8.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>口腔衛生不良</td>
{{if $bnum!=$rowdata.1.Ora.1.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.1.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.1.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.1.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.1.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.1.0+$rowdata.2.Ora.1.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.1.0-$rowdata.2.Ora.1.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>牙結石</td>
{{if $bnum!=$rowdata.1.Ora.2.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.2.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.2.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.2.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.2.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.2.0+$rowdata.2.Ora.2.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.2.0-$rowdata.2.Ora.2.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>牙齦炎</td>
{{if $bnum!=$rowdata.1.Ora.5.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.5.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.5.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.5.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.5.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.5.0+$rowdata.2.Ora.5.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.5.0-$rowdata.2.Ora.5.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>牙周炎</td>
{{if $bnum!=$rowdata.1.Ora.3.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.3.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.3.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.3.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.3.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.3.0+$rowdata.2.Ora.3.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.3.0-$rowdata.2.Ora.3.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>齒列咬合不正</td>
{{if $bnum!=$rowdata.1.Ora.4.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.4.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.4.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.4.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.4.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.4.0+$rowdata.2.Ora.4.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.4.0-$rowdata.2.Ora.4.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>口腔黏膜異常</td>
{{if $bnum!=$rowdata.1.Ora.6.0}}{{assign var=b value=1}}{{else}}{{assign var=b value=0}}{{/if}}
<td>{{if $b}}{{$bnum-$rowdata.1.Ora.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.1.Ora.6.0/$bnum*100|round:2}}
<td>{{if $b}}{{$h-$v}}%{{/if}}</td>
{{if $gnum!=$rowdata.2.Ora.6.0}}{{assign var=g value=1}}{{else}}{{assign var=g value=0}}{{/if}}
<td>{{if $g}}{{$gnum-$rowdata.2.Ora.6.0}}{{/if}}</td>
{{assign var=v value=$rowdata.2.Ora.6.0/$gnum*100|round:2}}
<td>{{if $g}}{{$h-$v}}%{{/if}}</td>
{{assign var=av value=$rowdata.1.Ora.6.0+$rowdata.2.Ora.6.0}}
{{if $anum!=$av}}{{assign var=a value=1}}{{else}}{{assign var=a value=0}}{{/if}}
<td>{{if $a}}{{$bnum+$gnum-$rowdata.1.Ora.6.0-$rowdata.2.Ora.6.0}}{{/if}}</td>
{{assign var=v value=$av/$anum*100|round:2}}
<td>{{if $a}}{{$h-$v}}%{{/if}}</td>
</tr>
<tr style="background-color: #F0F0F0;">
<td>其他</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color: white;">
<td colspan="2" style="text-align: center;">尿液篩檢異常</td>
</tr>
<tr style="background-color: white;">
<td colspan="2" style="text-align: center;">蟯蟲陽性</td>
</tr>
</table>
</td>
</tr></table>
