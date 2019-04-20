<?php 
// $Id: view.php 8744 2016-01-08 14:30:27Z qfon $
  include "config.php" ;

  $id = intval($_GET['id']) ;
  //加一次	 
  $tsqlstr =  " update $tbname set act_view = act_view+1 where act_ID='$id' " ; 	
  $result = $CONN->Execute( $tsqlstr) ; 
  
  //讀此筆記錄
  $sqlstr = " select * from $tbname where act_ID='$id' " ;
  $result =$CONN->Execute( $sqlstr) ; 	
  $nb=$result->FetchRow()  ;  
    

  
?>

<html>
<head>
<title><?php $nb[act_name] ?>相片</title>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<script language="JavaScript1.2">
<!--
if (document.all&&!window.print){
leftright.style.width=document.body.clientWidth-2
topdown.style.height=document.body.clientHeight-2
}
else if (document.layers){
document.leftright.clip.width=window.innerWidth
document.leftright.clip.height=1
document.topdown.clip.width=1
document.topdown.clip.height=window.innerHeight
}
function followmouse1(){
//move cross engine for IE 4+
leftright.style.pixelTop=document.body.scrollTop+event.clientY+1
topdown.style.pixelTop=document.body.scrollTop
if (event.clientX<document.body.clientWidth-2)
topdown.style.pixelLeft=document.body.scrollLeft+event.clientX+1
else
topdown.style.pixelLeft=document.body.clientWidth-2
}
function followmouse2(e){
//move cross engine for NS 4+
document.leftright.top=e.y+1
document.topdown.top=pageYOffset
document.topdown.left=e.x+1
}
if (document.all)
document.onmousemove=followmouse1
else if (document.layers){
window.captureEvents(Event.MOUSEMOVE)
window.onmousemove=followmouse2
}
function regenerate(){
window.location.reload()
}
function regenerate2(){
setTimeout("window.onresize=regenerate",400)
}
if ((document.all&&!window.print)||document.layers)
//if the user is using IE 4 or NS 4, both NOT IE 5+
window.onload=regenerate2
//-->
</script>
<style>
<!--
body {scrollbar-face-color : #FBB955 ; scrollbar-shadow-color : #FCD998 ; scrollbar-darkshadow-color : #FCD998 ; scrollbar-highlight-color : #FCD998 ; scrollbar-3dlight-color : #FCD998 ; scrollbar-track-color : #FDDFB3 ; scrollbar-arrow-color : white}

#leftright, #topdown{
position:absolute;
left:0;
top:0;
width:1px;
height:1px;
layer-background-color:#B0D0F8;
background-color:RED;
z-index:100;
font-size:1px;
}


-->
</style>
		
</head>
<body bgcolor="#EEEEEE"  style="scrollbar-3d-light-color:white;
scrollbar-arrow-color:royalblue;
scrollbar-base-color:#0066ff;
scrollbar-dark-shadow-color:#00ff00;
scrollbar-face-color:#66ccff;
scrollbar-highlight-color:blueviolet;
scrollbar-shadow-color:black ;
">

<div id="leftright" style="width:expression(document.body.clientWidth-2)">
</div>
<div id="topdown" style="height:expression(document.body.clientHeight-2)">
</div> 
		

<table width="95%" border="0" cellspacing="0" cellpadding="4" align="center" bordercolorlight="#00cc00" bordercolordark="#CCCCFF">
  <tr>
    <td><h1><?php echo $nb[act_name] ?></h1> <?php echo $nb[act_info] ?></td>
  </tr>
</table>
<table width="95%" border="0" cellspacing="0" cellpadding="4" align="center" bordercolorlight="#00CC00" bordercolordark="#CCCCFF">
  <tr>
<?php

  function get_smaill_list($updir){
     //取得小圖列表



   if ( is_dir($updir) ) {
 
     $dirs = dir($updir) ;
     $dirs ->rewind() ;
     while ( ($filelist = $dirs->read()) and !$stop_m) {
     	 if (($filelist!=".") && ($filelist!="..")){

     	   //windows      
     	   if (WIN_PHP_OS() ) {

     	       if ( (eregi("(.jpg|.jpeg|.png|.gif|.bmp)$", $filelist))  and !(strstr($filelist,'!!!_')) )
         	 $filelist_arr[] = $filelist ;
     	   }else {	
     	      //其他   	
         	if (strstr($filelist,'!!!_'))   	//縮小圖	
         	  $filelist_arr[] = $filelist ;
           }
         }
     }
     $dirs->close() ;  	
     sort ($filelist_arr) ;
     return $filelist_arr ;
   }
  }
  
//========================================================================  
     $picdir=$htmpath ."/" .$nb[act_dir] ;
     $updir=$savepath ."/" .$nb[act_dir] ;
     
     $act_dir =$nb[act_dir] ;

     //chdir($updir) ;
   $pic_i = 0 ;   
   $file_list = get_smaill_list($updir) ;
   foreach ($file_list as $k=>$v ) {

     	 if ($picnum >= 5) {
     	    echo "</tr><tr>" ;
     	    $picnum =0 ;
     	 }   
     	 if (WIN_PHP_OS() ) {
     	    //for WINDOWS    
         	     $big = $picdir."/" .$v ;
         	     $pic_file = $updir . "/" . $v ;
         	     list($width, $height, $type, $attr) = getimagesize( $pic_file);     
                     $w = $width/8;
                     $h = $height/8 ;   
         	     $url = "view_list.php?act_dir=$act_dir&id=$id&now_pic_id=$pic_i" ;
                 
                 echo "<td><a href=\"$url\" ><img src=\"$big\"  width=$w height=$h border=\"1\"></a></td>" ;
                 $picnum = $picnum +1 ;
                 $pic_i ++ ;
   
         }else {    
            // for Linux
         	     $big = substr($v,4);
         	     $filelist=$picdir."/" .$v ;
         	     $big=$picdir."/" . $big ;
         	     $url = "view_list.php?act_dir=$act_dir&id=$id&now_pic_id=$pic_i" ;
                 echo "<td><a href=\"$url\" ><img src=\"$filelist\"  border=\"1\"></a></td>" ;
                 $picnum = $picnum +1 ;
                 $pic_i ++ ;
         }     	    	
   	
   }	
   

     if ($picnum) echo "</tr>" ;
?>     
</table>
<p align="center"><a href="index.php">回上頁</a></p>
</body>
</html>