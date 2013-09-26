<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Simple file logger
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Log_File extends Log
{

    const MODE_READ   = 'r';
    const MODE_WRITE  = 'w';
    const MODE_APPEND = 'a';

    /**
     * File path
     * @var string
     */
    protected $_path;

    /**
     * File hanlde
     * @var resource
     */
    protected $_h;

    /**
     * Mode in which the log is opened(write, write and append, read)
     * @var string
     */
    protected $_mode;

    /**
     * Construct file logger
     * 
     * @param string $path
     */
    public function  __construct($path,  $mode = self::MODE_WRITE, $flush_limit = 1024)
    {
        $this->flush_limit = $flush_limit;
        $this->_path = $path;
        $this->_mode = $mode;

        if ($mode == self::MODE_WRITE)
        {
            // Delete previous log file
            @unlink($path);
        }
    }

    /**
     * Obtain file handle
     * 
     * @return resource
     */
    protected function _h()
    {
        if ($this->_h === NULL)
        {
            $this->_h = fopen($this->_path, $this->_mode);
        }
        return $this->_h;
    }

    /**
     * Write pending messages to file
     */
    protected function _write()
    {
        if ($this->_mode == self::MODE_READ)
            throw new Kohana_Exception('Unable to write to the log - it has been opened in the READ mode');
        
        if ( ! empty($this->_messages))
        {
            foreach ($this->_messages as $message)
            {
                $text = date('Y-m-d H:i:s') . "\t";

                switch ($message['level'])
                {
                    case self::INFO:    $text .= "INFO:\t";    break;
                    case self::WARNING: $text .= "WARNING:\t"; break;
                    case self::ERROR:   $text .= "ERROR:\t";   break;
                    case self::FATAL:   $text .= "FATAL:\t";   break;
                }

                $text .= $message['message'];

                fputs($this->_h(), $text . PHP_EOL);
                fputs($this->_h(), PHP_EOL); // use blank line as separator
            }
        }
    }

    /**
     * Read all the messages from file
     * 
     * @return array
     */
    public function read()
    {
        if ($this->_mode != self::MODE_READ)
            throw new Kohana_Exception('Unable to read from the log - it has been opened in the WRITE mode');

        $messages = array();

        if ( !is_readable($this->_path))
            return $messages;

        do {
            // Read first line of the log message
            $line = fgets($this->_h());
            if ($line === FALSE)
                break; // EOF

            $line = trim($line, " \t\r\n");

            if ($line == '')
                continue; // skip empty lines

            $line = explode("\t", $line, 3);
            $time    = isset($line[0]) ? $line[0] : '';
            $level   = isset($line[1]) ? $line[1] : '';
            $message = isset($line[2]) ? $line[2] : '';
            
            switch ($level)
            {
                case 'WARNING:': $level = Log::WARNING; break;
                case 'ERROR:':   $level = Log::ERROR;   break;
                case 'FATAL:':   $level = Log::FATAL;   break;
                default:         $level = Log::INFO;
            }

            // Read other possible lines of the log message
            while ($line = trim(fgets($this->_h()), " \t\r\n"))
            {
                $message .= PHP_EOL . $line;
            }

            $messages[] = array(
                'message' => $message,
                'level'   => $level,
                'time'    => $time
            );
        }
        while ($line !== FALSE);

        return $messages;
    }

    /**
     * Return number of messages in log for each log level
     *
     * @return array
     */
    public function stats()
    {
        if ($this->_mode != self::MODE_READ)
            throw new Kohana_Exception('Unable to get log stats - it has been opened in the WRITE mode');

        $stats = array(
            0            => 0, // number of all messages in log
            Log::INFO    => 0,
            Log::WARNING => 0,
            Log::ERROR   => 0,
            Log::FATAL   => 0
        );

        if ( ! is_readable($this->_path))
            return $stats;
        
        do {
            // Read first line of the log message
            $line = fgets($this->_h());
            if ($line === FALSE)
                break; // EOF

            $line = trim($line, " \t\r\n");

            if ($line == '')
                continue; // skip empty lines

            $stats[0]++;

            $line = explode("\t", $line, 3);
            $level = isset($line[1]) ? $line[1] : '';

            switch ($level)
            {
                case 'INFO:':    $stats[Log::INFO]++;    break;
                case 'WARNING:': $stats[Log::WARNING]++; break;
                case 'ERROR:':   $stats[Log::ERROR]++;   break;
                case 'FATAL:':   $stats[Log::FATAL]++;   break;
            }
            
            // Read other possible lines of the log message
            while ($line = trim(fgets($this->_h()), " \t\r\n"))
            {
            }

        }
        while ($line !== FALSE);

        return $stats;
    }

    /**
     * Close file handle
     */
    public function  __destruct()
    {
        parent::__destruct();

        if ($this->_h)
        {
            fclose($this->_h);
        }
    }
}