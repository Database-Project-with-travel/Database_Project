create table inbound_purpose(
    年 int NOT NULL,
	月 int NOT NULL,
	居住地 varchar(15) NOT NULL,
	業務 int,
	觀光 int,
	探親 int,
	會議 int,
	求學 int,
	展覽 int,
	醫療 int,
	其他 int,
    primary key (年, 月, 居住地)
);

create trigger drop_eng_purpose before insert on inbound_purpose
	for each row
		set new.居住地 = substring_index(new.居住地, '(', 1);

load data local infile './data/Inbound_Purpose.csv'
into table inbound_purpose
character set utf8
fields terminated by ','
enclosed by '\"'
lines terminated by "\n"
ignore 2 lines;

delete from inbound_purpose where 年 = 0;
