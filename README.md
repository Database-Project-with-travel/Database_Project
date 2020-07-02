# Database_Project

##Setup DataBase and Create Tables
To use Chinese, after creating the database, please type the command:
ALTER DATABASE yourdatabasename CHARACTER SET utf8 COLLATE utf8_unicode_ci;

Open mysql in the Database_Project directory.
To create tables, use:
source create_table/create_table.sql

Note that / and - in the table names or table instances are all fullwidth ／ and —,
and that 阿富汗(觀光) is truncated into 阿富汗.
