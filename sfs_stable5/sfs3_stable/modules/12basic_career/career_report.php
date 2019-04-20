<?php

// $Id:  $

//取得設定檔
include_once "config.php";
include "../../include/sfs_case_score.php";

sfs_check();

//報表輸出
if($_POST['go']=='報表輸出'){
	$new_page="<p STYLE='page-break-after: always;'>";
	foreach($_POST['student_sn'] as $student_sn){
		//page 1
		//抓取學生基本資料
		$query="SELECT stud_name, stud_addr_1 FROM stud_base WHERE student_sn=$student_sn";
		$rs=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
		$stud_name=$rs->fields['stud_name'];
		$stud_addr = $rs->fields['stud_addr_1'];
		//抓取學生學期就讀班級
		$stud_seme_arr=get_student_seme_list($student_sn);
		
		//取得教師、輔導教師既有資料
		$query="select * from career_contact where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		$content_array=unserialize($res->fields['content']);

		
		$contact_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr bgcolor='#c4d9ff' align='center'><td>學期</td><td>班級</td><td>班級座號</td><td>導師姓名</td><td>輔導教師姓名</td></tr>";
		//內容
		foreach($stud_seme_arr as $seme_key=>$value){
			$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
			$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
			$contact_list.="<tr align='center'><td>{$value['year_seme']}</td>
			<td>{$value['seme_class_name']}</td>
			<td>{$value['seme_num']}</td>
			<td>{$content_array[$seme_key][tutor]}</td>
			<td>{$content_array[$seme_key][guidance]}</td>			
			</tr>";
			if($curr_seme_key==$seme_key) $curr_class_name='--'.$value['seme_class_name'].'--';
		}
		$contact_list.="</table>";
		
		//page 2
		//抓取處室聯絡電話
		$room_tel=get_room_tel();
		$room_list="<p align='left'>若有生涯輔導相關問題，可洽詢：<br><br><br>◎學校相關處室聯絡電話：<br><br>　教務處：{$room_tel['教務處']}<br>　學務處：{$room_tel['學務處']}<br>　輔導處：{$room_tel['輔導處']}</p><br>";	
		
		//抓取學生學期就讀班級
		$stud_seme_arr=get_student_seme($student_sn);
		
		//取得導師及輔導教師資料
		$query="select * from career_contact where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		$content_array=unserialize($res->fields['content']);

		$contact_list2="<p align='left'>◎導師及輔導教師：</p><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr bgcolor='#c4d9ff' align='center'><td>年級</td><td>導師姓名</td><td>導師聯絡電話</td><td>輔導教師姓名</td><td>輔導教師聯絡電話</td></tr>";
		//內容
		foreach($stud_seme_arr as $seme_key=>$year_seme){
			$bgcolor=($career_previous or $curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
			$readonly=($career_previous or $curr_seme_key==$seme_key)?'':'readonly';
			$contact_list2.="<tr align='center'><td>$seme_key</td>
				<td>{$content_array[$seme_key][tutor]}</td>
				<td>{$content_array[$seme_key][tutor_tel]}</td>
				<td>{$content_array[$seme_key][guidance]}</td>
				<td>{$content_array[$seme_key][guidance_tel]}</td>	
				</tr>";
		}
		$contact_list2.="</table>";
		
		$words="　　自103學年度起，多數國中畢業生將以免試入學方式升讀高中、職校及五專，學生可參考個人的能力、性向、興趣及人格特質等因素，選擇最適合自己的學校類別。因此，規劃辦理各項試探及實作活動，以協助學生自我認識及探索未來進路，並提供適性的生涯規劃建議，是國中生涯輔導工作的首要目標。<br>
	  　　為落實國中學生生涯輔導機制，培養學生生涯抉擇能力，並協助教師、家長在輔導學生進行生涯規劃時有所依據，教育部著手設計本生涯輔導紀錄手冊，涵括：學生的成長軌跡、各項心理測驗結果、學習成果及特殊表現、生涯輔導紀錄等，並透過生涯發展規劃書，幫助學生在進行進路規劃時有更清晰、明確的步驟和方式。<br>
	 　　為了讓教師、學生及家長能了解如何使用本紀錄手冊，分別說明如后：<br>
	 <br><b><u>給老師的話</u></b><br>
	　　本手冊的規劃，在提供學校導師、輔導教師等學生生涯輔導相關人員一套系統、明確的紀錄與資訊，以協助學生進行進路選擇，聚焦於未來發展的方向。<br>
	　　這本生涯輔導紀錄手冊的功用著重在「資料紀錄」與「輔導」兩方面。在資料蒐集及記錄方面，學校教師可參考目錄頁所列填寫時間，運用相關課程，協助學生完成資料建置。如：個人成長紀錄、心理測驗結果、各項學習成果及特殊表現、生涯發展規劃書及與師長討論生涯規劃的諮詢內容等。由於這本輔導紀錄手冊部分內容，與生涯檔案課程學習單連結，因此，建議導師與輔導教師建立協同及整合機制，協助學生彙整並填寫相關資料。學校可利用家長日、班親會及親職講座等活動，向家長說明手冊的功能及內容（參閱「給家長的話」）。導師或輔導老師將手冊發予學生時，亦可針對「給國中生的話」進行導讀，以增進家長及學生對手冊的認識。<br>
	　　有關生涯輔導紀錄（手冊第21頁），請在與學生進行生涯諮商後完成相關紀錄。要提醒教師的是，本手冊中的輔導紀錄，請聚焦於學生的生涯輔導與規劃，凡是與學生及家長討論相關議題，都可填寫於手冊的紀錄表。因此，可以考慮將這本手冊與學生輔導資料B表一起保管，方便隨時繕寫。最後，在著手填寫生涯發展規劃書時，可以邀請學生及家長根據這三年蒐集的資訊，共同討論完成，協助學生進行適性之進路選擇。<br>
	　　　本紀錄手冊平時由學校相關處室統一保存。發還學生填寫時，請提醒學生妥善保管，以免因遺失造成資料闕漏，影響後續生涯輔導之進行。學生畢業時，本紀錄手冊可發還學生本人參考運用，部分資料若相關處室需留存，請自行影印。此外，若遇學生異動（轉班、轉學等），本紀錄手冊請隨學籍相關資料一併轉移。<br>
	　<br><b><u>給國中生的話</u></b><br>
	　　這本生涯輔導紀錄手冊主要在協助同學記錄個人生涯規劃所需的相關資料，以利在九年級做升學進路選擇時，有較完整、系統的資訊可供參考。<br>
	　　要如何選擇最適合自己的生涯進路？學校各領域的學習、各項社團活動及相關心理測驗等，都可以幫助同學們了解自己的人格特質、專長、興趣等。學校辦理的生涯發展教育課程及活動、生涯檔案資料等，也可以協助您透過體驗、探索來認識自己，以及了解高中、職校及五專的學校特色，請思考您所做的性向、興趣測驗結果為何？是屬於學術導向？或是比較適合就讀職業學校的哪種群科？<br>
	　　每一次填寫手冊時，希望您都能以認真的態度，審慎思考並完成相關資料，這樣才能建置正確、完整的資訊，讓學校老師及家長充分了解您的個人特質、進路需求及個人內外在資源與限制，將各種影響生涯決策的因素納入考量，透過討論、評估，幫助您規劃出一個適合自己的生涯目標。<br>
	　<br><b><u>給家長的話</u></b><br>
	　　從孩子一落地，每位家長就投入了無數的心力，提供大量的教育資源，希望孩子健康成長、快樂學習，並能選擇最適合的發展方向，逐步實現自己的理想。為此，學校協助學生建置這本生涯輔導紀錄手冊，希望彙集個人生涯規劃、進路選擇所需的資料，透過友善的輔導歷程，與家長一起展望孩子的未來，幫助孩子找到人生方向，並希望每個孩子都能擇其所愛、樂其所擇，悠遊於符合自己性向與興趣的天地中，盡情展現長才，發揮光熱，成為各行各業的翹楚。<br>
	　　從許多研究及實例驗證，如果孩子可以清楚自己的興趣及能力，並且設定好未來發展的目標，就較能心無旁騖、不畏艱難、堅定的朝目標前進。因為所做的是個人所愛，所以能完全投入、充滿熱情與活力；又因為有理想、有目標，所以願意堅持到底，其成就何以限量！<br>
	　　在強調適性揚才、多元發展的時代，我們相信每個孩子都有個人揮灑的天地。如何協助孩子發揮優勢能力並規劃生涯方向，是學校與家長共同的課題。建議您參考這本手冊的各項紀錄，找時間跟孩子聊聊、聽聽孩子的想法，必要時可與學校老師一起討論，以協助孩子在生涯洪流中，找到適合的航道，讓他們帶著滿滿的祝福，張帆遠颺，過著幸福、美滿、充實又有意義的人生！<br>
";
		
		//page 3
		$mystory="<p align='left'>一、我的成長故事<br>　（一）自我認識<br>　　　請同學就您認識的自己作勾選，可複選。</p>";
		//抓取個性、各項活動參照表
		$personality_items=SFS_TEXT('個性(人格特質)');
		$activity_items=SFS_TEXT('各項活動');
	
		//取得我的成長故事既有資料
		$query="select personality,interest,specialty from career_mystory where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		
		//抓取自我認識各個項目的內容
		$personality_array=unserialize($res->fields['personality']);
		$interest_array=unserialize($res->fields['interest']);
		$specialty_array=unserialize($res->fields['specialty']);
		
		$personality_checkox="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>個性(人格特質)</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr><tr>";
			
		$activities="";
		foreach($activity_items as $key=>$value){
			$activities.="($key)$value ";
		}
		
		/*
		$interest_checkox="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>休閒興趣</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr><tr>";
		
		$specialty_checkox="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>專長</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr><tr>";
		*/

		
		for($i=$min;$i<=$max;$i++){
			$personality_checkox.="<td>";
			foreach($personality_items as $key=>$value){
				$color=$personality_array[$i][$key]?'#0000ff':'#aaaaaa';
				$checked=$personality_array[$i][$key]?'●':'○';
				$personality_checkox.="$checked<font color='$color'>$value</font><br>";
			}
			$personality_checkox.="</td>";
			
			/*
			$interest_checkox.="<td>";
			foreach($activity_items as $key=>$value){
				$color=$interest_array[$i][$key]?'#ff0000':'#000000';
				$checked=$interest_array[$i][$key]?'●':'○';
				$interest_checkox.="$checked<font color='$color'>$value</font><br>";
			}
			$interest_checkox.="</td>";
			
			$specialty_checkox.="<td>";
			foreach($activity_items as $key=>$value){
				$color=$specialty_array[$i][$key]?'#ff0000':'#000000';
				$checked=$specialty_array[$i][$key]?'●':'○';
				$specialty_checkox.="$checked<font color='$color'>$value</font><br>";
			}
			$specialty_checkox.="</td>";
			*/
			
			$interest_checkox.="<td valign='top'>我喜歡從事的活動：<br>";
			foreach($activity_items as $key=>$value){
				$interest_checkox.=$interest_array[$i][$key]?"$key. $value ":"";
			}
			$interest_checkox.="</td>";
			
			$specialty_checkox.="<td valign='top'>我擅長的事：<br>";
			foreach($activity_items as $key=>$value){
				$specialty_checkox.=$specialty_array[$i][$key]?"$key. $value ":"";
			}
			$specialty_checkox.="</td>";
			
		}
		$personality_checkox.='</tr></table>';
		//$interest_checkox.='</tr></table>';
		//$specialty_checkox.='</tr></table>';	
		$activities="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=3>各項活動</td><td colspan=3>$activities</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td colspan=3>年級</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>
		<tr><td colspan=3>休閒興趣</td>$interest_checkox</tr>
		<tr><td colspan=3>專長</td>$specialty_checkox</tr></table>";
		
		$mystory.="$personality_checkox $new_page $activities";
		
		
		
		//page 4
		$mystory2="<p align='left'>　（二）職業與我<br>　　　透過與師長、家人、親友的溝通與分享，將有助於您對未來職業的了解、更能審慎規劃自己未來的進路。請與您所信任或比較了解您的師長、家人、親友討論後，填寫或勾選下列問題。</p>";
		//職業與我-問題陣列定義
		$suggestion_question=array(1=>'家人、師長或親友曾經建議我未來可選擇的職業',2=>'給我建議的人',3=>'建議我選擇這項職業的原因');
		$myown_question=array(1=>'我最感興趣的職業',2=>'我對這職業感興趣的原因',3=>'這項職業需具備的學歷、能力、專長或其他條件');
		$others_question=array(1=>'我想要進一步了解哪些職業');
		
		//抓取選擇職業時重視的條件參照表
		$weight_items=SFS_TEXT('選擇職業時重視的條件');
		
		//取得我的成長故事既有資料
		$query="select occupation_suggestion,occupation_myown,occupation_others,occupation_weight from career_mystory where student_sn=$student_sn";
		$res=$CONN->Execute($query);
		
		//抓取自我認識各個項目的內容
		$suggestion_array=unserialize($res->fields['occupation_suggestion']);
		$myown_array=unserialize($res->fields['occupation_myown']);
		$others_array=unserialize($res->fields['occupation_others']);
		$weight_array=unserialize($res->fields['occupation_weight']);

		$suggestion_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=4>家人、師長或親友的建議</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>問　　　　題</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>";	
		foreach($suggestion_question as $key=>$value){
			$suggestion_list.="<tr><td>$key. $value</td>";
			for($i=$min;$i<=$max;$i++){
				$mydata=$suggestion_array[$i][$key];
				$suggestion_list.="<td>$mydata</td>";
			}
			$suggestion_list.='</tr>';		
		}
		$suggestion_list.='</table><br>';	
		
		
		$myown_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=4>我最感興趣的職業</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>問　　　　題</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>";	
		foreach($myown_question as $key=>$value){
			$myown_list.="<tr><td>$key. $value</td>";
			for($i=$min;$i<=$max;$i++){
				$mydata=$myown_array[$i][$key];
				$myown_list.="<td>$mydata</td>";
			}
			$myown_list.='</tr>';		
		}
		$myown_list.='</table><br>';
		
		
		$others_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td colspan=4>我想要進一步了解的職業</td></tr>
		<tr bgcolor='#ffcccc' align='center'><td>問　　　　題</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>";	
		foreach($others_question as $key=>$value){
			$others_list.="<tr><td>$key. $value</td>";
			for($i=$min;$i<=$max;$i++){
				$mydata=$others_array[$i][$key];
				$others_list.="<td>$mydata</td>";
			}
			$others_list.='</tr>';		
		}
		$others_list.='</table><br>';
		
		//重視條件　（與上面程式結構不同）
		$weight_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
			<tr bgcolor='#ccccff' align='center'><td colspan=4>選擇職業時，我重視的條件(可複選)</td></tr>
			<tr bgcolor='#ffcccc' align='center'><td width=200>填寫說明</td><td>{$class_year[$min]}級</td><td>{$class_year[$min+1]}級</td><td>{$class_year[$min+2]}級</td></tr>
			<tr>
			<td valign='top' width=300>
			<li>進行生涯規劃時，應澄清與瞭解個人特質，搜集學校與職業資料，同時考量家人意見、社會與環境變遷、各項助力阻力因素等。</li>
			<li>在個人特質的澄清與瞭解方面，除了興趣、能力外，工作價值觀（個人重視的條件）也是重要影響因素。</li>
			<li>（八、九年級填寫）</li>	
			</td>";
		
		for($i=$min;$i<=$max;$i++){
			$weight_list.="<td>";
			foreach($weight_items as $key=>$value){
				$color=$weight_array[$i][$key]?'#0000ff':'#000000';
				$checked=$weight_array[$i][$key]?'●':'○';
				$weight_list.="$checked<font color='$color'>$value</font><br>";
			}
		}
		$weight_list.="</td></tr></table>";	
		
		$mystory2.=$suggestion_list.$myown_list.$others_list.$weight_list;

		$psy_test="<p align='left'>二、各項心理測驗<br>";
		
		$menu_arr=array(1=>'性向測驗',2=>'興趣測驗',3=>'其他測驗(1)',4=>'其他測驗(2)');
		foreach($menu_arr as $id=>$item){
			//取得性向測驗既有資料
			$query="select * from career_test where student_sn=$student_sn and id=$id";
			$res=$CONN->Execute($query);
			if($res){
				while(!$res->EOF){
					$sn=$res->fields['sn'];
					$content=unserialize($res->fields['content']);
					$title=$content['title'];
					$test_result=$content['data'];

					$study=$res->fields['study'];
					$job=$res->fields['job'];
					
					$content_list="<br>　( $id ) $item<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111'>
						<tr bgcolor='#ccffcc' align='center'><td colspan=2><b>$title</b></td></tr><tr></tr>
						<tr bgcolor='#ffcccc' align='center'><td>項目</td><td>內容結果</td></tr>";
					if($test_result){
						foreach($test_result as $key=>$value) $content_list.="<tr><td>$key</td><td align='center'>$value</td></tr>";
					} else $content_list.="<tr align='center'><td colspan=2 height=100>沒有發現任何分項紀錄！</td></tr>";
					$content_list.="</table>◎根據測驗結果，並且參考興趣及學業成績：<br>在升學方面，我適合就讀（學校類別和科別）： $study
						<br>在就業方面，我適合從事（工作類別）： $job<br><br>";

					$res->MoveNext();
				}
			} else $content_list="<center><font size=5 color='#ff0000'><br><br>未發現任何{$menu_arr[$menu]}紀錄！<br><br></font></center>";
		}	
		
		$psy_test.=$content_list;
		
	
		
		//page 5
		$study_spe="<p align='left'>三、學習成果及特殊表現";
		
		//取得領域學習成績資料
		$fin_score=cal_fin_score(array($student_sn),$stud_seme_arr);

		$link_ss=array("chinese"=>"語文-國文","english"=>"語文-英語","math"=>"數學","social"=>"社會","nature"=>"自然與生活科技","art"=>"藝術與人文","health"=>"健康與體育","complex"=>"綜合活動");
		//表格欄位抬頭
		$study_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td>";
		foreach($link_ss as $key=>$value) $study_list.="<td>$value</td>";
		$study_list.="<td>對於我的學習表現，我認為</td></tr>";
		
		//內容
		foreach($stud_seme_arr as $seme_key=>$year_seme){			
			$study_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>";
			foreach($link_ss as $key=>$value) $study_list.="<td>{$fin_score[$student_sn][$key][$year_seme]['score']}</td>";
			$study_list.="<td>{$ponder_array[$seme_key]}</td></tr>";
		}
		$study_list.="</table>";
		
		
		//取得教育會考成績資料
		$exam_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr bgcolor='#c4d9ff' align='center'><td>紀錄時間</td><td>國文</td><td>英語</td><td>數學</td><td>自然</td><td>社會</td><td>寫作測驗</td></tr>";
		$query="select * from career_exam where student_sn=$student_sn order by update_time desc";
		$res=$CONN->Execute($query);
		if($res){
			$exam_list.="<tr align='center'>
				<td>{$res->fields['update_time']}</td>
				<td>{$res->fields['c']}</td>
				<td>{$res->fields['e']}</td>
				<td>{$res->fields['m']}</td>
				<td>{$res->fields['n']}</td>
				<td>{$res->fields['s']}</td>
				<td>{$res->fields['w']}</td>
				</tr>";			
			} else $exam_list.="<tr align='center'><td colspan=7>未發現教育會考成績資料</td></tr>";
		$exam_list.="</table>";
		
		//取得體適能成績資料
		$fitness_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr bgcolor='#c4d9ff' align='center'>
			<td>年級</td><td>學期</td>
			<td>身高<br>(cm)</td>
			<td>體重<br>(kg)</td>
			<td>BMI指數<br>(kg/m<sup>2</sup>)</td>
			<td>測驗年月</td>
			<td>坐姿前彎<br>(cm) [%]</td>
			<td>仰臥起坐<br>(次) [%]</td>
			<td>立定跳遠<br>(cm) [%]</td>
			<td>心肺適能<br>(秒) [%]</td>
			<td>年齡</td>
			<td>獎章</td>
			</tr>";
		$query="select * from fitness_data where student_sn=$student_sn order by c_curr_seme";
		$res=$CONN->Execute($query);
		while(!$res->EOF){
			$c_curr_seme=$res->fields['c_curr_seme'];
			$seme_key=array_search($c_curr_seme,$stud_seme_arr);
			//判定獎章
			$g=0;
			$s=0;
			$c=0;
			$passed=0;
			for($i=1;$i<=4;$i++) {
				$field_name='prec'.$i;
				if($res->fields[$field_name]>=85) $g++;
				if($res->fields[$field_name]>=75) $s++;
				if($res->fields[$field_name]>=50) $c++;
				if($res->fields[$field_name]>=25) $passed++;  //通過門檻標準  程式現設為25%以上
			}				
			$medal='';
			if($g==4) $medal="金"; elseif($s==4) $medal="銀 "; elseif($c==4) $medal="銅";
			$fitness_list.="<tr align='center'>
				<td>$seme_key</td><td>$c_curr_seme</td>
				<td>{$res->fields['tall']}</td>
				<td>{$res->fields['weigh']}</td>
				<td>{$res->fields['bmt']}</td>
				<td>{$res->fields['test_y']}-{$res->fields['test_m']}</td>
				<td>{$res->fields['test1']} [{$res->fields['prec1']}]</td>
				<td>{$res->fields['test2']} [{$res->fields['prec2']}]</td>
				<td>{$res->fields['test3']} [{$res->fields['prec3']}]</td>
				<td>{$res->fields['test4']} [{$res->fields['prec4']}]</td>
				<td>{$res->fields['age']}</td>
				<td>$medal</td>
				</tr>";			
			$res->MoveNext();
		}
		$fitness_list.="</table><font size=1><br>（1）檢測項目：<br>
　　A.肌耐力：一分鐘屈膝仰臥起坐。<br>
　　B.柔軟度：坐姿體前彎。<br>
　　C.瞬發力：立定跳遠。<br>
　　D.心肺耐力：跑走（女生：800公尺、男生：1,600公尺）。<br>
   （2）評等標準：「體適能成績總評結果」與「單一項體適能成績結果」皆分為五種評等，擇優採計七年級上學期至九年級上學期期間之檢測成績，各評等標準說明如下：
   <table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=9px;' bordercolor='#111111' width=100%>
   <tr><td>總評結果評等</td><td>總評結果標準</td><td>單項結果評等</td><td>單項結果標準</td></tr>
<tr><td>金質</td><td>四項成績均達常模百分等級85以上者</td><td>金牌</td><td>單項成績達常模百分等級85以上者</td></tr>
<tr><td>銀質</td><td>四項成績均達常模百分等級75以上者</td><td>銀牌</td><td>單項成績達常模百分等級75以上者</td></tr>
<tr><td>銅質</td><td>四項成績均達常模百分等級50以上者</td><td>銅牌</td><td>單項成績達常模百分等級50以上者</td></tr>
<tr><td>中等</td><td>四項成績均達常模百分等級25以上者</td><td>中等</td><td>單項成績達常模百分等級25以上者</td></tr>
<tr><td>待加強</td><td>四項成績中任一項未達常模百分等級25者</td><td>待加強</td><td>單項成績未達常模百分等級25者</td></tr>
</table>
（3）檢測成績證明分為下列二種：<br>
　　A.教育部補助設置之體適能檢測站成績證明。<br>
　　B.學校自行檢測成績證明。<br>
　　◎體適能檢測相關資訊，請參閱教育部體適能網http://www.fitness.org.tw/
</font>";
	
		$study_spe.="<br><br>1.各領域學習成績 $study_list<br>2.國中教育會考表現 $exam_list<br>3.體適能檢測表現 $fitness_list </p>";


	//page 6
	//表格欄位抬頭
	$assistant_list="<p align='left'>（二）我的經歷（幹部、社團）<br><br>　1.幹部：填寫曾經擔任的全校性、班級幹部或各領域（科）小老師職務，任期須滿一學期以上(含滿一學期)。
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>幹部</td><td>小老師</td><td>自我省思</td>";
	//內容
	$act="<input type='submit' value='儲存紀錄' name='go' onclick='return confirm(\"確定要\"+this.value+\"?\")'>";
	//讀取幹部資料	
	$query="select * from career_self_ponder where student_sn=$student_sn and id='3-2'";
	$res=$CONN->Execute($query);
	$ponder_array=unserialize($res->fields['content']);
	
	foreach($stud_seme_arr as $seme_key=>$year_seme){			
		$assistant_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
		<td align='left'>1. {$ponder_array[$seme_key][1][1]}<br>2. {$ponder_array[$seme_key][1][2]}</td>
		<td align='left'>1. {$ponder_array[$seme_key][2][1]}<br>2. {$ponder_array[$seme_key][2][2]}</td>";
		$assistant_list.="<td align='left'>{$ponder_array[$seme_key][data]}</td></tr>";
	}
	$assistant_list.="</table></p>";

	
	//社團資料
	//表格欄位抬頭
	$club_list="<p align='left'>　2.社團：參加學校於課程內或課後（含假日及寒暑假）實施之社團，滿一學期/20小時。
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>社團名稱</td><td>成績</td><td>擔任職務</td><td>老師評語</td><td>自我省思</td>";

	$query="select * from association where student_sn=$student_sn order by seme_year_seme";
	$res=$CONN->Execute($query);
	if($res){
		while(!$res->EOF){
			$seme_year_seme=$res->fields['seme_year_seme'];
			$seme_key=array_search($seme_year_seme,$stud_seme_arr);
			$club_score=$res->fields['score']?$res->fields['score']:'--';
			$feed_back=str_replace("\r\n",'<br>',$res->fields['stud_feedback']);
			$club_list.="<tr align='center'>
			<td>$seme_key</td><td>$seme_year_seme</td>
			<td>{$res->fields['association_name']}</td>
			<td>{$club_score}</td>
			<td>{$res->fields['stud_post']}</td>
			<td align='left'>{$res->fields['description']}</td>
			<td align='left'>$feed_back</td>
			</tr>";			
			$res->MoveNext();
		}
	} else $club_list.="<tr align='center'><td colspan=6 height=24>未發現社團活動紀錄！</td></tr>";
	$club_list.="</table></p>";
	
	//page 7
	//表格欄位抬頭
	$race_list="<p align='left'>（三）參與各項競賽成果<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'>
		<td>NO.</td><td colspan=2>範圍性質</td><td>競賽名稱</td><td>得獎名次</td><td>證書日期</td><td>主辦單位</td><td>備註</td>";

	//各項競賽成果
	$query="select * from career_race where student_sn=$student_sn order by certificate_date";
	$res=$CONN->Execute($query);
	if($res){
		while(!$res->EOF){
			$ii++;
			$sn=$res->fields['sn'];
			$memo=str_replace("\r\n",'<br>',$res->fields['memo']);
			$race_list.="<tr align='center'>
				<td>$ii</td>
				<td>{$level_array[$res->fields['level']]}</td>
				<td>{$squad_array[$res->fields['squad']]}</td>
				<td align='left'>{$res->fields['name']}</td>
				<td>{$res->fields['rank']}</td>
				<td>{$res->fields['certificate_date']}</td>
				<td align='left'>{$res->fields['sponsor']}</td>
				<td align='left'>$memo</td>
				</tr>";	
			$res->MoveNext();
		}
	} else $race_list.="<tr align='center'><td colspan=7 height=24>未發現各項競賽成果紀錄！</td></tr>";
	$race_list.="</table></p>";
	
	//page 8
	$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
	$reward_list="<p align='left'>（四）行為表現獎懲紀錄<br>　※獎懲明細：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size:9pt;' bordercolor='#111111' width=100%>
				<tr align='center' bgcolor='#ccccff'><td>NO.</td><td>學期別</td><td>獎懲日期</td><td>獎懲類別</td><td>獎懲事由</td><td>獎懲依據</td><td>銷過日期</td></tr>";
			
	//抓取指定學生的獎懲紀錄
	$seme_reward=array();
	$sql="SELECT * FROM reward WHERE student_sn=$student_sn ORDER BY reward_year_seme,reward_date";
	$res=$CONN->Execute($sql) or user_error("讀取失敗！<br>$sql",256);
	if($res)
	while(!$res->EOF)
	{
		$reward_kind=$res->fields['reward_kind'];
		$reward_cancel_date=$res->fields['reward_cancel_date'];
		$reward_year_seme=substr($res->fields['reward_year_seme'],0,-1).'-'.substr($res->fields['reward_year_seme'],-1);
		$recno++;
		$bgcolor=($reward_kind>0)?'#ccffcc':'#ffcccc';
		if($reward_cancel_date=='0000-00-00') $reward_cancel_date=''; else $bgcolor='#cccccc';
		$reward_list.="<tr bgcolor='$bgcolor' align='center'><td>$recno</td><td>$reward_year_seme</td><td>{$res->fields['reward_date']}</td><td>{$reward_arr[$res->fields['reward_kind']]}</td><td align='left'>{$res->fields['reward_reason']}</td><td align='left'>{$res->fields['reward_base']}</td><td>$reward_cancel_date</td></tr>";
		//學期統計
		$reward_year_seme=$res->fields['reward_year_seme'];
		$seme_key=array_search($reward_year_seme,$stud_seme_arr);
		$reward_kind=$res->fields['reward_kind'];			
		
		switch($reward_kind){
			case 1:	$seme_reward_effective[$seme_key][1]++;	$seme_reward_effective['sum'][1]++;	break;
			case 2:	$seme_reward_effective[$seme_key][1]+=2;	$seme_reward_effective['sum'][1]+=2; break;
			case 3:	$seme_reward_effective[$seme_key][3]++;	$seme_reward_effective['sum'][3]++;	break;
			case 4:	$seme_reward_effective[$seme_key][3]+=2;	$seme_reward_effective['sum'][3]+=2; break;
			case 5:	$seme_reward_effective[$seme_key][9]++;	$seme_reward_effective['sum'][9]++;	break;
			case 6:	$seme_reward_effective[$seme_key][9]+=2;	$seme_reward_effective['sum'][9]+=2; break;
			case 7:	$seme_reward_effective[$seme_key][9]+=3;	$seme_reward_effective['sum'][9]+=3; break;
			case -1: $seme_reward_effective[$seme_key][-1]++;	$seme_reward_effective['sum'][-1]++; break;
			case -2: $seme_reward_effective[$seme_key][-1]+=2;	$seme_reward_effective['sum'][-1]+=2; break;
			case -3: $seme_reward_effective[$seme_key][-3]++;	$seme_reward_effective['sum'][-3]++; break;
			case -4: $seme_reward_effective[$seme_key][-3]+=2;	$seme_reward_effective['sum'][-3]+=2; break;
			case -5: $seme_reward_effective[$seme_key][-9]++;	$seme_reward_effective['sum'][-9]++; break;
			case -6: $seme_reward_effective[$seme_key][-9]+=2;	$seme_reward_effective['sum'][-9]+=2; break;
			case -7: $seme_reward_effective[$seme_key][-9]+=3;	$seme_reward_effective['sum'][-9]+=3; break;
		}
		//銷過統計
		if($reward_cancel_date<>'0000-00-00'){
			switch($reward_kind){
				case -1: $seme_reward_canceled[$seme_key][-1]++; $seme_reward_canceled['sum'][-1]++; break;
				case -2: $seme_reward_canceled[$seme_key][-1]+=2; $seme_reward_canceled['sum'][-1]+=2; break;
				case -3: $seme_reward_canceled[$seme_key][-3]++; $seme_reward_canceled['sum'][-3]++; break;
				case -4: $seme_reward_canceled[$seme_key][-3]+=2; $seme_reward_canceled['sum'][-3]+=2; break;
				case -5: $seme_reward_canceled[$seme_key][-9]++; $seme_reward_canceled['sum'][-9]++; break;
				case -6: $seme_reward_canceled[$seme_key][-9]+=2; $seme_reward_canceled['sum'][-9]+=2; break;
				case -7: $seme_reward_canceled[$seme_key][-9]+=3; $seme_reward_canceled['sum'][-9]+=3; break;
			}
		}			
		$res->MoveNext();
	} else $reward_list.="<tr><td colspan=12 align='center'><font size=5 color='#ff0000'>未發現任何獎懲明細！</font></td>";
	$reward_list.="</table>";
	
	//學期統計列表
	//表格欄位抬頭
	$seme_list="<br>　※獎懲統計：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
	<tr align='center' bgcolor='#ffcccc'><td rowspan=2>年級</td><td rowspan=2>學期</td><td colspan=6 bgcolor='#ccccff'>獎懲紀錄</td><td colspan=3 bgcolor='#cccccc'>改過銷過紀錄</td><td rowspan=2>自我省思</td></tr>
	<tr align='center'  bgcolor='#ccccff'><td>大功</td><td>小功</td><td>嘉獎</td><td>警告</td><td>小過</td><td>大過</td><td bgcolor='#cccccc'>警告</td><td bgcolor='#cccccc'>小過</td><td bgcolor='#cccccc'>大過</td></tr>
	";
	//內容
	//讀取自我省思資料	
	$query="select * from career_self_ponder where student_sn=$student_sn and id='3-4'";
	$res=$CONN->Execute($query);
	$ponder_array=unserialize($res->fields['content']);
	
	foreach($stud_seme_arr as $seme_key=>$year_seme){			
		$seme_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>
			<td>{$seme_reward_effective[$seme_key][9]}</td><td>{$seme_reward_effective[$seme_key][3]}</td><td>{$seme_reward_effective[$seme_key][1]}</td><td>{$seme_reward_effective[$seme_key][-1]}</td><td>{$seme_reward_effective[$seme_key][-3]}</td><td>{$seme_reward_effective[$seme_key][-9]}</td>
			<td>{$seme_reward_canceled[$seme_key][-1]}</td><td>{$seme_reward_canceled[$seme_key][-3]}</td><td>{$seme_reward_canceled[$seme_key][-9]}</td>";
		$seme_list.="<td align='left'>{$ponder_array[$seme_key]}</td></tr>";
	}
	//全年統計
	$seme_list.="<tr align='center' bgcolor='#ccccff'><td colspan=2 bgcolor='#ccffcc'>就學期間統計</td>
		<td>{$seme_reward_effective['sum'][9]}</td><td>{$seme_reward_effective['sum'][3]}</td><td>{$seme_reward_effective['sum'][1]}</td><td>{$seme_reward_effective['sum'][-1]}</td><td>{$seme_reward_effective['sum'][-3]}</td><td>{$seme_reward_effective['sum'][-9]}</td>
		<td bgcolor='#cccccc'>{$seme_reward_canceled['sum'][-1]}</td><td bgcolor='#cccccc'>{$seme_reward_canceled['sum'][-3]}</td><td bgcolor='#cccccc'>{$seme_reward_canceled['sum'][-9]}</td>
		<td bgcolor='#ccffcc'>{$ponder_array['sum']}</td></tr>";
	$seme_list.="</table></p>";
	
	
	//page 9  （五）服務學習紀錄
	$room_arr=room_kind();
	//表格欄位抬頭
	$service_list="<p align='left'>（五）服務學習紀錄<br><br>　※明細列表：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
	<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>服務日期</td><td colspan=2>參加校內外公共服務學習事項及活動項目</td><td>分鐘數</td><td>主辦單位</td><td>自我省思</td>";

	$query="select a.*,b.* from stud_service_detail a inner join stud_service b on a.item_sn=b.sn where confirm=1 and student_sn=$student_sn order by year_seme";
	$res=$CONN->Execute($query);
	
	if($res){		
		while(!$res->EOF){
			$year_seme=$res->fields['year_seme'];
			$seme_key=array_search($year_seme,$stud_seme_arr);
			$feed_back=str_replace("\r\n",'<br>',$res->fields['stud_feedback']);
			$service_list.="<tr align='center'>
			<td>$seme_key</td><td>$year_seme</td>
			<td>{$res->fields['service_date']}</td> 
			<td>{$res->fields['item']}</td><td align='left'>{$res->fields['memo']}</td>
			<td>{$res->fields['minutes']}</td>
			<td>{$room_arr[$res->fields['department']]}</td>
			<td align='left'>$feed_back</td>
			</tr>";
			$seme_sum[$seme_key]+=$res->fields['minutes'];
			$res->MoveNext();
		}
	} else $service_list.="<tr align='center'><td colspan=6 height=24>未發現已認證的服務學習紀錄！</td></tr>";
	
	$service_list.="</table>";
	//統計表
	$service_list.="<br><br>　※學期統計：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
	<tr align='center' bgcolor='#ffcccc'><td>年級</td><td>學期</td><td>分鐘數</td><td>服務時數</td></tr>";
	foreach($stud_seme_arr as $seme_key=>$year_seme){
		$minutes=$seme_sum[$seme_key]; $minutes_sum+=$minutes;
		$hours=round($minutes/60,2); $hours_sum+=$hours;			
		$service_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td><td>$minutes</td><td>$hours</td></tr>";
	}
	$service_list.="<tr align='center' bgcolor='#ffcccc'><td colspan=2>就學期間統計</td><td>$minutes_sum</td><td>$hours_sum</td></tr></table></p>";
	
	//page 10  
	//表格欄位抬頭
	$explore_list="<p align='left'>（六）生涯試探活動紀錄
			<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
			<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>年級</td><td>學期</td><td>試探學程及群科</td><td>活動方式</td><td>參與試探活動後圈出對該群科感興趣的程度</td><td>自我省思</td>";
	//抓取個性、各項活動參照表
	$course_array=SFS_TEXT('生涯試探學程及群科');
	$activity_array=SFS_TEXT('生涯試探活動方式');

	//取得生涯試探活動既有資料
	$query="select * from career_explore where student_sn=$student_sn order by seme_key";
	$res=$CONN->Execute($query);
	if($res){
		while(!$res->EOF){
			$ii++;
			$sn=$res->fields['sn'];
				$self_ponder=str_replace("\r\n",'<br>',$res->fields['self_ponder']);
				$explore_list.="<tr align='center'>
					<td>$ii</td>
					<td>{$res->fields['seme_key']}</td>
					<td>{$stud_seme_arr[$res->fields['seme_key']]}</td>
					<td>{$course_array[$res->fields['course_id']]}</td>
					<td>{$activity_array[$res->fields['activity_id']]}</td>
					<td>{$res->fields['degree']}</td>
					<td align='left'>$self_ponder</td>
					</tr>";	
			$res->MoveNext();
		}
	} else $explore_list.="<tr align='center'><td colspan=7 height=24>未發現生涯試探活動紀錄！</td></tr>";
	$explore_list.="</table></p>";
	
	//page 11 
	//抓取生涯方向思考項目參照表
	$ponder_items=SFS_TEXT('生涯方向思考項目');

	//取得既有資料
	$query="select ponder from career_view where student_sn=$student_sn";
	$res=$CONN->Execute($query);
	$ponder_array=unserialize($res->fields['ponder']);
	
	$ponder_list="<p align='left'>四、生涯統整面面觀<br>　◎適合自己的生涯方向<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px; width:100%' bordercolor='#111111'>
	<tr bgcolor='#ccccff' align='center'><td>NO.</td><td>項目</td><td>內容</td></tr>";
		
	$ponder_list.="<td bgcolor='$bgcolor'>";
	foreach($ponder_items as $key=>$value){
		$ii++;
		$ponder_list.="<tr><td align='center'>$ii</td><td>$value</td><td>{$ponder_array[$key]}</td></tr>";
	}
	$ponder_list.='</tr></table></p>';
	
	
	//抓取生涯選擇方向參照表
	$direction_items=SFS_TEXT('生涯選擇方向');
	//取得既有資料
	$query="select direction from career_view where student_sn=$student_sn";
	$res=$CONN->Execute($query);
	$direction_array=unserialize($res->fields['direction']);
	$direction_list="<p align='left'>　（一）與家長及學校教師討論後，自己想要選擇的方向：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr bgcolor='#ccccff' align='center'><td>項次</td><td>自己的想法</td><td>家長的期望</td><td>學校教師的建議</td><td>備註</td></tr>";
	
	$direction_initial=array(1=>'self',2=>'parent',3=>'teacher');
	for($i=1;$i<=3;$i++){
		$direction_list.="<tr><td align='center'>$i</td>";
		foreach($direction_initial as $key=>$value){
			$target_value=$direction_array['item'][$i][$value];
			$target=$direction_items[$target_value];
			$direction_list.="<td>$target</td>";				
		}
		$direction_list.="<td>{$direction_array['memo'][$i]}</td></tr>";
	}
	
	$direction_list.='</table>';
	
	$checked=$direction_array['identical']?'是':'否';
	$direction_list.="<br>　（二）想一想<br>　　1.自己的想法是否和家長期望或老師建議一致？
		<br>　　　$checked ，原因：{$direction_array['reason']}
		<br>　　2.如果我的想法與家長的期望不同，可以如何溝通呢？<br>{$direction_array['communicate']}";
	$direction_list.='</p>';
	
	
	//地理位置與交通
	
	//gmap here
	$geodata = '
<p align="left">
◎瞭解想升讀學校的地理位置、評估學校與住家間的交通概況，是選擇志願學校時的重要考量因素。建議可依下列步驟進行思考：
</p>
<p align="left">
（一）依下列免試就學區行政圖，找出自己住家所在的區域位置並標註記號。
</p>
<p align="left">
（二）找出您想升讀學校的所在區域位置，並分別標註記號。
</p>
<p align="left">			
（三）每一所想升讀學校與住家最便捷的交通方式、需花費的時間與車資。
</p>';
$geodata .= "<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>";			
$geodata .= '<tr><td><div id="test1" class="gmap3"></div></td></tr></table>';
	
	
	$course_list="<p align='left'>　（三）想升讀的學程或科別及學校：<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr align='center' bgcolor='#ffcccc'>
				<td>志願序</td><td>學校</td><td>學程或科別</td><td>地理位置</td><td>交通方式</td><td>往返時間</td><td>往返車資</td><td>備註</td>";
	//志願學校	
	$geodataSchool = array();
	
	//抓取課程志願
	$query="select * from career_course where student_sn=$student_sn order by aspiration_order";
	$res=$CONN->Execute($query);
	if($res){
		while(!$res->EOF){
			$ii=$res->fields['aspiration_order'];
			$sn=$res->fields['sn'];
				$memo=str_replace("\r\n",'<br>',$res->fields['memo']);
				$course_list.="<tr align='center'>
					<td>$ii</td>
					<td>{$res->fields['school']}</td>
					<td>{$res->fields['course']}</td>
					<td>{$res->fields['position']}</td>
					<td>{$res->fields['transportation']}</td>
					<td>{$res->fields['transportation_time']}</td>
					<td>{$res->fields['transportation_toll']}</td>
					<td align='left'>$memo</td>
					</tr>";
			$geodataSchool[] = $res->fields['school'];
			$res->MoveNext();
		}
	} else $course_list.="<tr align='center'><td colspan=7 height=24>未發現想升讀的學程或科別紀錄！</td></tr>";
	$course_list.="</table></p>";
	
	//五、生涯發展規劃書
	//抓取既有資料
	$query="select sn,aspiration_order,school,course,factor from career_course where student_sn=$student_sn order by aspiration_order";
	$res=$CONN->Execute($query);
	$evaluate_count=$res+1;
	while(!$res->EOF){
		$ii=$res->fields['aspiration_order'];
		$evaluate[$ii]['sn']=$res->fields['sn'];
		$evaluate[$ii]['school']=$res->fields['school'];
		$evaluate[$ii]['course']=$res->fields['course'];
		$evaluate[$ii]['factor']=unserialize($res->fields['factor']);
		$res->MoveNext();
	}
	//表格欄位抬頭
	$evaluate_list="<p align='left'>五、生涯發展規劃書<br>　◎生涯評核表：將我想升讀的高中或高職、五專學校及科別，評估各項考慮因素與每個選項的符合程度，並填入「0～5」的分數，5分代表非常符合，0分代表非常不符合。
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'>
		<td bgcolor='#ddffcf'><p align='right'>★志願學校★</p><p align='left'>★考慮因素★</p></td>";
	foreach($evaluate as $order=>$evaluate_data){
		$evaluate_list.="<td>$order<br>{$evaluate_data['school']}<br>{$evaluate_data['course']}</td>";
	}
	$evaluate_list.='</tr>';

	//抓取考慮因素項目
	$factor_items=array('self'=>'個人因素','env'=>'環境因素','info'=>'資訊因素');
	foreach($factor_items as $item=>$title){
		$factor=SFS_TEXT($title);
		$evaluate_list.="<tr bgcolor='#ddffdd'><td colspan=$evaluate_count>● $title</td></tr>";
		foreach($factor as $key=>$data){
			$evaluate_list.="<tr><td>　 -$data</td>";
			foreach($evaluate as $order=>$evaluate_data){
				$evaluate[$order]['sum']+=$evaluate_data['factor'][$item][$key];
				if($order==$_POST['edit_order']){
					$edit_radio='';
					for($i=1;$i<=5;$i++){
						$checked=($evaluate_data[factor][$item][$key]==$i)?'checked':'';
						$color=($evaluate_data[factor][$item][$key]==$i)?'#ff0000':'#000000';
						$edit_radio.="<input type='radio' name='evaluate[$item][$key]' value=$i $checked><font color='$color'>$i</font>";	
					}					
					$evaluate_list.="<td bgcolor='#fcffcf' align='center'>$edit_radio<input type='hidden' name='sn' value='{$evaluate_data['sn']}'</td>";
				} else { 
					$evaluate_list.="<td align='center'>{$evaluate_data[factor][$item][$key]}</td>"; 
				}
			}
			$evaluate_list.='</tr>';
		}			
	}	
	//加入總計列
	$evaluate_list.="<tr></tr><tr bgcolor='#ddffdd' align='center'><td>★　　總　　　計　　★</td>";
	foreach($evaluate as $order=>$value){
		$evaluate_list.="<td><b>{$value['sum']}<b></td>"; 
	}		
	$evaluate_list.="</tr></table></p>";
			
	//※相關心理測驗結果
	$query="select * from career_test where student_sn=$student_sn and id<3";
	$res=$CONN->Execute($query);
	while(!$res->EOF){
		$id=$res->fields['id'];
		$highest_arr=explode(',',$res->fields['highest']);
		foreach($highest_arr as $key=>$value) $career_test[$id][$key]=$value;	
		$res->MoveNext();
	}
	
	$psy_result="<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr align='center'><td rowspan=2 bgcolor='#ffcccc'>相關心理測驗結果</td>
			<td bgcolor='#ffcccc'>性向測驗分數最高的3項分測驗</td><td>{$career_test[1][0]}</td><td>{$career_test[1][1]}</td><td>{$career_test[1][2]}</td></tr>
		<tr align='center'><td bgcolor='#ffcccc'>興趣測驗分數最高的3項分測驗</td><td>{$career_test[2][0]}</td><td>{$career_test[2][1]}</td><td>{$career_test[2][2]}</td></tr>
		</table><br>";	
	
	/*
	$psy_result="<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
		<tr align='center' bgcolor='#ffcccc'><td colspan=3>相關心理測驗結果</td></tr><tr align='center' valign='top'>";
	//取得測驗既有資料
	$item_arr=array(1=>'性向測驗',2=>'興趣測驗',3=>'其他測驗(1)',4=>'其他測驗(2)');
	foreach($item_arr as $key=>$title){
		$query="select * from career_test where student_sn=$student_sn and id=$key";
		$res=$CONN->Execute($query);
		if($res){
			while(!$res->EOF){
				$sn=$res->fields['sn'];
				$content=unserialize($res->fields['content']);

				$title=$content['title'];
				$test_result=$content['data'];
				$study=$res->fields['study'];
				$job=$res->fields['job'];
				
				$content_list="<td><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
						<tr bgcolor='#ccffcc' align='center'><td colspan=2><b>$title</b></td></tr><tr></tr>
						<tr bgcolor='#ffcccc' align='center'><td>項目</td><td>內容結果</td></tr>";
				if($test_result){
					foreach($test_result as $key2=>$value) $content_list.="<tr><td>$key2</td><td align='center'>$value</td></tr>";
				} else $content_list.="<tr align='center'><td colspan=2 height=100>沒有發現任何分項紀錄！</td></tr>";
				
				$content_list.="<tr bgcolor='#fcccfc'><td colspan=2>●根據測驗結果，在升學方面，我適合就讀： $study<br>●根據測驗結果，在就業方面，我適合從事： $job</td></tr></table></td>";
				
				$psy_result.=$content_list;
//echo $content_list;				
				$res->MoveNext();
			}
	} else $content_list="<td><center><font size=2 color='#ff0000'>未發現任何{$item_arr[$key]}紀錄！<br></font></center></td>";	
	}
	$psy_result.="</tr></table><br>";	
	*/
//exit;		
	//取得領域學習成績資料
	$fin_score=cal_fin_score(array($student_sn),$stud_seme_arr);
	$link_ss=array("chinese"=>"語文-國文","english"=>"語文-英語","math"=>"數學","social"=>"社會","nature"=>"自然與生活科技","art"=>"藝術與人文","health"=>"健康與體育","complex"=>"綜合活動");
	//表格欄位抬頭
	$study_list="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
	<tr align='center' bgcolor='#ffcccc'><td rowspan=2>學習表現<br>(五學期平均成績)</td>";
	foreach($link_ss as $key=>$value) $study_list.="<td>$value</td>";
	
	//內容
	/*
	foreach($stud_seme_arr as $seme_key=>$year_seme){			
		$bgcolor=($curr_seme_key==$seme_key)?'#ffdfdf':'#cfefef';
		$readonly=($curr_seme_key==$seme_key)?'':'readonly';
		$study_list.="<tr align='center'><td>$seme_key</td><td>$year_seme</td>";
		foreach($link_ss as $key=>$value) $study_list.="<td>{$fin_score[$student_sn][$key][$year_seme]['score']}</td>";
	}
	*/
	//總成績
	$study_list.="<tr align='center' bgcolor='#ffffcc'>";
	foreach($link_ss as $key=>$value) $study_list.="<td><b>{$fin_score[$student_sn][$key]['avg']['score']}</b></td>";
	$study_list.="</tr></table>";
	
	$way_result.="<br><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr bgcolor='#ffcccc' align='center'><td>生涯目標</td></tr><tr><td>我想升讀的學校-學程：";
	//抓取既有資料
	$query="select aspiration_order,school,course from career_course where student_sn=$student_sn and aspiration_order>0 order by aspiration_order";
	$res=$CONN->Execute($query);
	//$evaluate_count=$res+1;
	while(!$res->EOF){
		$way_result.="({$res->fields['aspiration_order']}){$res->fields['school']}-{$res->fields['course']}　 ";
		$res->MoveNext();
	}
	$way_result.="</font></td></tr></table>";
	
	//生涯選擇方向
	//抓取生涯選擇方向參照表
	$direction_items=SFS_TEXT('生涯選擇方向');
	
	//取得師長綜合意見既有資料
	$query="select * from career_opinion where student_sn=$student_sn";
	$res=$CONN->Execute($query);
	if($res){
		while(!$res->EOF){
			$ii++;
			$sn=$res->fields['sn'];				
			$parent=' ,'.$res->fields['parent'].',';	
			$parent_radio='';
	
			foreach($direction_items as $d_key=>$d_value){
				$comp=','.$d_key.',';
				$checked=strpos($parent,$comp)?'●':'○';
				$color=strpos($parent,$comp)?'#0000ff':'#000000';
				$parent_radio.="<font color='$color'>$checked$d_value </font>";					
			}
			$parent_memo=$res->fields['parent_memo'];
			
			$tutor=' ,'.$res->fields['tutor'].',';
			foreach($direction_items as $d_key=>$d_value){
				$comp=','.$d_key.',';
				$checked=strpos($tutor,$comp)?'●':'○';
				$color=strpos($tutor,$comp)?'#0000ff':'#000000';
				$tutor_radio.="<font color='$color'>$checked$d_value </font>";	
			}
			$tutor_memo=$res->fields['tutor_memo'];
			
			$guidance=' ,'.$res->fields['guidance'].',';
			foreach($direction_items as $d_key=>$d_value){
				$comp=','.$d_key.',';
				$checked=strpos($guidance,$comp)?'●':'○';
				$color=strpos($guidance,$comp)?'#0000ff':'#000000';
				$guidance_radio.="<font color='$color'>$checked$d_value </font>";					
			}
			$guidance_memo=$res->fields['guidance_memo'];
			
			$opinions_list.="<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
						<tr bgcolor='#ffcccc' align='center'><td colspan=2>師長綜合意見</td><td>　簽　章　</td></tr>";
			
			$opinions_list.="<tr><td align='center'>家長意見</td><td>綜合以上相關資料，我希望孩子選擇：$parent_radio<br>說明：$parent_memo</td><td></td></tr>
									<tr><td align='center'>導師意見</td><td>綜合以上相關資料，建議學生選讀：$tutor_radio<br>說明：$tutor_memo</td><td></td></tr>
									<tr><td align='center'>輔導教師（輔導小組）意見</td><td>綜合以上相關資料，建議學生選讀：$guidance_radio<br>說明：$guidance_memo</td><td></td></tr>";
			
			$today=date("Y 年 m 月 d 日");
			$opinions_list.="<tr><td colspan=3>（本欄為申請高中職實用技能學程專用欄）<br><br>
		校名：$school_long_name<br><br>
		學生姓名：$stud_name 　　　　 □應屆畢業：$curr_class_name 　 □非應屆畢業 <br><br>          
		承辦人： 　　　　　　　　　　 承辦處室主任： 　　　　　　　　　　　　 填表日期： $today</td></tr></table><br>";
			
			$res->MoveNext();
		}
	} else {  $opinions_list="<br><br><br><center><font size=4 color='#ff0000'>未發現任何{$menu_arr[$menu]}紀錄！</font></center>";	}
	
	//六、其他生涯輔導紀錄
	$guidance_list="<br>六、其他生涯輔導紀錄<br>（一）生涯輔導紀錄（由導師或輔導教師填寫）<br>
　　1.請導師、輔導教師與學生或家長進行生涯諮詢輔導後，或其他適當時機（如：學生性向或興趣測驗個別解釋、生涯探索活動檢核回饋、家長日或班親會座談討論、家訪或電訪紀錄等），將輔導重點或建議記錄於本頁，作為九年級與學生討論生涯發展規劃書時之參考。<br>
　　2.諮詢輔導內容請掌握各年級生涯輔導核心內涵（七年級--自我察覺與探索：產業初探、八年級--生涯覺察與試探：認識生涯類群、九年級--生涯探索與進路選擇）。<br>
　　3.建議教師可影印本頁以方便隨時記錄或建置於電腦檔案中，再定期將紀錄表浮貼於手冊。<br>
				<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
				<tr bgcolor='#ffcccc' align='center'><td>NO.</td><td>日期</td><td>對象</td><td>輔導重點或建議</td><td>輔導教師</td><td>紀錄日期</td></tr>";
	//抓取既有諮詢紀錄
	$query="select * from career_guidance where student_sn=$student_sn order by guidance_date";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	if($res){
		while(!$res->EOF){
			$ii++;
			$sn=$res->fields['sn'];
			$emphasis=str_replace("\r\n",'<br>',$res->fields['emphasis']);
			$guidance_list.="<tr align='center'>
			<td>$ii</td>						
			<td>{$res->fields['guidance_date']}</td>
			<td>{$res->fields['target']}</td>
			<td align='left'>$emphasis</td>
			<td>{$res->fields['teacher_name']}</td>	
			<td>{$res->fields['update_time']}</td>
				</tr>";	
			$res->MoveNext();
		}
	} else $guidance_list.="<tr align='center'><td colspan=7 height=24>未發現生涯輔導既有諮詢紀錄！</td></tr>";
	$guidance_list.="</table>";
	
	//表格欄位抬頭
	$consultation_list="（二）生涯諮詢紀錄（由學生填寫）<br>
　　您曾與學校教師或家長討論過與未來升學或就業有關的事情嗎?師長與您討論的內容及建議，可以作為九年級進行生涯抉擇時的參考。請您將每學期與師長討論有關生涯規劃的內容摘要記錄於本頁。<br>
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>年級</td><td>日期</td><td>您諮詢的師長</td><td>討論重點及意見</td><td>備註</td>";
	
	//抓取既有諮詢紀錄
	$query="select * from career_consultation where student_sn=$student_sn order by seme_key";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	if($res){
		while(!$res->EOF){
			$ii++;
			$sn=$res->fields['sn'];
			$memo=str_replace("\r\n",'<br>',$res->fields['memo']);
			$emphasis=str_replace("\r\n",'<br>',$res->fields['emphasis']);
			$consultation_list.="<tr align='center'>
				<td>$ii</td>
				<td>{$res->fields['seme_key']}</td>
				<td>{$res->fields['consultation_date']}</td>
				<td>{$res->fields['teacher_name']}</td>						
				<td align='left'>$emphasis</td>
				<td align='left'>$memo</td>
				</tr>";	
			$res->MoveNext();
		}
	} else $consultation_list.="<tr align='center'><td colspan=7 height=24>未發現生涯輔導既有諮詢紀錄！</td></tr>";
	$consultation_list.="</table>";
	
	//表格欄位抬頭
	$parent_list="（三）家長的話<br>	
　　孩子在選擇國中畢業後想升讀的學校時，家長的意見與鼓勵非常重要。請您協助孩子一起完成這本生涯輔導紀錄手冊，陪他找到適合自己的發展方向。<br>
　　請您參閱孩子在本學年度已完成的資料，寫下給孩子的鼓勵與建議，並提醒孩子將本手冊繳交學校教師。謝謝您！<br>
		<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width=100%>
		<tr align='center' bgcolor='#ffcccc'><td>NO.</td><td>年級-學期</td><td>日期</td><td>參閱資料</td><td>給孩子的鼓勵及建議</td><td>親師溝通導師簽章</td>";
	
	//抓取既有諮詢紀錄
	$query="select * from career_parent where student_sn=$student_sn order by seme_key";
	$res=$CONN->Execute($query) or die("SQL錯誤:$query");
	if($res){
		while(!$res->EOF){
			$ii++;
			//參閱資料還原為陣列
			$items_array=unserialize($res->fields['items']);
			
			$sn=$res->fields['sn'];
			$items_list='';
			foreach($items as $key=>$value){
				$color=$items_array[$key]?'#ff0000':'#000000';
				$checked=$items_array[$key]?'●':'○';
				$items_list.="$checked $value<br>";
			}
			$items_list.="";
			
			$suggestion=str_replace("\r\n",'<br>',$res->fields['suggestion']);
			$tutor_confirm=str_replace("\r\n",'<br>',$res->fields['tutor_confirm']);
			$parent_list.="<tr align='center'>
				<td>$ii</td>
				<td>{$res->fields['seme_key']}</td>
				<td>{$res->fields['suggestion_date']}</td>
				<td align='left'>$items_list</td>						
				<td align='left'>$suggestion</td>						
				<td align='left'>{$res->fields['tutor_confirm']}<br>{$res->fields['tutor_name']}-{$res->fields['confirm_date']}</td>						
				</tr>";	
			$res->MoveNext();
		}
	} else $parent_list.="<tr align='center'><td colspan=7 height=24>未發現生涯輔導家長的鼓勵及建議紀錄！</td></tr>";
	$parent_list.="</table>";
	
	$memo="附錄<br>十三職群與相關性向測驗、興趣測驗之對應分析結果<br>
<font size=1>　　一、十三職群與相關性向測驗、興趣測驗之對應分析結果，乃參採教育部於2010年委託國立臺灣師範大學進行「遴薦國民中學學生選習技藝教育課程之相關測驗調查工具研究」，經調查目前多數國中學校普遍使用之性向、興趣測驗（多因素性向測驗、我喜歡做的事、國中生涯興趣量表）做為主要分析研究之對象，故無法涵蓋所有學校使用之相關測驗，惟各校仍可就相關測驗中之各分測驗結果自行參考對照。<br>
　　二、適性化職涯性向測驗及情境式職涯興趣測驗係為國科會期中報告「職涯資訊系統：新模型、新設計、新貢獻」提供十三職群對應分析結果。<br>
<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
<tr align='center'>
	<td>測驗名稱</td><td>多因素<br>性向測驗</td><td>我喜歡做的事</td><td>國中生涯<br>興趣量表</td><td>適性化職涯<br>性向測驗</td><td>情境式職涯<br>興趣測驗</td>
</tr>
<tr align='center' bgcolor='#ffcccc'>
<td>
出版社</td><td>中國行為<br>科學社</td><td>勞委會<br>職業訓練局</td><td>心理出版社</td><td>臺灣師大<br>心測中心</td><td>臺灣師大<br>


心測中心</td>
</tr>
<tr align='center' bgcolor='#ccccff'>
<td>
<p align='right'>編製年代</p><p align='left'>職群名稱</p></td>
<td>1984</td><td>1987</td><td>
2000</td>
<td>
2011</td>
<td>
2011</td>
</tr>
</thead>
<tr align='center'>
<td>
機械群</td>
<td>
語文推理<br>
數學推理<br>
空間關係<br>
機械推理</td>
<td>
機械<br>
工業生產<br>
企業事務<br>
領導</td>
<td>
農工型<br>
數理型<br>
服務型</td>
<td>
數學<br>
空間<br>
科學推理</td>
<td>
R（實用型）<br>
I（研究型）<br>
C（事務型）</td>
</tr>
<tr align='center'>
<td>
動力機<br>
械群</td>
<td>
語文推理<br>
數學推理<br>
空間關係<br>
機械推理</td>
<td>
機械<br>
個人服務<br>
工業生產<br>
銷售</td>
<td>
農工型<br>
數理型<br>
服務型</td>
<td>
語文<br>
空間<br>
邏輯推理<br>
科學推理</td>
<td>
R（實用型）<br>
S（社會型）</td>
</tr>
<tr align='center'>
<td>
電機與電<br>
子群</td>
<td>
數學推理<br>
空間關係<br>
抽象推理</td>
<td>
科學<br>
機械<br>
工業生產<br>
保全</td>
<td>
農工型<br>
數理型<br>
服務型</td>
<td>
數學<br>
空間<br>
邏輯推理</td>
<td>
R（實用型）<br>
I（研究型）</td>
</tr>
<tr align='center'>
<td>
土木與<br>
建築群</td>
<td>
數學推理<br>
空間關係<br>
抽象推理<br>
知覺速度與確度</td>
<td>
建築：科學<br>
藝術<br>
機械<br>
領導<br>
土木：科學<br>
機械<br>
藝術<br>
個人服務<br>
社會服務</td>
<td>
農工型<br>
數理型<br>
文藝型<br>
文書型</td>
<td>
數學<br>
空間<br>
邏輯推理<br>
美感</td>
<td>
R（實用型）<br>
A（藝術型）</td>
</tr>
<tr align='center'>
<td>
化工群</td>
<td>
數學推理<br>
抽象推理<br>
知覺速度與確度</td>
<td>
科學<br>
機械<br>
工業生產</td>
<td>
數理型<br>
農工型<br>
文書型</td>
<td>
數學<br>
邏輯推理<br>
觀察</td>
<td>
R（實用型）<br>
I（研究型）<br>
A（藝術型）</td>
</tr>
<tr align='center'>
<td>
商業與<br>
管理群</td>
<td>
語文推理<br>
數學推理<br>
抽象推理<br>
知覺速度與確度</td>
<td>
企業事務<br>
銷售<br>
個人服務</td>
<td>
服務型<br>
文書型<br>
法商型</td>
<td>
語文<br>
數學<br>
邏輯推理</td><td>
E（企業型）<br>
C（事務型）<br>
S（社會型）</td>
</tr>
<tr align='center'>
<td>
設計群</td>
<td>
語文推理<br>
空間關係<br>
抽象推理<br>
知覺速度與確度</td>
<td>
藝術<br>
銷售<br>
機械<br>
領導</td>
<td>
文藝型<br>
法商型<br>
農工型</td>
<td>
空間<br>
觀察<br>
美感<br>
創意</td>
<td>
A（藝術型）<br>
R（實用型）</td>
</tr>
<tr align='center'>
<td>
農業群</td>
<td>
數學推理<br>
抽象推理<br>
知覺速度與確度</td>
<td>
動植物<br>
科學<br>
機械</td>
<td>
農工型<br>
數理型<br>
文藝型<br>
服務型</td>
<td>
邏輯推理<br>
觀察<br>
創意</td>
<td>
R（實用型）<br>
S（社會型）<br>
I（研究型）</td>
</tr>
<tr align='center'>
<td>
食品群</td>
<td>
語文推理<br>
數學推理</td>
<td>
工業生產<br>
科學<br>
藝術<br>
機械</td>
<td>
農工型<br>
數理型<br>
服務型<br>
文書型</td>
<td>
數學<br>
邏輯推理<br>
創意</td>
<td>
R（實用型）<br>
E（企業型）<br>
S（社會型）</td>
</tr>
<tr align='center'>
<td>
家政群</td>
<td>
語文推理<br>
空間關係<br>
知覺速度與確度</td>
<td>
藝術<br>
銷售<br>
個人服務</td>
<td>
服務型<br>
文藝型<br>
數理型<br>
法商型</td>
<td>
語文<br>
空間<br>
美感<br>
創意</td>
<td>
S（社會型）<br>
A（藝術型）</td>
</tr>
<tr align='center'>
<td>
餐旅群</td>
<td>
語文推理<br>
數學推理<br>
知覺速度與確度</td>
<td>
個人服務<br>
銷售<br>
藝術</td>
<td>
服務型<br>
文藝型<br>
農工型</td>
<td>
空間<br>
觀察<br>
美感<br>
創意</td>
<td>
S（社會型）<br>
R（實用型）</td>
</tr>
<tr align='center'>
<td>
水產群</td>
<td>
機械推理<br>
知覺速度與確度</td>
<td>
動植物<br>
科學<br>
機械<br>
銷售</td>
<td>
農工型<br>
數理型<br>
服務型</td>
<td>
邏輯推理<br>
觀察</td>
<td>
R（實用型）<br>
E（企業型）<br>
I（研究型）</td>
</tr>
<tr align='center'>
<td>
海事群</td>
<td>
空間關係<br>
機械推理<br>
知覺速度與確度</td>
<td>
機械<br>
工業生產<br>
領導</td>
<td>
農工型<br>
服務型<br>
數理型</td>
<td>
語文<br>
空間<br>
科學推理</td>
<td>R（實用型）<br>S（社會型）</td>
	</tr>
</table>
　資料來源：徐昊杲（2010）。遴薦國民中學學生選習技藝教育課程之相關測驗調查工具研究。臺北：教育部。<br>
　宋曜廷（2010）。職涯資訊系統：新模型、新設計、新貢獻，國科會期中報告。</font>

$new_page

<table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#111111' width='100%'>
<tr align='center' bgcolor='#ffcccc'><td rowspan=2>學期別</td><td colspan=2>{$class_year[$min]}級</td><td colspan=2>{$class_year[$min+1]}級</td><td colspan=2>{$class_year[$min+2]}級</td></tr>
<tr align='center' bgcolor='#ffcccc'><td>第1學期</td><td>第2學期</td><td>第1學期</td><td>第2學期</td><td>第1學期</td><td>第2學期</td></tr>
<tr style='height: 72.9pt' align='center'><td>審閱<br>核章</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<tr style='height: 72.9pt' align='center'><td>審閱<br>日期</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
<tr style='height: 72.9pt' align='center'><td>備註</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
";
	
		$rpt.="<center><P style='font-size:36px; color:blue; font-family:標楷體'><br><br>國中學生生涯輔導紀錄手冊</p><br>(101.8-104.6)<br><P style='font-size:12px; color:red;'>《學生個人資料，請妥善保管，並遵守保密原則》</P><br><br><br><br><br><br>
		<h3><table align='center'><tr><td>學校名稱：</td><td>$school_long_name</td></tr><tr><td>學生姓名：</td><td>$stud_name</td></tr></table></h3>$contact_list</center>
		$new_page $room_list<br>$contact_list2
		$new_page $words
		$new_page $mystory $new_page $mystory2
		$new_page $psy_test
		$new_page $study_spe
		$new_page $assistant_list<br>$club_list
		$new_page $race_list
		$new_page $reward_list $seme_list
		$new_page $service_list
		$new_page $explore_list
		$new_page $ponder_list		
		$new_page $direction_list<br>$course_list<br><br>
		$geodata
		$new_page $evaluate_list
		$new_page $psy_result $study_list $way_result
		$new_page $opinions_list
		$new_page $guidance_list
		$new_page $consultation_list
		$new_page $parent_list
		$new_page $memo
		";
		
		$rpt.=$new_page;
	}
	echo '<html>	<head>'."\n";
	echo '<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>'."\n";
	echo '<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>'."\n";
	echo '<script type="text/javascript" src="'.$SFS_PATH_HTML.'javascripts/gmap3.js"></script>'."\n";			
	echo '<script type="text/javascript" src="'.$SFS_PATH_HTML.'javascripts/markerwithlabel.js"></script>'."\n";	
	echo "
	<script type='text/javascript'>
      $(function(){
        var addressArr = {};";
	 foreach ($geodataSchool as $ii=>$schoolName)
        echo "addressArr['[".($ii+1)."] $schoolName'] = '$schoolName';";
        
     echo "
		addressArr['我的家'] = '$stud_addr'; 
     	$.each(addressArr , function(label ,address) {
        $('#test1').gmap3({
          defaults:{ 
            classes:{
              Marker:MarkerWithLabel
            }
          },
          map:{
            address:address,
            options:{
              zoom: 8,
            }
          },
          marker:{
            address:address,
            options:{
              labelContent: '$425K',
              labelAnchor: new google.maps.Point(52, -2),
              labelClass: 'labels',
              labelStyle: {opacity: 0.75},
              labelContent:  label
            }
          }
        });
       });
        
     });
    </script>	\n";
	echo '
	<style>
		.labels {font-size:10px; color: break; background:#af0;  white-space:nowrap; padding:2px}
      .gmap3{
        margin: 20px auto;
        border: 1px dashed #C0C0C0;
        width: 800px;
        height: 300px;
      }
    </style>
			';
	echo '</head><body>';
	echo $rpt;
	echo '</body></html>';
	exit;
}




//秀出網頁
head("生涯輔導報表輸出");

echo <<<HERE
<script>

function tagall(status,s) {
  var i =0;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].name==s) {
      document.myform.elements[i].checked=status;
    }
    i++;
  }
}

