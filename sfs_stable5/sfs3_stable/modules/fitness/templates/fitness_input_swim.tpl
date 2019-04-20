{{* $Id: fitness_input.tpl 7816 2013-12-17 14:10:29Z infodaes $ *}}
{{include file="$SFS_TEMPLATE/header.tpl"}}
{{include file="$SFS_TEMPLATE/menu.tpl"}}
    <style type="text/css">
        .Box1:focus
        {
            border: thin solid #FFD633;
            -webkit-box-shadow: 0px 0px 3px #F7BB2E;
            -moz-box-shadow: 0px 0px 3px #F7BB2E;
            box-shadow: 0px 0px 3px #F7BB2E;
        }
        .Box1
        {
            height: 20px;
            width: 50px;
            text-align: justify;
            letter-spacing: 1px; /*CSS letter-spacing Property*/
            padding: 1px;
            font-size: medium;
            font-weight: bold;
            font-style: normal;
        }
        .Box2:focus
        {
            border: thin solid #FFD633;
            -webkit-box-shadow: 0px 0px 3px #F7BB2E;
            -moz-box-shadow: 0px 0px 3px #F7BB2E;
            box-shadow: 0px 0px 3px #F7BB2E;
        }
        .Box2
        {
            height: 20px;
            width: 50px;
            text-align: justify;
            letter-spacing: 1px; /*CSS letter-spacing Property*/
            padding: 1px;
            font-size: medium;
            font-weight: bold;
            font-style: normal;
        }

    </style>
<script language="JavaScript">
function openwindow(t){
	window.open ("quick_input.php?t="+t+"&class_num={{$class_num}}&c_curr_seme={{$smarty.post.year_seme}}","成績處理","toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=no,copyhistory=no,width=600,height=420");
}
</script>

<table bgcolor="#DFDFDF" cellspacing="1" cellpadding="4" width="620">
<form name="myform" action="{{$smarty.server.PHP_SELF}}" method="post">
<input type="hidden" name="act" value="">
<tr>
<td bgcolor="#FFFFFF" width="620">
<table border="0" bgcolor="#FFFFFF" width="100%">
  <tr>
<td bgcolor="#FFFFFF" valign="top"><p>{{$seme_menu}} {{$class_menu}} </p></td>
<td bgcolor="#FFFFFF" valign="top" align="right">統一設定測驗日期：<input type="text" name="check_test_date" value="" size="10"><input type="button" value="設定" onclick="tag_test_date()"><input  style="color:#FF0000" type="button" value="資料儲存" onclick="document.myform.act.value='save';document.myform.submit()"></td>
  </tr>
  {{if $INFO || $admin}}
  <tr>
  <td colspan="2">
  {{if $admin}}
  <input type="button" value="匯出本學期資料" onclick="document.myform.act.value='export';document.myform.submit()">
  {{/if}}
  <font size="2" color="red">{{$INFO}}</font>
  </td>
  </tr>
  {{/if}}
</table>
</td>
</tr>
<tr>
<td>
<table bgcolor="#9ebcdd" cellspacing="1" cellpadding="4" width="100%">
<tr bgcolor="#c4d9ff">
<td align="center">座號</td>
<td align="center">姓名</td>
<td align="center">學號</td>
<td align="center">生日</td>
<td align="center">測驗日期</td>
<td align="center">
	實施游泳教學
 <input type="checkbox" name="check_teach_swim" value="1" onclick="tag_teach_swim()">
</td>
<td align="center">級別</td>
<td align="center">成績</td>
</tr>
{{foreach from=$rowdata item=d key=i}}
{{assign var=sn value=$d.student_sn}}
<tr bgcolor="white">
<td class="small">{{$d.seme_num}}</td>
<td class="small"><font color="{{if $d.stud_sex==1}}blue{{elseif $d.stud_sex==2}}red{{else}}black{{/if}}">{{$d.stud_name}}</font></td>
<td style="text-align:right;">{{$d.stud_id}}</td>
<td style="text-align:right;">{{$d.stud_birthday}}</td>
<td style="text-align:right;"><input type="text" size="10" name="test_date[{{$d.student_sn}}]" value="{{$fd.$sn.test_date}}"></td>
<td style="text-align:center;">
 <input type="checkbox" name="teach_swim[{{$d.student_sn}}]" value="1" {{if $fd.$sn.teach_swim==1}} checked{{/if}}>有實施
