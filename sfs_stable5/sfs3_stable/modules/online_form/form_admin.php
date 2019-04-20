<?php

// $Id: form_admin.php 9191 2018-01-05 07:37:01Z smallduh $

include "config.php";

sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

if(empty($act))$act="";

if($act=="add"){
	$ofsn=addnew($_SESSION['session_tea_sn']);
	header("location: {$_SERVER['PHP_SELF']}?act=add_step1&ofsn=$ofsn");
}elseif($act=="add_step1"){
	$main=&addForm1($_SESSION['session_tea_sn'],$ofsn);
}elseif($act=="add_step2"){
	$ofsn=add_step1($ofsn,$_SESSION['session_tea_sn'],$newForm);
	if(!empty($ofsn)){
		header("location: {$_SERVER['PHP_SELF']}?act=addForm2&ofsn=$ofsn&n=$n");
	}else{
		trigger_error("建立線上填報失敗！", E_USER_ERROR);
	}
}elseif($act=="add_step3"){
	$result=add_step3($newForm,$ofsn,$col_n,$mode);
	if($result){
		header("location: {$_SERVER['PHP_SELF']}?act=ut_form&ofsn=$ofsn");
	}else{
		//error_msg("建立線上填報失敗！");
	}
}elseif($act=="ut_form"){
	$main=&utfile($ofsn);
}elseif($act=="addForm2"){
	if($n<1)$n=1;
	$main=&addForm2($ofsn,$n);
}elseif($act=="edit_form"){
	$main=&edit_form($ofsn);
}elseif($act=="view_form"){
	$main=&view_form($ofsn);
}elseif($act=="enable_form"){
	able_form($ofsn,'1');
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="unable_form"){
	able_form($ofsn,'0');
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="view_form_result"){
	$main=&view_form_result($ofsn,$mode);
}elseif($act=="del"){
	$ofsn=$_POST['opt1'];
	del_form($ofsn);
	$main=&allAskForm($_SESSION['session_tea_sn']);
	//header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="save_modify"){
	save_modify($ofsn,$_SESSION['session_tea_sn'],$newForm);
	header("location: {$_SERVER['PHP_SELF']}");
}elseif($act=="view_demo"){
	$main=&view_demo();
}elseif($act=="unSign_list"){
	$main=&unSign_list($ofsn);
}elseif($act=="download"){
	$main=download($ofsn,$type);
}elseif($act=="school_sign_form"){
	$main=&view_form($ofsn);
}else{
	del_none();
	$main=&allAskForm($_SESSION['session_tea_sn']);
}

//秀出網頁
head("線上填報");

echo $main;
foot();




//列出未完成人
function &unSign_list($ofsn){
	global $CONN;

	$f=get_form_data($ofsn);
	$nook=0;


	//找出該填報主題的欄位
	$sql_select="select * from form_col where ofsn=$ofsn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while($c=$recordSet->FetchRow()){
		$main.="<td valign='top' style='font-size: 9px'>$c[col_title]</td>";
		$colsn[]=$c[col_sn];
		$cf[]=$c[col_function];
		$colchk[]=$c[col_chk];
	}


	//找出所有人
	$sql_select="select teacher_sn,name from teacher_base where  teach_condition=0 order by teacher_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while(list($tsn,$teacher_name)=$recordSet->FetchRow()){

		$school_col_v="";
		$unSign=false;
		//找出該校對該題的答案
		for($i=0;$i<sizeof($colsn);$i++){
			$col_sn=$colsn[$i];
			$v=get_someone_value($tsn,$col_sn);
			$school_col_v.="<td>$v</td>";
			if(is_null($v) and $colchk[$i]=='1')$unSign=true;
		}


		if($unSign){
			$main2.="
			<tr bgcolor='white'>
			<td style='font-size:12px' nowrap>$teacher_name</td>
			$school_col_v
			</tr>";
			$nook++;
			$unsign_list[]=$teacher_name;
		}
	}
	$school_unsign_list=(is_array($unsign_list))?"<center>未填報完成數（包含『必填』欄位未填完整）： $nook 間人，所有名單如下：</center><p>".implode("、",$unsign_list):"";

	$data=(empty($school_unsign_list))?"<p align='center'>全部均已填報完畢！</p>".view_form_result($ofsn):"
	$school_unsign_list
	<p>
	<table cellspacing='1' cellpadding='2' align='center' bgcolor='lightGray'>
	<tr bgcolor='#E1E6FF'><td style='font-size:12px'>填報人</td>$main</tr>
	$main2
	</table>";

	return $data;
}

