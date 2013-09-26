<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * URL helper class.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class URL extends Kohana_URL {

    /**
     * Generates uri using specified route name and parameters.
     * If $save_history is specified than current requests uri is appended to uri via '/~' as history parameter.
     *
     * @param  string|Route $route          Route name or Route instance
     * @param  array        $params         Route parameters
     * @param  boolean      $save_history   Append current uri to history via /~
     * @return string
     */
    public static function uri_to($route = NULL, array $params = NULL, $save_history = FALSE)
    {
        $request = Request::current();

        if ($route !== NULL && ! $route instanceof Route)
        {
            $route = Route::get($route);
        }   
        else
        {
            $route = $request->route;
        }

        if ($save_history)
        {
            // Append current uri as history
            if ( ! isset($params['history']))
            {
                $params['history'] = $request->uri;
            }            
        }
        else
        {
            unset($params['history']);
        }
        $uri = $route->uri($params);

        // Save "in window" parameter
        if ( ! empty($_GET['window']))
        {
            $uri .= '?window=1';
        }
        return $uri;
    }

    /**
     * Generates url using specified route name and parameters.
     *
     * @param  string|Route $route          Route name or Route instance
     * @param  array        $params         Route parameters
     * @param  boolean      $save_history   Append current uri to history via /~
     * @return string
     */
    public static function to($route = NULL, array $params = NULL, $save_history = FALSE)
    {
        return URL::site(URL::uri_to($route, $params, $save_history));
    }

    /**
     * Create uri from current uri by replacing parameters from $params.
     *
     * @param  array $params
     * @param  array $ignored_params
     * @return string
     */
    public static function uri_self(array $params, array $ignored_params = array('page'))
    {
        $request = Request::current();

        $request_params = $request->param();

        $request_params['directory']  = $request->directory;
        $request_params['controller'] = $request->controller;
        $request_params['action']     = $request->action;

        // Unset ignored params
        foreach ($ignored_params as $param)
        {
            unset($request_params[$param]);
        }

        $params = array_merge($request_params , $params);

        // Preserve current history
        $history = $request->param('history');
        if ($history !== NULL)
        {
            $params['history'] = $history;
            $save_history = TRUE;
        }
        else
        {
            $save_history = FALSE;
        }

        return URL::uri_to(NULL, $params, $save_history);
    }

    /**
     * Create uri from current uri by replacing parameters from $params.
     *
     * @param  array $params
     * @param  array $ignored_params
     * @return string
     */
    public static function self(array $params, array $ignored_params = array('page'))
    {
        return URL::site(URL::uri_self($params, $ignored_params));
    }

    /**
     * Generate uri back based on requests uri history part.
     * If there is no history part in current uri - route with the name $default
     * and default parameters is used.
     * If $default is NULL - current route with default parameters is used.
     *
     * @param  string  $default Name of the default route to use if history is empty. NULL - use current request uri
     * @param  integer $levels  How many levels to go back
     * @param  array   $params
     * @return string
     */
    public static function uri_back($default = NULL, $levels = 1, array $params = NULL)
    {
        $request = Request::current();

        for ($i = 0; $i < $levels; $i++)
        {
            if ($i == 0)
            {
                // First - take history from the current url
                $history = $request->param('history');                
            }
            else
            {
                // Parse route to retrieve the history param
                list($name, $request_params) = URL::match($history);

                if (isset($request_params['history']))
                {
                    $history = $request_params['history'];
                }
                else
                {
                    $history = NULL;
                }
            }
            
            if ($history === NULL)
            {
                if ($default === NULL)
                {
                    // Use current route
                    return Request::current()->route->uri($params);
                }
                else
                {
                    // Get route by name
                    return Route::get($default)->uri($params);
                }
            }
            if ($history === '') {
                return '#';
            }
        }

        if ($params === NULL)
        {
            // There is no need to change any params in history uri
            return $history;
        }
        else
        {
            list($name, $request_params) = URL::match($history);
            $params = array_merge($request_params , $params);

            return URL::uri_to($name, $params, ( ! empty($params['history'])));
        }
    }

    /**
     * Generate uri back based on requests uri history part
     *
     * @param  string $default Name of the default route to use if history is empty. NULL - use current request uri
     * @param  integer $levels How many levels to go back
     * @param  array   $params
     * @return string
     */
    public static function back($default = NULL, $levels = 1, array $params = NULL)
    {
        return URL::site(URL::uri_back($default, $levels, $params));
    }

    /**
     * Match given uri against routes and parse it
     *
     * @param  string $uri
     * @return array Route name and parsed params
     */
    public static function match($uri)
    {
		$routes = Route::all();

		foreach ($routes as $name => $route)
		{
			if ($params = $route->matches($uri))
                return array($name, $params);
        }
        return array(NULL, NULL);
    }


    public static function encode($value)
    {
        return @bin2hex($value);
    }

    public static function decode($value)
    {
        return @pack('H*', $value);
    }
}