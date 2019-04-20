<?php

require "config.php";


sfs_check();


    // 叫用 SFS3 的版頭
head("本地端XML檢查");

$tool_bar=make_menu($toxml_menu);
echo $tool_bar;
?>
    <div class="prevnext">
        <li>本功能乃直接呼叫瀏覽器本身的 XML 驗證器進行 XML 語法檢驗，建議使用 Google Chrome 瀏覽器執行本功能。</li>
        <li>請直接利用文字編輯器，如 <a href="https://notepad-plus-plus.org/download" target="_blank">Notepad++</a> 開啟您的 XML 文件，然後使用「複製/貼上」功能，在表單中貼入您的XML 文件進行語法檢查。</li>
        <li>檢查出錯誤時，程式會立即中斷，請根據訊息立即進行修正，然後再重新「複製/貼上」進行檢驗，直到沒錯誤為止。</li>
        <textarea id="xml1" rows="20" style="width:100%" cols="20" name="xml1"></textarea>
        <span>
            <input type="button" value="進行驗證" onclick="validateXML('xml1')">
            <input type="button" value="清除內容" onclick="xml1.value=''">
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
                    alert("沒有找到錯誤！");
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
                    alert("沒有找到錯誤！");
                }
            }
            else
            {
                alert('您的瀏覽器不支持 XML 驗證器');
            }
        }
    </script>
<?php
// SFS3 的版尾
foot();



?>