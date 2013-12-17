<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Authentication & authorization
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Auth {

    /**
     * @var Auth
     */
    protected static $_instance;

    /**
     * @return Auth
     */
    public static function instance()
    {
        if (self::$_instance === NULL)
        {
            self::$_instance = new Auth();
        }
        return self::$_instance;
    }

    /**
     * Is current user allowed to do $privilege?
     *
     * @param string $privilege
     * @return boolean
     */
    public static function granted($privilege)
    {
        return self::instance()->get_user()->granted($privilege);
    }

    
    /**
     * Current authenticated user
     * @var Model_User
     */
    protected $_user;

    /*
     * Login errors and messages
     */
    protected $_messages;


    /**
     * Gets all available privileges
     *
     * @return array
     */
    public function privileges()
    {
        return self::instance()->get_user()->group->privileges();
    }

    /**
     * Gets current authenticated user
     *
     * @return Model_User
     */
    public function get_user()
    {
        if ($this->_user === NULL)
        {
            $user = new Model_User();

            // Try the login from session
            $login = Session_Native::instance()->get('login');
            if ($login !== NULL)
            {
                $user->find_by_email($login);

                // Invalid login stored in session - destroy session, reset user to anonymous
                if ($user->id === NULL)
                {
                    Session_Native::instance()->destroy();
                    $user->init();
                }
            }
            
            if ($user->id === NULL)
            {
                // Try autologin via cookie
                $token_str = Cookie::get('login_token', '');
                if ($token_str != '')
                {
                    $token = Model::fly('Model_Token')->find_by_token_and_type($token_str, Model_Token::TYPE_LOGIN);
                    if (isset($token->id))
                    {
                        $user->find($token->user_id);

                        if (isset($user->id))
                        {                            
                            // User was succesfully found - regenerate token
                            $token->delete();

                            $token = $user->generate_token(Model_Token::TYPE_LOGIN);
                            Cookie::set('login_token', $token->token, Model_Token::LOGIN_LIFETIME);

                            // and store user login in session
                            Session_Native::instance()->set('login', $user->email);
                        }
                        else
                        {
                            // Invalid token
                            $token->delete();
                            Cookie::set('login_token', NULL);
                        }
                    }
                    else
                    {
                        // Invalid token string
                        Cookie::set('login_token', NULL);
                    }
                }
            }

            $this->_user = $user;
        }

        return $this->_user;
    }

    /**
     * Peforms a login attempt
     */
    public function login($login, $password, $remember = FALSE)
    {
        $user = new Model_User();

        // Try to find user by login specified
        $user->find_by_email($login);

        if ($user->id === NULL)
        {
            // Invalid login
            $this->error('Пользователя с указанным email не существует!', 'email');
            return FALSE;
        }

        // Check password
        if ($this->calculate_hash($password, $this->get_salt($user->hash)) !== $user->hash)
        {
            // Invalid password
            $this->error('Пароль указан неверно!', 'password');
            return FALSE;
        }

        if (!$user->active) {
            // Not active user
            $this->error('Доступ пользователя на портал ограничен!');
            return FALSE;            
        }
        // Login succeded!
        $user->complete_login();
        // Save user to session
        Session_Native::instance()->set('login', $user->email);

        if ($remember)
        {
            // Enable autologin via cookie
            $token = $user->generate_token(Model_Token::TYPE_LOGIN);
            Cookie::set('login_token', $token->token, Model_Token::LOGIN_LIFETIME);
        }

        $this->_user = $user;
        return TRUE;
    }

    /**
     * Forces user to be current authenticated
     *
     * @param Model_User $user
     */
    public function set_authenticated(Model_User $user)
    {
        $this->_user = $user;

        // Save to session
        Session_Native::instance()->set('login', $user->email);
    }

    /**
     * Log user out, destroy the session, disable autologin
     */
    public function logout()
    {
        // Destroy session
        Session_Native::instance()->destroy();
        // Delete all login tokens
        Model::fly('Model_Token')->delete_all_by_user_id_and_type((int) $this->get_user()->id, Model_Token::TYPE_LOGIN);
        Cookie::set('login_token', NULL);
        // Reset user
        $this->get_user()->init();
    }

    /**
     * Calculate hash from password using existing or generated salt.
     *
     * @param  string $password
     * @param  string $salt FALSE to generate new salt seed
     * @return string
     */
    public function calculate_hash($password, $salt = FALSE)
    {
        if ($salt === FALSE)
        {
            // Generate salt
            $salt = substr($this->hash(uniqid(NULL, TRUE)), 0, 4);
        }

        $hash = $this->hash($password . $salt);

        // Append salt to hash
        return $hash . ':' . $salt;
    }

    /**
     * Get salt from password hash
     *
     * @param  string $hash
     * @return string
     */
    public function get_salt($hash)
    {
        return substr(strstr($hash, ':'), 1);
    }

    /**
     * Hash function
     *
     * @param  string $str
     * @return string
     */
    public function hash($str)
    {
        return hash('sha1', $str);
    }

    // -------------------------------------------------------------------------
    // Errors & messages
    // -------------------------------------------------------------------------
    /**
     * Add a message (error, warning) to this model
     *
     * @param string $text
     * @param string $field
     * @param integer $type
     * @return Model
     */
    public function message($text, $field = NULL, $type = FlashMessages::MESSAGE)
    {
        $this->_messages[] = array(
            'text'  => $text,
            'field' => $field,
            'type'  => $type
        );

        return $this;
    }

    /**
     * Add an error to this model
     *
     * @param  string $text
     * @param  string $field
     * @return Model
     */
    public function error($text, $field = NULL)
    {
        return $this->message($text, $field, FlashMessages::ERROR);
    }

    /**
     * Get all model errors at once
     *
     * @return array
     */
    public function errors()
    {
        $errors = array();
        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                $errors[] = $message;
            }
        }
        return $errors;
    }

    /**
     * Does this model have errors?
     *
     * @return boolean
     */
    public function has_errors()
    {
        foreach ($this->_messages as $message)
        {
            if ($message['type'] == FlashMessages::ERROR)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    private function  __construct()
    {
    }
}
