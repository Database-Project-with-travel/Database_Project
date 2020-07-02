create table inbound_age(
    年 int NOT NULL,
	月 int NOT NULL,
	居住地 varchar(15) NOT NULL,
	1—12歲 int,
	13—19歲 int,
	20—29歲 int,
	30—39歲 int,
	40—49歲 int,
	50—59歲 int,
	60歲以上 int,
    primary key (年, 月, 居住地)
);

create trigger drop_eng_in_age before insert on inbound_age
	for each row
		set new.居住地 = substring_index(new.居住地, '(', 1);

load data local infile './data/Inbound_Age.csv'
into table inbound_age
character set utf8
fields terminated by ','
enclosed by '\"'
lines terminated by "\n"
ignore 2 lines;

delete from inbound_age where 年 = 0;
