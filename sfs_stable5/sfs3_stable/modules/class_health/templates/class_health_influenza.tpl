{{* $Id: class_health_influenza.tpl 7728 2013-10-28 09:02:05Z smallduh $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/jquery.min.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/hovertip.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		window.setTimeout(hovertipInit, 1);
     });
	function act(a,b,c) {
		if ((a=='del' && confirm('確定要刪除此筆資料 ?')) || a!='del') {
			document.myform.act.value=a;
			document.myform.student_sn.value=b;
			document.myform.dis_date.value=c;
			document.myform.submit();
		}
	}
</script>
<style type="text/css" media="all">@import "{{$SFS_PATH_HTML}}javascripts/css.css";</style>

<table border="0" cellspacing="1" cellpadding="2" width="100%" bgcolor="#cccccc">
<form name="myform" method="post" action="{{$smarty.server.SCRIPT_NAME}}">
<tr>
<td bgcolor="white">
<input type="submit" name="add" value="新增資料">
<input type="hidden" name="act" value="add">
<input type="hidden" name="student_sn">
<input type="hidden" name="dis_date">
<br>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="3" class="small">
<tr style="background-color:#E6E9F9;text-align:center;">
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td rowspan="2">性別</td>
<td rowspan="2">出生年次</td>
<td colspan="7">症狀</td>
<td rowspan="2">請假<br>狀況</td>
<td>發病日</td>
<td>就診醫院</td>
<td>採檢日期</td>
<td rowspan="2">有無<br>施打<br>流感<br>疫苗</td>
<td rowspan="2">有感<br>冒症<br>症家<br>屬　</td>
<td rowspan="2">家人<br>最近<br>曾否<br>出國</td>
<td rowspan="2">備註</td>
<td rowspan="2">功能</td>
</tr>
<tr style="background-color:#E6E9F9;text-align:center;vertical-align:top;">
<td>發<br>高<br>燒</td>
<td>肌<br>肉<br>酸<br>痛</td>
<td>頭<br>痛</td>
<td>極<br>度<br>倦<br>怠</td>
<td>咳<br>嗽</td>
<td>呼<br>吸<br>喘</td>
<td>喉<br>嚨<br>痛</td>
<td style="vertical-align:middle;">就診日</td>
<td style="vertical-align:middle;">醫師診斷病名</td>
<td style="vertical-align:middle;">檢驗報告</td>
</tr>
{{assign var=j value=1}}
{{foreach from=$rowdata item=d key=i}}
{{assign var=sn value=$d.student_sn}}
{{php}}
$this->_tpl_vars['v']=explode("@@@",$this->_tpl_vars['d']['sym_str']);
$this->_tpl_vars['vv']=explode("@@@",$this->_tpl_vars['d']['oth_chk']);
$vvv=explode("@@@",$this->_tpl_vars['d']['oth_txt']);
reset($vvv);
while(list($k,$v)=each($vvv)) {
	$vvvv=explode("###",$v);
	$this->_tpl_vars['vvv'][$vvvv[0]]=$vvvv[1];
}
{{/php}}
<tr style="background-color:{{if $ii}}#F0F0F0{{else}}white{{/if}};">
<td rowspan="2">{{$studdata.$sn.seme_num}}</td>
<td rowspan="2">{{$studdata.$sn.stud_name}}</td>
<td rowspan="2">{{$studdata.$sn.stud_sex}}</td>
<td rowspan="2">{{$studdata.$sn.stud_birthyear}}</td>
<td rowspan="2">{{if (in_array(1,$v))}}v{{/if}}</td>
<td rowspan="2">{{if (in_array(2,$v))}}v{{/if}}</td>
<td rowspan="2">{{if (in_array(3,$v))}}v{{/if}}</td>
<td rowspan="2">{{if (in_array(4,$v))}}v{{/if}}</td>
<td rowspan="2">{{if (in_array(5,$v))}}v{{/if}}</td>
<td rowspan="2">{{if (in_array(6,$v))}}v{{/if}}</td>
<td rowspan="2">{{if (in_array(7,$v))}}v{{/if}}</td>
<td rowspan="2">{{$status[$d.status]}}</td>
<td>{{$d.dis_date}}</td>
<td>{{$d.diag_hos}}</td>
<td>{{if $d.chk_date!="0000-00-00"}}{{$d.chk_date}}{{else}}&nbsp;{{/if}}</td>
<td rowspan="2" style="text-align:center;">{{if (in_array(1,$vv))}}v{{/if}}</td>
<td rowspan="2">{{$vvv.0}}</td>
<td rowspan="2" style="text-align:center;">{{if (in_array(2,$vv))}}v{{/if}}</td>
<td rowspan="2">{{if $vvv.1}}<a href="#" id="j{{$i}}">***</a><ul style="display: block;" class="hovertip" target="j{{$i}}">{{$vvv.1}}</ul>{{/if}}</td>
<td rowspan="2"><a href="#" OnClick="act('edit','{{$sn}}','{{$d.dis_date}}');">編輯</a> <a href="#" OnClick="act('del','{{$sn}}','{{$d.dis_date}}');">刪除</a></td>
</tr>
<tr style="background-color:{{if $ii}}#F0F0F0{{else}}white{{/if}};">
<td>{{if $d.diag_date!="0000-00-00"}}{{$d.diag_date}}{{else}}&nbsp;{{/if}}</td>
<td>{{$d.diag_name}}</td>
<td>{{$d.chk_report}}</td>
</tr>
{{assign var=ii value=$j-$ii}}
{{foreachelse}}
<tr style="background-color:white;">
<td colspan="20" style="color:red;text-align:center;">目前無資料</td>
</tr>
{{/foreach}}
</table>
</td>
<table width="100%">
<tr bgcolor="#FBFBC4">
<td><img src="{{$SFS_PATH_HTML}}images/filefind.png" width="16" height="16" hspace="3" border="0">相關說明</td>
</tr>
<tr><td style="line-height:150%;">
<ol>
<li class="small">本程式目前為苗栗縣使用格式，其他縣市請使用「<a href="inflection.php">疑似傳染病通報</a>」程式進行記錄。</li>
<li class="small">本程式與「疑似傳染病通報」程式已完成結合，若於本程式進行記錄，將同步於「疑似傳染病通報」進行記錄。</li>
</ol>
</td></tr>
</table>
</tr>
</form>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}
