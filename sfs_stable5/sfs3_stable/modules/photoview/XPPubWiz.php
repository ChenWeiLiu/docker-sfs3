<?php
  require "config.php";
$nday = date("Y-m-d") ;  


/*
	XPPubWiz.php
		http://tim.digicol.de/xppubwiz/

	Sample PHP backend for the Microsoft Windows XP Publishing Wizard

	This standalone PHP script provides a complete backend for the Microsoft Windows XP Publishing Wizard,
	a nice tool for file uploads to any HTTP server providing such a backend.

	Requirements:
	- Any web server running PHP 4.1 or greater (with session support)
	- Clients running Microsoft Windows XP

	Getting started:
	- Copy this script anwhere on your web server.
	- Change the strings in the "General configuration" section below (optional).
	- Change the user account and directory information below (recommended).
	- Point your web browser to the URL you copied this script to, and add this querystring:
		?step=reg
	- A file download (xppubwiz.reg) will start.
	- Save the file on your harddisk and double-click it to register your server with the Publishing Wizard.
	- In the Windows Explorer, select some files and click "Publish [...] on the web" in the Windows XP task pane.
	- After confirming your file selection, your server will show up in the list of services. Go ...

	Authors:
		Tim Strehle <tim@digicol.de>
		Andre Basse <andre@digicol.de>

	Version: 1.0b

	CVS Version: $Id: XPPubWiz.php 8198 2014-11-05 01:41:25Z smallduh $

	$Log: XPPubWiz.php,v $
	Revision 1.1.2.3  2006-12-06 00:28:20  prolin
	*** empty log message ***

	Revision 1.1.2.1  2006/01/05 11:53:41  prolin
	*** empty log message ***
	

	Revision 1.8  2003/05/30 09:31:13  tim
	Fixed non-escaped backslashes in JavaScript manifest variable:
	Christian Walczyk found out that file names beginning with "u" or "x" produced
	a JavaScript error (because \u and \x mean something special).

	Revision 1.7  2003/03/14 08:40:06  tim
	Bug fixes for register_globals = off and magic_quotes_gpc = on.
	

	Based on + inspired by the Gallery (http://gallery.menalto.com/) XP Publishing Wizard implementation,
	written by
		Demian Johnston
		Bharat Mediratta

	=====================================================================
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or (at
	your option) any later version.

	This program is distributed in the hope that it will be useful, but
	WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	General Public License for more details.
	=====================================================================

	Technical information can be found here:
		http://msdn.microsoft.com/library/default.asp?url=/library/en-us/shellcc/platform/shell/programmersguide/shell_basics/shell_basics_extending/publishing_wizard/pubwiz_intro.asp
		http://www.zonageek.com/code/misc/wizards/
*/

// General configuration

$protocol = 'http';
if (isset($_SERVER[ 'HTTPS' ]))
  if ($_SERVER[ 'HTTPS' ] == 'on')
	$protocol .= 's';

$cfg = array(
	'wizardheadline'    => $school_sshort_name,
	'wizardbyline'      => '相片館快速上傳區',
	'finalurl'          => $SFS_PATH_HTML .'modules/photoview/',
	'registrykey'       => strtr($_SERVER[ 'HTTP_HOST' ], '.:', '__') .'sfs3_photoview',
	'wizardname'        => $school_sshort_name,
	'wizarddescription' => '相片館快速上傳'
	);





// Determine page/step to display, as this script contains a four-step wizard:
// "login", "options", "check", "upload" (+ special "reg" mode, see below)

$allsteps = array( 'login', 'options', 'check', 'upload', 'reg' );

$step = 'login';

if (isset($_REQUEST[ 'step' ]))
  if (in_array($_REQUEST[ 'step' ], $allsteps))
	$step = $_REQUEST[ 'step' ];


// Special registry file download mode:
// Call this script in your browser and set ?step=reg to download a .reg file for registering
// your server with the Windows XP Publishing Wizard

if ($step == 'reg')
  { header('Content-Type: application/octet-stream; name="xppubwiz.reg"');
	header('Content-disposition: attachment; filename="xppubwiz.reg"');

	echo
		'Windows Registry Editor Version 5.00' . "\n\n" .
		'[HKEY_CURRENT_USER\\Software\\Microsoft\\Windows\\CurrentVersion\\Explorer\\PublishingWizard\\PublishingWizard\\Providers\\' . $cfg[ 'registrykey' ] . ']' . "\n" .
		'"displayname"="' . $cfg[ 'wizardname' ] . '"' . "\n" .
		'"description"="' . $cfg[ 'wizarddescription' ] . '"' . "\n" .
		'"href"="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] . '"' . "\n" .
		'"icon"="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . dirname($_SERVER[ 'PHP_SELF' ]) . '/favicon.ico"';

	exit;
  }


