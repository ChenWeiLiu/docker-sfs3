<?php
// $Id: newslist.php 8812 2016-02-06 13:23:43Z qfon $

ob_start();
//session_start();
include "config.php";

$nm_pagenow = $_POST['nm_pagenow'];


//這支程式是新聞瀏覽的程式, 不須做 sys_check的動作
if ($m_arr["IS_STANDALONE"]=='0') {
 //秀出網頁布景標頭
 head("新聞發佈"); 

?>
<?php } else { ?>
<html lang="zh-TW">
<head>
<meta http-equiv="content-type" content="text/html; charset=Big5" >
</head>
<body>
<?php }?>
<?php 
// $title,$postdate,$schname,$poster,$news,$imagename

function tblistnews($news_sno,$title,$user_name,$news,$postdate,$newslink,$imagename){

	// $newscontent 的長度切為 244(122個中文字)
	$news=substr($news,0,244);
	$news=substr_replace($news,"．．．．",-8);
	echo "<tr bgcolor='#FFF7D1' onMouseOver=setBG('#C3FF74',this) onMouseout=setBGOff('#FFF7D1',this)>\n\r";
	echo "<td width='110'>".$postdate."</td>\n\r";
	echo "<td width='590'>";
	//echo "<a href='shownews.php?rdnum=$newsno'>";
	echo "<a href='shownews.php?rdsno=$news_sno'>".$title."</a>--".$user_name."報導<br>\n\r";
	$destInfo  = pathInfo($imagename);
	if ($destInfo['extension'] != ""){
		echo "<img src='".$imagename."' align='right'>\n\r";
	}
	echo nl2br($news);
	echo "</a></td>\n\r";
	echo "</tr>\n\r";
	//echo "<tr><td>&nbsp;</td><td>&nbsp;</td></tr>\n\r";
}
?>

<script language="JavaScript">
<!--
function setBG(TheColor,thetable) {
	thetable.bgColor=TheColor;
}
function setBGOff(TheColor,thetable) {
	thetable.bgColor=TheColor;
}
//-->
</script>

<?php

 $search_key=$_POST['search_key'];


//先找出共幾筆資料, 分成幾頁
//$sql_totalnews = "SELECT * FROM newsmig";
$sql_totalnews = "SELECT count(*) FROM newsmig";
if ($search_key!='') {
//$sql_totalnews .= " where title like '%".$search_key."%' or news like '%".$search_key."%' ";
$sql_totalnews .= " where title like ? or news like ? ";
}

    ///mysqli	
$mysqliconn = get_mysqli_conn();
$stmt = "";
$search_key="%$search_key%";
$stmt = $mysqliconn->prepare($sql_totalnews);
$stmt->bind_param('ss',$search_key,$search_key);
$stmt->execute();
$stmt->bind_result($numbers);
$stmt->fetch();
$stmt->close();

///mysqli

//$rs1 = $CONN->Execute($sql_totalnews);
//$numbers = $rs1->RecordCount();

if ($m_arr["nums_perpage"] != ""){
	$nums_perpage = $m_arr["nums_perpage"];
}else{
	$nums_perpage = 10;
}

//nums_perpage 由 config.php 引入
if ($numbers%$nums_perpage==0){
	$pages = $numbers/$nums_perpage;
}else{
	$pages = floor($numbers/$nums_perpage)+1;
}


//第一次進入, 設pagenow的SESSION變數, 起始值=1
//if (!session_is_registered("nm_pagenow")) {
//	session_register("nm_pagenow");
    if (empty($nm_pagenow))
	{
	$_SESSION["nm_pagenow"]=($_SESSION["nm_pagenow"]<1)?1:$_SESSION["nm_pagenow"];
	
	//若使用者有點上一頁, 下一頁 --> pagenow 可加加減減
if ($_POST["cp"]== "goback" and $_SESSION["nm_pagenow"]!= 1){
	$_SESSION["nm_pagenow"]=$_SESSION["nm_pagenow"]-1;
}elseif($_POST["cp"]=="gonext" and $_SESSION["nm_pagenow"]<=$pages){
	$_SESSION["nm_pagenow"]=$_SESSION["nm_pagenow"]+1;
}elseif($_POST["cp"]==""){
	$_SESSION["nm_pagenow"]=1;
}

//若目前頁數大於總頁數, 把目前頁數設為最大值 2014.06.24
if ($_SESSION["nm_pagenow"]>$pages) { $_SESSION["nm_pagenow"]=$pages; }

	}
	else
	{
	$_SESSION["nm_pagenow"]=$nm_pagenow;	
	}
//}



