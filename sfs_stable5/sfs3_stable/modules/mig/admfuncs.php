<?php
                                                                                                                             
// $Id: admfuncs.php 5310 2009-01-10 07:57:56Z hami $

/*
 * funcs.php - function library for MiG
 *
 * MiG - A general purpose photo gallery management system.
 *
 * Copyright (C) 2000 Daniel M. Lowe	<dan@tangledhelix.com>
 *
 * LICENSE INFORMATION
 * -------------------
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * ----------------------------------------------------------------------
 *
 * Please see the files in the docs/ subdirectory.
 *
 * Do not modify this file directly.  Please see the file docs/Install.txt
 * for installation directions.  The code is written in such a way that
 * all of your customization needs should be taken care of by the config
 * file "mig.cfgg".
 *
 * If you find that is not the case, and you hack in support for some
 * feature you want to see in MiG, please contact me with a code diff
 * and if I agree that it is useful to the general public, I will
 * incorporate your code into the main code base.
 *
 */
// filelist.txt 記錄檔名			
// 不需要 register_globals
if (!ini_get('register_globals')) {
        ini_set("magic_quotes_runtime", 0);
        extract( $_POST );
        extract( $_GET );
        extract( $_SERVER );
}


global $albumDir;
$uppath= ereg_replace("^.", "", $currDir);



$uppath= ereg_replace("^/", "", $uppath);
$filelist_path = $albumDir."/".$uppath."/filelist.txt";	

	
if (!file_exists($filelist_path)) {	
	$file = fopen($filelist_path, 'w');
	fputs($file,"filelist\n");
}
else
	$file = fopen($filelist_path, 'r');
while ($line = fgets($file, 4096)) {
	$temp = explode ("::",substr($line,0,-1));
	$chktemp[$temp[0]] = $temp[1];
	$chktemp_name[$temp[0]] = $temp[2];
}
fclose($file);



/* printTemplate() - prints HTML page from a template file */

function printTemplate($baseURL, $templateDir, $templateFile, $version,
                       $maintAddr, $folderList, $imageList, $backLink,
                       $albumURLroot, $image, $currDir, $newCurrDir,
                       $pageTitle, $prevLink, $nextLink, $currPos,
                       $description, $youAreHere, $distURL, $albumDir,$adminBlock)
{

  /* Panic if the template file doesn't exist. */
  if (! file_exists("$templateDir/$templateFile")) {
    print "ERROR: $templateDir/$templateFile does not exist!\n";
    exit;
  }

  $file = fopen("$templateDir/$templateFile", "r"); /* Open template file */
  $line = fgets($file, 4096);			/* Get first line */

  while (!feof($file)) {			/* Loop until EOF */

    /* Look for include directives and process them */
    if (ereg("^#include", $line)) {
      $orig_line = $line;
      $line = trim($line);
      $line = str_replace("#include \"", "", $line);
      $line = str_replace("\";", "", $line);
      if (ereg("/", $line)) {
        $line  = "<!-- ERROR: #include directive failed.\n";
        $line .= "     Path included a '/' character, indicating an\n";
        $line .= "     absolute or relative path.  All included files\n";
        $line .= "     must be located in the templates/ directory.\n";
        $line .= "  Directive was:\n";
        $line .= "     $orig_line\n";
        $line .= "-->\n";
        print $line;
      } else {
        $incl_file = $line;
        if (file_exists("$templateDir/$incl_file")) {
          $incfile = fopen("$templateDir/$incl_file", "r");
          $line = fgets($incfile, 4096);
          while (!feof($incfile)) {
            print $line;
            $line = fgets($incfile, 4096);
          }
          fclose($incfile);
        } else {
          $line  = "<!-- ERROR: #include directive failed.\n";
          $line .= "     Named file '$incl_file' does not exist.\n";
          $line .= "  Directive was:\n";
          $line .= "    $orig_line\n";
          $line .= "-->\n";
          print $line;
        }
      }
    } else {

      /* Make sure this is URL encoded */
      $encodedImageURL = migURLencode($image);

      /* Get image pixel size for <IMG> element */
      //$imageProps = GetImageSize("$albumDir/$currDir/$image");
      //$imageSize = $imageProps[3];

      /* Do substitution for various variables */
      $line = str_replace("%%baseURL%%", $baseURL, $line);
      $line = str_replace("%%maintAddr%%", $maintAddr, $line);
      $line = str_replace("%%version%%", $version, $line);
      $line = str_replace("%%folderList%%", $folderList, $line);
      $line = str_replace("%%imageList%%", $imageList, $line);
      $line = str_replace("%%backLink%%", $backLink, $line);
      $line = str_replace("%%currDir%%", $currDir, $line);
      $line = str_replace("%%newCurrDir%%", $newCurrDir, $line);
      $line = str_replace("%%image%%", $image, $line);
      $line = str_replace("%%albumURLroot%%", $albumURLroot, $line);
      $line = str_replace("%%pageTitle%%", $pageTitle, $line);
      $line = str_replace("%%nextLink%%", $nextLink, $line);
      $line = str_replace("%%prevLink%%", $prevLink, $line);
      $line = str_replace("%%currPos%%", $currPos, $line);
      $line = str_replace("%%description%%", $description, $line);
      $line = str_replace("%%youAreHere%%", $youAreHere, $line);
      $line = str_replace("%%distURL%%", $distURL, $line);
      $line = str_replace("%%encodedImageURL%%", $encodedImageURL, $line);
      $line = str_replace("%%imageSize%%", $imageSize, $line);
      $line = str_replace("%%adminBlock%%", $adminBlock, $line);
      print $line;			/* Print resulting line */
    }
    $line = fgets($file, 4096);		/* Grab another line */
  }

  fclose($file);
  return 1;

}	// -- End of printTemplate()


