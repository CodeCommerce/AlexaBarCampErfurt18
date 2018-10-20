<?php

require __DIR__ . '/../vendor/autoload.php';

$bTest = false;

if ($bTest) {
    $json = json_decode(file_get_contents(__DIR__ . "/RequestTests/NextSessionRequest.json"));
} else {
    $json = json_decode(file_get_contents('php://input'));
    $logger = new Monolog\Logger('requestlog');
    $f = fopen("../log/request_" . date("His_dmY") . ".log", "a+");
    $logger->pushHandler(new StreamHandler($f, $logger::WARNING));
    $logger->warning(file_get_contents('php://input'));
}

$alexaApi = new CodeCommerce\AlexaApi\Controller\RequestHandler($json, $logger);