/****
  `news_sno` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(60) default NULL,
  `posterid` varchar(10) default NULL,
  `news` text,
  `postdate` datetime NOT NULL default '0000-00-00 00:00:00',
  `newslink` varchar(70) default NULL
******/

$sql_listnews = "SELECT news_sno,title,posterid,news,postdate,newslink FROM newsmig ";
if ($search_key!='') {
//$sql_listnews .= "where title like '%".$search_key."%' or news like '%".$search_key."%' ";
$sql_listnews .= "where title like ? or news like ? ";
}
$sql_listnews .= "ORDER BY postdate DESC \n\r";

//本頁最後一筆
$rdend = ($_SESSION["nm_pagenow"]*$nums_perpage > $numbers)?($numbers % $nums_perpage):$nums_perpage;
//本頁的第一筆
$rdstart = ($_SESSION["nm_pagenow"]-1) * $nums_perpage;

// SQL語法加上 LIMIT 子句, 限定本頁該出現的資料
$sql_listnews .= " LIMIT $rdstart , $rdend\n\r";


    ///mysqli	
$stmt = "";
$search_key="%$search_key%";
$stmt = $mysqliconn->prepare($sql_listnews);
$stmt->bind_param('ss',$search_key,$search_key);
$stmt->execute();
$stmt->bind_result($news_sno,$title,$posterid,$news,$postdate,$newslink);
//$stmt->fetch();
//$stmt->close();

///mysqli

//$rs = $CONN->Execute($sql_listnews);
?>
<form method="post" name="myform" action="<?php echo $_SERVER['php_self'];?>">
	<input type="hidden" name="cp" value="">
<?php
echo "<table align='center' width='696' bgcolor='#C3FF74'>";
echo "<tr>\n\r";
echo "	<td>共 ".$numbers." 筆新聞　分成 ".$pages." 頁 <input type='text' name='search_key' value='".$search_key."' size='10'><input type='submit' value='篩選'> </td>\n\r";

//若 pagenow = 1 , 停在第一頁, 就如無法再 上一頁 了
if ($_SESSION["nm_pagenow"]==1) {
	echo "	<td>上一頁</td>\n\r";
}else{
	?>
 		<td><a href="#" onclick="document.myform.cp.value='goback';document.myform.submit()" title="第<?php echo $_SESSION["nm_pagenow"]-1; ?>頁">上一頁</a></td>
  <?php
}

//若 pagenow = pages , 停在最後一頁, 就如無法再 下一頁 了 
if ($_SESSION["nm_pagenow"]==$pages) {
	echo "	<td>下一頁</td>\n\r";
}else{
	?>
		<td><a href="#" onclick="document.myform.cp.value='gonext';document.myform.submit()" title="第<?php echo $_SESSION["nm_pagenow"]+1; ?>頁">下一頁</a></td>
  <?php
}
echo "	<td><a href='postnews.php?act=add'>新增新聞</a></td>\n\r";
echo "</tr>";
echo "</table>\n\r";
echo "<table align='center' width='700'>\n\r";
//if ($rs){
if ($numbers){	
	//while ($ar=$rs->FetchRow()) {
	  while ($stmt->fetch()) {
		$ar=array($news_sno,$title,$posterid,$news,$postdate,$newslink);
		list($news_sno,$title,$posterid,$news,$postdate,$newslink)=$ar;
		userdata($posterid);
		//先把 檔名(含路徑) 處理出來
		clearstatcache();
		$pn_dir = $savepath.$news_sno."/";
		$pn_dir_url = $htmlsavepath.$news_sno."/";
		$direxist=file_exists($pn_dir);
		if (!$direxist){
			$pn_dir = "";
			$pn_dir_url = "";
			$imagename = "";
		}else{
			$handle=opendir($pn_dir);
			$j = 0;
			while ($file = readdir($handle)) {
				if (substr($file,0,3) == 'Si-') {
					$fname[$j] = $file;
					$j++;
				}
			}
			if ($j == 0) $fname = array();
			$lastnum = $j - 1;
			// 這?要 random 找出一支 S- 開頭的小圖
			$whichone = rand(0,$lastnum);
			$imagename=$pn_dir_url.$fname[$whichone];
			//echo "<br>".$imagename;
		}
		//再把datetime 改成date
		$postdate = substr($postdate,0,4)."-".substr($postdate,5,2)."-".substr($postdate,8,2);
		//echo "<tr><td>".$imgagename."</td></tr>";
		tblistnews($news_sno,$title,$user_name,$news,$postdate,$newslink,$imagename);
	}
}else{
	echo "<tr><td align='center'><br>連線失敗</td></tr>\n\r";
}

?>
</table>
</form>

<?php
if ($m_arr["IS_STANDALONE"]=='0'){
	//SFS3佈景結尾
	foot();
}
?>
