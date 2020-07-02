create table country(
    年 int NOT NULL,
	月 int NOT NULL,
	國家名稱 varchar(15) NOT NULL,
	英文國名 varchar(40),
    primary key (年, 月, 國家名稱)
);

create trigger drop_bracket_in_afghaninstan before insert on country
	for each row
		set new.國家名稱 = substring_index(new.國家名稱, '(', 1);

load data local infile './data/Inbound_Age.csv'
into table country
character set utf8
fields terminated by ','
enclosed by '\"'
lines terminated by "\n"
ignore 2 lines;

delete from country where 年 = 0;
