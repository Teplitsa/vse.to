<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Backend route
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Route_Backend extends Route
{

    /**
     * Does this route match the specified uri?
     * 
     * @param  string $uri
     * @return array
     */
    public function matches($uri)
    {
        // Cut 'admin' prefix from uri
        if (preg_match('#admin(/(.*))?#', $uri, $matches))
        {
            $uri = isset($matches[2]) ? $matches[2] : '';
        }
        
        // Cut site id from the beginning of the uri
        if (Kohana::config('sites.multi') && preg_match('#^site-(\d++)(/(.*))?#', $uri, $matches))
        {            
            $site_id = $matches[1];
            $uri = isset($matches[3]) ? $matches[3] : '';
            
            $params = parent::matches($uri);

            // Add site id to params
            if ($params !== FALSE && ! isset($params['site_id']))
            {
                $params['site_id'] = $site_id;
            }
        }
        else
        {
            $params = parent::matches($uri);
        }

        // Add 'backend' directory
        if ($params !== FALSE && ! isset($params['directory']))
        {
            $params['directory'] = 'backend';
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
        // Add 'backend' directory
        if ($params !== FALSE && ! isset($params['directory']))
        {
            $params['directory'] = 'backend';
        }

        $uri = parent::uri($params);
        $uri = ltrim($uri, '/');

        // Prepend current site id to uri
        if (Kohana::config('sites.multi'))
        {
            if (isset($params['site_id']))
            {
                $site_id = $params['site_id'];
            }
            else
            {
                $site_id = Model_Site::current()->id;
            }
            
            if ($site_id !== NULL)
            {
                $uri = 'site-' . $site_id . '/' . $uri;
            }
        }
        
        // Prepend "admin" prefix
        $uri = 'admin/' . $uri;

        return $uri;
    }
}