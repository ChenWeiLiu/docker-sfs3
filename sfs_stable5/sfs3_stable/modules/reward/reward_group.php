<?php

// $Id: reward_group.php 7193 2013-03-05 16:07:47Z smallduh $

//取得設定檔
include_once "config.php";

sfs_check();

//取得學年學期
$year_seme=$_REQUEST[year_seme];
if ($year_seme) {
	$sel_year=intval(substr($year_seme,0,3));
	$sel_seme=substr($year_seme,3,1);
} else {
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

//處理日期
if ($_POST[temp_reward_date]) {
	$dd=explode("-",$_POST[temp_reward_date]);
	if ($dd[0]<1911) $dd[0]+=1911;
	$_POST[temp_reward_date]=implode("-",$dd);
}

//取得內容序號
$sel_dep=$_REQUEST[sel_dep];

//取得處理模式
$act=$_REQUEST[act];

$One=($_POST[One])?$_POST[One]:$_POST[stud_id];
$year_name=$_POST[year_name];
$class_name=$_POST[class_name];
$class_num=$_POST[class_num];
$past_class_id=$_POST[past_class_id];
if ($One) {
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$One'";
	$res=$CONN->Execute($query);
	$seme_class=$res->fields[seme_class];
	$class_num=intval($res->fields[seme_num]);
	$year_name=intval(substr($seme_class,-3,1))-$IS_JHORES;
	$class_name=intval(substr($seme_class,-2,2));
} else {
	if ($year_name && $class_name && $class_num) {
		if ($year_name>$IS_JHORES) {
			$year_name-=$IS_JHORES;
		}
		$seme_class=sprintf("%d%02d",($year_name+$IS_JHORES),$class_name);
		$query="select a.stud_id,b.stud_study_cond from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and a.seme_num='$class_num' and b.student_sn=a.student_sn and b.stud_study_cond='0'";
		//$query="select stud_id from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' and seme_num='$class_num'";
		$res=$CONN->Execute($query);
		$One=$res->fields['stud_id'];
	}
}

if ($year_name && $class_name) 
	$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,($year_name+$IS_JHORES),$class_name);
else
	$class_id=$_REQUEST[class_id];
if ($past_class_id && $class_id!=$past_class_id) $One="";

//加入一班資料
if ($One=="all") {
	if ($class_id) {
		$c=explode("_",$class_id);
		$seme_class=intval($c[2]).$c[3];
		$query="select * from reward where reward_id='$sel_dep'";
		$res=$CONN->Execute($query) or trigger_error($query);
		$rd=$res->fields['reward_div'];
		$rk=$res->fields['reward_kind'];
		$rys=$res->fields['reward_year_seme'];
		$rdt=$res->fields['reward_date'];
		$rr=addslashes($res->fields['reward_reason']);
		$rcd=date("Y-m-j");
		$rb=addslashes($res->fields['reward_base']);
		$rccd=$res->fields['reward_cancel_date'];
		$rs=$res->fields['reward_sub'];
		$rip=getip();
		$query="select a.* from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and b.stud_study_cond='0' order by a.seme_num";
		$res=$CONN->Execute($query) or trigger_error($query);
		while (!$res->EOF) {
			$id=$res->fields['stud_id'];
			$sn=$res->fields['student_sn'];
			$query_insert="insert into reward (reward_div,reward_kind,stud_id,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$rd','$rk','$id','$rys','$rdt','$rr','$rcd','$rb','$rccd','$_SESSION[session_log_id]','$rip','$rs','$sel_dep','$sn')";
			$res_insert=$CONN->Execute($query_insert) or trigger_error($query_insert);
			cal_rew(substr($rys,0,strlen($rys)-1),substr($rys,-1),$id); //即時統計總表 by smallduh 2013.3.5
			$res->MoveNext();			
		}
	}
	$One="";
}

