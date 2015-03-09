<?php
namespace Sms\Controller;


use AP_XmlStrategy\View\Model\XmlModel;
use Sms\Model\PhoneNumber;
use Zend\Mvc\Controller\AbstractActionController;
use ZendTest\Form\TestAsset\Entity\Phone;

class SmsController extends AbstractActionController
{

    private $phoneNumberTable;

    public function indexAction()
    {
        $number = $this->getREquest()->getQuery('From');
        $body = $this->getREquest()->getQuery('Body');
        $view = null;

        switch($body) {
            case 'register':
                $view = $this->register($number);
                break;
            default:
                $xmlModel = new XmlModel();
                $xmlModel->setRootNode('Response');
                $xmlModel->setVariables(array('Message' => 'Text "register" to register for the prize give away'));
                break;
        }

        return $view;
    }

    private function register($number)
    {
        $xmlModel = new XmlModel();
        $xmlModel->setRootNode('Response');
        if ($this->isRegistered($number)) {
            $xmlModel->setVariables(array('Message' => 'You are already registered!'));
            return $xmlModel;
        }

        $phoneNumber = new PhoneNumber();
        $phoneNumber->number = $number;
        $phoneNumber->available = 1;

        $this->getPhoneNumberTable()->saveNumber($phoneNumber);

        $xmlModel->setVariables(array('Message' => 'You are now registered!'));

        return $xmlModel;
    }

    private function isRegistered($number)
    {
        try{
            $this->getPhoneNumberTable()->lookUpByNumber($number);
            return true;
        } catch(\Exception $e) {
        }

        return false;
    }

    public function getPhoneNumberTable()
    {
        if (!$this->phoneNumberTable) {
            $sm = $this->getServiceLocator();
            $this->phoneNumberTable = $sm->get('Sms\Model\PhoneNumberTable');
        }

        return $this->phoneNumberTable;
    }
}