// Send no-cache headers

header('Expires: Mon, 26 Jul 2002 05:00:00 GMT');              // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: no-cache="set-cookie", private');       // HTTP/1.1
header('Pragma: no-cache');                                    // HTTP/1.0


// Start session

session_name('phpxppubwiz');
@session_start();

if (! isset($_SESSION[ 'authuser' ]))
  $_SESSION[ 'authuser' ] = '';


// Send character set header

header('Content-Type: text/html; charset=BIG5');


// Set maximum execution time to unlimited to allow large file uploads

set_time_limit(180);

?>
<html>
<head>
<title>XP Publishing Wizard Server Script</title>


</head>
<body>
<?php
//chkid
function chk_id($log_id = "", $log_pass = ""){
	global $CONN ;
    $log_pass=pass_operate($log_pass) ;
    
	if (!get_magic_quotes_gpc()) {
              $log_id=addslashes($log_id) ;
              $log_pass=addslashes($log_pass) ;
        }

	// 確定連線成立
	if (!$CONN) user_error("資料庫連線不存在！請檢查相關設定！",256);

	$sql_select = " select teacher_sn,name from teacher_base where teach_condition = 0 and teach_id='$log_id' and login_pass='$log_pass' and teach_id<>''";
	$recordSet = $CONN -> Execute($sql_select) or trigger_error("資料連結錯誤：" . $sql_select, E_USER_ERROR);
        
	while(list($teacher_sn, $name) = $recordSet -> FetchRow()){
		$_SESSION['authuser'] = $log_id;
		$_SESSION['session_tea_sn'] = $teacher_sn;
		$_SESSION['session_tea_name'] = $name;
    }
    
    if ($_SESSION['authuser']) 
       return true ;

       
}		


// Variables for the XP wizard buttons

$WIZARD_BUTTONS = 'false,true,false';
$ONBACK_SCRIPT  = '';
$ONNEXT_SCRIPT  = '';


// Authenticate

if (isset($_REQUEST[ 'user' ]) && isset($_REQUEST[ 'password' ])) 
  //做認証
  chk_id($_REQUEST[ 'user' ] ,$_REQUEST[ 'password' ]) ;


// Check page/step

if ($_SESSION[ 'authuser' ] == '')
  $step = 'login';
elseif ($step == 'login')
  $step = 'input_data';

if ($step == 'options') {
  if ( (!isset($_REQUEST['Iact_name']))  and ($_REQUEST['Iact_name']<>'')  )
     $step = 'input_data';
  else { 
         $_SESSION['Iact_date'] = $_REQUEST['Iact_date'] ;
         $_SESSION['Iact_name'] = $_REQUEST['Iact_name'] ;
         $_SESSION['Iact_info'] = $_REQUEST['Iact_info'] ;
         $_SESSION['Iuser'] = $_SESSION['session_tea_name'] ;
         
         
         //建立目錄(以建立日期)
         chdir($savepath) ; 	
         $dirstr = "$nday" ;
         $count = 0 ;
         while (is_dir($dirstr)) {
         	$count ++ ;
         	$dirstr = "$nday-" . $count;
         }	
         mkdir($dirstr , 0700) ;
         
         $_SESSION['dirstr'] = $dirstr;
         
         $updir = $savepath. $dirstr   ;          
         $_SESSION['updir'] = $updir;

  }	    
}    
if ($step == 'check') 
  if (! (isset($_REQUEST[ 'manifest' ]) && isset($_REQUEST[ 'dir' ]) ))
	$step = 'options';




if ($step == 'check')
  if (($_REQUEST[ 'manifest' ] == '') || ($_REQUEST[ 'dir' ] == ''))
	$step = 'options';




if ($step == 'login')
  { ?>

	<form method="post" id="login" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">

	<center>

	<h3>登入</h3>

	<table border="0">
	<tr>
		<td>帳號:</td>
		<td><input type="text" name="user" value="" /></td>
	</tr>
	<tr>
		<td>密碼:</td>
		<td><input type="password" name="password" value="" /></td>
	</tr>
	</table>

	<p>&nbsp;</p>

	</center>

	<input type="hidden" name="step" value="input_data" />

	</form>

	<?php

	$ONNEXT_SCRIPT  = 'login.submit();';
	$ONBACK_SCRIPT  = 'window.external.FinalBack();';
	$WIZARD_BUTTONS = 'true,true,false';
  }


// Step 2: Display options form (directory choosing)

