{{foreach from=$data_arr item=content key=arr_key}}
{{if $data_arr[$arr_key].year_num != null && $data_arr[$arr_key].year_num != ''}}
	<學生資料>
		<學生基本資料>
		{{assign var=id_kind value=$data_arr[$arr_key].stud_country_kind}}
		{{if $id_kind ==0}}
		  {{assign var=id_kind_local value=$id_kind_arr[$id_kind]}}
		  {{assign var=id_num_local value=$data_arr[$arr_key].stud_person_id}}
		{{else}} 
		  {{assign var=id_kind_foreign value=$id_kind_arr[$id_kind]}}
		  {{assign var=id_num_foreign value=$data_arr[$arr_key].stud_person_id}}		
		{{/if}}  
		    <身分證證照>			
				<證照種類>身分證</證照種類>
				<證照號碼>{{$id_num_local}}</證照號碼>
			</身分證證照>
			<外國證照> 
				<證照種類>{{$id_kind_foreign}}</證照種類>
				<證照號碼>{{$id_num_foreign}}</證照號碼>
			</外國證照>
			<學校代碼>
				<現在學校代碼>{{$sch_id}}</現在學校代碼>
			</學校代碼>
			<基本資料>
                 <現在年級>{{$data_arr[$arr_key].year_num}}</現在年級>
			     <現在班級序號>{{$data_arr[$arr_key].class_num}}</現在班級序號>
				 <現在班級名稱>{{$data_arr[$arr_key].year_num}}年{{$data_arr[$arr_key].class_num}}班</現在班級名稱>
			     <現在座號>{{$data_arr[$arr_key].site_num}}</現在座號>
			</基本資料>
			
            {{assign var=stud_kind value=$data_arr[$arr_key].stud_kind}}
            {{foreach from=$stud_kind item=sk_arr key=sk_key}}
			<學生身份別>
					<學生身份註記_類別>{{$stud_kind_arr[$sk_key]}}</學生身份註記_類別>
					<學生身份註記_備註></學生身份註記_備註>
			</學生身份別>
            {{/foreach}}
			<原住民>
				<原住民_居住地>{{$data_arr[$arr_key].yuanzhumin.area}}</原住民_居住地>
			</原住民>
			<學生班級性質>
            {{assign var=class_kind value=$data_arr[$arr_key].stud_class_kind}}
            {{assign var=stud_spe_kind value=$data_arr[$arr_key].stud_spe_kind}}
				<班級性質>{{$class_kind_arr[$class_kind]}}</班級性質>
				<特教班類別>{{$spe_kind_arr[$stud_spe_kind]}}</特教班類別>
			</學生班級性質>
			<入學>
			{{assign var=preschool_status value=$data_arr[$arr_key].stud_preschool_status}}
					<入學資格>{{$preschool_status_arr[$preschool_status]}}</入學資格>
			</入學>
			<畢修業核准文號>
            {{assign var=grad_kind value=$data_arr[arr_key].grad_kind}}
				<畢修業別>{{$grad_kind_arr[$grad_kind]}}</畢修業別>
				<畢修業_日期>{{$data_arr[$arr_key].grad_date}}</畢修業_日期>
			</畢修業核准文號>
		</學生基本資料>
		<親屬及監護人基本資料>
            <父親基本資料>
				<父親_出生年次>{{$data_arr[$arr_key].fath_birthyear}}</父親_出生年次>
			    <父親_原國籍></父親_原國籍>
				<父親_已入中華民國國籍></父親_已入中華民國國籍>
                {{assign var=f_is_live value=$data_arr[$arr_key].fath_alive}}
				<父親_存歿>{{$is_live_arr[$f_is_live]}}</父親_存歿>
                {{assign var=f_rela value=$data_arr[$arr_key].fath_relation}}
				<與父關係>{{$f_rela_arr[$f_rela]}}</與父關係>
                {{assign var=f_edu value=$data_arr[$arr_key].fath_education}}
				<父親_教育程度>{{$edu_kind_arr[$f_edu]}}</父親_教育程度>
                {{assign var=f_grad_kind value=$data_arr[$arr_key].fath_grad_kind}}
				<父親_職業>{{$data_arr[$arr_key].fath_occupation}}</父親_職業>
			</父親基本資料>
			<母親基本資料>
			    <母親_出生年次>{{$data_arr[$arr_key].moth_birthyear}}</母親_出生年次>
   				<母親_原國籍></母親_原國籍>
				<母親_已入中華民國國籍></母親_已入中華民國國籍>
                {{assign var=m_is_live value=$data_arr[$arr_key].moth_alive}}
				<母親_存歿>{{$is_live_arr[$m_is_live]}}</母親_存歿>
                {{assign var=m_rela value=$data_arr[$arr_key].moth_relation}}
				<與母關係>{{$m_rela_arr[$m_rela]}}</與母關係>
                {{assign var=m_edu value=$data_arr[$arr_key].moth_education}}
				<母親_教育程度>{{$edu_kind_arr[$m_edu]}}</母親_教育程度>
                {{assign var=m_grad_kind value=$data_arr[$arr_key].moth_grad_kind}}
				<母親_職業>{{$data_arr[$arr_key].moth_occupation}}</母親_職業>
		    </母親基本資料>
		    <監護人基本資料>
                {{assign var=g_rela value=$data_arr[$arr_key].guardian_relation}}
		        <與監護人之關係>{{$g_rela_arr[$m_rela]}}</與監護人之關係>
			    <監護人_地址></監護人_地址>
		    </監護人基本資料>
			<祖父母> 			
	            <祖父_姓名>{{$data_arr[$arr_key].grandfath_name}}</祖父_姓名>
				<祖父_通訊地址></祖父_通訊地址>
				<祖母_姓名>{{$data_arr[$arr_key].grandmoth_name}}</祖母_姓名>
				<祖母_通訊地址></祖母_通訊地址>
		    </祖父母>
		</親屬及監護人基本資料>
	    <國小學期資料>
        {{assign var=semester_arr value=$data_arr[$arr_key].semester}}
        {{foreach from=$semester_arr item=semester key=semester_key}}
			<個別學期資料>
			    <學期基本資料>
				  <學年別>{{$semester_arr[$semester_key].year}}</學年別>
				  <學期別>{{$semester_arr[$semester_key].semester}}</學期別>
                  {{assign var=study_year value=$semester_arr[$semester_key].study_year}}
				  <年級>{{$study_year}}</年級>
				  <班級>{{$semester_arr[$semester_key].seme_class_name}}</班級>
				  <座號>{{$semester_arr[$semester_key].seme_num}}</座號>
			    </學期基本資料>
				<國小學期成績資料>
					<語文_學習領域百分制成績></語文_學習領域百分制成績>
					<本國語文_學習領域百分制成績></本國語文_學習領域百分制成績>
					<國語文百分制成績></國語文百分制成績>
					<本土語文百分制成績></本土語文百分制成績>
					<本土語言類別></本土語言類別>
					<新住民語文百分制成績></新住民語文百分制成績>
					<外國語文_學習領域百分制成績></外國語文_學習領域百分制成績>
					<英語百分制成績></英語百分制成績>
					<數學_學習領域百分制成績></數學_學習領域百分制成績>
					<自然與生活科技_學習領域百分制成績></自然與生活科技_學習領域百分制成績>
					<自然科學_學習領域百分制成績></自然科學_學習領域百分制成績>
					<社會_學習領域百分制成績></社會_學習領域百分制成績>
					<健康與體育_學習領域百分制成績></健康與體育_學習領域百分制成績>
					<藝術與人文_學習領域百分制成績></藝術與人文_學習領域百分制成績>
					<生活課程_學習領域百分制成績></生活課程_學習領域百分制成績>
					<綜合活動_學習領域百分制成績></綜合活動_學習領域百分制成績>
					<科技_學習領域百分制成績></科技_學習領域百分制成績>
					<彈性時數>
						<彈性時數_資料內容>
							<彈性時數_科目名稱></彈性時數_科目名稱>
							<彈性時數_科目百分制成績></彈性時數_科目百分制成績>
						</彈性時數_資料內容>
					</彈性時數>
				</國小學期成績資料>
				<日常生活表現>
					<學期出缺席_應出席日數></學期出缺席_應出席日數>
					<學期出缺席_事假數></學期出缺席_事假數>
					<學期出缺席_病假數></學期出缺席_病假數>
					<學期出缺席_公假數></學期出缺席_公假數>
					<學期出缺席_喪假數></學期出缺席_喪假數>
					<學期出缺席_曠課數></學期出缺席_曠課數>
					<學期出缺席_其他假數></學期出缺席_其他假數>
					<學期出缺席_單位></學期出缺席_單位>
				</日常生活表現>
				<心理測驗>
					<心理測驗_資料內容>
					    <心理測驗類別代碼></心理測驗類別代碼>
						<心理測驗_名稱></心理測驗_名稱>
						<心理測驗_原始分數></心理測驗_原始分數>
						<心理測驗_常模樣本></心理測驗_常模樣本>
						<心理測驗_標準分數></心理測驗_標準分數>
						<心理測驗_百分等級></心理測驗_百分等級>
						<心理測驗_解釋></心理測驗_解釋>
					</心理測驗_資料內容>
				</心理測驗>
				<輔導資料>
					<父母關係></父母關係>
					<居住情形></居住情形>
					<經濟狀況></經濟狀況>
					<最喜愛學習領域>
						<最喜愛學習領域_資料內容></最喜愛學習領域_資料內容>
					</最喜愛學習領域>
					<最困難學習領域>
						<最困難學習領域_資料內容></最困難學習領域_資料內容>
					</最困難學習領域>
					<特殊才能>
						<特殊才能_資料內容></特殊才能_資料內容>
					</特殊才能>
					<興趣>
						<興趣_資料內容></興趣_資料內容>
					</興趣>
					<生活習慣>
						<生活習慣_資料內容></生活習慣_資料內容>
					</生活習慣>
					<人際關係>
						<人際關係_資料內容></人際關係_資料內容>
					</人際關係>
					<外向行為>
						<外向行為_資料內容></外向行為_資料內容>
					</外向行為>
					<內向行為>
						<內向行為_資料內容></內向行為_資料內容>
					</內向行為>
					<學習行為>
						<學習行為_資料內容></學習行為_資料內容>
					</學習行為>
					<不良習慣>
						<不良習慣_資料內容></不良習慣_資料內容>
					</不良習慣>
					<焦慮行為>
						<焦慮行為_資料內容></焦慮行為_資料內容>
					</焦慮行為>
				</輔導資料>
				<輔導訪談紀錄>
					<輔導訪談紀錄_資料內容>
						<紀錄日期></紀錄日期>
						<連絡對象></連絡對象>
						<連絡事項></連絡事項>
						<內容要點></內容要點>
					</輔導訪談紀錄_資料內容>
				</輔導訪談紀錄>
			</個別學期資料>
{{/foreach}}
		</國小學期資料>
		<異動資料>
			<異動基本資料>
				<原就讀縣市></原就讀縣市>
				<原就讀學校名稱></原就讀學校名稱>
				<原就讀學校代碼></原就讀學校代碼>
				<異動日期></異動日期>
				<異動原因></異動原因>
			</異動基本資料>
		</異動資料>	
	</學生資料>
	{{/if}}
{{/foreach}}