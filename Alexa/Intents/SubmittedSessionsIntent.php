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

class SubmittedSessionsIntent implements IntentsInterface
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
        $sessions = $config['SubmittedSessions'];

        $ssml = new SSML();
        $ssml->addSentence('Hier hast du eine kleine Auswahl von Sessions die bisher eingereicht wurden.');
        shuffle($sessions);

        $i = 1;
        foreach ($sessions as $key => $sessionDetail) {
            $ssml->addSentence($sessionDetail['title'] . " von " . $sessionDetail['author'])
                ->addSentence($this->getRandomAddingSentence());
            $i++;
            if ($i > 5) {
                break;
            }
        }

        $ssml->addSentence('Wir hoffen du hast eine spannende Session für dich gefunden.')
            ->addSentence('Du kannst jederzeit eine Session einreichen - wir freuen uns darauf.')
            ->addSentence('Weitere Informationen findest du unter www.barcamp-erfurt.de');

        $outSpeech = new Outspeech();
        $outSpeech->setType($outSpeech::TYPE_SSML)
            ->setSsml($ssml);

        $response = new Response($outSpeech);
        $responseBody = new ResponseBody($response);
        new ResponseHandler($responseBody);
    }

    protected function getRandomAddingSentence()
    {
        $randomSentence = [
            'Eine weitere Session.',
            'Weiter gibt es eine Session mit dem Titel.',
            'Unter anderem eine Session mit dem Titel.',
        ];

        shuffle($randomSentence);

        return $randomSentence[0];
    }
}