function buildAdminHere($baseadminURL, $currDir, $image, $maxFileSize)
{
  	global $albumDir;
  	$directory = "$albumDir".substr($currDir,1)."/mig.cf";
  	//echo "$directory";
  	if (file_exists("$directory")) {
  		$file = fopen("$directory", 'r');
  		while ($buffer = fgets($file, 4096))   
  			$line .= $buffer;
		
		fclose($file);
	}
  	//$max = $maxFileSize * 1024;
	if ($currDir == '.' )
		$currDir_temp = "主目錄";
	else
		$currDir_temp = $currDir;
		
	$temp = "<form action=\"$baseadminURL\" method=\"post\"  enctype=\"multipart/form-data\" >
   		<table bgcolor=#ccbbdd>
   		<tr><td><font size=\"-1\">
   		執行動作：<select name=sel>
   		<option value=1>建立目錄
   		<option value=2>刪除目錄
   		</select>
   		目錄名稱：<input name=\"cpath\" size=12>
   		<input type=hidden name=currDir value=\"".stripslashes($currDir)."\" >
   		<input type=hidden name=pageType value=\"$pageType\">
   		</font>
   		</td><td><font size=\"-1\">
   		<input type=submit name=key value=\"開始\"></font></td></tr>
   		<tr><td colspan=2><hr size=1></td></tr>
   		<tr><td><font size=\"-1\">
   		上傳圖檔：<input type=\"file\" size=\"15\" maxlength=\"60\" name=\"infile\">&nbsp;&nbsp;圖片說明：<input type=\"text\" size=\"20\" name=\"t1\"></font></td>
   		<td><input type=submit name=key value=\"上傳\"></td></tr>
   		<tr><td colspan=2><font size=\"-1\" color=red>■支援 zip檔&nbsp;&nbsp;■上傳檔案越大，程式執行時間越長</font></td></tr>
   		<hr size=1></td></tr>
   		</table>
   		<table bgcolor=#ccddbb><tr><td>
   		$currDir_temp 說明：<BR><textarea name=\"mig_memo\" rows=3 cols=45>$line</textarea></td></tr>
   		<tr><td><input type=\"submit\" name=\"key\" value=\"建立說明\"></td></tr>
   		</table></form>";
   $hereString = $url.$temp;
   //$hereString = $url."<a href=\"$baseadminURL?currDir=$workingCopy&crrPath=$label\">建立目錄e</a>" ;
   
  if ($image != "") {
    $hereString .= "&nbsp;:&nbsp;<b>$image</b>";
  }

  $x = $hereString;
  $hereString = "<font size=\"-1\">" . $x . "</font>";
  return $hereString;

}	// -- End of buildYouAreHere()


/* buildDirList() - creates list of directories available */