//下載填報資料
function download($ofsn,$type){
	global $CONN,$UPLOAD_URL;
	$use_table_array=array("excel","word","sxw");
	if($type=="excel"){
		$file_type="application/vnd.ms-excel";
		$name2="xls";
	}elseif($type=="word"){
		$file_type="application/vnd.ms-word";
		$name2="doc";
	}elseif($type=="sxw"){
		$file_type="application/vnd.sun.xml.writer";
		$name2="sxw";
	}else{
		$file_type="text/plain";
		$name2="csv";
	}

	$f=get_form_data($ofsn);
	$ok=get_ok_count($ofsn);


	//找出該填報主題的欄位
	$sql_select="select * from form_col where ofsn=$ofsn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while($c=$recordSet->FetchRow()){
		$main.=(in_array($type,$use_table_array))?"<td>$c[col_title]</td>":"\"".$c[col_title]."\",";
		$colsn[]=$c[col_sn];
		$cf[]=$c[col_function];

	}

	//找出所有人
	$sql_select="select teacher_sn,name from teacher_base where  teach_condition=0 order by teacher_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while(list($tsn,$teacher_name)=$recordSet->FetchRow()){

		$school_col_v="";
		//找出該校對該題的答案
		for($i=0;$i<sizeof($colsn);$i++){
			$col_sn=$colsn[$i];

			$v=get_someone_value($tsn,$col_sn);

			$school_col_v.=(in_array($type,$use_table_array))?"<td align='center' class='small'>$v</td>":"\"".$v."\",";
		}


		if(in_array($type,$use_table_array)){
			$main2.="<tr><td>$teacher_name</td>$school_col_v</tr>";
		}else{
			$school_col_v=substr($school_col_v,0,-1);
			$main2.="\"".$teacher_name."\",".$school_col_v."\n";
		}
	}



	$filename="SFS3_Sign_".$ofsn."_data.".$name2;
	header("Content-type: ".$file_type.";CHARSET=big5");
	header("Content-Disposition: attachment; filename=$filename");
	if(in_array($type,$use_table_array)){
		echo "<table border='1'>
		<tr bgcolor='yellow'><td>填報人</td>$main</tr>
		$main2
		</table>";
	}else{
		echo "\"填報人\",".$main."\n".$main2;
	}
	exit;
}


//ok


