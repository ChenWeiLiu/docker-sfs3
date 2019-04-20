<?php

$ys=$_GET['ys'];
if($ys)
{

include "config.php";

sfs_check();


//秀出網頁
head("$ys 學期社團紀錄列式");

if(checkid($_SERVER['SCRIPT_FILENAME'],1)){

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

if($_POST['act']=='刪除選取' and $_POST['sn']){
	$sn_list=implode(',',$_POST['sn']);
	
	//刪除
	$sql="DELETE FROM association WHERE club_sn=0 AND sn in ($sn_list)";
	$res=$CONN->Execute($sql) or user_error("刪除失敗！<br>$sql",256);
}

//抓取資料
$stud_select="SELECT a.*,b.curr_class_num,b.stud_name,b.stud_id FROM association a INNER JOIN stud_base b ON a.student_sn=b.student_sn WHERE a.seme_year_seme='$ys' AND a.club_sn=0 ORDER BY b.curr_class_num,a.seme_year_seme";
$rs=$CONN->Execute($stud_select) or user_error("讀取失敗！<br>$stud_select",256);
$data="<tr align='center' bgcolor='#ffdddd'><td>班級</td><td>座號</td><td>姓名</td><td>學號</td>
	<td>學期別</td><td>社團名稱</td><td>擔任職務</td><td>成績</td><td>指導教師評語</td><td>匯入日期</td>";
while(!$rs->EOF) {
	$class_id=substr($rs->fields['curr_class_num'],0,3);
	$class_num=substr($rs->fields['curr_class_num'],-2);
	$sn=$rs->fields['sn'];
	$data.="<tr align='center'><td>$class_id</td><td>$class_num</td><td>{$rs->fields['stud_name']}</td><td>{$rs->fields['stud_id']}</td>
			<td>{$rs->fields['seme_year_seme']}</td><td>{$rs->fields['association_name']}</td><td>{$rs->fields['stud_post']}</td><td>{$rs->fields['score']}</td><td>{$rs->fields['description']}</td><td>{$rs->fields['update_time']}</td>";
	$rs->MoveNext();
}
echo "<form name='myform' method='post' action='$_SERVER[SCRIPT_NAME]'>
		<table border='2' cellpadding='0' cellspacing='0' style='border-collapse: collapse; font-size:11pt' bordercolor='#111111' id='AutoNumber1' width='100%'>
		$data
		</table></form>";
} else echo "<br><br><br><p align='center'>具有模組管理權限者，方可進行操作！</p>";
foot();
}
?>