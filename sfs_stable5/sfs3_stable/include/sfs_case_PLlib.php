<?php

// $Id: sfs_case_PLlib.php 7771 2013-11-15 06:39:56Z smallduh $
// 取代 PLlib.php

/*
 * 函數庫
 * 作者
 * prolin  http://sy3es.tnc.edu.tw/~prolin 
 * hami    cik@mail.wpes.tcc.edu.tw
*/

//版本號
$PLlib_VERSON = 2.0;
$PLlib_DATE = "2001-10-1";

function DtoCh($dday="", $st="-") {
  if (!$dday) //使用預設日期
  $dday = date("Y-m-j");
  //把西元日期改為民國日期  $st為分隔符號
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}
	$d[0] = $d[0] - 1911 ;

	$cday = $d[0]."-".$d[1]."-".$d[2] ;
	return $cday ;
}

function ChtoD($dday, $st="-") {
  //把民國日期改為西元日期  $st為分隔符號
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}
	$d[0] = $d[0] + 1911 ;

	$cday = $d[0]."-".$d[1]."-".$d[2] ;
	return $cday ;
}

function Getday($dday ,$st="-") {
  //把西元日期中取得日期  $st為分隔符號
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}
  //$d[0] = $d[0] - 1911 ;

  //轉為數字傳回
	return intval($d[2]) ;	
}	

function GetdayAdd($dday ,$dayn,$st="-") {
  //日期中加減日數
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}
	return date("Y-m-d",mktime(0,0,0,$d[1],$d[2] + $dayn,$d[0])) ;
}

function GetMonthAdd($dday ,$monthn,$st="-") {
  //日期中加減月
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}  
	return date("Y-m-d",mktime(0,0,0,$d[1]+$monthn,$d[2] ,$d[0])) ;
}

function GetYearAdd($dday ,$yearn,$st="-") {
  //日期中加減年
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}  
	return date("Y-m-d",mktime(0,0,0,$d[1],$d[2] ,$d[0]+$yearn)) ;
}

function StrToDate($dday ,$st="-") {
  //字串格式轉為日期格式
	$tok = strtok($dday,$st) ;
	$i = 0 ;
	while ($tok) {
		$d[$i] =$tok ;
		$tok = strtok($st) ;
		$i = $i+1 ;
	}
	return mktime(0,0,0,$d[1],$d[2] ,$d[0]+$yearn) ;
}

function redir( $surl , $sec) {
  //等 $sec 秒後，轉向到$sul 網頁，必須放在 header部份
  print("<meta http-equiv=\"refresh\" content=\"" . $sec . "; url=" . $surl ."\">  \n" ) ; 

  //備註，php 中函數 Header("Location:cal2.php") ;會馬上轉址，無法出現訊息及等待
}

function allowip( $ip ,$allow=true ) {
  //ip來源判斷 ,$ip判斷字串，$allow true:允許  false:不允許連線

  if ($_SERVER['HTTP_X_FORWARDED_FOR']){ 
   $remoIP=$_SERVER['HTTP_X_FORWARDED_FOR']; 
  } 
  else { 
   $remoIP=$_SERVER['REMOTE_ADDR']; 
  }   
  
  $tok = strtok($ip,".") ;
  $i=0 ;
  
  //$debug = 1 ;
  
  while ($tok) {  
        $soip[$i] = $tok ;
        //if ($debug )  echo "soip[$i]= $soip[$i] \n "; 
        $i++ ; 
        $tok = strtok(".") ;
  }  
  
  $tok = strtok($remoIP,".") ; 
  $i= 0 ;
  while ($tok) {
        $rmip[$i] = $tok ;
        //if ($debug )  echo "rmip[$i]= $rmip[$i] \n "; 
        $i++ ; 
        $tok = strtok(".") ;
        
  }          

  $numi = count($soip);
  $notmach = false ;		//預設為正確的
  //if ($debug ) echo "numi = $numi \n " ;       
  for ($i=0 ;$i<$numi ; $i++) {
      if ( $soip[$i] != '*') {
         if ($soip[$i] != $rmip[$i]) $notmach = true ;
      }   
  }       
  //if ($debug ) echo "notmach = $notmach \n" ;       
  if ($allow=true) $notmach=(!$notmach) ;	//正向列表
  return($notmach) ;

}

function getip() {
  //取得連線者 IP

  if ($_SERVER['HTTP_X_FORWARDED_FOR']){ 
   $remoIP=$_SERVER['HTTP_X_FORWARDED_FOR']; 
  } 
  else { 
   $remoIP=$_SERVER['REMOTE_ADDR']; 
  }   

  return($remoIP) ;

}

