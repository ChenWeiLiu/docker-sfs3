<?php
//計算目前的秒數 傳入 YYYY-MM-DD HH:ii:ss
function NowAllSec($DateTime) {
  $mon=substr($DateTime,5,2);
  if (substr($mon,0,1)=="0") $mon=substr($mon,1,1);
  $day=substr($DateTime,8,2);
  if (substr($day,0,1)=="0") $day=substr($day,1,1);
  $st=date("U",mktime(substr($DateTime,11,2),substr($DateTime,14,2),substr($DateTime,17,2),$mon,$day,substr($DateTime,0,4)));
  return $st;
}

function big52utf8($big5str) {  
	$blen = strlen($big5str);  
	$utf8str = "";  
		for($i=0; $i<$blen; $i++) {    
			$sbit = ord(substr($big5str, $i, 1));    
			if ($sbit < 129) {      
				$utf8str.=substr($big5str,$i,1);    
			}elseif ($sbit > 128 && $sbit < 255) {     
				$new_word = iconv("big5", "UTF-8", substr($big5str,$i,2));
				$utf8str.=($new_word=="")?" ":$new_word;      
				$i++;    
			} //end if 
		} // end for
	
	return $utf8str;
}

function shownews($NEW) {
	global $UPLOAD_NEWS_URL,$MANAGER;
	?>
	<table border="0" width="100%">
		<tr>
			<td bgcolor="#CCCCFF" style="color:#800000">※<?php echo $NEW['title'];?>&nbsp;<font style="color:#808080;font-size:9pt">/&nbsp;有效期限: <?php echo $NEW['sttime'];?> ~ <?php echo $NEW['endtime'];?></font>
			<?php
			 if ($MANAGER) {
			  ?>
			  <img src="./images/edit.png" border="0" title="編輯" style="cursor:hand" onclick="document.myform.option1.value='<?php echo $NEW['nsn'];?>';document.myform.act.value='update';document.myform.submit();">
			  <img src="./images/del.png" border="0" title="刪除"  style="cursor:hand" onclick="javascript:del_news(<?php echo $NEW['nsn'];?>);">
			  <?php
			 }
			?>	
			</td>
		</tr>
	</table>
	<div align="center">
 	<table border="0" width="97%">
		<tr>
			<td>
				<?php echo shownewhtml($NEW['memo'],$NEW['htmlcode']);?>
			</td>
		</tr>
		
		<?php
		$query="select * from contest_files where nsn='".$NEW['nsn']."'";
 	  $result=mysql_query($query);
 	  if (mysql_num_rows($result)>0) {
      ?>
    <tr><td>
      <table border="1" width="100%" bordercolor="#008080" style="border-collapse:collapse">
      <tr>
      	<td style="font-size:10pt"><font style="color:#FF6600">。本消息含附件，請在檔名上按滑鼠右鍵選擇【另存目標】：</font>
      
      <?php 	  	
      while ($row=mysql_fetch_array($result,1)) {
       ?>
       <li><a href="<?php echo $UPLOAD_NEWS_URL;?><?php echo $row['filename'];?>"><?php echo $row['ftext'];?></a>
       <?php
      }
      ?>
     </td></tr>
      </table>
    </td></tr>
      <?php
 	  } // end if
		?>
	</table>
  </div>
  <Script Language="JavaScript">
   //刪除消息
   function del_news(NSN) {
    Y=confirm("您確認要刪除本消息?");
    if (Y) {
     document.myform.option1.value=NSN;
     document.myform.act.value='del';
     document.myform.submit();
    
    } else {
      return false;
    }
    
   }
  </Script>
	<?php
}

function showgroups($tsn,$stid) {
 $query="select stid,name from contest_user where tsn='$tsn' and ifgroup='$stid'";
 $result=mysql_query($query);
 if (mysql_num_rows($result)>0) {
 echo "&nbsp;( 組員: &nbsp;";
  while ($row=mysql_fetch_array($result,1)) {
   echo $row['stid'].$row['name']."&nbsp;";
  }
  echo ")";
 }
}

