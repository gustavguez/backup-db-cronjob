<?php
    //Composer load
    include_once("./vendor/autoload.php");

    //Include config
    include_once("./config.php");

    //Create backup db class
    $backup = new Gustavguez\Backup\BackupDbJob(
        $dumpDirectory,
        $dbHost,
        $dbName,
        $dbUser,
        $dbPassword
    );

    //Process $_GET
    $backup->processGET();

    //IS valid request?
    if($backup->isValidKey($backupKey)){
        //Start dump steps
        $backup->dumpDB();
    } else {
        //Invalid key, no dump
        die('Invalid key!');
    }
?>