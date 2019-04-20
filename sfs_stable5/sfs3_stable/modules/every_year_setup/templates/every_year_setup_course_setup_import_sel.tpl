{{* $Id: every_year_setup_course_setup_import_sel.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<table bgcolor="#9EBCDD" cellspacing="1" cellpadding="4">
<tr bgcolor="#FFFFFF">
<td class="title_sbody1" nowrap>《進行匯入作業》1.選擇排課系統：</td><td><select name="sys"><option value="">請選系統</option><option value="sing">欣河排課系統</option><option value="stc">STC排課系統</option><option value="ya">一也排課系統</option></select></td>
<td class="title_sbody1" nowrap><input type="submit" name="act" value="開始匯入檔案"></td>
<td class="title_sbody1" nowrap><input type="submit" value="回到對應狀態"></td>
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
<li class="small">請先選擇排課系統，後續作業將依所選系統進行不同的流程。</li>
<li class="small">本系統目前支援欣河、STC、一也排課系統。</li>
<li class="small">其他排課系統支援請洽系統開發小組。</li>
</ol>
</td>
</tr>
</table>
