<?php

// $Id: chk.php 5453 2009-04-17 03:07:33Z brucelyc $

// 取得設定檔
include "config.php";

sfs_check();

//取導師任教班級
$class_num=get_teach_class();
$class_id=sprintf("%03d_%d_%02d_%02d",curr_year(),curr_seme(),substr($class_num,-3,strlen($class_num)-2),substr($class_num,-2));
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());

if($_POST['act']=='列印'){
	$selected_stud=$_POST['selected_stud'];
	$item_px=$_POST['item_px'];
	$sign_h=$_POST['sign_h'];
	$title=$_POST['title'];
	//換頁html碼
	$newpage="<P style='page-break-after:always'></P>";
	$stud_count=count($selected_stud);
	if($stud_count){
		$stud_count--;
		$seme_year_seme =sprintf("%03d",curr_year()).curr_seme();
		$sel_year=curr_year();
		$sel_seme=curr_seme();

		//將class_id轉為class_num
		$class_id_arr=explode('_',$class_id);
		$class_num=($class_id_arr[2]+0).$class_id_arr[3];

		//轉換班級代碼
		$class=class_id_2_old($class_id);
		$class_name=$class[5];
		
		//檢核表項目
		$itemdata=get_chk_item($sel_year,$sel_seme);
		
		foreach($selected_stud as $counter=>$sn_value) {			
			//取得指定學生資料
			$stu=get_stud_base($sn_value,"");

			$stud_id=$stu['stud_id'];
			$stud_name=$stu['stud_name'];
			$curr_class_num=substr($stu['curr_class_num'],-2);
				
			//檢核表值
			$chk_item=chk_kind();
			$chk_value=get_chk_value($sn_value,$sel_year,$sel_seme,$chk_item,"value");
	
			//其他表現文字
			$query="select * from stud_seme_score_nor where seme_year_seme='$seme_year_seme' and student_sn='$sn_value' order by ss_id";
			$res=$CONN->Execute($query);
			$r=array();
			while(!$res->EOF) {
				$r[$res->fields['ss_id']]=$res->fields['ss_score_memo'];
				$res->MoveNext();
			}
			$nor_memo=$r;

			//開始產生HTML資料
			$chk_data.="<p align='center'><font size=5>$school_long_name<BR>$sel_year 學年度第 $sel_seme 學期日常生活表現檢核表</font></p>";
			$chk_data.="<table align='center' cellspacing='4'><tr>
						<td>班級：<font color='blue'>$class_name</font></td><td width='40'></td>
						<td>座號：<font color='green'>$curr_class_num</font></td><td width='40'></td>
						<td>姓名：<font color='red'>$stud_name</font></td>
						</tr></table></font>";
			$chk_data.="<table  STYLE='font-size: ".$item_px."px' border=2 cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolorlight='#000000' bordercolordark='#000000' width='100%'>
						<tr bgcolor='#FFCCCC'><td colspan='2' align='center'>日常生活檢核項目</td><td align='center'>表現狀況</td><td align='center'>備註</td></tr>";
			
			//重整資料為二維陣列
			$data_array=array();			
			foreach($itemdata['items'] as $key=>$value) {
				$main=$value['main'];
				$sub=$value['sub'];
				$data_array[$main][$sub]=$value['item'];
			}
			//詳式檢核項目情形列表
			foreach($data_array as $key=>$main) {
				$rowspan=count($main)-1;
				$chk_data.="<tr><td rowspan=$rowspan align='center'>".$main[0]."</td>";
				for($i=1;$i<=$rowspan;$i++){
					$chk_data.="<td>".$main[$i]."</td>";
					$chk_data.="<td align='center' width='120'>".$chk_value[$key][$i]['score']."</td><td>".$chk_value[$key][$i]['memo']."</td></tr>";					
				}
			}
			//行為描述
			$chk_data.="<table border=2 cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolorlight='#000000' bordercolordark='#000000' width='100%'>
						<tr><td rowspan=4 align='center' bgcolor='#c4d9ff' width=80>行為描述<BR>與<BR>具體建議</td>
						<td align='center' bgcolor='#c4d9ff' width=80>日常生活</td><td>$nor_memo[0]</td></tr>
						<tr><td align='center' bgcolor='#c4d9ff' width=80>團體活動</td><td>$nor_memo[1]</td></tr>
						<tr><td align='center' bgcolor='#c4d9ff' width=80>公共服務</td><td>※校內: $nor_memo[2]<br>※社區: $nor_memo[3]</td></tr>
						<tr><td align='center' bgcolor='#c4d9ff' width=80>特殊表現</td><td>※校內: $nor_memo[4] <br>※校外: $nor_memo[5]</td></tr>
						</table>";
			//簽章處理
			$chk_data.="<table border=2 cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolorlight='#000000' bordercolordark='#000000' width='100%'>
						<tr align='center' bgcolor=#FFAAAA><td>導師</td><td>$title</td><td>校長</td></tr><tr height=$sign_h><td></td><td></td><td></td></tr></table>";
			//換頁
			if($counter<$stud_count) $chk_data.=$newpage;
		}
		echo $chk_data;
	} else $chk_data="您並未選取任何學生！";
	
	exit;
}

