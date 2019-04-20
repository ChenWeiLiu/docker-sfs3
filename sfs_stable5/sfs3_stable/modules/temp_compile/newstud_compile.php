<?php

// $Id: newstud_compile.php 5310 2009-01-10 07:57:56Z hami $

/*引入學務系統設定檔*/
require "config.php";
if($_GET['offset']) $offset=$_GET['offset'];
elseif ($_POST['offset']) $offset=$_POST['offset'];
else $offset=0;
$rs_limit=$CONN->Execute("SELECT pm_value FROM pro_module WHERE pm_name='temp_compile' AND pm_item='limit'");
$limit=$rs_limit->fields['pm_value'];
$class_year_b=$_REQUEST['class_year_b'];
$work=$_REQUEST['work'];
$class_kind=$_REQUEST['class_kind'];
if (empty($class_kind)) $class_kind="temp_class";
$order_name=$_REQUEST['order_name'];
$new_class_year=$_REQUEST['new_class_year'];

//使用者認證
sfs_check();

//程式檔頭
if(!$_POST['Submit6']) {
	head("新生編班");
	print_menu($menu_p,"class_year_b=$class_year_b");

	//設定主網頁顯示區的背景顏色
	echo "<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc><tr><td bgcolor='#FFFFFF'>";
}

//工作選單
$class_sel[$class_kind]="selected";
if ($class_kind=="temp_class") {
	$class_sort="temp_class";
	$class_site="temp_site";
	$class_str="";
	$c_str="臨時";
} else {
	$class_sort="oth_class";
	$class_site="oth_site";
	$class_str="and sure_oth='1'";
	$c_str="學藝活動";
}
$selected[$work]="selected";
$class_cname=array("temp_class"=>"新生臨時班","oth_class"=>"新生學藝班");

$menu="
	<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
	<select name='class_year_b' onchange='this.form.submit();'>";
	$chk=($class_year_b)?$class_year_b:$IS_JHORES+1;
	while (list($k,$v)=each($class_year)) {
		$checked=($chk==$k)?"selected":"";
		$menu.="<option value='$k' $checked>$v</option>\n";
	}
$menu.="</select>
	<select name='class_kind' onChange='jumpMenu0()'>
	<option value='temp_class' ".$class_sel[temp_class].">".$class_cname[temp_class]."</option>\n
	<option value='oth_class' ".$class_sel[oth_class].">".$class_cname[oth_class]."</option>\n
	</select>
	<select name='work' onChange='jumpMenu0()'>
	<option value=''>請選擇工作項目</option>\n
	<option value='1' ".$selected[1].">設定班別</option>\n
	<option value='2' ".$selected[2].">人工編班</option>\n
	<option value='3' ".$selected[3].">自動編班</option>\n
	<option value='4' ".$selected[4].">編班查詢</option>\n
	<option value='5' ".$selected[5].">查詢未編班名單</option>\n
	<option value='6' ".$selected[6].">列印編班名冊</option>\n
	</select>
	</form>";
if(!$_POST['Submit6'])	echo "<table><tr><td>".$menu."<td>";

//網頁內容請置於此處
if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
$new_sel_year=date("Y")-1911;

