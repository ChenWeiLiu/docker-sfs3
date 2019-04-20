#$Id: module.sql 5311 2009-01-10 08:11:55Z hami $
#
# 資料表格式： `stud_clan`
#

CREATE TABLE IF NOT EXISTS `stud_subkind` (
  `student_sn` int(11) NOT NULL default '0',
  `clan` varchar(30) NOT NULL default '',
  `area` varchar(20) NOT NULL default '',
  `memo` varchar(20) NOT NULL default '',
  `note` varchar(20) NOT NULL default '',
  `type_id` varchar(5) NOT NULL default '',
  PRIMARY KEY  (`student_sn`,`type_id`)
);

#
# 資料表格式： `stud_subkind_ref`
#

CREATE TABLE IF NOT EXISTS `stud_subkind_ref` (
  `type_id` varchar(5) NOT NULL default '',
  `clan_title` varchar(20) NOT NULL default '',
  `area_title` varchar(20) NOT NULL default '',
  `memo_title` varchar(20) NOT NULL default '',
  `note_title` varchar(20) NOT NULL default '',
  `clan` text NOT NULL,
  `area` text NOT NULL,
  `memo` text NOT NULL,
  `note` text NOT NULL,
  PRIMARY KEY  (`type_id`)
) ;

INSERT INTO `stud_subkind_ref` VALUES ('1', '登記類別', '障礙程度', '', '', '視障\r\n聽障\r\n語障\r\n智障\r\n顏面傷殘\r\n多重障礙', '輕度\r\n中度\r\n中重度\r\n重度\r\n極重度', '', '');
INSERT INTO `stud_subkind_ref` VALUES ('2', '登記親代', '登記類別', '', '', '父\r\n母\r\n父母', '視障\r\n聽障\r\n語障\r\n智障\r\n顏面傷殘\r\n多重障礙', '', '');
INSERT INTO `stud_subkind_ref` VALUES ('3', '符合資格', '有效期限', '', '', '第一款\r\n第二款\r\n第三款', '年底(12/31)\r\n年中(06/30)', '', '');
INSERT INTO `stud_subkind_ref` VALUES ('9', '族別', '區域', '', '', '排灣族\r\n魯凱族\r\n卑南族\r\n阿美族\r\n泰雅族\r\n賽夏族\r\n布農族\r\n達悟族\r\n鄒族\r\n太魯閣族\r\n邵族\r\n噶瑪蘭\r\n雅美族\r\n撒奇萊雅族', '山地\r\n平地', '', '');
INSERT INTO `stud_subkind_ref` VALUES ('100', '外籍親代', '國籍', '已入籍', '年齡差距45以上', '父\r\n母', '阿富汗\r\n阿爾巴尼亞\r\n南極洲\r\n阿爾及利亞\r\n美屬薩摩亞\r\n安道爾共和國\r\n阿根廷\r\n澳大利亞\r\n奧地利\r\n孟加拉\r\n亞美尼亞\r\n比利時\r\n不丹\r\n玻利維亞\r\n巴西\r\n汶萊\r\n保加利亞\r\n緬甸\r\n白俄羅斯\r\n柬埔寨\r\n加拿大\r\n斯里蘭卡\r\n智利\r\n哥倫比亞\r\n哥斯大黎加\r\n古巴\r\n捷克\r\n丹麥\r\n多明尼加\r\n薩爾瓦多\r\n愛沙尼亞\r\n斐濟\r\n芬蘭\r\n法國\r\n喬治亞\r\n德國\r\n希臘\r\n瓜地馬拉\r\n宏都拉斯\r\n香港\r\n匈牙利\r\n冰島\r\n印度\r\n伊朗\r\n伊拉克\r\n愛爾蘭\r\n以色列\r\n義大利\r\n牙買加\r\n日本\r\n哈薩克\r\n北韓\r\n南韓\r\n科威特\r\n黎巴嫩\r\n拉脫維亞\r\n賴比瑞亞\r\n立陶宛\r\n盧森堡\r\n澳門\r\n馬來西亞\r\n馬爾地夫\r\n馬爾他\r\n墨西哥\r\n摩納哥\r\n蒙古\r\n摩洛哥\r\n莫三比克\r\n尼泊爾\r\n蘭\r\n紐西蘭\r\n尼加拉瓜\r\n奈及利亞\r\n挪威\r\n帛琉\r\n巴基斯坦\r\n巴拿馬\r\n巴拉圭\r\n祕魯\r\n波蘭\r\n葡萄牙\r\n羅馬尼亞\r\n俄羅斯\r\n塞內加爾\r\n新加坡\r\n南非\r\n西班牙\r\n史瓦濟蘭\r\n瑞典\r\n瑞士\r\n東加\r\n阿拉伯大公國\r\n突尼西亞\r\n土耳其\r\n烏克蘭\r\n馬其頓\r\n英國\r\n坦尚尼亞\r\n美國\r\n委內瑞拉\r\n尚比亞\r\n中國\r\n菲律賓\r\n泰國\r\n印度尼西亞\r\n越南\r\n', '\r\n是\r\n否', '\r\n是\r\n否');

INSERT INTO `sfs_text` VALUES (0, 100, 'stud_kind', 1, '100', '外籍或大陸配偶子女', '30,', 30, '.');    