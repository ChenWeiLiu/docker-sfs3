<?php

// $Id: reward_one.php 8052 2014-06-03 23:54:43Z hsiao $

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

//取得編輯選項
$on_reward=$_REQUEST[on_reward];

//取得處理模式
$act=$_REQUEST[act];
$chk_id=$_POST[chk_id];
if ($on_reward && !$_POST[past_on_reward] && $act!="清除內容") $act="確定新增";

//使用快貼
if ($_POST['act_paste']) $act="確定新增";


//取得學生學號
if ($_POST[past_stud_id]!=$_REQUEST[One]) {
	$One=$_REQUEST[One];
	$focus_str="<body OnLoad='document.base_form.One.focus()'>";
	$focus=1;
} elseif ($_POST[past_stud_id]!=$_POST[stud_id])
	$One=$_POST[stud_id];
elseif (!empty($_REQUEST[reward_id])) {
	$query="select stud_id from reward where reward_id='$_REQUEST[reward_id]'";
	$res=$CONN->Execute($query);
	$One=$res->fields[stud_id];
} else {
	//如果班級選單改變
	if ($_REQUEST[class_id]!=$_REQUEST[past_class_id]) {
		$class_id=$_REQUEST[class_id];
		$c=explode("_",$class_id);
		$seme_class=intval($c[2]).$c[3];
		$year_name=intval($c[2]);
		$class_name=intval($c[3]);
		//$class_num="";
		//$One="";
                //修正改變班級選單時不會即時顯示學號問題
                //$class_num="01";
                //修正如果1號轉學後改變選單會發生班級跳掉的問題
                $class_num=$CONN->Execute("select a.seme_num from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and b.student_sn=a.student_sn and b.stud_study_cond='0'")->fields[seme_num];
                $sql="select a.stud_id,b.stud_study_cond from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and a.seme_num='$class_num' and b.student_sn=a.student_sn and b.stud_study_cond='0'";
                $rs=$CONN->Execute($sql);
                $stud_id=$rs->fields[stud_id];
                if (!empty($stud_id)) $One=$stud_id;
                $focus_str="<body OnLoad='document.base_form.One.focus()'>";
                $focus=1;
	} else {
		//取得年級班級座號
		$year_name=intval($_POST[year_name]);
		$class_name=$_POST[class_name];
		$class_num=$_POST[class_num];

		//處理國中正確年級
		if ($year_name) $year_name+=$IS_JHORES;

		//如果班級選單沒改變，但輸入年級、班級、座號
		if ($year_name && $class_name && $class_num) {
			$seme_class=$year_name.sprintf("%02d",$class_name);
			$seme_num=sprintf("%02d",$class_num);
			$sql="select a.stud_id,b.stud_study_cond from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and a.seme_num='$seme_num' and b.student_sn=a.student_sn and b.stud_study_cond='0'";
			$rs=$CONN->Execute($sql);
			$stud_id=$rs->fields[stud_id];
			if (!empty($stud_id)) $One=$stud_id;
			$focus_str="<body OnLoad='document.base_form.year_name.focus()'>";
			$focus=2;
		}
	}
}

//取得週次
$weeks_array=get_week_arr($sel_year,$sel_seme,$_POST[temp_reward_date]);
$sel_week=$_REQUEST[sel_week];
if ($sel_week) $weeks_array[0]=$sel_week;
if ($weeks_array[0]=="") $weeks_array[0]=1;
$sel_week=$weeks_array[0];

//如果沒有學號也沒有班級
if (empty($One) && empty($class_id)) {
	$sql="select stud_id from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class,seme_num";
	$rs=$CONN->Execute($sql);
	$One=$rs->fields[stud_id];
}

//如果有學號
if ($One) {
	$sql="select student_sn,seme_class,seme_num from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$One'";
	$rs=$CONN->Execute($sql);
	$student_sn=$rs->fields[student_sn];
	if (!$student_sn) 
		$One="";
	else {
		$seme_class=$rs->fields[seme_class];
		$year_name=intval(substr($seme_class,0,-2));
		$class_name=intval(substr($seme_class,-2,2));
		$class_num=intval($rs->fields[seme_num]);
	}
}

//若不為清除模式則取得內容 
if ($act!="清除內容" && ($act=="確定新增" || $act=="確定修改" || $on_reward)) {
	//取得獎懲內容
	$reward_kind=$_POST[reward_kind];
	$reward_reason=$_POST[reward_reason];
	$reward_base=$_POST[reward_base];
	$reward_date=$_POST[temp_reward_date];
	$reward_bonus=$_POST[reward_bonus];
}

