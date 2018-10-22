<?php
require __DIR__ . '/../vendor/autoload.php';

use Monolog\Handler\StreamHandler;

define('TEST_MODE', false);

$logger = new Monolog\Logger('requestlog');

if (TEST_MODE) {
    $json = json_decode(file_get_contents(__DIR__ . "/RequestTests/LocationRequest.json"));
} else {
    $json = json_decode(file_get_contents('php://input'));
    $f = fopen(__DIR__ . "/../log/request_" . date("His_dmY") . ".log", "a+");
    $logger->pushHandler(new StreamHandler($f, $logger::WARNING));
    $logger->warning(file_get_contents('php://input'));
    $logger->warning(print_r($_SERVER, true));
}

$alexaApi = new CodeCommerce\AlexaApi\Controller\RequestHandler($json, $logger);