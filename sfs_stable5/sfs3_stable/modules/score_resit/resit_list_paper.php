<?php	
header('Content-type: text/html;charset=big5');
// $Id: index.php 5310 2009-01-10 07:57:56Z smallduh $
//取得設定檔
include_once "config.php";

//驗證是否登入
sfs_check(); 
//製作選單 ( $school_menu_p陣列設定於 module-cfg.php )
$tool_bar=&make_menu($school_menu_p);

//讀取補考學期別設定
$sql="select * from resit_seme_setup limit 1";
$res=$CONN->Execute($sql);
$SETUP=$res->fetchrow();
$C_year_seme=substr($SETUP['now_year_seme'],0,3)."學年度 第 ".substr($SETUP['now_year_seme'],-1)." 學期";

$seme_year_seme=$SETUP['now_year_seme'];

//目前處理的學年學期
$sel_year = substr($SETUP['now_year_seme'],0,3);
$sel_seme = substr($SETUP['now_year_seme'],-1);

//抓取班級設定裡的班級名稱
$class_base= class_base($curr_year_seme);

$score_sn=$_GET['sn'];
$scope=$_GET['scope'];
$Cyear=$_GET['Cyear'];

$paper_setup=get_paper_sn($SETUP['now_year_seme'],$Cyear,$scope);

//已選定的年級

 		if($Cyear>2){
			$ss_link=array("語文"=>"language","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
		} else {
			$ss_link=array("語文"=>"language","數學"=>"math","健康與體育"=>"health","生活"=>"life","綜合活動"=>"complex");
			$link_ss=array("language"=>"語文","math"=>"數學","health"=>"健康與體育","life"=>"生活","complex"=>"綜合活動");
		}

$sql="select a.*,b.stud_id,b.stud_name,b.curr_class_num from resit_exam_score a,stud_base b where a.sn='$score_sn' and a.student_sn=b.student_sn";
$res=$CONN->Execute($sql) or die ("讀取試卷資料發生錯誤! SQL=".$sql);
$row=$res->fetchRow();
$curr_class_num=$row['curr_class_num'];

$seme_class=substr($curr_class_num,0,3);
$seme_num=substr($curr_class_num,-2);

echo "<font color=red>補考學期別：".$C_year_seme."</font><br>";
echo "<font color=red>補考領域：".$link_ss[$scope]."</font>，".$class_base[$seme_class].$seme_num."號 ".$row['stud_name']."，補考得分：".$row['score']."<br>";
echo "<hr><br>";

$items=unserialize($row['items']);
$answers=unserialize($row['answers']);
?>
 <table border="0">
 	<tr>
 	  <td>
 	  <span id="show_buttom">
 	  	<input type="button" id="list_paper_end" value="結束檢視" onclick="window.close()">
 	  	<table border='0'>
 	  	
		<?php
		$i=0;
    foreach ($items as $k=>$v) {
    	$i++;
				?>
				<tr><td><hr></td></tr>
				<tr>
					<td><?php echo show_item($v,2,$answers[$k],$i);?></td>
				</tr>
				<?php 			  
    
    } // end foreach
		?>
		</table>
 	  </span>
 	  </td>
 	</tr>
 </table> 	


