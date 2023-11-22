# Purpose of the script
This script used for migration from old/other mssql db into new mysqldb. It is to ease the process of copy pasting queries and validating the data before we insert into the new db. This script can also support migration from multiple mssql db into one mysql db.

# How to use
Varible definition:-
* **withTransaction** (true/false): Will run the queries using transaction , if encounter any error , everything will be rollback. meaning non of the query will be inserted. This is to ensure that we only want a fully success migration.
* **dryrun** (true/false): In order for this to work, withTransaction must be true. Dryrun meaning, even if all the queries succeed but it will still not insert into the db.
* **echo** (true/false): To echo in CLI 
* **logErrors** (true/false): To log down all the queries ran, the execution time, the errors.

# Thing to take note
In your query files, things you may do as below:-
* If you have multiple old db, sometimes you may only want to run the query once, something like inserting a hardcoded thing. You can achieving that by added !!! infront of the query.
* You must put the mysql table name in front of the query with brackets to indicate this data is to be inserted into that table EX: (testTable)SELECT * FROM MSSQL.table;

* **YOU MUST PUT ; at the end of every query**

* In order for multiple db to work, remember to put **{YOUR_DB_NAME}** in front of your table for every query. For example, INSERT INTO TABLE_A SELECT * FROM {YOUR_DB_NAME}.TABLE_A;

* With transaction will not work , if there's a CREATE/ALTER/DROP/TRUNCATE command in your queries. For more info : <a href="https://dev.mysql.com/doc/refman/8.0/en/cannot-roll-back.html#:~:text=Some%20statements%20cannot%20be%20rolled,alter%20tables%20or%20stored%20routines." target="_blank">__Click here...__</a>
