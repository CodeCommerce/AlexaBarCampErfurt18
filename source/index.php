<?php

require_once '../vendor/autoload.php';

$alexaApi = new CodeCommerce\AlexaApi\Controller\ResponseHandler(json_decode(file_get_contents('php://input')));