function buildDirList($baseURL, $albumDir, $currDir, $imageDir)
{

  $oldCurrDir = $currDir;	/* Stash this to build full path with */

  $enc_currdir = $currDir;
  $currDir = rawurldecode($enc_currdir);

  /* Read in config info for this directory				*/
  /* buildDirList() only really cares about <Hidden> and <Sort> tags.	*/

  /* array prototypes */
  $hidden = array();
  $presorted = array();

  if (file_exists("$albumDir/$currDir/mig.cfg")) {
    $file = fopen("$albumDir/$currDir/mig.cfg", "r");
    $line = fgets($file, 4096);		/* get first line */
    while (!feof($file)) {
      /* Parse <Hidden> structure */
      if (eregi("^<hidden>", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</hidden>", $line)) {
          $line = trim($line);
          $hidden[$line] = 1;
          $line = fgets($file, 4096);
        }
      }
      /* Parse <Sort> structure */
      if (eregi("^<sort", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</sort>", $line)) {
          $line = trim($line);
          /* If it's a directory not a file, stuff it in the sort list */
          if (is_dir("$albumDir/$currDir/$line")) {
            $presorted[$line] = 1;	/* sorted array */
          }
          $line = fgets($file, 4096);
        }
      }
      $line = fgets($file, 4096);		/* get another line */
    }

    fclose($file);
  }
  
  $dir = opendir("$albumDir/$currDir");		/* Open directory handle */
  $directories = array();	/* prototype */

  while ($file = readdir($dir)) {

    /* Ignore . and .. and make sure it's a directory		*/
    if ($file != "." and $file != ".."
        and is_dir("$albumDir/$currDir/$file"))
    {

      /* Ignore anything that's hidden or was already sorted.	*/
      if (!$hidden[$file] and !$presorted[$file]) {

        /* Stash file in an array */
        $directories[$file] = 1;
      }
    }
  }

  ksort($directories);	/* sort so we can yank them in sorted order	*/
  reset($directories);	/* reset array pointer to beginning		*/

  /* snatch each element from $directories and shove it on the end of	*/
  /* $presorted								*/
  while (list($file,$junk) = each($directories)) {
    $presorted[$file] = 1;
  }

  reset($presorted);		/* reset array pointer */

  while (list($file,$junk) = each($presorted)) {

    /* Surmise the full path to work with */
    $newCurrDir = $oldCurrDir . "/" . $file;

    /* URL-encode the directory name in case it contains spaces		*/
    /* or other weirdness.						*/
    //$enc_file = migURLencode($newCurrDir);
    $enc_file = stripslashes($newCurrDir);
    /* Build the link itself for re-use below */
    $linkURL  = "<a href=\"$baseURL?pageType=folder";
    $linkURL .= "&currDir=$enc_file\">";

    /* Reword $file so it doesn't allow wrapping of the label		*/
    /* (fixes odd formatting bug in MSIE).				*/
    /* Also, render _ as a space.					*/
    $nbspfile = $file;
    $nbspfile = str_replace(" ", "&nbsp;", $nbspfile);
    $nbspfile = str_replace("_", "&nbsp;", $nbspfile);

    /* Build the full link (icon plus folder name) and tack it on	*/
    /* the end of the list.						*/
    $directoryList .= $linkURL;
    $directoryList .= "<img src=\"$imageDir/folder.gif\" ";
    $directoryList .= "border=\"0\"></a>&nbsp;";
    $directoryList .= $linkURL;
    $directoryList .= "<font size=\"-1\">".stripslashes($nbspfile)."</font></a><br>\n";
  }

  closedir($dir); 

  /* If there aren't any subfolders to look at, then just say so. */
  if ($directoryList == "") {
     $directoryList = "NULL";
  }

  return $directoryList;

}	// -- End of buildDirList()



/* buildImageList() - creates a list of images available */

