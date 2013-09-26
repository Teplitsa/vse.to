<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Frontend route
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Route_Frontend extends Route
{

    /**
     * Does this route match the specified uri?
     * 
     * @param  string $uri
     * @return array
     */
    public function matches($uri)
    {
        $params = parent::matches($uri);

        // Add 'frontend' directory
        if ($params !== FALSE && ! isset($params['directory']))
        {
            $params['directory'] = 'frontend';
        }

        return $params;
    }

	/**
	 * Generates a URI for the current route based on the parameters given.
	 *
	 *
	 * @param   array   URI parameters
	 * @return  string
	 * @throws  Kohana_Exception
	 * @uses    Route::REGEX_Key
	 */
	public function uri(array $params = NULL)
	{
        // Add 'frontend' directory
        if ($params !== FALSE && ! isset($params['directory']))
        {
            $params['directory'] = 'frontend';
        }

        return parent::uri($params);
    }
}