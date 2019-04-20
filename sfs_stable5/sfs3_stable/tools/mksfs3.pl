#! /usr/bin/perl
#---------------------------------------------------------
# $Id: mksfs3.pl 5311 2009-01-10 08:11:55Z hami $
#
# mksfs3.pl --- SFS3 學務系統模組產生器 
# Written by OLS3 ver 1.0.2 (ols3@www.tnc.edu.tw)
#
# Copyright (C) 2003 OLS3 
# 本程式是自由軟體，可以依與 Perl 相同的授權條款散佈。
#---------------------------------------------------------

system("clear");

$COPYRIGHT=<<C1;
#---------------------------------------------------------
# mksfs3.pl --- SFS3 學務系統模組產生器 
# Written by OLS3 ver 1.0.2 (ols3@www.tnc.edu.tw)
# Copyright (C) 2003 OLS3 
# 本程式是自由軟體，可以依與 Perl 相同的授權條款散佈。
#
# 這個小程式可以幫您產生 SFS3 模組的骨幹檔案，您只要
# 編輯修改這些檔案，便可以很輕鬆地製作 SFS3 的模組。
#---------------------------------------------------------
C1

print $COPYRIGHT;

# Questions for creating SFS3 module

# Q1 模組作者
while(!$author) {
	print "\n您大名? (中英文皆可) ";
	$author=<>;
	chomp $author;
}

# Q2 聯絡 email
while(!$your_email) {
	print "\n您的電子郵件位址? ";
	$your_email=<>;
	chomp $your_email;
}

# Q3 模組中文名稱
while(!$module_chinese_name) {
	print "\n您的模組中文名稱? ";
	$module_chinese_name=<>;
	chomp $module_chinese_name;
}

# Q4 模組目錄名稱
while(!$module_ename) {
	print "\n模組目錄名稱? (英數字為佳) ";
	$module_ename=<>;
	chomp $module_ename;
	if ( -f $module_ename || -d $module_ename ) {
	    print "在您的現在路徑中，$module_ename 已存在了! 請換一個唄!\n";
	    $module_ename='';
	}
}

# Q 模組存放路徑
while(!$module_path) {
	print "\n模組存放路徑?(例：/var/www/html/sfs3/modules)\n    按 Enter ---> 目前的路徑\n    按 1 -------> /var/www/html/sfs3/modules\n    按 2 -------> /home/apache/htdocs/sfs3/modules)\n    按 3 -------> /usr/local/apache/htdocs/sfs3/modules)\n請輸入：[路徑/Enter/1/2/3]？ ";
	$module_path=<>;
	if ($module_path eq "\n") { $module_path='.'; }
	chomp $module_path;
	if ($module_path == 1) { $module_path='/var/www/html/sfs3/modules'; }
	if ($module_path == 2) { $module_path='/home/apache/htdocs/sfs3/modules'; }
	if ($module_path == 3) { $module_path='/usr/local/apache/htdocs/sfs3/modules'; }

	if ( ! -d $module_path ) {
	    print "$module_path 這個路徑不存在! 請重新輸入!\n";
	    $module_path='';
	} elsif ( ! -w $module_path ) {
	    print "$module_path 這個路徑您沒有寫入權! 請切換root身份或重新輸入!\n";
	    $module_path='';
	}
}



# Q5 模組用途簡述
while(!$module_simple_description) {
	print "\n模組用途簡述? ";
	$module_simple_description=<>;
	chomp $module_simple_description;
}

# Q6 模組主要檔名
while(!$mainfile) {
	print "\n模組主要檔名? (比如 easy.php) ";
	$mainfile=<>;
	chomp $mainfile;
}

# Q7 資料庫的資料表名稱
while($table_name eq '') {
	print "\n模組需用到的資料表名稱? (英數字/若無請按Enter) ";
	$table_name=<>;
}

chomp $table_name;

# Q8 模組版本
while(!$module_version) {
	print "\n模組版本? (格式範例：1.0.0) ";
	$module_version=<>;
	chomp $module_version;
}

# Q9 模組建立日期
while(!$module_create_date) {
	print "\n模組建立日期? (格式範例：2003/05/03) ";
	$module_create_date=<>;
	chomp $module_create_date;
}

# 開始建立
print "\n按 Enter 鍵開始自動產生模組標準檔 ....\n";
my $ans=<>;

my $mkdir_no=mkdir "$module_path/$module_ename", 0775;

if (!$mkdir_no) {
    print "開啟 $module_ename 目錄錯誤! $!\n";
    exit;
}

%FILES=(
1  => "author.txt",
2  => "config.php",
3  => "$mainfile",
4  => "index.php",
5  => "INSTALL",
6  => "module-cfg.php",
7  => "module.sql",
8  => "NEWS",
9  => "README",
10 => "MANIFEST"
);


$FILE_1=<<H1;
$module_ename - $module_chinese_name
原版作者：$author

$your_email $module_create_date

$module_simple_description
H1

#--------------------------------------------------


$FILE_2=<<H2;
<?php

// \$Id\$

require_once "./module-cfg.php";

include_once "../../include/config.php";

?>
H2

#--------------------------------------------------

$FILE_3=<<H3;
<?php

// 引入 SFS3 的函式庫
include "../../include/config.php";

// 引入您自己的 config.php 檔
require "config.php";

// 認證
sfs_check();

// 叫用 SFS3 的版頭
head("$module_chinese_name");

//
// 您的程式碼由此開始



// SFS3 的版尾
foot();

?>
H3

#--------------------------------------------------

$FILE_4=<<H4;
<?php
// \$Id\$
header("Location: $mainfile");
?>
H4

#--------------------------------------------------


$FILE_5=<<H5;
* $module_chinese_name

