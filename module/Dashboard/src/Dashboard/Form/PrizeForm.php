<?php
namespace Dashboard\Form;


use Zend\Form\Form;

class PrizeForm extends Form
{
    public function __construct($name = null)
    {

        parent::__construct('prize');

        $this->add(array(
            'name' => 'id',
            'type' => 'Hidden'
        ));

        $this->add(array(
            'name' => 'name',
            'type' => 'Text',
            'options' => array(
                'label' => 'Prize'
            )
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
            'attributes' => array(
                'value' => 'Go',
                'id' => 'submitbutton',
            ),
        ));
    }
}