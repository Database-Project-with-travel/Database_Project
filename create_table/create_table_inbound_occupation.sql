create table inbound_occupation(
    年 int NOT NULL,
	月 int NOT NULL,
	居住地 varchar(15) NOT NULL,
	商人 int,
	秘書職員 int,
	專門技術 int,
	文化工作 int,
	家管退休 int,
	軍公職 int,
	其他職業 int,
    primary key (年, 月, 居住地)
);

create trigger drop_eng_in_occupation before insert on inbound_occupation
	for each row
		set new.居住地 = substring_index(new.居住地, '(', 1);

load data local infile './data/Inbound_Occupation.csv'
into table inbound_occupation
character set utf8
fields terminated by ','
enclosed by '\"'
lines terminated by "\n"
ignore 2 lines
(年,月,居住地, @dummy, 商人, 秘書職員, 專門技術, 文化工作, 家管退休, 軍公職, 其他職業);

delete from inbound_occupation where 年 = 0;
