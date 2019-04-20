<?php
//$Id: stud_psy_tran2.php 5974 2010-08-02 05:11:28Z hami $
require "config.php";

sfs_check();
set_time_limit(0);

$change_arr = array ('sse_s1'=>'喜愛困難科目','sse_s3'=>'特殊才能','sse_s4'=>'興趣','sse_s5'=>'生活習慣','sse_s6'=>'人際關係','sse_s7'=>'外向行為','sse_s8'=>'內向行為','sse_s9'=>'學習行為','sse_s10'=>'不良習慣','sse_s11'=>'焦慮行為');

$newarr ['sse_s1'] = array(1=>'語文',2=>'數學',3=>'自然與生活科技',4=>'社會',5=>'健康與體育',6=>'藝術與人文',7=>'生活課程',8=>'綜合活動',9=>'鄉土語言');
$newarr ['sse_s3'] = array(1=>'球類',2=>'田徑',3=>'游泳', 4=>'武術', 5=>'美術',6=>'樂器演奏', 7=>'歌唱', 8=>'工藝', 9=>'家事', 10=>'演說', 11=>'寫作', 12=>'舞蹈', 13=>'戲劇', 14=>'書法',15=>'珠算', 16=>'領導', 17=>'英打',18=>'中打',19=>'外語', 20=>'電腦', 21=>'其他');
$newarr['sse_s4'] = array(1=>'電視電影',2=>'閱讀',3=>'登山',4=>'露營',5=>'旅行郊遊',6=>'美術',7=>'划船游泳',8=>'釣魚',9=>'國術',10=>'樂器演奏',11=>'歌唱',12=>'音樂欣賞',13=>'舞蹈',14=>'繪畫',15=>'集郵',16=>'打球',17=>'編織',18=>'下棋',19=>'養小動物',20=>'作物栽培',21=>'電腦',24=>'其他');
$newarr['sse_s5'] = array(1=>'整潔',2=>'勤勞',3=>'節儉',4=>'作息有規律',5=>'骯髒',6=>'懶惰',7=>'浪費',8=>'作息無規律',9=>'其他');
$newarr['sse_s6'] = array(1=>'和氣',2=>'合群',3=>'活潑',4=>'信賴他人',5=>'好爭吵',6=>'自我中心',7=>'冷漠',8=>'多疑善妒',9=>'其他');
$newarr['sse_s7'] = array(1=>'領導力強',2=>'健談',3=>'慷慨',4=>'熱心公務',5=>'欺侮同學',6=>'常講粗話',7=>'好遊蕩',8=>'愛唱反調',9=>'其他');
$newarr['sse_s8'] = array(1=>'謹慎',2=>'文靜',3=>'自信',4=>'情緒穩定',5=>'畏縮',6=>'過份沉默',7=>'過份依賴',8=>'多愁善感',9=>'其他');
$newarr['sse_s9'] = array(1=>'專心',2=>'積極努力',3=>'有恆心',4=>'沈思好問',5=>'分心',6=>'被動馬虎',7=>'半途而廢',8=>'偏心某科',9=>'其他');
$newarr['sse_s10'] = array(1=>'無',2=>'發怪聲',3=>'口吃',4=>'作弄他人',5=>'吃指頭',6=>'咬筆',7=>'沉迷不良書刊',8=>'沉迷電動玩具',9=>'上課吃東西',10=>'說謊',11=>'吸煙',12=>'吸毒',13=>'其他');
$newarr['sse_s11'] = array(1=>'無',2=>'表情緊張',3=>'發抖',4=>'胸痛',5=>'坐立不安',6=>'玩弄東西',7=>'肚子痛',8=>'頭痛',9=>'思考障礙',10=>'其他');

$sel_sse = isset($_POST['sel_sse'])?$_POST['sel_sse']:'sse_s1';

$arr = sfs_text($change_arr[$sel_sse]);
$arr1 = $newarr[$sel_sse];

