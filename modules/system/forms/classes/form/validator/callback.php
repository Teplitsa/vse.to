<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Use custom callback for validation
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Validator_Callback extends Validator {

    const INVALID = 'INVALID';

    protected $_messages = array(
        self::INVALID => 'Validation failed',
    );

    /**
     * Name of function or array(object,method) to use as callback
     * @var callback
     */
    protected $_callback;

    /**
     * Additional arguments for callback
     * @var array
     */
    protected $_args;

    /**
     * Creates validator
     *
     * Callback may be specified as string name of function or
     * as array(object, method) when method is used as callback
     * @link http://www.php.net/manual/en/language.pseudo-types.php#language.types.callback
     *
     * Callback is supposed to have following arguments:
     * function my_validate_callback($value, $context = NULL, .. additional args go here ..);
     *
     * You can supply additional arguments in $args array
     *
     * @param callback $callback        Callback
     * @param array   $args             Additional arguments for callback
     * @param array   $messages         Error messages templates
     * @param boolean $breaks_chain     Break chain after validation failure
     */
    public function  __construct($callback, array $args = NULL, array $messages = NULL, $breaks_chain = TRUE)
    {
        parent::__construct($messages, $breaks_chain);

        // Check that specified callback is valid
        if (is_array($callback))
        {
            if (count($callback) < 2)
            {
                throw new Exception('Invalid callback specified - :callback',
                    array(':callback' => print_r($callback, TRUE))
                );
            }

            if ( ! is_object($callback[0]) && ! is_string($callback[0]))
            {
                throw new Exception('Invalid object or class specified for callback :object',
                    array(':object' => $callback[0])
                );
            }

            if (is_string($callback[0]) && ! class_exists($callback[0]))
            {
                throw new Exception('Unknown class specified in callback :class',
                    array(':class' => $callback[0])
                );
            }
        }
        else
        {
            $callback = (string)$callback;
            if ( ! function_exists($callback))
            {
                throw new Exception('Unknown function specified as callback :function',
                    array(':function' => $callback)
                );
            }
        }

        $this->_callback = $callback;
        $this->_args = $args;
    }

    /**
     * Validate: call the callback
     *
     * WARNING: context is passed by reference
     *
     * @param array $context
     */
    protected function _is_valid(array $context = NULL)
    {
        $params = array($this->_value, & $context);
        $params = array_merge($params, $this->_args);

        $result = call_user_func_array($this->_callback, $params);

        if ( ! is_bool($result))
        {
            throw new Exception('Callback :callback has not returned a boolean value!',
                array(':callback' =>
                            (is_array($this->_callback)
                                ? '(' . get_class($this->_callback[0]) . ',' . (string)$this->_callback[1] . ')'
                                : (string)$this->_callback )
                )
            );
        }

        if ( ! $result)
        {
            $this->_error(self::INVALID);
            return FALSE;
        }

        return TRUE;
    }
}