<?php	
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//���o�]�w��
include_once "config.php";
//���ҬO�_�n�J
sfs_check(); 
//�s�@��� ( $school_menu_p�}�C�]�w�� module-cfg.php )
$tool_bar=&make_menu($school_menu_p);
//Ū���ثe�ާ@���Ѯv���S���޲z�v , �f�t module-cfg.php �̪��]�w
$module_manager=checkid($_SERVER['SCRIPT_FILENAME'],1);

/**************** �}�l�q�X���� ******************/
//�q�X SFS3 ���D
head();
//�C�X���
echo $tool_bar;


$sql="show tables";
$res=$CONN->Execute($sql) or die("Error! sql=".$sql);
$row=$res->getRows();
?>
<table border="1" width="100%">
 <tr>
  <td>��ƪ�W��</td>
  <td>�w�]�@�X</td>
  <td>��lCreate SQL</td>
  <td>�Ҧ����</td>
  <td>�ഫUTF8 Create SQL</td>
 </tr>
<?php
foreach ($row as $v) {
 $table_name=$v[0];    //��ƪ�W�{
 $sql_table="SHOW TABLE STATUS WHERE NAME LIKE '$table_name';";
       $res_create=$CONN->Execute($sql_table);
       $row_table=$res_create->getRows();
       $sql_create="SHOW CREATE TABLE $table_name";
       $res_create=$CONN->Execute($sql_create);
       $row_create=$res_create->getRows();

       ?>
    <tr>
     <td><?= $table_name?></td>
     <td><?php
        echo $row_table[0]['Collation'];
       //foreach ($row_table as $k=>$vv) {
        //echo "<pre>";
        //print_r($vv);
      //print_r($row_table);
        //echo "</pre>";
       //}

      ?>
     <?php // $row_table['Collation']; ?>
     </td>

     <td>
      <?php
      echo $row_create[0][1];
      $C=explode("_",$row_table[0]['Collation']);
      $CHAR=$C[0];  //�s�X
      /*
        echo "<pre>";
      print_r($row_create);
      echo "</pre>";
      */
      ?>
     </td>
     <td>
      <?php
       $columns=getColumn($table_name);
      echo "<pre>";
      print_r($columns);
      echo "</pre>";
      ?>
     </td>
     <td>
      <?php
      $O=explode("ENGINE=",$row_create[0][1]);
      if ($CHAR=='utf8') {
          $O[0]=str_replace("utf8_","utf8mb4_",$O[0]);
      }
      $new_create_sql=$O[0]." ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC";
      echo $new_create_sql;
      ?>
     </td>
    </tr>
    <?php
 }
 ?>
</table>



?>

 
<?php
//  --�{���ɧ�
foot();

function getColumn($table) {
 global $CONN;
 $sql = "show columns FROM `$table`";
 $res=$CONN->Execute($sql);
 $row=$res->getRows();
 $columns=array();
 foreach ($row as $v)  {
     if (!in_array($v['Field'],$columns)) $columns[]=$v['Field'];
 }
 return $columns;


}
?>