<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE 學籍交換資料 SYSTEM "http://sfscvs.tc.edu.tw/student_3_0_tcc.dtd" >
<學籍交換資料>
{{foreach from=$data_arr item=content key=arr_key}}
	<學生資料>
		<學生基本資料>
			<基本資料>
				<學生姓名>{{$data_arr[$arr_key].stud_name}}</學生姓名>
	{{assign var=stud_sex value=$data_arr[$arr_key].stud_sex}}
				<學生性別>{{$sex_arr[$stud_sex]}}</學生性別>
				<學生生日>{{$data_arr[$arr_key].stud_birthday}}</學生生日>
				<現在年級>{{$data_arr[$arr_key].year_num}}</現在年級>
				<現在班級>{{$data_arr[$arr_key].class_num}}</現在班級>
				<現在座號>{{$data_arr[$arr_key].site_num}}</現在座號>
				<學生身份註記>
	{{assign var=stud_kind value=$data_arr[$arr_key].stud_kind}}
	{{foreach from=$stud_kind item=sk_arr key=sk_key}}
					<學生身份註記_資料內容>
						<學生身份註記_類別>{{$stud_kind_arr[$sk_key]}}</學生身份註記_類別>
						<學生身份註記_備註>null</學生身份註記_備註>
					</學生身份註記_資料內容>
	{{/foreach}}
				</學生身份註記>
				<原住民>
					<原住民_居住地>{{$data_arr[$arr_key].yuanzhumin.area}}</原住民_居住地>
					<原住民_族別>{{$data_arr[$arr_key].yuanzhumin.clan}}</原住民_族別>
				</原住民>
				<身分證證照>
					<國籍>{{$data_arr[$arr_key].stud_country}}</國籍>
	{{assign var=id_kind value=$data_arr[$arr_key].stud_country_kind}}
					<證照種類>{{$id_kind_arr[$id_kind]}}</證照種類>
					<證照號碼>{{$data_arr[$arr_key].stud_person_id}}</證照號碼>
					<僑居地>{{$data_arr[$arr_key].stud_country_name}}</僑居地>
				</身分證證照>
				<連絡資料>
					<戶籍地址>
						<戶籍地址_縣市名>{{$data_arr[$arr_key].stud_addr_1.0}}</戶籍地址_縣市名>
						<戶籍地址_鄉鎮市區名>{{$data_arr[$arr_key].stud_addr_1.1}}</戶籍地址_鄉鎮市區名>
						<戶籍地址_村里>{{$data_arr[$arr_key].stud_addr_1.2}}</戶籍地址_村里>
						<戶籍地址_鄰>{{$data_arr[$arr_key].stud_addr_1.3}}</戶籍地址_鄰>
						<戶籍地址_路街>{{$data_arr[$arr_key].stud_addr_1.4}}</戶籍地址_路街>
						<戶籍地址_段>{{$data_arr[$arr_key].stud_addr_1.5}}</戶籍地址_段>
						<戶籍地址_巷>{{$data_arr[$arr_key].stud_addr_1.6}}</戶籍地址_巷>
						<戶籍地址_弄>{{$data_arr[$arr_key].stud_addr_1.7}}</戶籍地址_弄>
						<戶籍地址_號>{{$data_arr[$arr_key].stud_addr_1.8}}</戶籍地址_號>
						<戶籍地址_之>{{$data_arr[$arr_key].stud_addr_1.9}}</戶籍地址_之>
						<戶籍地址_樓>{{$data_arr[$arr_key].stud_addr_1.10}}</戶籍地址_樓>
						<戶籍地址_樓之>{{$data_arr[$arr_key].stud_addr_1.11}}</戶籍地址_樓之>
						<戶籍地址_其他>{{$data_arr[$arr_key].stud_addr_1.12}}</戶籍地址_其他>
					</戶籍地址>
					<通訊地址>
						<通訊地址_縣市名>{{$data_arr[$arr_key].stud_addr_2.0}}</通訊地址_縣市名>
						<通訊地址_鄉鎮市區名>{{$data_arr[$arr_key].stud_addr_2.1}}</通訊地址_鄉鎮市區名>
						<通訊地址_村里>{{$data_arr[$arr_key].stud_addr_2.2}}</通訊地址_村里>
						<通訊地址_鄰>{{$data_arr[$arr_key].stud_addr_2.3}}</通訊地址_鄰>
						<通訊地址_路街>{{$data_arr[$arr_key].stud_addr_2.4}}</通訊地址_路街>
						<通訊地址_段>{{$data_arr[$arr_key].stud_addr_2.5}}</通訊地址_段>
						<通訊地址_巷>{{$data_arr[$arr_key].stud_addr_2.6}}</通訊地址_巷>
						<通訊地址_弄>{{$data_arr[$arr_key].stud_addr_2.7}}</通訊地址_弄>
						<通訊地址_號>{{$data_arr[$arr_key].stud_addr_2.8}}</通訊地址_號>
						<通訊地址_之>{{$data_arr[$arr_key].stud_addr_2.9}}</通訊地址_之>
						<通訊地址_樓>{{$data_arr[$arr_key].stud_addr_2.10}}</通訊地址_樓>
						<通訊地址_樓之>{{$data_arr[$arr_key].stud_addr_2.11}}</通訊地址_樓之>
						<通訊地址_其他>{{$data_arr[$arr_key].stud_addr_2.12}}</通訊地址_其他>
					</通訊地址>
					<通訊電話>{{$data_arr[$arr_key].stud_tel_2}}</通訊電話>
					<行動電話>{{$data_arr[$arr_key].stud_tel_3}}</行動電話>
				</連絡資料>
				<學生班級性質>
	{{assign var=class_kind value=$data_arr[$arr_key].stud_class_kind}}
	{{assign var=stud_spe_kind value=$data_arr[$arr_key].stud_spe_kind}}
	{{assign var=stud_spe_class_kind value=$data_arr[$arr_key].stud_spe_class_kind}}
	{{assign var=stud_spe_class_id value=$data_arr[$arr_key].stud_spe_class_id}}
					<班級性質>{{$class_kind_arr[$class_kind]}}</班級性質>
					<特教班類別>{{$spe_kind_arr[$stud_spe_kind]}}</特教班類別>
					<特教班班別>{{$spe_class_kind_arr[$stud_spe_class_kind]}}</特教班班別>
					<特殊班上課性質>{{$spe_class_id_arr[$stud_spe_class_id]}}</特殊班上課性質>
				</學生班級性質>
				<入學前教育資料>
					<幼稚園入學>
	{{assign var=preschool_status value=$data_arr[$arr_key].stud_preschool_status}}
						<幼稚園入學資格>{{$preschool_status_arr[$preschool_status]}}</幼稚園入學資格>
						<幼稚園_教育部學校代碼>{{$data_arr[$arr_key].stud_preschool_id}}</幼稚園_教育部學校代碼>
						<幼稚園_學校名稱>{{$data_arr[$arr_key].stud_preschool_name}}</幼稚園_學校名稱>
					</幼稚園入學>
					<國小入學>
	{{assign var=preschool_status value=$data_arr[$arr_key].stud_Mschool_status}}
						<國小入學資格>{{$preschool_status_arr[$preschool_status]}}</國小入學資格>
						<國小_教育部學校代碼>{{$data_arr[$arr_key].stud_mschool_id}}</國小_教育部學校代碼>
						<國小_學校名稱>{{$data_arr[$arr_key].stud_mschool_name}}</國小_學校名稱>
					</國小入學>
				</入學前教育資料>
				<畢修業核准文號>
	{{assign var=grad_kind value=$data_arr[arr_key].grad_kind}}
					<畢修業別>{{$grad_kind_arr[$grad_kind]}}</畢修業別>
					<畢修業_日期>{{$data_arr[$arr_key].grad_date}}</畢修業_日期>
					<畢修業_字>{{$data_arr[$arr_key].grad_word}}</畢修業_字>
					<畢修業_號>{{$data_arr[$arr_key].grad_num}}</畢修業_號>
				</畢修業核准文號>
				<父親基本資料>
					<父親_姓名>{{$data_arr[$arr_key].fath_name}}</父親_姓名>
					<父親_出生年次>{{$data_arr[$arr_key].fath_birthyear}}</父親_出生年次>
					<父親_原國籍></父親_原國籍>
					<父親_已入中華民國國籍></父親_已入中華民國國籍>
	{{assign var=f_is_live value=$data_arr[$arr_key].fath_alive}}
					<父親_存歿>{{$is_live_arr[$f_is_live]}}</父親_存歿>
	{{assign var=f_rela value=$data_arr[$arr_key].fath_relation}}
					<與父關係>{{$f_rela_arr[$f_rela]}}</與父關係>
					<父親_身分證號>{{$data_arr[$arr_key].fath_p_id}}</父親_身分證號>
	{{assign var=f_edu value=$data_arr[$arr_key].fath_education}}
					<父親_教育程度>{{$edu_kind_arr[$f_edu]}}</父親_教育程度>
	{{assign var=f_grad_kind value=$data_arr[$arr_key].fath_grad_kind}}
					<父親_畢修業別>{{$grad_kind_arr[$f_grad_kind]}}</父親_畢修業別>
					<父親_職業>{{$data_arr[$arr_key].fath_occupation}}</父親_職業>
					<父親_服務單位>{{$data_arr[$arr_key].fath_unit}}</父親_服務單位>
					<父親_職稱>{{$data_arr[$arr_key].fath_work_name}}</父親_職稱>
					<父親_電話號碼-公>{{$data_arr[$arr_key].fath_phone}}</父親_電話號碼-公>
					<父親_電話號碼-宅>{{$data_arr[$arr_key].fath_home_phone}}</父親_電話號碼-宅>
					<父親_行動電話>{{$data_arr[$arr_key].fath_hand_phone}}</父親_行動電話>
					<父親_電子郵件信箱>{{$data_arr[$arr_key].fath_email}}</父親_電子郵件信箱>
				</父親基本資料>
				<母親基本資料>
					<母親_姓名>{{$data_arr[$arr_key].moth_name}}</母親_姓名>
					<母親_出生年次>{{$data_arr[$arr_key].moth_birthyear}}</母親_出生年次>
					<母親_原國籍></母親_原國籍>
					<母親_已入中華民國國籍></母親_已入中華民國國籍>
	{{assign var=m_is_live value=$data_arr[$arr_key].moth_alive}}
					<母親_存歿>{{$is_live_arr[$m_is_live]}}</母親_存歿>
	{{assign var=m_rela value=$data_arr[$arr_key].moth_relation}}
					<與母關係>{{$m_rela_arr[$m_rela]}}</與母關係>
					<母親_身分證號>{{$data_arr[$arr_key].moth_p_id}}</母親_身分證號>
	{{assign var=m_edu value=$data_arr[$arr_key].moth_education}}
					<母親_教育程度>{{$edu_kind_arr[$m_edu]}}</母親_教育程度>
	{{assign var=m_grad_kind value=$data_arr[$arr_key].moth_grad_kind}}
					<母親_畢修業別>{{$grad_kind_arr[$m_grad_kind]}}</母親_畢修業別>
					<母親_職業>{{$data_arr[$arr_key].moth_occupation}}</母親_職業>
					<母親_服務單位>{{$data_arr[$arr_key].moth_unit}}</母親_服務單位>
					<母親_職稱>{{$data_arr[$arr_key].moth_work_name}}</母親_職稱>
					<母親_電話號碼-公>{{$data_arr[$arr_key].moth_phone}}</母親_電話號碼-公>
					<母親_電話號碼-宅>{{$data_arr[$arr_key].moth_home_phone}}</母親_電話號碼-宅>
					<母親_行動電話>{{$data_arr[$arr_key].moth_hand_phone}}</母親_行動電話>
					<母親_電子郵件信箱>{{$data_arr[$arr_key].moth_email}}</母親_電子郵件信箱>
				</母親基本資料>
				<祖父基本資料>
					<祖父_姓名>{{$data_arr[$arr_key].grandfath_name}}</祖父_姓名>
	{{assign var=gf_is_live value=$data_arr[$arr_key].grandfath_alive}}
					<祖父_存歿>{{$is_live_arr[$gf_is_live]}}</祖父_存歿>
				</祖父基本資料>
				<祖母基本資料>
					<祖母_姓名>{{$data_arr[$arr_key].grandmoth_name}}</祖母_姓名>
	{{assign var=gm_is_live value=$data_arr[$arr_key].grandmoth_alive}}
					<祖母_存歿>{{$is_live_arr[$gm_is_live]}}</祖母_存歿>
				</祖母基本資料>
				<監護人>
					<監護人_姓名>{{$data_arr[$arr_key].guardian_name}}</監護人_姓名>
	{{assign var=g_rela value=$data_arr[$arr_key].guardian_relation}}
					<與監護人之關係>{{$g_rela_arr[$m_rela]}}</與監護人之關係>
					<監護人_身分證號>{{$data_arr[$arr_key].guardian_p_id}}</監護人_身分證號>
					<監護人_地址>{{$data_arr[$arr_key].guardian_address}}</監護人_地址>
					<監護人_服務單位>{{$data_arr[$arr_key].guardian_unit}}</監護人_服務單位>
					<監護人_職稱>{{$data_arr[$arr_key].grandmoth_name}}</監護人_職稱>
					<監護人_連絡電話>{{$data_arr[$arr_key].guardian_phone}}</監護人_連絡電話>
					<監護人_行動電話>{{$data_arr[$arr_key].guardian_hand_phone}}</監護人_行動電話>
					<監護人_電子郵件信箱>{{$data_arr[$arr_key].guardian_email}}</監護人_電子郵件信箱>
				</監護人>
				<兄弟姊妹>
	{{assign var=bs_arr value=$data_arr[$arr_key].bro_sis}}
	{{foreach from=$bs_arr item=bs key=bs_key}}
					<兄弟姊妹_資料內容>
	{{assign var=bs_calling value=$bs_arr[$bs_key].bs_calling}}
						<兄弟姊妹_稱謂>{{$bs_calling_kind_arr[$bs_calling]}}</兄弟姊妹_稱謂>
						<兄弟姊妹_姓名>{{$bs_arr[$bs_key].bs_name}}</兄弟姊妹_姓名>
					</兄弟姊妹_資料內容>
	{{/foreach}}
				</兄弟姊妹>
				<其他親屬>
	{{assign var=kin_arr value=$data_arr[$arr_key].kinfolk}}
	{{foreach from=$kin_arr item=kin key=kin_key}}
					<其他親屬_資料內容>
						<其他親屬_姓名>{{$kin_arr[$kin_key].kin_name}}</其他親屬_姓名>
	{{assign var=kin_calling value=$kin_arr[$kin_key].kin_calling}}
						<其他親屬_稱謂>{{$g_rela_arr[$kin_calling]}}</其他親屬_稱謂>
						<其他親屬_連絡電話>{{$kin_arr[$kin_key].kin_phone}}</其他親屬_連絡電話>
						<其他親屬_行動電話>{{$kin_arr[$kin_key].kin_hand_phone}}</其他親屬_行動電話>
						<其他親屬_電子郵件信箱>{{$kin_arr[$kin_key].kin_email}}</其他親屬_電子郵件信箱>
					</其他親屬_資料內容>
	{{/foreach}}
				</其他親屬>
			</基本資料>
		</學生基本資料>
		<學期資料>
			{{assign var=semester_arr value=$data_arr[$arr_key].semester}}
			{{foreach from=$semester_arr item=semester key=semester_key}}
			<個別學期資料>
				<學年別>{{$semester_arr[$semester_key].year}}</學年別>
				<學期別>{{$semester_arr[$semester_key].semester}}</學期別>
				<班級座號>
					{{assign var=study_year value=$semester_arr[$semester_key].study_year}}
					<年級>{{$study_year}}</年級>
					<班級>{{$semester_arr[$semester_key].seme_class_name}}</班級>
					<座號>{{$semester_arr[$semester_key].seme_num}}</座號>
				</班級座號>
				<學期成績>
					<導師姓名>{{$semester_arr[$semester_key].teacher}}</導師姓名>
					<語文_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.language.$semester_key.score}}</語文_學習領域百分制成績>
					<語文_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.chinese}};{{$data_arr[$arr_key].semester_score_memo.$semester_key.local}};{{$data_arr[$arr_key].semester_score_memo.$semester_key.english}}</語文_學習領域文字描述>
					<本國語文百分制成績>{{$data_arr[$arr_key].semester_score.chinese.$semester_key.score}}</本國語文百分制成績>
					<本土語文百分制成績>{{$data_arr[$arr_key].semester_score.local.$semester_key.score}}</本土語文百分制成績>
					<本土語言類別></本土語言類別>
					<英語百分制成績>{{$data_arr[$arr_key].semester_score.english.$semester_key.score}}</英語百分制成績>
					<數學_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.math.$semester_key.score}}</數學_學習領域百分制成績>
					<數學_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.math}}</數學_學習領域文字描述>
					<自然與生活科技_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.nature.$semester_key.score}}</自然與生活科技_學習領域百分制成績>
					<自然與生活科技_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.nature}}</自然與生活科技_學習領域文字描述>
					<社會_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.social.$semester_key.score}}</社會_學習領域百分制成績>
					<社會_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.social}}</社會_學習領域文字描述>
					<健康與體育_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.health.$semester_key.score}}</健康與體育_學習領域百分制成績>
					<健康與體育_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.health}}</健康與體育_學習領域文字描述>
					<藝術與人文_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.art.$semester_key.score}}</藝術與人文_學習領域百分制成績>
					<藝術與人文_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.art}}</藝術與人文_學習領域文字描述>
					<生活課程_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.life.$semester_key.score}}</生活課程_學習領域百分制成績>
					<生活課程_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.life}}</生活課程_學習領域文字描述>
					<綜合活動_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.complex.$semester_key.score}}</綜合活動_學習領域百分制成績>
					<綜合活動_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.complex}}</綜合活動_學習領域文字描述>
					<彈性時數>
						{{assign var=semester_elasticity_arr value=$data_arr[$arr_key].semester_score_memo.$semester_key.elasticity}}
						{{foreach from=$semester_elasticity_arr item=elasticity_data key=subject_id}}
						<彈性時數_分項科目>
							<彈性時數_科目名稱>{{$elasticity_data.subject_name}}</彈性時數_科目名稱>
							<彈性時數_科目百分制成績>{{$elasticity_data.score}}</彈性時數_科目百分制成績>
						</彈性時數_分項科目>
						{{/foreach}}
					</彈性時數>
				</學期成績>
				<日常生活表現>
					<日常生活表現_文字描述>{{$data_arr[$arr_key].semester_score_nor.$semester_key.ss_score_memo}}</日常生活表現_文字描述>
					<學期出缺席_應出席日數>{{$seme_course_date_arr[$semester_key].$study_year}}</學期出缺席_應出席日數>
					<學期出缺席_事假數>{{$data_arr[$arr_key].semester_absence.$semester_key.1}}</學期出缺席_事假數>
					<學期出缺席_病假數>{{$data_arr[$arr_key].semester_absence.$semester_key.2}}</學期出缺席_病假數>
					<學期出缺席_曠課數>{{$data_arr[$arr_key].semester_absence.$semester_key.3}}</學期出缺席_曠課數>
					<學期出缺席_其他假數>{{$data_arr[$arr_key].semester_absence.$semester_key.others}}</學期出缺席_其他假數>
					<學期出缺席_單位>{{if $jhores>=6}}節{{else}}日{{/if}}</學期出缺席_單位>
				</日常生活表現>
				<特殊優良表現>
					{{assign var=semester_spe_arr value=$data_arr[$arr_key].semester_spe.$semester_key}}
					{{foreach from=$semester_spe_arr item=semester_spe_data key=ss_id}}
					<優良表現事蹟>
						<優良表現_日期>{{$semester_spe_data.sp_date}}</優良表現_日期>
						<優良表現_事由>{{$semester_spe_data.sp_memo}} </優良表現_事由>
					</優良表現事蹟>
					{{/foreach}}
				</特殊優良表現>
				<心理測驗>
					{{assign var=psy_test_arr value=$data_arr[$arr_key].psy_test.$semester_key}}
					{{foreach from=$psy_test_arr item=psy_test_data key=sn}}
					<心理測驗_資料內容>
						<心理測驗_名稱>{{$psy_test_data.item}}</心理測驗_名稱>
						<心理測驗_原始分數>{{$psy_test_data.score}}</心理測驗_原始分數>
						<心理測驗_常模樣本>{{$psy_test_data.model}}</心理測驗_常模樣本>
						<心理測驗_標準分數>{{$psy_test_data.standard}}</心理測驗_標準分數>
						<心理測驗_百分等級>{{$psy_test_data.pr}}</心理測驗_百分等級>
						<心理測驗_解釋>{{$psy_test_data.explanation}} </心理測驗_解釋>
					</心理測驗_資料內容>
					{{/foreach}}
				</心理測驗>
				<輔導紀錄>
					<父母關係>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_relation}}</父母關係>
					<家庭氣氛>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_family_air}}</家庭氣氛>
					<父管教方式>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_father}}</父管教方式>
					<母管教方式>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_mother}}</母管教方式>
					<居住情形>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_live_state}}</居住情形>
					<經濟狀況>{{$data_arr[$arr_key].semester_eduh.$semester_key.sse_rich_state}}</經濟狀況>
					<最喜愛學習領域>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s1 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<最喜愛學習領域_資料內容>{{$sse_data}}</最喜愛學習領域_資料內容>
						{{/if}}
						{{/foreach}}
					</最喜愛學習領域>
					<最困難學習領域>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s2 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<最困難學習領域_資料內容>{{$sse_data}}</最困難學習領域_資料內容>
						{{/if}}
						{{/foreach}}
					</最困難學習領域>
					<特殊才能>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s3 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<特殊才能_資料內容>{{$sse_data}}</特殊才能_資料內容>
						{{/if}}
						{{/foreach}}
						<武術_其他></武術_其他>
					</特殊才能>
					<興趣>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s4 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<興趣_資料內容>{{$sse_data}}</興趣_資料內容>
						{{/if}}
						{{/foreach}}
					</興趣>
					<生活習慣>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s5 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<生活習慣_資料內容>{{$sse_data}}</生活習慣_資料內容>
						{{/if}}
						{{/foreach}}
					</生活習慣>
					<人際關係>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s6 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<人際關係_資料內容>{{$sse_data}}</人際關係_資料內容>
						{{/if}}
						{{/foreach}}
					</人際關係>
					<外向行為>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s7 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<外向行為_資料內容>{{$sse_data}}</外向行為_資料內容>
						{{/if}}
						{{/foreach}}
					</外向行為>
					<內向行為>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s8 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<內向行為_資料內容>{{$sse_data}}</內向行為_資料內容>
						{{/if}}
						{{/foreach}}
					</內向行為>
					<學習行為>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s9 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<學習行為_資料內容>{{$sse_data}}</學習行為_資料內容>
						{{/if}}
						{{/foreach}}
					</學習行為>
					<不良習慣>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s10 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<不良習慣_資料內容>{{$sse_data}}</不良習慣_資料內容>
						{{/if}}
						{{/foreach}}
					</不良習慣>
					<焦慮行為>
						{{foreach from=$data_arr[$arr_key].semester_eduh.$semester_key.sse_s11 item=sse_data key=sn}}
						{{if $sse_data<>""}}
						<焦慮行為_資料內容>{{$sse_data}}</焦慮行為_資料內容>
						{{/if}}
						{{/foreach}}
					</焦慮行為>
				</輔導紀錄>
				<輔導訪談紀錄>
					{{foreach from=$data_arr[$arr_key].semester_talk.$semester_key item=talk_data key=sn}}
					<輔導訪談紀錄_資料內容>
						<紀錄日期>{{$talk_data.sst_date}}</紀錄日期>
						<連絡對象>{{$talk_data.sst_name}}</連絡對象>
						<連絡事項>{{$talk_data.sst_main}}</連絡事項>
						<內容要點>{{$talk_data.sst_memo}} </內容要點>
					</輔導訪談紀錄_資料內容>
					{{/foreach}}
				</輔導訪談紀錄>
			</個別學期資料>
			{{/foreach}}
		</學期資料>
		<異動資料>
			{{foreach from=$data_arr[$arr_key].stud_move item=move_data key=move_id}}
			<異動資料_資料內容>
				<原就讀縣市>{{$move_data.move_c_unit}}</原就讀縣市>
				<原就讀學校名稱>{{$move_data.school}}</原就讀學校名稱>
				<原就讀學校代碼></原就讀學校代碼>
				<異動日期>{{$move_data.move_date}}</異動日期>
				<異動核准機關名稱>{{$move_data.move_c_unit}}</異動核准機關名稱>
				<異動原因>{{$move_data.move_kind}}</異動原因>
				<核准文號_日期>{{$move_data.move_c_date}}</核准文號_日期>
				<核准文號_字>{{$move_data.move_c_word}}</核准文號_字>
				<核准文號_號>{{$move_data.move_c_num}}</核准文號_號>
			</異動資料_資料內容>
			{{/foreach}}
		</異動資料>
		<期中資料>
			<期中缺席>
				<期中總缺席_事假數>{{$data_arr[$arr_key].absent.$semester_key.summary.1}}</期中總缺席_事假數>
				<期中總缺席_病假數>{{$data_arr[$arr_key].absent.$semester_key.summary.2}}</期中總缺席_病假數>
				<期中總缺席_曠課數>{{$data_arr[$arr_key].absent.$semester_key.summary.3}}</期中總缺席_曠課數>
				<期中總缺席_其他假數>{{$data_arr[$arr_key].absent.$semester_key.summary.others}}</期中總缺席_其他假數>
				<期中總缺席_單位>日</期中總缺席_單位>
				<期中缺席_資料內容>
					{{foreach from=$data_arr[$arr_key].absent.$semester_key.monthly item=monthly_data key=monthly_id}}
					<期中缺席_分月資料>
						<期中缺席_年>{{$monthly_data.year}}</期中缺席_年>
						<期中缺席_月>{{$monthly_data.month}}</期中缺席_月>
						<期中缺席_事假數>{{$monthly_data.1}}</期中缺席_事假數>
						<期中缺席_病假數>{{$monthly_data.2}}</期中缺席_病假數>
						<期中缺席_曠課數>{{$monthly_data.3}}</期中缺席_曠課數>
						<期中缺席_其他假數>{{$monthly_data.others}}</期中缺席_其他假數>
					</期中缺席_分月資料>
					{{/foreach}}
				</期中缺席_資料內容>
			</期中缺席>
			<期中成績>
				<期中成績_語文>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.language item=language_data key=language_id}}
					<期中成績_語文資料內容>
						<期中成績_語文領域段考別>{{$language_id}}</期中成績_語文領域段考別>
						<期中成績_本國語文百分制成績>{{$language_data.chinese}}</期中成績_本國語文百分制成績>
						<期中成績_本土語文百分制成績>{{$language_data.local}}</期中成績_本土語文百分制成績>
						<期中成績_英語百分制成績>{{$language_data.english}}</期中成績_英語百分制成績>
					</期中成績_語文資料內容>
					{{/foreach}}
				</期中成績_語文>
				<期中成績_數學>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.math item=math_data key=math_id}}
					<期中成績_數學資料內容>
						<期中成績_數學領域段考別>{{$math_id}}</期中成績_數學領域段考別>
						<期中成績_數學領域百分制成績>{{$math_data.area_score.average}}</期中成績_數學領域百分制成績>
					</期中成績_數學資料內容>
					{{/foreach}}
				</期中成績_數學>
				<期中成績_自然與生活科技>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.nature item=nature_data key=nature_id}}
					<期中成績_自然與生活科技資料內容>
						<期中成績_自然與生活科技領域段考別>{{$nature_id}}</期中成績_自然與生活科技領域段考別>
						<期中成績_自然與生活科技領域百分制成績>{{$nature_data.area_score.average}}</期中成績_自然與生活科技領域百分制成績>
					</期中成績_自然與生活科技資料內容>
					{{/foreach}}
				</期中成績_自然與生活科技>
				<期中成績_社會>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.social item=social_data key=social_id}}
					<期中成績_社會資料內容>
						<期中成績_社會領域段考別>{{$social_id}}</期中成績_社會領域段考別>
						<期中成績_社會領域百分制成績>{{$social_data.area_score.average}}</期中成績_社會領域百分制成績>
					</期中成績_社會資料內容>
					{{/foreach}}
				</期中成績_社會>
				<期中成績_健康與體育>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.health item=health_data key=health_id}}
					<期中成績_健康與體育資料內容>
						<期中成績_健康與體育領域段考別>{{$health_id}}</期中成績_健康與體育領域段考別>
						<期中成績_健康與體育領域百分制成績>{{$health_data.area_score.average}}</期中成績_健康與體育領域百分制成績>
					</期中成績_健康與體育資料內容>
					{{/foreach}}
				</期中成績_健康與體育>
				<期中成績_藝術與人文>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.art item=art_data key=art_id}}
					<期中成績_藝術與人文資料內容>
						<期中成績_藝術與人文領域段考別>{{$art_id}}</期中成績_藝術與人文領域段考別>
						<期中成績_藝術與人文領域百分制成績>{{$art_data.area_score.average}}</期中成績_藝術與人文領域百分制成績>
					</期中成績_藝術與人文資料內容>
					{{/foreach}}
				</期中成績_藝術與人文>
				<期中成績_生活課程>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.life item=life_data key=life_id}}
					<期中成績_生活課程資料內容>
						<期中成績_生活課程領域段考別>{{$life_id}}</期中成績_生活課程領域段考別>
						<期中成績_生活課程領域百分制成績>{{$life_data.area_score.average}}</期中成績_生活課程領域百分制成績>
					</期中成績_生活課程資料內容>
					{{/foreach}}
				</期中成績_生活課程>
				<期中成績_綜合活動>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.complex item=complex_data key=complex_id}}
					<期中成績_綜合活動資料內容>
						<期中成績_綜合活動領域段考別>{{$complex_id}}</期中成績_綜合活動領域段考別>
						<期中成績_綜合活動領域百分制成績>{{$complex_data.area_score.average}}</期中成績_綜合活動領域百分制成績>
					</期中成績_綜合活動資料內容>
					{{/foreach}}
				</期中成績_綜合活動>
				<期中成績_彈性分項科目>
					{{foreach from=$data_arr[$arr_key].this_semester_score.$semester_key.elasticity item=elasticity_data key=elasticity_id}}
					<期中成績_彈性時數>
						<期中成績_彈性時數科目名稱>{{$elasticity_data.subject_name}}</期中成績_彈性時數科目名稱>
						<期中成績_彈性時數科目百分制成績>{{$elasticity_data.score}}</期中成績_彈性時數科目百分制成績>
					</期中成績_彈性時數>
					{{/foreach}}
				</期中成績_彈性分項科目>
			</期中成績>
			<期中獎懲>
				{{foreach from=$data_arr[$arr_key].reward.$semester_key item=reward_data key=reward_id}}
				<期中獎懲紀錄>
					<期中獎懲_日期>{{$reward_data.reward_date}}</期中獎懲_日期>
					<期中獎懲_類別>{{$reward_data.kind}}</期中獎懲_類別>
					<期中獎懲_次數>{{$reward_data.amount}}</期中獎懲_次數>
					<期中獎懲_事由>{{$reward_data.reward_reason}}</期中獎懲_事由>
				</期中獎懲紀錄>
				{{/foreach}}
			</期中獎懲>
			<社團活動>
				<社團活動內容>
					<社團名稱>null</社團名稱>
					<社團活動成績>null</社團活動成績>
				</社團活動內容>
			</社團活動>
		</期中資料>
		{{if $smarty.post.career}}
		<生涯輔導紀錄>
			<我的成長故事>
				<自我認識>
					{{assign var=career_self value=$data_arr[$arr_key].career.self}}
					{{foreach from=$career_self item=grade_data key=grade}}
					<自我認識_資料內容>
						<年級>{{$grade}}</年級>
						<個性>
							{{foreach from=$grade_data.personality item=item_data key=item_id}}
							<個性_資料內容>
								<項目>{{$item_data}}</項目>
							</個性_資料內容>
							{{/foreach}}
						</個性>
						<休閒興趣>
							{{foreach from=$grade_data.interest item=item_data key=item_id}}
							<休閒興趣_資料內容>
								<項目>{{$item_data}}</項目>
							</休閒興趣_資料內容>
							{{/foreach}}
						</休閒興趣>
						<專長>
							{{foreach from=$grade_data.specialty item=item_data key=item_id}}
							<專長_資料內容>
								<項目>{{$item_data}}</項目>
							</專長_資料內容>
							{{/foreach}}
						</專長>
					</自我認識_資料內容>
					{{/foreach}}
				</自我認識>
				<職業與我>
					{{assign var=career_job value=$data_arr[$arr_key].career.job}}
					{{foreach from=$career_job item=grade_data key=grade}}
					<職業與我_資料內容>
						<年級>{{$grade}}</年級>
						<建議未來可選擇的職業>{{$career_job.$grade.suggestion.1}}</建議未來可選擇的職業>
						<給建議的人>{{$career_job.$grade.suggestion.2}}</給建議的人>
						<建議選擇這項職業的原因>{{$career_job.$grade.suggestion.3}}</建議選擇這項職業的原因>
						<我最感興趣的職業>{{$career_job.$grade.myown.1}}</我最感興趣的職業>
						<我對這職業感興趣的原因>{{$career_job.$grade.myown.2}}</我對這職業感興趣的原因>
						<這項職業需具備的學歷能力專長或其他條件>{{$career_job.$grade.myown.3}}</這項職業需具備的學歷能力專長或其他條件>
						<我想要進一步了解的職業>{{$career_job.$grade.others.1}}</我想要進一步了解的職業>
						<選擇職業時重視的條件>
							{{assign var=weights value=$grade_data.weight}}
							{{foreach from=$weights item=item_data key=item_id}}
							<選擇職業時重視的條件_資料內容>
								<重視的條件>{{$item_data}}</重視的條件>
							</選擇職業時重視的條件_資料內容>
							{{/foreach}}
						</選擇職業時重視的條件>
					</職業與我_資料內容>
					{{/foreach}}
				</職業與我>
			</我的成長故事>
			<各項心理測驗>
				{{assign var=career_psy value=$data_arr[$arr_key].career.psy}}
				{{foreach from=$career_psy item=psy_data key=sn}}
				<各項心理測驗_資料內容>
					<測驗類別>{{$psy_data.id}}</測驗類別>
					<測驗名稱>{{$psy_data.title}}</測驗名稱>
					<測驗結果>
						{{foreach from=$psy_data.item item=item_data key=item_key}}
						<測驗結果_資料內容>
							<分測驗項目>{{$item_key}}</分測驗項目>
							<百分等級或標準分數>{{$item_data}}</百分等級或標準分數>
						</測驗結果_資料內容>
						{{/foreach}}
						<適合就讀的學校類別和科別>{{$psy_data.study}}</適合就讀的學校類別和科別>
						<適合從事的工作類別>{{$psy_data.job}}</適合從事的工作類別>
					</測驗結果>
				</各項心理測驗_資料內容>
				{{/foreach}}
			</各項心理測驗>

			<學習成果及特殊表現>
				<我的學習表現>
					<領域學習成績>
						{{assign var=semester_arr value=$data_arr[$arr_key].semester}}
						{{foreach from=$semester_arr item=semester key=semester_key}}
						{{assign var=study_year value=$semester_arr[$semester_key].study_year}}
						{{assign var=study_seme value=$semester_arr[$semester_key].semester}}
						<領域學習成績_資料內容>
							<年級>{{$study_year}}</年級>
							<學期>{{$study_seme}}</學期>
							<語文領域_國文>{{$data_arr[$arr_key].semester_score.chinese.$semester_key.score}}</語文領域_國文>
							<語文領域_英語>{{$data_arr[$arr_key].semester_score.english.$semester_key.score}}</語文領域_英語>
							<數學領域>{{$data_arr[$arr_key].semester_score.math.$semester_key.score}}</數學領域>
							<社會領域>{{$data_arr[$arr_key].semester_score.social.$semester_key.score}}</社會領域>
							<自然與生活科技領域>{{$data_arr[$arr_key].semester_score.nature.$semester_key.score}}</自然與生活科技領域>
							<藝術與人文領域>{{$data_arr[$arr_key].semester_score.art.$semester_key.score}}</藝術與人文領域>
							<健康與體育領域>{{$data_arr[$arr_key].semester_score.health.$semester_key.score}}</健康與體育領域>
							<綜合活動領域>{{$data_arr[$arr_key].semester_score.complex.$semester_key.score}}</綜合活動領域>
							<自我省思>{{$data_arr[$arr_key].career.ponder.3.1.$study_year.$study_seme}}</自我省思>
						</領域學習成績_資料內容>
						{{/foreach}}
					</領域學習成績>
					<教育會考表現>
						<國文>{{$data_arr[$arr_key].career.exam.c}}</國文>
						<數學>{{$data_arr[$arr_key].career.exam.m}}</數學>
						<英語>{{$data_arr[$arr_key].career.exam.e}}</英語>
						<社會>{{$data_arr[$arr_key].career.exam.s}}</社會>
						<自然>{{$data_arr[$arr_key].career.exam.n}}</自然>
						<寫作測驗>{{$data_arr[$arr_key].career.exam.w}}</寫作測驗>
					</教育會考表現>
					<體適能檢測表現>
						{{assign var=career_fitness value=$data_arr[$arr_key].career.fitness}}
						{{foreach from=$career_fitness item=fitness_data key=id}}
						<體適能檢測表現_資料內容>
							<檢測單位>{{$fitness_data.organization}}</檢測單位>
							<檢測日期>{{$fitness_data.test_y}}-{{$fitness_data.test_m}}-01</檢測日期>
							<檢測時年齡>{{$fitness_data.age}}</檢測時年齡>
							<身高>{{$fitness_data.tall}}</身高>
							<體重>{{$fitness_data.weigh}}</體重>
							<身體質量指數>{{$fitness_data.bmt}}</身體質量指數>
							<坐姿體前彎>
								<檢測成績>{{$fitness_data.test1}}</檢測成績>
								<百分等級>{{$fitness_data.prec1}}</百分等級>
							</坐姿體前彎>
							<立定跳遠>
								<檢測成績>{{$fitness_data.test3}}</檢測成績>
								<百分等級>{{$fitness_data.prec3}}</百分等級>
							</立定跳遠>
							<仰臥起坐>
								<檢測成績>{{$fitness_data.test2}}</檢測成績>
								<百分等級>{{$fitness_data.prec2}}</百分等級>
							</仰臥起坐>
							<心肺適能>
								<檢測成績>{{$fitness_data.test4}}</檢測成績>
								<百分等級>{{$fitness_data.prec4}}</百分等級>
							</心肺適能>
						</體適能檢測表現_資料內容>
						{{/foreach}}
					</體適能檢測表現>
				</我的學習表現>
				<我的經歷>
					<幹部>
						{{assign var=semester_arr value=$data_arr[$arr_key].semester}}
						{{foreach from=$semester_arr item=semester key=semester_key}}
						{{assign var=study_year value=$semester_arr[$semester_key].study_year}}
						{{assign var=study_seme value=$semester_arr[$semester_key].semester}}
						<幹部_資料內容>
							<年級>{{$study_year}}</年級>
							<學期>{{$study_seme}}</學期>
							<幹部名稱>
								{{assign var=cadre_arr value=$data_arr[$arr_key].career.ponder.3.2.$study_year.$study_seme.1}}
								{{foreach from=$cadre_arr item=cadre key=cadre_key}}
								<幹部名稱_資料內容>
									<名稱>{{$cadre}}</名稱>
								</幹部名稱_資料內容>
								{{/foreach}}
							</幹部名稱>
							<小老師>
								{{assign var=lt_arr value=$data_arr[$arr_key].career.ponder.3.2.$study_year.$study_seme.2}}
								{{foreach from=$lt_arr item=lt key=lt_key}}
								<小老師_資料內容>
									<名稱>{{$lt}}</名稱>
								</小老師_資料內容>
								{{/foreach}}
							</小老師>
							<自我省思>{{$data_arr[$arr_key].career.ponder.3.2.$study_year.$study_seme.data}}</自我省思>
						</幹部_資料內容>
						{{/foreach}}
					</幹部>
					<社團>
						{{assign var=association_arr value=$data_arr[$arr_key].career.association}}
						{{foreach from=$association_arr item=association key=sn}}
						<社團_資料內容>
							<年級>{{$association.grade}}</年級>
							<學期>{{$association.semester}}</學期>
							<社團名稱>{{$association.association_name}}</社團名稱>
							<擔任職務>{{$association.stud_post}}</擔任職務>
							<自我省思>{{$association.stud_feedback}}</自我省思>
						</社團_資料內容>
						{{/foreach}}
					</社團>
				</我的經歷>
				<參與各項競賽成果>
					{{assign var=race_arr value=$data_arr[$arr_key].career.race}}
					{{foreach from=$race_arr item=race key=sn}}
					<參與各項競賽成果_資料內容>
						<範圍>{{$race.level}}</範圍>
						<性質>{{$race.squad}}</性質>
						<競賽名稱>{{$race.name}}</競賽名稱>
						<得獎名次>{{$race.rank}}</得獎名次>
						<證書日期>{{$race.certificate_date}}</證書日期>
						<主辦單位>{{$race.sponsor}}</主辦單位>
						<備註>{{$race.memo}} </備註>
					</參與各項競賽成果_資料內容>
					{{/foreach}}
				</參與各項競賽成果>
				<行為表現獎懲紀錄>
					{{assign var=semester_arr value=$data_arr[$arr_key].semester}}
					{{foreach from=$semester_arr item=semester key=semester_key}}
					{{assign var=study_year value=$semester_arr[$semester_key].study_year}}
					{{assign var=study_seme value=$semester_arr[$semester_key].semester}}
					<行為表現獎懲紀錄_資料內容>
						<年級>{{$study_year}}</年級>
						<學期>{{$study_seme}}</學期>
						<獎懲紀錄>
							<獎勵>
								<嘉獎>{{$data_arr[$arr_key].career.reward_effective.$study_year.$study_seme.1}}</嘉獎>
								<小功>{{$data_arr[$arr_key].career.reward_effective.$study_year.$study_seme.3}}</小功>
								<大功>{{$data_arr[$arr_key].career.reward_effective.$study_year.$study_seme.9}}</大功>
							</獎勵>
							<懲處>
								<警告>{{$data_arr[$arr_key].career.reward_effective.$study_year.$study_seme.a}}</警告>
								<小過>{{$data_arr[$arr_key].career.reward_effective.$study_year.$study_seme.b}}</小過>
								<大過>{{$data_arr[$arr_key].career.reward_effective.$study_year.$study_seme.c}}</大過>
							</懲處>
						</獎懲紀錄>
						<銷過紀錄>
							<警告>{{$data_arr[$arr_key].career.reward_canceled.$study_year.$study_seme.a}}</警告>
							<小過>{{$data_arr[$arr_key].career.reward_canceled.$study_year.$study_seme.b}}</小過>
							<大過>{{$data_arr[$arr_key].career.reward_canceled.$study_year.$study_seme.c}}</大過>
						</銷過紀錄>
						<自我省思>{{$data_arr[$arr_key].career.ponder.3.4.$study_year.$study_seme}}</自我省思>
					</行為表現獎懲紀錄_資料內容>
					{{/foreach}}
				</行為表現獎懲紀錄>
				<服務學習紀錄>
					{{assign var=service_arr value=$data_arr[$arr_key].career.service}}
					{{foreach from=$service_arr item=service key=sn}}
					<服務學習紀錄_資料內容>
						<年級>{{$service.grade}}</年級>
						<學期>{{$service.semester}}</學期>
						<服務日期>{{$service.service_date}}</服務日期>
						<服務項目>{{$service.item}}</服務項目>
						<服務內容>{{$service.memo}} </服務內容>
						<時數>{{$service.hours}}</時數>
						<主辦單位>{{$service.sponsor}}</主辦單位>
						<自我省思>{{$service.feedback}} </自我省思>
					</服務學習紀錄_資料內容>
					{{/foreach}}
				</服務學習紀錄>
				<生涯試探活動紀錄>
					{{assign var=explore_arr value=$data_arr[$arr_key].career.explore}}
					{{foreach from=$explore_arr item=explore key=sn}}
					<生涯試探活動紀錄_資料內容>
						<年級>{{$explore.grade}}</年級>
						<學期>{{$explore.semester}}</學期>
						<試探學程及群科>{{$explore.course}}</試探學程及群科>
						<活動方式>{{$explore.activity}}</活動方式>
						<興趣的程度>{{$explore.degree}}</興趣的程度>
						<自我省思>{{$explore.ponder}} </自我省思>
					</生涯試探活動紀錄_資料內容>
					{{/foreach}}
				</生涯試探活動紀錄>
			</學習成果及特殊表現>
			<生涯統整面面觀>
				<生涯思索>
					{{assign var=think_arr value=$data_arr[$arr_key].career.think}}
					{{foreach from=$think_arr item=think key=id}}
					<生涯思索_資料內容>
						<項目>{{$id}}</項目>
						<內容>{{$think}}</內容>
					</生涯思索_資料內容>
					{{/foreach}}
				</生涯思索>
				<生涯方向>
					<選擇方向>
						{{assign var=direction_arr value=$data_arr[$arr_key].career.direction}}
						{{foreach from=$direction_arr.item item=direction_item key=id}}
						<選擇方向_資料內容>
							<項次>{{$id}}</項次>
							<自己的想法>{{$direction_item.self}}</自己的想法>
							<家長的期望>{{$direction_item.parent}}</家長的期望>
							<學校教師的建議>{{$direction_item.teacher}}</學校教師的建議>
							<備註>{{$direction_item.memo}} </備註>
						</選擇方向_資料內容>
						{{/foreach}}
					</選擇方向>
					<想法和家長期望或老師建議一致>{{if $direction_arr.identical}}是{{else}}否{{/if}}</想法和家長期望或老師建議一致>
					<是否一致原因>{{$direction_arr.reason}}</是否一致原因>
					<期望不同如何溝通>{{$direction_arr.communicate}}</期望不同如何溝通>
				</生涯方向>
				<想升讀的學程或科別>
					{{assign var=aspiration_arr value=$data_arr[$arr_key].career.aspiration}}
					{{foreach from=$aspiration_arr item=aspiration_item key=order}}
					<想升讀的學程或科別_資料內容>
						<學校校名>{{$aspiration_item.school}}</學校校名>
						<學程或科別>{{$aspiration_item.course}}</學程或科別>
						<地理位置>{{$aspiration_item.position}}</地理位置>
						<交通方式>{{$aspiration_item.transportation}}</交通方式>
						<往返時間>{{$aspiration_item.transportation_time}}</往返時間>
						<往返車資>{{$aspiration_item.transportation_toll}}</往返車資>
						<備註>{{$aspiration_item.memo}} </備註>
					</想升讀的學程或科別_資料內容>
					{{/foreach}}
				</想升讀的學程或科別>
			</生涯統整面面觀>
			<生涯發展規劃書>
				<生涯評核表>
					{{foreach from=$aspiration_arr item=aspiration_item key=order}}
					{{if $aspiration_item.factor}}
					<生涯評核表_資料內容>
						<校名>{{$aspiration_item.school}}</校名>
						<科別>{{$aspiration_item.course}}</科別>
						<個人因素>
							{{foreach from=$factors.self item=item_data key=item_key}}
							<個人因素_資料內容>
								<考慮因素>{{$item_data}}</考慮因素>
								<符合程度分數>{{$aspiration_item.factor.self.$item_key}}</符合程度分數>
							</個人因素_資料內容>
							{{/foreach}}
						</個人因素>
						<環境因素>
							{{foreach from=$factors.env item=item_data key=item_key}}
							<環境因素_資料內容>
								<考慮因素>{{$item_data}}</考慮因素>
								<符合程度分數>{{$aspiration_item.factor.env.$item_key}}</符合程度分數>
							</環境因素_資料內容>
							{{/foreach}}
						</環境因素>
						<資訊因素>
							{{foreach from=$factors.info item=item_data key=item_key}}
							<資訊因素_資料內容>
								<考慮因素>{{$item_data}}</考慮因素>
								<符合程度分數>{{$aspiration_item.factor.info.$item_key}}</符合程度分數>
							</資訊因素_資料內容>
							{{/foreach}}
						</資訊因素>
					</生涯評核表_資料內容>
					{{/if}}
					{{/foreach}}
				</生涯評核表>
				<相關心理測驗結果>
					{{assign var=test_arr value=$data_arr[$arr_key].career.test}}
					<性向測驗分數最高的分測驗>
						{{foreach from=$test_arr.1 item=test_item key=item_key}}
						<性向測驗分數最高的分測驗_資料內容>
							<次序>{{math equation="x+1" x=$item_key assign="item_key"}}{{$item_key}}</次序>
							<分測驗>{{$test_item}}</分測驗>
						</性向測驗分數最高的分測驗_資料內容>
						{{/foreach}}
					</性向測驗分數最高的分測驗>
					<興趣測驗分數最高的分測驗>
						{{foreach from=$test_arr.2 item=test_item key=item_key}}
						<興趣測驗分數最高的分測驗_資料內容>
							<次序>{{math equation="x+1" x=$item_key assign="item_key"}}{{$item_key}}</次序>
							<分測驗>{{$test_item}}</分測驗>
						</興趣測驗分數最高的分測驗_資料內容>
						{{/foreach}}
					</興趣測驗分數最高的分測驗>
				</相關心理測驗結果>
				<學習表現>
					<國文>{{$data_arr[$arr_key].semester_score.chinese.avg.score}}</國文>
					<英語>{{$data_arr[$arr_key].semester_score.english.avg.score}}</英語>
					<數學>{{$data_arr[$arr_key].semester_score.math.avg.score}}</數學>
					<社會>{{$data_arr[$arr_key].semester_score.social.avg.score}}</社會>
					<自然>{{$data_arr[$arr_key].semester_score.nature.avg.score}}</自然>
					<藝術與人文>{{$data_arr[$arr_key].semester_score.art.avg.score}}</藝術與人文>
					<健康與體育>{{$data_arr[$arr_key].semester_score.health.avg.score}}</健康與體育>
					<綜合活動>{{$data_arr[$arr_key].semester_score.complex.avg.score}}</綜合活動>
				</學習表現>
				<想升讀的學校>
					{{assign var=school_arr value=$data_arr[$arr_key].career.school}}
					{{foreach from=$school_arr item=school_item key=item_key}}
					<想升讀的學校_資料內容>
						<志願序>{{$item_key}}</志願序>
						<校名>{{$school_item}}</校名>
					</想升讀的學校_資料內容>
					{{/foreach}}
				</想升讀的學校>
				<師長綜合意見>
					<家長>
						{{assign var=opinion_arr value=$data_arr[$arr_key].career.opinion.parent}}
						{{foreach from=$opinion_arr item=opinion_item key=item_key}}
						<家長希望_資料內容>
							<選擇>{{$opinion_item}}</選擇>
						</家長希望_資料內容>
						{{/foreach}}
						<家長意見說明>{{$data_arr[$arr_key].career.opinion.parent.memo}} </家長意見說明>
					</家長>
					<導師>
						{{assign var=opinion_arr value=$data_arr[$arr_key].career.opinion.tutor}}
						{{foreach from=$opinion_arr item=opinion_item key=item_key}}
						<導師建議_資料內容>
							<建議>{{$opinion_item}} </建議>
						</導師建議_資料內容>
						{{/foreach}}
						<導師建議說明>{{$data_arr[$arr_key].career.opinion.tutor.memo}} </導師建議說明>
					</導師>
					<輔導教師>
						{{assign var=opinion_arr value=$data_arr[$arr_key].career.opinion.guidance}}
						{{foreach from=$opinion_arr item=opinion_item key=item_key}}
						<輔導教師建議_資料內容>
							<建議>{{$opinion_item}}</建議>
						</輔導教師建議_資料內容>
						{{/foreach}}
						<輔導教師建議說明>{{$data_arr[$arr_key].career.opinion.guidance.memo}} </輔導教師建議說明>
					</輔導教師>
				</師長綜合意見>
			</生涯發展規劃書>
			<其他生涯輔導紀錄>
				<生涯輔導紀錄>
					{{assign var=item_arr value=$data_arr[$arr_key].career.guidance}}
					{{foreach from=$item_arr item=item key=key}}
					<生涯輔導紀錄_資料內容>
						<日期>{{$item.guidance_date}}</日期>
						<對象>{{$item.target}}</對象>
						<輔導重點或建議>{{$item.emphasis}} </輔導重點或建議>
						<輔導教師>{{$item.teacher_name}}</輔導教師>
					</生涯輔導紀錄_資料內容>
					{{/foreach}}
				</生涯輔導紀錄>
				<生涯諮詢紀錄>
					{{assign var=item_arr value=$data_arr[$arr_key].career.consultation}}
					{{foreach from=$item_arr item=item key=key}}
					<生涯諮詢紀錄_資料內容>
						<年級>{{$item.grade}}</年級>
						<學期>{{$item.semester}}</學期>
						<諮詢的師長>{{$item.teacher_name}}</諮詢的師長>
						<討論重點及意見>{{$item.emphasis}}</討論重點及意見>
						<備註>{{$item.memo}}</備註>
					</生涯諮詢紀錄_資料內容>
					{{/foreach}}
				</生涯諮詢紀錄>
				<家長的話>
					{{assign var=item_arr value=$data_arr[$arr_key].career.parent}}
					{{foreach from=$item_arr item=item key=key}}
					<家長的話_資料內容>
						<年級>{{$item.grade}}</年級>
						<參閱資料>
							{{assign var=item_arr value=$item.consult}}
							{{foreach from=$consult_arr item=consult_item key=consult_key}}
							<參閱資料_資料內容>
								<項目>{{$consult_item}}</項目>
							</參閱資料_資料內容>
							{{/foreach}}
						</參閱資料>
						<給孩子的鼓勵及建議>{{$item.suggestion}} </給孩子的鼓勵及建議>
						<給孩子的鼓勵及建議日期>{{$item.suggestion_date}}</給孩子的鼓勵及建議日期>
						<親師溝通>{{$item.tutor_name}} </親師溝通>
						<親師溝通日期>{{$item.tutor_confirm}}</親師溝通日期>
					</家長的話_資料內容>
					{{/foreach}}
				</家長的話>
			</其他生涯輔導紀錄>
			<學校相關處室審閱紀錄>
				{{foreach from=$semester_arr item=semester key=semester_key}}
				{{assign var=study_year value=$semester_arr[$semester_key].study_year}}
				{{assign var=study_seme value=$semester_arr[$semester_key].semester}}
				<學校相關處室審閱紀錄_資料內容>
					<年級>{{$study_year}}</年級>
					<學期>{{$study_seme}}</學期>
					<審閱人員>{{$data_arr[$arr_key].career.ponder.9.9.$study_year.$study_seme.teacher}}</審閱人員>
					<審閱日期>{{$data_arr[$arr_key].career.ponder.9.9.$study_year.$study_seme.date}}</審閱日期>
					<備註>{{$data_arr[$arr_key].career.ponder.9.9.$study_year.$study_seme.memo}} </備註>
				</學校相關處室審閱紀錄_資料內容>
				{{/foreach}}
			</學校相關處室審閱紀錄>
		</生涯輔導紀錄>
		{{/if}}
	</學生資料>
{{/foreach}}
</學籍交換資料>
