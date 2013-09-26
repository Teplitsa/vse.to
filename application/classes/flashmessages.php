<?php

class FlashMessages
{
    const MESSAGE = 1;
    const ERROR   = 2;

    /**
     * Add new message
     *
     * @param string $text
     * @param integer $type
     * @param string $category
     */
    public static function add($text, $type = self::MESSAGE, $category = 'default')
    {
        $messages = Session::instance()->get('flash_messages', array());
        $messages[] = array(
            'text'     => $text,
            'type'     => $type,
            'category' => $category
        );
        Session::instance()->set('flash_messages', $messages);
    }

    /**
     * Add several messages at once
     * 
     * @param array $messages
     * @param string $category
     */
    public static function add_many(array $messages, $category = 'default')
    {
        $msgs = Session::instance()->get('flash_messages', array());
        
        foreach ($messages as $message)
        {
            if (is_array($message))
            {
                $text = $message['text'];
                $type = $message['type'];
            }
            else
            {
                $text = (string) $message;
                $type = self::MESSAGE;
            }

            $msgs[] = array(
                'text'     => $text,
                'type'     => $type,
                'category' => $category
            );
        }

        Session::instance()->set('flash_messages', $msgs);
    }

    /**
     * Return all messages for specified category and delete them from session
     *
     * @param  string $category
     * @return array
     */
    public static function fetch_all($category = 'default')
    {
        $result = array();

        $messages = Session::instance()->get('flash_messages', array());

        // Fetch only messages with specified category
        foreach ($messages as $k => $message)
        {
            if ($message['category'] == $category)
            {
                $result[] = $message;
                unset($messages[$k]);
            }
        }
        
        Session::instance()->set('flash_messages', $messages);
        return $result;
    }
}