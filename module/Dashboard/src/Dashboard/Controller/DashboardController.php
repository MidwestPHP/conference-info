<?php
namespace Dashboard\Controller;


use Dashboard\Form\PrizeForm;
use Sms\Model\PhoneNumber;
use Sms\Model\Prize;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DashboardController extends AbstractActionController
{

    private $phoneNumberTable;

    private $prizeTable;

    private $twilioService;

    public function indexAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            $this->redirect()->toRoute('zfcuser');
        }
        return new ViewModel(array('numbers' => $this->getPhoneNumberTable()->fetchAll()));
    }

    /*
     * START PRIZE ACTIONS
     */

    public function prizesAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            $this->redirect()->toRoute('zfcuser');
        }
        return new ViewModel(array('prizes' => $this->getPrizeTable()->fetchAll()));
    }

    public function addPrizeAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            $this->redirect()->toRoute('zfcuser');
        }
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
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            $this->redirect()->toRoute('zfcuser');
        }
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'addPrize'
            ));
        }

        // Get the Album with the specified id.  An exception is thrown
        // if it cannot be found, in which case go to the index page.
        try {
            $prize = $this->getPrizeTable()->getPrize($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'prizes'
            ));
        }

        $form = new PrizeForm();
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
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            $this->redirect()->toRoute('zfcuser');
        }
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'prizes'
            ));
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int)$request->getPost('id');
                $this->getPrizeTable()->deletePrize($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('dashboard', array(
                'action' => 'prizes'
            ));
        }

        return array(
            'id' => $id,
            'prize' => $this->getPrizeTable()->getPrize($id)
        );
    }

    /*
     * END PRIZE ACTIONS
     */

    /*
     * START triggering prizes
     */

    public function triggerAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {
            $this->redirect()->toRoute('zfcuser');
        }
        // Get the random number
        $numbers = $this->getPhoneNumberTable()->selectAvailableNumbers();

        $count = count($numbers);

        $randomNumber = mt_rand(0, $count - 1);

        foreach($numbers as $i=>$number) {
            if($i == $randomNumber) {
                $winner = $number;
                break;
            }
        }


        // Get the random prize
        $prizes = $this->getPrizeTable()->selectAvailablePrizes();

        $count = count($prizes);

        $randomNumber = mt_rand(0, $count - 1);

        foreach($prizes as $i=>$value) {
            if($i == $randomNumber) {
                $prize = $value;
                break;
            }
        }

        // Send the message that they won X prize
        $message = $this->getTwilioService()->sendSms($winner->number, "Congrats. You won {$prize->name}. Come to the registration desk to claim your prize.");

        // Log the number to not trigger it again
        $winner->available = 0;
        $this->getPhoneNumberTable()->saveNumber($winner);

        // log the price to not trigger it again
        $prize->available = 0;
        $this->getPrizeTable()->savePrize($prize);

        return new ViewModel(array('number' => $number, 'prize' => $prize));
    }

    /*
     * END triggering prizes
     */

    public function getTwilioService()
    {
        if (!$this->twilioService) {
            $sm = $this->getServiceLocator();
            $this->twilioService = $sm->get('Dashboard\Model\Twilio');
        }

        return $this->twilioService;
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