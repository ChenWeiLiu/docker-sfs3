{{* $Id: health_sight_noti.tpl 5963 2010-06-15 02:06:54Z hami $ *}}
<style>
#dialog {font-size:12px}
#dialog h1 {font-size:15px}
#dialog li {cursor: pointer; list-style: none;}
.li-selected {border:thin #e00 solid;}
#show_student_name{color:blue; border:#ccc thin double; padding:1px; margin:2px;}
#show_side{color:blue; border:#ccc thin double; padding:1px; margin:2px;}

</style>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.widget.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.position.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.resizable.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/ui/jquery.ui.dialog.js"></script>
<script type="text/javascript" src="{{$SFS_PATH_HTML}}javascripts/external/bgiframe/jquery.bgiframe.js"></script>
<script>

var manage_item = {"1":"視力保建","2":"點藥治療","3":"配鏡矯治","4":"家長未處理","5":"更換鏡片","6":"定期檢查","7":"遮眼治療","8":"另類治療","9":"配戴隱型眼鏡","N":"其它"}
var curr_student_sn = '';
var curr_side ='';
var curr_button = '';
$(document).ready(function(){

 $(".checkAll").click(function(){
	      $.each($(".snCheck"),function(){
	          $(this).attr('checked', !$(this).is(':checked'));
	      });
	 });


	$("#dialog").dialog({bgiframe: true, autoOpen: false, height: 530, modal: true});

	$(".manage_id").click(function(){
		curr_button = $(this);
		var id = $(this).attr('id');
	 	var exploded = id.split('-');
	 	curr_student_sn = exploded[2];
	 	curr_year_seme = exploded[1];
	 	curr_side = exploded[0];
	 	var student_name = $("#student_name-"+curr_student_sn).text();

	 	if (exploded[0]=='l') var side='左眼'; else var side='右眼';
	 	$("#show_student_name").html(student_name);
	 	$("#show_side").html(side);
		$("#dialog").dialog('open');

	});

	$(".sign").click(function(){
		var id =$(this).attr('id');
		var arr = id.split('_');
		if (arr[0]=='My' && $('#Hy_'+arr[1]+'_'+arr[2]).attr('checked'))
			$('#Hy_'+arr[1]+'_'+arr[2]).attr('checked','');
		else if (arr[0]=='Hy' && $('#My_'+arr[1]+'_'+arr[2]).attr('checked'))
			$('#My_'+arr[1]+'_'+arr[2]).attr('checked','');

		var value = $(this).attr('checked');
		var year_seme = $("select[name='year_seme']").val();

		$.post('ajax-sight-noti.php',{
			type : 'update_sight_noti',
			year_seme : year_seme,
			id : id,
			value: value
			},function(data){});

	});

	$("#save-manage-diag").click(function(){
		updateValue('N');
	});

	$("#manage_item li").click(function(){
		var id = $(this).attr('id').substr(5);
		if (id == 'N') {
			$("#diagDiv").show();
			return;
		}

		updateValue(id);
	});
	// 更新就診醫院
	$(".hospital").change(function(){
		var id = $(this).attr('id').substr(9);
		var val = $(this).attr('value');
		$.post('ajax-input-sight-status.php',{
			type : 'update_health_sight_hospital',
			id : id,
			val: val
		},function(data){

		});

	});

	// 更新處置
	function updateValue(id){
		var year_seme = $("select[name='year_seme']").val();
		var diag = $("#diag").attr('value');
		$.post('ajax-input-sight-status.php',{
			type : 'update_health_sight_manage_id',
			year_seme : year_seme,
			student_sn : curr_student_sn,
			side	: curr_side,
			id: id,
			diag: diag
		},function(data){
			if (data) {
				if (data.length==1)
				$(curr_button).attr('value', data+'.'+manage_item[data]);
				else
				$(curr_button).attr('value', 'N.'+data);
			}
			else
			$(curr_button).attr('value', '..');
			$("#diag").attr('value','');
			$("#diagDiv").hide();
			$("#dialog").dialog('close');
		});
	}

	// 列印清單
	$(".printListBtn").click(function(){

		var action = $("form[name='myform']").attr('action');
		$("form[name='myform']").attr('target','_blank');
		$("form[name='myform']").attr('action','sight_noti_list.php');
		$("form[name='myform']").submit();
		$("form[name='myform']").attr('target','');
		$("form[name='myform']").attr('action',action);
	});

	$("#dialog li").hover(
			function(){$(this).addClass('li-selected')},
			function(){$(this).removeClass('li-selected')}
			);
});
</script>

<input type="submit" name="print" value="列印通知單">
<input type="button"  class="printListBtn" name="print_list" value="列印清單">
<input type="button"  class="checkAll" value="全選/反選" >
<span class="small">回條繳交日期<input type="text" name="rmonth" size="2">月<input type="text" name="rday" size="2">日</span>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="2" class="small">
<tr style="background-color:#c4d9ff;text-align:center;">
<td rowspan="2">選</td>
<td rowspan="2">年級</td>
<td rowspan="2">班級</td>
<td rowspan="2">座號</td>
<td rowspan="2">姓名</td>
<td colspan="2">裸視</td>
<td colspan="2">矯正</td>
<td colspan="5">診斷與治療（右眼）</td>
<td colspan="5">診斷與治療（左眼）</td>
<td rowspan="2">處置</td>
<td rowspan="2">就診醫療院所</td>
</tr>
<tr style="background-color:#c4d9ff;text-align:center;">
<td>右眼</td>
<td>左眼</td>
<td>右眼</td>
<td>左眼</td>
<td>近視</td>
<td>遠視</td>
<td>弱視</td>
<td>散光</td>
<td>其他</td>
<td>近視</td>
<td>遠視</td>
<td>弱視</td>
<td>散光</td>
<td>其他</td>
</tr>
{{assign var=year_seme value=$smarty.post.year_seme}}
{{assign var=seme_class value=$smarty.post.class_name}}
{{foreach from=$health_data->stud_data item=seme_class key=i}}
{{assign var=year_name value=$i|@substr:0:-2}}
{{assign var=class_name value=$i|@substr:-2:2}}
{{foreach from=$seme_class item=d key=seme_num name=rows}}
{{assign var=j value=$j+1}}
{{assign var=sn value=$d.student_sn}}
{{assign var=dd value=$health_data->health_data.$sn.$year_seme}}
<tr style="background-color:white;">
<td style="background-color:#ce6"><input type="checkbox"  class="snCheck" name="student_sn[]"  value="{{$sn}}"></td>
<td style="background-color:#f4feff;">{{$year_name}}</td>
<td style="background-color:#f4feff;">{{$class_name}}</td>
<td style="background-color:#f4feff;">{{$seme_num}}</td>
<td id="student_name-{{$sn}}" style="color:{{if $health_data->stud_base.$sn.stud_sex==1}}blue{{elseif $health_data->stud_base.$sn.stud_sex==2}}red{{else}}black{{/if}};background-color:#fbf8b9;">{{$health_data->stud_base.$sn.stud_name}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_o<=0.8}}red{{else}}blue{{/if}};">{{$dd.r.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_o<=0.8}}red{{else}}blue{{/if}};">{{$dd.l.sight_o}}</td>
<td style="text-align:center;color:{{if $dd.r.sight_r<=0.8}}red{{else}}blue{{/if}};">{{$dd.r.sight_r}}</td>
<td style="text-align:center;color:{{if $dd.l.sight_r<=0.8}}red{{else}}blue{{/if}};">{{$dd.l.sight_r}}</td>
<td><input type="checkbox"  class="sign My" id="My_r_{{$sn}}"  {{if $dd.r.My}}checked{{/if}} /></td>
<td><input type="checkbox"  class="sign Hy" id="Hy_r_{{$sn}}"  {{if $dd.r.Hy}}checked{{/if}} /></td>
<td><input type="checkbox"  class="sign Ast" id="Ast_r_{{$sn}}"  {{if $dd.r.Ast}}checked{{/if}}/></td>
<td><input type="checkbox"  class="sign Amb" id="Amb_r_{{$sn}}" {{if $dd.r.Amb}}checked{{/if}} /></td>
<td><input type="checkbox"  class="sign other" id="other_r_{{$sn}}" {{if $dd.r.other}}checked{{/if}} /></td>