</td>
<td style="text-align:right;"><input class="Box1" type="text" name="swim_class[{{$d.student_sn}}]" value="{{$fd.$sn.swim_class}}"></td>
<td style="text-align:right;"><input class="Box2" type="text" name="swim_score[{{$d.student_sn}}]" value="{{$fd.$sn.swim_score}}"></td>
</tr>
{{/foreach}}
</table>
<input style="color:#FF0000" type="button" value="資料儲存" onclick="document.myform.act.value='save';document.myform.submit()">
</td></tr>
<tr>
<td bgcolor="#FFFFF" style="font-size:10pt">
說明：<br>
1.自102學年度起，教育部要求各級學校將學生「游泳與自救能力」相關資料上傳至教育部體適能網站 <a href="http://www.fitness.org.tw/" target="_blank">http://www.fitness.org.tw/</a>，故開發此功能，方便任課教師協助輸入資料並整合資料後匯出。<br>
2.<font color=blue>「級別」欄位</font>：請依教育部體育署公布之「全國中、小學學生游泳與自救能力基本指標（五級）」評斷，學校如未實施游泳教學，但學生已透過民間團體或游泳比賽取得游泳能力證明者或由授課教師認定具有各級游泳能力者仍需進行上傳作業。請依序輸入1-5（代表第一至五級），未達第一級游泳能力者請輸入0，因故無法進行檢測者請留空。<br>													
3.<font color=blue>「成績」欄位</font>：第三至五級請輸入測驗成績，5分28秒輸入「5.28」；28秒01，請輸入「0.28」即可，秒數以後成績無條件捨去。															

</td>
</tr>

</table>


</form>


{{include file="$SFS_TEMPLATE/footer.tpl"}}

    <script type="text/javascript">
    //等級欄位控制
        $(".Box1").live("keydown", function (e) {
					if (e.keyCode == 13 || e.keyCode==40) {
						var allInputs = $(".Box1");
						for (var i = 0; i < allInputs.length; i++) {
							if (allInputs[i] == this) {
								//while ((allInputs[i]).name == (allInputs[i + 1]).name) {
								//	i++;
								//}

								if ((i + 1) < allInputs.length ){
											$(allInputs[i + 1]).focus();
										 if($(allInputs[i + 1]).val()!="") //判斷力面是否有值
                			{
                				$(allInputs[i + 1]).select(); //選取效果
                			}

								}
							}
						} // end for
					} // end if e.keycode==13
				
					if (e.keyCode == 38) {
						var allInputs = $(".Box1");
						for (var i = 0; i < allInputs.length; i++) {
							if (allInputs[i] == this) {
								//while ((allInputs[i]).name == (allInputs[i + 1]).name) {
								//	i++;
								//}

								if (i>0 ){
											$(allInputs[i - 1]).focus();
										 if($(allInputs[i - 1]).val()!="") //判斷力面是否有值
                			{
                				$(allInputs[i - 1]).select(); //選取效果
                			}											
								}
							}
						} // end for
					} // end if e.keycode==13
					
				});

				//成績欄位控制
        $(".Box2").live("keydown", function (e) {
					if (e.keyCode == 13 || e.keyCode==40) {
						var allInputs = $(".Box2");
						for (var i = 0; i < allInputs.length; i++) {
							if (allInputs[i] == this) {
								//while ((allInputs[i]).name == (allInputs[i + 1]).name) {
								//	i++;
								//}

								if ((i + 1) < allInputs.length ){
											$(allInputs[i + 1]).focus();
										 if($(allInputs[i + 1]).val()!="") //判斷力面是否有值
                			{
                				$(allInputs[i + 1]).select(); //選取效果
                			}
								}
							}
						} // end for
					} // end if e.keycode==13
				
					if (e.keyCode == 38) {
						var allInputs = $(".Box2");
						for (var i = 0; i < allInputs.length; i++) {
							if (allInputs[i] == this) {
								//while ((allInputs[i]).name == (allInputs[i + 1]).name) {
								//	i++;
								//}

								if ((i - 1) >0 ){
											$(allInputs[i - 1]).focus();
										 if($(allInputs[i - 1]).val()!="") //判斷力面是否有值
                			{
                				$(allInputs[i - 1]).select(); //選取效果
                			}
								}
							}
						} // end for
					} // end if e.keycode==13
					
				});

        
    </script>

<Script language="JavaScript">


   function tag_teach_swim() {
  		var i =0;
  		var status=document.myform.check_teach_swim.checked;
  		while (i < document.myform.elements.length)  {
    		if (document.myform.elements[i].name.substr(0,10)=='teach_swim') {
      		document.myform.elements[i].checked=status;
    		}
    		i++;
  		}
		}
	  function tag_test_date() {
  		var i =0;
  		var status=document.myform.check_test_date.value;
  		while (i < document.myform.elements.length)  {
    		if (document.myform.elements[i].name.substr(0,9)=='test_date') {
      		document.myform.elements[i].value=status;
    		}
    		i++;
  		}
		}

</Script>