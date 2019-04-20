<?php

// $Id: new_function.php 7856 2014-01-13 07:34:06Z brucelyc $

// logo 圖
function print_logo_image($image,$title="") {
	global $SFS_PATH_HTML,$SFS_PATH;
	return "<img alt=\"$title\" src=\"$SFS_PATH_HTML".get_path($_SERVER['SCRIPT_FILENAME'])."/images/$image\">";
}

/***
印出模組
$module[$i][msn] 程式代號
$module[$i][showname] 程式名稱
$module[$i][isopen] 是否為不需認證程式 (1 代表示是)
***/

//$col_num=>圖示分為幾欄
function print_module($msn="",$index=0,$col_num=4) {
	global $SFS_PATH_HTML,$nocols,$SFS_PATH,$THEME_URL,$FOLDER,$THEME_COLOR;
	
	//取得學校授權 session ,hami 2003-3-25
	$session_prob = get_session_prot();

	//若是目前在第一層，則不要秀出左邊圖示選單，若是大於第一層，則秀出左邊圖示選單
	if(!empty($msn) or empty($_SESSION['session_tea_name'])){
		
		//如果不是在首頁或是未登入，取得該分類底下所有[啟動]模組 array
		$module = get_module($msn);

		//秀出首頁訊息
		if ($index)	include "$SFS_PATH/include/open_all.php";
		
		//表格寬度
		$tw=$col_num*50;
		
		$main="<table width='$tw' align='center' border='0' cellpadding='10' cellspacing='0' class='small'>";
		
		$a=$col_num;

		//$m為底下每個一模組，是陣列
		foreach($module as $m){
			//模組編號
			$pro_kind_id=$m[msn];
			//模組中文名稱
			$pro_kind_name=$m['showname'];
			//模組目錄名稱
			$pro_dir_name=$m['dirname'];
			//模組圖檔
			$icon=($THEME_COLOR)?$THEME_COLOR."_new_icon.png":"new_icon.png";
			//資料庫是否有圖檔紀錄
			$icon=(empty($m['icon_image']))?$icon:$m['icon_image'];
			//是模組還是分類
			$kind=$m['kind'];
			//首頁檔名
			$home_index="";
			//模組路徑
			$store_path="$SFS_PATH/modules";
			//是否開放
			$pro_isopen=$m['isopen'];
		
			$tr=(($a%$col_num)==0)?"<tr>":"";
			$tr2=(($a%$col_num)==$col_num-1)?"</tr>\n":"";
			$pic=(empty($pro_dir_name))?$SFS_PATH_HTML."images/$icon":$SFS_PATH_HTML."modules/$pro_dir_name/images/$icon";
			$real_pic=(empty($pro_dir_name))?$SFS_PATH."/images/$icon":$SFS_PATH."/modules/$pro_dir_name/images/$icon";

			$show_pic=(is_file($real_pic) and file_exists($real_pic))?$pic:$THEME_URL."/images/no_icon.gif";

			$show_pic=($m['kind']=="分類")?$THEME_URL."/images/".$FOLDER:$show_pic;
			$url=($kind=="模組")?$SFS_PATH_HTML."modules/$pro_dir_name/":"$_SERVER[SCRIPT_NAME]?_Msn=$pro_kind_id";
			
			//假設有連結、圖示
			if(!is_null($_SESSION[$session_prob][$pro_kind_id]) || $pro_isopen=='1') {
				if ($home_index=="none")$home_index="";
				$main.="
				$tr
				<td align='center' valign='top' nowrap>
				<a href=\"$url\">
				<img src=\"$show_pic\" border=0 alt=\"$pro_kind_name\">
				<br>$pro_kind_name</a></td>
				$tr2";
				$a++;
			}

		}
		$main.="</table></center>\n";
	}else{
		$main=&my_web();
	}
	echo $main;
}