//執行動作判斷
if($act=="edit"){
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id],$One,$_REQUEST[reward_id]);
}elseif($act=="確定新增"){
	if ($_POST['act_paste']) {
	
	 //快貼方式
	 /* 導入的變數
    $reward_kind => 4  獎例種類
    $year_seme => 1021  學年學期
    $One => 20001			stud_id
    $reward_reason =>  事由
    $reward_base =>    依據
    $reward_date] =>   日期 轉成西元
 
	 */
	$data_array=explode("\n",$_POST['data_array']);
	//記錄已存入幾筆資料
	$Insert_rec=0; 
	foreach ($data_array as $OneData) {
	 if (trim($OneData)=="") continue;
	 $O=explode("\t",$OneData);
	 foreach ($O as $k=>$v) {
	  $ONE[$k]=trim($v);
	 }
	 $seme_year_seme=$reward_year_seme=$ONE[0];
	 $sel_year=substr($seme_year_seme,0,3);
	 $sel_seme=substr($seme_year_seme,-1);
	 //年級別,若為國中，輸入 1,2,3 自動加 6
		if($IS_JHORES>=6) {
	 	 $ONE[1]=($ONE[1]>6)?$ONE[1]:$ONE[1]+6;
		}
  //班級
		$seme_class=sprintf("%1d%02d",$ONE[1],$ONE[2]);
	//座號
		$seme_num=$ONE[3];
	 //取得學生學號及student_sn
	 $query="select student_sn,stud_id from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' and seme_num='$seme_num'";
	 $res=$CONN->Execute($query) or die($query);
	 $student_sn=$res->fields['student_sn'];
	 $One=$res->fields['stud_id'];
	 
	 //獎懲內容
	 $reward_reason=$ONE[5];
	 //獎懲種類
	 $reward_kind=0;
	 foreach ($reward_arr as $k=>$v) {
	   if ($v==stripslashes($ONE[6])) { $reward_kind=$k; break; }
	 }
	 
	 //獎懲依據
	 $reward_base=$ONE[7];
	 //日期
		$dd=explode("-",$ONE[8]);
		if ($dd[0]<1911) $dd[0]+=1911;  //轉換成西元
		$reward_date=implode("-",$dd);
   //開始	
   if (!empty($One) && !empty($reward_kind) && !empty($reward_reason) && !empty($reward_base)){
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=date("Y-m-j");
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn,reward_bonus) values ('$reward_div','$One','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn','$_POST[reward_bonus]')";
		//echo $query."<br>";
		
		$res=$CONN->Execute($query);
		$dep_id=$CONN->Insert_ID();
		$query="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
		$CONN->Execute($query);
		cal_rew($sel_year,$sel_seme,$One);
    $Insert_rec++;
   }
	 
	} // end foreach
   $INFO="已利用整批快貼方式, 建立".$Insert_rec."筆資料, 請務必校對資料正確性!";
	} else {
	 //個人獎懲模式
	 if (!empty($One) && !empty($reward_kind) && !empty($reward_reason) && !empty($reward_base)){
		$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
		$reward_year_seme=$sel_year.$sel_seme;
		$query="select student_sn from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$One'";
		$res=$CONN->Execute($sql);
		$student_sn=$res->fields[student_sn];
		$reward_div=($reward_kind>0)?"1":"2";
		$reward_sub=1;
		$reward_c_date=date("Y-m-j");
		$reward_ip=getip();
		$query="insert into reward (reward_div,stud_id,reward_kind,reward_year_seme,reward_date,reward_reason,reward_c_date,reward_base,reward_cancel_date,update_id,update_ip,reward_sub,dep_id,student_sn,reward_bonus) values ('$reward_div','$One','$reward_kind','$reward_year_seme','$reward_date','$reward_reason','$reward_c_date','$reward_base','0000-00-00','$_SESSION[session_log_id]','$reward_ip','$reward_sub','0','$student_sn','$_POST[reward_bonus]')";
		$res=$CONN->Execute($query);
		$dep_id=$CONN->Insert_ID();
		$query="update reward set dep_id='$dep_id' where reward_id='$dep_id'";
		$CONN->Execute($query);
		cal_rew($sel_year,$sel_seme,$One);
	 }
  }
	
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id],$One,$dep_id);
}elseif($act=="確定修改"){
	$reward_id=$_POST[reward_id];
  //檢查是否資料有更動, 若有更動, 不再歸屬於團體獎懲, 把 dep_id 改為自己的 reward_id
	 $dep_id="";
	 $query="select reward_kind from reward where reward_id='$reward_id'";
	 $res=$CONN->Execute($query);
	 list($reward_kind1)=$res->fetchRow();
   if ($reward_kind!=$reward_kind1) {
     $dep_id=",dep_id='".$reward_id."'";
   }
	$reward_div=($reward_kind>0)?"1":"2";
	$query="update reward set reward_div='$reward_div',reward_kind='$reward_kind',reward_reason='$reward_reason',reward_base='$reward_base',reward_date='$reward_date',reward_bonus='$reward_bonus'".$dep_id." where reward_id='$reward_id'";
	$CONN->Execute($query);
	cal_rew($sel_year,$sel_seme,$One);
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id],$One,$reward_id);
}elseif($act=="del"){
	del_one($sel_year,$sel_seme,$_REQUEST[reward_id]);
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id],$One,"");
}else{
	$main=&mainForm($sel_year,$sel_seme,$_REQUEST[class_id],$One,"");
}


