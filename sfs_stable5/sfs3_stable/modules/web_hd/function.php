<?php
	//個人網路硬碟目錄列表
	function reco ($s="0",$b="0"){
		global $CONN,$act;
		$back=str_repeat ("&nbsp;&nbsp;", $b);
		$b++;
		$sql="select * from hd_dir where teacher_sn='{$_SESSION['session_tea_sn']}' and struct='$s' and enable=1 order by dir_sn";
		//echo $sql."<br>";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$i=0;
		while(!$rs->EOF){
			$dir_sn[$i]=$rs->fields['dir_sn'];
			if(!$act) $act=$dir_sn[$i];
			$struct[$i]=$rs->fields['struct'];
			if($struct[$i]==0) $start_dir=$dir_sn[$i];//起始虛擬目錄
			$chinese_name[$i]=$rs->fields['chinese_name'];
			if($act==$dir_sn[$i]) {
				$main.=$back."<span style=\"background-color:#CFD5FF ;\"><a href='{$_SERVER['PHP_SELF']}?act=$dir_sn[$i]'><img src='images/folder_open.png' border='0' align='botton'> ".$chinese_name[$i]."</a></span><br>\n";
			}else{
				$main.=$back."<span onmouseover=\"style.background='#FFF6BA'\" onmouseout=\"style.background='#E9E9E9'\"><a href='{$_SERVER['PHP_SELF']}?act=$dir_sn[$i]'><img src='images/folder.png' border='0' align='botton'> ".$chinese_name[$i]."</a></span><br>\n";
			}
			$teacher_sn[$i]=$rs->fields['teacher_sn'];
			$level[$i]=$rs->fields['level'];
			$enable[$i]=$rs->fields['enable'];
			$main.=reco ($dir_sn[$i],$b);
			$i++;
			$rs->MoveNext();
		}
		return $main;
	}

	//目前目錄的檔案列表
	function file_list($dir_sn,$ms=""){
		global $CONN,$mang,$UPLOAD_PATH;
		$sql="select * from hd_file where teacher_sn='{$_SESSION['session_tea_sn']}' and dir_sn='$dir_sn' and enable=1 order by file_sn";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$i=0;
		$main="
			<form action='{$_SERVER['PHP_SELF']}' method='POST'>
			<input type='hidden' name='act' value='$dir_sn'>
			<tr bgcolor='#CFD4FF'><td width='1'>&nbsp;</td><td bgcolor='#FDBCD5' nowrap>檔案名稱</td><td bgcolor='#FDBCD5' nowrap>下載</td><td bgcolor='#FDBCD5'>大小</td><td width='50%' bgcolor='#FDBCD5'>說明</td></tr>\n
		";
		while(!$rs->EOF){
			$file_sn[$i]=$rs->fields['file_sn'];
			$source_name[$i]=$rs->fields['source_name'];
			$comment[$i]=$rs->fields['comment'];
			$level[$i]=$rs->fields['level'];
			//檢查檔案的大小
			$file_size[$i]=filesize ($UPLOAD_PATH."web_hd/$file_sn[$i]");
			if($file_size[$i]>1000000) $file_size[$i]=round($file_size[$i]/1024/1024,2)."MB";
			else $file_size[$i]=round($file_size[$i]/1024,2)."KB";
			//$file_info[$i]=file_info($real_name[$i]);
			if($mang=="全選") $checked[$i]=" checked";
			elseif($mang=="取消選擇") $checked[$i]="";
			$main.="<tr><td width='1' bgcolor='#CFD4FF'><input type='checkbox' name='file_arr[$i]' value='{$file_sn[$i]}' $checked[$i]></td><td nowrap><img src='images/document.png' border='0' align='botton'> ".$source_name[$i]."</td><td><a href='down.php?file={$file_sn[$i]}'><img src='images/filesave.png' border='0'></a></td><td>".$file_size[$i]."</td><td width='50%'> ".$comment[$i]." </td></tr>\n";
			$i++;
			$rs->MoveNext();
		}
		//完整目錄名稱
		$full_dir=substr(dir_name($dir_sn),2);

		$main="<table width='100%'><tr><td colspan='5'><h4> $full_dir 檔案列表</h4><hr></td></tr>".$ms.$main."</table>\n<p></p>";
		return $main;
	}
