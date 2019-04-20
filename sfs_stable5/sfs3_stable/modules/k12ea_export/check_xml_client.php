<?php

require "config.php";


sfs_check();


    // �s�� SFS3 �����Y
head("���a��XML�ˬd");

$tool_bar=make_menu($toxml_menu);
echo $tool_bar;
?>
    <div class="prevnext">
        <li>���\��D�����I�s�s���������� XML ���Ҿ��i�� XML �y�k����A��ĳ�ϥ� Google Chrome �s�������楻�\��C</li>
        <li>�Ъ����Q�Τ�r�s�边�A�p <a href="https://notepad-plus-plus.org/download" target="_blank">Notepad++</a> �}�ұz�� XML ���A�M��ϥΡu�ƻs/�K�W�v�\��A�b��椤�K�J�z��XML ���i��y�k�ˬd�C</li>
        <li>�ˬd�X���~�ɡA�{���|�ߧY���_�A�ЮھڰT���ߧY�i��ץ��A�M��A���s�u�ƻs/�K�W�v�i������A����S���~����C</li>
        <textarea id="xml1" rows="20" style="width:100%" cols="20" name="xml1"></textarea>
        <span>
            <input type="button" value="�i������" onclick="validateXML('xml1')">
            <input type="button" value="�M�����e" onclick="xml1.value=''">
        </span>
    </div>


    <script>
        var xt="",h3OK=1
        function checkErrorXML(x)
        {
            xt=""
            h3OK=1
            checkXML(x)
        }

        function checkXML(n)
        {
            var l,i,nam
            nam=n.nodeName
            if (nam=="h3")
            {
                if (h3OK==0)
                {
                    return;
                }
                h3OK=0
            }
            if (nam=="#text")
            {
                xt=xt + n.nodeValue + "\n"
            }
            l=n.childNodes.length
            for (i=0;i<l;i++)
            {
                checkXML(n.childNodes[i])
            }
        }

        function validateXML(txt)
        {
// code for IE
            if (window.ActiveXObject)
            {
                var xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async=false;
                xmlDoc.loadXML(document.all(txt).value);

                if(xmlDoc.parseError.errorCode!=0)
                {
                    txt="Error Code: " + xmlDoc.parseError.errorCode + "\n";
                    txt=txt+"Error Reason: " + xmlDoc.parseError.reason;
                    txt=txt+"Error Line: " + xmlDoc.parseError.line;
                    alert(txt);
                }
                else
                {
                    alert("�S�������~�I");
                }
            }
// code for Mozilla, Firefox, Opera, etc.
            else if (document.implementation.createDocument)
            {
                var parser=new DOMParser();
                var text=document.getElementById(txt).value;
                var xmlDoc=parser.parseFromString(text,"text/xml");

                if (xmlDoc.getElementsByTagName("parsererror").length>0)
                {
                    checkErrorXML(xmlDoc.getElementsByTagName("parsererror")[0]);
                    alert(xt)
                }
                else
                {
                    alert("�S�������~�I");
                }
            }
            else
            {
                alert('�z���s��������� XML ���Ҿ�');
            }
        }
    </script>
<?php
// SFS3 ������
foot();



?>