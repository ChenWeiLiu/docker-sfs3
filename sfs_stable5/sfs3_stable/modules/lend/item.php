<?php

//$Id: item.php 6731 2012-03-28 01:50:11Z infodaes $
include "config.php";
sfs_check();

$status=$_POST['status'];
$nature=$_POST['nature'];
$EditSearch=$_POST['EditSearch'];
if($EditSearch){ $nature=''; }



//處理上傳自訂的格式
if($_POST['do_key']=='上傳') {
	$default_filename='last_pics.zip';
	$is_win=ereg('win', strtolower($_SERVER['SERVER_SOFTWARE']))?true:false;
	//利用score_paper模組裡已經有的unzip.exe
	$zipfile=($is_win)?"$SFS_PATH/modules/score_paper/UNZIP32.EXE":"/usr/bin/unzip";

	$arg1=($is_win)?"START /min cmd /c ":"";
	$arg2=($is_win)?"-d":"-d";


	if($_FILES['myown']['type'] == "application/x-zip-compressed"){
			$filename=$default_filename;
	}elseif(strtolower(substr($_FILES['myown']['name'],-3))=="zip"){
			$filename=$default_filename;
	}else{
			die("請上傳ZIP類型檔案!!");
	}

	if (!is_dir($UPLOAD_PATH)) {
			die("上傳目錄 $UPLOAD_PATH 不存在！");
	}


	//統一上傳目錄
	$upath=$UPLOAD_PATH."lend";
	if (!is_dir($upath))  { mkdir($upath) or die($upath."建立失敗！"); }

	//上傳目的地
	$todir=$upath;
	$the_file=$todir.'/'.$filename;
	copy($_FILES['myown']['tmp_name'],$the_file);
	unlink($_FILES['myown']['tmp_name']);
	
	$todir=$upath."/pics/";
	if (is_dir($todir)) {
			deldir($todir);
	} else { mkdir($todir) or die($todir."目的目錄建立失敗！"); }
   
	if (!file_exists($zipfile)) {
			die($zipfile."不存在！");
	}elseif(!file_exists($the_file)) {
			die($the_file."不存在！");
	}

	$cmd=$arg1." ".$zipfile." ".$the_file." ".$arg2." ".$todir;
	exec($cmd,$output,$rv);
}



if($_POST['BtnSubmit']=='物品條碼' and $_POST[item_selected]){
	
	
	$Cols=$m_arr['Label_Cols'];
	$Barcode_Font=$m_arr['Barcode_Font'];
	$item_selected=$_POST[item_selected];
	
	$showdata.="<CENTER><font face='標楷體'>管理者：".$teacher_array[$session_tea_sn]['title']."-".$teacher_array[$session_tea_sn]['name']."　　　　類別：$nature</font>";
	$showdata.="<table align=center width=".$m_arr['Table_width']."% border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111'>";
	
	for($i=0;$i<count($item_selected);$i++)
	{
		$item_arr=explode($split_str,$item_selected[$i]);
		$mod=($i) % $Cols;

		if(!$mod) { $showdata.="<tr>"; }
		$target=$item_selected[$i];
		$showdata.="<td align='center'>".$item_arr[1]."<BR><font face='$Barcode_Font'>*".$item_arr[0]."*</font></td>";
		if($mod==($cols-1)) { $showdata.="</tr>"; }
	}
	$showdata.=(!($mod==$cols-1)?"</tr>":"")."</table></CENTER>";
	
	$go="<HTML><HEAD><TITLE>經管物品條碼</TITLE></HEAD>
		<BODY onLoad='printPage()' onclick='window.location.href=\"$_SERVER[PHP_SELF]\"'>

		<SCRIPT LANGUAGE='JavaScript'>
		function printPage() {
		window.print();
		}
		</SCRIPT>
		$showdata
		</BODY>
		</HTML>";
	echo $go;
	exit;
}


//秀出網頁
if(!$remove_sfs3head) head("經管物品維護");

echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='item_selected[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

//$manager_sn=$_REQUEST['manager_sn'];



//將新增項目值帶入
$serial_a=$_POST[serial_a];
$barcode_a=$_POST[barcode_a];
$item_a=$_POST[item_a];
$asset_no_a=$_POST[asset_no_a];
$nature_a=$_POST[nature_a];
$position_a=$_POST[position_a];
$maker_a=$_POST[maker_a];
$model_a=$_POST[model_a];
$healthy_a=$_POST[$healthy_a];
$opened_a=$_POST[opened_a];
$days_limit_a=$_POST[days_limit_a];
$sign_date_a=$_POST[sign_date_a];
$cost_a=$_POST[cost_a];
$saler_a=$_POST[saler_a];
$warranty_a=$_POST[warranty_a];
$importance_a=$_POST[importance_a];
$usage_years_a=$_POST[usage_years_a];