//列出所有的填報資料
function &allAskForm($teacher_sn=0){
	
	global $CONN,$today,$school_menu_p,$module_manager;
	
	
	$tool_bar=&make_menu($school_menu_p);

	$help_text="注意喔！『刪除』按鈕一按，若選『確定』，該項填報資料就通通刪除囉！（包括所有人對該次調查所填寫的資料）所以請小心使用。||所謂『關閉』就是把某個填報活動暫停起來，如此，資料不會消失，但人便無法再繼續填報，當然，再次『啟用』就又可以繼續填報了。";
	$help=&help($help_text,$help_title="相關說明");
  if ($_POST['listall']) {
	 $sql_select="select * from form_all order by ofsn desc";
  } else {
	 $sql_select="select * from form_all where teacher_sn=$teacher_sn order by ofsn desc";
	}
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);

	while($f=$recordSet->FetchRow()){
		$ofsn=$f[ofsn];

		if($f[enable]=='1' and $today <= $f[of_dead_line]){
			$enableTXT="<font color='#0000FF'>on</font>";
			$enableTool="<a href='{$_SERVER['PHP_SELF']}?act=unable_form&ofsn=$ofsn'>關閉</a>";
		}else{
			$enableTXT="<font color='#808000'>off</font>";
			$enableTool="<a href='{$_SERVER['PHP_SELF']}?act=enable_form&ofsn=$ofsn'>啟用</a>";
		}
		$ok=get_ok_count($ofsn);

		$alllist.="<tr bgcolor='#FFFFFF'>
		<td nowrap class='small'><a href='{$_SERVER['PHP_SELF']}?act=view_form_result&ofsn=$ofsn'>$f[of_title]</a></td>
		<td align='center' nowrap class='small'>$enableTXT</td>
		<td align='center' nowrap class='small'>$f[of_start_date] - $f[of_dead_line]</td>
		<td align='center' nowrap class='small'><a href='{$_SERVER['PHP_SELF']}?act=school_sign_form&ofsn=$ofsn'>看外觀</a></td>
		<td align='center' nowrap class='small'><a href='{$_SERVER['PHP_SELF']}?act=edit_form&ofsn=$ofsn'>修改</a></td>
		<td align='center' nowrap class='small'>$enableTool</td>
		<td align='center' nowrap class='small'><a href=\"javascript:func($ofsn);\">刪除</a></td>
		<td align='center' nowrap class='small'><a href='{$_SERVER['PHP_SELF']}?act=download&type=csv&ofsn=$ofsn'>csv</a></td>
		<td align='center' nowrap class='small'><a href='{$_SERVER['PHP_SELF']}?act=download&type=excel&&ofsn=$ofsn'>xls</a></td>
		<td align='center' nowrap class='small'><a href='{$_SERVER['PHP_SELF']}?act=download&type=word&&ofsn=$ofsn'>doc</a></td>
		<td align='center' nowrap class='small'>已填：<a href='{$_SERVER['PHP_SELF']}?act=view_form_result&ofsn=$ofsn'>$ok</a>，<a href='{$_SERVER['PHP_SELF']}?act=unSign_list&ofsn=$ofsn'>未填名單</a></td></tr>\n
		";
	}



	if(empty($alllist)){
		$alllist="<tr bgcolor='#FFFFFF'><td nowrap class='small' colspan=11>目前沒有任何資料。
		<p>要不要<a href='form_admin.php?act=add'>新增一個調查表</a>呢？</p></td></tr>\n";
	}
  if ($module_manager) {
    $manager="
    <form name='myform' method='post' action='".$_SERVER['php_self']."'>
    <input type='hidden' name='act' value=''>
    <input type='hidden' name='opt1' value=''>
    <table border='0'>
     <tr>
       <td>※您具有模組管理權-- 
       <input type='checkbox' name='listall' value='1'".(($_POST['listall'])?" checked":"")." onclick='document.myform.submit()'>顯示所有調查表
       </td>
     </tr>
    </table>
    </form>\n
    ";
  } else {
    $manager="";
  }
	$main="
 	<script>
	function func(ofsn){
	var sure = window.confirm('確定要刪除這篇填報資料？連同人所填的資料都會一並刪除喔！');
	if (!sure) {
	return;
	}
  
  document.myform.opt1.value=ofsn;
  document.myform.act.value='del';
  document.myform.submit();
  
	//location.href=\"{$_SERVER['PHP_SELF']}?act=del&ofsn=\" + ofsn;
	}
	</script>
	$tool_bar
	$manager
	<table width='100%' cellspacing='1' cellpadding='2' bgcolor='#9EBCDD' align='center'>
	<tr bgcolor='#E1E6FF'>
	<td align='center' nowrap class='small'>名稱</td>
	<td align='center' nowrap class='small'>狀態</td>
	<td align='center' nowrap class='small'>起訖時間</td>
	<td align='center' colspan=4 nowrap class='small'>填報相關功能</td>
	<td align='center' colspan=3 nowrap class='small'>下載儲存</td>
	<td align='center' colspan=2 nowrap class='small'>填報結果</td></tr>
	$alllist
	</table>";




	$all="
	$main
	<table bgcolor='#9EBCDD' cellspacing=0 cellpadding=4>
	<tr bgcolor='#FFFFFF'><td>
	$help
	</td></tr>
	</table>
	";
	return $all;
}


