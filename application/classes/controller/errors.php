<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Error handling controller
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class Controller_Errors extends Controller
{
    /**
     * Renders an error
     *
     * @param string  $uri
     * @param integer $status
     * @param string  $message
     */
    public function action_error($uri, $status, $message = NULL)
    {
        if ($status !== NULL)
        {
            $status = (int) $status;
            $this->request->status = $status;
        }
        else
        {
            $status = $this->request->status;
        }

        if ($message === NULL)
        {
            $message = Request::$messages[$status];
        }

        if ( ! Request::$is_ajax)
        {
            if ($status == 404)
            {
                $layout_script = 'layouts/errors/404';
            }
            else
            {
                $layout_script = 'layouts/errors/500';
            }

            $layout = $this->prepare_layout($layout_script);
            $layout->message = __($message);
            $this->request->response = $layout;
        }
    }
}