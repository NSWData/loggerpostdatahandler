<?php

/*************************************************************
 *
 * Receive incoming Halytech MicroSpider data (v1.23)
 * HTTP Post
 *
 * Data arrives via 2 POSTS
 *              1) identify logger as POST['SERIALNUMBER']
 *				2) data file 	
 * 
 *************************************************************/
error_reporting(0);
//require_once "../../../../lib/LogReport.class.php";
$timestamp = date('YmdHis');


/**************************************************************
 * Parse the incoming data
**************************************************************/

$upload_dir = '/var/www/htdocs/import/loggerpostdatahandler/RainMicroSpider/raw/';
$loading_zone = '/data/loadingzone/import/ImportRainMicroSpider/';


$logger_id = 'UNKNOWN';
print $logger_id;
foreach($_FILES as $file) {
        var_dump($file);

        // Record the serial number
        if (!empty($_POST["SERIALNUMBER"])) {
                $logger_id = $_POST["SERIALNUMBER"];
        }
        $default_target_file = $upload_dir . basename($file["name"]);
        $target_file = $upload_dir . $logger_id . '_' . $timestamp . '.txt';
        $import_file = $loading_zone . $logger_id . '_' . $timestamp . '.txt';

        // Save the file
        move_uploaded_file($file["tmp_name"], $target_file);

        $last_line = system(`tail -n 1 $target_file`);
		print $last_line;
}

$cmd = "/usr/bin/scp $target_file 52.63.51.77:/data/loadingzone/import/ImportRainMicroSpider/";
system($cmd);
//copy($target_file, $import_file);

// We need to respond to the Micro Spider logger with an 'OK' to tell it to close the connection
header('HTTP/1.1 200 OK', true, 200);
header('Content-Length: 102400');
header('Content-Type: application/json');
echo('OK');

$rpt = 'http.spider.post';
$rptSum = $logger_id . ' Halytech HTTP Post';
$rptMsg = "New Logger post from $logger_id @ $timestamp \n\nLast Recordings : \n" . $last_line;
$rptMsg .= "\n\nSaving to : $target_file\n";
$rptStatus = 'OK';
//$record = new LogReport($rpt,$rptSum,$rptMsg,$rptStatus,null,$logger_id);
?>

