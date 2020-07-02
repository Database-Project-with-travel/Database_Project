create table outbound_age(
    年 int NOT NULL,
	月 int NOT NULL,
	國家名稱 varchar(15) NOT NULL,
	1—12歲 int,
	13—19歲 int,
	20—29歲 int,
	30—39歲 int,
	40—49歲 int,
	50—59歲 int,
	60歲以上 int,
    primary key (年, 月, 國家名稱)
);

create trigger drop_eng before insert on outbound_age
	for each row
		set new.國家名稱 = substring_index(new.國家名稱, '(', 1);

load data local infile './data/Outbound_Age.csv'
into table outbound_age
character set utf8
fields terminated by ','
enclosed by '"'
lines terminated by "\n"
ignore 2 lines;

delete from outbound_age where 年 = 0;
