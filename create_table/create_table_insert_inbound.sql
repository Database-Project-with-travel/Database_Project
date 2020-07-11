create table insert_inbound(
	id int NOT NULL auto_increment,
	年 int NOT NULL,
	月 int NOT NULL,
	居住地 varchar(15) NOT NULL,
	年齡 varchar(15) NOT NULL,
	華僑／外籍 varchar(15) NOT NULL,
	性別 varchar(15) NOT NULL,
	職業 varchar(15) NOT NULL,
	來臺原因 varchar(15) NOT NULL,
	交通方式 varchar(15) NOT NULL,
	primary key(id)
);