//儲存修改
function save_modify($ofsn,$teacher_sn,$newForm){
	global $CONN;
	add_step1($ofsn,$teacher_sn,$newForm);
	//找出題數
	$sql_select="select count(*) from form_col where ofsn=$ofsn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	list($n)=$recordSet->FetchRow();
	//更新欄位資料
	for($i=1;$i<=$n;$i++){
		$title=$i."_col_title";
		$text=$i."_col_text";
		$dataType=$i."_col_dataType";
		$value=$i."_col_value";
		$chk=$i."_col_chk";
		$function=$i."_col_function";
		$sort=$i."_col_sort";
		$col_sn=$i."_col_sn";
		add_col2db($ofsn,$newForm[$title],$newForm[$text],$newForm[$dataType],$newForm[$value],$newForm[$chk],$newForm[$function],$newForm[$sort],$newForm[$col_sn]);
	}
	return;
}

//新增或更新一個欄位，把欄位設定寫入資料庫
function add_col2db($ofsn,$title,$text,$dataType,$value,$chk,$function,$sort,$col_sn=0){
	global $CONN;
	if(!empty($col_sn)){
		$str="update form_col set ofsn=$ofsn,col_title='$title',col_text='$text',col_dataType='$dataType',col_value='$value',col_chk='$chk',col_function='$function',col_sort=$sort where col_sn=$col_sn";
	}else{
		$str="INSERT INTO form_col
		(ofsn,col_title,col_text,col_dataType,col_value,col_chk,col_function,col_sort)
		VALUES
		($ofsn,'$title','$text','$dataType','$value','$chk','$function','$sort')";
	}

	$recordSet=$CONN->Execute($str) or die($str);

	if(!empty($recordSet)){
		$ID=(!empty($col_sn))?$col_sn:mysql_insert_id();
	}else{
		trigger_error($str, E_USER_ERROR);
	}
	return $ID;
}


//刪除未建立的報表
function del_none(){
	global $CONN,$today,$path,$DIR_TNCCENTER;
	$sql_delete="delete from form_all where of_title='無主題' && teacher_sn={$_SESSION['session_tea_sn']}";
	$CONN->Execute($sql_delete) or die($sql_delete);
	return;
}



//刪除某一填報資料
function del_form($ofsn){
	global $CONN;
	$str="delete from form_all where ofsn=$ofsn";
	$CONN->Execute($str) or die($str);
	$str="delete from form_col where ofsn=$ofsn";
	$CONN->Execute($str) or die($str);
	$str="delete from form_fill_in where ofsn=$ofsn";
	$CONN->Execute($str) or die($str);
	$str="delete from form_value where ofsn=$ofsn";
	$CONN->Execute($str) or die($str);
	return $ofsn;
}


//先開一個檔
function addnew($teacher_sn){
	global $CONN,$today;
	$sql_insert="insert into form_all (of_title,of_start_date,of_dead_line,of_text,teacher_sn,of_date,enable) values('無主題',now(),now(),'',$teacher_sn,now(),'0')";
	$CONN->Execute($sql_insert) or user_error($sql_insert,256);
	$ofsn=mysql_insert_id();
	return $ofsn;
}

//取得某一填報表單資料
function get_form_data($ofsn){
	global $CONN;
	$sql_select="select * from form_all where ofsn=$ofsn";
	$recordSet=$CONN->Execute($sql_select) or user_error($sql_select,256);
	$f=$recordSet->FetchRow();
	return $f;
}

//取得完成數
function get_ok_count($ofsn){
	global $CONN;
	//找出總數
	$sql_select="select count(*) from form_fill_in where ofsn=$ofsn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	list($ok)=$recordSet->FetchRow();
	return $ok;
}


//啟用一個填報區
function able_form($ofsn=0,$mode="1"){
	global $CONN;
	$str="update form_all set enable='$mode' where ofsn=$ofsn";
	$recordSet=$CONN->Execute($str) or die($str);
	return false;
}

