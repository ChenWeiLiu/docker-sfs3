<?php
// $Id: help.php 6032 2010-08-25 09:33:51Z infodaes $

include "config.php";
sfs_check();

//�q�X����
head("�ϥλ���");

//��V������
$linkstr="item_id=$item_id";
echo print_menu($MENU_P,$linkstr);

$help_doc="<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>�P<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: �з���'>�Ш|���Q�G�~����򥻱Ш|��T���G<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='http://12basic.edu.tw/'><span style='color: #0000CC; text-decoration: none'>http://12basic.edu.tw/</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: �s�ө���,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>�P<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: �з���'>���L���Q�G�~����򥻱Ш|��T���G<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://sites.google.com/a/ms.tnjh.ylc.edu.tw/ylc12/'><span style='color: #0000CC; text-decoration: none'>https://sites.google.com/a/ms.tnjh.ylc.edu.tw/ylc12/</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: �s�ө���,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>�P<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: �з���'>���L�ϧK�դJ�ǤΨ�������μҲվާ@�����G<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://docs.google.com/document/d/11WsWFk59BC7Sbi1YLAEdbXbk7iPpTdIwqLA9JbKQOvI/edit?usp=sharing'><span style='color: #0000CC; text-decoration: none'>https://docs.google.com/document/d/11WsWFk59BC7Sbi1YLAEdbXbk7iPpTdIwqLA9JbKQOvI/edit?usp=sharing</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: �s�ө���,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>�P<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: �з���'>���L�ϧK�դJ��12�~��жW�B��Ǳĭp���Z��Ӫ��G<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://drive.google.com/drive/folders/17PY35CHgXVuZlut1FFtJRIpsmB5qYXi6?usp=sharing'><span style='color: #0000CC; text-decoration: none'>https://drive.google.com/drive/folders/17PY35CHgXVuZlut1FFtJRIpsmB5qYXi6?usp=sharing</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: �s�ө���,serif'>
</span></p>
<p class='MsoNormal' style='text-indent: -17.85pt; line-height: 20.0pt; margin-left: 35.7pt'>
<span lang='EN-US' style='font-size: 10.0pt; font-family: Symbol'>�P<span style='font:7.0pt &quot;Times New Roman&quot;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</span></span><span style='font-size: 14.0pt; font-family: �з���'>106�Ǧ~�׶��L�ϰ��Ť����ǮէK�դJ�ǩe���|�Ҹդ��o�J�Ǩt�Υ��O�G<span lang='EN-US'><a style='color: blue; text-decoration: underline; text-underline: single' target='_BLANK' href='https://ylc.entry.edu.tw/NoExamImitate_YL/NoExamImitateHome/Apps/Page/Public/09/ChooseSys.aspx'><span style='color: #0000CC; text-decoration: none'>https://ylc.entry.edu.tw/NoExamImitate_YL/NoExamImitateHome/Apps/Page/Public/09/ChooseSys.aspx</span></a></span></span><span lang='EN-US' style='font-size: 14.0pt; font-family: �s�ө���,serif'>
</span></p>";
$help_doc .= "<div>
				�����G<br>
				<ul>
					<li type='1' value='1' style='margin:6px 30px;'>�N��J�ǻP�����p�սЦ�<a href='{$SFS_PATH_HTML}modules/sfs_man2/' target='_blank'>�i�Ҳ��v���޲z�j</a>�]�w</li>
					<li type='1' value='2' style='margin:6px 30px;'>�ǥͼ��y�����P�g�B�������ۨt��<a href='{$SFS_PATH_HTML}modules/reward/' target='_blank'>�i�ǥͼ��g�j</a>�ҲաC</li>
					<li type='1' value='3' style='margin:6px 30px;'>�ǥ��m�Ҭ������ۨt��<a href='{$SFS_PATH_HTML}modules/absent/' target='_blank'>�i���m�Ҽ��g�޲z�j</a>�ҲաC</li>
					<li type='1' value='4' style='margin:6px 30px;'>�v�ɪ��{�Ц�<a href='{$SFS_PATH_HTML}modules/career_race/' target='_blank'>�i�ͲP�����v�ɰO���j</a>�Ҳյn���C</li>
					<li type='1' value='5' style='margin:6px 30px;'>��A����{�Ц�<a href='{$SFS_PATH_HTML}modules/fitness/' target='_blank'>�i��A��޲z�j</a>�Ҳյn���C</li>
					<li type='1' value='6' style='margin:6px 30px;'>�ǥͨ����]�w�Ц�<a href='{$SFS_PATH_HTML}modules/stud_subkind/' target='_blank'>�i�ǥͨ����l���O�j</a>�Ҳյn���C</li>
				</ul>
			</div>";
echo $help_doc;
foot();
?>