//目前所在目錄
function dir_name($dir_sn,$dir_name=""){
	global $CONN;
	$sql="select struct,chinese_name from hd_dir where dir_sn='$dir_sn' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$struct=$rs->fields['struct'];
	$chinese_name=$rs->fields['chinese_name'];

	if($struct) $full_dir.=dir_name($struct,$chinese_name);

	$full_dir.=' / '.addslashes($chinese_name);

	//$full_dir=substr($full_dir,1);
	return $full_dir;

}

//管理介面
function dir_mag($dir_sn){
	global $CONN;
	$b1="
		<form action='{$_SERVER['PHP_SELF']}' method='POST' name='form1'>
			<input type='text' name='new_dir_name' size='20' maxlength='100' value='新目錄名稱' onfocus=\"clear1(this)\">
			<input type='submit' value='新增一個子目錄'>
			<input type='hidden' name='mang' value='new_sub'>
			<input type='hidden' name='act' value='$dir_sn'>
		</form>";
	$b2="
		<form action='{$_SERVER['PHP_SELF']}' method='POST'>
			<input type='text' name='upd_dir_name' size='20' maxlength='100' value='目錄重新命名' onfocus=\"clear1(this)\">
			<input type='submit' value='更改本目錄名稱'>
			<input type='hidden' name='mang' value='upname_dir'>
			<input type='hidden' name='act' value='$dir_sn'>
		</form>";
	$b3="
			<form action='{$_SERVER['PHP_SELF']}' method='POST'>
			<input type='submit' value='刪除本目錄'>
			<input type='hidden' name='mang' value='del_dir'>
			<input type='hidden' name='act' value='$dir_sn'>
		</form>";

	$main="
		<table cellpadding='5'>
		<tr><td nowrap>$b1</td></tr>
		<tr><td nowrap>$b2</td></tr>
		<tr><td nowrap>$b3</td></tr>
		</table>";
	return $main;
}

//管理介面
function file_mag($dir_sn){
	global $CONN;
	$sql="select * from hd_dir where teacher_sn='{$_SESSION['session_tea_sn']}'  and enable=1";
		//echo $sql."<br>";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$i=0;
		while(!$rs->EOF){
			$dir_sn_arr[$i]=$rs->fields['dir_sn'];
			$full_dir[$i]=substr(dir_name($dir_sn_arr[$i]),2);
			$options.="<option value='{$dir_sn_arr[$i]}'>$full_dir[$i]</option>";
			$i++;
			$rs->MoveNext();
		}
	$b4="<input type='submit' name='mang' value='全選'>";

	$b5="<input type='submit' name='mang' value='取消選擇'>";

	$b6="<input type='submit' name='mang' value='刪除選擇'>";

	$b7="<input type='submit' name='mang' value='搬移到'>
		<select name='moveto'>
			$options
        </select>";
	$b8="<input type='submit' name='mang' value='更名為'><input type='text' name='new_name' size='20' value='重新命名' onfocus=\"clear1(this)\">";

	$b9="<form action ='{$_SERVER['PHP_SELF']}' enctype='multipart/form-data' method='post'>
		<input type='submit' name='mang' value='上傳'>
		<input type='hidden' name='act' value='$dir_sn'>
		<input type='file' name='userdata'><br>
		<textarea name='comment' onfocus=\"clear1(this)\" cols='40' rows='4'>上傳的檔案說明</textarea>
		</form>";


	$main="
		<table cellpadding='5'>
		<tr><td nowrap>$b4</td></tr>
		<tr><td nowrap>$b5</td></tr>
		<tr><td nowrap>$b6</td></tr>
		<tr><td nowrap>$b7</td></tr>
		<tr><td nowrap>$b8</td></tr>
		</form>
		<tr><td nowrap>$b9</td></tr>
		</table>
		";
	return $main;
}

