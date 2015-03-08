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
        $number = '612-270-8838';
        $something = 'register';
        $view = null;

        switch($something) {
            case 'register':
                $view = $this->register($number);
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