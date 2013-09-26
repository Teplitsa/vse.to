<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Routing system.
 * Differences from Kohana_Route:
 *  1)  Directory is automatically cut out of controller name when uri is constructed
 *  2)  Optional parameters with default values are NOT placed in uri, when uri
 *      is constructed.
 *  3)  add() function to create routes that are instances of custom classes
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Route extends Kohana_Route {

	/**
     * Adds a named route and returns it.
     * Just like Route::set(), but allows route to be an instance of class derived from Route
	 *
	 * @param  string $name
	 * @param  Route  $route
	 * @return Route
	 */
	public static function add($name, Route $route)
	{
		return Route::$_routes[$name] = $route;
	}

	/**
	 * Generates a URI for the current route based on the parameters given.
	 *
	 *     // Using the "default" route: "users/profile/10"
	 *     $route->uri(array(
	 *         'controller' => 'users',
	 *         'action'     => 'profile',
	 *         'id'         => '10'
	 *     ));
	 *
	 * @param   array   URI parameters
	 * @return  string
	 * @throws  Kohana_Exception
	 * @uses    Route::REGEX_Key
	 */
	public function uri(array $params = NULL)
	{
		if ($params === NULL)
		{
			// Use the default parameters
			$params = $this->_defaults;
		}
		else
		{
			// Add the default parameters
			$params += $this->_defaults;
		}

        // Chop the directory prefix off controller
        if (isset($params['directory']) && $params['directory'] !== '')
        {
			$prefix = str_replace(array('\\', '/'), '_', trim($params['directory'], '/')) . '_';
            if (stripos($params['controller'], $prefix) === 0)
            {
                $params['controller'] = substr($params['controller'], strlen($prefix));
            }
        }

		// Start with the routed URI
		$uri = $this->_uri;

		if (strpos($uri, '<') === FALSE AND strpos($uri, '(') === FALSE)
		{
			// This is a static route, no need to replace anything
			return $uri;
		}

        // Process optional parameters and cut out groups with all default values
		while (preg_match('#\([^()]++\)#', $uri, $match))
		{
			// Search for the matched value
			$search = $match[0];

			// Remove the parenthesis from the match as the replace
			$replace = substr($match[0], 1, -1);

            $all_default = TRUE;

            preg_match_all('#'.Route::REGEX_KEY.'#', $replace, $matches);

            foreach ($matches[1] as $param)
			{
				if (       isset($params[$param])
                    && ( ! isset($this->_defaults[$param]) || $params[$param] != $this->_defaults[$param])
                )
				{
                    // There is a parameter with non-default value in group :-(
                    $all_default = FALSE;
                    break;
				}
			}

            if ($all_default)
            {
                // If all parameters in optional group are default - cut the group out
                $uri = str_replace($search, '', $uri);
            }
            else
            {
                // Leave the group in URI and remove parenthesis
                $uri = str_replace($search, $replace, $uri);
            }
		}
        // Replace parameters with their values

		while(preg_match('#'.Route::REGEX_KEY.'#', $uri, $match))
		{
			list($key, $param) = $match;

			if ( ! isset($params[$param]))
			{
				// Ungrouped parameters are required
				throw new Kohana_Exception('Required route parameter not passed: :param',
					array(':param' => $param));
			}

			$uri = str_replace($key, $params[$param], $uri);
		}
		// Trim all extra slashes from the URI
		$uri = preg_replace('#//+#', '/', rtrim($uri, '/'));
		return $uri;
	}
}
