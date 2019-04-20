{{* $Id: every_year_setup_course_setup_import_csv.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap>《進行匯入作業》2.上傳<font color='red'>(欣河排課系統)</font>課表資料檔：</td><td><input type=file name="upload_file"></td>
<td class="title_sbody1" nowrap><input type=submit name="upload" value="上傳"></td>
<td class="title_sbody1" nowrap><input type=submit value="回到對應狀態"></td>
<input type="hidden" name="year_seme" value="{{$sel_year}}-{{$sel_seme}}">
<input type="hidden" name="import" value="1">
</form>
</tr>
</table>
<br>
<table>
<tr bgcolor="#FBFBC4"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td></tr>
<tr><td style="line-height: 150%;">
<ol>
<li class="small">請先上傳課表檔<a href="demo_course.csv">(範例檔)</a>以供系統進行解析(.CSV檔案)。</li>
<li class="small">請勿使用中文檔名以免發生錯誤。</li>
<li class="small">表頭請勿更改，表頭中class_no，科目，科目名稱，星期，節次，教師，教師名稱為必要欄位，其餘為選填欄位。</li>
<li class="small">檔案上傳前請以OpenOffice.org之Calc存檔處理，以避免非數字欄位因缺少雙引號而造成系統判讀錯誤問題。</li>
</ol>
</td>
</tr>
</table>
