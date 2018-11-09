<?php

namespace BarCamp\Alexa\Intents;

use CodeCommerce\AlexaApi\Controller\ResponseHandler;
use CodeCommerce\AlexaApi\Intents\IntentsInterface;
use CodeCommerce\AlexaApi\Model\BackgroundImage;
use CodeCommerce\AlexaApi\Model\Directives;
use CodeCommerce\AlexaApi\Model\Outspeech;
use CodeCommerce\AlexaApi\Model\Request;
use CodeCommerce\AlexaApi\Model\Response;
use CodeCommerce\AlexaApi\Model\ResponseBody;
use CodeCommerce\AlexaApi\Model\SSML;
use CodeCommerce\AlexaApi\Model\System;
use CodeCommerce\AlexaApi\Model\Template;
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
            $ssml->addSentence($sessionDetail['title'] . " von " . $sessionDetail['author']);
            $i++;
            if ($i > 2) {
                break;
            } else {
                $ssml->addSentence($this->getRandomAddingSentence());
            }
        }

        $ssml->addSentence('Wir hoffen du hast eine spannende Session für dich gefunden.')
            ->addSentence('Du kannst jederzeit eine Session einreichen - wir freuen uns darauf.')
            ->addSentence('Weitere Informationen findest du unter www.barcamp-erfurt.de');

        $outSpeech = new Outspeech();
        $outSpeech->setType($outSpeech::TYPE_SSML)
            ->setSsml($ssml);

        $response = new Response($outSpeech);

        if ($viewPort = $this->system->getViewport()) {
            $template = new Template(Template::BODY_TEMPLATE_1_SIMPLE_TEXT_IMAGES);
            $template->setTitle('Hallo')
                ->setBackButton($template::BACK_BUTTON_VISIBLE)
                ->setPrimary('Text 1');
            if ($viewPort->isDevice($viewPort::DEVICE_TYPE_SPOT)) {
                $template->addBackgroundImage(
                    'https://www.codecommerce.de/wp-content/uploads/2018/04/18403105_1372829509477095_2872277146168686090_n-316x316.jpg',
                    'test'
                );
            }
            if ($viewPort->isDevice($viewPort::DEVICE_TYPE_SHOW1) || $viewPort->isDevice($viewPort::DEVICE_TYPE_SHOW2)) {
                $template->addBackgroundImage(
                    'https://c1.staticflickr.com/2/1654/24166023156_0ea6459e0f_b.jpg',
                    'test'
                );
            }
            $directives = new Directives($template);
            $response->setDirectives($directives);
        }

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