class sfs_grid_menu {
	var $row = 18 ;	     //顯示筆數
	var $width = 120 ;   //寬
	var $key_item = "";  // 索引欄名
	var $bgcolor = "#FFFFFF";
	var $fontsize = "font-size: 13px";
	var $sql_str = "";   //SQL 命令	
	var $count_row = 0;  //總筆數
	var $link_str ="--"; //連接字串
	var $display_item = array(); //顯示欄名陣列
	var $display_color = array(); //顯示顏色陣列
	var $color_index_item="";		//顏色判斷欄位
	var $select_item =array(); // option 陣列
	var $color_item =array(); // option 陣列
	var $up_str =""; //上方顯示字串
	var $down_str =""; //下方顯示字串
	var $disabled = 0 ; //變為不可操作否
	var $dispaly_nav = true; // 顯示下方按鈕
	var $top_option = "";	//第一個選項文字
	var $nodata_name ="無資料";
	var $class_ccs ="";
	var $show_item_tol = true; //顯示不同類別統計

//執行命令
function do_query() {
	$res_arr =array();
	$display_arr =array();
	//顯示欄位
	$display_arr = $this->display_item;
	$num = count($display_arr);
	$result = mysql_query($this->sql_str) or die("Error: $this->sql_str");
	$this->count_row = mysql_num_rows ($result);
	
	if( $this->count_row < $this->row )
		$this->row = $this->count_row;
	
	while($row = mysql_fetch_array($result)) {
		$temp ="";
		for ($i=0;$i < $num ;$i++) {
			$temp .= $row[$display_arr[$i]];
			if ($i+1 < $num )
				$temp .= $this->link_str;
		}		
		$this->select_item[$row[$this->key_item]] = $temp ;
		if ($this->color_index_item)
			$this->color_item[$row[$this->key_item]] = $row[$this->color_index_item] ;
	}	 
}

// 顯示
function print_grid($key,$up_str="",$down_str="") {
	global $SFS_PATH_HTML;
		
	if ($up_str) //上方顯示字串
		$this->up_str = $up_str;
	if ($down_str) //下方顯示字串
		$this->down_str = $down_str;
	if ($this->dispaly_nav) {//獨立選擇
		echo "<SCRIPT>function update(s_value) {document.gridform.".$this->key_item.".value=s_value; document.gridform.submit();}</SCRIPT>";	
		if ($this->disabled) //變為不可操作否
			echo "<form name=\"gridform\" method=post action=\"{$_SERVER['SCRIPT_NAME']}\" disabled>";
		else
			echo "<form name=\"gridform\" method=post action=\"{$_SERVER['SCRIPT_NAME']}\">";
	}
	echo "<table width=\"1\" cellspacing=0 cellpadding=0>\n";
	if ($this->up_str)
		echo "<tr ><td align=center ".$this->class_ccs.">".$this->up_str."</td></tr>";
	echo "<tr><td ".$this->class_ccs.">";
	echo "<img src=\"$SFS_PATH_HTML"."images/pixel_clear.gif\" width=\"".$this->width."\" height=1>";
	echo "</td></tr>";
	
  	if ($this->count_row == 0 && $this->dispaly_nav)
  		echo "</tr><td align=center ".$this->class_ccs.">".$this->nodata_name."</td></tr>";
  	else {  		
  		echo "<tr ".$this->class_ccs."><td align=center ".$this->class_ccs.">\n";
		if ($this->dispaly_nav) //獨立選擇
	  		echo "<select style=\"background-color:".$this->bgcolor.";font-size: ".$this->fontsize."\" onchange=\"document.gridform.submit()\" size=\"".$this->row."\" name=\"".$this->key_item."\">"; 
		else
			echo "<select style=\"background-color:".$this->bgcolor.";font-size: ".$this->fontsize."\"  size=\"".$this->row."\" name=\"".$this->key_item."\">"; 
  		$ii=1;
		if($this->top_option)
			echo "<option value=\"\">".$this->top_option."</option>";
  		while (list($tid,$tname) = each($this->select_item)) {
  			if ($this->color_index_item)  			
  				$temp_color= " STYLE=\"color: ".$this->display_color[$this->color_item[$tid]]."\" ";
  			if ($tid == $key) {
  				echo "<option $temp_color value=\"$tid\" selected >$tname</option>";
  				$nav_prior = $temp_id ;
  				$flag = 1;
  				$this_row = $ii;
  			}
  			else
  				echo "<option $temp_color value=\"$tid\">$tname</option>";  		  			
  			$temp_id = $tid ;
  			if ($flag>0) {  			
  				if ($flag == 2 ) {
  					$nav_next = $temp_id;
  					$flag= 0;
  				}
  				else
  					$flag++;
  			}
  			$ii++;
  		}
  		$ii--;
  		echo "</select>";  	
  		echo "</td>\n";
  		echo "</tr>\n";
		if ($this->dispaly_nav) {
	  		echo "<tr >\n";
				echo "<td align=center ".$this->class_ccs.">筆數： $this_row / $ii</td>\n";
  			echo "</tr>\n";
  			//統計不同選項
  			if ($this->color_index_item && $this->show_item_tol) {
  				$color_tol = array_count_values(array_values($this->color_item));
  				echo "<tr><td align=center ".$this->class_ccs." >計：" ;
  				reset($color_tol);
  				while(list($tid,$tname)= each($color_tol))
  					echo "<font color=".$this->display_color[$tid].">＊$tname</font>&nbsp;";
  				echo "</td></tr>\n";
  			}
	  		echo "<tr><td align=center>" ;
  			if ($nav_prior)
  				echo "&nbsp;<input type=button value=\"    ^    \" onclick=\"update('$nav_prior')\" >";
	  		else
  				echo "&nbsp;<input type=button value=\"    ^    \" disabled >";
  			if ($nav_next)
  				echo "&nbsp;<input type=button value=\"    v    \" onclick=\"update('$nav_next')\" >";
	  		else
  				echo "&nbsp;<input type=button value=\"    v    \" disabled >";
  			echo "</td></tr>\n";
		}
  		if ($this->down_str !="")
			echo "<tr><td align=center>".$this->down_str."</td></tr>";		
			
	}
	echo "<input type=hidden name=nav_prior value=\"$nav_prior\">\n";
	echo "<input type=hidden name=nav_next value=\"$nav_next\">\n";
	echo "</table>";
	if ($this->dispaly_nav)//獨立選擇
		echo "</form>\n";	
}

}


