<?php
//把教師的 E-mail欄位全部填入 帳號＠mail.fnjh.tc.edu.tw
include_once ('config.php');

$query="select teacher_sn,teach_id from teacher_base where teach_condition=0";
$result=$CONN->Execute($query);
while ($row=$result->FetchRow()) {
  $teacher_sn=$row['teacher_sn'];
  $teach_id=$row['teach_id'];
  $email=$teach_id."@mail.fnjh.tc.edu.tw";
  
  $sql="select teacher_sn from teacher_connect where teacher_sn='$teacher_sn'";
  $res=$CONN->Execute($sql) or die ("Error! query=".$sql);;
  
  if ($res->RecordCount()>0) {
   $sql="update teacher_connect set email='$email' where teacher_sn='$teacher_sn'";
   $res=$CONN->Execute($sql);
   echo "修改".$teach_id."=>".$email."<br>";
  } else {
   $sql="insert into teacher_connect (teacher_sn,email) values ('$teacher_sn','$email')";
   $res=$CONN->Execute($sql) or die ("Error! query=".$sql);
   echo "新增".$teach_id."=>".$email."<br>";
  } 
  

}


?>