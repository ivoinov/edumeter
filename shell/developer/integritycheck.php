#!/usr/bin/php
<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Seven
 * @package    Libs
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @deprecated Since a lot of updates it need an upgrade
 */

    require_once "../../app/seven.php";

    if(!($integrity_check = Seven::getSingleton('developer/integritycheck'))) {
        echo "It seems developer package disbled\n";
        return 1;
    }

    function show_report($report, $level = 0) {
        $pad = str_repeat("     ", $level);
        echo $pad . "* [" . report_type($report->getState()) . "] " . strip_tags($report->getMessage()) . "\n";
        foreach($report->getSubreports() as $_report)
            show_report($_report, $level + 1);

        if($report->hasHowtoFix())
            echo $pad . "  [[FIX]] " . $report->getHowtoFix() . "\n";
    }

    function report_type($state) {
        switch($state) {
            case Developer_Model_Integritycheck_Report::STATE_OK:
                return "ok";
            case Developer_Model_Integritycheck_Report::STATE_ADVICE:
                return "advice";
            case Developer_Model_Integritycheck_Report::STATE_INFO:
                return "info";
            case Developer_Model_Integritycheck_Report::STATE_WARNING:
                return "WARNING";
            case Developer_Model_Integritycheck_Report::STATE_ERROR:
                return "ERROR";
        }
        return "Unknown";
    }

    show_report($integrity_check->check());


