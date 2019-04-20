<?php
// $Id: newstud_notification.php 8869 2016-04-11 07:09:52Z infodaes $

/*引入學務系統設定檔*/
require "config.php";
$class_year_b=$_REQUEST['class_year_b'];
$act=$_REQUEST['act'];
$org=trim($_REQUEST['org']);
$num=trim($_REQUEST['num']);
$c_place=trim($_REQUEST['c_place']);
$c_date=trim($_REQUEST['c_date']);
$c_time=trim($_REQUEST['c_time']);
$p_date=trim($_REQUEST['p_date']);
$p_time=trim($_REQUEST['p_time']);
$note=trim($_REQUEST['note']);
$note2=trim($_REQUEST['note2']);
$ref_year=$_REQUEST['ref_year'];
if (empty($class_year_b)) $class_year_b=$IS_JHORES+1;

//使用者認證
sfs_check();
$year = date("Y")-1911;
if($act=="send"){
	if ($_POST[save]) {
		//儲存設定
		$sql="replace into new_stud_notification (year,org,num,c_place,c_date,c_time,p_date,p_time,note,note2,class_year) values('$year','$org','$num','$c_place','$c_date','$c_time','$p_date','$p_time','$note','$note2','$class_year_b') ";
		$CONN->Execute($sql) or trigger_error($sql,256);
	}
	$sel_str="";
	if ($_POST[sel]=="0") {
		$hv="1";
	} elseif ($_POST[sel]=="1") {
		$start_num=$_POST[start_num];
		$end_num=$_POST[end_num];
		$sel_str="and temp_id >='A".$start_num."' and temp_id <= 'A".$end_num."'";
		$hv="1";
	} elseif ($_POST[sel]=="2" && count($_POST[sch])>0) {
		/*
		while (list($k,$v)=each($_POST[sch])) {
			$sel_str.="'$v',";
		}
		*/
		foreach($_POST[sch] as $v) $sel_str.="'$v',";
		
		if ($sel_str) $sel_str=substr($sel_str,0,-1);
		$sel_str="and old_school in ($sel_str)";
		$hv="1";
	}
	if ($_POST[ooo] && $hv) {
		//產生sxw
		$new_stud_table="new_stud";
		$new_stud_notification_table="new_stud_notification";
		make_ooo($new_stud_table,$new_stud_notification_table);
	}
	if ($_POST[html] && $hv) {
		//產生html
		$new_stud_table="new_stud";
		$new_stud_notification_table="new_stud_notification";
		make_html($new_stud_table,$new_stud_notification_table);
	}
}

//程式檔頭
head("新生編班");
print_menu($menu_p,"class_year_b=$class_year_b");


//抓取歷史年度資料
$sql="select year from new_stud_notification order by year desc";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);

$year_combo="<select name='ref_year' onchange='this.form.submit()'>";

if(! $ref_year) $ref_year=$year;
{
while ($data=$rs->FetchRow()) {
	if ($ref_year==$data['year'])
                $year_combo.="<option value='".$data['year']."' selected>".$data['year']."</option>";
        else
                $year_combo.="<option value='".$data['year']."' >".$data['year']."</option>";
	}
	}
$year_combo.="</select>";

$sql="select * from new_stud_notification where year=$ref_year and class_year='$class_year_b'";
$rs=$CONN->Execute($sql) or trigger_error($sql,256);
$org=$rs->fields['org'];
$num=$rs->fields['num'];
$c_place=$rs->fields['c_place'];
$c_date=$rs->fields['c_date'];
$c_time=$rs->fields['c_time'];
$p_date=$rs->fields['p_date'];
$p_time=$rs->fields['p_time'];
$note=$rs->fields['note'];
$note2=$rs->fields['note2'];

//設定主網頁顯示區的背景顏色
$main="<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";

//網頁內容請置於此處
$main.="<form action='{$_SERVER['PHP_SELF']}' method='POST'>";

