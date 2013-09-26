<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Confirm extends Form {

    /**
     * Message
     * @var string
     */
    protected $_message;

    /**
     * Label for 'submit' button
     * @var string
     */
    protected $_yes = 'Да';

    /**
     * Label for 'cancel' button
     * @var string
     */
    protected $_no = 'Нет';

    /**
     * Creates "Yes/No" confirmation form with specified message
     *
     * @param string $message
     */
    public function  __construct($message, $yes = 'Да', $no = 'Нет')
    {
        $this->_message = $message;
        $this->_yes = $yes;
        $this->_no = $no;
        parent::__construct();
    }

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Message
        $element = new Form_Element_Text('text', array('class' => 'confirm_message'));
        $element->value = $this->_message;
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => $this->_no),
                    array('class' => 'button_cancel')
                ));

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => $this->_yes),
                    array('class' => 'button_accept')
                ));
    }
}
