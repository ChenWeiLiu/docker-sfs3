<?php

// $Id: function.php 5310 2009-01-10 07:57:56Z hami $

/* 取得學務系統設定檔 */
include "../../include/config.php";

sfs_check();
$p_number=34;   //score_input and score_stu裡無學生資料時,預設學生人數
$exist="";              //點選班級科目成績時,若資料庫裡有資料(表示已新增此考試資料),$exist='true',反之$exist=""
$number=array(); //學生座號暫存陣列
$name=array();    //學生姓名暫存陣列
$score=array();   //學生成績暫存陣列
$mod=0;              //modify bit為1,代表此成績已送至教務處,不能修改
$class_score_sn=array();        //成績類別

if(empty($sel_year))$sel_year = curr_year(); //目前學年
if(empty($sel_seme))$sel_seme = curr_seme(); //目前學期

//$global_var陣列紀錄常用的外部變數
//$sel_year                 =       目前學年
//$sel_seme                =      目前學期
//$curr_class_year      =      班級
//$curr_class_name    =      年級
//$curr_subject         =         科目
//$curr_form            =          表格形式
//$send_to              =          送至教務處選單
//$score_kind         =          選擇單一成績或選擇全部成績
$global_var=array(
        $sel_year,$sel_seme,$curr_class_year,$curr_class_name,$curr_subject,$curr_form,$send_to,$score_kind);

//$score_mem主要用來紀錄未完成的成績單,ex:當輸入到一半時,發現輸入考試類別錯誤,需要轉換類別時
//已輸入的成績還會出現在新的成績類別
$score_mem=array();
for($i=1;$i<=$p_number;$i++)
	$score_mem[$i]=${"score_".$i};


//以下是函式
//秀出上層選單列表
function &list_class($global_var){
	global $CONN,$class_year,$class_name,$p_number,$score_mem;

	while(list($tkey,$tvalue)= each ($class_year)){
		$selected=($global_var[2]==$tkey)?"selected":"";
		$year_temp .= "<option value='$tkey' $selected>$tvalue</option>";
	}
	while(list($tkey,$tvalue)= each ($class_name)){
		$selected=($global_var[3]==$tkey)?"selected":"";
		$class_temp .= "<option value='$tkey' $selected>$tvalue</option>";
	}

	$str_select="select subject_id,subject_name from teacher_subject where subject_year=0 order by subject_id";
	$recordSet=$CONN->Execute($str_select);
	while (!$recordSet->EOF) {
		$subid = $recordSet->fields["subject_id"];
		$subname = $recordSet->fields["subject_name"];
		$selected=($subid==$global_var[4])?"selected":"";
		$sub_temp .= "<option value='$subid' $selected>$subname</option>\n";
  		$recordSet->MoveNext();
	}

	$forma_selected=($global_var[5]==1)?"selected":"";
	$formb_selected=($global_var[5]==2)?"selected":"";

	//
	$sel_kind=&show_exam_select($global_var);

	//相關功能表
	//$tool_bar=tool_bar($sel_year,$sel_seme);

	$main="
	<form action='{$_SERVER['PHP_SELF']}' method='post' name='myform' onsubmit='return check_score(".$p_number.")'>
 	<select name='curr_class_year' onChange='jumpMenu()'>
	<option value=''>年級</option>
	$year_temp
	</select>
	<select name='curr_class_name' onChange='jumpMenu()'>
	<option value='0'>班級</option>
	$class_temp
	</select>班
	<select name='curr_subject' onChange='jumpMenu()'>
	<option value='0'>科目</option>
	$sub_temp
	</select>科目
	$sel_kind
	<select name='curr_form' onChange='submit()'>
	<option value='0'>形式</option>
	<option value='1' $forma_selected>橫式</option>
	<option value='2' $formb_selected>直式</option>
	</select>形式<hr>
	<input type='text' name='practice_exam' size='10'>
	<input type='submit' name='add_practice' value='新增小考'>
	<input type='radio' name='send_to' value='1'>送至教務處
	<br>PS:沒有選取會將成績暫存,可供修改!若選取成績會送到教務處,不能修改! <hr>
	";
	return  $main;
}

