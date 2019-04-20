{{* $Id: academic_record_chk.tpl 7514 2013-09-12 06:16:05Z smallduh $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
	var remote=null;
	function OpenWindow(p,x){
		strFeatures ="top=300,left=20,width=500,height=210,toolbar=0,resizable=no,scrollbars=yes,status=0";
		remote = window.open("comment.php?cq="+p,"MyNew", strFeatures);
		if (remote != null) {
			if (remote.opener == null) remote.opener = self;
		}
		if (x == 1) { return remote; }
	}

	function checkmemo(){
		var str='';
		var len = $(".setArea").length;
		for(i=0;i<len ;i++){
			if ($("#V{{$student_sn}}_"+i).val() !=''){
				str += $("#V{{$student_sn}}_"+i).val();
				if (i<{{$itemdata.nums|@count}}){
					str += '，';
				}
			}
		}
		if (str !=''){
			str = str.substring(0,str.length-1) + '。';
			$("#nor_memo0").val(str);
		}
	}

	function set_chk(a){
		var i=0, v=new Array(10);
{{foreach from=$chk_item item=d key=i}}
		v[{{$i}}]="{{$d}}";
{{/foreach}}
		if (confirm('將全部項目的表現狀況都改成「'+v[a]+'」?')){
			while (i < document.myform.elements.length) {
				b=document.myform.elements[i].id.substring(0,1);
				if (b=='c') {
					c=document.myform.elements[i].id.substring(1,2);
					if (c==a)
						document.myform.elements[i].checked=true;
					else
						document.myform.elements[i].checked=false;
				}
				i++;
			}
			alert('此動作並未進行儲存, 請記得按下「確定儲存」改變才會生效!');
		}
	}
	
	function form_signal() {
	 signal_1.style.display="block";
	 signal_2.style.display="block";
	 signal_3.style.display="block";
	 past_1.style.display="none";
	}
	
  function form_past() {
	 signal_1.style.display="none";
	 signal_2.style.display="none";
	 signal_3.style.display="none";
	 past_1.style.display="block";
	}
</script>

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4">
<form name="myform" method="post" action="{{$smarty.server.PHP_SELF}}">
	<tr class="small">
		
		<td valign="top" align="center">{{$date_select}}<BR>{{$class_select}}<BR>{{$stud_select}}
			<BR><input type='checkbox' name='chknext' value='1' {{if $smarty.post.chknext}}checked{{/if}}>自動跳下一位
		</td>
		
		<td bgcolor="#FFFFFF" valign="top">
		{{if $student_sn}}
			<p align="center">
			<font size="3">{{$sch_cname}}{{$sel_year}}學年度第{{$sel_seme}}學期日常生活表現檢核表</p>
			<table align="center" cellspacing="4" id="signal_1">
				<tr>
					<td>班級：<font color="blue">{{$class_name}}</font></td><td width="40"></td>
					<td>座號：<font color="green">{{$stu_class_num}}</font></td><td width="40"></td>
					<td>姓名：<font color="red">{{$stu.stud_name}}</font></td>
				</tr>
				<tr>
					<td colspan="5">
						<fieldset class="small">
							<span style="color:blue;">表現狀況全設為：</span>
								{{foreach from=$chk_item item=c key=i}}
								<input type="radio" OnClick="set_chk({{$i}});">{{$c}}&nbsp;
								{{/foreach}}
						</fieldset>
						<fieldset>
								<font size=2 color='brown'><input type="checkbox" name="auto_spe" value="1" {{if $smarty.post.auto_spe}}checked{{/if}} onclick="this.form.submit();">列示本學期本學生已登載的輔導資料-特殊表現紀錄</font>
						</fieldset>
					</td>
				</tr>
			</table>
			</font>
			
			<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%" id="signal_2">
				<tr bgcolor="#c4d9ff">
					<td colspan="2" align="center">日常行為表現項目</td><td align="center">表現狀況</td><td align="center">具體建議</td>
				</tr>
					{{foreach from=$itemdata.items item=d key=i}}
					{{assign var=main value=$d.main}}
					{{assign var=sub value=$d.sub}}
					{{if $d.sub!=1}}
				<tr bgcolor="white">
					{{/if}}
					{{if $d.sub==0}}
					<td rowspan="{{$itemdata.nums.$main.num-1}}" class="small">{{$d.item}}</td>
					{{else}}
					<td class="small" width="180">{{$d.item}}</td><td class="small">{{$chk_value.$main.$sub.score}}</td>
					{{if $d.sub==1}}
					{{* <td rowspan="{{$itemnum.$main.num-1}}" class="small"><img src="../../images/comment.png" width="16" height="16" border="0" align="left" onClick="return OpenWindow('V{{$student_sn}}_{{$main}}')"><input type="text" name="stud_memo[{{$student_sn}}][{{$main}}]" id="V{{$student_sn}}_{{$main}}" value="{{$chk_value.$main.0.memo}}" style="width:100pt;"></td> *}}
					<td rowspan="{{$itemdata.nums.$main.num-1}}" class="small"><textarea class="setArea" name="chk[{{$student_sn}}][{{$main}}][memo]" id="V{{$student_sn}}_{{$main}}" style="width:100pt;"  rows="{{$itemdata.nums.$main.num-1}}" onblur="checkmemo()">{{$chk_value.$main.0.memo}}</textarea></td>
					{{/if}}
				</tr>
					{{/if}}
				{{foreachelse}}
			
			<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%">
				<tr style="background-color:yellow;color:red;">
					<td>本學期尚未設定檢核表，目前無法輸入，請盡速連絡學務（訓導）處。</td>
				</tr>
			{{/foreach}}
			{{if $itemdata.items}}
				<tr bgcolor="#c4d9ff"><td colspan="4" style="text-align:center;">日常生活表現</td></tr>
				<tr bgcolor="#d4e9ff"><td colspan="4" style="text-align:center;"><textarea rows="2" cols="80" id="nor_memo0" name="nor_memo[{{$student_sn}}][0]">{{$nor_memo.0}}</textarea></td></tr>
				<tr bgcolor="#c4d9ff"><td colspan="4" style="text-align:center;">團體活動紀錄</td></tr>
				<tr bgcolor="#d4e9ff"><td colspan="4" style="text-align:center;"><textarea rows="2" cols="80" name="nor_memo[{{$student_sn}}][1]">{{$nor_memo.1}}</textarea></td></tr>
				<tr bgcolor="#c4d9ff"><td colspan="4" style="text-align:center;">公共服務紀錄</td></tr>
				<tr bgcolor="#d4e9ff"><td colspan="4" style="text-align:center;">
			
			<table border="0" class="small">
			 <tr>
			  <td width="50%">
				<fieldset>
					<legend>校內服務</legend>
					<textarea rows="2" cols="36" name="nor_memo[{{$student_sn}}][2]">{{$nor_memo.2}}</textarea>
				</fieldset>
			  </td>
			  <td>
				 <fieldset>
					<legend>社區服務</legend>
						<textarea rows="2" cols="36" name="nor_memo[{{$student_sn}}][3]">{{$nor_memo.3}}</textarea>
				 </fieldset>
				 </td>
				</tr>
			</table>
			
			</td></tr>
		<tr bgcolor="#c4d9ff"><td colspan="4" style="text-align:center;">校內外特殊表現紀錄</td></tr>
		<tr bgcolor="#d4e9ff"><td colspan="4" style="text-align:center;">
			<table border="0" class="small">{{if $smarty.post.auto_spe}}
			<tr>				
				<td valign="top" align="center">{{if $spe_data_1}}輔導-校內特殊表現紀錄參考：<BR><TEXTAREA rows="4" cols="36" name="eduh_memo4" disabled>{{$spe_data_1}}</TEXTAREA><BR><input type="button" name="paste_it" value="複製至檢核表-校內特殊表現" onclick="document.getElementById('nor_memo4').value=document.myform.eduh_memo4.value;">{{/if}}</td>
   			<td valign="top" align="center">{{if $spe_data_2}}輔導-校外特殊表現紀錄參考：<BR><TEXTAREA rows="4" cols="36" name="eduh_memo5" disabled>{{$spe_data_2}}</TEXTAREA><BR><input type="button" name="paste_it" value="複製至檢核表-校外特殊表現" onclick="document.getElementById('nor_memo5').value=document.myform.eduh_memo5.value;">{{/if}}</td>
			</tr>{{/if}}<tr><td width="50%">
       <fieldset>
			<legend>校內特殊表現</legend>
			<textarea rows="5" cols="36" name="nor_memo[{{$student_sn}}][4]" id="nor_memo4" bgcolor="#FFFCCCC">{{$nor_memo.4}}</textarea>
			</fieldset>
			</td><td>
     <fieldset>
			<legend>校外特殊表現</legend>
			<textarea rows="5" cols="36" name="nor_memo[{{$student_sn}}][5]" id="nor_memo5" bgcolor="#FFFCCCC">{{$nor_memo.5}}</textarea>
		</fieldset>
	
		
</tr></table>
</td></tr>

</table>

<table border="0" width="100%" id="signal_3">
 <tr>
  <td align="center">
		<input type="submit" name="save" value="確定儲存" OnClick="document.myform.nav_next.value='{{$next_student_sn}}';">
		<input type="submit" value="還原">
		<input type="submit" name="clear" value="清除">
		<input type="button" value="快貼後五欄文字記錄" onclick="form_past()">
  </td>
  </tr>
</table>
{{/if}}
<input type='hidden' name='nav_next' value="">
<input type='hidden' name='class_reset' value="">
<input type='hidden' name='semester_reset' value="">
</form>

<!---快貼用的表單 -->
 <table border="0" width="100%" id="past_1" style="display:none">
 <form method="post" name="pastform" action="{{$smarty.server.PHP_SELF}}">
  <input type="hidden" name="class_id" value="{{$class_id}}">
  <input type="hidden" name="mode" value="pastALL">
  <tr>
   <td>快貼全班記錄明細 class_id: {{$class_id}}</td>
  </tr>
  <tr>
   <td>
    <textarea name="stud_data" cols="100" rows="10"></textarea>
   </td>
  </tr>
  <tr>
   <td>
   <input type="submit" value="確定貼上"><input type="button" value="返回單筆新增" onclick="form_signal()">
   </td>
  </tr>
  <tr>
   <td>
      <table border="1" width="100%" style="border-collapsecollapse" bordercolor="#CCCCCC">
     <tr>
       <td style="color:#800000;font-size:9pt">
       ※說明 : 如果您習慣以 Excel 等方式管理學生日常生活表現記錄 (Excel範例檔:<a href="images/demo.xls">下載</a>) ,本程式可方便您快速鍵入全班學生的生活表現記錄。<br>請直接選擇內容部分如圖, 複製/貼上再送出即可.<br>
       <img src="images/demo.jpg" border="0" width="80%"><br>
       注意! 欄位的順序必須正確,一列為一個學生資料, 座號與姓名是用於比對, 必須正確該筆資料才會存入.
       </td>
     </tr>
   </table>	

   </td>
  </tr>
  </form>
 </table>
 <!--------------------->
</td>
{{/if}}
</tr>
</table>



{{include file="$SFS_TEMPLATE/footer.tpl"}}
