
CREATE TABLE IF NOT EXISTS `artical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `password` varchar(10) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_publish` tinyint(1) DEFAULT NULL,
  `teacher_sn` int(11) NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- --------------------------------------------------------

--
-- 資料表格式： `artical_detail`
--

CREATE TABLE IF NOT EXISTS `artical_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) DEFAULT NULL,
  `content` text,
  `publish_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hits` int(11) DEFAULT '1',
  `artical_id` int(11) NOT NULL,
  `student_sn` int(11) DEFAULT NULL,
  `image_align` tinyint(4) NOT NULL DEFAULT '0',
  `teacher_sn` int(11) NOT NULL,
  `class_number` varchar(5) NOT NULL,
  `photo_ext` varchar(5) NOT NULL,
  `photo_memo` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_artical_detail_artical1` (`artical_id`)
);

-- --------------------------------------------------------


CREATE TABLE IF NOT EXISTS `artical_paramter` (
  `id` int(11) NOT NULL,
  `paramter` text NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

--
-- 列出以下資料庫的數據： `artical_paramter`
--

INSERT INTO `artical_paramter` (`id`, `paramter`, `update_time`) VALUES
(1, '', '2010-06-15 01:30:39');