//加入一人資料
if ($_POST[add] && $One) {
	$query="select * from reward where reward_id='$sel_dep'";
	$res=$CONN->Execute($query) or trigger_error($query);
	$rd=$res->fields['reward_div'];
	$rk=$res->fields['reward_kind'];
	$rys=$res->fields['reward_year_seme'];
	$rdt=$res->fields['reward_date'];
	$rr=addslashes($res->fields['reward_reason']);
	$rcd=date("Y-m-j");
	$rb=addslashes($res->fields['reward_base']);
	$rccd=$res->fields['reward_cancel_date'];
	$rs=$res->fields['reward_sub'];
	$rip=getip();
	$id=$One;
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$id'";
	$res=$CONN->Execute($query) or trigger_error($query);
	$sn=$res->fields['student_sn'];
	$query_insert="insert into reward (reward_div,reward_kind,stud_id,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$rd','$rk','$id','$rys','$rdt','$rr','$rcd','$rb','$rccd','$_SESSION[session_log_id]','$rip','$rs','$sel_dep','$sn')";
	$res_insert=$CONN->Execute($query_insert) or trigger_error($query_insert);
	cal_rew(substr($rys,0,strlen($rys)-1),substr($rys,-1),$id); //即時統計總表 by smallduh 2013.1.8
}

//取得週次
$weeks_array=get_week_arr($sel_year,$sel_seme,$_POST[temp_reward_date]);
$sel_week=$_REQUEST[sel_week];
if ($sel_week) $weeks_array[0]=$sel_week;
if ($weeks_array[0]=="") $weeks_array[0]=1;
$sel_week=$weeks_array[0];

//若不為清除模式則取得內容 
if ($act!="清除內容" && ($act=="確定新增" || $act=="確定修改")) {
	//取得獎懲內容
	$reward_kind=$_POST[reward_kind];
	$reward_reason=$_POST[reward_reason];
	$reward_base=$_POST[reward_base];
	$reward_date=$_POST[temp_reward_date];
}

//執行動作判斷
if($act=="edit"){
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[reward_id]);
}elseif($act=="確定新增"){
	if (!empty($reward_kind) && !empty($reward_reason) && !empty($reward_base)){
		$reward_year_seme=$sel_year.$sel_seme;
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=date("Y-m-j");
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn) values ('$reward_div','','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','')"; 
		$res=$CONN->Execute($query);
	}
	$main=&mainForm($sel_year,$sel_seme,$CONN->Insert_ID());
}elseif($act=="確定修改"){
	$reward_id=$_POST[reward_id];
	$reward_div=($reward_kind>0)?"1":"2";
	$query="update reward set reward_div='$reward_div',reward_kind='$reward_kind',reward_reason='$reward_reason',reward_base='$reward_base',reward_date='$reward_date' where reward_id='$reward_id'";
	$CONN->Execute($query);
	//修改對應之個人記錄
	$query="update reward set reward_div='$reward_div',reward_kind='$reward_kind',reward_reason='$reward_reason',reward_base='$reward_base',reward_date='$reward_date' where dep_id='$reward_id'";
	$CONN->Execute($query);
	$main=&mainForm($sel_year,$sel_seme,$_POST[reward_id]);
}elseif($act=="adddata"){
	$reward_id=$_GET[reward_id];
	$main=&mainForm($sel_year,$sel_seme,$_GET[reward_id]);
}elseif($act=="del_group"){
	$query="delete from reward where reward_id='$_REQUEST[reward_id]'";
	$CONN->Execute($query);
	$query="delete from reward where dep_id='$_REQUEST[reward_id]'";
	$CONN->Execute($query);
	$main=&mainForm($sel_year,$sel_seme,"");
}elseif($act=="del"){
	del_one($sel_year,$sel_seme,$_REQUEST[reward_id]);
	$main=&mainForm($sel_year,$sel_seme,"");
}else{
	$main=&mainForm($sel_year,$sel_seme,"");
}


//秀出網頁
head("團體獎懲登記");
echo $main;
foot();