<td><input type="checkbox"  class="sign My" id="My_l_{{$sn}}" {{if $dd.l.My}}checked{{/if}} /></td>
<td><input type="checkbox"  class="sign Hy" id="Hy_l_{{$sn}}" {{if $dd.l.Hy}}checked{{/if}}/></td>
<td><input type="checkbox"  class="sign Ast" id="Ast_l_{{$sn}}" {{if $dd.l.Ast}}checked{{/if}}/></td>
<td><input type="checkbox"  class="sign Amb" id="Amb_l_{{$sn}}" {{if $dd.l.Amb}}checked{{/if}} /></td>
<td><input type="checkbox"  class="sign other" id="other_l_{{$sn}}" {{if $dd.l.other}}checked{{/if}}/></td>
<td>
{{if $dd.l.manage_id eq 'N'}}
<input type="button" id="r-{{$year_seme}}-{{$sn}}" class="manage_id" value="N.{{$dd.l.diag}}" />
{{else}}
<input type="button"  id="l-{{$year_seme}}-{{$sn}}"  class="manage_id" value="{{if $dd.l.manage_id}}{{$dd.l.manage_id}}.{{$manage_item[$dd.l.manage_id]}}{{else}}..{{/if}}" />
{{/if}}
</td>
<td><input type="text" size="6"  id="hospital-{{$year_seme}}-{{$sn}}" class="hospital" value="{{if $dd.l.hospital}}{{$dd.l.hospital}}{{/if}}" />
</td>
</tr>
{{/foreach}}
{{foreachelse}}
<tr><td colspan="27" style="background-color:white;text-align:center;color:red;">無資料</td></tr>
{{/foreach}}
</table>
<input type="submit" name="print" value="列印通知單">
<input type="button"  class="printListBtn" name="print_list" value="列印清單">
<input type="button"  class="checkAll" value="全選/反選" >
</td></tr></table>
</td>
</tr>
</table>

<div id="dialog"  title="處置狀況登錄">
<p><span id="show_student_name"></span> <span id="show_side"></span> 處置登錄</p>
<div id="manage_item">
<ul>
{{foreach from=$manage_item key=key item=item}}
<li id="item-{{$key}}">{{$key}}.{{$item}}</li>
{{/foreach}}
<li  id="item-">X 取消</li>
</ul>
<div id="diagDiv" style="display:none;">
其他處置: <input type="text"  id="diag" size="20" /> <input type="button" id="save-manage-diag" value="儲存" />
</div>
</div>
</div>