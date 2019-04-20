<?php
include "config.php";

sfs_check();

//秀出網頁
head("報名身分");
print_menu($menu_p);

//學期別
$work_year_seme=$_REQUEST['work_year_seme'];
if($work_year_seme=='') $work_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
$academic_year=substr($curr_year_seme,0,-1);
$work_year=substr($work_year_seme,0,-1);
$session_tea_sn=$_SESSION['session_tea_sn'];

$stud_class=$_REQUEST['stud_class'];
$selected_stud=$_POST['selected_stud'];
$edit_sn=$_POST['edit_sn'];

$show_zero=$_POST['show_zero']?'checked':'';

if($_POST['act']=='取消') { $edit_sn=0;	$_POST['batch']=''; }

if($_POST['act']=='修改'){
	$sql="UPDATE 12basic_tcntc SET kind_id='{$_POST[kind_id]}',disability_id='{$_POST[disability_id]}',free_id='{$_POST[free_id]}',id_memo='{$_POST[id_memo]}',language_certified='{$_POST[language_id]}' WHERE academic_year=$work_year AND student_sn=$edit_sn AND editable='1'";
	$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
	$edit_sn=0;	
}

if($_POST['act']=='批次更新'){
	foreach($_POST['batch'] as $student_sn=>$data) {
		$sql="UPDATE 12basic_tcntc SET kind_id='{$data[kind_id]}',disability_id='{$data[disability_id]}',free_id='{$data[free_id]}',id_memo='{$data[id_memo]}',language_certified='{$data[language_id]}' WHERE academic_year=$work_year AND student_sn=$student_sn AND editable='1'";
		$res=$CONN->Execute($sql) or user_error("更新失敗！<br>$sql",256);
	}
	$edit_sn=0;
	$_POST['batch']='';
}

//橫向選單標籤
$linkstr="work_year_seme=$work_year_seme&stud_class=$stud_class";
echo print_menu($MENU_P,$linkstr);

//取得年度與學期的下拉選單
$recent_semester=get_recent_semester_select('work_year_seme',$work_year_seme);

//顯示班級
$class_list=get_semester_graduate_select('stud_class',$work_year_seme,$graduate_year,$stud_class);

//if($work_year==$academic_year) $tool_icon.="<font size=1>◎出現手指型鼠標時，可快按兩下可進行修改◎</font>";
$tool_icon="<input type='checkbox' name='show_zero' value=1 $show_zero onclick=\"this.form.submit();\"><font size=2 color='green'>顯示「(0)一般生」</font>";
$main="<form name='myform' method='post' action='$_SERVER[PHP_SELF]'><input type='hidden' name='batch' value=''><input type='hidden' name='edit_sn' value='$edit_sn'>$recent_semester $class_list $tool_icon 
<table border='2' cellpadding='3' cellspacing='0' border-collapse: collapse' bordercolor='#111111' id='AutoNumber1' width=100%>";

