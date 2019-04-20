{{* $Id: create_data_trans_dos.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<table cellspacing="1" cellpadding="3" class="main_body">
<tr bgcolor="#FFFFFF">
<form name="form0" enctype="multipart/form-data" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>上傳學生基本資料檔：</td>
<td colspan="2"><input type="file" name="upload_file"><input type="submit" name="doup_key" value="上傳"></td>
</form>
</tr>
<tr bgcolor="#FFFFFF">
<form name="form1" action="{{$smarty.server.PHP_SELF}}" method="post">
<td class="title_sbody1" nowrap>伺服器內存檔案：</td>
<td colspan="2">{{$file_menu1}}{{if $chk_file1}} &nbsp;<span style="color:red;">{{$chk_file1}}</span>{{/if}}</td>
</form>
</tr>
{{if $stud_base}}
<form name="form4" action="{{$smarty.server.PHP_SELF}}" method="post">
<tr><td colspan="3" style="color:white;text-align:center;">
第一筆學生資料
</td></tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">學號</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_id}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">姓名</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_name}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">身分證字號</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_person_id}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">性別</td>
<td colspan="2" style="background-color:white;text-align:left;">{{if $stud_base.stud_sex==1}}男{{elseif $stud_base.stud_sex==2}}女{{else}}未設定{{/if}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">生日</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_birthday}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">出生地</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_birth_place}}</td>
</tr>
{{assign var=d value=$stud_base.stud_study_cond}}
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">就學狀態</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$study_cond.$d}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">入學前國小</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_mschool_name}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">戶籍地址</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_addr_1}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">連絡地址</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_addr_2}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">連絡電話</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.stud_tel_2}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">監護人姓名</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.guardian_name}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">與監護人關係</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.guardian_relation}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">監護人地址</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.guardian_address}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">監護人電話</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.guardian_phone}}</td>
</tr>
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">監護人公司電話</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$stud_base.phone}}</td>
</tr>
{{foreach from=$stud_base.seme_year item=d key=i}}
<tr>
<td style="background-color:#cedcfd;color:blue;text-align:center;">第{{$i}}學期班號</td>
<td colspan="2" style="background-color:white;text-align:left;">{{$d}}學年度{{$stud_base.seme_class.$i}}班{{$stud_base.seme_num.$i}}號</td>
</tr>
{{/foreach}}
{{/if}}
{{if $err_msg}}
<tr><td colspan="3">
{{$err_msg}}
</td></tr>
{{/if}}
</table>
{{if $chk_file1}}
<input type="submit" name="import" value="確定匯入"> <input type="submit" name="del" value="確定刪除">
<input type="hidden" name="file_name1" value="{{$smarty.post.file_name1}}">
</form>
{{/if}}
{{if $line!=""}}
<span style="color:red;">您上傳的原始資料第 {{$line}} 行有問題，建議修改後重新上傳。</span><br>
中斷行資料：「<span style="color:red;">{{$brk_msg}}</span>」
{{/if}}
<table>
<tr bgcolor="#FBFBC4">
<td><img src="/sfs3/images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">如果檔案未上傳，請先選擇一個檔案上傳。</li>
<li class="small">如果檔案已上傳，則選擇要匯入的檔案。</li>
<li class="small">要處理的檔案：「\STUDENT\PERSON\Pxx\XBASICxx.DBF」代碼意義 (xx:學年)</li>
<li class="small">請先將此檔以「<a href="http://sfshelp.tcc.edu.tw/download/dBase2csv.rar">dBase轉CSV檔程式</a>」處理成 XBASICxx.CSV 檔再上傳。</li>
<li class="small">檔案說明：XBASIC90.dbf為90學年度入學學生基本資料，依此類推。</li>
<li class="small" style="color:red;">程式以檔名做為入學年判斷依據，所以請勿任意更改檔名。</li>
<li class="small"><a href="XBASIC91.CSV">範例檔</a>。</li>
</ol>
</td></tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