//取得左邊模組連結
function &get_big_module($col_num=4,$mode="") {
	global $SFS_PATH_HTML,$nocols,$SFS_PATH, $CONN,$THEME_URL,$THEME_URL,$FOLDER,$FOLDER_OPEN;
	
	//取得學校授權 session ,hami 2003-3-25
	$session_prob = get_session_prot();
	
	//若是目前在第一層，則不要秀出左邊圖示選單
	if(empty($_SESSION['session_tea_name']))return;
	
	$close_pic="<img src='".$THEME_URL."/images/close.png' width=16 height=16 border=0>";

	$arr = array();
	
	//是小選單或左邊大選單
	if($mode=="small"){
		$main="<form name='p' method='post'>
		<td align='right' nowrap>
		主分類選單：<select name='bm' class='small'  onChange=\"if(document.p.bm.value!='')change_link(document.p.bm.value)\">";
	}else{
		$main="<td valign='top' align='right' nowrap width='100'  bgcolor='#F7F7F7' >
		<a href='".$THEME_URL."/chang_mode.php?cmk=close_left_menu&v=1'>$close_pic</a>
		<table border='0' cellpadding='2' cellspacing='0' align='center'>
		";
	}
	
	$query = "select msn,showname,isopen,kind,icon_image from sfs_module where islive='1' and kind='分類' and of_group='0' order by sort";
	$result = $CONN->Execute($query);
	$i =0 ;
	$home_index="index.php";
	while (list($pro_kind_id,$pro_kind_name,$pro_isopen,$kind,$icon) = $result->FetchRow()){
		//小選單
		if($mode=="small"){
			if(!is_null($_SESSION[$session_prob][$pro_kind_id]) || $pro_isopen) {
				if ($home_index=="none")	$home_index="";
				$selected=($_SERVER[REQUEST_URI]=="/index.php?_Msn=$pro_kind_id")?"selected":"";
				$main.="
				<option value='".$SFS_PATH_HTML."index.php?_Msn=$pro_kind_id' $selected>$pro_kind_name</option>
				";
			}		
		}else{
		//大選單
			$pic=(empty($pro_dir_name))?
			$SFS_PATH_HTML."images/$icon":
			$SFS_PATH_HTML."modules/$pro_dir_name/images/$icon";
		
			$real_pic=(empty($pro_dir_name))?$SFS_PATH."/images/$icon":$SFS_PATH."/modules/$pro_dir_name/images/$icon";

			$show_pic=(is_file($real_pic) and file_exists($real_pic))?$pic:$THEME_URL."/images/no_icon.png";
			$folder_pic=($pro_kind_id==$_GET[_Msn])?$FOLDER_OPEN:$FOLDER;
			$show_pic=($kind=="分類")?$THEME_URL."/images/$folder_pic":$show_pic;

			if(!is_null($_SESSION[$session_prob][$pro_kind_id]) || $pro_isopen) {
				if ($home_index=="none")	$home_index="";
				$main.="
				<tr>
				<td align='center' valign='top' nowrap class='small'>
				<a href=\"".$SFS_PATH_HTML."index.php?_Msn=$pro_kind_id\">
				<img src=\"$show_pic\" border=0><br>$pro_kind_name</a></td>
				</tr>
				<tr>
				<td height=10></td>
				</tr>";
			}
			$a++;
		}
	}
	
	//額外模組
	$main.=($mode=="small")?"<option value='".$SFS_PATH_HTML."index.php?_Msn=other'>額外模組</option>":"
	<tr>
	<td align='center' valign='top' nowrap class='small'>
	<a href=\"".$SFS_PATH_HTML."index.php?_Msn=other\">	
	<img src=\"".$THEME_URL."/images/".(($FOLDER=="fc.gif")?"frc.png":"folder_red.png")."\" width=48 height=48 border=0><br>額外模組</a></td>
	</tr>
	<tr>
	<td height=10></td>
	</tr>";
	
	$main.=($mode=="small")?"</select></td></form><td valign='center' nowrap><a href='".$THEME_URL."/chang_mode.php?cmk=close_left_menu&v=0'>$close_pic</a></td>":"</table></td>\n";
	return $main;
}