//觀看某一填報的內容結果
function &view_form_result($ofsn=0,$mode=""){
	global $CONN,$UPLOAD_URL;
	$function_name=array("avg"=>"平均","sum"=>"總計","count"=>"計數");

	$f=get_form_data($ofsn);
	$ok=get_ok_count($ofsn);
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];



	//找出該填報主題的欄位
	$sql_select="select * from form_col where ofsn=$ofsn order by col_sn";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while($c=$recordSet->FetchRow()){
		$main.="<td align='center' class='small'>$c[col_title]</td>";
		$colsn[]=$c[col_sn];
		$cf[]=$c[col_function];
		$col_type[]=$c[col_dataType];
	}

	if($mode=="class"){
		//找出帶班的老師
		$sql_select="SELECT p.class_num,t.name,t.teacher_sn FROM teacher_post p , teacher_base t WHERE p.teacher_sn =t.teacher_sn and t.teach_condition =0 order by p.class_num";
	}else{
		//找出所有教師
		$sql_select="select teacher_sn,name from teacher_base where teach_condition=0 order by teacher_sn";
	}

	$recordSet=$CONN->Execute($sql_select) or user_error("錯誤訊息：",$sql_select,256);
	while($thedata=$recordSet->FetchRow()){
		//如果是班級模式，沒有班級的老師不列出
		if($mode=="class" and empty($thedata[class_num]))continue;

		$tsn=($mode=="class")?$thedata[teacher_sn]:$thedata[teacher_sn];
		$teacher_name=($mode=="class")?$thedata[name]:$thedata[name];
		$class_name=($mode=="class" and !empty($thedata[class_num]))?class_id2big5($thedata[class_num],$sel_year,$sel_seme):"";

		$school_col_v="";
		//找出該教師所填的的答案
		for($i=0;$i<sizeof($colsn);$i++){
			$col_sn=$colsn[$i];
			$type = $col_type[$i];
			$v=get_someone_value($tsn,$col_sn);
			if($type=='file')
				$v = "<a href='$UPLOAD_URL".get_store_path()."/".$ofsn."/".$col_sn."/$v'>$v</a>";
			$school_col_v.="<td align='center' class='small'>$v</td>";
		}

		$fill_time = get_someone_time($thedata[teacher_sn], $ofsn);
		$main2.="
		<tr bgcolor='white'><td class='small'>$class_name $teacher_name</td>
		$school_col_v
		<td ' class='small'>$fill_time</td>
		</tr>";
	}

	for($i=0;$i<sizeof($cf);$i++){
		$cfn=$cf[$i];
		$col_sn=$colsn[$i];
		if($cfn=="avg"){
			$cfrv=get_someone_value_avg($col_sn);
		}elseif($cfn=="count"){
			$cfrv=get_someone_value_count($col_sn);
		}elseif($cfn=="sum"){
			$cfrv=get_someone_value_sum($col_sn);
		}else{
			$cfrv="";
		}
		$main3.="<td>$cfrv</td>";
	}

	$mode_sel=($mode=="class")?"<a href='{$_SERVER[PHP_SELF]}?act=view_form_result&ofsn=$ofsn'>切換成教師模式</a>":"<a href='{$_SERVER[PHP_SELF]}?act=view_form_result&ofsn=$ofsn&mode=class'>切換成班級模式</a>";

	$data="
	<center>填報數：已有 $ok 人完成填報。<p>
	$mode_sel
	</center>
	<table cellspacing='1' cellpadding='2' align='center' bgcolor='lightGray'>
	<tr bgcolor='#E1E6FF'><td align='center' class='small'>題目：</td>$main<td>填報時間</td></tr>
	<tr bgcolor='yellow'><td align='center' class='small'>彙整功能</td>$main3<td></td></tr>
	$main2
	</table>";
	return $data;
}