$grade_selected="<select name='class_year_b' OnChange='this.form.submit()'>";
while (list($k,$v)=each($class_year)) {
	$checked=($class_year_b==$k)?"selected":"";
	$grade_selected.="<option value='$k' $checked>$v</option>\n";
}
$grade_selected.="</select>";
$sel[intval($_POST[sel])]="checked";
$main.="<table cellspacing=5 cellpadding=0><tr><td valign='top'>
	<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
	<tr bgcolor='#E1ECFF'><td> $grade_selected  入學通知單基本資料設定　　　( 沿用 $year_combo 年度設定 )<td>下載選擇
	</tr>
	<tr bgcolor='#FFF7CD'><td valign='top'>
	<table border='0'>
	<tr><td>分發機關：</td><td><input type='text' name='org' value='$org' size='20'></td></tr>
	<tr><td>分發令字號：</td><td><input type='text' name='num' value='$num' size='20'></td></tr>
	<tr><td>報到地點：</td><td><input type='text' name='c_place' value='$c_place' size='20'></td></tr>
	<tr><td>報到日期：</td><td><input type='text' name='c_date' value='$c_date' size='20'></td></tr>
	<tr><td>報到時間：</td><td><input type='text' name='c_time' value='$c_time' size='20'></td></tr>
	<tr><td>註冊日期：</td><td><input type='text' name='p_date' value='$p_date' size='20'></td></tr>
	<tr><td>註冊時間：</td><td><input type='text' name='p_time' value='$p_time' size='20'></td></tr>
	<tr><td>注意事項：<br>（通知聯）</td><td><textarea name='note' cols='40' rows='4'>$note</textarea></td></tr>
	<tr><td>注意事項：<br>（報到聯）</td><td><textarea name='note2' cols='40' rows='4'>$note2</textarea></td></tr>
	<tr><td>家長姓名形式：</td><td><input type='radio' name='model' value='0' checked>XXX 先生<br><input type='radio' name='model' value='1'>XXX 之家長<br><input type='radio' name='model' value='2'>XXX 之戶長</td></tr>
	</table>
	<input type='hidden' name='act' value='send'>
	<input type='submit' name='save' value='儲存 $year 年度資料'><input type='submit' name='ooo' value='下載入學通知單(.sxw)'><input type='submit' name='html' value='下載入學通知單(網頁式)'>
	<td valign='top'><input type='radio' name='sel' value='0' $sel[0]>空白通知單<br><br>
	<input type='radio' name='sel' value='1' $sel[1]>依編號：<br>　　從A<input type='text' size='5' name='start_num'>～A<input type='text' size='5' name='end_num'><br>
	<br><input type='radio' name='sel' value='2' $sel[2]>依學校：<br>";
$query="select distinct old_school from new_stud where stud_study_year='$year' and class_year='$class_year_b'";
$res=$CONN->Execute($query);
$i=0;
while (!$res->EOF) {
	$old_school=$res->fields[old_school];
	$main.="　　<input type='checkbox' name='sch[$i]' value='$old_school'>".$old_school."<br>";
	$i++;
	$res->MoveNext();
}

//說明
$help_text="
下載入學通知單前請先將新生基本資料匯入並進行自動編班，以便先產生臨時的班級和臨時的座號。||
如果您下載的是網頁式的通知單，請使用IE並到「檔案」→「設定列印格式」，將頁首、頁尾的內容都清空，以得到最佳效果。
";
$help=help($help_text);

$main.="</tr></table></tr></table></form>$help
<br>
";
//結束主網頁顯示區
$main.="</td></tr></table>";
echo $main;
//程式檔尾
foot();