//秀出網頁
head("個人獎懲登記");
if ($on_reward) echo $focus_str;
echo $main;
foot();
?>

<Script>
	$("#go_paste_form").click(function(){
	 paste_form.style.display='block';	
	 reward_form.style.display='none';		
	})
	
	$("#go_reward_form").click(function(){
	 paste_form.style.display='none';	
	 reward_form.style.display='block';		
	})

</Script>


<?php
//主要輸入畫面
function &mainForm($sel_year,$sel_seme,$class_id="",$One="",$reward_id=""){
	global $student_menu_p,$CONN,$year_name,$class_name,$class_num,$reward_arr,$sel_week,$on_reward,$reward_kind,$reward_reason,$reward_base,$reward_date,$focus,$IS_JHORES;
	global $INFO;
	//相關功能表
	$tool_bar=&make_menu($student_menu_p);

	//是否為修改模式
	if ($reward_id) {
		$query="select * from reward where reward_id='$reward_id'";
		$res=$CONN->Execute($query);
		$One=$res->fields[stud_id];
		$reward_kind=$res->fields[reward_kind];
		$reward_reason=$res->fields[reward_reason];
		$reward_base=$res->fields[reward_base];
		$reward_date=$res->fields[reward_date];
		$reward_bonus=$res->fields[reward_bonus];
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

	//取出正確班級代碼
	if (!empty($One)) {
		$sql="select seme_class from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='$One'";
		$rs=$CONN->Execute($sql);
		$seme_class=$rs->fields[seme_class];
		$year_name=intval(substr($seme_class,0,-2));
		$class_name=intval(substr($seme_class,-2,2));
		$class_id=sprintf("%03d_%d_%02d_%02d",$sel_year,$sel_seme,$year_name,$class_name);
	}
	$year_name-=$IS_JHORES;

	//取得該班及學生名單，以及填寫表格
	$signForm=&signForm($sel_year,$sel_seme,$class_id,$One,$reward_id);

	//年級與班級選單
	$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,true);
	$stud_select=get_stud_select($class_id,$One,"stud_id","this.form.submit",1);

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
	
	//免試入學積分採計
	$reward_bonus=$reward_bonus==='0'?0:1;
	$bonus_input="<input type='radio' name='reward_bonus' value='1' ".($reward_bonus?'checked':'').">是 <input type='radio' name='reward_bonus' value='0' ".($reward_bonus?'':'checked').">否 ";

	//編輯選項狀況
	$chk_r=($on_reward)?"checked":"";
	if ($on_reward)
		if ($focus==1)
			$One="";
		elseif ($focus==2) {
			$year_name="";
			$class_name="";
			$class_num="";
		}

	$main="
	$tool_bar";
	if ($INFO!="") {
	$main.="<font color=red>$INFO</font>";
	}
	$main.="
	<table border='0'>
	<!-- 個人獎懲表單 -->
	<form action='$_SERVER[SCRIPT_NAME]' name='base_form' method='post'>
	<tr id='reward_form'>
	<td>
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<tr class='title_sbody2'>
	<td>請選學年度<td align='left' bgcolor='white' colspan='2'>$year_seme_select<input type='hidden' name='reward_kind' value='$reward_kind'><input type='hidden' name='reward_reason' value='$reward_reason'><input type='hidden' name='reward_base' value='$reward_base'><input type='hidden' name='temp_reward_date' value='$reward_date'>
	</tr>
	<tr class='title_sbody2'>
	<td>請選班級和姓名<td align='left' bgcolor='white' colspan='2'>$class_select $stud_select<input type='hidden' name='past_class_id' value='$class_id'>
	</tr>
	<tr class='title_sbody2'>
	<td>或直接輸入學號<td align='left' bgcolor='white' colspan='2'><input type='text' size='10' maxsize='10' name='One' value='$One'><input type='hidden' name='past_stud_id' value='$One'>
	</tr>
	<tr class='title_sbody2'>
	<td>或直接輸入班級座號<td align='left' bgcolor='white' colspan='2'><input type='text' size='2' maxsize='2' name='year_name' value='$year_name'> 年級 <input type='text' size='2' maxsize='2' name='class_name' value='$class_name'> 班 <input type='text' size='2' maxsize='2' name='class_num' value='$class_num'> 號 <input type='submit' value='確定'>
	</tr>";
	if (!empty($reward_kind) || !empty($reward_reason) || !empty($reward_base))
		$main.="<tr class='title_sbody2'><td>編輯選項<td align='left' bgcolor='white' colspan='2'><input type='checkbox' name='on_reward' $chk_r onClick='this.form.submit()'>連續輸入獎懲資料<input type='hidden' name='past_on_reward' value='".(!$on_reward)."'></tr>";
	$main.="
	</form>
	<form action='$_SERVER[SCRIPT_NAME]' method='post' name='reward_one_form'>
	<tr class='title_sbody2'>
	<td>獎懲類別<td align='left' bgcolor='white'>$reward_select<td align='left' bgcolor='white' rowspan='5' valign='bottom'>
	<input type='submit' name='act' value='$submit_msg'><br>
	<input type='submit' name='act' value='清除內容'><br>
	<input type='button' name='past_form' id='go_paste_form' value='整批快貼'>
	<input type='hidden' name='reward_id' value='$reward_id'>
	<input type='hidden' name='year_seme' value='$seme_year_seme'>
	<input type='hidden' name='One' value='$One'>
	<input type='hidden' name='on_reward' value='$on_reward'>
	</tr>
	<tr class='title_sbody2'>
	<td>獎懲事由<td align='left' bgcolor='white'><input type='text' name='reward_reason' value='$reward_reason' size='50' >
	</tr>
	<tr class='title_sbody2'>
	<td>獎懲依據<td align='left' bgcolor='white'><input type='text' name='reward_base' value='$reward_base' size='50' >
	</tr>
	<tr class='title_sbody2'>
	<td>獎懲日期<td align='left' bgcolor='white'>$date_input
	</tr>
	<tr class='title_sbody2'>
	<td>免試入學積分採計<td align='left' bgcolor='white'>$bonus_input
	</tr>
	</table>
	</td>
	</tr>
	<tr id='paste_form' style='display:none'>
	<td>
  <!-- 快貼表單 -->	
	<table cellspacing='1' cellpadding='3' bgcolor='#C6D7F2'>
	<input type='hidden' name='act_paste' value=''>
		<tr class='title_sbody2'>	
			<td align='left'>整批快貼：請依<a href='images/paste_demo.xls'>範例檔</a>貼上資料.</td>
			<td align='right'>
				<input type='button' value='送出資料' onclick='document.reward_one_form.act_paste.value=1;document.reward_one_form.submit()'>
				<input type='button' value='回個人登記' id='go_reward_form'>
			</td>
		</tr>
		<tr class='title_sbody2'>	
			<td colspan='2'><textarea name='data_array' rows='8' cols='80'></textarea></td>
		</tr>
		<tr class='title_sbody2'>
		<td colspan='2' align='left'><font color=red>1.利用快貼, 可快速而大量的建立學生獎懲紀錄.<br>2.注意! 大量貼上時, 處理時間較長, 請耐心等候。</font></td>
		</tr>
	</table>
	</tr>
	</form>
  </table>	
	$signForm
	";
	return $main;
}