//改變字串顏色
function chang_word_color($arr,$str,$str_num=0,$color="red") {
	if ($str_num) { //截字串
		reset($arr);		
		foreach ($arr as $value) {
			$pos = strpos ($str, $value);
			if ($pos) {
				for ($i=$pos;$i>0;$i--) {//往前找一個 \n
					$pp++;
					if ($str[$i]=="\n")
						break;					
				}
				if($pp>$str_num)
					$str_num = $pp+5;
				$pos = $i;				
				break;
			}
		}
		$str = big5_substr(substr($str,$pos),0,$str_num);
	}
	
	reset($arr);
	foreach ($arr as $value) {
		$value = chop($value);
		if ($value) {
			$replace_text = "<font color=$color><B>$value</B></font>";			
			//$str = ereg_replace($value,$replace_text,$str);
			$str = str_replace($value,$replace_text,$str);
			
			 
		}
	}	
	return nl2br($str);
	
}

//截字串(取自 pigo@ms5.url.com.tw 發表)
function big5_substr($str,$start=1,$len=0) { 
	$return_str = ""; 
	$return_len = 0; 
	$big5_offset = 0; 
	$HB_MIN = 0x81; // 高位元組最小值 
	$HB_MAX = 0xfe; // 高位元組最大值 
	$LB1_MIN = 0x40; // 低位元組最小值 
	$LB1_MAX = 0x7e; // 低位元組最大值 
	$LB2_MIN = 0xa1; // 低位元組最小值 
	$LB2_MAX = 0xfe; // 低位元組最大值 

	$isHB = 0; // BIG5 的高位元組識別用 
	$isLB = 0; // BIG5 的低位元組識別用 

	if($len==0) $len = strlen($str); 

	for($i=0; $i<$len; $i++) { 
		$isLB = 0; 
		$isHB = 0; 

		$s1 = substr($str,$i,1); 
		$a1 = Ord($s1); //ASCII 
		if(!$isHB && $a1>=$HB_MIN && $a1<=$HB_MAX){ // 第一個字符合高位元組 
			$isHB = 1; 
			$s2 = substr($str,$i+1,1); 
			$a2 = Ord($s2); //ASCII 
			if( ($a2>=$LB1_MIN && $a2<=$LB1_MAX) || ($a2>=$LB2_MIN && $a2<=$LB2_MAX) ) // 第二個字符合低位元組 
				$isLB = 1; 
		} 


		if($big5_offset >= $start) { 
			if($isHB && $isLB) { 
				$return_str .= $s1.$s2; 
				$i++; 
			}
			else 
				$return_str .= $s1; 
			$return_len++; 
			if($return_len >= $len) break; 
		} 
		else { 
			if($isHB && $isLB) $i++; 
		} 
		$big5_offset++; 
	} 
	return $return_str; 
} 

//分頁的函式 (取自 alan_huang@mail2000.com.tw)

function pagesplit($page,$totalpage,$pagerange,$link=""){
	global $nowrange;
	//if($link)
	$totalrange = intval($totalpage/$pagerange);
	if($totalpage % $pagerange <> 0)//計算總分頁區間
		$totalrange++;
	

	if(!$nowrange || $nowrange < 1)//check從前面傳過來的nowrange是否正確
		$nowrange=1;


	if($nowrange > $totalrange)
		$nowrange = $totalrange;
	
	if($pagerange >  $totalpage)
		$pagerange = $totalpage;


	//印出分頁
	//印出前間隔頁
	if($nowrange > 1)
		$SPLITSTR .="<a href={$_SERVER['SCRIPT_NAME']}?nowrange=".($nowrange-1)."&page=".(($nowrange-1)*$pagerange)."&$link>前".$pagerange."頁</a>";

	$end_page = $nowrange*$pagerange+1;
	if ($end_page > $totalpage)
		$end_page= $totalpage+1;

	for($i=(($nowrange-1)*$pagerange+1);$i < $end_page ;$i++) {
		
		if($i==$page){
			$SPLITSTR .= " <font color=#FF0000>$i</font> ";
		}
		else{
			$SPLITSTR .=" <a href={$_SERVER['SCRIPT_NAME']}?nowrange=$nowrange&page=$i&$link>$i</a> ";
		}
	}
	//印出後間隔頁
	if($nowrange < $totalrange){
		$SPLITSTR .= " <a href={$_SERVER['SCRIPT_NAME']}?nowrange=".($nowrange+1)."&page=".($nowrange*$pagerange+1)."&$link>後".$pagerange."頁</a> ";
	}
	return $SPLITSTR;

}



