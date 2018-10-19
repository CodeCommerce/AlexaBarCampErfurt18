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

class NextSessionIntent implements IntentsInterface
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
        $config = Yaml::parseFile(__DIR__ . '/../../source/sessionplan.yml');
        $cur_time = date('H:i');

        if ($sessions = $config['Sessions']) {
            $sessionSlotTime = $this->getNextTimeSlotSessions($sessions, $cur_time);
            $this->renderResponse($sessionSlotTime, $sessions[$sessionSlotTime]);
        }
    }

    protected function renderResponse($time, $sessionSlot)
    {
        $outSpeech = new Outspeech();

        $ssml = new SSML();
        $ssml->addText('Folgende Sessions finden um ')
            ->addTime($time)
            ->addText(' statt.');


        foreach ($sessionSlot as $session => $room) {
            $ssml->addSentence('Im Raum ' . $room . ' findet die Session ' . $session . ' statt.');
        }
        $outSpeech->setType($outSpeech::TYPE_SSML)
            ->setSsml($ssml);

        $response = new Response();
        $response->setOutputSpeech($outSpeech);

        $responseBody = new ResponseBody();
        $responseBody->setResponse($response);
        $rh = new ResponseHandler($responseBody);
    }

    /**
     * @param $sessions
     * @param $cur_time
     * @return int|null|string
     */
    protected function getNextTimeSlotSessions($sessions, $cur_time)
    {
        $nextTimeSlot = null;
        foreach ($sessions as $time => $sessionInfo) {
            if ($time > $cur_time && ($time < $nextTimeSlot || null === $nextTimeSlot)) {
                $nextTimeSlot = $time;
            }
        }

        return $nextTimeSlot;
    }
}
