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

class NextBarcampIntent implements IntentsInterface
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
        list($date_from, $date_to) = $this->getNextBarCampDate();

        $date_today = time();
        $date_from_time = strtotime($date_from);

        $days_till_barcamp = round(($date_from_time - $date_today) / 86400, 0);

        $ssml = new SSML();
        $ssml->addSentence('Barcamp ist vom ' . $date_from . ' bis zum ' . $date_to)
            ->addText('Das sind nocht genau ')
            ->addNumber($days_till_barcamp)
            ->addText(' Tage.')
            ->addWhisper(' Also solltest du dir schnell eine Karte organisieren.');

        $outSpeech = new Outspeech();
        $outSpeech->setType($outSpeech::TYPE_SSML)
            ->setSsml($ssml);

        $response = new Response($outSpeech);
        $responseBody = new ResponseBody($response);
        new ResponseHandler($responseBody);
    }

    protected function getNextBarCampDate()
    {
        $config = Yaml::parseFile(__DIR__ . '/../../source/barcamp_config.yml');

        return [
            $config['BarCamp']['Datum']['von'],
            $config['BarCamp']['Datum']['bis'],
        ];
    }
}