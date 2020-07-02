create table exchange_rate(
    日期 date NOT NULL,
	美元／新台幣 double,
	人民幣／新台幣 double,
	歐元／美元 double,
	美元／日幣 double,
	英鎊／美元 double,
	澳幣／美元 double,		
	美元／港幣 double,		
	美元／人民幣 double,		
	美元／南非幣 double,		
	紐幣／美元 double,
    primary key (日期)
);

load data local infile './data/200901-202004_Exchange_Rate.csv'
into table exchange_rate
fields terminated by ','
lines terminated by "\r\n"
ignore 1 lines;
