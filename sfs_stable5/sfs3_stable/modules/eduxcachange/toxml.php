<?php

// $Id: toxml.php 5588 2009-08-16 17:13:02Z infodaes $

// �ޤJ SFS3 ���禡�w
include "../../include/config.php";

// �ޤJ�z�ۤv�� config.php ��
require "config.php";

// �{��
sfs_check();

// �s�� SFS3 �����Y
head("��e�pXML�W��");

//
// �z���{���X�Ѧ��}�l

$tool_bar=make_menu($eduxcachange_menu);

echo $tool_bar;

/*
echo <<<HERE

<table border='1' cellpadding='4' cellspacing='0' bgcolor='#0000FF'><tr>
<td  bgcolor='#FFFFFF' class='small'>
�Ш|���E�~�@�e�ҵ{�G
<p>�ǥͦ��Z���q�ξ��y�q�l��ƥ洫�@�~�C</p>
<p>���@�~�̻P���w ������p�ǡоǥ;��y�����Z�����d��ƥ洫�W��з�3.0�� �зǬ����C</p>
<p>�Ա��аѦҡG<a href="http://www.edu.tw/moecc/content.aspx?site_content_sn=6011" target=_blank>�Ш|���Ш|��F���ƺ���</a></p>
</td>
</tr></table>

HERE;
*/

echo <<<HERE

<br>

<table style="border-style: solid;border-color: #FF0000;background-color: #FFCCCC;border-width: thin;border-collapse: collapse">
    <tr>
        <td style="padding:20px;color:#FF0000">
            <li>�����}�o���@�H�����ͲP�W���A���Ҳդw����x����@�C</li>
            <li>���󪺰��D�ԸߡA�гw����@�̡C</li>
        </td>
    </tr>
</table>

HERE;

// SFS3 ������
foot();

?>