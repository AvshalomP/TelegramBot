<?php
 
//phpinfo();

define('BOT_TOKEN', $_ENV["TOKEN"]);
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');
define('CH_STATUS', $_ENV["JSONURL"]);

ini_set("allow_url_fopen", 1);

// read channels status
$chContent  = file_get_contents(urlencode(CH_STATUS));
$chJson     = json_decode($chContent, true);

// read incoming info and grab the chatID
$content    = file_get_contents("php://input");
$update     = json_decode($content, true);
$chatID     = $update["message"]["chat"]["id"];
$message    = $update["message"]["text"];

// retrieving channel status
$status = $message;
foreach($chJson as $key => $val) {
    if ($key == $message)
    {
       $status = $val;
       break;
    }
}
// compose reply
$reply ="";
switch ($status) {
    case "0":
        $reply =  "Source problem";
        break;
    case "1":
        $reply =  "Active";
        break;
    default:
        $reply =  "No such channel";
}

// send reply
$sendto =API_URL."sendmessage?chat_id=".$chatID."&text=".$reply;
//file_get_contents(urlencode($sendto));
http_get_contents($sendto);

// Create a debug channels_bot.log to check the response/repy from Telegram in JSON format.
// You can disable it by commenting checkJSON.
checkJSON($chatID,$update);
function checkJSON($chatID,$update){

    $myFile = "channels_bot.log";
    $updateArray = print_r($update,TRUE);
    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $chatID ."nn");
    fwrite($fh, $updateArray."nn");
    fclose($fh);
}

checkChJson($chJson);
function checkChJson($chJson){

$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, CH_STATUS);
$result = curl_exec($ch);
curl_close($ch);

$obj = json_decode($result);
//echo $obj->access_token;
    $myFile = "channels_status.log";
    $updateArray = print_r($obj, TRUE);//($chJson,TRUE);
    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $updateArray."nn");
    fclose($fh);
}

function http_get_contents($url)
{
  /*$ch = curl_init();
  curl_setopt($ch, CURLOPT_TIMEOUT, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  if(FALSE === ($retval = curl_exec($ch))) {
      log2("error".$url);
    error_log(curl_error($ch));
  } else {
    return $retval;
  }*/
    if ( ! function_exists( 'curl_init' ) ) 
    {
        log2("error".$url);
        die( 'The cURL library is not installed.' );
    }
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    if (FALSE == $output = curl_exec( $ch ))
    {
        log2("error".$url);
    }
    curl_close( $ch );
    return $output;
}



function log2($text){

    $myFile = "channels_bot2.log";
    
    $fh = fopen($myFile, 'a') or die("can't open file");
    fwrite($fh, $text);
    fclose($fh);
}

?>