/* ----- class  tools ---------------------------- */

//下拉選單
//說明: $s_name-選單名稱 ,$id --索引ID ,$arr-- 內容陣列 ,$has_empty --先列出空白 1代表是
class drop_select {
	var $s_name=""; //選單名稱
	var $id="";	//索引ID
	var $arr = array(); //內容陣列
	var $unvisible_arr = array(); //不顯示選項
	var $has_empty = true; //先列出空白
	var $top_option = "";
	var $bgcolor = "#FFFFFF";
	var $font_style = "font-size:13px";
	var $is_submit = false; //更動時送出查詢
	var $other_script = ""; //OnChange中的其他script
	var $multiple = false; //可複選
	var $multiple_id = array(); //複選的索引ID
	var $size = 1; //大小
	var $is_display_color =false;
	var $color_index_arr= array(); //顏色對照陣列
	var $color_item = array("red","blue","black","yellow","green","orange");
	var $use_val_as_key = false; //是否以陣列值為索引
	var $font_color = "#000000"; //前景字顏色
	var $bgcolor_arr=array("#E3DBFF","#E2D9FD","#DBD3F6","#D5CDEF","#CDC6E6","#C4BDDC","#C5BEDD","#BCB5D3","#B4ADCA","#ABA5CD"); //背景色漸層
	var $is_bgcolor_list = false; //是否以漸層呈現背景

	function do_select() {
		echo $this->get_select();
	}
	
	function get_select() {
		$can_multiple=($this->multiple)?"multiple":"";
		$res = "<select name=\"".$this->s_name."\" $can_multiple size=\"".$this->size."\" style=\"background-color:".$this->bgcolor.";".$this->font_style."\"";
		if ($this->other_script)
			$this->other_script .= ";";
		if ($this->is_submit)
			$res .= " onchange=\"".$this->other_script."this.form.submit();\"";
		$res .=">\n";
		if ($this->top_option)
			$res .= "<option value=\"\">".$this->top_option."\n";
		else if ($this->has_empty)
			$res .= "<option value=\"\">\n";
		reset($this->arr);
		while(list($tid,$tname) = each($this->arr)){
			$tid=($this->use_val_as_key)?$tname:$tid;
			if(!in_array($tid,$this->unvisible_arr)) { //判斷是否顯示
				if ($this->is_bgcolor_list){
                                        $bgii = $bgi % count($this->bgcolor_arr);
                                        $bgcolor_temp = "background-color: ".$this->bgcolor_arr[$bgii].";";
					$bgi++;
                                        $temp_color=" STYLE=\"$bgcolor_temp  color: ".$this->font_color."\"";
                                }
				else if ($this->is_display_color||$this->is_bgcolor_list) //顏色顯示
                                        $temp_color=" STYLE=\" color: ".$this->color_item[$this->color_index_arr[$tid]]."\"";

				if (($tid == $this->id) or in_array($tid,$this->multiple_id)) {
					$bgcolor=($key==$result->fields["abs_kind"])?"style='background-color: #ccffcc;'":'';
					$res .= "<option $temp_color value=\"$tid\" selected $bgcolor>$tname</option>\n";
				} else $res .= "<option $temp_color value=\"$tid\">$tname</option>\n";
			}
		}
		$res .= "</select>";	
		return $res;
	}

}


//treemenu
/*********************************************/
  /*  PHP TreeMenu 1.1                         */
  /*                                           */
  /*  Author: Bjorge Dijkstra                  */
  /*  email : bjorge@gmx.net                   */
  /*                                           */  
  /*  Placed in Public Domain                  */
  /*                                           */  
  /*********************************************/

  /*********************************************/
  /*  Settings                                 */
  /*********************************************/
  /*                                           */      
  /*  $treefile variable needs to be set in    */
  /*  main file                                */
  /*                                           */ 
  /*********************************************/
  
  /*********************************************/
  /*                                           */
  /* - Multiple root node fix by Dan Howard    */
  /*                                           */
  /*********************************************/

  /*********************************************/
  /*                                           */
  /* - class mode by hami                      */
  /*                                           */
  /*********************************************/

  /*********************************************/
  /* read file to $tree array                  */
  /* tree[x][0] -> tree level (層級)           */
  /* tree[x][1] -> item text  (文字)           */
  /* tree[x][2] -> item link  (連結)           */
  /* tree[x][3] -> link target(開啟新視窗)     */
  /* tree[x][4] -> last item in subtree        */
  /*********************************************/