if ($_POST['act'] == '確定轉表'){
	$c_arr = array();
	$error_arr =array();
	foreach($arr as $id=>$val){
		if ($_POST["c_$id"] ==='')
		$error_arr[] = $id;
		elseif (!is_numeric($_POST["c_$id"])) // 不對應
		continue;
		$c_arr[$id] = $_POST["c_$id"];

	}
	if (count($error_arr)==0){
		if ($sel_sse == 'sse_s1'){ // 喜愛困難科目
			$start = 1; $end=2;
		}else{
			$start = substr($sel_sse,5);
			$end = $start;
		}

		for($si=$start;$si<=$end;$si++){
			$query = "SELECT seme_year_seme,stud_id,sse_s$si FROM stud_seme_eduh ";
			$res = $CONN->Execute($query) or trigger_error($query ,254);
			//echo $query; exit;
			foreach ($res as $row) {
				$ss1 = '';
				$temp_arr = explode(",",$row["sse_s$si"]);
				foreach($temp_arr as $data){
					if ($data<>'' and $c_arr[$data]<>'')
					$ss1 .=','.$c_arr[$data];
				}
				//echo $row["sse_s$si"]."--$ss1, <BR>";
				$seme_year_seme = $row['seme_year_seme'];
				$stud_id = $row['stud_id'];
		//		echo "$ss1  --".$row["sse_s$si"]."<br>";
				if ($ss1<>'' and   chop($row["sse_s$si"]) <> "$ss1,") {
					$query = "UPDATE  stud_seme_eduh  SET sse_s$si='$ss1,'  WHERE seme_year_seme='$seme_year_seme' AND stud_id='$stud_id'";

					$CONN->Execute($query) or die($query);
				}
				//	if ($i++>20) break;
			}
		}
		$t_kind = $change_arr[$sel_sse];
		// 更改舊表設定
		$query = " UPDATE   sfs_text SET g_id=9,t_kind='bak_$t_kind'  WHERE t_kind='$t_kind'";
		$CONN->Execute($query) or die($query);
	//echo $query;
		join_sfs_text(1,$t_kind,   $newarr[$sel_sse]);
		//print_r($newarr[$sel_sse]);
		$arr = sfs_text($t_kind);
	}

}

head('輔導記錄表轉換');
print_menu($menu_p);

?>

<table border='1' cellpadding='10' cellspacing='0' 	style='border-collapse: collapse' bordercolor='#111111' width='100%'  	id='AutoNumber1'>
<tr bgcolor='#EEEEEE'>
<td colspan="2"><?php  echo $submenu; ?></td>
</tr>
	<tr bgcolor='#FFCCCC'>
		<td align='center'>說 明</td>
		<td align='center'>新舊表轉換</td>
	</tr>
	<tr>
		<td width='45%' valign="top">

<ul>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>為何要進行轉表？</font></p>

  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>SFS3原輔導記錄源自教育部學籍資料交換標準 XML2.0
  設計，96年公佈之3.0標準與原2.0不同。為符合新公佈的 XML 3.0 標準，舊有記錄必須進行轉表動作。</font></p>
  </li>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>一定要進行轉表？</font></p>
  <p style='margin-top: 0; margin-bottom: 0'><font size='2'>輔導紀錄相關表冊,日後將以新表做資料參照，若貴校未進行轉表，會有資料擷取錯誤的情況。</font></p>
  </li>
  <li>
  <p style='margin-top: 0; margin-bottom: 0'><font color='#FF0000' size='2'>怎樣進行轉表？</font></p>
  <p style='margin-top: 0; margin-bottom: 0'>
  <font size='2'>請在右列表單,選擇輔導記錄表項目,進行新舊表對應轉換</font></p>
  <p style='margin-top: 0; margin-bottom: 0'>
  <span style='font-size:large;color:red;background-color: yellow;'>應謹慎操作轉換作業,若對應錯誤,將無法回復舊設定</span></p>
  </li>

</ul>

		</td>
		<td><?php if ($error_arr): ?>
		<h2>轉檔失敗! 下列欄位未選</h2>
		<ul style='background-color: yellow'>
		<?php foreach($error_arr as $val):  ?>
			<li><?php echo $arr[$val] ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
		<form action="<?php echo $_SERVER['PHP_SELF']?>" name="myform"
			id="myform" method="post">選擇輔導記錄表項目 <select name="sel_sse"
			onChange="this.form.submit()">
			<?php foreach($change_arr as $id=>$val):?>
			<option value="<?php echo $id ?>" <?php if ($id==$sel_sse): ?>
				selected <?php endif;?>><?php echo $val?></option>
				<?php endforeach;?>
		</select>
		 <?php if ($arr ===$arr1): ?>
				<input type="submit" name="act" value="已轉表完成" disabled>
		<?php else: ?>
				<input type="submit" name="act" value="確定轉表">
		<?php endif; ?>
		 <br />
		<table
			style="background-color: #eeffdd; border-collapse: collapse; width: 100%">
			<tr style="background-color: #dfd">
				<td style="text-align: right">舊表名稱</td>
				<td style="text-align: center">=></td>
				<td>新表名稱</td>
			</tr>
			<?php foreach($arr as $id=>$val):?>
			<tr>
				<td align="right"><?php $sel_key = array_search(chop($val),array_values($arr1));$i=0; ?>
				<span style="color: red"><?php echo $val?> </span></td>
				<td style="text-align: center">對應到</td>
				<td><select name="c_<?php echo $id?>">
					<option value="">--</option>
					<?php foreach($arr1 as $id2=>$val2):?>
					<option value="<?php echo $id2?>" <?php if ( $sel_key===$i++):?>
						selected <?php endif;?>><?php echo $val2  ?></option>
						<?php endforeach;?>
					<option vale="no">**不對應**</option>
				</select></td>
			</tr>
			<?php endforeach;?>
		</table>
		<input type="hidden" name="sel" value="<?php echo $sel ?>">
		</form>
		</td>
	</tr>
</table>
<?php
foot();
?>