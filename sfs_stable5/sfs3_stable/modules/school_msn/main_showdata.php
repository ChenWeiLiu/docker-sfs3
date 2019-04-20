<?php
header('Content-type: text/html; charset=utf-8');
include_once ('config.php');
include_once ('my_functions.php');

$ann_num=-1;

 mysql_query("SET NAMES 'utf8'");
   //計算有效公告, 內部公告
   $nowsec=date("U",mktime(0,0,0,date("n"),date("j"),date("Y")));
   $nowdate=date("Y-m-d 0:0:0");
   $query="select idnumber,data,data_kind from sc_msn_data where to_id='' and to_days(curdate())<=(to_days(post_date)+last_date) and (data_kind=0 or data_kind=2) order by post_date desc";
   $result=mysql_query($query);
   $board_num=mysql_num_rows($result);
   while($row=mysql_fetch_row($result)) {
   	 $ann_num++;
    list($idnumber,$data,$data_kind)=$row;
    $ann_data[$ann_num]=nl2br($data);
    
     $query_file="select filename,filename_r from sc_msn_file where idnumber='".$idnumber."'";
  	$result_file=mysql_query($query_file);
	  	if (mysql_num_rows($result_file)) {
       $ann_data[$ann_num].="<font size=5 color=#000000>--《校園MSN/檔案下載》</font>";
	  	} else {
	  	 $ann_data[$ann_num].="<font size=5 color=#000000>--《校園MSN/校內訊息》</font>";
	  	}
    
   
   }


//讀取近日公告 , 儲存在 $ann_data 陣列 , $ann_num 變數表公告數
$BOARD_P=$SOURCE."_p";
$BOARD_KIND=$SOURCE."_kind";
$CONN->Execute("SET NAMES 'latin1'");
$query="select a.* from $BOARD_P a,$BOARD_KIND b where to_days(a.b_open_date)+$LAST_DAYS > to_days(curdate()) and a.bk_id = b.bk_id order by a.b_open_date desc ,a.b_post_time desc ";
$res=$CONN->Execute($query) or die("Error! query=".$query);
if ($res->RecordCount()>0) {
 while ($row=$res->FetchRow()) {
  $ann_num++;
//$ann_data[$ann_num]=big52utf8($row['b_open_date']." ".$row['b_sub']." <font size=1>(".$row['b_unit']."_".$row['b_title'].")</font>");
 $ann_data[$ann_num]=big52utf8(addslashes($row['b_sub']))."<font size=5 color=#000000>--《".$row['b_open_date'].",首頁/".big52utf8($row['b_unit'])."/".big52utf8($row['b_title'])."公告》</font>";
 }
} else {
 $ann_num=0;
 $ann_data[0]="近期內無新公告!";
}




//從首頁截取公告 第一頁

//取出 sfs3資料
 mysql_query("SET NAMES 'utf8'");

   $query="select data from sc_msn_data where to_id='' and to_days(curdate())<=(to_days(post_date)+last_date) and data_kind=3 order by post_date desc";
   $result=mysql_query($query);
   while($row=mysql_fetch_row($result)) {
    list($data)=$row;
    $ann_data[$ann_num]=$data;
    $ann_num++;
   }
   




$cc[0]="#FFFFFF";
$cc[1]="#FFFF00";
$cc[2]="#00FFFF";
$cc[3]="#00FF00";

   $board_num=$ann_num;
?>
<html>
<head>
<META HTTP-EQUIV="Expires" CONTENT="Fri, Jan 01 1900 00:00:00 GMT">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<title>訊息</title>
<style>
A:link {font-size:9pt;color:#ff0000; text-decoration: none}
A:visited {font-size:9pt;color: #ff0000; text-decoration: none;}
A:hover {font-size:9pt;color: #ffff00; text-decoration: underline}
.ann{overflow:hidden;height:750px;color:#0000FF;font-size:56pt;font-family:標楷體} 
</style>
</head>
<body bgcolor="#f9f7b3" leftmargin="3" topmargin="0" style="overflow: hidden">
<div id="ann_box" class="ann" style="width:100%;"> 
	<?php
	for ($ii=0;$ii<$ann_num;$ii++) {
	?>
		  <div id="a1" class="ann"><?php echo $ann_data[$ii];?></div>
	<?php
	}
	?>
</div> 

</body>
</html>
<Script language="JavaScript">
	function reloading() {
		//window.location.reload();
	  window.location.href='main_showpic.php';
	}
	
	function slideLine(box,stf,delay,speed,h) 
	{   //取得id
		   var allelement=<?php echo $ann_num-1;?>;
		   //內容共要往上翻頁幾次 , 為總內容數-1
		   var start_ok=1;
		   var slideBox = document.getElementById(box);
		      //預設值 delay:幾毫秒滾動一次(1000毫秒=1秒)   
		      //       speed:數字越小越快，h:高度
		   var delay = delay||10000,speed = speed||20,h = h||750;
		   var tid = null,pause = false;
		      //setInterval跟setTimeout的用法可以咕狗研究一下~
		   var s = function(){tid=setInterval(slide, speed);}
		      //主要動作的地方
		   var slide = function(){
		      //當滑鼠移到上面的時候就會暫停
		        //if(pause) return;
		      //滾動條往下滾動 數字越大會越快但是看起來越不連貫，所以這邊用1
		        slideBox.scrollTop += 5;
		      //滾動到一個高度(h)的時候就停止
		        if(slideBox.scrollTop%h == 0){
		      //跟setInterval搭配使用的
		          clearInterval(tid);
		      //將剛剛滾動上去的前一項加回到整列的最後一項
		          slideBox.appendChild(slideBox.getElementsByTagName(stf)[0]);
		      //再重設滾動條到最上面
		          slideBox.scrollTop = 0;
		      //延遲多久再執行一次
		          setTimeout(s, delay);
		          start_ok++;
		           if (start_ok>=allelement) { 
		           	setTimeout("reloading()",15000); 
		           }
		          } //end if 
		        }   //滑鼠移上去會暫停 移走會繼續動
		          slideBox.onmouseover=function(){pause=true;}
		          slideBox.onmouseout=function(){pause=false;}
		          //起始的地方，沒有這個就不會動囉
		          setTimeout(s, delay); 
	}
		
		           //網頁load完會執行一次 
		           //五個屬性各別是：外面div的id名稱、包在裡面的標籤類型 
		           //延遲毫秒數、速度、高度

		          slideLine('ann_box','div',6000,20,750); 

</Script>

 