if ($step == "input_data")
  { ?>

	<form method="post" id="input_step" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">

	<center>

	<h3>設定上傳相片資料 </h3>

	<table  border="1" cellspacing="0">
      <tr> 
        <td >相片名稱</td>
        <td ><input type="text" name="Iact_name"  id ="Iact_name"  value=""></td>
      </tr>
      <tr> 
        <td>建制日期</td>
        <td><input type="text" name="Iact_date" value="<?php echo $nday ; ?>"></td>
      </tr>
      <tr> 
        <td>簡介</td>
        <td><textarea name="Iact_info" cols="20" rows="4"></textarea></td>
      </tr>


    </table>
  </center>

	<input type="hidden" name="step" value="options" />
	<input type="hidden" name="manifest" value="" />

	<script>

	function docheck()
	{ 
	  if (document.getElementById('Iact_name').value != '')
	  //if (document.all('Iact_name').value != '')
	     input_step.submit();
	  else 
	     alert('相片名稱一定要輸入！') ;   
	}

	</script>

	</form>

	<?php

	$ONNEXT_SCRIPT  = 'docheck();';
	$ONBACK_SCRIPT  = 'window.external.FinalBack();';
	$WIZARD_BUTTONS = 'false,true,false';

  }

?>

<div id="content"/>

</div>
<?php
  
if ($step == "options")
  { ?>

	<form method="post" id="options" action="<?php echo $_SERVER[ 'PHP_SELF' ]; ?>">

	<center>

	<h3>設定上傳目錄 </h3>

	<select id="dir" name="dir" size="2" width="40">
	    <option value="<?php echo $_SESSION[ 'updir'] ?>" selected="selected">相片目錄區</option>
	</select>

  </center>

	<input type="hidden" name="step" value="check" />
	<input type="hidden" name="manifest" value="" />

	<script>

	function docheck()
	{ var xml = window.external.Property('TransferManifest');
	  options.manifest.value = xml.xml;
	  options.submit();
	}

	</script>

	</form>

	<?php

   $ONNEXT_SCRIPT  = "docheck();";
   $WIZARD_BUTTONS = "false,true,false";
  }

?>

<div id="content"/>

</div>

<?php

// Step 3: Check file list + selected options, prepare file upload

if ($step == "check")
  { /* Now we're embedding the HREFs to POST to into the transfer manifest.

	The original manifest sent by Windows XP looks like this:

	<transfermanifest>
		<filelist>
			<file id="0" source="C:\pic1.jpg" extension=".jpg" contenttype="image/jpeg" destination="pic1.jpg" size="530363">
				<metadata>
					<imageproperty id="cx">1624</imageproperty>
					<imageproperty id="cy">2544</imageproperty>
				</metadata>
			</file>
			<file id="1" source="C:\pic2.jpg" extension=".jpg" contenttype="image/jpeg" destination="pic2.jpg" size="587275">
				<metadata>
					<imageproperty id="cx">1960</imageproperty>
					<imageproperty id="cy">3008</imageproperty>
				</metadata>
			</file>
		</filelist>
	</transfermanifest>

	We will add a <post> child to each <file> section, and an <uploadinfo> child to the root element.
	*/

	// stripslashes if the evil "magic_quotes_gpc" are "on" (hint by Juan Valdez <juanvaldez123@hotmail.com>)

	if (ini_get('magic_quotes_gpc') == '1')
	  $manifest = stripslashes($_REQUEST[ 'manifest' ]);
	else
	  $manifest = $_REQUEST[ 'manifest' ];

	$parser = xml_parser_create();

	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);

	$xml_ok = xml_parse_into_struct($parser, $manifest, $tags, $index);

	$manifest = "<?xml version=\"1.0\" encoding=\"BIG5\" ?>";

	foreach ($tags as $i => $tag)
	  { if (($tag[ 'type' ] == 'open') || ($tag[ 'type' ] == 'complete'))
		  { if ($tag[ 'tag' ] == 'file')
			  $filedata = array(
				'id'                => -1,
				'source'            => '',
				'extension'         => '',
				'contenttype'       => '',
				'destination'       => '',
				'size'              => -1,
				'imageproperty_cx'  => -1,
				'imageproperty_cy'  => -1
				);

			$manifest .= '<' . $tag[ 'tag' ];

			if (isset($tag[ 'attributes' ]))
			  foreach ($tag[ 'attributes' ] as $key => $value)
				{ $manifest .= ' ' . $key . '="' . $value . '"';

				  if ($tag[ 'tag' ] == 'file')
					$filedata[ $key ] = $value;
				}

			if (($tag[ 'type' ] == 'complete') && (! isset($tag[ 'value' ])))
			  $manifest .= '/';

			$manifest .= '>';

			if (isset($tag[ 'value' ]))
			  { $manifest .= htmlspecialchars($tag[ 'value' ]);

				if ($tag[ 'type' ] == 'complete')
				  $manifest .= '</' . $tag[ 'tag' ] . '>';

				if (($tag[ 'tag' ] == 'imageproperty') && isset($tag[ 'attributes' ]))
				  if (isset($tag[ 'attributes' ][ 'id' ]))
					$filedata[ 'imageproperty_' . $tag[ 'attributes' ][ 'id' ] ] = $tag[ 'value' ];
			  }
		  }
		elseif ($tag[ 'type' ] == 'close')
		  { if ($tag[ 'tag' ] == 'file')
			  { $protocol = 'http';
				if (isset($_SERVER[ 'HTTPS' ]))
				  if ($_SERVER[ 'HTTPS' ] == 'on')
					$protocol .= 's';

				$manifest .= 
					'<post href="' . $protocol . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'PHP_SELF' ] . '" name="userfile">' .
					'	<formdata name="MAX_FILE_SIZE">10000000</formdata>' .
					'	<formdata name="step">upload</formdata>' .
					'	<formdata name="todir">' . htmlspecialchars($_REQUEST[ 'dir' ]) . '</formdata>';

				foreach ($filedata as $key => $value)
				  $manifest .= '<formdata name="' . $key . '">' . htmlspecialchars($value) . '</formdata>';

				$manifest .= '</post>';
			  }
			elseif ($tag[ 'level' ] == 1)
			  $manifest .= '<uploadinfo><htmlui href="' . $cfg[ 'finalurl' ] . '"/></uploadinfo>';

			$manifest .= '</' . $tag[ 'tag' ] . '>';
		  }
	  }

	// Check whether we created well-formed XML ...

	if (xml_parse_into_struct($parser,$manifest,$tags,$index) >= 0)
	  { ?>

		<script>

		var newxml = '<?php echo str_replace('\\', '\\\\', $manifest); ?>';
		var manxml = window.external.Property('TransferManifest');

		manxml.loadXML(newxml);

		window.external.Property('TransferManifest') = manxml;
		window.external.SetWizardButtons(true,true,true);

		content.innerHtml = manxml;
		window.external.FinalNext();

		</script>

		<?php
	  }
  }


