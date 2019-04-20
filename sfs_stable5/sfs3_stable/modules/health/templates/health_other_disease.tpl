{{* $Id: health_other_disease.tpl 5310 2009-01-10 07:57:56Z hami $ *}}

<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" class="small">
<form name="v" method="post" action="{{$smarty.server.PHP_SELF}}">
<tr style="text-align:center;color:blue;background-color:#bedcfd;">
<td colspan="2">學校代碼</td>
</tr>
<tr style="background-color:white;text-align:center;">
<td>{{$s_arr.sch_id}}</td>
</tr>
</table>
<input type="submit" name="cdc" value="學校學生資料檔下載">
<input type="submit" name="inject" value="預種資料檔下載">
<table>
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">為配合行政院衛生署疾病管制局「學校傳染病監視通報資訊系統」資料上傳，本程式可將上傳所需資料檔匯出。</li>
<li class="small">若學校代碼錯誤，請務必先洽系統管理者將代碼修正，否則資料將無法上傳成功。</li>
<li class="small">該系統採固定檔名匯入方式，重覆上傳時資料將會覆蓋，所以需注意：(1)不可任意修改檔名，(2)上傳資料若有問題可重覆上傳。</li>
</ol>
</td></tr>
</form>
</table>
