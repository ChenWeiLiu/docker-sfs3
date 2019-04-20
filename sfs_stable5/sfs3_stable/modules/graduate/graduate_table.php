<?php

// $Id: graduate_table.php 7707 2013-10-23 12:13:23Z smallduh $

/*引入學務系統設定檔*/
require "config.php";

if($_GET['class_year_b']) $class_year_b=$_GET['class_year_b'];
else $class_year_b=$_POST['class_year_b'];
if($_GET['select_seme_year']) $select_seme_year=$_GET['select_seme_year'];
else $select_seme_year=$_POST['select_seme_year'];
if($_GET['order_name']) $order_name=$_GET['order_name'];
else $order_name=$_POST['order_name'];
if($_GET['class_id']) $class_id=$_GET['class_id'];
else $class_id=$_POST['class_id'];
if($_GET['dfile']) $dfile=$_GET['dfile'];
else $dfile=$_POST['dfile'];

//使用者認證
sfs_check();

if($dfile=="csv"){
	$filename="grad".$select_seme_year.$class_id.".csv";
	header("Content-disposition: filename=$filename");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
					//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

    header("Expires: 0");
	if($class_id) {
		$class_id_arr=explode("_",$class_id);
		$class_year=intval($class_id_arr[2]);
		$class_sort=intval($class_id_arr[3]);
		$where=" where gs.stud_grad_year='$select_seme_year' and class_year='$class_year' and class_sort='$class_sort' and gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
	}
	else $where=" where gs.stud_grad_year='$select_seme_year' and gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
	$sql_csv="select gs.* ,sb.stud_person_id,sb.stud_name,sb.stud_sex,sb.stud_tel_1,sb.stud_birthday,sb.stud_addr_1,sd.guardian_name from grad_stud as gs, stud_base as sb ,stud_domicile as sd $where";
	$rs_csv=$CONN->Execute($sql_csv);
	$i=0;
	echo "入學年，舊校名，身分證字號，姓名，性別，電話，生日（西元），家長姓名，住址";
	while(!$rs_csv->EOF){
		$stud_study_year[$i]=$rs_csv->fields['stud_grad_year']+1;
		$old_school[$i]=$school_short_name;
		$stud_person_id[$i]=$rs_csv->fields['stud_person_id'];
		$stud_name[$i]=$rs_csv->fields['stud_name'];
		$stud_sex[$i]=$rs_csv->fields['stud_sex'];
		$stud_tel_1[$i]=$rs_csv->fields['stud_tel_1'];
		$stud_birthday[$i]=$rs_csv->fields['stud_birthday'];
		$guardian_name[$i]=$rs_csv->fields['guardian_name'];
		$stud_addr_1[$i]=$rs_csv->fields['stud_addr_1'];
		echo "\n$stud_study_year[$i],$old_school[$i],$stud_person_id[$i],$stud_name[$i],$stud_sex[$i],$stud_tel_1[$i],$stud_birthday[$i],$guardian_name[$i],$stud_addr_1[$i]";
		$rs_csv->MoveNext();
		$i++;
	}
}

elseif($dfile=="sxw"){
	echo ooo();
}