//印出抬頭連結
function &print_location() {
	global $SFS_PATH_HTML,$SFS_THEME,$CDCLOGIN,$HTTPS;
	//取得連結
	
	if ($_SESSION['session_log_id']){
		$b=$_SESSION['session_tea_name'] . "登入｜<a href='".$SFS_PATH_HTML."login.php?logout=yes'><img src='".$SFS_PATH_HTML."themes/$SFS_THEME/images/exit.png' alt='' width='16' height='16' hspace='3' border='0' align='absmiddle'>登出</a>";
	}else{
		if ($HTTPS=="") $LOGINURL=$SFS_PATH_HTML;
		else $LOGINURL=$HTTPS;
		$b=($CDCLOGIN)?"<a href=\"$SFS_PATH_HTML"."login.php?cdc=1\">憑證登入</a> &nbsp; | &nbsp; <a href=\"$SFS_PATH_HTML"."login.php\">一般登入</a>":"<a href=\"$LOGINURL"."login.php\">登入系統</a>";
	}

	$main[]=get_sfs_path($_REQUEST["_Msn"]);
	$main[]=$b;
	return $main;
}

//印出選單 menu
function print_menu($menu,$link="",$page=0) {
	$main=&make_menu($menu,$link,$page);
	echo $main;
}

//印出選單 menu
function &make_menu($menu,$link="",$page=0) {
	global $SFS_PATH_HTML,$SFS_THEME;

	if ($link !=""){
		$link ="?".$link;
	}
	$the_script  = substr (strrchr ($_SERVER[SCRIPT_NAME], "/"), 1);
	$button="";
	while (list($tid,$tname) = each($menu)) {
		if ($tid == $the_script ) {
			$button.="<td class='tab' bgcolor='#FFF158'>&nbsp;<a href=\"$tid"."$link\">$tname</a>&nbsp;</td>";
		}else{
			$button.="<td class='tab' bgcolor='#EFEFEF'>&nbsp;<a href=\"$tid"."$link\">$tname</a>&nbsp;</td>";
		}
	}
	$main="
	<table cellspacing=1 cellpadding=3><tr>
	$button
	</tr></table>
	";
	return $main;
}


//個人化介面
function &my_web(){
	global $SFS_PATH_HTML,$nocols,$SFS_PATH, $CONN;
	include_once $SFS_PATH."/include/sfs_case_signpost.php";

	//檢查系統設定
	$chk_sys_setup=&chk_sys_setup();
	//填報
	$p=get_main_prob("",1,"online_form");
	$sign_form=(!empty($p[msn]))?school_sign_form():"";
	//公告
	$p=($_SESSION['session_who']=="教師")?get_main_prob("",1,"new_board"):"";
	$all_post=(!empty($p[msn]))?showPost():"";
	//顯示上次登入時間
	$today=date("Y-m-d G:i:s",mktime (date("G"),date("i"),date("s"),date("m"),date("d"),date("Y")));
	$tableName = "login_log";
	$teacher_sn = $_SESSION['session_tea_sn'];
	$Create_db="CREATE TABLE if not exists $tableName (
	   log_id int(10) unsigned NOT NULL  auto_increment,
	   teacher_sn smallint(6) unsigned NOT NULL  ,
	   login_time datetime NOT NULL default '0000-00-00 00:00:00' ,
	   PRIMARY KEY  (log_id))";
	mysql_query($Create_db);
	$result = mysql_query("select login_time from $tableName where teacher_sn = $teacher_sn");
	$recordSet = mysql_fetch_row($result);
	if ($recordSet != NULL) {
	list($login_time) = $recordSet;
	$result = mysql_query("update $tableName set login_time = '$today' where teacher_sn = $teacher_sn");
	$recordSet = mysql_fetch_row($result);
	} else {
	$login_time = $today;
	$sql_select = "insert into $tableName (teacher_sn,login_time) values ('$teacher_sn','$today')";
	$recordSet = $CONN->Execute($sql_select);
	}

	$main="	
	<p>".$_SESSION['session_tea_name']."您好：歡迎使用學務系統
	";
	if ($login_time==$today) {
	   $main.="</p>您是第一次登入本系統<br><br>";
	} else {
	   $main.="<font color=#000088><small>&nbsp;&nbsp;(上次登入時間：$login_time)</small></font></p>";
	}   
	$main.="	
	<table width='98%' align='center'>
	<tr><td valign='top'>$all_post<br>$sign_form</td>
	<td align='right' valign='top'>$chk_sys_setup</td></tr>
	</table>
	";
	
	return $main;
}


