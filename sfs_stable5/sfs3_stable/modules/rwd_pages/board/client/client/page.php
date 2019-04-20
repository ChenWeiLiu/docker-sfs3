<?php
include "lib.php";
include "config.php";
require __DIR__ . '/vendor/autoload.php';
use \Curl\Curl;

$ERROR=0;
//pre check everything

if ( (!is_writable("download")) ){
  $ERROR=1;
  $download = __DIR__."/download";
  $msg=sprintf("client 端的%s要可寫入",$download);

}


$myLog = "download/ajax.log";
//file_put_contents($wFile,($_GET));
$obj = json_decode(urldecode(implode('',$_GET)));
//$obj->{'articleId'}
//$obj->{'page'}


if (isset($obj->{'page'})){
	$page = $obj->{'page'} ;
}else{
	$page = 0;
}

if ($ERROR==1){
	$show = Array();
	$show['records'][0]['articleId'] = "0000";
  $show['records'][0]['headline'] =  rawurlencode($msg);
	$sfs3server = new SFS3Server();
	print $sfs3server->json($show);
  exit;
}else{

	$curl = new Curl();
	$curl->setopt(CURLOPT_RETURNTRANSFER, TRUE);
	$curl->setopt(CURLOPT_SSL_VERIFYPEER, FALSE);
	$curl->get($sfs3BoardUrl,array(
			'page' => $page,
	));


	if ($curl->error) {
			//echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage;
			$msg = $curl->errorCode . ': ' . $curl->errorMessage;
			$show = Array();
			$show['records'][0]['articleId'] = "0000";
			$show['records'][0]['headline'] =  rawurlencode($msg);
			$sfs3server = new SFS3Server();
			print $sfs3server->json($show);
			exit;
	}else {


		if (isset($obj->{'articleId'})){ 
			//file_put_contents($myLog,($obj->{'articleId'}));
			//$jsonFile = sprintf("json/board%d.json",$obj->{'page'}); 
			//$jsonObj = json_decode(file_get_contents($jsonFile));
			$jsonObj = json_decode($curl->response);
			//file_put_contents($myLog,$jsonObj);
			
			$downloadFiles = array();

			$i=0;
			
			foreach($jsonObj->records  as $article){
			//print $article->articleId ;
			//file_put_contents($myLog,"cccccc");
				if($article->articleId == $obj->{'articleId'}){

						if (isset($article->attachFile)){
							//file_put_contents($myLog,sizeof($article->attachFile));
								foreach($article->attachFile as $attachment){
									$attachment->linkFile;
									//file_put_contents($myLog,$attachment->linkFile);
									//file_put_contents($myLog,$attachment->displayFile);
									$downloadFiles[$i]['linkFile'] = $attachment->linkFile;
									$downloadFiles[$i]['displayFile'] = $attachment->displayFile;
									$i++;
								}

						}else{
							//file_put_contents($myLog,"no attachment");
						}

				}


			}//end foreach($jsonObj->records  as $article)
			

		//file_put_contents($myLog,sizeof($downloadFiles));
			foreach($downloadFiles as $key=>$file){
				//file_put_contents($myLog,$downloadFiles[$key]['linkFile']);
				
				$dirPath = "download/".$obj->articleId.'/';
				$displayFile = $downloadFiles[$key]['displayFile'] ;	
				$linkFile = $downloadFiles[$key]['linkFile'] ;	
					//file_put_contents($myLog,$dirPath.$linkFile);
				if (!file_exists($dirPath.$linkFile)) {
					//file_put_contents($myLog,"file is empty");
					if(!file_exists($dirPath)){
						mkdir($dirPath,0777);
					}

					$downloadPath = $downloadBaseUrl.$obj->articleId.'/'.$linkFile;
					//$downlaodFileName = "download/".$obj->articleId."/".$displayFile ;
					//file_put_contents($myLog,$downloadPath);
				$fileSaveAs="download/".$obj->articleId.'/'.$linkFile;
				$agent = new Curl();
				$agent->setOpt(CURLOPT_ENCODING , 'gzip');
				$agent->download($downloadPath,$fileSaveAs);
				$agent->close();

			/*
					$wget = '/usr/bin/wget';
					//$downloadPath = "http://163.17.39.135/data/school/board/2912/1452829198_1-2016_01_15.pdf";
					$cmd = "$wget --directory-prefix=$dirPath $downloadPath ";
				exec($cmd);
					//file_put_contents("download/".$obj->articleId."/".$displayFile, fopen("$myLog,$downloadBaseUrl.$linkFile", 'r'));
			*/

					
				}
				
			}//foreach($downloadFiles as $key=>$file)

		}//end if


	  print ($curl->response);
	}

	$curl->close();
}
