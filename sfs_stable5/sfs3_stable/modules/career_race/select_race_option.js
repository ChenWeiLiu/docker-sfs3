
//選擇類別
function SelectR_name(reset_option) {
	  if (reset_option) {
     removeAllOptions(document.myform.r_name);
     addOption(document.myform.r_name, "", "請選擇競賽項目", "");
    }
    //體育類
    //全縣
    if (document.myform.level.value == '4' && document.myform.nature.value=='體育類') {
        addOption(document.myform.r_name, "縣（市）級運動會", "縣（市）級運動會", "");
        addOption(document.myform.r_name, "縣（市）級中等學校聯合運動會", "縣（市）級中等學校聯合運動會", "");
        addOption(document.myform.r_name, "全國球類聯賽初賽", "全國球類聯賽初賽", "");
        addOption(document.myform.r_name, "國民中小學班際大隊接力錦標賽", "國民中小學班際大隊接力錦標賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    }
    //全國
    if (document.myform.level.value == '2' && document.myform.nature.value=='體育類') {
        addOption(document.myform.r_name, "全國中等學校運動會", "全國中等學校運動會", "");
        addOption(document.myform.r_name, "全國運動會", "全國運動會", "");
        addOption(document.myform.r_name, "全國球類聯賽", "全國球類聯賽", "");
        addOption(document.myform.r_name, "全民運動會", "全民運動會", "");
        addOption(document.myform.r_name, "全國各單項錦標賽", "全國各單項錦標賽", "");
		addOption(document.myform.r_name, "全國身心障礙國民運動會", "全國身心障礙國民運動會賽", "");
		addOption(document.myform.r_name, "全國原住民族運動會", "全國原住民族運動會", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    }

		//國際
    if (document.myform.level.value == '1' && document.myform.nature.value=='體育類')
    {
        addOption(document.myform.r_name, "奧林匹克運動會", "奧林匹克運動會", "");
        addOption(document.myform.r_name, "世界運動會", "世界運動會", "");
        addOption(document.myform.r_name, "亞洲運動會", "亞洲運動會", "");
        addOption(document.myform.r_name, "世界中學生運動會", "世界中學生運動會", "");
        addOption(document.myform.r_name, "世界盃各單項錦標賽", "世界盃各單項錦標賽", "");
        addOption(document.myform.r_name, "亞洲盃各單項錦標賽", "亞洲盃各單項錦標賽", "");	
        addOption(document.myform.r_name, "東亞運", "東亞運", "");	
        addOption(document.myform.r_name, "世界盃或亞洲盃（含各單項）", "世界盃或亞洲盃（含各單項）", "");	
        addOption(document.myform.r_name, "青年奧林匹克運動會", "青年奧林匹克運動會", "");
        addOption(document.myform.r_name, "東亞運動會", "東亞運動會", "");
        addOption(document.myform.r_name, "亞洲青年運動會", "亞洲青年運動會", "");
        addOption(document.myform.r_name, "亞洲沙灘運動會", "亞洲沙灘運動會", "");		
		addOption(document.myform.r_name, "亞洲室內及武藝運動會", "亞洲室內及武藝運動會", "");
		addOption(document.myform.r_name, "亞洲帕拉運動會", "亞洲帕拉運動會", "");
		addOption(document.myform.r_name, "帕拉林匹克運動會", "帕拉林匹克運動會", "");
		addOption(document.myform.r_name, "聽障達福林匹克運動會", "聽障達福林匹克運動會", "");
		addOption(document.myform.r_name, "亞太聽障運動會", "亞太聽障運動會", "");
		addOption(document.myform.r_name, "世界運動會", "世界運動會", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    }
    
    //科學類
    //全縣
    if (document.myform.level.value == '4' && document.myform.nature.value=='科學類') {
        addOption(document.myform.r_name, "縣（市）級中小學科學展覽會", "縣（市）級中小學科學展覽會", "");
        addOption(document.myform.r_name, "ContestWorld GreenMech Contest 世界機關王競賽縣(市)初賽", "ContestWorld GreenMech Contest 世界機關王競賽縣(市)初賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    }
    //全國
    if (document.myform.level.value == '2' && document.myform.nature.value=='科學類') {
        addOption(document.myform.r_name, "全國中小學科學展覽會", "全國中小學科學展覽會", "");
        addOption(document.myform.r_name, "全國工業機器人大賽", "全國工業機器人大賽", "");
        addOption(document.myform.r_name, "全國國鼎盃機器人大賽", "全國國鼎盃機器人大賽", "");
        addOption(document.myform.r_name, "ContestWorld GreenMech Contest 世界機關王競賽全國賽", "ContestWorld GreenMech Contest 世界機關王競賽全國賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    }

		//國際
    if (document.myform.level.value == '1' && document.myform.nature.value=='科學類')
    {
        addOption(document.myform.r_name, "國際科展", "國際科展", "");
        addOption(document.myform.r_name, "加拿大科學展覽會", "加拿大科學展覽會", "");
        addOption(document.myform.r_name, "荷蘭國際科學展覽會", "荷蘭國際科學展覽會", "");
        addOption(document.myform.r_name, "香港聯校科學展覽會", "香港聯校科學展覽會", "");
        addOption(document.myform.r_name, "美國國際科技展覽會", "美國國際科技展覽會", "");
        addOption(document.myform.r_name, "新加坡科技展覽會", "新加坡科技展覽會", "");
        addOption(document.myform.r_name, "奧林匹克競賽", "奧林匹克競賽", "");
        addOption(document.myform.r_name, "世界盃青少年機械人競賽", "世界盃青少年機械人競賽", "");
        addOption(document.myform.r_name, "國際奧林匹克機器人大賽", "國際奧林匹克機器人大賽", "");
        addOption(document.myform.r_name, "ContestWorld GreenMech Contest 世界機關王競賽", "ContestWorld GreenMech Contest 世界機關王競賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    }


    //語文類
    if (document.myform.nature.value=='語文類') {
    	//全縣
    	if (document.myform.level.value == '4') {
        addOption(document.myform.r_name, "縣（市）語文競賽", "縣（市）語文競賽", "");
        addOption(document.myform.r_name, "縣（市）讀者劇場競賽", "縣（市）讀者劇場競賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
    	//全國
    	if (document.myform.level.value == '2') {
        addOption(document.myform.r_name, "全國語文競賽", "全國語文競賽", "");
        addOption(document.myform.r_name, "教育部文藝創作獎", "教育部文藝創作獎", "");
		addOption(document.myform.r_name, "原住民族語單詞競賽", "原住民族語單詞競賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
	  } // end if 語文類

    //音樂類
    if (document.myform.nature.value=='音樂類') {
    	//全縣
    	if (document.myform.level.value == '4') {
        addOption(document.myform.r_name, "全國學生音樂比賽縣(市)初賽", "全國學生音樂比賽縣(市)初賽", "");
        addOption(document.myform.r_name, "全國師生鄉土歌謠比賽縣(市)初賽", "全國師生鄉土歌謠比賽縣(市)初賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
    	//全國
    	if (document.myform.level.value == '2') {
        addOption(document.myform.r_name, "全國音樂比賽", "全國音樂比賽", "");
        addOption(document.myform.r_name, "全國師生鄉土歌謠比賽", "全國師生鄉土歌謠比賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}

			//國際
    	if (document.myform.level.value == '1')	{
        addOption(document.myform.r_name, "經外館證明，三個國家以上的跨國比賽", "經外館證明，三個國家以上的跨國比賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
	  } // end if 音樂類
    
    //美術類
    if (document.myform.nature.value=='美術類') {
    	//全縣
    	if (document.myform.level.value == '4') {
        addOption(document.myform.r_name, "全國學生美術比賽縣(市)初賽", "全國學生美術比賽縣(市)初賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
    	//全國
    	if (document.myform.level.value == '2') {
        addOption(document.myform.r_name, "全國學生美術比賽", "全國學生美術比賽", "");
		addOption(document.myform.r_name, "全國原住民兒童繪畫創作比賽", "全國原住民兒童繪畫創作比賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}

			//國際
    	if (document.myform.level.value == '1')	{
        addOption(document.myform.r_name, "經外館證明，三個國家以上的跨國比賽", "經外館證明，三個國家以上的跨國比賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
	  } // end if 美術類
	  
    //舞蹈類
    if (document.myform.nature.value=='舞蹈類') {
    	//全縣
    	if (document.myform.level.value == '4') {
        addOption(document.myform.r_name, "全國學生舞蹈比賽縣(市)初賽", "全國學生舞蹈比賽縣(市)初賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
    	//全國
    	if (document.myform.level.value == '2') {
        addOption(document.myform.r_name, "全國學生舞蹈比賽", "全國學生舞蹈比賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}

			//國際
    	if (document.myform.level.value == '1')	{
        addOption(document.myform.r_name, "經外館證明，三個國家以上的跨國比賽", "經外館證明，三個國家以上的跨國比賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
	  } // end if 舞蹈類
	  
    //技藝教育類
    if (document.myform.nature.value=='技藝教育類') {
    	//全縣
    	if (document.myform.level.value == '4') {
        addOption(document.myform.r_name, "縣（市）級技藝競賽", "縣（市）級技藝競賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
 	  } // end if 技藝教育類
 	  
    //綜合類
    if (document.myform.nature.value=='綜合類') {
    	//全縣
    	if (document.myform.level.value == '4') {
        addOption(document.myform.r_name, "全國學生創意戲劇比賽縣(市)初賽(原創意偶戲競賽)", "全國學生創意戲劇比賽縣(市)初賽(原創意偶戲競賽)", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
    	//全國
    	if (document.myform.level.value == '2') {
        addOption(document.myform.r_name, "全國學生創意戲劇比賽(原創意偶戲競賽)", "全國學生創意戲劇比賽(原創意偶戲競賽)", "");
        addOption(document.myform.r_name, "全國學生圖畫書創作獎", "全國學生圖畫書創作獎", "");
        addOption(document.myform.r_name, "全國法規資料庫競賽活動", "全國法規資料庫競賽活動", "");
        addOption(document.myform.r_name, "全國環保知識挑戰擂臺賽", "全國環保知識挑戰擂臺賽", "");
        addOption(document.myform.r_name, "臺灣學校網界博覽會", "臺灣學校網界博覽會", "");
		addOption(document.myform.r_name, "全國中小學客家藝文競賽", "全國中小學客家藝文競賽", "");
		addOption(document.myform.r_name, "原住民族語戲劇競賽", "原住民族語戲劇競賽", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}

			//國際
    	if (document.myform.level.value == '1')	{
        addOption(document.myform.r_name, "國際學校網界博覽會", "國際學校網界博覽會", "");
        addOption(document.myform.r_name, "其他", "其他", "");
    	}
	  } // end if 綜合類
	  
	  //其他類
    if (document.myform.level.value == '4' && document.myform.nature.value=='其他類') {
        addOption(document.myform.r_name, "其他", "其他", "");
        document.myform.weight.value='0';
        document.myform.weight_tech.value='0';
		}
    if (document.myform.level.value == '2' && document.myform.nature.value=='其他類') {
        addOption(document.myform.r_name, "其他", "其他", "");
        document.myform.weight.value='0';
        document.myform.weight_tech.value='0';
		}
    if (document.myform.level.value == '1' && document.myform.nature.value=='其他類') {
        addOption(document.myform.r_name, "其他", "其他", "");
        document.myform.weight.value='0';
        document.myform.weight_tech.value='0';
		}

	  
	  

    
}


////////////////// 

function Check_select() {
  if (document.myform.r_name.value=='其他') {
        document.myform.weight.value='0';
        document.myform.weight_tech.value='0';
  } else {
        document.myform.weight.value='1';
        document.myform.weight_tech.value='1';
  }
}

function removeAllOptions(selectbox)
{
    var i;
    for (i = selectbox.options.length - 1; i >= 0; i--)
    {
//selectbox.options.remove(i);
        selectbox.remove(i);
    }
}


function addOption(selectbox, value, text)
{
    var optn = document.createElement("OPTION");
    optn.text = text;
    optn.value = value;
    selectbox.options.add(optn);
}