function buildImageList($baseURL, $baseDir, $albumDir, $currDir,
                        $albumURLroot, $maxColumns, $directoryList,
                        $markerType, $markerLabel,$baseadminURL)
{

  /* Read in the "hidden stuff" information */
  $hidden = array(); /* prototype */
  $presorted = array(); /* prototype */
  if (file_exists("$albumDir/$currDir/mig.cfg")) {
    $file = fopen("$albumDir/$currDir/mig.cfg", "r");
    $line = fgets($file, 4096);         /* get first line */
    while (!feof($file)) {
      if (eregi("^<hidden>", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</hidden>", $line)) {
          $line = trim($line);
          $hidden[$line] = 1;
          $line = fgets($file, 4096);
        }
      }
      if (eregi("^<sort", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</sort>", $line)) {
          $line = trim($line);
          /* If it's a file, not a directory, add to the list */
          if (is_file("$albumDir/$currDir/$line")) {
            $presorted[$line] = 1;
          }
          $line = fgets($file, 4096);
        }
      }
      $line = fgets($file, 4096);               /* get another line */
    }
    fclose($file);
  }

  $dir = opendir("$albumDir/$currDir");		/* Open directory handle */

  $row = 0;	/* Counters for the table formatting */
  $col = 0;

  $maxColumns--;	/* Tricks maxColumns into working since it	*/
			/* really starts at 0, not 1.			*/

  /* prototype the array */
  $imagefiles = array();

  while ($file = readdir($dir)) {
    /* Skip over thumbnails */
    if ($markerType == "suffix" and
        eregi("_$markerLabel\.(gif|jpg|png|jpeg|jpe)$", $file)) {
      continue;
    }
    if ($markerType == "prefix" and ereg("^$markerLabel\_", $file)) {
      continue;
    }

    $ext = getFileExtension($file);
    if (is_file("$albumDir/$currDir/$file") and !$hidden[$file]
        and !$presorted[$file] and eregi("^(jpg|gif|png|jpeg|jpe)$", $ext))
    {
      /* Stash file in an array */
      $imagefiles[$file] = 1;
    }
  }

  ksort($imagefiles);	/* sort, so we get a sorted list to stuff onto the
				end of $presorted */
  reset($imagefiles);	/* reset array pointer */

  /* Join the two sorted lists together into a single list */
  while (list($file,$junk) = each($imagefiles)) {
    $presorted[$file] = 1;
  }
  reset($presorted);	/* reset array pointer */

  while (list($file,$junk) = each($presorted)) {

    $ext = getFileExtension($file);
    if (eregi("^(jpg|gif|png|jpeg|jpe)$", $ext)) {

      /* If this is a new row, start a new <TR> */
      if ($col == 0) {
        $imageList .= "<tr>";
      }

      $fname = getFileName($file);
      $img = buildImageURL($baseURL, $baseDir, $albumDir, $currDir,
                           $albumURLroot, $fname, $ext, $markerType,
                           $markerLabel,$baseadminURL);
      $imageList .= $img;

      /* Keep track of what row and column we are on */
      if ($col == $maxColumns) {
        $imageList .= "</tr>";
        $row++;
        $col = 0;
      } else {
        $col++;
      }
    }
  }

  closedir($dir);

  /* If there aren't any images to work with, just say so. */
  if ($imageList == "") {
    $imageList = "NULL";

  } elseif (!eregi("</tr>$", $imageList)) {
    /* Stick a </tr> on the end if it isn't there already. */
    $imageList .= "</tr>";
  }

  return $imageList;

}	// -- End of buildImageList()



/* buildBackLink() - spits out a "back one section" link */

function buildBackLink($baseURL, $currDir, $type, $homeLink, $homeLabel)
{
  
  /* $type notes whether we want a "back" link or "up one level" link.	*/
  if ($type == "back") {
    $label = "回上&nbsp;一&nbsp;層";
  } elseif ($type == "up") {
    $label = "回&nbsp;&nbsp;索引頁&nbsp;";
  }

  /* don't send a link back if we're a the root of the tree */
  if ($currDir == ".") {
    if ($homeLink != "") {
      if ($homeLabel == "") {
        $homeLabel = $homeLink;
      } else {
        /* Get rid of spaces due to silly formatting in MSIE */
        $homeLabel = str_replace(" ", "&nbsp;", $homeLabel);
      }
      /* Build a link to the "home" page */
      $homeLink = stripslashes($homeLink);
      $retval  = "<font size=\"-1\">[&nbsp;<a href=\"";       
      $retval .= "$homeLink\">back&nbsp;to&nbsp;$homeLabel</a>";
      $retval .= "&nbsp;]</font>";      
      $retval .= "<br><br>";
    } else {
      $retval = "<br>";
    }
    return $retval;
  }

  /* Trim off the last directory, so we go "back" one. */
  $junk = ereg_replace("/[^/]+$", "", $currDir);
  $newCurrDir = $junk;
  $newCurrDir = stripslashes($newCurrDir);
  $retval  = "<font size=\"-1\">[&nbsp;";
  $retval .= "<a href=\"$baseURL?currDir=$newCurrDir\">$label</a>";
  $retval .= "&nbsp;]</font><br><br>";
  return $retval;

}	// -- End of buildBackLink()



