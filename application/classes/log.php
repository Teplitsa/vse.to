<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract logger
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Log
{
    // log levels
    const INFO    = 1;
    const WARNING = 2;
    const ERROR   = 3;
    const FATAL   = 4;

    /**
     * If message count exceeds this limit - flush them via write()
     * @var integer
     */
    public $flush_limit = 1024;

    /**
     * Messages
     * @var array
     */
    protected $_messages = array();

    /**
     * Add message to log
     *
     * @param integer $level
     * @param string $msg
     */
    public function message($message, $level = self::INFO)
    {
        $this->_messages[] = array('message' => $message, 'level' => $level);

        if (count($this->_messages) > $this->flush_limit)
        {
            $this->flush();
        }
    }

    /**
     * Flush current messages to log
     */
    public function flush()
    {
        if ( ! empty($this->_messages))
        {
            $this->_write();
            $this->_messages = array();
        }
    }

    /**
     * Write pending messages to log
     */
    abstract protected function _write();

    /**
     * Read log messages
     */
    abstract public function read();

    /**
     * Flush log on destruction
     */
    public function  __destruct()
    {
        $this->flush();
    }

}