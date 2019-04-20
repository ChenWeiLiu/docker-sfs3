<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML><HEAD><TITLE>身高體重測量結果通知單</TITLE>
<META http-equiv=Content-Type content="text/html; charset=big5">
</HEAD>
<BODY>
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=year_name value=$seme_class|@substr:0:-2}}
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=h value=$health_data->health_data.$sn.$year_seme}}
<TABLE style="border-collapse: collapse; margin: auto; font: 14pt 標楷體,標楷體,serif; page-break-after: auto;" cellSpacing="0" cellPadding="0" width="640" border="0">
  <TBODY>
  <TR>
    <TD style="PADDING-RIGHT: 1pt; PADDING-LEFT: 1pt; PADDING-BOTTOM: 0cm; PADDING-TOP: 0cm;" width="640">
      <TABLE style="BORDER-COLLAPSE: collapse; text-align: center; vertical-align: middle; font: 12pt 標楷體,標楷體,serif;" cellSpacing="0" cellPadding="2" width="640" border="0">
        <TBODY>
        <TR style="height: 16pt;">
          <TD style="font-size:16pt;">{{$school_data.sch_cname}}　<strong>身高體重測量結果通知單</strong><br></TD>
                </TR>
                <TR>
                  <TD style="text-align: left;">
                  親愛的家長：<br><p style="font-size:6pt;"></p>
                  貴子女 <strong>{{$year_data.$year_name}}{{$class_data.$seme_class}}班 {{$seme_num}}</strong> 號 <strong>{{$health_data->stud_base.$sn.stud_name}}</strong><br>
                  身高 <strong>{{$h.height}}</strong> 公分 體重 <strong>{{$h.weight}}</strong> 公斤 BMI值 <strong>{{$h.BMI}}</strong><br>
                  經判讀結果 <strong>{{$Bid_arr[$h.Bid]}}</strong><br><p style="font-size:6pt;"></p>

{{if $h.Bid==0}}
                  貴子女本學期之體重檢查結果發現體型瘦小，為維護貴子女的健康，請貴家長先分析引起孩子瘦弱的原因是否為飲食習慣不良造成？或帶往醫>
院作更進一步的檢查，以瞭解有無其他潛在疾病因素造成。現僅提供以下均衡營養資料，希望家長能配合家居生活，以改善其飲食營養狀況。<br>
                  <ol style="font-size: 11pt;">
                        <li>每天按時進餐，保持心情愉快、細嚼慢嚥，以增進食慾幫助消化。</li>
                        <li>早餐為一天之首，一定要吃且量要足質地要好。</li>
                        <li>飲食要定量且不偏食，營養要平均分配在三餐中。</li>
                        <li>避免影響吃正餐的因素，例如兩餐之間吃零食、甜點。</li>
                        <li>多喝開水促進正常排泄，維護健康。</li>
                        <li>每天要作適量戶外運動。</li>
                        <li>每天要喝兩～三杯牛奶，供蛋白質、鈣質、維生素B2，促進兒童生長，防止蛀牙。</li>
                        <li>不以食物作為獎賞或懲罰的工具。</li>
                  </ol>
{{elseif $h.Bid==1}}
                  貴子女本學期之體重檢查結果發現體型在正常範圍內，為維護貴子女的健康，請貴家長仍能於日常生活中，繼續協助保持良好的生活飲食習慣>。於此再提供相關營養資料作為輔助參考，希望能有所助益。更期待因您們的努力，使貴子女能擁有正常健康的生長發育。<br>
                  <ol style="font-size: 11pt;">
                        <li>飲食要平衡，營養要平均分配在三餐中。</li>
                        <li>每天要喝兩～三杯牛奶，供蛋白質、鈣質、維生素B2，促進兒童生長，防止蛀牙。</li>
                        <li>早餐的營養必須均衡，並包括一份高蛋白的食物。例如：牛奶一杯、荷包蛋一個或白煮蛋一個、饅頭一個、水果一份。</li>
                        <li>學童由於活動量大，兒童除正餐外，可增加一～二次點心，尤其兒童下午放學時可提供一次點心（最好是奶類製品）。</li>
                        <li>多喝開水促進正常排泄，維護健康。</li>
                        <li>每天要作適量運動，並且持之以恆。</li>
                        <li>不在吃飯時看電視，進餐的氣氛應和樂，避免在吃飯時間，責罵兒童。</li>
                  </ol>
{{else}}
                  貴子女本學期之體重檢查結果發現體重過重。因體重過重容易引起心臟血管疾病，動作不靈活，身體形象較差。為維護貴子女身體的健康，希>
望家長能自行評估學童日常飲食習慣是否有不宜之處。若有，請參考以下體重控制的資料，以協助學童控制體重。<br>
                  <ol style="font-size: 11pt;">
                        <li>食物的製作儘量採用蒸、煮、烤、涼拌。</li>
                        <li>改變進餐的程序，先喝湯再吃菜、飯和肉。</li>
                        <li>一定在餐桌上用餐，吃到不餓不要吃到飽。</li>
                        <li>飯後定離開餐桌且立即刷牙。</li>
                        <li>最好能每日記錄進食的食物種類和量。</li>
                        <li>避免吃零食宵夜，尤其不要用吃來獎懲自己。</li>
                        <li>多選擇吃起來麻煩，吃不多又花時間的食物。</li>
                        <li>若兩餐之間有飢餓感時，可選擇精神力量的支持、注意力的轉移，或選低熱量的食物補充（如：蘇打餅乾、多水份水果、紅白葡萄>
、小黃瓜、蒟蒻）。</li>
                        <li>每日養成規律運動的好習慣，並持之以恆。</li>
                        <li>控制體重的過程要具有三心，即信心、決心和恆心。</li>
                  </ol>
{{/if}}
                  　　　　　　　　此致<br>
                  貴家長<p style="font-size:6pt;"></p>

          <p style="font-size: 10pt; text-align: right;">{{$school_data.sch_cname}} 健康中心　敬啟　　 {{$smarty.now|date_format:"%Y"}}.{{$smarty.now|date_format:"%m"}}.{{$smarty.now|date_format:"%d"}}　</p>
                  </TD>
                </TR>
                </TBODY>
          </TABLE>
        </TD>
  </TR>
  </TBODY>
</TABLE>
{{if $smarty.foreach.rows.iteration%2==1}}
<p style="border-bottom: dashed 1px;"></p>
{{else}}
<p style="page-break-after:always;"> </p>
{{/if}}
{{/foreach}}
</BODY></HTML>
