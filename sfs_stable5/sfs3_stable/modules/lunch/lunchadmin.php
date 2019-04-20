<?php

require "config.php";
sfs_check();

// 檢查 php.ini 是否打開 file_uploads ?
check_phpini_upload();

if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
}

// 開始日期
$begdate=($_GET['begdate']) ? $_GET['begdate'] : $_POST['begdate'];

$module_name=basename(dirname(__FILE__));
$base=$UPLOAD_PATH.$module_name.'/';
if (!file_exists($base)) @mkdir($base,0755);
$today_year = date("Y")-1911;
$url_base=$UPLOAD_URL.$module_name.'/';

// 今天星期幾，取得週一日期   
$mday = date(  "w" );
if ($mday>0) $weekfirst = GetdayAdd(date("Y-m-d"),($mday-1)*-1);
	else $weekfirst = GetdayAdd(date("Y-m-d"),1);

// 指定日期當週星期一日期
if ($begdate) {    
   $mday = date(  "w" ,StrToDate($begdate));
   $begdate = GetdayAdd($begdate,($mday-1)*-1);
}   
  
// 若未指定開始日期，則指向這一週
if ($begdate == 0)   $begdate = $weekfirst ;
// 這週五日期
$enddate = GetdayAdd($begdate ,$WEEK_DAYS-1); 
  
$nextweek = GetdayAdd($begdate , 7);   //下一週
$prevweek = GetdayAdd($begdate , -7);	 //前一週

//供應商
$supplier=$_REQUEST['supplier'];

if ($supplier and $_POST['Submit']) {
	//echo '<pre>';
	//print_r($_FILES);
	//echo '</pre>';
	foreach($_POST['content'] as $md=>$data){
		$pid=$data['pid'];
		$fdate=$data[date];
		if($pid){
			$sqlstr = "UPDATE lunchtb SET pdate='$data[date]',pMday='$md',pFood='$data[food]',pMenu='$data[menu]',pFruit='$data[fruit]',pPs='$data[ps]',pDesign='$supplier',pNutrition='$data[nutri]' WHERE pN='$pid'";
			$result = $CONN->Execute($sqlstr); 
			$myID=$pid;
		} else {
			$sqlstr = "insert into lunchtb(pdate,pMday,pFood,pMenu,pFruit,pPs,pDesign,pNutrition) values('$data[date]','$md','$data[food]','$data[menu]','$data[fruit]','$data[ps]','$supplier','$data[nutri]')" ;
			$result = $CONN->Execute($sqlstr); 
			$myID=$CONN->Insert_ID();		
		}		
		
		//上傳照片處理
		$path=$base.(substr($fdate,0,4)-1911);
		if (!file_exists($path)) @mkdir($path,0755);
		$new_file_path=$path."/";
		
		//照片
		$new_file_name=$fdate."-".$myID.".jpg";			
		upload_lunch_file($_FILES[myfile], $md, $new_file_path, $new_file_name);
		
		//檢驗合格圖
		$new_file_name=$fdate."-".$myID."-cer.jpg";
		upload_lunch_file($_FILES[certify], $md, $new_file_path, $new_file_name);
	}
	header("Location: lunch.php?begdate=$begdate");
//exit;
}


// 下一週 及 上一週 的位址
$linknext = basename($_SERVER['PHP_SELF'])."?begdate=$nextweek&supplier=$supplier";
$linknow = basename($_SERVER['PHP_SELF'])."?begdate=$weekfirst&supplier=$supplier";
$linkprev = basename($_SERVER['PHP_SELF'])."?begdate=$prevweek&supplier=$supplier";
 
head("午餐食譜登錄");

