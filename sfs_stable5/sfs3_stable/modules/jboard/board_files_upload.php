<?php
		$fileCount = count($_FILES);
		if ($fileCount > 0){
			//上傳檔案
			//$file_path = "$USR_DESTINATION/$b_id";
				$tt = time();
			for($i=1 ; $i<=$fileCount; $i++){
				if ($_FILES["resourceFile_$i"]['name']=='')
					continue;
				if (!check_is_php_file($_FILES["resourceFile_$i"]['name'])){
					//if (!is_dir($file_path))	mkdir($file_path,0700);
					//copy($_FILES["resourceFile_$i"]['tmp_name'],$file_path."/".$tt.'_'.$i.'-'.$_FILES["resourceFile_$i"]['name']);
					$org_filename=$_FILES["resourceFile_$i"]['name'];
		      //檢驗副檔名
      		$expand_name=explode(".",$org_filename);
      		$nn=count($expand_name)-1;  //取最後一個當附檔名
      		$ATTR=strtolower($expand_name[$nn]); //轉小寫副檔名
					$new_filename=$tt."_".$i."-".date("Y_m_d");
					//copy($_FILES["resourceFile_$i"]['tmp_name'],$file_path."/".$tt.'_'.$i.'-'.$_FILES["resourceFile_$i"]['name']);
				  //copy($_FILES["resourceFile_$i"]['tmp_name'],$file_path."/".$new_filename);
				  //儲存附檔資訊
       		$sFP=fopen($_FILES["resourceFile_$i"]['tmp_name'],"r");				//載入檔案
       		$sFilesize=filesize($_FILES["resourceFile_$i"]['tmp_name']); 	//檔案大小       		
       		if ($sFilesize>$Max_upload*1024*1024) {
       			//超過限制大小, 不存
       	    continue;
       	  }else{
       		$sFiletype=$_FILES["resourceFile_$i"]['type'];  							//檔案屬性
       		/* 2014.09.30 取消
       		//轉碼 , 把檔案內容存入
       		$sFile=addslashes(fread($sFP,filesize($_FILES["resourceFile_$i"]['tmp_name'])));
       		$sFile=base64_encode($sFile);
				  */
				  /* 將檔名複製到檔案區, 並更成新檔名  2014.09.30 */
				  copy($_FILES["resourceFile_$i"]['tmp_name'],$Download_Path.$new_filename); 				  
				  $query="insert into jboard_files (b_id,org_filename,new_filename,filesize,filetype) values ('$b_id','$org_filename','$new_filename','$sFilesize','$sFiletype')";
				  $CONN->Execute($query) or die ($query);

				  
				  }
				}
			}
		}

?>