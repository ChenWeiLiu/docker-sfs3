<?php

include "config.php";

sfs_check();

//秀出網頁
head("歷屆畢業生名冊");
print_menu($menu_p);

//if(checkid($_SERVER['SCRIPT_FILENAME'],1)){

echo <<<HERE
<script>

function tagall(status,s) {
  var i =0;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].name==s) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}


function check_select() {
  var i=0; j=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].checked) {
		if(document.myform.elements[i].name=='sn[]') j++;
    }
    i++;
  }
  
  if(j==0) { alert("尚未選取任何的紀錄！"); answer=false; }
  
  return answer;
}

</script>
HERE;
/*
if($_POST['act']=='刪除選取' and $_POST['sn']){
	$sn_list=implode(',',$_POST['sn']);
	
	//刪除
	$sql="DELETE FROM association WHERE club_sn=0 AND sn in ($sn_list)";
	$res=$CONN->Execute($sql) or user_error("刪除失敗！<br>$sql",256);
}
*/
//台師大心測中心要的學校代碼
$school_id =  $SCHOOL_BASE['sch_id'];
//抓取畢業年度列表
$grad_year_radio='畢業年度：';
$sql="SELECT DISTINCT stud_grad_year FROM grad_stud";
$rs=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
while(!$rs->EOF) {
	$grad_year=$rs->fields['stud_grad_year'];
	$checked=($grad_year==$_POST[grad_year])?'checked':'';
	$grad_year_radio.="<input type='radio' name='grad_year' value='$grad_year' $checked onclick=\"this.form.submit();\">$grad_year ";
	$rs->MoveNext();
}


if($_POST['grad_year']) {
	$grad_kind=array(1=>'畢業',2=>'修業');
	//抓取資料
	$i=0;
	$stud_select="SELECT a.*,b.curr_class_num,b.stud_name,b.stud_person_id,b.stud_addr_1,b.stud_addr_2,b.stud_tel_1,b.stud_tel_2,b.enroll_school FROM grad_stud a LEFT JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.stud_grad_year='{$_POST[grad_year]}' ORDER BY b.curr_class_num"; //a.class_year,a.class_sort,a.grad_kind,a.grad_num
        $rs=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
        $data="<tr align='center' bgcolor='#ffdddd'><td>NO.</td><td>班級</td><td>座號</td><td>學號</td><td>學校代碼</td><td>姓名</td><td>身分證字號</td><td>性別</td><td>類別</td><td>證書號</td><td>畢業成績</td><td>入學學校</td><td>升學學校</td><td>戶籍地址</td><td>戶籍電話</td><td>聯絡地址</td><td>聯絡電話</td>";
        while(!$rs->EOF) {
                $i++;
                $person_id=($rs->fields['stud_person_id']);
                $sex = substr($person_id,1,1);
                $class_id=substr($rs->fields['curr_class_num'],0,3);
                $class_num=substr($rs->fields['curr_class_num'],-2);
                $nature=$grad_kind[$rs->fields['grad_kind']];
                $bgcolor=($rs->fields['grad_kind']==1)?'#ffffff':'#dddddd';
                $data.="<tr align='center' bgcolor='$bgcolor'><td>$i</td><td>$class_id</td><td>$class_num</td><td>{$rs->fields['stud_id']}</td><td>$school_id</td><td>{$rs->fields['stud_name']}</td><td>$person_id</td><td>$sex</td><td>$nature</td>
                                <td>{$rs->fields['grad_num']}</td><td>{$rs->fields['grad_score']}</td><td>{$rs->fields['enroll_school']}</td><td>{$rs->fields['new_school']}</td>
                                <td align='left'>{$rs->fields['stud_addr_1']}</td><td>{$rs->fields['stud_tel_1']}</td><td align='left'>{$rs->fields['stud_addr_2']}</td><td>{$rs->fields['stud_tel_2']}</td>";
                $rs->MoveNext();
	}
}
echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>$grad_year_radio
		<table border='2' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-size:9pt' bordercolor='#111111' id='AutoNumber1' width='100%'>
		$data
		</table></form>";
//} else echo "<br><br><br><p align='center'>具有模組管理權限者，方可進行操作！</p>";
foot();
?>