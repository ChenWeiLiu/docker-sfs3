<?php

// $Id:  $

//取得設定檔
include_once "config.php";

sfs_check();

// 健保卡查核
switch ($ha_checkary){
        case 2:
                ha_check();
                break;
        case 1:
                if (!check_home_ip()){
                        ha_check();
                }
                break;
}

//秀出網頁
head("生涯統整面面觀");

//模組選單
print_menu($menu_p);

//檢查是否開放
if (!$mystory){
   echo "模組變數尚未開放本功能，請洽詢學校系統管理者！";
   exit;
}

//取得目前學年度
$curr_year=curr_year();
$curr_seme=curr_seme();

//目前選定學期
$seme_year_seme=sprintf('%03d%1d',$curr_year,$curr_seme);
$student_sn=$_SESSION['session_tea_sn'];
$stud_name=$_SESSION['session_tea_name'];

$menu=$_POST['menu'];

$min=$IS_JHORES?7:4;
$max=$IS_JHORES?9:6;


//抓取學生戶籍地址
$query="select stud_addr_2 from stud_base where student_sn=$student_sn";
$res=$CONN->Execute($query);
$stud_addr=$res->fields[0];

//抓取學生本學期就讀班級
$query="select * from stud_seme where student_sn=$student_sn and seme_year_seme='$seme_year_seme'";
$res=$CONN->Execute($query);
$seme_class=$res->fields['seme_class'];
$seme_class_name=$res->fields['seme_class_name'];
$seme_num=$res->fields['seme_num'];
$stud_grade=substr($seme_class,0,-2);

//儲存紀錄處理
if($_POST['go']=='儲存紀錄'){
	switch($menu){
		case 1:
			$ponder=serialize($_POST['ponder']);
			//檢查是否已有舊紀錄
			$query="select sn from career_view where student_sn=$student_sn";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$sn=$res->fields[0];
			if($sn) $query="update career_view set ponder='$ponder' where sn=$sn";
				else $query="insert into career_view set student_sn=$student_sn,ponder='$ponder'";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");	
			break;
		case 2:
			$direction=serialize($_POST['direction']);
			//檢查是否已有舊紀錄
			$query="select sn from career_view where student_sn=$student_sn";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$sn=$res->fields[0];
			if($sn) $query="update career_view set direction='$direction' where sn=$sn";
			else $query="insert into career_view set student_sn=$student_sn,direction='$direction'";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");	
			break;
	}
}

//產生選單
$memu_select="※我是 $stud_name ，本學期就讀班級： $seme_class ，座號： $seme_num 。<br>※我要檢視或設定";
$menu_arr=array(1=>'生涯思索',2=>'選擇方向',3=>'想升讀學科及學校的地理交通');
foreach($menu_arr as $key=>$title){
	$checked=($menu==$key)?'checked':''; 
	$color=($menu==$key)?'#0000ff':'#000000'; 
	$memu_select.="<input type='radio' name='menu' value='$key' $checked onclick='this.form.submit();'><b><font color='$color'>$title</font></b>";}


//檢查是否為可填寫月份
$view_months="[,$view_months,]";
$pos=strpos($view_months,$curr_month,1);
	
