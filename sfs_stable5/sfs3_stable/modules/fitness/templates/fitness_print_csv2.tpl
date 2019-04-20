姓名,性別,學號/座號,出生年月,檢測時年齡,檢測日期,BMI,BMI判讀,仰臥起坐,仰臥起坐百分等級,仰臥起坐結果,仰臥起坐門檻,坐姿體前彎,坐姿體前彎百分等級,坐姿體前彎結果,坐姿體前彎門檻,立定跳遠,立定跳遠百分等級,立定跳遠結果,立定跳遠門檻,心肺適能,心肺適能百分等級,心肺適能結果,心肺適能門檻,未達門檻項次
{{foreach from=$rowdata item=d key=i}}
{{assign var=sn value=$d.student_sn}}
{{assign var=birthday value=$d.4}}
"{{$d.stud_name}}","{{$fd.$sn.sex_title}}","{{$d.stud_id}}","{{$d.stud_birthday3}}","{{$fd.$sn.age}}","中華民國{{$fd.$sn.test_y}}年{{$fd.$sn.test_m|string_format:"%02d"}}月","{{$fd.$sn.bmt}}","{{$fd.$sn.bid}}","{{$fd.$sn.test2}}","{{$fd.$sn.prec2}}","{{if $fd.$sn.prec2>=85}}金牌{{elseif $fd.$sn.prec2>=75}}銀牌{{elseif $fd.$sn.prec2>=50}}銅牌{{else}}--{{/if}}","{{$fd.$sn.test2_lower}}","{{$fd.$sn.test1}}","{{$fd.$sn.prec1}}","{{if $fd.$sn.prec1>=85}}金牌{{elseif $fd.$sn.prec1>=75}}銀牌{{elseif $fd.$sn.prec1>=50}}銅牌{{else}}--{{/if}}","{{$fd.$sn.test1_lower}}","{{$fd.$sn.test3}}","{{$fd.$sn.prec3}}","{{if $fd.$sn.prec3>=85}}金牌{{elseif $fd.$sn.prec3>=75}}銀牌{{elseif $fd.$sn.prec3>=50}}銅牌{{else}}--{{/if}}","{{$fd.$sn.test3_lower}}","{{$fd.$sn.test4}}","{{$fd.$sn.prec4}}","{{if $fd.$sn.prec4>=85}}金牌{{elseif $fd.$sn.prec4>=75}}銀牌{{elseif $fd.$sn.prec4>=50}}銅牌{{else}}--{{/if}}","{{$fd.$sn.test4_lower}}","{{$fd.$sn.lower}}"
{{/foreach}}