class TreeMenu {
	var  $img_url="";
	var  $img_expand   = "tree_expand.gif";
	var  $img_collapse = "tree_collapse.gif";
	var  $img_line     = "tree_vertline.gif";  
	var  $img_split    = "tree_split.gif";
	var  $img_end      = "tree_end.gif";
	var  $img_leaf     = "tree_leaf.gif";
	var  $img_spc      = "tree_space.gif";	
	var  $width = 200;
	var  $doexe = array();
	var  $script="";
	var  $ccs = "nav" ;
	var  $default_p = "";
	var  $oth_link ="" ; //其他連結
	var  $split_str= "^^";
	function TreeMenu() {
		global $SFS_PATH_HTML,$SCRIPT_NAME ;
		if($this->script=="")	  		
	  		$this->script	=  $SCRIPT_NAME;
	  	  
		//路徑設定
		if ($this->img_url) {
			if (substr($this->img_url,-1) != "/")			
				$this->img_url .= "/";
		}
		else
			$this->img_url = $SFS_PATH_HTML."images/tree/";
			
		$this->img_expand   = $this->img_url.$this->img_expand;
		$this->img_collapse = $this->img_url.$this->img_collapse;
		$this->img_line     = $this->img_url.$this->img_line;
		$this->img_split    = $this->img_url.$this->img_split;
		$this->img_end      = $this->img_url.$this->img_end;
		$this->img_leaf     = $this->img_url.$this->img_leaf;
		$this->img_spc      = $this->img_url.$this->img_spc;		
	}
		
	//印出
	function print_tree($old_p = "") {
		global $SFS_PATH_HTML ;
		$p = $GLOBALS[p];
		if ($p=="")
			$p = $this->default_p;
		$maxlevel=0;
		$cnt=0;	
  		for ($i=0; $i<count($this->doexe) ;$i++){
    			$buffer =$this->doexe[$i];
    			$tree[$cnt][0]=strspn($buffer,".");
    			$tmp=rtrim(substr($buffer,$tree[$cnt][0]));
    			$node=explode($this->split_str,$tmp); 
    			$tree[$cnt][1]=$node[0];
    			$tree[$cnt][2]=$node[1];
    			$tree[$cnt][3]=$node[2];
    			$tree[$cnt][4]=0;
    			if ($tree[$cnt][0] > $maxlevel) $maxlevel=$tree[$cnt][0];    
    				$cnt++;
		}	
  		
  		for ($i=0; $i<count($tree); $i++) {
			$expand[$i]=0;
			$visible[$i]=0;
			$levels[$i]=0;
		}
		
		/*********************************************/
		/*  Get Node numbers to expand               */
		/*********************************************/

		if ($p!="") $explevels = explode($this->split_str,$p);
		$i=0;
		while($i<count($explevels)) {
			$expand[$explevels[$i]]=1;
			$i++;
		}
  
		/*********************************************/
		/*  Find last nodes of subtrees              */
		/*********************************************/
  
		$lastlevel=$maxlevel;
		for ($i=count($tree)-1; $i>=0; $i--) {
			if ( $tree[$i][0] < $lastlevel ) {
				for ($j=$tree[$i][0]+1; $j <= $maxlevel; $j++) {
					$levels[$j]=0;
				}
			}
			if ( $levels[$tree[$i][0]]==0 ) {
				$levels[$tree[$i][0]]=1;
				$tree[$i][4]=1;
			}
			else
				$tree[$i][4]=0;
			$lastlevel=$tree[$i][0];  
		}
  
  
		/*********************************************/
		/*  Determine visible nodes                  */
		/*********************************************/
  
		// all root nodes are always visible
		for ($i=0; $i < count($tree); $i++) if ($tree[$i][0]==1) $visible[$i]=1;


		for ($i=0; $i < count($explevels); $i++) {
			$n=$explevels[$i];
			if ( ($visible[$n]==1) && ($expand[$n]==1) ) {
				$j=$n+1;
				while ( $tree[$j][0] > $tree[$n][0] ) {
					if ($tree[$j][0]==$tree[$n][0]+1) $visible[$j]=1;     
						$j++;
				}
			}
		}
  
  
		/*********************************************/
		/*  Output nicely formatted tree             */
		/*********************************************/
  
		for ($i=0; $i<$maxlevel; $i++) $levels[$i]=1;

		$maxlevel++;
  
		echo "<table cellspacing=0 cellpadding=0 border=0 cols=".($maxlevel+3)." width=100%>\n";
		echo "<tr>";

		echo "<img src=\"$SFS_PATH_HTML"."images/pixel_clear.gif\" width=\"".$this->width."\" height=1>";
		for ($i=0; $i<$maxlevel; $i++) echo "<td width=16></td>";
		echo "<td width=100%>&nbsp;</td></tr>\n";
		$cnt=0;
		while ($cnt<count($tree)) {
			if ($visible[$cnt]) {
				/****************************************/
				/* start new row                        */
				/****************************************/      
				echo "<tr>";
      
				/****************************************/
				/* vertical lines from higher levels    */
				/****************************************/
				$i=0;
				while ($i<$tree[$cnt][0]-1) {
					if ($levels[$i]==1)
						echo "<td><a name='$cnt'></a><img src=\"".$this->img_line."\"></td>";
					else
						echo "<td><a name='$cnt'></a><img src=\"".$this->img_spc."\"></td>";
					$i++;
				}
      
				/****************************************/
				/* corner at end of subtree or t-split  */
				/****************************************/         
				if ($tree[$cnt][4]==1) {
					echo "<td><img src=\"".$this->img_end."\"></td>";
					$levels[$tree[$cnt][0]-1]=0;
				}
				else {
					echo "<td><img src=\"".$this->img_split."\"></td>";                  
					$levels[$tree[$cnt][0]-1]=1;    
				} 
      
				/********************************************/
				/* Node (with subtree) or Leaf (no subtree) */
				/********************************************/
				if ($tree[$cnt+1][0]>$tree[$cnt][0]) {
        
					/****************************************/
					/* Create expand/collapse parameters    */
					/****************************************/
					if ($this->oth_link) //其他連結
						$params="?".$this->oth_link."&p=";
					else
						$params="?p=";
					
					$i=0; 
					while($i<count($expand)) {
						if ( ($expand[$i]==1) && ($cnt!=$i) || ($expand[$i]==0 && $cnt==$i)) {
							$params=$params.$i;
							$params=$params.$this->split_str;
						}
						$i++;
					}
               
					if ($expand[$cnt]==0)
						echo "<td><a href=\"".$this->script.$params."#$cnt\"><img src=\"".$this->img_expand."\" border=no></a></td>";
					else
						echo "<td><a href=\"".$this->script.$params."#$cnt\"><img src=\"".$this->img_collapse."\" border=no></a></td>";         
				}
				else {
					/*************************/
					/* Tree Leaf             */
					/*************************/

					echo "<td><img src=\"".$this->img_leaf."\"></td>";         
				}
      
				/****************************************/
				/* output item text                     */
				/****************************************/
				if ($tree[$cnt][2]=="")
					echo "<td colspan=".($maxlevel-$tree[$cnt][0])."><div class=".$this->ccs.">".$tree[$cnt][1]."</div></td>";
				else if ($old_p)
					echo "<td colspan=".($maxlevel-$tree[$cnt][0])."><div class=".$this->ccs."><a href=\"".$tree[$cnt][2]."&p=$old_p\" target=\"".$tree[$cnt][3]."\">".$tree[$cnt][1]."</a></div></td>";
				else
					echo "<td colspan=".($maxlevel-$tree[$cnt][0])."><div class=".$this->ccs."><a href=\"".$tree[$cnt][2]."\" target=\"".$tree[$cnt][3]."\">".$tree[$cnt][1]."</a></div></td>";
				/****************************************/
				/* end row                              */
				/****************************************/
              
				echo "</tr>\n";      
			}
			$cnt++;    
		}
		echo "</table>\n";
	
	
	} // end print()