/* buildImageURL() -- spit out HTML for a particular image */

function buildImageURL($baseURL, $baseDir, $albumDir, $currDir,
                       $albumURLroot, $fname, $ext, $markerType,
                       $markerLabel,$baseadminURL)
{
  $newCurrDir = getNewCurrDir($currDir);
  $oldCurrDir = $currDir;
  $currDir = migURLencode($currDir);

  $newFname = rawurlencode($fname);

  /* Only show a thumbnail if one exists.  Otherwise use a default	*/
  /* "generic" thumbnail image.						*/
  if ($markerType == "prefix") {
    $thumbFile  = "$albumDir/$oldCurrDir/$markerLabel";
    $thumbFile .= "_$fname.$ext";
  }
  if ($markerType == "suffix") {
    $thumbFile  = "$albumDir/$oldCurrDir/$fname";
    $thumbFile .= "_$markerLabel.$ext";
  }
  if (file_exists($thumbFile)) {
    if ($markerType == "prefix") {
      $thumbImage  = "$albumURLroot/$currDir/$markerLabel";
      $thumbImage .= "_$fname.$ext";
    }
    if ($markerType == "suffix") {
      $thumbImage  = "$albumURLroot/$currDir/$fname";
      $thumbImage .= "_$markerLabel.$ext";
    }
    $thumbImage = migURLencode($thumbImage);
  } else {
    $newRoot = ereg_replace("/[^/]+$", "", $baseURL);
    $thumbImage = "$newRoot/images/no_thumb.gif";
  }

  $alt_desc = getImageDescription($albumDir, $currDir, "$fname.$ext");
  $alt_exif = getExifDescription($albumDir, $currDir, "$fname.$ext");

  /* if both are present, separate with "--" */
  if ($alt_desc and $alt_exif) {
    $alt_desc .= " -- $alt_exif";
  }

  /* Figure out the image's size (in bytes and pixels) for display */
  $imageFile = "$albumDir/$oldCurrDir/$fname.$ext";

  /* Figure out the pixels */
  $imageProps = GetImageSize($imageFile);
  $imageWidth = $imageProps[0];
  $imageHeight = $imageProps[1];

  /* Figure out the bytes */
  $imageSize = filesize($imageFile);
  if ($imageSize > 1048576) {
    $imageSize = sprintf("%01.1f", $imageSize / 1024 / 1024) . "MB";
  } elseif ($imageSize > 1024) {
    $imageSize = sprintf("%01.1f", $imageSize / 1024) . "KB";
  } else {
    $imageSize = $imageSize . " bytes";
  }

  /* Figure out thumbnail geometry */
  $thumbHTML = "";
  if (file_exists($thumbFile)) {
    $thumbProps = GetImageSize($thumbFile);
    $thumbHTML = $thumbProps[3];
  }
  global $chktemp_name; 
  $url  = "<td class=\"image\"><a href=\"$baseURL?currDir=".stripslashes($oldCurrDir);
  $url .= "&pageType=image&image=$newFname";
  $url .= ".$ext\"><img src=\"$thumbImage\" alt=\"$alt_desc\"";
  $url .= " border=\"0\" $thumbHTML >";  
  $url .= "</a><br><font size=\"-1\">";
  $url .= "$fname.$ext<br>($imageWidth" ."x$imageHeight,";
  $url .= " $imageSize)";
  if ($chktemp_name[$newFname.".$ext"])
  	$url .= "<BR>建立者：".$chktemp_name[$newFname.".$ext"];  
  
  if($baseadminURL !="" && check_is_del($newFname.".$ext") ) //管理者
  {  	  
  $url .= " <br><a href=\"$baseadminURL?sel=del&currDir=".stripslashes($oldCurrDir);
  $url .= "&pageType=folder&image=$newFname";  
  $url .= ".$ext\">刪除</a>";
  }  
  $url .= " </font></td>\n";

  return $url;

}	// -- End of buildImageURL()