//快速連結選單
function fast_link(){
	global $SFS_PATH_HTML,$THEME_URL,$CONN;
	//取得學校授權 session ,hami 2003-3-25
	$session_prob = get_session_prot();
	
	//取得目前網頁所在的模組目錄名稱
	$SCRIPT_NAME=$_SERVER[SCRIPT_NAME];
	$SN=explode("/",$SCRIPT_NAME);
	$dirname=$SN[count($SN)-2];
	//取出模組編號
    $sql_select = "SELECT msn FROM sfs_module where dirname='$dirname'";
    $recordSet=$CONN->Execute($sql_select) or user_error("SQL語法錯誤： $sql_select",256);
    list($msn)= $recordSet -> FetchRow();
			
	//取得登入後的模組權限
	foreach ($_SESSION[$session_prob] as $pro_kind_id => $of_group) {
		//取得模組資訊
		$prob=get_main_prob($pro_kind_id);
		if(empty($prob['islive']))continue;

		$sort=$prob['sort'];
		$blank=($prob['kind']=="分類")?"◎ ":"　‧";

		//$selected=($_REQUEST[_Msn]==$prob['msn'])?"selected":"";
		
		if($_REQUEST[_Msn]==$prob['msn']){
			$selected="selected";
		}elseif(empty($_REQUEST[_Msn]) and $msn==$prob['msn']){
			$selected="selected";
		}else{
			$selected="";
		}
		$url=($prob['kind']=="分類")?$SFS_PATH_HTML."index.php?_Msn=$prob[msn]":$SFS_PATH_HTML."modules/".$prob['dirname']."/index.php";

		//把個人可用的模組放到陣列中
		$man_p[$of_group][$sort]="<option value='$url' $selected>".$blank.$prob[showname]."</option>\n";

		//主要分類陣列
		if(empty($of_group)){
			$main_prob[$sort]=$pro_kind_id;
		}
	}
	//主要分類排序
	ksort ($main_prob);
	reset ($main_prob);

	//模組分類排序
	ksort ($man_p);
	reset ($man_p);

	foreach ($main_prob as $main_pro_kind_order=>$main_pro_kind_id){
		//主要模組
		$all_power.=$man_p[0][$main_pro_kind_order];
		ksort ($man_p[$main_pro_kind_id]);
		reset ($man_p[$main_pro_kind_id]);
		foreach ($man_p[$main_pro_kind_id] as $order=>$value){
			//底下模組
			$all_power.=$value;
		}
	}

	$all_power="
	<table cellspacing='0' cellpadding='0' class='small'><tr>
	<form name='power' method='post'><td><font color='#FFFFFF'>快速連結：</font></td><td>
	<select name='fast_link' size='1' class='small' onChange=\"if(document.power.fast_link.value!='')change_link(document.power.fast_link.value)\">
	$all_power
	</select></td><td valign='center'><a href='".$THEME_URL."/chang_mode.php?cmk=close_fast_link&v=1'><img src='".$THEME_URL."/images/close.png' width=16 height=16 border=0></a></td>
	</form>
	</tr></table>
	";
	return $all_power;
}


