<?php
//$Id: parent_get.php 7711 2013-10-23 13:07:37Z smallduh $
include "config.php";
include "function.php";

$class_name_arr = class_base() ;	//班級陣列
        
//取得內容：
          
        

if ($_POST[Submit] == "送出") {
    
     if ($_POST[data]<>"") 
        $put_data = $_POST[data] ;
     
     $name_array = preg_split("/[\n\s,]+/",$put_data) ;


     for ($i = 0 ; $i < count($name_array); $i++ ) {
         if (trim($name_array[$i])<>"") {
           
            $stud_id_array = Get_parent_name2(trim($name_array[$i])) ;
            if (count($stud_id_array) >0)  {
            	for ($j = 0 ; $j < count($stud_id_array) ; $j++) {
            	    $csvdata .=  Get_stud_data($stud_id_array[$j]) ."\n" ;
            	}    
            }	
         	
         }		
     	
     }	
 
	//以串流方式送出 data.csv
	header("Content-disposition: attachment; filename=data.xls");
	header("Content-type: application/octetstream");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
     echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=big5\">\n" ;
     echo "<table border=1><tr><td>姓名</td><td>學號</td><td>生日</td><td>身份証字號</td><td>班級</td><td>座號</td><td>性別</td><td>電話</td><td>緊急電話</td><td>地址</td><td>監護人</td><td>父親</td><td>父親行動</td><td>母親</td></tr> \n" ;
     echo  $csvdata ;
     echo "</table>" ;
	exit;
	return;     

        
}        




//認證
sfs_check();

//秀出網頁布景標頭
head("以父母姓名查詢");

print_menu($school_menu_p) ;

//主要內容
$main="
<form action='' method='post' enctype='multipart/form-data' name='form1'>
  <table width='80%'  border='1'>

    <tr>
      <td><p>名單：</p>
        <p class='style1'>
          (以空格或逗號或分行輸入父母的姓名)<br>
          範例：<br>
          王小明,陳小扁 <br>
          
      </p>      </td>
      <td><p>
        <textarea name='data' cols='40' rows='10'></textarea> 
        </p>        </td>
    </tr>
    <tr>
      <td colspan='2'><div align='center'>
        <input type='submit' name='Submit' value='送出'>
      </div></td>
    </tr>
    <tr bgcolor='#CCCCCC'>
      <td>說明：</td>
      <td>只輸入姓名，取得完成個人資料，會轉匯出成csv格式。</td>
    </tr>    
  </table>
";
echo $main;

//佈景結尾
foot();

?>