//新增子目錄
function new_sub_fun($act,$new_dir_name){
	global $CONN;
	$new_dir_name=trim($new_dir_name);
	if(!$new_dir_name) return 0;
	$new_dir_name=double_dir($act,$new_dir_name,$new_dir_name,$num="0");
	$sql="insert into hd_dir(struct , chinese_name , teacher_sn , level , enable) values('$act','$new_dir_name','{$_SESSION['session_tea_sn']}','a','1')";
	$CONN->Execute($sql) or trigger_error($sql,256);

}

function upd_sub_fun($act,$upd_dir_name){
	global $CONN;
	$upd_dir_name=trim($upd_dir_name);
	if(!$upd_dir_name) return 0;
	$sql="update hd_dir set chinese_name='$upd_dir_name' where dir_sn='$act' ";
	$CONN->Execute($sql) or trigger_error($sql,256);

}

function del_sub_fun($act){
	global $CONN;
	$sql="select count(*) from hd_file where dir_sn='$act' and enable=1 ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$c1=$rs->fields['0'];
	$sql="select count(*) from hd_dir where struct='$act' and enable=1 ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$c2=$rs->fields['0'];
	$count=$c1+$c2;
	if($count>0) {
		$message[0]="string";
		$message[1]="<font color='red'>本目錄內上有子目錄或檔案存在，禁止刪除！</font><br>";
		return $message;
	}else{
		$sql="select struct from hd_dir where dir_sn='$act' ";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$struct[1]=$rs->fields['struct'];
		$struct[0]="sn";
		$sql="delete from hd_dir where dir_sn='$act' ";
		$CONN->Execute($sql) or trigger_error($sql,256);
		return $struct;
	}
}

function del_file_fun($act,$file_arr){
	global $CONN,$UPLOAD_PATH;
	foreach($file_arr as $one){
		//刪除實體檔案，實體檔名就是他的流水號
		unlink($UPLOAD_PATH."web_hd/$one");
		$sql="delete from hd_file where file_sn='$one' ";
		$CONN->Execute($sql) or trigger_error($sql,256);
	}

	return $act;

}

//檔案上傳函式
    function upload_file($dir_sn){
		global $CONN,$comment,$UPLOAD_PATH;
        //判斷上傳檔案是否存在
        if (!$_FILES['userdata']['tmp_name']) user_error("沒有傳入檔案代碼！請檢查！",256);
        if (!$_FILES['userdata']['name']) user_error("沒有傳入檔案代碼！請檢查！",256);
        if (!$_FILES['userdata']['size']) user_error("沒有傳入檔案代碼！請檢查！",256);
		if (!$dir_sn) user_error("沒有傳入目錄代碼！請檢查！",256);
		//檢查檔案名稱是否重複，若重複加上C..
		$file_name=double_file($dir_sn,$_FILES['userdata']['name'],$_FILES['userdata']['name'],$num="0");

		//寫入資料庫
		$sql="insert into hd_file (dir_sn , source_name , comment , teacher_sn , file_level , enable) values('$dir_sn' , '$file_name' , '$comment' , '{$_SESSION['session_tea_sn']}' , 'a' , '1')";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$new_name=$CONN->Insert_ID();

        //複製檔案到指定位置
		if(!is_dir($UPLOAD_PATH."web_hd")) mkdir ($UPLOAD_PATH."web_hd", 0700);
        copy($_FILES['userdata']['tmp_name'], $UPLOAD_PATH."web_hd/$new_name");
        //移除暫存檔
        unlink ($_FILES['userdata']['tmp_name']);

		//轉向到原來目錄
       return $dir_sn;
	}

	function double_file($dir_sn,$file_name,$o,$num="0"){
		global $CONN;
		//檢查檔案名稱是否重複，若重複加上C..
		$sql="select count(*) from hd_file where source_name='$file_name' and teacher_sn='{$_SESSION['session_tea_sn']}' and dir_sn='$dir_sn' and enable=1";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$cp=$rs->fields['0'];
		$num=$num+$cp;
		if($cp>0) {
			$file_name="copy".$num."_".$o;
			$file_name=double_file($dir_sn,$file_name,$o,$num);
		}
		return $file_name;

	}
	function double_dir($struct,$dir_name,$o,$num="0"){
		global $CONN;
		//檢查檔案名稱是否重複，若重複加上C..
		$sql="select count(*) from hd_dir where chinese_name='$dir_name' and teacher_sn='{$_SESSION['session_tea_sn']}' and struct='$struct' and enable=1";
		$rs=$CONN->Execute($sql) or trigger_error($sql,256);
		$cp=$rs->fields['0'];
		$num=$num+$cp;
		if($cp>0) {
			//echo $dir_name;
			$dir_name="copy".$num."_".$o;
			$dir_name=double_dir($struct,$dir_name,$o,$num);
		}
		return $dir_name;

	}

	function move_file($dir_sn,$file_arr,$moveto){
		global $CONN;
		foreach($file_arr as $one){
			$sql="select source_name from hd_file where file_sn='$one' ";
			$rs=$CONN->Execute($sql) or trigger_error($sql,256);
			$file_name=$rs->fields['source_name'];
			$file_name=double_file($moveto,$file_name,$file_name);
			$sql2="update hd_file set dir_sn='$moveto' , source_name='$file_name' where file_sn='$one' ";
			$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
		}
		return $dir_sn;
	}

	function rename_file($dir_sn,$file_arr,$new_name){
		global $CONN;
		foreach($file_arr as $one){
			$new_name=double_file($dir_sn,$new_name,$new_name);
			$sql2="update hd_file set source_name='$new_name' where file_sn='$one' ";
			$rs2=$CONN->Execute($sql2) or trigger_error($sql2,256);
		}
		return $dir_sn;
	}

