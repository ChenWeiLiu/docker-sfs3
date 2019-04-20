<?php

// $Id: comment.php 5310 2009-01-10 07:57:56Z hami $

/* 取得學務系統設定檔 */
include "../../include/config.php";
//sfs_check();

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}
//echo $cq;
$MSETUP =& get_module_setup("academic_record");
$is_modify = $MSETUP[is_modify];
//echo $is_modify;
/************************主要內容*************************/ 

$teacher_id=$_SESSION['session_log_id'];//取得登入老師的id
//$comm_length->評語類別,ex:四字箴言...
//$level->等級,ex:甲,乙,丙,丁
//$comment->評語序號
//$comm->評語字串
//$show_mod->顯示或隱藏編修功能,1->顯示,0->隱藏
//$data->類別,等級,評語的編修選擇
//$add_one->1為延續評語,0反之
if(is_null($add_one))$add_one=1;

//編修評語
if($add_comment!='' and $send_comm=="確定執行")   mod_data();
//主要函式
$main=&list_comment($cq);

//秀出網頁
//head("成績輸入系統");
//print_menu($menu_p);
?>
<html>
<body>
<style>
body,td,input,select,textarea{font-size: 12px; }
</style>
<script language="JavaScript1.2">
function show(){
	var test=document.myform.add_comment.value;
	switch(test){
	case 1:temp='新增';
	case 2:temp='修改';
	case 3:temp='刪除';
	}
	var t=confirm("確定執行？");return t;
}

function helpme(){
	alert("【使用說明】\n1.新增方法：先選擇評語的類別和等級，再輸入你的評語，選擇新增，再按確定就完成了！\n2.修改方法：\n(1)更改評語：先選定一個評語，選擇修改評語，更改評語，再按確定就完成了！\n(2)更改評語的類別等級：先選定一個評語，選擇修改評語，選擇你要更改了類別或等級，不需按確定！\n3.刪除方法：先選定一個評語，選擇刪除評語，再按確定就完成了！");
	}
</script>
<?
echo $main."</body></html>";
/**********************************************************/
//foot();

