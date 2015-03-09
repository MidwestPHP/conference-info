<?php
namespace Sms\Model;

class PhoneNumber
{
    public $id;
    public $number;
    public $prizeId;
    public $available;

    public $minId;
    public $maxId;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->number = (!empty($data['number'])) ? $data['number'] : null;
        $this->prizeId = (!empty($data['prizeId'])) ? $data['prizeId'] : null;
        $this->available = (!empty($data['available'])) ? $data['available'] : null;

        $this->minId = (!empty($data['minId'])) ? $data['minId'] : null;
        $this->maxId = (!empty($data['maxId'])) ? $data['maxId'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}