function quota(){
	global $CONN,$UPLOAD_PATH;
	//檢視目前該使用者有多少目錄
	$sql="select count(*) from hd_dir where teacher_sn='{$_SESSION['session_tea_sn']}' and enable=1";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$many_dir=$rs->fields['0'];

	//檢視目前該使用者有多少檔案，及使用容量
	$sql="select file_sn from hd_file where teacher_sn='{$_SESSION['session_tea_sn']}' and enable=1";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$i=0;
	$size=0;
	while(!$rs->EOF){
		$file_sn[$i]=$rs->fields['file_sn'];
		$file_size[$i]=filesize ($UPLOAD_PATH."web_hd/$file_sn[$i]");
		$size=$size+$file_size[$i];
		$i++;
		$rs->MoveNext();
	}
	$many_file=$i;
	$QTA[1]=$many_dir+$many_file;
	$QTA[0]=$size;
	return $QTA;
}

//判斷是否為系統管理員
function is_admin(){
	global $CONN;
	$sql0="SELECT id_sn FROM pro_check_new WHERE pro_kind_id='1' and id_kind='教師' ";
	$rs0=$CONN->Execute($sql0) or trigger_error($sql0,256);
	if ($rs0) {
		while( $ar = $rs0->FetchRow() ) {
			$admin_arr[]=$ar['id_sn'];
		}
	}
	if(in_array( $_SESSION['session_tea_sn'],$admin_arr)) return 1;
	else return 0;
}

//檢查是否已經超過配額
function over_quota(){
	global $CONN;
	//取出個人配額
	$sql="select * from hd_quota where teacher_sn='{$_SESSION['session_tea_sn']}' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$size=$rs->fields['size'];
	$many=$rs->fields['many'];

	//如果0的話立刻給預設值
	if(!$size) $CONN->Execute("insert into hd_quota (teacher_sn,size,many,enable) values('{$_SESSION['session_tea_sn']}','20','200','1')");

	if(!$size) $size=20;
	if(!$many) $many=200;

	$QTA=quota();
	$QTA[0]=round($QTA[0]/1024/1024,2);
	if(($QTA[0]>=$size) || ($QTA[1]>=$many)) $q_mess[0]=0;//滿了
	else $q_mess[0]=1;//還可寫入
	if(is_admin())
		$m_quota="<span class='button'><a href='./quota.php'>管理</span>";
	$q_mess[1]="<font color='red'>你的網路硬碟配額為 $size MB，最多檔案數為 $many 目前尚有 ".($size-$QTA[0])."MB的空間和尚可容納 ".($many-$QTA[1])."個目錄或檔案！</font> $m_quota ";

	return $q_mess;
}

?>