//將list_class_table改成list_sub_table
//新增$curr_subject欄位,代表選擇科目
//此函數只秀出修改成績介面大概輪廓
function &list_stu_table($global_var){
	global $CONN,$act,$p_number,$exist,$mod;
	$main=&list_class($global_var);

	$main.="<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>";
	get_stu_score($global_var);

	if($global_var[5]==1)//選擇橫式
	        $main.=form_kind_h($global_var);
	else if($global_var[5]==2)//選擇直式
	        $main.=form_kind_v($global_var);

	$main.="</table>";

	//送出資料時($act=='true'表示已新增一班某科的考試成績),'送出成績' 按鈕變成'修改成績'
	//或在資料庫裡也找到資料($exist=='true'),表示之前已新增,秀出成績列表,此時按鈕為'修改成績' 可修改成績
	if($act=='true' or $exist=='true')	$value="修改成績";
	else $value="送出成績";

	//modify欄位為0時,秀出送出成績的按鈕,為1時不能修改資料
	//考試類別選擇'全部考試' 時也不能修改資料
	if($mod==0 and $global_var[7]!='all')
		$main.="<table border='0' cellspacing='1' cellpadding='4' bgcolor='#9EBCDD'>"
		."<tr bgcolor='#E1ECFF'><td><input type='hidden' name='act'>"
		."<input type='submit' name='send_score' value='$value' onfocus='keyEnter(".$p_number.")'>"
		."</td></tr>\n</table>";

	return  $main;
}

function get_stu_score($global_var){
	global $CONN,$number,$name,$score,$p_number,$exist,$class_score_sn,$mod;

	$c=1;                           //計算一班總考試次數
	$p_number_tmp=1;     //計算班人數
	$sn_pointer=0;  //計算有幾次考試成績

	if($global_var[7]!='all'){
		$sort=($global_var[7]<=3)?$global_var[7]:($global_var[7]-3);
		$type=($global_var[7]<=3)?"performance":"practice";

		//若資料庫裡有資料,儲存至$number,$name,$score陣列
		$str_sel="select b.number,b.name,b.score,a.modify from score_input a,score_stu b"
			." where a.year='$global_var[0]' and a.semester='$global_var[1]' and a.c_year='$global_var[2]'"
			." and a.c_num='$global_var[3]' and a.c_type='$global_var[4]' and a.score_kind='$type'"
			." and a.score_sort='$sort' and b.class_sn=a.class_sn";

		$recordSet=$CONN->Execute($str_sel);
		if(!$recordSet->EOF){
			$mod = $recordSet->fields["modify"];

			while (!$recordSet->EOF) {
				$number[$c] = $recordSet->fields["number"];
				$name[$c] = $recordSet->fields["name"];
				$score[$c] = $recordSet->fields["score"];
				$c++;
				$recordSet->MoveNext();
			}
			$p_number=$c-1;
			$exist='true';
		}
	}

	else{      //選擇全部
		$str_sel="select b.class_sn,b.number,b.name,b.score,a.score_kind,a.score_sort from score_input a,score_stu b"
			." where a.year='$global_var[0]' and a.semester='$global_var[1]' and a.c_year='$global_var[2]'"
			." and a.c_num='$global_var[3]' and a.c_type='$global_var[4]' and b.class_sn=a.class_sn"
			." order by b.class_sn,b.serial";

		$recordSet=$CONN->Execute($str_sel);
		$exam_time=count_exam_time($global_var);
		if(!$recordSet->EOF){
			while (!$recordSet->EOF) {
				$kind=$recordSet->fields["score_kind"];
				$sort=$recordSet->fields["score_sort"];

				if($class_score_sn[$sn_pointer]!=($recordSet->fields["class_sn"])){
					$e_k=($kind=='practice')?1:0;
					$e_n=$sort+$e_k*3;
					$sn_pointer++;
					$class_score_sn[$sn_pointer]=$e_n;
				}

				if($kind=="performance" and $sort==1){
					$number[$p_number_tmp] = $recordSet->fields["number"];
					$name[$p_number_tmp] = $recordSet->fields["name"];
					$score[$c] = $recordSet->fields["score"];
					$p_number_tmp++;$c++;
				}

				else{
					$score[$c] = $recordSet->fields["score"];
					$c++;
				}
				$recordSet->MoveNext();
			}
			$p_number=$p_number_tmp-1;
			$exist='true';
		}
	}
}