//取得該班及學生名單，以及填寫表格
function &signForm($sel_year,$sel_seme,$class_id,$One="",$id=""){
	global $CONN,$weekN,$weeks_array,$reward_arr,$sel_week,$class_year;
	
	//取得學生陣列
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$all_sn="";
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' order by seme_class,seme_num";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$stud_id=$res->fields[stud_id];
		$student_sn=$res->fields[student_sn];
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
		$class_id=$res->fields[class_id];
		$c=explode("_",$class_id);
		$c_year=intval($c[2]);
		$class_name[$c_year.$c[3]]=$class_year[$c_year].$res->fields[c_name]."班";
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
        $last_str=($sw2<count($weeks_array))?"and a.reward_date<'$weeks_array[$sw2]'":"";

	//顯示資料
	$reward_year_seme=$sel_year.$sel_seme;
	$reward_data="";
	$query="select a.* from reward a inner join stud_seme b on a.student_sn=b.student_sn and b.seme_year_seme='$seme_year_seme' where a.reward_year_seme='$reward_year_seme' and a.reward_date>='$weeks_array[$sw1]' $last_str and dep_id=reward_id order by b.seme_class,b.seme_num";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$reward_id=$res->fields[reward_id];
		$reward_kind=$res->fields[reward_kind];
		$bgcolor=($reward_kind>0)?"#FFE6D9":"#E6F2FF";
		$bgcolor=$res->fields[reward_bonus]?$bgcolor:'#dddddd';
		$reward_bonus=$res->fields[reward_bonus]?"<img src='images/ok.png'>":"";		
		if ($reward_id==$id) $bgcolor="#FFFF00";
		$stud_id=$res->fields[stud_id];
		$cancel_date=$res->fields[reward_cancel_date];
		if ($reward_kind>0) {
			$cancel_date="-----";
			$oo_path="good";
		} else {
			if ($cancel_date=="0000-00-00")
				$cancel_date="未銷過";
			else
				$cancel_date=DtoCh($cancel_date);
			$oo_path="bad";
		}
		$url_str="$_SERVER[SCRIPT_NAME]?sel_year=$sel_year&sel_seme=$sel_seme&sel_week=$sel_week&reward_id=$reward_id";
		$chked=($chk_id[$reward_id])?"checked":"";
		$reward_data.="
		<tr bgcolor=$bgcolor>
		<td><input type='checkbox' name='chk_id[".$reward_id."] $chked'>
		<td>$stud_id
		<td>".$stud_name[$stud_id]."
		<td>".$class_name[$seme_class[$stud_id]]."
		<td>".$seme_num[$stud_id]."
		<td>".addslashes($reward_arr[$reward_kind])."
		<td width='150'>".addslashes($res->fields[reward_reason])."
		<td width='150'>".addslashes($res->fields[reward_base])."
		<td>".DtoCh($res->fields[reward_date])."
		<td>$cancel_date
		<td align='center'>$reward_bonus
		<td><a href=$url_str&act=edit><img src='images/edit.png' border='0' alt='修改'></a> <a href=$url_str&act=del onClick=\"return confirm('確定刪除".$stud_name[$stud_id]."的這一筆記錄?')\"><img src='images/del.png' border='0' alt='刪除' ></a> <a href=reward_rep.php?stud_id=$stud_id&reward_id=$reward_id&oo_path=$oo_path><img src='images/print.png' border='0' alt='列印通知書'></a></tr>";
		$res->MoveNext();
	}
	$main="
	<table cellspacing='0' cellpadding='0'0class='small'>
	<tr><td valign='top'>
		<table cellspacing='1' cellpadding='3' bgcolor='#9ebcdd' class='small'>
		<form action='$_SERVER[SCRIPT_NAME]' method='post' name='myform'>
		<tr class='title_sbody1'>
		<td colspan='12' align='left'>週次&gt;$weeks_url
		</tr>
		<tr class='title_sbody2'>
		<td align='left'>選取</td>
		<td align='left'>學號</td>
		<td align='left'>姓名</td>
		<td align='left'>班級</td>
		<td align='left'>座號</td>
		<td align='left'>獎懲類別</td>
		<td align='left'>獎懲事由</td>
		<td align='left'>獎懲依據</td>
		<td align='left'>獎懲日期</td>
		<td align='left'>銷過日期</td>
		<td align='left'>積分採計</td>
		<td align='left'>功能選項</td>
		</tr>
		".stripslashes($reward_data)."
		</table>
	</td><td valign='top'>
	<input type='hidden' name='sel_year' value='$sel_year'>
	<input type='hidden' name='sel_seme' value='$sel_seme'>
	<input type='hidden' name='class_id' value='$class_id'>";
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

<script language="JavaScript">
function checkok()
{
	var OK=true;
	if(document.myform.id.value=='')
	{
		if(document.myform.stud_class.value==0)
		{	alert('未選擇班級');
			OK=false;
		}	
		if(document.myform.stud_id.value=='')
		{	alert('未選擇學生');
			OK=false;
		}	
	}
	if(document.myform.reward_kind.value=='')
	{	alert('未選擇類別');
		OK=false;
	}	
	if(document.myform.reward_reason.value=='')
	{	alert('未填事由');
		OK=false;
	}	
	if(document.myform.reward_base.value=='')
	{	alert('未填依據');
		OK=false;
	}
	if (OK == true){
		OK=date_check();
	   }
	return OK;
}

function setfocus(element) {
	element.focus();
return;
}
//-->
</script>
