<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$host = "localhost";
$user = "username";
$pass = "password";
$database = "plasmakashmir";

class DB
{
    private static $connection = NULL;

    private function __construct()
    {
    }
    private function __clone()
    {
    }

    public static function getConnection()
    {
        $host = "localhost";
        $user = "username";
        $pass = "password";
        $database = "plasmakashmir";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

        if (!isset(self::$connection))
        {
            self::$connection = new PDO("mysql:host=" . $host . ";dbname=$database", $user, $pass, $options);
        }
        return self::$connection;
    }

}

function send_text($number, $text) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://www.fast2sms.com/dev/bulk?authorization=U5HtjgypMn9YQOlu7Ai8eqL0ZNSKGRFPrwxEBzvJa34IVkXhf6eNx1REobLmZnfsT8OyAlShtqXBgU0M&sender_id=FSTSMS&message=" .
    urlencode($text)."&language=english&route=p&numbers=".
    urlencode($number),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache"
    ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        return "cURL Error #:" . $err;
    } else {
        return "sent";
    }
}

?>

