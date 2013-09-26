<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Validate uploaded file
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Form_Validator_File extends Form_Validator {

    const NO_FILE  = 'NO_FILE';
    const TOO_BIG  = 'TOO_BIG';
    const ERROR    = 'ERROR';

    protected $_messages = array(
        self::NO_FILE  => 'Не указан файл',
        self::TOO_BIG  => 'Размер файла на может превышать :upload_max_filesize',
        self::ERROR    => 'Произошла ошибка при загрузке файла'
    );

    /**
     * It's possible not to specify a file
     * @var boolean
     */
    protected $_allow_empty;

    /**
     * Creates uploaded file validator
     *
     * @param string  $name                 Name of file in $_FILES array
     * @param array   $messages             Error messages templates
     * @param boolean $breaks_chain         Break chain after validation failure
     * @param boolean $allow_empty          Allow empty strings
     */
    public function  __construct(array $messages = NULL, $breaks_chain = TRUE, $allow_empty = FALSE)
    {
        parent::__construct($messages, $breaks_chain);
        
        $this->_allow_empty = $allow_empty;
    }

    /**
     * Validate
     *
     * @param array $context Form data
     * @return boolean
     */
    protected function _is_valid(array $context = NULL)
    {
        $file = $this->_value;

        if (empty($file))
        {
            if ( ! $this->_allow_empty)
            {
                $this->_error(self::NO_FILE);
                return FALSE;
            }
            else
            {
                return TRUE;
            }
        }

        if ($file['error'] !== UPLOAD_ERR_OK)
        {
            switch ($file['error'])
            {
                case UPLOAD_ERR_NO_FILE:
                    if ( ! $this->_allow_empty)
                    {
                        $this->_error(self::NO_FILE);
                        return FALSE;
                    }
                    else
                    {
                        return TRUE;
                    }

                case UPLOAD_ERR_INI_SIZE:
                    $this->_error(self::TOO_BIG);
                    return FALSE;

                default:
                    $this->_error(self::ERROR);
                    return FALSE;
            }
        }

        if ( ! isset($file['tmp_name']) || ! is_uploaded_file($file['tmp_name']))
        {
            $this->_error(self::ERROR);
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Replaces placeholders in error message
     *
     * @param string $error_text
     * @return string
     */
    protected function _replace_placeholders($error_text)
    {
        $error_text = parent::_replace_placeholders($error_text);

        $upload_max_filesize = ini_get('upload_max_filesize');
        $upload_max_filesize = str_replace(array('G', 'M', 'K'), array(' Gb', ' Mb', ' Kb'), $upload_max_filesize);
        $error_text = str_replace(':upload_max_filesize', $upload_max_filesize, $error_text);

        return $error_text;
    }

}