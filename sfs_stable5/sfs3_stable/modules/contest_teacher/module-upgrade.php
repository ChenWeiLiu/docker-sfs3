<?php
//$Id: module-upgrade.php 6737 2012-04-06 12:25:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
// reward_reason和reward_base 欄位屬性為text

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//以上保留--------------------------------------------------------


$up_file_name =$upgrade_str."2013-03-02.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_setup` ADD `password` varchar(4) NULL" ; //競賽密碼設計
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 競賽時可設計密碼才能進入-- by smallduh (2013-03-02)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


$up_file_name =$upgrade_str."2013-03-03.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_setup` ADD `delete_enable` tinyint(1) not NULL default '0'" ; //允許刪除
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "新增欄位, 是否允許刪除-- by smallduh (2013-03-03)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//修改資料表，增加競賽密碼設計
$up_file_name =$upgrade_str."2013-03-04.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_setup` CHANGE `qtext` `qtext` text not NULL" ; //
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改題目欄位格式為文字 text -- by smallduh (2013-03-04)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//修改資料表，查資料比賽增加記錄評分老師功能
$up_file_name =$upgrade_str."2013-03-08.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_record1` ADD `teacher_sn` int(10) NULL" ; //
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "查資料比賽增加記錄評分老師功能 -- by smallduh (2013-03-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//修改資料表，增加打字測驗
$up_file_name =$upgrade_str."2017-02-03.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "create TABLE `contest_typebank` ("; 	//打字題庫
	$query[0].="id int(5) not null auto_increment,";  	//流水號
	$query[0].="kind tinyint(1) not null,";				//類別 1中打 2英打
	$query[0].="article varchar(100) not null,";		//文章標題
	$query[0].="content text not null,";				//文章內容
	$query[0].="open tinyint(1) not null,";				//是否開放練習
	$query[0].="primary key (id)";
	$query[0].=") ENGINE=MyISAM ";

	$query[1] = "create TABLE `contest_typerec` ("; 	//打字比賽記錄
	$query[1].="id int(5) not null auto_increment,";  	//流水號
	$query[1].="race_id int(5) not null,";				//競賽流水號 , 每個競賽有兩次打字機會
	$query[1].="type_id_1 int(5) not null,";			//使用題庫流水號1
	$query[1].="type_id_2 int(5) not null,";			//使用題庫流水號2
	$query[1].="student_sn int(10) not null,";			//學生
	$query[1].="sttime_1 datetime not null,";			//第１次開始時間
	$query[1].="endtime_1 datetime not null,";			//第１次結束時間
	$query[1].="answer_1 text not null,";				//第１次作答內容
	$query[1].="correct_1 decimal(3,2) not null,";		//第１次作答答對率
	$query[1].="speed_1 int(5) not null,";				//第１次速度 (正確字數)
	$query[1].="sttime_2 datetime not null,";			//第2次開始時間
	$query[1].="endtime_2 datetime not null,";			//第2次結束時間
	$query[1].="answer_2 text not null,";				//第2次作答內容
	$query[1].="correct_2 decimal(3,2) not null,";		//第2次作答答對率
	$query[1].="speed_2 int(5) not null,";				//第2次速度 (正確字數)
	$query[1].="score_correct decimal(3,2) not null,";	//最終答對率
	$query[1].="score_speed int(5) not null,";			//最終速度 (正確字數)
	$query[1].="primary key (id)";
	$query[1].=") ENGINE=MyISAM ";

	$art1 = "　　朋友買了一件衣料，綠色的底子帶白色方格，當她拿給我們看時，一位對圍棋十分感興趣\r\n";
	$art1.= "的同學說：「啊，好像棋盤似的。」\r\n";
	$art1.= "　　「我看倒有點像稿紙。」我說。\r\n";
	$art1.= "　　「真像一塊塊綠豆糕。」一位外號叫「大食客」的同學緊接著說。\r\n";
	$art1.= "我們不禁哄堂大笑，同樣的一件衣料，每個人卻有不同的感覺。那位朋友連忙把衣料用紙包好\r\n";
	$art1.= "，她覺得衣料就是衣料，不是棋盤，也不是稿紙，更不是綠豆糕。\r\n";
	$art1.= "　　人人的欣賞觀點不盡相同，那是和個人的性格與生活環境有關。\r\n";
	$art1.= "　　如果經常逛布店的話，便會發現很少有一匹布沒有人選購過；換句話說，任何質地或花色\r\n";
	$art1.= "的衣料，都有人欣賞它。一位鞋店的老闆曾指著櫥窗裡一雙式樣毫不漂亮的鞋子說：「無論怎\r\n";
	$art1.= "麼難看的樣子，還是有人喜歡，所以不怕賣不出去。」\r\n";
	$art1.= "　　就以「人」來說，又何嘗不是如此？也許我們看某人不順眼，但是在他的男友和女友心中\r\n";
	$art1.= "，往往認為他如「天仙」或「白馬王子」般地完美無缺。\r\n";
	$art1.= "　　人總會去尋求自己喜歡的事物，每個人的看法或觀點不同，並沒有什麼關係，重要的是人\r\n";
	$art1.= "與人之間，應該有彼此容忍和尊重對方的看法與觀點的雅量。\r\n";
	$art1.= "　　如果他能從這扇門望見日出的美，景你又何必要他走向那扇窗去聆聽鳥鳴呢？你聽你的烏\r\n";
	$art1.= "鳴，他看他的日出，彼此都會有等量的美的感受。人與人偶有不禁哄堂大笑，同樣的一件衣料\r\n";
	$art1.= "，每個人卻有不同的摩擦，往往都是由於缺乏那分雅量的緣故；因此，為了減少摩擦，增進和\r\n";
	$art1.= "諧，我們必須努力培養雅量。";


	$art2 ="As President Donald Trump's White House attempts to embark on a period of order and discipline, many in Washington \r\n";
	$art2.="are greeting the news with a collective eye roll.\r\n";
	$art2.="\r\n";
	$art2.="At the start of Trump's third week in office, top advisers are trying to move beyond the infighting and feuds inside\r\n";
	$art2.="the West Wing, which have alarmed Republicans and official Washington far more than the President himself.\r\n";
	$art2.="\r\n";
	$art2.="White House chief of staff Reince Priebus is asserting more authority to run things, administration officials say,\r\n";
	$art2.="in hopes of trying to \"keep things running smoothly\" after a rocky -- and active -- first two weeks.\r\n";
	$art2.="\r\n";
	$art2.="The administration has privately pledged to do a better job of keeping relevant government agencies and congressional\r\n";
	$art2.="allies in the loop when rolling out executive actions and legislative priorities -- a far cry from the sloppy implementation\r\n";
	$art2.="of Trump's travel ban. That experience left aides cringing at the public beating they were taking, and personally\r\n";
	$art2.="irritated Trump.\r\n";
	$art2.="\r\n";
	$art2.="\"The first 10 days there's a bit of learning the ropes for any incoming administration,\" said Jason Miller, a \r\n";
	$art2.="former spokesman for Trump's presidential campaign. \"They're going to be finding their sea legs and getting \r\n";
	$art2.="everything nailed down.\"\r\n";
	$art2.="\r\n";
	$art2.="Privately, lobbyists, congressional staffers and other GOP political operatives said they're dubious that an\r\n";
	$art2.="orderly White House is on the horizon.\r\n";
	$art2.="\r\n";
	$art2.="\"I just don't see how the leopard changes his spots,\" said one GOP operative, who declined to be named because this \r\n";
	$art2.="person didn't want to appear to be rooting against the President. \"He got to the job by drinking rocket fuel, and \r\n";
	$art2.="now people are wondering if he can sit down and delegate and be a responsible executive.\"\r\n";
	$art2.="\r\n";
	$art2.="Within the White House, Trump's team has been more intent on quashing stories about turf wars and internal conflict \r\n";
	$art2.="than actually resolving them, said a top Republican close to the administration.\r\n";
	$art2.="\r\n";
	$art2.="This Republican, who spoke on condition of anonymity to frankly discuss internal workings of the administration,\r\n";
	$art2.="said any suggestion that all conflicts between Priebus and chief strategist Steve Bannon have been eliminated are mistaken.\r\n";
	$art2.="\r\n";
	$art2.="And that doesn't much matter to Trump. He operates easily in tumultuous environments. When disagreements arise, staffers \r\n";
	$art2.="tend to duke it out before they head to the Oval Office, keeping most of the discord from Trump's view.\r\n";
	$art2.="\r\n";
	$art2.="The turmoil surrounding Trump has often been ascribed to whichever aide has his ear at the time. Priebus's style is more \r\n";
	$art2.="cautious; he cares about the details. Bannon favors disruptive action and isn't fazed by a little public outcry if it's \r\n";
	$art2.="in pursuit of sweeping change.\r\n";
	$art2.="\r\n";
	$art2.="But the reality is the frenzied pace -- and now the cycle of chaos to calm -- is mostly driven by Trump, according to people \r\n";
	$art2.="close to him.\r\n";
	$art2.="\r\n";
	$art2.="The President's priority was to move quickly to deliver on bold promises he made on the campaign trail. When he saw the backlash \r\n";
	$art2.="over the travel ban, he aimed to correct the process by tapping Priebus to run point going forward.\r\n";
	$art2.="\r\n";
	$art2.="It's a cyclical pattern that Republicans close to the White House predict will dominate at least the first year of his administration.\r\n";
	$art2.="\r\n";
	$art2.="\"We've been punked enough times,\" said one Republican operative in Washington, who spoke anonymously because this person works\r\n";
	$art2.="with the White House. \"The only thing that can change him is the weight of the office. And hopefully it begins to weigh on him.\"\r\n";
	$art2.="\r\n";
	$art2.="Trump may be largely immune to this kind of volatility, but everyone surrounding him is not. A number of former campaign staffers are \r\n";
	$art2.="seeking job opportunities within government agencies -- even as positions within the White House remain unfilled -- to distance \r\n";
	$art2.="themselves from the \"West Wing circus,\" according to a person familiar with the situation.";

	$query[2]="insert into contest_typebank (kind,article,content,open) VALUES ('1','雅量','".addslashes($art1)."','1')";
	$query[3]="insert into contest_typebank (kind,article,content,open) VALUES ('2','White House turmoil rankles Washington more than Trump','".addslashes($art2)."','1')";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "增加打字比賽用資料表 -- by smallduh (2017-02-03)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//修改資料表，增加打字測驗題目欄位
$up_file_name =$upgrade_str."2017-02-10.txt";
if (!is_file($up_file_name)) {
	$query[0] = "ALTER TABLE `contest_setup` ADD `type_id_1` int(5) not NULL" ; //打字比賽第1篇
	$query[1] = "ALTER TABLE `contest_setup` ADD `type_id_2` int(5) not NULL" ; //打字比賽第2篇
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "增加打字比賽題目欄位 -- by smallduh (2017-02-10)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}

//修改資料表欄位
$up_file_name =$upgrade_str."2017-02-11.txt";
if (!is_file($up_file_name)) {
	$query[0] = "ALTER TABLE `contest_typerec` CHANGE `correct_1` `correct_1` DECIMAL( 5, 2 ) NOT NULL ;";
	$query[1] = "ALTER TABLE `contest_typerec` CHANGE `correct_2` `correct_2` DECIMAL( 5, 2 ) NOT NULL ;";
	$query[2] = "ALTER TABLE `contest_typerec` CHANGE `score_correct` `score_correct` DECIMAL( 5, 2 ) NOT NULL ;";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改正確率欄位 -- by smallduh (2017-02-11)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}

?>