if($_POST['BtnSubmit']=='匯入')
{
	if ($_FILES['import']['size'] >0 && $_FILES['import']['name'] != "")
	{
		//讀出csv內容
		$items_arr=array();
		$fp = fopen($_FILES['import']['tmp_name'],"r");
		while ($data = sfs_fgetcsv($fp,2000, ","))
		{
  			$items_arr[]=$data;
		}
		fclose($fp);
		
		//echo "<PRE>";
		//print_r($items_arr);
		//echo "</PRE>";
		
		//準備匯入sql
		if(count($items_arr))
		{
			if($m_arr['import_ARR']='I') $sql='INSERT'; else $sql='REPLACE'; 
			$sql.=' INTO equ_equipments(serial,barcode,item,asset_no,nature,position,maker,model,healthy,opened,days_limit,sign_date,cost,saler,warranty,importance,usage_years,manager_sn) VALUES';
			foreach($items_arr as $key=>$value)
			{
				if($key)    //第一列習慣為標題列  不匯入
				{
					$items_value='';
					foreach($value as $field)
					{
						$items_value.='"'.$field.'",';
					}
					$sql.="($items_value$session_tea_sn),";
				}
			}
			$sql=substr($sql,0,-1);
			$sql=str_replace('""','NULL',$sql);
			//echo $sql;
			//開始進行匯入
			$res=$CONN->Execute($sql) or user_error("匯入物品紀錄失敗！<br>$sql",256);
			$executed='◎ '.date('Y/m/d h:i:s')." 已自[".$_FILES['import']['name']."]匯入物品資料";
		}
	}
}


if($_POST['BtnSubmit']=='新增'){
	$sql="INSERT INTO equ_equipments SET manager_sn=$session_tea_sn,serial='$serial_a',barcode='$barcode_a',item='$item_a',asset_no='$asset_no_a',";
	$sql.="nature='$nature_a',position='$position_a',maker='$maker_a',model='$model_a',healthy='$healthy_a',opened='$opened_a',";
	$sql.="days_limit='$days_limit_a',sign_date='$sign_date_a',cost='$cost_a',warranty='$warranty_a',importance='$importance_a',usage_years='$usage_years_a';";
	$sql=str_replace("''","NULL",$sql);
	$res=$CONN->Execute($sql) or user_error("新增物品紀錄失敗！<br>$sql",256);
	$nature=$nature_a;
	$executed='◎ '.date('Y/m/d h:i:s')." 已新增 $serial_a $item_a 物品資料!" ;
}

if($_POST['BtnSubmit']=='更新'){
	$sn_e=$_POST[sn_e];
	$serial_e=$_POST[serial_e];
	$barcode_e=$_POST[barcode_e];
	$item_e=$_POST[item_e];
	$asset_no_e=$_POST[asset_no_e];
	$nature_e=$_POST[nature_e];
	$position_e=$_POST[position_e];
	$maker_e=$_POST[maker_e];
	$model_e=$_POST[model_e];
	$healthy_e=$_POST[$healthy_e];
	$opened_e=$_POST[opened_e];
	$days_limit_e=$_POST[days_limit_e];
	$sign_date_e=$_POST[sign_date_e];
	$cost_e=$_POST[cost_e];
	$saler_e=$_POST[saler_e];
	$warranty_e=$_POST[warranty_e];
	$importance_e=$_POST[importance_e];
	$usage_years_e=$_POST[usage_years_e];
	
	$sql="UPDATE equ_equipments SET manager_sn=$session_tea_sn,serial='$serial_e',barcode='$barcode_e',item='$item_e',asset_no='$asset_no_e',";
	$sql.="nature='$nature_e',position='$position_e',maker='$maker_e',model='$model_e',healthy='$healthy_e',opened='$opened_e',";
	$sql.="days_limit='$days_limit_e',sign_date='$sign_date_e',cost='$cost_e',warranty='$warranty_e',importance='$importance_e',usage_years='$usage_years_e'";
	$sql.=" WHERE sn=$sn_e";
	$sql=str_replace("''","NULL",$sql);
	$res=$CONN->Execute($sql) or user_error("編修物品紀錄失敗！<br>$sql",256);
	$executed='◎ '.date('Y/m/d h:i:s')." 已更新 #$sn_e $serial_e $item_e 物品資料!";
}



