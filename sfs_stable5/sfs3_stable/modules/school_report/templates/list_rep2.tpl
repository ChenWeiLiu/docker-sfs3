{{* $Id: list_rep.tpl,v 1.1.2.2 2007/01/13 01:06:55 hami Exp $ *}}
{{assign var=weeks value=$this->get_week($smarty.request.year_seme)}}
{{if $smarty.request.week_num}}
{{assign var=week_num value=$smarty.request.week_num}}
{{else}}
{{assign var=week_num value=$this->getCurrweek()}}
{{/if}}
{{assign var=year_seme_arr value=$this->get_year_seme()}}
{{if $smarty.request.year_seme}}
{{assign var=year_seme value=$smarty.request.year_seme}}
{{else}}
{{assign var=year_seme value=$year_seme_arr|@array_keys|@end}}
{{/if}}

{{assign var=title_arr value=$this->get_title()}}
<HTML>
<HEAD>
	<META HTTP-EQUIV="CONTENT-TYPE" CONTENT="text/html; charset=Big5">
	<script language="javascript">
    /* fix by licf can scroll on IE and Firefox 2009/09/24*/
	var myoLayer;
    //偵測捲動捲軸
    function runscroll(){
        //init_pos = document.getElementById('oLayer').style.posTop;
        myoLayer = document.getElementById('oLayer');
        var browser_ver = CheckBrowser();
        if(browser_ver=="IE"){
            document.body.onscroll=function(){
                myoLayer.style.posTop=document.body.scrollTop;   
            }				
        }else if(browser_ver=="Firefox"){
            window.addEventListener("scroll",checkScroll,false);
        }
    }
    function checkScroll(){
            myoLayer.style.top = document.body.scrollTop;
    }
    //判斷瀏覽器
    function CheckBrowser(){ 
        var cb = "Unknown"; 
        if(window.ActiveXObject){ 
            cb = "IE"; 
        }else if(navigator.userAgent.toLowerCase().indexOf("firefox") != -1){ 
            cb = "Firefox"; 
        }else if((typeof document.implementation != "undefined") && (typeof document.implementation.createDocument != "undefined") && (typeof HTMLDocument != "undefined")){ 
            cb = "Mozilla"; 
        }else if(navigator.userAgent.toLowerCase().indexOf("opera") != -1){ 
            cb = "Opera"; 
        } 
        return cb; 
    }
    </script>
</HEAD>
<BODY LANG="zh-TW" DIR="LTR" onLoad="runscroll();">

<div id="oLayer" style="position:absolute; right:10px; top:0px; z-index:2; background:green; height:20px; background-color: #FFFF33;">
||
{{foreach from=$this->get_all($year_seme,$smarty.request.open_date,1) item=arr}}
<a href="#{{$arr.id}}" style=" color:#990000">{{$title_arr[$arr.teacher_sn].title_name}}</a>||
{{/foreach}}
</div>

<p align="center" style="font-family:'標楷體'; font-weight:bold; color:#330066; font-size:40px">{{$SCHOOL_BASE.sch_cname_ss}}{{$smarty.request.open_date|@addslashes}}晨會報告事項列表</p>

<ul>
{{foreach from=$this->get_all($year_seme,$smarty.request.open_date,1) item=arr}}
	<li><p style="font-size:28px;font-family:'標楷體'; font-weight:bold">
	 <a name="{{$arr.id}}" id="{{$arr.id}}">{{$title_arr[$arr.teacher_sn].title_name}}</a> 
	  {{$arr.name}} 
	  -- 
	  {{$arr.title}}
	   </P>
	<div style="font-size:55px; padding-left:50px;font-family:'標楷體';text-align: justify; color:#990000;font-weight: bold;letter-spacing:0.2em;line-height:55pt;">{{$arr.content}}</div>
	<hr size="10" style="filter:progid:DXImageTransform.Microsoft.Gradient(gradientType='1',startColorstr='#FFFF00',endColorstr='#008800')"> 
	</li>
  {{/foreach}}
</ul>
</BODY>
</HTML>