//最新消息列表
function listnews($target) {
	global $PHP_MENU,$PHP_URL,$PHP_PAGE;
	
	?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
   	<tr bgcolor="#FFFFCC">
   		<td style="font-size:10pt;color:#800000" width="40" align="center">編號</td>
   		<td style="font-size:10pt;color:#800000" align="center">消息主題</td>
   		<td style="font-size:10pt;color:#800000" width="120" align="center">發佈時間</td>
   		<td style="font-size:10pt;color:#800000" width="120" align="center">結束時間</td>
   		<td style="font-size:10pt;color:#800000" width="50" align="center">附檔</td>
  		<td style="font-size:10pt;color:#800000" width="50" align="center">操作</td>

   	</tr>	
 <?php
    $row=mysql_fetch_row(mysql_query("select count(*) as num from contest_news"));
   	 list($ALL)=$row; 
   	 $PAGEALL=ceil($ALL/$PHP_PAGE); //無條件進位
   	 $st=($target-1)*$PHP_PAGE;
   	 $query="select * from news limit ".$st.",".$PHP_PAGE;
   	 $result=mysql_query($query);
 	  while ($row=mysql_fetch_row($result)) {
 	  	list($id,$nsn,$title,$sttime,$endtime,$memo)=$row;
 	  $query="select count(*) as num from files where nsn='".$nsn."'";
 	  list($F)=mysql_fetch_row(mysql_query($query));
 	?>
   	<tr>
   		<td style="font-size:10pt" align="center"><?php echo $id;?></td>
  		<td style="font-size:10pt"><?php echo $title;?></td>
  		<td style="font-size:10pt" align="center"><?php echo $sttime;?></td>
  		<td style="font-size:10pt" align="center"><?php echo $endtime;?></td>
  		<td style="font-size:10pt" align="center"><?php echo $F;?>個附件</td>
  		<td style="font-size:10pt" align="center">
  			<a style="cursor:hand" onclick="document.form2.nsn.value='<?php echo $nsn;?>';document.form2.mode.value='edit';document.form2.submit();"><img src="<?php echo $PHP_URL;?>fig/edit.jpg" border="0"></a>&nbsp;
  			<a style="cursor:hand" onclick="document.form2.mode.value='drop';document.form2.nsn.value='<?php echo $nsn;?>';if (confirmdelete('刪除第<?php echo $id;?>則消息')) { document.form2.submit() }"><img src="<?php echo $PHP_URL;?>fig/drop.png"  border="0"></a>
  		</td>
  	</tr>
  <?php
    }
  ?>
  	</table>
  	<table border="0" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0">
  	<tr>
  	 <td style="font-size:10pt">換頁 
  	 <?php
  	 //頁碼
  	  for($i=1;$i<=$PAGEALL;$i++) {
  	  	if ($i==$target) {
  		  	   echo $i."&nbsp;";
				 }else{
  	   ?>
  	    <a href="<?php echo $_SERVER['PHP_SELF'];?>?target=<?php echo $i;?>"><?php echo $i;?></a>&nbsp;
  	   <?php
  	     } // end if
  	  } //end for
  	 ?>
  	 </td>
  	</tr>
  </table>
 
<?php

} // end function