//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$reward_id=""){
	global $student_menu_p,$CONN,$reward_arr,$sel_week,$reward_kind,$reward_reason,$reward_base,$reward_date,$act,$One,$class_id,$IS_JHORES;
	//相關功能表
	$tool_bar=&make_menu($student_menu_p);

	//是否為修改模式
	if ($reward_id) {
		$query="select * from reward where reward_id='$reward_id'";
		$res=$CONN->Execute($query);
		$reward_kind=$res->fields[reward_kind];
		$reward_reason=$res->fields[reward_reason];
		$reward_base=$res->fields[reward_base];
		$reward_date=$res->fields[reward_date];
		$submit_msg="確定修改";
		
	} else {
		$submit_msg="確定新增";
	}

	//學年學期選單
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$year_seme_p=get_class_seme();
	$year_seme_select = "<select name='year_seme' onchange='this.form.submit()';>\n";
	while (list($k,$v)=each($year_seme_p)){
		if ($seme_year_seme==$k)
	      		$year_seme_select.="<option value='$k' selected>$v</option>\n";
	      	else
	      		$year_seme_select.="<option value='$k'>$v</option>\n";
	}
	$year_seme_select.= "</select>"; 

	//取得填寫表格
	$signForm=&signForm($sel_year,$sel_seme,$act,$reward_id);

	//獎懲選單
	$sel1 = new drop_select(); //選單類別
	$sel1->s_name = "reward_kind"; //選單名稱	
	$sel1->id = $reward_kind; //預設選項	
	$sel1->arr = $reward_arr; //內容陣列		
	$sel1->top_option = "-- 選擇獎懲 --";
	$reward_select=$sel1->get_select();

	// 日期函式
	if ($reward_date=="") $reward_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d"),date("Y")));
	$seldate=new date_class("myform");
	$seldate->demo="";

	//獎懲日期
	$date_input=$seldate->date_add("reward_date",$reward_date);

	$main="
	$tool_bar
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<form action='$_SERVER[SCRIPT_NAME]' name='base_form' method='post'>
	<tr class='title_sbody2'>
	<td>請選學年度<td align='left' bgcolor='white' colspan='2'>$year_seme_select<input type='hidden' name='reward_kind' value='$reward_kind'><input type='hidden' name='reward_reason' value='$reward_reason'><input type='hidden' name='reward_base' value='$reward_base'><input type='hidden' name='temp_reward_date' value='$reward_date'>
	</tr>
	</form>
	<form action='$_SERVER[SCRIPT_NAME]' method='post'>
	<tr class='title_sbody2'>
	<td>獎懲類別<td align='left' bgcolor='white'>$reward_select<td align='left' bgcolor='white' rowspan='4'><input type='submit' name='act' value='$submit_msg'><br><input type='submit' name='act' value='清除內容'><input type='hidden' name='reward_id' value='$reward_id'><input type='hidden' name='year_seme' value='$seme_year_seme'>
	</tr>
	<tr class='title_sbody2'>
	<td>獎懲事由<td align='left' bgcolor='white'><input type='text' name='reward_reason' value='$reward_reason' size='30' maxlength='30'>
	</tr>
	<tr class='title_sbody2'>
	<td>獎懲依據<td align='left' bgcolor='white'><input type='text' name='reward_base' value='$reward_base' size='30' maxlength='30'>
	</tr>
	<tr class='title_sbody2'>
	<td>獎懲日期<td align='left' bgcolor='white'>$date_input
	</tr>
	</form>
	</table>
	$signForm
	";
	return $main;
}

