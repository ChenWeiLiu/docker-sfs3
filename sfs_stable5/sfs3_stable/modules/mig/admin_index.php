<?php
                                                                                                                             
// $Id: admin_index.php 6810 2012-06-22 08:17:27Z smallduh $

/*
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
 * file "mig.cfg".
 *
 * If you find that is not the case, and you hack in support for some
 * feature you want to see in MiG, please contact me with a code diff
 * and if I agree that it is useful to the general public, I will
 * incorporate your code into the main code base for distribution.
 *
 * If I don't incorporate it I may very well offer it as "contributed"
 * code that others can download if they wish to do so.
 *
 */
/* 設定檔 */
/* Version number - Do not change */

//系統認證
### 載入校務系統設定檔
//if(!$is_load)
//	include "../../include/config.php";
//session_start();
//session_register("session_log_id");
include "mig_config.php";

// 不需要 register_globals
if (!ini_get('register_globals')) {
	ini_set("magic_quotes_runtime", 0);
	extract( $_POST );
	extract( $_GET );
	extract( $_SERVER );
}

if(!checkid(substr($PHP_SELF,1))){
	$go_back=1; //回到自已的認證畫面  
//	include $templateDir."/header.php";
	include "../../rlogin.php";  
//	include $templateDir."/footer.php"; 
	exit;
}

//判別是否為系統管理者
$man_flag = checkid($SCRIPT_FILENAME,1) ;

if(!$is_standalone)
	head($P_TITLE);

/* Read configuration file */
if (file_exists($configFile)) {
	$realConfig = $configFile;
}
else {
	$realConfig = $defaultConfigFile;
}
require($realConfig);

/* Is this a jump URL? */
if ($jump and $jumpMap[$jump] and $SERVER_NAME) {
	header("Location: http://$SERVER_NAME$baseURL?$jumpMap[$jump]");
	exit;
}

/* Well, GIGO... set default to sane if someone screws up their	*/
/* config file.							*/
if ($markerType != "prefix" and $markerType != "suffix") {
	$markerType = "suffix";
}
if (!$markerLabel) {
	$markerLabel = "th";
}

/* Make functions available for use */
$funcsFile   = $baseDir . "/admfuncs.php";
require($funcsFile);

/* Look at $currDir from a security angle.  Don't let folks go outside */
/* the album directory base */
$currDir = $_GET[currDir];
$pageType = $_GET[pageType];

if (ereg("\.\.", $currDir)) {
print "瀏覽受限\n";
exit;
}

if ($err == "size"){
	print "檔案太大!! 請小於 $maxFileSize K \n";
	exit;
};

if ($currDir == "") {
	$currDir = ".";
}


/* if $pageType is null, or "folder") generate a folder view */

if ($pageType == "folder" || $pageType == "") {
	if ($is_standalone)
		$templateFile = "admfolder.html";	/* HTML template to use */
	else
		$templateFile = "admfolder_sfs.html";	/* HTML template to use */

  /* Generate some HTML to pass to the template parser */

  /* list of available folders */
	$folderList = buildDirList($baseURL, $albumDir, $currDir, $imageDir);
  
  /*  Admin block */  
  //echo $currDir."<BR>";
	$adminBlock = buildAdminHere($baseadminURL, $currDir, "", $maxFileSize);
  
  /* list of available images */
  
	$imageList = buildImageList($baseURL, $baseDir, $albumDir, $currDir,
                              $albumURLroot, $maxColumns, $folderList,
                              $markerType, $markerLabel,$baseadminURL);
  /* bulletin text, if any */
	$bulletin = getBulletin($albumDir, $currDir);

  
  
  
  /* Only frame the lists in table code when appropriate.		*/

  /* no folders or images - print the "no contents" line.		*/
if ($folderList == "NULL" and $imageList == "NULL") {
	$folderList = "沒有&nbsp;內容.";
	$folderList = folderFrame($folderList);
	$imageList = "";

  /* images, no folders.  Frame the imagelist in a table.		*/
} elseif ($folderList == "NULL" and $imageList != "NULL") {
	$folderList = "";
	$imageList = imageFrame($imageList);

/* folders but no images.  Frame the folderlist in a table.		*/
} elseif ($imageList == "NULL" and $folderList != "NULL") {
	$imageList = "";
	$folderList = folderFrame($folderList);

  /* We have folders and we have images, so frame both in tables.	*/
} else {
	$folderList = folderFrame($folderList);
	$imageList = imageFrame($imageList);

}

  /* We have a bulletin */
  if ($bulletin != "") {
    $bulletin = descriptionFrame($bulletin);
  }

  /* build the "back" link */
  $backLink = buildBackLink($baseURL, $currDir, "back", $homeLink, $homeLabel);

  /* build the "you are here" line */
  
  $youAreHere = buildYouAreHere($baseURL, $currDir, "",1);

  /* parse the template file and print to stdout */
  printTemplate($baseURL, $templateDir, $templateFile, $version, $maintAddr,
                $folderList, $imageList, $backLink, "", "", "", "",
                $pageTitle, "", "", "", $bulletin, $youAreHere, $distURL,
                $albumDir,$adminBlock);

/* If $pageType is "image", show an image */

} elseif ($pageType == "image") {

  /* Trick the back link into going to the right place by adding	*/
  /* a bogus directory at the end.					*/
  $backLink = buildBackLink($baseURL, "$currDir/blah", "up", "", "");

  $Links = buildNextPrevLinks($baseURL, $albumDir, $currDir, $image,
                              $markerType, $markerLabel);

  /* I figure five pipes is a good seperator. */
  list($nextLink, $prevLink, $currPos) = explode("|||||", $Links);

  /* Get image description */
  $description  = getImageDescription($albumDir, $currDir, $image);
  $exifDescription = getExifDescription($albumDir, $currDir, $image);

  /* If both descriptions are non-NULL, separate them with an HR */
  if ($description and $exifDescription) {
    $description .= "<hr>$exifDescription";
  }

  /* If there's a description at all, frame it in a table. */
  if ($description != "") {
    $description = descriptionFrame($description);
  }

  /* Build the "you are here" line */
  $youAreHere = buildYouAreHere($baseURL, $currDir, $image,1);

  $templateFile = "image.html";
  $newCurrDir = getNewCurrDir($currDir);



  printTemplate($baseURL, $templateDir, $templateFile, $version, $maintAddr,
                "", "", $backLink, $albumURLroot, $image, $currDir,
                $newCurrDir, $pageTitle, $prevLink, $nextLink, $currPos,
                $description, $youAreHere, $distURL, $albumDir,$adminBlock);
  
}

if (!$is_stand_alone)
	foot();
?>

