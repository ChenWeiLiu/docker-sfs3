{{* $Id: health_checks_import.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}
<script>
function chk_file() {
	if (document.myform.upload_file.value=="") {
		alert("請先選擇上傳檔案");
	} else {
		document.myform.encoding="multipart/form-data";
		document.myform.submit();
	}
}
</script>

<table border="0" cellspacing="0" cellpadding="0">
<tr><td style="vertical-align:top;"><br>
<input type="radio" name="" checked>匯入「理學檢查」資料<br>
<input type="radio" name="">匯入「實驗室檢查」資料<br>
<input type="file" name="upload_file">
<input type="button" name="upload" value="上傳檔案" OnClick="chk_file();"><br>

{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>本程式為匯入健檢單位所提供之健檢電子資料而撰寫。</li>
	</ol>
</td></tr>
</table>
</td>
</tr>
</form>
</table>