function make_ooo($new_stud_table,$new_stud_notification_table){
	global $CONN,$year,$class_year,$class_year_b,$sel_str,$_POST;

	//Openofiice的路徑
	$oo_path = "ooo_notification";

	//檔名種類
	$filename=$year."入學通知單.sxw";

	//新增一個 EasyZip 實例
	$ttt = new EasyZip;
	$ttt->setPath($oo_path);	
	$ttt->addDir("META-INF");
	$ttt->addfile("settings.xml");
	$ttt->addfile("styles.xml");
	$ttt->addfile("meta.xml");

	//讀出 content.xml
	$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

	// 加入換頁 tag
	$data = str_replace("<office:automatic-styles>",'<office:automatic-styles><style:style style:name="BREAK_PAGE" style:family="paragraph" style:parent-style-name="Standard"><style:properties fo:break-before="page"/></style:style>',$data);

	//拆解 content.xml
	$arr1 = explode("<office:body>",$data);
	//檔頭
	$con_head = $arr1[0]."<office:body>";
	$arr2 = explode("</office:body>",$arr1[1]);
	//資料內容
	$con_body = $arr2[0];
	//檔尾
	$con_foot = "</office:body>".$arr2[1];

	//共通資料
	$sql9="select * from $new_stud_notification_table where year='$year' and class_year='$class_year_b'";
	$rs9=$CONN->Execute($sql9) or trigger_error($sql9,256);
	$fd_arr['org']=$rs9->fields['org'];
	$fd_arr['num']=$rs9->fields['num'];
	$fd_arr['c_place']=$rs9->fields['c_place'];
	$fd_arr['c_date']=$rs9->fields['c_date'];
	$fd_arr['c_time']=$rs9->fields['c_time'];
	$fd_arr['p_date']=$rs9->fields['p_date'];
	$fd_arr['p_time']=$rs9->fields['p_time'];
	$fd_arr['note']=str_replace("<br />","</text:p><text:p text:style-name=\"P2\">",nl2br($rs9->fields['note']));
	$fd_arr['note2']=str_replace("<br />","</text:p><text:p text:style-name=\"P2\">",nl2br($rs9->fields['note2']));
	$sql="select * from school_base";
	$res=$CONN->Execute($sql);
	$fd_arr["school_name"]=$res->fields['sch_cname'];
	$fd_arr["sch_addr"]=$res->fields['sch_addr'];
	$fd_arr["sch_post_num"]=$res->fields['sch_post_num'];
	$fd_arr["school_tel"]=$res->fields['sch_phone'];
	$sql="select * from school_room where room_id='2'";
	$res=$CONN->Execute($sql);
	$fd_arr["room_name"]=$res->fields['room_name'];
	$query="select * from temp_class where year='$year' order by class_id";
	$res=$CONN->Execute($query);
	if ($res)
		while (!$res->EOF) {
			$class_id=$res->fields[class_id];
			$cclass[$class_id]=$class_year[substr($class_id,0,1)].$res->fields[c_name]."班";
			$res->MoveNext();
		}

	if (!empty($sel_str)) {
		//每一位學生的基本資料
		$sql="select * from $new_stud_table where stud_study_year='$year' $sel_str order by temp_id";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$count=$rs->RecordCount();
		$replace_data ='';
		$i=1;
		while(!$rs->EOF){
			$temp_arr['old_school'] = $rs->fields['old_school'];
			$temp_arr['stud_name'] = $rs->fields['stud_name'];
			$temp_arr['stud_sex'] = ($rs->fields['stud_sex']=="2")?"女":"男";
			$birth=explode("-",$rs->fields['stud_birthday']);
			$temp_arr['stud_birthday'] =($birth[0]-1911).".".$birth[1].".".$birth[2];
			if ($_POST['model']==1)
				$temp_arr['guardian_name'] = $temp_arr['stud_name']." 之家長";
			elseif ($_POST['model']==2)
				$temp_arr['guardian_name'] = $temp_arr['stud_name']." 之戶長";
			else
				$temp_arr['guardian_name'] = $rs->fields['guardian_name']." 先生";
			$temp_arr['stud_address'] = $rs->fields['stud_address'];
			$temp_arr['temp_id'] = $rs->fields['temp_id'];
			$temp_arr['temp_class'] = $cclass[$rs->fields['temp_class']];
			$temp_arr['temp_site'] = $rs->fields['temp_site'];
			$temp_arr['class']=$c_year."年".$c_name."班";
			$temp_arr['class_site'] = $rs->fields['class_site'];
			$temp_arr['old_class']=$rs->fields['old_class'];
			foreach($fd_arr as $fd_i => $fd_v){
				$temp_arr[$fd_i] = $fd_v;
			}

			$replace_data .= $ttt->change_temp($temp_arr,$con_body,0);
			if ($i<$count) $replace_data .='<text:p text:style-name="BREAK_PAGE"/>';
			$i++;
			$rs->MoveNext();
		}
	} else {
		//空白表中該有的資料
		foreach($fd_arr as $fd_i => $fd_v){
			$temp_arr[$fd_i] = $fd_v;
		}
		$temp_arr['guardian_name'] = "　　　";
		$temp_arr['temp_id'] = "　　　";
		$temp_arr['temp_class'] = "　　　　";
		$replace_data .= $ttt->change_temp($temp_arr,$con_body,0);
	}

	$replace_data = $con_head.$replace_data.$con_foot;

	//把一些多餘的標籤以空白取代
	$pattern[]="/\{([^\}]*)\}/";
	$replacement[]="";
	$replace_data=preg_replace($pattern, $replacement, $replace_data);
	$replace_data=str_replace ('&lt;br /&gt;', '</text:p><text:p text:style-name=\'Standard\'>', $replace_data);

	// 加入 content.xml 到zip 中
	$ttt->add_file($replace_data,"content.xml");

	//產生 zip 檔
	$sss = $ttt->file();

	//以串流方式送出 ooo.sxw
	header("Content-disposition: attachment; filename=$filename");
	header("Content-type: application/vnd.sun.xml.writer");
	echo $sss;
	exit;
}