//寫入資料
if($_POST['Submit1']=='儲存'){
	$query="select * from $class_kind where year='$new_sel_year' order by class_id";
	$res=$CONN->Execute($query);
	while (!$res->EOF) {
		$class_id=$res->fields[class_id];
		$cclass[$class_id]=$res->fields[c_name];
		if ($cclass[$class_id]=="") $cclass[$class_id]="*";
		$res->MoveNext();
	}
	$class_nk=${"class_name_kind_".$_POST[c_name_kind]};
	$c_num=$_POST[c_num];
	for ($i=1;$i<=$c_num;$i++) {
		$class_id=$class_year_b.sprintf("%02d",$i);
		if (empty($cclass[$class_id]))
			$query="insert into $class_kind (class_id,year,c_name,c_sort) values ('$class_id','$new_sel_year','".$class_nk[$i]."','$i')";
		else
			$query="update $class_kind set c_name='".$class_nk[$i]."' where class_id='$class_id' and year='$new_sel_year'";
		$CONN->Execute($query);
	}
	if ($c_num<count($cclass)) {
		$query="delete from $class_kind where class_id > '$class_id'";
		$CONN->Execute($query);
	}
}
if($_POST['Submit2']){
	$c_name=$_POST[c_name];
	while(list($k,$v)=each($c_name)) {
		$query="update $class_kind set c_name='$v' where class_id='$k' and year='$new_sel_year'";
		$CONN->Execute($query);
	}
}
if($_POST['Submit3']){
	$stud_id=$_POST[stud_id];
	while(list($k,$v)=each($stud_id)) {
		$query="select * from new_stud where temp_id='A".$v."' and stud_study_year='$new_sel_year'";
		$res=$CONN->Execute($query);
		if (empty($res->fields[stud_name])) continue;
		$query="update new_stud set $class_sort='$_POST[input_class]',$class_site='$k' where temp_id='A".$v."' and stud_study_year='$new_sel_year'";
		$CONN->Execute($query);
	}
}
if($_POST['Submit4']){
	$kind=$_POST[kind];
	$lkind=$_POST[lkind];
	$proc=$_POST[proc];
	$query="select count(stud_name) from new_stud where stud_study_year='$new_sel_year' and class_year='$class_year_b' $class_str";
	$res=$CONN->Execute($query);
	$studs[0]=$res->fields[0];
	$query="select count(stud_name) from new_stud where stud_study_year='$new_sel_year' and class_year='$class_year_b' and stud_sex='1' $class_str";
	$res=$CONN->Execute($query);
	$studs[1]=$res->fields[0];
	$query="select count(stud_name) from new_stud where stud_study_year='$new_sel_year' and class_year='$class_year_b' and stud_sex='2' $class_str";
	$res=$CONN->Execute($query);
	$studs[2]=$res->fields[0];
	$query="update new_stud set $class_sort='',$class_site='' where stud_study_year='$new_sel_year'";
	$CONN->Execute($query);
	$query="select max(class_id) from $class_kind where year='$new_sel_year' and class_id like '$class_year_b%'";
	$res=$CONN->Execute($query);
	$classs=intval(substr($res->fields[0],1));
	switch ($kind) {
		case 0:
			$class_order="order by temp_id";
			break;
		case 1:
			$class_order="order by old_school,old_class";
			break;
		case 2:
			$class_order="order by stud_name";
			break;
                case 3:
                        $class_order="order by stud_id";
                        break;
		default:
			break;
	}
	switch ($lkind) {
		case 0:
			$sexcyc=array("0"=>"不分");
			break;
		case 1:
			$sexcyc=array("1"=>"男","2"=>"女");
			break;
		case 2:
			$sexcyc=array("2"=>"女","1"=>"男");
			break;
	}
	switch ($proc) {
		case 0:
			$cyc=0;
			while (list($x,$y)=each($sexcyc)) {
				$sex_where=($x!=0)?"and stud_sex='".$x."'":"";
				$pers=intval(($studs[$x]-1)/$classs);
				$perss=intval(($studs[0]-1)/$classs);
				$query="select * from new_stud where stud_study_year='$new_sel_year' and class_year='$class_year_b' $sex_where $class_str $class_order";
				$res=$CONN->Execute($query);
				$i=0;
				while ($i<$classs) {
					$i++;
					$temp_class=$class_year_b.sprintf("%02d",$i);
					$query="select max($class_site) from new_stud where stud_study_year='$new_sel_year' and $class_sort='$temp_class'";
					$res1=$CONN->Execute($query);
					$start_site=intval($res1->fields[0]);
					if ($cyc>0) {
						$k=(($studs[0]-1)%$classs)+1;
						$pp=($i<=$k)?$perss+1:$perss;
					} else {
						$k=(($studs[$x]-1)%$classs)+1;
						$pp=($i<=$k)?$pers+1:$pers;
					}
					$j=0;
					while (($j+$start_site)<$pp) {
						$j++;
						$newstud_sn=$res->fields[newstud_sn];
						$query="update new_stud set $class_sort='$temp_class',$class_site='".($start_site+$j)."' where newstud_sn='$newstud_sn'";
						$CONN->Execute($query);
						$res->MoveNext();
					}
				}
				$cyc++;
			}
			break;
		case 1:
			while (list($x,$y)=each($sexcyc)) {
				$sex_where=($x!=0)?"and stud_sex='".$x."'":"";
				$pers=$_POST[max_num];
				$query="select * from new_stud where stud_study_year='$new_sel_year' and class_year='$class_year_b' $sex_where $class_str $class_order";
				$res=$CONN->Execute($query);
				$i=0;
				while ($i<$classs) {
					$i++;
					$j=0;
					while ($j<$pers || ($i==$classs && !$res->EOF)) {
						$j++;
						$newstud_sn=$res->fields[newstud_sn];
						$temp_class=$class_year_b.sprintf("%02d",$i);
						$query="update new_stud set $class_sort='$temp_class',$class_site='$j' where newstud_sn='$newstud_sn'";
						$CONN->Execute($query);
						$res->MoveNext();
					}
				}
			}
			break;
		default:
			break;
	}
}
if($_GET['del']){
	$query="update new_stud set $class_sort='',$class_site='' where temp_id='A".$_GET['del']."' and stud_study_year='$new_sel_year'";
	$CONN->Execute($query);
}

