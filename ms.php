<?php
/**
 * Created by PhpStorm.
 * User: sxg
 * Date: 2022/8/1
 * Time: 14:24
 */

require_once "vendor/autoload.php";

$server = new Socket\Ms\Server("tcp://127.0.0.1:9501");

$server->listen();
$server->accept();