//最新消息表單
function form_news($NEWS) {
?>
   <table border="1" width="100%" style="border-collapse: collapse" bordercolor="#C0C0C0" cellpadding="2">
  	<tr>
  		<td width="80" align="right" style="color:#800000">消息標題</td>
  		<td><input type="text" name="title" size="70" value="<?php echo $NEWS['title'];?>"></td>
  	</tr>
  	<tr>
  		<td width="80" align="right" style="color:#800000">開始日期</td>
  		<td>
  			<input type="text" id="sday" name="sday" size="10" value="<?php echo substr($NEWS['sttime'],0,10);?>">
			
		<script type="text/javascript">
		new Calendar({
  		    inputField: "sday",
   		    dateFormat: "%Y-%m-%d",
    	    trigger: "sday",
    	    bottomBar: true,
    	    weekNumbers: false,
    	    showTime: 24,
    	    onSelect: function() {this.hide();}
		    });
		</script>

					

  			時間：<?php SelectTime('stime_hour',substr($NEWS['sttime'],-8,2),24);?>點 <?php SelectTime('stime_min',substr($NEWS['sttime'],-5,2),60);?>分

  		</td>
  	</tr>
  	<tr>
  		<td width="80" align="right" style="color:#800000">結束日期</td>
  		<td>
  			<input type="text" id="eday" name="eday" size="10" value="<?php echo substr($NEWS['endtime'],0,10);?>">
					<script type="text/javascript">
				    new Calendar({
  		      inputField: "eday",
   		      trigger: "eday",
   		      dateFormat: "%Y-%m-%d",
    		    bottomBar: true,
    		    weekNumbers: false,
    		    showTime: 24,
    		    onSelect: function() {this.hide();}
				    });
					</script>
        時間：<?php SelectTime('etime_hour',substr($NEWS['endtime'],-8,2),24);?>點<?php SelectTime('etime_min',substr($NEWS['endtime'],-5,2),60);?>分
  		</td>
  	</tr>

  	<tr>
  		<td width="80" align="right" style="color:#800000">消息內容</td>
  		<td><textarea rows="10" name="memo" cols="70"><?php echo $NEWS['memo'];?></textarea></td>
  	</tr>
  	<tr>
  		<td width="80" align="right" style="color:#800000">內文格式</td>
  		<td>
  		 <input type="radio" name="htmlcode" value="0" <?php if ($NEWS['htmlcode']==0) { echo "checked"; } ?>>純文字
  		 <input type="radio" name="htmlcode" value="1" <?php if ($NEWS['htmlcode']==1) { echo "checked"; } ?>>含HTML標籤	
  		</td>
  	</tr>
		<tr>
			<td width="80" align="right" style="font-size: 10pt" bgColor="#ffffcc">附加檔案</td>
			<td align="right" style="color:#800000">
				<?php
				//檢查有沒有附加檔案
				$query="select * from contest_files where nsn='".$NEWS['nsn']."'";
				$result=mysql_query($query);
				if (mysql_num_rows($result)>0) {
				?>
				<table border="1" width="100%"style=" border-collapse: collapse" bordercolor="#FFCCCC">
					<tr><td>
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td style="color:#800000;font-size:10pt">※已存在附檔</td>
					</tr>
					<?php 
					  while ($row=mysql_fetch_array($result,1)) {
					 	?>
					 	<tr>
					 		<td><?php echo $row['ftext'];?><img src="./images/del.png" border="0" title="刪除"  style="cursor:hand" onclick="if (confirm('您確定要\n刪除「<?php echo $row['ftext'];?>」?')) { document.myform.RETURN.value='<?php echo $_POST['RETURN'];?>';document.myform.option2.value='<?php echo $row['fsn'];?>';document.myform.act.value='del_file';document.myform.submit(); }"></td>
					 	</tr>
					<?php
					  }// end while
					?>
				</table>
			</td></tr>
		</table>
				<?php
				} // end if file num >0
				?>
				<table border="0" width="100%">
					<tr>
						<td><input type="file" class="multi" name="thefile[]"></td>
						<td align="left"><input type="button" value="加入此檔" name="B1"></td>
					</tr>
				</table>		
			</td>
		</tr>
  </table>
<?php
} // end function

//最新消息附檔
function news_files($nsn) {
	
	global $UPLOAD_NEWS_PATH;
	
  if (count($_FILES['thefile']['name']>0)) {
   for ($i=0;$i<count($_FILES['thefile']['name']);$i++) {
       $NowFile=$ftext=$_FILES['thefile']['name'][$i]; //檔名
     if ($NowFile!="") {
      //檢驗副檔名
      $expand_name=explode(".",$NowFile);
      $nn=count($expand_name)-1;  //取最後一個當附檔名
      $ATTR=strtolower($expand_name[$nn]); //轉小寫副檔名
      //檢測是否允許上傳此類型檔案
      if (check_file_attr($ATTR)) {
      //新名 , 附屬在 $idnumber 留言中
      $filename=$nsn.date("y").date("m").date("d").date("H").date("i").date("s").$i.".".$ATTR;
      copy($_FILES['thefile']['tmp_name'][$i],$UPLOAD_NEWS_PATH.$filename);
      //替file建立sn      
       $query="insert into contest_files (nsn,ftext,filename) values ('$nsn','$ftext','$filename')";
       mysql_query($query);
      } // end if check_file_attr
     } 
    }// end for
  } //end if file 	

}

//檢測檔案類型
function check_file_attr($ATTR) {
 global $PHP_FILE_ATTR;
 if (strpos(" ".$PHP_FILE_ATTR,$ATTR)) {
  return true;
 } else {
  return false;
 }
}

