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
 * @package    Seven
 * @author     Sergey Kolodyazhnyy <sergey.kolodyazhnyy@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 *
 */

    Header("HTTP/1.0 500 Internal Server Error");

    /**
    *   @var $e Exception
    */

   $error_code = strtoupper(dechex(time()) . "-" . dechex(rand(0x1000, 0xFFFF)));

   // generate report
   function generate_report($e) {
       ob_start();
       require_once "errors/developement/500.php";
       $report = ob_get_contents();
       ob_end_clean();
       return $report;
   }

   try {
       $path = BP . DS . "var" . DS . "reports";
       if(!is_dir($path)) mkdir($path, 0777, true);
       file_put_contents($path . DS . $error_code . ".html", $report = generate_report($e));
       // try to send e-mail report
       if(Seven::getSiteConfig("webmaster/general/send_exception") && ($email = Seven::getSiteConfig("webmaster/general/email")))
           mail($email, "Error report #" . $error_code, $report,  "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n");
   } catch(Exception $ex) {
   }


?>
<html>
    <head>
        <title>Error 500: Internal Server Error</title>
        <style>
            body { font-family: sans-serif; font-size: 14px; margin: 20px; text-align: center; color: #333; background: #eee; }
            h1 { font-size: 30px; font-weight: normal; color: #b00; border-bottom: 1px solid #999; padding-bottom: 10px; }
            h2 { font-size: 18px; font-weight: normal; color: #999; }
            h1 .light { display: inline; color: #aaa; }
            pre { background: #e0e0e0; padding: 0px 10px; }
            span.error-report-code { padding: 10px; font-size: 20px; color: #800; display: block; }
            div.page-holder { border-radius: 10px; box-shadow: 0 0 20px #999; width: 700px; text-align: left; margin: 30px auto; border: 2px solid #aaa; background: white; padding: 0 20px 40px; }
            p { text-align: justify; }
        </style>
    </head>
    <body>
        <div class="page-holder">
            <h1>The error  has occurred<span class="light">, call the Administrator!</span></h1>
            <div class="content">
                <p>No really, server encounted an internal error or misconfiguration and was unable to complete your request. Please, contact the server administrator and inform them of the time the error occurred, and anything you have made done that the may have caused  the error.</p>
                <p>Error report number:<span class="error-report-code"><?php echo $error_code ?></span></p>
                <p>We will fix it as soon as possible.</p>
                <p class="sign">Thankyou in advanse</p>
            </div>
        </div>
    </body>
</html>
