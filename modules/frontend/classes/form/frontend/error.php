<?php defined('SYSPATH') or die('No direct script access.');

class Form_Frontend_Error extends Form {

    /**
     * Message
     * @var string
     */
    protected $_message;

    /**
     * Label for 'cancel' button
     * @var string
     */
    protected $_cancel = 'Назад';

    /**
     * Url for cancel button
     * @var string
     */
    protected $_cancel_url;

    /**
     * Create a form with error message and 'back' button
     *
     * @param string $message
     */
    public function  __construct($message = NULL, $cancel = 'Назад', $cancel_url = NULL)
    {
        $this->_message    = $message;
        $this->_cancel     = $cancel;
        if ($cancel_url !== NULL)
        {
            $this->_cancel_url = $cancel_url;
        }
        else
        {
            $this->_cancel_url = URL::back();
        }

        parent::__construct();
    }

    /**
     * Initialize form fields
     */
    public function init()
    {
        // Message
        $element = new Form_Element_Text('text', array('class' => 'error_message'));
        $element->value = $this->_message;
        $this->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            // ----- Cancel button
            $fieldset
                ->add_component(new Form_Element_LinkButton('cancel',
                    array('url' => URL::back(), 'label' => $this->_cancel),
                    array('class' => 'button_cancel')
                ));
    }
}