if($_POST['BtnSubmit']=='刪除'){
	$action_item=$_POST['action_item'];
	$sql="SELECT equ_serial FROM equ_record WHERE equ_serial='$action_item'";
	$res=$CONN->Execute($sql) or user_error("檢查物品借用紀錄失敗！<br>$sql",256);
	$count=$res->recordcount();
	$sql="SELECT equ_serial FROM equ_request WHERE equ_serial='$action_item'";
	$res=$CONN->Execute($sql) or user_error("檢查物品預借紀錄失敗！<br>$sql",256);
	$count+=$res->recordcount();
	if($count) $executed="<font color='red'>物品編號[$action_item]有借用紀錄或預借申請, 系統禁止您刪除!</font>";
	else {
		//echo "您剛剛決定要刪除的是.... $action_item";
		$sql="DELETE FROM equ_equipments WHERE serial='$action_item'";
		$res=$CONN->Execute($sql) or user_error("刪除物品紀錄失敗！<br>$sql",256);
		$executed="<font color='blue'>物品編號[$action_item]已經刪除!</font>";
	}
}

if($_POST['BtnSubmit']=='編輯'){
	$modify_serial=$_POST['action_item'];
}

//橫向選單標籤
//$linkstr="manager_sn=$manager_sn";
if($_GET['menu']<>'off') echo print_menu($MENU_P,$linkstr);

$main="<table align=center border='2' cellpadding='5' cellspacing='0' style='border-collapse: collapse' bordercolor='#111111' id='AutoNumber1'>";
$main.="<form name='myform'  enctype='multipart/form-data' method='post' action='$_SERVER[PHP_SELF]'>類別：<select name='nature' onchange='this.form.EditSearch.value=\"\"; this.form.submit()'><option></option>";

	//取得物品分類
	$sql_select="SELECT nature,count(*) as amount FROM equ_equipments WHERE manager_sn=$session_tea_sn GROUP BY nature";
	$res=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	
	//echo $sql_select;
	
	while(!$res->EOF) {
		$main.="<option ".($nature==$res->fields['nature']?"selected":"")." value=".$res->fields['nature'].">".$res->fields['nature']."(".$res->fields['amount'].")</option>";
	$res->MoveNext();
	}
	$main.="</select>　名稱查詢：<input type='text' name='EditSearch' size='10' value='$EditSearch'><input type='submit' value='查詢' name='BtnSubmit'>";
	$main.="　<a href='lend_csv_format.zip'>檔案匯入：</a><input type='file' name='import' size=15><input type='submit' value='匯入' name='BtnSubmit'> <input type='submit' name='BtnSubmit' value='物品條碼' this.form.submit();\">";
	
	
	$showdata.="
	<input type='hidden' name='action_item'>
	<tr bgcolor='$Tr_BGColor'>
		<td align='center' width='100'>維護</td>
		<td align='center'><input type='checkbox' name='tag' onclick='javascript:tagall(this.checked);'>編號</td>
		<td align='center'>國際條碼號</td>
		<td align='center'>物品名稱</td>
		<td align='center'>財產編號</td>
		<td align='center'>分類</td>
		<td align='center'>位置</td>
		<td align='center'>製造商</td>
		<td align='center'>型號</td>
		<td align='center'>機能</td>
		<td align='center'>外借</td>
		<td align='center'>借期</td>
		<td align='center'>購買日期</td>
		<td align='center'>購買金額</td>
		<td align='center'>經銷商</td>
		<td align='center'>保固期限</td>
		<td align='center'>風險評估</td>
		<td align='center'>年限</td>
		<td align='center'>狀態</td>
	</tr>";
if($nature or $EditSearch)
{
	
//取得已預約的紀錄
$Requested_arr=array();
//$sql_select="SELECT * FROM equ_request WHERE ISNULL(memo)";
$sql_select="SELECT * FROM equ_request";
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);	
while(!$result->EOF)
{
	$Requested_arr[$result->fields['equ_serial']]['teacher_sn']=$result->fields['teacher_sn'];
	$Requested_arr[$result->fields['equ_serial']]['ask_date']=$result->fields['ask_date'];
	$Requested_arr[$result->fields['equ_serial']]['status']=$result->fields['status'];
	$result->MoveNext();
}

//echo "<PRE>";
//print_r($Requested_arr);
//echo "</PRE>";

