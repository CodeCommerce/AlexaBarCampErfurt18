<?php

namespace BarCamp\Alexa\Intents;

use CodeCommerce\AlexaApi\Controller\ResponseHandler;
use CodeCommerce\AlexaApi\Intents\IntentsInterface;
use CodeCommerce\AlexaApi\Model\Outspeech;
use CodeCommerce\AlexaApi\Model\Request;
use CodeCommerce\AlexaApi\Model\Response;
use CodeCommerce\AlexaApi\Model\ResponseBody;
use CodeCommerce\AlexaApi\Model\SSML;
use CodeCommerce\AlexaApi\Model\System;
use Symfony\Component\Yaml\Yaml;

class LocationIntent implements IntentsInterface
{
    protected $request;
    protected $system;

    /**
     * IntentsInterface constructor.
     * @param Request $request
     * @param System  $system
     */
    public function __construct(Request $request, System $system)
    {
        $this->request = $request;
        $this->system = $system;
    }

    /**
     * @return mixed
     */
    public function runIntent()
    {
        $config = Yaml::parseFile(__DIR__ . '/../../source/barcamp_config.yml');
        if ($config['BarCamp']['Lokation']) {
            $ssml = new SSML();
            $ssml->addText('Das Barcamp findet unter der folgenden Adresse statt. ')
                ->addTextAs($config['BarCamp']['Lokation'], $ssml::INTERPRET_AS_ADDRESS);
            $outputSpeech = new Outspeech();
            $outputSpeech->setType($outputSpeech::TYPE_SSML)
                ->setSsml($ssml);
        } else {
            $outputSpeech = new Outspeech('Aktuell haben wir noch keine Lokation festgelegt.');
        }

        $responseBody = new ResponseBody(new Response($outputSpeech));
        new ResponseHandler($responseBody);
    }
}
