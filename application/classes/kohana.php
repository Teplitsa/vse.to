<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Framework core
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Kohana extends Kohana_Core
{
    /**
     * Set up the Kohana::$is_cli when being run using the php-cgi binary from command line
     * 
     * @param array $settings
     */
    public static function  init(array $settings = NULL)
    {
		if (Kohana::$_init)
		{
			// Do not allow execution twice
			return;
		}
        
        parent::init($settings);

        if (PHP_SAPI == 'cgi' && ! isset($_SERVER['HTTP_HOST']))
        {
            // Looks like we've been launched with php-cgi from command line
            Kohana::$is_cli = TRUE;
        }
    }

    /**
     * Inline exception handler, displays the error message, source of the
     * exception, and the stack trace of the error.
     *
     * @uses    Kohana::exception_text
     * @param   object   exception object
     * @return  boolean
     */
    public static function exception_handler(Exception $e)
    {
        if (Kohana::$environment === Kohana::DEVELOPMENT)
        {
            parent::exception_handler($e);
        }
        else
        {
            // We are in production mode
            try {

                if (Request::$instance !== NULL)
                {
                    $request = Request::$instance;
                }
                else
                {
                    $request = new Request('');
                }

                if ($request->status !== 404)
                {
                    // Set status to error
                    $request->status = 500;
                }

                // Log all errors, except 404
                if ($request->status !== 404 && is_object(Kohana::$log))
                {
                    // Get the exception information
                    $type    = get_class($e);
                    $code    = $e->getCode();
                    $message = $e->getMessage();
                    $file    = $e->getFile();
                    $line    = $e->getLine();

                    // Create a text version of the exception
                    $error = Kohana::exception_text($e);

                    // Add this exception to the log
                    Kohana::$log->add(Kohana::ERROR, $error);

                    // Make sure the logs are written
                    Kohana::$log->write();
                }

                // Clean the output buffer if one exists
                ob_get_level() and ob_clean();

                $ctrl_error = $request->get_controller('Controller_Errors');
                $ctrl_error->action_error($request->uri, $request->status);

                echo $request
                        ->send_headers()
                        ->response;
            }
            catch (Exception $e)
            {                
                // An exception happend during production exception handling...
                // Something has gone definitly wrong.
                parent::exception_handler($e);
            }
        }
    }

}