if(checkid($_SERVER['SCRIPT_FILENAME'],1))
{
	$Designer="<select name='supplier' onchange='this.form.submit();'><option></option>";
	foreach($DESIGN as $key=>$value){
		$selected=($value==$supplier)?'selected':'';
		$Designer.="<option value='$value'$selected>$value</option>";	
	}
	$Designer.="</select>";	
	$main.="<td>$Designer</td>";
	$main="<form method='post' enctype='multipart/form-data' action='$_SERVER[SCRIPT_NAME]'>
			<table><tr>
			<td>
			◎用餐日期：".DtoCh($begdate)." ~ ".DtoCh($enddate)."　<a href='$linkprev'> <img src='./images/prev.png' width=12 border=0 alt='前一週' title='前一週'></a>
			<a href='$linknow'><img src='./images/now.png' width=12 border=0 alt='本週' title='本週'></a>	
			<a href='$linknext'><img src='./images/next.png' width=12 border=0 alt='下一週' title='下一週'></a></td>
			<td>
			　　　◎食譜設計者：$Designer 
			<input type='hidden' name='begdate' value='$begdate'> <input type='submit' name='Submit' value='登錄'>
			<input type='reset' name='Submit2' value='重設'>
			</td>
			<td align='right'>　　<a href='lunch.php?begdate=$begdate'><img src='./images/view.png' alt='回瀏覽模式' title='回瀏覽模式' border=0 height=24></a></td>
			</tr></table>
			<table border='2' cellpadding='7' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>";
	if($supplier){
		//讀取資料庫
		$sqlstr="SELECT * FROM lunchtb WHERE pDesign='$supplier' AND pDate BETWEEN '$begdate' AND '$enddate'";
		$result=$CONN->Execute($sqlstr);
		unset($food);
		while($nb=$result->FetchRow()) {
			$md = $nb[pMday];			//取得星期幾
			$food[$md]["date"]= $nb[pDate];
			$food[$md]["food"]= $nb["pFood"];	//主食
			$food[$md]["menu"]= $nb["pMenu"];	//菜單
			$food[$md]["fruit"]= $nb["pFruit"];	//水果
			$food[$md]["ps"]= $nb["pPs"];		//備註
			$food[$md]["design"]= $nb["pDesign"];	//設計者
			$food[$md]["pid"]= $nb["pN"];	//索引號
			$food[$md]["nutri"]= $nb["pNutrition"];	//營養成分
			$food[$md]["photo"]=$nb[pDate]."-".$nb[pN].".jpg";  //原始圖	
		   $food[$md]["s_photo"]="s-".$food[$md]["photo"];  //縮圖	   
		   $food[$md]["certify"]=$nb[pDate]."-".$nb[pN]."-cer.jpg";  //原始圖	
		   $food[$md]["s_certify"]="s-".$food[$md]["certify"];  //縮圖	  
			if($nb["pDesign"]!=""){
				$WeekDesign=$nb["pDesign"];
			}
		}

		$main.="<tr bgcolor='#FFFFCC' align='center'><td>項　　目</td>";
		//for ($i=1;$i<=$WEEK_DAYS; $i++) {
		//	$main .="<td>星期".$WEEK_DATE[$i-1]."</td>";
		//}
		for ($md=1 ; $md<=$WEEK_DAYS ;$md++) {
			 $my_date=$food[$md]["date"]?"<br>( ".substr($food[$md]["date"],-5)." )":'';
			 $main .= " <td width='18%'>星期".$WEEK_DATE[$md-1]."$my_date</td>" ;
		}
		$main .= "</tr>";

		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>主　　食</td>";
		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$main .= "<td><input type='text' name='content[$md][food]' size='$INPUT_SIZE' value='".$food[$md]['food']."'></td>";
		}
		$main .= "</tr>";
		
		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>菜　　色</td>";
		for($md=1; $md<=$WEEK_DAYS ; $md++) {
			$main .= "<td><textarea name='content[$md][menu]' rows='$TEXTAREA_ROWS_SIZE' cols='".$TEXTAREA_COLS_SIZE."'>".$food[$md]['menu']."</textarea></td>";
		}
		$main .= "</tr>";

		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>水　　果</td>";
		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$main .= "<td><input type='text' name='content[$md][fruit]' size=$INPUT_SIZE' value='".$food[$md]['fruit']."'></td>";
		}
		$main .= "</tr>";

		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>營養成分</td>";
		for($md=1; $md<=$WEEK_DAYS ; $md++) {
			$main .= "<td><textarea name='content[$md][nutri]' rows='$TEXTAREA_ROWS_SIZE' cols='".$TEXTAREA_COLS_SIZE."'>".$food[$md]['nutri']."</textarea></td>";
		}
		$main .= "</tr>";

		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>備　　註</td>";
		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$main .= "<td><input type='text' name='content[$md][ps]' size='$INPUT_SIZE' value='".$food[$md]['ps']."'></td>";
		}
		$main .= "</tr>";

		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>照　　片</td>";
		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$photo_url=$url_base.(substr($food[$md][photo],0,4)-1911);				
			$show_photo=$photo_url.'/'.$food[$md]['s_photo'];	
				$link_photo=$photo_url.'/'.$food[$md]['photo'];						
			$s_photo=$base.(substr($food[$md][photo],0,4)-1911).'/'.$food[$md]['s_photo'];
				$link_s_photo=$base.(substr($food[$md][photo],0,4)-1911).'/'.$food[$md]['photo'];
			if (file_exists($s_photo) && is_file($s_photo)){
				$my_photo= "<a href='$link_photo' target='_BLANK'><img src='$show_photo' border=0></a><br>" ;
			} else $my_photo='';
			$main .= "<td>$my_photo<input type='file' name='myfile[$md]' size='11' ></td>";
		}
		$main .= "</tr>";
		
		$main .= "<tr bgcolor='#FFFFFF' align='center'><td bgcolor='#DDFFDD'>檢驗證明</td>";
		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$certify_url=$url_base.(substr($food[$md][certify],0,4)-1911);				
			$show_certify=$certify_url.'/'.$food[$md]['s_certify'];	
				$link_certify=$certify_url.'/'.$food[$md]['certify'];						
			$s_certify=$base.(substr($food[$md][certify],0,4)-1911).'/'.$food[$md]['s_certify'];
				$link_s_certify=$base.(substr($food[$md][certify],0,4)-1911).'/'.$food[$md]['certify'];
			if (file_exists($s_certify) && is_file($s_certify)){
				$my_certify= "<a href='$link_certify' target='_BLANK'><img src='$show_certify' border=0></a><br>" ;
			} else $my_certify='';
			$main .= "<td>$my_certify<input type='file' name='certify[$md]' size='11' ></td>";
		}
		$main .= "</tr>";

		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$main .= "<input type='hidden' name='content[$md][pid]' value='".$food[$md]['pid']."'>";
		}
		
		for($md=1 ; $md<=$WEEK_DAYS ; $md++) {
			$mydate=$food[$md]['date']?$food[$md]['date']:GetdayAdd($begdate,$md-1);  //用餐日期判定
			$main .= "<input type='hidden' name='content[$md][date]' value='$mydate'>";
		}

	} else $main.="<tr><td align='center'><font size=3 color='brown'>請先選擇食譜設計者！</font></td></tr>";
	$main.="</table><p><font style='font-size:12px' color='blue'>※照片僅能上傳JPG格式，容量請控制在1MB以下。</font></p></form>";
	echo $main;
} else echo "<center><font size=5 color='red'><BR><BR>您未有管理權限，無法新增或修改！</font></center>";
	