function check_is_del ($newFname) {	
	global $chktemp,$man_flag;
	$flag = false;	
	if (count($chktemp)>0)
		$key_arr = array_keys($chktemp) ;
	else	
		$key_arr=array("0");
		
	if ($chktemp[$newFname]== $_SESSION[session_log_id] || !in_array ($newFname, $key_arr)|| $man_flag)
		$flag = true;	
	return $flag;
}





/* buildNextPrevLinks() -- Build a link to the "next" and "previous"	*/
/* images.								*/

function buildNextPrevLinks($baseURL, $albumDir, $currDir, $image,
                            $markerType, $markerLabel)
{

  /* Read in the config file */
  $hidden = array(); /* prototype */
  $presorted = array(); /* prototype */
  if (file_exists("$albumDir/$currDir/mig.cfg")) {
    $file = fopen("$albumDir/$currDir/mig.cfg", "r");
    $line = fgets($file, 4096);         /* get first line */
    while (!feof($file)) {
      if (eregi("^<hidden>", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</hidden>", $line)) {
          $line = trim($line);
          $hidden[$line] = 1;
          $line = fgets($file, 4096);
        } 
      } 
      /* Parse <Sort> structure */
      if (eregi("^<sort", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</sort>", $line)) {
          $line = trim($line);
          /* If it's a file not a directory, stuff it in the sort list */
          if (is_file("$albumDir/$currDir/$line")) {
            $presorted[$line] = 1;      /* sorted array */
          }
          $line = fgets($file, 4096);
        }
      }
      $line = fgets($file, 4096);               /* get another line */
    } 
    fclose($file);
  } 

  $newCurrDir = getNewCurrDir($currDir);

  $dir = opendir("$albumDir/$currDir");		/* Open directory handle */

  /* Gather all files into an array */
  $fileList = array();
  while ($file = readdir($dir)) {
    /* Ignore thumbnails */
    if ($markerType == "prefix" and
        ereg("^$markerLabel\_", $file)) {
      continue;
    }
    if ($markerType == "suffix" and
        eregi("_$markerLabel\.(gif|jpg|png|jpeg|jpe)$", $file)) {
      continue;
    }
    /* Only look at valid image formats */
    if (!eregi("\.(gif|jpg|png|jpeg|jpe)$", $file)) {
      continue; 
    } 
    /* Ignore the hidden images */
    if ($hidden[$file]) {
      continue;
    }
    /* Make sure this is a file, not a directory. */
    /* and make sure it isn't presorted */
    if (is_file("$albumDir/$currDir/$file") and ! $presorted[$file]) {
      $fileList[$file] = 1;
    }
  }
  closedir($dir); 
  ksort($fileList);	/* sort, so we see sorted results */
  reset($fileList);	/* reset array pointer */

  /* snatch each element from $filelist and shove it on the end of */
  /* $presorted */
  while (list($file,$junk) = each($fileList)) {
    $presorted[$file] = 1;
  }
  reset($presorted);	/* reset array pointer */

  /* Gather all files into an array */
  $i = 1;
  $fList = array ( "blah" ); 
  /* Yes, position 0 is garbage.  Makes the math easier later. */
  while (list($file, $junk) = each($presorted)) {
    
    /* If "this" is the one we're looking for, mark it as such. */
    if ($file == $image) {
      $ThisImagePos = $i;
    }
    $fList[$i] = $file;	/* Stash filename in the array */
    $i++;              /* increment the counter, of course. */
  } 
  reset($fList);

  $i--;			/* Get rid of the last increment... */

  /* Next is one more than $ThisImagePos.  Test if that has a value	*/
  /* and if it does, consider it "next".				*/
  if ($fList[$ThisImagePos+1]) {
    $next = migURLencode($fList[$ThisImagePos+1]);
  } else {
    $next = "NA";
  }

  /* Previous must always be one less than the current index.  If	*/
  /* that has a value, that is.  Unless the current index is "1" in	*/
  /* which case we know there is no previous.				*/
    
  if ($ThisImagePos == 1) {
    $prev = "NA";
  } elseif ($fList[$ThisImagePos-1]) {
    $prev = migURLencode($fList[$ThisImagePos-1]); 
  } 

  //$currDir = migURLencode($currDir);
  $newCurrDir = getNewCurrDir($currDir);

  if ($prev == "NA") {
    $pLink  = "<font size=\"-1\">[&nbsp;<font color=\"#999999\">";
    $pLink .= "上一張&nbsp;照片</font>&nbsp;]</font>";
  } else {
    $pLink  = "<font size=\"-1\">[&nbsp;";
    $pLink .= "<a href=\"$baseURL?pageType=image&currDir=".stripslashes($currDir);
    $pLink .= "&image=$prev\">";
    $pLink .= "上一張&nbsp;照片</a>&nbsp;]</font>";
  }

  if ($next == "NA") {
    $nLink  = "<font size=\"-1\">[&nbsp;<font color=\"#999999\">";
    $nLink .= "下一張&nbsp;照片</font>&nbsp;]</font>";
  } else {
    $nLink  = "<font size=\"-1\">[&nbsp;";
    $nLink .= "<a href=\"$baseURL?pageType=image&currDir=".stripslashes($currDir);
    $nLink .= "&image=$next\">";
    $nLink .= "下一張&nbsp;照片</a>&nbsp;]</font>";
  }

  /* Current position in the list */
  $currPos = "#" . $ThisImagePos . "&nbsp;of&nbsp;" . $i;

  /* I figure five |'s is a good string seperator :-)  */
  $retval = $nLink ."|||||". $pLink ."|||||". $currPos;
  return $retval;

}	// -- End of buildNextPrevLinks()



