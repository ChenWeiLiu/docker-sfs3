
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
    // this function is used to fill the category list on load
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
        addOption(document.myform.selectdistrict, "東區", "東區", "");
        addOption(document.myform.selectdistrict, "南區", "南區", "");
        addOption(document.myform.selectdistrict, "北區", "北區", "");
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
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '永和區') {
        addOption(document.myform.selectschool, "011601", "私立育才國小", "");
        addOption(document.myform.selectschool, "011603", "私立及人國小", "");
        addOption(document.myform.selectschool, "011604", "私立竹林國小", "");
        addOption(document.myform.selectschool, "014641", "市立永和國小", "");
        addOption(document.myform.selectschool, "014642", "市立秀朗國小", "");
        addOption(document.myform.selectschool, "014643", "市立頂溪國小", "");
        addOption(document.myform.selectschool, "014644", "市立網溪國小", "");
        addOption(document.myform.selectschool, "014645", "市立永平國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '八里區') {
        addOption(document.myform.selectschool, "011602", "私立聖心國小", "");
        addOption(document.myform.selectschool, "014746", "市立八里國小", "");
        addOption(document.myform.selectschool, "014747", "市立長坑國小", "");
        addOption(document.myform.selectschool, "014748", "市立米倉國小", "");
        addOption(document.myform.selectschool, "014805", "市立大崁國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '烏來區') {
        addOption(document.myform.selectschool, "011606", "私立信賢種籽親子實小", "");
        addOption(document.myform.selectschool, "014684", "市立烏來國(中)小", "");
        addOption(document.myform.selectschool, "014685", "市立福山國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '土城區') {
        addOption(document.myform.selectschool, "011607", "私立裕德國(中)小", "");
        addOption(document.myform.selectschool, "014646", "市立土城國小", "");
        addOption(document.myform.selectschool, "014647", "市立清水國小", "");
        addOption(document.myform.selectschool, "014648", "市立頂埔國小", "");
        addOption(document.myform.selectschool, "014649", "市立廣福國小", "");
        addOption(document.myform.selectschool, "014773", "市立樂利國小", "");
        addOption(document.myform.selectschool, "014774", "市立安和國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '板橋區') {
        addOption(document.myform.selectschool, "014601", "市立板橋國小", "");
        addOption(document.myform.selectschool, "014602", "市立國光國小", "");
        addOption(document.myform.selectschool, "014603", "市立新埔國小", "");
        addOption(document.myform.selectschool, "014604", "市立埔墘國小", "");
        addOption(document.myform.selectschool, "014605", "市立莒光國小", "");
        addOption(document.myform.selectschool, "014606", "市立後埔國小", "");
        addOption(document.myform.selectschool, "014607", "市立海山國小", "");
        addOption(document.myform.selectschool, "014608", "市立江翠國小", "");
        addOption(document.myform.selectschool, "014610", "市立文聖國小", "");
        addOption(document.myform.selectschool, "014611", "市立沙崙國小", "");
        addOption(document.myform.selectschool, "014612", "市立文德國小", "");
        addOption(document.myform.selectschool, "014766", "市立中山國小", "");
        addOption(document.myform.selectschool, "014768", "市立實踐國小", "");
        addOption(document.myform.selectschool, "014769", "市立大觀國小", "");
        addOption(document.myform.selectschool, "014770", "市立溪洲國小", "");
        addOption(document.myform.selectschool, "014771", "市立信義國小", "");
        addOption(document.myform.selectschool, "014772", "市立重慶國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '樹林區') {
        addOption(document.myform.selectschool, "014613", "市立樹林國小", "");
        addOption(document.myform.selectschool, "014614", "市立文林國小", "");
        addOption(document.myform.selectschool, "014615", "市立大同國小", "");
        addOption(document.myform.selectschool, "014616", "市立武林國小", "");
        addOption(document.myform.selectschool, "014617", "市立山佳國小", "");
        addOption(document.myform.selectschool, "014618", "市立育德國小", "");
        addOption(document.myform.selectschool, "014619", "市立柑園國小", "");
        addOption(document.myform.selectschool, "014767", "市立三多國小", "");
        addOption(document.myform.selectschool, "014775", "市立彭福國小", "");
        addOption(document.myform.selectschool, "014776", "市立育林國小", "");
        addOption(document.myform.selectschool, "014814", "市立桃子腳國(中)小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '鶯歌區') {
        addOption(document.myform.selectschool, "014620", "市立鶯歌國小", "");
        addOption(document.myform.selectschool, "014621", "市立二橋國小", "");
        addOption(document.myform.selectschool, "014622", "市立中湖國小", "");
        addOption(document.myform.selectschool, "014623", "市立鳳鳴國小", "");
        addOption(document.myform.selectschool, "014777", "市立建國國小", "");
        addOption(document.myform.selectschool, "014804", "市立永吉國小", "");
        addOption(document.myform.selectschool, "014807", "市立昌福國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '三峽區') {
        addOption(document.myform.selectschool, "014624", "市立三峽國小", "");
        addOption(document.myform.selectschool, "014625", "市立大埔國小", "");
        addOption(document.myform.selectschool, "014626", "市立民義國小", "");
        addOption(document.myform.selectschool, "014627", "市立成福國小", "");
        addOption(document.myform.selectschool, "014628", "市立大成國小", "");
        addOption(document.myform.selectschool, "014629", "市立建安國小", "");
        addOption(document.myform.selectschool, "014630", "市立插角國小", "");
        addOption(document.myform.selectschool, "014631", "市立有木國小", "");
        addOption(document.myform.selectschool, "014632", "市立五寮國小", "");
        addOption(document.myform.selectschool, "014778", "市立安溪國小", "");
        addOption(document.myform.selectschool, "014799", "市立介壽國小", "");
        addOption(document.myform.selectschool, "014806", "市立中園國小", "");
        addOption(document.myform.selectschool, "014815", "市立龍埔國小", "");
        addOption(document.myform.selectschool, "013601", "市立北大國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '中和區') {
        addOption(document.myform.selectschool, "014633", "市立中和國小", "");
        addOption(document.myform.selectschool, "014634", "市立復興國小", "");
        addOption(document.myform.selectschool, "014635", "市立興南國小", "");
        addOption(document.myform.selectschool, "014636", "市立秀山國小", "");
        addOption(document.myform.selectschool, "014637", "市立積穗國小", "");
        addOption(document.myform.selectschool, "014638", "市立自強國小", "");
        addOption(document.myform.selectschool, "014639", "市立錦和國小", "");
        addOption(document.myform.selectschool, "014640", "市立景新國小", "");
        addOption(document.myform.selectschool, "014796", "市立光復國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '汐止區') {
        addOption(document.myform.selectschool, "014650", "市立汐止國小", "");
        addOption(document.myform.selectschool, "014651", "市立長安國小", "");
        addOption(document.myform.selectschool, "014652", "市立保長國小", "");
        addOption(document.myform.selectschool, "014653", "市立崇德國小", "");
        addOption(document.myform.selectschool, "014654", "市立北港國小", "");
        addOption(document.myform.selectschool, "014655", "市立北峰國小", "");
        addOption(document.myform.selectschool, "014656", "市立東山國小", "");
        addOption(document.myform.selectschool, "014657", "市立白雲國小", "");
        addOption(document.myform.selectschool, "014779", "市立樟樹國小", "");
        addOption(document.myform.selectschool, "014797", "市立秀峰國小", "");
        addOption(document.myform.selectschool, "014798", "市立金龍國小", "");
        addOption(document.myform.selectschool, "014811", "市立青山國(中)小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '萬里區') {
        addOption(document.myform.selectschool, "014658", "市立萬里國小", "");
        addOption(document.myform.selectschool, "014659", "市立野柳國小", "");
        addOption(document.myform.selectschool, "014660", "市立大鵬國小", "");
        addOption(document.myform.selectschool, "014661", "市立大坪國小", "");
        addOption(document.myform.selectschool, "014662", "市立崁腳國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '金山區') {
        addOption(document.myform.selectschool, "014663", "市立金山國小", "");
        addOption(document.myform.selectschool, "014664", "市立中角國小", "");
        addOption(document.myform.selectschool, "014665", "市立三和國小", "");
        addOption(document.myform.selectschool, "014780", "市立金美國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '平溪區') {
        addOption(document.myform.selectschool, "014709", "市立平溪國小", "");
        addOption(document.myform.selectschool, "014710", "市立菁桐國小", "");
        addOption(document.myform.selectschool, "014711", "市立十分國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '新店區') {
        addOption(document.myform.selectschool, "014666", "市立新店國小", "");
        addOption(document.myform.selectschool, "014667", "市立直潭國小", "");
        addOption(document.myform.selectschool, "014668", "市立青潭國小", "");
        addOption(document.myform.selectschool, "014669", "市立雙峰國小", "");
        addOption(document.myform.selectschool, "014670", "市立大豐國小", "");
        addOption(document.myform.selectschool, "014671", "市立中正國小", "");
        addOption(document.myform.selectschool, "014672", "市立安坑國小", "");
        addOption(document.myform.selectschool, "014673", "市立雙城國小", "");
        addOption(document.myform.selectschool, "014674", "市立屈尺國小", "");
        addOption(document.myform.selectschool, "014675", "市立龜山國小", "");
        addOption(document.myform.selectschool, "014781", "市立新和國小", "");
        addOption(document.myform.selectschool, "014794", "市立北新國小", "");
        addOption(document.myform.selectschool, "014813", "市立達觀國(中)小", "");
        addOption(document.myform.selectschool, "011302", "私立康橋實驗高中附設國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '深坑區') {
        addOption(document.myform.selectschool, "014676", "市立深坑國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '石碇區') {
        addOption(document.myform.selectschool, "014677", "市立石碇國小", "");
        addOption(document.myform.selectschool, "014678", "市立和平國小", "");
        addOption(document.myform.selectschool, "014679", "市立永定國小", "");
        addOption(document.myform.selectschool, "014680", "市立雲海國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '坪林區') {
        addOption(document.myform.selectschool, "014682", "市立坪林國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '瑞芳區') {
        addOption(document.myform.selectschool, "014686", "市立瑞芳國小", "");
        addOption(document.myform.selectschool, "014687", "市立義方國小", "");
        addOption(document.myform.selectschool, "014688", "市立瑞柑國小", "");
        addOption(document.myform.selectschool, "014689", "市立瑞濱國小", "");
        addOption(document.myform.selectschool, "014690", "市立九份國小", "");
        addOption(document.myform.selectschool, "014691", "市立瓜山國小", "");
        addOption(document.myform.selectschool, "014692", "市立濂洞國小", "");
        addOption(document.myform.selectschool, "014693", "市立猴硐國小", "");
        addOption(document.myform.selectschool, "014694", "市立瑞亭國小", "");
        addOption(document.myform.selectschool, "014695", "市立吉慶國小", "");
        addOption(document.myform.selectschool, "014696", "市立鼻頭國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '雙溪區') {
        addOption(document.myform.selectschool, "014697", "市立雙溪國小", "");
        addOption(document.myform.selectschool, "014698", "市立柑林國小", "");
        addOption(document.myform.selectschool, "014699", "市立上林國小", "");
        addOption(document.myform.selectschool, "014700", "市立牡丹國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '貢寮區') {
        addOption(document.myform.selectschool, "014702", "市立貢寮國小", "");
        addOption(document.myform.selectschool, "014703", "市立福隆國小", "");
        addOption(document.myform.selectschool, "014704", "市立澳底國小", "");
        addOption(document.myform.selectschool, "014706", "市立和美國小", "");
        addOption(document.myform.selectschool, "014708", "市立福連國小", "");
        addOption(document.myform.selectschool, "014809", "市立豐珠國(中)小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '平溪區') {
        addOption(document.myform.selectschool, "014709", "市立平溪國小", "");
        addOption(document.myform.selectschool, "014710", "市立菁桐國小", "");
        addOption(document.myform.selectschool, "014711", "市立十分國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '淡水區') {
        addOption(document.myform.selectschool, "014712", "市立淡水國小", "");
        addOption(document.myform.selectschool, "014713", "市立育英國小", "");
        addOption(document.myform.selectschool, "014714", "市立文化國小", "");
        addOption(document.myform.selectschool, "014715", "市立天生國小", "");
        addOption(document.myform.selectschool, "014716", "市立水源國小", "");
        addOption(document.myform.selectschool, "014717", "市立興仁國小", "");
        addOption(document.myform.selectschool, "014718", "市立忠山國小", "");
        addOption(document.myform.selectschool, "014719", "市立屯山國小", "");
        addOption(document.myform.selectschool, "014720", "市立中泰國小", "");
        addOption(document.myform.selectschool, "014721", "市立坪頂國小", "");
        addOption(document.myform.selectschool, "014722", "市立竹圍國小", "");
        addOption(document.myform.selectschool, "014782", "市立鄧公國小", "");
        addOption(document.myform.selectschool, "014783", "市立新興國小", "");
        addOption(document.myform.selectschool, "014817", "市立新市國小", "");
        addOption(document.myform.selectschool, "011301", "私立淡江高中附設國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '石門區') {
        addOption(document.myform.selectschool, "014723", "市立石門國小", "");
        addOption(document.myform.selectschool, "014724", "市立乾華國小", "");
        addOption(document.myform.selectschool, "014725", "市立老梅國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '三芝區') {
        addOption(document.myform.selectschool, "014726", "市立三芝國小", "");
        addOption(document.myform.selectschool, "014727", "市立橫山國小", "");
        addOption(document.myform.selectschool, "014728", "市立興華國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '新莊區') {
        addOption(document.myform.selectschool, "014729", "市立新莊國小", "");
        addOption(document.myform.selectschool, "014730", "市立中港國小", "");
        addOption(document.myform.selectschool, "014731", "市立思賢國小", "");
        addOption(document.myform.selectschool, "014732", "市立頭前國小", "");
        addOption(document.myform.selectschool, "014733", "市立國泰國小", "");
        addOption(document.myform.selectschool, "014734", "市立豐年國小", "");
        addOption(document.myform.selectschool, "014735", "市立丹鳳國小", "");
        addOption(document.myform.selectschool, "014736", "市立光華國小", "");
        addOption(document.myform.selectschool, "014737", "市立民安國小", "");
        addOption(document.myform.selectschool, "014738", "市立昌隆國小", "");
        addOption(document.myform.selectschool, "014765", "市立興化國小", "");
        addOption(document.myform.selectschool, "014788", "市立榮富國小", "");
        addOption(document.myform.selectschool, "014789", "市立裕民國小", "");
        addOption(document.myform.selectschool, "014790", "市立新泰國小", "");
        addOption(document.myform.selectschool, "014791", "市立中信國小", "");
        addOption(document.myform.selectschool, "014801", "市立昌平國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '泰山區') {
        addOption(document.myform.selectschool, "014739", "市立泰山國小", "");
        addOption(document.myform.selectschool, "014740", "市立明志國小", "");
        addOption(document.myform.selectschool, "014795", "市立同榮國小", "");
        addOption(document.myform.selectschool, "014812", "市立義學國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '五股區') {
        addOption(document.myform.selectschool, "014741", "市立成州國小", "");
        addOption(document.myform.selectschool, "014742", "市立更寮國小", "");
        addOption(document.myform.selectschool, "014743", "市立五股國小", "");
        addOption(document.myform.selectschool, "014792", "市立德音國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '蘆洲區') {
        addOption(document.myform.selectschool, "014744", "市立蘆洲國小", "");
        addOption(document.myform.selectschool, "014745", "市立鷺江國小", "");
        addOption(document.myform.selectschool, "014786", "市立成功國小", "");
        addOption(document.myform.selectschool, "014787", "市立仁愛國小", "");
        addOption(document.myform.selectschool, "014808", "市立忠義國小", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '林口區') {
        addOption(document.myform.selectschool, "014749", "市立林口國小", "");
        addOption(document.myform.selectschool, "014750", "市立南勢國小", "");
        addOption(document.myform.selectschool, "014751", "市立嘉寶國小", "");
        addOption(document.myform.selectschool, "014752", "市立瑞平國小", "");
        addOption(document.myform.selectschool, "014753", "市立興福國小", "");
        addOption(document.myform.selectschool, "014793", "市立麗園國小", "");
        addOption(document.myform.selectschool, "014802", "市立麗林國小", "");
        addOption(document.myform.selectschool, "014816", "市立頭湖國小", "");
        addOption(document.myform.selectschool, "010F01", "國立林口啟智學校", "");
    }
    if (document.myform.selectcity.value == '新北市' && document.myform.selectdistrict.value == '三重區') {
        addOption(document.myform.selectschool, "014754", "市立三重國小", "");
        addOption(document.myform.selectschool, "014755", "市立永福國小", "");
        addOption(document.myform.selectschool, "014756", "市立光榮國小", "");
        addOption(document.myform.selectschool, "014757", "市立厚德國小", "");
        addOption(document.myform.selectschool, "014758", "市立碧華國小", "");
        addOption(document.myform.selectschool, "014759", "市立三光國小", "");
        addOption(document.myform.selectschool, "014760", "市立光興國小", "");
        addOption(document.myform.selectschool, "014761", "市立正義國小", "");
        addOption(document.myform.selectschool, "014762", "市立修德國小", "");
        addOption(document.myform.selectschool, "014763", "市立二重國小", "");
        addOption(document.myform.selectschool, "014764", "市立興穀國小", "");
        addOption(document.myform.selectschool, "014784", "市立重陽國小", "");
        addOption(document.myform.selectschool, "014785", "市立五華國小", "");
        addOption(document.myform.selectschool, "014803", "市立集美國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '松山區') {
        addOption(document.myform.selectschool, "313601", "市立松山國小", "");
        addOption(document.myform.selectschool, "313602", "市立西松國小", "");
        addOption(document.myform.selectschool, "313604", "市立敦化國小", "");
        addOption(document.myform.selectschool, "313605", "市立民生國小", "");
        addOption(document.myform.selectschool, "313606", "市立民權國小", "");
        addOption(document.myform.selectschool, "313607", "市立民族國小", "");
        addOption(document.myform.selectschool, "313608", "市立三民國小", "");
        addOption(document.myform.selectschool, "313609", "市立健康國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '信義區') {
        addOption(document.myform.selectschool, "323601", "市立興雅國小", "");
        addOption(document.myform.selectschool, "323602", "市立永春國小", "");
        addOption(document.myform.selectschool, "323603", "市立光復國小", "");
        addOption(document.myform.selectschool, "323604", "市立三興國小", "");
        addOption(document.myform.selectschool, "323605", "市立信義國小", "");
        addOption(document.myform.selectschool, "323606", "市立吳興國小", "");
        addOption(document.myform.selectschool, "323607", "市立福德國小", "");
        addOption(document.myform.selectschool, "323608", "市立永吉國小", "");
        addOption(document.myform.selectschool, "323609", "市立博愛國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '大安區') {
        addOption(document.myform.selectschool, "330601", "國立臺北教大實小", "");
        addOption(document.myform.selectschool, "331601", "私立復興國小", "");
        addOption(document.myform.selectschool, "331602", "私立立人國(中)小", "");
        addOption(document.myform.selectschool, "331603", "私立新民小學", "");
        addOption(document.myform.selectschool, "333601", "市立龍安國小", "");
        addOption(document.myform.selectschool, "333602", "市立大安國小", "");
        addOption(document.myform.selectschool, "333603", "市立幸安國小", "");
        addOption(document.myform.selectschool, "333604", "市立建安國小", "");
        addOption(document.myform.selectschool, "333605", "市立仁愛國小", "");
        addOption(document.myform.selectschool, "333606", "市立金華國小", "");
        addOption(document.myform.selectschool, "333607", "市立古亭國小", "");
        addOption(document.myform.selectschool, "333608", "市立銘傳國小", "");
        addOption(document.myform.selectschool, "333609", "市立公館國小", "");
        addOption(document.myform.selectschool, "333610", "市立新生國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '中山區') {
        addOption(document.myform.selectschool, "343601", "市立中山國小", "");
        addOption(document.myform.selectschool, "343602", "市立中正國小", "");
        addOption(document.myform.selectschool, "343603", "市立長安國小", "");
        addOption(document.myform.selectschool, "343604", "市立長春國小", "");
        addOption(document.myform.selectschool, "343605", "市立大直國小", "");
        addOption(document.myform.selectschool, "343606", "市立大佳國小", "");
        addOption(document.myform.selectschool, "343607", "市立五常國小", "");
        addOption(document.myform.selectschool, "343608", "市立吉林國小", "");
        addOption(document.myform.selectschool, "343609", "市立懷生國小", "");
        addOption(document.myform.selectschool, "343610", "市立永安國小", "");
        addOption(document.myform.selectschool, "343611", "市立濱江國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '中正區') {
        addOption(document.myform.selectschool, "353601", "市立螢橋國小", "");
        addOption(document.myform.selectschool, "353602", "市立河堤國小", "");
        addOption(document.myform.selectschool, "353603", "市立忠義國小", "");
        addOption(document.myform.selectschool, "353604", "市立國語實小", "");
        addOption(document.myform.selectschool, "353605", "市立南門國小", "");
        addOption(document.myform.selectschool, "353606", "市立東門國小", "");
        addOption(document.myform.selectschool, "353607", "市立忠孝國小", "");
        addOption(document.myform.selectschool, "353608", "臺北市立大學附小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '大同區') {
        addOption(document.myform.selectschool, "363601", "市立蓬萊國小", "");
        addOption(document.myform.selectschool, "363602", "市立日新國小", "");
        addOption(document.myform.selectschool, "363603", "市立太平國小", "");
        addOption(document.myform.selectschool, "363604", "市立永樂國小", "");
        addOption(document.myform.selectschool, "363605", "市立雙蓮國小", "");
        addOption(document.myform.selectschool, "363606", "市立大同國小", "");
        addOption(document.myform.selectschool, "363607", "市立大龍國小", "");
        addOption(document.myform.selectschool, "363608", "市立延平國小", "");
        addOption(document.myform.selectschool, "363609", "市立大橋國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '萬華區') {
        addOption(document.myform.selectschool, "371601", "私立光仁小學", "");
        addOption(document.myform.selectschool, "373601", "市立新和國小", "");
        addOption(document.myform.selectschool, "373602", "市立雙園國小", "");
        addOption(document.myform.selectschool, "373603", "市立東園國小", "");
        addOption(document.myform.selectschool, "373604", "市立大理國小", "");
        addOption(document.myform.selectschool, "373605", "市立西園國小", "");
        addOption(document.myform.selectschool, "373606", "市立萬大國小", "");
        addOption(document.myform.selectschool, "373607", "市立華江國小", "");
        addOption(document.myform.selectschool, "373608", "市立西門國小", "");
        addOption(document.myform.selectschool, "373609", "市立老松國小", "");
        addOption(document.myform.selectschool, "373610", "市立龍山國小", "");
        addOption(document.myform.selectschool, "373611", "市立福星國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '文山區') {
        addOption(document.myform.selectschool, "380601", "國立政大實小", "");
        addOption(document.myform.selectschool, "381601", "私立靜心小學", "");
        addOption(document.myform.selectschool, "381602", "私立中山小學", "");
        addOption(document.myform.selectschool, "381603", "私立再興小學", "");
        addOption(document.myform.selectschool, "383601", "市立景美國小", "");
        addOption(document.myform.selectschool, "383602", "市立武功國小", "");
        addOption(document.myform.selectschool, "383603", "市立興德國小", "");
        addOption(document.myform.selectschool, "383604", "市立溪口國小", "");
        addOption(document.myform.selectschool, "383605", "市立興隆國小", "");
        addOption(document.myform.selectschool, "383606", "市立志清國小", "");
        addOption(document.myform.selectschool, "383607", "市立景興國小", "");
        addOption(document.myform.selectschool, "383608", "市立木柵國小", "");
        addOption(document.myform.selectschool, "383609", "市立永建國小", "");
        addOption(document.myform.selectschool, "383610", "市立實踐國小", "");
        addOption(document.myform.selectschool, "383611", "市立博嘉國小", "");
        addOption(document.myform.selectschool, "383612", "市立指南國小", "");
        addOption(document.myform.selectschool, "383613", "市立明道國小", "");
        addOption(document.myform.selectschool, "383614", "市立萬芳國小", "");
        addOption(document.myform.selectschool, "383615", "市立力行國小", "");
        addOption(document.myform.selectschool, "383616", "市立萬興國小", "");
        addOption(document.myform.selectschool, "383617", "市立萬福國小", "");
        addOption(document.myform.selectschool, "383618", "市立興華國小", "");
        addOption(document.myform.selectschool, "383619", "市立辛亥國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '南港區') {
        addOption(document.myform.selectschool, "393601", "市立南港國小", "");
        addOption(document.myform.selectschool, "393602", "市立舊莊國小", "");
        addOption(document.myform.selectschool, "393603", "市立玉成國小", "");
        addOption(document.myform.selectschool, "393604", "市立成德國小", "");
        addOption(document.myform.selectschool, "393605", "市立胡適國小", "");
        addOption(document.myform.selectschool, "393606", "市立東新國小", "");
        addOption(document.myform.selectschool, "393607", "市立修德國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '內湖區') {
        addOption(document.myform.selectschool, "403601", "市立內湖國小", "");
        addOption(document.myform.selectschool, "403602", "市立碧湖國小", "");
        addOption(document.myform.selectschool, "403603", "市立潭美國小", "");
        addOption(document.myform.selectschool, "403604", "市立東湖國小", "");
        addOption(document.myform.selectschool, "403605", "市立西湖國小", "");
        addOption(document.myform.selectschool, "403606", "市立康寧國小", "");
        addOption(document.myform.selectschool, "403607", "市立明湖國小", "");
        addOption(document.myform.selectschool, "403608", "市立麗山國小", "");
        addOption(document.myform.selectschool, "403609", "市立新湖國小", "");
        addOption(document.myform.selectschool, "403610", "市立文湖國小", "");
        addOption(document.myform.selectschool, "403611", "市立大湖國小", "");
        addOption(document.myform.selectschool, "403612", "市立南湖國小", "");
        addOption(document.myform.selectschool, "403613", "市立麗湖國小", "");
        addOption(document.myform.selectschool, "400144", "國立臺灣戲曲學院附設國小", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '士林區') {
        addOption(document.myform.selectschool, "411601", "私立華興小學", "");
        addOption(document.myform.selectschool, "413601", "市立士林國小", "");
        addOption(document.myform.selectschool, "413602", "市立士東國小", "");
        addOption(document.myform.selectschool, "413603", "市立福林國小", "");
        addOption(document.myform.selectschool, "413604", "市立陽明山國小", "");
        addOption(document.myform.selectschool, "413605", "市立社子國小", "");
        addOption(document.myform.selectschool, "413606", "市立雨聲國小", "");
        addOption(document.myform.selectschool, "413607", "市立富安國小", "");
        addOption(document.myform.selectschool, "413608", "市立劍潭國小", "");
        addOption(document.myform.selectschool, "413609", "市立溪山國小", "");
        addOption(document.myform.selectschool, "413610", "市立平等國小", "");
        addOption(document.myform.selectschool, "413611", "市立百齡國小", "");
        addOption(document.myform.selectschool, "413612", "市立雙溪國小", "");
        addOption(document.myform.selectschool, "413613", "市立葫蘆國小", "");
        addOption(document.myform.selectschool, "413614", "市立雨農國小", "");
        addOption(document.myform.selectschool, "413615", "市立天母國小", "");
        addOption(document.myform.selectschool, "413616", "市立文昌國小", "");
        addOption(document.myform.selectschool, "413617", "市立芝山國小", "");
        addOption(document.myform.selectschool, "413618", "市立蘭雅國小", "");
        addOption(document.myform.selectschool, "413619", "市立三玉國小", "");
        addOption(document.myform.selectschool, "413F01", "市立啟智學校", "");
        addOption(document.myform.selectschool, "413F02", "市立啟明學校", "");
    }
    if (document.myform.selectcity.value == '臺北市' && document.myform.selectdistrict.value == '北投區') {
        addOption(document.myform.selectschool, "421601", "私立薇閣小學", "");
        addOption(document.myform.selectschool, "421602", "私立奎山中學附小", "");
        addOption(document.myform.selectschool, "423601", "市立北投國小", "");
        addOption(document.myform.selectschool, "423602", "市立逸仙國小", "");
        addOption(document.myform.selectschool, "423603", "市立石牌國小", "");
        addOption(document.myform.selectschool, "423604", "市立關渡國小", "");
        addOption(document.myform.selectschool, "423605", "市立湖田國小", "");
        addOption(document.myform.selectschool, "423606", "市立清江國小", "");
        addOption(document.myform.selectschool, "423607", "市立泉源國小", "");
        addOption(document.myform.selectschool, "423608", "市立大屯國小", "");
        addOption(document.myform.selectschool, "423609", "市立湖山國小", "");
        addOption(document.myform.selectschool, "423610", "市立桃源國小", "");
        addOption(document.myform.selectschool, "423611", "市立文林國小", "");
        addOption(document.myform.selectschool, "423612", "市立義方國小", "");
        addOption(document.myform.selectschool, "423613", "市立立農國小", "");
        addOption(document.myform.selectschool, "423614", "市立明德國小", "");
        addOption(document.myform.selectschool, "423615", "市立洲美國小", "");
        addOption(document.myform.selectschool, "423616", "市立文化國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大安區') {
        addOption(document.myform.selectschool, "064682", "市立大安國小", "");
        addOption(document.myform.selectschool, "064683", "市立三光國小", "");
        addOption(document.myform.selectschool, "064684", "市立海墘國小", "");
        addOption(document.myform.selectschool, "064685", "市立大安區永安國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '潭子區') {
        addOption(document.myform.selectschool, "061601", "私立華盛頓國小", "");
        addOption(document.myform.selectschool, "064626", "市立潭子國小", "");
        addOption(document.myform.selectschool, "064627", "市立僑忠國小", "");
        addOption(document.myform.selectschool, "064628", "市立東寶國小", "");
        addOption(document.myform.selectschool, "064629", "市立潭子區新興國小", "");
        addOption(document.myform.selectschool, "064741", "市立潭陽國小", "");
        addOption(document.myform.selectschool, "064760", "市立頭家國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '豐原區') {
        addOption(document.myform.selectschool, "064601", "市立豐原國小", "");
        addOption(document.myform.selectschool, "064602", "市立瑞穗國小", "");
        addOption(document.myform.selectschool, "064603", "市立南陽國小", "");
        addOption(document.myform.selectschool, "064604", "市立富春國小", "");
        addOption(document.myform.selectschool, "064605", "市立豐村國小", "");
        addOption(document.myform.selectschool, "064606", "市立翁子國小", "");
        addOption(document.myform.selectschool, "064607", "市立豐田國小", "");
        addOption(document.myform.selectschool, "064608", "市立合作國小", "");
        addOption(document.myform.selectschool, "064751", "市立葫蘆墩國小", "");
        addOption(document.myform.selectschool, "064759", "市立福陽國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '后里區') {
        addOption(document.myform.selectschool, "064609", "市立內埔國小", "");
        addOption(document.myform.selectschool, "064610", "市立后里國小", "");
        addOption(document.myform.selectschool, "064611", "市立月眉國小", "");
        addOption(document.myform.selectschool, "064612", "市立七星國小", "");
        addOption(document.myform.selectschool, "064613", "市立育英國小", "");
        addOption(document.myform.selectschool, "064614", "市立后里區泰安國小", "");
        addOption(document.myform.selectschool, "060F01", "國立臺中啟明學校", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '神岡區') {
        addOption(document.myform.selectschool, "064615", "市立神岡國小", "");
        addOption(document.myform.selectschool, "064616", "市立豐洲國小", "");
        addOption(document.myform.selectschool, "064617", "市立社口國小", "");
        addOption(document.myform.selectschool, "064618", "市立圳堵國小", "");
        addOption(document.myform.selectschool, "064619", "市立岸裡國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大雅區') {
        addOption(document.myform.selectschool, "064620", "市立大雅國小", "");
        addOption(document.myform.selectschool, "064621", "市立三和國小", "");
        addOption(document.myform.selectschool, "064622", "市立大明國小", "");
        addOption(document.myform.selectschool, "064623", "市立上楓國小", "");
        addOption(document.myform.selectschool, "064624", "市立汝鎏國小", "");
        addOption(document.myform.selectschool, "064625", "市立陽明國小", "");
        addOption(document.myform.selectschool, "064746", "市立文雅國小", "");
        addOption(document.myform.selectschool, "064765", "市立六寶國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '外埔區') {
        addOption(document.myform.selectschool, "064630", "市立外埔國小", "");
        addOption(document.myform.selectschool, "064631", "市立安定國小", "");
        addOption(document.myform.selectschool, "064632", "市立鐵山國小", "");
        addOption(document.myform.selectschool, "064633", "市立馬鳴國小", "");
        addOption(document.myform.selectschool, "064634", "市立水美國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '東勢區') {
        addOption(document.myform.selectschool, "064635", "市立東勢國小", "");
        addOption(document.myform.selectschool, "064636", "市立中山國小", "");
        addOption(document.myform.selectschool, "064637", "市立石城國小", "");
        addOption(document.myform.selectschool, "064638", "市立東勢區成功國小", "");
        addOption(document.myform.selectschool, "064639", "市立石角國小", "");
        addOption(document.myform.selectschool, "064640", "市立中科國小", "");
        addOption(document.myform.selectschool, "064641", "市立新成國小", "");
        addOption(document.myform.selectschool, "064642", "市立明正國小", "");
        addOption(document.myform.selectschool, "064747", "市立新盛國小", "");
        addOption(document.myform.selectschool, "064762", "市立東新國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '石岡區') {
        addOption(document.myform.selectschool, "064643", "市立石岡國小", "");
        addOption(document.myform.selectschool, "064644", "市立土牛國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '新社區') {
        addOption(document.myform.selectschool, "064645", "市立新社國小", "");
        addOption(document.myform.selectschool, "064646", "市立新社區東興國小", "");
        addOption(document.myform.selectschool, "064647", "市立大南國小", "");
        addOption(document.myform.selectschool, "064648", "市立協成國小", "");
        addOption(document.myform.selectschool, "064649", "市立大林國小", "");
        addOption(document.myform.selectschool, "064650", "市立崑山國小", "");
        addOption(document.myform.selectschool, "064651", "市立中和國小", "");
        addOption(document.myform.selectschool, "064730", "市立福民國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '清水區') {
        addOption(document.myform.selectschool, "064652", "市立清水國小", "");
        addOption(document.myform.selectschool, "064653", "市立西寧國小", "");
        addOption(document.myform.selectschool, "064654", "市立建國國小", "");
        addOption(document.myform.selectschool, "064655", "市立大秀國小", "");
        addOption(document.myform.selectschool, "064656", "市立三田國小", "");
        addOption(document.myform.selectschool, "064657", "市立甲南國小", "");
        addOption(document.myform.selectschool, "064658", "市立高美國小", "");
        addOption(document.myform.selectschool, "064659", "市立大楊國小", "");
        addOption(document.myform.selectschool, "064660", "市立東山國小", "");
        addOption(document.myform.selectschool, "064739", "市立?榔國小", "");
        addOption(document.myform.selectschool, "064754", "市立吳厝國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '梧棲區') {
        addOption(document.myform.selectschool, "064661", "市立梧棲國小", "");
        addOption(document.myform.selectschool, "064662", "市立梧南國小", "");
        addOption(document.myform.selectschool, "064663", "市立梧棲區中正國小", "");
        addOption(document.myform.selectschool, "064664", "市立永寧國小", "");
        addOption(document.myform.selectschool, "064744", "市立中港國小", "");
        addOption(document.myform.selectschool, "064757", "市立大德國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大甲區') {
        addOption(document.myform.selectschool, "064665", "市立大甲國小", "");
        addOption(document.myform.selectschool, "064666", "市立德化國小", "");
        addOption(document.myform.selectschool, "064667", "市立大甲區文昌國小", "");
        addOption(document.myform.selectschool, "064668", "市立順天國小", "");
        addOption(document.myform.selectschool, "064669", "市立文武國小", "");
        addOption(document.myform.selectschool, "064670", "市立日南國小", "");
        addOption(document.myform.selectschool, "064671", "市立東明國小", "");
        addOption(document.myform.selectschool, "064672", "市立華龍國小", "");
        addOption(document.myform.selectschool, "064673", "市立西岐國小", "");
        addOption(document.myform.selectschool, "064674", "市立東陽國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '沙鹿區') {
        addOption(document.myform.selectschool, "064675", "市立沙鹿國小", "");
        addOption(document.myform.selectschool, "064676", "市立文光國小", "");
        addOption(document.myform.selectschool, "064677", "市立竹林國小", "");
        addOption(document.myform.selectschool, "064678", "市立北勢國小", "");
        addOption(document.myform.selectschool, "064679", "市立公明國小", "");
        addOption(document.myform.selectschool, "064680", "市立公館國小", "");
        addOption(document.myform.selectschool, "064681", "市立鹿峰國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '龍井區') {
        addOption(document.myform.selectschool, "064686", "市立龍山國小", "");
        addOption(document.myform.selectschool, "064687", "市立龍井國小", "");
        addOption(document.myform.selectschool, "064688", "市立龍津國小", "");
        addOption(document.myform.selectschool, "064689", "市立龍海國小", "");
        addOption(document.myform.selectschool, "064690", "市立龍港國小", "");
        addOption(document.myform.selectschool, "064691", "市立龍泉國小", "");
        addOption(document.myform.selectschool, "064692", "市立龍峰國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '烏日區') {
        addOption(document.myform.selectschool, "064693", "市立烏日國小", "");
        addOption(document.myform.selectschool, "064694", "市立僑仁國小", "");
        addOption(document.myform.selectschool, "064695", "市立喀哩國小", "");
        addOption(document.myform.selectschool, "064696", "市立東園國小", "");
        addOption(document.myform.selectschool, "064697", "市立溪尾國小", "");
        addOption(document.myform.selectschool, "064698", "市立旭光國小", "");
        addOption(document.myform.selectschool, "064699", "市立五光國小", "");
        addOption(document.myform.selectschool, "064743", "市立九德國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大肚區') {
        addOption(document.myform.selectschool, "064700", "市立大肚國小", "");
        addOption(document.myform.selectschool, "064701", "市立瑞峰國小", "");
        addOption(document.myform.selectschool, "064702", "市立永順國小", "");
        addOption(document.myform.selectschool, "064703", "市立追分國小", "");
        addOption(document.myform.selectschool, "064704", "市立大忠國小", "");
        addOption(document.myform.selectschool, "064755", "市立山陽國小", "");
        addOption(document.myform.selectschool, "064761", "市立瑞井國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '大里區') {
        addOption(document.myform.selectschool, "064705", "市立大里國小", "");
        addOption(document.myform.selectschool, "064706", "市立內新國小", "");
        addOption(document.myform.selectschool, "064707", "市立崇光國小", "");
        addOption(document.myform.selectschool, "064708", "市立塗城國小", "");
        addOption(document.myform.selectschool, "064709", "市立瑞城國小", "");
        addOption(document.myform.selectschool, "064710", "市立健民國小", "");
        addOption(document.myform.selectschool, "064711", "市立草湖國小", "");
        addOption(document.myform.selectschool, "064738", "市立益民國小", "");
        addOption(document.myform.selectschool, "064748", "市立大元國小", "");
        addOption(document.myform.selectschool, "064752", "市立永隆國小", "");
        addOption(document.myform.selectschool, "064753", "市立美群國小", "");
        addOption(document.myform.selectschool, "064756", "市立立新國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '霧峰區') {
        addOption(document.myform.selectschool, "064712", "市立霧峰國小", "");
        addOption(document.myform.selectschool, "064713", "市立僑榮國小", "");
        addOption(document.myform.selectschool, "064714", "市立四德國小", "");
        addOption(document.myform.selectschool, "064715", "市立五福國小", "");
        addOption(document.myform.selectschool, "064716", "市立萬豐國小", "");
        addOption(document.myform.selectschool, "064717", "市立峰谷國小", "");
        addOption(document.myform.selectschool, "064718", "市立桐林國小", "");
        addOption(document.myform.selectschool, "064719", "市立復興國小", "");
        addOption(document.myform.selectschool, "064720", "市立霧峰區光正國小", "");
        addOption(document.myform.selectschool, "064749", "市立吉峰國小", "");
        addOption(document.myform.selectschool, "064763", "市立光復國(中)小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '太平區') {
        addOption(document.myform.selectschool, "064721", "市立太平區太平國小", "");
        addOption(document.myform.selectschool, "064722", "市立宜欣國小", "");
        addOption(document.myform.selectschool, "064723", "市立新光國小", "");
        addOption(document.myform.selectschool, "064724", "市立坪林國小", "");
        addOption(document.myform.selectschool, "064725", "市立光隆國小", "");
        addOption(document.myform.selectschool, "064726", "市立黃竹國小", "");
        addOption(document.myform.selectschool, "064727", "市立頭汴國小", "");
        addOption(document.myform.selectschool, "064728", "市立東汴國小", "");
        addOption(document.myform.selectschool, "064740", "市立建平國小", "");
        addOption(document.myform.selectschool, "064742", "市立太平區中華國小", "");
        addOption(document.myform.selectschool, "064745", "市立東平國小", "");
        addOption(document.myform.selectschool, "064750", "市立新平國小", "");
        addOption(document.myform.selectschool, "064758", "市立車籠埔國小", "");
        addOption(document.myform.selectschool, "064764", "市立長億國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '和平區') {
        addOption(document.myform.selectschool, "064729", "市立和平區和平國小", "");
        addOption(document.myform.selectschool, "064731", "市立白冷國小", "");
        addOption(document.myform.selectschool, "064732", "市立達觀國小", "");
        addOption(document.myform.selectschool, "064733", "市立中坑國小", "");
        addOption(document.myform.selectschool, "064734", "市立平等國小", "");
        addOption(document.myform.selectschool, "064735", "市立博愛國小", "");
        addOption(document.myform.selectschool, "064736", "市立自由國小", "");
        addOption(document.myform.selectschool, "064737", "市立梨山國(中)小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '北區') {
        addOption(document.myform.selectschool, "190601", "國立臺中教大附小", "");
        addOption(document.myform.selectschool, "191602", "私立育仁國小", "");
        addOption(document.myform.selectschool, "193616", "市立北區太平國小", "");
        addOption(document.myform.selectschool, "193617", "市立北區中華國小", "");
        addOption(document.myform.selectschool, "193618", "市立篤行國小", "");
        addOption(document.myform.selectschool, "193619", "市立健行國小", "");
        addOption(document.myform.selectschool, "193620", "市立省三國小", "");
        addOption(document.myform.selectschool, "193642", "市立立人國小", "");
        addOption(document.myform.selectschool, "193654", "市立賴厝國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '北屯區') {
        addOption(document.myform.selectschool, "191604", "私立慎齋小學", "");
        addOption(document.myform.selectschool, "191605", "私立明道普霖斯頓小學", "");
        addOption(document.myform.selectschool, "193632", "市立北屯國小", "");
        addOption(document.myform.selectschool, "193633", "市立僑孝國小", "");
        addOption(document.myform.selectschool, "193634", "市立四張犁國小", "");
        addOption(document.myform.selectschool, "193635", "市立松竹國小", "");
        addOption(document.myform.selectschool, "193636", "市立軍功國小", "");
        addOption(document.myform.selectschool, "193637", "市立北屯區大坑國小", "");
        addOption(document.myform.selectschool, "193638", "市立逢甲國小", "");
        addOption(document.myform.selectschool, "193639", "市立建功國小", "");
        addOption(document.myform.selectschool, "193640", "市立北屯區新興國小", "");
        addOption(document.myform.selectschool, "193641", "市立仁愛國小", "");
        addOption(document.myform.selectschool, "193643", "市立北屯區文昌國小", "");
        addOption(document.myform.selectschool, "193647", "市立文心國小", "");
        addOption(document.myform.selectschool, "193648", "市立四維國小", "");
        addOption(document.myform.selectschool, "193653", "市立陳平國小", "");
        addOption(document.myform.selectschool, "193658", "市立東光國小", "");
        addOption(document.myform.selectschool, "193660", "市立仁美國小", "");
        addOption(document.myform.selectschool, "191302", "私立葳格高中附設國小", "");
        addOption(document.myform.selectschool, "191315", "私立磊川華德福實驗教育學校", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '西屯區') {
        addOption(document.myform.selectschool, "191606", "私立麗澤國(中)小", "");
        addOption(document.myform.selectschool, "193621", "市立西屯國小", "");
        addOption(document.myform.selectschool, "193622", "市立西屯區泰安國小", "");
        addOption(document.myform.selectschool, "193623", "市立大鵬國小", "");
        addOption(document.myform.selectschool, "193624", "市立西屯區永安國小", "");
        addOption(document.myform.selectschool, "193625", "市立協和國小", "");
        addOption(document.myform.selectschool, "193626", "市立大仁國小", "");
        addOption(document.myform.selectschool, "193645", "市立重慶國小", "");
        addOption(document.myform.selectschool, "193649", "市立何厝國小", "");
        addOption(document.myform.selectschool, "193650", "市立國安國小", "");
        addOption(document.myform.selectschool, "193651", "市立上石國小", "");
        addOption(document.myform.selectschool, "193659", "市立上安國小", "");
        addOption(document.myform.selectschool, "193661", "市立長安國小", "");
        addOption(document.myform.selectschool, "193662", "市立惠來國小", "");
        addOption(document.myform.selectschool, "193664", "市立東海國小", "");
        addOption(document.myform.selectschool, "191301", "私立東大附中附設國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '中區') {
        addOption(document.myform.selectschool, "193601", "市立中區光復國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "193602", "市立臺中國小", "");
        addOption(document.myform.selectschool, "193603", "市立大智國小", "");
        addOption(document.myform.selectschool, "193604", "市立東區成功國小", "");
        addOption(document.myform.selectschool, "193605", "市立進德國小", "");
        addOption(document.myform.selectschool, "193606", "市立力行國小", "");
        addOption(document.myform.selectschool, "193607", "市立樂業國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '西區') {
        addOption(document.myform.selectschool, "193608", "市立忠孝國小", "");
        addOption(document.myform.selectschool, "193609", "市立忠信國小", "");
        addOption(document.myform.selectschool, "193610", "市立大同國小", "");
        addOption(document.myform.selectschool, "193611", "市立忠明國小", "");
        addOption(document.myform.selectschool, "193612", "市立西區中正國小", "");
        addOption(document.myform.selectschool, "193644", "市立大勇國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '南區') {
        addOption(document.myform.selectschool, "193613", "市立南區和平國小", "");
        addOption(document.myform.selectschool, "193614", "市立國光國小", "");
        addOption(document.myform.selectschool, "193615", "市立信義國小", "");
        addOption(document.myform.selectschool, "193657", "市立樹義國小", "");
    }
    if (document.myform.selectcity.value == '臺中市' && document.myform.selectdistrict.value == '南屯區') {
        addOption(document.myform.selectschool, "193627", "市立南屯國小", "");
        addOption(document.myform.selectschool, "193628", "市立鎮平國小", "");
        addOption(document.myform.selectschool, "193629", "市立文山國小", "");
        addOption(document.myform.selectschool, "193630", "市立春安國小", "");
        addOption(document.myform.selectschool, "193631", "市立黎明國小", "");
        addOption(document.myform.selectschool, "193646", "市立南屯區東興國小", "");
        addOption(document.myform.selectschool, "193652", "市立大新國小", "");
        addOption(document.myform.selectschool, "193655", "市立永春國小", "");
        addOption(document.myform.selectschool, "193656", "市立惠文國小", "");
        addOption(document.myform.selectschool, "193663", "市立大墩國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '北區') {
        addOption(document.myform.selectschool, "211601", "私立寶仁小學", "");
        addOption(document.myform.selectschool, "213616", "市立立人國小", "");
        addOption(document.myform.selectschool, "213617", "市立公園國小", "");
        addOption(document.myform.selectschool, "213618", "市立開元國小", "");
        addOption(document.myform.selectschool, "213619", "市立大光國小", "");
        addOption(document.myform.selectschool, "213637", "市立大港國小", "");
        addOption(document.myform.selectschool, "213642", "市立文元國小", "");
        addOption(document.myform.selectschool, "213645", "市立賢北國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "213601", "市立東區勝利國小", "");
        addOption(document.myform.selectschool, "213602", "市立博愛國小", "");
        addOption(document.myform.selectschool, "213603", "市立東區大同國小", "");
        addOption(document.myform.selectschool, "213604", "市立東光國小", "");
        addOption(document.myform.selectschool, "213605", "市立德高國小", "");
        addOption(document.myform.selectschool, "213606", "市立崇學國小", "");
        addOption(document.myform.selectschool, "213639", "市立東區復興國小", "");
        addOption(document.myform.selectschool, "213640", "市立崇明國小", "");
        addOption(document.myform.selectschool, "213646", "市立裕文國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '南區') {
        addOption(document.myform.selectschool, "213607", "市立志開國小", "");
        addOption(document.myform.selectschool, "213608", "市立南區新興國小", "");
        addOption(document.myform.selectschool, "213609", "市立省躬國小", "");
        addOption(document.myform.selectschool, "213610", "市立喜樹國小", "");
        addOption(document.myform.selectschool, "213611", "市立龍崗國小", "");
        addOption(document.myform.selectschool, "213612", "市立日新國小", "");
        addOption(document.myform.selectschool, "213613", "市立永華國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '仁德區') {
        addOption(document.myform.selectschool, "114601", "市立仁德國小", "");
        addOption(document.myform.selectschool, "114602", "市立文賢國小", "");
        addOption(document.myform.selectschool, "114603", "市立長興國小", "");
        addOption(document.myform.selectschool, "114604", "市立依仁國小", "");
        addOption(document.myform.selectschool, "114605", "市立大甲國小", "");
        addOption(document.myform.selectschool, "114606", "市立仁和國小", "");
        addOption(document.myform.selectschool, "114607", "市立德南國小", "");
        addOption(document.myform.selectschool, "114608", "市立虎山國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '歸仁區') {
        addOption(document.myform.selectschool, "114609", "市立歸仁國小", "");
        addOption(document.myform.selectschool, "114610", "市立歸南國小", "");
        addOption(document.myform.selectschool, "114611", "市立保西國小", "");
        addOption(document.myform.selectschool, "114612", "市立大潭國小", "");
        addOption(document.myform.selectschool, "114778", "市立文化國小", "");
        addOption(document.myform.selectschool, "114785", "市立紅瓦厝國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '關廟區') {
        addOption(document.myform.selectschool, "114613", "市立關廟國小", "");
        addOption(document.myform.selectschool, "114614", "市立五甲國小", "");
        addOption(document.myform.selectschool, "114615", "市立保東國小", "");
        addOption(document.myform.selectschool, "114616", "市立崇和國小", "");
        addOption(document.myform.selectschool, "114617", "市立文和國小", "");
        addOption(document.myform.selectschool, "114618", "市立深坑國小", "");
        addOption(document.myform.selectschool, "114619", "市立新光國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '龍崎區') {
        addOption(document.myform.selectschool, "114620", "市立龍崎國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '永康區') {
        addOption(document.myform.selectschool, "114623", "市立永康國小", "");
        addOption(document.myform.selectschool, "114624", "市立大灣國小", "");
        addOption(document.myform.selectschool, "114625", "市立三村國小", "");
        addOption(document.myform.selectschool, "114626", "市立西勢國小", "");
        addOption(document.myform.selectschool, "114627", "市立永康區復興國小", "");
        addOption(document.myform.selectschool, "114628", "市立龍潭國小", "");
        addOption(document.myform.selectschool, "114629", "市立大橋國小", "");
        addOption(document.myform.selectschool, "114776", "市立崑山國小", "");
        addOption(document.myform.selectschool, "114777", "市立五王國小", "");
        addOption(document.myform.selectschool, "114782", "市立永信國小", "");
        addOption(document.myform.selectschool, "114784", "市立永康區勝利國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '新化區') {
        addOption(document.myform.selectschool, "114630", "市立新化國小", "");
        addOption(document.myform.selectschool, "114631", "市立那拔國小", "");
        addOption(document.myform.selectschool, "114632", "市立口碑國小", "");
        addOption(document.myform.selectschool, "114633", "市立大新國小", "");
        addOption(document.myform.selectschool, "114779", "市立正新國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '山上區') {
        addOption(document.myform.selectschool, "114635", "市立山上國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '玉井區') {
        addOption(document.myform.selectschool, "114636", "市立玉井國小", "");
        addOption(document.myform.selectschool, "114637", "市立層林國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '楠西區') {
        addOption(document.myform.selectschool, "114638", "市立楠西國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '南化區') {
        addOption(document.myform.selectschool, "114639", "市立南化國小", "");
        addOption(document.myform.selectschool, "114640", "市立北寮國小", "");
        addOption(document.myform.selectschool, "114641", "市立西埔國小", "");
        addOption(document.myform.selectschool, "114642", "市立玉山國小", "");
        addOption(document.myform.selectschool, "114643", "市立瑞峰國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '左鎮區') {
        addOption(document.myform.selectschool, "114644", "市立左鎮國小", "");
        addOption(document.myform.selectschool, "114646", "市立光榮國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '善化區') {
        addOption(document.myform.selectschool, "114647", "市立善化國小", "");
        addOption(document.myform.selectschool, "114648", "市立茄拔國小", "");
        addOption(document.myform.selectschool, "114649", "市立善化區大同國小", "");
        addOption(document.myform.selectschool, "114650", "市立大成國小", "");
        addOption(document.myform.selectschool, "114651", "市立陽明國小", "");
        addOption(document.myform.selectschool, "114652", "市立善糖國小", "");
        addOption(document.myform.selectschool, "114653", "市立小新國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '新市區') {
        addOption(document.myform.selectschool, "114654", "市立新市國小", "");
        addOption(document.myform.selectschool, "114655", "市立大社國小", "");
        addOption(document.myform.selectschool, "110328", "國立南科國際實驗高中附設國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '安定區') {
        addOption(document.myform.selectschool, "114656", "市立安定國小", "");
        addOption(document.myform.selectschool, "114657", "市立南安國小", "");
        addOption(document.myform.selectschool, "114658", "市立安定區南興國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '麻豆區') {
        addOption(document.myform.selectschool, "114659", "市立麻豆國小", "");
        addOption(document.myform.selectschool, "114660", "市立培文國小", "");
        addOption(document.myform.selectschool, "114661", "市立文正國小", "");
        addOption(document.myform.selectschool, "114662", "市立大山國小", "");
        addOption(document.myform.selectschool, "114663", "市立安業國小", "");
        addOption(document.myform.selectschool, "114664", "市立北勢國小", "");
        addOption(document.myform.selectschool, "114665", "市立港尾國小", "");
        addOption(document.myform.selectschool, "114667", "市立紀安國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '佳里區') {
        addOption(document.myform.selectschool, "114668", "市立佳里國小", "");
        addOption(document.myform.selectschool, "114669", "市立佳興國小", "");
        addOption(document.myform.selectschool, "114670", "市立延平國小", "");
        addOption(document.myform.selectschool, "114671", "市立塭內國小", "");
        addOption(document.myform.selectschool, "114672", "市立子龍國小", "");
        addOption(document.myform.selectschool, "114673", "市立仁愛國小", "");
        addOption(document.myform.selectschool, "114674", "市立通興國小", "");
        addOption(document.myform.selectschool, "114780", "市立信義國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '西港區') {
        addOption(document.myform.selectschool, "114675", "市立西港國小", "");
        addOption(document.myform.selectschool, "114676", "市立港東國小", "");
        addOption(document.myform.selectschool, "114677", "市立西港區成功國小", "");
        addOption(document.myform.selectschool, "114678", "市立後營國小", "");
        addOption(document.myform.selectschool, "114680", "市立松林國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '七股區') {
        addOption(document.myform.selectschool, "114681", "市立七股國小", "");
        addOption(document.myform.selectschool, "114682", "市立後港國小", "");
        addOption(document.myform.selectschool, "114683", "市立竹橋國小", "");
        addOption(document.myform.selectschool, "114684", "市立三股國小", "");
        addOption(document.myform.selectschool, "114685", "市立光復國小", "");
        addOption(document.myform.selectschool, "114686", "市立篤加國小", "");
        addOption(document.myform.selectschool, "114688", "市立龍山國小", "");
        addOption(document.myform.selectschool, "114689", "市立建功國小", "");
        addOption(document.myform.selectschool, "114691", "市立大文國小", "");
        addOption(document.myform.selectschool, "114692", "市立樹林國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '將軍區') {
        addOption(document.myform.selectschool, "114693", "市立將軍國小", "");
        addOption(document.myform.selectschool, "114694", "市立漚汪國小", "");
        addOption(document.myform.selectschool, "114695", "市立苓和國小", "");
        addOption(document.myform.selectschool, "114696", "市立鯤鯓國小", "");
        addOption(document.myform.selectschool, "114697", "市立長平國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '北門區') {
        addOption(document.myform.selectschool, "114699", "市立北門國小", "");
        addOption(document.myform.selectschool, "114700", "市立蚵寮國小", "");
        addOption(document.myform.selectschool, "114701", "市立文山國小", "");
        addOption(document.myform.selectschool, "114702", "市立錦湖國小", "");
        addOption(document.myform.selectschool, "114703", "市立雙春國小", "");
        addOption(document.myform.selectschool, "114705", "市立三慈國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '學甲區') {
        addOption(document.myform.selectschool, "114706", "市立學甲國小", "");
        addOption(document.myform.selectschool, "114707", "市立中洲國小", "");
        addOption(document.myform.selectschool, "114708", "市立宅港國小", "");
        addOption(document.myform.selectschool, "114709", "市立頂洲國小", "");
        addOption(document.myform.selectschool, "114710", "市立東陽國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '下營區') {
        addOption(document.myform.selectschool, "114711", "市立下營國小", "");
        addOption(document.myform.selectschool, "114712", "市立中營國小", "");
        addOption(document.myform.selectschool, "114713", "市立賀建國小", "");
        addOption(document.myform.selectschool, "114714", "市立甲中國小", "");
        addOption(document.myform.selectschool, "114716", "市立東興國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '六甲區') {
        addOption(document.myform.selectschool, "114717", "市立六甲國小", "");
        addOption(document.myform.selectschool, "114718", "市立林鳳國小", "");
        addOption(document.myform.selectschool, "114722", "市立嘉南國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '官田區') {
        addOption(document.myform.selectschool, "114720", "市立官田國小", "");
        addOption(document.myform.selectschool, "114721", "市立隆田國小", "");
        addOption(document.myform.selectschool, "114723", "市立渡拔國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '大內區') {
        addOption(document.myform.selectschool, "114724", "市立大內國小", "");
        addOption(document.myform.selectschool, "114726", "市立二溪國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '新營區') {
        addOption(document.myform.selectschool, "114728", "市立新營國小", "");
        addOption(document.myform.selectschool, "114729", "市立新民國小", "");
        addOption(document.myform.selectschool, "114730", "市立新橋國小", "");
        addOption(document.myform.selectschool, "114731", "市立新營區新興國小", "");
        addOption(document.myform.selectschool, "114732", "市立新進國小", "");
        addOption(document.myform.selectschool, "114733", "市立南梓國小", "");
        addOption(document.myform.selectschool, "114734", "市立新生國小", "");
        addOption(document.myform.selectschool, "114735", "市立土庫國小", "");
        addOption(document.myform.selectschool, "114736", "市立公誠國小", "");
        addOption(document.myform.selectschool, "114781", "市立新泰國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '鹽水區') {
        addOption(document.myform.selectschool, "114737", "市立鹽水國小", "");
        addOption(document.myform.selectschool, "114738", "市立歡雅國小", "");
        addOption(document.myform.selectschool, "114739", "市立?頭港國小", "");
        addOption(document.myform.selectschool, "114740", "市立月津國小", "");
        addOption(document.myform.selectschool, "114742", "市立竹埔國小", "");
        addOption(document.myform.selectschool, "114743", "市立仁光國小", "");
        addOption(document.myform.selectschool, "114744", "市立岸內國小", "");
        addOption(document.myform.selectschool, "114747", "市立文昌國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '白河區') {
        addOption(document.myform.selectschool, "114748", "市立白河國小", "");
        addOption(document.myform.selectschool, "114749", "市立玉豐國小", "");
        addOption(document.myform.selectschool, "114750", "市立竹門國小", "");
        addOption(document.myform.selectschool, "114751", "市立內角國小", "");
        addOption(document.myform.selectschool, "114753", "市立仙草國小", "");
        addOption(document.myform.selectschool, "114755", "市立河東國小", "");
        addOption(document.myform.selectschool, "114756", "市立大竹國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '柳營區') {
        addOption(document.myform.selectschool, "114758", "市立柳營國小", "");
        addOption(document.myform.selectschool, "114759", "市立果毅國小", "");
        addOption(document.myform.selectschool, "114760", "市立重溪國小", "");
        addOption(document.myform.selectschool, "114761", "市立太康國小", "");
        addOption(document.myform.selectschool, "114762", "市立新山國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '後壁區') {
        addOption(document.myform.selectschool, "114763", "市立後壁國小", "");
        addOption(document.myform.selectschool, "114764", "市立菁寮國小", "");
        addOption(document.myform.selectschool, "114765", "市立安溪國小", "");
        addOption(document.myform.selectschool, "114766", "市立新東國小", "");
        addOption(document.myform.selectschool, "114767", "市立永安國小", "");
        addOption(document.myform.selectschool, "114768", "市立新嘉國小", "");
        addOption(document.myform.selectschool, "114769", "市立樹人國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '東山區') {
        addOption(document.myform.selectschool, "114770", "市立東山國小", "");
        addOption(document.myform.selectschool, "114771", "市立聖賢國小", "");
        addOption(document.myform.selectschool, "114772", "市立東原國小", "");
        addOption(document.myform.selectschool, "114774", "市立青山國小", "");
        addOption(document.myform.selectschool, "114775", "市立吉貝耍國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '中西區') {
        addOption(document.myform.selectschool, "210601", "國立臺南大學附小", "");
        addOption(document.myform.selectschool, "213614", "市立協進國小", "");
        addOption(document.myform.selectschool, "213620", "市立中西區成功國小", "");
        addOption(document.myform.selectschool, "213621", "市立永福國小", "");
        addOption(document.myform.selectschool, "213622", "市立忠義國小", "");
        addOption(document.myform.selectschool, "213623", "市立進學國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '安平區') {
        addOption(document.myform.selectschool, "213615", "市立新南國小", "");
        addOption(document.myform.selectschool, "213624", "市立石門國小", "");
        addOption(document.myform.selectschool, "213625", "市立西門國小", "");
        addOption(document.myform.selectschool, "213641", "市立安平國小", "");
        addOption(document.myform.selectschool, "213644", "市立億載國小", "");
        addOption(document.myform.selectschool, "211320", "財團法人慈濟高中附設國小", "");
    }
    if (document.myform.selectcity.value == '臺南市' && document.myform.selectdistrict.value == '安南區') {
        addOption(document.myform.selectschool, "213626", "市立安順國小", "");
        addOption(document.myform.selectschool, "213627", "市立和順國小", "");
        addOption(document.myform.selectschool, "213628", "市立海東國小", "");
        addOption(document.myform.selectschool, "213629", "市立安慶國小", "");
        addOption(document.myform.selectschool, "213630", "市立土城國小", "");
        addOption(document.myform.selectschool, "213631", "市立青草國小", "");
        addOption(document.myform.selectschool, "213632", "市立鎮海國小", "");
        addOption(document.myform.selectschool, "213633", "市立顯宮國小", "");
        addOption(document.myform.selectschool, "213634", "市立長安國小", "");
        addOption(document.myform.selectschool, "213635", "市立安南區南興國小", "");
        addOption(document.myform.selectschool, "213636", "市立安佃國小", "");
        addOption(document.myform.selectschool, "213638", "市立海佃國小", "");
        addOption(document.myform.selectschool, "213643", "市立學東國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鳳山區') {
        addOption(document.myform.selectschool, "124601", "市立鳳山國小", "");
        addOption(document.myform.selectschool, "124602", "市立大東國小", "");
        addOption(document.myform.selectschool, "124603", "市立文山國小", "");
        addOption(document.myform.selectschool, "124604", "市立鳳山區中正國小", "");
        addOption(document.myform.selectschool, "124605", "市立五甲國小", "");
        addOption(document.myform.selectschool, "124606", "市立曹公國小", "");
        addOption(document.myform.selectschool, "124607", "市立誠正國小", "");
        addOption(document.myform.selectschool, "124608", "市立南成國小", "");
        addOption(document.myform.selectschool, "124609", "市立五福國小", "");
        addOption(document.myform.selectschool, "124610", "市立鳳山區中山國小", "");
        addOption(document.myform.selectschool, "124611", "市立新甲國小", "");
        addOption(document.myform.selectschool, "124612", "市立鳳山區忠孝國小", "");
        addOption(document.myform.selectschool, "124740", "市立鳳西國小", "");
        addOption(document.myform.selectschool, "124742", "市立文德國小", "");
        addOption(document.myform.selectschool, "124743", "市立瑞興國小", "");
        addOption(document.myform.selectschool, "124748", "市立正義國小", "");
        addOption(document.myform.selectschool, "124749", "市立福誠國小", "");
        addOption(document.myform.selectschool, "124751", "市立過埤國小", "");
        addOption(document.myform.selectschool, "124752", "市立中崙國小", "");
        addOption(document.myform.selectschool, "124761", "市立文華國小", "");
        addOption(document.myform.selectschool, "124762", "市立鳳翔國小", "");
        addOption(document.myform.selectschool, "124739", "市立鎮北國小", "");
    }

    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '林園區') {
        addOption(document.myform.selectschool, "124613", "市立林園國小", "");
        addOption(document.myform.selectschool, "124614", "市立中芸國小", "");
        addOption(document.myform.selectschool, "124615", "市立港埔國小", "");
        addOption(document.myform.selectschool, "124616", "市立金潭國小", "");
        addOption(document.myform.selectschool, "124617", "市立汕尾國小", "");
        addOption(document.myform.selectschool, "124757", "市立王公國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '大寮區') {
        addOption(document.myform.selectschool, "124618", "市立永芳國小", "");
        addOption(document.myform.selectschool, "124619", "市立翁園國小", "");
        addOption(document.myform.selectschool, "124620", "市立忠義國小", "");
        addOption(document.myform.selectschool, "124621", "市立昭明國小", "");
        addOption(document.myform.selectschool, "124622", "市立潮寮國小", "");
        addOption(document.myform.selectschool, "124623", "市立中庄國小", "");
        addOption(document.myform.selectschool, "124624", "市立溪寮國小", "");
        addOption(document.myform.selectschool, "124625", "市立大寮國小", "");
        addOption(document.myform.selectschool, "124750", "市立山頂國小", "");
        addOption(document.myform.selectschool, "124756", "市立後庄國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '大樹區') {
        addOption(document.myform.selectschool, "124626", "市立大樹國小", "");
        addOption(document.myform.selectschool, "124627", "市立九曲國小", "");
        addOption(document.myform.selectschool, "124628", "市立溪埔國小", "");
        addOption(document.myform.selectschool, "124629", "市立姑山國小", "");
        addOption(document.myform.selectschool, "124630", "市立水寮國小", "");
        addOption(document.myform.selectschool, "124631", "市立小坪國小", "");
        addOption(document.myform.selectschool, "124632", "市立興田國小", "");
        addOption(document.myform.selectschool, "124633", "市立龍目國小", "");
        addOption(document.myform.selectschool, "121320", "私立義大國際高中附設國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '仁武區') {
        addOption(document.myform.selectschool, "124634", "市立仁武國小", "");
        addOption(document.myform.selectschool, "124635", "市立烏林國小", "");
        addOption(document.myform.selectschool, "124636", "市立八卦國小", "");
        addOption(document.myform.selectschool, "124637", "市立灣內國小", "");
        addOption(document.myform.selectschool, "124744", "市立登發國小", "");
        addOption(document.myform.selectschool, "124747", "市立竹後國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '大社區') {
        addOption(document.myform.selectschool, "124638", "市立大社區大社國小", "");
        addOption(document.myform.selectschool, "124639", "市立嘉誠國小", "");
        addOption(document.myform.selectschool, "124746", "市立觀音國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鳥松區') {
        addOption(document.myform.selectschool, "124640", "市立鳥松國小", "");
        addOption(document.myform.selectschool, "124641", "市立仁美國小", "");
        addOption(document.myform.selectschool, "124642", "市立大華國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '岡山區') {
        addOption(document.myform.selectschool, "124643", "市立岡山國小", "");
        addOption(document.myform.selectschool, "124644", "市立前峰國小", "");
        addOption(document.myform.selectschool, "124645", "市立嘉興國小", "");
        addOption(document.myform.selectschool, "124646", "市立兆湘國小", "");
        addOption(document.myform.selectschool, "124647", "市立後紅國小", "");
        addOption(document.myform.selectschool, "124648", "市立和平國小", "");
        addOption(document.myform.selectschool, "124745", "市立竹圍國小", "");
        addOption(document.myform.selectschool, "124758", "市立壽天國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '橋頭區') {
        addOption(document.myform.selectschool, "124650", "市立仕隆國小", "");
        addOption(document.myform.selectschool, "124651", "市立五林國小", "");
        addOption(document.myform.selectschool, "124652", "市立甲圍國小", "");
        addOption(document.myform.selectschool, "124653", "市立興糖國小", "");
        addOption(document.myform.selectschool, "124753", "市立橋頭國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '燕巢區') {
        addOption(document.myform.selectschool, "124654", "市立燕巢國小", "");
        addOption(document.myform.selectschool, "124655", "市立橫山國小", "");
        addOption(document.myform.selectschool, "124656", "市立深水國小", "");
        addOption(document.myform.selectschool, "124657", "市立安招國小", "");
        addOption(document.myform.selectschool, "124658", "市立鳳雄國小", "");
        addOption(document.myform.selectschool, "124659", "市立金山國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '田寮區') {
        addOption(document.myform.selectschool, "124660", "市立田寮區新興國小", "");
        addOption(document.myform.selectschool, "124661", "市立崇德國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '阿蓮區') {
        addOption(document.myform.selectschool, "124664", "市立阿蓮國小", "");
        addOption(document.myform.selectschool, "124665", "市立中路國小", "");
        addOption(document.myform.selectschool, "124666", "市立復安國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '路竹區') {
        addOption(document.myform.selectschool, "124667", "市立路竹國小", "");
        addOption(document.myform.selectschool, "124668", "市立路竹區大社國小", "");
        addOption(document.myform.selectschool, "124669", "市立下坑國小", "");
        addOption(document.myform.selectschool, "124670", "市立竹滬國小", "");
        addOption(document.myform.selectschool, "124671", "市立三埤國小", "");
        addOption(document.myform.selectschool, "124672", "市立北嶺國小", "");
        addOption(document.myform.selectschool, "124673", "市立一甲國小", "");
        addOption(document.myform.selectschool, "124760", "市立蔡文國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '湖內區') {
        addOption(document.myform.selectschool, "124674", "市立文賢國小", "");
        addOption(document.myform.selectschool, "124675", "市立明宗國小", "");
        addOption(document.myform.selectschool, "124676", "市立大湖國小", "");
        addOption(document.myform.selectschool, "124677", "市立海埔國小", "");
        addOption(document.myform.selectschool, "124678", "市立三侯國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '茄萣區') {
        addOption(document.myform.selectschool, "124679", "市立茄萣國小", "");
        addOption(document.myform.selectschool, "124680", "市立茄萣區成功國小", "");
        addOption(document.myform.selectschool, "124681", "市立砂崙國小", "");
        addOption(document.myform.selectschool, "124754", "市立興達國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '永安區') {
        addOption(document.myform.selectschool, "124682", "市立永安國小", "");
        addOption(document.myform.selectschool, "124683", "市立新港國小", "");
        addOption(document.myform.selectschool, "124741", "市立維新國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '彌陀區') {
        addOption(document.myform.selectschool, "124684", "市立彌陀國小", "");
        addOption(document.myform.selectschool, "124685", "市立南安國小", "");
        addOption(document.myform.selectschool, "124686", "市立壽齡國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '梓官區') {
        addOption(document.myform.selectschool, "124687", "市立梓官國小", "");
        addOption(document.myform.selectschool, "124688", "市立蚵寮國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '旗山區') {
        addOption(document.myform.selectschool, "124689", "市立旗山國小", "");
        addOption(document.myform.selectschool, "124690", "市立溪洲國小", "");
        addOption(document.myform.selectschool, "124691", "市立旗山區鼓山國小", "");
        addOption(document.myform.selectschool, "124692", "市立圓潭國小", "");
        addOption(document.myform.selectschool, "124693", "市立旗尾國小", "");
        addOption(document.myform.selectschool, "124694", "市立嶺口國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '美濃區') {
        addOption(document.myform.selectschool, "124697", "市立美濃國小", "");
        addOption(document.myform.selectschool, "124698", "市立東門國小", "");
        addOption(document.myform.selectschool, "124699", "市立吉洋國小", "");
        addOption(document.myform.selectschool, "124700", "市立龍肚國小", "");
        addOption(document.myform.selectschool, "124701", "市立中壇國小", "");
        addOption(document.myform.selectschool, "124702", "市立廣興國小", "");
        addOption(document.myform.selectschool, "124703", "市立龍山國小", "");
        addOption(document.myform.selectschool, "124704", "市立福安國小", "");
        addOption(document.myform.selectschool, "124705", "市立吉東國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '六龜區') {
        addOption(document.myform.selectschool, "124706", "市立六龜國小", "");
        addOption(document.myform.selectschool, "124707", "市立荖濃國小", "");
        addOption(document.myform.selectschool, "124708", "市立新發國小", "");
        addOption(document.myform.selectschool, "124709", "市立龍興國小", "");
        addOption(document.myform.selectschool, "124710", "市立新威國小", "");
        addOption(document.myform.selectschool, "124711", "市立寶來國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '杉林區') {
        addOption(document.myform.selectschool, "124712", "市立月美國小", "");
        addOption(document.myform.selectschool, "124713", "市立上平國小", "");
        addOption(document.myform.selectschool, "124714", "市立新庄國小", "");
        addOption(document.myform.selectschool, "124715", "市立集來國小", "");
        addOption(document.myform.selectschool, "124716", "市立杉林國小", "");
        addOption(document.myform.selectschool, "124730", "市立杉林區民族大愛國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '內門區') {
        addOption(document.myform.selectschool, "124718", "市立內門國小", "");
        addOption(document.myform.selectschool, "124719", "市立觀亭國小", "");
        addOption(document.myform.selectschool, "124720", "市立溝坪國小", "");
        addOption(document.myform.selectschool, "124721", "市立金竹國小", "");
        addOption(document.myform.selectschool, "124722", "市立木柵國小", "");
        addOption(document.myform.selectschool, "124723", "市立西門國小", "");
        addOption(document.myform.selectschool, "124724", "市立景義國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '甲仙區') {
        addOption(document.myform.selectschool, "124725", "市立甲仙國小", "");
        addOption(document.myform.selectschool, "124726", "市立小林國小", "");
        addOption(document.myform.selectschool, "124727", "市立寶隆國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '那瑪夏區') {
        addOption(document.myform.selectschool, "124731", "市立民生國小", "");
        addOption(document.myform.selectschool, "124755", "市立那瑪夏區民權國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '茂林區') {
        addOption(document.myform.selectschool, "124732", "市立茂林國小", "");
        addOption(document.myform.selectschool, "124733", "市立多納國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '桃源區') {
        addOption(document.myform.selectschool, "124734", "市立桃源國小", "");
        addOption(document.myform.selectschool, "124735", "市立建山國小", "");
        addOption(document.myform.selectschool, "124736", "市立興中國小", "");
        addOption(document.myform.selectschool, "124737", "市立寶山國小", "");
        addOption(document.myform.selectschool, "124738", "市立樟山國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鹽埕區') {
        addOption(document.myform.selectschool, "513601", "市立鹽埕國小　", "");
        addOption(document.myform.selectschool, "513602", "市立鹽埕區忠孝國小　", "");
        addOption(document.myform.selectschool, "513603", "市立光榮國小　", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '鼓山區') {
        addOption(document.myform.selectschool, "523601", "市立鼓山區鼓山國小　", "");
        addOption(document.myform.selectschool, "523602", "市立鼓岩國小　", "");
        addOption(document.myform.selectschool, "523603", "市立內惟國小　", "");
        addOption(document.myform.selectschool, "523604", "市立鼓山區中山國小　", "");
        addOption(document.myform.selectschool, "523605", "市立壽山國小　", "");
        addOption(document.myform.selectschool, "523606", "市立龍華國小　", "");
        addOption(document.myform.selectschool, "523607", "市立九如國小　", "");
        addOption(document.myform.selectschool, "521301", "私立明誠高中附設國小", "");
        addOption(document.myform.selectschool, "521303", "私立大榮高中附設國小", "");
        addOption(document.myform.selectschool, "521401", "私立中華藝校附設國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '左營區') {
        addOption(document.myform.selectschool, "533601", "市立左營國小　", "");
        addOption(document.myform.selectschool, "533602", "市立舊城國小", "");
        addOption(document.myform.selectschool, "533603", "市立新莊國小", "");
        addOption(document.myform.selectschool, "533604", "市立新民國小", "");
        addOption(document.myform.selectschool, "533605", "市立明德國小", "");
        addOption(document.myform.selectschool, "533606", "市立勝利國小", "");
        addOption(document.myform.selectschool, "533607", "市立屏山國小", "");
        addOption(document.myform.selectschool, "533608", "市立永清國小", "");
        addOption(document.myform.selectschool, "533609", "市立新上國小", "");
        addOption(document.myform.selectschool, "533610", "市立福山國小", "");
        addOption(document.myform.selectschool, "533611", "市立文府國小", "");
        addOption(document.myform.selectschool, "533612", "市立新光國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '楠梓區') {
        addOption(document.myform.selectschool, "543601", "市立楠梓國小", "");
        addOption(document.myform.selectschool, "543602", "市立後勁國小", "");
        addOption(document.myform.selectschool, "543603", "市立援中國小", "");
        addOption(document.myform.selectschool, "543604", "市立右昌國小", "");
        addOption(document.myform.selectschool, "543605", "市立莒光國小", "");
        addOption(document.myform.selectschool, "543606", "市立加昌國小", "");
        addOption(document.myform.selectschool, "543607", "市立楠陽國小", "");
        addOption(document.myform.selectschool, "543608", "市立翠屏國(中)小", "");
        addOption(document.myform.selectschool, "543609", "市立油廠國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '三民區') {
        addOption(document.myform.selectschool, "553601", "市立三民國小", "");
        addOption(document.myform.selectschool, "553602", "市立鼎金國小", "");
        addOption(document.myform.selectschool, "553603", "市立愛國國小", "");
        addOption(document.myform.selectschool, "553604", "市立十全國小", "");
        addOption(document.myform.selectschool, "553605", "市立正興國小", "");
        addOption(document.myform.selectschool, "553606", "市立博愛國小", "");
        addOption(document.myform.selectschool, "553607", "市立獅湖國小", "");
        addOption(document.myform.selectschool, "553608", "市立三民區民族國小", "");
        addOption(document.myform.selectschool, "553609", "市立莊敬國小", "");
        addOption(document.myform.selectschool, "553610", "市立光武國小", "");
        addOption(document.myform.selectschool, "553611", "市立東光國小", "");
        addOption(document.myform.selectschool, "553612", "市立河濱國小", "");
        addOption(document.myform.selectschool, "553613", "市立陽明國小", "");
        addOption(document.myform.selectschool, "553614", "市立河堤國小", "");
    }

    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '新興區') {
        addOption(document.myform.selectschool, "563601", "市立新興區新興國小　", "");
        addOption(document.myform.selectschool, "563602", "市立大同國小　", "");
        addOption(document.myform.selectschool, "563603", "市立信義國小　", "");
        addOption(document.myform.selectschool, "563604", "市立七賢國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '前金區') {
        addOption(document.myform.selectschool, "573601", "市立前金國小　", "");
        addOption(document.myform.selectschool, "573602", "市立建國國小　", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '苓雅區') {
        addOption(document.myform.selectschool, "583601", "市立苓洲國小　", "");
        addOption(document.myform.selectschool, "583602", "市立苓雅區成功國小", "");
        addOption(document.myform.selectschool, "583603", "市立五權國小　", "");
        addOption(document.myform.selectschool, "583604", "市立凱旋國小", "");
        addOption(document.myform.selectschool, "583605", "市立四維國小　", "");
        addOption(document.myform.selectschool, "583606", "市立福東國小　", "");
        addOption(document.myform.selectschool, "583607", "市立苓雅區中正國小　", "");
        addOption(document.myform.selectschool, "583608", "市立福康國小", "");
        addOption(document.myform.selectschool, "580301", "國立高師大附中附設國小", "");
        addOption(document.myform.selectschool, "583F01", "市立高雄啟智學校", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '前鎮區') {
        addOption(document.myform.selectschool, "593601", "市立前鎮國小　", "");
        addOption(document.myform.selectschool, "593602", "市立獅甲國小　", "");
        addOption(document.myform.selectschool, "593603", "市立仁愛國小　", "");
        addOption(document.myform.selectschool, "593604", "市立樂群國小　", "");
        addOption(document.myform.selectschool, "593605", "市立愛群國小　", "");
        addOption(document.myform.selectschool, "593606", "市立復興國小　", "");
        addOption(document.myform.selectschool, "593607", "市立瑞豐國小　", "");
        addOption(document.myform.selectschool, "593608", "市立明正國小　", "");
        addOption(document.myform.selectschool, "593609", "市立光華國小　", "");
        addOption(document.myform.selectschool, "593610", "市立瑞祥國小", "");
        addOption(document.myform.selectschool, "593611", "市立鎮昌國小", "");
        addOption(document.myform.selectschool, "593612", "市立佛公國小", "");
        addOption(document.myform.selectschool, "593613", "市立前鎮區民權國小", "");
        addOption(document.myform.selectschool, "593614", "市立紅毛港國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '旗津區') {
        addOption(document.myform.selectschool, "603601", "市立旗津國小", "");
        addOption(document.myform.selectschool, "603602", "市立大汕國小", "");
        addOption(document.myform.selectschool, "603603", "市立中洲國小", "");
    }
    if (document.myform.selectcity.value == '高雄市' && document.myform.selectdistrict.value == '小港區') {
        addOption(document.myform.selectschool, "613601", "市立小港國小", "");
        addOption(document.myform.selectschool, "613602", "市立鳳林國小", "");
        addOption(document.myform.selectschool, "613604", "市立青山國小", "");
        addOption(document.myform.selectschool, "613605", "市立太平國小", "");
        addOption(document.myform.selectschool, "613606", "市立鳳鳴國小", "");
        addOption(document.myform.selectschool, "613607", "市立坪頂國小", "");
        addOption(document.myform.selectschool, "613608", "市立二苓國小", "");
        addOption(document.myform.selectschool, "613609", "市立桂林國小", "");
        addOption(document.myform.selectschool, "613610", "市立漢民國小", "");
        addOption(document.myform.selectschool, "613611", "市立華山國小", "");
        addOption(document.myform.selectschool, "613612", "市立港和國小", "");
        addOption(document.myform.selectschool, "613613", "市立鳳陽國小", "");
        addOption(document.myform.selectschool, "613614", "市立明義國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '宜蘭市') {
        addOption(document.myform.selectschool, "024601", "縣立中山國小", "");
        addOption(document.myform.selectschool, "024602", "縣立宜蘭國小", "");
        addOption(document.myform.selectschool, "024603", "縣立力行國小", "");
        addOption(document.myform.selectschool, "024604", "縣立新生國小", "");
        addOption(document.myform.selectschool, "024605", "縣立光復國小", "");
        addOption(document.myform.selectschool, "024606", "縣立育才國小", "");
        addOption(document.myform.selectschool, "024607", "縣立凱旋國小", "");
        addOption(document.myform.selectschool, "024608", "縣立黎明國小", "");
        addOption(document.myform.selectschool, "024609", "縣立南屏國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '羅東鎮') {
        addOption(document.myform.selectschool, "024610", "縣立羅東國小", "");
        addOption(document.myform.selectschool, "024611", "縣立成功國小", "");
        addOption(document.myform.selectschool, "024612", "縣立公正國小", "");
        addOption(document.myform.selectschool, "024613", "縣立北成國小", "");
        addOption(document.myform.selectschool, "024614", "縣立竹林國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '蘇澳鎮') {
        addOption(document.myform.selectschool, "024615", "縣立蘇澳國小", "");
        addOption(document.myform.selectschool, "024616", "縣立馬賽國小", "");
        addOption(document.myform.selectschool, "024617", "縣立蓬萊國小", "");
        addOption(document.myform.selectschool, "024618", "縣立士敏國小", "");
        addOption(document.myform.selectschool, "024619", "縣立永樂國小", "");
        addOption(document.myform.selectschool, "024620", "縣立南安國小", "");
        addOption(document.myform.selectschool, "024621", "縣立岳明國小", "");
        addOption(document.myform.selectschool, "024622", "縣立育英國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '頭城鎮') {
        addOption(document.myform.selectschool, "024623", "縣立頭城國小", "");
        addOption(document.myform.selectschool, "024624", "縣立竹安國小", "");
        addOption(document.myform.selectschool, "024625", "縣立二城國小", "");
        addOption(document.myform.selectschool, "024626", "縣立大溪國小", "");
        addOption(document.myform.selectschool, "024627", "縣立大里國小", "");
        addOption(document.myform.selectschool, "024628", "縣立梗枋國小", "");
        addOption(document.myform.selectschool, "024698", "縣立人文國(中)小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '礁溪鄉') {
        addOption(document.myform.selectschool, "024629", "縣立礁溪國小", "");
        addOption(document.myform.selectschool, "024630", "縣立四結國小", "");
        addOption(document.myform.selectschool, "024631", "縣立龍潭國小", "");
        addOption(document.myform.selectschool, "024632", "縣立玉田國小", "");
        addOption(document.myform.selectschool, "024633", "縣立三民國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '員山鄉') {
        addOption(document.myform.selectschool, "024634", "縣立員山國小", "");
        addOption(document.myform.selectschool, "024635", "縣立深溝國小", "");
        addOption(document.myform.selectschool, "024636", "縣立七賢國小", "");
        addOption(document.myform.selectschool, "024637", "縣立同樂國小", "");
        addOption(document.myform.selectschool, "024638", "縣立湖山國小", "");
        addOption(document.myform.selectschool, "024639", "縣立大湖國小", "");
        addOption(document.myform.selectschool, "024640", "縣立內城國(中)小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '壯圍鄉') {
        addOption(document.myform.selectschool, "024643", "縣立壯圍國小", "");
        addOption(document.myform.selectschool, "024644", "縣立古亭國小", "");
        addOption(document.myform.selectschool, "024645", "縣立公館國小", "");
        addOption(document.myform.selectschool, "024646", "縣立過嶺國小", "");
        addOption(document.myform.selectschool, "024647", "縣立大福國小", "");
        addOption(document.myform.selectschool, "024648", "縣立新南國小", "");
        addOption(document.myform.selectschool, "021310", "私立中道高中附設國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '五結鄉') {
        addOption(document.myform.selectschool, "024649", "縣立五結國小", "");
        addOption(document.myform.selectschool, "024650", "縣立學進國小", "");
        addOption(document.myform.selectschool, "024651", "縣立中興國小", "");
        addOption(document.myform.selectschool, "024652", "縣立利澤國小", "");
        addOption(document.myform.selectschool, "024653", "縣立孝威國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '冬山鄉') {
        addOption(document.myform.selectschool, "024654", "縣立冬山國小", "");
        addOption(document.myform.selectschool, "024655", "縣立東興國小", "");
        addOption(document.myform.selectschool, "024656", "縣立順安國小", "");
        addOption(document.myform.selectschool, "024657", "縣立武淵國小", "");
        addOption(document.myform.selectschool, "024658", "縣立廣興國小", "");
        addOption(document.myform.selectschool, "024659", "縣立大進國小", "");
        addOption(document.myform.selectschool, "024660", "縣立柯林國小", "");
        addOption(document.myform.selectschool, "024699", "縣立慈心華德福實驗國(中)小", "");
        addOption(document.myform.selectschool, "024701", "縣立清溝國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '三星鄉') {
        addOption(document.myform.selectschool, "024661", "縣立三星國小", "");
        addOption(document.myform.selectschool, "024662", "縣立大洲國小", "");
        addOption(document.myform.selectschool, "024663", "縣立憲明國小", "");
        addOption(document.myform.selectschool, "024664", "縣立萬富國小", "");
        addOption(document.myform.selectschool, "024665", "縣立大隱國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '大同鄉') {
        addOption(document.myform.selectschool, "024668", "縣立四季國小", "");
        addOption(document.myform.selectschool, "024669", "縣立南山國小", "");
        addOption(document.myform.selectschool, "024670", "縣立大同國小", "");
        addOption(document.myform.selectschool, "024671", "縣立寒溪國小", "");
    }
    if (document.myform.selectcity.value == '宜蘭縣' && document.myform.selectdistrict.value == '南澳鄉') {
        addOption(document.myform.selectschool, "024672", "縣立南澳國小", "");
        addOption(document.myform.selectschool, "024673", "縣立碧候國小", "");
        addOption(document.myform.selectschool, "024674", "縣立武塔國小", "");
        addOption(document.myform.selectschool, "024675", "縣立澳花國小", "");
        addOption(document.myform.selectschool, "024676", "縣立東澳國小", "");
        addOption(document.myform.selectschool, "024677", "縣立金岳國小", "");
        addOption(document.myform.selectschool, "024678", "縣立金洋國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '龍潭區') {
        addOption(document.myform.selectschool, "031601", "私立福祿貝爾國小", "");
        addOption(document.myform.selectschool, "031602", "私立諾瓦國小", "");
        addOption(document.myform.selectschool, "034722", "市立龍潭國小", "");
        addOption(document.myform.selectschool, "034723", "市立德龍國小", "");
        addOption(document.myform.selectschool, "034724", "市立潛龍國小", "");
        addOption(document.myform.selectschool, "034725", "市立石門國小", "");
        addOption(document.myform.selectschool, "034726", "市立高原國小", "");
        addOption(document.myform.selectschool, "034727", "市立龍源國小", "");
        addOption(document.myform.selectschool, "034728", "市立三和國小", "");
        addOption(document.myform.selectschool, "034729", "市立武漢國小", "");
        addOption(document.myform.selectschool, "034755", "市立龍星國小", "");
        addOption(document.myform.selectschool, "034769", "市立三坑國小", "");
        addOption(document.myform.selectschool, "034785", "市立雙龍國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '中壢區') {
        addOption(document.myform.selectschool, "031603", "私立有得國(中)小", "");
        addOption(document.myform.selectschool, "034666", "市立中壢國小", "");
        addOption(document.myform.selectschool, "034667", "市立中平國小", "");
        addOption(document.myform.selectschool, "034668", "市立新明國小", "");
        addOption(document.myform.selectschool, "034669", "市立芭里國小", "");
        addOption(document.myform.selectschool, "034670", "市立新街國小", "");
        addOption(document.myform.selectschool, "034671", "市立信義國小", "");
        addOption(document.myform.selectschool, "034672", "市立普仁國小", "");
        addOption(document.myform.selectschool, "034673", "市立富台國小", "");
        addOption(document.myform.selectschool, "034674", "市立青埔國小", "");
        addOption(document.myform.selectschool, "034675", "市立內壢國小", "");
        addOption(document.myform.selectschool, "034676", "市立大崙國小", "");
        addOption(document.myform.selectschool, "034677", "市立山東國小", "");
        addOption(document.myform.selectschool, "034678", "市立中正國小", "");
        addOption(document.myform.selectschool, "034679", "市立自立國小", "");
        addOption(document.myform.selectschool, "034680", "市立龍岡國小", "");
        addOption(document.myform.selectschool, "034681", "市立內定國小", "");
        addOption(document.myform.selectschool, "034745", "市立興國國小", "");
        addOption(document.myform.selectschool, "034746", "市立華勛國小", "");
        addOption(document.myform.selectschool, "034753", "市立林森國小", "");
        addOption(document.myform.selectschool, "034764", "市立忠福國小", "");
        addOption(document.myform.selectschool, "034765", "市立興仁國小", "");
        addOption(document.myform.selectschool, "034773", "市立中原國小", "");
        addOption(document.myform.selectschool, "034774", "市立元生國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '桃園區') {
        addOption(document.myform.selectschool, "031604", "私立康萊爾國(中)小", "");
        addOption(document.myform.selectschool, "034601", "市立桃園國小", "");
        addOption(document.myform.selectschool, "034602", "市立東門國小", "");
        addOption(document.myform.selectschool, "034603", "市立中埔國小", "");
        addOption(document.myform.selectschool, "034604", "市立成功國小", "");
        addOption(document.myform.selectschool, "034605", "市立會稽國小", "");
        addOption(document.myform.selectschool, "034606", "市立建國國小", "");
        addOption(document.myform.selectschool, "034607", "市立中山國小", "");
        addOption(document.myform.selectschool, "034608", "市立文山國小", "");
        addOption(document.myform.selectschool, "034609", "市立南門國小", "");
        addOption(document.myform.selectschool, "034610", "市立西門國小", "");
        addOption(document.myform.selectschool, "034611", "市立龍山國小", "");
        addOption(document.myform.selectschool, "034612", "市立北門國小", "");
        addOption(document.myform.selectschool, "034743", "市立青溪國小", "");
        addOption(document.myform.selectschool, "034747", "市立同安國小", "");
        addOption(document.myform.selectschool, "034752", "市立建德國小", "");
        addOption(document.myform.selectschool, "034756", "市立大有國小", "");
        addOption(document.myform.selectschool, "034758", "市立慈文國小", "");
        addOption(document.myform.selectschool, "034759", "市立大業國小", "");
        addOption(document.myform.selectschool, "034760", "市立同德國小", "");
        addOption(document.myform.selectschool, "034775", "市立莊敬國小", "");
        addOption(document.myform.selectschool, "034780", "市立快樂國小", "");
        addOption(document.myform.selectschool, "034781", "市立永順國小", "");
        addOption(document.myform.selectschool, "034782", "市立新埔國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '蘆竹區') {
        addOption(document.myform.selectschool, "034613", "市立南崁國小", "");
        addOption(document.myform.selectschool, "034614", "市立公埔國小", "");
        addOption(document.myform.selectschool, "034615", "市立蘆竹國小", "");
        addOption(document.myform.selectschool, "034616", "市立大竹國小", "");
        addOption(document.myform.selectschool, "034617", "市立新興國小", "");
        addOption(document.myform.selectschool, "034618", "市立外社國小", "");
        addOption(document.myform.selectschool, "034619", "市立頂社國小", "");
        addOption(document.myform.selectschool, "034620", "市立海湖國小", "");
        addOption(document.myform.selectschool, "034621", "市立山腳國小", "");
        addOption(document.myform.selectschool, "034622", "市立大華國小", "");
        addOption(document.myform.selectschool, "034623", "市立新莊國小", "");
        addOption(document.myform.selectschool, "034744", "市立錦興國小", "");
        addOption(document.myform.selectschool, "034761", "市立光明國小", "");
        addOption(document.myform.selectschool, "034786", "市立龍安國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '大園區') {
        addOption(document.myform.selectschool, "034624", "市立大園國小", "");
        addOption(document.myform.selectschool, "034625", "市立圳頭國小", "");
        addOption(document.myform.selectschool, "034626", "市立內海國小", "");
        addOption(document.myform.selectschool, "034627", "市立溪海國小", "");
        addOption(document.myform.selectschool, "034628", "市立潮音國小", "");
        addOption(document.myform.selectschool, "034629", "市立竹圍國小", "");
        addOption(document.myform.selectschool, "034630", "市立果林國小", "");
        addOption(document.myform.selectschool, "034631", "市立后厝國小", "");
        addOption(document.myform.selectschool, "034632", "市立沙崙國小", "");
        addOption(document.myform.selectschool, "034633", "市立埔心國小", "");
        addOption(document.myform.selectschool, "034634", "市立五權國小", "");
        addOption(document.myform.selectschool, "034635", "市立陳康國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '龜山區') {
        addOption(document.myform.selectschool, "034636", "市立龜山國小", "");
        addOption(document.myform.selectschool, "034637", "市立壽山國小", "");
        addOption(document.myform.selectschool, "034638", "市立福源國小", "");
        addOption(document.myform.selectschool, "034639", "市立大崗國小", "");
        addOption(document.myform.selectschool, "034640", "市立大埔國小", "");
        addOption(document.myform.selectschool, "034641", "市立大坑國小", "");
        addOption(document.myform.selectschool, "034642", "市立山頂國小", "");
        addOption(document.myform.selectschool, "034643", "市立龍壽國小", "");
        addOption(document.myform.selectschool, "034644", "市立新路國小", "");
        addOption(document.myform.selectschool, "034645", "市立樂善國小", "");
        addOption(document.myform.selectschool, "034751", "市立迴龍國(中)小", "");
        addOption(document.myform.selectschool, "034757", "市立幸福國小", "");
        addOption(document.myform.selectschool, "034762", "市立文華國小", "");
        addOption(document.myform.selectschool, "034770", "市立楓樹國小", "");
        addOption(document.myform.selectschool, "034772", "市立南美國小", "");
        addOption(document.myform.selectschool, "034776", "市立自強國小", "");
        addOption(document.myform.selectschool, "034784", "市立文欣國小", "");
        addOption(document.myform.selectschool, "034787", "市立長庚國小", "");
        addOption(document.myform.selectschool, "034789", "市立大湖國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '八德區') {
        addOption(document.myform.selectschool, "034646", "市立大成國小", "");
        addOption(document.myform.selectschool, "034647", "市立大勇國小", "");
        addOption(document.myform.selectschool, "034648", "市立八德國小", "");
        addOption(document.myform.selectschool, "034649", "市立瑞豐國小", "");
        addOption(document.myform.selectschool, "034650", "市立霄裡國小", "");
        addOption(document.myform.selectschool, "034651", "市立大安國小", "");
        addOption(document.myform.selectschool, "034652", "市立茄苳國小", "");
        addOption(document.myform.selectschool, "034653", "市立廣興國小", "");
        addOption(document.myform.selectschool, "034748", "市立大忠國小", "");
        addOption(document.myform.selectschool, "031320", "私立新興高中附設國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '大溪區') {
        addOption(document.myform.selectschool, "034654", "市立大溪國小", "");
        addOption(document.myform.selectschool, "034655", "市立美華國小", "");
        addOption(document.myform.selectschool, "034656", "市立內柵國小", "");
        addOption(document.myform.selectschool, "034657", "市立福安國小", "");
        addOption(document.myform.selectschool, "034658", "市立百吉國小", "");
        addOption(document.myform.selectschool, "034659", "市立瑞祥國小", "");
        addOption(document.myform.selectschool, "034660", "市立中興國小", "");
        addOption(document.myform.selectschool, "034661", "市立員樹林國小", "");
        addOption(document.myform.selectschool, "034662", "市立仁善國小", "");
        addOption(document.myform.selectschool, "034663", "市立僑愛國小", "");
        addOption(document.myform.selectschool, "034664", "市立南興國小", "");
        addOption(document.myform.selectschool, "034665", "市立永福國小", "");
        addOption(document.myform.selectschool, "034763", "市立田心國小", "");
        addOption(document.myform.selectschool, "034788", "市立仁和國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '平鎮區') {
        addOption(document.myform.selectschool, "034682", "市立南勢國小", "");
        addOption(document.myform.selectschool, "034683", "市立宋屋國小", "");
        addOption(document.myform.selectschool, "034684", "市立新勢國小", "");
        addOption(document.myform.selectschool, "034685", "市立忠貞國小", "");
        addOption(document.myform.selectschool, "034686", "市立東勢國小", "");
        addOption(document.myform.selectschool, "034687", "市立山豐國小", "");
        addOption(document.myform.selectschool, "034688", "市立復旦國小", "");
        addOption(document.myform.selectschool, "034689", "市立北勢國小", "");
        addOption(document.myform.selectschool, "034742", "市立東安國小", "");
        addOption(document.myform.selectschool, "034750", "市立祥安國小", "");
        addOption(document.myform.selectschool, "034754", "市立文化國小", "");
        addOption(document.myform.selectschool, "034766", "市立平興國小", "");
        addOption(document.myform.selectschool, "034767", "市立義興國小", "");
        addOption(document.myform.selectschool, "034778", "市立新榮國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '楊梅區') {
        addOption(document.myform.selectschool, "034690", "市立楊梅國小", "");
        addOption(document.myform.selectschool, "034691", "市立上田國小", "");
        addOption(document.myform.selectschool, "034692", "市立大同國小", "");
        addOption(document.myform.selectschool, "034693", "市立富岡國小", "");
        addOption(document.myform.selectschool, "034694", "市立瑞原國小", "");
        addOption(document.myform.selectschool, "034695", "市立上湖國小", "");
        addOption(document.myform.selectschool, "034696", "市立水美國小", "");
        addOption(document.myform.selectschool, "034697", "市立瑞埔國小", "");
        addOption(document.myform.selectschool, "034698", "市立高榮國小", "");
        addOption(document.myform.selectschool, "034699", "市立四維國小", "");
        addOption(document.myform.selectschool, "034700", "市立瑞梅國小", "");
        addOption(document.myform.selectschool, "034749", "市立楊明國小", "");
        addOption(document.myform.selectschool, "034768", "市立瑞塘國小", "");
        addOption(document.myform.selectschool, "034771", "市立楊心國小", "");
        addOption(document.myform.selectschool, "034779", "市立楊光國(中)小", "");
        addOption(document.myform.selectschool, "034529", "市立仁美國中附設國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '新屋區') {
        addOption(document.myform.selectschool, "034701", "市立新屋國小", "");
        addOption(document.myform.selectschool, "034702", "市立啟文國小", "");
        addOption(document.myform.selectschool, "034703", "市立東明國小", "");
        addOption(document.myform.selectschool, "034704", "市立頭洲國小", "");
        addOption(document.myform.selectschool, "034705", "市立永安國小", "");
        addOption(document.myform.selectschool, "034706", "市立笨港國小", "");
        addOption(document.myform.selectschool, "034707", "市立北湖國小", "");
        addOption(document.myform.selectschool, "034708", "市立大坡國小", "");
        addOption(document.myform.selectschool, "034709", "市立蚵間國小", "");
        addOption(document.myform.selectschool, "034710", "市立社子國小", "");
        addOption(document.myform.selectschool, "034711", "市立埔頂國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '觀音區') {
        addOption(document.myform.selectschool, "034712", "市立觀音國小", "");
        addOption(document.myform.selectschool, "034713", "市立大潭國小", "");
        addOption(document.myform.selectschool, "034714", "市立保生國小", "");
        addOption(document.myform.selectschool, "034715", "市立新坡國小", "");
        addOption(document.myform.selectschool, "034716", "市立崙坪國小", "");
        addOption(document.myform.selectschool, "034717", "市立上大國小", "");
        addOption(document.myform.selectschool, "034718", "市立育仁國小", "");
        addOption(document.myform.selectschool, "034719", "市立草漯國小", "");
        addOption(document.myform.selectschool, "034720", "市立富林國小", "");
        addOption(document.myform.selectschool, "034721", "市立樹林國小", "");
    }
    if (document.myform.selectcity.value == '桃園市' && document.myform.selectdistrict.value == '復興區') {
        addOption(document.myform.selectschool, "034730", "市立介壽國小", "");
        addOption(document.myform.selectschool, "034731", "市立三民國小", "");
        addOption(document.myform.selectschool, "034732", "市立義盛國小", "");
        addOption(document.myform.selectschool, "034733", "市立霞雲國小", "");
        addOption(document.myform.selectschool, "034734", "市立奎輝國小", "");
        addOption(document.myform.selectschool, "034735", "市立光華國小", "");
        addOption(document.myform.selectschool, "034736", "市立高義國小", "");
        addOption(document.myform.selectschool, "034737", "市立長興國小", "");
        addOption(document.myform.selectschool, "034738", "市立三光國小", "");
        addOption(document.myform.selectschool, "034740", "市立羅浮國小", "");
        addOption(document.myform.selectschool, "034741", "市立巴崚國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '竹東鎮') {
        addOption(document.myform.selectschool, "041601", "私立上智國小", "");
        addOption(document.myform.selectschool, "044619", "縣立竹東國小", "");
        addOption(document.myform.selectschool, "044620", "縣立中山國小", "");
        addOption(document.myform.selectschool, "044621", "縣立大同國小", "");
        addOption(document.myform.selectschool, "044622", "縣立二重國小", "");
        addOption(document.myform.selectschool, "044623", "縣立竹中國小", "");
        addOption(document.myform.selectschool, "044624", "縣立員崠國小", "");
        addOption(document.myform.selectschool, "044625", "縣立陸豐國小", "");
        addOption(document.myform.selectschool, "044626", "縣立瑞峰國小", "");
        addOption(document.myform.selectschool, "044679", "縣立上館國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '竹北市') {
        addOption(document.myform.selectschool, "041602", "私立康乃薾國(中)小", "");
        addOption(document.myform.selectschool, "044627", "縣立竹北國小", "");
        addOption(document.myform.selectschool, "044628", "縣立中正國小", "");
        addOption(document.myform.selectschool, "044629", "縣立竹仁國小", "");
        addOption(document.myform.selectschool, "044630", "縣立新社國小", "");
        addOption(document.myform.selectschool, "044631", "縣立六家國小", "");
        addOption(document.myform.selectschool, "044632", "縣立東海國小", "");
        addOption(document.myform.selectschool, "044633", "縣立豐田國小", "");
        addOption(document.myform.selectschool, "044634", "縣立麻園國小", "");
        addOption(document.myform.selectschool, "044635", "縣立新港國小", "");
        addOption(document.myform.selectschool, "044636", "縣立鳳岡國小", "");
        addOption(document.myform.selectschool, "044678", "縣立博愛國小", "");
        addOption(document.myform.selectschool, "044680", "縣立光明國小", "");
        addOption(document.myform.selectschool, "044682", "縣立十興國小", "");
        addOption(document.myform.selectschool, "044683", "縣立興隆國小", "");
        addOption(document.myform.selectschool, "044684", "縣立東興國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '關西鎮') {
        addOption(document.myform.selectschool, "044601", "縣立關西國小", "");
        addOption(document.myform.selectschool, "044602", "縣立東安國小", "");
        addOption(document.myform.selectschool, "044603", "縣立石光國小", "");
        addOption(document.myform.selectschool, "044604", "縣立坪林國小", "");
        addOption(document.myform.selectschool, "044605", "縣立南和國小", "");
        addOption(document.myform.selectschool, "044606", "縣立太平國小", "");
        addOption(document.myform.selectschool, "044607", "縣立東光國小", "");
        addOption(document.myform.selectschool, "044608", "縣立錦山國小", "");
        addOption(document.myform.selectschool, "044609", "縣立玉山國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '新埔鎮') {
        addOption(document.myform.selectschool, "044610", "縣立新埔國小", "");
        addOption(document.myform.selectschool, "044611", "縣立新星國小", "");
        addOption(document.myform.selectschool, "044612", "縣立照門國小", "");
        addOption(document.myform.selectschool, "044613", "縣立清水國小", "");
        addOption(document.myform.selectschool, "044614", "縣立照東國小", "");
        addOption(document.myform.selectschool, "044615", "縣立北平國小", "");
        addOption(document.myform.selectschool, "044616", "縣立枋寮國小", "");
        addOption(document.myform.selectschool, "044617", "縣立寶石國小", "");
        addOption(document.myform.selectschool, "044618", "縣立文山國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '湖口鄉') {
        addOption(document.myform.selectschool, "044637", "縣立新湖國小", "");
        addOption(document.myform.selectschool, "044638", "縣立和興國小", "");
        addOption(document.myform.selectschool, "044639", "縣立信勢國小", "");
        addOption(document.myform.selectschool, "044640", "縣立湖口國小", "");
        addOption(document.myform.selectschool, "044642", "縣立長安國小", "");
        addOption(document.myform.selectschool, "044643", "縣立中興國小", "");
        addOption(document.myform.selectschool, "044644", "縣立華興國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '新豐鄉') {
        addOption(document.myform.selectschool, "044641", "縣立山崎國小", "");
        addOption(document.myform.selectschool, "044650", "縣立福興國小", "");
        addOption(document.myform.selectschool, "044651", "縣立新豐國小", "");
        addOption(document.myform.selectschool, "044652", "縣立瑞興國小", "");
        addOption(document.myform.selectschool, "044653", "縣立福龍國小", "");
        addOption(document.myform.selectschool, "044654", "縣立埔和國小", "");
        addOption(document.myform.selectschool, "044681", "縣立松林國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '橫山鄉') {
        addOption(document.myform.selectschool, "044645", "縣立橫山國小", "");
        addOption(document.myform.selectschool, "044646", "縣立田寮國小", "");
        addOption(document.myform.selectschool, "044647", "縣立大肚國小", "");
        addOption(document.myform.selectschool, "044648", "縣立沙坑國小", "");
        addOption(document.myform.selectschool, "044649", "縣立內灣國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '芎林鄉') {
        addOption(document.myform.selectschool, "044655", "縣立芎林國小", "");
        addOption(document.myform.selectschool, "044656", "縣立碧潭國小", "");
        addOption(document.myform.selectschool, "044657", "縣立五龍國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '寶山鄉') {
        addOption(document.myform.selectschool, "044658", "縣立寶山國小", "");
        addOption(document.myform.selectschool, "044659", "縣立新城國小", "");
        addOption(document.myform.selectschool, "044660", "縣立雙溪國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '北埔鄉') {
        addOption(document.myform.selectschool, "044662", "縣立北埔國小", "");
        addOption(document.myform.selectschool, "044663", "縣立大坪國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '峨眉鄉') {
        addOption(document.myform.selectschool, "044664", "縣立峨眉國小", "");
        addOption(document.myform.selectschool, "044665", "縣立富興國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '尖石鄉') {
        addOption(document.myform.selectschool, "044666", "縣立尖石國小", "");
        addOption(document.myform.selectschool, "044667", "縣立嘉興國小", "");
        addOption(document.myform.selectschool, "044668", "縣立新樂國小", "");
        addOption(document.myform.selectschool, "044669", "縣立梅花國小", "");
        addOption(document.myform.selectschool, "044670", "縣立錦屏國小", "");
        addOption(document.myform.selectschool, "044671", "縣立玉峰國小", "");
        addOption(document.myform.selectschool, "044672", "縣立石磊國小", "");
        addOption(document.myform.selectschool, "044673", "縣立秀巒國小", "");
        addOption(document.myform.selectschool, "044674", "縣立新光國小", "");
    }
    if (document.myform.selectcity.value == '新竹縣' && document.myform.selectdistrict.value == '五峰鄉') {
        addOption(document.myform.selectschool, "044675", "縣立五峰國小", "");
        addOption(document.myform.selectschool, "044676", "縣立桃山國小", "");
        addOption(document.myform.selectschool, "044677", "縣立花園國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '苗栗市') {
        addOption(document.myform.selectschool, "054601", "縣立建功國小", "");
        addOption(document.myform.selectschool, "054602", "縣立大同國小", "");
        addOption(document.myform.selectschool, "054603", "縣立僑育國小", "");
        addOption(document.myform.selectschool, "054604", "縣立文山國小", "");
        addOption(document.myform.selectschool, "054605", "縣立啟文國小", "");
        addOption(document.myform.selectschool, "054606", "縣立新英國小", "");
        addOption(document.myform.selectschool, "054717", "縣立文華國小", "");
        addOption(document.myform.selectschool, "054718", "縣立福星國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '頭屋鄉') {
        addOption(document.myform.selectschool, "054607", "縣立頭屋國小", "");
        addOption(document.myform.selectschool, "054608", "縣立明德國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '公館鄉') {
        addOption(document.myform.selectschool, "054609", "縣立公館國小", "");
        addOption(document.myform.selectschool, "054610", "縣立五穀國小", "");
        addOption(document.myform.selectschool, "054611", "縣立福基國小", "");
        addOption(document.myform.selectschool, "054612", "縣立鶴岡國小", "");
        addOption(document.myform.selectschool, "054614", "縣立開礦國小", "");
        addOption(document.myform.selectschool, "054615", "縣立南河國小", "");
        addOption(document.myform.selectschool, "054721", "縣立仁愛國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '銅鑼鄉') {
        addOption(document.myform.selectschool, "054616", "縣立銅鑼國小", "");
        addOption(document.myform.selectschool, "054617", "縣立中興國小", "");
        addOption(document.myform.selectschool, "054618", "縣立九湖國小", "");
        addOption(document.myform.selectschool, "054619", "縣立新隆國小", "");
        addOption(document.myform.selectschool, "054620", "縣立興隆國小", "");
        addOption(document.myform.selectschool, "054621", "縣立文峰國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '三義鄉') {
        addOption(document.myform.selectschool, "054622", "縣立建中國小", "");
        addOption(document.myform.selectschool, "054623", "縣立僑成國小", "");
        addOption(document.myform.selectschool, "054624", "縣立育英國小", "");
        addOption(document.myform.selectschool, "054625", "縣立鯉魚國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '苑裡鎮') {
        addOption(document.myform.selectschool, "054627", "縣立苑裡國小", "");
        addOption(document.myform.selectschool, "054628", "縣立文苑國小", "");
        addOption(document.myform.selectschool, "054629", "縣立山腳國小", "");
        addOption(document.myform.selectschool, "054630", "縣立中正國小", "");
        addOption(document.myform.selectschool, "054631", "縣立藍田國小", "");
        addOption(document.myform.selectschool, "054632", "縣立中山國小", "");
        addOption(document.myform.selectschool, "054633", "縣立林森國小", "");
        addOption(document.myform.selectschool, "054634", "縣立蕉埔國小", "");
        addOption(document.myform.selectschool, "054722", "縣立客庄國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '通霄鎮') {
        addOption(document.myform.selectschool, "054635", "縣立通霄國小", "");
        addOption(document.myform.selectschool, "054636", "縣立五福國小", "");
        addOption(document.myform.selectschool, "054637", "縣立城中國小", "");
        addOption(document.myform.selectschool, "054638", "縣立啟明國小", "");
        addOption(document.myform.selectschool, "054639", "縣立新埔國小", "");
        addOption(document.myform.selectschool, "054640", "縣立烏眉國小", "");
        addOption(document.myform.selectschool, "054641", "縣立南和國小", "");
        addOption(document.myform.selectschool, "054642", "縣立坪頂國小", "");
        addOption(document.myform.selectschool, "054643", "縣立圳頭國小", "");
        addOption(document.myform.selectschool, "054644", "縣立楓樹國小", "");
        addOption(document.myform.selectschool, "054645", "縣立福興武術國(中)小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '西湖鄉') {
        addOption(document.myform.selectschool, "054646", "縣立西湖國小", "");
        addOption(document.myform.selectschool, "054647", "縣立五湖國小", "");
        addOption(document.myform.selectschool, "054648", "縣立僑文國小", "");
        addOption(document.myform.selectschool, "054649", "縣立瑞湖國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '頭份市') {
        addOption(document.myform.selectschool, "054650", "縣立頭份國小", "");
        addOption(document.myform.selectschool, "054651", "縣立六合國小", "");
        addOption(document.myform.selectschool, "054652", "縣立永貞國小", "");
        addOption(document.myform.selectschool, "054653", "縣立尖山國小", "");
        addOption(document.myform.selectschool, "054654", "縣立僑善國小", "");
        addOption(document.myform.selectschool, "054655", "縣立斗煥國小", "");
        addOption(document.myform.selectschool, "054656", "縣立后庄國小", "");
        addOption(document.myform.selectschool, "054657", "縣立新興國小", "");
        addOption(document.myform.selectschool, "054658", "縣立信德國小", "");
        addOption(document.myform.selectschool, "054715", "縣立建國國小", "");
        addOption(document.myform.selectschool, "054720", "縣立蟠桃國小", "");
        addOption(document.myform.selectschool, "054724", "縣立信義國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '竹南鎮') {
        addOption(document.myform.selectschool, "054659", "縣立竹南國小", "");
        addOption(document.myform.selectschool, "054660", "縣立照南國小", "");
        addOption(document.myform.selectschool, "054661", "縣立大埔國小", "");
        addOption(document.myform.selectschool, "054662", "縣立頂埔國小", "");
        addOption(document.myform.selectschool, "054663", "縣立海口國小", "");
        addOption(document.myform.selectschool, "054716", "縣立竹興國小", "");
        addOption(document.myform.selectschool, "054719", "縣立新南國小", "");
        addOption(document.myform.selectschool, "054723", "縣立山佳國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '三灣鄉') {
        addOption(document.myform.selectschool, "054664", "縣立三灣國小", "");
        addOption(document.myform.selectschool, "054667", "縣立大坪國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '南庄鄉') {
        addOption(document.myform.selectschool, "054668", "縣立南庄國小", "");
        addOption(document.myform.selectschool, "054669", "縣立田美國小", "");
        addOption(document.myform.selectschool, "054670", "縣立南埔國小", "");
        addOption(document.myform.selectschool, "054671", "縣立東河國小", "");
        addOption(document.myform.selectschool, "054672", "縣立蓬萊國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '造橋鄉') {
        addOption(document.myform.selectschool, "054673", "縣立造橋國小", "");
        addOption(document.myform.selectschool, "054674", "縣立談文國小", "");
        addOption(document.myform.selectschool, "054675", "縣立錦水國小", "");
        addOption(document.myform.selectschool, "054676", "縣立龍昇國小", "");
        addOption(document.myform.selectschool, "054677", "縣立僑樂國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '後龍鎮') {
        addOption(document.myform.selectschool, "054679", "縣立後龍國小", "");
        addOption(document.myform.selectschool, "054680", "縣立新港國(中)小", "");
        addOption(document.myform.selectschool, "054681", "縣立大山國小", "");
        addOption(document.myform.selectschool, "054683", "縣立龍坑國小", "");
        addOption(document.myform.selectschool, "054684", "縣立溪洲國小", "");
        addOption(document.myform.selectschool, "054685", "縣立外埔國小", "");
        addOption(document.myform.selectschool, "054686", "縣立成功國小", "");
        addOption(document.myform.selectschool, "054687", "縣立中和國小", "");
        addOption(document.myform.selectschool, "054688", "縣立同光國小", "");
        addOption(document.myform.selectschool, "054689", "縣立海寶國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '大湖鄉') {
        addOption(document.myform.selectschool, "054690", "縣立大湖國小", "");
        addOption(document.myform.selectschool, "054691", "縣立南湖國小", "");
        addOption(document.myform.selectschool, "054692", "縣立華興國小", "");
        addOption(document.myform.selectschool, "054693", "縣立大南國小", "");
        addOption(document.myform.selectschool, "054694", "縣立東興國小", "");
        addOption(document.myform.selectschool, "054695", "縣立武榮國小", "");
        addOption(document.myform.selectschool, "054696", "縣立新開國小", "");
        addOption(document.myform.selectschool, "054697", "縣立栗林國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '獅潭鄉') {
        addOption(document.myform.selectschool, "054698", "縣立獅潭國小", "");
        addOption(document.myform.selectschool, "054699", "縣立豐林國小", "");
        addOption(document.myform.selectschool, "054700", "縣立永興國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '卓蘭鎮') {
        addOption(document.myform.selectschool, "054701", "縣立卓蘭國小", "");
        addOption(document.myform.selectschool, "054702", "縣立內灣國小", "");
        addOption(document.myform.selectschool, "054703", "縣立豐田國小", "");
        addOption(document.myform.selectschool, "054704", "縣立坪林國小", "");
        addOption(document.myform.selectschool, "054705", "縣立雙連國小", "");
        addOption(document.myform.selectschool, "054706", "縣立景山國小", "");
    }
    if (document.myform.selectcity.value == '苗栗縣' && document.myform.selectdistrict.value == '泰安鄉') {
        addOption(document.myform.selectschool, "054707", "縣立泰安國(中)小", "");
        addOption(document.myform.selectschool, "054708", "縣立泰興國小", "");
        addOption(document.myform.selectschool, "054709", "縣立清安國小", "");
        addOption(document.myform.selectschool, "054711", "縣立汶水國小", "");
        addOption(document.myform.selectschool, "054712", "縣立象鼻國小", "");
        addOption(document.myform.selectschool, "054714", "縣立梅園國小", "");
        addOption(document.myform.selectschool, "054725", "縣立士林國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '彰化市') {
        addOption(document.myform.selectschool, "074601", "縣立中山國小", "");
        addOption(document.myform.selectschool, "074602", "縣立民生國小", "");
        addOption(document.myform.selectschool, "074603", "縣立平和國小", "");
        addOption(document.myform.selectschool, "074604", "縣立南郭國小", "");
        addOption(document.myform.selectschool, "074605", "縣立南興國小", "");
        addOption(document.myform.selectschool, "074606", "縣立東芳國小", "");
        addOption(document.myform.selectschool, "074607", "縣立泰和國小", "");
        addOption(document.myform.selectschool, "074608", "縣立三民國小", "");
        addOption(document.myform.selectschool, "074609", "縣立聯興國小", "");
        addOption(document.myform.selectschool, "074610", "縣立大竹國小", "");
        addOption(document.myform.selectschool, "074611", "縣立國聖國小", "");
        addOption(document.myform.selectschool, "074612", "縣立快官國小", "");
        addOption(document.myform.selectschool, "074613", "縣立石牌國小", "");
        addOption(document.myform.selectschool, "074614", "縣立忠孝國小", "");
        addOption(document.myform.selectschool, "074774", "縣立信義國(中)小", "");
        addOption(document.myform.selectschool, "074775", "縣立大成國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '芬園鄉') {
        addOption(document.myform.selectschool, "074615", "縣立芬園國小", "");
        addOption(document.myform.selectschool, "074616", "縣立富山國小", "");
        addOption(document.myform.selectschool, "074617", "縣立寶山國小", "");
        addOption(document.myform.selectschool, "074618", "縣立同安國小", "");
        addOption(document.myform.selectschool, "074619", "縣立文德國小", "");
        addOption(document.myform.selectschool, "074620", "縣立茄荖國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '花壇鄉') {
        addOption(document.myform.selectschool, "074621", "縣立花壇國小", "");
        addOption(document.myform.selectschool, "074622", "縣立文祥國小", "");
        addOption(document.myform.selectschool, "074623", "縣立華南國小", "");
        addOption(document.myform.selectschool, "074624", "縣立僑愛國小", "");
        addOption(document.myform.selectschool, "074625", "縣立三春國小", "");
        addOption(document.myform.selectschool, "074626", "縣立白沙國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '和美鎮') {
        addOption(document.myform.selectschool, "074627", "縣立和美國小", "");
        addOption(document.myform.selectschool, "074628", "縣立和東國小", "");
        addOption(document.myform.selectschool, "074629", "縣立大嘉國小", "");
        addOption(document.myform.selectschool, "074630", "縣立大榮國小", "");
        addOption(document.myform.selectschool, "074631", "縣立新庄國小", "");
        addOption(document.myform.selectschool, "074632", "縣立培英國小", "");
        addOption(document.myform.selectschool, "074769", "縣立和仁國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '線西鄉') {
        addOption(document.myform.selectschool, "074633", "縣立線西國小", "");
        addOption(document.myform.selectschool, "074634", "縣立曉陽國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '伸港鄉') {
        addOption(document.myform.selectschool, "074635", "縣立新港國小", "");
        addOption(document.myform.selectschool, "074636", "縣立伸東國小", "");
        addOption(document.myform.selectschool, "074637", "縣立伸仁國小", "");
        addOption(document.myform.selectschool, "074638", "縣立大同國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '鹿港鎮') {
        addOption(document.myform.selectschool, "074639", "縣立鹿港國小", "");
        addOption(document.myform.selectschool, "074640", "縣立文開國小", "");
        addOption(document.myform.selectschool, "074641", "縣立洛津國小", "");
        addOption(document.myform.selectschool, "074642", "縣立海埔國小", "");
        addOption(document.myform.selectschool, "074643", "縣立新興國小", "");
        addOption(document.myform.selectschool, "074644", "縣立草港國小", "");
        addOption(document.myform.selectschool, "074645", "縣立頂番國小", "");
        addOption(document.myform.selectschool, "074646", "縣立東興國小", "");
        addOption(document.myform.selectschool, "074771", "縣立鹿東國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '福興鄉') {
        addOption(document.myform.selectschool, "074647", "縣立管嶼國小", "");
        addOption(document.myform.selectschool, "074649", "縣立西勢國小", "");
        addOption(document.myform.selectschool, "074650", "縣立大興國小", "");
        addOption(document.myform.selectschool, "074651", "縣立永豐國小", "");
        addOption(document.myform.selectschool, "074652", "縣立日新國小", "");
        addOption(document.myform.selectschool, "074653", "縣立育新國小", "");
        addOption(document.myform.selectschool, "074648", "縣立文昌國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '秀水鄉') {
        addOption(document.myform.selectschool, "074654", "縣立秀水國小", "");
        addOption(document.myform.selectschool, "074655", "縣立馬興國小", "");
        addOption(document.myform.selectschool, "074656", "縣立華龍國小", "");
        addOption(document.myform.selectschool, "074657", "縣立明正國小", "");
        addOption(document.myform.selectschool, "074658", "縣立陝西國小", "");
        addOption(document.myform.selectschool, "074659", "縣立育民國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '溪湖鎮') {
        addOption(document.myform.selectschool, "074660", "縣立溪湖國小", "");
        addOption(document.myform.selectschool, "074661", "縣立東溪國小", "");
        addOption(document.myform.selectschool, "074662", "縣立湖西國小", "");
        addOption(document.myform.selectschool, "074663", "縣立湖東國小", "");
        addOption(document.myform.selectschool, "074664", "縣立湖南國小", "");
        addOption(document.myform.selectschool, "074665", "縣立媽厝國小", "");
        addOption(document.myform.selectschool, "074777", "縣立湖北國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '埔鹽鄉') {
        addOption(document.myform.selectschool, "074666", "縣立埔鹽國小", "");
        addOption(document.myform.selectschool, "074667", "縣立大園國小", "");
        addOption(document.myform.selectschool, "074668", "縣立南港國小", "");
        addOption(document.myform.selectschool, "074669", "縣立好修國小", "");
        addOption(document.myform.selectschool, "074670", "縣立永樂國小", "");
        addOption(document.myform.selectschool, "074671", "縣立新水國小", "");
        addOption(document.myform.selectschool, "074672", "縣立天盛國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '埔心鄉') {
        addOption(document.myform.selectschool, "074673", "縣立埔心國小", "");
        addOption(document.myform.selectschool, "074674", "縣立太平國小", "");
        addOption(document.myform.selectschool, "074675", "縣立舊館國小", "");
        addOption(document.myform.selectschool, "074676", "縣立羅厝國小", "");
        addOption(document.myform.selectschool, "074677", "縣立鳳霞國小", "");
        addOption(document.myform.selectschool, "074678", "縣立梧鳳國小", "");
        addOption(document.myform.selectschool, "074679", "縣立明聖國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '員林市') {
        addOption(document.myform.selectschool, "074680", "縣立員林國小", "");
        addOption(document.myform.selectschool, "074681", "縣立育英國小", "");
        addOption(document.myform.selectschool, "074682", "縣立靜修國小", "");
        addOption(document.myform.selectschool, "074683", "縣立僑信國小", "");
        addOption(document.myform.selectschool, "074684", "縣立員東國小", "");
        addOption(document.myform.selectschool, "074685", "縣立饒明國小", "");
        addOption(document.myform.selectschool, "074686", "縣立東山國小", "");
        addOption(document.myform.selectschool, "074687", "縣立青山國小", "");
        addOption(document.myform.selectschool, "074688", "縣立明湖國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '大村鄉') {
        addOption(document.myform.selectschool, "074689", "縣立大村國小", "");
        addOption(document.myform.selectschool, "074690", "縣立大西國小", "");
        addOption(document.myform.selectschool, "074691", "縣立村上國小", "");
        addOption(document.myform.selectschool, "074692", "縣立村東國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '永靖鄉') {
        addOption(document.myform.selectschool, "074693", "縣立永靖國小", "");
        addOption(document.myform.selectschool, "074694", "縣立福德國小", "");
        addOption(document.myform.selectschool, "074695", "縣立永興國小", "");
        addOption(document.myform.selectschool, "074696", "縣立福興國小", "");
        addOption(document.myform.selectschool, "074697", "縣立德興國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '田中鎮') {
        addOption(document.myform.selectschool, "074698", "縣立田中國小", "");
        addOption(document.myform.selectschool, "074699", "縣立三潭國小", "");
        addOption(document.myform.selectschool, "074700", "縣立大安國小", "");
        addOption(document.myform.selectschool, "074701", "縣立內安國小", "");
        addOption(document.myform.selectschool, "074702", "縣立東和國小", "");
        addOption(document.myform.selectschool, "074703", "縣立明禮國小", "");
        addOption(document.myform.selectschool, "074776", "縣立新民國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '社頭鄉') {
        addOption(document.myform.selectschool, "074704", "縣立社頭國小", "");
        addOption(document.myform.selectschool, "074705", "縣立橋頭國小", "");
        addOption(document.myform.selectschool, "074706", "縣立朝興國小", "");
        addOption(document.myform.selectschool, "074707", "縣立清水國小", "");
        addOption(document.myform.selectschool, "074708", "縣立湳雅國小", "");
        addOption(document.myform.selectschool, "074772", "縣立舊社國小", "");
        addOption(document.myform.selectschool, "074773", "縣立崙雅國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '二水鄉') {
        addOption(document.myform.selectschool, "074709", "縣立二水國小", "");
        addOption(document.myform.selectschool, "074710", "縣立復興國小", "");
        addOption(document.myform.selectschool, "074711", "縣立源泉國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '北斗鎮') {
        addOption(document.myform.selectschool, "074712", "縣立北斗國小", "");
        addOption(document.myform.selectschool, "074713", "縣立萬來國小", "");
        addOption(document.myform.selectschool, "074714", "縣立螺青國小", "");
        addOption(document.myform.selectschool, "074715", "縣立大新國小", "");
        addOption(document.myform.selectschool, "074716", "縣立螺陽國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '田尾鄉') {
        addOption(document.myform.selectschool, "074717", "縣立田尾國小", "");
        addOption(document.myform.selectschool, "074719", "縣立陸豐國小", "");
        addOption(document.myform.selectschool, "074720", "縣立仁豐國小", "");
        addOption(document.myform.selectschool, "074718", "縣立南鎮國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '埤頭鄉') {
        addOption(document.myform.selectschool, "074721", "縣立埤頭國小", "");
        addOption(document.myform.selectschool, "074722", "縣立合興國小", "");
        addOption(document.myform.selectschool, "074723", "縣立豐崙國小", "");
        addOption(document.myform.selectschool, "074724", "縣立芙朝國小", "");
        addOption(document.myform.selectschool, "074725", "縣立中和國小", "");
        addOption(document.myform.selectschool, "074726", "縣立大湖國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '溪州鄉') {
        addOption(document.myform.selectschool, "074727", "縣立溪州國小", "");
        addOption(document.myform.selectschool, "074728", "縣立僑義國小", "");
        addOption(document.myform.selectschool, "074729", "縣立三條國小", "");
        addOption(document.myform.selectschool, "074730", "縣立水尾國小", "");
        addOption(document.myform.selectschool, "074731", "縣立潮洋國小", "");
        addOption(document.myform.selectschool, "074732", "縣立成功國小", "");
        addOption(document.myform.selectschool, "074733", "縣立圳寮國小", "");
        addOption(document.myform.selectschool, "074734", "縣立大莊國小", "");
        addOption(document.myform.selectschool, "074735", "縣立南州國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '二林鎮') {
        addOption(document.myform.selectschool, "074736", "縣立二林國小", "");
        addOption(document.myform.selectschool, "074737", "縣立興華國小", "");
        addOption(document.myform.selectschool, "074738", "縣立中正國小", "");
        addOption(document.myform.selectschool, "074739", "縣立育德國小", "");
        addOption(document.myform.selectschool, "074740", "縣立香田國小", "");
        addOption(document.myform.selectschool, "074741", "縣立廣興國小", "");
        addOption(document.myform.selectschool, "074742", "縣立萬興國小", "");
        addOption(document.myform.selectschool, "074743", "縣立新生國小", "");
        addOption(document.myform.selectschool, "074744", "縣立中興國小", "");
        addOption(document.myform.selectschool, "074745", "縣立原斗國小", "");
        addOption(document.myform.selectschool, "074746", "縣立萬合國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '大城鄉') {
        addOption(document.myform.selectschool, "074747", "縣立大城國小", "");
        addOption(document.myform.selectschool, "074748", "縣立永光國小", "");
        addOption(document.myform.selectschool, "074749", "縣立西港國小", "");
        addOption(document.myform.selectschool, "074750", "縣立美豐國小", "");
        addOption(document.myform.selectschool, "074751", "縣立頂庄國小", "");
        addOption(document.myform.selectschool, "074752", "縣立潭墘國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '竹塘鄉') {
        addOption(document.myform.selectschool, "074753", "縣立竹塘國小", "");
        addOption(document.myform.selectschool, "074754", "縣立田頭國小", "");
        addOption(document.myform.selectschool, "074755", "縣立民靖國小", "");
        addOption(document.myform.selectschool, "074756", "縣立長安國小", "");
        addOption(document.myform.selectschool, "074757", "縣立土庫國小", "");
    }
    if (document.myform.selectcity.value == '彰化縣' && document.myform.selectdistrict.value == '芳苑鄉') {
        addOption(document.myform.selectschool, "074758", "縣立芳苑國小", "");
        addOption(document.myform.selectschool, "074759", "縣立後寮國小", "");
        addOption(document.myform.selectschool, "074760", "縣立民權國小", "");
        addOption(document.myform.selectschool, "074761", "縣立育華國小", "");
        addOption(document.myform.selectschool, "074762", "縣立草湖國小", "");
        addOption(document.myform.selectschool, "074763", "縣立建新國小", "");
        addOption(document.myform.selectschool, "074764", "縣立漢寶國小", "");
        addOption(document.myform.selectschool, "074765", "縣立王功國小", "");
        addOption(document.myform.selectschool, "074766", "縣立新寶國小", "");
        addOption(document.myform.selectschool, "074767", "縣立路上國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '埔里鎮') {
        addOption(document.myform.selectschool, "081601", "私立普台國小", "");
        addOption(document.myform.selectschool, "081602", "私立均頭國(中)小", "");
        addOption(document.myform.selectschool, "084615", "縣立埔里國小", "");
        addOption(document.myform.selectschool, "084616", "縣立南光國小", "");
        addOption(document.myform.selectschool, "084617", "縣立育英國小", "");
        addOption(document.myform.selectschool, "084618", "縣立史港國小", "");
        addOption(document.myform.selectschool, "084619", "縣立愛蘭國小", "");
        addOption(document.myform.selectschool, "084620", "縣立溪南國小", "");
        addOption(document.myform.selectschool, "084621", "縣立水尾國小", "");
        addOption(document.myform.selectschool, "084622", "縣立桃源國小", "");
        addOption(document.myform.selectschool, "084623", "縣立麒麟國小", "");
        addOption(document.myform.selectschool, "084624", "縣立太平國小", "");
        addOption(document.myform.selectschool, "084625", "縣立忠孝國小", "");
        addOption(document.myform.selectschool, "084626", "縣立中峰國小", "");
        addOption(document.myform.selectschool, "084627", "縣立大成國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '南投市') {
        addOption(document.myform.selectschool, "084601", "縣立南投國小", "");
        addOption(document.myform.selectschool, "084602", "縣立平和國小", "");
        addOption(document.myform.selectschool, "084603", "縣立新豐國小", "");
        addOption(document.myform.selectschool, "084604", "縣立營盤國小", "");
        addOption(document.myform.selectschool, "084605", "縣立西嶺國小", "");
        addOption(document.myform.selectschool, "084606", "縣立德興國小", "");
        addOption(document.myform.selectschool, "084607", "縣立光華國小", "");
        addOption(document.myform.selectschool, "084608", "縣立光榮國小", "");
        addOption(document.myform.selectschool, "084609", "縣立文山國小", "");
        addOption(document.myform.selectschool, "084610", "縣立僑建國小", "");
        addOption(document.myform.selectschool, "084611", "縣立漳和國小", "");
        addOption(document.myform.selectschool, "084612", "縣立嘉和國小", "");
        addOption(document.myform.selectschool, "084613", "縣立光復國小", "");
        addOption(document.myform.selectschool, "084614", "縣立千秋國小", "");
        addOption(document.myform.selectschool, "084748", "縣立漳興國小", "");
        addOption(document.myform.selectschool, "084750", "縣立康壽國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '草屯鎮') {
        addOption(document.myform.selectschool, "084629", "縣立草屯國小", "");
        addOption(document.myform.selectschool, "084630", "縣立敦和國小", "");
        addOption(document.myform.selectschool, "084631", "縣立新庄國小", "");
        addOption(document.myform.selectschool, "084632", "縣立碧峰國小", "");
        addOption(document.myform.selectschool, "084633", "縣立土城國小", "");
        addOption(document.myform.selectschool, "084634", "縣立雙冬國小", "");
        addOption(document.myform.selectschool, "084635", "縣立炎峰國小", "");
        addOption(document.myform.selectschool, "084636", "縣立中原國小", "");
        addOption(document.myform.selectschool, "084637", "縣立平林國小", "");
        addOption(document.myform.selectschool, "084638", "縣立坪頂國小", "");
        addOption(document.myform.selectschool, "084639", "縣立僑光國小", "");
        addOption(document.myform.selectschool, "084640", "縣立北投國小", "");
        addOption(document.myform.selectschool, "084641", "縣立富功國小", "");
        addOption(document.myform.selectschool, "084749", "縣立虎山國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '竹山鎮') {
        addOption(document.myform.selectschool, "084642", "縣立竹山國小", "");
        addOption(document.myform.selectschool, "084643", "縣立延平國小", "");
        addOption(document.myform.selectschool, "084644", "縣立社寮國小", "");
        addOption(document.myform.selectschool, "084645", "縣立過溪國小", "");
        addOption(document.myform.selectschool, "084646", "縣立大鞍國小", "");
        addOption(document.myform.selectschool, "084647", "縣立瑞竹國小", "");
        addOption(document.myform.selectschool, "084648", "縣立秀林國小", "");
        addOption(document.myform.selectschool, "084649", "縣立雲林國小", "");
        addOption(document.myform.selectschool, "084650", "縣立鯉魚國小", "");
        addOption(document.myform.selectschool, "084651", "縣立桶頭國小", "");
        addOption(document.myform.selectschool, "084652", "縣立中州國小", "");
        addOption(document.myform.selectschool, "084653", "縣立中和國小", "");
        addOption(document.myform.selectschool, "084751", "縣立前山國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '集集鎮') {
        addOption(document.myform.selectschool, "084655", "縣立集集國小", "");
        addOption(document.myform.selectschool, "084656", "縣立隘寮國小", "");
        addOption(document.myform.selectschool, "084657", "縣立永昌國小", "");
        addOption(document.myform.selectschool, "084658", "縣立和平國小", "");
        addOption(document.myform.selectschool, "084659", "縣立富山國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '名間鄉') {
        addOption(document.myform.selectschool, "084660", "縣立名間國小", "");
        addOption(document.myform.selectschool, "084661", "縣立新街國小", "");
        addOption(document.myform.selectschool, "084662", "縣立名崗國小", "");
        addOption(document.myform.selectschool, "084663", "縣立中山國小", "");
        addOption(document.myform.selectschool, "084664", "縣立弓鞋國小", "");
        addOption(document.myform.selectschool, "084665", "縣立田豐國小", "");
        addOption(document.myform.selectschool, "084666", "縣立僑興國小", "");
        addOption(document.myform.selectschool, "084667", "縣立新民國小", "");
        addOption(document.myform.selectschool, "081313", "私立弘明實驗高中附設國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '鹿谷鄉') {
        addOption(document.myform.selectschool, "084668", "縣立鹿谷國小", "");
        addOption(document.myform.selectschool, "084669", "縣立秀峰國小", "");
        addOption(document.myform.selectschool, "084670", "縣立文昌國小", "");
        addOption(document.myform.selectschool, "084671", "縣立鳳凰國小", "");
        addOption(document.myform.selectschool, "084672", "縣立內湖國小", "");
        addOption(document.myform.selectschool, "084673", "縣立初鄉國小", "");
        addOption(document.myform.selectschool, "084674", "縣立瑞田國小", "");
        addOption(document.myform.selectschool, "084675", "縣立和雅國小", "");
        addOption(document.myform.selectschool, "084676", "縣立廣興國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '中寮鄉') {
        addOption(document.myform.selectschool, "084677", "縣立中寮國小", "");
        addOption(document.myform.selectschool, "084678", "縣立爽文國小", "");
        addOption(document.myform.selectschool, "084679", "縣立永樂國小", "");
        addOption(document.myform.selectschool, "084680", "縣立永康國小", "");
        addOption(document.myform.selectschool, "084681", "縣立清水國小", "");
        addOption(document.myform.selectschool, "084682", "縣立至誠國小", "");
        addOption(document.myform.selectschool, "084683", "縣立永和國小", "");
        addOption(document.myform.selectschool, "084684", "縣立廣福國小", "");
        addOption(document.myform.selectschool, "084685", "縣立和興國小", "");
        addOption(document.myform.selectschool, "084686", "縣立廣英國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '魚池鄉') {
        addOption(document.myform.selectschool, "084687", "縣立魚池國小", "");
        addOption(document.myform.selectschool, "084688", "縣立頭社國小", "");
        addOption(document.myform.selectschool, "084689", "縣立東光國小", "");
        addOption(document.myform.selectschool, "084690", "縣立五城國小", "");
        addOption(document.myform.selectschool, "084691", "縣立明潭國小", "");
        addOption(document.myform.selectschool, "084693", "縣立新城國小", "");
        addOption(document.myform.selectschool, "084694", "縣立德化國小", "");
        addOption(document.myform.selectschool, "084695", "縣立共和國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '國姓鄉') {
        addOption(document.myform.selectschool, "084696", "縣立國姓國小", "");
        addOption(document.myform.selectschool, "084697", "縣立北山國小", "");
        addOption(document.myform.selectschool, "084698", "縣立北港國小", "");
        addOption(document.myform.selectschool, "084699", "縣立福龜國小", "");
        addOption(document.myform.selectschool, "084700", "縣立長流國小", "");
        addOption(document.myform.selectschool, "084701", "縣立南港國小", "");
        addOption(document.myform.selectschool, "084702", "縣立育樂國小", "");
        addOption(document.myform.selectschool, "084703", "縣立港源國小", "");
        addOption(document.myform.selectschool, "084704", "縣立長福國小", "");
        addOption(document.myform.selectschool, "084705", "縣立乾峰國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '水里鄉') {
        addOption(document.myform.selectschool, "084706", "縣立水里國小", "");
        addOption(document.myform.selectschool, "084707", "縣立郡坑國小", "");
        addOption(document.myform.selectschool, "084708", "縣立民和國小", "");
        addOption(document.myform.selectschool, "084709", "縣立新興國小", "");
        addOption(document.myform.selectschool, "084710", "縣立玉峰國小", "");
        addOption(document.myform.selectschool, "084711", "縣立永興國小", "");
        addOption(document.myform.selectschool, "084714", "縣立成城國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '信義鄉') {
        addOption(document.myform.selectschool, "084716", "縣立信義國小", "");
        addOption(document.myform.selectschool, "084717", "縣立羅娜國小", "");
        addOption(document.myform.selectschool, "084718", "縣立同富國小", "");
        addOption(document.myform.selectschool, "084719", "縣立愛國國小", "");
        addOption(document.myform.selectschool, "084720", "縣立人和國小", "");
        addOption(document.myform.selectschool, "084721", "縣立地利國小", "");
        addOption(document.myform.selectschool, "084722", "縣立東埔國小", "");
        addOption(document.myform.selectschool, "084724", "縣立潭南國小", "");
        addOption(document.myform.selectschool, "084727", "縣立桐林國小", "");
        addOption(document.myform.selectschool, "084728", "縣立隆華國小", "");
        addOption(document.myform.selectschool, "084729", "縣立新鄉國小", "");
        addOption(document.myform.selectschool, "084730", "縣立久美國小", "");
        addOption(document.myform.selectschool, "084731", "縣立雙龍國小", "");
        addOption(document.myform.selectschool, "084732", "縣立豐丘國小", "");
    }
    if (document.myform.selectcity.value == '南投縣' && document.myform.selectdistrict.value == '仁愛鄉') {
        addOption(document.myform.selectschool, "084733", "縣立仁愛國小", "");
        addOption(document.myform.selectschool, "084734", "縣立親愛國小", "");
        addOption(document.myform.selectschool, "084735", "縣立法治國小", "");
        addOption(document.myform.selectschool, "084736", "縣立合作國小", "");
        addOption(document.myform.selectschool, "084737", "縣立互助國小", "");
        addOption(document.myform.selectschool, "084738", "縣立力行國小", "");
        addOption(document.myform.selectschool, "084739", "縣立南豐國小", "");
        addOption(document.myform.selectschool, "084740", "縣立中正國小", "");
        addOption(document.myform.selectschool, "084741", "縣立廬山國小", "");
        addOption(document.myform.selectschool, "084742", "縣立發祥國小", "");
        addOption(document.myform.selectschool, "084743", "縣立萬豐國小", "");
        addOption(document.myform.selectschool, "084744", "縣立平靜國小", "");
        addOption(document.myform.selectschool, "084745", "縣立春陽國小", "");
        addOption(document.myform.selectschool, "084746", "縣立紅葉國小", "");
        addOption(document.myform.selectschool, "084747", "縣立清境國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '古坑鄉') {
        addOption(document.myform.selectschool, "091602", "私立福智國小", "");
        addOption(document.myform.selectschool, "094612", "縣立古坑國(中)小", "");
        addOption(document.myform.selectschool, "094613", "縣立東和國小", "");
        addOption(document.myform.selectschool, "094614", "縣立永光國小", "");
        addOption(document.myform.selectschool, "094615", "縣立華山國小", "");
        addOption(document.myform.selectschool, "094616", "縣立棋山國小", "");
        addOption(document.myform.selectschool, "094617", "縣立桂林國小", "");
        addOption(document.myform.selectschool, "094618", "縣立樟湖生態國(中)小", "");
        addOption(document.myform.selectschool, "094619", "縣立草嶺生態地質國小", "");
        addOption(document.myform.selectschool, "094620", "縣立華南國小", "");
        addOption(document.myform.selectschool, "094621", "縣立興昌國小", "");
        addOption(document.myform.selectschool, "094622", "縣立山峰華德福實小", "");
        addOption(document.myform.selectschool, "094623", "縣立水碓國小", "");
        addOption(document.myform.selectschool, "094624", "縣立新光國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '斗六市') {
        addOption(document.myform.selectschool, "094601", "縣立鎮西國小", "");
        addOption(document.myform.selectschool, "094603", "縣立溝壩國小", "");
        addOption(document.myform.selectschool, "094604", "縣立梅林國小", "");
        addOption(document.myform.selectschool, "094605", "縣立石榴國小", "");
        addOption(document.myform.selectschool, "094606", "縣立溪洲國小", "");
        addOption(document.myform.selectschool, "094607", "縣立林頭國小", "");
        addOption(document.myform.selectschool, "094608", "縣立保長國小", "");
        addOption(document.myform.selectschool, "094609", "縣立鎮南國小", "");
        addOption(document.myform.selectschool, "094611", "縣立久安國小", "");
        addOption(document.myform.selectschool, "094755", "縣立雲林國小", "");
        addOption(document.myform.selectschool, "091601", "私立維多利亞小學", "");
        addOption(document.myform.selectschool, "094602", "縣立鎮東國小", "");
        addOption(document.myform.selectschool, "094610", "縣立公誠國小", "");
        addOption(document.myform.selectschool, "094756", "縣立斗六國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '林內鄉') {
        addOption(document.myform.selectschool, "094625", "縣立林內國小", "");
        addOption(document.myform.selectschool, "094626", "縣立重興國小", "");
        addOption(document.myform.selectschool, "094627", "縣立九芎國小", "");
        addOption(document.myform.selectschool, "094628", "縣立成功國小", "");
        addOption(document.myform.selectschool, "094629", "縣立林中國小", "");
        addOption(document.myform.selectschool, "094630", "縣立民生國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '斗南鎮') {
        addOption(document.myform.selectschool, "094631", "縣立斗南國小", "");
        addOption(document.myform.selectschool, "094632", "縣立大東國小", "");
        addOption(document.myform.selectschool, "094633", "縣立石龜國小", "");
        addOption(document.myform.selectschool, "094634", "縣立重光國小", "");
        addOption(document.myform.selectschool, "094635", "縣立文安國小", "");
        addOption(document.myform.selectschool, "094636", "縣立僑真國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '莿桐鄉') {
        addOption(document.myform.selectschool, "094637", "縣立莿桐國小", "");
        addOption(document.myform.selectschool, "094638", "縣立饒平國小", "");
        addOption(document.myform.selectschool, "094639", "縣立大美國小", "");
        addOption(document.myform.selectschool, "094640", "縣立六合國小", "");
        addOption(document.myform.selectschool, "094641", "縣立僑和國小", "");
        addOption(document.myform.selectschool, "094642", "縣立育仁國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '大埤鄉') {
        addOption(document.myform.selectschool, "094643", "縣立大埤國小", "");
        addOption(document.myform.selectschool, "094644", "縣立舊庄國小", "");
        addOption(document.myform.selectschool, "094645", "縣立仁和國小", "");
        addOption(document.myform.selectschool, "094646", "縣立嘉興國小", "");
        addOption(document.myform.selectschool, "094647", "縣立聯美國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '虎尾鎮') {
        addOption(document.myform.selectschool, "094648", "縣立虎尾國小", "");
        addOption(document.myform.selectschool, "094649", "縣立立仁國小", "");
        addOption(document.myform.selectschool, "094650", "縣立大屯國小", "");
        addOption(document.myform.selectschool, "094651", "縣立中溪國小", "");
        addOption(document.myform.selectschool, "094652", "縣立光復國小", "");
        addOption(document.myform.selectschool, "094653", "縣立中正國小", "");
        addOption(document.myform.selectschool, "094654", "縣立平和國小", "");
        addOption(document.myform.selectschool, "094655", "縣立廉使國小", "");
        addOption(document.myform.selectschool, "094656", "縣立惠來國小", "");
        addOption(document.myform.selectschool, "094658", "縣立安慶國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '土庫鎮') {
        addOption(document.myform.selectschool, "094659", "縣立土庫國小", "");
        addOption(document.myform.selectschool, "094660", "縣立馬光國小", "");
        addOption(document.myform.selectschool, "094661", "縣立埤腳國小", "");
        addOption(document.myform.selectschool, "094662", "縣立後埔國小", "");
        addOption(document.myform.selectschool, "094663", "縣立秀潭國小", "");
        addOption(document.myform.selectschool, "094664", "縣立新庄國小", "");
        addOption(document.myform.selectschool, "094665", "縣立宏崙國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '褒忠鄉') {
        addOption(document.myform.selectschool, "094666", "縣立褒忠國小", "");
        addOption(document.myform.selectschool, "094667", "縣立龍巖國小", "");
        addOption(document.myform.selectschool, "094668", "縣立復興國小", "");
        addOption(document.myform.selectschool, "094669", "縣立潮厝華德福實小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '東勢鄉') {
        addOption(document.myform.selectschool, "094670", "縣立東勢國小", "");
        addOption(document.myform.selectschool, "094671", "縣立安南國小", "");
        addOption(document.myform.selectschool, "094672", "縣立明倫國小", "");
        addOption(document.myform.selectschool, "094673", "縣立同安國小", "");
        addOption(document.myform.selectschool, "094674", "縣立龍潭國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '臺西鄉') {
        addOption(document.myform.selectschool, "094675", "縣立臺西國小", "");
        addOption(document.myform.selectschool, "094676", "縣立崙豐國小", "");
        addOption(document.myform.selectschool, "094677", "縣立泉州國小", "");
        addOption(document.myform.selectschool, "094678", "縣立新興國小", "");
        addOption(document.myform.selectschool, "094679", "縣立尚德國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '西螺鎮') {
        addOption(document.myform.selectschool, "094680", "縣立文昌國小", "");
        addOption(document.myform.selectschool, "094681", "縣立中山國小", "");
        addOption(document.myform.selectschool, "094682", "縣立廣興國小", "");
        addOption(document.myform.selectschool, "094683", "縣立安定國小", "");
        addOption(document.myform.selectschool, "094684", "縣立吳厝國小", "");
        addOption(document.myform.selectschool, "094685", "縣立大新國小", "");
        addOption(document.myform.selectschool, "094686", "縣立文賢國小", "");
        addOption(document.myform.selectschool, "094687", "縣立文興國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '二崙鄉') {
        addOption(document.myform.selectschool, "094688", "縣立二崙國小", "");
        addOption(document.myform.selectschool, "094689", "縣立三和國小", "");
        addOption(document.myform.selectschool, "094690", "縣立油車國小", "");
        addOption(document.myform.selectschool, "094691", "縣立大同國小", "");
        addOption(document.myform.selectschool, "094692", "縣立永定國小", "");
        addOption(document.myform.selectschool, "094693", "縣立義賢國小", "");
        addOption(document.myform.selectschool, "094694", "縣立旭光國小", "");
        addOption(document.myform.selectschool, "094695", "縣立來惠國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '崙背鄉') {
        addOption(document.myform.selectschool, "094696", "縣立崙背國小", "");
        addOption(document.myform.selectschool, "094697", "縣立豐榮國小", "");
        addOption(document.myform.selectschool, "094698", "縣立大有國小", "");
        addOption(document.myform.selectschool, "094699", "縣立中和國小", "");
        addOption(document.myform.selectschool, "094700", "縣立陽明國小", "");
        addOption(document.myform.selectschool, "094701", "縣立東興國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '麥寮鄉') {
        addOption(document.myform.selectschool, "094702", "縣立麥寮國小", "");
        addOption(document.myform.selectschool, "094703", "縣立橋頭國小", "");
        addOption(document.myform.selectschool, "094704", "縣立明禮國小", "");
        addOption(document.myform.selectschool, "094705", "縣立興華國小", "");
        addOption(document.myform.selectschool, "094706", "縣立豐安國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '北港鎮') {
        addOption(document.myform.selectschool, "094707", "縣立南陽國小", "");
        addOption(document.myform.selectschool, "094708", "縣立北辰國小", "");
        addOption(document.myform.selectschool, "094709", "縣立好收國小", "");
        addOption(document.myform.selectschool, "094710", "縣立育英國小", "");
        addOption(document.myform.selectschool, "094711", "縣立東榮國小", "");
        addOption(document.myform.selectschool, "094712", "縣立朝陽國小", "");
        addOption(document.myform.selectschool, "094713", "縣立辰光國小", "");
        addOption(document.myform.selectschool, "094714", "縣立僑美國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '元長鄉') {
        addOption(document.myform.selectschool, "094715", "縣立元長國小", "");
        addOption(document.myform.selectschool, "094716", "縣立新生國小", "");
        addOption(document.myform.selectschool, "094717", "縣立客厝國小", "");
        addOption(document.myform.selectschool, "094718", "縣立山內國小", "");
        addOption(document.myform.selectschool, "094719", "縣立仁德國小", "");
        addOption(document.myform.selectschool, "094720", "縣立忠孝國小", "");
        addOption(document.myform.selectschool, "094721", "縣立仁愛國小", "");
        addOption(document.myform.selectschool, "094722", "縣立信義國小", "");
        addOption(document.myform.selectschool, "094723", "縣立和平國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '四湖鄉') {
        addOption(document.myform.selectschool, "094724", "縣立四湖國小", "");
        addOption(document.myform.selectschool, "094725", "縣立東光國小", "");
        addOption(document.myform.selectschool, "094726", "縣立飛沙國小", "");
        addOption(document.myform.selectschool, "094727", "縣立林厝國小", "");
        addOption(document.myform.selectschool, "094728", "縣立三崙國小", "");
        addOption(document.myform.selectschool, "094729", "縣立建陽國小", "");
        addOption(document.myform.selectschool, "094730", "縣立南光國小", "");
        addOption(document.myform.selectschool, "094731", "縣立鹿場國小", "");
        addOption(document.myform.selectschool, "094732", "縣立明德國小", "");
        addOption(document.myform.selectschool, "094733", "縣立建華國小", "");
        addOption(document.myform.selectschool, "094734", "縣立內湖國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '口湖鄉') {
        addOption(document.myform.selectschool, "094735", "縣立口湖國小", "");
        addOption(document.myform.selectschool, "094736", "縣立文光國小", "");
        addOption(document.myform.selectschool, "094737", "縣立金湖國小", "");
        addOption(document.myform.selectschool, "094738", "縣立下崙國小", "");
        addOption(document.myform.selectschool, "094739", "縣立興南國小", "");
        addOption(document.myform.selectschool, "094740", "縣立崇文國小", "");
        addOption(document.myform.selectschool, "094741", "縣立成龍國小", "");
        addOption(document.myform.selectschool, "094742", "縣立臺興國小", "");
        addOption(document.myform.selectschool, "094743", "縣立頂湖國小", "");
        //廢校addOption(document.myform.selectschool, "094744", "縣立過港國小", "");
    }
    if (document.myform.selectcity.value == '雲林縣' && document.myform.selectdistrict.value == '水林鄉') {
        addOption(document.myform.selectschool, "094746", "縣立蔦松國小", "");
        addOption(document.myform.selectschool, "094747", "縣立尖山國小", "");
        addOption(document.myform.selectschool, "094748", "縣立宏仁國小", "");
        addOption(document.myform.selectschool, "094749", "縣立文正國小", "");
        addOption(document.myform.selectschool, "094750", "縣立誠正國小", "");
        addOption(document.myform.selectschool, "094751", "縣立中興國小", "");
        addOption(document.myform.selectschool, "094752", "縣立和安國小", "");
        addOption(document.myform.selectschool, "094753", "縣立水燦林國小", "");
        addOption(document.myform.selectschool, "094754", "縣立大興國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '朴子市') {
        addOption(document.myform.selectschool, "104601", "縣立朴子國小", "");
        addOption(document.myform.selectschool, "104602", "縣立大同國小", "");
        addOption(document.myform.selectschool, "104603", "縣立雙溪國小", "");
        addOption(document.myform.selectschool, "104604", "縣立竹村國小", "");
        addOption(document.myform.selectschool, "104605", "縣立松梅國小", "");
        addOption(document.myform.selectschool, "104742", "縣立祥和國小", "");
        addOption(document.myform.selectschool, "104606", "縣立大鄉國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '布袋鎮') {
        addOption(document.myform.selectschool, "104607", "縣立布袋國小", "");
        addOption(document.myform.selectschool, "104608", "縣立景山國小", "");
        addOption(document.myform.selectschool, "104609", "縣立永安國小", "");
        addOption(document.myform.selectschool, "104610", "縣立過溝國小", "");
        addOption(document.myform.selectschool, "104611", "縣立貴林國小", "");
        addOption(document.myform.selectschool, "104612", "縣立新塭國小", "");
        addOption(document.myform.selectschool, "104613", "縣立新岑國小", "");
        addOption(document.myform.selectschool, "104614", "縣立好美國小", "");
        addOption(document.myform.selectschool, "104736", "縣立布新國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '大林鎮') {
        addOption(document.myform.selectschool, "104615", "縣立大林國小", "");
        addOption(document.myform.selectschool, "104616", "縣立三和國小", "");
        addOption(document.myform.selectschool, "104617", "縣立中林國小", "");
        addOption(document.myform.selectschool, "104618", "縣立排路國小", "");
        addOption(document.myform.selectschool, "104620", "縣立社團國小", "");
        addOption(document.myform.selectschool, "104739", "縣立平林國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '民雄鄉') {
        addOption(document.myform.selectschool, "104621", "縣立民雄國小", "");
        addOption(document.myform.selectschool, "104622", "縣立東榮國小", "");
        addOption(document.myform.selectschool, "104623", "縣立三興國小", "");
        addOption(document.myform.selectschool, "104624", "縣立菁埔國小", "");
        addOption(document.myform.selectschool, "104625", "縣立興中國小", "");
        addOption(document.myform.selectschool, "104626", "縣立秀林國小", "");
        addOption(document.myform.selectschool, "104627", "縣立松山國小", "");
        addOption(document.myform.selectschool, "104628", "縣立大崎國小", "");
        addOption(document.myform.selectschool, "104743", "縣立福樂國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '溪口鄉') {
        addOption(document.myform.selectschool, "104629", "縣立溪口國小", "");
        addOption(document.myform.selectschool, "104630", "縣立美林國小", "");
        addOption(document.myform.selectschool, "104631", "縣立柴林國小", "");
        addOption(document.myform.selectschool, "104632", "縣立柳溝國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '新港鄉') {
        addOption(document.myform.selectschool, "104633", "縣立新港國小", "");
        addOption(document.myform.selectschool, "104634", "縣立文昌國小", "");
        addOption(document.myform.selectschool, "104635", "縣立月眉國小", "");
        addOption(document.myform.selectschool, "104636", "縣立古民國小", "");
        addOption(document.myform.selectschool, "104637", "縣立復興國小", "");
        addOption(document.myform.selectschool, "104638", "縣立安和國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '六腳鄉') {
        addOption(document.myform.selectschool, "104639", "縣立蒜頭國小", "");
        addOption(document.myform.selectschool, "104640", "縣立六腳國小", "");
        addOption(document.myform.selectschool, "104641", "縣立六美國小", "");
        addOption(document.myform.selectschool, "104642", "縣立灣內國小", "");
        addOption(document.myform.selectschool, "104643", "縣立更寮國小", "");
        addOption(document.myform.selectschool, "104645", "縣立北美國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '東石鄉') {
        addOption(document.myform.selectschool, "104647", "縣立東石國小", "");
        addOption(document.myform.selectschool, "104648", "縣立塭港國小", "");
        addOption(document.myform.selectschool, "104649", "縣立三江國小", "");
        addOption(document.myform.selectschool, "104650", "縣立龍港國小", "");
        addOption(document.myform.selectschool, "104651", "縣立下楫國小", "");
        addOption(document.myform.selectschool, "104652", "縣立港墘國小", "");
        addOption(document.myform.selectschool, "104653", "縣立龍崗國小", "");
        addOption(document.myform.selectschool, "104654", "縣立網寮國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '鹿草鄉') {
        addOption(document.myform.selectschool, "104655", "縣立鹿草國小", "");
        addOption(document.myform.selectschool, "104656", "縣立重寮國小", "");
        addOption(document.myform.selectschool, "104657", "縣立下潭國小", "");
        addOption(document.myform.selectschool, "104658", "縣立碧潭國小", "");
        addOption(document.myform.selectschool, "104659", "縣立竹園國小", "");
        addOption(document.myform.selectschool, "104660", "縣立後塘國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '義竹鄉') {
        addOption(document.myform.selectschool, "104661", "縣立義竹國小", "");
        addOption(document.myform.selectschool, "104663", "縣立光榮國小", "");
        addOption(document.myform.selectschool, "104665", "縣立過路國小", "");
        addOption(document.myform.selectschool, "104666", "縣立和順國小", "");
        addOption(document.myform.selectschool, "104668", "縣立南興國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '太保市') {
        addOption(document.myform.selectschool, "104669", "縣立太保國小", "");
        addOption(document.myform.selectschool, "104670", "縣立安東國小", "");
        addOption(document.myform.selectschool, "104671", "縣立南新國小", "");
        addOption(document.myform.selectschool, "104672", "縣立新埤國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '水上鄉') {
        addOption(document.myform.selectschool, "104673", "縣立水上國小", "");
        addOption(document.myform.selectschool, "104674", "縣立大崙國小", "");
        addOption(document.myform.selectschool, "104675", "縣立柳林國小", "");
        addOption(document.myform.selectschool, "104676", "縣立忠和國小", "");
        addOption(document.myform.selectschool, "104677", "縣立義興國小", "");
        addOption(document.myform.selectschool, "104678", "縣立成功國小", "");
        addOption(document.myform.selectschool, "104679", "縣立北回國小", "");
        addOption(document.myform.selectschool, "104680", "縣立南靖國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '中埔鄉') {
        addOption(document.myform.selectschool, "104681", "縣立中埔國小", "");
        addOption(document.myform.selectschool, "104682", "縣立大有國小", "");
        addOption(document.myform.selectschool, "104683", "縣立中山國小", "");
        addOption(document.myform.selectschool, "104684", "縣立頂六國小", "");
        addOption(document.myform.selectschool, "104685", "縣立和睦國小", "");
        addOption(document.myform.selectschool, "104686", "縣立同仁國小", "");
        addOption(document.myform.selectschool, "104688", "縣立沄水國小", "");
        addOption(document.myform.selectschool, "104690", "縣立社口國小", "");
        addOption(document.myform.selectschool, "104692", "縣立灣潭國小", "");
        addOption(document.myform.selectschool, "104738", "縣立和興國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '番路鄉') {
        addOption(document.myform.selectschool, "104693", "縣立民和國小", "");
        addOption(document.myform.selectschool, "104694", "縣立內甕國小", "");
        addOption(document.myform.selectschool, "104695", "縣立黎明國小", "");
        addOption(document.myform.selectschool, "104696", "縣立大湖國小", "");
        addOption(document.myform.selectschool, "104698", "縣立隙頂國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '竹崎鄉') {
        addOption(document.myform.selectschool, "104700", "縣立竹崎國小", "");
        addOption(document.myform.selectschool, "104702", "縣立龍山國小", "");
        addOption(document.myform.selectschool, "104704", "縣立鹿滿國小", "");
        addOption(document.myform.selectschool, "104705", "縣立圓崇國小", "");
        addOption(document.myform.selectschool, "104706", "縣立內埔國小", "");
        addOption(document.myform.selectschool, "104707", "縣立桃源國小", "");
        addOption(document.myform.selectschool, "104708", "縣立中和國小", "");
        addOption(document.myform.selectschool, "104709", "縣立中興國小", "");
        addOption(document.myform.selectschool, "104710", "縣立光華國小", "");
        addOption(document.myform.selectschool, "104712", "縣立義仁國小", "");
        addOption(document.myform.selectschool, "104713", "縣立沙坑國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '梅山鄉') {
        addOption(document.myform.selectschool, "104715", "縣立梅山國小", "");
        addOption(document.myform.selectschool, "104716", "縣立梅圳國小", "");
        addOption(document.myform.selectschool, "104717", "縣立太平國小", "");
        addOption(document.myform.selectschool, "104719", "縣立太興國小", "");
        addOption(document.myform.selectschool, "104720", "縣立瑞里國小", "");
        addOption(document.myform.selectschool, "104721", "縣立大南國小", "");
        addOption(document.myform.selectschool, "104722", "縣立瑞峰國小", "");
        addOption(document.myform.selectschool, "104724", "縣立太和國小", "");
        addOption(document.myform.selectschool, "104725", "縣立仁和國小", "");
        addOption(document.myform.selectschool, "104740", "縣立梅北國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '大埔鄉') {
        addOption(document.myform.selectschool, "104726", "縣立大埔國小", "");
    }
    if (document.myform.selectcity.value == '嘉義縣' && document.myform.selectdistrict.value == '阿里山鄉') {
        addOption(document.myform.selectschool, "104727", "縣立達邦國小", "");
        addOption(document.myform.selectschool, "104729", "縣立十字國小", "");
        addOption(document.myform.selectschool, "104730", "縣立來吉國小", "");
        addOption(document.myform.selectschool, "104731", "縣立豐山國小", "");
        addOption(document.myform.selectschool, "104732", "縣立山美國小", "");
        addOption(document.myform.selectschool, "104733", "縣立新美國小", "");
        addOption(document.myform.selectschool, "104734", "縣立阿里山國(中)小", "");
        addOption(document.myform.selectschool, "104735", "縣立香林國小", "");
        addOption(document.myform.selectschool, "104737", "縣立茶山國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '屏東市') {
        addOption(document.myform.selectschool, "130601", "國立屏東教大實小", "");
        addOption(document.myform.selectschool, "134601", "縣立中正國小", "");
        addOption(document.myform.selectschool, "134602", "縣立仁愛國小", "");
        addOption(document.myform.selectschool, "134603", "縣立海豐國小", "");
        addOption(document.myform.selectschool, "134604", "縣立公館國小", "");
        addOption(document.myform.selectschool, "134606", "縣立鶴聲國小", "");
        addOption(document.myform.selectschool, "134607", "縣立凌雲國小", "");
        addOption(document.myform.selectschool, "134608", "縣立勝利國小", "");
        addOption(document.myform.selectschool, "134609", "縣立歸來國小", "");
        addOption(document.myform.selectschool, "134610", "縣立前進國小", "");
        addOption(document.myform.selectschool, "134611", "縣立唐榮國小", "");
        addOption(document.myform.selectschool, "134612", "縣立民和國小", "");
        addOption(document.myform.selectschool, "134613", "縣立建國國小", "");
        addOption(document.myform.selectschool, "134614", "縣立復興國小", "");
        addOption(document.myform.selectschool, "134615", "縣立忠孝國小", "");
        addOption(document.myform.selectschool, "134616", "縣立和平國小", "");
        addOption(document.myform.selectschool, "134772", "縣立信義國小", "");
        addOption(document.myform.selectschool, "134773", "縣立瑞光國小", "");
        addOption(document.myform.selectschool, "134774", "縣立崇蘭國小", "");
        addOption(document.myform.selectschool, "134786", "縣立民生國小", "");
        addOption(document.myform.selectschool, "134605", "縣立大同國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '萬丹鄉') {
        addOption(document.myform.selectschool, "134617", "縣立萬丹國小", "");
        addOption(document.myform.selectschool, "134618", "縣立新庄國小", "");
        addOption(document.myform.selectschool, "134619", "縣立興華國小", "");
        addOption(document.myform.selectschool, "134620", "縣立新興國小", "");
        addOption(document.myform.selectschool, "134621", "縣立社皮國小", "");
        addOption(document.myform.selectschool, "134622", "縣立廣安國小", "");
        addOption(document.myform.selectschool, "134623", "縣立興化國小", "");
        addOption(document.myform.selectschool, "134775", "縣立四維國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '麟洛鄉') {
        addOption(document.myform.selectschool, "134624", "縣立麟洛國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '九如鄉') {
        addOption(document.myform.selectschool, "134625", "縣立九如國小", "");
        addOption(document.myform.selectschool, "134626", "縣立後庄國小", "");
        addOption(document.myform.selectschool, "134627", "縣立惠農國小", "");
        addOption(document.myform.selectschool, "134771", "縣立三多國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '長治鄉') {
        addOption(document.myform.selectschool, "134628", "縣立長興國小", "");
        addOption(document.myform.selectschool, "134629", "縣立繁華國小", "");
        addOption(document.myform.selectschool, "134630", "縣立德協國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '鹽埔鄉') {
        addOption(document.myform.selectschool, "134631", "縣立鹽埔國小", "");
        addOption(document.myform.selectschool, "134632", "縣立仕絨國小", "");
        addOption(document.myform.selectschool, "134633", "縣立高朗國小", "");
        addOption(document.myform.selectschool, "134634", "縣立新圍國小", "");
        addOption(document.myform.selectschool, "134635", "縣立彭厝國小", "");
        addOption(document.myform.selectschool, "134636", "縣立振興國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '高樹鄉') {
        addOption(document.myform.selectschool, "134637", "縣立高樹國小", "");
        addOption(document.myform.selectschool, "134638", "縣立舊寮國小", "");
        addOption(document.myform.selectschool, "134639", "縣立新豐國小", "");
        addOption(document.myform.selectschool, "134640", "縣立田子國小", "");
        addOption(document.myform.selectschool, "134641", "縣立新南國小", "");
        addOption(document.myform.selectschool, "134642", "縣立泰山國小", "");
        addOption(document.myform.selectschool, "134644", "縣立大路關國中小", "");
        addOption(document.myform.selectschool, "134645", "縣立南華國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '里港鄉') {
        addOption(document.myform.selectschool, "134646", "縣立里港國小", "");
        addOption(document.myform.selectschool, "134647", "縣立載興國小", "");
        addOption(document.myform.selectschool, "134648", "縣立土庫國小", "");
        addOption(document.myform.selectschool, "134649", "縣立三和國小", "");
        addOption(document.myform.selectschool, "134780", "縣立塔樓國小", "");
        addOption(document.myform.selectschool, "134781", "縣立玉田國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '潮州鎮') {
        addOption(document.myform.selectschool, "134651", "縣立潮州國小", "");
        addOption(document.myform.selectschool, "134652", "縣立光春國小", "");
        addOption(document.myform.selectschool, "134653", "縣立光華國小", "");
        addOption(document.myform.selectschool, "134654", "縣立四林國小", "");
        addOption(document.myform.selectschool, "134655", "縣立潮南國小", "");
        addOption(document.myform.selectschool, "134656", "縣立潮東國小", "");
        addOption(document.myform.selectschool, "134777", "縣立潮昇國小", "");
        addOption(document.myform.selectschool, "134778", "縣立潮和國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '萬巒鄉') {
        addOption(document.myform.selectschool, "134657", "縣立萬巒國小", "");
        addOption(document.myform.selectschool, "134658", "縣立五溝國小", "");
        addOption(document.myform.selectschool, "134659", "縣立佳佐國小", "");
        addOption(document.myform.selectschool, "134661", "縣立赤山國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '內埔鄉') {
        addOption(document.myform.selectschool, "134662", "縣立內埔國小", "");
        addOption(document.myform.selectschool, "134663", "縣立育英國小", "");
        addOption(document.myform.selectschool, "134664", "縣立僑智國小", "");
        addOption(document.myform.selectschool, "134665", "縣立崇文國小", "");
        addOption(document.myform.selectschool, "134666", "縣立新生國小", "");
        addOption(document.myform.selectschool, "134667", "縣立榮華國小", "");
        addOption(document.myform.selectschool, "134668", "縣立黎明國小", "");
        addOption(document.myform.selectschool, "134669", "縣立隘寮國小", "");
        addOption(document.myform.selectschool, "134670", "縣立泰安國小", "");
        addOption(document.myform.selectschool, "134671", "縣立東勢國小", "");
        addOption(document.myform.selectschool, "134672", "縣立豐田國小", "");
        addOption(document.myform.selectschool, "134673", "縣立富田國小", "");
        addOption(document.myform.selectschool, "134776", "縣立東寧國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '竹田鄉') {
        addOption(document.myform.selectschool, "134674", "縣立竹田國小", "");
        addOption(document.myform.selectschool, "134675", "縣立西勢國小", "");
        addOption(document.myform.selectschool, "134676", "縣立大明國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '新埤鄉') {
        addOption(document.myform.selectschool, "134677", "縣立新埤國小", "");
        addOption(document.myform.selectschool, "134678", "縣立大成國小", "");
        addOption(document.myform.selectschool, "134679", "縣立萬隆國小", "");
        addOption(document.myform.selectschool, "134680", "縣立餉潭國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '枋寮鄉') {
        addOption(document.myform.selectschool, "134681", "縣立枋寮國小", "");
        addOption(document.myform.selectschool, "134682", "縣立僑德國小", "");
        addOption(document.myform.selectschool, "134683", "縣立建興國小", "");
        addOption(document.myform.selectschool, "134684", "縣立東海國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '東港鎮') {
        addOption(document.myform.selectschool, "134686", "縣立東港國小", "");
        addOption(document.myform.selectschool, "134687", "縣立東隆國小", "");
        addOption(document.myform.selectschool, "134688", "縣立海濱國小", "");
        addOption(document.myform.selectschool, "134689", "縣立以栗國小", "");
        addOption(document.myform.selectschool, "134690", "縣立大潭國小", "");
        addOption(document.myform.selectschool, "134779", "縣立東興國小", "");
        addOption(document.myform.selectschool, "134783", "縣立東光國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '新園鄉') {
        addOption(document.myform.selectschool, "134691", "縣立新園國小", "");
        addOption(document.myform.selectschool, "134692", "縣立仙吉國小", "");
        addOption(document.myform.selectschool, "134693", "縣立烏龍國小", "");
        addOption(document.myform.selectschool, "134694", "縣立港西國小", "");
        addOption(document.myform.selectschool, "134695", "縣立鹽洲國小", "");
        addOption(document.myform.selectschool, "134785", "縣立瓦瑤國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '琉球鄉') {
        addOption(document.myform.selectschool, "134696", "縣立琉球國小", "");
        addOption(document.myform.selectschool, "134697", "縣立天南國小", "");
        addOption(document.myform.selectschool, "134698", "縣立全德國小", "");
        addOption(document.myform.selectschool, "134699", "縣立白沙國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '崁頂鄉') {
        addOption(document.myform.selectschool, "134700", "縣立崁頂國小", "");
        addOption(document.myform.selectschool, "134701", "縣立港東國小", "");
        addOption(document.myform.selectschool, "134702", "縣立力社國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '林邊鄉') {
        addOption(document.myform.selectschool, "134703", "縣立林邊國小", "");
        addOption(document.myform.selectschool, "134704", "縣立仁和國小", "");
        addOption(document.myform.selectschool, "134705", "縣立竹林國小", "");
        addOption(document.myform.selectschool, "134706", "縣立崎峰國小", "");
        addOption(document.myform.selectschool, "134707", "縣立水利國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '南州鄉') {
        addOption(document.myform.selectschool, "134708", "縣立南州國小", "");
        addOption(document.myform.selectschool, "134709", "縣立同安國小", "");
        addOption(document.myform.selectschool, "134710", "縣立溪北國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '佳冬鄉') {
        addOption(document.myform.selectschool, "134711", "縣立佳冬國小", "");
        addOption(document.myform.selectschool, "134712", "縣立塭子國小", "");
        addOption(document.myform.selectschool, "134713", "縣立羌園國小", "");
        addOption(document.myform.selectschool, "134714", "縣立昌隆國小", "");
        addOption(document.myform.selectschool, "134715", "縣立大新國小", "");
        addOption(document.myform.selectschool, "134716", "縣立玉光國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '恆春鎮') {
        addOption(document.myform.selectschool, "134717", "縣立恆春國小", "");
        addOption(document.myform.selectschool, "134718", "縣立僑勇國小", "");
        addOption(document.myform.selectschool, "134720", "縣立山海國小", "");
        addOption(document.myform.selectschool, "134721", "縣立大光國小", "");
        addOption(document.myform.selectschool, "134722", "縣立水泉國小", "");
        addOption(document.myform.selectschool, "134723", "縣立大平國小", "");
        addOption(document.myform.selectschool, "134724", "縣立墾丁國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '車城鄉') {
        addOption(document.myform.selectschool, "134725", "縣立車城國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '滿州鄉') {
        addOption(document.myform.selectschool, "134729", "縣立滿州國小", "");
        addOption(document.myform.selectschool, "134730", "縣立長樂國小", "");
        addOption(document.myform.selectschool, "134731", "縣立永港國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '枋山鄉') {
        addOption(document.myform.selectschool, "134735", "縣立楓港國小", "");
        addOption(document.myform.selectschool, "134736", "縣立加祿國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '三地門鄉') {
        addOption(document.myform.selectschool, "134737", "縣立三地國小", "");
        addOption(document.myform.selectschool, "134740", "縣立青山國小", "");
        addOption(document.myform.selectschool, "134741", "縣立青葉國小", "");
        addOption(document.myform.selectschool, "134742", "縣立口社國小", "");
        addOption(document.myform.selectschool, "134784", "縣立賽嘉國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '瑪家鄉') {
        addOption(document.myform.selectschool, "134744", "縣立佳義國小", "");
        addOption(document.myform.selectschool, "134746", "縣立北葉國小", "");
        addOption(document.myform.selectschool, "134787", "縣立長榮百合國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '霧臺鄉') {
        addOption(document.myform.selectschool, "134748", "縣立霧臺國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '泰武鄉') {
        addOption(document.myform.selectschool, "134751", "縣立武潭國小", "");
        addOption(document.myform.selectschool, "134752", "縣立泰武國小", "");
        addOption(document.myform.selectschool, "134753", "縣立萬安國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '來義鄉') {
        addOption(document.myform.selectschool, "134754", "縣立來義國小", "");
        addOption(document.myform.selectschool, "134755", "縣立望嘉國小", "");
        addOption(document.myform.selectschool, "134756", "縣立文樂國小", "");
        addOption(document.myform.selectschool, "134757", "縣立南和國小", "");
        addOption(document.myform.selectschool, "134758", "縣立古樓國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '春日鄉') {
        addOption(document.myform.selectschool, "134759", "縣立春日國小", "");
        addOption(document.myform.selectschool, "134760", "縣立力里國小", "");
        addOption(document.myform.selectschool, "134761", "縣立古華國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '獅子鄉') {
        addOption(document.myform.selectschool, "134762", "縣立楓林國小", "");
        addOption(document.myform.selectschool, "134763", "縣立丹路國小", "");
        addOption(document.myform.selectschool, "134764", "縣立內獅國小", "");
        addOption(document.myform.selectschool, "134765", "縣立草埔國小", "");
    }
    if (document.myform.selectcity.value == '屏東縣' && document.myform.selectdistrict.value == '牡丹鄉') {
        addOption(document.myform.selectschool, "134766", "縣立石門國小", "");
        addOption(document.myform.selectschool, "134768", "縣立高士國小", "");
        addOption(document.myform.selectschool, "134769", "縣立牡丹國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '臺東市') {
        addOption(document.myform.selectschool, "140601", "國立臺東大學附小", "");
        addOption(document.myform.selectschool, "141601", "私立均一國(中)小", "");
        addOption(document.myform.selectschool, "144601", "縣立仁愛國小", "");
        addOption(document.myform.selectschool, "144602", "縣立復興國小", "");
        addOption(document.myform.selectschool, "144603", "縣立光明國小", "");
        addOption(document.myform.selectschool, "144604", "縣立寶桑國小", "");
        addOption(document.myform.selectschool, "144605", "縣立新生國小", "");
        addOption(document.myform.selectschool, "144606", "縣立豐里國小", "");
        addOption(document.myform.selectschool, "144607", "縣立豐榮國小", "");
        addOption(document.myform.selectschool, "144608", "縣立馬蘭國小", "");
        addOption(document.myform.selectschool, "144609", "縣立豐源國小", "");
        addOption(document.myform.selectschool, "144610", "縣立康樂國小", "");
        addOption(document.myform.selectschool, "144611", "縣立豐年國小", "");
        addOption(document.myform.selectschool, "144612", "縣立卑南國小", "");
        addOption(document.myform.selectschool, "144613", "縣立岩灣國小", "");
        addOption(document.myform.selectschool, "144614", "縣立南王國小", "");
        addOption(document.myform.selectschool, "144615", "縣立知本國小", "");
        addOption(document.myform.selectschool, "144616", "縣立建和國小", "");
        addOption(document.myform.selectschool, "144617", "縣立豐田國小", "");
        addOption(document.myform.selectschool, "144618", "縣立富岡國小", "");
        addOption(document.myform.selectschool, "144619", "縣立新園國小", "");
        addOption(document.myform.selectschool, "144701", "縣立東海國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '卑南鄉') {
        addOption(document.myform.selectschool, "144620", "縣立賓朗國小", "");
        addOption(document.myform.selectschool, "144621", "縣立溫泉國小", "");
        addOption(document.myform.selectschool, "144622", "縣立利嘉國小", "");
        addOption(document.myform.selectschool, "144623", "縣立初鹿國小", "");
        addOption(document.myform.selectschool, "144624", "縣立東成國小", "");
        addOption(document.myform.selectschool, "144625", "縣立富山國小", "");
        addOption(document.myform.selectschool, "144627", "縣立大南國小", "");
        addOption(document.myform.selectschool, "144628", "縣立太平國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '太麻里鄉') {
        addOption(document.myform.selectschool, "144629", "縣立大王國小", "");
        addOption(document.myform.selectschool, "144630", "縣立香蘭國小", "");
        addOption(document.myform.selectschool, "144632", "縣立三和國小", "");
        addOption(document.myform.selectschool, "144633", "縣立美和國小", "");
        addOption(document.myform.selectschool, "144635", "縣立大溪國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '大武鄉') {
        addOption(document.myform.selectschool, "144636", "縣立尚武國小", "");
        addOption(document.myform.selectschool, "144637", "縣立大武國小", "");
        addOption(document.myform.selectschool, "144638", "縣立大鳥國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '綠島鄉') {
        addOption(document.myform.selectschool, "144640", "縣立綠島國小", "");
        addOption(document.myform.selectschool, "144641", "縣立公館國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '鹿野鄉') {
        addOption(document.myform.selectschool, "144642", "縣立鹿野國小", "");
        addOption(document.myform.selectschool, "144643", "縣立龍田國小", "");
        addOption(document.myform.selectschool, "144644", "縣立永安國小", "");
        addOption(document.myform.selectschool, "144645", "縣立瑞豐國小", "");
        addOption(document.myform.selectschool, "144646", "縣立瑞源國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '關山鎮') {
        addOption(document.myform.selectschool, "144647", "縣立關山國小", "");
        addOption(document.myform.selectschool, "144648", "縣立月眉國小", "");
        addOption(document.myform.selectschool, "144649", "縣立德高國小", "");
        addOption(document.myform.selectschool, "144650", "縣立電光國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '池上鄉') {
        addOption(document.myform.selectschool, "144651", "縣立福原國小", "");
        addOption(document.myform.selectschool, "144652", "縣立大坡國小", "");
        addOption(document.myform.selectschool, "144653", "縣立萬安國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '東河鄉') {
        addOption(document.myform.selectschool, "144655", "縣立東河國小", "");
        addOption(document.myform.selectschool, "144656", "縣立都蘭國小", "");
        addOption(document.myform.selectschool, "144659", "縣立泰源國小", "");
        addOption(document.myform.selectschool, "144660", "縣立北源國小", "");
        addOption(document.myform.selectschool, "144703", "縣立興隆國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '成功鎮') {
        addOption(document.myform.selectschool, "144662", "縣立三民國小", "");
        addOption(document.myform.selectschool, "144663", "縣立成功國小", "");
        addOption(document.myform.selectschool, "144664", "縣立信義國小", "");
        addOption(document.myform.selectschool, "144665", "縣立三仙國小", "");
        addOption(document.myform.selectschool, "144666", "縣立和平國小", "");
        addOption(document.myform.selectschool, "144667", "縣立忠孝國小", "");
        addOption(document.myform.selectschool, "144668", "縣立博愛國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '長濱鄉') {
        addOption(document.myform.selectschool, "144669", "縣立長濱國小", "");
        addOption(document.myform.selectschool, "144671", "縣立寧埔國小", "");
        addOption(document.myform.selectschool, "144672", "縣立竹湖國小", "");
        addOption(document.myform.selectschool, "144673", "縣立三間國小", "");
        addOption(document.myform.selectschool, "144674", "縣立樟原國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '金峰鄉') {
        addOption(document.myform.selectschool, "144676", "縣立嘉蘭國小", "");
        addOption(document.myform.selectschool, "144677", "縣立介達國小", "");
        addOption(document.myform.selectschool, "144678", "縣立新興國小", "");
        addOption(document.myform.selectschool, "144679", "縣立賓茂國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '達仁鄉') {
        addOption(document.myform.selectschool, "144680", "縣立安朔國小", "");
        addOption(document.myform.selectschool, "144681", "縣立土?國小", "");
        addOption(document.myform.selectschool, "144683", "縣立臺?國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '蘭嶼鄉') {
        addOption(document.myform.selectschool, "144685", "縣立蘭嶼國小", "");
        addOption(document.myform.selectschool, "144686", "縣立椰油國小", "");
        addOption(document.myform.selectschool, "144687", "縣立東清國小", "");
        addOption(document.myform.selectschool, "144688", "縣立朗島國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '延平鄉') {
        addOption(document.myform.selectschool, "144689", "縣立桃源國小", "");
        addOption(document.myform.selectschool, "144690", "縣立武陵國小", "");
        addOption(document.myform.selectschool, "144692", "縣立鸞山國小", "");
        addOption(document.myform.selectschool, "144693", "縣立紅葉國小", "");
    }
    if (document.myform.selectcity.value == '臺東縣' && document.myform.selectdistrict.value == '海端鄉') {
        addOption(document.myform.selectschool, "144694", "縣立海端國小", "");
        addOption(document.myform.selectschool, "144695", "縣立初來國小", "");
        addOption(document.myform.selectschool, "144696", "縣立崁頂國小", "");
        addOption(document.myform.selectschool, "144697", "縣立廣原國小", "");
        addOption(document.myform.selectschool, "144698", "縣立錦屏國小", "");
        addOption(document.myform.selectschool, "144700", "縣立加拿國小", "");
        addOption(document.myform.selectschool, "144702", "縣立霧鹿國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '花蓮市') {
        addOption(document.myform.selectschool, "150601", "國立東華大學附設實小", "");
        addOption(document.myform.selectschool, "151602", "私立海星國小", "");
        addOption(document.myform.selectschool, "154601", "縣立明禮國小", "");
        addOption(document.myform.selectschool, "154602", "縣立明義國小", "");
        addOption(document.myform.selectschool, "154603", "縣立明廉國小", "");
        addOption(document.myform.selectschool, "154604", "縣立明恥國小", "");
        addOption(document.myform.selectschool, "154605", "縣立中正國小", "");
        addOption(document.myform.selectschool, "154606", "縣立信義國小", "");
        addOption(document.myform.selectschool, "154607", "縣立復興國小", "");
        addOption(document.myform.selectschool, "154608", "縣立中華國小", "");
        addOption(document.myform.selectschool, "154610", "縣立忠孝國小", "");
        addOption(document.myform.selectschool, "154611", "縣立北濱國小", "");
        addOption(document.myform.selectschool, "154612", "縣立鑄強國小", "");
        addOption(document.myform.selectschool, "154613", "縣立國福國小", "");
        addOption(document.myform.selectschool, "154711", "縣立中原國小", "");
        addOption(document.myform.selectschool, "151312", "財團法人慈濟大學附中附設國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '新城鄉') {
        addOption(document.myform.selectschool, "154614", "縣立新城國小", "");
        addOption(document.myform.selectschool, "154615", "縣立北埔國小", "");
        addOption(document.myform.selectschool, "154616", "縣立康樂國小", "");
        addOption(document.myform.selectschool, "154617", "縣立嘉里國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '吉安鄉') {
        addOption(document.myform.selectschool, "154618", "縣立吉安國小", "");
        addOption(document.myform.selectschool, "154619", "縣立宜昌國小", "");
        addOption(document.myform.selectschool, "154620", "縣立北昌國小", "");
        addOption(document.myform.selectschool, "154621", "縣立稻香國小", "");
        addOption(document.myform.selectschool, "154622", "縣立光華國小", "");
        addOption(document.myform.selectschool, "154623", "縣立南華國小", "");
        addOption(document.myform.selectschool, "154624", "縣立化仁國小", "");
        addOption(document.myform.selectschool, "154625", "縣立太昌國小", "");
        addOption(document.myform.selectschool, "150F01", "國立花蓮啟智學校", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '壽豐鄉') {
        addOption(document.myform.selectschool, "154626", "縣立壽豐國小", "");
        addOption(document.myform.selectschool, "154627", "縣立豐山國小", "");
        addOption(document.myform.selectschool, "154628", "縣立豐裡國小", "");
        addOption(document.myform.selectschool, "154629", "縣立志學國小", "");
        addOption(document.myform.selectschool, "154630", "縣立平和國小", "");
        addOption(document.myform.selectschool, "154631", "縣立溪口國小", "");
        addOption(document.myform.selectschool, "154632", "縣立月眉國小", "");
        addOption(document.myform.selectschool, "154633", "縣立水璉國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '鳳林鎮') {
        addOption(document.myform.selectschool, "154634", "縣立鳳林國小", "");
        addOption(document.myform.selectschool, "154636", "縣立大榮國小", "");
        addOption(document.myform.selectschool, "154637", "縣立鳳仁國小", "");
        addOption(document.myform.selectschool, "154638", "縣立北林國小", "");
        addOption(document.myform.selectschool, "154640", "縣立長橋國小", "");
        addOption(document.myform.selectschool, "154642", "縣立林榮國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '光復鄉') {
        addOption(document.myform.selectschool, "154643", "縣立光復國小", "");
        addOption(document.myform.selectschool, "154644", "縣立太巴塱國小", "");
        addOption(document.myform.selectschool, "154648", "縣立大進國小", "");
        addOption(document.myform.selectschool, "154707", "縣立西富國小", "");
        addOption(document.myform.selectschool, "154708", "縣立大興國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '瑞穗鄉') {
        addOption(document.myform.selectschool, "154649", "縣立瑞穗國小", "");
        addOption(document.myform.selectschool, "154650", "縣立瑞北國小", "");
        addOption(document.myform.selectschool, "154651", "縣立瑞美國小", "");
        addOption(document.myform.selectschool, "154652", "縣立鶴岡國小", "");
        addOption(document.myform.selectschool, "154653", "縣立舞鶴國小", "");
        addOption(document.myform.selectschool, "154654", "縣立富源國小", "");
        addOption(document.myform.selectschool, "154705", "縣立奇美國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '豐濱鄉') {
        addOption(document.myform.selectschool, "154655", "縣立豐濱國小", "");
        addOption(document.myform.selectschool, "154656", "縣立港口國小", "");
        addOption(document.myform.selectschool, "154657", "縣立靜浦國小", "");
        addOption(document.myform.selectschool, "154658", "縣立新社國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '玉里鎮') {
        addOption(document.myform.selectschool, "154660", "縣立玉里國小", "");
        addOption(document.myform.selectschool, "154661", "縣立中城國小", "");
        addOption(document.myform.selectschool, "154662", "縣立源城國小", "");
        addOption(document.myform.selectschool, "154663", "縣立樂合國小", "");
        addOption(document.myform.selectschool, "154664", "縣立觀音國小", "");
        addOption(document.myform.selectschool, "154665", "縣立高寮國小", "");
        addOption(document.myform.selectschool, "154666", "縣立松浦國小", "");
        addOption(document.myform.selectschool, "154667", "縣立春日國小", "");
        addOption(document.myform.selectschool, "154668", "縣立德武國小", "");
        addOption(document.myform.selectschool, "154669", "縣立三民國小", "");
        addOption(document.myform.selectschool, "154670", "縣立大禹國小", "");
        addOption(document.myform.selectschool, "154671", "縣立長良國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '富里鄉') {
        addOption(document.myform.selectschool, "154672", "縣立富里國小", "");
        addOption(document.myform.selectschool, "154674", "縣立東里國小", "");
        addOption(document.myform.selectschool, "154675", "縣立明里國小", "");
        addOption(document.myform.selectschool, "154676", "縣立吳江國小", "");
        addOption(document.myform.selectschool, "154677", "縣立學田國小", "");
        addOption(document.myform.selectschool, "154678", "縣立永豐國小", "");
        addOption(document.myform.selectschool, "154680", "縣立東竹國小", "");
        addOption(document.myform.selectschool, "154679", "縣立萬寧國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '秀林鄉') {
        addOption(document.myform.selectschool, "154681", "縣立秀林國小", "");
        addOption(document.myform.selectschool, "154682", "縣立富世國小", "");
        addOption(document.myform.selectschool, "154683", "縣立崇德國小", "");
        addOption(document.myform.selectschool, "154684", "縣立和平國小", "");
        addOption(document.myform.selectschool, "154685", "縣立景美國小", "");
        addOption(document.myform.selectschool, "154686", "縣立三棧國小", "");
        addOption(document.myform.selectschool, "154687", "縣立佳民國小", "");
        addOption(document.myform.selectschool, "154688", "縣立銅門國小", "");
        addOption(document.myform.selectschool, "154689", "縣立水源國小", "");
        addOption(document.myform.selectschool, "154690", "縣立銅蘭國小", "");
        addOption(document.myform.selectschool, "154691", "縣立文蘭國小", "");
        addOption(document.myform.selectschool, "154710", "縣立西寶國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '萬榮鄉') {
        addOption(document.myform.selectschool, "154692", "縣立萬榮國小", "");
        addOption(document.myform.selectschool, "154693", "縣立明利國小", "");
        addOption(document.myform.selectschool, "154694", "縣立見晴國小", "");
        addOption(document.myform.selectschool, "154695", "縣立馬遠國小", "");
        addOption(document.myform.selectschool, "154696", "縣立西林國小", "");
        addOption(document.myform.selectschool, "154697", "縣立紅葉國小", "");
    }
    if (document.myform.selectcity.value == '花蓮縣' && document.myform.selectdistrict.value == '卓溪鄉') {
        addOption(document.myform.selectschool, "154698", "縣立卓溪國小", "");
        addOption(document.myform.selectschool, "154699", "縣立崙山國小", "");
        addOption(document.myform.selectschool, "154700", "縣立立山國小", "");
        addOption(document.myform.selectschool, "154701", "縣立太平國小", "");
        addOption(document.myform.selectschool, "154702", "縣立卓清國小", "");
        addOption(document.myform.selectschool, "154703", "縣立卓樂國小", "");
        addOption(document.myform.selectschool, "154704", "縣立古風國小", "");
        addOption(document.myform.selectschool, "154706", "縣立卓楓國小", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '馬公市') {
        addOption(document.myform.selectschool, "164601", "縣立馬公國小", "");
        addOption(document.myform.selectschool, "164602", "縣立中正國小", "");
        addOption(document.myform.selectschool, "164603", "縣立中興國小", "");
        addOption(document.myform.selectschool, "164604", "縣立中山國小", "");
        addOption(document.myform.selectschool, "164605", "縣立石泉國小", "");
        addOption(document.myform.selectschool, "164606", "縣立東衛國小", "");
        addOption(document.myform.selectschool, "164607", "縣立興仁國小", "");
        addOption(document.myform.selectschool, "164608", "縣立山水國小", "");
        addOption(document.myform.selectschool, "164609", "縣立五德國小", "");
        addOption(document.myform.selectschool, "164610", "縣立時裡國小", "");
        addOption(document.myform.selectschool, "164611", "縣立風櫃國小", "");
        addOption(document.myform.selectschool, "164612", "縣立虎井國小", "");
        addOption(document.myform.selectschool, "164645", "縣立文澳國小", "");
        addOption(document.myform.selectschool, "164646", "縣立文光國小", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '湖西鄉') {
        addOption(document.myform.selectschool, "164614", "縣立成功國小", "");
        addOption(document.myform.selectschool, "164615", "縣立西溪國小", "");
        addOption(document.myform.selectschool, "164616", "縣立湖西國小", "");
        addOption(document.myform.selectschool, "164617", "縣立果葉國小", "");
        addOption(document.myform.selectschool, "164618", "縣立龍門國小", "");
        addOption(document.myform.selectschool, "164619", "縣立隘門國小", "");
        addOption(document.myform.selectschool, "164620", "縣立沙港國小", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '白沙鄉') {
        addOption(document.myform.selectschool, "164621", "縣立中屯國小", "");
        addOption(document.myform.selectschool, "164623", "縣立講美國小", "");
        addOption(document.myform.selectschool, "164624", "縣立港子國小", "");
        addOption(document.myform.selectschool, "164625", "縣立赤崁國小", "");
        addOption(document.myform.selectschool, "164627", "縣立鳥嶼國小", "");
        addOption(document.myform.selectschool, "164628", "縣立吉貝國小", "");
        addOption(document.myform.selectschool, "164629", "縣立後寮國小", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '西嶼鄉') {
        addOption(document.myform.selectschool, "164630", "縣立合橫國小", "");
        addOption(document.myform.selectschool, "164631", "縣立竹灣國小", "");
        addOption(document.myform.selectschool, "164633", "縣立大池國小", "");
        addOption(document.myform.selectschool, "164634", "縣立池東國小", "");
        addOption(document.myform.selectschool, "164635", "縣立赤馬國小", "");
        addOption(document.myform.selectschool, "164636", "縣立內垵國小", "");
        addOption(document.myform.selectschool, "164637", "縣立外垵國小", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '望安鄉') {
        addOption(document.myform.selectschool, "164638", "縣立望安國小", "");
        addOption(document.myform.selectschool, "164639", "縣立將軍國小", "");
        addOption(document.myform.selectschool, "164641", "縣立花嶼國小", "");
    }
    if (document.myform.selectcity.value == '澎湖縣' && document.myform.selectdistrict.value == '七美鄉') {
        addOption(document.myform.selectschool, "164643", "縣立七美國小", "");
        addOption(document.myform.selectschool, "164644", "縣立雙湖國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '信義區') {
        addOption(document.myform.selectschool, "173606", "市立東信國小", "");
        addOption(document.myform.selectschool, "173607", "市立中興國小", "");
        addOption(document.myform.selectschool, "173608", "市立深澳國小", "");
        addOption(document.myform.selectschool, "173609", "市立月眉國小", "");
        addOption(document.myform.selectschool, "173610", "市立東光國小", "");
        addOption(document.myform.selectschool, "173641", "市立深美國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '中山區') {
        addOption(document.myform.selectschool, "171601", "私立聖心小學", "");
        addOption(document.myform.selectschool, "173619", "市立中和國小", "");
        addOption(document.myform.selectschool, "173620", "市立仙洞國小", "");
        addOption(document.myform.selectschool, "173621", "市立中山國小", "");
        addOption(document.myform.selectschool, "173622", "市立港西國小", "");
        addOption(document.myform.selectschool, "173623", "市立中華國小", "");
        addOption(document.myform.selectschool, "173624", "市立太平國小", "");
        addOption(document.myform.selectschool, "173625", "市立德和國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '中正區') {
        addOption(document.myform.selectschool, "173601", "市立中正國小", "");
        addOption(document.myform.selectschool, "173602", "市立正濱國小", "");
        addOption(document.myform.selectschool, "173603", "市立忠孝國小", "");
        addOption(document.myform.selectschool, "173604", "市立和平國小", "");
        addOption(document.myform.selectschool, "173605", "市立八斗國小", "");
        addOption(document.myform.selectschool, "171306", "私立二信高中附設國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '仁愛區') {
        addOption(document.myform.selectschool, "173611", "市立仁愛國小", "");
        addOption(document.myform.selectschool, "173612", "市立信義國小", "");
        addOption(document.myform.selectschool, "173613", "市立成功國小", "");
        addOption(document.myform.selectschool, "173614", "市立南榮國小", "");
        addOption(document.myform.selectschool, "173615", "市立尚智國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '安樂區') {
        addOption(document.myform.selectschool, "173616", "市立安樂國小", "");
        addOption(document.myform.selectschool, "173617", "市立西定國小", "");
        addOption(document.myform.selectschool, "173618", "市立武崙國小", "");
        addOption(document.myform.selectschool, "173633", "市立建德國小", "");
        addOption(document.myform.selectschool, "173638", "市立隆聖國小", "");
        addOption(document.myform.selectschool, "173640", "市立長樂國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '七堵區') {
        addOption(document.myform.selectschool, "173626", "市立七堵國小", "");
        addOption(document.myform.selectschool, "173627", "市立華興國小", "");
        addOption(document.myform.selectschool, "173628", "市立五堵國小", "");
        addOption(document.myform.selectschool, "173629", "市立堵南國小", "");
        addOption(document.myform.selectschool, "173630", "市立瑪陵國小", "");
        addOption(document.myform.selectschool, "173631", "市立復興國小", "");
        addOption(document.myform.selectschool, "173632", "市立尚仁國小", "");
        addOption(document.myform.selectschool, "173639", "市立長興國小", "");
    }
    if (document.myform.selectcity.value == '基隆市' && document.myform.selectdistrict.value == '暖暖區') {
        addOption(document.myform.selectschool, "173634", "市立八堵國小", "");
        addOption(document.myform.selectschool, "173635", "市立暖暖國小", "");
        addOption(document.myform.selectschool, "173636", "市立暖江國小", "");
        addOption(document.myform.selectschool, "173637", "市立碇內國小", "");
        addOption(document.myform.selectschool, "173642", "市立暖西國小", "");
    }
    if (document.myform.selectcity.value == '新竹市' && document.myform.selectdistrict.value == '北區') {
        addOption(document.myform.selectschool, "180601", "國立新竹教大附小", "");
        addOption(document.myform.selectschool, "183602", "市立北門國小", "");
        addOption(document.myform.selectschool, "183603", "市立民富國小", "");
        addOption(document.myform.selectschool, "183605", "市立西門國小", "");
        addOption(document.myform.selectschool, "183611", "市立載熙國小", "");
        addOption(document.myform.selectschool, "183612", "市立南寮國小", "");
        addOption(document.myform.selectschool, "183626", "市立舊社國小", "");
    }
    if (document.myform.selectcity.value == '新竹市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "181601", "私立曙光國小", "");
        addOption(document.myform.selectschool, "181602", "私立矽谷國(中)小", "");
        addOption(document.myform.selectschool, "183601", "市立新竹國小", "");
        addOption(document.myform.selectschool, "183604", "市立東門國小", "");
        addOption(document.myform.selectschool, "183606", "市立竹蓮國小", "");
        addOption(document.myform.selectschool, "183607", "市立東園國小", "");
        addOption(document.myform.selectschool, "183608", "市立三民國小", "");
        addOption(document.myform.selectschool, "183609", "市立龍山國小", "");
        addOption(document.myform.selectschool, "183610", "市立關東國小", "");
        addOption(document.myform.selectschool, "183613", "市立建功國小", "");
        addOption(document.myform.selectschool, "183614", "市立水源國小", "");
        addOption(document.myform.selectschool, "183627", "市立陽光國小", "");
        addOption(document.myform.selectschool, "183628", "市立科園國小", "");
        addOption(document.myform.selectschool, "183629", "市立高峰國小", "");
        addOption(document.myform.selectschool, "183630", "市立青草湖國小", "");
        addOption(document.myform.selectschool, "180301", "國立科學工業園區實驗高中附設國小", "");
    }
    if (document.myform.selectcity.value == '新竹市' && document.myform.selectdistrict.value == '香山區') {
        addOption(document.myform.selectschool, "183615", "市立香山國小", "");
        addOption(document.myform.selectschool, "183616", "市立虎林國小", "");
        addOption(document.myform.selectschool, "183617", "市立港南國小", "");
        addOption(document.myform.selectschool, "183618", "市立大庄國小", "");
        addOption(document.myform.selectschool, "183619", "市立茄苳國小", "");
        addOption(document.myform.selectschool, "183620", "市立朝山國小", "");
        addOption(document.myform.selectschool, "183621", "市立大湖國小", "");
        addOption(document.myform.selectschool, "183622", "市立內湖國小", "");
        addOption(document.myform.selectschool, "183623", "市立南隘國小", "");
        addOption(document.myform.selectschool, "183625", "市立頂埔國小", "");
        addOption(document.myform.selectschool, "183631", "市立華德福實驗國小", "");
    }
    if (document.myform.selectcity.value == '嘉義市' && document.myform.selectdistrict.value == '東區') {
        addOption(document.myform.selectschool, "200601", "國立嘉義大學附小", "");
        addOption(document.myform.selectschool, "203601", "市立崇文國小", "");
        addOption(document.myform.selectschool, "203604", "市立民族國小", "");
        addOption(document.myform.selectschool, "203605", "市立宣信國小", "");
        addOption(document.myform.selectschool, "203608", "市立嘉北國小", "");
        addOption(document.myform.selectschool, "203610", "市立林森國小", "");
        addOption(document.myform.selectschool, "203612", "市立精忠國小", "");
        addOption(document.myform.selectschool, "203614", "市立蘭潭國小", "");
        addOption(document.myform.selectschool, "203615", "市立興安國小", "");
        addOption(document.myform.selectschool, "203619", "市立文雅國小", "");
    }
    if (document.myform.selectcity.value == '嘉義市' && document.myform.selectdistrict.value == '西區') {
        addOption(document.myform.selectschool, "203602", "市立博愛國小", "");
        addOption(document.myform.selectschool, "203603", "市立垂楊國小", "");
        addOption(document.myform.selectschool, "203606", "市立大同國小", "");
        addOption(document.myform.selectschool, "203607", "市立志航國小", "");
        addOption(document.myform.selectschool, "203609", "市立僑平國小", "");
        addOption(document.myform.selectschool, "203611", "市立北園國小", "");
        addOption(document.myform.selectschool, "203613", "市立育人國小", "");
        addOption(document.myform.selectschool, "203616", "市立世賢國小", "");
        addOption(document.myform.selectschool, "203617", "市立興嘉國小", "");
        addOption(document.myform.selectschool, "203618", "市立港坪國小", "");
        addOption(document.myform.selectschool, "200F01", "國立嘉義啟智學校", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金湖鎮') {
        addOption(document.myform.selectschool, "714601", "縣立金湖國小", "");
        addOption(document.myform.selectschool, "714606", "縣立開瑄國小", "");
        addOption(document.myform.selectschool, "714607", "縣立柏村國小", "");
        addOption(document.myform.selectschool, "714619", "縣立正義國小", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金寧鄉') {
        addOption(document.myform.selectschool, "714602", "縣立金寧國(中)小", "");
        addOption(document.myform.selectschool, "714613", "縣立古寧國小", "");
        addOption(document.myform.selectschool, "714614", "縣立金鼎國小", "");
        addOption(document.myform.selectschool, "714618", "縣立湖埔國小", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金城鎮') {
        addOption(document.myform.selectschool, "714603", "縣立中正國小", "");
        addOption(document.myform.selectschool, "714604", "縣立賢庵國小", "");
        addOption(document.myform.selectschool, "714605", "縣立古城國小", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '金沙鎮') {
        addOption(document.myform.selectschool, "714608", "縣立多年國小", "");
        addOption(document.myform.selectschool, "714609", "縣立金沙國小", "");
        addOption(document.myform.selectschool, "714610", "縣立何浦國小", "");
        addOption(document.myform.selectschool, "714611", "縣立安瀾國小", "");
        addOption(document.myform.selectschool, "714612", "縣立述美國小", "");
    }
    if (document.myform.selectcity.value == '金門縣' && document.myform.selectdistrict.value == '烈嶼鄉') {
        addOption(document.myform.selectschool, "714615", "縣立卓環國小", "");
        addOption(document.myform.selectschool, "714616", "縣立上岐國小", "");
        addOption(document.myform.selectschool, "714620", "縣立西口國小", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '南竿鄉') {
        addOption(document.myform.selectschool, "724601", "縣立介壽國(中)小", "");
        addOption(document.myform.selectschool, "724602", "縣立中正國(中)小", "");
        addOption(document.myform.selectschool, "724603", "縣立仁愛國小", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '北竿鄉') {
        addOption(document.myform.selectschool, "724604", "縣立塘岐國小", "");
        addOption(document.myform.selectschool, "724605", "縣立?里國小", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '莒光鄉') {
        addOption(document.myform.selectschool, "724606", "縣立敬恆國(中)小", "");
        addOption(document.myform.selectschool, "724607", "縣立東莒國小", "");
    }
    if (document.myform.selectcity.value == '連江縣' && document.myform.selectdistrict.value == '東引鄉') {
        addOption(document.myform.selectschool, "724608", "縣立東引國(中)小", "");
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
