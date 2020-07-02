create table outbound_traffic(
    年 int NOT NULL,
	月 int NOT NULL,
	國家名稱 varchar(15) NOT NULL,
	飛機_松山 int,
	飛機_桃園 int,
	飛機_臺中 int,
	飛機_高雄 int,
	飛機_其他 int,
	輪船_基隆 int,
	輪船_臺中 int,
	輪船_高雄 int,
	輪船_蘇澳 int,
	輪船_花蓮 int,
	輪船_金門 int,
	輪船_馬祖 int,
	輪船_其他 int,
    primary key (年, 月, 國家名稱)
);

create trigger drop_eng_traff before insert on outbound_traffic
	for each row
		set new.國家名稱 = substring_index(new.國家名稱, '(', 1);

load data local infile './data/Outbound_Traffic.csv'
into table outbound_traffic
character set utf8
fields terminated by ','
enclosed by '\"'
lines terminated by "\n"
ignore 2 lines;

delete from outbound_traffic where 年 = 0;
