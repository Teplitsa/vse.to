<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Money
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Money
{
    /**
     * Power of the smallest fraction (i.e рубль = 100 копеек)
     */
    const FRACTION = 100;
    
    /**
     * @var float
     */
    protected $_amount;

    /**
     * Construct money
     * 
     * @param float $amount
     */
    public function  __construct($amount = 0.0)
    {
        $this->set_amount($amount);
    }


    /**
     * Set amount from floating point number
     * 
     * @param float|string $amount
     */
    public function set_amount($amount)
    {
        if (is_string($amount))
        {
            $amount = l10n::string_to_float($amount);
        }
        
        $this->_amount = round($amount * Money::FRACTION);
    }

    /**
     * Get money amount as floating point number
     * 
     * @return float
     */
    public function get_amount()
    {
        return $this->_amount / Money::FRACTION;
    }


    /**
     * Specify the intrenal amount value explicitly (for example when retrieving value from db)
     * 
     * @param float $raw_amount
     */
    public function set_raw_amount($raw_amount)
    {
        $this->_amount = $raw_amount;
    }

    /**
     * Get internal amount value (for example to store in db)
     * 
     * @return float
     */
    public function get_raw_amount()
    {
        return $this->_amount;
    }

    // -------------------------------------------------------------------------
    // Arthmetic operations
    // -------------------------------------------------------------------------
    /**
     * Add money
     * 
     * @param  Money|float $amount
     * @return Money
     */
    public function add($amount)
    {
        if ($amount instanceof Money)
        {
            $this->_amount += $amount->get_raw_amount();
        }
        else
        {
            $this->_amount = round($this->_amount + $amount * Money::FRACTION);
        }
        
        return $this;
    }

    /**
     * Sub money
     *
     * @param  Money|float $amount
     * @return Money
     */
    public function sub($amount)
    {
        if ($amount instanceof Money)
        {
            $this->_amount -= $amount->get_raw_amount();
        }
        else
        {
            $this->_amount = round($this->_amount - $amount * Money::FRACTION);
        }

        return $this;
    }
    
    /**
     * Multiply amount
     *
     * @param  float $v
     * @return Money
     */
    public function mul($v)
    {
        $this->_amount = round($this->_amount * $v);
        
        return $this;
    }
    
    // -------------------------------------------------------------------------
    // Comparisions
    // -------------------------------------------------------------------------
    /**
     * Equal
     *
     * @param  Money $money
     * @return boolean
     */
    public function eq(Money $money)
    {
        return ($this->get_raw_amount() == $money->get_raw_amount());
    }

    /**
     * Greater than
     * 
     * @param  Money $money
     * @return boolean
     */
    public function gt(Money $money)
    {
        return ($this->get_raw_amount() > $money->get_raw_amount());
    }

    /**
     * Greater than or equal to
     *
     * @param  Money $money
     * @return boolean
     */
    public function ge(Money $money)
    {
        return ($this->get_raw_amount() >= $money->get_raw_amount());
    }

    /**
     * Less than
     *
     * @param  Money $money
     * @return boolean
     */
    public function lt(Money $money)
    {
        return ($this->get_raw_amount() < $money->get_raw_amount());
    }

    /**
     * Less than or equal to
     *
     * @param  Money $money
     * @return boolean
     */
    public function le(Money $money)
    {
        return ($this->get_raw_amount() <= $money->get_raw_amount());
    }

    /**
     * Not zero
     *
     * @return boolean
     */
    public function nz()
    {
        return $this->get_raw_amount() > 0;
    }

    // -------------------------------------------------------------------------
    // String representation
    // -------------------------------------------------------------------------
    /**
     * Format money to a string
     * 
     * @return string
     */
    public function format()
    {
        return sprintf("%.2f", $this->amount) . ' руб.';
    }

    // -------------------------------------------------------------------------
    // Magic methods
    // -------------------------------------------------------------------------
    public function  __toString()
    {
        return $this->format();
    }
    

    public function  __set($name, $value)
    {
        $setter = 'set_' . strtolower($name);

        if (method_exists($this, $setter))
        {
            $this->$setter($value);
        }
        else
        {
            throw new Kohana_Exception('Unknown property :name', array(':name' => $name));
        }
    }

    public function  __get($name)
    {
        $getter = 'get_' . strtolower($name);

        if (method_exists($this, $getter))
        {
            return $this->$getter();
        }
        else
        {
            throw new Kohana_Exception('Unknown property :name', array(':name' => $name));
        }
    }
}