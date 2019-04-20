{{* $Id: score_input_normal_quick.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<html>
<meta http-equiv="Content-Type" content="text/html;Charset=Big5">
<head>
<title>平時成績快速輸入</title>
</head>

<script>
<!--
var ss=0;
var is_change=false;

function set_default(){
	document.base_form.elements[ss].focus();
}

function check_change(){
	if(is_change){
		if (confirm('您已經更改資料是否要離開 ?'))
		window.close();
	} else {
		window.close();
	}
}

function set_ower(thetext,ower) {
	ss=ower;
	thetext.style.background="lemonchiffon";
	return true;
}

function unset_ower(thetext) {
	thetext.style.background="white";
	return true;
}

function reset_all() {
	for (var i=0;i<document.base_form.elements.length;i++) {
		var e = document.base_form.elements[i];
		if (e.type=="text") e.value="";
	}
	document.base_form.elements[0].focus();
}

// handle keyboard events
if (navigator.appName=="Mozilla")
	document.addEventListener("keyup",keypress,true);
else if (navigator.appName=="Netscape")
	document.captureEvents(Event.KEYPRESS);

if (navigator.appName != "Mozilla")
	document.onkeypress=keypress;

function keypress(e) {
	if (navigator.appName == "Microsoft Internet Explorer")
		tmp=window.event.keyCode;
	else if (navigator.appName=="Navigator" || navigator.appName=="Netscape")
		tmp=e.which;
	else if (navigator.appName=="Mozilla")
		tmp=e.keyCode;
  if(document.base_form.elements[ss].type != "text")
		return true;
	else if (tmp==13) { 
		var tt = parseFloat(document.base_form.elements[ss].value);
		if (isNaN(tt) || tt>100 || tt<0) {			
			alert('錯誤的分數!');
			document.base_form.elements[ss].value="";
			return false;
	}	else {			
			ss++;
			document.base_form.elements[ss].focus();
			is_change=true;
			return true;
		}
	} else
	return true;
}
//-->
</script>

<body onLoad="{{if $smarty.post.quick}}self.close(){{else}}set_default(){{/if}}">
<form name="base_form" action="{{$smarty.server.PHP_SELF}}" method="post">
<b>{{$sel_year}}學年度第{{$sel_seme}}學期「{{$full_class_name}}」成績輸入</b>
<table bgcolor="black" border="0" cellpadding="2" cellspacing="1">
{{assign var=freq value=$smarty.request.quick}}
{{assign var=stage value=$smarty.request.curr_sort}}
{{foreach from=$stud_list item=sv key=sn name=stud_list}}
{{if $smarty.foreach.stud_list.iteration % 3 == 1}}<tr bgcolor="#ffffff">{{/if}}
		<td align="center" bgcolor="gold">{{$stud_list.$sn.site_num}}</td>
		<td align="center" bgcolor="pink">{{$stud_list.$sn.name}}</td>
		{{assign var=score value=$data_arr.score.$stage.$freq.$sn}}
		<td align="center"><input type="text" name="nor_score[{{if $score==""}}n{{/if}}{{$sn}}]" value="{{if $score!="-100"}}{{$score}}{{/if}}" size="4" OnFocus="set_ower(this,{{$smarty.foreach.stud_list.iteration-1}})" OnBlur="unset_ower(this)"></td>
{{if $smarty.foreach.stud_list.iteration % 3 == 0}}</tr>{{/if}}
{{/foreach}}
</table><br>
<input type="button" name="ok" value="登錄成績" onClick="document.base_form.submit()">
<input type="button" name="go_away" value="放棄" onClick="check_change()">
<input type="button" name="reset_allBtn" value="清空" onClick="reset_all()">
<input type="hidden" name="teacher_course" value="{{$smarty.request.teacher_course}}">
<input type="hidden" name="class_subj" value="{{$smarty.request.class_subj}}">
<input type="hidden" name="curr_sort" value="{{$stage}}">
<input type="hidden" name="freq" value="{{$freq}}">
<input type="hidden" name="quick" value="{{$freq}}">
<input type="hidden" name="save" value="1">
<input type="hidden" name="test_name" value="{{$data_arr.status.$stage.$freq.name}}">
<input type="hidden" name="weighted" value="{{$data_arr.status.$stage.$freq.weighted}}">
</form>
</body>
</html>