//第二步驟的表單
function &addForm2($ofsn,$n,$mode=""){
	global $CONN,$teacher_sn,$today;

	$title="建立欲填報欄位（步驟二）";
	for($i=1;$i<=$n;$i++){
		$col.=get_col($i)."<hr>";
	}

	if(empty($mode))$mode="insert";



	$main="
	<form action='{$_SERVER['PHP_SELF']}' method='post'>
	$title
	$col
	<p>
	<input type='hidden' name='mode' value='$mode'>
	<input type='hidden' name='col_n' value='$n'>
	<input type='hidden' name='ofsn' value='$ofsn'>
	<input type='hidden' name='act' value='add_step3'>
	<center><input type='submit' value='下一步'></center>
	</form>
	說明：
	<ul>
	<li>『問題』：就是您要問的問題。
	<li>『預設值』：可在填答欄位中預先填入一個預設值。（非必須）
	<li>『說明』：萬一欄位的填寫較複雜時，可以寫上說明。（非必須）
	<li>『此題必填』：此功能為預設功能，尚未完成。
	<li>『填答型態』：填寫答案的資料型態。（建議填寫）
	<li>『彙整功能』：此功能會把所有的填答結果彙整起來，若是您選擇『加總』，那麼系統會幫您把該題的所有填報結果做加總。
	若選擇『計數』則會計算每個答案所填寫的人數。（非必須）
	</ul>
	<hr>
	小技巧：
	<ul>
	<li>可不可以限制填答項目？<p>
	可以！可以利用『選項』的欄位型態，讓填報者選答。
	<br>作法：
	<ol>
	<li>在『填答型態』選擇『選項』。
	<li>然後在『填答預設值』中可以輸入您的選項，選項用『;』（半形分號）隔開即可。
	<li>例如：『校長;主任;一般教師;代課教師』這樣就會產生四個選項的單選題。
	</ol>
	</ul>

	";
	return $main;
}


//建立新填報問卷的表單
function &addForm1($teacher_sn,$ofsn,$mode=""){
	global $CONN,$login_user,$today,$school_menu_p;
	$f=array();

	if($mode=="modify"){
		$f=get_form_data($ofsn);
		$pt="修改線上填報";
		$col_num="";
		$enable_v="1";
		$act_v="save_modify";
		$form1="";
		$form2="";
	}else{
		$pt="步驟一：建立一個填報問卷";
		$col_num="
		<tr bgcolor='#FFFFFF'>
		<td align='right' nowrap>欄位數：</td><td>
		<input type='text' name='n' class='tinyBorder' size=2> 題（也就是要填報的題目或欄位有幾個？）
		</td>
		</tr>";
		$enable_v="0";
		$act_v="add_step2";
		$form1="<form action='{$_SERVER['PHP_SELF']}' method='post'>";
		$form2="<center><input type='submit' value='下一步'></center></form>";
	}
	$tool_bar=&make_menu($school_menu_p);
	$main="
	$tool_bar
	<table bgcolor='#9EBCDD' cellspacing='1' cellpadding='4'>
	<tr bgcolor='#CDD5FF'><td colspan='2'>$pt</td></tr>
	$form1
	<tr bgcolor='#FFFFFF'>
	<td align='right'>填報名稱：</td>
	<td colspan='5'><input type='text' name='newForm[of_title]' size='50' class='tinyBorder' value='".$f["of_title"]."'></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td align='right'>填報說明：</td>
	<td><textarea cols='40' rows='6' name='newForm[of_text]' class='tinyBorder' style='width:100%'>$f[of_text]</textarea></td>
	</tr>
	<tr bgcolor='#FFFFFF'>
	<td align='right' nowrap>
	填報日期：</td><td>從 <input type='text' name='newForm[of_start_date]' value='$today' size='10' maxlength='10' class='tinyBorder'>
	起，至 <input type='text' name='newForm[of_dead_line]' size='10' maxlength='10' class='tinyBorder' value='$f[of_dead_line]'> 止</td>
	</tr>
	$col_num
	</table><p>
	<input type='hidden' name='newForm[enable]' value='$enable_v'>
	<input type='hidden' name='act' value='$act_v'>
	<input type='hidden' name='ofsn' value='$ofsn'>
	$form2
	";
	return $main;
}

//建立填報基本資料
function add_step1($ofsn,$teacher_sn,$newForm=""){
	global $CONN;
	$sql_update="update form_all set of_title='$newForm[of_title]',of_start_date='$newForm[of_start_date]',of_dead_line='$newForm[of_dead_line]',of_text='$newForm[of_text]',teacher_sn=$teacher_sn,of_date=now(),enable='$newForm[enable]' where ofsn=$ofsn";
	$CONN->Execute($sql_update) or die($sql_update);
	return $ofsn;
}