	//debug
	function debug() {
		for($i=0;$i<count($this->doexe);$i++)
			echo $this->doexe[$i]."<BR>";
	}
	
} //end TreeMenu

/* -------------------------------------------------- *)
(* Num2CNum  將阿拉伯數字轉成中文數字字串
(* 使用示例:
(*   Num2CNum(10002.34) ==> 一萬零二點三四
(*
(* Author: Wolfgang Chien <wolfgang@ms2.hinet.net>
(* Date: 1996/08/04
(* Update Date:
(* -------------------------------------------------- */

// (* 將字串反向, 例如: 傳入 '1234', 傳回 '4321' *)
function ConvertStr($sBeConvert){
	$tt = '';
	for ($x = strlen($sBeConvert)-1;$x>=0; $x--)
		$tt .= substr($sBeConvert,$x,1);
	return $tt;
} 

function Num2CNum($dblArabic,$ChineseNumeric='') {	
	if ($ChineseNumeric=='')
		$ChineseNumeric = '零一二三四五六七八九';
  	$result = "";
	$bInZero = True;
	$sArabic = $dblArabic;
	if (substr($sArabic,0,1) == '-'){
		$bMinus = True;
		$sArabic = substr($sArabic, 1, 254);
	}
	else
		$bMinus = False;
	
	$iPosOfDecimalPoint = strpos($sArabic,'.');  //(* 取得小數點的位置 *)
 	
  //(* 先處理整數的部分 *)
	if ($iPosOfDecimalPoint == 0)
		$sIntArabic = ConvertStr($sArabic);
	else
		$sIntArabic = ConvertStr(substr($sArabic, 0, $iPosOfDecimalPoint));
	
  //(* 從個位數起以每四位數為一小節 *)
	
	for ($iSection = 0 ; $iSection<= intval((strlen($sIntArabic)-1)/4);$iSection++) {
		$sSectionArabic = substr($sIntArabic, $iSection * 4 , 4);
		$sSection = '';	
    		
  		//  (* 以下的 i 控制: 個十百千位四個位數 *)
		for ($i=0;$i< strlen($sSectionArabic);$i++){
			$iDigit = Ord(substr($sSectionArabic,$i,1)) - 48;
			
			if ($iDigit == 0) {
			//(* 1. 避免 '零' 的重覆出現 *)
			//(* 2. 個位數的 0 不必轉成 '零' *)
        			if (!$bInZero and $i != 0)
					$sSection = '零'.$sSection;
				$bInZero = True;
			}
			else {

				switch ($i) {
					case 1: $sSection = '十'.$sSection;
					break;
					case 2: $sSection = '百'.$sSection;
					break;
					case 3: $sSection = '千'.$sSection;
					break;
				}
			
				$sSection = substr($ChineseNumeric, 2 * $iDigit, 2).$sSection;
				$bInZero = False;
			}
		}
		
    		//(* 加上該小節的位數 *)
    		
		if (strLen($sSection) == 0) {
			if (strLen($result) > 0 and substr($result, 0, 2) != '零')
				$result = '零'.$result;
		}
			
		else {
			switch ($iSection) {
				case 0: $result = $sSection;
				break;
				case 1: $result = $sSection . '萬' . $result;
				break;
				case 2: $result = $sSection . '億' . $result;
				break;
				case 3: $result = $sSection . '兆' . $result;
				break;
			}
					
		}
		 
	}
	

	
   //(* 處理小數點右邊的部分 *)
	if ($iPosOfDecimalPoint > 0 ) {
		
		$result .= '點';
		for ($i = $iPosOfDecimalPoint +1;$i <strLen($sArabic);$i++) {
			$iDigit = Ord(substr($sArabic,$i,1)) - 48;
			$result .= substr($ChineseNumeric, 2 * $iDigit , 2);
		}

	}

  //(* 其他例外狀況的處理 *)
	if (strLen($result) == 0)
		$result = '零';
	if (substr($result, 0, 4) == '一十')
		$result = substr($result, 2, 254);

	if (substr($result, 0, 2) == '點')
		$result = '零' .$result;

  //(* 是否為負數 *)
	if ($bMinus)
		$result = '負' .$result;
		
	return $result; 
}