foot();


function upload_lunch_file($myfile, $k, $new_file_path, $new_file_name){

	if($myfile[error][$k]==1){
		error_die('檔案大小超出 php.ini:upload_max_filesize 限制。');
		//die('上傳的照片容量過大');
	}elseif($myfile[error][$k]==2){
		error_die('檔案大小超出 MAX_FILE_SIZE 限制。');
	}elseif($myfile[error][$k]==3){
		error_die('檔案僅被部分上傳。');
	}

	$new_file=$new_file_path.$new_file_name;  //原始圖
	$s_new_file=$new_file_path.'s-'.$new_file_name;  //縮圖	
	if (is_uploaded_file($myfile[tmp_name][$k]) AND $myfile[error][$k]==0 and $myfile[size][$k]>0){
		if (file_exists($new_file) and is_file($new_file)) {
			//echo "刪除<br> ";
			unlink($new_file);
		}
		@move_uploaded_file($myfile[tmp_name][$k],$new_file);
		ImageResize($new_file, $s_new_file);
	}

}

function ImageResize($from_filename, $save_filename, $in_width=160, $in_height=120, $quality=100)
{
    $allow_format = array('jpeg', 'png', 'gif');
    $sub_name = $t = '';

    // Get new dimensions
    $img_info = getimagesize($from_filename);
    $width    = $img_info['0'];
    $height   = $img_info['1'];
    $imgtype  = $img_info['2'];
    $imgtag   = $img_info['3'];
    $bits     = $img_info['bits'];
    $channels = $img_info['channels'];
    $mime     = $img_info['mime'];

    list($t, $sub_name) = split('/', $mime);
    if ($sub_name == 'jpg') {
        $sub_name = 'jpeg';
    }

    if (!in_array($sub_name, $allow_format)) {
        return false;
    }

    // 取得縮在此範圍內的比例
    $percent = getResizePercent($width, $height, $in_width, $in_height);
    $new_width  = $width * $percent;
    $new_height = $height * $percent;

    // Resample
    $image_new = imagecreatetruecolor($new_width, $new_height);

    $image = imagecreatefromjpeg($from_filename);

    imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return imagejpeg($image_new, $save_filename, $quality);
}

function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
{
    if ($source_w < $inside_w && $source_h < $inside_h) {
        return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
    }

    $w_percent = $inside_w / $source_w;
    $h_percent = $inside_h / $source_h;

    return ($w_percent > $h_percent) ? $h_percent : $w_percent;
}
  
?>