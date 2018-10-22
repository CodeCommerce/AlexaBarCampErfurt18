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

class LaunchRequest implements IntentsInterface
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
        $this->system= $system;
    }

    /**
     * @return mixed
     */
    public function runIntent()
    {
        $ssml = new SSML();
        $ssml->addSentence('Herzlich Willkommen zum Barcamp Erfurt Alexa Skill.')
            ->addSentence('Wir freuen uns auf deinen Besuch.')
            ->addSentence('Weitere Informationen findest du unter. www.barcamp-erfurt.de.')
            ->addSentence('Du kannst mich fragen wann oder wo das nächste Barcamp stattfindet, oder welche Sessions am Barcamp Tag stattfinden.');

        $outputSpeech = new Outspeech();
        $outputSpeech->setType($outputSpeech::TYPE_SSML)
            ->setSsml($ssml);

        $responseBody = new ResponseBody(new Response($outputSpeech));
        new ResponseHandler($responseBody);
    }
}