// Step 4: This page will be called once for every file upload

if ($step == 'upload')
  { if (isset($_FILES) && isset($_REQUEST[ 'todir' ]) && isset($_REQUEST[ 'destination' ])) 
	 if (isset($_FILES[ 'userfile' ]) && ($_REQUEST[ 'todir' ] != '') && ($_REQUEST[ 'destination' ] != '')) {
     
     $session_tmp = 'photo_' . $_REQUEST[ 'todir' ] ;

     $filename = $_REQUEST[ 'todir' ] . '/' . $_REQUEST[ 'destination' ];    
     if (eregi("(.png|.jpg|.jpeg)$", $filename ) ) {
 		 if (! file_exists($filename)) {
		    move_uploaded_file($_FILES[ 'userfile' ][ 'tmp_name' ], $filename);
		    //最大圖寬
		    ImageResized($filename , $filename ,$BIG_PIC_X ,$BIG_PIC_Y) ; 
         }		    
		    
         $ss_pic =    $_REQUEST[ 'todir' ] . '/!!!_' . $_REQUEST[ 'destination' ];     
         //製作小圖
         //ImageCopyResizedTrue($filename , $ss_pic ,160 ,120) ; 
         ImageResized($filename , $ss_pic ,160 ,120) ; 
         //saveFile('/tmp/a.txt' , $filename . '--' . $ss_pic ."--- ( $session_tmp ) --" ) ;
         
         
        if (! isset($_SESSION[ $session_tmp ])) {
           $_SESSION[ $session_tmp ] = '1' ;
           //只寫入資料庫一次   
         
           $sqlstr = "insert into $tbname (act_ID,act_date,act_name,act_info,act_dir,act_postdate,act_auth,act_view)
              values ( '0', '$_SESSION[Iact_date]', '$_SESSION[Iact_name]' ,'$_SESSION[Iact_info]',  '$_SESSION[dirstr]',  '$nday' , '$_SESSION[Iuser]','0') " ;
           if($debug) echo "$sqlstr <br>" ;
           $result = $CONN->Execute( $sqlstr) ;    		         
        }            
      }
      

     }
}
?>

<script>

function OnBack()
{ <?php echo $ONBACK_SCRIPT; ?>
}

function OnNext()
{ <?php echo $ONNEXT_SCRIPT; ?>
}

function OnCancel()
{ // Don't know what this is good for:
  content.innerHtml+='<br>OnCancel';
}

function window.onload()
{ window.external.SetHeaderText("<?php echo strtr($cfg[ 'wizardheadline' ], '"', "'"); ?>","<?php echo strtr($cfg[ 'wizardbyline' ], '"', "'"); ?>");
  window.external.SetWizardButtons(<?php echo $WIZARD_BUTTONS; ?>);
}

</script>

</body>
</html>