//取得借用未歸紀錄
$NoReturn_arr=array();
$sql_select="SELECT equ_serial,teacher_sn,lend_date,refund_limit,(CURDATE()-refund_limit) as leftdays FROM equ_record WHERE ISNULL(refund_date)";
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);	
while(!$result->EOF)
{
	$NoReturn_arr[$result->fields['equ_serial']]['teacher_sn']=$result->fields['teacher_sn'];
	$NoReturn_arr[$result->fields['equ_serial']]['lend_date']=$result->fields['lend_date'];
	$NoReturn_arr[$result->fields['equ_serial']]['refund_limit']=$result->fields['refund_limit'];
	$NoReturn_arr[$result->fields['equ_serial']]['leftdays']=$result->fields['leftdays'];
	$result->MoveNext();
}

//echo "<PRE>";
//print_r($NoReturn_arr);
//echo "</PRE>";
	
//取得物品紀錄
$sql_select="SELECT * FROM equ_equipments WHERE manager_sn=$session_tea_sn";
if($EditSearch) $sql_select.=" AND item like '%$EditSearch%'"; else 
	if($nature) $sql_select.=" AND nature='$nature'";

//$sql_select.=" ORDER BY nature";
$result=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
if($result->recordcount()){
	while(!$result->EOF)
	{
		$status=0;
		$BGColor='#FFFFFF';
		$Alt_Message='';
		//已報廢物品顏色
		if($result->fields['crash_date']) $BGColor=$m_arr['Crashed_BGColor'];
		else {
			//檢查是否已經預約了　　　　　　$Requested_arr[$result->fields['serial']]['teacher_sn']
			if (array_key_exists($result->fields['serial'],$Requested_arr)) {
				$status=1;
				$BGColor=$m_arr['Requested_BGColor'];
				$Alt_Message=$teacher_array[$Requested_arr[$result->fields['serial']]['teacher_sn']]['name'].' '.$Requested_arr[$result->fields['serial']]['ask_date'].'['.$Requested_arr[$result->fields['serial']]['status'].']';
			}

			
			//檢查是否已經外借了
			if (array_key_exists($result->fields['serial'],$NoReturn_arr)) {
				$status=2;
				$BGColor=$m_arr['NotReturned_BGColor'];
				$Alt_Message=$teacher_array[$NoReturn_arr[$result->fields['serial']]['teacher_sn']]['name'].' '.$NoReturn_arr[$result->fields['serial']]['refund_limit'].'('.$NoReturn_arr[$result->fields['serial']]['leftdays'].')';
			}
		}		
		$alt=$teacher_array[$NoReturn_arr[$result->fields['serial']]['teacher_sn']]['title'];
		$alt.='-'.$NoReturn_arr[$result->fields['serial']]['refund_limit'];
		$alt.='-'.$NoReturn_arr[$result->fields['serial']]['leftdays'];		
		if($NoReturn_arr[$result->fields['serial']]['leftdays']>0) $Status_gif='out'; else $Status_gif='in';
		
		$sn=$result->fields['sn'];
		$serial=$result->fields['serial'];
		$item=$result->fields['item'];
		
		if($modify_serial===$serial){
			$showdata.="<tr bgcolor='red'>
			<td align='center'><img src='images/modify.png'><input type='submit' value='更新' name='BtnSubmit' onclick=' return confirm(\"確定要修改[$serial]$item?\")'></td>
			<td><input type='hidden' name='sn_e' value='$sn'><input type='hidden' name='serial_e' value='$serial'>$serial</td>
				<td><input type='text' size=10 name='barcode_e' value='".$result->fields['barcode']."'></td>
				<td><input type='text' size=20 name='item_e' value='".$result->fields['item']."'></td>
				<td><input type='text' size=10 name='asset_no_e' value='".$result->fields['asset_no']."'></td>
				<td><input type='text' size=10 name='nature_e' value='".$result->fields['nature']."'></td>
				<td><input type='text' size=4 name='position_e' value='".$result->fields['position']."'></td>
				<td><input type='text' size=10 name='maker_e' value='".$result->fields['maker']."'></td>
				<td><input type='text' size=10 name='model_e' value='".$result->fields['model']."'></td>
				<td><input type='text' size=4 name='healthy_e' value='".$result->fields['healthy']."'></td>
				<td><input type='text' size=2 name='opened_e' value='".$result->fields['opened']."'></td>
				<td><input type='text' size=2 name='days_limit_e' value='".$result->fields['days_limit']."'天></td>
				<td><input type='text' size=10 name='sign_date_e' value='".$result->fields['sign_date']."'></td>
				<td><input type='text' size=5 name='cost_e' value='".$result->fields['cost']."'></td>
				<td><input type='text' size=5 name='saler_e' value='".$result->fields['saler']."'></td>
				<td><input type='text' size=10 name='warranty_e' value='".$result->fields['warranty']."'></td>
				<td><input type='text' size=4 name='importance_e' value='".$result->fields['importance']."'></td>
				<td><input type='text' size=4 name='usage_years_e' value='".$result->fields['usage_years']."'></td>
				<td><input type='submit' value='更新' name='BtnSubmit' onclick=' return confirm(\"確定要修改[$serial]$item?\")'></td>
				</tr>";
		} else {
			$lend_pic="../../data/lend/pics/".$result->fields['barcode'].".jpg";
			$pic_show=$result->fields['barcode']?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#fccfaa';\" onMouseOut=\"this.style.backgroundColor='$BGColor';\" Onclick='receiver=window.open(\"$lend_pic\",\"物品圖片\",\"status=no,toolbar=no,location=no,menubar=no,width=$Pic_Width,height=$Pic_Height\");'":"";
			$showdata.="<tr align='center'><td>
			<input type='submit' value='編輯' name='BtnSubmit' onclick='this.form.action_item.value=\"$serial\"'>
			".($status?"":"<input type='submit' value='刪除' name='BtnSubmit' onclick='this.form.action_item.value=\"$serial\";return confirm(\"真的要刪除[$serial]$item?\")'>")."</td>
			<td><input type='checkbox' name='item_selected[]' value='$serial$split_str$item'>$serial</td>
			<td $pic_show>".$result->fields['barcode']."</td>
			<td $pic_show>$item</td>
				<td>".$result->fields['asset_no']."</td>
				<td>".$result->fields['nature']."</td>
				<td>".$result->fields['position']."</td>
				<td>".$result->fields['maker']."</td>
				<td>".$result->fields['model']."</td>
				<td>".$result->fields['healthy']."</td>
				<td>".$result->fields['opened']."</td>
				<td>".$result->fields['days_limit']."天</td>
				<td>".$result->fields['sign_date']."</td>
				<td>".$result->fields['cost']."</td>
				<td>".$result->fields['saler']."</td>
				<td>".$result->fields['warranty']."</td>
				<td>".$result->fields['importance']."</td>
				<td>".$result->fields['usage_years']."</td>
				<td onMouseOver=\"this.style.cursor='hand'\"><img src='images\\$status.gif' alt='$Alt_Message'  Onclick='receiver=window.open(\"\",\"收訊者\",\"status=no,toolbar=no,location=no,menubar=no,width=200,height=300\");receiver.document.write(\"$receiver\")'></td>
				</tr>";
		}
		$result->MoveNext();
	}
	
}
$showdata.="<tr bgcolor='$Tr_BGColor'> <td><input type='submit' value='新增' name='BtnSubmit'> </td>
			<td><input type='text' name='serial_a' value='$serial_a' size=10></td>
			<td><input type='text' name='barcode_a' value='$barcode_a' size=10></td>
			<td><input type='text' name='item_a' value='$item_a' size=20></td>
			<td><input type='text' name='asset_no_a' value='$asset_no_a' size=10></td>
			<td><input type='text' name='nature_a' value='$nature_a' size=10></td>
			<td><input type='text' name='position_a' value='$position_a' size=4></td>
			<td><input type='text' name='maker_a' value='$maker_a' size=10></td>
			<td><input type='text' name='model_a' value='$model_a' size=10></td>
			<td><input type='text' name='healthy_a' value='$healthy_a' size=4></td>
			<td><input type='text' name='opened_a' value='$opened_a' size=2></td>
			<td><input type='text' name='days_limit_a' value='$days_limit_a' size=2></td>
			<td><input type='text' name='sign_date_a' value='$sign_date_a' size=10></td>
			<td><input type='text' name='cost_a' value='$cost_a' size=5></td>
			<td><input type='text' name='saler_a' value='$saler_a' size=5></td>
			<td><input type='text' name='warranty_a' value='$warranty_a' size=10></td>
			<td><input type='text' name='importance_a' value='$importance_a' size=4></td>
			<td><input type='text' name='usage_years_a' value='$usage_years_a' size=4></td>
			<td><input type='submit' value='新增' name='BtnSubmit'></td>
			</tr>";
}

$showdata.="</table>◎物品圖片ZIP壓縮檔上傳：<input type=\"file\" name=\"myown\"><input type=\"submit\" name=\"do_key\" value=\"上傳\" onclick=\"if(this.form.myown.value) { return confirm(\'上傳後會將原上傳圖片替換，您確定要這樣做嗎?\'); } else return false;\"><font color='red' size=3>  (上傳前 1.請先轉換好適當尺寸，以免耗用過多系統硬碟空間 2.圖檔檔名請以國際條碼號命名)</font></form><BR>$executed";
echo $main.$showdata;

if(!$remove_sfs3head) foot();

?>
