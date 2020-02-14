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
        $dbPassword,

        $mailHost,
        $mailFrom,
        $mailFromPassword,
        $mailFromName,
        $mailTo,
        $mailToName,
        $mailSubject,
        $mailBody
    );

    //Process $_GET
    $backup->processGET();

    //IS valid request?
    if($backup->isValidKey($backupKey)){
        //Start dump steps
        $backup->dumpDB();
        $backup->sendDump();
    } else {
        //Invalid key, no dump
        die('Invalid key!');
    }
?>