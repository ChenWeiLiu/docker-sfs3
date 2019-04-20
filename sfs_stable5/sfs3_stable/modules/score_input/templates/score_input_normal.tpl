{{* $Id: score_input_normal.tpl 7134 2013-02-22 05:44:32Z hsiao $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

<script>
<!--
{{if $smarty.get.edit}}
function file_act(a) {
	document.base_form.action="nor_upd.php";
	document.base_form.act.value=a;
	document.base_form.submit();
}
{{/if}}
function openwindow(url_str){ 	 
	win=window.open (url_str,"成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=500,height=500"); 	 
	win.creator=self;
}

function unset_ower(thetext) {
	if(thetext.value>100){ thetext.style.background = '#FF0000'; alert("輸入成績高於100分");}
	else if(thetext.value<0){ thetext.style.background = '#AA5555'; alert("輸入成績為負數"); }
	else { thetext.style.background = '#FFFFFF'; }
	return true;
}
//-->
</script>
<table border="0" width="100%" cellspacing="1" cellpadding="2" bgcolor="#CCCCCC">
	<tr>
		<td width="100%" valign="top" bgcolor="#ffffff">
		<table cellpadding="0" cellspacing="0">
			<form name="menu_form" action="{{$smarty.server.PHP_SELF}}" method="post">
			<tr><td>{{$course_sel}}
			{{if $smarty.request.teacher_course}}
				{{$stage_sel}}
				{{if $is_send==0}}
					{{if $is_new_nor=="y"}}<input type="submit" name="add" value="新增一次平時考查成績">{{/if}}
				{{/if}}
			{{/if}}
			
			{{if count($stud_list)>0}}
				<table bgcolor="black" border="0" cellpadding="2" cellspacing="1">
				</form>		
				<form name="base_form" action="{{$smarty.server.PHP_SELF}}" method="post">
				<tr bgcolor="#E1ECFF" align="center">
					<td>學號</td>
					<td>座號</td>
					<td>姓名</td>
					{{if $pic_checked}}<td>大頭照</td>{{/if}}
					{{foreach from=$data_arr.status item=dsv key=stage}}
						{{foreach from=$data_arr.status.$stage item=dsvs key=freq}}
						{{assign var=tid value=$data_arr.status.$stage.$freq.teach_id}}
							{{if $freq==$smarty.get.edit && $smarty.session.session_log_id==$data_arr.status.$stage.$freq.teach_id}}
								<td class="small" align="center">
								{{if $is_mod_nor=='y'}}
									<input type="text" name="test_name" size="10" maxlength="40" value="{{$data_arr.status.$stage.$freq.name}}"><br>加權：<input type="text" name="weighted" size="5" maxlength="5" value="{{$data_arr.status.$stage.$freq.weighted}}">
								{{else}}
									{{$data_arr.status.$stage.$freq.name}}<br>加權：{{$data_arr.status.$stage.$freq.weighted}}<input type="hidden" name="test_name" value="{{$data_arr.status.$stage.$freq.name}}"><input type="hidden" name="weighted" value="{{$data_arr.status.$stage.$freq.weighted}}">
								{{/if}}
								{{if $is_send==0}}
									<br><a href="{{$smarty.server.PHP_SELF}}?edit={{$freq}}&teacher_course={{$smarty.request.teacher_course}}&class_subj={{$class_subj}}&curr_sort={{$curr_sort}}"><img src='./images/pen.png' border='0'></a>
									{{if $is_new_nor=='y'}}
										<a href="nor_del.php?del={{$freq}}&teacher_course={{$smarty.request.teacher_course}}&class_subj={{$class_subj}}&stage={{$curr_sort}}" onClick="return confirm('確定刪除這次成績 ?');"><img src='./images/del.png' border='0'></a>
									{{/if}}
								{{/if}}
								</td>
							{{else}}
								<td class="small" align="center">{{$data_arr.status.$stage.$freq.name}}<br>加權：{{$data_arr.status.$stage.$freq.weighted}}
								{{if ($smarty.session.session_log_id|lower)==($data_arr.status.$stage.$freq.teach_id|lower)}}
									{{if $is_send==0}}
										<br><a OnClick="openwindow('{{$smarty.server.PHP_SELF}}?quick={{$freq}}&teacher_course={{$smarty.request.teacher_course}}&class_subj={{$class_subj}}&curr_sort={{$curr_sort}}')"><img src="images/wedit.png" border="0"></a>
										<a href="{{$smarty.server.PHP_SELF}}?edit={{$freq}}&teacher_course={{$smarty.request.teacher_course}}&class_subj={{$class_subj}}&curr_sort={{$curr_sort}}"><img src='./images/pen.png' border='0'></a>
										{{if $is_new_nor=='y'}}
											<a href="nor_del.php?del={{$freq}}&teacher_course={{$smarty.request.teacher_course}}&class_subj={{$class_subj}}&stage={{$curr_sort}}" onClick="return confirm('確定刪除這次成績 ?');"><img src='./images/del.png' border='0'></a>
										{{/if}}
									{{/if}}
								{{else}}
									<br><font color="blue">[ {{$teacher_arr.$tid}} ]</font>
								{{/if}}
								</td>
							{{/if}}
						{{/foreach}}
							<td>平均</td>
						{{foreachelse}}
					{{/foreach}}
				</tr>
				{{foreach from=$stud_list item=sv key=sn}}
					<tr bgcolor="#ffffff">
						<td align="center">{{$stud_list.$sn.stud_id}}</td>
						<td align="center">{{$stud_list.$sn.site_num}}</td>
						<td align="center">{{$stud_list.$sn.name}}</td>
						{{if $pic_checked}}
							{{if $stud_list.$sn.pic}}
								<td align="center">
									<img src="{{$UPLOAD_URL}}photo/student/{{$stud_list.$sn.stud_study_year}}/{{$stud_list.$sn.stud_id}}" width={{$pic_width}}>
								</td>
							{{else}}
								<td>
								</td>
							{{/if}}
						{{/if}}
							{{foreach from=$data_arr.status item=dsv key=stage}}
								{{foreach from=$data_arr.status.$stage item=dsvs key=freq}}
									{{assign var=score value=$data_arr.score.$stage.$freq.$sn}}
									{{if $freq==$smarty.get.edit && $smarty.session.session_log_id==$data_arr.status.$stage.$freq.teach_id}}
									<td align="center" {{if $score<60 && $score!="-100"}}bgcolor="red"{{/if}}><input type="text" name="nor_score[{{if $score == ''}}n{{/if}}{{$sn}}]" value="{{if $score!="-100"}}{{$score}}{{/if}}" size="8" onBlur="unset_ower(this)"></td>
									{{else}}
									<td align="center">{{if $score=="-100"}} {{else}}{{if $score<60}}<font color="red">{{/if}}{{$score}}{{if $score<60}}</font>{{/if}}{{/if}}</td>
									{{/if}}
								{{/foreach}}
									{{assign var=avg value=$data_arr.score.$stage.avg.$sn}}
									<td align="right">{{if $avg=="-100"}} {{else}}{{if $avg<60}}<font color="red">{{/if}}{{$avg}}{{if $avg<60}}</font>{{/if}}{{/if}}</td>
								{{foreachelse}}
							{{/foreach}}
				{{/foreach}}
				</table>
				{{assign var=edit_freq value=$smarty.get.edit}}
				{{if $smarty.get.edit && $smarty.session.session_log_id==$data_arr.status.$stage.$edit_freq.teach_id}}
				<input type="submit" name="save" value="儲存"> <input type="button" value=" 匯入「{{$data_arr.status.$curr_sort.$edit_freq.name}}」 " OnClick="file_act('file_in')"> <input type="button" value=" 匯出「{{$data_arr.status.$curr_sort.$edit_freq.name}}」 " OnClick="file_act('file_out')"><br>
				{{/if}}
				<input type="hidden" name="teacher_course" value="{{$smarty.request.teacher_course}}">
				<input type="hidden" name="curr_sort" value="{{$curr_sort}}">
				<input type="hidden" name="freq" value="{{$edit_freq}}">
				<input type="hidden" name="class_subj" value="{{$class_subj}}">
				<input type="hidden" name="act" value="">
				{{if $is_send==0}}<input type="submit" name="trans" value="匯到學期的階段成績">{{/if}}
				</td>
			{{/if}}
			</tr>
			</form>
		</table>
	</tr>
</table>

{{include file="$SFS_TEMPLATE/footer.tpl"}}