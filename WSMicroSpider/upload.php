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

$upload_dir = '/var/www/htdocs/import/loggerpostdatahandler/WSMicroSpider/raw/';

$logger_id = 'UNKNOWN';
foreach($_FILES as $file) {
        var_dump($file);

        // Record the serial number
        if (!empty($_POST["SERIALNUMBER"])) {
                $logger_id = $_POST["SERIALNUMBER"];
        }
        $default_target_file = $upload_dir . basename($file["name"]);
        $target_file = $upload_dir . $logger_id . '_' . $timestamp . '.txt';

        // Save the file
        move_uploaded_file($file["tmp_name"], $target_file);

        $last_line = `tail -n 1 $target_file`;
}

// A wrapper shell script transfers as apache user doesn NOT HAVE private SSH keys !
// awoerlee@prod-api01-public:/var/www/scripts$ ./WSMicroSpiderLoadingZoneTransfer.sh

#$cmd = "/usr/bin/scp $target_file 34.194.103.24:/data/loadingzone/import/ImportWSMicrosSpider/";
#$cmd = "/usr/bin/scp $target_file 52.63.51.77:/data/loadingzone/import/ImportWSMicroSpider/";
#system($cmd);


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
