{{* $Id: health_trans.tpl 9240 2018-05-04 03:25:04Z igogo $ *}}

<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/jquery.min.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/jquery.progressbar.min.js"></script>
<script type="text/javascript">
<!--
var pp=0, d, acc, pass;
var arr=[0{{foreach from=$class_arr item=d key=k}}, {{$k}}{{/foreach}}];
function go() {
	acc=$('#myid').attr('value');
	pass=$('#mypass').attr('value');
	if (acc && pass) {
		$('#upBtn').attr('disabled', true);
		$('#upBtn').attr('value', '  資料上傳中, 請稍候...  ');
		$("#upBtn").get(0).style.color = "red";
		$('#proc').show();
		$('#pb1').progressBar(0);
		d=100/{{$class_arr|@count}};
		$.each(arr,function(i, n){
			if (n>0) {
				$.post('{{$smarty.server.SCRIPT_NAME}}',{ id: n, year_seme: "{{$smarty.post.year_seme}}", act: "xml", acc: acc, pass: pass},function(data){
					if (data!=''){
						pp+=d;
						$('#pb1').progressBar(pp);
						$('#msg').html(data);
						if (pp>99) {
							$('#upBtn').attr('value', '上傳完畢');
							$("#upBtn").get(0).style.color = "blue";
						}
					}
				});
			}
		});
	}
}

//-->
</script>

<table border="0" cellspacing="0" cellpadding="0" class="small" style="width:100%;">
<tr><td style="vertical-align:top;">
<span style="color:blue;">請輸入公務系統帳號密碼</span><br><br>
帳號：<input type="text" name="id" id="myid"><br>
密碼：<input type="password" name="pass" id="mypass"><br><br>
<div id="proc" style="display:none;">
上傳進度 <span class="progressBar" id="pb1">0%</span>
<br>
<div id="msg">
&nbsp;
</div></div><br>
<input type="button" id="upBtn" value="開始上傳" OnClick="go();">

{{*說明*}}
<table class="small" style="width:100%;">
<tr style="background-color:#FBFBC4;"><td><img src="../../images/filefind.png" width="16" height="16" hspace="3" border="0">說明</td></tr>
<tr><td style="line-height:150%;width:100%;">
	<ol>
	<li>本程式為因應98年暑假台中縣政府教育處體健科收集學生健康資料而撰寫。</li>
	<li>非台中縣學校資料將無法上傳。</li>
	</ol>
</td></tr>
</table>
</td>
</tr>
</form>
</table>
