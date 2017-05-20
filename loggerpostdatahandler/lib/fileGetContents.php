<?php
 /** $Id fileGetContents
   *
   * @auth : Adriaan Woerlee
   * @date : November 8 2010
   *
   * get the contents of a file as read data
   *
   **/

 /** @getHTTPContents
   *
   * @param string : the URL
   * @param mixed : authorisation credentials
   *        array('username' => username, 'password' => password)
   * @return mixed : read data
   *
   **/
 function getHTTPContents($url, $auth=null) {
     // if authorisation required set credentials in header`
     if ($auth == null) {
         $create = array('http' => array('method' => 'GET'));
     } else {
         $create = array('http' => array(
                 'method' => 'GET',
                 'header' =>  sprintf("Authorization: Basic %s \r\n",
                 base64_encode($auth['username'] . ':' . $auth['password'])) .
                 "Content-type: text/plain;charset=utf-8 \r\n"
             ));
     }
 
     $context = stream_context_create($create);
     $stream = file_get_contents($url, false, $context);
     return $stream;
 }
 ?>
