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

    public function editPrizeAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'addPrize'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $prize = $this->getPrizeTable()->getPrize($id);
        }
        catch (\Exception $ex) {
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'prizes'
            ));
        }

        $form  = new PrizeForm();
        $form->bind($prize);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($prize->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getPrizeTable()->savePrize($prize);

                // Redirect to list of albums
                return $this->redirect()->toRoute('dashboard', array(
                    'action' => 'prizes'
                ));
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deletePrizeAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'prizes'
            ));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getPrizeTable()->deletePrize($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'prizes'
            ));
        }

        return array(
            'id'    => $id,
            'prize' => $this->getPrizeTable()->getPrize($id)
        );
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