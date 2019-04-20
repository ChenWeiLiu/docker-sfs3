<?php
// $Id: module-upgrade.php 5765 2009-11-25 06:00:23Z brucelyc $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2009-11-04.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `csrc_record` change `id` `id` int(10) unsigned NOT NULL auto_increment";
	if ($CONN->Execute($query)) {
		$temp_query = "修改id屬性 -- by brucelyc (2009-11-04)\n$query";
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2009-11-25.txt";
if (!is_file($up_file_name)){
	$query_arr=array();
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,0,'意外事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,1,'校內交通意外事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,2,'校外教學交通意外事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,3,'校外交通意外事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,4,'食物中毒')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,5,'實驗室毒化物中毒')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,6,'其他毒化物中毒')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,7,'溺水事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,8,'運動、遊戲傷害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,9,'墜樓事件(非自殺)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,10,'攜子自殺')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,11,'學生自殺、自傷')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,12,'教職員工自殺、自傷')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,13,'山難事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,14,'實驗、實習傷害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,15,'工地整建傷人事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,16,'建築物坍塌傷人事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,17,'工讀場所傷害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (1,18,'其他意外傷害事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,0,'安全維護事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,1,'校內火警')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,2,'校外火警')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,3,'校內設施(備)遭破壞')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,4,'爆裂物危害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,5,'校屬財產、器材遭竊')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,6,'其他財物遭竊')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,7,'賃居糾紛事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,8,'交易糾紛')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,9,'網路糾紛')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,10,'遭詐騙事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,11,'遭殺害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,12,'遭強盜搶奪')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,13,'遭恐嚇勒索')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,14,'遭擄人勒贖')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,15,'遭性侵害或猥褻(18歲以上)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,16,'遭性侵害而至懷孕(18歲以上)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,17,'遭性騷擾(18歲以上)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,18,'其他遭暴力傷害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,19,'外人侵入騷擾師生事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,20,'遭外人入侵、破壞學校資訊系統')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,21,'校屬人員遭電腦網路詐騙事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (2,22,'其他校園安全維護事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,0,'暴力事件與偏差行為')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,1,'械鬥兇殺事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,2,'幫派鬥毆事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,3,'一般鬥毆事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,4,'疑涉殺人事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,5,'疑涉強盜搶奪')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,6,'疑涉恐嚇勒索')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,7,'疑涉擄人綁架')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,8,'疑涉偷竊案件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,9,'疑涉賭博事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,10,'疑涉性侵害或猥褻')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,11,'疑涉性騷擾事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,12,'疑涉及槍砲彈藥刀械管制事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,13,'疑涉及違反毒品危害防制條例')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,14,'疑涉妨害秩序、公務')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,15,'疑涉妨害家庭')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,16,'疑涉縱火、破壞事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,17,'飆車事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,18,'其他違法事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,19,'離家出走未就學(高中職(含)以上)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,20,'學生騷擾學校典禮事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,21,'學生騷擾教學事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,22,'幫派介入校園')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,23,'學生集體作弊')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,24,'入侵、破壞學校資訊系統')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,25,'電腦網路詐騙犯罪案件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,26,'有從事性交易或從事之虞者')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (3,27,'其他校園暴力或偏差行為')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,0,'管教衝突事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,1,'師長與學生間衝突事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,2,'師長與家長間衝突事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,3,'行政人員與學生間衝突')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,4,'行政人員與家長間衝突')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,5,'體罰、凌虐事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,6,'學生抗爭事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (4,7,'其他有關管教衝突事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,0,'兒童少年保護事件(18歲以下)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,1,'在外遊蕩')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,2,'離家出走三日內')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,3,'出入不正當場所')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,4,'兒童及少年福利法之保護輔導個案')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,5,'遭遺棄')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,6,'兒童或少年強迫婚嫁')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,7,'兒童或少年遭非法利用')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,8,'拐、綁、買、押兒童及少年')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,9,'拍攝、提供有害身心之影帶、圖書等')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,10,'施用(毒品、管制藥品)有害身心健康物質')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,11,'遭身心虐待')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,12,'迫誘兒童或少年猥褻或性交')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,13,'兒童或少年有從事性交易或從事之虞者')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,14,'其他違反兒童及少年性交易防治條例')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,15,'遭性侵害或猥褻(18歲以下)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,16,'遭性侵害而至懷孕(18歲以下)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,17,'遭性騷擾(18歲以下)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,18,'其他兒童少年保護事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (5,19,'高風險家庭')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,0,'天然災害事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,1,'風災')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,2,'水災')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,3,'震災')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,4,'山崩或土石流')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,5,'雷擊')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,6,'入侵紅火蟻')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (6,7,'其他重大災害')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,0,'疾病事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,1,'一般疾病')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,2,'法定疾病(腸病毒)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,3,'法定疾病(結核病)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,4,'法定疾病(猩紅熱)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,5,'法定疾病(百日咳)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,6,'法定疾病(水痘)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,7,'法定疾病(登革熱)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,8,'法定疾病(SARS)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,9,'法定疾病(其他)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (7,10,'一般疾病(紅眼症)')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,0,'其他事件')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,1,'教職員之間的問題')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,2,'總務的問題')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,3,'人事的問題')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,4,'行政的問題')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,5,'教務的問題')";
	$query_arr[] = "insert into csrc_item (main_id,sub_id,memo) values (8,6,'其他的問題')";
	while(list($k,$v)=each($query_arr)) {
		echo $v."<br>";
		$CONN->Execute($v);
	}
	$temp_query = "加入校安事項 -- by brucelyc (2009-11-25)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}
?>
