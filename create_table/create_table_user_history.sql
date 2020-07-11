create table user_history(
	query_time DATETIME DEFAULT CURRENT_TIMESTAMP,
	query_sql TEXT,
	primary key(query_time)
);
