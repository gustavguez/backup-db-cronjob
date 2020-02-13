<?php
namespace Gustavguez\Backup;

use Ifsnop\Mysqldump as IMysqldump;

class BackupDbJob {

    /**
     * Private models
     */
    protected $dumpDirectory;
    protected $dbHost;
    protected $dbName;
    protected $dbUser;
    protected $dbPassword;
    protected $lastSeedName;
    protected $keyFromParams;
    
    public function __construct(
        $dumpDirectory,
        $dbHost,
        $dbName,
        $dbUser,
        $dbPassword ) {
            $this->dumpDirectory = $dumpDirectory;
            $this->dbHost = $dbHost;
            $this->dbName = $dbName;
            $this->dbUser = $dbUser;
            $this->dbPassword = $dbPassword;
        }

    public function isValidKey($validKey){
        return ($this->keyFromParams == $validKey);
    }

    public function processGET(){
        $this->keyFromParams = $_GET['key'];
    }
    
    public function dumpDB(){
        try {
            $dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
            $this->lastSeedName = $this->getSeedName();

            //Create mysql dump obj
            $dump = new IMysqldump\Mysqldump($dsn, $this->dbUser, $this->dbPassword);
            $dump->start($this->dumpDirectory . $this->lastSeedName);
        } catch (\Exception $e) {
            echo 'mysqldump-php error: ' . $e->getMessage();
        }
    }

    private function getSeedName() {
        return date('d-m-Y') . '.sql';
    }
}