** 安裝法:

由 "系統管理" --> "模組權限管理"

** 若有其它安裝說明，請置於下：

====
註：這裡採用 outline 文件模式。

關於 outline，請參考 http://linux.tnc.edu.tw/techdoc/otl/ 的說明。
H5

#--------------------------------------------------


$FILE_6=<<H6;
<?php

// \$Id\$

//---------------------------------------------------
//
// 1.這裡定義：模組資料表名稱 (供 "模組權限設定" 程式使用)
//   這區的 "變數名稱" 請勿改變!!!
//-----------------------------------------------
//
// 若有一個以上，請接續此 \$MODULE_TABLE_NAME 陣列來定義
//
// 也可以用以下這種設法：
//
// \$MODULE_TABLE_NAME=array(0=>"lunchtb", 1=>"xxxx");
// 
// \$MODULE_TABLE_NAME[0] = "lunchtb";
// \$MODULE_TABLE_NAME[1]="xxxx";
//
// 請注意要和 module.sql 中的 table 名稱一致!!!
//---------------------------------------------------

// 資料表名稱定義

\$MODULE_TABLE_NAME[0] = "$table_name";

//---------------------------------------------------
//
// 2.這裡定義：模組中文名稱，請精簡命名 (供 "模組權限設定" 程式使用)
//
// 它會顯示給使用者
//-----------------------------------------------


\$MODULE_PRO_KIND_NAME = "$module_chinese_name";


//---------------------------------------------------
//
// 3. 這裡定義：模組版本相關資訊 (供 "自動更新程式" 取用)
//    這區的 "變數名稱" 請勿改變!!!
//
//---------------------------------------------------

// 模組最後更新版本
\$MODULE_UPDATE_VER="$module_version";

// 模組最後更新日期
\$MODULE_UPDATE="$module_create_date";


//---------------------------------------------------
//
// 4. 這裡請定義：您這支程式需要用到的：變數或常數
//---------------------------------^^^^^^^^^^
//
// (不想被 "模組參數管理" 控管者，請置放於此)
//
// 建議：請儘量用英文大寫來定義，最好要能由字面看出其代表的意義。
//
// 這區的 "變數名稱" 可以自由改變!!!
//
//---------------------------------------------------


// 待填


//---------------------------------------------------
//
// 5. 這裡定義：預設值要由 "模組參數管理" 程式來控管者，
//    若不想，可不必設定。
//
// 格式： var 代表變數名稱
//       msg 代表顯示訊息
//       value 代表變數設定值
//
// 若您決定將這些變數交由 "模組參數管理" 來控管，那麼您的模組程式
// 就要對這些變數有感知，也就是說：若這些變數值在模組參數管理中改變，
// 您的模組就要針對這些變數有不同的動作反映。
//
// 例如：某留言板模組，提供每頁顯示筆數的控制，如下：
// \$SFS_MODULE_SETUP[1] =
// array('var'=>"PAGENUM", 'msg'=>"每頁顯示筆數", 'value'=>10);
//
// 上述的意思是說：您定義了一個變數 PAGENUM，這個變數的預設值為 10
// PAGENUM 的中文名稱為 "每頁顯示筆數"，這個變數在安裝模組時會寫入
// pro_module 這個 table 中
//
// 我們有提供一個函式 get_module_setup
// 供您取用目前這個變數的最新狀況值，
//
// 使用法：
//
// \$ret_array =& get_module_setup("$module_ename")
//
//
// 詳情請參考 include/sfs_core_module.php 中的說明。
//
// 這區的 "變數名稱 \$SFS_MODULE_SETUP" 請勿改變!!!
//---------------------------------------------------


//\$SFS_MODULE_SETUP[0] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>1);

// 第2,3,4....個，依此類推： 

// \$SFS_MODULE_SETUP[1] =
//	array('var'=>"xxxx", 'msg'=>"yyyy", 'value'=>0);

// \$SFS_MODULE_SETUP[2] =
//	array('var'=>"ssss", 'msg'=>"tttt", 'value'=>1);

?>
H6


#--------------------------------------------------


$FILE_7=<<H7;
#
# 資料表格式： `$table_name`
#
# 請將您的資料表 CREATE TABLE 語法置於下。
# 若無，則請將本檔 module.sql 刪除。




H7

#--------------------------------------------------

$FILE_8=<<H8;
* $module_create_date $module_version

$module_simple_description

====
註：這裡採用 outline 文件模式。

關於 outline，請參考 http://linux.tnc.edu.tw/techdoc/otl/ 的說明。
H8

#--------------------------------------------------

$FILE_9=<<H9;
* 請參考 INSTALL 的說明

====
註：這裡採用 outline 文件模式。

關於 outline，請參考 http://linux.tnc.edu.tw/techdoc/otl/ 的說明。
H9


#--------------------------------------------------

$manifest=join "\n", sort values %FILES;

$FILE_10=<<HA;
* 本模組檔案列表清單：(MANIFEST)

$manifest

HA

#--------------------------------------------------


print "\n已自動產生以下 SFS3 標準模組檔案：\n\n";


foreach $n (keys %FILES) {

	$content="FILE_$n";
	create_module_file($FILES{$n}, $$content);

}


print "\n完成!\n自動產生的模組檔案全在 * $module_ename * 目錄中!\n\n";
print "請以這些標準模組檔為骨架，來設計您的模組!\n\n";
print "參考資源：\n";
print "http://linux.tnc.edu.tw/techdoc/sfs-module-howto/t1.html\n";


#--------------------------------------

sub create_module_file {
    my ($filename, $content)=@_;

	print "$module_path/$module_ename/$filename\n";
	open(F, "> $module_path/$module_ename/$filename") || die;
	print F $content;
	close(F);
}