//以下是函式
//秀出上層選單列表
function &list_comment($cq){
	global $CONN,$comm_length,$level,$comment,$comm,$show_mod,$data,$add_one,$teacher_id,$send_comm,$add_comment,$is_modify;
	$data_kind=array('','類別','等級','評語');
	$tmp_kind='';$tmp_level='';

	$sel="select * from comment_kind where kind_teacher_id='0' or kind_teacher_id='$teacher_id'";
	$comm_len=$CONN->Execute($sel);
	while(!$comm_len->EOF){
		$tmp_value=$comm_len->fields[0];
		$tmp_name=$comm_len->fields[2];
		$selected=($comm_length==$tmp_value)?"selected":"";
		$len.="<option value='$tmp_value' $selected>$tmp_name</option>\n";
		if($selected=='selected') $tmp_kind=$tmp_name;
		$comm_len->MoveNext();
	}
	$comm_length_select="類別：<select name='comm_length' onChange='submit()'>
	<option value=''>選擇類別</option>$len</select>";

	$sel="select * from comment_level where level_teacher_id='0' or level_teacher_id='$teacher_id'";
	$comm_lev=$CONN->Execute($sel);
	while(!$comm_lev->EOF){
		$tmp_value=$comm_lev->fields[0];
		$tmp_name=$comm_lev->fields[2];
		$selected=($level==$tmp_value)?"selected":"";
		$select.="<option value='$tmp_value' $selected>$tmp_name</option>\n";
		if($selected=='selected') $tmp_level=$tmp_name;
		$comm_lev->MoveNext();
	}
	$level_select="等級：<select name='level' onChange='submit()'>
	<option value=''></option>$select</select>";
	
	if($comment!='')	$point_of_kind=3;
	elseif($level!='') $point_of_kind=2;
	elseif($comm_length!='') $point_of_kind=1;
	for($k=1;$k<count($data_kind);$k++) {
	        $selected=($data==$k or $point_of_kind==$k)?"selected":"";
		$data_word.="<option value='$k' $selected>$data_kind[$k]</option>\n";
	}

	$sel="select serial,comm from comment where kind='$comm_length' and level='$level' and (teacher_id='0' or teacher_id='$teacher_id')";
	$comm_text=$CONN->Execute($sel);
	while(!$comm_text->EOF){
	        $c=(strlen($comm_text->fields[1])<=8)?$comm_text->fields[1]:substr($comm_text->fields[1],0,8)."...";
		$ser=$comm_text->fields[0];
		$selected=($comment==$comm_text->fields[0])?"selected":"";
		$comment_line.="<option value='$ser' $selected>$c</option>\n";
		$comm_text->MoveNext();
	}
	$comm_act=($add_one==0)?"1'>不延續評語":"0'>延續評語";
	$comment_select.="評語：<select name='comment' onChange='submit()'>
		<option value=''>選擇評語</option>$comment_line</select>  [狀態:
		<a href='{$_SERVER['PHP_SELF']}?comm_length=$comm_length&level=$level&comment=$comment
		&show_mod=$show_mod&cq=$cq&add_one=$comm_act</a>]\n";
	if ($is_modify == 'y'){
	$act=($show_mod==1)?'0\'>【回選單】':'1\'>【編輯詞庫】';
	$act_line="<a href='{$_SERVER['PHP_SELF']}?comm_length=$comm_length&level=$level&comment=$comment
		&comm=$comm&cq=$cq&add_one=$add_one&show_mod=$act</a>";
	}
	//<select name='data'><option value=' '>--種類--</option>$data_word</select>
	$selected1=($add_comment==1)?"selected":"";
	$selected2=($add_comment==2)?"selected":"";
	$selected3=($add_comment==3)?"selected":"";
	//判別是否為系統管理者
	$man_flag = checkid($_SERVER[SCRIPT_FILENAME],1) ;
		if ($man_flag)
			$temp  = "
            	<option value='1' $selected1>新增</option>
                <option value='2' $selected2>修改</option>
                <option value='3' $selected3>刪除</option>";
  	     else
  	         $temp = "
  	            <option value='1' $selected1>新增</option>";  	 
  	         $mod_line_sel=($show_mod==1)?"我要<select name='add_comment' >$temp</select><select name='data' onChange='submit()'><option value=''>？</option>$data_word</select>中的":"";

	$mod_line_act=($show_mod==1)?"<tr bgcolor='#E1ECFF'><td>
		<input type='reset' value='重設'>\n
		<input type='submit' name='send_comm' value='確定執行'></td></tr>":"";

	//$onsubmit=($show_mod==1)?"onsubmit='return show()'":"";
	$main="
	<img src='images/wizard.png' width='18' height='18' hspace='4' align='middle' border='0'>評語選填工具 $act_line<p>
	<table cellspacing=1 cellpadding=4 width=100%  bgcolor='#1E3B89'>
	<tr bgcolor='#E1ECFF'><td>
	<form name='myform' method='post' action='{$_SERVER['PHP_SELF']}' $onsubmit>$mod_line_sel";
	if($show_mod==0){
		$main.=$comm_length_select;
		if($comm_length!='')  $main.=$level_select;
		if($comm_length!='' and $level!='')   $main.=$comment_select;
		//$main.=$act_line;
	}
	else{
		//$main.="步驟 2：選擇編修資料 : ";
		$font="<font color=red size='2'>  ( 新增時可不選 )</font>";
		$font_comm="<font color=red size='2'>  ( 類別與等級一定要選 )</font>";
                if( $data==3 )  $main.=$comm_length_select.$level_select.$comment_select."<br>".$font_comm;
		elseif( $data==2 )  $main.=$level_select.$font;
		elseif( $data==1 ) $main.=$comm_length_select.$font;
		elseif( $point_of_kind==3)  $main.=$comm_length_select.$level_select.$comment_select."<br>".$font_comm;
		elseif( $point_of_kind==2)  $main.=$level_select.$font;
		elseif( $point_of_kind==1) $main.=$comm_length_select.$font;
		else    $main.='';
	}
	$main.="
	<input type='hidden' name='show_mod' value='$show_mod'>\n
		<input type='hidden' name='add_one' value='$add_one'>\n
		<input type='hidden' name='cq' value='$cq'>
		<textarea name='comm' cols='50' rows='3' wrap='soft' style='width:100%'>\n";

	$sel="select comm from comment where serial='$comment' and kind='$comm_length' and level='$level'";
	$sel_comment=$CONN->Execute($sel);
	$end=substr($comm,-2);
	if($comm!='' and $end!='。' and $end!='，' and $sel_comment->fields[0]!='' and $add_one==1)
		$sel_comment->fields[0]='，'.$sel_comment->fields[0];
	$word=($add_one==1)?$comm.$sel_comment->fields[0]:$sel_comment->fields[0];

	if($send_comm=='確定執行'||$send_comm_back=='確定')    $mainc.='';
	elseif($data==1)   $mainc.=$tmp_kind;
	elseif($data==2)   $mainc.=$tmp_level;
	else $mainc.=$word;

	$main.="$mainc</textarea>（100字以內）<font onclick='helpme()'>說明</font>\n
	$mod_line_act
	</form></table><form name='back' action='describe.php' method='post'><table><tr><td>	
	<input type='button' name='send_comm_back' value='確定' 
	onClick=\"window.opener.document.des_form.".$cq.".value='".$mainc."';window.opener.document.des_form.".$cq.".style.background = '#B6C9FD';setTimeout('window.close()',100);\">
	</td></tr></table></form></table>\n";
	return  $main;
}