//秀出橫式成績表
function &form_kind_h($global_var){
        global $p_number,$score_mem,$number,$name,$score,$class_score_sn;

        $line_stu=10;	//預設每行列出十位學生
        $td_width=intval(700/$line_stu);	//表格寬度
        $exam_time=count_exam_time($global_var);           //總考試次數

        if(($p_number%$line_stu)!=0)	$add=1;//人數非整數,行數要加一

        $line=intval($p_number/$line_stu+$add);//有幾行

        for ($i=0;$i<$line;$i++){
	$line_number=($i==intval($p_number/$line_stu))?($p_number%$line_stu):$line_stu;//每行列出幾個學生

	//座號欄位
	$main.="<tr><td>"
	."<table border='0' width='100%' cellspacing='1' bgcolor='#9EBCDD'><tr bgcolor='#E1ECFF'>"
	."<td align='center' width='$td_width' class='css1'>座號</td>";
	for($j=1;$j<=$line_stu;$j++){
		$num=($i*$line_stu)+$j;//座號
		if($j>$line_number)
			$main.="<td width='$td_width'></td>";
		elseif($number[$num]!="")
			$main.="<td align='center' width='$td_width'>".$number[$num]."</td>";
		else
			$main.="<td align='center' width='$td_width'>$num</td>";
	}
	$main.="</tr>\n";

	//姓名欄位
	$main.="<tr bgcolor='#E1ECFF'><td align='center' width='$td_width' class='css1'>姓名</td>";
	for($j=1;$j<=$line_stu;$j++){
		$n_num=($i*$line_stu)+$j;//座號
		if($j>$line_number)
			$main.="<td width='$td_width'></td>";
		elseif($name[$n_num]!="")
			$main.="<td align='center' width='$td_width' class='name'>".$name[$n_num]."</td>";
		else
			$main.="<td align='center' width='$td_width' class='name'>name_".$n_num."</td>";
	}
	$main.="</tr>\n";

	//成績欄位
	if($global_var[7]!='all'){
	$main.="<tr bgcolor='#E1ECFF'><td align='center' width='$td_width' class='css1'>成績</td>";
	for($j=1;$j<=$line_stu;$j++){
		$num=($i*$line_stu)+$j;//成績編號
		if($j>$line_number)
			$main.="<td width='$td_width'></td>";
		elseif($score[$num]!=""){
			if($score[$num] < 60)      $score_color="ls";
			else if($score[$num]>100)       $score_color="ies";
			else    $score_color="hs";

			$main.="<td align='center' width='$td_width'>"
			."<input type='text' name='score_".$num."' maxlength='3' size='3' value='".$score[$num]."' class='$score_color'></td>";
		}
		else{
			$main.="<td align='center' width='$td_width'>"
			."<input type='text' name='score_".$num."' maxlength='3' size='3' ";
			//如果成績欄位有輸入資料,在改變形式時會紀錄輸入的成績
			if($score_mem[$num]!="")
				$main.="value=".$score_mem[$num];
			$main.="></td>";
		}
	}
	$main.="</tr></table><hr>\n";
	}
	else{
	$arr_count=0;
		for($score_line=0;$score_line<$exam_time;$score_line++){
			$sco=($score_line<3)?($score_line+1):($score_line-2);
			$sco_name=($score_line<3)?"定時":"平時";

			$p=0;
			$main.="<tr bgcolor='#E1ECFF'><td align='center' width='$td_width' class='css1'>"
			."<font size='1'>第".$sco."次".$sco_name."成績</font></td>";
			for($j=1;$j<=$line_stu;$j++){
			        $this_score=$score[($i*$line_stu)+$j+$arr_count*$p_number];
			        if($this_score < 60)      $score_color="ls";
			        else if($this_score >100)       $score_color="es";
			        else    $score_color="hs";

			        $p=($p==1)?1:0;
			        if($j>$line_number or $class_score_sn[$arr_count+1]!=($score_line+1))
			                $main.="<td width='$td_width'></td>";
			        else{
			                $main.="<td align='center' width='$td_width' class='$score_color'>"
			                .$this_score."</td>";
			                $p=1;
			        }
			}
			if($p==1) $arr_count++;
				$main.="</tr>\n";
		}
		$main.="</table><hr>\n";
	}
        }
        $main.="</td></tr>";
        return $main;
}

