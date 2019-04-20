<?php
// $Id: board_show.php 7779 2013-11-20 16:09:00Z smallduh $
// --系統設定檔
ini_set('memory_limit', '-1');
include	"config.php";
include_once "../../include/sfs_case_dataarray.php";

//echo $_GET['api_key'].";".$_GET['act'];;

if ($_GET['api_key']!=$api_key) {
  $row[1]="API Key 錯誤!";
}
//測試
if ($_GET['act']=='test') {
	$row="恭喜! 連線成功!";
  $row=base64_encode(addslashes($row));
  //$row[1]="ok!";
}

//取得令日的圖id值
if ($_GET['act']=='GetDayPicID') {
  $kind_id=intval($_GET['kind_id']);  //指定分類區
  $day=date("m-d");
  //取得預設圖片與今日圖片
  $sql="select init_pic_set,day_pic_set from jshow_setup where kind_id='$kind_id'";
  $res=$CONN->Execute($sql);
  $init_pic_set=$res->fields['init_pic_set'];
  $day_pic_set=$res->fields['day_pic_set'];
  $day_pic_set=unserialize($day_pic_set);
  
  //取得
  if ($day_pic_set[$day]==0) {
   if ($init_pic_set==0) {
   	 $row=0;
   } else {
     $row=$init_pic_set;  //以預設值取代
   }
  } else {
   $row=$day_pic_set[$day];
  }
}

//取得某kind_id的所有圖id
if ($_GET['act']=='GetPicByKindID') {
  $kind_id=intval($_GET['kind_id']);  //指定分類區
  //取得預設圖片與今日圖片
  $sql="select * from jshow_pic where kind_id='$kind_id' and display='1' order by sort";
  if ($_GET['visible']>0) $sql.=" limit ".$_GET['visible']; 
  $res=$CONN->Execute($sql);
  $ROW=$res->GetRows();
  //轉碼
  $row=array();
  foreach ($ROW as $k=>$v) {
        $row[$k]=array_base64_encode($v);
  }
}

//取得某kind_id的所有圖id依隨機
if ($_GET['act']=='GetPicByKindIDorderByRand') {
  $kind_id=intval($_GET['kind_id']);  //指定分類區
  //若有指定必選圖片
  if ($_GET['visible_must']!="") {
    $must="and id not in (".$_GET['visible_must'].")";
    //取得必選圖片
    $sql="select * from jshow_pic where kind_id='$kind_id' and display='1' and id in (".$_GET['visible_must'].") order by sort";
  	$res=$CONN->Execute($sql);
  	$ROW_MUST=$res->GetRows();
  } else {
    $must="";
  }
  
  //取得預設圖片與今日圖片
  $sql="select * from jshow_pic where kind_id='$kind_id' and display='1' ".$must." order by RAND()";
  if ($_GET['visible']>0) $sql.=" limit ".$_GET['visible']; 
  $res=$CONN->Execute($sql);
  $ROW=$res->GetRows();
  //轉碼
  $row=array();
  $i=0;
  foreach ($ROW_MUST as $k=>$v) {
        $i++;
        $row[$i]=array_base64_encode($v);
  }
  foreach ($ROW as $k=>$v) {
        $i++;
        $row[$i]=array_base64_encode($v);
  }
}


//取得kind_id的設定值
if ($_GET['act']=='GetSetup') {
  $kind_id=intval($_GET['kind_id']);  //指定分類區
  
  //取得預設圖片與今日圖片
  $sql="select * from jshow_setup where kind_id='$kind_id'";
  $res=$CONN->Execute($sql);
  
  $ROW=$res->fetchRow();
  
  $row=array_base64_encode($ROW);
  
}


//讀取圖 , 不用轉碼
if ($_GET['act']=='GetImage' and $_GET['id']!="") {	
	$id=intval($_GET['id']);
	$query="select * from jshow_pic where id='".$id."'";
	$res=$CONN->Execute($query) or die($query);
	$row= $res->fetchRow();	
	$filename=$row['filename'];
	
	//讀取圖檔
	 $sFP=fopen($USR_DESTINATION.$filename,"r");							//載入檔案
   $sFilesize=filesize($USR_DESTINATION.$filename); 				//檔案大小
   $sFiletype=filetype($USR_DESTINATION.$filename);  				//檔案屬性
       		
  //轉碼 
   $sFile=addslashes(fread($sFP,$sFilesize));
   $sFile=base64_encode($sFile);
   
   fclose($sFP);
	
	//傳遞的資料
	 $row['content']=$sFile;
	 $row['filetype']=$sFiletype;
}


//計數加1
if ($_GET['act']=='Url_Click') {
  $id=intval($_GET['id']);  //指定分類區
  
  //取得預設圖片與今日圖片
  $sql="select url,url_click from jshow_pic where id='$id'";
  $res=$CONN->Execute($sql);
  $url_click=$res->fields['url_click'];
  $row=$res->fields['url'];   //傳出的 url
  
  $url_click+=1;
  
  $sql="update jshow_pic set url_click='$url_click' where id='$id'";
  $res=$CONN->Execute($sql); 
  
}



//送出
exit(json_encode($row));

//將陣列編碼
function array_base64_encode($arr) {
  $B_arr=array();
  foreach ($arr as $k=>$v) {
    $B_arr[$k]=base64_encode(addslashes($v));
  }
  	return $B_arr;
}

