<?php defined('SYSPATH') or die('No direct script access.');

class Model_Coupon extends Model
{
    public static $date_as_timestamp = FALSE;
    
    /**
     * @return string
     */
    public function default_code()
    {
        return Text::random('alnum', 8);
    }

    /**
     * @return Money
     */
    public function default_discount_sum()
    {
        return new Money();
    }

    /**
     * @return float
     */
    public function default_discount_percent()
    {
        return 0;
    }

    /**
     * @return integer
     */
    public function default_valid_after()
    {
        if (self::$date_as_timestamp)
        {
            return mktime(0, 0, 0);
        }
        else {
            return date('Y-m-d');
        }
    }

    /**
     * @return integer
     */
    public function default_valid_before()
    {
        if (self::$date_as_timestamp)
        {
            return mktime(23, 59, 59);
        }
        else
        {
            return date('Y-m-d');
        }
    }

    /**
     * Determine discount type by finding which value (sum or percent) is not zero
     * 
     * @return string
     */
    public function get_discount_type()
    {
        if (isset($this->_properties['discount_type']))
        {
            return $this->_properties['discount_type'];
        }        
        elseif ($this->discount_sum->amount > 0)
        {
            return 'sum';
        }
        else
        {
            return 'percent';
        }
    }

    /**
     * Get discount value (sum or percent - depends on type)
     * 
     * @return float
     */
    public function get_discount()
    {
        if (isset($this->_properties['discount']))
        {
            return $this->_properties['discount'];
        }
        elseif ($this->discount_type == 'sum')
        {
            return $this->discount_sum->amount;
        }
        else
        {
            return $this->discount_percent;
        }
    }

    /**
     * @return string
     */
    public function get_discount_formatted()
    {
        if ($this->discount_type == 'sum')
        {
            return $this->discount_sum->format();
        }
        else
        {
            return $this->discount_percent . ' %';
        }
    }

    /**
     * Return the unix-timestamp of day after which coupon is considered valid
     * 
     * @return integer
     */
    public function get_date_from()
    {
        return $this->valid_after;
    }

    /**
     * @return string
     */
    public function get_date_from_formatted()
    {
        if (self::$date_as_timestamp)
        {
            return l10n::date(Kohana::config('date.format'), $this->date_from);
        }
        else
        {
            return l10n::date_convert($this->date_from, '%Y-%m-%d', Kohana::config('date.format'));
        }
    }


    /**
     * Return the unix-timestamp of day before which coupon is considered valid
     *
     * @return integer
     */
    public function get_date_to()
    {
        return $this->valid_before;
    }

    /**
     * @return string
     */
    public function get_date_to_formatted()
    {
        if (self::$date_as_timestamp)
        {
            return l10n::date(Kohana::config('date.format'), $this->date_to);
        }
        else
        {
            return l10n::date_convert($this->date_to, '%Y-%m-%d', Kohana::config('date.format'));
        }
    }

    /**
     * Set the day from which coupon is valid
     *
     * @param integer $value [!]unix-timestamp
     */
    public function set_date_from($value)
    {
        $this->valid_after = $value;
    }

    /**
     * Set the day before which coupon is valid
     *
     * @param integer $value [!]unix-timestamp
     */
    public function set_date_to($value)
    {
        if (self::$date_as_timestamp)
        {
            $this->valid_before = $value + 24*60*60 - 1;
        }
        else
        {
            $this->valid_before = $value;
        }
    }

    /**
     * @return string
     */
    public function get_description_short()
    {
        return Text::limit_chars($this->description, 63, '...');
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return ($this->multiple ? 'многоразовый' : 'одноразовый');
    }

    /**
     * Get the coupon user (if this coupon is personal)
     * 
     * @return Model_User
     */
    public function get_user()
    {
        $user = new Model_User();
        $user->find((int) $this->user_id);
        return $user;
    }

    /**
     * Get sites this coupon is valid in
     *
     * @return array array(site_id => TRUE/FALSE)
     */
    public function get_sites()
    {
        if ( ! isset($this->_properties['sites']))
        {
            $result = array();

            if ( ! isset($this->id))
            {
                // For new coupon all sites are selected
                /*
                $sites = Model::fly('Model_Site')->find_all();
                foreach ($sites as $site)
                {
                    $result[$site->id] = 1;
                }
                 */

                // Select current site for new coupon
                if (Model_Site::current()->id !== NULL)
                {
                    $result[Model_Site::current()->id] = 1;
                }
            }
            else
            {
                $sites = Model_Mapper::factory('CouponSite_Mapper')->find_all_by_coupon_id($this, (int) $this->id,
                    array('columns' => array('site_id'), 'as_array' => TRUE)
                );
                foreach ($sites as $site)
                {
                    $result[$site['site_id']] = 1;
                }
            }

            $this->_properties['sites'] = $result;
        }
        return $this->_properties['sites'];
    }


    /**
     * Validate values
     * 
     * @param  array $newvalues
     * @return boolean
     */
    public function validate(array $newvalues)
    {
        // date range
        if ( ! isset($newvalues['date_from']) || ! isset($newvalues['date_to']))
        {
            $this->error('Вы не указали срок действия купона!', 'date_from');
            return FALSE;
        }

        if (
                self::$date_as_timestamp && ($newvalues['date_to'] < $newvalues['date_from'])
             || ! self::$date_as_timestamp && (l10n::timestamp('%Y-%m-%d', $newvalues['date_to']) < l10n::timestamp('%Y-%m-%d', $newvalues['date_from']))
        )
        {
            $this->error('Дата конца срока не может быть меньше даты начала!', 'date_to');
            return FALSE;
        }

        // discount amount
        if ( ! isset($newvalues['discount']) || ! isset($newvalues['discount_type']))
        {
            $this->error('Вы не указали скидку!', 'discount');
            return FALSE;
        }

        if ($newvalues['discount_type'] == 'percent' && $newvalues['discount'] > 100.0)
        {
            $this->error('Скидка не может быть больше 100%!', 'discount');
            return FALSE;
        }
        
        return TRUE;
    }


    /**
     * Save coupon
     *
     * @param  boolean $force_create
     * @return integer
     */
    public function save($force_create = FALSE)
    {
        if ($this->discount_type == 'sum')
        {
            $this->discount_percent     = 0;
            $this->discount_sum->amount = $this->discount;
        }
        else
        {
            $this->discount_percent     = $this->discount;
            $this->discount_sum->amount = 0;
        }

        parent::save($force_create);

        // Mark sites selected for this coupon
        Model_Mapper::factory('CouponSite_Mapper')->link_coupon_to_sites($this, $this->sites);
    }
}