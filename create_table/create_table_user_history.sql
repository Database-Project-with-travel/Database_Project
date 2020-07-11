create table user_history(
	query_time DATETIME DEFAULT CURRENT_TIMESTAMP,
	query_sql varchar(15),
	primary key(query_time)
);