function count_exam_time($global_var){
	global $CONN;
	$str_sel="select class_sn from score_input"
		." where year='$global_var[0]' and semester='$global_var[1]' and c_year='$global_var[2]'"
		." and c_num='$global_var[3]' and c_type='$global_var[4]' order by class_sn";
	$recordSet=$CONN->Execute($str_sel);
	return $exam_time=$recordSet->RecordCount();
}

function &form_kind_v($global_var){
	global $CONN,$p_number,$score_mem,$number,$name,$score;

	$main.="<tr><td align='center' class='css1'>座號</td><td align='center' class='css1'>姓名</td><td align='center' class='css1'>成績</td></tr>\n";
	for($i=1;$i<=$p_number;$i++){
		if($number[$i]!=null){
			$main.="<tr bgcolor='#E1ECFF'><td align='center'>".$number[$i]."</td>"
			."<td align='center'>".$name[$i]."</td>"
			."<td align='center'>"
			."<input type='text' name='score_".$i."' maxlength='3' size='3' value='".$score[$i]."'>"
			."</td></tr>\n";
		}
		else{
			$main.="<tr bgcolor='#E1ECFF'><td align='center'>$i</td>"
			."<td align='center'>name_".$i."</td>"
			."<td align='center'>"
			."<input type='text' name='score_".$i."' maxlength='3' size='3' ";
				if($score_mem[$i]!="")
						$main.="value=".$score_mem[$i];
			$main.="></td></tr>\n";
		}
	}
	return $main;

}

//新增,修改資料
function insert_sql($global_var,$ins_or_up){
	global $CONN,$p_number,$score_mem;
	$up_input=update_score_input($global_var);

	if($up_input){
		$class_id=select_class_sn($global_var);

		if($class_id!=0){
		        if($ins_or_up=='ins'){
			for($i=1;$i<=$p_number;$i++){
				$name="name_".$i;
				$score=${"score_".$i};
				$sco_stu="insert into score_stu values('0','$class_id','$i','$name','$score_mem[$i]')";
				$rs_update=$CONN->Execute($sco_stu);
				if(!$rs_update)		print $rs_update->ErrorMsg();
			}
			echo "insert success";
		        }
		        else if($ins_or_up=='up'){
			for($i=1;$i<=$p_number;$i++){
			        $sco_stu="update score_stu set score='$score_mem[$i]' where class_sn='$class_id' and number='$i'";
			        $rs_update=$CONN->Execute($sco_stu);
			        if(!$rs_update)		print $rs_update->ErrorMsg();
			}
			echo "update success";
		        }
		}
		else echo "無此score_input->class_sn!";
	}
	else echo "score_input 修改失敗!";
}

function select_class_sn($global_var){
	global $CONN;

	$kind_type=(($global_var[7])<=3)?"performance":"practice";
	$kind_sort=($kind_type=="performance")?$global_var[7]:($global_var[7]-3);

              $tmp_str="select class_sn from score_input where year='$global_var[0]' and semester='$global_var[1]' and "
		." c_year='$global_var[2]' and c_num='$global_var[3]' and c_type='$global_var[4]'"
		." and score_kind='$kind_type' and score_sort='$kind_sort'";
	$rs_select_class_sn=$CONN->Execute($tmp_str);
	if($rs_select_class_sn){
		$id=$rs_select_class_sn->fields[0];
		return $id;
	}
	else{
		print $rs_select_class_sn->ErrorMsg();
		return false;
	}
}

function update_score_input($global_var){
	global $CONN;
	$class_id=select_class_sn($global_var);
	if($class_id){
		$str="update score_input set modify='$global_var[6]' where class_sn='$class_id'";
		$rs_update_input = $CONN->Execute($str);
		if(!$rs_update_input)	print $rs_update_input->ErrorMsg();
		else return true;
	}
	else return false;
}

