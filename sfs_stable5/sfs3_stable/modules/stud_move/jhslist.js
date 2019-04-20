
function disp_text()
{
    var w = document.myform.selectschool.selectedIndex;
    var i = document.myform.selectcity.selectedIndex;
    var selected_city = document.myform.selectcity.options[i].text;
    var selected_value = document.myform.selectschool.options[w].value;
    var selected_text = document.myform.selectschool.options[w].text;
    document.myform.city.value = selected_city;
    document.myform.school.value = selected_text;
    document.myform.school_id.value = selected_value;
    if (selected_city == '其他' && selected_text == '其他') {
        document.myform.city.readOnly = false;
        document.myform.school.readOnly = false;
        document.myform.school_id.readOnly = false;
    } else {
        document.myform.city.readOnly = true;
        document.myform.school.readOnly = true;
        document.myform.school_id.readOnly = true;
    }
}

function fillCity() {
    //document.myform.city.value = '';
    //document.myform.school.value = '';
    //document.myform.school_id.value = '';
    addOption(document.myform.selectcity, "其他", "其他", "");
    addOption(document.myform.selectcity, "外籍生", "外籍生", "");
    addOption(document.myform.selectcity, "港澳大陸生", "港澳大陸生", "");
    addOption(document.myform.selectcity, "出國", "出國", "");
    addOption(document.myform.selectcity, "新北市", "新北市", "");
    addOption(document.myform.selectcity, "臺北市", "臺北市", "");
    addOption(document.myform.selectcity, "臺中市", "臺中市", "");
    addOption(document.myform.selectcity, "臺南市", "臺南市", "");
    addOption(document.myform.selectcity, "高雄市", "高雄市", "");
    addOption(document.myform.selectcity, "宜蘭縣", "宜蘭縣", "");
    addOption(document.myform.selectcity, "桃園市", "桃園市", "");
    addOption(document.myform.selectcity, "新竹縣", "新竹縣", "");
    addOption(document.myform.selectcity, "苗栗縣", "苗栗縣", "");
    addOption(document.myform.selectcity, "彰化縣", "彰化縣", "");
    addOption(document.myform.selectcity, "南投縣", "南投縣", "");
    addOption(document.myform.selectcity, "雲林縣", "雲林縣", "");
    addOption(document.myform.selectcity, "嘉義縣", "嘉義縣", "");
    addOption(document.myform.selectcity, "屏東縣", "屏東縣", "");
    addOption(document.myform.selectcity, "臺東縣", "臺東縣", "");
    addOption(document.myform.selectcity, "花蓮縣", "花蓮縣", "");
    addOption(document.myform.selectcity, "澎湖縣", "澎湖縣", "");
    addOption(document.myform.selectcity, "基隆市", "基隆市", "");
    addOption(document.myform.selectcity, "新竹市", "新竹市", "");
    addOption(document.myform.selectcity, "嘉義市", "嘉義市", "");
    addOption(document.myform.selectcity, "金門縣", "金門縣", "");
    addOption(document.myform.selectcity, "連江縣", "連江縣", "");
}