// javascript comba
class js_comba {	
	var $parent_arr = array();	//父選單陣列
	var $child_arr = array();	//子選單陣列
	var $parent_memo = "";		//父選單 option 說明
	var $child_memo = "";		//子選單 option 說明
	var $form_name = "myform";	//form name
	var $select_name_p = "";	//父 select name
	var $select_name_c = "";	//子 select name
	var $selected_item_p = "";	//父 selected option
	var $selected_item_c = "";	//子 selected option
	function print_comba() {				
		$res ="<SELECT name=".$this->select_name_p." \n";
		$res .="onchange=populateCountry(document.".$this->form_name.",document.".$this->form_name.".".$this->select_name_p.".options[document.".$this->form_name.".".$this->select_name_p.".selectedIndex].value)> \n";
		 //說明
		if($this->parent_memo)
			$res .="<OPTION selected value=\"\">".$this->parent_memo."</OPTION>\n";
		//印出選項
		while(list($tid,$tname)= each($this->parent_arr))
			if ($this->selected_item_p == $tname)
				$res .="<OPTION  value=$tid selected>$tname</OPTION>\n";
			else
				$res .="<OPTION  value=$tid>$tname</OPTION>\n";
	
		$res .="</SELECT> \n";
		
		//子選單
		$res .="<SELECT name=".$this->select_name_c." >\n";
		//$res .="onchange=populateUSstate(document.".$this->form_name.",document.".$this->form_name.".".$this->select_name_c.".options[document.".$this->form_name.".".$this->select_name_c.".selectedIndex].text)> \n";
		 //說明
		if($this->child_memo)
			$res .="<OPTION selected value=\"\">".$this->child_memo."</OPTION>\n";
		$res .= "</SELECT>";
		$res .= "<SCRIPT language=JavaScript> \n";
		$res .="<!-- Begin \n";
		
		while (list($p_id,$c_arr)=each($this->child_arr)) {
			//if ($this->$child_memo)
				$res .="var A$p_id"."Array =  new Array(\"('".$this->$child_memo."','',true,true)\",\n";			
			$temp_str = "";
			while (list(,$tname)=each($c_arr))			
				$temp_str.= "\"('$tname')\",\n";		
			$res .= substr($temp_str,0,-2);
			$res .=");\n";
			
		}
		
		$res .="function populateCountry(inForm,selected) { \n";
		$res .="var selectedArray = eval(\"A\"+selected + \"Array\");\n";
		$res .="while (selectedArray.length < inForm.".$this->select_name_c.".options.length) {\n";
		$res .="inForm.".$this->select_name_c.".options[(inForm.".$this->select_name_c.".options.length - 1)] = null;\n";
		$res .="}\n";
		$res .="for (var i=0; i < selectedArray.length; i++) {\n";
		$res .="eval(\"inForm.".$this->select_name_c.".options[i]=\" + \"new Option\" + selectedArray[i]);\n";
		$res .="}\n";
		$res .="if (inForm.".$this->select_name_p.".options[0].value == '') {\n";
		$res .="inForm.".$this->select_name_p.".options[0]= null;\n";
		$res .="if ( navigator.appName == 'Netscape') {\n";
		$res .="if (parseInt(navigator.appVersion) < 4) {\n";
		$res .="window.history.go(0);\n";
		$res .="}\n";
		$res .="else {   	\n";
		$res .="if (navigator.platform == 'Win32' || navigator.platform == 'Win16') {\n";
		$res .="window.history.go(0);\n";
		$res .="            }\n";
		$res .="         }\n";
		$res .="      }\n";
		$res .="   }\n";
		$res .="}\n";

		$res .="// End -->\n";
		$res .="</SCRIPT>\n";
		return $res;
	}
}