/* buildYouAreHere() - build the "You are here" line for the top	*/
/* of each page								*/

function buildYouAreHere($baseURL, $currDir, $image,$isAdmin)
{
  global $pageTitle;
  $workingCopy = $currDir;

  while ($workingCopy != ".") {

    $label = ereg_replace("^.*/", "", $workingCopy);
    $label = str_replace("_", " ", $label);
    $encodedCopy = $workingCopy;
    if ($image == "" and $workingCopy == $currDir) {
      $url = "&nbsp;:&nbsp;<b>$label</b>";
    } else {
      $url  = "&nbsp;:&nbsp;<a href=\"$baseURL?currDir=$encodedCopy\">";
      $url .= "$label</a>";
    }
    $workingCopy = ereg_replace("/[^/]+$", "", $workingCopy);
    $x = $hereString;
    $hereString = $url . $x;
  }
  
  if ($currDir == ".") {
    $url = "<b>$pageTitle"."主目錄</b>";
    $x = $hereString;
  } else {
    $url = "<a href=\"$baseURL?currDir=$workingCopy\">主目錄</a>";
    $x = $hereString;
  }
  
  if(!$isAdmin) //管理者
  	{
  	 $retval  = "<font size=\"-1\">[&nbsp;<a href=\"";
      	 $retval .= "admin_index.php\">管理</a>";
      	 $retval .= "&nbsp;]&nbsp;&nbsp;</font>";
      	 $url = $retval.$url;
  	}
  else
  	{
  	 $retval  = "<font size=\"-1\">[&nbsp;<a href=\"";
      	 $retval .= "logout.php?logout=1\">登出</a>";
      	 $retval .= "&nbsp;]&nbsp;&nbsp;</font>";
      	 $url = $retval.$url;
  	}  	
  	
  $hereString = $url . $x;	
  if ($image != "") {
    $hereString .= "&nbsp;:&nbsp;<b>$image</b>";
  }

  $x =  stripslashes ($hereString);
  $hereString = "<font size=\"-1\">" . $x . "</font>";
  return $hereString;

}	// -- End of buildYouAreHere()



/* getFileExtension() - figure out a file's extension and return it. */

function getFileExtension($file)
{
  $ext = ereg_replace("^.*\.", "", $file);
  return $ext;

}	// -- End of getFileExtension()



/* getFileName() - figure out a file's name sans extension. */

function getFileName($file)
{
  $fname = ereg_replace("\.[^\.]+$", "", $file);
  return $fname;

}	// -- End of getFileName()



/* getImageDescription() - Fetches an image description from the	*/
/* comments file (mig.cfg)						*/

