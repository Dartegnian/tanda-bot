<?php

namespace App\Conversations;

use Illuminate\Foundation\Inspiring;
use Mpociot\BotMan\Answer;
use Mpociot\BotMan\Button;
use Mpociot\BotMan\Conversation;
use Mpociot\BotMan\Question;
use App\Utilities\TandaApi;

class SettingsConversation extends Conversation
{
    private $api;

    /**
     * Makes Menu
     *
     * @return void
     */
    public function getToken()
    {
        $this->ask('Hi! I\'m Tanya, your cool and awesome tanda assistant. to start, please enter your token: ', function (Answer $answer) {
            $token = $answer->getText();
            $this->askQuestions($token);
        });
    }

    /**
     * Asks questions
     *
     * @return void
     */
    public function askQuestions($token)
    {

        $question = Question::create('What do you want to do?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Tell a joke')->value('joke'),
                Button::create('Inspiring Quote')->value('quote'),
                Button::create('Time in')->value('timein'),
                Button::create('Time out')->value('timeout'),
                Button::create('Bye')->value('Yoko na'),
            ]);

        $this->ask($question, function (Answer $answer) use ($token) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'joke') {
                    $joke = json_decode(file_get_contents('http://api.icndb.com/jokes/random'));
                    $this->say($joke->value->joke);
                } else if ($answer->getValue() === 'timein') {
                    $api = new TandaApi($token);
                    $api->clockIn();
                    $this->say("Gotcha!");
                } else if ($answer->getValue() === 'timeout') {
                    $api = new TandaApi($token);
                    $api->clockOut();
                    $this->say("Take Care!");
                } else if ($answer->getValue() === 'quote') {
                    $this->say(Inspiring::quote());
                } else {
                    $this->say('Bye!');
                }
            }
        });
    }


    public function run()
    {
        $this->getToken();
    }
}