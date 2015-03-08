<?php
namespace Dashboard\Controller;


use Dashboard\Form\PrizeForm;
use Sms\Model\Prize;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{

    private $phoneNumberTable;

    private $prizeTable;

    public function indexAction()
    {
        return new ViewModel(array('numbers' => $this->getPhoneNumberTable()->fetchAll()));
    }

    public function prizesAction()
    {
        return new ViewModel(array('prizes' => $this->getPrizeTable()->fetchAll()));
    }

    public function addPrizeAction()
    {
        $form = new PrizeForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $prize = new Prize();
            $form->setInputFilter($prize->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $prize->exchangeArray($form->getData());

                $this->getPrizeTable()->savePrize($prize);
            }

            $this->redirect()->toUrl('/dashboard/prizes');
        }

        return array('form' => $form);
    }

    public function getPhoneNumberTable()
    {
        if (!$this->phoneNumberTable) {
            $sm = $this->getServiceLocator();
            $this->phoneNumberTable = $sm->get('Sms\Model\PhoneNumberTable');
        }

        return $this->phoneNumberTable;
    }

    public function getPrizeTable()
    {
        if (!$this->prizeTable) {
            $sm = $this->getServiceLocator();
            $this->prizeTable = $sm->get('Sms\Model\PrizeTable');
        }

        return $this->prizeTable;
    }
}