<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Index extends Controller_Backend
{
    /**
     * Render layout
     *
     * @param  View|string $content
     * @param  string $layout_script
     * @return string
     */
    public function render_layout($content, $layout_script = NULL)
    {
        $view = new View('backend/workspace');
        $view->content = $content;

        $layout = $this->prepare_layout($layout_script);
        $layout->caption = 'Панель управления';
        $layout->content = $view;
        return $layout->render();
    }

    /**
     * Index action
     */
	public function action_index()
	{
        $this->request->response = $this->render_layout(new View('backend/index'));
	}

    /**
     * Render flash messages
     * 
     * @return string
     */
    public function widget_flashmessages()
    {
        $messages = FlashMessages::fetch_all();

        $view = new View('backend/flashmessages');
        $view->messages = $messages;
        return $view->render();
    }
}
