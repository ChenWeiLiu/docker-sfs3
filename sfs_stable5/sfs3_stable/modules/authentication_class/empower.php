<?php

include "config.php";

sfs_check();


if($_POST['act']=='撤除選取的已授權名單'){
	$sn_list='';
	foreach($_POST[empowered_selected] as $sn) $sn_list.="$sn,";
	if($sn_list){
		$sn_list=substr($sn_list,0,-1);
		$sql="DELETE FROM authentication_empower WHERE sn IN ($sn_list)";
		$res=$CONN->Execute($sql) or user_error("刪除認證授權失敗！<br>$sql",256);		
	}
}

if($_POST['act']=='寫入授權'){
	$empowered=$_POST['empowered'];
	$batch_value="";
	foreach($empowered as $subitem_sn=>$empowered_sn)
	{
		if($empowered_sn) $batch_value.="('$curr_year_seme','$subitem_sn','$my_class_id','$my_sn','$empowered_sn',now()),";
	}
	$batch_value=substr($batch_value,0,-1);
	
	$sql_select="INSERT INTO authentication_empower(year_seme,subitem_sn,class_id,teacher_sn,empowered_sn,empowered_date) values $batch_value";
	$res=$CONN->Execute($sql_select) or user_error("簽認失敗！<br>$sql_select",256);

};

//秀出網頁
head("學習認證授權");

echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='empowered_selected[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

//橫向選單標籤
echo print_menu($MENU_P);
if($my_class_id){   //判定是否為班級導師
	//取得認證中項目的下拉選單，判斷是否有班級應認證細目
	$main="<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>
			<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111' width='100%'>
			<tr align='center' bgcolor='#FAFCAA'><td colspan=6><B>$class_base[$my_class_id]</B></td><td colspan=2><input type='submit' name='act' value='撤除選取的已授權名單' onclick='return confirm(\"確定要撤除選定的授權?\")'></td></tr>
			<tr align='center' bgcolor='#FFCCCC'><td>管理處室</td><td>認證項目</td><td>認證日期</td><td>細目</td><td>年級</td><td>積分</td>
			<td><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>已授權名單</td>
			<td><input type='submit' name='act' value='寫入授權'></td></tr>";
	$sql_select="select * from authentication_item WHERE CURDATE() BETWEEN start_date AND end_date order by code";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(!$res->EOF) {
		//抓取細目中有無任教班級的年級
		$item_sn=$res->fields[sn];
		$sql_subitem="select * from authentication_subitem WHERE item_sn=$item_sn ORDER BY code";
		$res_subitem=$CONN->Execute($sql_subitem) or user_error("讀取失敗！<br>$sql_subitem",256);
		$room_id=$res->fields[room_id];
		while(!$res_subitem->EOF) {

			$subitem_sn=$res_subitem->fields[sn];
			$grade_array=explode(',',$res_subitem->fields[grades]);
			$teacher_select="<select name='empowered[$subitem_sn]'><option value=''></option>$teacher_option</select>";
			if(in_array($my_class_grade,$grade_array)) {
				//取得本班級本細目已授權資料
				$teacher_data='';
				$sql_empower="select * from authentication_empower WHERE subitem_sn=$subitem_sn AND class_id='$my_class_id'";
				$res_empower=$CONN->Execute($sql_empower) or user_error("讀取失敗！<br>$sql_empower",256);
				while(!$res_empower->EOF) {
					//去除本細目已授權教師名單
					$empowered_sn=$res_empower->fields[empowered_sn];
					$empowered_name=$teacher_array[$empowered_sn];
					$empowered_list="<option value=$empowered_sn> $empowered_name </option>";
					$teacher_select=str_replace($empowered_list,"",$teacher_select);
					
					//顯示已授權教師姓名
					$teacher_data.="<input type='checkbox' name='empowered_selected[]' value='{$res_empower->fields[sn]}'>$empowered_name";
					$teacher_data.="<BR>";
					$res_empower->MoveNext();				
				}
				$teacher_data=substr($teacher_data,0,-4);
				$teacher_data=$teacher_data?$teacher_data:"--";
				$main.="<tr align='center'><td>$room_kind_array[$room_id]</td><td>{$res->fields[sn]}-{$res->fields[nature]}-{$res->fields[code]}-{$res->fields[title]}</td><td>{$res->fields[start_date]} ~ {$res->fields[end_date]}</td><td>{$res_subitem->fields[code]}-{$res_subitem->fields[title]}</td>
						<td>{$res_subitem->fields[grades]}</td><td>{$res_subitem->fields[bonus]}</td><td>$teacher_data</td><td>$teacher_select</td></tr>";
			}
			$res_subitem->MoveNext();
		}	
		$res->MoveNext();
	}
	$main.="</table></form>";

	echo $main;
} else {
	$main="<form name='myform' method='post' action='./authentication.php'>
			<input type='hidden' name='item_sn' value=''><input type='hidden' name='curr_class_id' value=''><input type='hidden' name='sn' value=''>
			<li>您非班級導師，下面顯示被授權的資訊~~~~</li>";
	$sql_empowered="select a.*,b.* from authentication_empower a INNER JOIN authentication_subitem b ON a.subitem_sn=b.sn WHERE a.empowered_sn=$my_sn AND a.year_seme='$curr_year_seme' ORDER BY class_id,code";
	$res_empowered=$CONN->Execute($sql_empowered) or user_error("讀取失敗！<br>$sql_empowered",256);
	if($res_empowered->recordcount()){
		//取得認證項目陣列
		$item_array=array();
		$sql="select * from authentication_item";
		$res_item=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
		while(!$res_item->EOF){
			$sn=$res_item->fields[sn];
			$item_array[$sn][code]=$res_item->fields[code];
			$item_array[$sn][title]=$res_item->fields[title];
			$item_array[$sn][nature]=$res_item->fields[nature];
			$item_array[$sn][room_id]=$res_item->fields[room_id];
			
			$res_item->MoveNext();
		}
		//取得認證中項目的下拉選單，判斷是否有班級應認證細目
		$main.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse;' bordercolor='#111111' width='100%'>
				<tr align='center' bgcolor='#FFCCCC'><td>班級</td><td>認證項目</td><td>動作</td><td>轉授權導師</td><td>授權日期</td><td>適用年級</td><td>積分</td></tr>";
		while(!$res_empowered->EOF) {
			$item_sn=$res_empowered->fields[item_sn];
			$subitem_sn=$res_empowered->fields[subitem_sn];
			$class_id=$res_empowered->fields[class_id];
			$item_data=$item_array[$item_sn][nature].'-'.$item_array[$item_sn][title];
			$class_name=$class_base[$class_id];
			$teacher_name=$teacher_array[$res_empowered->fields[teacher_sn]];
			$auth_submit=" <input type='button' name='act' value='進行認證' onclick=\"this.form.item_sn.value=$item_sn; this.form.sn.value=$subitem_sn; this.form.curr_class_id.value='$class_id'; this.form.submit();\"";
		
			$main.="<tr align='center'><td>$class_name</td><td>$item_data → {$res_empowered->fields[code]}-{$res_empowered->fields[title]}</td><td>$auth_submit</td><td>$teacher_name</td><td>{$res_empowered->fields[empowered_date]}</td><td>{$res_empowered->fields[grades]}</td><td>{$res_empowered->fields[bonus]}</td></tr>";	
			$res_empowered->MoveNext();
		}
		$main.="</table>";
	} else $main.="<li>~~未發現本學期被授權認證的資訊！</li></form>";
	echo $main;
}
foot();
?>