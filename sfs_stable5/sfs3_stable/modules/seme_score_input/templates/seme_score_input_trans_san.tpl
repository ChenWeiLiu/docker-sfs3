{{* $Id: seme_score_input_trans_san.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table border="0" cellspacing="0" cellpadding="0"><tr><td style="vertical-align:top;">
<table cellspacing="1" cellpadding="3" class="main_body">
<tr bgcolor="#FFFFFF">
<form name="form0" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>上傳科目設定檔：</td>
<td colspan="2"><input type="file" name="upload_setup_file"><input type="submit" name="doup_key" value="上傳"></td>
</form>
</tr>
<tr bgcolor="#FFFFFF">
<form name="form1" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>伺服器內存檔案：</td>
<td colspan="2">{{$file_menu1}}{{if $chk_file1}} &nbsp;<span style="color:red;">{{$chk_file1}}</span>{{/if}}</td>
</form>
</tr>
{{if $smarty.post.file_name1}}
<tr bgcolor="#FFFFFF">
<form name="form2" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>上傳成績檔：</td>
<td colspan="2"><input type=file name="upload_file"><input type=submit name="doup_key" value="上傳"><input type="hidden" name="file_name1" value="{{$smarty.post.file_name1}}"></td>
</form>
</tr>
<tr bgcolor="#FFFFFF">
<form name="form3" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>伺服器內存檔案：</td>
<td colspan="2">{{$file_menu2}}{{if $chk_file1}} &nbsp;<span style="color:red;">{{$chk_file2}}</span>{{/if}}<input type="hidden" name="file_name1" value="{{$smarty.post.file_name1}}"></td>
</form>
</tr>
{{if $rowdata}}
<form name="form4" action="{{$smarty.server.PHP_SELF}}" method="post">
{{foreach from=$stud_data item=d key=i name=s}}
<tr><td colspan="3" style="color:white;">
<input type="radio" name="stud_study_year" value="{{$d.stud_study_year}}" {{if $smarty.foreach.s.iteration==1}}checked{{/if}}>
學號 : {{$d.stud_id}} &nbsp;&nbsp; 
姓名 : {{$d.stud_name}} &nbsp;&nbsp; 
性別 : {{if $d.stud_sex==1}}男{{elseif $d.stud_sex==2}}女{{else}}未設定{{/if}} &nbsp;&nbsp; 
入學年 : {{$d.stud_study_year}}</td></tr>
{{/foreach}}
<tr style="background-color:#aecced;color:white;text-align:center;">
<td>原設定科目名</td>
<td>成績</td>
<td>匯入對應科目名</td></tr>
{{foreach from=$rowdata item=d key=i}}
<tr>
<td nowrap style="background-color:#cedcfd;color:blue;text-align:center;">{{$subj_arr.$i.subj_name}}</td>
<td style="background-color:white;text-align:right;">{{$d}}&nbsp;&nbsp;</td>
<td style="background-color:white;text-align:center;">{{$subj_menu|@substr_replace:$i:23:3}}</td>
</tr>
{{/foreach}}
{{/if}}
{{/if}}
</table>
{{if $rowdata}}
<input type="submit" name="import" value="確定匯入">
<input type="hidden" name="file_name1" value="{{$smarty.post.file_name1}}">
<input type="hidden" name="file_name2" value="{{$smarty.post.file_name2}}">
</form>
{{/if}}
{{if $ok>0}}<span class="small" style="color:blue;">匯入正確筆數：{{$ok}}<br>{{/if}}
{{if $in_err>0}}<span class="small" style="color:red;">匯入錯誤筆數：{{$in_err}}<br>{{/if}}
{{if $sn_err>0}}<span class="small" style="color:red;">學號錯誤筆數：{{$sn_err}}<br>{{/if}}
</td><td style="vertical-align:top;">
<table cellspacing="1" cellpadding="3" class="main_body" style="color:red;">
<tr style="background-color:#f0f0f0;">
<td>
注意事項：
<ol>
<li>本程式不適用於「班級課程」的課程設定模式。</li>
<li>匯入前請先建立學生基本資料及各學期相關設定。</li>
<li>學務系統內之課程設定請盡量配合原系統課程設定。</li>
<li>不匯入的科目可不選「匯入對應科目」。</li>
{{if $rowdata}}
<li style="color:blue;">若沒有出現學生姓名表示學生基本資料未建立。</li>
<li style="color:blue;">若「匯入對應科目」沒有出現選單表示課程未設定。</li>
<li style="color:blue;">若成績值為「999」表示該生該科未輸入成績。</li>
{{/if}}
{{if $in_err>0}}
<li style="color:green;">「匯入錯誤」為該生該學期已有成績。</li>
{{/if}}
{{if $sn_err>0}}
<li style="color:green;">「學號錯誤」為該學號找不到對應的學生基本資料。</li>
{{/if}}
</ol>
</td>
</tr></table>
</td></tr></table>
<table>
<tr bgcolor="#FBFBC4">
<td><img src="/sfs3/images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">如果檔案未上傳，請先選擇一個檔案上傳。</li>
<li class="small">如果檔案已上傳，則選擇要匯入的檔案。</li>
<li class="small">要處理的檔案：<br>
(1) 科目設定檔 :「\STUDENT\COURSE\R9x\Y91S9xyz.DBF」<br>
(2) 成績檔 :「\STUDENT\STAGE\G9x\Y91G9xyz.DBF」<br>
代碼意義 (x:學年最後一碼、y:學期、z:年級)</li>
<li class="small">請先將此二檔以OpenOffice.org Calc打開，然後存成CSV格式後再選擇上傳，儲存時「編輯篩選設定」務必勾選且檔名勿改。</li>
<li class="small">上傳前先確認檔名為「Y91S9xyz.CSV」與「Y91G9xyz.CSV」，兩個檔案務必配合(同學年度、同學期、同年級)。</li>
<li class="small">檔案說明：Y91S9417.dbf為94學年度第1學期7年級科目設定檔、Y91G9417.dbf為94學年度第1學期7年級成績檔，依此類推。</li>
<li class="small"><a href="Y91S9417.CSV">科目設定範例檔</a>、<a href="Y91G9417.CSV">成績範例檔</a>。</li>
</ol>
</td></tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
