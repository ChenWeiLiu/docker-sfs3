<學籍交換資料>
{{foreach from=$data_arr item=content key=arr_key}}
	{{ if $content.stud_name!='' }}
	<學生資料>
		<學生基本資料>
			<身分證證照>
				<國籍>{{$data_arr[$arr_key].stud_country}}</國籍>
				{{assign var=id_kind value=$data_arr[$arr_key].stud_country_kind}}
				<證照種類>{{$id_kind_arr[$id_kind]}}</證照種類>
				<證照號碼>{{$data_arr[$arr_key].stud_person_id}}</證照號碼>
				<僑居地>{{$data_arr[$arr_key].stud_country_name}}</僑居地>
			</身分證證照>
			<學校代碼>
				<現在學校代碼>{{$school_edu_id}}</現在學校代碼>
			</學校代碼>
			<基本資料>
				<學生姓名>{{$data_arr[$arr_key].stud_name}} </學生姓名>
	{{assign var=stud_sex value=$data_arr[$arr_key].stud_sex}}
				<學生性別>{{$sex_arr[$stud_sex]}}</學生性別>
				<學生生日>{{$data_arr[$arr_key].stud_birthday}}</學生生日>
				<現在年級>{{$data_arr[$arr_key].year_num}}</現在年級>
				<現在班級>{{$data_arr[$arr_key].class_num}}</現在班級>
				<現在座號>{{$data_arr[$arr_key].site_num}}</現在座號>
			</基本資料>
			<學生身份註記>
	{{assign var=stud_kind value=$data_arr[$arr_key].stud_kind}}
	{{foreach from=$stud_kind item=sk_arr key=sk_key}}
				<學生身份註記_資料內容>
					<學生身份註記_類別>{{$stud_kind_arr[$sk_key]}} </學生身份註記_類別>
					<學生身份註記_備註>null</學生身份註記_備註>
				</學生身份註記_資料內容>
	{{/foreach}}
			</學生身份註記>
			<連絡資料>
				<戶籍地址>
					<戶籍地址_縣市名>{{$data_arr[$arr_key].stud_addr_1.0}} </戶籍地址_縣市名>
					<戶籍地址_鄉鎮市區名>{{$data_arr[$arr_key].stud_addr_1.1}} </戶籍地址_鄉鎮市區名>
					<戶籍地址_村里>{{$data_arr[$arr_key].stud_addr_1.2}} </戶籍地址_村里>
					<戶籍地址_鄰>{{$data_arr[$arr_key].stud_addr_1.3}} </戶籍地址_鄰>
					<戶籍地址_路街>{{$data_arr[$arr_key].stud_addr_1.4}} </戶籍地址_路街>
					<戶籍地址_段>{{$data_arr[$arr_key].stud_addr_1.5}} </戶籍地址_段>
					<戶籍地址_巷>{{$data_arr[$arr_key].stud_addr_1.6}} </戶籍地址_巷>
					<戶籍地址_弄>{{$data_arr[$arr_key].stud_addr_1.7}} </戶籍地址_弄>
					<戶籍地址_號>{{$data_arr[$arr_key].stud_addr_1.8}} </戶籍地址_號>
					<戶籍地址_之>{{$data_arr[$arr_key].stud_addr_1.9}} </戶籍地址_之>
					<戶籍地址_樓>{{$data_arr[$arr_key].stud_addr_1.10}} </戶籍地址_樓>
					<戶籍地址_樓之>{{$data_arr[$arr_key].stud_addr_1.11}} </戶籍地址_樓之>
					<戶籍地址_其他>{{$data_arr[$arr_key].stud_addr_1.12}} </戶籍地址_其他>
				</戶籍地址>
				<通訊地址>
					<通訊地址_縣市名>{{$data_arr[$arr_key].stud_addr_2.0}} </通訊地址_縣市名>
					<通訊地址_鄉鎮市區名>{{$data_arr[$arr_key].stud_addr_2.1}} </通訊地址_鄉鎮市區名>
					<通訊地址_村里>{{$data_arr[$arr_key].stud_addr_2.2}} </通訊地址_村里>
					<通訊地址_鄰>{{$data_arr[$arr_key].stud_addr_2.3}} </通訊地址_鄰>
					<通訊地址_路街>{{$data_arr[$arr_key].stud_addr_2.4}} </通訊地址_路街>
					<通訊地址_段>{{$data_arr[$arr_key].stud_addr_2.5}} </通訊地址_段>
					<通訊地址_巷>{{$data_arr[$arr_key].stud_addr_2.6}} </通訊地址_巷>
					<通訊地址_弄>{{$data_arr[$arr_key].stud_addr_2.7}} </通訊地址_弄>
					<通訊地址_號>{{$data_arr[$arr_key].stud_addr_2.8}} </通訊地址_號>
					<通訊地址_之>{{$data_arr[$arr_key].stud_addr_2.9}} </通訊地址_之>
					<通訊地址_樓>{{$data_arr[$arr_key].stud_addr_2.10}} </通訊地址_樓>
					<通訊地址_樓之>{{$data_arr[$arr_key].stud_addr_2.11}} </通訊地址_樓之>
					<通訊地址_其他>{{$data_arr[$arr_key].stud_addr_2.12}} </通訊地址_其他>
				</通訊地址>
				<通訊電話>{{$data_arr[$arr_key].stud_tel_2}}</通訊電話>
				<行動電話>{{$data_arr[$arr_key].stud_tel_3}}</行動電話>
			</連絡資料>
			<原住民>
				<原住民_居住地>{{$data_arr[$arr_key].yuanzhumin.area}} </原住民_居住地>
				<原住民_族別>{{$data_arr[$arr_key].yuanzhumin.clan}} </原住民_族別>
			</原住民>
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
					<幼稚園_學校名稱>{{$data_arr[$arr_key].stud_preschool_name}} </幼稚園_學校名稱>
				</幼稚園入學>
				<國小入學>
	{{assign var=preschool_status value=$data_arr[$arr_key].stud_Mschool_status}}
					<國小入學資格>{{$preschool_status_arr[$preschool_status]}}</國小入學資格>
					<國小_教育部學校代碼>{{$data_arr[$arr_key].stud_mschool_id}}</國小_教育部學校代碼>
					<國小_學校名稱>{{$data_arr[$arr_key].stud_mschool_name}} </國小_學校名稱>
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
				<父親_姓名>{{$data_arr[$arr_key].fath_name}} </父親_姓名>
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
				<父親_職業>{{$data_arr[$arr_key].fath_occupation}} </父親_職業>
				<父親_服務單位>{{$data_arr[$arr_key].fath_unit}} </父親_服務單位>
				<父親_職稱>{{$data_arr[$arr_key].fath_work_name}} </父親_職稱>
				<父親_電話號碼-公>{{$data_arr[$arr_key].fath_phone}}</父親_電話號碼-公>
				<父親_電話號碼-宅>{{$data_arr[$arr_key].fath_home_phone}}</父親_電話號碼-宅>
				<父親_行動電話>{{$data_arr[$arr_key].fath_hand_phone}}</父親_行動電話>
				<父親_電子郵件信箱>{{$data_arr[$arr_key].fath_email}}</父親_電子郵件信箱>
			</父親基本資料>
			<母親基本資料>
				<母親_姓名>{{$data_arr[$arr_key].moth_name}} </母親_姓名>
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
				<母親_職業>{{$data_arr[$arr_key].moth_occupation}} </母親_職業>
				<母親_服務單位>{{$data_arr[$arr_key].moth_unit}} </母親_服務單位>
				<母親_職稱>{{$data_arr[$arr_key].moth_work_name}} </母親_職稱>
				<母親_電話號碼-公>{{$data_arr[$arr_key].moth_phone}}</母親_電話號碼-公>
				<母親_電話號碼-宅>{{$data_arr[$arr_key].moth_home_phone}}</母親_電話號碼-宅>
				<母親_行動電話>{{$data_arr[$arr_key].moth_hand_phone}}</母親_行動電話>
				<母親_電子郵件信箱>{{$data_arr[$arr_key].moth_email}}</母親_電子郵件信箱>
			</母親基本資料>
			<監護人>
				<監護人_姓名>{{$data_arr[$arr_key].guardian_name}}</監護人_姓名>
				{{assign var=g_rela value=$data_arr[$arr_key].guardian_relation}}
				<與監護人之關係>{{$g_rela_arr[$m_rela]}}</與監護人之關係>
				<監護人_身分證號>{{$data_arr[$arr_key].guardian_p_id}}</監護人_身分證號>
				<監護人_地址>{{$data_arr[$arr_key].guardian_address}}</監護人_地址>
				<監護人_服務單位>{{$data_arr[$arr_key].guardian_unit}} </監護人_服務單位>
				<監護人_職稱>{{$data_arr[$arr_key].grandmoth_name}}</監護人_職稱>
				<監護人_連絡電話>{{$data_arr[$arr_key].guardian_phone}}</監護人_連絡電話>
				<監護人_行動電話>{{$data_arr[$arr_key].guardian_hand_phone}}</監護人_行動電話>
				<監護人_電子郵件信箱>{{$data_arr[$arr_key].guardian_email}}</監護人_電子郵件信箱>
			</監護人>
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
			<兄弟姊妹>
	{{assign var=bs_arr value=$data_arr[$arr_key].bro_sis}}
	{{foreach from=$bs_arr item=bs key=bs_key}}
				<兄弟姊妹_資料內容>
	{{assign var=bs_calling value=$bs_arr[$bs_key].bs_calling}}
					<兄弟姊妹_稱謂>{{$bs_calling_kind_arr[$bs_calling]}}</兄弟姊妹_稱謂>
					<兄弟姊妹_姓名>{{$bs_arr[$bs_key].bs_name}} </兄弟姊妹_姓名>
				</兄弟姊妹_資料內容>
	{{/foreach}}
			</兄弟姊妹>
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
					<班級>{{$semester_arr[$semester_key].study_class}}</班級>
					<座號>{{$semester_arr[$semester_key].seme_num}}</座號>
				</班級座號>
				<學期成績>
					<導師姓名>{{$semester_arr[$semester_key].teacher}} </導師姓名>
					<語文_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.language.$semester_key.score}}</語文_學習領域百分制成績>
					<語文_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.chinese}};{{$data_arr[$arr_key].semester_score_memo.$semester_key.local}};{{$data_arr[$arr_key].semester_score_memo.$semester_key.english}} </語文_學習領域文字描述>
					<本國語文百分制成績>{{$data_arr[$arr_key].semester_score.chinese.$semester_key.score}}</本國語文百分制成績>
					<本土語文百分制成績>{{$data_arr[$arr_key].semester_score.local.$semester_key.score}}</本土語文百分制成績>
					<本土語言類別></本土語言類別>
					<英語百分制成績>{{$data_arr[$arr_key].semester_score.english.$semester_key.score}}</英語百分制成績>
					<數學_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.math.$semester_key.score}}</數學_學習領域百分制成績>
					<數學_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.math}}</數學_學習領域文字描述>
					<自然與生活科技_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.nature.$semester_key.score}}</自然與生活科技_學習領域百分制成績>
					<自然與生活科技_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.nature}} </自然與生活科技_學習領域文字描述>
					<社會_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.social.$semester_key.score}}</社會_學習領域百分制成績>
					<社會_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.social}} </社會_學習領域文字描述>
					<健康與體育_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.health.$semester_key.score}}</健康與體育_學習領域百分制成績>
					<健康與體育_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.health}} </健康與體育_學習領域文字描述>
					<藝術與人文_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.art.$semester_key.score}}</藝術與人文_學習領域百分制成績>
					<藝術與人文_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.art}} </藝術與人文_學習領域文字描述>
					<生活課程_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.life.$semester_key.score}}</生活課程_學習領域百分制成績>
					<生活課程_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.life}}</生活課程_學習領域文字描述>
					<綜合活動_學習領域百分制成績>{{$data_arr[$arr_key].semester_score.complex.$semester_key.score}}</綜合活動_學習領域百分制成績>
					<綜合活動_學習領域文字描述>{{$data_arr[$arr_key].semester_score_memo.$semester_key.complex}} </綜合活動_學習領域文字描述>
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
					<日常生活表現_文字描述>{{$data_arr[$arr_key].semester_score_nor.$semester_key.ss_score_memo}} </日常生活表現_文字描述>
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
						<優良表現_事由>{{$semester_spe_data.sp_memo}}</優良表現_事由>
					</優良表現事蹟>
					{{/foreach}}
				</特殊優良表現>
				<心理測驗>
					{{assign var=psy_test_arr value=$data_arr[$arr_key].psy_test.$semester_key}}
					{{foreach from=$psy_test_arr item=psy_test_data key=sn}}
					<心理測驗_資料內容>
						<心理測驗_名稱>{{$psy_test_data.item}} </心理測驗_名稱>
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
						<連絡事項>{{$talk_data.sst_main}} </連絡事項>
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
				<原就讀學校名稱>{{$move_data.school}} </原就讀學校名稱>
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
	</學生資料>
	{{/if}}
{{/foreach}}
</學籍交換資料>
