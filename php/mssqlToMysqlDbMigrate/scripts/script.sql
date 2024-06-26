({YOUR_DB_NAME}.temporaryTable)SELECT * FROM {MSSQL_DB_NAME}.[testTable];
({YOUR_DB_NAME}.temporaryTable)SELECT * FROM {MSSQL_DB_NAME}.[testTable2];
-- Example for Ignore;
[IGNORE]({YOUR_DB_NAME}.temporaryTable)SELECT * FROM {MSSQL_DB_NAME}.[testTable2];
--Example for run once;
!!!({YOUR_DB_NAME}.temporaryTable)SELECT * FROM {MSSQL_DB_NAME}.[testTable2];
|MYSQL|INSERT INTO tableA SELECT 1,2,3;