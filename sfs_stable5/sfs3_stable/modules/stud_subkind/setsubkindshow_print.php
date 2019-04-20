<?php
// $Id: setsubkind.php 8973 2016-09-12 08:14:48Z infodaes $

include_once "config.php";
//include_once "../../include/sfs_case_dataarray.php";
sfs_check();

$SCRIPT_FILENAME = $_SERVER['SCRIPT_FILENAME'];

 //取得教師所上年級、班級
$session_tea_sn = $_SESSION['session_tea_sn'] ;
$query =" select class_num  from teacher_post  where teacher_sn  ='$session_tea_sn'  ";
$result =  $CONN->Execute($query) or user_error("讀取失敗！<br>$query",256) ;
$row = $result->FetchRow() ; 
$class_num = $row["class_num"];

if( checkid($SCRIPT_FILENAME,1) OR $class_num) {

     //設定方法 
      $setmethod=($_POST[setbycombo]);
   if($setmethod) $setmethod='checked';

    $POST_op = $_POST[op];
     echo $POST_op; 

    //取得學生身份列表
     $type_select="SELECT d_id,t_name FROM sfs_text WHERE t_kind='stud_kind' AND d_id>0 order by t_order_id";
     $recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
     //$All_stud_kind_sql 所有學生身份別總代號表 
     $All_stud_kind_sql="( ";
     $All_type_id_sql="( ";
     $type_id_total = 0;     
       while (list($d_id,$t_name)=$recordSet->FetchRow()) {
		   $type_id_total++;
		   $type_id_d_id[$d_id] = $d_id.$t_name;
            if ($type_id==$d_id) {
                  $typedata.="<option value='$d_id' selected>($d_id)$t_name</option>";
             } else { 
                 $typedata.="<option value='$d_id'>($d_id)$t_name</option>";
             }           
     $All_stud_kind_sql.=" stud_kind like '%,$d_id,%' OR";
     $All_type_id_sql.=" type_id='$d_id' OR";
        }
     
     $All_stud_kind_sql.=" stud_kind like '%,$type_id,%'  )";
     $All_type_id_sql.=" type_id='$type_id' )";
  
     //學期別
      $curr_year_seme = sprintf("%03d%d",curr_year(),curr_seme());
     
     //取得學生子身份類別清單資料
      //$type_select="SELECT * FROM stud_subkind_ref WHERE type_id='$type_id'";
      $type_select="SELECT * FROM stud_subkind_ref WHERE ". $All_type_id_sql;
      $recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
      while($sunkind_data=$recordSet->FetchRow()) {
      $type_id_title=$sunkind_data[type_id];
      $clan_title[$type_id_title]=$sunkind_data[clan_title];
      $area_title[$type_id_title]=$sunkind_data[area_title];
      $memo_title[$type_id_title]=$sunkind_data[memo_title];
      $note_title[$type_id_title]=$sunkind_data[note_title];
      $ext1_title[$type_id_title]=$sunkind_data[ext1_title];
      $ext2_title[$type_id_title]=$sunkind_data[ext2_title];

      $clan_list[$type_id_title]=explode("\r\n",$sunkind_data[clan]);
      $area_list[$type_id_title]=explode("\r\n",$sunkind_data[area]);
      $memo_list[$type_id_title]=explode("\r\n",$sunkind_data[memo]);
      $note_list[$type_id_title]=explode("\r\n",$sunkind_data[note]);
      $ext1_list[$type_id_title]=explode("\r\n",$sunkind_data[ext1]);
      $ext2_list[$type_id_title]=explode("\r\n",$sunkind_data[ext2]);
      
      }
   
      // 取出班級陣列
       $class_base = class_base($work_year_seme);
       
     //取得類別 學生資料
      //改成分兩階段擷取資料

     //第一階段----取出stud_base合乎資格學生
      $type_select="SELECT student_sn,left(curr_class_num,length(curr_class_num)-2) as class_id,curr_class_num,stud_id,stud_name 
                               FROM stud_base 
                               WHERE stud_study_cond=0 
                               AND ".$All_stud_kind_sql." ";
      $type_select.=(!checkid($SCRIPT_FILENAME,1) AND $class_num<>'')?" AND curr_class_num like '$class_num%'":"";
      $type_select.=" ORDER BY curr_class_num";
      $recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);
      $row_count=$recordSet->RecordCount(); 
      if($row_count<>0)  {
       //將student_sn轉成陣列字串
         $select_sn='';
           while ($data=$recordSet->FetchRow()) {
               $select_sn.=$data['student_sn'].",";
               $stud_data[$data['student_sn']]['class_id']=$data['class_id'];
               $stud_data[$data['student_sn']]['curr_class_num']=$data['curr_class_num'];
               $stud_data[$data['student_sn']]['stud_id']=$data['stud_id'];
               $stud_data[$data['student_sn']]['stud_name']=$data['stud_name'];
               $stud_class_id[$data['class_id']] = $data['class_id'];
            }
           $select_sn=substr($select_sn,0,-1);
           //$stud_class_id_unique = array_unique($stud_class_id);
           sort($stud_class_id);
           $count_stud_class_id = count($stud_class_id);
 
           //第二階段----取出stud_subkind紀錄
           $type_select="SELECT * FROM stud_subkind WHERE student_sn IN ($select_sn) AND " ;
           $type_select.=$All_type_id_sql;
          $recordSet=$CONN->Execute($type_select) or user_error("讀取失敗！<br>$type_select",256);

          //將資料加入$stud_data陣列後
           while ($data=$recordSet->FetchRow()) {
			    $stud_subkind_type_id = $data['type_id'];
                //echo  $stud_subkind_type_id;
                $stud_data[$data['student_sn']]['clan']=$type_id_d_id[$stud_subkind_type_id].":".$clan_title[$stud_subkind_type_id].":".$data['clan'];
                $stud_data[$data['student_sn']]['area']=$area_title[$stud_subkind_type_id].":".$data['area'];
                $stud_data[$data['student_sn']]['memo']=$memo_title[$stud_subkind_type_id].":".$data['memo'];
                $stud_data[$data['student_sn']]['note']=$note_title[$stud_subkind_type_id].":".$data['note'];
		        $stud_data[$data['student_sn']]['ext1']=$ext1_title[$stud_subkind_type_id].":".$data['ext1'];
		        $stud_data[$data['student_sn']]['ext2']=$ext2_title[$stud_subkind_type_id].":".$data['ext2'];
          }
        }
      //是否開放導師可以自由輸入屬性
       $m_arr = get_sfs_module_set("stud_subkind");
       if($class_num) {  if($m_arr['free_input']='Y') $free_input=''; else $free_input='disabled'; }
                     $sn_data=explode(',',$select_sn);
                   for($ki=0;$ki< $count_stud_class_id;$ki++) {
					    $listdata.="<table width='100%' border=1 cellspacing='1' cellpadding='1' bordercolor='#CCCCFF' style='page-break-before:always;'>
                                <tr bgcolor='#CCCCFF'>
                                  <td>NO.</td>
                                  <td>年班座號</td>
                                  <td>學號</td>
                                  <td>姓名</td>
                                  <td>欄位01</td>
                                  <td>欄位02</td>
                                  <td>欄位03</td>
                                  <td>欄位04</td>
			                      <td>欄位05</td>
			                      <td>欄位06</td>
                                </tr>";
                                $kj=0;  
                     for($k=0;$k<$row_count;$k++)   {
                         $class_id=$stud_data[($sn_data[$k])]['class_id'];
                         $class_name=$class_base[$class_id];               
                         if ($class_id ==$stud_class_id[$ki]) {
                         $listdata.="<tr>
                                               <td width='2%' >".($kj+1)."</td>
                                               <td width='5%' >".$stud_data[$sn_data[$k]][curr_class_num]."</td>
                                               <td width='3%' >".$stud_data[$sn_data[$k]][stud_id]."</td>
                                               <td width='5%' >".$stud_data[$sn_data[$k]][stud_name]."</td>
                                               <td width='20%' >".$stud_data[($sn_data[$k])][clan]."</td>
                                               <td width='15%' >".$stud_data[($sn_data[$k])][area]."</td>
                                               <td width='17%' >".$stud_data[($sn_data[$k])][memo]."</td>
                                               <td width='14%' >".$stud_data[($sn_data[$k])][note]."</td>
                                               <td width='3%' >".$stud_data[($sn_data[$k])][ext1]."</td>
                                               <td width='3%' >".$stud_data[($sn_data[$k])][ext2]."</td>
                                             </tr>";     
                                             $kj++; 
                           }                                                               
                     }
                      $listdata.="</table>
                                              <br>"; 
                   }                       
                                             
} else { echo "<h2><center><BR><BR><font color=#FF0000>您並未被授權使用此模組(非導師或模組管理員)</font></center></h2>"; } 
 
?>
<html>
<title>班級總報表</title>
<body onload='window.print()'>
<?php
if(empty($listdata)) $listdata = "無任何資料";
echo $listdata;
?>
</body>
</html>

