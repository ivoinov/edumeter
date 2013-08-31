#!/usr/bin/env php
<?php

require_once "../app/seven.php";
require_once "../lib/simple_html_dom.php";

$filename = BP . DS . 'school_list.csv';
$websiteUrl = "isuo.org/schools/view/id/";
$regionPrefix = array("cr", "vn", "vl", "dp", "dn", "zt", "zk", "zp", "if", "kv", "ko", "kr", "lg", "lv", "mk", "od", "pl", "rv", "sv", "su", "te", "kh", "ks", "km", "ck", "cv", "cg");
if(file_exists($filename)) {
    $handle = fopen($filename,'w+');
    if(!$handle) {
        die("Can't open file {$filename}");
    }
    for($i = 0; $i <= 500000; $i++) {
        $ch = curl_init($websiteUrl . $i);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $responseHeaders = curl_getinfo($ch);
        if($responseHeaders['http_code'] == 404) {
            continue;
        } else {
            $csvLine = array();
            $html = file_get_html($responseHeaders['redirect_url']);
            $tableElement = $html->find('table.zebra-stripe');
            $csvLine['fullName'] = $tableElement[0]->children(1)->children(1)->plaintext;
            $csvLine['shortName'] = $tableElement[0]->children(2)->children(1)->plaintext;
            $csvLine['$index'] = $tableElement[0]->children(6)->children(1)->plaintext;
            $csvLine['address'] = $tableElement[0]->children(7)->children(1)->plaintext;
            $csvLine['description'] = _getDescription($tableElement[0]);
            fputcsv($handle, $csvLine, ';', '"');
        }
    }
} else {
    die("File does not exist");
}

function _getDescription($tBody)
{
    $description = "";
    if(trim($tBody->children(8)->children(1)->plaintext)) {
        $description .= "Директор:" . trim($tBody->children(8)->children(1)->plaintext) . "\n";
    }
    for($i = 11; $i < 20; $i++) {
        if(trim($tBody->children($i)->children(1)->plaintext)) {
            $description .= trim($tBody->children($i)->children(0)->plaintext) . trim($tBody->children($i)->children(1)->plaintext) . "\n";
        }
    }
    return $description;
}