//欄位設定
function &get_col($n=0,$c=""){

	if(isset($c) and !empty($c)){
		$checked_1=($c[col_chk]=='1')?"checked":"";
		$checked_0=($c[col_chk]=='0')?"checked":"";
		$selected_varchar=($c[col_dataType]=='varchar')?"selected":"";
		$selected_int=($c[col_dataType]=='int')?"selected":"";
		$selected_date=($c[col_dataType]=='date')?"selected":"";
		$selected_bool=($c[col_dataType]=='bool')?"selected":"";
		$selected_file=($c[col_dataType]=='file')?"selected":"";
		$selected_sum=($c[col_function]=='sum')?"selected":"";
		$selected_avg=($c[col_function]=='avg')?"selected":"";
		$selected_count=($c[col_function]=='count')?"selected":"";
	}else{
		$checked_1="checked";
		$checked_0="";
		$selected_varchar="";
		$selected_int="";
		$selected_date="";
		$selected_bool="";
		$selected_file="";
		$selected_sum="";
		$selected_avg="";
		$selected_count="";
	}

	$main="
	<table width='96%' border='0' cellspacing='0' cellpadding='3' align='center' bgcolor='#123456'>
	<tr bgcolor='white'>
	<td align='right' bgcolor='#FBD08F'>問題 $n ：</td>
	<td bgcolor='#FBD08F'>
	<input type='text' name='newForm[".$n."_col_title]' value='$c[col_title]' size='30' class='tinyBorder'>
	</td>
	<td align='right' nowrap>預設值：</td>
	<td><input type='text' name='newForm[".$n."_col_value]' value='$c[col_value]' class='tinyBorder' size=15></td>
	</tr>
	<tr bgcolor='white'>
	<td colspan='2' rowspan='3'>說明：（可省略）<br>
	<textarea cols='20' rows='2' name='newForm[".$n."_col_text]' class='tinyBorder' style='width:100%'>$c[col_text]</textarea></td>
	<td align='right' nowrap>此題必填？</td><td>
	<input type='radio' name='newForm[".$n."_col_chk]' value='1' $checked_1>是
	<input type='radio' name='newForm[".$n."_col_chk]' value='0' $checked_0>否
	</td>
	</tr>
	<tr bgcolor='white'>
	<td align='right' nowrap>
	填答型態：</td><td>
	<select name='newForm[".$n."_col_dataType]'>
	<option value='varchar' $selected_varchar>文字</option>
	<option value='int' $selected_int>數字</option>
	<option value='date' $selected_date>日期</option>
	<option value='bool' $selected_bool>選項</option>
	<option value='file' $selected_file>檔案</option>
	</select>
	</td>
	</tr>
	<tr bgcolor='white'>
	<td align='right' nowrap>彙整功能</td><td>
	<select name='newForm[".$n."_col_function]'>
	<option value=''>無</option>
	<option value='sum' $selected_sum>加總</option>
	<option value='avg' $selected_avg>平均</option>
	<option value='count' $selected_count>計數</option>
	</select>
	</td>
	</tr>
	<input type='hidden' name='newForm[".$n."_col_sn]' value=$c[col_sn]>
	<input type='hidden' name='newForm[".$n."_col_sort]' value=$n>
	</table>
	";
	return $main;
}



//第三步驟
function add_step3($newForm,$ofsn,$col_n,$mode="update"){
	global $CONN;
	for($i=1;$i<=$col_n;$i++){
		$title=$i."_col_title";
		$text=$i."_col_text";
		$dataType=$i."_col_dataType";
		$value=$i."_col_value";
		$chk=$i."_col_chk";
		$function=$i."_col_function";
		$sort=$i."_col_sort";

		$a=$i."_col_title";
		if(!empty($newForm[$a])){
			if($mode=="insert"){
				$main=add_col2db($ofsn,$newForm[$title],$newForm[$text],$newForm[$dataType],$newForm[$value],$newForm[$chk],$newForm[$function],$newForm[$sort]);
			}elseif($mode=="update"){
				$main=add_col2db($newForm[$ofsn],$newForm[$title],$newForm[$text],$newForm[$dataType],$newForm[$value],$newForm[$chk],$newForm[$function],$newForm[$sort],$newForm[$col_sn]);
			}
		}
	}

	return $main;
}

