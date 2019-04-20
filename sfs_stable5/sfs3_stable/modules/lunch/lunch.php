<?php

// $Id: lunch.php 8821 2016-02-22 01:15:31Z infodaes $

//************************************ 
//  午餐食譜公佈
//  by 林朝敏
// 林朝敏的半點心工作坊
//  http://sy3es.tnc.edu.tw/~prolin
//  89/9/17   ver:0.3
//************************************

require "config.php" ;

$begdate=$_GET['begdate'];
$PHP_SELF = basename($_SERVER['PHP_SELF']) ;
$module_name=basename(dirname(__FILE__));
$url_base=$UPLOAD_URL.$module_name.'/';
$base=$UPLOAD_PATH.$module_name.'/';
$today_year = date("Y")-1911;


//今天星期幾，取得周一日期   
$mday = date("w" );

if ($mday>0) {
	$weekfirst = GetdayAdd(date("Y-m-d"),($mday-1)*-1);
} else {
	$weekfirst = GetdayAdd(date("Y-m-d"),1);
}

if ($begdate) {    //在本周及下周
   $mday = date(  "w" ,StrToDate($begdate));
   $begdate = GetdayAdd($begdate,($mday-1)*-1);   	//指定日期當周星期一日期
}   
  
if ($begdate == 0)   $begdate = $weekfirst ;		//未指定，指向這一周
$enddate = GetdayAdd($begdate ,$WEEK_DAYS-1); 	 //這周五日期 
  

if ($_GET["m"]) {   //為月份
  $nextweek = GetMonthAdd($begdate , 1);   //下月
  $prevweek = GetMonthAdd($begdate , -1);	 //上月
  $linknext = "<a href='$PHP_SELF?begdate=$nextweek&m=1'><img src='./images/next.png' width=12 border=0 alt='次月' title='次月'></a>";
  $linknow = "<a href='$PHP_SELF?begdate=$weekfirst&m=1'><img src='./images/now.png' width=12 border=0 alt='本月' title='本月'></a>";
  $linkprev = "<a href='$PHP_SELF?begdate=$prevweek&m=1'><img src='./images/prev.png' width=12 border=0 alt='上月' title='上月'></a>";
  
} else {
  $nextweek = GetdayAdd($begdate , 7);   //下周一
  $prevweek = GetdayAdd($begdate , -7);	 //前一周	
  $linknext = "<a href='$PHP_SELF?begdate=$nextweek'><img src='./images/next.png' width=12 border=0 alt='下一週' title='下一週'></a>" ;
  $linknow = "<a href='$PHP_SELF?begdate=$weekfirst'><img src='./images/now.png' width=12 border=0 alt='本週' title='本週'></a>";
  $linkprev = "<a href='$PHP_SELF?begdate=$prevweek'><img src='./images/prev.png' width=12 border=0 alt='前一週' title='前一週'></a>" ;
}
  
//是否有獨立的界面
if ($IS_STANDALONE)
	include "header.php";
else
	head("午餐食譜");
   
if ($_GET["m"]) {
	$mode="<a href='$PHP_SELF?m=0'><img src='./images/week.png' width=16 border=0 alt='切換至週顯示模式' title='切換至週顯示模式'></a>";
	$mday = substr($begdate,0,7);
	$filter = "WHERE pDate like '$mday%'";
} else {
	$mode.="<a href='$PHP_SELF?m=1'><img src='./images/month.png' width=16 border=0 alt='切換至月顯示模式' title='切換至月顯示模式'></a>";
	$filter = "WHERE pDate between '$begdate' and '$enddate'";
	$mday=DtoCh($begdate)." ~ ".DtoCh($enddate);
}

//$title = "<center>$show_week_str</center>";
$title="<table style='font-size: $font_size;' width=100%><tr><td align='right'>◎日期：<font color='blue'>$mday</font> 　 $linkprev $linknow $linknext 　 $mode</td></tr></table>";
echo $title ;

//抓取已開列的廠商
$sqlstr= "SELECT DISTINCT pDesign FROM lunchtb $filter";
$rs = $CONN->Execute($sqlstr);
$DESIGN=array();
while(!$rs->EOF) {
	$DESIGN[]=$rs->fields[0];
	$rs->MoveNext();
}

if (count($DESIGN)<=1 ) //適合舊版、單一菜單設計
   show_lunch_table() ;
else {   		//兩家以上
   for($j=0; $j<count($DESIGN); $j++){ 
      show_lunch_table($DESIGN[$j]) ;	
   }	
}

