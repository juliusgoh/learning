<?php
$newDbName = "test";
$db = new mysqli("localhost", "root", "", $newDbName);
$mssqlDBPDO = new PDO("sqlsrv:Server=JULIUS_ASUS;", "julius", "root");

$mssqlDbs = array("[testDb].[dbo]","[testDb2].[dbo]");
$withTransaction = true;
$dryrun = false;
$echo = true;
$logErrors = false;
$logFileName = "logs/".date("YmdHis")."-migrate.log";

if($withTransaction)
{
    $db->begin_transaction();
}

listFolderFiles('./scripts');
if($withTransaction)
{
    if ($dryrun)
    {
        $db->rollback();
    }
    else
    {
        $db->commit();
    }
}

function listFolderFiles($dir)
{
    $ffs = scandir($dir);
    $foldersToSkip = array('.','..','logs','testFolder','007smMarketing','runSeperately');
    foreach($foldersToSkip AS $skipFolder)
    {
        if(in_array($skipFolder,$ffs))
        {
            unset($ffs[array_search($skipFolder, $ffs, true)]);
        }
    }
    
    if (array_search('migrate.php', $ffs, true) !== false)
    {
        unset($ffs[array_search('migrate.php', $ffs, true)]);
    }

    // prevent empty ordered elements
    if (count($ffs) < 1)
        return;

    foreach ($ffs as $ff)
    {
        if (is_dir($dir . '/' . $ff))
        {
            echoAndLog("\n\n*********************** RUNNING FOR (" . $ff . ") ***********************");
            listFolderFiles($dir . '/' . $ff);
        }
        else
        {
            runScript($dir . "/" . $ff);
        }
    }
}

function runScript($script)
{
    global $db;
    global $newDbName;
    global $withTransaction;
    global $mssqlDBPDO;
    global $mssqlDbs;

    echoAndLog("*********************************************** RUNNING script $script ***********************************************\n");
    $file = file_get_contents($script);
    $sqls = explode(";", trim($file));
    
    array_pop($sqls);
    $i = 1;
    foreach ($sqls as $sql)
    {
        $runOnce = false;
        if(substr($sql, 0 , 3) == "!!!")
        {
            $runOnce = true;
        }
        
        $open = strpos($sql, "(");
        $close = strpos($sql, ")");
        $dbTable = str_replace("{YOUR_DB_NAME}", $newDbName, substr($sql, $open + 1, $close - $open - 1));
        $query = substr($sql, $close + 1, strlen($sql));
        foreach($mssqlDbs AS $mssqlDbName)
        {
            $query = str_replace("{MSSQL_DB_NAME}",$mssqlDbName,$query);
            $mssqlQueryResult = $mssqlDBPDO->query($query);
            foreach ($mssqlQueryResult->fetchAll(PDO::FETCH_NUM) as $row)
            {
                $sql = "SELECT ";
                $hasNextData = true;
                $j = 0;
                while ($hasNextData)
                {
                    if (isset($row[$j]))
                    {
                        $sql .= "'" . $row[$j] . "',";
                    }
        
                    if (!isset($row[$j + 1]))
                    {
                        $sql = rtrim($sql, ",");
                        $hasNextData = false;
                    }
        
                    $j++;
                }
            }
        
            $alteredSql = "INSERT INTO $dbTable $sql";
            echoAndLog("RUNNING QUERY $i for $mssqlDbName: \n" . trim($alteredSql)."\n");
            $start = microtime(true);
            if (!$db->query($alteredSql))
            {
                $end = microtime(true);
                $elapsed = $end - $start;
                echoAndLog("\nError description: " . $db->error . " ($elapsed sec)\n\n");
                if($withTransaction)
                {
                    $db->rollback();
                }
    
                exit();
            }
            else
            {
                $end = microtime(true);
                $elapsed = $end - $start;
                echoAndLog("\nAffected rows: {$db->affected_rows} ($elapsed sec)\n\n");
                if($runOnce)
                {
                    break;
                }
            }
        }        
        $i++;
    }
}

function echoAndLog($str)
{
    global $echo;
    global $logErrors;
    global $logFileName;
    if ($echo)
    {
        echo $str;
    }

    if ($logErrors)
    {
        file_put_contents($logFileName, $str, FILE_APPEND | LOCK_EX);
    }
}