function read_file($file) {

        if (!($fp = fopen($file, 'r' ))) return false;

        $contents = fread($fp, filesize($file));

        fclose($fp);

        return $contents;
}

function write_file($file, $contents) {

        if ($fp = fopen($file, "w")) {

                fputs($fp, $contents, strlen($contents));

                fclose($fp);

                return 1;

        } else {

                return 0;
        }
}

//其中$num為傳入的身分證字號，輸入正確傳回true，錯誤傳回false
function isIDnum($num){
//先用RE語法初步檢驗輸入格式是否正確
if(preg_match("/^[a-zA-Z][12][0-9]{8}$/",$num,$r)){
        //算出開頭英文字母的地區代碼
        $x=10+strpos("abcdefghjklmnpqrstuvxywzio",strtolower($num[0]));
        //檢查碼1=地區代碼十位數的數字+(個位數的數字x9)
        $chksum=($x-($x%10))/10+($x%10)*9;
        //檢查碼2=身分數字部分依序乘上8,7,6,5,4,3,2,1然後加起來
        //如n123033877就是1*8+2*7+3*6+...+7*1
        for($i=1;$i<9;$i++){
                $chksum+=$num[$i]*(9-$i);
        }
        //檢查碼=用10減去檢查碼1+檢查碼2的個位數,再取其個位數
        //然後用最後的檢查碼和身分證字號的最後一個數字比較
        //如果相同就是對的,不同就是錯的
        $chksum=(10-$chksum%10)%10;
        if($chksum==$num[9])return true;
}
return false;
}

//替換 Big5 特殊字元
// big5_trouble.txt 所列文字內碼含有 \
// big5_trouble2.txt 所列文字內碼含有 |
// $mode = 0 替換 \
// $mode = 1 替換 |
// $mode = 2 替換 兩種
function big5_trouble_change($source,$mode=0,$default_str=''){
$big5_arr = array();
$big5_arr2 = array();
$sss = $source;
if ($default_str=='')
		$default_str="\\";
if ($mode==0||$mode==2){
	$filename = dirname(__FILE__)."/big5_trouble.txt";
	$fd = fopen($filename,"r");
	$ii=0;
	while (!feof ($fd)) {
		if ($ii++==0)
			continue;
		$temp_ss = trim(fgets($fd,1024));
		$big5_arr[] = $temp_ss;
	}
	fclose ($fd);
	
	for($i=0;$i<count($big5_arr);$i++){
		$r1 = $big5_arr[$i];
		$r2 = $big5_arr[$i].$default_str;
		$sss = str_replace($r1,$r2, $sss);
	}
}

if ($mode==1||$mode==2){
	$filename = dirname(__FILE__)."/big5_trouble2.txt";
	$fd = fopen($filename,"r");
	$ii=0;
	while (!feof ($fd)) {
		if ($ii++==0)
			continue;
		$temp_ss = trim(fgets($fd,1024));
		$temp_arr = explode("_",$temp_ss);
		$big5_arr2[] = $temp_arr[0];
	}
	fclose ($fd);
	
	for($i=0;$i<count($big5_arr2);$i++){
		$r1 = $big5_arr2[$i];
		$r2 = $big5_arr2[$i].$default_str;
		$sss = str_replace($r1,$r2, $sss);
	}
}

return $sss;

}




//判斷是否在 windows 作業系統中
function WIN_PHP_OS() {
  //echo PHP_OS ;      
  if (eregi("^win", PHP_OS)) 
     return true ;
        
}        


//LINUX 中執行外部指令，需安裝 unzip 套件
//windows php.ini 需要打使用  php_zip.dll 模組
function p_unzip($file, $path) {
  if (!WIN_PHP_OS()) {
        // for linux
       chdir($path) ; 
       exec(escapeshellcmd("unzip $file ")) ;        
       return ;
  }            
  
  $zip = zip_open($file);
  if ($zip) {
   while ($zip_entry = zip_read($zip)) {
     if (zip_entry_filesize($zip_entry) > 0) {
       // str_replace must be used under windows to convert "/" into "\"
       $complete_path = $path.str_replace('/','\\',dirname(zip_entry_name($zip_entry)));
       $complete_name = $path.str_replace ('/','\\',zip_entry_name($zip_entry));
       if(!file_exists($complete_path)) { 
         $tmp = '';
         foreach(explode('\\',$complete_path) AS $k) {
           $tmp .= $k.'\\';
           if(!file_exists($tmp)) {
             mkdir($tmp, 0777); 
           }
         } 
       }
       if (zip_entry_open($zip, $zip_entry, "r")) {
         $fd = fopen($complete_name, 'w');
         fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
         fclose($fd);
         zip_entry_close($zip_entry);
       }
     }
   }
   zip_close($zip);
  }
}

?>
