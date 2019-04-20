{{* $Id: health_analyze_accident_count.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table cellspacing="0" cellpadding="0"><tr>
<td style="vertical-align:top;">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="3">項目</td>
<td colspan="3" rowspan="2">性別</td>
<td colspan="3">時間</td>
<td colspan="11">地點</td>
<td colspan="15">部位</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">上<br>午</td>
<td rowspan="2">中<br>午</td>
<td rowspan="2">下<br>午</td>
<td rowspan="2">運<br>動<br>場</td>
<td rowspan="2">遊<br>戲<br>器<br>材</td>
<td rowspan="2">普<br>通<br>教<br>室</td>
<td rowspan="2">專<br>科<br>教<br>室</td>
<td rowspan="2">走<br>廊</td>
<td rowspan="2">樓<br>梯</td>
<td rowspan="2">地<br>下<br>室</td>
<td rowspan="2" nowrap>體活<br>育動<br>館中<br>或心</td>
<td rowspan="2">廁<br>所</td>
<td rowspan="2">校<br>外</td>
<td rowspan="2">其<br>他</td>
<td rowspan="2">頭</td>
<td rowspan="2">頸</td>
<td rowspan="2">肩</td>
<td rowspan="2">胸</td>
<td rowspan="2">腹</td>
<td rowspan="2">背</td>
<td rowspan="2">眼</td>
<td rowspan="2">顏<br>面</td>
<td rowspan="2">口<br>腔</td>
<td rowspan="2">耳<br>鼻<br>喉</td>
<td rowspan="2">上<br>肢</td>
<td rowspan="2">腰</td>
<td rowspan="2">臀<br>部</td>
<td rowspan="2">下<br>肢</td>
<td rowspan="2">會<br>陰</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>合<br>計</td>
<td>男</td>
<td>女</td>
</tr>
{{assign var=dd value=$rowdata}}
{{foreach from=$month_arr item=d}}
<tr style="background-color:{{cycle values="white,yellow"}};">
<td>數值</td>
<td>{{$dd.sex.3.$d}}</td>
<td>{{$dd.sex.1.$d}}</td>
<td>{{$dd.sex.2.$d}}</td>
<td> </td>
<td> </td>
<td> </td>
<td>{{$dd.place.1.$d}}</td>
<td>{{$dd.place.2.$d}}</td>
<td>{{$dd.place.3.$d}}</td>
<td>{{$dd.place.4.$d}}</td>
<td>{{$dd.place.5.$d}}</td>
<td>{{$dd.place.6.$d}}</td>
<td>{{$dd.place.7.$d}}</td>
<td>{{$dd.place.8.$d}}</td>
<td>{{$dd.place.9.$d}}</td>
<td>{{$dd.place.10.$d}}</td>
<td>{{$dd.place.999.$d}}</td>
<td>{{$dd.part.1.$d}}</td>
<td>{{$dd.part.2.$d}}</td>
<td>{{$dd.part.3.$d}}</td>
<td>{{$dd.part.4.$d}}</td>
<td>{{$dd.part.5.$d}}</td>
<td>{{$dd.part.6.$d}}</td>
<td>{{$dd.part.7.$d}}</td>
<td>{{$dd.part.8.$d}}</td>
<td>{{$dd.part.9.$d}}</td>
<td>{{$dd.part.10.$d}}</td>
<td>{{$dd.part.11.$d}}</td>
<td>{{$dd.part.12.$d}}</td>
<td>{{$dd.part.13.$d}}</td>
<td>{{$dd.part.14.$d}}</td>
<td>{{$dd.part.15.$d}}</td>
</tr>
{{/foreach}}
</table><br>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="5" class="small" style="text-align:center;">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="3">項目</td>
<td colspan="24">受傷種類</td>
<td colspan="9">處理方式</td>
<td rowspan="3">觀<br>察<br>時<br>間<br>．<br>分</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td colspan="10">意外傷害</td>
<td colspan="14">內科疾患</td>
<td rowspan="2">傷<br>口<br>護<br>理</td>
<td rowspan="2">冰<br>敷</td>
<td rowspan="2">熱<br>敷</td>
<td rowspan="2">休<br>息<br>觀<br>察</td>
<td rowspan="2">通<br>知<br>家<br>長</td>
<td rowspan="2">家<br>長<br>帶<br>回</td>
<td rowspan="2">校<br>方<br>送<br>醫</td>
<td rowspan="2">衛<br>生<br>教<br>育</td>
<td rowspan="2">其<br>他<br>處<br>理</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>擦<br>傷</td>
<td>裂<br>割<br>傷</td>
<td>夾<br>壓<br>傷</td>
<td>挫<br>撞<br>傷</td>
<td>扭<br>傷</td>
<td>灼<br>燙<br>傷</td>
<td>叮<br>咬<br>傷</td>
<td>骨<br>折</td>
<td>舊<br>傷</td>
<td>外<br>科<br>其<br>他</td>
<td>發<br>燒</td>
<td>暈<br>眩</td>
<td>噁<br>心<br>嘔<br>吐</td>
<td>頭<br>痛</td>
<td>牙<br>痛</td>
<td>胃<br>痛</td>
<td>腹<br>痛</td>
<td>腹<br>瀉</td>
<td>經<br>痛</td>
<td>氣<br>喘</td>
<td>流<br>鼻<br>血</td>
<td>疹<br>癢</td>
<td>眼<br>疾</td>
<td>內<br>科<br>其<br>他</td>
</tr>
{{foreach from=$month_arr item=d}}
<tr style="background-color:white;">
<td>數值</td>
<td>{{$dd.status.1.$d}}</td>
<td>{{$dd.status.2.$d}}</td>
<td>{{$dd.status.3.$d}}</td>
<td>{{$dd.status.4.$d}}</td>
<td>{{$dd.status.5.$d}}</td>
<td>{{$dd.status.6.$d}}</td>
<td>{{$dd.status.7.$d}}</td>
<td>{{$dd.status.8.$d}}</td>
<td>{{$dd.status.9.$d}}</td>
<td>{{$dd.status.10.$d}}</td>
<td>{{$dd.status.11.$d}}</td>
<td>{{$dd.status.12.$d}}</td>
<td>{{$dd.status.13.$d}}</td>
<td>{{$dd.status.14.$d}}</td>
<td>{{$dd.status.15.$d}}</td>
<td>{{$dd.status.16.$d}}</td>
<td>{{$dd.status.17.$d}}</td>
<td>{{$dd.status.18.$d}}</td>
<td>{{$dd.status.19.$d}}</td>
<td>{{$dd.status.20.$d}}</td>
<td>{{$dd.status.21.$d}}</td>
<td>{{$dd.status.22.$d}}</td>
<td>{{$dd.status.23.$d}}</td>
<td>{{$dd.status.24.$d}}</td>
<td>{{$dd.attend.1.$d}}</td>
<td>{{$dd.attend.2.$d}}</td>
<td>{{$dd.attend.3.$d}}</td>
<td>{{$dd.attend.4.$d}}</td>
<td>{{$dd.attend.5.$d}}</td>
<td>{{$dd.attend.6.$d}}</td>
<td>{{$dd.attend.7.$d}}</td>
<td>{{$dd.attend.8.$d}}</td>
<td>{{$dd.attend.9.$d}}</td>
<td>{{$dd.min.$d}}</td>
</tr>
{{/foreach}}
</table>
</td></tr></table>