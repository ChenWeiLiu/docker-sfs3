<?php
//如果為中 心端, 設定 COOKIE
if ($_GET['sch_id']) {
 $sch_id = $_GET['sch_id'];
 setcookie("cookie_sch_id","$sch_id",0,"/","");
}
include_once ('config.php');


?>
<html>
<head>
<meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>校園MSN</title>
</head>
<body onLoad="gowindow()">

</body>
<Script language='JavaScript'>
	 
	 window.focus();
	 
    function gowindow(){    //在載入時改變視窗大小
       reSize(390,560);         //設定為 1024*768 大小
    }
    function reSize(x,y){
        XX=screen.availWidth
        YY=screen.availHeight
        PX=XX-x; 
        PY=YY-y
        MX=(XX-x)/2;
        MY=(YY-y)/2;
        
        top.resizeTo(x,y);   // 利用 resizeto 改變視窗大小
        <?php
        if ($POSITION=="") $POSITION=0;
        switch ($POSITION) {
          case 0:  //右上
        		echo "top.moveTo(PX,0);   //移動到右上角";
          break;

          case 1:  //左上
        	  echo "top.moveTo(0,0);   //移動到右上角";
          break;

          case 2:  //正中
        	  echo "top.moveTo(MX,MY);   //移動到正中";
          break;

          case 3:  //右下
        	  echo "top.moveTo(PX,PY);   //移動到右下角";          
          break;
        	
          case 4:  //左下
        	  echo "top.moveTo(0,PY);   //移動到左下角";          
          break;
        }

        ?>        
         window.location.href='main_window.php';
 		}

</Script>
</html>
