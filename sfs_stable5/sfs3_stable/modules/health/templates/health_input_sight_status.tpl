{{* $Id: health_input_sight_status.tpl 5910 2010-03-17 03:55:59Z hami $ *}}

<style>
#dialog {font-size:11px}
#dialog li {cursor: pointer; list-style: none;}
.li-selected {border:thin #e00 solid;}
</style>
<script>
var manage_item = {"1":"視力保建","2":"點藥治療","3":"配鏡矯治","4":"家長未處理","5":"更換鏡片","6":"定期檢查","7":"遮眼治療","8":"另類治療","9":"配戴隱型眼鏡","	N":"其它"}
var curr_year_seme = '';
var curr_student_sn = '';
var curr_side ='';
var curr_button = '';
function chk(a,b) {
	c=document.getElementById(b+a).checked;
	document.getElementById('M'+a).checked=false;
	document.getElementById('H'+a).checked=false;
	document.getElementById('A'+a).checked=false;
	document.getElementById(b+a).checked=c;
}

function showManageItem() {
	var ss = '<ul>';
	$.each(manage_item,function(i,v){
		ss += '<li><sapn class="select-item" id="item-'+i+'">'+i+'.'+v+'</span></li>';
	});
	ss +='<li><span class="select-item" id="item-">取消</span></li>';
	ss += '</ul>';
	$("#manage_item").html(ss);
}

$(document).ready(function(){
	//showManageItem();
	$("#manage_item li").click(function(){
		var id = $(this).attr('id').substr(5);
		$.post('ajax-input-sight-status.php',{
			type : 'update_health_sight_manage_id',
			year_seme : curr_year_seme,
			student_sn : curr_student_sn,
			side	: curr_side,
			id: id
		},function(data){
			if (data) {
				$(curr_button).attr('value', manage_item[data]);
			}
			else
			$(curr_button).attr('value', '..');
			$("#dialog").dialog('close');
		});
	});

	$("#dialog").dialog({bgiframe: true, autoOpen: false, height: 300, modal: true});
	$(".manage_id").click(function(){
		curr_button = $(this);
		var id = $(this).attr('id');
	 	var exploded = id.split('-');
	 	curr_student_sn = exploded[2];
	 	curr_year_seme = exploded[1];
	 	curr_side = exploded[0];
	 	var student_name = $("#student_name-"+curr_student_sn).text();
	 	if (exploded[0]=='l') var side='左眼'; else var side='右眼';
	 	$("#dialog h1").html(student_name+side+'處置登錄');
		//console.log($(this).parents('tr').children('.student_name'));
		$("#dialog").dialog('open');
	});

	$("#dialog li").hover(
			function(){$(this).addClass('li-selected')},
			function(){$(this).removeClass('li-selected')}
			);
});


</script>

<input type="submit" name="save" value="確定儲存">
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr bgcolor="#c4d9ff">
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="6">右眼</td>
<td align="center" colspan="6">左眼</td>
<td align="center" rowspan="2">座號</td>
<td align="center" rowspan="2">姓名</td>
<td align="center" colspan="6">右眼</td>
<td align="center" colspan="6">左眼</td>
</tr>
<tr bgcolor="#c4d9ff">
<td align="center">近<br>視</td>
<td align="center">遠<br>視</td>
<td align="center">弱<br>視</td>
<td align="center">散<br>光</td>
<td align="center">其<br>他</td>
<td align="center">處<br>置</td>
<td align="center">近<br>視</td>
<td align="center">遠<br>視</td>
<td align="center">弱<br>視</td>
<td align="center">散<br>光</td>
<td align="center">其<br>他</td>
<td align="center">處<br>置</td>
<td align="center">近<br>視</td>
<td align="center">遠<br>視</td>
<td align="center">弱<br>視</td>
<td align="center">散<br>光</td>
<td align="center">其<br>他</td>
<td align="center">處<br>置</td>
<td align="center">近<br>視</td>
<td align="center">遠<br>視</td>
<td align="center">弱<br>視</td>
<td align="center">散<br>光</td>
<td align="center">其<br>他</td>
<td align="center">處<br>置</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data.$seme_class item=d key=seme_num name=rows}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
{{if $smarty.foreach.rows.iteration % 2==1}}
<tr style="background-color:white;">
{{/if}}
{{counter assign=i}}
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td id="student_name-{{$sn}}" style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td align="center"><input type="checkbox" id="M{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][r][My]" value="1" {{if $dd.r.My}}checked{{/if}} OnChange="chk('{{$i}}','M');">{{if $dd.r.My}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][My]" value="1">{{/if}}</td>
<td align="center"><input type="checkbox" id="H{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][r][Hy]" value="1" {{if $dd.r.Hy}}checked{{/if}} OnChange="chk('{{$i}}','H');">{{if $dd.r.Hy}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][Hy]" value="1">{{/if}}</td>
<td align="center"><input type="checkbox" id="A{{$i}}" name="update[new][{{$sn}}][{{$year_seme}}][r][Ast]" value="1" {{if $dd.r.Ast}}checked{{/if}} OnChange="chk('{{$i}}','A');">{{if $dd.r.Ast}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][Ast]" value="1">{{/if}}</td>
<td align="center"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][r][Amb]" value="1" {{if $dd.r.Amb}}checked{{/if}}>{{if $dd.r.Amb}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][Amb]" value="1">{{/if}}</td>
<td align="center"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][r][other]" value="1" {{if $dd.r.other}}checked{{/if}}>{{if $dd.r.other}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][r][other]" value="1">{{/if}}</td>
<td>
<input type="button" id="r-{{$year_seme}}-{{$sn}}" class="manage_id" value="{{if $dd.r.manage_id}}{{$manage_item[$dd.r.manage_id]}}{{else}}..{{/if}}" />
</td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][l][My]" value="1" {{if $dd.l.My}}checked{{/if}}>{{if $dd.l.My}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][My]" value="1">{{/if}}</td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][l][Hy]" value="1" {{if $dd.l.Hy}}checked{{/if}}>{{if $dd.l.Hy}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][Hy]" value="1">{{/if}}</td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][l][Ast]" value="1" {{if $dd.l.Ast}}checked{{/if}}>{{if $dd.l.Ast}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][Ast]" value="1">{{/if}}</td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][l][Amb]" value="1" {{if $dd.l.Amb}}checked{{/if}}>{{if $dd.l.Amb}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][Amb]" value="1">{{/if}}</td>
<td align="center" style="background-color:#f0f0ff;"><input type="checkbox" name="update[new][{{$sn}}][{{$year_seme}}][l][other]" value="1" {{if $dd.l.other}}checked{{/if}}>{{if $dd.l.other}}<input type="hidden" name="update[old][{{$sn}}][{{$year_seme}}][l][other]" value="1">{{/if}}</td>
<td>
<input type="button"  id="l-{{$year_seme}}-{{$sn}}"  class="manage_id" value="{{if $dd.l.manage_id}}{{$manage_item[$dd.l.manage_id]}}{{else}}..{{/if}}" />
</td>
{{if $smarty.foreach.rows.iteration % 2==0}}
</tr>
{{/if}}
{{/foreach}}
</table>
<input type="submit" name="save" value="確定儲存">

<div id="dialog"  title="處置狀況登錄">
<h1></h1>
<div id="manage_item">
<ul>
{{foreach from=$manage_item key=key item=item}}
<li id="item-{{$key}}">{{$key}}.{{$item}}</li>
{{/foreach}}
<li  id="item-">取消</li>
</ul>
</div>
</div>

