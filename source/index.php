<?php
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;

$bTest = false;

$logger = new Monolog\Logger('requestlog');

if ($bTest) {
    $json = json_decode(file_get_contents(__DIR__ . "/RequestTests/NextBarcampRequest.json"));
} else {
    $json = json_decode(file_get_contents('php://input'));
    $f = fopen(__DIR__ . "/../log/request_" . date("His_dmY") . ".log", "a+");
    $logger->pushHandler(new StreamHandler($f, $logger::WARNING));
    $logger->warning(file_get_contents('php://input'));
}

$alexaApi = new CodeCommerce\AlexaApi\Controller\RequestHandler($json, $logger);