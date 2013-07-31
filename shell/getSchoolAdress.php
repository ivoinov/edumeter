#!/usr/bin/env php
<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivoinov
 * Date: 7/31/13
 * Time: 10:29 PM
 * To change this template use File | Settings | File Templates.
 */
require_once "../app/seven.php";
$filename = BP . DS . 'fail_schools.csv';
if(file_exists($filename)) {
    $handle = fopen($filename,'r');
    if(!$handle) {
        die("Can't open file {$filename}");
    }
    while(!feof($handle)) {
        $line = fgetcsv($handle,1024,',');
        $schoolId = $line[0];
        $oldAddress = $line[4];
    }
} else {
    die("File does not exist");
}