//秀出網頁
head("列印班級學生詳式日常生活檢核表");

echo <<<HERE
<script>
function tagall(status) {
  var i =0;

  while (i < document.myform.elements.length)  {
    if (document.myform.elements[i].name=='selected_stud[]') {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}
</script>
HERE;

echo print_menu($school_menu_p);

$main="<table border='1' cellpadding='3' cellspacing='0' style='border-collapse: collapse' bordercolor='#AAAAAA' width='100%'>
		<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]' target='_BLANK'>";

//取得stud_base中班級學生列表並據以與前sql對照後顯示
$stud_select="SELECT a.student_sn,a.seme_num,b.stud_name,b.stud_sex FROM stud_seme a,stud_base b WHERE seme_year_seme='$curr_year_seme' and a.seme_class='$class_num' and a.student_sn=b.student_sn ORDER BY seme_num";
$recordSet=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
//以checkbox呈現
$col=7; //設定每一列顯示幾人
$studentdata="";
while(list($student_sn,$seme_num,$stud_name,$stud_sex)=$recordSet->FetchRow()) {
	if($recordSet->currentrow() % $col==1) $studentdata.="<tr>";
	if (array_key_exists($student_sn,$listed)) {
			$studentdata.="<td bgcolor=".($listed[$recordSet->fields[student_sn]-1]?"#CCCCCC":"#FFFFDD").">▲($seme_num)$stud_name</td>";
	} else {
		$studentdata.="<td bgcolor=".($stud_sex==1?"#CCFFCC":"#FFCCCC")."><input type='checkbox' name='selected_stud[]' value='$student_sn' id='stud_selected'>($seme_num)$stud_name</td>";
	}
	if($recordSet->currentrow() % $col==0  or $recordSet->EOF) $studentdata.="</tr>";
}
$studentdata.="<tr height='50'><td align='center' colspan=$col>				
				<input type='button' name='all_stud' value='全選' onClick='javascript:tagall(1);'>
				<input type='button' name='clear_stud'  value='全不選' onClick='javascript:tagall(0);'>
				◎檢核項目字體大小：<input type='text' name='item_px' value=12 size=2>px 
				◎處室主任抬頭：<input type='radio' value='教導主任' name='title'>教導 <input type='radio' value='訓導主任' name='title'>訓導 <input type='radio' value='學務主任' name='title' checked>學務  
				◎簽章列高：<input type='text' name='sign_h' value=60 size=2> 
				<input type='submit' value='列印' name='act'></td></tr>";

echo $main.$studentdata."</form></table>";

?>
