<?php
namespace AgileBM\ZKLib\Test;

require __DIR__.'/../autoload.php';

use \AgileBM\ZKLib;

$ip = '10.0.0.14';
$port = 4370;
$timeout = 3;
$retry = 4;

$rslt = false;
$strLastError = '';

exec("ping {$ip}", $arrOutput, $intStatus);
if ($intStatus !== 0) {
    return false ;
}

$objZK = new \AgileBM\ZKLib\ZKLib($ip, $port, $timeout);

try {
    do {
        $rslt = $objZK->connect() ;
    } while($retry-- > 0 && !$rslt); 
} catch (Exception $ex) {
    $strLastError = $ex->getMessage();
    die($strLastError);
}

try {
    // disable device before getting data to maintain consistency 
    $objZK->disableDevice();
    // this line meant to re-sycn the device 
    //$this->_objZK->setTime(date('Y-m-d H:i:s'));
    $arrAttendance = $objZK->getAttendance();
    //   enable device after getting all data 
    $objZK->enableDevice();

    if (!empty($arrAttendance)) {
        print(PHP_EOL);
        foreach($arrAttendance as $item) {
            print("{$item['id']} :: {$item['timestamp']}" . PHP_EOL);
        }
    }
} catch(Exception $ex) {
    $strLastError = $ex->getMessage();
    print($strLastError);
}

$objZK->disconnect();