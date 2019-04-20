<?php


//預設的引入檔，不可移除。
include "config.php";




sfs_check();

head("自訂樣版說明");
print_menu($menu_p);


// smarty的一些設定  -----------------------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates";

$tpl_defult=array("head"=>"prt_ps_head.htm","body"=>"prt_ps_body.htm","end"=>"prt_ps_end.htm");

//  自訂的樣本檔名  -----------------------------------
$tpl_self=array("head"=>"my_prt_ps_head.htm","body"=>"my_prt_ps_body.htm","end"=>"my_prt_ps_end.htm");



?>
<H3>如何調整成績証明通用版的範本檔</H3>
<HR size=1 color=red>
<P>1.您須對css與html語法有一些認識。<BR>
<P>2.複製所須的三個範本檔，並更改檔名。<BR>
<P>範本檔的位置放於<?=$template_dir?>目錄下。<BR>
內有<B><?=$tpl_defult[head]?></B>、<B><?=$tpl_defult[body]?></B>、<B><?=$tpl_defult[end]?></B>
三個檔案，這是系統預設的。<BR>
<P>您可複製上述三個檔案並更改檔名為：<BR>
<FONT COLOR='#009900'>
<B><?=$tpl_self[head]?></B>、<B><?=$tpl_self[body]?></B>、<B><?=$tpl_self[end]?></B></FONT>
<P>3.利用CSS與HTML語法修改<B><?=$tpl_self[body]?></B>的內容即可，另兩個檔並不重要,但一定要有。<BR>
<P><FONT COLOR='red'>注意</FONT>：有{{????}}字樣的東西不要任意的修改，那是程式會用到的變數。
<P>4.將上述三個改過的檔再傳到主機內。<BR>
<P>5.以後列印將以您的範本檔為主。
<P>6.同時放心的更新您的系統，因為系統只會更新預設範本檔。<BR>


<BR><BR>
<DIV style="color:blue" onclick="alert('作者群：\n陽明 江添河\n 和群 姚榮輝\n二林 紀明村\n草湖 曾彥鈞\n北斗 李欣欣\n大城 林畯城');">◎By 彰化縣學務系統開發小組</DIV>
<BR><BR>
