create table country_currency(
	國家名稱 varchar(15) NOT NULL,
	幣別 varchar(8) NOT NULL,
	ISO varchar(5) NOT NULL,
	primary key (國家名稱)
);

load data local infile './data/Currency.csv'
into table country_currency
character set utf8
fields terminated by ','
lines terminated by "\n"
ignore 1 rows;