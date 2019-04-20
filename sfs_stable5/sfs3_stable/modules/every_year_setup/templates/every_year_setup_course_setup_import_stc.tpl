{{* $Id: every_year_setup_course_setup_import_stc.tpl 6188 2010-09-23 02:30:46Z brucelyc $ *}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap>《進行匯入作業》2.上傳<font color='red'>(STC排課系統)</font>設定檔：</td><td><input type=file name="upload_file"></td>
<td class="title_sbody1" nowrap><input type=submit name="upload" value="上傳"></td>
<td class="title_sbody1" nowrap><input type=submit value="回到對應狀態"></td>
<input type="hidden" name="year_seme" value="{{$sel_year}}-{{$sel_seme}}">
<input type="hidden" name="import" value="1">
</tr>
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" colspan="4" style="text-align:left;background-color:white;" nowrap>
<font color="blue">※請先選擇匯入項目：</font><br>
<input type="radio" name="file_name" value="ClassNum" {{if $enable_class}}disabled{{else}}checked{{/if}}>1.班級設定檔(ClassNum) <input type="checkbox" name="force7" {{if $enable_class}}disabled{{/if}}>強制從七年級開始匯入<br>
<input type="radio" name="file_name" value="ClassTab" {{if $enable_class}}checked{{else}}disabled{{/if}}>2.課表設定檔(ClassTab)<br>
<input type="radio" name="file_name" value="CoursNam" {{if $enable_class==0}}disabled{{/if}}>3.科目名稱檔(CoursNam)<br>
<input type="radio" name="file_name" value="ClassCur" {{if $enable_class==0}}disabled{{/if}}>4.班級配課檔(ClassCur)<br>
<input type="radio" name="file_name" value="TeachNam" {{if $enable_class==0}}disabled{{/if}}>5.教師姓名檔(TeachNam)
</td>
</tr>
</form>
</table>
<br>
<table>
<tr bgcolor="#FBFBC4"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td></tr>
<tr><td style="line-height: 150%;">
<ol>
<li class="small">STC排課系統之各檔案內容有一定的關聯，請直接使用該系統檔案，本模組不另提供範例檔供人工作業使用。</li>
<li class="small">請勿以人工方式自行更改檔名或檔案內容，以避免不可預期之錯誤發生。</li>
<li class="small">請依序匯入班級設定檔(ClassNum)、課表設定檔(ClassTab)、科目名稱檔(CoursNam)、班級配課檔(ClassCur)、教師姓名檔(TeachNam)。</li>
</ol>
</td>
</tr>
</table>
