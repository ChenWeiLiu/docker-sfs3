<?php

// �N���ɱq��Ʈw���X��q�X
// �o�q�{�����ϥΤ覡�� <img src="img_show.php">
// �o�ˤ~�����Φb��L�����W
// �{���}�l
include_once('board_config.php');
$name = $_GET['name'];
$b_id = intval($_GET['b_id']);

///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
if ($name <> "") {
    $stmt = $mysqliconn->prepare("select filetype,content from jboard_images where b_id='$b_id' and filename=?");
    $stmt->bind_param('s', $name);
}
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($filetype, $picture);
$stmt->fetch();
$stmt->close();
///mysqli

/*
  $query="select filetype,content from jboard_images where b_id='".$b_id."' and filename='".$name."'";
  $res=$CONN->Execute($query);
  $filetype=$res->fields['filetype'];
  $picture=$res->fields['content'];
 */

Header("Content-type: $filetype");
//Header("Content-type: images/gif");
// �бN��Ʈw�����Ϥ���쪺��ƨ��X�� $picture �ܼ�
$picture = stripslashes(base64_decode($picture));
echo $picture;
// �{������
?>