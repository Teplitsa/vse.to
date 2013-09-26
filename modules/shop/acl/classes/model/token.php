<?php defined('SYSPATH') or die('No direct script access.');

class Model_Token extends Model
{
    const TYPE_LOGIN    = 1; // A token used to login user via cookie
    const TYPE_PASSWORD = 2; // A token user to recover password

    const LOGIN_LIFETIME    = 604800; // Auto-login token is valid for 7 days
    const PASSWORD_LIFETIME = 172800; // Password recovery token is valid for two days

    /**
     * Default token type
     * 
     * @return integer
     */
    public function default_type()
    {
        return Model_Token::TYPE_LOGIN;
    }

    /**
     * Default expiration time for token
     * 
     * @return integer
     */
    public function default_expires_at()
    {
        switch ($this->type)
        {
            case Model_Token::TYPE_LOGIN:
                return time() + Model_Token::LOGIN_LIFETIME;
                break;

            case Model_Token::TYPE_PASSWORD:
                return time() + Model_Token::PASSWORD_LIFETIME;
                break;

            default:
                throw new Kohana_Exception('Unable to get default expiration time: invalid or not defined token type');
        }
    }
}