//學生登入
function stud_login($active,$INFO) {
 global $PHP_CONTEST;
 $query="select * from contest_setup where endtime>'".date('Y-m-d H:i:s')."' and active='".$active."' order by sttime";
 $result=mysql_query($query);
 if (mysql_num_rows($result)==0) {
  echo "目前系統中沒有相關競賽(類別:".$PHP_CONTEST[$active].") 正在進行或即將進行!";
  exit();
 }
 $STUD=get_student($_SESSION['session_tea_sn']);
?>
<br>
<SCRIPT TYPE="text/javascript">
	<!--
		function submitenter(myfield,e)	{
			var keycode;
				if (window.event) keycode = window.event.keyCode;
				else if (e) keycode = e.which;
				else return true;
				if (keycode == 13) 	{
					document.myform.act.value='login';
   				document.myform.submit();
   			  return false;
   			} else {
   				return true;
   			}
		} // end function
//-->
</SCRIPT>


<div align="center">
	<table border="0" width="500">
   <tr>
   	  <td>系統時間：<?php echo date("Y-m-d H:i:s");?></td>

   </tr>
	 <tr>
	  <td>
	   學生競賽登入：<?php echo $STUD['class_name']." ".$STUD['seme_num']."號 ".$STUD['stud_name'];?>
	  </td>
	 </tr>
	 <tr>
	  <td>
	<table border="1" width="500" style="border-collapse: collapse" bordercolor="#800000">
		<tr>
			<td>
			<table border="0" width="500" cellpadding="5">
        <tr>
        	<td width="100" bgcolor="#CCFFCC" style="font-size:10pt" align="center">請選擇競賽項目</td>
        	<td bgcolor="#CCFFCC" style="font-size:10pt">
        		<select size="1" name="tsn">
        			<?php
        			while ($row=mysql_fetch_array($result)) {
        			?>
        			<option value="<?php echo $row['tsn']?>"><?php echo $row['title'];?>(<?php echo $PHP_CONTEST[$row['active']];?>)</option>
        		 <?php
 							} //end while
        		 ?>
        	</td>
        </tr>
				<tr>
        	<td width="100" bgcolor="#CCFFCC" style="font-size:10pt" align="center">請輸入競賽密碼</td>
        	<td bgcolor="#CCFFCC" style="font-size:10pt"><input type="text" name="password" size="5" onKeyPress="return submitenter(this,event)"></td>
				</tr>        
			</table>
			</td>
		</tr>
	</table>
	  
	  </td>
	 </tr>
	</table>
	
	<br>
  <input type="button" style="color:#FF0000" value="確認無誤登入" onclick="document.myform.act.value='login';document.myform.submit();"> 
  <br><br>
  <font color="#FF0000"><?php echo $INFO;?></font>
</div>
 <?php
}


//取得學生資料
function get_student($student_sn) {
  global $c_curr_seme;
  $query="select a.stud_name,b.seme_class,b.seme_num from stud_base a,stud_seme b where a.student_sn=b.student_sn and a.student_sn='$student_sn' and b.seme_year_seme='$c_curr_seme'";
  $res=mysql_query($query);
  $stud=mysql_fetch_array($res,1);
  //轉換中文班級名稱
  $C=sprintf('%03d_%d_%02d_%02d',substr($c_curr_seme,0,3),substr($c_curr_seme,-1,1),substr($stud['seme_class'],0,1),substr($stud['seme_class'],1,2));
  $class_base=class_id_2_old($C);
  $stud['class_name']=$class_base[5]; //班名稱 一年1班, 一年2班....
  
  return $stud;
  
}



