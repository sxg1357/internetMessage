<?php
/**
 * Created by PhpStorm.
 * User: sxg
 * Date: 2022/8/7
 * Time: 9:56
 */

require_once "vendor\autoload.php";

$client = new \Socket\Ms\Client("tcp://127.0.0.1:9501");

$client->on("connect", function (\Socket\Ms\Client $client) {

});

$client->on("error", function (\Socket\Ms\Client $client, $error_code, $error_message) {
    fprintf(STDOUT, "error_codee:%s,error_message:%s\n", $error_code, $error_message);
});

$client->on("receive", function (\Socket\Ms\Client $client) {

});


$client->on("close", function (\Socket\Ms\Client $client) {

});