switch($work){
	case 1:
		$Create_db="CREATE TABLE if not exists temp_class (
			class_sn smallint(5) unsigned NOT NULL auto_increment,
			class_id smallint(5) unsigned NOT NULL default '0',
			year smallint(5) unsigned NOT NULL default '0',
			c_name varchar(20) NOT NULL default '',
			c_sort tinyint(3) unsigned NOT NULL default '0',
			PRIMARY KEY (class_sn))";
		mysql_query($Create_db);  
		$Create_db="CREATE TABLE if not exists oth_class (
			class_sn smallint(5) unsigned NOT NULL auto_increment,
			class_id smallint(5) unsigned NOT NULL default '0',
			year smallint(5) unsigned NOT NULL default '0',
			c_name varchar(20) NOT NULL default '',
			c_sort tinyint(3) unsigned NOT NULL default '0',
			PRIMARY KEY (class_sn))";
		mysql_query($Create_db);  
		//取得班級命名方式
		if($chk <= 6){
			$pre_text="國小";
		}elseif($chk <= 9){
			$pre_text="國中";
		}elseif($chk <= 12){
			$pre_text="高中";
		}
		$end_txt="級";
		$query="select max(class_id) from $class_kind where year='$new_sel_year' and class_id like '$chk%'";
		$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$c_num=$res->fields[0];
		$c_num=intval(substr($c_num,1,2));
		$query="select c_name from $class_kind where year='$new_sel_year' and class_id like '$chk%'";
		$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
		$c_name=$res->fields[c_name];
		if(in_array($c_name,$class_name_kind_1)) $yc_name=1;
		elseif(in_array($c_name,$class_name_kind_2)) $yc_name=2;
		elseif(in_array($c_name,$class_name_kind_3)) $yc_name=3;
		elseif(!empty($c_name)) $yc_name=4;
		$class_nk="";
		for($i=0;$i<sizeof($class_name_kind);$i++){
			$selected=($yc_name==$i)?"selected":"";
			$class_nk.="<option value='$i' $selected>$class_name_kind[$i]</option>\n";
		}
		$classnk="<select name='c_name_kind'>$class_nk</select>\n";
		$select_class_num="<input type='text' name='c_num' size='3' value='$c_num'>\n";
		$all_year.="	<tr bgcolor='#FFF7CD'>
				<td>$pre_text".$school_kind_name[$chk]."$end_txt</td>
				<td>共 $select_class_num 班</td>
				<td>$classnk</td>
				<td><a href='{$_SERVER['PHP_SELF']}?class_kind=$class_kind&work=$work&class_year_b=$class_year_b&edit=edit'>各班級設定</a></td></tr>";
		echo "	</tr>
			<form name='form' method='post' action='{$_SERVER['PHP_SELF']}'>
			<table cellspacing=5 cellpadding=0><tr><td valign='top'>
			<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
			<tr bgcolor='#E1ECFF'><td>新生年級</td><td>臨時班級數</td><td>名稱種類</td><td>各班列表</td>
			".$all_year."</table>
			<input type='submit' name='Submit1' value='儲存'>
			<input type='hidden' name='class_year_b' value='$class_year_b'>
			<input type='hidden' name='class_kind' value='$class_kind'>
			<input type='hidden' name='work' value='$work'>";
		if ($_GET[edit]) {
			echo "<td align='center'><form name='form' method='post' action='{$_SERVER['PHP_SELF']}'><table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4><tr bgcolor='#E1ECFF'><td>修改個別班級名稱</td></tr>\n";
			$query="select class_id,c_name from $class_kind where year='$new_sel_year' and class_id like '$chk%' order by class_id";
			$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
			while (!$res->EOF) {
				echo "<tr bgcolor='#E1E6FF'><td align='center'>".$school_kind_name[$chk]."<input type='text' size='4' name='c_name[".$res->fields[class_id]."]' value='".$res->fields[c_name]."'>班</td></tr>\n";
				$res->MoveNext();
			}
			echo "</table><input type='submit' name='Submit2' value='確定修改'><input type='hidden' name='class_year_b' value='$class_year_b'><input type='hidden' name='class_kind' value='$class_kind'></form></td>";
		}
		echo "</tr></table>";
		break;

	case 2:
		$input_class=$_REQUEST[input_class];
		if (empty($input_class)) $input_class=$class_year_b."01";
		$class_menu=full_class_name($input_class,"input_class",$new_sel_year,$class_year_b,$class_kind);
		echo "<form name='form' method='post' action='{$_SERVER['PHP_SELF']}'>$class_menu<input type='hidden' name='work' value='$work'><input type='hidden' name='class_kind' value='$class_kind'></form></table>";
		$query="select * from new_stud where stud_study_year='$new_sel_year' and $class_sort='$input_class' order by $class_site";
		$res=$CONN->Execute($query) or  trigger_error($query,E_USER_ERROR);
		$sum=$res->RecordCount();
		while (!$res->EOF) {
			$id=$res->fields[$class_site];
			$stud_name[$id]=$res->fields[stud_name];
			$sex=$res->fields[stud_sex];
			if ($sex==1) {
				$stud_sex[$id]="男";
				$fontcolor[$id]="'blue'";
			} else {
				$stud_sex[$id]="女";
				$fontcolor[$id]="'#FF6633'";
			}
			$stud_id[$id]=substr($res->fields[temp_id],1);
			$url[$id]=(empty($stud_name[$id]))?"":"<a href={$_SERVER['PHP_SELF']}?work=2&input_class=$input_class&del=$stud_id[$id]&class_kind=$class_kind>調出</a>";
			$max_num=intval($id);
			$res->MoveNext();
		}
		$sum=($sum>$max_num)?$sum:$max_num;
		if ($sum<60) $sum=60;
		echo "	<form name='form' method='post' action='{$_SERVER['PHP_SELF']}'>
			<input type='submit' name='Submit3' value='儲存'>
			<table><tr><td>
			<table bgcolor='#000000' border='0' cellpadding='2' cellspacing='1'>
			<tr bgcolor='#E1ECFF'>
			<td>座號</td>
			<td>臨時編號</td>
			<td>學生姓名</td>
			<td>性別</td>
			<td>調出此班</td>
			</tr>\n";
		for ($i=1;$i<=$sum;$i++) {
			echo "	<tr bgcolor='#FFF7CD'>
				<td>$i</td>
				<td>A<input type='text' size='5' name='stud_id[$i]' value='$stud_id[$i]'></td>
				<td><font color=$fontcolor[$i]>$stud_name[$i]</font></td>
				<td align='center'><font color=$fontcolor[$i]>$stud_sex[$i]</font></td>
				<td align='center'>$url[$i]</td>
				</tr>\n";
		}
		echo "</table></td></tr></table><input type='hidden' name='class_year_b' value='$class_year_b'><input type='hidden' name='class_kind' value='$class_kind'><input type='hidden' name='input_class' value='$input_class'><input type='hidden' name='work' value='$work'><input type='submit' name='Submit3' value='儲存'></form>";
		break;

	case 3:
		$ksel[intval($_POST[kind])]="checked";
		$lsel[intval($_POST[lkind])]="checked";
		$psel[intval($_POST[proc])]="checked";
		echo "	</tr></table><form name='form' method='post' action='{$_SERVER['PHP_SELF']}'>
			<br>自動編班方式為：<br>
			<table cellspacing=5 cellpadding=0><tr><td valign='top'>
			<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
			<tr bgcolor='#E1ECFF' vlign='top'><td>
			<input type='radio' name='kind' value='0' $ksel[0]>依臨時編號<br>
			<input type='radio' name='kind' value='1' $ksel[1]>依原就讀學校<br>
			<input type='radio' name='kind' value='2' $ksel[2]>依姓名<br>
                        <input type='radio' name='kind' value='2' $ksel[3]>依正式學號
			</td><td>
			<input type='radio' name='lkind' value='0' $lsel[0]>不管性別<br>
			<input type='radio' name='lkind' value='1' $lsel[1]>每班男女均等，先編男生<br>
			<input type='radio' name='lkind' value='2' $lsel[2]>每班男女均等，先編女生<br>
			</td><td>
			<input type='radio' name='proc' value='0' $psel[0]>平均編至各班<br>
			<input type='radio' name='proc' value='1' $psel[1]>各班編滿<input type='text' size='2' name='max_num' value='$max_num'>人<br>
			</td></tr></table>
			<input type='submit' name='Submit4' value='開始編班'>
			<input type='hidden' name='work' value='$work'>
			<input type='hidden' name='class_year_b' value='$class_year_b'>
			<input type='hidden' name='class_kind' value='$class_kind'>
			</td></tr></table></form>";
		echo "	目前臨時編班狀況：<br>
			<table cellspacing=5 cellpadding=0><tr><td valign='top'>
			<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
			<tr bgcolor='#E1ECFF'><td>班別</td><td>男生人數</td><td>女生人數</td><td>總人數</td></tr>";
		$query="select * from $class_kind where year='$new_sel_year' and class_id like '$class_year_b%'";
		$res=$CONN->Execute($query);
		while (!$res->EOF){
			$class_id=$res->fields[class_id];
			$query="select stud_sex,count(stud_name) from new_stud where stud_study_year='$new_sel_year' and $class_sort='$class_id' group by stud_sex";
			$res_sex=$CONN->Execute($query);
			while (!$res_sex->EOF) {
				$sex[$class_id][$res_sex->fields[stud_sex]]=intval($res_sex->fields[1]);
				$res_sex->MoveNext();
			}
			echo "	<tr bgcolor='#FFF7CD'>
				<td align='center'>".$class_year[substr($class_id,0,1)].$res->fields[c_name]."班</td>
				<td align='right'>".intval($sex[$class_id][1])."</td>
				<td align='right'>".intval($sex[$class_id][2])."</td>
				<td align='right'>".intval($sex[$class_id][1]+$sex[$class_id][2])."</td></tr>";
			$res->MoveNext();
		}
		echo "</table></td></tr></table></table>";
		break;

	case 4:
		echo "	</tr></table><br>";
		echo "	<form name='form' method='post' action='{$_SERVER['PHP_SELF']}'>
			學生姓名：　<input type='text' name='stud_name' value='$stud_name'><br>\n
			臨時編號：　<input type='text' name='stud_id' value='$stud_id'><br>\n
			班級：　　　<input type='text' name='$class_sort' value='$temp_class'><br>\n
			身分證字號：<input type='text' name='stud_person_id' value='$stud_person_id'><br>\n
			生日：　　　<input type='text' name='stud_birthday' value='$stud_birthday'><br>\n
			電話：　　　<input type='text' name='stud_tel' value='$stud_tel'><br>\n
			住址：　　　<input type='text' name='stud_addr' value='$stud_addr'><small>(輸入部份住址即可)</small><br>\n
			家長姓名：　<input type='text' name='guardian_name' value='$guardian_name'><br>\n
			原就讀學校：<input type='text' name='old_school' value='$old_school'><br>\n
			<input type='hidden' name='work' value='$work'>
			<input type='hidden' name='class_year_b' value='$class_year_b'>
			<input type='hidden' name='class_kind' value='$class_kind'>
			<input type='submit' name='Submit5' value='開始查詢'><br><br>";
		if ($_POST[Submit5]) {
			if ($_POST[stud_name]) $where="and stud_name='$_POST[stud_name]'";
			if ($_POST[stud_id]) $where.="and temp_id='$_POST[stud_id]'";
			if ($_POST[$class_sort]) $where.="and $class_sort='$_POST[$class_sort]'";
			if ($_POST[stud_person_id]) $where.="and stud_person_id='$_POST[stud_person_id]'";
			if ($_POST[stud_birthday]) $where.="and stud_birthday='$_POST[stud_birthday]'";
			if ($_POST[stud_tel]) $where.="and stud_tel_1='$_POST[stud_tel]'";
			if ($_POST[stud_addr]) $where.="and stud_address like '$_POST[stud_addr]%'";
			if ($_POST[guardian_name]) $where.="and guardian_name='$_POST[guardian_name]'";
			if ($_POST[old_school]) $where.="and old_school='$_POST[old_school]'";
			$query="select * from new_stud where stud_study_year='$new_sel_year' $where order by stud_id";
			$res=$CONN->Execute($query);
			if ($res) {
				echo "<center><hr size='2' width='95%'><table border='0' cellspacing='2'><tr bgcolor='#FFEC6E'><td>臨時編號</td><td>班級</td><td>學生姓名</td><td>身分證字號</td><td>生日</td><td>電話</td><td>家長姓名</td><td>住址</td><td>原就讀學校</td></tr>";
				while (!$res->EOF) {
					echo "<tr bgcolor='#E6F7E2'><td>".$res->fields[temp_id]."</td><td>".$res->fields[$class_sort]."</td><td>".$res->fields[stud_name]."</td><td>".$res->fields[stud_person_id]."</td><td>".$res->fields[stud_birthday]."</td><td>".$res->fields[stud_tel_1]."</td><td>".$res->fields[guardian_name]."</td><td>".$res->fields[stud_address]."</td><td>".$res->fields[old_school]."</td></tr>";
					$res->MoveNext();
				}
				if ($res->RecordCount()==0) echo "<tr bgcolor='#E6F7E2'><td colspan='9' align='center'>查無符合資料</td></tr>";
				echo "</table></center>";
			}
		}
		echo "	</form></table>";
		break;

	case 5:
		echo "	</tr></table><br>
			目前尚未編班學生為：<br>
			<table cellspacing=5 cellpadding=0><tr><td valign='top'>
			<table bgcolor='#9EBCDD' cellspacing=1 cellpadding=4>
			<tr bgcolor='#E1ECFF'><td>臨時編號</td><td>姓名</td><td>性別</td></tr>";
		$query="select * from new_stud where stud_study_year='$new_sel_year' and class_year='$class_year_b' and (round($class_sort)='0' or round($class_site)='0') $class_str";
		$res=$CONN->Execute($query);
		while (!$res->EOF) {
			$sex=$res->fields[stud_sex];
			if ($sex==1) {
				$stud_sex="男";
				$fontcolor="'blue'";
			} else {
				$stud_sex="女";
				$fontcolor="'#FF6633'";
			}
			echo "<tr bgcolor='#FFF7CD'><td>".$res->fields[temp_id]."</td><td><font color=$fontcolor>".$res->fields[stud_name]."</font></td><td align='center'><font color=$fontcolor>$stud_sex</font></td></tr>";
			$res->MoveNext();
		}
		if ($res->RecordCount()==0) echo "<tr bgcolor='#FFF7CD'><td colspan='3' align='center'>查無資料</td></tr>";
		echo "</table></td></tr></table></table>";
		break;
	case 6:
		$query="select min(class_id),max(class_id) from $class_kind where year='$new_sel_year' and class_id like '$class_year_b%'";
		$res=$CONN->Execute($query);
		$min_class=intval(substr($res->fields[0],1,2));
		$max_class=intval(substr($res->fields[1],1,2));
		$start_class=$_POST[start_class];
		if (empty($start_class)) $start_class=$min_class;
		$end_class=$_POST[end_class];
		if (empty($end_class)) $end_class=$max_class;
		$checked[intval($_POST[kind])]="checked";
		if ($_POST[Submit6]) {
			$query="select * from school_base";
			$res=$CONN->Execute($query);
			$school_name=$res->fields[sch_cname];
			$query="select * from $class_kind where year='$new_sel_year' and class_id like '$class_year_b%' order by class_id";
			$res=$CONN->Execute($query);
			while (!$res->EOF) {
				$classn[$res->fields[class_id]]=$res->fields[c_name]."班";
				$res->MoveNext();
			}
			$sc=$class_year_b.sprintf("%02d",$start_class);
			$ec=$class_year_b.sprintf("%02d",$end_class);
			$csex=array("1"=>"男","2"=>"女");
			$query="select * from new_stud where stud_study_year='$new_sel_year' and $class_sort >= '$sc' and $class_sort <= '$ec' $class_str order by $class_sort,$class_site";
			$res=$CONN->Execute($query);
			while (!$res->EOF) {
				$temp_class=$res->fields[$class_sort];
				if ($temp_class!=$old_temp_class) $i=1;
				$temp_site=$res->fields[$class_site];
				$id_arr[$temp_class][$temp_site]=$res->fields[temp_id];
				$sure_study_arr[$temp_class][$temp_site]=($res->fields[sure_study])?$res->fields[sure_study]:0;
				$name_arr[$temp_class][$temp_site]=$res->fields[stud_name];
				$sex_arr[$temp_class][$temp_site]=$csex[$res->fields[stud_sex]];
				$c_year=$res->fields[class_year];
				$c_sort=$res->fields[class_sort];
				$c_site=$res->fields[class_site];
				if (intval($c_sort)!=0 && intval($c_site)!=0) {
					$classsite[$temp_class][$temp_site]=$res->fields[class_year].sprintf("%02d",$res->fields[class_sort])."-".sprintf("%02d",$res->fields[class_site]);
				} else {
					$classsite[$temp_class][$temp_site]="-----";
				}
				$i++;
				$res->MoveNext();
			}
			$pages=count($id_arr);
			reset ($id_arr);
			$pg=1;
			while (list($k,$v)=each($id_arr)) {
				if ($k=="") {
					$pages--;
					continue;
				}
				switch ($_POST[kind]) {
					case 0:
						echo "	<html><head><meta http-equiv=\"Content-Language\" content=\"zh-tw\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">
							<title>$school_name $new_sel_year 學年度 新生".$c_str."編班名冊</title></head>
							<body>
							<p align=\"center\"><font face=\"標楷體\" size=\"5\">$school_name $new_sel_year 學年度 新生".$c_str."編班名冊</font></p><p align=\"left\">".$c_str."編班：".$classn[$k]."</p>
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"610\">
							<tr>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">學　號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">性別</td>
							<td style=\"border-left:0.75pt solid windowtext; border-right:3px double windowtext; border-top:1.5pt solid windowtext; border-bottom:0.75pt solid windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm\" align=\"center\" width=\"70\">備　註</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">學　號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">性別</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">備　註</td>
							</tr>";
						for ($i=1;$i<=30;$i++)	{
							$j=$i+30;
							if ($i % 5 != 0)
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$i."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$i]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\">".$sex_arr[$k][$i]."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width:1\" align=\"center\"></td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">".$name_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\">".$sex_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"></td>
									</tr>";
							else
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$i</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$i]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\">".$sex_arr[$k][$i]."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width: 1.5pt\" align=\"center\"></td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1.5pt\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">".$name_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\">".$sex_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"></td>
									</tr>";
						}
						break;
					case 1:
						echo "	<html><head><meta http-equiv=\"Content-Language\" content=\"zh-tw\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">
							<title>$school_name $new_sel_year 學年度 新生正式編班名冊</title></head>
							<body>
							<p align=\"center\"><font face=\"標楷體\" size=\"4\">$school_name $new_sel_year 學年度</font><br><font face=\"標楷體\" size=\"5\">新生臨時編班<->正式編班對照名冊</font></p><p align=\"left\">臨時班別：".$classn[$k]."</p>
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"610\">
							<tr>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">臨時編號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-left:0.75pt solid windowtext; border-right:3px double windowtext; border-top:1.5pt solid windowtext; border-bottom:0.75pt solid windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm\" align=\"center\" width=\"105\">正式座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">臨時編號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"105\">正式座號</td>
							</tr>";
						for ($i=1;$i<=30;$i++)	{
							$j=$i+30;
							if ($i % 5 != 0)
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$i."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$i]."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width:1\" align=\"center\">".$classsite[$k][$i]."</td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\">".$classsite[$k][$j]."</td>
									</tr>";
							else
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$i</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$i]."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width: 1.5pt\" align=\"center\">".$classsite[$k][$i]."</td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1.5pt\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\">".$classsite[$k][$j]."</td>
									</tr>";
						}
						break;
					case 2:
						echo "	<html><head><meta http-equiv=\"Content-Language\" content=\"zh-tw\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">
							<title>$school_name $new_sel_year 學年度 新生正式編班名冊</title></head>
							<body>
							<p align=\"center\"><font face=\"標楷體\" size=\"4\">$school_name $new_sel_year 學年度</font><br><font face=\"標楷體\" size=\"5\">新生臨時編班<->正式編班對照名冊</font></p><p align=\"left\">臨時班別：".$classn[$k]."</p>
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"610\">
							<tr>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">臨時編號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-left:0.75pt solid windowtext; border-right:3px double windowtext; border-top:1.5pt solid windowtext; border-bottom:0.75pt solid windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm\" align=\"center\" width=\"105\">正式座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">臨時編號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"105\">正式座號</td>
							</tr>";
						for ($i=1;$i<=30;$i++)	{
							$j=$i+30;
							if ($classsite[$k][$i]=="-----") $name_arr[$k][$i]="-----";
							if ($classsite[$k][$j]=="-----") $name_arr[$k][$j]="-----";
							if ($i % 5 != 0)
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$i."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$i]."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width:1\" align=\"center\">".$classsite[$k][$i]."</td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\">".$classsite[$k][$j]."</td>
									</tr>";
							else
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$i</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$i]."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width: 1.5pt\" align=\"center\">".$classsite[$k][$i]."</td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1.5pt\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">&nbsp;".$name_arr[$k][$j]."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\">".$classsite[$k][$j]."</td>
									</tr>";
						}
						break;
					case 3:
						echo "	<html><head><meta http-equiv=\"Content-Language\" content=\"zh-tw\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">
							<title>$school_name $new_sel_year 學年度 新生臨時編班報到檢核名冊</title></head>
							<body>
							<p align=\"center\"><font face=\"標楷體\" size=\"5\">$school_name $new_sel_year 學年度<br>新生臨時編班報到檢核名冊</font></p><p align=\"left\">臨時編班：".$classn[$k]."</p>
							<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse: collapse\" bordercolor=\"#111111\" width=\"610\">
							<tr>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">學　號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">性別</td>
							<td style=\"border-left:0.75pt solid windowtext; border-right:3px double windowtext; border-top:1.5pt solid windowtext; border-bottom:0.75pt solid windowtext; padding-left:1.4pt; padding-right:1.4pt; padding-top:0cm; padding-bottom:0cm\" align=\"center\" width=\"70\">備　註</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">座號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">學　號</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"90\">姓　　名</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\" width=\"35\">性別</td>
							<td style=\"border-style: solid; border-color: windowtext; border-width: 1.5pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\" width=\"70\">備　註</td>
							</tr>";
						for ($i=1;$i<=30;$i++)	{
							$j=$i+30;
							if ($i % 5 != 0)
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$i."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">&nbsp;".(($sure_study_arr[$k][$i])?$name_arr[$k][$i]:"")."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\">".(($sure_study_arr[$k][$i])?$sex_arr[$k][$i]:"")."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width:1\" align=\"center\"></td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\">".(($sure_study_arr[$k][$j])?$name_arr[$k][$j]:"")."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; padding: 0cm 1.4pt;\" align=\"center\">".(($sure_study_arr[$k][$j])?$sex_arr[$k][$j]:"")."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 0.75pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"></td>
									</tr>";
							else
								echo "	<tr>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-left: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">$i</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$i]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">&nbsp;".(($sure_study_arr[$k][$i])?$name_arr[$k][$i]:"")."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\">".(($sure_study_arr[$k][$i])?$sex_arr[$k][$i]:"")."</td>
									<td style=\"border-right-style: double; border-right-width: 3; border-bottom-style:solid; border-bottom-width: 1.5pt\" align=\"center\"></td>
									<td style=\"border-bottom-style: solid; border-bottom-width: 1.5pt\" align=\"center\"><font face=\"Dotum\">$j</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\"><font face=\"Dotum\">".$id_arr[$k][$j]."</font></td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\">".(($sure_study_arr[$k][$j])?$name_arr[$k][$j]:"")."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; padding: 0cm 1.4pt;\" align=\"center\">".(($sure_study_arr[$k][$j])?$sex_arr[$k][$j]:"")."</td>
									<td style=\"border-style: solid; border-color: windowtext; border-width: 0.75pt 0.75pt 1.5pt; border-right: 1.5pt solid windowstext; padding: 0cm 1.4pt;\" align=\"center\"></td>
									</tr>";
						}
						break;
				}
				if ($pages!=$pg) echo "</table><span lang=\"zh-tw\" style=\"font-size:12.0pt;font-family:&quot;Times New Roman&quot;mso-fareast-font-family:新細明體;mso-font-kerning:1.0pt;mso-ansi-language:zh-tw;mso-fareast-language:ZH-TW;mso-bidi-language:zh-tw\"><br clear=\"all\" style=\"mso-special-character:line-break;page-break-before:always\"></span>";
				$pg++;
			}
			echo "</body></html>";
		} else {
		echo "	</tr></table><br><form name='form' method='post' action='{$_SERVER['PHP_SELF']}' target='new'>班級範圍：自<input type='text' name='start_class' size='2' value='$start_class'>班至<input type='text' size='2' name='end_class' value='$end_class'>班<br>
			<input type='radio' name='kind' value='0' $checked[0]>".$class_cname[$class_kind]."<->臨時編號名冊 <br>";
		if ($class_kind=="temp_class") echo "
			<input type='radio' name='kind' value='1' $checked[1]>".$class_cname[$class_kind]."<->正式編班名冊 <br>
			<input type='radio' name='kind' value='2' $checked[2]>".$class_cname[$class_kind]."<->正式編班名冊 <br>
			<input type='radio' name='kind' value='3' $checked[3]>".$class_cname[$class_kind]."<->報到檢核名冊 <br>";
		echo "	<input type='submit' name='Submit6' value='開始列印'>
			<input type='hidden' name='work' value='$work'>
			<input type='hidden' name='class_year_b' value='$class_year_b'>
			<input type='hidden' name='class_kind' value='$class_kind'>
			</form>";
		}
		break;
	default:
		echo "</tr></table>";
}