//系統自動檢查
function &chk_sys_setup($sel_year="",$sel_seme=""){
	global $CONN,$SFS_PATH_HTML;

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);
	
	$isroot=who_is_root();
	$id_sn=$_SESSION[session_tea_sn];
	if(empty($isroot[$id_sn][p_id]))return;
	
	
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

	$main="<table bgcolor='#F60A64' cellspacing=1 cellpadding=2>
	<tr><td align='center'><b><font color='#000000' size=2><font color='#FFF158'>$sel_year</font>學年度第<font color='#FFF158'>$sel_seme</font>學期</font><br><font color='white' size=2>系統設定自動檢查</font></b></td></tr>
	<tr bgcolor='#FFFFF'><td class='small'>";

	//檢查學校基本設定
	$sql_select = "SELECT count(*) FROM school_base WHERE sch_cname='校園自由軟體交流網' or  sch_cname_s='校園自由軟體交流網' or   sch_cname_ss='校園自由軟體交流網'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($n)= $recordSet->FetchRow();
	if($n>0){
		$main.="尚未設定：『<a href='".$SFS_PATH_HTML."modules/school_setup/'>學校基本設定</a>』<br>";
	}

	//檢查班級設定
	$sql_select = "SELECT count(*) FROM school_class WHERE year='$sel_year' and semester='$sel_seme' and enable='1'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($n1)= $recordSet->FetchRow();
	$main.=($n1>0)?"<a href='".$SFS_PATH_HTML."modules/every_year_setup/class_year_setup.php?act=view&sel_year=$sel_year&sel_seme=$sel_seme'>全校共 $n1 班</a><br>":"尚未設定：『<a href='".$SFS_PATH_HTML."modules/every_year_setup/class_year_setup.php?act=setup&sel_year=$sel_year&sel_seme=$sel_seme'>班級設定</a>』<br>";

	//檢查考試設定
	$sql_select = "SELECT setup_id FROM score_setup WHERE year='$sel_year' and semester='$sel_seme' and enable='1'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($setup_id)= $recordSet->FetchRow();
	$main.=(!empty($setup_id))?"<a href='".$SFS_PATH_HTML."modules/every_year_setup/score_setup.php'>觀看本學期考試設定</a><br>":"尚未設定：『<a href='".$SFS_PATH_HTML."modules/every_year_setup/score_setup.php?act=setup&sel_year=$sel_year&sel_seme=$sel_seme'>考試設定</a>』<br>";

	//檢查課程設定
	$sql_select = "SELECT count(ss_id) FROM score_ss WHERE year='$sel_year' and semester='$sel_seme' and enable='1'";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	list($n2)= $recordSet->FetchRow();
	$main.=(!empty($n2))?"<a href='".$SFS_PATH_HTML."modules/every_year_setup/ss_setup.php?act=viewall&sel_year=$sel_year&sel_seme=$sel_seme'>觀看各年級課程設定</a><br>":"尚未設定：『<a href='".$SFS_PATH_HTML."modules/every_year_setup/ss_setup.php'>課程設定</a>』<br>";

	//檢查課表設定
	$n3=0;
	$sql_select = "SELECT class_id FROM score_course WHERE year='$sel_year' and semester='$sel_seme' group by class_id";
	$recordSet=$CONN->Execute($sql_select) or trigger_error("錯誤訊息： $sql_select", E_USER_ERROR);
	while(list($nn)= $recordSet->FetchRow()){
		$n3++;
	}


	if(empty($n3)){
		$main.="尚未設定：『<a href='".$SFS_PATH_HTML."modules/every_year_setup/course_setup.php'>課表設定</a>』<br>";
	}elseif($n1!=$n3){
		$main.="<a href='".$SFS_PATH_HTML."modules/every_year_setup/course_setup.php'>課表不完整，僅".$n3."班</a><br>";
	}else{
		$main.="<a href='".$SFS_PATH_HTML."modules/every_year_setup/course_setup.php'>課表設定OK</a><br>";
	}

	$main.="</td></tr></table>";

	return $main;
}
?>