//增加附件
function &utfile($ofsn){

	//取得該填報資料的附加檔
	$allfile =& getFormFile("ofsn",$ofsn);
	able_form($ofsn,'1');

	$title="上傳附件（步驟三）";
	$main="
	<script language='JavaScript'>
	function upload(){
		strFeatures = 'top=150,left=150,width=400,height=50,toolbar=0,menubar=0,location=0,directories=0,status=0';
		window.open('upload.php?col_name=ofsn&col_sn=$ofsn','upload',strFeatures);
		window.name = 'opener';
	}
	</script>
	$title
	<table border='0' cellspacing='0' cellpadding='4' align='center'>
	<tr>
		<td align='right' nowrap>有無附件檔：<br>
		<input type='button' value='新增附件' class='tinyBorder' onClick='upload()'>
		<input type='button' value='完成' onClick='window.location.href=\"{$_SERVER['PHP_SELF']}\"'>

		</td><td>
		$allfile
		</td>
	</tr>
	</table><p>
	";
	return $main;
}


//編輯某一填報表單
function &edit_form($ofsn){
	global $CONN,$today,$path,$DIR_TNCCENTER;
	$main=&addForm1($_SESSION['session_tea_sn'],$ofsn,"modify");
	//取得該題所有填報欄位

	$sql_select="select * from form_col where ofsn=$ofsn order by col_sort";
	$recordSet=$CONN->Execute($sql_select) or die($sql_select);
	while($c=$recordSet->FetchRow()){
		$col.=get_col($c[col_sort],$c);
	}
	$all="
	<form action='{$_SERVER['PHP_SELF']}' method='POST'>
	$main
	$col
	<center><input type='submit' value='修改完畢'></center>
	</form>
	";
	return $all;
}





//範例
function &view_demo(){
	global $school_menu_p;
	$tool_bar=&make_menu($school_menu_p);
	$main="

	$tool_bar
	<table cellspacing=1 cellpadding=4 bgcolor='#9EBCDD'>
	<tr><td>新增一個調查表</td></tr>
	<tr><td><img src='images/demo1.png' width=471 height=125 border=1><p>&nbsp;</td></tr>
	<tr><td>填寫填報相關訊息</td></tr>
	<tr><td><img src='images/demo2.png' width=536 height=303 border=1><p>&nbsp;</td></tr>
	<tr><td>設定各個欄位</td></tr>
	<tr><td><img src='images/demo3.png' width=629 height=427 border=1><p>&nbsp;</td></tr>
	<tr><td>若有附件可以按『新增附件』，若是沒有附件按『完成』即可。</td></tr>
	<tr><td><img src='images/demo4.png' width=440 height=86 border=1><p>&nbsp;</td></tr>
	<tr><td>上傳附件按『瀏覽』找出您要上傳的檔案即可，上傳附件數不限。</td></tr>
	<tr><td><img src='images/demo5.png' width=409 height=140 border=1><p>&nbsp;</td></tr>
	<tr><td>新增後，新的填報資訊會出現在管理介面中。</td></tr>
	<tr><td><img src='images/demo6.png' width=656 height=66 border=1><p>&nbsp;</td></tr>
	<tr><td>教師登入學務系統系統後會立即看到填報資訊。</td></tr>
	<tr><td><img src='images/demo7.png' width=464 height=232 border=1><p>&nbsp;</td></tr>
	<tr><td>教師填報時的畫面：</td></tr>
	<tr><td><img src='images/demo8.png' width=620 height=422 border=1><p>&nbsp;</td></tr>
	<tr><td>填報後，填報結果會立即改變，呈現幾人已經填報了。</td></tr>
	<tr><td><img src='images/demo9.png' width=654 height=72 border=1><p>&nbsp;</td></tr>
	<tr><td>可以觀看詳細的填報結果</td></tr>
	<tr><td><img src='images/demo10.png' width=642 height=266 border=1><p>&nbsp;</td></tr>
	<tr><td>也可以找出誰還沒填報。</td></tr>
	<tr><td><img src='images/demo11.png' width=628 height=268 border=1><p>&nbsp;</td></tr>
	</tr></table>
	";
	return $main;
}

?>
