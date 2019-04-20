<?php

// $Id: index.php 7731 2013-10-29 05:45:26Z smallduh $

/* 取得設定檔 */
include_once "config.php";

sfs_check();

$sel_year=curr_year();
$sel_seme=curr_seme();
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$SAVE_INFO=array();
$SAVE_INFO[0]=$SAVE_INFO[1]=$SAVE_INFO[2]=0;
$INFO="";
if ($_POST['act']=='儲存登記') {
	//echo "<pre>";
	//print_r($_POST);
	//echo "</pre>";
	//exit(); 
	
  foreach ($_POST['selected_students'] as $student_sn) {
  	//取得班級，格式化成 class_id , 取得　stud_id , 再利用 add_one 函式存入
  	$query="select a.seme_class,a.seme_num,b.stud_id,b.stud_name from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.student_sn=b.student_sn and a.student_sn='$student_sn'";
  	$res=$CONN->Execute($query) or die ("SQL 發生錯誤! ".$query);
  	$row=$res->FetchRow();
    //print_r($row);	
  	//$class_id格式102_1_07_15 (102年第1學期7年15班)
  	$class_id=sprintf('%03d_%d_%02d_%02d',$sel_year,$sel_seme,substr($row['seme_class'],0,1),substr($row['seme_class'],1,2));
  	add_one($sel_year,$sel_seme,$class_id,$row['stud_id'],$_POST[s]);	
	}	
	$INFO="操作資訊：".date("Y-m-d H:i:s")."傳入 ".$SAVE_INFO[0]." 筆資料, 成功儲存 ".$SAVE_INFO[1]." 筆, 另有 ".$SAVE_INFO[2]." 筆重覆.　若需修改，請利用「缺曠課登記」功能。";  
}


if(!empty($_REQUEST[this_date])){
        $d=explode("-",$_REQUEST[this_date]);
}else{
        $d=explode("-",date("Y-m-d"));
}
$year=(empty($_REQUEST[year]))?$d[0]:$_REQUEST[year];
$month=(empty($_REQUEST[month]))?$d[1]:$_REQUEST[month];
$day=(empty($_REQUEST[day]))?$d[2]:$_REQUEST[day];

$cal = new MyCalendar;
$cal->setStartDay(1);
$cal->getDateLink();
$mc=$cal->getMonthView($month,$year,$day);
  $the_cal="
   <table cellspacing='1' cellpadding='2' bgcolor='#E2ECFC' class='small'>
       <tr bgcolor='#FEFBDA'>
         <td align='center'>
           <a href='$_SERVER[SCRIPT_NAME]?act=$_REQUEST[act]&this_day=$today&class_id=$class_id' class='box'><img src='".$SFS_PATH_HTML."images/today.png' alt='回到今天' width='16' height='16' hspace='2' border='0' align='absmiddle'>回到今天</a>
         </td></tr>
         <tr bgcolor='#FFFFFF'><td>$mc</td></tr>
    </table>";

$act=$_REQUEST[act];


//秀出網頁
head("團體登記");
$tool_bar=&make_menu($school_menu_p);

        for ($j=1;$j<=6;$j++)
        $js.="
		function disableall_cb_".$j."() {
			var uf=document.myform.include_uf.checked;
			var df=document.myform.include_df.checked;
			var max_i=document.myform.cb_".$j.".length;
			if (uf & df) {
			  for (i=0;i<max_i;i++) {
			    document.myform.cb_".$j."[i].checked=false;
			    document.myform.cb_".$j."[i].disabled=true;
			  }
			} else {
				if (uf) document.myform.cb_".$j."[0].checked=!document.myform.cb_".$j."[0].checked;
				if (df) document.myform.cb_".$j."[(max_i-1)].checked=!document.myform.cb_".$j."[(max_i-1)].checked;
			  for (i=1;i<(max_i-1);i++) {
			    document.myform.cb_".$j."[i].checked=!document.myform.cb_".$j."[i].checked;
			  }
			  document.myform.cb_".$j."_all.checked=false;
			}
		}
                function ableall_cb_".$j."() {
                  for (i=0;i<document.myform.cb_".$j.".length;i++) {
                    document.myform.cb_".$j."[i].disabled=false;
                  }
                }
        ";