//新增,修改資料
function mod_data(){
	global $CONN,$teacher_id,$comm_length,$level,$comment,$comm,$show_mod,$data,$add_one,$add_comment;
	if(empty($comm))return;
	if($data!=''){
		switch($add_comment){
		case 1:         //新增
		        switch($data){
			        case 1:
					$comm_ins="insert into comment_kind values('0','$teacher_id','$comm')";
					$rs=$CONN->Execute($comm_ins);
					if(!$rs)	print $rs->ErrorMsg();
					break;
				case 2:
					$comm_ins="insert into comment_level values('0','$teacher_id','$comm')";
					$rs=$CONN->Execute($comm_ins);
					if(!$rs)	print $rs->ErrorMsg();
					break;
				case 3:
					if($comm_length!='' and $level!=''){
						$comm_ins="insert into comment values('0','$teacher_id','','','$comm_length','$level','$comm')";
						$rs=$CONN->Execute($comm_ins);
						if(!$rs)	print $rs->ErrorMsg();
					}
					else echo "請選擇類別與等級！";
					break;
			}
			break;
		case 2:          //修改
			switch($data){
				case 1:
					if($comm_length!=''){
						$sel="select kind_teacher_id from comment_kind where kind_serial='$comm_length' and kind_teacher_id='$teacher_id'";
						$rs_sel=$CONN->Execute($sel);
						if($rs_sel){
							$comm_ins="update comment_kind set kind_name='$comm' where kind_serial='$comm_length'";
							$rs=$CONN->Execute($comm_ins);
							if(!$rs)   print $rs->ErrorMsg();
						}
					}
					else echo "請選擇類別！";
                                        break;
				case 2:
					if($level!=''){
						$sel="select level_teacher_id from comment_level where level_serial='$level' and level_teacher_id='$teacher_id'";
						$rs_sel=$CONN->Execute($sel);
						if($rs_sel){
							$comm_ins="update comment_level set level_name='$comm' where level_serial='$level'";
							$rs=$CONN->Execute($comm_ins);
							if(!$rs)   print $rs->ErrorMsg();
						}
					}
					else echo "請選擇等級！";
                                        break;
				case 3:
					if($comment!=''){
						$sel="select teacher_id from comment where serial='$comment' and teacher_id='$teacher_id'";
						$rs_sel=$CONN->Execute($sel);
						if($rs_sel){
							$comm_ins="update comment set kind='$comm_length',level='$level',comm='$comm' where serial='$comment'";
							$rs=$CONN->Execute($comm_ins);
							if(!$rs)   print $rs->ErrorMsg();
						}
					}
					else echo "請選擇要修改的評語！";
					break;
			}
			break;
		case 3:         //刪除
			switch($data){
				case 1:
					if($comm_length!=''){
						$sel="select kind_teacher_id from comment_kind where kind_serial='$comm_length' and kind_teacher_id='$teacher_id'";
						$rs_sel=$CONN->Execute($sel);
						if($rs_sel){
							$comm_del="delete from comment_kind where kind_serial='$comm_length'";
							$rs=$CONN->Execute($comm_del);
							if(!$rs)	print $rs->ErrorMsg(); 
						}
					}
					else echo "請選擇類別！";
					break;
				case 2:
					if($level!=''){
						$sel="select level_teacher_id from comment_level where level_serial='$level' and level_teacher_id='$teacher_id'";
						$rs_sel=$CONN->Execute($sel);
						if($rs_sel){
							$comm_del="delete from comment_level where level_serial='$level'";
							$rs=$CONN->Execute($comm_del);
							if(!$rs)	print $rs->ErrorMsg();
						}
					}
					else echo "請選擇等級！";
					break;
				case 3:
					if($comment!=''){ 
                                                $sel="select teacher_id from comment where serial='$comment' and teacher_id='$teacher_id'";
						$rs_sel=$CONN->Execute($sel);
						if($rs_sel){
							$comm_del="delete from comment where serial='$comment'";
							$rs=$CONN->Execute($comm_del);
							if(!$rs)	print $rs->ErrorMsg();
						}
					}
					else echo "請選擇要刪除的評語！";
					break;
			}
			break;
		}
	}
}
?>