//取得該班及學生名單，以及填寫表格
function &signForm($sel_year,$sel_seme,$act,$id=""){
	global $CONN,$weekN,$weeks_array,$reward_arr,$sel_week,$sel_dep,$class_year,$One,$year_name,$class_name,$class_num,$class_id;

	//取得學生陣列
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$all_id="";
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class,seme_num";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$student_sn=$res->fields[student_sn];
		$stud_id=$res->fields[stud_id];
		$seme_class[$stud_id]=$res->fields[seme_class];
		$seme_num[$stud_id]=$res->fields[seme_num];
		$all_sn.="'".$student_sn."',";
		$res->MoveNext();
	}
	$all_sn=substr($all_sn,0,-1);
	$query="select stud_id,stud_name from stud_base where student_sn in ($all_sn)";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$stud_id=$res->fields[stud_id];
		$stud_name[$stud_id]=addslashes($res->fields[stud_name]);
		$res->MoveNext();
	}

	//取得班級陣列
	$query="select class_id,c_name from school_class where year='$sel_year' and semester='$sel_seme' order by class_id";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$c=explode("_",$res->fields[class_id]);
		$c_year=intval($c[2]);
		$c_name[$c_year.$c[3]]=$class_year[$c_year].$res->fields[c_name]."班";
		$res->MoveNext();
	}

	//週別資料
	$year_seme=sprintf("%03d",$sel_year).$sel_seme;
	while (list($k,$v)=each($weeks_array)) {
		if ($k!=0) {
			if ($k==$weeks_array[0]) {
				$weeks_url.="<font color='#ff0000'>".$k."</font>, ";
			} else 
				$weeks_url.="<a href={$_SERVER['SCRIPT_NAME']}?sel_week=$k&year_seme=$year_seme&stud_id=$One>".$k."</a>, ";
		}
	}
	$weeks_url=substr($weeks_url,0,-2);
	$sw1=$weeks_array[0];
	$sw2=$sw1+1;
	$last_str=($sw2<count($weeks_array))?"and reward_date<'$weeks_array[$sw2]'":"";

	//顯示資料
	$reward_year_seme=$sel_year.$sel_seme;
	$reward_data="";
	$query="select * from reward where reward_year_seme='$reward_year_seme' and reward_date>='$weeks_array[$sw1]' $last_str and stud_id='' order by reward_date,reward_kind";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$reward_id=$res->fields[reward_id];
		$deps=$reward_id;
		$reward_kind=$res->fields[reward_kind];
		$oo_path=($reward_kind>0)?"good":"bad";
		$query_more="select count(reward_id) from reward where dep_id='$reward_id'";
		$res_more=$CONN->Execute($query_more);
		$dep=$res_more->fields[0];
		$url_more="<a href='$_SERVER[SCRIPT_NAME]?sel_year=$sel_year&sel_seme=$sel_seme&sel_week=$sel_week";
		$stud_table="";
		if ($sel_dep==$reward_id) {
			$url_more.="'><img src='images/tree_collapse.gif' border='0'></a>";
			$stud_table="";
			if ($res_more>0 || $act=="adddata") {
				if ($act=="adddata") {
					$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,false);
					$class_select2=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,true);
					$stud_select=(empty($class_id))?"":get_stud_select($class_id,$One,"stud_id","",true);
					$stud_select=addslashes($stud_select);
					$hidden_str="<input type='hidden' name='sel_dep' value='$sel_dep'><input type='hidden' name='sel_week' value='$sel_week'><input type='hidden' name='act' value='adddata'><input type='hidden' name='add' value='one'>";
					if ($class_id) $submit_str="<input type='submit' value='確定新增'>";
					$add_table="
						<tr bgcolor='#E8F9C8'><td colspan='2' align='center'>增一班<td align='right' colspan='4'>請選班級<td colspan='10' bgcolor='white'>$class_select <input type='submit' name='addclass' value='確定新增'><input type='hidden' name='stud_id' value='all'><input type='hidden' name='year_seme' value='$year_seme'><input type='hidden' name='sel_week' value='$sel_week'><input type='hidden' name='sel_dep' value='$reward_id'></tr>
						</form>
						<form action='$_SERVER[SCRIPT_NAME]' method='post'>
						<tr bgcolor='#E8F9C8'><td colspan='2' rowspan='3' align='center'>增一人<td align='right' colspan='4'>請選擇班級和姓名<td colspan='10' bgcolor='white'>$class_select2 $stud_select $hidden_str $submit_str<input type='hidden' name='past_class_id' value='$class_id'></tr>
						</form>
						<form action='$_SERVER[SCRIPT_NAME]' method='post'>
						<tr bgcolor='#E8F9C8'><td align='right' colspan='4'>或直接輸入學號<td colspan='10' bgcolor='white'><input type='text' size='10' maxsize='10' name='One' value='$One'>$hidden_str</tr>
						</form>
						<form action='$_SERVER[SCRIPT_NAME]' method='post'>
						<tr bgcolor='#E8F9C8'><td align='right' colspan='4'>或直接輸入班級座號<td colspan='10' bgcolor='white'><input type='text' size='2' maxsize='2' name='year_name' value='$year_name'> 年級 <input type='text' size='2' maxsize='2' name='class_name' value='$class_name'> 班 <input type='text' size='2' maxsize='2' name='class_num' value='$class_num'> 號 <input type='submit' value='確定新增'></tr>
						$hidden_str
						</form>";
				}
				$stud_table="
						<tr><td colspan='7' bgcolor='#FBF8B9'><table cellspacing='0' cellpadding='0'0class='small'>
						<tr><td valign='top'>
						<table cellspacing='1' cellpadding='3' bgcolor='#9ebcdd' class='small'>
						<form action='$_SERVER[SCRIPT_NAME]' method='post' name='myform1'>
						$add_table
						<tr bgcolor='#E8F9C8'><td>學號<td>班級<td>座號<td>姓名<td>選項<td>學號<td>班級<td>座號<td>姓名<td>選項<td>學號<td>班級<td>座號<td>姓名<td>選項</tr>";
				$table_num=0;
				$query_more="select a.stud_id,a.reward_id,b.student_sn,b.seme_class,b.seme_num from reward a left join stud_seme b on b.seme_year_seme='$year_seme' and a.student_sn=b.student_sn where a.dep_id='$reward_id' order by b.seme_class,b.seme_num";
				$res_more=$CONN->Execute($query_more);
				while (!$res_more->EOF) {
					$table_num=$table_num % 3 + 1;
					$stud_id=$res_more->fields[stud_id];
					$reward_id=$res_more->fields[reward_id];
					$student_sn=$res_more->fields[student_sn];
					$seme_class=$res_more->fields[seme_class];
					$seme_num=$res_more->fields[seme_num];
					$query_stud="select stud_name from stud_base where student_sn='$student_sn'";
					$res_stud=$CONN->Execute($query_stud);
					$stud_name=addslashes($res_stud->fields[stud_name]);
					if ($table_num==1) $stud_table.="<tr bgcolor='#E8F9C8'>";
					$stud_table.="<td>$stud_id<td>".$c_name[$seme_class]."<td>$seme_num<td>$stud_name<td><a href='$_SERVER[SCRIPT_NAME]?act=del&sel_year=$sel_year&sel_seme=$sel_seme&sel_week=$sel_week&reward_id=$reward_id&sel_dep=$sel_dep' onClick=\"return confirm('確定刪除".$stud_name."的這一筆記錄?')\"><img src='images/del.png' border='0'></a>";
					if ($table_num==3) $stud_table.="</tr>\n";
					$res_more->MoveNext();
				}
				if ($table_num!=0) {
					for ($i=$table_num;$i<3;$i++) $stud_table.="<td><td><td><td><td>";
					if ($table_num<3) $stud_table.="</tr>\n";
				}
				$stud_table.="</form></table></tr></table></tr>";
			}
			$rows=2;
		} else {
			$url_more.="&sel_dep=$reward_id'><img src='images/tree_expand.gif' border='0'></a>";
			$rows=1;
		}
		$mr=($dep>0)?$url_more:"";
		$bgcolor=($reward_kind>0)?"#FFE6D9":"#E6F2FF";
		if ($reward_id==$id) $bgcolor="#FFFF00";
		$url_str="$_SERVER[SCRIPT_NAME]?sel_year=$sel_year&sel_seme=$sel_seme&sel_week=$sel_week&reward_id=$deps";
		$reward_data.="
		<tr bgcolor=$bgcolor>
		<td align='center' rowspan='$rows'>$mr
		<td>".addslashes($reward_arr[$reward_kind])."
		<td width='150'>".addslashes($res->fields[reward_reason])."
		<td width='150'>".addslashes($res->fields[reward_base])."
		<td>".DtoCh($res->fields[reward_date])."
		<td>$dep
		<td><a href='$url_str&act=adddata&sel_dep=$deps'><img src='images/add.png' border='0' alt='加入一個學生'></a> <a href='$url_str&act=edit'><img src='images/edit.png' border='0' alt='修改'></a> <a href='$url_str&act=del_group' onClick=\"return confirm('確定刪除此記錄及以下共".$dep."筆記錄?')\"><img src='images/del.png' border='0' alt='刪除'></a>";
		if ($dep>0) $reward_data.=" <a href='reward_rep.php?stud_id=$stud_id&reward_id=$reward_id&oo_path=$oo_path'><img src='images/print.png' border='0' alt='列印通知書'></a>";
		$reward_data.="
		</tr>
		$stud_table
		";
		$res->MoveNext();
	}
	$main="
	<table cellspacing='0' cellpadding='0'0class='small'>
	<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#9ebcdd' class='small'>
		<form action='$_SERVER[SCRIPT_NAME]' method='post' name='myform'>
		<tr class='title_sbody1'>
		<td colspan='7' align='left'>週次&gt;$weeks_url
		</tr>
		<tr class='title_sbody2'>
		<td align='left'>內容</td>
		<td align='left'>獎懲類別</td>
		<td align='left'>獎懲事由</td>
		<td align='left'>獎懲依據</td>
		<td align='left'>獎懲日期</td>
		<td align='left'>人數</td>
		<td align='left'>功能選項</td>
		</tr>
		".stripslashes($reward_data)."
		</table>
	</td><td valign='top'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>";
	$main.="
	</form>
	</td></tr>
	</table>
	";
	return $main;
}

function del_one($sel_year,$sel_seme,$reward_id) {
	global $CONN;

	$query="select stud_id from reward where reward_id='$reward_id'";
	$res=$CONN->Execute($query);
	$One=$res->fields[stud_id];
	$query="delete from reward where reward_id='$reward_id'";
	$CONN->Execute($query);
	cal_rew($sel_year,$sel_seme,$One);
}
?>