function show_lunch_table($DESIGN="") {
	global $begdate,$enddate, $WEEK_DAYS ,$WEEK_DATE ,$CONN, $base,$url_base,$linkprev,$linknow,$linknext,$font_size,$column_bgcolor_m,$column_bgcolor_w;
	if($_GET["m"]) { //月份
		$mday = substr($begdate,0,7);
		$sqlstr = "SELECT * FROM lunchtb WHERE pDate like '$mday%'" ;   	
		if($DESIGN) $sqlstr .= " and pDesign='$DESIGN'" ;
		$sqlstr .= " order by pDate " ;

		$result = $CONN->Execute($sqlstr) ;
		if($result) {
			while ($nb=$result->FetchRow()) {
				$md = $nb[pMday];			//取得星期幾

				$pMenu= nl2br($nb[pMenu]);	//菜單
				$pNutri= nl2br($nb[pNutrition]);	//營養成分
				$food["photo"]=$nb[pDate]."-".$nb[pN].".jpg";  //原始圖	
				$food["s_photo"]="s-".$food["photo"];  //縮圖

				if ($md ==1) 
					$tr_c = " bgcolor='#EEEEEE' " ;
				else  
					$tr_c = " " ;   
				
				//$show_photo=$photo_url.'/'.$food['s_photo'];
				$photo_url=$url_base.(substr($nb[pDate],0,4)-1911);
				$show_photo=$photo_url.'/'.$food['s_photo'];
				
				//	$s_photo=$photo_path.'/'.$food['s_photo'];
				$s_photo=$base.(substr($nb[pDate],0,4)-1911).'/'.$food['s_photo'];;
				
				if (file_exists($s_photo) && is_file($s_photo)){
					$my_photo= '<img src="'.$show_photo .'">'. "" ;
				}	
				
				//合格證明
				$certify_url=$url_base.(substr($nb[pDate],0,4)-1911);
				$show_certify=$certify_url.'/'.$food['s_certify'];
				
				//	$s_certify=$certify_path.'/'.$food['s_certify'];
				$s_certify=$base.(substr($nb[pDate],0,4)-1911).'/'.$food['s_certify'];;
				
				if (file_exists($s_certify) && is_file($s_certify)){
					$my_certify= '<img src="'.$show_certify .'">'. "" ;
				}
				
				
				if ($nb[pFood]) {
					$main .= "<tr $tr_c ><td>$nb[pDate]</td><td align='center'>$md</td><td>$nb[pFood]</td><td>$pMenu</td><td>$nb[pFruit]</td><td>$pNutri</td><td>$nb[ps]</td><td>$my_photo</td><td>$my_certify</td>";
					$main .= "<tr> " ;
				}
				unset($food);
				unset($my_photo);
			}
			$main = "◎食譜設計者：<font color=blue><b>$DESIGN</b></font></u></b>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size: $font_size;' bordercolor='#111111' id='AutoNumber1'>
				<tr bgcolor='$column_bgcolor_m' align='center'><td>日期</td><td>星期</td><td>主食</td><td>菜色</td><td>水果</td><td>營養成分</td><td>備註</td><td>照片</td><td>檢驗合格證明</td></tr> 
					$main</table><br>" ;
		}	

    } else { //週
		//讀取資料庫
		$sqlstr = "SELECT * FROM lunchtb WHERE pDate between '$begdate' and '$enddate'";
		if($DESIGN) $sqlstr.=" and pDesign = '$DESIGN' ";
		$result = $CONN->Execute($sqlstr) ;
		if($result) {
			while ($nb=$result->FetchRow()) {
			   $md = $nb[pMday];			//取得星期幾
			   $food[$md]["date"]= $nb[pDate];
			   $food[$md]["food"]= $nb[pFood];	//主食
			   $food[$md]["menu"]= $nb[pMenu];	//菜單
			   $food[$md]["fruit"]= $nb[pFruit];	//水果
			   $food[$md]["ps"]= $nb[pPs];		//備註
			   $food[$md]["design"]= $nb[pDesign];	//設計者
			   $food[$md]["nutri"]= $nb[pNutrition];	//營養成分
			   $food[$md]["photo"]=$nb[pDate]."-".$nb[pN].".jpg";  //原始圖	
			   $food[$md]["s_photo"]="s-".$food[$md]["photo"];  //縮圖	   
			   $food[$md]["certify"]=$nb[pDate]."-".$nb[pN]."-cer.jpg";  //原始圖	
			   $food[$md]["s_certify"]="s-".$food[$md]["certify"];  //縮圖	   
			}
			
				
				//檢查有無管理權限
				if(checkid($_SERVER['SCRIPT_FILENAME'],1)) $is_admin_link="<a href='lunchadmin.php?begdate=".$begdate."&supplier=".$food[$md]['design']."'><img src='./images/admin.png' alt='管理' title='管理' width=16 border=0></a>"; else $is_admin_link='';
				//列出食譜表格
				$main = "◎食譜設計者：<font color=blue><b>".$food[$md]['design']."</b> $is_admin_link</font></u></b>";
				$main .= "<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size: $font_size;' bordercolor='#111111' id='AutoNumber1' width='100%'>
							<tr bgcolor='$column_bgcolor_w' align='center'> 
								<td class='td_sboady1' align='center'>項　　目</td>";

				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
					 $my_date=$food[$md]["date"]?"<br>( ".substr($food[$md]["date"],-5)." )":'';
					 $main .= " <td>星期".$WEEK_DATE[$md-1]."$my_date</td>" ;
				}

				$main .= "</tr>";
				$main .= "<tr align='center'>"; 
				$main .= "<td>主　　食</td>";

				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						if ($food[$md]["food"]) $main .= "<td>" . $food[$md]["food"] . "</td>" ;
						else $main .= "<td>&nbsp;</td>" ;
				}

				$main .= "</tr>";
				$main .= "<tr align='center'>"; 
				$main .= "<td>菜　　色</td>";

				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						if ($food[$md]["menu"]) $main .= "<td>". nl2br($food[$md]['menu']) ."</td>" ;
						else $main .= "<td>&nbsp;</td>" ;
				}

				$main .= "</tr>";
				$main .= "<tr align='center'>"; 
				$main .= "<td>水　　果</td>";

				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						if ($food[$md]["fruit"]) $main .= "<td>" . $food[$md]['fruit']. "</td>" ;
						else $main .= "<td>&nbsp;</td>" ;
				}

				$main .= "</tr>";

				$main .= "<tr align='center'>"; 
				$main .= "<td>營養成分</td>";

				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						if ($food[$md]["nutri"]) $main .= "<td>". nl2br($food[$md]['nutri']) ."</td>" ;
						else $main .= "<td>&nbsp;</td>" ;
				}

				$main .= "</tr>";
				$main .= "<tr align='center'>"; 
				$main .= "<td>照　　片</td>";
				
				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						$main .= "<td>&nbsp;";					
						$photo_url=$url_base.(substr($food[$md][photo],0,4)-1911);				
						$show_photo=$photo_url.'/'.$food[$md]['s_photo'];	
							$link_photo=$photo_url.'/'.$food[$md]['photo'];						
						$s_photo=$base.(substr($food[$md][photo],0,4)-1911).'/'.$food[$md]['s_photo'];
							$link_s_photo=$base.(substr($food[$md][photo],0,4)-1911).'/'.$food[$md]['photo'];
						if (file_exists($s_photo) && is_file($s_photo)){
							$main .= "<a href='$link_photo' target={$md}_photo><img src='$show_photo' border=0></a>" ;
						}
						$main .= "</td>" ;
				}
				$main .= "</tr>";

				
				$main .= "<tr align='center'>"; 
				$main .= "<td>檢驗證明</td>";
				
				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						$main .= "<td>&nbsp;";
						
						$certify_url=$url_base.(substr($food[$md]['certify'],0,4)-1911);				
						$show_certify=$certify_url.'/'.$food[$md]['s_certify'];
							$link_certify=$photo_url.'/'.$food[$md]['certify'];		
						$s_certify=$base.(substr($food[$md][certify],0,4)-1911).'/'.$food[$md]['s_certify'];
							$link_s_certify=$base.(substr($food[$md][certify],0,4)-1911).'/'.$food[$md]['certify'];
						if (file_exists($s_certify) && is_file($s_certify)){
							$main .= "<a href='$link_certify' target={$md}_certify><img src='$show_certify' border=0></a>" ;
						}
						$main .= "</td>" ;
				}
				$main .= "</tr>";
				
				$main .= "<tr align='center'>"; 
				$main .= "<td>備　　註</td>";

				for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
						if ($food[$md]["ps"]) $main.="<td>".$food[$md]['ps']."</td>";
						else $main .= "<td>&nbsp;</td>" ;
				}
				$main .= "</tr>";

				$main .= "</table><p>";
			}
		}	
		echo $main;	
}


?>
