<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Ulogin {
    
    protected $config = array(
        // Возможные значения: small, panel, window
        'type'             => 'panel',
        
        // на какой адрес придёт POST-запрос от uLogin
        'redirect_uri'     => NULL,
        
        // Сервисы, выводимые сразу
        'providers'        => array(
            'vkontakte',
            'facebook',
            'twitter',
            'google',
        ),
        
        // Выводимые при наведении
        'hidden'         => array(
            'odnoklassniki',
            'mailru',
            'livejournal',
            'openid'
        ),
        
        // Эти поля используются для значения поля username в таблице users
        'username'         => array (
            'first_name',
        ),
        
        // Обязательные поля
        'fields'         => array(
            'email',
        ),
        
        // Необязательные поля
        'optional'        => array(),
    );
    
    protected static $_used_id = array();
    
    public static function factory(array $config = array())
    {
        return new Ulogin($config);
    }
    
    public function __construct(array $config = array())
    {
        $this->config = array_merge($this->config, Kohana::$config->load('ulogin')->as_array(), $config);
        
        if ($this->config['redirect_uri'] === NULL)
            $this->config['redirect_uri'] = Url::base(Request::$current, true);//Request::initial()->url(true);
    }
    
    public function render()
    {    
        $params =     
            'display='.$this->config['type'].
            '&fields='.implode(',', array_merge($this->config['username'], $this->config['fields'])).
            '&providers='.implode(',', $this->config['providers']).
            '&hidden='.implode(',', $this->config['hidden']).
            '&redirect_uri='.$this->config['redirect_uri'].
            '&optional='.implode(',', $this->config['optional']);
        
        $view = View::factory('ulogin/ulogin')
                    ->set('cfg', $this->config)
                    ->set('params', $params);
        do
        {
            $uniq_id = "uLogin_".rand();
        }
        while(in_array($uniq_id, self::$_used_id));
        
        self::$_used_id[] = $uniq_id;
        
        $view->set('uniq_id', $uniq_id);
        
        return $view->render();
    }
    
    public function __toString()
    {
        try
        {
            return $this->render();
        }
        catch(Exception $e)
        {
            Kohana_Exception::handler($e);
            return '';
        }
    }
    
    public function login()
    {
        if (empty($_POST['token']))
            throw new Kohana_Exception('Empty token.');
            
        if (!($domain = parse_url(URL::base(), PHP_URL_HOST)))
        {
            $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        }
        
        $uLoginUrl = 'http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $domain;
        $s = file_get_contents($uLoginUrl);
        $user = json_decode($s, true);
        
        $ulogin = Model::fly('Model_Ulogin')->find_by_identity($user['identity']);
        
        if ($ulogin->id == NULL)
        {
            $current_user = Auth::instance()->get_user();
            if ($current_user->id)
            {
                $this->create_ulogin($ulogin, $user);
            }
            else
            {
                $cfg_fields = array_merge($this->config['fields'], $this->config['optional']);
                foreach($cfg_fields as $field)
                {
                    if (!empty($user[$field]))
                        $data[$field] = $user[$field];
                }
                            
//                $orm_user = $this->create_new_user($data);
                $real_user = $this->create_new_user($data);
                
                $password = $this->generatePassword();
                $real_user->set_password($password);
                $real_user->save();
                
                $user['user_id'] = $real_user->id;
                
                $this->create_ulogin($ulogin, $user);
                
                Auth::instance()->login($real_user->email, $password);
                
                return $password;
            }
        }
        else
        {
//            Auth::instance()->force_login($ulogin->user);
            Auth::instance()->set_authenticated(Model::fly('Model_User')->find_by_id($ulogin->user_id));
        }
    }
    
    public function mode()
    {
        return !empty($_POST['token']);
    }
    
    protected function create_ulogin($ulogin, $post)
    {
//        return $ulogin->values($post, array(
//            'user_id',
//            'identity',
//            'network',
//        ))->create();
        
        $ulogin = new Model_Ulogin();
        $ulogin->values($post);
        $ulogin->save();
        
    }

    protected function create_new_user($data)
    {
//        $orm_user = ORM::factory('User')->values($data)->create();
//        $orm_user->add('roles', ORM::factory('Role', array('name' => 'login')));
//        return $orm_user;
        
        $user = new Model_User();
        $user->group_id = $user->default_group_id();
        $user->active = 1;
		
        if($user->validate_create($data))
        {
            $user->values($data);
            $user->save();
        }
        else
        {
            $errors = $user->errors();
            throw new Kohana_Exception( $errors[0]['text'] );
        }
        return $user;
    }
    
    // From http://stackoverflow.com/a/1837443
    function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }
    
}