print("create table inbound_chinese(\n",
    "年 int NOT NULL,\n",
	"月 int NOT NULL,\n",
	"居住地 varchar(15) NOT NULL,\n",
	"華僑	int,\n",
	"外籍     int,\n",
    "primary key (年, 月, 居住地)\n",
");\n",
sep="")
print()

country_names = ['日本','韓國','北韓','香港','中國大陸','澳門','蒙古','越南','泰國','馬來西亞','新加坡','菲律賓','汶萊','印尼','柬埔寨','寮國','帝汶','緬甸','索羅門群島','吐瓦魯','印度','巴基斯坦','斯里蘭卡','孟加拉','尼泊爾','不丹','馬爾地夫','塞席爾','吉里巴斯','伊朗','伊拉克','巴林','南葉門','沙烏地阿拉伯','科威特','卡達','阿曼','以色列','約旦','敘利亞','黎巴嫩','阿拉伯聯合大公國','葉門','土耳其','賽普勒斯','阿富汗','埃及','摩洛哥','阿爾及利亞','突尼西亞','利比亞','蘇丹','索馬利亞','南非','象牙海岸','奈及利亞','賴比瑞亞','幾內亞','安哥拉','剛果共和國','中非共和國','波札那','喀麥隆','查德','衣索匹亞','加彭','甘比亞','迦納','肯亞','賴索托','馬拉加西','模里西斯','馬利','馬拉威','莫三鼻克','尼日','盧安達','塞內加爾','獅子山','坦桑尼亞','烏干達','尚比亞','多哥','辛巴威','史瓦濟蘭','布吉納法索','貝南','赤道幾內亞','維德角島','吉布地','西撒哈拉','幾內亞比索','葛摩','留尼旺','聖多美及普林西比','美國','加拿大','百慕達','墨西哥','巴拿馬','巴貝多','巴哈馬','古巴','哥斯達黎加','多明尼加','瓜地馬拉','宏都拉斯','海地','牙買加','尼加拉瓜','薩爾瓦多','千里達及托貝哥','聖文森','貝里斯','格瑞那達','聖露西亞','聖克里斯多福','阿根廷','巴西','智利','委內瑞拉','玻利維亞','哥倫比亞','厄瓜多爾','巴拉圭','秘魯','烏拉圭','蘇利南','蓋亞納','澳洲','紐西蘭','裴濟','諾魯','帛琉','東加','巴布亞新幾內亞','萬那杜','馬紹爾','大溪地','密克羅尼西亞','比利時','法國','德國','義大利','荷蘭','英國','西班牙','瑞典','瑞士','挪威','丹麥','愛爾蘭','冰島','葡萄牙','芬蘭','奧地利','南斯拉夫','希臘','馬爾他','盧森堡','摩納哥','安道爾','列支敦斯登','白俄羅斯','烏克蘭','教廷','克羅埃西亞','波士尼亞','愛沙尼亞','立陶宛','拉脫維亞','馬其頓','斯洛凡尼亞','斯洛伐克','俄羅斯','東德','波蘭','捷克','匈牙利','阿爾巴尼亞','羅馬尼亞','保加利亞','摩爾多瓦','聖馬利諾']
i = 0
for names in country_names:
    print("load data local infile './data/Inbound_Overseas_Chinese.csv'",
    "into table inbound_chinese\n",
    "character set utf8\n",
    "fields terminated by ','\n",
    "enclosed by '\"'\n",
    "lines terminated by \"\\n\"",
    "ignore 4 lines\n",
    "(年,月,@col0,@col1,@col2,@col3,@col4,@col5,@col6,@col7,@col8,@col9,@col10,@col11,@col12,@col13,@col14,@col15,@col16,@col17,@col18,@col19,@col20,@col21,@col22,@col23,@col24,@col25,@col26,@col27,@col28,@col29,@col30,@col31,@col32,@col33,@col34,@col35,@col36,@col37,@col38,@col39,@col40,@col41,@col42,@col43,@col44,@col45,@col46,@col47,@col48,@col49,@col50,@col51,@col52,@col53,@col54,@col55,@col56,@col57,@col58,@col59,@col60,@col61,@col62,@col63,@col64,@col65,@col66,@col67,@col68,@col69,@col70,@col71,@col72,@col73,@col74,@col75,@col76,@col77,@col78,@col79,@col80,@col81,@col82,@col83,@col84,@col85,@col86,@col87,@col88,@col89,@col90,@col91,@col92,@col93,@col94,@col95,@col96,@col97,@col98,@col99,@col100,@col101,@col102,@col103,@col104,@col105,@col106,@col107,@col108,@col109,@col110,@col111,@col112,@col113,@col114,@col115,@col116,@col117,@col118,@col119,@col120,@col121,@col122,@col123,@col124,@col125,@col126,@col127,@col128,@col129,@col130,@col131,@col132,@col133,@col134,@col135,@col136,@col137,@col138,@col139,@col140,@col141,@col142,@col143,@col144,@col145,@col146,@col147,@col148,@col149,@col150,@col151,@col152,@col153,@col154,@col155,@col156,@col157,@col158,@col159,@col160,@col161,@col162,@col163,@col164,@col165,@col166,@col167,@col168,@col169,@col170,@col171,@col172,@col173,@col174,@col175,@col176,@col177,@col178,@col179,@col180,@col181,@col182,@col183,@col184,@col185,@col186,@col187,@col188,@col189,@col190,@col191,@col192,@col193,@col194,@col195,@col196,@col197,@col198,@col199,@col200,@col201,@col202,@col203,@col204,@col205,@col206,@col207,@col208,@col209,@col210,@col211,@col212,@col213,@col214,@col215,@col216,@col217,@col218,@col219,@col220,@col221,@col222,@col223,@col224,@col225,@col226,@col227,@col228,@col229,@col230,@col231,@col232,@col233,@col234,@col235,@col236,@col237,@col238,@col239,@col240,@col241,@col242,@col243,@col244,@col245,@col246,@col247,@col248,@col249,@col250,@col251,@col252,@col253,@col254,@col255,@col256,@col257,@col258,@col259,@col260,@col261,@col262,@col263,@col264,@col265,@col266,@col267,@col268,@col269,@col270,@col271,@col272,@col273,@col274,@col275,@col276,@col277,@col278,@col279,@col280,@col281,@col282,@col283,@col284,@col285,@col286,@col287,@col288,@col289,@col290,@col291,@col292,@col293,@col294,@col295,@col296,@col297,@col298,@col299,@col300,@col301,@col302,@col303,@col304,@col305,@col306,@col307,@col308,@col309,@col310,@col311,@col312,@col313,@col314,@col315,@col316,@col317,@col318,@col319,@col320,@col321,@col322,@col323,@col324,@col325,@col326,@col327,@col328,@col329,@col330,@col331,@col332,@col333,@col334,@col335,@col336,@col337,@col338,@col339,@col340,@col341,@col342,@col343,@col344,@col345,@col346,@col347,@col348,@col349,@col350,@col351,@col352,@col353,@col354,@col355,@col356,@col357,@col358,@col359,@col360,@col361,@col362,@col363,@col364,@col365,@col366,@col367)\n",
    "set 居住地 = '",names,"', 華僑=@col",i,",外籍=@col",i+1,";", sep="")
    i = i+2
print("\ndelete from inbound_chinese where 年 = 0;")

