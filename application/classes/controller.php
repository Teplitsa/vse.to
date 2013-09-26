<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Controller
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class Controller extends Kohana_Controller
{
    /**
     * @var Request
     */
    public $request;

    /**
     * Now the before() function must return the boolean value.
     * It is used to controll the execution of the current action:
     * if TRUE  - the action will be executed
     * if FALSE - the action will NOT be executed (but the after() method will still be called)
     *
     * Note, that before() function MUST return FALSE in case it forwards the request
     *
     * @return boolean
     */
    public function before()
    {
        return TRUE;
    }

    /**
     * Creates and prepares layout to be used for controller
     *
     * @return Layout
     */
    public function prepare_layout($layout_script = NULL)
    {
        if ($layout_script === NULL)
        {
            throw new Kohana_Exception('Layout script name undefined');
        }
        
        $layout = Layout::instance();
        $layout->set_filename($layout_script);

        return $layout;
    }

    /**
     * Render layout
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        $layout = $this->prepare_layout($layout_script);
        $layout->content = $content;
        return $layout->render();
    }

    /**
     * Generic ajax actions - prepares the response for output
     */
    protected function _action_ajax()
    {
        // Put flash messages into response
        $this->request->response['messages'] = FlashMessages::fetch_all();
        // Encode in json
        $this->request->response = json_encode($this->request->response);
    }
}