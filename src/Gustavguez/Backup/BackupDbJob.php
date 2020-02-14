<?php
namespace Gustavguez\Backup;

use Ifsnop\Mysqldump as IMysqldump;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

    protected $mailHost;
    protected $mailFrom;
    protected $mailFromPassword;
    protected $mailFromName;
    protected $mailTo;
    protected $mailToName;
    protected $mailSubject;
    protected $mailBody;
    
    public function __construct(
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
    ) {
            $this->dumpDirectory = $dumpDirectory;
            $this->dbHost = $dbHost;
            $this->dbName = $dbName;
            $this->dbUser = $dbUser;
            $this->dbPassword = $dbPassword;

            $this->mailHost = $mailHost;
            $this->mailFrom = $mailFrom;
            $this->mailFromPassword = $mailFromPassword;
            $this->mailFromName = $mailFromName;
            $this->mailTo = $mailTo;
            $this->mailToName = $mailToName;
            $this->mailSubject = $mailSubject;
            $this->mailBody = $mailBody;
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
            //Do nothing
        }
    }

    public function sendDump(){
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
            $mail->Host       = $this->mailHost;                    // Set the SMTP server to send through
            $mail->Username   = $this->mailFrom;                     // SMTP username
            $mail->Password   = $this->mailFromPassword;                               // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
            $mail->Port       = 465;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom($this->mailFrom, $this->mailFromName);
            $mail->addAddress($this->mailTo, $this->mailToName);

            // Attachments
            $mail->addAttachment($this->dumpDirectory . $this->lastSeedName);

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $this->mailSubject;
            $mail->Body    = $this->mailBody;

            $mail->send();
        } catch (\Exception $e) {
            //Do nothing
        }
    }

    private function getSeedName() {
        return date('d-m-Y') . '.sql';
    }
}