$act=($menu and $pos)?"<center><input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")' style='border-width:1px; cursor:hand; color:white; background:#5555ff; font-size:20px; height=42'></center>":"◎學校設定可填寫月份：$m_arr[view_months]";
switch($menu){
	case 1:
		//抓取生涯方向思考項目參照表
		$ponder_items=SFS_TEXT('生涯方向思考項目');
	
		//取得既有資料
		$query="select ponder from career_view where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		$ponder_array=unserialize($res->fields['ponder']);
		
		$ponder_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td>NO.</td><td>項目</td><td>內容</td></tr>";
			
		$ponder_list.="<td bgcolor='$bgcolor'>";
		foreach($ponder_items as $key=>$value){
			$ii++;
			if($pos)
				$ponder_list.="<tr><td align='center'>$ii</td><td>$value</td><td><textarea name='ponder[$key]' style='border-width:1px; width=100%; height=100%;'>{$ponder_array[$key]}</textarea></td></tr>";
			else $ponder_list.="<tr><td align='center'>$ii</td><td>$value</td><td>".str_replace("\r\n","<br>",$ponder_array[$key])."</td></tr>";
		}
		$ponder_list.='</tr></table>';
		
		$showdata="$ponder_list";
		
		break;
	case 2:	
		//抓取生涯選擇方向參照表
		$direction_items=SFS_TEXT('生涯選擇方向');
		//取得既有資料
		$query="select direction from career_view where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		$direction_array=unserialize($res->fields['direction']);
		$max=max(3,count($direction_array['item']));

			$direction_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#ccccff' align='center'><td>項次</td><td>自己的想法</td><td>家長的期望</td><td>學校教師的建議</td><td>備註</td></tr>";
			
			$direction_initial=array(1=>'self',2=>'parent',3=>'teacher');
			for($i=1;$i<=$max;$i++){
				$direction_list.="<tr bgcolor='#ffeeee'><td align='center'>$i</td>";
				foreach($direction_initial as $key=>$value){
					if($pos) {
						$target="<select name='direction[item][$i][$value]'><option value=''></option>";
						foreach($direction_items as $d_key=>$d_value){
								$target_value=$direction_array['item'][$i][$value];
								$selected=($d_key==$target_value)?'selected':'';
								$target.="<option value='$d_key' $selected>$d_value</option>";					
						}
						$target.='</select>';
					} else {
						$target="$d_key $d_value";					
					}
					$direction_list.="<td>$target</td>";				
				}
				if($pos) $direction_list.="<td><textarea name='direction[item][$i][memo]' style='border-width:1px; width=100%; height=100%;'>{$direction_array[item][$i][memo]}</textarea></td></tr>";
					else $direction_list.="<td>".str_replace("\r\n","<br>",$direction_array[item][$i][memo])."</td></tr>";
			}			
			$direction_list.='</table>';
		
		$direction_list.="<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width='100%'>";
		if($pos) {
			$checked=$direction_array['identical']?'checked':'';
			$direction_list.="<tr><td align='center' rowspan=2 bgcolor='#ccffcc'>想<br><br>一<br><br>想</td><td colspan=5>1.自己的想法是否和家長期望或老師建議一致？ <input type='checkbox' name='direction[identical]' value=1 $checked>是
				<br><br>原因：<textarea name='direction[reason]' style='border-width:1px; width=100%; height=100%; background:#ffeeee'>{$direction_array['reason']}</textarea></td></tr>
				<tr><td colspan=5>2.如果我的想法與家長的期望不同，可以如何溝通呢？<br><textarea name='direction[communicate]' style='border-width:1px; width=100%; height=100%; background:#ffeeee'>{$direction_array['communicate']}</textarea></td></tr>";
		} else {
			$checked=$direction_array['identical']?'■是 □否':'□是 ■否';
			$direction_list.="<tr><td align='center' rowspan=2 bgcolor='#ccffcc'>想<br><br>一<br><br>想</td><td colspan=5>1.自己的想法是否和家長期望或老師建議一致？ $checked
				<br><br>原因：".str_replace("\r\n","<br>",$direction_array['reason'])."</td></tr>
				<tr><td colspan=5>2.如果我的想法與家長的期望不同，可以如何溝通呢？<br>".str_replace("\r\n","<br>",$direction_array['communicate'])."</td></tr>";
		}
		$direction_list.='</table>';
		
		//想升讀的學科
		$query="select * from career_school where student_sn=$student_sn order by aspiration_order";
		$res=$CONN->Execute($query);
		
		
		$showdata="$direction_list";
		
		break;
	case 3:
		if($_POST['go']=='修改'){
			$query="update career_course set aspiration_order='{$_POST['aspiration_order']}',school='{$_POST['school']}',course='{$_POST['course']}',position='{$_POST['position']}',transportation='{$_POST['transportation']}',transportation_time='{$_POST['transportation_time']}',transportation_toll='{$_POST['transportation_toll']}',memo='{$_POST['memo']}' where sn={$_POST['edit_sn']}";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$_POST['edit_sn']=0;
		} elseif($_POST['go']=='刪除'){
			$query="delete from career_course where sn={$_POST['edit_sn']}";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
			$_POST['edit_sn']=0;
		} elseif($_POST['go']=='新增'){
			$query="insert into career_course set student_sn=$student_sn,aspiration_order=0,school='○○市立○○高中(職)',position='←→',transportation='步行←→火車←→捷運←→公車←→步行',transportation_time='○時○分',transportation_toll='○元'";
			$res=$CONN->Execute($query) or die("SQL錯誤:$query");
		}	
		
		$act=" ◎可填寫月份：$m_arr[view_months] ◎ ";
		//表格欄位抬頭
		$my_act=$pos?"<input type='submit' name='go' value='新增'><input type='hidden' name='edit_sn' value=''><input type='hidden' name='add' value=''>":"";
		$course_list="※我的聯絡地址： $stud_addr 。<br>※我想升讀的學程或科別($act) $my_act
			<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' id='AutoNumber1' width=100%>
			<tr align='center' bgcolor='#ffcccc'>
			<td>志願序</td><td>學校</td><td>學程或科別</td><td>地理位置</td><td>交通方式</td><td>往返時間</td><td>往返車資</td><td>備註</td>";
		
		//抓取課程志願
		$query="select * from career_course where student_sn=$student_sn order by aspiration_order";
		$res=$CONN->Execute($query);
		if($res->RecordCount()){
			while(!$res->EOF){
				$ii=$res->fields['aspiration_order'];
				$sn=$res->fields['sn'];
				if($_POST['edit_sn']==$sn and $pos){
					foreach($level_array as $key=>$value){
						$checked=($key==$res->fields['level'])?'checked':'';
						$level_radio.="<input type='radio' name='level' value='$key' $checked>$value<br>";
					}
					foreach($squad_array as $key=>$value){
						$checked=($key==$res->fields['squad'])?'checked':'';
						$squad_radio.="<input type='radio' name='squad' value='$key' $checked>$value<br>";
					}
					$course_list.="<tr align='center' bgcolor='#ffffcc'>
						<td><input type='text' name='aspiration_order' value='$ii' size=3><input type='hidden' name='del_sn' value='{$_POST['edit_sn']}'>
						<br><input type='submit' value='修改' name='go' onclick='document.myform.edit_sn.value=\"$sn\";return confirm(\"確定要\"+this.value+\"?\")'>
						<br><input type='submit' value='刪除' name='go' onclick='document.myform.edit_sn.value=\"$sn\"; return confirm(\"確定要\"+this.value+\"?\")'>
						<br><input type='reset' value='取消' onclick='this.form.submit();'>
						</td>
						<td><input type='text' name='school' value='{$res->fields['school']}'></td>
						<td><input type='text' name='course' value='{$res->fields['course']}' size=10></td>
						<td><input type='text' name='position' value='{$res->fields['position']}' size=10></td>
						<td><input type='text' name='transportation' value='{$res->fields['transportation']}' size=30></td>
						<td><input type='text' name='transportation_time' value='{$res->fields['transportation_time']}' size=10></td>
						<td><input type='text' name='transportation_toll' value='{$res->fields['transportation_toll']}' size=5></td>
						<td><textarea name='memo'  style='border-width:1px; color:brown; width=100%; height=100%;'>{$res->fields['memo']}</textarea></td>
						</tr>";
				} else {
					$memo=str_replace("\r\n",'<br>',$res->fields['memo']);
					$java_script=$pos?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#ccccff';\" onMouseOut=\"this.style.backgroundColor='#ffffff';\" ondblclick='document.myform.edit_sn.value=\"$sn\"; document.myform.submit();'":"";
					$course_list.="<tr align='center' $java_script>
						<td>$ii</td>
						<td><input type='button' style='border-width:1px; color:blue; width=100%;' value='{$res->fields['school']}' onclick='calcRoute(\"$stud_addr\",\"{$res->fields['school']}\");'></td>
						<td>{$res->fields['course']}</td>
						<td>{$res->fields['position']}</td>
						<td>{$res->fields['transportation']}</td>
						<td>{$res->fields['transportation_time']}</td>
						<td>{$res->fields['transportation_toll']}</td>
						<td align='left'>$memo</td>
						</tr>";	
				}
				$res->MoveNext();
			}
		} else $course_list.="<tr align='center'><td colspan=7 height=24>未發現想升讀的學程或科別紀錄！</td></tr>";
		$course_list.="</table>";
		
		$showdata="<br>$course_list";
	
	break;
}

