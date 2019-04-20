{{* $Id: board_ticker.tpl 5310 2009-01-10 07:57:56Z hami $ *}}
<HTML><HEAD><TITLE>{{$school_short_name}}速報</TITLE>
<META http-equiv=Content-Type content="text/html; charset=BIG5">
<STYLE type=text/css>
A {
		FONT-WEIGHT: {{$fw}};
		FONT-SIZE: {{$fs}}pt;
		COLOR: {{$fc}};
		LINE-HEIGHT: {{$lh}}pt;
		TEXT-DECORATION: {{$td}};
		font-variant: normal
	}
A:hover {
		COLOR: {{$hc}}
	}
</STYLE>

<SCRIPT language=JAVASCRIPT>
<!--
// --- Global variable stuff here
       var theItemCount;
       var theCurrentStory;
       var theCurrentLength;
       var theStorySummary;
       var theTargetLink;
       var theCharacterTimeout;
       var theStoryTimeout;
       var theBrowserVersion;
       var theBrowserName;
       var theWidgetOne;
       var theWidgetTwo;
       var theSpaceFiller;
       var theLeadString;
       var theStoryState;

// --- Check for old browser and force applet
       theBrowserName = navigator.appName;
       theBrowserVersion = parseInt(navigator.appVersion);
       if (theBrowserVersion < 4)
       {
          location.href = "ticker_err.htm";
       }

// --- Only run for V4 browsers (check browser again here - some old browsers won't do this inline)
       function startTicker()
       {
          theBrowserVersion = parseInt(navigator.appVersion);
          if (theBrowserVersion < 4)
          {
             location.href = "ticker_err.htm";
             return;
          }

// ------ Check and fixup incoming data block
          if(!document.getElementById('properties'))
          {
             document.getElementById('incoming').innerHTML = "<DIV ID=\"properties\"><DIV ID=\"itemcount\">1</DIV></DIV><DIV ID=\"stories\"><DIV ID=\"1\"><DIV ID=\"Summary\">{{$school_short_name}}校務佈告欄</DIV><DIV ID=\"SiteLink\">{{$SFS_PATH_HTML}}modules/board/board_view.php</DIV><DIV ID=\"UrlLink\"></DIV></DIV></DIV>";
          }
// ------ Set up initial values
          theCharacterTimeout = 50;
          theStoryTimeout     = 5000;
          theWidgetOne        =  "_";
          theWidgetTwo        =  "-";
          theStoryState       = 1;
          theCurrentStory     = -1;
          theCurrentLength    = 0;
          theLeadString       = " ";
          theSpaceFiller      = " ";
          if (theBrowserName == "Netscape")
          	theItemCount        = document.getElementById('itemcount').textContent;
          else
          	theItemCount        = document.getElementById('itemcount').innerText;

// ------ Begin the ticker       
          runTheTicker();
       }

// --- The basic rotate function
       function runTheTicker()
       {
          if(theStoryState == 1)
          {
             setupNextStory();
          }
          if(theCurrentLength != theStorySummary.length)
          {
             drawStory();
          }
          else
          {
             closeOutStory();
          }
       }

// --- Index to next story
       function setupNextStory()
       {
          theStoryState = 0;
          theCurrentStory++;
          theCurrentStory = theCurrentStory % theItemCount;
          if (theBrowserName == "Netscape") {
	          theStorySummary = document.getElementById('Summary_'+theCurrentStory).textContent;
	          theTargetLink   = document.getElementById('SiteLink_'+theCurrentStory).textContent;
	          if(theTargetLink == "")
	          {
	             theTargetLink = document.getElementById('UrlLink_'+theCurrentStory).textContent;
	          }
        	} else {
	          theStorySummary = document.getElementById('Summary_'+theCurrentStory).innerText;
	          theTargetLink   = document.getElementById('SiteLink_'+theCurrentStory).innerText;
	          if(theTargetLink == "")
	          {
	             theTargetLink = document.getElementById('UrlLink_'+theCurrentStory).innerText;
	          }
	        }
          theCurrentLength = 0;
          document.getElementById('hottext').href = theTargetLink;
       }

// --- Draw a teletype line
       function drawStory()
       {
          var myWidget;
          if((theCurrentLength % 2) == 1)
          {
             myWidget = theWidgetOne;
          }
          else
          {
             myWidget = theWidgetTwo;
          }
          document.getElementById('hottext').innerHTML = theLeadString + theStorySummary.substring(0,theCurrentLength) + myWidget + theSpaceFiller;
          theCurrentLength++;
          setTimeout("runTheTicker()", theCharacterTimeout);
       }

// --- Finalise the item
       function closeOutStory()
       {
          document.getElementById('hottext').innerHTML = theLeadString + theStorySummary + theSpaceFiller;
          theStoryState = 1;
          setTimeout("runTheTicker()", theStoryTimeout);
       }
//-->
</SCRIPT>
</HEAD>
<BODY bgColor={{$bc}} onload=startTicker();>
<TABLE>
	<TR><TD>
		<DIV id=visible><A href="/" id=hottext target="_blank"></A></DIV>
	</TD></TR>
</TABLE>
<DIV id=incoming style="DISPLAY: none">
	<DIV id=stories>
{{foreach from=$data_arr item=dd key=i}}
		<DIV id={{$i}}>
			<DIV id=Summary_{{$i}}>{{$data_arr.$i.b_title}}：{{$data_arr.$i.b_sub}}(公告日期{{$data_arr.$i.b_open_date}})</DIV>
			<DIV id=UrlLink_{{$i}}>{{$SFS_PATH_HTML}}modules/board/board_show.php?b_id={{$data_arr.$i.b_id}}></DIV> 
			<DIV id=SiteLink_{{$i}}></DIV>
			<DIV id=Duration_{{$i}}>10 seconds</DIV>
		</DIV>
{{assign var=total value=$i}}
{{/foreach}}
	</DIV>
	<DIV id=properties>
		<DIV id=itemcount>{{$total+1}}</DIV>
	</DIV>
</DIV>
</BODY>
</HTML>