function getImageDescription($albumDir, $currDir, $image)
{

  $description = array(); /* prototype */

  if (file_exists("$albumDir/$currDir/mig.cfg")) {
    $file = fopen("$albumDir/$currDir/mig.cfg", "r");
    $line = fgets($file, 4096);		/* get first line */
    while (!feof($file)) {
      if (eregi("^<comment", $line)) {
        $commfilename = trim($line);
        $commfilename = str_replace("\">", "", $commfilename);
        $commfilename = eregi_replace("^<comment \"", "", $commfilename);
        $line = fgets($file, 4096);
        while (!eregi("^</comment", $line)) {
          $line = trim($line);
          $mycomment .= "$line ";
          $line = fgets($file, 4096);
        }
        $description[$commfilename] = $mycomment;
        $commfilename = "";
        $mycomment = "";
      }
      $line = fgets($file, 4096);	/* get another line */
    }
    fclose($file);
  }

  $imageDesc = "";
  if ($description[$image]) {
    $imageDesc = $description[$image];
  }

  return $imageDesc;

}	// -- End of getImageDescription()



/* getExifDescription() - Fetches a comment if available from the	*/
/* Exif comments file (exif.inf)					*/

function getExifDescription($albumDir, $currDir, $image)
{

  if (file_exists("$albumDir/$currDir/exif.inf")) {

    $file = fopen("$albumDir/$currDir/exif.inf", "r");
    $line = fgets($file, 4096);			/* get first line */
    while (!feof($file)) {
      if (ereg("^BEGIN ", $line)) {
        $fname = ereg_replace("^BEGIN ", "", $line);
        $fname = chop($fname);
      } elseif (ereg("^Comment      :", $line)) {
        list($x, $comment) = explode(": ", $line);
        $comment = chop($comment);
        $desc[$fname] = $comment;
      }
      $line = fgets($file, 4096);
    }

    return $desc[$image];

  } else {
    return "";
  }
}	// -- End of getExifDescription()



/* getNewCurrDir() - replaces the silly old $newCurrDir being all	*/
/* over the place.  Especially in the URI string itself.		*/

function getNewCurrDir($currDir)
{

  $newCurrDir = ereg_replace("^\.\/", "", $currDir);
  $newCurrDir = migURLencode($newCurrDir);
  return $newCurrDir;

}	// -- End of getNewCurrDir()



/* getBulletin() - get Bulletin text					*/

function getBulletin($albumDir, $currDir)
{

  if (file_exists("$albumDir/$currDir/mig.cfg")) {
    $file = fopen("$albumDir/$currDir/mig.cfg", "r");
    $line = fgets($file, 4096);
    while (!feof($file)) {
      if (eregi("^<bulletin>", $line)) {
        $line = fgets($file, 4096);
        while (!eregi("^</bulletin>", $line)) {
          $bulletin .= $line;
          $line = fgets($file, 4096);
        }
      }
      $line = fgets($file, 4096);
    }
    fclose($file);
  }

  return $bulletin;

}	// -- End of getBulletin()



/* migURLencode() - fixes a problem where "/" turns into "%2F" when	*/
/* using rawurlencode().						*/

function migURLencode($string)
{

  $new = $string;
  $new = rawurldecode($new);	/* decode first */
  $new = rawurlencode($new);	/* then encode */

  $new = str_replace("%2F", "/", $new);		/* slash (/) */

  return $new;

}	// -- End of migURLencode()



/* folderFrame() - frames stuff in HTML table code... avoids template	*/
/* problems in places where there are images but no folders, or vice	*/
/* versa.								*/

function folderFrame($input)
{

  $retval  = "<table border=\"0\" cellpadding=\"3\">";
  $retval .= "<tr><td class=\"folder\">";
  $retval .= $input;
  $retval .= "</td></tr></table><br>";

  return $retval;

}	// -- End of folderFrame()



/* descriptionFrame() - Same thing as folderFrame() for descriptions.	*/

function descriptionFrame($input)
{

  $retval  = "<table border=\"0\" cellpadding=\"10\" width=\"60%\">";
  $retval .= "<tr><td class=\"desc\">";
  $retval .= $input;
  $retval .= "</td></tr></table><br>";

  return $retval;

}	// -- End of descriptionFrame()



/* imageFrame() - Same thing as folderFrame() but for image tables.	*/

function imageFrame($input)
{

  $retval  = "<table border=\"0\" cellpadding=\"5\" class=\"image\">";
  $retval .= "<tr><td>";
  $retval .= $input;
  $retval .= "</td></tr></table><br>";

  return $retval;

}	// -- End of imageFrame()


?>