else{
	//程式檔頭
	head("畢業生作業");

	$menu_p = array("graduate_out.php"=>"畢業轉出","graduate_table.php"=>"畢業生名冊", "graduate_score.php"=>"畢業成績");
	print_menu($menu_p);
	//設定主網頁顯示區的背景顏色
	echo "
	<table border=0 cellspacing=1 cellpadding=2 width=100% bgcolor=#cccccc>
	<tr>
	<td bgcolor='#FFFFFF'>";
	//網頁內容請置於此處
	if(empty($sel_year))$sel_year = curr_year(); //目前學年
	if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期
	$new_sel_year=date("Y")-1911;//目前民國年

	//歷年下拉選單stud_seme 
	if($select_seme_year) {
		if($class_id) {
			$class_id_arr=explode("_",$class_id);
			$class_year=intval($class_id_arr[2]);
			$class_sort=intval($class_id_arr[3]);
			$where=" where gs.stud_grad_year='$select_seme_year' and class_year='$class_year' and class_sort='$class_sort' and gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
		}
		else $where=" where gs.stud_grad_year='$select_seme_year' and gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
	}
	else $where=" where gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
	if($order_name) $sql="select gs.*,sb.stud_person_id,sb.stud_name,sb.stud_sex,sb.stud_tel_1,sb.stud_birthday,sb.stud_addr_1,sd.guardian_name from grad_stud as gs, stud_base as sb ,stud_domicile as sd $where order by $order_name";
	else $sql="select gs.* ,sb.stud_person_id,sb.stud_name,sb.stud_sex,sb.stud_tel_1,sb.stud_birthday,sb.stud_addr_1,sd.guardian_name from grad_stud as gs, stud_base as sb ,stud_domicile as sd $where";
	$rs=$CONN->Execute($sql);
	$i=0;
	while(!$rs->EOF){
		$stud_grad_year[$i]=$rs->fields['stud_grad_year'];
		$stud_id[$i]=$rs->fields['stud_id'];
		$rs->MoveNext();
		$i++;
	}
	$seme_year = array_unique ($stud_grad_year);
	$col_name="select_seme_year";
	$id=$select_seme_year;
	$menu="<option value=''>選擇學年度</option>\n";
	while(list($key , $val) = each($seme_year)) {
		$selected=($id==$val)?"selected":"";
		$menu.="<option value='$val' $selected>".$val."學年度</option>\n";

	}
	$seme_year_menu="
		<form name='form1' method='post' action='{$_SERVER['PHP_SELF']}'>
			<select name='$col_name' onChange='jumpMenu1()'>
				$menu
			</select>
		</form>";


	if($select_seme_year){
	//選擇班級
	$class_select_menu=&get_class_select($select_seme_year,2,$Cyear,$col_name="class_id",$jump_fn="jumpMenu2",$class_id,$mode="長");
	$class_select_obj="
		<form name='form2' method='post' action='{$_SERVER['PHP_SELF']}'>
		$class_select_menu
		<input type='hidden' name='select_seme_year' value='$select_seme_year'>
		</form>
	";
	}
	echo "<table border='0'><tr><td>".$seme_year_menu."</td><td>".$class_select_obj."</td><td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&dfile=csv'><span class='button'>下載csv檔</span></a></td><td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&dfile=sxw'><span class='button'>下載sxw檔</span></a></td></tr>";

	//列出名單
	if($select_seme_year){
		echo "<tr><td colspan='4'><table bgcolor='black' border='0' cellpadding='2' cellspacing='1'>
					<tr bgcolor='#FFEC6E'>
						<td colspan='7'>".$school_long_name." ".$select_seme_year."學年度畢業生名冊</td>
					</tr>
					<tr bgcolor='#FFEC6E'>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sb.stud_person_id'>姓名</a></td>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sb.stud_person_id'>身分證字號</a></td>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sb.stud_sex'>性別</a></td>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sb.stud_tel_1'>電話</a></td>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sb.stud_birthday'>生日</a></td>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sd.guardian_name'>家長姓名</a></td>
						<td><a href='{$_SERVER['PHP_SELF']}?select_seme_year=$select_seme_year&class_id=$class_id&order_name=sb.stud_addr_1'>住址</a></td>

					</tr>";
		$clear_stud_id = array_unique ($stud_id);
		if($order_name) $orderby=" order by $order_name";
		while(list($key1 , $stud_val) = each($clear_stud_id)) {
			$sql_base="select sb.stud_person_id,sb.stud_name,sb.stud_sex,sb.stud_tel_1,sb.stud_birthday,sb.stud_addr_1,sd.guardian_name from stud_base as sb,stud_domicile as sd where sb.stud_id='$stud_val' and sd.stud_id='$stud_val'";
			$rs_base=$CONN->Execute($sql_base);
			$stud_person_id=$rs_base->fields['stud_person_id'];
			$stud_name=$rs_base->fields['stud_name'];
			$stud_sex=$rs_base->fields['stud_sex'];
			$stud_tel_1=$rs_base->fields['stud_tel_1'];
			$stud_birthday=$rs_base->fields['stud_birthday'];
			if($stud_birthday!="" || $stud_birthday!="0000-00-00") $stud_birthday=DtoCh($stud_birthday);
			else $stud_birthday="";
			$guardian_name=$rs_base->fields['guardian_name'];
			$stud_address=$rs_base->fields['stud_addr_1'];
			if($stud_sex=="1"){
					$bgc="#C7CAFD";
					$stud_sex_ch="男";
			}
			elseif($stud_sex=="2"){
					$bgc="#F9C8FD";
					$stud_sex_ch="女";
			}
			echo "<tr bgcolor='$bgc'>
				<td><font color='$fcolor'>$stud_name</font></td>
				<td><font color='$fcolor'>$stud_person_id</font></td>
				<td><font color='$fcolor'>$stud_sex_ch</font></td>
				<td><font color='$fcolor'>$stud_tel_1</font></td>
				<td><font color='$fcolor'>$stud_birthday</font></td>
				<td><font color='$fcolor'>$guardian_name</font></td>
				<td><font color='$fcolor'>$stud_address</font></td>
				</tr>";
		}
		echo "</table></td></tr></table>";
	}
	else echo "</table>";

	//結束主網頁顯示區
	echo "</td>";
	echo "</tr>";
	echo "</table>";

	//程式檔尾
	foot();

?>

<script language="JavaScript1.2">
<!-- Begin

function jumpMenu1(){
	var str, classstr ;
 if (document.form1.select_seme_year.options[document.form1.select_seme_year.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?select_seme_year=" + document.form1.select_seme_year.options[document.form1.select_seme_year.selectedIndex].value;
	}
}

function jumpMenu2(){
	var str, classstr ;
    if (document.form2.class_id.options[document.form2.class_id.selectedIndex].value!="") {
	location="<?PHP echo $_SERVER['PHP_SELF'] ?>?select_seme_year=" + document.form2.select_seme_year.value + "&class_id=" + document.form2.class_id.options[document.form2.class_id.selectedIndex].value;
	}
}
//  End -->
</script>

<?php
}

		function ooo(){
			global $CONN,$school_long_name,$class_id,$select_seme_year;

			$oo_path = "ooo_grad";

			$filename="grad".$select_seme_year.$class_id.".sxw";

			//新增一個 zipfile 實例
			$ttt = new EasyZip;
			$ttt->setPath($oo_path);
			$ttt->addDir('META-INF');
			$ttt->addfile("settings.xml");
			$ttt->addfile("styles.xml");
			$ttt->addfile("meta.xml");

			//讀出 content.xml
			$data = $ttt->read_file(dirname(__FILE__)."/$oo_path/content.xml");

			//由資料庫取出相關資料
			if($class_id) {
				$class_id_arr=explode("_",$class_id);
				$class_year=intval($class_id_arr[2]);
				$class_sort=intval($class_id_arr[3]);
				$where=" where gs.stud_grad_year='$select_seme_year' and class_year='$class_year' and class_sort='$class_sort' and gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
			}
			else $where=" where gs.stud_grad_year='$select_seme_year' and gs.stud_id=sb.stud_id and sb.stud_id=sd.stud_id";
			$sql_sxw="select gs.* ,sb.stud_person_id,sb.stud_name,sb.stud_sex,sb.stud_tel_1,sb.stud_birthday,sb.stud_addr_1,sd.guardian_name from grad_stud as gs, stud_base as sb ,stud_domicile as sd $where";
			$rs_sxw=$CONN->Execute($sql_sxw);
			$i=0;
			while(!$rs_sxw->EOF){
				$stud_id[$i]=$rs_sxw->fields['stud_id'];
				$stud_study_year[$i]=$rs_sxw->fields['stud_grad_year']+1;
				$old_school[$i]=$school_short_name;
				$stud_person_id[$i]=$rs_sxw->fields['stud_person_id'];
				$stud_name[$i]=$rs_sxw->fields['stud_name'];
				$stud_sex[$i]=$rs_sxw->fields['stud_sex'];
				if($stud_sex[$i]=="1")	$stud_sex_ch[$i]="男";
				elseif($stud_sex[$i]=="2")	$stud_sex_ch[$i]="女";
				else	$stud_sex_ch[$i]="";
				$stud_tel_1[$i]=$rs_sxw->fields['stud_tel_1'];
				$stud_birthday[$i]=$rs_sxw->fields['stud_birthday'];
				if($stud_birthday[$i]!="" || $stud_birthday[$i]!="0000-00-00") $stud_birthday[$i]=DtoCh($stud_birthday[$i]);
				else $stud_birthday[$i]="";
				$guardian_name[$i]=$rs_sxw->fields['guardian_name'];
				$stud_addr_1[$i]=$rs_sxw->fields['stud_addr_1'];
				$rs_sxw->MoveNext();
				$i++;
			}
			//將 content.xml 的 tag 取代
			$temp_arr["school_name"] = $school_long_name;
			$temp_arr["seme_year"] = $select_seme_year."學年度畢業生名冊";
			$temp_arr["title"]="
				<table:table-row>
					<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
						<text:p text:style-name='P3'>姓名</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>身分證號</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>性別</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>聯絡電話</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>出生日期</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>家長姓名</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>住址</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.H2' table:value-type='string'>
						<text:p text:style-name='P3'>備考</text:p>
					</table:table-cell>
				</table:table-row>";
			for($i=0;$i<count($stud_id);$i++){
				$cont.="
				<table:table-row>
					<table:table-cell table:style-name='course_tbl.A2' table:value-type='string'>
						<text:p text:style-name='P3'>$stud_name[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>$stud_person_id[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>$stud_sex_ch[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>$stud_tel_1[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>$stud_birthday[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>$guardian_name[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.B2' table:value-type='string'>
						<text:p text:style-name='P3'>$stud_addr_1[$i]</text:p>
					</table:table-cell>
					<table:table-cell table:style-name='course_tbl.H2' table:value-type='string'>
						<text:p text:style-name='P3'/>
					</table:table-cell>
				</table:table-row>";
			}
			$temp_arr["cont"] = $cont;
			// change_temp 會將陣列中的 big5 轉為 UTF-8 讓 openoffice 可以讀出
			$replace_data = $ttt->change_temp($temp_arr,$data);

			// 加入 content.xml 到zip 中
			$ttt->add_file($replace_data,"content.xml");

			//產生 zip 檔
			$sss = & $ttt->file();

			//以串流方式送出 ooo.sxw
			header("Content-disposition: attachment; filename=$filename");
			header("Content-type: application/vnd.sun.xml.writer");
			//header("Pragma: no-cache");
							//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");

			header("Expires: 0");

			echo $sss;

			exit;
			return;
		}
?>
