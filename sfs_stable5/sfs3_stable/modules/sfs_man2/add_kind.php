<?php

// $Id: add_kind.php 5310 2009-01-10 07:57:56Z hami $

//設定檔載入檢查
require "config.php";
// 認證檢查
sfs_check();

//執行動作判斷
if($_POST[act]=="insert"){
	add($_POST[data]);
	header("location: $_SERVER[PHP_SELF]");
}else{
	$main=&main_form();
}


//秀出網頁
head("學務程式設定");
echo $main;
foot();

//主要表格
function &main_form(){
	global $school_menu_p;

	$tool_bar=&make_menu($school_menu_p);
	$group_tree=get_group_tree();
	$main="
	$tool_bar
	<table  cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>
	<form action='$_SERVER[PHP_SELF]' method='POST'>
		<tr bgcolor='white'><td>
		<p>輸入分類名稱：
		<input type='text' name='data[showname]' value=''>
		<input type='hidden' name='data[kind]' value='分類'>
		</p><p>
		將此分類放到：".get_of_group("","data[of_group]",0,"分類","1")."之下
		</p><p>
		<input type='checkbox' name='data[islive]' value=1>立即啟用
		</p><p>
		<input type='checkbox' name='data[isopen]' value=1>允許一般網友進入瀏覽
		</p><p>
		<input type='hidden' name='act' value='insert'>
		<input type='submit' value='新增模組分類'>
		</p></td>
		</tr>
		</form>
	</table>
	</form>
	<p>
	<table  cellspacing='1' cellpadding='3' bgcolor='#C0C0C0' class='small'>
	$group_tree
	</table>
	</p>";
	return $main;
}

//新增
function add($data){
	global $CONN;
	//取得該分類下最後一個排序數字
	$sort=get_sort($data[of_group]);

	$sql_insert = "insert into sfs_module (showname,dirname,sort,isopen,islive,of_group,ver,icon_image,author,creat_date,kind,txt) values ('$data[showname]','$data[dirname]','$sort','$data[isopen]','$data[islive]','$data[of_group]','$data[ver]','$data[icon_image]','$data[author]','$data[creat_date]','$data[kind]','$data[txt]')";
	$CONN->Execute($sql_insert) or user_error("新增失敗！<br>$sql_insert",256);
	$msn=mysql_insert_id();
	return $msn;
}

//樹狀分類遞迴
function get_group_tree($curr_msn="",$group=0,$level=-1){
	global $CONN;

 	$level++;

	$sql_select="select msn,showname from sfs_module where kind='分類' and of_group='$group' order by sort";

	$recordSet=$CONN->Execute($sql_select) or user_error("讀取失敗！<br>$sql_select",256);
	while(list($msn,$showname)=$recordSet->FetchRow()){
		$name[$msn]=$showname;
	}

	if(empty($name) or sizeof($name)<=0)return;
	for($i=0;$i<$level;$i++){
		$blank.="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	}
	foreach($name as $msn=>$showname){
		$tool="
		<td><a href='index.php?msn=$group&set_msn=$msn&mode=setup'>設定</a></td>
		";
		if($group==0){
			$option.="<tr bgcolor='white'><td>".$blank.$showname."</td>$tool</tr>";
		}elseif($group!=0){

			$option.="<tr bgcolor='white'><td>".$blank."".$showname."</td>$tool</tr>";
		}
		$option.=get_group_tree($curr_msn,$msn,$level);
	}
	return $option;
}
?>
