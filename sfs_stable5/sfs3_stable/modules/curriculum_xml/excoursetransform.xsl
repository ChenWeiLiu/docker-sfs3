<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:msxsl="urn:schemas-microsoft-com:xslt">
  <!--<xsl:output method="html" version="1.0" encoding="UTF-8" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd" indent="yes" media-type="text/html"/>-->
  <xsl:template match="/">
    <html>
      <header>
        <meta http-equiv="Expires" content="0"/>
        <meta http-equiv="Cache-Control" content="no-cache"/>
        <meta http-equiv="Pragma" content="no-cache"/>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"/>
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"/>
        <script type="text/javascript" src="http://inservice.edu.tw/JS/jquery.tablesorter.min.js"/>
        <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css"/>
        <script type="text/javascript" src="http://inservice.edu.tw/JS/jquery.tablesorter.min.js"/>
        <link rel="stylesheet" href="http://inservice.edu.tw/CSS/blue/style.css"/>
        <script type="text/javascript">
        //<![CDATA[
					$(document).ready(function(){
					  $("table").tablesorter();
					  alert("利用XSLT將XML轉換成HTML+Javascript(jquery)的範例");
					});
				//]]>
		    </script>
      </header>
		  <body>
		    <h1>教師課表交換資料（建立日期：<xsl:value-of select="//@createdate"/>）</h1>
		    <hr/>
		    <h2>縣市資料</h2>
		    <table id="DataBase_exchangecity" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
		      <thead>
		        <tr>
		          <th>縣市名稱</th>
		          <th>學校數</th>
		        </tr>
		      </thead>
		      <tbody>
		      <xsl:for-each select="//exchangecity">
		        <tr>
		          <td><xsl:value-of select="@cityname"/></td>
		          <td><xsl:value-of select="count(exchangeschool)"/></td>
		        </tr>
		      </xsl:for-each>
		      </tbody>
		    </table>
		    <h2>學校資料</h2>
		    <table id="DataBase_exchangeschool" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
		      <thead>
		        <tr>
		          <th>縣市名稱</th>
				  <th>學校名稱</th>
				  <th>學校代碼</th>		          
		          <th>課表數</th>
		        </tr>
		      </thead>
		      <tbody>
		      <xsl:for-each select="//exchangeschool">
		        <tr>
		          <td><xsl:value-of select="../@cityname"/></td>
				  <td><xsl:value-of select="@schoolname"/></td>
		          <td><xsl:value-of select="@schoolid"/></td>
		          <td><xsl:value-of select="count(curriculumdata)"/></td>
		        </tr>
		      </xsl:for-each>
		      </tbody>
		    </table>
		    <h2>課表資料</h2>
		    <table id="DataBase_curriculumdata" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
		      <thead><tr><th>縣市名稱</th><th>學校名稱</th><th>學年度</th><th>學期</th><th>教師人數</th><th>課程筆數</th></tr></thead>
		      <tbody>
		      <xsl:for-each select="//curriculumdata">
		        <tr>
		          <td><xsl:value-of select="../../@cityname"/></td>
		          <td><xsl:value-of select="../@schoolname"/></td>
		          <td><xsl:value-of select="@syear"/></td>
		          <td><xsl:value-of select="@session"/></td>
		          <td><xsl:value-of select="count(teacherdata/teacher)"/></td>
              <td><xsl:value-of select="count(curriculums/curriculum)"/></td>
		        </tr>
		      </xsl:for-each>
		      </tbody>
		    </table>
		    <h2>教師資料</h2>
		    <table id="DataBase_teacher" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
		      <thead><tr><th>縣市名稱</th><th>學校名稱</th><th>學年度</th><th>學期</th><th>教師名稱</th><th>身分證統一編號</th><th>教師證</th><th>學習領域任教專門科目</th></tr></thead>
		      <tbody>
		      <xsl:for-each select="//teacher">
		        <tr>
		          <td><xsl:value-of select="../../../../@cityname"/></td>
		          <td><xsl:value-of select="../../../@schoolname"/></td>
		          <td><xsl:value-of select="../../@syear"/></td>
		          <td><xsl:value-of select="../../@session"/></td>
		          <td><xsl:value-of select="teacheruname"/></td>
              <td><xsl:value-of select="@idnumber"/></td>
              <td>
                <ul>
                  <xsl:for-each select="certificates/certificate">
                     <li><xsl:value-of select="."/>（日期：<xsl:value-of select="@certdate"/>）</li>
                  </xsl:for-each>
                </ul>
              </td>
              <td>
                <ul>
                  <xsl:for-each select="tachersubjects/tachersubject">
                     <li>學習領域：<xsl:value-of select="tachersubjectdomain"/><br/>任教專門科目：<xsl:value-of select="tachersubjectexpertise"/></li>
                  </xsl:for-each>
                </ul>
              </td>
		        </tr>
		      </xsl:for-each>
		      </tbody>
		    </table>
		    <h2>課程資料</h2>
		    <table id="DataBase_curriculum" class="tablesorter" border="0" cellpadding="0" cellspacing="1">
		      <thead><tr><th>縣市名稱</th><th>學校名稱</th><th>學年度</th><th>學期</th><th>授課教師身分證統一編號</th><th>年級</th><th>班級</th><th>週次</th><th>節次</th><th>科目規範</th><th>科目名稱</th></tr></thead>
		      <tbody>
		      <xsl:for-each select="//curriculum">
		        <tr>
		          <td><xsl:value-of select="../../../../@cityname"/></td>
		          <td><xsl:value-of select="../../../@schoolname"/></td>
		          <td><xsl:value-of select="../../@syear"/></td>
		          <td><xsl:value-of select="../../@session"/></td>
		          <td><xsl:value-of select="@teacheridnumber"/></td>
              <td><xsl:value-of select="@classyear"/></td>
              <td><xsl:value-of select="@classname"/></td>
              <td><xsl:value-of select="week"/></td>
              <td><xsl:value-of select="classtime"/></td>              
              <td>
                <xsl:if test="*[position()=last()][namespace-uri()='http://inservice.edu.tw/curriculumexchange/2011/10/curriculum10']">
                  高級中學課程標準暨綱要
                </xsl:if>
                <xsl:if test="*[position()=last()][namespace-uri()='http://inservice.edu.tw/curriculumexchange/2011/10/curriculum20']">
                  職業學校群科課程綱要
                </xsl:if>
                <xsl:if test="*[position()=last()][namespace-uri()='http://inservice.edu.tw/curriculumexchange/2011/10/curriculum3040']">
                  國民中小學九年一貫課程綱要
                </xsl:if>
                 <xsl:if test="*[position()=last()][namespace-uri()='http://inservice.edu.tw/curriculumexchange/2011/10/curriculum30']">
                  國民中學課程標準
                </xsl:if>
                 <xsl:if test="*[position()=last()][namespace-uri()='http://inservice.edu.tw/curriculumexchange/2011/10/curriculum40']">
                  國民小學課程標準
                </xsl:if>
              </td>
              <td><xsl:value-of select="*[position()=last()]"/></td>
		        </tr>
		      </xsl:for-each>
		      </tbody>
		    </table>
		  <!--
			<hr/>
			<h2>教師課表</h2>
			<xsl:for-each select="//exchangecity[1]//exchangeschool[1]//curriculumdata[1]//teacher">
				<xsl:variable name="idnumber" select="@idnumber"/> 
				<h3>教師：<xsl:value-of select="teacheruname"/>課表</h3>
				<table border="1">
					<tr><td>&#160;</td><th>週一</th><th>週二</th><th>週三</th><th>週四</th><th>週五</th></tr>
					<tr>
					<th>第一節</th>
					<td>
						<xsl:value-of select="../..//curriculum[@teacheridnumber=$idnumber][week='週一'][classtime='第一節']/*[position()=last()]"/>
					</td>
					<td>
						<xsl:value-of select="../..//curriculum[@teacheridnumber=$idnumber][week='週二'][classtime='第一節']/*[position()=last()]"/>
					</td>
					<td>
						<xsl:value-of select="../..//curriculum[@teacheridnumber=$idnumber][week='週三'][classtime='第一節']/*[position()=last()]"/>
					</td>
					<td>
						<xsl:value-of select="../..//curriculum[@teacheridnumber=$idnumber][week='週四'][classtime='第一節']/*[position()=last()]"/>
					</td>
					<td>
						<xsl:value-of select="../..//curriculum[@teacheridnumber=$idnumber][week='週五'][classtime='第一節']/*[position()=last()]"/>
					</td>
					<td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第二節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第三節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第四節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第五節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第六節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第七節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第八節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第九節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
					<tr><th>第十節</th><td></td><td></td><td></td><td></td><td></td><td></td></tr>
				</table>
			</xsl:for-each>
			-->
		    <hr/>
		    <span>
          XLST版本:<xsl:value-of select="system-property('xsl:version')"/>
        </span>
        <span>
          XLST解析器:<xsl:value-of select="system-property('xsl:vendor')"/>
          <xsl:if test="system-property('xsl:vendor')='Microsoft'">
            （MSXML版本：<xsl:value-of select="system-property('msxsl:version')"/>
            <!--
			<xsl:if test="system-property('msxsl:version')&lt;6">
              <a href="http://www.microsoft.com/downloads/zh-tw/details.aspx?FamilyID=993c0bcf-3bcf-4009-be21-27e85e1857b1">請下載 最新版本(MSXML) 6.0</a>
            </xsl:if>
			-->
            ）
          </xsl:if>
         （網址:<xsl:value-of select="system-property('xsl:vendor-url')"/>）
        </span>
    	</body>
	  </html>
	</xsl:template>
</xsl:stylesheet>