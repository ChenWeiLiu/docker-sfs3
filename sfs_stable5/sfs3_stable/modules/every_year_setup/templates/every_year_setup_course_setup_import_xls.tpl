{{* $Id: every_year_setup_course_setup_import_xls.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap>《進行匯入作業》2.上傳<font color='red'>(一也排課系統)</font>課表資料檔：</td><td><input type=file name="upload_file"></td>
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
<li class="small">請先上傳課表檔<a href="demo_course.xls">(範例檔)</a>以供系統進行解析(.XLS檔案)。</li>
<li class="small">請勿使用中文檔名以免發生錯誤。</li>
<li class="small">表頭請勿更改，表頭中班級、教師、科目、星期、節1～節7為必要欄位，其餘為選填欄位。</li>
</ol>
</td>
</tr>
</table>