function &show_exam_select($global_var){
        global $CONN;

        if($global_var[0] and $global_var[1] and $global_var[2] and $global_var[3] and $global_var[4] ){

	$kind_select="select score_kind,score_sort from score_input where year='$global_var[0]' and semester='$global_var[1]' and "
		." c_year='$global_var[2]' and c_num='$global_var[3]' and c_type='$global_var[4]' order by class_sn";
	$recordkind=$CONN->Execute($kind_select);

	if(!$recordkind->EOF){
		while (!$recordkind->EOF) {
			$kind_type = $recordkind->fields["score_kind"];
			$kind_sort = $recordkind->fields["score_sort"];
			if($kind_type=='performance'){
				$tmp=0;
				$exam_name='定期考';
			}else{
				$tmp=1;
				$exam_name='平時考';
			}
			$n=$tmp*3+$kind_sort;
			$selected=($n==$global_var[7])?"selected":"";
			$kind_tmp .= "<option value='$n' $selected>第".$kind_sort."次$exam_name</option>\n";
			$recordkind->MoveNext();
		}
		if($global_var[7]=='all')       $selected_all='selected';
		$kind_tmp .= "<option value='all' $selected_all>選擇全部考試</option>\n";

		return "<select name='score_kind' onChange='jumpMenu()'>
			<option value='0'>類別</option>
			$kind_tmp
			</select>考試類別";
	}
	else{
		for($a=1;$a<=2;$a++){
			if($a==1) $type='performance';  else $type='practice';
			for($i=1;$i<=3;$i++){
				$kind_select="insert into score_input values ('0','$global_var[0]','$global_var[1]','$global_var[2]'
				,'$global_var[3]','','$global_var[4]','普通班','$type','$i','$global_var[6]')";
				$recordinsert=$CONN->Execute($kind_select);
			}
			if($recordinsert)       echo "insert success";
		}
		$show_kind=&show_exam_select($global_var);
		return $show_kind;
	}
        }

        else{
	$str="<select name='score_kind' onChange='jumpMenu()'>
	<option value='0'>類別</option>\n";
	for($i=1;$i<=6;$i++){
		$n=($i<=3)?$i:($i-3);
		$selected=($i==$global_var[7])?"selected":"";
		$exam=($i<=3)?"定時考":"平時考";
		$str.="<option value='$i' $selected>第".$n."次$exam</option>\n";
	}
	$str.="<option value='all'>選擇全部考試</option></select>考試類別\n";
	return $str;
        }

}

function add_practice($global_var,$practice_exam){
	global $CONN;
	$select_practice="select score_sort from score_input where year='$global_var[0]' and"
	." semester='$global_var[1]' and c_year='$global_var[2]' and c_num='$global_var[3]'"
	." and c_type='$global_var[4]' and c_kind='普通班' and score_kind='practice' and score_sort='$practice_exam'";
	$recordsel=$CONN->Execute($select_practice);
	if(!$recordsel->EOF)
	        echo "此平時考已存在!";
	else{
		$insert_practice="insert into score_input values ('0','$global_var[0]','$global_var[1]','$global_var[2]'
		,'$global_var[3]','','$global_var[4]','普通班','practice','$practice_exam','$global_var[6]')";
		$recordinsert=$CONN->Execute($insert_practice);
		if(!$recordinsert)       echo "insert fail";
	}
}

function instruction(){
	echo "<table border='0' bgcolor='#ffff87' width='90%' align='center'>
                        <tr bgcolor='#e1ecff'><td><font size='2'>
		【使用說明】<br>
		1.依序選擇年級、班級、科目、形式後，如果此科目已登錄成績，會秀出未送至教務處的成績<br>
		2.成績輸入方法:<br>&nbsp;&nbsp;&nbsp;&nbsp;
		(1) 不可為文字<br> &nbsp;&nbsp;&nbsp;&nbsp;
                            (2) +或*代表100分<br>&nbsp;&nbsp;&nbsp;&nbsp;
                            (3) ?代表沒有來考試  <br>&nbsp;&nbsp;&nbsp;&nbsp;
                            (4) 在ie環境下，按Enter可自動跳至下一欄位，非ie環境下，
                            ex:Netscape 或 Mozilla 可使用Tab鍵<br>&nbsp;&nbsp;&nbsp;&nbsp;
                            (5) 送至教務處選單代表成績已確定不會在更改了!<br>
		3.切換表格形式時，已輸入的成績不會消失
                        </font></td></tr></table>";
}
?>
