<?php
$db = new mysqli('localhost', 'root', '','migratedDB');

$withTransaction = true;
$dryrun = true;
$echo = true;
$logErrors = false;
$oldDbs = array('OLD_DB_TO_BE_MIGRATE_1','OLD_DB_TO_BE_MIGRATE_2');
$logFileName = "logs/".date("YmdHis")."-migrate.log";

if($withTransaction)
{
    $db->begin_transaction();
}
function listFolderFiles($dir)
{
    $ffs = scandir($dir);
    $foldersToSkip = array('.','..','logs','testFolder');
    foreach($foldersToSkip AS $skipFolder)
    {
        if(in_array($skipFolder,$ffs))
        {
            unset($ffs[array_search($skipFolder, $ffs, true)]);
        }
    }
    
    if (array_search('mysqlToMysqlDbMigrateScript.php', $ffs, true) !== false)
    {
        unset($ffs[array_search('mysqlToMysqlDbMigrateScript.php', $ffs, true)]);
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
    global $oldDbs;
    global $withTransaction;
    echoAndLog("*********************************************** RUNNING script $script ***********************************************\n");
    $file = file_get_contents($script);
    $sqls = explode(";", trim($file));
    
    array_pop($sqls);
    $i = 1;
    foreach ($sqls as $sql)
    {
        $runOnce = false;
        if(substr(trim($sql), 0 , 3) == "!!!")
        {
            $runOnce = true;
        }

        foreach($oldDbs AS $oldDbName)
        {
            $alteredSql = str_replace('{YOUR_DB_NAME}',$oldDbName,trim($sql));
            $alteredSql = $runOnce ? substr($alteredSql, 3) : $alteredSql;
            echoAndLog("RUNNING QUERY $i for $oldDbName: \n" . trim($alteredSql)."\n\n");
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
    
    if (!file_exists('logs')) {
        mkdir('logs', 0777, true);
    }

    if ($echo)
    {
        echo $str;
    }

    if ($logErrors)
    {
        file_put_contents($logFileName, $str, FILE_APPEND | LOCK_EX);
    }
}

listFolderFiles('./testFolder');
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