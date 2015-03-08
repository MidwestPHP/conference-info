<?php
namespace Sms\Model;

class Prize
{
    public $id;
    public $name;
    public $available;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->name = (!empty($data['name'])) ? $data['name'] : null;
        $this->available = (!empty($data['available'])) ? $data['available'] : null;
    }
}