function check_select() {
  var i=0; j=0; answer=true;
  while (i < document.myform.elements.length)  {
    if(document.myform.elements[i].checked) {
		if(document.myform.elements[i].name=='student_sn[]') j++;
    }
    i++;
  }
  
  if(j==0) { alert("尚未選取學生！"); answer=false; }
  
  return answer;
}

</script>
HERE;

//模組選單
print_menu($menu_p,$linkstr);

if($c_id){
	//字體選項
	$font_array=array(0=>'',1=>'',);
	$font_radio="<inpit type='radio' name='font' value=";
	
	$class_select.="<input type='checkbox' name='stud_check' onclick='javascript:tagall(this.checked,\"student_sn[]\");'>選取全部/取消選取 <input type='submit' name='go'  value='報表輸出' onclick='this.form.target=\"_BLANK\"; return check_select();'>";
	$student_select='';
	//產生學生名單
	$query="select a.student_sn,a.seme_num,b.stud_name,b.stud_sex from `stud_seme` a inner join stud_base b on a.student_sn=b.student_sn where seme_year_seme='$curr_year_seme' and seme_class='{$c_id}' order by a.seme_num";
	$res=$CONN->Execute($query) or die("SQL錯誤：<br>$query");
	while(!$res->EOF){
		$i++;
		$checked=($student_sn==$res->fields['student_sn'])?'checked':'';
		$color=($res->fields['stud_sex']==1)?'#0000ff':'#ff0000';
		$color=($student_sn==$res->fields['student_sn'])?'#00ff00':$color;
		$student_select.="<input type='checkbox' name='student_sn[]' value='{$res->fields['student_sn']}' $checked><font color='$color'>({$res->fields['seme_num']}) {$res->fields['stud_name']}</font> ";
		if($i%7==0) $student_select.="<br>";
		
		$res->MoveNext();
	}
}

$main="<font size=2><form method='post' action='$_SERVER[SCRIPT_NAME]' name='myform'><table border='2' cellpadding='3' cellspacing='0' style='border-collapse: collapse; font-size=12px;' bordercolor='#aa1111' width=700>
<tr><td valign='top'>$class_select</td></tr><tr><td>$student_select</td></tr></table></form></font>";

echo $main;

foot();

?>
