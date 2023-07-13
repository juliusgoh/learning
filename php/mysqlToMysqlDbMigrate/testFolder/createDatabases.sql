-- Create OLD TEST DB;
-- CREATE DATABASE IF NOT EXISTS {YOUR_DB_NAME};
-- CREATE TABLE IF NOT EXISTS {YOUR_DB_NAME}.testTable(id bigint(20) NOT NULL,data1 varchar(30) DEFAULT NULL,data2 varchar(50) DEFAULT NULL,enabled tinyint(4) DEFAULT 1, PRIMARY KEY (`id`));

-- !!!CREATE TABLE IF NOT EXISTS migratedTestTable (id bigint(20) NOT NULL,data1 varchar(30) DEFAULT NULL,data2 varchar(50) DEFAULT NULL,enabled tinyint(4) DEFAULT 1, PRIMARY KEY (`id`));
-- INSERT INTO {YOUR_DB_NAME}.testTable(id,data1,data2,enabled) VALUES (1,1,1,0);
-- INSERT INTO {YOUR_DB_NAME}.testTable(id,data1,data2,enabled) VALUES (2,2,2,1);
-- INSERT INTO {YOUR_DB_NAME}.testTable(id,data1,data2,enabled) VALUES (3,4,5,1);