//結束主網頁顯示區
if(!$_POST['Submit6']) {
	echo "</td></tr></table>";

	//程式檔尾
	foot();
}

//臨時編班的班級選單
function  full_class_name($id,$col_name,$stud_study_year,$class_year_b,$class_kind){
	global $CONN;

	$temp_str="<select name='$col_name' onchange='this.form.submit();'>\n";
	$query="select * from $class_kind where year='$stud_study_year' and class_id like '$class_year_b%' order by class_id";
	$res=$CONN->Execute($query) or trigger_error($query,E_USER_ERROR);
	if ($res->RecordCount()==0)
		return "尚未設定班級，請先設定！";
	else {
		while (!$res->EOF) {
			$selected=($id==$res->fields[class_id])?"selected":"";
			$temp_str.="<option value='".$res->fields[class_id]."' $selected>".$res->fields[c_name]."班</option>\n";
			$res->MoveNext();
		}
		$temp_str.="</select>\n";
		return $temp_str;
	}
	
}
?>

<script language="JavaScript1.2">
<!-- Begin
function jumpMenu0(){
	var str, classstr ;
 if (document.form1.class_kind.options[document.form1.class_kind.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?class_kind=" + document.form1.class_kind.options[document.form1.class_kind.selectedIndex].value + "&class_year_b=" + document.form1.class_year_b.options[document.form1.class_year_b.selectedIndex].value + "&work=" + document.form1.work.options[document.form1.work.selectedIndex].value;
	}
}
function jumpMenu1(){
	var str, classstr ;
 if (document.form1.work.options[document.form1.work.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?class_kind=" + document.form1.class_kind.options[document.form1.class_kind.selectedIndex].value + "&class_year_b=" + document.form1.class_year_b.options[document.form1.class_year_b.selectedIndex].value + "&work=" + document.form1.work.options[document.form1.work.selectedIndex].value;
	}
}
//  End -->
</script>
