<?php defined('SYSPATH') or die('No direct script access.');

class Model_Go extends Model_Res
{       
    /**
     * Back up properties before changing (for logging)
     */
    public $backup = TRUE;
        
    // -------------------------------------------------------------------------
    // Actions
    // -------------------------------------------------------------------------
    /**
     * @return boolean
     */
    public function default_active()
    {        
        return FALSE;
    }    
    /**
     * Get announce for this telemost
     *
     * @return Model_Product
     */
    public function get_telemost()
    {
        if ( ! isset($this->_properties['telemost']))
        {
            $telemost = new Model_Telemost();

            if ($this->telemost_id != 0)
            {
                $telemost->find($this->telemost_id);
            }

            $this->_properties['telemost'] = $telemost;
        }

        return $this->_properties['telemost'];
    }    
    
    /**
     * Save model and log changes
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {  
        parent::save($force_create);

        $this->notify();
        
        //$this->log_changes($this, $this->previous());
    }

    /**
     * Delete productcomment
     */
    public function delete()
    {
        //@FIXME: It's more correct to log AFTER actual deletion, but after deletion we have all model properties reset
        //$this->log_delete($this);

        parent::delete();
    }

    /**
     * Send e-mail notification about created order
     */
    public function notify()
    {
//        $notify_reviewer = 'notify_'.Model_Group::SHOWMAN_GROUP_ID;
//        $notify_editor = 'notify_'.Model_Group::EDITOR_GROUP_ID;
//
//        if ($this->$notify_reviewer == '1') {
//            $this->notify_reviewer();
//        } elseif ($this->$notify_editor == '1') {
//            $this->notify_editor();
//        }
    }
    
//    public function notify_reviewer()
//    {           
//        try {
//            $settings = Model_Site::current()->settings;
//
//            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
//            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
//            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';
//                        
//            $reviewer = $this->role;
//            $email_reviewer = $reviewer->email;
//
//            $editor = $this->product->role;
//            $email_editor = $editor->email;                
//
//            if ($email_sender != '')
//            {
//                $email_from = array($email_from => $email_sender);
//            }
//
//            if ($email_editor != '' && $email_reviewer != '')
//            {
//                // Init mailer
//                SwiftMailer::init();
//                $transport = Swift_MailTransport::newInstance();
//                $mailer = Swift_Mailer::newInstance($transport);
//
//                // --- Send message to editor
//                
//                $message = Swift_Message::newInstance()
//                    ->setSubject('Заявка на рецензию на портале ' . URL::base(FALSE, TRUE))
//                    ->setFrom($email_from)
//                    ->setTo($email_editor);
//
//                // Message body
//                $twig = Twig::instance();
//
//                $template = $twig->loadTemplate('mail/order_editor');
//
//                $message->setBody($template->render(array(
//                    'productcomment' => $this,
//                    'editor' => $editor,
//                    'reviewer' => $reviewer,
//                    'article' => $this->product
//                )));
//                // Send message
//                $mailer->send($message);
//                
//                
//                $message = Swift_Message::newInstance()
//                    ->setSubject('Заявка на рецензию на портале ' . URL::base(FALSE, TRUE))
//                    ->setFrom($email_from)
//                    ->setTo($email_reviewer);
//
//                // Message body
//                $twig = Twig::instance();
//
//                $template = $twig->loadTemplate('mail/order_reviewer');
//
//                $body = $template->render(array(
//                    'productcomment' => $this,
//                    'editor' => $editor,
//                    'reviewer' => $reviewer,
//                    'article' => $this->product
//                ));
//
//                if ($signature != '')
//                {
//                    $body .= "\n\n\n" . $signature;
//                }
//
//                $message->setBody($body);
//
//                // Send message
//                $mailer->send($message);
//                
//            }
//        }
//        catch (Exception $e)
//        {
//            if (Kohana::$environment !== Kohana::PRODUCTION)
//            {
//                throw $e;
//            }
//        }
//    }  
//    
//    public function notify_editor()
//    {           
//        try {
//            $settings = Model_Site::current()->settings;
//
//            $email_from   = isset($settings['email']['from'])      ? $settings['email']['from']      : '';
//            $email_sender = isset($settings['email']['sender'])    ? $settings['email']['sender']    : '';
//            $signature    = isset($settings['email']['signature']) ? $settings['email']['signature'] : '';
//                        
//            $reviewer = $this->role;
//            $email_reviewer = $reviewer->email;
//
//            $editor = $this->product->role;
//            $email_editor = $editor->email;                
//
//            if ($email_sender != '')
//            {
//                $email_from = array($email_from => $email_sender);
//            }
//
//            if ($email_editor != '' && $email_reviewer != '')
//            {
//                // Init mailer
//                SwiftMailer::init();
//                $transport = Swift_MailTransport::newInstance();
//                $mailer = Swift_Mailer::newInstance($transport);
//
//                // --- Send message to editor
//                
//                $message = Swift_Message::newInstance()
//                    ->setSubject('Завершенная рецензия на портале ' . URL::base(FALSE, TRUE))
//                    ->setFrom($email_from)
//                    ->setTo($email_editor);
//
//                // Message body
//                $twig = Twig::instance();
//
//                $template = $twig->loadTemplate('mail/ok_editor');
//
//                $message->setBody($template->render(array(
//                    'productcomment' => $this,
//                    'editor' => $editor,
//                    'reviewer' => $reviewer,
//                    'article' => $this->product
//                )));
//                // Send message
//                $mailer->send($message);
//                
//                
//                $message = Swift_Message::newInstance()
//                    ->setSubject('Завершенная рецензия на портале ' . URL::base(FALSE, TRUE))
//                    ->setFrom($email_from)
//                    ->setTo($email_reviewer);
//
//                // Message body
//                $twig = Twig::instance();
//
//                $template = $twig->loadTemplate('mail/ok_reviewer');
//
//                $body = $template->render(array(
//                    'productcomment' => $this,
//                    'editor' => $editor,
//                    'reviewer' => $reviewer,
//                    'article' => $this->product
//                ));
//
//                if ($signature != '')
//                {
//                    $body .= "\n\n\n" . $signature;
//                }
//
//                $message->setBody($body);
//
//                // Send message
//                $mailer->send($message);
//                
//            }
//        }
//        catch (Exception $e)
//        {
//            if (Kohana::$environment !== Kohana::PRODUCTION)
//            {
//                throw $e;
//            }
//        }
//    }      
//    /**
//     * Log telemost comment changes
//     *
//     */
//    public function log_changes(Model_Telemost $new_telemost, Model_Telemost $old_telemost)
//    {
//        $text = '';
//
//        $created = ! isset($old_telemost->id);
//
//        $has_changes = FALSE;
//
//        if ($created)
//        {
//            $text .= '<strong>Добавлена заявка на телемост для анонса " {{id-' . $new_telemost->product_id . "}}</strong>\n";
//        }
//        else
//        {
//            $text .= '<strong>Изменёна заявка на телемост для анонса {{id-' . $new_telemost->product_id . "}}</strong>\n";
//        }
//
//        // ----- info
//        if ($created)
//        {
//            $text .= Model_History::changes_text('Дополнительная информация', $new_telemost->info);
//        }
//        elseif ($old_telemost->info != $new_telemost->info)
//        {
//            $text .= Model_History::changes_text('Дополнительная информация', $new_telemost->info, $old_telemost->info);
//            $has_changes = TRUE;
//        }
//
//        if ($created || $has_changes)
//        {
//            // Save text in history
//            $history = new Model_History();
//            $history->text = $text;
//            $history->item_id = $new_telemost->product_id;
//            $history->item_type = 'product';
//            $history->save();
//        }
//    }
//
//    /**
//     * Log the deletion of telemost
//     *
//     * @param Model_Telemost $telemost
//     */
//    public function log_delete(Model_ProductComment $telemost)
//    {
//        $text = 'Удалёна заявка на телемост к анонсу {{id-' . $telemost->product_id . '}}';
//
//        // Save text in history
//        $history = new Model_History();
//        $history->text = $text;
//        $history->item_id = $telemost->product_id;
//        $history->item_type = 'product';
//        $history->save();
//    }
    
    public function get_place(array $params = NULL)
    {
        if ( ! isset($this->_properties['place']))
        {
            $place = new Model_Place();
            $place->find((int) $this->place_id, $params);
            $this->_properties['place'] = $place;
        }
        return $this->_properties['place'];
    }     

}