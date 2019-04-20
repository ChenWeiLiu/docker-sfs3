{{* $Id: health_export.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<table border="0" cellspacing="0" cellpadding="0">
<tr><td style="vertical-align:top;"><br>
{{if $sch_id}}
教育部學校代號: <span style="color:red; border:#ccc solid thin;">{{$sch_id}}</span><br><br>
<input type="submit" name="export1" value="匯出「身高體重」Excel簡化版檔案"><br>
<input type="submit" name="export2" value="匯出「視力檢查」Excel簡化版檔案"><br>
<input type="submit" name="export3" value="匯出「健康檢查」Excel簡化版檔案"><br>
<input type="submit" name="export4" value="匯出「口腔檢查」Excel簡化版檔案"><br>
<input type="submit" name="export5" value="匯出「個人疾病史」Excel簡化版檔案"><br>
<input type="submit" name="export6" value="匯出「傷病」Excel簡化版檔案"><br>
<input type="submit" name="export7" value="匯出「立體感檢查」Excel簡化版檔案"><br><br>
{{else}}
<span style="color:red; border:#ccc solid thin; background: #ff0">貴校學校代碼(教育部)未填,請連絡網管人員處理</span>
<input type="submit" name="export"  disabled="true"  value="匯出檔案">
{{/if}}
{{*說明*}}
<table class="small">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;">
	<ol>
	<li>本程式為因應100年體育司收集學生健康資料而撰寫。</li>
	</ol>
</td></tr>
</table>
</td>
</tr>
</form>
</table>
