<?php

namespace Dashboard\Model;

class Twilio
{
    public $number;

    private $serviceTwilio;

    public function __construct(\Services_Twilio $services_Twilio)
    {
        $this->serviceTwilio = $services_Twilio;
    }

    public function sendSms($number, $message)
    {
        try {
            return $this->serviceTwilio->account->messages->create(array(
                'From'=> $this->number,
                'To' => $number,
                'Body' => $message
            ));
        } catch(\Services_Twilio_RestException $e) {
            return $e->getMessage();
        }
    }

    public function sendVoice()
    {

    }


}