$geodata=($menu==3)?'<SCRIPT type=text/javascript src="http://maps.googleapis.com/maps/api/js?sensor=false"></SCRIPT>

	<SCRIPT type=text/javascript>
	var directionsDisplay;
	var directionsService = new google.maps.DirectionsService();
	var map;
	var oldDirections = [];
	var currentDirections = null;

	function initialize() {
	var myOptions = {
	zoom: '.$gmap_zoom.',
	center: new google.maps.LatLng('.$gmap_location.'),
	mapTypeId: google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	directionsDisplay = new google.maps.DirectionsRenderer({
	"map": map,
	"preserveViewport": true,
	"draggable": true
	});
	directionsDisplay.setPanel(document.getElementById("directions_panel"));

	google.maps.event.addListener(directionsDisplay, "directions_changed",
	function() {
	if (currentDirections) {
	oldDirections.push(currentDirections);
	setUndoDisabled(false);
	}
	currentDirections = directionsDisplay.getDirections();
	});

	setUndoDisabled(true);

	calcRoute("'.$stud_addr.'","'.$school_short_name.'");
	}

	function calcRoute(start,end) {
		
	var mstart = start;
    var mend = end;

	var request = {
	origin:mstart,
	destination:mend,
	travelMode: google.maps.DirectionsTravelMode.DRIVING
	};
	directionsService.route(request, function(response, status) {
	if (status == google.maps.DirectionsStatus.OK) {
	directionsDisplay.setDirections(response);
	}
	});
	}

	function undo() {
	currentDirections = null;
	directionsDisplay.setDirections(oldDirections.pop());
	if (!oldDirections.length) {
	setUndoDisabled(true);
	}
	}

	function setUndoDisabled(value) {
	document.getElementById("undo").disabled = value;
	}


	$(function(){ 
		initialize();
	})
</SCRIPT>

<DIV style="WIDTH: 75%; FLOAT: left; HEIGHT: 500" id=map_canvas></DIV>
<DIV style="WIDTH: 25%; FLOAT: right; HEIGHT: 500; OVERFLOW: auto">
<BUTTON style="MARGIN: auto; DISPLAY: block" id="undo" onclick=undo()>Undo</BUTTON> 
<DIV style="WIDTH: 100%; font-size: 12px; color:blue;" id=directions_panel></DIV></DIV>':'';


$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'>$memu_select $showdata<br>$act </form></font>$geodata";

echo $main;

foot();

?>
