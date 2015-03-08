<?php
namespace Sms\Model;

class PhoneNumber
{
    public $id;
    public $number;
    public $available;
    public $prizeId;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->number = (!empty($data['number'])) ? $data['number'] : null;
        $this->available = (!empty($data['available'])) ? $data['available'] : null;
        $this->prizeId = (!empty($data['prizeId'])) ? $data['prizeId'] : null;
    }
}