function SelectCity() {
    removeAllOptions(document.myform.selectdistrict);
    addOption(document.myform.selectdistrict, "", "請選擇區域", "");
    if (document.myform.selectcity.value == '其他') {
        addOption(document.myform.selectdistrict, "其他", "其他", "");
    }
    if (document.myform.selectcity.value == '外籍生') {
        addOption(document.myform.selectdistrict, "外籍生", "外籍生", "");
    }
    if (document.myform.selectcity.value == '港澳大陸生') {
        addOption(document.myform.selectdistrict, "港澳大陸生", "港澳大陸生", "");
    }
    if (document.myform.selectcity.value == '出國') {
        addOption(document.myform.selectdistrict, "出國", "出國", "");
    }
    // this function is used to fill the category list on load
    if (document.myform.selectcity.value == '新北市')
    {
        addOption(document.myform.selectdistrict, "永和區", "永和區", "");
        addOption(document.myform.selectdistrict, "八里區", "八里區", "");
        addOption(document.myform.selectdistrict, "烏來區", "烏來區", "");
        addOption(document.myform.selectdistrict, "土城區", "土城區", "");
        addOption(document.myform.selectdistrict, "板橋區", "板橋區", "");
        addOption(document.myform.selectdistrict, "樹林區", "樹林區", "");
        addOption(document.myform.selectdistrict, "鶯歌區", "鶯歌區", "");
        addOption(document.myform.selectdistrict, "三峽區", "三峽區", "");
        addOption(document.myform.selectdistrict, "中和區", "中和區", "");
        addOption(document.myform.selectdistrict, "汐止區", "汐止區", "");
        addOption(document.myform.selectdistrict, "萬里區", "萬里區", "");
        addOption(document.myform.selectdistrict, "金山區", "金山區", "");
        addOption(document.myform.selectdistrict, "新店區", "新店區", "");
        addOption(document.myform.selectdistrict, "深坑區", "深坑區", "");
        addOption(document.myform.selectdistrict, "石碇區", "石碇區", "");
        addOption(document.myform.selectdistrict, "坪林區", "坪林區", "");
        addOption(document.myform.selectdistrict, "瑞芳區", "瑞芳區", "");
        addOption(document.myform.selectdistrict, "雙溪區", "雙溪區", "");
        addOption(document.myform.selectdistrict, "貢寮區", "貢寮區", "");
        addOption(document.myform.selectdistrict, "平溪區", "平溪區", "");
        addOption(document.myform.selectdistrict, "淡水區", "淡水區", "");
        addOption(document.myform.selectdistrict, "石門區", "石門區", "");
        addOption(document.myform.selectdistrict, "三芝區", "三芝區", "");
        addOption(document.myform.selectdistrict, "新莊區", "新莊區", "");
        addOption(document.myform.selectdistrict, "泰山區", "泰山區", "");
        addOption(document.myform.selectdistrict, "五股區", "五股區", "");
        addOption(document.myform.selectdistrict, "蘆洲區", "蘆洲區", "");
        addOption(document.myform.selectdistrict, "林口區", "林口區", "");
        addOption(document.myform.selectdistrict, "三重區", "三重區", "");
    }
    if (document.myform.selectcity.value == '臺北市') {
        addOption(document.myform.selectdistrict, "松山區", "松山區", "");
        addOption(document.myform.selectdistrict, "信義區", "信義區", "");
        addOption(document.myform.selectdistrict, "大安區", "大安區", "");
        addOption(document.myform.selectdistrict, "中山區", "中山區", "");
        addOption(document.myform.selectdistrict, "中正區", "中正區", "");
        addOption(document.myform.selectdistrict, "大同區", "大同區", "");
        addOption(document.myform.selectdistrict, "萬華區", "萬華區", "");
        addOption(document.myform.selectdistrict, "文山區", "文山區", "");
        addOption(document.myform.selectdistrict, "南港區", "南港區", "");
        addOption(document.myform.selectdistrict, "內湖區", "內湖區", "");
        addOption(document.myform.selectdistrict, "士林區", "士林區", "");
        addOption(document.myform.selectdistrict, "北投區", "北投區", "");
    }
    if (document.myform.selectcity.value == '臺中市') {
        addOption(document.myform.selectdistrict, "潭子區", "潭子區", "");
        addOption(document.myform.selectdistrict, "豐原區", "豐原區", "");
        addOption(document.myform.selectdistrict, "后里區", "后里區", "");
        addOption(document.myform.selectdistrict, "神岡區", "神岡區", "");
        addOption(document.myform.selectdistrict, "大雅區", "大雅區", "");
        addOption(document.myform.selectdistrict, "外埔區", "外埔區", "");
        addOption(document.myform.selectdistrict, "東勢區", "東勢區", "");
        addOption(document.myform.selectdistrict, "石岡區", "石岡區", "");
        addOption(document.myform.selectdistrict, "新社區", "新社區", "");
        addOption(document.myform.selectdistrict, "清水區", "清水區", "");
        addOption(document.myform.selectdistrict, "梧棲區", "梧棲區", "");
        addOption(document.myform.selectdistrict, "大甲區", "大甲區", "");
        addOption(document.myform.selectdistrict, "沙鹿區", "沙鹿區", "");
        addOption(document.myform.selectdistrict, "龍井區", "龍井區", "");
        addOption(document.myform.selectdistrict, "烏日區", "烏日區", "");
        addOption(document.myform.selectdistrict, "大肚區", "大肚區", "");
        addOption(document.myform.selectdistrict, "大里區", "大里區", "");
        addOption(document.myform.selectdistrict, "霧峰區", "霧峰區", "");
        addOption(document.myform.selectdistrict, "太平區", "太平區", "");
        addOption(document.myform.selectdistrict, "和平區", "和平區", "");
        addOption(document.myform.selectdistrict, "大安區", "大安區", "");
        addOption(document.myform.selectdistrict, "北區", "北區", "");
        addOption(document.myform.selectdistrict, "北屯區", "北屯區", "");
        addOption(document.myform.selectdistrict, "西屯區", "西屯區", "");
        addOption(document.myform.selectdistrict, "中區", "中區", "");
        addOption(document.myform.selectdistrict, "東區", "東區", "");
        addOption(document.myform.selectdistrict, "西區", "西區", "");
        addOption(document.myform.selectdistrict, "南區", "南區", "");
        addOption(document.myform.selectdistrict, "南屯區", "南屯區", "");
    }
    if (document.myform.selectcity.value == '臺南市') {
        addOption(document.myform.selectdistrict, "北區", "北區", "");
        addOption(document.myform.selectdistrict, "東區", "東區", "");
        addOption(document.myform.selectdistrict, "南區", "南區", "");
        addOption(document.myform.selectdistrict, "仁德區", "仁德區", "");
        addOption(document.myform.selectdistrict, "歸仁區", "歸仁區", "");
        addOption(document.myform.selectdistrict, "關廟區", "關廟區", "");
        addOption(document.myform.selectdistrict, "龍崎區", "龍崎區", "");
        addOption(document.myform.selectdistrict, "永康區", "永康區", "");
        addOption(document.myform.selectdistrict, "新化區", "新化區", "");
        addOption(document.myform.selectdistrict, "山上區", "山上區", "");
        addOption(document.myform.selectdistrict, "玉井區", "玉井區", "");
        addOption(document.myform.selectdistrict, "楠西區", "楠西區", "");
        addOption(document.myform.selectdistrict, "南化區", "南化區", "");
        addOption(document.myform.selectdistrict, "左鎮區", "左鎮區", "");
        addOption(document.myform.selectdistrict, "善化區", "善化區", "");
        addOption(document.myform.selectdistrict, "新市區", "新市區", "");
        addOption(document.myform.selectdistrict, "安定區", "安定區", "");
        addOption(document.myform.selectdistrict, "麻豆區", "麻豆區", "");
        addOption(document.myform.selectdistrict, "佳里區", "佳里區", "");
        addOption(document.myform.selectdistrict, "西港區", "西港區", "");
        addOption(document.myform.selectdistrict, "七股區", "七股區", "");
        addOption(document.myform.selectdistrict, "將軍區", "將軍區", "");
        addOption(document.myform.selectdistrict, "北門區", "北門區", "");
        addOption(document.myform.selectdistrict, "學甲區", "學甲區", "");
        addOption(document.myform.selectdistrict, "下營區", "下營區", "");
        addOption(document.myform.selectdistrict, "六甲區", "六甲區", "");
        addOption(document.myform.selectdistrict, "官田區", "官田區", "");
        addOption(document.myform.selectdistrict, "大內區", "大內區", "");
        addOption(document.myform.selectdistrict, "新營區", "新營區", "");
        addOption(document.myform.selectdistrict, "鹽水區", "鹽水區", "");
        addOption(document.myform.selectdistrict, "白河區", "白河區", "");
        addOption(document.myform.selectdistrict, "柳營區", "柳營區", "");
        addOption(document.myform.selectdistrict, "後壁區", "後壁區", "");
        addOption(document.myform.selectdistrict, "東山區", "東山區", "");
        addOption(document.myform.selectdistrict, "中西區", "中西區", "");
        addOption(document.myform.selectdistrict, "安平區", "安平區", "");
        addOption(document.myform.selectdistrict, "安南區", "安南區", "");
    }
    if (document.myform.selectcity.value == '高雄市') {
        addOption(document.myform.selectdistrict, "鳳山區", "鳳山區", "");
        addOption(document.myform.selectdistrict, "林園區", "林園區", "");
        addOption(document.myform.selectdistrict, "大寮區", "大寮區", "");
        addOption(document.myform.selectdistrict, "大樹區", "大樹區", "");
        addOption(document.myform.selectdistrict, "仁武區", "仁武區", "");
        addOption(document.myform.selectdistrict, "大社區", "大社區", "");
        addOption(document.myform.selectdistrict, "鳥松區", "鳥松區", "");
        addOption(document.myform.selectdistrict, "岡山區", "岡山區", "");
        addOption(document.myform.selectdistrict, "橋頭區", "橋頭區", "");
        addOption(document.myform.selectdistrict, "燕巢區", "燕巢區", "");
        addOption(document.myform.selectdistrict, "田寮區", "田寮區", "");
        addOption(document.myform.selectdistrict, "阿蓮區", "阿蓮區", "");
        addOption(document.myform.selectdistrict, "路竹區", "路竹區", "");
        addOption(document.myform.selectdistrict, "湖內區", "湖內區", "");
        addOption(document.myform.selectdistrict, "茄萣區", "茄萣區", "");
        addOption(document.myform.selectdistrict, "永安區", "永安區", "");
        addOption(document.myform.selectdistrict, "彌陀區", "彌陀區", "");
        addOption(document.myform.selectdistrict, "梓官區", "梓官區", "");
        addOption(document.myform.selectdistrict, "旗山區", "旗山區", "");
        addOption(document.myform.selectdistrict, "美濃區", "美濃區", "");
        addOption(document.myform.selectdistrict, "六龜區", "六龜區", "");
        addOption(document.myform.selectdistrict, "杉林區", "杉林區", "");
        addOption(document.myform.selectdistrict, "內門區", "內門區", "");
        addOption(document.myform.selectdistrict, "甲仙區", "甲仙區", "");
        addOption(document.myform.selectdistrict, "那瑪夏區", "那瑪夏區", "");
        addOption(document.myform.selectdistrict, "茂林區", "茂林區", "");
        addOption(document.myform.selectdistrict, "桃源區", "桃源區", "");
        addOption(document.myform.selectdistrict, "鹽埕區", "鹽埕區", "");
        addOption(document.myform.selectdistrict, "鼓山區", "鼓山區", "");
        addOption(document.myform.selectdistrict, "左營區", "左營區", "");
        addOption(document.myform.selectdistrict, "楠梓區", "楠梓區", "");
        addOption(document.myform.selectdistrict, "三民區", "三民區", "");
        addOption(document.myform.selectdistrict, "新興區", "新興區", "");
        addOption(document.myform.selectdistrict, "前金區", "前金區", "");
        addOption(document.myform.selectdistrict, "苓雅區", "苓雅區", "");
        addOption(document.myform.selectdistrict, "前鎮區", "前鎮區", "");
        addOption(document.myform.selectdistrict, "旗津區", "旗津區", "");
        addOption(document.myform.selectdistrict, "小港區", "小港區", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣') {
        addOption(document.myform.selectdistrict, "宜蘭市", "宜蘭市", "");
        addOption(document.myform.selectdistrict, "羅東鎮", "羅東鎮", "");
        addOption(document.myform.selectdistrict, "蘇澳鎮", "蘇澳鎮", "");
        addOption(document.myform.selectdistrict, "頭城鎮", "頭城鎮", "");
        addOption(document.myform.selectdistrict, "礁溪鄉", "礁溪鄉", "");
        addOption(document.myform.selectdistrict, "員山鄉", "員山鄉", "");
        addOption(document.myform.selectdistrict, "壯圍鄉", "壯圍鄉", "");
        addOption(document.myform.selectdistrict, "五結鄉", "五結鄉", "");
        addOption(document.myform.selectdistrict, "冬山鄉", "冬山鄉", "");
        addOption(document.myform.selectdistrict, "三星鄉", "三星鄉", "");
        addOption(document.myform.selectdistrict, "大同鄉", "大同鄉", "");
        addOption(document.myform.selectdistrict, "南澳鄉", "南澳鄉", "");
    }
    if (document.myform.selectcity.value == '桃園市') {
        addOption(document.myform.selectdistrict, "桃園區", "桃園區", "");
        addOption(document.myform.selectdistrict, "八德區", "八德區", "");
        addOption(document.myform.selectdistrict, "大溪區", "大溪區", "");
        addOption(document.myform.selectdistrict, "蘆竹區", "蘆竹區", "");
        addOption(document.myform.selectdistrict, "龜山區", "龜山區", "");
        addOption(document.myform.selectdistrict, "大園區", "大園區", "");
        addOption(document.myform.selectdistrict, "中壢區", "中壢區", "");
        addOption(document.myform.selectdistrict, "龍潭區", "龍潭區", "");
        addOption(document.myform.selectdistrict, "平鎮區", "平鎮區", "");
        addOption(document.myform.selectdistrict, "楊梅區", "楊梅區", "");
        addOption(document.myform.selectdistrict, "新屋區", "新屋區", "");
        addOption(document.myform.selectdistrict, "復興區", "復興區", "");
        addOption(document.myform.selectdistrict, "觀音區", "觀音區", "");
    }
    if (document.myform.selectcity.value == '新竹縣') {
        addOption(document.myform.selectdistrict, "竹東鎮", "竹東鎮", "");
        addOption(document.myform.selectdistrict, "竹北市", "竹北市", "");
        addOption(document.myform.selectdistrict, "關西鎮", "關西鎮", "");
        addOption(document.myform.selectdistrict, "新埔鎮", "新埔鎮", "");
        addOption(document.myform.selectdistrict, "湖口鄉", "湖口鄉", "");
        addOption(document.myform.selectdistrict, "新豐鄉", "新豐鄉", "");
        addOption(document.myform.selectdistrict, "橫山鄉", "橫山鄉", "");
        addOption(document.myform.selectdistrict, "芎林鄉", "芎林鄉", "");
        addOption(document.myform.selectdistrict, "寶山鄉", "寶山鄉", "");
        addOption(document.myform.selectdistrict, "北埔鄉", "北埔鄉", "");
        addOption(document.myform.selectdistrict, "峨眉鄉", "峨眉鄉", "");
        addOption(document.myform.selectdistrict, "尖石鄉", "尖石鄉", "");
        addOption(document.myform.selectdistrict, "五峰鄉", "五峰鄉", "");
    }
    if (document.myform.selectcity.value == '苗栗縣') {
        addOption(document.myform.selectdistrict, "苗栗市", "苗栗市", "");
        addOption(document.myform.selectdistrict, "頭屋鄉", "頭屋鄉", "");
        addOption(document.myform.selectdistrict, "公館鄉", "公館鄉", "");
        addOption(document.myform.selectdistrict, "銅鑼鄉", "銅鑼鄉", "");
        addOption(document.myform.selectdistrict, "三義鄉", "三義鄉", "");
        addOption(document.myform.selectdistrict, "苑裡鎮", "苑裡鎮", "");
        addOption(document.myform.selectdistrict, "通霄鎮", "通霄鎮", "");
        addOption(document.myform.selectdistrict, "西湖鄉", "西湖鄉", "");
        addOption(document.myform.selectdistrict, "頭份市", "頭份市", "");
        addOption(document.myform.selectdistrict, "竹南鎮", "竹南鎮", "");
        addOption(document.myform.selectdistrict, "三灣鄉", "三灣鄉", "");
        addOption(document.myform.selectdistrict, "南庄鄉", "南庄鄉", "");
        addOption(document.myform.selectdistrict, "造橋鄉", "造橋鄉", "");
        addOption(document.myform.selectdistrict, "後龍鎮", "後龍鎮", "");
        addOption(document.myform.selectdistrict, "大湖鄉", "大湖鄉", "");
        addOption(document.myform.selectdistrict, "獅潭鄉", "獅潭鄉", "");
        addOption(document.myform.selectdistrict, "卓蘭鎮", "卓蘭鎮", "");
        addOption(document.myform.selectdistrict, "泰安鄉", "泰安鄉", "");
    }
    if (document.myform.selectcity.value == '彰化縣') {
        addOption(document.myform.selectdistrict, "彰化市", "彰化市", "");
        addOption(document.myform.selectdistrict, "芬園鄉", "芬園鄉", "");
        addOption(document.myform.selectdistrict, "花壇鄉", "花壇鄉", "");
        addOption(document.myform.selectdistrict, "和美鎮", "和美鎮", "");
        addOption(document.myform.selectdistrict, "線西鄉", "線西鄉", "");
        addOption(document.myform.selectdistrict, "伸港鄉", "伸港鄉", "");
        addOption(document.myform.selectdistrict, "鹿港鎮", "鹿港鎮", "");
        addOption(document.myform.selectdistrict, "福興鄉", "福興鄉", "");
        addOption(document.myform.selectdistrict, "秀水鄉", "秀水鄉", "");
        addOption(document.myform.selectdistrict, "溪湖鎮", "溪湖鎮", "");
        addOption(document.myform.selectdistrict, "埔鹽鄉", "埔鹽鄉", "");
        addOption(document.myform.selectdistrict, "埔心鄉", "埔心鄉", "");
        addOption(document.myform.selectdistrict, "員林市", "員林市", "");
        addOption(document.myform.selectdistrict, "大村鄉", "大村鄉", "");
        addOption(document.myform.selectdistrict, "永靖鄉", "永靖鄉", "");
        addOption(document.myform.selectdistrict, "田中鎮", "田中鎮", "");
        addOption(document.myform.selectdistrict, "社頭鄉", "社頭鄉", "");
        addOption(document.myform.selectdistrict, "二水鄉", "二水鄉", "");
        addOption(document.myform.selectdistrict, "北斗鎮", "北斗鎮", "");
        addOption(document.myform.selectdistrict, "田尾鄉", "田尾鄉", "");
        addOption(document.myform.selectdistrict, "埤頭鄉", "埤頭鄉", "");
        addOption(document.myform.selectdistrict, "溪州鄉", "溪州鄉", "");
        addOption(document.myform.selectdistrict, "二林鎮", "二林鎮", "");
        addOption(document.myform.selectdistrict, "大城鄉", "大城鄉", "");
        addOption(document.myform.selectdistrict, "竹塘鄉", "竹塘鄉", "");
        addOption(document.myform.selectdistrict, "芳苑鄉", "芳苑鄉", "");
    }
    if (document.myform.selectcity.value == '南投縣') {
        addOption(document.myform.selectdistrict, "埔里鎮", "埔里鎮", "");
        addOption(document.myform.selectdistrict, "南投市", "南投市", "");
        addOption(document.myform.selectdistrict, "草屯鎮", "草屯鎮", "");
        addOption(document.myform.selectdistrict, "竹山鎮", "竹山鎮", "");
        addOption(document.myform.selectdistrict, "集集鎮", "集集鎮", "");
        addOption(document.myform.selectdistrict, "名間鄉", "名間鄉", "");
        addOption(document.myform.selectdistrict, "鹿谷鄉", "鹿谷鄉", "");
        addOption(document.myform.selectdistrict, "中寮鄉", "中寮鄉", "");
        addOption(document.myform.selectdistrict, "魚池鄉", "魚池鄉", "");
        addOption(document.myform.selectdistrict, "國姓鄉", "國姓鄉", "");
        addOption(document.myform.selectdistrict, "水里鄉", "水里鄉", "");
        addOption(document.myform.selectdistrict, "信義鄉", "信義鄉", "");
        addOption(document.myform.selectdistrict, "仁愛鄉", "仁愛鄉", "");
    }
    if (document.myform.selectcity.value == '雲林縣') {
        addOption(document.myform.selectdistrict, "古坑鄉", "古坑鄉", "");
        addOption(document.myform.selectdistrict, "斗六市", "斗六市", "");
        addOption(document.myform.selectdistrict, "林內鄉", "林內鄉", "");
        addOption(document.myform.selectdistrict, "斗南鎮", "斗南鎮", "");
        addOption(document.myform.selectdistrict, "莿桐鄉", "莿桐鄉", "");
        addOption(document.myform.selectdistrict, "大埤鄉", "大埤鄉", "");
        addOption(document.myform.selectdistrict, "虎尾鎮", "虎尾鎮", "");
        addOption(document.myform.selectdistrict, "土庫鎮", "土庫鎮", "");
        addOption(document.myform.selectdistrict, "褒忠鄉", "褒忠鄉", "");
        addOption(document.myform.selectdistrict, "東勢鄉", "東勢鄉", "");
        addOption(document.myform.selectdistrict, "臺西鄉", "臺西鄉", "");
        addOption(document.myform.selectdistrict, "西螺鎮", "西螺鎮", "");
        addOption(document.myform.selectdistrict, "二崙鄉", "二崙鄉", "");
        addOption(document.myform.selectdistrict, "崙背鄉", "崙背鄉", "");
        addOption(document.myform.selectdistrict, "麥寮鄉", "麥寮鄉", "");
        addOption(document.myform.selectdistrict, "北港鎮", "北港鎮", "");
        addOption(document.myform.selectdistrict, "元長鄉", "元長鄉", "");
        addOption(document.myform.selectdistrict, "四湖鄉", "四湖鄉", "");
        addOption(document.myform.selectdistrict, "口湖鄉", "口湖鄉", "");
        addOption(document.myform.selectdistrict, "水林鄉", "水林鄉", "");
    }
    if (document.myform.selectcity.value == '嘉義縣') {
        addOption(document.myform.selectdistrict, "朴子市", "朴子市", "");
        addOption(document.myform.selectdistrict, "布袋鎮", "布袋鎮", "");
        addOption(document.myform.selectdistrict, "大林鎮", "大林鎮", "");
        addOption(document.myform.selectdistrict, "民雄鄉", "民雄鄉", "");
        addOption(document.myform.selectdistrict, "溪口鄉", "溪口鄉", "");
        addOption(document.myform.selectdistrict, "新港鄉", "新港鄉", "");
        addOption(document.myform.selectdistrict, "六腳鄉", "六腳鄉", "");
        addOption(document.myform.selectdistrict, "東石鄉", "東石鄉", "");
        addOption(document.myform.selectdistrict, "鹿草鄉", "鹿草鄉", "");
        addOption(document.myform.selectdistrict, "義竹鄉", "義竹鄉", "");
        addOption(document.myform.selectdistrict, "太保市", "太保市", "");
        addOption(document.myform.selectdistrict, "水上鄉", "水上鄉", "");
        addOption(document.myform.selectdistrict, "中埔鄉", "中埔鄉", "");
        addOption(document.myform.selectdistrict, "番路鄉", "番路鄉", "");
        addOption(document.myform.selectdistrict, "竹崎鄉", "竹崎鄉", "");
        addOption(document.myform.selectdistrict, "梅山鄉", "梅山鄉", "");
        addOption(document.myform.selectdistrict, "大埔鄉", "大埔鄉", "");
        addOption(document.myform.selectdistrict, "阿里山鄉", "阿里山鄉", "");
    }
    if (document.myform.selectcity.value == '屏東縣') {
        addOption(document.myform.selectdistrict, "屏東市", "屏東市", "");
        addOption(document.myform.selectdistrict, "萬丹鄉", "萬丹鄉", "");
        addOption(document.myform.selectdistrict, "麟洛鄉", "麟洛鄉", "");
        addOption(document.myform.selectdistrict, "九如鄉", "九如鄉", "");
        addOption(document.myform.selectdistrict, "長治鄉", "長治鄉", "");
        addOption(document.myform.selectdistrict, "鹽埔鄉", "鹽埔鄉", "");
        addOption(document.myform.selectdistrict, "高樹鄉", "高樹鄉", "");
        addOption(document.myform.selectdistrict, "里港鄉", "里港鄉", "");
        addOption(document.myform.selectdistrict, "潮州鎮", "潮州鎮", "");
        addOption(document.myform.selectdistrict, "萬巒鄉", "萬巒鄉", "");
        addOption(document.myform.selectdistrict, "內埔鄉", "內埔鄉", "");
        addOption(document.myform.selectdistrict, "竹田鄉", "竹田鄉", "");
        addOption(document.myform.selectdistrict, "新埤鄉", "新埤鄉", "");
        addOption(document.myform.selectdistrict, "枋寮鄉", "枋寮鄉", "");
        addOption(document.myform.selectdistrict, "東港鎮", "東港鎮", "");
        addOption(document.myform.selectdistrict, "新園鄉", "新園鄉", "");
        addOption(document.myform.selectdistrict, "琉球鄉", "琉球鄉", "");
        addOption(document.myform.selectdistrict, "崁頂鄉", "崁頂鄉", "");
        addOption(document.myform.selectdistrict, "林邊鄉", "林邊鄉", "");
        addOption(document.myform.selectdistrict, "南州鄉", "南州鄉", "");
        addOption(document.myform.selectdistrict, "佳冬鄉", "佳冬鄉", "");
        addOption(document.myform.selectdistrict, "恆春鎮", "恆春鎮", "");
        addOption(document.myform.selectdistrict, "車城鄉", "車城鄉", "");
        addOption(document.myform.selectdistrict, "滿州鄉", "滿州鄉", "");
        addOption(document.myform.selectdistrict, "枋山鄉", "枋山鄉", "");
        addOption(document.myform.selectdistrict, "三地門鄉", "三地門鄉", "");
        addOption(document.myform.selectdistrict, "瑪家鄉", "瑪家鄉", "");
        addOption(document.myform.selectdistrict, "霧臺鄉", "霧臺鄉", "");
        addOption(document.myform.selectdistrict, "泰武鄉", "泰武鄉", "");
        addOption(document.myform.selectdistrict, "來義鄉", "來義鄉", "");
        addOption(document.myform.selectdistrict, "春日鄉", "春日鄉", "");
        addOption(document.myform.selectdistrict, "獅子鄉", "獅子鄉", "");
        addOption(document.myform.selectdistrict, "牡丹鄉", "牡丹鄉", "");
    }
    if (document.myform.selectcity.value == '臺東縣') {
        addOption(document.myform.selectdistrict, "臺東市", "臺東市", "");
        addOption(document.myform.selectdistrict, "卑南鄉", "卑南鄉", "");
        addOption(document.myform.selectdistrict, "太麻里鄉", "太麻里鄉", "");
        addOption(document.myform.selectdistrict, "大武鄉", "大武鄉", "");
        addOption(document.myform.selectdistrict, "綠島鄉", "綠島鄉", "");
        addOption(document.myform.selectdistrict, "鹿野鄉", "鹿野鄉", "");
        addOption(document.myform.selectdistrict, "關山鎮", "關山鎮", "");
        addOption(document.myform.selectdistrict, "池上鄉", "池上鄉", "");
        addOption(document.myform.selectdistrict, "東河鄉", "東河鄉", "");
        addOption(document.myform.selectdistrict, "成功鎮", "成功鎮", "");
        addOption(document.myform.selectdistrict, "長濱鄉", "長濱鄉", "");
        addOption(document.myform.selectdistrict, "金峰鄉", "金峰鄉", "");
        addOption(document.myform.selectdistrict, "達仁鄉", "達仁鄉", "");
        addOption(document.myform.selectdistrict, "蘭嶼鄉", "蘭嶼鄉", "");
        addOption(document.myform.selectdistrict, "延平鄉", "延平鄉", "");
        addOption(document.myform.selectdistrict, "海端鄉", "海端鄉", "");
    }
    if (document.myform.selectcity.value == '花蓮縣') {
        addOption(document.myform.selectdistrict, "花蓮市", "花蓮市", "");
        addOption(document.myform.selectdistrict, "新城鄉", "新城鄉", "");
        addOption(document.myform.selectdistrict, "吉安鄉", "吉安鄉", "");
        addOption(document.myform.selectdistrict, "壽豐鄉", "壽豐鄉", "");
        addOption(document.myform.selectdistrict, "鳳林鎮", "鳳林鎮", "");
        addOption(document.myform.selectdistrict, "光復鄉", "光復鄉", "");
        addOption(document.myform.selectdistrict, "瑞穗鄉", "瑞穗鄉", "");
        addOption(document.myform.selectdistrict, "豐濱鄉", "豐濱鄉", "");
        addOption(document.myform.selectdistrict, "玉里鎮", "玉里鎮", "");
        addOption(document.myform.selectdistrict, "富里鄉", "富里鄉", "");
        addOption(document.myform.selectdistrict, "富里鄉", "富里鄉", "");
        addOption(document.myform.selectdistrict, "秀林鄉", "秀林鄉", "");
        addOption(document.myform.selectdistrict, "萬榮鄉", "萬榮鄉", "");
        addOption(document.myform.selectdistrict, "卓溪鄉", "卓溪鄉", "");
    }
    if (document.myform.selectcity.value == '澎湖縣') {
        addOption(document.myform.selectdistrict, "馬公市", "馬公市", "");
        addOption(document.myform.selectdistrict, "湖西鄉", "湖西鄉", "");
        addOption(document.myform.selectdistrict, "白沙鄉", "白沙鄉", "");
        addOption(document.myform.selectdistrict, "西嶼鄉", "西嶼鄉", "");
        addOption(document.myform.selectdistrict, "望安鄉", "望安鄉", "");
        addOption(document.myform.selectdistrict, "七美鄉", "七美鄉", "");
    }
    if (document.myform.selectcity.value == '基隆市') {
        addOption(document.myform.selectdistrict, "仁愛區", "仁愛區", "");
        addOption(document.myform.selectdistrict, "安樂區", "安樂區", "");
        addOption(document.myform.selectdistrict, "七堵區", "七堵區", "");
        addOption(document.myform.selectdistrict, "暖暖區", "暖暖區", "");
        addOption(document.myform.selectdistrict, "信義區", "信義區", "");
        addOption(document.myform.selectdistrict, "中山區", "中山區", "");
        addOption(document.myform.selectdistrict, "中正區", "中正區", "");
    }
    if (document.myform.selectcity.value == '新竹市') {
        addOption(document.myform.selectdistrict, "東區", "東區", "");
        addOption(document.myform.selectdistrict, "北區", "北區", "");
        addOption(document.myform.selectdistrict, "香山區", "香山區", "");
    }
    if (document.myform.selectcity.value == '嘉義市') {
        addOption(document.myform.selectdistrict, "東區", "東區", "");
        addOption(document.myform.selectdistrict, "西區", "西區", "");
    }
    if (document.myform.selectcity.value == '金門縣') {
        addOption(document.myform.selectdistrict, "金湖鎮", "金湖鎮", "");
        addOption(document.myform.selectdistrict, "金寧鄉", "金寧鄉", "");
        addOption(document.myform.selectdistrict, "金城鎮", "金城鎮", "");
        addOption(document.myform.selectdistrict, "金沙鎮", "金沙鎮", "");
        addOption(document.myform.selectdistrict, "烈嶼鄉", "烈嶼鄉", "");
    }
    if (document.myform.selectcity.value == '連江縣') {
        addOption(document.myform.selectdistrict, "南竿鄉", "南竿鄉", "");
        addOption(document.myform.selectdistrict, "北竿鄉", "北竿鄉", "");
        addOption(document.myform.selectdistrict, "莒光鄉", "莒光鄉", "");
        addOption(document.myform.selectdistrict, "東引鄉", "東引鄉", "");
    }
}

function SelectDistrict() {
// ON selection of category this function will work

    removeAllOptions(document.myform.selectschool);
    addOption(document.myform.selectschool, "", "請選擇學校", "");
    if (document.myform.selectcity.value == '其他' && document.myform.selectdistrict.value == '其他') {
        addOption(document.myform.selectschool, "??????", "其他", "");
    }
    if (document.myform.selectcity.value == '外籍生' && document.myform.selectdistrict.value == '外籍生') {
        addOption(document.myform.selectschool, "??????", "外籍生", "");
    }
    if (document.myform.selectcity.value == '港澳大陸生' && document.myform.selectdistrict.value == '港澳大陸生') {
        addOption(document.myform.selectschool, "??????", "港澳大陸生", "");
    }
    if (document.myform.selectcity.value == '出國' && document.myform.selectdistrict.value == '出國') {
        addOption(document.myform.selectschool, "??????", "出國", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '土城區') {
        addOption(document.myform.selectschool, "011503", "私立裕德國中(小)", "");
        addOption(document.myform.selectschool, "014524", "市立土城國中", "");
        addOption(document.myform.selectschool, "014555", "市立中正國中", "");
        addOption(document.myform.selectschool, "014356", "市立清水高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '板橋區') {
        addOption(document.myform.selectschool, "014501", "市立板橋國中", "");
        addOption(document.myform.selectschool, "014503", "市立重慶國中", "");
        addOption(document.myform.selectschool, "014504", "市立江翠國中", "");
        addOption(document.myform.selectschool, "014505", "市立中山國中", "");
        addOption(document.myform.selectschool, "014506", "市立新埔國中", "");
        addOption(document.myform.selectschool, "014552", "市立溪崑國中", "");
        addOption(document.myform.selectschool, "014573", "市立大觀國中", "");
        addOption(document.myform.selectschool, "014575", "市立忠孝國中", "");
        addOption(document.myform.selectschool, "010301", "國立華僑中學附設國中", "");
        addOption(document.myform.selectschool, "011323", "私立光仁高中附設國中", "");
        addOption(document.myform.selectschool, "014302", "市立海山高中附設國中", "");
        addOption(document.myform.selectschool, "014363", "市立光復高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '新莊區') {
        addOption(document.myform.selectschool, "014507", "市立新莊國中", "");
        addOption(document.myform.selectschool, "014508", "市立新泰國中", "");
        addOption(document.myform.selectschool, "014509", "市立福營國中", "");
        addOption(document.myform.selectschool, "014510", "市立頭前國中", "");
        addOption(document.myform.selectschool, "014559", "市立中平國中", "");
        addOption(document.myform.selectschool, "011310", "財團法人恆毅高中附設國中", "");
        addOption(document.myform.selectschool, "014353", "市立丹鳳高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '三重區') {
        addOption(document.myform.selectschool, "014512", "市立光榮國中", "");
        addOption(document.myform.selectschool, "014513", "市立明志國中", "");
        addOption(document.myform.selectschool, "014514", "市立碧華國中", "");
        addOption(document.myform.selectschool, "014561", "市立三和國中", "");
        addOption(document.myform.selectschool, "014572", "市立二重國中", "");
        addOption(document.myform.selectschool, "011306", "私立金陵女中附設國中", "");
        addOption(document.myform.selectschool, "011316", "私立格致高中附設國中", "");
        addOption(document.myform.selectschool, "014311", "市立三重高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '永和區') {
        addOption(document.myform.selectschool, "014516", "市立永和國中", "");
        addOption(document.myform.selectschool, "014517", "市立福和國中", "");
        addOption(document.myform.selectschool, "014315", "市立永平高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '中和區') {
        addOption(document.myform.selectschool, "014518", "市立中和國中", "");
        addOption(document.myform.selectschool, "014519", "市立積穗國中", "");
        addOption(document.myform.selectschool, "014520", "市立漳和國中", "");
        addOption(document.myform.selectschool, "014554", "市立自強國中", "");
        addOption(document.myform.selectschool, "011309", "財團法人南山高中附設國中", "");
        addOption(document.myform.selectschool, "011324", "私立竹林高中附設國中", "");
        addOption(document.myform.selectschool, "014362", "市立錦和高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '鶯歌區') {
        addOption(document.myform.selectschool, "014521", "市立鶯歌國中", "");
        addOption(document.myform.selectschool, "014560", "市立鳳鳴國中", "");
        addOption(document.myform.selectschool, "014565", "市立尖山國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '樹林區') {
        addOption(document.myform.selectschool, "014523", "市立柑園國中", "");
        addOption(document.myform.selectschool, "014569", "市立育林國中", "");
        addOption(document.myform.selectschool, "014574", "市立三多國中", "");
        addOption(document.myform.selectschool, "014577", "市立桃子腳國中(小)", "");
        addOption(document.myform.selectschool, "014322", "市立樹林高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '三峽區') {
        addOption(document.myform.selectschool, "014525", "市立三峽國中", "");
        addOption(document.myform.selectschool, "014567", "市立安溪國中", "");
        addOption(document.myform.selectschool, "011329", "財團法人辭修高中附設國中", "");
        addOption(document.myform.selectschool, "014326", "市立明德高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '八里區') {
        addOption(document.myform.selectschool, "014527", "市立八里國中", "");
        addOption(document.myform.selectschool, "011311", "私立聖心女中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '泰山區') {
        addOption(document.myform.selectschool, "014528", "市立泰山國中", "");
        addOption(document.myform.selectschool, "014558", "市立義學國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '五股區') {
        addOption(document.myform.selectschool, "014529", "市立五股國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '蘆洲區') {
        addOption(document.myform.selectschool, "014530", "市立蘆洲國中", "");
        addOption(document.myform.selectschool, "014576", "市立鷺江國中", "");
        addOption(document.myform.selectschool, "011318", "私立徐匯高中附設國中", "");
        addOption(document.myform.selectschool, "014357", "市立三民高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '林口區') {
        addOption(document.myform.selectschool, "014531", "市立林口國中", "");
        addOption(document.myform.selectschool, "014571", "市立崇林國中", "");
        addOption(document.myform.selectschool, "014579", "市立佳林國中", "");
        addOption(document.myform.selectschool, "011317", "私立醒吾高中附設國中", "");
        addOption(document.myform.selectschool, "010F01", "國立林口啟智學校", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '汐止區') {
        addOption(document.myform.selectschool, "014533", "市立汐止國中", "");
        addOption(document.myform.selectschool, "014568", "市立樟樹國中", "");
        addOption(document.myform.selectschool, "014570", "市立青山國中(小)", "");
        addOption(document.myform.selectschool, "011312", "私立崇義高中附設國中", "");
        addOption(document.myform.selectschool, "014332", "市立秀峰高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '淡水區') {
        addOption(document.myform.selectschool, "014534", "市立淡水國中", "");
        addOption(document.myform.selectschool, "014566", "市立正德國中", "");
        addOption(document.myform.selectschool, "011301", "私立淡江高中附設國中", "");
        addOption(document.myform.selectschool, "014364", "市立竹圍高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '三芝區') {
        addOption(document.myform.selectschool, "014536", "市立三芝國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '石門區') {
        addOption(document.myform.selectschool, "014537", "市立石門國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '萬里區') {
        addOption(document.myform.selectschool, "014539", "市立萬里國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '坪林區') {
        addOption(document.myform.selectschool, "014540", "市立坪林國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '新店區') {
        addOption(document.myform.selectschool, "014541", "市立文山國中", "");
        addOption(document.myform.selectschool, "014542", "市立五峰國中", "");
        addOption(document.myform.selectschool, "014580", "市立達觀國中(小)", "");
        addOption(document.myform.selectschool, "011302", "財團法人康橋實驗高中附設國中", "");
        addOption(document.myform.selectschool, "011322", "財團法人崇光女中附設國中", "");
        addOption(document.myform.selectschool, "011325", "私立及人高中附設國中", "");
        addOption(document.myform.selectschool, "014343", "市立安康高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '瑞芳區') {
        addOption(document.myform.selectschool, "014544", "市立瑞芳國中", "");
        addOption(document.myform.selectschool, "014545", "市立欽賢國中", "");
        addOption(document.myform.selectschool, "011399", "私立時雨高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '貢寮區') {
        addOption(document.myform.selectschool, "014546", "市立貢寮國中", "");
        addOption(document.myform.selectschool, "014578", "市立豐珠國中(小)", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '深坑區') {
        addOption(document.myform.selectschool, "014549", "市立深坑國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '平溪區') {
        addOption(document.myform.selectschool, "014550", "市立平溪國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '烏來區') {
        addOption(document.myform.selectschool, "014551", "市立烏來國中(小)", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '金山區') {
        addOption(document.myform.selectschool, "014338", "市立金山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '雙溪區') {
        addOption(document.myform.selectschool, "014347", "市立雙溪高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '石碇區') {
        addOption(document.myform.selectschool, "014348", "市立石碇高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '松山區') {
        addOption(document.myform.selectschool, "313501", "市立介壽國中", "");
        addOption(document.myform.selectschool, "313502", "市立民生國中", "");
        addOption(document.myform.selectschool, "313504", "市立中山國中", "");
        addOption(document.myform.selectschool, "313505", "市立敦化國中", "");
        addOption(document.myform.selectschool, "313301", "市立西松高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '信義區') {
        addOption(document.myform.selectschool, "323502", "市立興雅國中", "");
        addOption(document.myform.selectschool, "323503", "市立永吉國中", "");
        addOption(document.myform.selectschool, "323504", "市立?公國中", "");
        addOption(document.myform.selectschool, "323505", "市立信義國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '大安區') {
        addOption(document.myform.selectschool, "331502", "私立立人國中(小)", "");
        addOption(document.myform.selectschool, "333501", "市立仁愛國中", "");
        addOption(document.myform.selectschool, "333502", "市立大安國中", "");
        addOption(document.myform.selectschool, "333504", "市立芳和國中", "");
        addOption(document.myform.selectschool, "333505", "市立金華國中", "");
        addOption(document.myform.selectschool, "333506", "市立懷生國中", "");
        addOption(document.myform.selectschool, "333507", "市立民族國中", "");
        addOption(document.myform.selectschool, "333508", "市立龍門國中", "");
        addOption(document.myform.selectschool, "330301", "國立師大附中附設國中", "");
        addOption(document.myform.selectschool, "331301", "私立延平中學附設國中", "");
        addOption(document.myform.selectschool, "331304", "私立復興實驗高中附設國中", "");
        addOption(document.myform.selectschool, "333301", "市立和平高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '中山區') {
        addOption(document.myform.selectschool, "343502", "市立長安國中", "");
        addOption(document.myform.selectschool, "343504", "市立北安國中", "");
        addOption(document.myform.selectschool, "343505", "市立新興國中", "");
        addOption(document.myform.selectschool, "343506", "市立五常國中", "");
        addOption(document.myform.selectschool, "343507", "市立濱江國中", "");
        addOption(document.myform.selectschool, "343302", "市立大同高中附設國中", "");
        addOption(document.myform.selectschool, "343303", "市立大直高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '中正區') {
        addOption(document.myform.selectschool, "353501", "市立螢橋國中", "");
        addOption(document.myform.selectschool, "353502", "市立古亭國中", "");
        addOption(document.myform.selectschool, "353503", "市立南門國中", "");
        addOption(document.myform.selectschool, "353504", "市立弘道國中", "");
        addOption(document.myform.selectschool, "353505", "市立中正國中", "");
        addOption(document.myform.selectschool, "351301", "私立強恕中學附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '大同區') {
        addOption(document.myform.selectschool, "363501", "市立建成國中", "");
        addOption(document.myform.selectschool, "363502", "市立忠孝國中", "");
        addOption(document.myform.selectschool, "363504", "市立民權國中", "");
        addOption(document.myform.selectschool, "363506", "市立蘭州國中", "");
        addOption(document.myform.selectschool, "363507", "市立重慶國中", "");
        addOption(document.myform.selectschool, "361301", "私立靜修女中附設國中", "");
        addOption(document.myform.selectschool, "363302", "市立成淵高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '萬華區') {
        addOption(document.myform.selectschool, "373501", "市立萬華國中", "");
        addOption(document.myform.selectschool, "373503", "市立雙園國中", "");
        addOption(document.myform.selectschool, "373505", "市立龍山國中", "");
        addOption(document.myform.selectschool, "371301", "私立立人高中附設國中", "");
        addOption(document.myform.selectschool, "373302", "市立大理高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '文山區') {
        addOption(document.myform.selectschool, "381501", "私立靜心國中", "");
        addOption(document.myform.selectschool, "383501", "市立木柵國中", "");
        addOption(document.myform.selectschool, "383502", "市立實踐國中", "");
        addOption(document.myform.selectschool, "383503", "市立北政國中", "");
        addOption(document.myform.selectschool, "383504", "市立景美國中", "");
        addOption(document.myform.selectschool, "383505", "市立興福國中", "");
        addOption(document.myform.selectschool, "383507", "市立景興國中", "");
        addOption(document.myform.selectschool, "380301", "國立政大附中附設國中", "");
        addOption(document.myform.selectschool, "381301", "私立東山高中附設國中", "");
        addOption(document.myform.selectschool, "381304", "私立再興中學附設國中", "");
        addOption(document.myform.selectschool, "381305", "私立景文高中附設國中", "");
        addOption(document.myform.selectschool, "383302", "市立萬芳高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '南港區') {
        addOption(document.myform.selectschool, "393501", "市立誠正國中", "");
        addOption(document.myform.selectschool, "393503", "市立成德國中", "");
        addOption(document.myform.selectschool, "393301", "市立南港高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '內湖區') {
        addOption(document.myform.selectschool, "403501", "市立內湖國中", "");
        addOption(document.myform.selectschool, "403502", "市立麗山國中", "");
        addOption(document.myform.selectschool, "403503", "市立三民國中", "");
        addOption(document.myform.selectschool, "403504", "市立西湖國中", "");
        addOption(document.myform.selectschool, "403505", "市立東湖國中", "");
        addOption(document.myform.selectschool, "403506", "市立明湖國中", "");
        addOption(document.myform.selectschool, "401302", "私立方濟中學附設國中", "");
        addOption(document.myform.selectschool, "401303", "私立達人女中附設國中", "");
        addOption(document.myform.selectschool, "400144", "國立台灣戲曲學院", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '士林區') {
        addOption(document.myform.selectschool, "413501", "市立士林國中", "");
        addOption(document.myform.selectschool, "413502", "市立蘭雅國中", "");
        addOption(document.myform.selectschool, "413504", "市立至善國中", "");
        addOption(document.myform.selectschool, "413505", "市立格致國中", "");
        addOption(document.myform.selectschool, "413506", "市立福安國中", "");
        addOption(document.myform.selectschool, "413508", "市立天母國中", "");
        addOption(document.myform.selectschool, "411302", "私立衛理女中附設國中", "");
        addOption(document.myform.selectschool, "411303", "私立華興中學附設國中", "");
        addOption(document.myform.selectschool, "413301", "市立陽明高中附設國中", "");
        addOption(document.myform.selectschool, "413302", "市立百齡高中附設國中", "");
        addOption(document.myform.selectschool, "413F01", "市立啟智學校", "");
        addOption(document.myform.selectschool, "413F02", "市立啟明學校", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '北投區') {
        addOption(document.myform.selectschool, "421501", "私立奎山國中", "");
        addOption(document.myform.selectschool, "423501", "市立北投國中", "");
        addOption(document.myform.selectschool, "423502", "市立新民國中", "");
        addOption(document.myform.selectschool, "423503", "市立明德國中", "");
        addOption(document.myform.selectschool, "423504", "市立桃源國中", "");
        addOption(document.myform.selectschool, "423505", "市立石牌國中", "");
        addOption(document.myform.selectschool, "423506", "市立關渡國中", "");
        addOption(document.myform.selectschool, "421301", "私立薇閣高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '') {
        addOption(document.myform.selectschool, "313302", "市立中崙高中附設國中", "");
        addOption(document.myform.selectschool, "400144", "國立臺灣戲曲學院附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大安區') {
        addOption(document.myform.selectschool, "064512", "市立大安國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '豐原區') {
        addOption(document.myform.selectschool, "064501", "市立豐原國中", "");
        addOption(document.myform.selectschool, "064502", "市立豐東國中", "");
        addOption(document.myform.selectschool, "064503", "市立豐南國中", "");
        addOption(document.myform.selectschool, "064545", "市立豐陽國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '潭子區') {
        addOption(document.myform.selectschool, "064504", "市立潭子國中", "");
        addOption(document.myform.selectschool, "064538", "市立潭秀國中", "");
        addOption(document.myform.selectschool, "061301", "財團法人常春藤高中附設國中", "");
        addOption(document.myform.selectschool, "061317", "私立弘文高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大雅區') {
        addOption(document.myform.selectschool, "064505", "市立大雅國中", "");
        addOption(document.myform.selectschool, "064541", "市立大華國中", "");
        addOption(document.myform.selectschool, "060323", "國立中科實驗高級中學附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '神岡區') {
        addOption(document.myform.selectschool, "064506", "市立神岡國中", "");
        addOption(document.myform.selectschool, "064551", "市立神圳國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '后里區') {
        addOption(document.myform.selectschool, "064507", "市立后里國中", "");
        addOption(document.myform.selectschool, "064308", "市立后綜高中附設國中", "");
        addOption(document.myform.selectschool, "060F01", "國立臺中啟明學校", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '外埔區') {
        addOption(document.myform.selectschool, "064509", "市立外埔國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大甲區') {
        addOption(document.myform.selectschool, "064510", "市立大甲國中", "");
        addOption(document.myform.selectschool, "064511", "市立日南國中", "");
        addOption(document.myform.selectschool, "064539", "市立順天國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '清水區') {
        addOption(document.myform.selectschool, "064513", "市立清水國中", "");
        addOption(document.myform.selectschool, "064514", "市立清泉國中", "");
        addOption(document.myform.selectschool, "064540", "市立清海國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '沙鹿區') {
        addOption(document.myform.selectschool, "064515", "市立沙鹿國中", "");
        addOption(document.myform.selectschool, "064534", "市立北勢國中", "");
        addOption(document.myform.selectschool, "064535", "市立鹿寮國中", "");
        addOption(document.myform.selectschool, "064549", "市立公明國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '梧棲區') {
        addOption(document.myform.selectschool, "064516", "市立梧棲國中", "");
        addOption(document.myform.selectschool, "064342", "市立中港高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '龍井區') {
        addOption(document.myform.selectschool, "064517", "市立龍井國中", "");
        addOption(document.myform.selectschool, "064518", "市立四箴國中", "");
        addOption(document.myform.selectschool, "064550", "市立龍津國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大肚區') {
        addOption(document.myform.selectschool, "064519", "市立大道國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '烏日區') {
        addOption(document.myform.selectschool, "064520", "市立烏日國中", "");
        addOption(document.myform.selectschool, "064521", "市立溪南國中", "");
        addOption(document.myform.selectschool, "064546", "市立光德國中", "");
        addOption(document.myform.selectschool, "061313", "私立明道高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '霧峰區') {
        addOption(document.myform.selectschool, "064522", "市立霧峰國中", "");
        addOption(document.myform.selectschool, "064523", "市立光復國中(小)", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '太平區') {
        addOption(document.myform.selectschool, "064525", "市立太平國中", "");
        addOption(document.myform.selectschool, "064526", "市立中平國中", "");
        addOption(document.myform.selectschool, "064543", "市立新光國中", "");
        addOption(document.myform.selectschool, "061315", "私立華盛頓高中附設國中", "");
        addOption(document.myform.selectschool, "064336", "市立長億高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '石岡區') {
        addOption(document.myform.selectschool, "064527", "市立石岡國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '東勢區') {
        addOption(document.myform.selectschool, "064529", "市立東勢國中", "");
        addOption(document.myform.selectschool, "064530", "市立東華國中", "");
        addOption(document.myform.selectschool, "064531", "市立東新國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大里區') {
        addOption(document.myform.selectschool, "064532", "市立成功國中", "");
        addOption(document.myform.selectschool, "064537", "市立光榮國中", "");
        addOption(document.myform.selectschool, "064544", "市立光正國中", "");
        addOption(document.myform.selectschool, "064547", "市立立新國中", "");
        addOption(document.myform.selectschool, "064548", "市立爽文國中", "");
        addOption(document.myform.selectschool, "061310", "私立大明高中附設國中", "");
        addOption(document.myform.selectschool, "061314", "私立僑泰高中附設國中", "");
        addOption(document.myform.selectschool, "061318", "私立立人高中附設國中", "");
        addOption(document.myform.selectschool, "064324", "市立大里高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '和平區') {
        addOption(document.myform.selectschool, "064533", "市立和平國中", "");
        addOption(document.myform.selectschool, "064552", "市立梨山國中(小)", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '西屯區') {
        addOption(document.myform.selectschool, "191503", "私立麗澤國中(小)", "");
        addOption(document.myform.selectschool, "193516", "市立中山國中", "");
        addOption(document.myform.selectschool, "193519", "市立漢口國中", "");
        addOption(document.myform.selectschool, "193520", "市立安和國中", "");
        addOption(document.myform.selectschool, "193521", "市立至善國中", "");
        addOption(document.myform.selectschool, "193526", "市立福科國中", "");
        addOption(document.myform.selectschool, "191301", "私立東大附中附設國中", "");
        addOption(document.myform.selectschool, "191302", "私立葳格高中附設國中", "");
        addOption(document.myform.selectschool, "193313", "市立西苑高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '西區') {
        addOption(document.myform.selectschool, "193501", "市立居仁國中", "");
        addOption(document.myform.selectschool, "193509", "市立光明國中", "");
        addOption(document.myform.selectschool, "193510", "市立向上國中", "");
        addOption(document.myform.selectschool, "193303", "市立忠明高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '北區') {
        addOption(document.myform.selectschool, "193502", "市立雙十國中", "");
        addOption(document.myform.selectschool, "193514", "市立五權國中", "");
        addOption(document.myform.selectschool, "193518", "市立立人國中", "");
        addOption(document.myform.selectschool, "191305", "私立新民高中附設國中", "");
        addOption(document.myform.selectschool, "191313", "私立曉明女中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '南區') {
        addOption(document.myform.selectschool, "193504", "市立崇倫國中", "");
        addOption(document.myform.selectschool, "193512", "市立四育國中", "");
        addOption(document.myform.selectschool, "191308", "私立宜寧高中附設國中", "");
        addOption(document.myform.selectschool, "191309", "私立明德女中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '北屯區') {
        addOption(document.myform.selectschool, "193505", "市立大德國中", "");
        addOption(document.myform.selectschool, "193506", "市立北新國中", "");
        addOption(document.myform.selectschool, "193517", "市立崇德國中", "");
        addOption(document.myform.selectschool, "193524", "市立三光國中", "");
        addOption(document.myform.selectschool, "193525", "市立四張犁國中", "");
        addOption(document.myform.selectschool, "191311", "私立衛道高中附設國中", "");
        addOption(document.myform.selectschool, "193315", "市立東山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "193507", "市立東峰國中", "");
        addOption(document.myform.selectschool, "193511", "市立育英國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '南屯區') {
        addOption(document.myform.selectschool, "193508", "市立黎明國中", "");
        addOption(document.myform.selectschool, "193522", "市立萬和國中", "");
        addOption(document.myform.selectschool, "193523", "市立大業國中", "");
        addOption(document.myform.selectschool, "193527", "市立大墩國中", "");
        addOption(document.myform.selectschool, "191314", "私立嶺東高中附設國中", "");
        addOption(document.myform.selectschool, "193316", "市立惠文高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '新社區') {
        addOption(document.myform.selectschool, "064328", "市立新社高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '北區') {
        addOption(document.myform.selectschool, "213506", "市立民德國中", "");
        addOption(document.myform.selectschool, "213507", "市立成功國中", "");
        addOption(document.myform.selectschool, "213508", "市立延平國中", "");
        addOption(document.myform.selectschool, "213517", "市立文賢國中", "");
        addOption(document.myform.selectschool, "211304", "財團法人聖功女中附設國中", "");
        addOption(document.myform.selectschool, "211317", "私立崑山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '南區') {
        addOption(document.myform.selectschool, "213504", "市立大成國中", "");
        addOption(document.myform.selectschool, "213515", "市立新興國中", "");
        addOption(document.myform.selectschool, "213303", "市立南寧高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "213501", "市立後甲國中", "");
        addOption(document.myform.selectschool, "213502", "市立忠孝國中", "");
        addOption(document.myform.selectschool, "213514", "市立復興國中", "");
        addOption(document.myform.selectschool, "213518", "市立崇明國中", "");
        addOption(document.myform.selectschool, "211301", "私立長榮高中附設國中", "");
        addOption(document.myform.selectschool, "211310", "私立光華女中附設國中", "");
        addOption(document.myform.selectschool, "211318", "私立德光高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '仁德區') {
        addOption(document.myform.selectschool, "111501", "私立城光國中", "");
        addOption(document.myform.selectschool, "114501", "市立仁德國中", "");
        addOption(document.myform.selectschool, "114502", "市立仁德文賢國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '七股區') {
        addOption(document.myform.selectschool, "111502", "私立昭明國中", "");
        addOption(document.myform.selectschool, "114528", "市立後港國中", "");
        addOption(document.myform.selectschool, "114529", "市立竹橋國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '歸仁區') {
        addOption(document.myform.selectschool, "114503", "市立歸仁國中", "");
        addOption(document.myform.selectschool, "114544", "市立沙崙國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '關廟區') {
        addOption(document.myform.selectschool, "114504", "市立關廟國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '永康區') {
        addOption(document.myform.selectschool, "114505", "市立永康國中", "");
        addOption(document.myform.selectschool, "114543", "市立大橋國中", "");
        addOption(document.myform.selectschool, "114306", "市立大灣高中附設國中", "");
        addOption(document.myform.selectschool, "114307", "市立永仁高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '龍崎區') {
        addOption(document.myform.selectschool, "114508", "市立龍崎國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '新化區') {
        addOption(document.myform.selectschool, "114509", "市立新化國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '善化區') {
        addOption(document.myform.selectschool, "114510", "市立善化國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '玉井區') {
        addOption(document.myform.selectschool, "114511", "市立玉井國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '山上區') {
        addOption(document.myform.selectschool, "114512", "市立山上國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '安定區') {
        addOption(document.myform.selectschool, "114513", "市立安定國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '楠西區') {
        addOption(document.myform.selectschool, "114514", "市立楠西國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '新市區') {
        addOption(document.myform.selectschool, "114515", "市立新市國中", "");
        addOption(document.myform.selectschool, "110328", "國立南科國際實驗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '南化區') {
        addOption(document.myform.selectschool, "114516", "市立南化國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '左鎮區') {
        addOption(document.myform.selectschool, "114517", "市立左鎮國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '麻豆區') {
        addOption(document.myform.selectschool, "114518", "市立麻豆國中", "");
        addOption(document.myform.selectschool, "111323", "私立黎明高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '下營區') {
        addOption(document.myform.selectschool, "114519", "市立下營國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '六甲區') {
        addOption(document.myform.selectschool, "114520", "市立六甲國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '官田區') {
        addOption(document.myform.selectschool, "114521", "市立官田國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '大內區') {
        addOption(document.myform.selectschool, "114522", "市立大內國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '佳里區') {
        addOption(document.myform.selectschool, "114523", "市立佳里國中", "");
        addOption(document.myform.selectschool, "114524", "市立佳興國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '學甲區') {
        addOption(document.myform.selectschool, "114525", "市立學甲國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '西港區') {
        addOption(document.myform.selectschool, "114526", "市立西港國中", "");
        addOption(document.myform.selectschool, "111320", "私立港明高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '將軍區') {
        addOption(document.myform.selectschool, "114527", "市立將軍國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '北門區') {
        addOption(document.myform.selectschool, "114530", "市立北門國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '新營區') {
        addOption(document.myform.selectschool, "114531", "市立南新國中", "");
        addOption(document.myform.selectschool, "114532", "市立太子國中", "");
        addOption(document.myform.selectschool, "114533", "市立新東國中", "");
        addOption(document.myform.selectschool, "111313", "私立南光高中附設國中", "");
        addOption(document.myform.selectschool, "111321", "私立興國高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '鹽水區') {
        addOption(document.myform.selectschool, "114534", "市立鹽水國中", "");
        addOption(document.myform.selectschool, "111322", "私立明達高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '白河區') {
        addOption(document.myform.selectschool, "114535", "市立白河國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '柳營區') {
        addOption(document.myform.selectschool, "114536", "市立柳營國中", "");
        addOption(document.myform.selectschool, "111318", "私立鳳和高中附設國中", "");
        addOption(document.myform.selectschool, "111326", "私立新榮高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '東山區') {
        addOption(document.myform.selectschool, "114537", "市立東山國中", "");
        addOption(document.myform.selectschool, "114538", "市立東原國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '後壁區') {
        addOption(document.myform.selectschool, "114539", "市立後壁國中", "");
        addOption(document.myform.selectschool, "114540", "市立菁寮國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '中西區') {
        addOption(document.myform.selectschool, "213505", "市立金城國中", "");
        addOption(document.myform.selectschool, "213509", "市立建興國中", "");
        addOption(document.myform.selectschool, "213510", "市立中山國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '安平區') {
        addOption(document.myform.selectschool, "213511", "市立安平國中", "");
        addOption(document.myform.selectschool, "211320", "財團法人慈濟高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '安南區') {
        addOption(document.myform.selectschool, "213512", "市立安南國中", "");
        addOption(document.myform.selectschool, "213513", "市立安順國中", "");
        addOption(document.myform.selectschool, "213519", "市立和順國中", "");
        addOption(document.myform.selectschool, "213520", "市立海佃國中", "");
        addOption(document.myform.selectschool, "211315", "私立瀛海高中附設國中", "");
        addOption(document.myform.selectschool, "213316", "市立土城高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鳳山區') {
        addOption(document.myform.selectschool, "124501", "市立鳳山國中", "");
        addOption(document.myform.selectschool, "124503", "市立鳳西國中", "");
        addOption(document.myform.selectschool, "124504", "市立五甲國中", "");
        addOption(document.myform.selectschool, "124505", "市立鳳甲國中", "");
        addOption(document.myform.selectschool, "124506", "市立忠孝國中", "");
        addOption(document.myform.selectschool, "124543", "市立青年國中", "");
        addOption(document.myform.selectschool, "124549", "市立中崙國中", "");
        addOption(document.myform.selectschool, "124550", "市立鳳翔國中", "");
        addOption(document.myform.selectschool, "121318", "私立正義高中附設國中", "");
        addOption(document.myform.selectschool, "124340", "市立福誠高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '大寮區') {
        addOption(document.myform.selectschool, "124507", "市立大寮國中", "");
        addOption(document.myform.selectschool, "124508", "市立潮寮國中", "");
        addOption(document.myform.selectschool, "124539", "市立中庄國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '大樹區') {
        addOption(document.myform.selectschool, "124509", "市立大樹國中", "");
        addOption(document.myform.selectschool, "124510", "市立溪埔國中", "");
        addOption(document.myform.selectschool, "121307", "財團法人普門中學附設國中", "");
        addOption(document.myform.selectschool, "121320", "私立義大國際高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鳥松區') {
        addOption(document.myform.selectschool, "124512", "市立鳥松國中", "");
        addOption(document.myform.selectschool, "124302", "市立文山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '大社區') {
        addOption(document.myform.selectschool, "124514", "市立大社國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '岡山區') {
        addOption(document.myform.selectschool, "124515", "市立岡山國中", "");
        addOption(document.myform.selectschool, "124516", "市立前峰國中", "");
        addOption(document.myform.selectschool, "124546", "市立嘉興國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '永安區') {
        addOption(document.myform.selectschool, "124517", "市立永安國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '橋頭區') {
        addOption(document.myform.selectschool, "124518", "市立橋頭國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '梓官區') {
        addOption(document.myform.selectschool, "124519", "市立梓官國中", "");
        addOption(document.myform.selectschool, "124541", "市立蚵寮國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '燕巢區') {
        addOption(document.myform.selectschool, "124520", "市立燕巢國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '阿蓮區') {
        addOption(document.myform.selectschool, "124521", "市立阿蓮國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '湖內區') {
        addOption(document.myform.selectschool, "124523", "市立湖內國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '茄萣區') {
        addOption(document.myform.selectschool, "124524", "市立茄萣國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '田寮區') {
        addOption(document.myform.selectschool, "124525", "市立田寮國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '彌陀區') {
        addOption(document.myform.selectschool, "124526", "市立彌陀國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '旗山區') {
        addOption(document.myform.selectschool, "124527", "市立旗山國中", "");
        addOption(document.myform.selectschool, "124528", "市立圓富國中", "");
        addOption(document.myform.selectschool, "124529", "市立大洲國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '美濃區') {
        addOption(document.myform.selectschool, "124530", "市立美濃國中", "");
        addOption(document.myform.selectschool, "124531", "市立南隆國中", "");
        addOption(document.myform.selectschool, "124532", "市立龍肚國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '六龜區') {
        addOption(document.myform.selectschool, "124534", "市立寶來國中", "");
        addOption(document.myform.selectschool, "124333", "市立六龜高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '杉林區') {
        addOption(document.myform.selectschool, "124535", "市立杉林國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '內門區') {
        addOption(document.myform.selectschool, "124536", "市立內門國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '甲仙區') {
        addOption(document.myform.selectschool, "124537", "市立甲仙國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '林園區') {
        addOption(document.myform.selectschool, "124538", "市立中芸國中", "");
        addOption(document.myform.selectschool, "124311", "市立林園高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '那瑪夏區') {
        addOption(document.myform.selectschool, "124542", "市立那瑪夏國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '路竹區') {
        addOption(document.myform.selectschool, "124544", "市立一甲國中", "");
        addOption(document.myform.selectschool, "124322", "市立路竹高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '仁武區') {
        addOption(document.myform.selectschool, "124545", "市立大灣國中", "");
        addOption(document.myform.selectschool, "124313", "市立仁武高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '茂林區') {
        addOption(document.myform.selectschool, "124547", "市立茂林國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '桃源區') {
        addOption(document.myform.selectschool, "124548", "市立桃源國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鹽埕區') {
        addOption(document.myform.selectschool, "513501", "市立鹽埕國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鼓山區') {
        addOption(document.myform.selectschool, "523502", "市立壽山國中", "");
        addOption(document.myform.selectschool, "523503", "市立明華國中", "");
        addOption(document.myform.selectschool, "523504", "市立七賢國中", "");
        addOption(document.myform.selectschool, "521301", "私立明誠高中附設國中", "");
        addOption(document.myform.selectschool, "521303", "私立大榮高中附設國中", "");
        addOption(document.myform.selectschool, "523301", "市立鼓山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '左營區') {
        addOption(document.myform.selectschool, "533501", "市立左營國中", "");
        addOption(document.myform.selectschool, "533502", "市立大義國中", "");
        addOption(document.myform.selectschool, "533503", "市立立德國中", "");
        addOption(document.myform.selectschool, "533504", "市立龍華國中", "");
        addOption(document.myform.selectschool, "533505", "市立福山國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '楠梓區') {
        addOption(document.myform.selectschool, "543501", "市立楠梓國中", "");
        addOption(document.myform.selectschool, "543502", "市立右昌國中", "");
        addOption(document.myform.selectschool, "543503", "市立後勁國中", "");
        addOption(document.myform.selectschool, "543504", "市立國昌國中", "");
        addOption(document.myform.selectschool, "543505", "市立翠屏國中(小)", "");
        addOption(document.myform.selectschool, "540301", "國立中山大學附屬國光高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '三民區') {
        addOption(document.myform.selectschool, "553501", "市立鼎金國中", "");
        addOption(document.myform.selectschool, "553502", "市立三民國中", "");
        addOption(document.myform.selectschool, "553503", "市立民族國中", "");
        addOption(document.myform.selectschool, "553504", "市立陽明國中", "");
        addOption(document.myform.selectschool, "553505", "市立正興國中", "");
        addOption(document.myform.selectschool, "551301", "私立立志高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '前金區') {
        addOption(document.myform.selectschool, "573501", "市立前金國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '苓雅區') {
        addOption(document.myform.selectschool, "583501", "市立苓雅國中", "");
        addOption(document.myform.selectschool, "583502", "市立五福國中", "");
        addOption(document.myform.selectschool, "583503", "市立大仁國中", "");
        addOption(document.myform.selectschool, "583505", "市立英明國中", "");
        addOption(document.myform.selectschool, "580301", "國立高師大附中附設國中", "");
        addOption(document.myform.selectschool, "581301", "私立復華高中附設國中", "");
        addOption(document.myform.selectschool, "581302", "私立道明中學附設國中", "");
        addOption(document.myform.selectschool, "583301", "市立中正高中附設國中", "");
        addOption(document.myform.selectschool, "583F01", "市立高雄啟智學校", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '前鎮區') {
        addOption(document.myform.selectschool, "591501", "私立優佳國中", "");
        addOption(document.myform.selectschool, "593501", "市立獅甲國中", "");
        addOption(document.myform.selectschool, "593502", "市立前鎮國中", "");
        addOption(document.myform.selectschool, "593503", "市立瑞豐國中", "");
        addOption(document.myform.selectschool, "593504", "市立光華國中", "");
        addOption(document.myform.selectschool, "593505", "市立興仁國中", "");
        addOption(document.myform.selectschool, "593302", "市立瑞祥高中附設國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '旗津區') {
        addOption(document.myform.selectschool, "603501", "市立旗津國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '小港區') {
        addOption(document.myform.selectschool, "613501", "市立小港國中", "");
        addOption(document.myform.selectschool, "613502", "市立鳳林國中", "");
        addOption(document.myform.selectschool, "613503", "市立中山國中", "");
        addOption(document.myform.selectschool, "613504", "市立明義國中", "");
        addOption(document.myform.selectschool, "613505", "市立餐旅國中", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '新興區') {
        addOption(document.myform.selectschool, "563301", "市立新興高中附設國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '宜蘭市') {
        addOption(document.myform.selectschool, "024501", "縣立宜蘭國中", "");
        addOption(document.myform.selectschool, "024502", "縣立中華國中", "");
        addOption(document.myform.selectschool, "024503", "縣立復興國中", "");
        addOption(document.myform.selectschool, "024524", "縣立凱旋國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '羅東鎮') {
        addOption(document.myform.selectschool, "024504", "縣立羅東國中", "");
        addOption(document.myform.selectschool, "024505", "縣立東光國中", "");
        addOption(document.myform.selectschool, "024506", "縣立國華國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '頭城鎮') {
        addOption(document.myform.selectschool, "024507", "縣立頭城國中", "");
        addOption(document.myform.selectschool, "024526", "縣立人文國中(小)", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '蘇澳鎮') {
        addOption(document.myform.selectschool, "024508", "縣立蘇澳國中", "");
        addOption(document.myform.selectschool, "024509", "縣立文化國中", "");
        addOption(document.myform.selectschool, "024510", "縣立南安國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '三星鄉') {
        addOption(document.myform.selectschool, "024511", "縣立三星國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '礁溪鄉') {
        addOption(document.myform.selectschool, "024512", "縣立礁溪國中", "");
        addOption(document.myform.selectschool, "024513", "縣立吳沙國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '冬山鄉') {
        addOption(document.myform.selectschool, "024514", "縣立冬山國中", "");
        addOption(document.myform.selectschool, "024515", "縣立順安國中", "");
        addOption(document.myform.selectschool, "024525", "縣立慈心華德福實驗國中(小)", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '五結鄉') {
        addOption(document.myform.selectschool, "024516", "縣立五結國中", "");
        addOption(document.myform.selectschool, "024517", "縣立興中國中", "");
        addOption(document.myform.selectschool, "024518", "縣立利澤國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '員山鄉') {
        addOption(document.myform.selectschool, "024519", "縣立員山國中", "");
        addOption(document.myform.selectschool, "024520", "縣立內城國中(小)", "");
        addOption(document.myform.selectschool, "021301", "私立慧燈高中附設國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '壯圍鄉') {
        addOption(document.myform.selectschool, "024521", "縣立壯圍國中", "");
        addOption(document.myform.selectschool, "021310", "私立中道高中附設國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '大同鄉') {
        addOption(document.myform.selectschool, "024523", "縣立大同國中", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '南澳鄉') {
        addOption(document.myform.selectschool, "024322", "縣立南澳高中附設國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '中壢區') {
        addOption(document.myform.selectschool, "031502", "私立有得國中(小)", "");
        addOption(document.myform.selectschool, "034521", "市立新明國中", "");
        addOption(document.myform.selectschool, "034522", "市立內壢國中", "");
        addOption(document.myform.selectschool, "034523", "市立大崙國中", "");
        addOption(document.myform.selectschool, "034524", "市立龍岡國中", "");
        addOption(document.myform.selectschool, "034525", "市立興南國中", "");
        addOption(document.myform.selectschool, "034526", "市立自強國中", "");
        addOption(document.myform.selectschool, "034527", "市立東興國中", "");
        addOption(document.myform.selectschool, "034545", "市立龍興國中", "");
        addOption(document.myform.selectschool, "034563", "市立過嶺國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '桃園區') {
        addOption(document.myform.selectschool, "034501", "市立桃園國中", "");
        addOption(document.myform.selectschool, "034502", "市立青溪國中", "");
        addOption(document.myform.selectschool, "034503", "市立文昌國中", "");
        addOption(document.myform.selectschool, "034504", "市立建國國中", "");
        addOption(document.myform.selectschool, "034505", "市立中興國中", "");
        addOption(document.myform.selectschool, "034542", "市立慈文國中", "");
        addOption(document.myform.selectschool, "034546", "市立福豐國中", "");
        addOption(document.myform.selectschool, "034551", "市立同德國中", "");
        addOption(document.myform.selectschool, "034554", "市立大有國中", "");
        addOption(document.myform.selectschool, "034556", "市立會稽國中", "");
        addOption(document.myform.selectschool, "034562", "市立經國國中", "");
        addOption(document.myform.selectschool, "031313", "私立振聲高中附設國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '蘆竹區') {
        addOption(document.myform.selectschool, "034506", "市立南崁國中", "");
        addOption(document.myform.selectschool, "034507", "市立山腳國中", "");
        addOption(document.myform.selectschool, "034508", "市立大竹國中", "");
        addOption(document.myform.selectschool, "034550", "市立光明國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '大園區') {
        addOption(document.myform.selectschool, "034509", "市立大園國中", "");
        addOption(document.myform.selectschool, "034510", "市立竹圍國中", "");
        addOption(document.myform.selectschool, "034565", "市立青埔國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '大溪區') {
        addOption(document.myform.selectschool, "034511", "市立大溪國中", "");
        addOption(document.myform.selectschool, "034513", "市立仁和國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '龜山區') {
        addOption(document.myform.selectschool, "034515", "市立大崗國中", "");
        addOption(document.myform.selectschool, "034552", "市立幸福國中", "");
        addOption(document.myform.selectschool, "034555", "市立龜山國中", "");
        addOption(document.myform.selectschool, "034559", "市立迴龍國中(小)", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '八德區') {
        addOption(document.myform.selectschool, "034516", "市立八德國中", "");
        addOption(document.myform.selectschool, "034517", "市立大成國中", "");
        addOption(document.myform.selectschool, "031320", "私立新興高中附設國中", "");
        addOption(document.myform.selectschool, "034347", "市立永豐高中附設國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '平鎮區') {
        addOption(document.myform.selectschool, "034518", "市立中壢國中", "");
        addOption(document.myform.selectschool, "034520", "市立平南國中", "");
        addOption(document.myform.selectschool, "034543", "市立平興國中", "");
        addOption(document.myform.selectschool, "034549", "市立東安國中", "");
        addOption(document.myform.selectschool, "034560", "市立平鎮國中", "");
        addOption(document.myform.selectschool, "031310", "私立六和高中附設國中", "");
        addOption(document.myform.selectschool, "031311", "私立復旦高中附設國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '楊梅區') {
        addOption(document.myform.selectschool, "034528", "市立楊梅國中", "");
        addOption(document.myform.selectschool, "034529", "市立仁美國中", "");
        addOption(document.myform.selectschool, "034530", "市立富岡國中", "");
        addOption(document.myform.selectschool, "034531", "市立瑞原國中", "");
        addOption(document.myform.selectschool, "034544", "市立楊明國中", "");
        addOption(document.myform.selectschool, "034557", "市立楊光國中(小)", "");
        addOption(document.myform.selectschool, "034564", "市立瑞坪國中", "");
        addOption(document.myform.selectschool, "031312", "私立治平高中附設國中", "");
        addOption(document.myform.selectschool, "031326", "私立大華高中附設國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '觀音區') {
        addOption(document.myform.selectschool, "034533", "市立觀音國中", "");
        addOption(document.myform.selectschool, "034534", "市立草漯國中", "");
        addOption(document.myform.selectschool, "034332", "市立觀音高中附設國中部", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '新屋區') {
        addOption(document.myform.selectschool, "034535", "市立新屋國中", "");
        addOption(document.myform.selectschool, "034536", "市立大坡國中", "");
        addOption(document.myform.selectschool, "034537", "市立永安國中", "");
        addOption(document.myform.selectschool, "031319", "私立清華高中附設國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '龍潭區') {
        addOption(document.myform.selectschool, "034538", "市立龍潭國中", "");
        addOption(document.myform.selectschool, "034539", "市立凌雲國中", "");
        addOption(document.myform.selectschool, "034540", "市立石門國中", "");
        addOption(document.myform.selectschool, "034561", "市立武漢國中", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '復興區') {
        addOption(document.myform.selectschool, "034541", "市立介壽國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '竹北市') {
        addOption(document.myform.selectschool, "041501", "私立康乃薾國中(小)", "");
        addOption(document.myform.selectschool, "044509", "縣立竹北國中", "");
        addOption(document.myform.selectschool, "044510", "縣立鳳岡國中", "");
        addOption(document.myform.selectschool, "044511", "縣立六家國中", "");
        addOption(document.myform.selectschool, "044526", "縣立博愛國中", "");
        addOption(document.myform.selectschool, "044527", "縣立仁愛國中", "");
        addOption(document.myform.selectschool, "044529", "縣立成功國中", "");
        addOption(document.myform.selectschool, "041303", "私立義民高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '竹東鎮') {
        addOption(document.myform.selectschool, "044501", "縣立竹東國中", "");
        addOption(document.myform.selectschool, "044502", "縣立二重國中", "");
        addOption(document.myform.selectschool, "044503", "縣立員東國中", "");
        addOption(document.myform.selectschool, "044528", "縣立自強國中", "");
        addOption(document.myform.selectschool, "041306", "私立東泰高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '關西鎮') {
        addOption(document.myform.selectschool, "044504", "縣立關西國中", "");
        addOption(document.myform.selectschool, "044505", "縣立石光國中", "");
        addOption(document.myform.selectschool, "044506", "縣立富光國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '新埔鎮') {
        addOption(document.myform.selectschool, "044507", "縣立新埔國中", "");
        addOption(document.myform.selectschool, "044508", "縣立照門國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '芎林鄉') {
        addOption(document.myform.selectschool, "044512", "縣立芎林國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '新豐鄉') {
        addOption(document.myform.selectschool, "044513", "縣立新豐國中", "");
        addOption(document.myform.selectschool, "044514", "縣立精華國中", "");
        addOption(document.myform.selectschool, "044525", "縣立忠孝國中", "");
        addOption(document.myform.selectschool, "041305", "私立忠信高中附設國中", "");
        addOption(document.myform.selectschool, "041307", "私立仰德高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '橫山鄉') {
        addOption(document.myform.selectschool, "044515", "縣立橫山國中", "");
        addOption(document.myform.selectschool, "044516", "縣立華山國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '寶山鄉') {
        addOption(document.myform.selectschool, "044517", "縣立寶山國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '北埔鄉') {
        addOption(document.myform.selectschool, "044518", "縣立北埔國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '峨眉鄉') {
        addOption(document.myform.selectschool, "044519", "縣立峨眉國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '湖口鄉') {
        addOption(document.myform.selectschool, "044521", "縣立新湖國中", "");
        addOption(document.myform.selectschool, "044522", "縣立中正國中", "");
        addOption(document.myform.selectschool, "044320", "縣立湖口高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '五峰鄉') {
        addOption(document.myform.selectschool, "044523", "縣立五峰國中", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '尖石鄉') {
        addOption(document.myform.selectschool, "044524", "縣立尖石國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '苗栗市') {
        addOption(document.myform.selectschool, "054501", "縣立苗栗國中", "");
        addOption(document.myform.selectschool, "054502", "縣立大倫國中", "");
        addOption(document.myform.selectschool, "054503", "縣立明仁國中", "");
        addOption(document.myform.selectschool, "051306", "私立建臺高中附設國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '頭屋鄉') {
        addOption(document.myform.selectschool, "054504", "縣立頭屋國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '公館鄉') {
        addOption(document.myform.selectschool, "054505", "縣立公館國中", "");
        addOption(document.myform.selectschool, "054506", "縣立鶴岡國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '銅鑼鄉') {
        addOption(document.myform.selectschool, "054507", "縣立文林國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '苑裡鎮') {
        addOption(document.myform.selectschool, "054510", "縣立致民國中", "");
        addOption(document.myform.selectschool, "054309", "縣立苑裡高中附設國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '通霄鎮') {
        addOption(document.myform.selectschool, "054511", "縣立通霄國中", "");
        addOption(document.myform.selectschool, "054512", "縣立南和國中", "");
        addOption(document.myform.selectschool, "054513", "縣立烏眉國中", "");
        addOption(document.myform.selectschool, "054514", "縣立啟新國中", "");
        addOption(document.myform.selectschool, "054534", "縣立福興武術國中(小)", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '西湖鄉') {
        addOption(document.myform.selectschool, "054515", "縣立西湖國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '頭份市') {
        addOption(document.myform.selectschool, "054516", "縣立頭份國中", "");
        addOption(document.myform.selectschool, "054518", "縣立文英國中", "");
        addOption(document.myform.selectschool, "054532", "縣立建國國中", "");
        addOption(document.myform.selectschool, "051305", "私立大成高中附設國中", "");
        addOption(document.myform.selectschool, "054317", "縣立興華高中附設國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '竹南鎮') {
        addOption(document.myform.selectschool, "054519", "縣立竹南國中", "");
        addOption(document.myform.selectschool, "054520", "縣立照南國中", "");
        addOption(document.myform.selectschool, "051302", "私立君毅高中附設國中", "");
        addOption(document.myform.selectschool, "054333", "縣立大同高中附設國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '三灣鄉') {
        addOption(document.myform.selectschool, "054521", "縣立三灣國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '南庄鄉') {
        addOption(document.myform.selectschool, "054522", "縣立南庄國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '造橋鄉') {
        addOption(document.myform.selectschool, "054523", "縣立造橋國中", "");
        addOption(document.myform.selectschool, "054524", "縣立大西國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '後龍鎮') {
        addOption(document.myform.selectschool, "054525", "縣立後龍國中", "");
        addOption(document.myform.selectschool, "054526", "縣立維真國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '大湖鄉') {
        addOption(document.myform.selectschool, "054527", "縣立大湖國中", "");
        addOption(document.myform.selectschool, "054528", "縣立南湖國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '獅潭鄉') {
        addOption(document.myform.selectschool, "054529", "縣立獅潭國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '泰安鄉') {
        addOption(document.myform.selectschool, "054531", "縣立泰安國中(小)", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '卓蘭鎮') {
        addOption(document.myform.selectschool, "050314", "國立卓蘭實驗高中附設國中", "");
        addOption(document.myform.selectschool, "051307", "私立全人實驗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '三義鄉') {
        addOption(document.myform.selectschool, "054308", "縣立三義高中附設國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '北斗鎮') {
        addOption(document.myform.selectschool, "074501", "縣立北斗國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '福興鄉') {
        addOption(document.myform.selectschool, "074502", "縣立鹿港國中", "");
        addOption(document.myform.selectschool, "074521", "縣立福興國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '鹿港鎮') {
        addOption(document.myform.selectschool, "074503", "縣立鹿鳴國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '線西鄉') {
        addOption(document.myform.selectschool, "074504", "縣立線西國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '彰化市') {
        addOption(document.myform.selectschool, "074505", "縣立陽明國中", "");
        addOption(document.myform.selectschool, "074506", "縣立彰安國中", "");
        addOption(document.myform.selectschool, "074507", "縣立彰德國中", "");
        addOption(document.myform.selectschool, "074538", "縣立彰興國中", "");
        addOption(document.myform.selectschool, "074540", "縣立彰泰國中", "");
        addOption(document.myform.selectschool, "074541", "縣立信義國中(小)", "");
        addOption(document.myform.selectschool, "071311", "私立精誠高中附設國中", "");
        addOption(document.myform.selectschool, "074308", "縣立彰化藝術高中附設國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '芬園鄉') {
        addOption(document.myform.selectschool, "074509", "縣立芬園國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '員林市') {
        addOption(document.myform.selectschool, "074510", "縣立員林國中", "");
        addOption(document.myform.selectschool, "074511", "縣立明倫國中", "");
        addOption(document.myform.selectschool, "074536", "縣立大同國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '二林鎮') {
        addOption(document.myform.selectschool, "074512", "縣立萬興國中", "");
        addOption(document.myform.selectschool, "074537", "縣立原斗國中", "");
        addOption(document.myform.selectschool, "074313", "縣立二林高中附設國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '竹塘鄉') {
        addOption(document.myform.selectschool, "074514", "縣立竹塘國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '大城鄉') {
        addOption(document.myform.selectschool, "074515", "縣立大城國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '芳苑鄉') {
        addOption(document.myform.selectschool, "074516", "縣立草湖國中", "");
        addOption(document.myform.selectschool, "074517", "縣立芳苑國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '溪湖鎮') {
        addOption(document.myform.selectschool, "074518", "縣立溪湖國中", "");
        addOption(document.myform.selectschool, "074339", "縣立成功高中附設國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '埔鹽鄉') {
        addOption(document.myform.selectschool, "074519", "縣立埔鹽國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '埔心鄉') {
        addOption(document.myform.selectschool, "074520", "縣立埔心國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '秀水鄉') {
        addOption(document.myform.selectschool, "074522", "縣立秀水國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '伸港鄉') {
        addOption(document.myform.selectschool, "074524", "縣立伸港國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '大村鄉') {
        addOption(document.myform.selectschool, "074525", "縣立大村國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '花壇鄉') {
        addOption(document.myform.selectschool, "074526", "縣立花壇國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '永靖鄉') {
        addOption(document.myform.selectschool, "074527", "縣立永靖國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '二水鄉') {
        addOption(document.myform.selectschool, "074529", "縣立二水國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '社頭鄉') {
        addOption(document.myform.selectschool, "074530", "縣立社頭國中", "");
        addOption(document.myform.selectschool, "070F02", "國立彰化啟智學校", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '田尾鄉') {
        addOption(document.myform.selectschool, "074531", "縣立田尾國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '溪州鄉') {
        addOption(document.myform.selectschool, "074532", "縣立溪州國中", "");
        addOption(document.myform.selectschool, "074533", "縣立溪陽國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '埤頭鄉') {
        addOption(document.myform.selectschool, "074534", "縣立埤頭國中", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '和美鎮') {
        addOption(document.myform.selectschool, "074535", "縣立和群國中", "");
        addOption(document.myform.selectschool, "074323", "縣立和美高中附設國中", "");
        addOption(document.myform.selectschool, "070F01", "國立和美實驗學校", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '田中鎮') {
        addOption(document.myform.selectschool, "071317", "私立文興高中附設國中", "");
        addOption(document.myform.selectschool, "074328", "縣立田中高中附設國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '埔里鎮') {
        addOption(document.myform.selectschool, "081502", "私立均頭國中(小)", "");
        addOption(document.myform.selectschool, "084505", "縣立埔里國中", "");
        addOption(document.myform.selectschool, "084506", "縣立大成國中", "");
        addOption(document.myform.selectschool, "084507", "縣立宏仁國中", "");
        addOption(document.myform.selectschool, "081314", "私立普台高中附設國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '南投市') {
        addOption(document.myform.selectschool, "084501", "縣立南投國中", "");
        addOption(document.myform.selectschool, "084502", "縣立南崗國中", "");
        addOption(document.myform.selectschool, "084503", "縣立中興國中", "");
        addOption(document.myform.selectschool, "084504", "縣立鳳鳴國中", "");
        addOption(document.myform.selectschool, "084532", "縣立營北國中", "");
        addOption(document.myform.selectschool, "081311", "私立五育高中附設國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '草屯鎮') {
        addOption(document.myform.selectschool, "084508", "縣立草屯國中", "");
        addOption(document.myform.selectschool, "084510", "縣立日新國中", "");
        addOption(document.myform.selectschool, "084309", "縣立旭光高中附設國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '竹山鎮') {
        addOption(document.myform.selectschool, "084511", "縣立竹山國中", "");
        addOption(document.myform.selectschool, "084512", "縣立延和國中", "");
        addOption(document.myform.selectschool, "084513", "縣立社寮國中", "");
        addOption(document.myform.selectschool, "084514", "縣立瑞竹國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '集集鎮') {
        addOption(document.myform.selectschool, "084515", "縣立集集國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '名間鄉') {
        addOption(document.myform.selectschool, "084516", "縣立名間國中", "");
        addOption(document.myform.selectschool, "084517", "縣立三光國中", "");
        addOption(document.myform.selectschool, "081313", "私立弘明實驗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '鹿谷鄉') {
        addOption(document.myform.selectschool, "084518", "縣立鹿谷國中", "");
        addOption(document.myform.selectschool, "084519", "縣立瑞峰國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '中寮鄉') {
        addOption(document.myform.selectschool, "084520", "縣立中寮國中", "");
        addOption(document.myform.selectschool, "084521", "縣立爽文國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '魚池鄉') {
        addOption(document.myform.selectschool, "084522", "縣立魚池國中", "");
        addOption(document.myform.selectschool, "084523", "縣立明潭國中", "");
        addOption(document.myform.selectschool, "081312", "私立三育高中附設國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '國姓鄉') {
        addOption(document.myform.selectschool, "084524", "縣立國姓國中", "");
        addOption(document.myform.selectschool, "084525", "縣立北梅國中", "");
        addOption(document.myform.selectschool, "084526", "縣立北山國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '水里鄉') {
        addOption(document.myform.selectschool, "084527", "縣立水里國中", "");
        addOption(document.myform.selectschool, "084528", "縣立民和國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '信義鄉') {
        addOption(document.myform.selectschool, "084529", "縣立信義國中", "");
        addOption(document.myform.selectschool, "084530", "縣立同富國中", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '仁愛鄉') {
        addOption(document.myform.selectschool, "084531", "縣立仁愛國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '林內鄉') {
        addOption(document.myform.selectschool, "091502", "私立淵明國中", "");
        addOption(document.myform.selectschool, "094530", "縣立林內國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '西螺鎮') {
        addOption(document.myform.selectschool, "091503", "私立東南國中(代用)", "");
        addOption(document.myform.selectschool, "094519", "縣立西螺國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '古坑鄉') {
        addOption(document.myform.selectschool, "091505", "私立福智國中", "");
        addOption(document.myform.selectschool, "094308", "縣立古坑華德福實驗高級中學附設國中", "");
        addOption(document.myform.selectschool, "094512", "縣立古坑國中(小)", "");
        addOption(document.myform.selectschool, "094527", "縣立東和國中", "");
        addOption(document.myform.selectschool, "094544", "縣立樟湖生態國中(小)", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '斗南鎮') {
        addOption(document.myform.selectschool, "094502", "縣立東明國中", "");
        addOption(document.myform.selectschool, "094301", "縣立斗南高中附設國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '大埤鄉') {
        addOption(document.myform.selectschool, "094503", "縣立大埤國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '四湖鄉') {
        addOption(document.myform.selectschool, "094504", "縣立飛沙國中", "");
        addOption(document.myform.selectschool, "094505", "縣立四湖國中", "");
        addOption(document.myform.selectschool, "091311", "私立文生高中附設國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '水林鄉') {
        addOption(document.myform.selectschool, "094506", "縣立水林國中", "");
        addOption(document.myform.selectschool, "094526", "縣立蔦松國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '二崙鄉') {
        addOption(document.myform.selectschool, "094508", "縣立二崙國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '褒忠鄉') {
        addOption(document.myform.selectschool, "094509", "縣立褒忠國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '莿桐鄉') {
        addOption(document.myform.selectschool, "094510", "縣立莿桐國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '崙背鄉') {
        addOption(document.myform.selectschool, "094511", "縣立崙背國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '東勢鄉') {
        addOption(document.myform.selectschool, "094513", "縣立東勢國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '元長鄉') {
        addOption(document.myform.selectschool, "094514", "縣立元長國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '斗六市') {
        addOption(document.myform.selectschool, "094515", "縣立斗六國中", "");
        addOption(document.myform.selectschool, "094516", "縣立雲林國中", "");
        addOption(document.myform.selectschool, "094529", "縣立石榴國中", "");
        addOption(document.myform.selectschool, "091308", "私立正心高中附設國中", "");
        addOption(document.myform.selectschool, "091320", "雲林縣維多利亞實驗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '虎尾鎮') {
        addOption(document.myform.selectschool, "094517", "縣立虎尾國中", "");
        addOption(document.myform.selectschool, "094518", "縣立崇德國中", "");
        addOption(document.myform.selectschool, "094543", "縣立東仁國中", "");
        addOption(document.myform.selectschool, "091316", "私立揚子高中附設國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '北港鎮') {
        addOption(document.myform.selectschool, "094520", "縣立北港國中", "");
        addOption(document.myform.selectschool, "094521", "縣立建國國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '口湖鄉') {
        addOption(document.myform.selectschool, "094522", "縣立宜梧國中", "");
        addOption(document.myform.selectschool, "094523", "縣立口湖國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '臺西鄉') {
        addOption(document.myform.selectschool, "094524", "縣立臺西國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '土庫鎮') {
        addOption(document.myform.selectschool, "094525", "縣立土庫國中", "");
        addOption(document.myform.selectschool, "094528", "縣立馬光國中", "");
        addOption(document.myform.selectschool, "091307", "私立永年高中附設國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '斗六市鎮') {
        addOption(document.myform.selectschool, "091320", "私立維多利亞實驗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '麥寮鄉') {
        addOption(document.myform.selectschool, "094307", "縣立麥寮高中附設國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '朴子市') {
        addOption(document.myform.selectschool, "104501", "縣立朴子國中", "");
        addOption(document.myform.selectschool, "104502", "縣立東石國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '布袋鎮') {
        addOption(document.myform.selectschool, "104503", "縣立布袋國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '東石鄉') {
        addOption(document.myform.selectschool, "104504", "縣立過溝國中", "");
        addOption(document.myform.selectschool, "104515", "縣立東榮國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '大林鎮') {
        addOption(document.myform.selectschool, "104505", "縣立大林國中", "");
        addOption(document.myform.selectschool, "101303", "私立同濟高中附設國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '新港鄉') {
        addOption(document.myform.selectschool, "104506", "縣立新港國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '民雄鄉') {
        addOption(document.myform.selectschool, "104507", "縣立民雄國中", "");
        addOption(document.myform.selectschool, "104508", "縣立大吉國中", "");
        addOption(document.myform.selectschool, "101304", "私立協同高中附設國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '六腳鄉') {
        addOption(document.myform.selectschool, "104509", "縣立六嘉國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '太保市') {
        addOption(document.myform.selectschool, "104511", "縣立太保國中", "");
        addOption(document.myform.selectschool, "104512", "縣立嘉新國中", "");
        addOption(document.myform.selectschool, "104326", "縣立永慶高中附設國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '溪口鄉') {
        addOption(document.myform.selectschool, "104513", "縣立溪口國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '鹿草鄉') {
        addOption(document.myform.selectschool, "104514", "縣立鹿草國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '水上鄉') {
        addOption(document.myform.selectschool, "104516", "縣立水上國中", "");
        addOption(document.myform.selectschool, "104517", "縣立忠和國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '中埔鄉') {
        addOption(document.myform.selectschool, "104518", "縣立中埔國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '竹崎鄉') {
        addOption(document.myform.selectschool, "104520", "縣立昇平國中", "");
        addOption(document.myform.selectschool, "104319", "縣立竹崎高中附設國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '義竹鄉') {
        addOption(document.myform.selectschool, "104521", "縣立義竹國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '番路鄉') {
        addOption(document.myform.selectschool, "104522", "縣立民和國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '梅山鄉') {
        addOption(document.myform.selectschool, "104523", "縣立梅山國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '大埔鄉') {
        addOption(document.myform.selectschool, "104524", "縣立大埔國中", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '阿里山鄉') {
        addOption(document.myform.selectschool, "104526", "縣立阿里山國中(小)", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '崁頂鄉') {
        addOption(document.myform.selectschool, "131501", "私立南榮國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '屏東市') {
        addOption(document.myform.selectschool, "134501", "縣立明正國中", "");
        addOption(document.myform.selectschool, "134502", "縣立中正國中", "");
        addOption(document.myform.selectschool, "134503", "縣立公正國中", "");
        addOption(document.myform.selectschool, "134505", "縣立鶴聲國中", "");
        addOption(document.myform.selectschool, "134506", "縣立至正國中", "");
        addOption(document.myform.selectschool, "131308", "私立陸興高中附設國中", "");
        addOption(document.myform.selectschool, "134304", "縣立大同高中附設國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '長治鄉') {
        addOption(document.myform.selectschool, "134507", "縣立長治國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '麟洛鄉') {
        addOption(document.myform.selectschool, "134508", "縣立麟洛國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '九如鄉') {
        addOption(document.myform.selectschool, "134509", "縣立九如國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '里港鄉') {
        addOption(document.myform.selectschool, "134510", "縣立里港國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '鹽埔鄉') {
        addOption(document.myform.selectschool, "134511", "縣立鹽埔國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '高樹鄉') {
        addOption(document.myform.selectschool, "134512", "縣立高樹國中", "");
        addOption(document.myform.selectschool, "134513", "縣立高泰國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '內埔鄉') {
        addOption(document.myform.selectschool, "134514", "縣立內埔國中", "");
        addOption(document.myform.selectschool, "134515", "縣立崇文國中", "");
        addOption(document.myform.selectschool, "131311", "私立美和高中附設國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '竹田鄉') {
        addOption(document.myform.selectschool, "134516", "縣立竹田國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '潮州鎮') {
        addOption(document.myform.selectschool, "134517", "縣立潮州國中", "");
        addOption(document.myform.selectschool, "134518", "縣立光春國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '萬巒鄉') {
        addOption(document.myform.selectschool, "134519", "縣立萬巒國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '新埤鄉') {
        addOption(document.myform.selectschool, "134520", "縣立新埤國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '萬丹鄉') {
        addOption(document.myform.selectschool, "134522", "縣立萬丹國中", "");
        addOption(document.myform.selectschool, "134542", "縣立萬新國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '新園鄉') {
        addOption(document.myform.selectschool, "134523", "縣立新園國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '林邊鄉') {
        addOption(document.myform.selectschool, "134525", "縣立林邊國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '南州鄉') {
        addOption(document.myform.selectschool, "134526", "縣立南州國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '佳冬鄉') {
        addOption(document.myform.selectschool, "134527", "縣立佳冬國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '琉球鄉') {
        addOption(document.myform.selectschool, "134528", "縣立琉球國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '車城鄉') {
        addOption(document.myform.selectschool, "134530", "縣立車城國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '恆春鎮') {
        addOption(document.myform.selectschool, "134531", "縣立恆春國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '滿州鄉') {
        addOption(document.myform.selectschool, "134532", "縣立滿州國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '瑪家鄉') {
        addOption(document.myform.selectschool, "134533", "縣立瑪家國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '泰武鄉') {
        addOption(document.myform.selectschool, "134535", "縣立泰武國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '牡丹鄉') {
        addOption(document.myform.selectschool, "134536", "縣立牡丹國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '枋山鄉') {
        addOption(document.myform.selectschool, "134537", "縣立獅子國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '東港鎮') {
        addOption(document.myform.selectschool, "134538", "縣立東新國中", "");
        addOption(document.myform.selectschool, "134324", "縣立東港高中附設國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '枋寮鄉') {
        addOption(document.myform.selectschool, "134321", "縣立枋寮高中附設國中", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '來義鄉') {
        addOption(document.myform.selectschool, "134334", "縣立來義高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '臺東市') {
        addOption(document.myform.selectschool, "141501", "私立均一國中(小)", "");
        addOption(document.myform.selectschool, "144501", "縣立新生國中", "");
        addOption(document.myform.selectschool, "144502", "縣立東海國中", "");
        addOption(document.myform.selectschool, "144503", "縣立寶桑國中", "");
        addOption(document.myform.selectschool, "144504", "縣立卑南國中", "");
        addOption(document.myform.selectschool, "144505", "縣立豐田國中", "");
        addOption(document.myform.selectschool, "144506", "縣立知本國中", "");
        addOption(document.myform.selectschool, "140301", "國立臺東大學附屬體育高中附設國中", "");
        addOption(document.myform.selectschool, "141307", "私立育仁高中附設國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '卑南鄉') {
        addOption(document.myform.selectschool, "144507", "縣立初鹿國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '鹿野鄉') {
        addOption(document.myform.selectschool, "144508", "縣立鹿野國中", "");
        addOption(document.myform.selectschool, "144509", "縣立瑞源國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '關山鎮') {
        addOption(document.myform.selectschool, "144510", "縣立關山國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '池上鄉') {
        addOption(document.myform.selectschool, "144511", "縣立池上國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '太麻里鄉') {
        addOption(document.myform.selectschool, "144512", "縣立大王國中", "");
        addOption(document.myform.selectschool, "144513", "縣立賓茂國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '大武鄉') {
        addOption(document.myform.selectschool, "144514", "縣立大武國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '東河鄉') {
        addOption(document.myform.selectschool, "144515", "縣立都蘭國中", "");
        addOption(document.myform.selectschool, "144516", "縣立泰源國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '成功鎮') {
        addOption(document.myform.selectschool, "144517", "縣立新港國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '長濱鄉') {
        addOption(document.myform.selectschool, "144518", "縣立長濱國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '延平鄉') {
        addOption(document.myform.selectschool, "144519", "縣立桃源國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '海端鄉') {
        addOption(document.myform.selectschool, "144520", "縣立海端國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '綠島鄉') {
        addOption(document.myform.selectschool, "144521", "縣立綠島國中", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '蘭嶼鄉') {
        addOption(document.myform.selectschool, "144322", "縣立蘭嶼高中附設國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '玉里鎮') {
        addOption(document.myform.selectschool, "154501", "縣立玉里國中", "");
        addOption(document.myform.selectschool, "154502", "縣立玉東國中", "");
        addOption(document.myform.selectschool, "154503", "縣立三民國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '花蓮市') {
        addOption(document.myform.selectschool, "154504", "縣立美崙國中", "");
        addOption(document.myform.selectschool, "154505", "縣立花崗國中", "");
        addOption(document.myform.selectschool, "154506", "縣立國風國中", "");
        addOption(document.myform.selectschool, "154522", "縣立自強國中", "");
        addOption(document.myform.selectschool, "151312", "財團法人慈濟大學附中附設國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '新城鄉') {
        addOption(document.myform.selectschool, "154507", "縣立秀林國中", "");
        addOption(document.myform.selectschool, "154508", "縣立新城國中", "");
        addOption(document.myform.selectschool, "151306", "私立海星高中附設國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '吉安鄉') {
        addOption(document.myform.selectschool, "154509", "縣立吉安國中", "");
        addOption(document.myform.selectschool, "154510", "縣立宜昌國中", "");
        addOption(document.myform.selectschool, "154523", "縣立化仁國中", "");
        addOption(document.myform.selectschool, "150F01", "國立花蓮啟智學校", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '壽豐鄉') {
        addOption(document.myform.selectschool, "154511", "縣立壽豐國中", "");
        addOption(document.myform.selectschool, "154512", "縣立平和國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '光復鄉') {
        addOption(document.myform.selectschool, "154513", "縣立光復國中", "");
        addOption(document.myform.selectschool, "154514", "縣立富源國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '鳳林鎮') {
        addOption(document.myform.selectschool, "154515", "縣立鳳林國中", "");
        addOption(document.myform.selectschool, "154516", "縣立萬榮國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '富里鄉') {
        addOption(document.myform.selectschool, "154517", "縣立富里國中", "");
        addOption(document.myform.selectschool, "154518", "縣立富北國中", "");
        addOption(document.myform.selectschool, "154521", "縣立東里國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '豐濱鄉') {
        addOption(document.myform.selectschool, "154519", "縣立豐濱國中", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '瑞穗鄉') {
        addOption(document.myform.selectschool, "154520", "縣立瑞穗國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '馬公市') {
        addOption(document.myform.selectschool, "164501", "縣立馬公國中", "");
        addOption(document.myform.selectschool, "164502", "縣立中正國中", "");
        addOption(document.myform.selectschool, "164503", "縣立澎南國中", "");
        addOption(document.myform.selectschool, "164513", "縣立文光國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '湖西鄉') {
        addOption(document.myform.selectschool, "164504", "縣立湖西國中", "");
        addOption(document.myform.selectschool, "164505", "縣立志清國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '白沙鄉鎮') {
        addOption(document.myform.selectschool, "164506", "縣立鎮海國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '白沙鄉') {
        addOption(document.myform.selectschool, "164507", "縣立白沙國中", "");
        addOption(document.myform.selectschool, "164508", "縣立吉貝國中", "");
        addOption(document.myform.selectschool, "164514", "縣立鳥嶼國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '西嶼鄉') {
        addOption(document.myform.selectschool, "164509", "縣立西嶼國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '望安鄉') {
        addOption(document.myform.selectschool, "164510", "縣立望安國中", "");
        addOption(document.myform.selectschool, "164511", "縣立將澳國中", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '七美鄉') {
        addOption(document.myform.selectschool, "164512", "縣立七美國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '信義區') {
        addOption(document.myform.selectschool, "173503", "市立信義國中", "");
        addOption(document.myform.selectschool, "173509", "市立成功國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '中山區') {
        addOption(document.myform.selectschool, "171308", "私立聖心高中附設國中", "");
        addOption(document.myform.selectschool, "173304", "市立中山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '中正區') {
        addOption(document.myform.selectschool, "173505", "市立中正國中", "");
        addOption(document.myform.selectschool, "173510", "市立正濱國中", "");
        addOption(document.myform.selectschool, "171306", "私立二信高中附設國中", "");
        addOption(document.myform.selectschool, "173314", "市立八斗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '七堵區') {
        addOption(document.myform.selectschool, "173501", "市立明德國中", "");
        addOption(document.myform.selectschool, "173513", "市立百福國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '仁愛區') {
        addOption(document.myform.selectschool, "173502", "市立銘傳國中", "");
        addOption(document.myform.selectschool, "173508", "市立南榮國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '安樂區') {
        addOption(document.myform.selectschool, "173512", "市立建德國中", "");
        addOption(document.myform.selectschool, "173516", "市立武崙國中", "");
        addOption(document.myform.selectschool, "173306", "市立安樂高中附設國中", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '暖暖區') {
        addOption(document.myform.selectschool, "173515", "市立碇內國中", "");
        addOption(document.myform.selectschool, "173307", "市立暖暖高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹市' && document.myform.selectdistrict.value == '北區') {
        addOption(document.myform.selectschool, "183503", "市立光華國中", "");
        addOption(document.myform.selectschool, "183508", "市立南華國中", "");
        addOption(document.myform.selectschool, "183515", "市立竹光國中", "");
        addOption(document.myform.selectschool, "181307", "私立磐石高中附設國中", "");
        addOption(document.myform.selectschool, "183306", "市立成德高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "181501", "私立矽谷國中(小)", "");
        addOption(document.myform.selectschool, "183501", "市立建華國中", "");
        addOption(document.myform.selectschool, "183502", "市立培英國中", "");
        addOption(document.myform.selectschool, "183504", "市立育賢國中", "");
        addOption(document.myform.selectschool, "183505", "市立光武國中", "");
        addOption(document.myform.selectschool, "183510", "市立三民國中", "");
        addOption(document.myform.selectschool, "183514", "市立新科國中", "");
        addOption(document.myform.selectschool, "181305", "私立光復高中附設國中", "");
        addOption(document.myform.selectschool, "181306", "私立曙光女中附設國中", "");
        addOption(document.myform.selectschool, "183313", "市立建功高中附設國中", "");
        addOption(document.myform.selectschool, "180301", "國立科學工業園區實驗高中附設國中", "");
    }
    if (document.myform.selectcity.value == '新竹市' && document.myform.selectdistrict.value == '香山區') {
        addOption(document.myform.selectschool, "183509", "市立富禮國中", "");
        addOption(document.myform.selectschool, "183511", "市立內湖國中", "");
        addOption(document.myform.selectschool, "183512", "市立虎林國中", "");
        addOption(document.myform.selectschool, "183307", "市立香山高中附設國中", "");
    }
    if (document.myform.selectcity.value == '嘉義市' && document.myform.selectdistrict.value == '西區') {
        addOption(document.myform.selectschool, "203505", "市立民生國中", "");
        addOption(document.myform.selectschool, "203506", "市立玉山國中", "");
        addOption(document.myform.selectschool, "203508", "市立北園國中", "");
        addOption(document.myform.selectschool, "200F01", "國立嘉義啟智學校", "");
    }
    if (document.myform.selectcity.value == '嘉義市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "203501", "市立大業國中", "");
        addOption(document.myform.selectschool, "203502", "市立北興國中", "");
        addOption(document.myform.selectschool, "203503", "市立嘉義國中", "");
        addOption(document.myform.selectschool, "203504", "市立南興國中", "");
        addOption(document.myform.selectschool, "203507", "市立蘭潭國中", "");
        addOption(document.myform.selectschool, "201304", "私立興華高中附設國中", "");
        addOption(document.myform.selectschool, "201310", "私立嘉華高中附設國中", "");
        addOption(document.myform.selectschool, "201312", "私立輔仁高中附設國中", "");
        addOption(document.myform.selectschool, "201313", "私立宏仁女中附設國中", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金城鎮') {
        addOption(document.myform.selectschool, "714501", "縣立金城國中", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金湖鎮') {
        addOption(document.myform.selectschool, "714502", "縣立金湖國中", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金寧鄉') {
        addOption(document.myform.selectschool, "714503", "縣立金寧國中(小)", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金沙鎮') {
        addOption(document.myform.selectschool, "714504", "縣立金沙國中", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '烈嶼鄉') {
        addOption(document.myform.selectschool, "714505", "縣立烈嶼國中", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '南竿鄉') {
        addOption(document.myform.selectschool, "724501", "縣立介壽國中(小)", "");
        addOption(document.myform.selectschool, "724502", "縣立中正國中(小)", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '北竿鄉') {
        addOption(document.myform.selectschool, "724503", "縣立中山國中", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '莒光鄉') {
        addOption(document.myform.selectschool, "724504", "縣立敬恆國中(小)", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '東引鄉') {
        addOption(document.myform.selectschool, "724505", "縣立東引國中(小)", "");
    }

}
////////////////// 

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