function make_html($new_stud_table,$new_stud_notification_table){
	global $CONN,$year,$class_year,$class_year_b,$sel_str,$_POST;

	echo "	<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\"><title></title><style>
		<!--
			P { margin-bottom: 0.21cm }
			TD P { margin-bottom: 0.21cm }
			.dotline {BORDER-BOTTOM-STYLE: dotted; BORDER-LEFT-STYLE: dotted; BORDER-RIGHT-STYLE: dotted; BORDER-TOP-STYLE: dotted
		-->
		</style></head><body>";

	//共通資料
	$sql9="select * from $new_stud_notification_table where year='$year' and class_year='$class_year_b'";
	$rs9=$CONN->Execute($sql9) or trigger_error($sql9,256);
	$org=$rs9->fields['org'];
	$num=$rs9->fields['num'];
	$c_place=$rs9->fields['c_place'];
	$c_date=$rs9->fields['c_date'];
	$c_time=$rs9->fields['c_time'];
	$p_date=$rs9->fields['p_date'];
	$p_time=$rs9->fields['p_time'];
	$note=nl2br($rs9->fields['note']);
	$note2=nl2br($rs9->fields['note2']);
	$sql="select * from school_base";
	$res=$CONN->Execute($sql);
	$school_name=$res->fields['sch_cname'];
	$sch_addr=$res->fields['sch_addr'];
	$sch_post_num=$res->fields['sch_post_num'];
	$school_tel=$res->fields['sch_phone'];
	$sql="select * from school_room where room_id='2'";
	$res=$CONN->Execute($sql);
	$room_name=$res->fields['room_name'];
	$query="select * from temp_class where year='$year' order by class_id";
	$res=$CONN->Execute($query);
	if ($res)
		while (!$res->EOF) {
			$class_id=$res->fields[class_id];
			$cclass[$class_id]=$class_year[substr($class_id,0,1)].$res->fields[c_name]."班";
			$res->MoveNext();
		}

	if (!empty($sel_str)) {
		//每一位學生的基本資料
		$sql="select * from $new_stud_table where stud_study_year='$year' $sel_str order by temp_id";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$count=$rs->RecordCount();
		$replace_data ='';
		$i=1;
		while(!$rs->EOF){
			$old_school = $rs->fields['old_school'];
			$stud_name = $rs->fields['stud_name'];
			$stud_sex = ($rs->fields['stud_sex']=="2")?"女":"男";
			$birth = explode("-",$rs->fields['stud_birthday']);
			$stud_birthday =($birth[0]-1911).".".$birth[1].".".$birth[2];
                        if ($_POST['model']==1)
                                $guardian_name = $stud_name." 之家長";
                        elseif ($_POST['model']==2)
                                $guardian_name = $stud_name." 之戶長";
                        else
                                $guardian_name = $rs->fields['guardian_name']." 先生";
			$stud_address = $rs->fields['stud_address'];
			$temp_id = $rs->fields['temp_id'];
			$temp_class = $cclass[$rs->fields['temp_class']];
			$temp_site = $rs->fields['temp_site'];
			$class = $c_year."年".$c_name."班";
			$class_site = $rs->fields['class_site'];
			$old_class = $rs->fields['old_class'];
			foreach($fd_arr as $fd_i => $fd_v){
				$temp_arr[$fd_i] = $fd_v;
			}
			$np=($i<$count)?"<span lang=\"zh-tw\" style=\"font-size:8.0pt;font-family:&quot;Times New Roman&quot;mso-fareast-font-family:新細明體;mso-font-kerning:1.0pt;mso-ansi-language:zh-tw;mso-fareast-language:ZH-TW;mso-bidi-language:zh-tw\"><br clear=\"all\" style=\"mso-special-character:line-break;page-break-before:always\"></span>":"";
			$i++;
			show_html($sch_post_num,$sch_addr,$school_name,$room_name,$school_tel,$stud_address,$guardian_name,$stud_name,$stud_sex,$org,$num,$c_place,$c_date,$c_time,$p_date,$p_time,$note,$note2,$temp_id,$temp_class,$temp_site,$stud_birthday,$old_school,$old_class,$np);
			$rs->MoveNext();
		}
	} else { 
		show_html($sch_post_num,$sch_addr,$school_name,$room_name,$school_tel,"　","　　　","","",$org,$num,$c_place,$c_date,$c_time,$p_date,$p_time,$note,$note2,"","","","","","","");
	}
	echo "</body></html>";
	exit;
}

function show_html($sch_post_num,$sch_addr,$school_name,$room_name,$school_tel,$stud_address,$guardian_name,$stud_name,$stud_sex,$org,$num,$c_place,$c_date,$c_time,$p_date,$p_time,$note,$note2,$temp_id,$temp_class,$temp_site,$stud_birthday,$old_school,$old_class,$np) {

	echo "
<p STYLE=\"margin-bottom: 0cm\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"Dotum, sans-serif\">$sch_post_num</font><br>
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">$sch_addr<br>
".$school_name."　".$room_name."<br>
　　　　　　學校電話：</font><font FACE=\"Dotum, sans-serif\">$school_tel</font></p>
<p STYLE=\"margin-bottom: 0cm\">　</p>
<p STYLE=\"margin-bottom: 0cm\"><font face=\"標楷體\" size=\"4\">　<br>　　　　　　".$stud_address."<br>　　　　　　".$guardian_name."　啟</p>
<p STYLE=\"margin-bottom: 0cm\"><br><br><br><br></p>
<hr width=\"95%\" class=\"dotline\" size=\"2\" color=\"#000000\">
<p ALIGN=\"CENTER\" STYLE=\"margin-bottom: 0cm\">
<font SIZE=\"5\" STYLE=\"font-size: 20pt\" FACE=\"標楷體\">".$school_name."入學通知單(通知聯)</font><br><br>
<center>
<table WIDTH=\"605\" BORDER=\"3\" BORDERCOLOR=\"#000000\" CELLPADDING=\"4\" CELLSPACING=\"0\">
<colgroup><col WIDTH=\"76\"><col WIDTH=\"208\"><col WIDTH=\"79\"><col WIDTH=\"204\"></colgroup>
<thead>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">學生姓名</font></td>
<td WIDTH=\"208\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$stud_name."　</font></td>
<td WIDTH=\"79\" VALIGN=\"TOP\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">性　　別</font></td>
<td WIDTH=\"204\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$stud_sex."　</font></td>
</tr>
</thead>
<tr>
<td>
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">分發機關</font></td>
<td COLSPAN=\"3\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$org."　</font></td>
</tr>
<tr>
<td>
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">分發文字號</font></td>
<td COLSPAN=\"3\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$num."　</font></td>
</tr>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">報到地點</font></td>
<td COLSPAN=\"3\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$c_place."　</font></td>
</tr>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">報到日期</font></td>
<td WIDTH=\"208\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"Dotum, sans-serif\">".$c_date."　</font></td>
<td WIDTH=\"79\" VALIGN=\"TOP\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">報到時間</font></td>
<td WIDTH=\"204\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"Dotum, sans-serif\">".$c_time."　</font></td>
</tr>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">註冊日期</font></td>
<td WIDTH=\"208\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"Dotum, sans-serif\">".$p_date."　</font></td>
<td WIDTH=\"79\" VALIGN=\"TOP\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">註冊時間</font></td>
<td WIDTH=\"204\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"Dotum, sans-serif\">".$p_time."　</font></td>
</tr>
</table>
</center>
<p STYLE=\"margin-bottom: 0cm\"><font SIZE=\"2\" FACE=\"標楷體\">".$note."</font></p>
<hr width=\"95%\" class=\"dotline\" size=\"2\" color=\"#000000\">
<p ALIGN=\"CENTER\" STYLE=\"margin-bottom: 0cm\">
<font SIZE=\"5\" STYLE=\"font-size: 20pt\" FACE=\"標楷體\">".$school_name."入學通知單(報到聯)</font><br>
<font SIZE=\"2\" STYLE=\"font-size: 12pt\" FACE=\"標楷體\">報到編號：<font SIZE=\"2\" STYLE=\"font-size: 12pt\" FACE=\"Dotum, sans-serif\">$temp_id</font>
　　　　　　臨時班級：$temp_class  
　　　　　　臨時座號：<font SIZE=\"2\" STYLE=\"font-size: 12pt\" FACE=\"Dotum, sans-serif\">$temp_site</font></font></p>
<center>
<table WIDTH=\"605\" BORDER=\"3\" BORDERCOLOR=\"#000000\" CELLPADDING=\"4\" CELLSPACING=\"0\">
<colgroup>
<col WIDTH=\"76\"><col WIDTH=\"166\"><col WIDTH=\"49\"><col WIDTH=\"49\">
<col WIDTH=\"49\"><col WIDTH=\"163\">
</colgroup>
<thead>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">學生姓名</font></td>
<td WIDTH=\"166\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$stud_name."　</font></td>
<td WIDTH=\"49\" VALIGN=\"TOP\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">性　別</font></td>
<td WIDTH=\"49\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$stud_sex."　</font></td>
<td WIDTH=\"49\" VALIGN=\"TOP\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">生　日</font></td>
<td WIDTH=\"163\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">
<font FACE=\"Dotum, sans-serif\">".$stud_birthday."　</font></font></td>
</tr>
</thead>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">家長姓名</font></td>
<td COLSPAN=\"5\" WIDTH=\"507\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$guardian_name."　</font></td>
</tr>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">戶籍地址</font></td>
<td COLSPAN=\"5\" WIDTH=\"507\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$stud_address."　</font></td>
</tr>
<tr>
<td WIDTH=\"76\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">畢業國小</font></td>
<td COLSPAN=\"2\" WIDTH=\"223\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$old_school."　</font></td>
<td COLSPAN=\"2\" WIDTH=\"105\" VALIGN=\"TOP\">
<p ALIGN=\"CENTER\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">國小班級</font></td>
<td WIDTH=\"163\" VALIGN=\"TOP\">
<p ALIGN=\"LEFT\" STYLE=\"font-style: normal; font-weight: medium\">
<font SIZE=\"2\" STYLE=\"font-size: 11pt\" FACE=\"標楷體\">".$old_class."　</font></td>
</tr>
</table>
</center>
<p><font SIZE=\"2\" FACE=\"標楷體\">$note2</font></p>
".$np;
	return;
}
?>
