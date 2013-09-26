<?php defined('SYSPATH') or die('No direct script access.');

class Form_Backend_Site extends Form_Backend
{

    /**
     * Initialize form fields
     */
    public function init()
    {
        // HTML class
        $this->attribute('class', 'w500px');

        // ----- General tab
        $tab = new Form_Fieldset_Tab('general_tab', array('label' => 'Основные свойства'));
        $this->add_component($tab);

            if (Kohana::config('sites.multi'))
            {
                // ----- Domain
                $element = new Form_Element_Input('url',
                    array('label' => 'Адрес', 'required' => TRUE),
                    array('maxlength' => 255)
                );
                $element
                    ->add_filter(new Form_Filter_TrimCrop(255))
                    ->add_validator(new Form_Validator_NotEmptyString());

                $tab->add_component($element);
            }

            // ----- Caption
            $element = new Form_Element_Input('caption',
                array('label' => 'Название проекта', 'required' => TRUE),
                array('maxlength' => 255)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(255))
                ->add_validator(new Form_Validator_NotEmptyString());
            $tab->add_component($element);

        // ----- email tab
        $tab = new Form_Fieldset_Tab('email_tab', array('label' => 'E-Mail оповещения'));
        $this->add_component($tab);

            // ----- to
            $element = new Form_Element_Input('settings[email][to]',
                array(
                    'label' => 'E-Mail администратора',
                    'comment' => 'На этот адрес по умолчанию будут отправляться все административные e-mail оповещения с сайта'
                ),
                array('maxlength' => 63)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $tab->add_component($element);

            // ----- from
            $element = new Form_Element_Input('settings[email][from]',
                array(
                    'label' => 'Адрес отправителя', 'required' => TRUE,
                    'comment' => 'Этот e-mail адрес будет стоять в поле "От кого" у оповещений, приходящих с сайта клиентам и администратору'
                ),
                array('maxlength' => 63)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(63))
                ->add_validator(new Form_Validator_NotEmptyString());
            $tab->add_component($element);

            // ----- sender
            $element = new Form_Element_Input('settings[email][sender]',
                array(
                    'label' => 'Имя отправителя',
                    'comment' => 'Имя отправителя для оповещений, приходящих с сайта клиентам и администратору'
                ),
                array('maxlength' => 63)
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(63));
            $tab->add_component($element);

            // ----- textarea
            $element = new Form_Element_Textarea('settings[email][signature]',
                array(
                    'label' => 'Подпись письма для клиентов',
                    'comment' => 'Подпись, добавляемая к email оповещениям для клиентов'
                )
            );
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

        
        // ----- seo tab
        $tab = new Form_Fieldset_Tab('seo_tab', array('label' => 'SEO'));
        $this->add_component($tab);

            // ----- Title
            $element = new Form_Element_Textarea('settings[meta_title]', array('label' => 'Метатег title'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

            // ----- Description
            $element = new Form_Element_Textarea('settings[meta_description]', array('label' => 'Метатег description'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

            // ----- Keywords
            $element = new Form_Element_Textarea('settings[meta_keywords]', array('label' => 'Метатег keywords'), array('rows' => 3));
            $element
                ->add_filter(new Form_Filter_TrimCrop(511));
            $tab->add_component($element);

        // ----- Form buttons
        $fieldset = new Form_Fieldset_Buttons('buttons');
        $this->add_component($fieldset);

            if (Kohana::config('sites.multi'))
            {
                // ----- Cancel button
                $fieldset
                    ->add_component(new Form_Element_LinkButton('cancel',
                        array('url' => URL::back(), 'label' => 'Отменить'),
                        array('class' => 'button_cancel')
                    ));
            }

            // ----- Submit button
            $fieldset
                ->add_component(new Form_Backend_Element_Submit('submit',
                    array('label' => 'Сохранить'),
                    array('class' => 'button_accept')
                ));
    }
}