//學生作答記錄 (查資料),傳入 $TEST array , $student_sn , 傳回 array [0]=1有上傳，array[0]=0 無上傳，arrray[1]=顯示的訊息
function get_stud_record1_info($TEST,$student_sn) {
     	 $query="select count(*) as num from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."'";
    	 list($N)=mysql_fetch_row(mysql_query($query));
    	 $RR[1]="已作答 ".$N." 題";
    	 $RR[0]=($N==0)?0:1;   //1表有作答，0表無作答
    	 
    	 
     	 //學生已評分記錄
 	 		 $chk_right=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."' and chk=1"));
     	 $chk_none=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."' and chk=0"));
     	 $chk_wrong=mysql_num_rows(mysql_query("select * from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."' and chk=-1"));
   	 
    	 if ($chk_none==$N) {
    	 	$RR[2]=0;
    	 	$RR[3]="尚未評分";
    	  }else{
    	  $RR[2]=1;
    	  $RR[3]="答對 ".$chk_right." 題，答錯 ".$chk_wrong." 題";	
    	 }

       //取得最後作答時間
       if ($RR['0']==1) {
        $query="select anstime from contest_record1 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."' order by anstime desc limit 0,1";
        list($t)=mysql_fetch_row(mysql_query($query));
        $RR[4]=$t;
       } else {
         $RR[4]="無作答";
       }
    	 return $RR;
}


//學生作答記錄 (作品上傳),傳入 $TEST array , $student_sn , 傳回 array [0]=1有上傳，array[0]=0 無上傳，arrray[1]=顯示的訊息
function get_stud_record2_info($TEST,$student_sn) {
  global $UPLOAD_U;
    	 $query="select filename from contest_record2 where tsn='".$TEST['tsn']."' and student_sn='".$student_sn."'";
    	 list($FILE)=mysql_fetch_row(mysql_query($query));
    	 if ($FILE!="") {
    	   //$RR[1]="<a href='".$UPLOAD_U[$TEST['active']].$FILE."' target='_blank'>觀看</a>";

			 if ($TEST['active']==7) {
				 $RR[1]="<img src=\"./images/view.png\" height=\"18\" style=\"cursor: pointer\" onclick=\"show_scratch('{$FILE}')\">";
			 } else {
				 $RR[1]="<img src=\"./images/view.png\" height=\"18\" style=\"cursor: pointer\" onclick=\"show_pic('{$FILE}')\">
				 <a href=\"{$UPLOAD_U[$TEST['active']]}{$FILE}\" target=\"_blank\"><img src=\"./images/download.png\" height=\"18\"></a>";
			 }


    	   $RR[0]=1;
    	   $RR[4]=$FILE;
    	  }else{
    	   $RR[1]="未上傳!";
     	   $RR[0]=0;
    	  }   	 
    	  
     	 $query="select count(*) as num,AVG(score) as score from contest_score_record2 where score>0 and tsn='".$TEST['tsn']."' and student_sn='".$student_sn."'";
    	 $result=mysql_query($query);
  	 	 $WORKS=mysql_fetch_array($result,1); //會用到 score 欄位
    	 $RR[2]=$WORKS['num'];  //幾個成績 ,0表未評分
    	 $RR[3]=round($WORKS['score'],2); 

   return $RR;
}

//學生打字比賽記錄
function get_stud_record_type($TEST,$student_sn) {
	global $CONN;
	$query="select * from contest_typerec where race_id='".$TEST['tsn']."' and student_sn='".$student_sn."'";
	$res=$CONN->Execute($query);
	if ($res->RecordCount()>0) {
		$RR[0]=1;
		$row=$res->fetchrow();
		$n=0;
		$s=0;
		if ($row['endtime_1']!='0000-00-00 00:00:00') $n++;
		if ($row['endtime_2']!='0000-00-00 00:00:00') $n++;
		if ($row['sttime_1']!='0000-00-00 00:00:00') $s++;
		if ($row['sttime_2']!='0000-00-00 00:00:00') $s++;
		$RR[2]=$s;   //練習了幾次
		if ($n>0) {
			$RR['speed']=round($row['score_speed']/10,2);
			$RR[1]="完成　".$n."　次檢測，速度：".$RR['speed']." 字/分，積分：".$row['score_speed']."，正確率：".$row['score_correct'];
			$RR['correct']=$row['score_correct'];
		} else {
			$RR[1]="已登入比賽，檢測了 $s 次，尚無成績 (請注意該生有沒有完成檢測) ";
		}

	} else {
	 $RR[0]=0;
	 $RR[1]="無檢測記錄";
		$RR['speed']=0;
	}

	return $RR;
}


function showhtml($w) {
 $w=preg_replace("/\n/","<br>\n",$w);
 $regex = "{ ((https?|telnet|gopher|file|wais|ftp):[\\w/\\#~:.?+=&%@!\\-]+?)(?=[.:?\\-]*(?:[^\\w/\\#~:.?+=&%@!\\-]|$)) }x";
 return preg_replace($regex, "<a href=\"$1\" target=\"_blank\" alt=\"$1\" title=\"$1\">$1</a>",$w);
}

function shownewhtml($w,$h) {
	if ($h==0) {
   $w=preg_replace("/\n/","<br>\n",$w);
  }
 $regex = "{ ((https?|telnet|gopher|file|wais|ftp):[\\w/\\#~:.?+=&%@!\\-]+?)(?=[.:?\\-]*(?:[^\\w/\\#~:.?+=&%@!\\-]|$)) }x";
 return preg_replace($regex, "<a href=\"$1\" target=\"_blank\" alt=\"$1\" title=\"$1\">$1</a>",$w);
}

//縮圖程式
/**
 The MIT License

 Copyright (c) 2007 <Tsung-Hao>

 Permission is hereby granted, free of charge, to any person obtaining a copy
 of this software and associated documentation files (the "Software"), to deal
 in the Software without restriction, including without limitation the rights
 to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 copies of the Software, and to permit persons to whom the Software is
 furnished to do so, subject to the following conditions:

 The above copyright notice and this permission notice shall be included in
 all copies or substantial portions of the Software.

 THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 THE SOFTWARE.
 *
 * 抓取要縮圖的比例, 下述只處理 jpeg
 * $from_filename : 來源路徑, 檔名, ex: /tmp/xxx.jpg
 * $save_filename : 縮圖完要存的路徑, 檔名, ex: /tmp/ooo.jpg
 * $in_width : 縮圖預定寬度
 * $in_height: 縮圖預定高度
 * $quality  : 縮圖品質(1~100)
 *
 * Usage:
 *   ImageResize('ram/xxx.jpg', 'ram/ooo.jpg');
 */
function ImageResize($from_filename, $save_filename, $in_width=400, $in_height=300, $quality=100)
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

    // $function_name: set function name
    //   => imagecreatefromjpeg, imagecreatefrompng, imagecreatefromgif
    /*
    // $sub_name = jpeg, png, gif
    $function_name = 'imagecreatefrom' . $sub_name;

    if ($sub_name=='png')
        return $function_name($image_new, $save_filename, intval($quality / 10 - 1));

    $image = $function_name($filename); //$image = imagecreatefromjpeg($filename);
    */
    
    
    //$image = imagecreatefromjpeg($from_filename);
    
    $function_name = 'imagecreatefrom'.$sub_name;
    $image = $function_name($from_filename);

    imagecopyresampled($image_new, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    return imagejpeg($image_new, $save_filename, $quality);
    
   
     
    
}

/**
 * 抓取要縮圖的比例
 * $source_w : 來源圖片寬度
 * $source_h : 來源圖片高度
 * $inside_w : 縮圖預定寬度
 * $inside_h : 縮圖預定高度
 *
 * Test:
 *   $v = (getResizePercent(1024, 768, 400, 300));
 *   echo 1024 * $v . "\n";
 *   echo  768 * $v . "\n";
 */
function getResizePercent($source_w, $source_h, $inside_w, $inside_h)
{
    if ($source_w < $inside_w && $source_h < $inside_h) {
        return 1; // Percent = 1, 如果都比預計縮圖的小就不用縮
    }

    $w_percent = $inside_w / $source_w;
    $h_percent = $inside_h / $source_h;

    return ($w_percent > $h_percent) ? $h_percent : $w_percent;
}

  




//底下為 java function ============================================================
?>
<Script Language="JavaScript">
  function confirmdelete(theSqlQuery)
  {
    var is_confirmed = confirm('您確定要 :\n' + theSqlQuery);
    if (is_confirmed) {
     return true;
    }else{
     return false;
    }
  }
  
  //顯示剩餘時間
	function checkLeaveTime() 
	{
    var strLeaveMin=Math.floor(inttestsec/60);
    var strLeaveSec=inttestsec-Math.floor(inttestsec/60)*60;
     if (strLeaveSec<10) { strLeaveSec="0"+strLeaveSec; }
     if (strLeaveMin<10) { strLeaveMin="0"+strLeaveMin; }
    showLeaveTime=strLeaveMin+"分"+strLeaveSec+"秒";
    document.myform.time.value=showLeaveTime;
    if (inttestsec<=0) {
     document.myform.act.value=ACT;
     document.myform.submit(); //自動送出
    }
    inttestsec=inttestsec-1;
    TestTimer=setTimeout("checkLeaveTime()",1000);
  }

  
</Script>