if($stud_class)
{
	//取得學生身份列表
	$kinddata=array();
	$type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";
	$recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
	while(list($d_id,$t_name)=$recordSet->FetchRow()) {
		$kinddata[$d_id]=$t_name;
	}
	
	//抓取既有身份對照表
	$sql="SELECT kind_data,disability_data,free_data FROM 12basic_kind WHERE year_seme='$work_year_seme'";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	$kind_data=unserialize($res->fields[0]);
	$disability_data=unserialize($res->fields[1]);
	$free_data=unserialize($res->fields[2]);

	//取得指定學年已經開列的學生清單
	$student_list_array=get_student_list($work_year);

	//檢查是否有可修改紀錄的參與免試學生
	$editable_sn_array=get_editable_sn($work_year);

	//取得指定學年已經開列的學生身分	
	$id_array=get_student_id($work_year);

	//取得stud_base中班級學生列表並據以與前sql對照後顯示
	$stud_select="SELECT a.student_sn,a.seme_class,a.seme_num,b.stud_name,b.stud_sex,b.stud_id,b.stud_kind,b.stud_study_year,b.stud_kind FROM stud_seme a inner join stud_base b on a.student_sn=b.student_sn WHERE a.seme_year_seme='$work_year_seme' AND a.seme_class='$stud_class' AND b.stud_study_cond in (0,5,15) ORDER BY a.seme_num";
	$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
	//假使是當年度便可以批次編修
	if($work_year==$academic_year and !$_POST['batch']) $java_script="onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='#ff8888';\" ondblclick='document.myform.batch.value=\"1\"; document.myform.submit();'";
	$studentdata="<tr align='center' bgcolor='#ff8888' $java_script><td width=80>學號</td><td width=50>班級</td><td width=50>座號</td><td width=120>姓名</td><td width=$pic_width>大頭照</td><td>SFS3內註記的身份類別</td><td>報名身份</td><td>族語認證</td><td>身心障礙</td><td>低收失業</td><td>備註</td>";
	while(list($student_sn,$seme_class,$seme_num,$stud_name,$stud_sex,$stud_id,$stud_kind,$stud_study_year,$stud_kind)=$recordSet->FetchRow()) {
		//有特殊身分別的才列出
		$stud_kind=substr(str_replace(',0,','',$stud_kind),0,-1);
		$my_kind_arr=explode(',',$stud_kind);
				
		$my_pic=$pic_checked?get_pic($stud_study_year,$stud_id):'';
		$seme_num=sprintf('%02d',$seme_num);
		$stud_sex_color=($stud_sex==1)?"#CCFFCC":"#FFDDDD";
		$my_kind_id=$id_array[$student_sn]['kind_id'];
			$my_kind="($my_kind_id){$stud_kind_arr[$my_kind_id]}";
			if(!$show_zero and !$my_kind_id) $my_kind='';
		$my_disability_id=$id_array[$student_sn]['disability_id'];
			$my_disability="($my_disability_id){$stud_disability_arr[$my_disability_id]}";
			if(!$show_zero and !$my_disability_id) $my_disability='';
		$my_free_id=$id_array[$student_sn]['free_id'];
			$my_free="($my_free_id){$stud_free_arr[$my_free_id]}";
			if(!$show_zero and !$my_free_id) $my_free='';
		//***
		$my_language_certified=$id_array[$student_sn]['language_certified']?'是':'';
		//***
/*
		$kind_id=$stud_kind_arr[$my_kind_id];
		$disability_id=$stud_disability_arr[$my_disability_id];
		$free_id=$stud_free_arr[$my_free_id];
*/
		$id_memo=$id_array[$student_sn]['id_memo'];
		$action='';		
		$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
		$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
		$java_script='';
		
		$kind_id_data='';
		foreach($my_kind_arr as $id){
			if($id){
				$color='#aaaaaa';
				if($kind_data[$id]) $color='#0000ff'; elseif($disability_data[$id]) $color='#ff0000'; elseif($free_data[$id]) $color='#aa00aa';
				$kind_id_data.="<li><font color='$color'>($id)$kinddata[$id]</font></li>";
			}
		}
		
		//批次編修
		if($_POST['batch']){
			if(array_key_exists($student_sn,$editable_sn_array) and array_key_exists($student_sn,$student_list_array)){
				//產生對應的報名身分select元件
				$my_kind="<select name='batch[$student_sn][kind_id]'>";
				foreach($stud_kind_arr as $kind_key=>$kind_value){
					$selected='';
					$bg_color='';
					if($kind_key==$my_kind_id){
						$selected='selected';
						$bg_color="style='background-color: #ffcccc;'";
					}
					$my_kind.="<option value='$kind_key' $selected $bg_color>($kind_key) $kind_value</option>";
				}
				$my_kind.="</select>";	

				//產生族語認證select元件
				$selected=$my_language_certified?'selected':'';
				$my_language_certified="<select name='batch[$student_sn][language_id]'><option value='0' selected></option><option value='1' $selected>是</option></select>";

				//產生對應的身心障礙select元件
				$my_disability="<select name='batch[$student_sn][disability_id]'>";
				foreach($stud_disability_arr as $disability_key=>$disability_value){
					$selected='';
					$bg_color='';
					if($disability_key==$my_disability_id){
						$selected='selected';
						$bg_color="style='background-color: #ffcccc;'";
					}
					$my_disability.="<option value='$disability_key' $selected $bg_color>($disability_key) $disability_value</option>";
				}
				$my_disability.="</select>";	
				
				//產生對應的低收失業select元件
				$my_free="<select name='batch[$student_sn][free_id]'>";
				foreach($stud_free_arr as $free_key=>$free_value){
					$selected='';
					$bg_color='';
					if($free_key==$my_free_id){
						$selected='selected';
						$bg_color="style='background-color: #ffcccc;'";
					}
					$my_free.="<option value='$free_key' $selected $bg_color>($free_key) $free_value</option>";
				}
				$my_free.="</select>";

				
				//產生備註欄
				$id_memo="<input type='text' size=10 name='batch[$student_sn][id_memo]' value='$id_memo'";
			}			
		} else {
			if($student_sn==$edit_sn){
				//產生對應的報名身分select元件
				$my_kind="<select name='kind_id'>";
				foreach($stud_kind_arr as $kind_key=>$kind_value){
					$selected='';
					$bg_color='';
					if($kind_key==$my_kind_id){
						$selected='selected';
						$bg_color="style='background-color: #ffcccc;'";
					}
					$my_kind.="<option value='$kind_key' $selected $bg_color>($kind_key) $kind_value</option>";
				}
				$my_kind.="</select>";
				
				//產生族語認證select元件
				$selected=$my_language_certified?'selected':'';
				$my_language_certified="<select name='language_id'><option value='0' selected></option><option value='1' $selected>是</option></select>";
				
				

				//產生對應的身心障礙select元件
				$my_disability="<select name='disability_id'>";
				foreach($stud_disability_arr as $disability_key=>$disability_value){
					$selected='';
					$bg_color='';
					if($disability_key==$my_disability_id){
						$selected='selected';
						$bg_color="style='background-color: #ffcccc;'";
					}
					$my_disability.="<option value='$disability_key' $selected $bg_color>($disability_key) $disability_value</option>";
				}
				$my_disability.="</select>";	
				
				//產生對應的低收失業select元件
				$my_free="<select name='free_id'>";
				foreach($stud_free_arr as $free_key=>$free_value){
					$selected='';
					$bg_color='';
					if($free_key==$my_free_id){
						$selected='selected';
						$bg_color="style='background-color: #ffcccc;'";
					}
					$my_free.="<option value='$free_key' $selected $bg_color>($free_key) $free_value</option>";
				}
				$my_free.="</select>";
				
				//產生備註欄
				$id_memo="<input type='text' size=10 name='id_memo' value='$id_memo'";

				//動作按鈕
				$action="<br><br><input type='submit' name='act' value='修改' onclick='return confirm(\"確定要修改 $stud_name 的報名身分資料?\")'> <input type='submit' name='act' value='取消' onclick='document.myform.edit_sn.value=0;'>";		
				$stud_sex_color='#ffffaa';
			} else {		
				if(array_key_exists($student_sn,$student_list_array)){
					$editable=array_key_exists($student_sn,$editable_sn_array)?1:0;
					$stud_sex_color=$editable?$stud_sex_color:$uneditable_bgcolor;
					$java_script=($work_year==$academic_year and $editable and $kind_editable)?"onMouseOver=\"this.style.cursor='hand'; this.style.backgroundColor='#aaaaff';\" onMouseOut=\"this.style.backgroundColor='$stud_sex_color';\" ondblclick='document.myform.edit_sn.value=\"$student_sn\"; document.myform.submit();'":'';
				} else { $stud_sex_color='#aaaaaa'; }
			}
		}
		$stud_sex_color=array_key_exists($student_sn,$student_list_array)?$stud_sex_color:'#aaaaaa';		
		$studentdata.="<tr align='center' bgcolor='$stud_sex_color' $java_script><td>$stud_id</td><td>$seme_class</td><td>$seme_num</td><td>$stud_name</td><td>$my_pic</td><td align='left'>$kind_id_data</td><td align='left'><font color='#0000ff'>$my_kind</font></td><td align='center'>$my_language_certified</td><td align='left'><font color='#ff0000'>$my_disability</font></td><td align='left'><font color='#aa00aa'>$my_free</font></td><td align='left'>$id_memo $action</td></tr>";
	}
	if($_POST['batch']) $studentdata.="<tr align='center'><td colspan=10><input type='submit' name='act' value='批次更新' onclick='return confirm(\"確定要修改本班學生所有的身分資料?\")'> <input type='submit' name='act' value='取消'></td></tr>";
}

//顯示封存狀態資訊
echo get_sealed_status($work_year).'<br>';

echo $main.$studentdata."</form></table>";
foot();
?>