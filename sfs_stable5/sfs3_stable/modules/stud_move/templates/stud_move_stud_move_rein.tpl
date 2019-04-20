{{* $Id: stud_move_stud_move_rein.tpl 9169 2017-11-13 09:01:26Z tuheng $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}

{{$schoolist}}
{{$fillcity}}


<script>
function checkok()	{
	var OK=true;	
	if(document.myform.move_out_kind.value=='')
	{	alert('未選擇調出類別');
		OK=false;
	}	
	if(document.myform.student_sn.value=='')	{
		alert('未選擇學生');
		OK=false;
	}	
	if(document.myform.move_kind.value=='')
	{	alert('未選擇復學類別');
		OK=false;
	}	
	if(document.myform.stud_class.value==0)	{
		alert('未選擇班級');
		OK=false;
	}
	if (OK==true) return confirm('確定新增 '+document.myform.student_sn.options[document.myform.student_sn.selectedIndex].text+' '+document.base_form.move_kind.options[document.base_form.move_kind.selectedIndex].text+'記錄 ?');
	return OK
}

function openModal(studentnewsn,stud_name,stud_id,stud_birthday,stud_in_class,stud_out_school_info)
{
  var para = studentnewsn + ';' + stud_name.trim() + ';' + stud_id + ';' + stud_birthday+ ';' + stud_in_class.trim() + ';' + stud_out_school_info.trim() + ';' + '{{$session_schoolnameeduno}}';
  para = encodeURIComponent(para);
  var targeturi = encodeURI("{{$sesion_path}}"+para);
  window.open(targeturi);
}

//-->
</script>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td width="100%" valign=top bgcolor="#CCCCCC">
    <form name ="myform" action="{{$smarty.server.SCRIPT_NAME}}" method="post" >
		<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body >
			<tr>
				<td class=title_mbody colspan=2 align=center > 學生復學作業 </td>
			</tr>
			{{if $smarty.get.do_key!="edit"}}
		    <tr>
				<td class=title_sbody2>選擇學期</td>
				<td>{{$seme_sel}}</td>
			</tr>
			
			
			<tr>
				<td class=title_sbody2>調出類別</td>
				<td>{{$out_kind_sel}}</td>
			</tr>
			{{/if}}
			<tr>
				<td class=title_sbody2>選擇學生</td>
				<td>{{$stud_sel}}</td>
			</tr>
			<tr>
				<td class=title_sbody2>復學類別</td>
				<td>{{$move_kind_sel}}</td>
			</tr>
			{{if $smarty.get.do_key!="edit"}}
			<tr>
				<td class=title_sbody2>選擇班級</td>
				<td>{{$class_sel}}</td>
			</tr>
			{{/if}}
			<tr>
				<td class=title_sbody2>生效日期</td>
				<td> 西元 <input type="text" size="10" maxlength="10" name="move_date" id="move_date" value="{{if $default_date}}{{$default_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}"></td>
			</tr>
                                            <tr>
                        <td align="right" CLASS="title_sbody2">請選擇原就讀學校</td>
                        <td><SELECT  NAME="selectcity" onChange="SelectCity();" ><Option value="">請選擇縣市</option></SELECT>&nbsp;<SELECT  NAME="selectdistrict" onChange="SelectDistrict();" ><Option value="">請選擇區域</option></SELECT>&nbsp;<SELECT NAME="selectschool" onchange="disp_text();"><Option value="">請選擇學校</option></SELECT></td>
                    </tr>

                    <tr>
                        <td align="right" CLASS="title_sbody2">原就讀縣市</td>
                        <td><input type="text" size="20" maxlength="20" name="city" value="{{$default_city}}" readyonly></td>
                    </tr>
                    <tr>
                        <td align="right" CLASS="title_sbody2">原就讀學校</td>
                        <td><input type="text" size="20" maxlength="20" name="school" value="{{$default_school}}" readyonly></td>
                    </tr>
                    <tr> 
                        <td align="right" CLASS="title_sbody2">原就讀學校教育部代碼</td>   
                        <td><input type="text" size="10" maxlength="6" name="school_id" value="{{$default_school_id}}" readyonly></td>   
                    </tr> 
			<tr>
				<td align="right" CLASS="title_sbody2">異動核准機關名稱</td>
				<td><input type="text" size="30" maxlength="30" name="move_c_unit" value="{{$default_unit}}"></td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody2">核准日期</td>
				<td> 西元 <input type="text" size="10" maxlength="10" name="move_c_date" id="move_c_date" value="{{if $default_c_date}}{{$default_c_date}}{{else}}{{$smarty.now|date_format:"%Y-%m-%d"}}{{/if}}"></td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody2">核准字</td>
				<td><input type="text" size="20" maxlength="20" name="move_c_word" value="{{$default_word}}"> 字 </td>
			</tr>
			<tr>
				<td align="right" CLASS="title_sbody2">核准號</td>
				<td> 第 <input type="text" size="14" maxlength="14" name="move_c_num" value="{{if $default_c_num}}{{$default_c_num}}{{/if}}"> 號 </td>
			</tr>
			<tr>
	    	<td width="100%" align="center" colspan="5" >
	    	<input type="hidden" name="move_id" value="{{$smarty.get.move_id}}">
				<input type=submit name="do_key" value ="{{if $smarty.get.do_key!="edit"}} 確定復學 {{else}} 確定修改 {{/if}}" onClick="return {{if $smarty.get.do_key!="edit"}}checkok(){{else}}confirm('確定修改 '+document.myform.student_sn.options[document.myform.student_sn.selectedIndex].text+' 復學記錄?'){{/if}}" >    </td>
			</tr>
		</table><br></td>
	</tr>
	<TR>
		<TD>
			<table border="1" cellspacing="0" cellpadding="2" bordercolorlight="#333354" bordercolordark="#FFFFFF"  width="100%" class=main_body ><tr><td colspan=9 class=title_top1 align=center >本學期復學學生</td></tr>
				<TR class=title_mbody >				
					<TD>異動類別</TD>
					<TD>生效日期</TD>
					<TD>學號</TD>				
					<TD>姓名</TD>				
					<TD>班級</TD>	
                                        <TD>原就讀學校</TD>
                                        <TD>原就讀縣市</TD>
					<TD>核准單位</TD>
					<TD>字號</TD>
					<TD>編修</TD>
                    <TD><img src='./images/bridge.jpg'></TD>
				</TR>                                
                                    {{assign var=len value=0}}
				{{section loop=$stud_move name=arr_key}}
                                {{assign var=len value=$len+1}}
					<TR class=nom_2>
						{{assign var=kid value=$stud_move[arr_key].move_kind}}
						{{assign var=cid value=$stud_move[arr_key].seme_class}}
						<TD>{{$kind_arr.$kid}}</TD>
						<TD>{{$stud_move[arr_key].move_date}}</TD>
						<TD>{{$stud_move[arr_key].stud_id}}</TD>	
                                                {{assign var=stud_id value=$stud_move[arr_key].stud_id}}
						<TD>{{$stud_move[arr_key].stud_name}}</TD>
                                                {{assign var=stud_name value=$stud_move[arr_key].stud_name}}
                                                {{assign var=studpersonid value=$stud_move[arr_key].stud_person_id}}
                                                {{assign var=studbirthday value=$stud_move[arr_key].stud_birthday}}
						<TD>{{$class_list.$cid}}</TD>		
                                                {{assign var=studclass value=$class_list.$cid}}
                                                <TD>({{$stud_move[arr_key].school_id}})&nbsp;{{$stud_move[arr_key].school}}</TD>
                                                {{assign var=oldeduid value=$stud_move[arr_key].school_id}}
                                                {{assign var=oldeduname value=$stud_move[arr_key].school}}
                                                <TD>{{$stud_move[arr_key].city}}</TD>	
                                                {{assign var=oldcounty value=$stud_move[arr_key].city}}
						<TD>{{if $stud_move[arr_key].move_c_unit}}{{$stud_move[arr_key].move_c_unit}}{{else}}<font color="red">尚未輸入</font>{{/if}}</TD>
						<TD>{{$stud_move[arr_key].move_c_date}} {{$stud_move[arr_key].move_c_word}}字第{{$stud_move[arr_key].move_c_num}}號</TD>
						<TD><a href="{{$smarty.server.SCRIPT_NAME}}?do_key=edit&move_id={{$stud_move[arr_key].move_id}}">編輯</a>&nbsp;&nbsp;
						<a href="{{$smarty.server.SCRIPT_NAME}}?do_key=delete&&move_id={{$stud_move[arr_key].move_id}}&student_sn={{$stud_move[arr_key].student_sn}}" onClick="return confirm('確定取消 {{$stud_move[arr_key].stud_id}}--{{$stud_move[arr_key].stud_name}} {{$kind_arr.$kid}}記錄 ?');">刪除記錄</a>
						<a href='../toxml/stud_data_patch.php' target='_BLANK'>資料補登</a></TD>
						
                        <td align='center'>
					    <span id='{{$stud_move[arr_key].stud_person_id}}'>
					        <img src='images/filefind.png' class='chk_resource' id='id-{{$stud_move[arr_key].stud_person_id}}-{{$sch_id}}-{{$stud_move[arr_key].school_id}}' title='檢查是否可橋接下載' style='cursor: pointer'>
					    </span>
                        </td>
					</TR>
                                        {{assign var=tmp_jqfunImport value="
                                            $tmp_jqfunImport$('button#btnXCAImport$len').click({
                                            password: $('#pin').val(),
                                            sessionid: '$session_id',
                                            cookieschid: '$cookie_sch_id',
                                            useragent: $useragent,
                                            studid: '$stud_id',
                                            studname: '$stud_name',
                                            targetpage: $target_page,
                                            studpersonid: '$studpersonid',
                                            studbirthday: '$studbirthday',
                                            studclass: '$studclass',
                                            oldeduid: '$oldeduid',
                                            oldcounty: '$oldcounty',
                                            oldeduname: '$oldeduname',
                                            neweduid: '$sch_id',
                                            neweduname: '$uploadname'
                                            },function(event){
                                            event.preventDefault();
                                            if (!$('#pin').val()) {
                                                 $.unblockUI();
                                                alert('請輸入PIN碼');
                                                $('#pin').focus();
                                            } else{
                                                event.data.password=$('#pin').val();
                                                console.log(JSON.stringify(event.data));

                                                $.ajax({
                                                    url: 'https://localhost:8443/xcaexchange/import',
                                                    dataType: 'json',
                                                    contentType: 'application/json',
                                                    method: 'POST',
                                                    data: JSON.stringify(event.data),
                                                    success: function (data, textStatus, jqXHR) {
                                                        console.log(JSON.stringify(data));
                                                        obj = JSON.parse(JSON.stringify(data));
                                                        alert(obj.status);
                                                    },
                                                    error: function (jqXHR, textStatus, errorThrown) {
                                                        console.log(textStatus);
                                                        alert('請確定微型伺服器己啟動');
                                                    }
                                                });
                                            }
                                        });
                                            "
                                        }}
				{{/section}}
			</table></TD>
	</TR>
	<TR>
		<TD></TD>
	</TR>
	</form>
</table>
<div id="domMessage" style="display:none;"> 
    <img src="{{$loadingimg}}" alt="PORCESSING" id="loader"/>&nbsp;&nbsp;憑證讀取中...請稍候...
</div> 
                        
{{literal}}
<script type="text/javascript">
    $(document).ready((function () {
        $(document).ajaxComplete($.unblockUI);

        {{/literal}}{{$tmp_jqfunImport}}{{literal}}
        
        var checkcardurl = "https://localhost:8443/checkcard/exists";
        $("#btnCheckCard").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            console.log(checkcardurl);

            $.get(checkcardurl,
                    function (data) {
                        console.log(JSON.stringify(data));
                        obj = JSON.parse(JSON.stringify(data));
                        alert(obj.status);
                    }
            ).error(
                    function (err) {
                        alert('請確定微型伺服器己啟動');
                    });
        });

        var checkipurl = "https://localhost:8443/checkcard/getip";
        $("#btnCheckIP").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            console.log(checkipurl);

            $.get(checkipurl,
                    function (data) {
                        console.log(JSON.stringify(data));
                        obj = JSON.parse(JSON.stringify(data));
                        alert(obj.status);
                    }
            ).error(
                    function (err) {
                        alert('請確定微型伺服器己啟動');
                    });
        });

        var xcaregurl = "https://localhost:8443/checkcard/regxca";
        $("#btnRegXCA").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            console.log(xcaregurl);
            $.ajax({
                url: xcaregurl,
                dataType: "json",
                contentType: 'application/json',
                method: "POST",
                data: JSON.stringify({"schoolid": '{{/literal}}{{$sch_id}}{{literal}}'}),
                success: function (data, textStatus, jqXHR) {
                    console.log(JSON.stringify(data));
                    obj = JSON.parse(JSON.stringify(data));
                    alert(obj.status);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                    alert('請確定微型伺服器己啟動');
                }
            });
        });
        var studmoveuploadsrurl = "https://localhost:8443/sr/upload/studmove";
        $("#btnStudMoveUploadSR").click(function (event) {
            $.blockUI({message: $('#domMessage')});
            event.preventDefault();
            console.log(studmoveuploadsrurl);
            if (!$("#pin").val()) {
                $.unblockUI();
                alert('請輸入PIN碼');
                $("#pin").focus();
            } else {
                console.log($("#pin").val());
                $.ajax({
                    url: studmoveuploadsrurl,
                    dataType: "json",
                    contentType: 'application/json',
                    method: "POST",
                    data: JSON.stringify({
                        "password": $("#pin").val(),
                        "cookieschid": '{{/literal}}{{$cookie_sch_id}}{{literal}}',
                        "eduid": '{{/literal}}{{$sch_id}}{{literal}}',
                        "currseme": '{{/literal}}{{$curr_seme}}{{literal}}',
                        "sessionid": '{{/literal}}{{$session_id}}{{literal}}',
                        "useragent": {{/literal}}{{$useragent}}{{literal}},
                        "targetpage": {{/literal}}{{$target_page}}{{literal}},
                        "submitip": '{{/literal}}{{$real_ip}}{{literal}}',
                        "uploadid": '{{/literal}}{{$uploadid}}{{literal}}',
                        "uploadname": {{/literal}}{{$uploadname}}{{literal}}
                    }),
                    success: function (data, textStatus, jqXHR) {
                        console.log(JSON.stringify(data));
                        obj = JSON.parse(JSON.stringify(data));
                        alert(obj.status);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(textStatus);
                        alert('請確定微型伺服器己啟動');
                    }
                });
            }
        });

        //檢查有沒有轉出校學生, 是否已可下載
        $(".chk_resource").click(function(){
            var the_id=$(this).attr("id");
            ID=the_id.split("-");

            var stud_person_id=ID[1];
            var request_edu_id=ID[2];
            var resource_edu_id=ID[3];
            //alert(resource_edu_id);


            //ajax 檢查轉入端有沒有學生
            $.ajax({
                type: 'post',
                url: 'stud_move_request.php',
                data: { stud_person_id:stud_person_id,request_edu_id:request_edu_id,resource_edu_id:resource_edu_id },
                dataType: 'text',
                error: function(xhr) {
                    alert('ajax request error!!');
                },
                success: function(response) {
                    var res_data = JSON.parse(response);  //把傳入的資料轉為 json 格式再分析
                    if (res_data.result!=1) {
                        $("#"+stud_person_id).html("<img src='./images/forbidden.png' title='目前無法下載'>");
                        alert (res_data.message);
                    } else {
                        $("#"+stud_person_id).html("<img src=\"./images/download.png\" title=\"按我橋接下載\" style=\"cursor: pointer\" onclick=\"click_download('"+resource_edu_id+"','"+stud_person_id+"')\">");
                    }

                }
            });

        });


    }));

    function click_download(resource_edu_id,stud_person_id) {
        //alert (resource_edu_id);
        $("#submit_resource_edu_id").attr("value",resource_edu_id);
        $("#submit_stud_person_id").attr("value",stud_person_id);
        document.bridge_download.submit();
    }
 </script>
{{/literal}}
{{include file="$SFS_TEMPLATE/footer.tpl"}}

<form method="post" action="stud_move_bridge.php" name="bridge_download" id="bridge_download">
    <input type="hidden" name="request_username" value="{{$session_tea_name}}">
    <input type="hidden" name="request_edu_name" value="{{$sch_cname}}">
    <input type="hidden" name="request_edu_id" value="{{$sch_id}}" id="submit_request_edu_id">
    <input type="hidden" name="resource_edu_id" value="" id="submit_resource_edu_id">
    <input type="hidden" name="stud_person_id" value="" id="submit_stud_person_id">
</form>