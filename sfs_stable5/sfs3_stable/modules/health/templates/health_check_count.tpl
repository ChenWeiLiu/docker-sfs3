{{* $Id: health_check_count.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
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
<td>複檢正常</td>
<td>複檢異常</td>
<td>未就醫</td>
<td>就醫率</td>
<td>備註(及其他異常項目)</td>
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
{{if $smarty.post.class_name}}
<tr style="background-color:white;">
<td rowspan="11">眼</td>
<td style="text-align:left;">視力不良(含4種)</td>
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
<tr style="background-color:#f4feff;">
<td style="text-align:right;">近視</td>
<td>{{$rowdata.1.Oph.My|intval}}</td>
<td>{{$rowdata.1.Oph.My/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.Oph.My|intval}}</td>
<td>{{$rowdata.2.Oph.My/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.Oph.My|intval}}</td>
<td>{{$rowdata.all.Oph.My/$studnum_arr.all*100|@round:2}}%</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:right;">遠視</td>
<td>{{$rowdata.1.Oph.Hy|intval}}</td>
<td>{{$rowdata.1.Oph.Hy/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.Oph.Hy|intval}}</td>
<td>{{$rowdata.2.Oph.Hy/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.Oph.Hy|intval}}</td>
<td>{{$rowdata.all.Oph.Hy/$studnum_arr.all*100|@round:2}}%</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:right;">散光</td>
<td>{{$rowdata.1.Oph.Ast|intval}}</td>
<td>{{$rowdata.1.Oph.Ast/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.Oph.Ast|intval}}</td>
<td>{{$rowdata.2.Oph.Ast/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.Oph.Ast|intval}}</td>
<td>{{$rowdata.all.Oph.Ast/$studnum_arr.all*100|@round:2}}%</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:right;">弱視</td>
<td>{{$rowdata.1.Oph.Amb|intval}}</td>
<td>{{$rowdata.1.Oph.Amb/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.Oph.Amb|intval}}</td>
<td>{{$rowdata.2.Oph.Amb/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.Oph.Amb|intval}}</td>
<td>{{$rowdata.all.Oph.Amb/$studnum_arr.all*100|@round:2}}%</td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">辨色力異常</td>
{{assign var=subject value="Oph"}}
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">斜視</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">睫毛倒插</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">眼球震顫</td>
{{assign var=nno value=5}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">眼瞼下垂</td>
{{assign var=nno value=6}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:#f4feff;">
<td rowspan="11">耳<br>鼻<br>喉</td>
<td style="text-align:left;">聽力異常</td>
{{assign var=subject value="Ent"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">疑似中耳炎</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">耳道畸形</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">唇顎裂</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">構音異常</td>
{{assign var=nno value=5}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">耳前?管</td>
{{assign var=nno value=6}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">耵聹栓塞</td>
{{assign var=nno value=7}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">慢性鼻炎</td>
{{assign var=nno value=8}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">過敏性鼻炎</td>
{{assign var=nno value=9}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">扁桃腺腫大</td>
{{assign var=nno value=10}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:white;">
<td rowspan="4">頭<br>頸</td>
<td style="text-align:left;">斜頸</td>
{{assign var=subject value="Hea"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">甲狀腺腫</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">淋巴腺腫大</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:white;">
<td rowspan="5" style="background-color:#f4feff;">胸<br>部</td>
<td style="text-align:left;">胸廓異常</td>
{{assign var=subject value="Pul"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">心雜音</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">心律不整</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">呼吸聲異常</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:#f4feff;">
<td rowspan="3" style="background-color:white;">腹<br>部</td>
<td style="text-align:left;">肝脾腫大</td>
{{assign var=subject value="Dig"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">疝氣</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:white;">
<td rowspan="6" style="background-color:#f4feff;">脊<br>柱<br>四<br>肢</td>
<td style="text-align:left;">脊柱側彎</td>
{{assign var=subject value="Spi"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">多併指</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">青蛙肢</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">關節變形</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">水腫</td>
{{assign var=nno value=5}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:white;">
<td rowspan="5">泌<br>尿<br>生<br>殖</td>
<td style="text-align:left;">隱睪</td>
{{assign var=subject value="Uro"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">陰囊腫大</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">包皮異常</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">精索靜脈曲張</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:#f4feff;">
<td rowspan="7">皮<br>膚</td>
<td style="text-align:left;">癬</td>
{{assign var=subject value="Der"}}
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">疣</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">紫斑</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">疥瘡</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">溼疹</td>
{{assign var=nno value=5}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">異位性皮膚炎</td>
{{assign var=nno value=6}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:white;">
<td rowspan="9">口<br>腔</td>
<td style="text-align:left;">齲齒</td>
{{assign var=subject value="Ora"}}
{{assign var=nno value=7}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">缺牙</td>
{{assign var=nno value=8}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">口腔衛生不良</td>
{{assign var=nno value=1}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">牙結石</td>
{{assign var=nno value=2}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">牙齦炎</td>
{{assign var=nno value=5}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">牙周炎</td>
{{assign var=nno value=3}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">齒列咬合不正</td>
{{assign var=nno value=4}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:#f4feff;">
<td style="text-align:left;">口腔黏膜異常</td>
{{assign var=nno value=6}}
<td>{{$rowdata.1.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.1.$subject.$nno.un/$studnum_arr.1*100|@round:2}}%</td>
<td>{{$rowdata.2.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.2.$subject.$nno.un/$studnum_arr.2*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.un|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.un/$studnum_arr.all*100|@round:2}}%</td>
<td>{{$rowdata.all.$subject.$nno.2|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.se|intval}}</td>
<td>{{$rowdata.all.$subject.$nno.1|intval}}</td>
{{assign var=d value=$rowdata.all.$subject.$nno.se-$rowdata.all.$subject.$nno.1|intval}}
<td>{{$d/$rowdata.all.$subject.$nno.se*100|round}}%</td>
<td></td>
</tr>
<tr style="background-color:white;">
<td style="text-align:left;">其他</td>
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
<tr style="background-color:#f4feff;">
<td colspan="2">尿液篩檢異常</td>
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
<tr style="background-color:white;">
<td colspan="2">蟯蟲陽性</td>
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
{{/if}}
</table>
{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>異狀百分比 = 異狀人數 ÷ 受檢總人數。</li>
	</ol>
</td></tr>
</table>
</td></tr></table>
