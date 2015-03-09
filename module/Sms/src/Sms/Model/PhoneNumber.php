<?php
namespace Sms\Model;

class PhoneNumber
{
    public $id;
    public $number;
    public $available;

    public $minid;
    public $maxId;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->number = (!empty($data['number'])) ? $data['number'] : null;
        $this->available = (!empty($data['available'])) ? $data['available'] : 1;

        $this->minId = (!empty($data['minId'])) ? $data['minId'] : null;
        $this->maxId = (!empty($data['maxId'])) ? $data['maxId'] : null;
    }
}