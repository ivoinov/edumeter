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

?>
<html>
    <head>
        <title>#<?php echo ($e->getCode() ? $e->getCode() : 500) ?>: <?php echo $e->getMessage() ?></title>
        <style>
            body { font-family: sans-serif; font-size: 13px; margin: 20px; }
            h1 { font-size: 25px; font-weight: normal; color: #b00; }
            h2 { font-size: 18px; font-weight: normal; color: #999; }
            h1 .light { display: inline; color: #aaa; }
            pre { background: #e0e0e0; padding: 0px 20px; }
            pre.toppadding { padding: 20px; }
        </style>
    </head>
    <body>
        <h1><span class="light">[<?php echo get_class($e) ?>]</span> <?php echo $e->getMessage() ?></h1>
        <p><b>File: </b><?php echo str_replace(BP . DS, "", $e->getFile()) . ": " . $e->getLine(); ?></p>
        <!-- exclude_hash_begin -->
        <p><b>Time: </b><?php echo gmdate("m/d/Y h:m:s A"); ?></p>
        <!-- exclude_hash_end -->
        <?php if($e instanceof Seven_Db_Exception) : ?>
        <h2>SQL Query</h2>
        <pre class="toppadding"><?php echo $e->getSqlQuery(); ?></pre>
        <?php endif; ?>
        <?php if($e instanceof  Core_Exception_Layout) : ?>
        <h2>Layout tags</h2>
        <pre class="toppadding"><?php echo implode(', ', Seven::getSingleton('core/layout')->getTags()); ?></pre>
        <?php endif; ?>
        <h2>Stack trace</h2>
        <pre class="toppadding"><?php echo str_replace(BP . DS, "", $e->getTraceAsString()); ?></pre>
        <h2>Query string (GET)</h2>
        <pre><?php echo str_replace("&lt;?php&nbsp;", "", highlight_string("<?php " . var_export($_GET, true), true)); ?></pre>
        <h2>Request Data (POST)</h2>
        <pre><?php echo str_replace("&lt;?php&nbsp;", "", highlight_string("<?php " . var_export($_POST, true), true)); ?></pre>
        <h2>Request File Data (FILES)</h2>
        <pre><?php echo str_replace("&lt;?php&nbsp;", "", highlight_string("<?php " . var_export($_FILES, true), true)); ?></pre>
        <?php 
        	try { 
        		$request_string = "<h2>Request Data</h2><pre>" . str_replace("&lt;?php&nbsp;", "", highlight_string("<?php " . var_export(Seven::app()->getRequest()->getData(), true), true)) . "</pre>";
        		echo $request_string;
         	} catch(Exception $e) {}; 
        ?>
    </body>
</html>
