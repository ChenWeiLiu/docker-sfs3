
<!DOCTYPE html>
<html>
<head>


<title>
{{$my_title}}
</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<style type="text/css">
	body {
		font-family: "Helvetica Neue", Helvetica, Arial, "微軟正黑體", sans-serif;
	}

	table.table {
		font-size: 18px;
	}

	table.submitdata {
		font-size: 16px;
	}

	table.list{
		width: 100%;
	}
	table.list td{
		vertical-align: bottom;
		border: 1px solid #ddd;
	}

</style>

{{if $route != "mainView"}} {{*網頁輸出的頁面呈現*}}
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script>
console.log("hello");

{{if $update_stud_eng_names == 'yes'}}
$(document).ready(function(){
    $.post("../update_stud_eng.php",
    {
			{{foreach key=id item=eng_name from=$eng_name_format }}
				{{$id}}:"{{$eng_name}}",
			{{/foreach}}
    },
    function(data,status){
      //alert("Data: " + data);
    });
});
{{/if}} {{*end if $update_stud_eng_names == 'yes'*}}

</script>

{{/if}} {{*if $route != "mainView"*}}

</head>




<body class="container">

{{foreach key=id item=name from=$users_name_data }}

<table class="table table-hover table-condensed table-striped table-bordered" cellspacing="0" width="100%" >
    <tr class="success"> 
	<td class="col-md-12"> 雙語畢業證書姓名英譯調查 </td>
    </tr>

    <tr>
        <td>
          <table class="list" style="width:100%">
	    <tr>
	      <td width="15%">
		座號:{{$id}}  {{*座號*}} 
	      </td>
	      <td width="25%">
               姓名:{{$name}}{{*姓名*}} 
	      </td>
	      <td width="60%">
		 請於下方擇一勾選，以利製作雙語畢業證書，有護照者，從護照之音譯。 
	      </td>
	    </tr>
	  </table>
        </td>
    </tr>

    <tr>
      <td>
	姓名英譯:
	<ul>
    {{*姓名英譯*}}
    </br>
    <li> 已有護照。音譯為_____________________________ <br> (請交護照影本供導師核對)</li>
    <li> 漢語:  {{$eng_name_format['hy'].$id}} </li>
    <li> 通用: {{$eng_name_format['ty'].$id}} </li>
    <li> 威妥碼: {{$eng_name_format['wg'].$id}} </li>
    <li> 國音: {{$eng_name_format['g2'].$id}} </li>
    <br>
    <li> 其它:___________________________________
      </ul>
      </td>
    </tr>

    <tr>
        <td>
          <table class="list" style="width:100%">
	    <tr>
	      <td width="20%">
		<br>
		家長簽名
	      </td>
	      <td width="80%" style="text-align: right;" >
		  ____年_____月_____日
	      </td>
	    </tr>
	  </table>
        </td>
    </tr>

</table>
<br>
<br>

{{/foreach}}

<table class="table table-hover table-condensed table-striped table-bordered" cellspacing="0" width="100%" >
		<form name='form9' method='post' action='' target="" >
		 <input name="set_all_pinyin_metod" type="hidden" value="{{$default_pinyin}}">
		 <input name="set_name_format" type="hidden" value="{{$_post_name_format}}">
		<input name="class_name" type="hidden" value="{{$class_name}}">

  <tr class="success"> 
    <th class="col-md-1">座號</th>
    <th class="col-md-1">姓名</th>
		 <th class="col-md-1">拼音方式</th>
		 <th class="col-md-3">多音字</th>
		 <th class="col-md-3">姓名英譯</th>
{{foreach key=id item=name from=$users_name_data }}
	{{*送出名字*}}
		{{assign var='uid' value=$id}}
		{{assign var='uuid' value="users_name_data[$uid]"}}
		<input name="{{$uuid}}" type="hidden" value="{{$name}}">
	{{*end 送出名字*}}

  <tr>

	<td>{{$id}} {{*座號*}}</td>
	<td>{{$name}}{{*姓名*}}</td>
	<td style="white-space:nowrap">

{{if $route == "mainView"}}
  {{*拼音方式*}}		
  {{assign var='uid' value=$id}}
  {{assign var='uuid' value="pinyin_select[$uid]"}}
  {{html_options name="$uuid" selected=$pinyin_selected_values.$id options=$pinyin_method_options onchange="this.form.submit()"}}
{{else}}
  {{*當列印時的樣式*}}
  {{$pinyin_method_options[$pinyin_selected_values.$id]}}
{{/if}}
	</td>
	<td>

	{{*多音字*}}
	{{if isset($users_multi_ph.$id)}}
		{{foreach from=$users_multi_ph.$id key=pos item=multi_ph_options}}
			{{$hanzi.$id.$pos.chinese}}
				{{assign var='uid' value=$id}}
				{{assign var='uuid' value="ph_select[$uid][$pos]]"}}

				{{html_options name=$uuid options=$multi_ph_options selected=$post_ph_selected_values.$id.$pos onchange="this.form.submit()"}}
		{{/foreach}}
	{{/if}}


{{if $route != "mainView"}}
	{{if isset($users_multi_ph.$id)}}
		{{foreach from=$users_multi_ph.$id key=pos item=multi_ph_options}}
			<span class="multi_ph">
			{{$hanzi.$id.$pos.chinese}}:
			{{assign var = 'ph_sn' value=$post_ph_selected_values.$id.$pos}}
			{{$users_multi_ph.$id.$pos.$ph_sn}}
			</span>
		 {{/foreach}}
  {{/if}}
{{/if}}  {{*end if $route != "mainView"*}}
	</td>
  <td>
    {{*姓名英譯*}}
    {{$eng_name_format.$id}}
  </td>
</tr>

{{/foreach}}

		</form>
</table>

<br/>
    <div>
	{{*$eng_name_format|@print_r*}}
      <p>本次查詢共{{$time_elapsed}}秒完成</p>
    </div>


</body>
</html>