echo "<style type=\"text/css\">
<!--
.calendarTr {font-size:12px; font-weight: bolder; color: #006600}
.calendarHeader {font-size:12px; font-weight: bolder; color: #cc0000}
.calendarToday {font-size:12px; background-color: #ffcc66}
.calendarTheday {font-size:12px; background-color: #ccffcc}
.calendar {font-size:11px;font-family: Arial, Helvetica, sans-serif;}
.dateStyle {font-size:15px;font-family: Arial; color: #cc0066; font-weight: bolder}
-->
</style>
<script language=\"JavaScript\">
        $js
</script>
";

$main=&signForm($sel_year,$sel_seme);
echo $tool_bar;
$main="<form action='$_SERVER[SCRIPT_NAME]' method='post' name='myform'>
<table border='1' style='border-collapse:collapse' cellpadding='5' bordercolor='#D1D1D1' bgcolor='#F0F0F0'>
 <tr><td>
<table border='0'>
<tr><td valign='top'>".$main."</td><td valign='top'>".$the_cal."</td></tr></table>";
echo $main;

//取得目前所有班級
$class_array=class_base();

?>
<table border="0" width="100%">
 <tr>
   <td>◎預選學生名單(請進行最後勾選再按下「儲存登記」)</td>
 </tr>
 <tr>
   <td>
   	<table border="2" style="border-collapse:collapse" bordercolor="#111111" bgcolor="#FFDDDD" width="100%">
   		<tr>
   			<td><span id="show_selected_students">目前無預選名單</span></td>
   		</tr>
   	</table>   
   </td>
 </tr>
 <tr>
  <td style="color:#FF0000;font-size:10pt"><?php echo $INFO;?></td>
 </tr>
</table>
</td></tr>
</table>

</form>
<form method="post" name="myform2" action="<?php echo $_SERVER['php_self'];?>">
<table border="0">
 <tr>
  <td>◎選擇班級
  	<select name="the_class" size="1" id="the_class">
  	 <option value="">請選擇班級</option>
					<?php
					 foreach ($class_array as $k=>$v) {
					 ?>
					 <option value="<?php echo $k;?>" ><?php echo $v;?></option>
					 <?php
					 }
					?>  	 
  	</select> <input type="button" id="chk_all" value="全選"><input type="button" id="chk_all_clear" value="全不選">
  	</td>	
 </tr>
 <tr>
 	<td>
 	
 		<span id="the_students"></span>
 	</td>
 </tr>
 <tr>
   <td><input type="button" value="預選這些學生" id="btn_select_students"></td>
 </tr>
</table>
</form>
<Script>
 $("#the_class").change(function(){
    $.ajax({
   	type: "post",
    url: 'ajax_return_students.php',
    data: { the_class: $('#the_class').val() , pre_selected:$('#pre_selected').val() },
    error: function(xhr) {
      alert('ajax request 發生錯誤, 無法取得學生名單!');
    },
    success: function(response) {
    	$('#the_students').html(response);
      $('#the_students').fadeIn();      
    }
   });   // end $.ajax
 }); // end #the_class

 $("#btn_select_students").click(function(){
 	//處理勾選的名單, 做成陣列
 	var selectedItems = new Array();
 		$("input[name*='chk_student[]']:checked").each(function() {
 					selectedItems.push($(this).val());
 		});

 if (selectedItems .length == 0)
     alert("請勾選學生");
 else
 	
 	　//傳送被勾選的名單(轉成以;隔開的字申)及已預選(pre_selected)的 hidden 值
    $.ajax({
   	type: "post",
    url: 'ajax_select_students.php',
    data: { items:selectedItems.join(';') , pre_selected:$('#pre_selected').val() },
    dataType: "text",
    error: function(xhr) {
      alert('ajax request 發生錯誤, 無法取得學生名單!');
    },
    success: function(response) {
    	$('#show_selected_students').html(response);
      $('#show_selected_students').fadeIn(); 
      //最後傳回名單的 table  及 <input type="hidden" name="pre_selected">
    }
   });   // end $.ajax
 }); // end #the_class

//全選
$("#chk_all").click(function(){
  $(".chk_student").attr("checked","true");
});

//全不選
$("#chk_all_clear").click(function(){
  $(".chk_student").attr("checked","");
});


</Script>
<?php

foot();


//列出填寫表格
function &signForm($sel_year,$sel_seme){
	
	        global $year,$month,$day,$CONN,$weekN,$IS_JHORES,$dayn;

        
		
	$sql="select Max(sections) as maxsections from score_setup where year='$sel_year' and semester='$sel_seme'";
	$rs=$CONN->Execute($sql) or trigger_error("SQL語法錯誤：$sql", E_USER_ERROR);
	if ($rs) {
		while (!$rs->EOF) {
			$all_sections=$rs->fields['maxsections'];
			$rs->MoveNext();
		}
	}
		
		//以7節課為標準
        //$all_sections=7;		
           for($i=1;$i<=$all_sections;$i++){
                  $sections_txt.="<td>$i 節</td>";
           }

        //取得缺曠課類別
        $absent_kind_array= SFS_TEXT("缺曠課類別");
        $option="
        <option value=''></option>";
        foreach($absent_kind_array as $k){
                $option.="<option value='$k'>$k</option>\n";
        }


                $scond=study_cond();
                $tool="缺曠課種類";
                $seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
                
								$fday=mktime(0,0,0,$month,$day,$year);
                $dd=getdate($fday);
                $fday-=($dd[wday]-1)*86400;
                for ($j=0;$j<=5;$j++) {
                        //取得該學生資料
                        $smkt=$fday+$j*86400;
                        $syear=date("Y",$smkt);
                        $smonth=date("m",$smkt);
                        $sday=date("d",$smkt);
                        $dd=getdate($smkt);
                        $did=date("Y-m-d",$smkt);
                        $pid=date("Y-m-d",$fday-7*86400);
                        $fid=date("Y-m-d",$fday+7*86400);
                        $e_name="cb_".$dd[wday];
                        
                        //曠課種類
                        $select="<select name='s[$did][kind]' id='tool'>$option</select>";
                        $checked="checked";

                        $sections_data="";
                        $close_allday=false;
                        
												//每一節課的勾選欄位
                         for($i=1;$i<=$all_sections;$i++){
                                $sv="<input type='checkbox' id='$e_name' name='s[$did][section][]' value='$i'>";
                                $sections_data.="<td>$sv</td>\n";
                         }


                        //升旗
                        $ufv="<input type='checkbox' id='$e_name' name='s[$did][section][]' value='uf'>";
                        $uf="<td bgcolor='#FBF8B9'>$ufv</td>";

                        //降旗
                        $dfv="<input type='checkbox' id='$e_name' name='s[$did][section][]' value='df'>";
                        $df="<td bgcolor='#FFE6D9'>$dfv</td>";

                        //整天
                        //看是否要關閉「整天」功能
                        $disabled="";
                        
                        $allday="<input type='checkbox' id='".$e_name."_all' $disabled name='s[$did][section][]' value='allday' onClick=\"if (this.checked==false){javascript:ableall_$e_name() } else { javascript:disableall_$e_name()}\">";

                        $all_day="<td bgcolor='#E8F9C8'>$allday</td>";
                       
                        $data.="<tr bgcolor='#FFFFFF'>";
                        $data.="
                        <td align='center'>".$did."<br>(".$weekN[$dd[wday]-1].")</td>
                        $uf
                        $sections_data
                        $df
                        $all_day
                        <td bgcolor='#ECff8F9' vlign='middle'>$select</td>
                        </tr>";
                }
                $site_title=$pre_str."座號".$next_str;
                $date_title="<td align='center'><a href='$_SERVER[SCRIPT_NAME]?this_date=$pid'>▲</a><br>日期<br><a href='$_SERVER[SCRIPT_NAME]?this_date=$fid'>▼</a></td>";
        

        $main="        
        <font color=#0000FF>◎請先填寫缺曠課資料，再進行名單選擇：</font>
        <table cellspacing='0' cellpadding='0'0class='small'>
        <tr><td valign='top'>
                <table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2' class='small'>
                <tr bgcolor='#E6F2FF'>

                $date_title
                <td bgcolor='#FBF8B9'>升旗</td>
                $sections_txt
                <td bgcolor='#FFE6D9'>降旗</td>
                <td bgcolor='#E8F9C8'>整天</td>
                <td bgcolor='#ECff8F9'>缺曠課種類</td>
                </tr>
                <form action='$_SERVER[SCRIPT_NAME]' method='post' name='myform'>
                $data
                </table>
        </td><td valign='top'>
                <input type='hidden' name='sel_year' value='$sel_year'>
                <input type='hidden' name='sel_seme' value='$sel_seme'>
                <input type='hidden' name='class_id' value='$class_id'>
                <input type='hidden' name='this_date' value='$year-$month-$day'>
                <input type='hidden' name='date' value='$year-$month-$day'>
		<div class='small'><input type='checkbox' name='include_uf' checked>整天含升旗</div>
		<div class='small'><input type='checkbox' name='include_df' checked>整天含降旗</div>
                <input type='submit' name='act' value='儲存登記'>";
        $main.="
                
        </td></tr>
        </table>
        ";
        return $main;
}

//新增資料
function add_all($sel_year,$sel_seme,$class_id="",$date="",$data=array()){
	global $SAVE_INFO;
/*
s[091005][uf]
s[091005][section]
s[091005][df]
s[091005][allday]
s[091005][kind]
s[091005][date]
*/
        foreach($data as $id =>$v){
                foreach($v[section] as $section){
                        if(empty($v['kind']))continue;
                        add($sel_year,$sel_seme,$id,$class_id,$date,$section,$v['kind']);
                }
        }
        return;
}
//新增一人資料
function add_one($sel_year,$sel_seme,$class_id="",$stud_id="",$data=array()){
	global $SAVE_INFO;
        foreach($data as $id =>$v){
                foreach($v[section] as $section){
                        if(empty($v['kind']))continue;
                        $SAVE_INFO[0]++;
                        add($sel_year,$sel_seme,$stud_id,$class_id,$id,$section,$v['kind']);
                }
        }
        return;
}

//新增單一筆資料
function add($sel_year,$sel_seme,$stud_id,$class_id="",$date,$section,$kind){
        global $CONN,$SAVE_INFO;
        $d=explode("-",$date);
        $c=explode("_",$class_id);

        //$new_class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$c[2]-($c[0]-$sel_year)+$IS_JHORES,$c[3]);
        
        $chk_sql="select * from stud_absent where year='$sel_year' and semester='$sel_seme' and stud_id='$stud_id' and date='$date' and section='$section'";
        $res_chk=$CONN->Execute($chk_sql);
        
        if ($res_chk->RecordCount()==0) {
         $sql_insert = "insert into stud_absent (year,semester,class_id,stud_id,date,absent_kind,section,sign_man_sn,sign_man_name,sign_time,month) values ('$sel_year','$sel_seme','$class_id','$stud_id','$date','$kind','$section','$_SESSION[session_tea_sn]','$_SESSION[session_tea_name]',now(),'$d[1]')";
         $CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
         sum_abs($sel_year,$sel_seme,$stud_id);
          $SAVE_INFO[1]++;
        } else {
          $SAVE_INFO[2]++;
        }
        return;
}

?>
