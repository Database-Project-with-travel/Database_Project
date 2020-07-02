create table inbound_traffic(
    年 int NOT NULL,
	月 int NOT NULL,
	居住地 varchar(15) NOT NULL,
	飛機_松山 int,
	飛機_桃園 int,
	飛機_臺中 int,
	飛機_高雄 int,
	飛機_其他 int,
	輪船_基隆 int,
	輪船_臺中 int,
	輪船_高雄 int,
	輪船_花蓮 int,
	輪船_其他 int,
    primary key (年, 月, 居住地)
);

create trigger drop_eng_in_traffic before insert on inbound_traffic
	for each row
		set new.居住地 = substring_index(new.居住地, '(', 1);

load data local infile './data/Inbound_Traffic.csv'
into table inbound_traffic
character set utf8
fields terminated by ','
enclosed by '\"'
lines terminated by "\n"
ignore 2 lines
(年, 月, 居住地, @dummy, 飛機_松山, 飛機_桃園, 飛機_臺中, 飛機_高雄, 飛機_其他, 輪船_基隆, 輪船_臺中, 輪船_高雄, 輪船_花蓮, 輪船_其他);

delete from inbound_traffic where 年 = 0;
