<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5" />
<meta name="description" content="freemind flash browser"/>
<meta name="keywords" content="freemind,flash"/>
<title>心智圖展示</title>
<script type="text/javascript" src="flashobject.js"></script>
<style type="text/css">
  
/* hide from ie on mac \*/
html {
  height: 100%;
  overflow: hidden;
}
                
#flashcontent {
  height: 96%;
}
/* end hide */
                  
body {
  height: 100%;
  margin: 0;
  padding: 0;
  background-color: #9999ff;
}
                                                
</style>

<script language="javascript">
  function giveFocus() { 
    document.visorFreeMind.focus();  
  }
</script></head>

<body onLoad="giveFocus();">
<?php
  echo $_GET["name"];
  echo " 的 ".$_GET["tn"]." 　作業: ";
  echo "<a href=\"".$_GET["uu"]."\">原始檔案下載</a>";
?>
<div id="flashcontent" onmouseover="giveFocus();">
  Flash plugin or Javascript are turned off.</br>
  Activate both  and reload to view the mindmap
</div>                 
<script type="text/javascript">
  function getMap(map){
    var result=map;
    var loc=document.location+'';
  
    return result;
  }
  var fo = new FlashObject("visorFreemind.swf", "visorFreeMind", "100%", "96%", 6, "#9999ff");
  fo.addParam("quality", "high");
  fo.addParam("bgcolor", "#a0a0f0");
  fo.addVariable("openUrl", "_blank");
  fo.addVariable("startCollapsedToLevel","3");
  fo.addVariable("maxNodeWidth","200");
//
  fo.addVariable("mainNodeShape","elipse");
  fo.addVariable("justMap","flase");
  fo.addVariable("initLoadFile",getMap("<?php
  echo $_GET["uu"];
//echo "freeMindFlashBrowser.mm";
?>"));
  fo.addVariable("defaultToolTipWordWrap",200);
  fo.addVariable("offsetX","left");
  fo.addVariable("offsetY","top");
  fo.addVariable("buttonsPos","top");
  fo.addVariable("min_alpha_buttons",20);
  fo.addVariable("max_alpha_buttons",100);
  fo.addVariable("scaleTooltips","false");    
                                                                                      
  fo.write("flashcontent");
  // ]]>
</script>

</body>
</html>
