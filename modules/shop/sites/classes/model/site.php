<?php defined('SYSPATH') or die('No direct script access.');

class Model_Site extends Model
{
    /**
     * Current site
     * 
     * @var Model_Site
     */
    protected static $_current;

    /**
     * Get currently active site
     *
     * @return Model_Site
     */
    public static function current()
    {
        if ( ! isset(Model_Site::$_current))
        {                    
            $site = new Model_Site();

            if (Kohana::config('sites.multi'))
            {
                // Determine current site for multi-site environment
                // by "site_id" param or by host name
                $site_id = Request::current()->param('site_id', NULL);
                if (APP == 'BACKEND' &&  ! empty($site_id))
                {
                    // Detect site by explicitly specified id
                    $site->find($site_id);
                }
                elseif (APP == 'FRONTEND')
                {
                    // Detect site by url for frontend application
                    $url = self::canonize_url(URL::base(FALSE, TRUE));
                    $site->find_by_url($url);
                }
            }
            else
            {
                // Don't use multisite feature - there is always a single site
                // Select the first one from db
                $site->find_by();
            }

            Model_Site::$_current = $site;
        }
        return Model_Site::$_current;
    }

    /**
     * Get site caption by site id
     * 
     * @param  integer $id
     * @return string
     */
    public static function caption($id)
    {
        // Obtain all sites (CACHED!)
        $sites = Model::fly('Model_Site')->find_all();

        if (isset($sites[$id]))
        {
            return $sites[$id]->caption;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Canonize site url
     *  -   strip out protocols
     *  -   strip out "www."
     *  -   strip out port
     *  -   trim slashes
     * 
     * @param string $url
     */
    public static function canonize_url($url)
    {
        $info = @parse_url($url);
        if ($info === FALSE)
        {
            // url is really seriously malformed
            return $url;
        }

        $host = isset($info['host']) ? $info['host'] : '';
        $path = isset($info['path']) ? $info['path'] : '';

        // Strip out 'www.'
        if (strpos($host, 'www.') === 0)
        {
            $host = substr($host, strlen('www.'));
        }

        $path = rtrim($path, '/\\');

        return $host . $path;
    }

    /**
     * Set site url
     * 
     * @param string $url
     */
    public function set_url($url)
    {
        $this->_properties['url'] = self::canonize_url($url);
    }

    /**
     * Validate new values
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        if (Kohana::config('sites.multi'))
        {
            // ----- url
            if ( ! isset($newvalues['url']))
            {
                $this->error('Вы не указали адрес', 'url');
                return FALSE;
            }

            if (@parse_url($newvalues['url']) === FALSE)
            {
                $this->error('Некорректный адрес сайта', 'url');
                return FALSE;
            }
        }

        return TRUE;
    }

    /**
     * Save the site
     *
     * @param boolean $force_create
     */
    public function save($force_create = FALSE)
    {
        if ( ! isset($this->id))
        {
            // Creating a new site
            parent::save($force_create);

            // ----- Create the default structure

            // 1) System product properties
            /*
            $property = new Model_Property();

            $property->init(array(
                'site_id' => $this->id,
                'name'    => 'price',
                'caption' => 'Цена',
                'type'    => Model_Property::TYPE_TEXT,
                'system'  => TRUE
            ));
            $property->save();
             * 
             */
        }
        else
        {
            return parent::save($force_create);
        }
    }

    /**
     * Default parameters for find_all_by_...() methods
     * 
     * @return array
     */
    public function params_for_find_all()
    {
        return array(
            'key'      => 'id',
            'order_by' => 'id',
            'desc'     => FALSE
        );
    }
}