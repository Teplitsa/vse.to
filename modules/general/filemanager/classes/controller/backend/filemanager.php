<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Backend_Filemanager extends Controller_Backend
{
    /**
     * Default action
     */
	public function action_index()
	{
        $view = new View('backend/workspace');

        if ( ! $this->request->param('fm_tinymce'))
        {
            // Filemanager is NOT rendered in TinyMCE popup window
            $view->caption = 'Файловый менеджер';
        }

        $view->content = $this->widget_files();

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
	}

    /**
     * Create new directory
     */
    public function action_mkdir()
    {
        try {
            $this->setup_root_path();

            $file = new Model_File();

            // Create in current directory
            $file->relative_dir_name = URL::decode($this->request->param('fm_path'));

            $form = new Form_Backend_File(TRUE, TRUE);

            if (Request::$method == 'POST')
            {
                // Add values from _POST to form
                $data = $form->get_values();

                // Validate form and file
                if ($form->validate() && $file->validate_mkdir($data))
                {
                    // Make dir
                    $file->values($data);
                    if ($file->do_mkdir($data))
                    {
                        // Directory was created succesfully
                        $this->request->redirect(URL::uri_back());
                    }
                }

                // Add model errors to form
                $form->errors($file->errors());
            }
        }
        catch (Filemanager_Exception $e)
        {
            // An error occured
            $form = new Form_Backend_Error($e->getMessage());
        }

        $view = new View('backend/form');
        $view->caption = 'Создание директории';
        $view->form = $form;

        // Display flash message
        if (isset($_GET['flash']) && (string) $_GET['flash'] == 'ok')
        {
            $form->message('Директория создана успешно');
        }

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }

    /**
     * Rename file/dir
     */
    public function action_rename()
    {
        try {
            $this->setup_root_path();

            $file = new Model_File();

            $file->relative_path = URL::decode($this->request->param('fm_path'));

            if ( ! $file->file_exists())
            {
                // Specified path doesn't exists or access is denied
                throw new Filemanager_Exception('Файл "' . $file->path . '" не найден!');
            }

            if ( ! $file->is_relative())
            {
                // $file->path is outside root path
                throw new Filemanager_Exception('Путь "' . $file->path . '" находится вне корневой директории "' . $file->root_path . '"!');
            }


            $form = new Form_Backend_File($file->is_dir());

            // Set default values from model
            $form->set_defaults(array(
                'relative_dir_name'  => $file->relative_dir_name,
                'base_name'          => $file->base_name
            ));

            if ($form->is_submitted())
            {
                $data = $form->get_values();

                // Validate form and file
                if ($form->validate() && $file->validate_rename($data))
                {
                    // Rename file
                    if ($file->do_rename($data))
                    {
                        // Renaming succeded
                        $this->request->redirect(URL::uri_back());
                    }
                }

                // Get model errors
                $form->errors($file->errors());
            }
        }
        catch (Filemanager_Exception $e)
        {
            // An error occured
            $form = new Form_Backend_Error($e->getMessage());
        }

        $view = new View('backend/form');
        $view->caption = 'Изменение имени ' . ($file->is_dir() ? 'директории' : 'файла');
        $view->form = $form;

        // Display flash message
        if (isset($_GET['flash']) && (string) $_GET['flash'] == 'ok')
        {
            $form->message('Имя файла изменено');
        }

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }

    /**
     * Delete file/dir
     */
    public function action_delete()
    {
        try {
            $this->setup_root_path();

            $file = new Model_File();

            $file->relative_path = URL::decode($this->request->param('fm_path'));

            if ( ! $file->file_exists())
            {
                // Specified path doesn't exists or access is denied
                throw new Filemanager_Exception('Файл "' . $file->path . '" не найден!');
            }

            $errors = array();

            if (Request::$method == 'POST')
            {
                if ($file->validate_delete())
                {
                    // Delete file/dir
                    if ($file->do_delete())
                    {
                        // Deletion succeded, redirect back
                        $this->request->redirect( URL::uri_back());
                    }
                }

                // Add model errors
                $errors = $file->get_errors();
            }

            // Display form with request for delete confirmation
            $form = new Form_Backend_Confirm('Удалить ' . ($file->is_dir() ? 'директорию' : 'файл') .' "' . $file->relative_path . '"?');

            // Add errors to form
            if (!empty($errors))
            {
                $form->add_errors(array_values($errors));
            }
        }
        catch (Filemanager_Exception $e)
        {
            // An error occured
            $form = new Form_Backend_Error($e->getMessage());
        }

        $view = new View('backend/form');
        $view->caption = 'Удаление ' . ($file->is_dir() ? 'директории' : 'файла');
        $view->form = $form;

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }

    /**
     * Edit file contents
     */
    public function action_edit()
    {
        try {
            $this->setup_root_path();

            $file = new Model_File();

            $file->relative_path = URL::decode($this->request->param('fm_path'));

            if ( ! $file->file_exists())
            {
                // Specified path doesn't exists or access is denied
                throw new Filemanager_Exception('Файл "' . $file->relative_path . '" не найден!');
            }

            if ($file->is_dir())
            {
                throw new Filemanager_Exception('Путь "' . $file->relative_path . '" является директорией!');
            }

            $form = new Form_Backend_EditFile();

            // Set default values from model
            $form->set_defaults(array(
                'content' => $file->content,
            ));

            if ($form->is_submitted())
            {
                // Validate form and file
                if ($form->validate() && $file->validate_save())
                {
                    $file->values($form->get_values());

                    // Save file contents
                    if ($file->save())
                    {
                        // Renaming succeded
                        $this->request->redirect(Request::current()->uri . '?flash=ok');
                    }
                }

                // Get model errors
                $form->add_errors($file->get_errors());
            }
        }
        catch (Filemanager_Exception $e)
        {
            // An error occured
            $form = new Form_Backend_Error($e->getMessage());
        }

        $view = new View('backend/form');
        $view->caption = 'Редактирование файла ' . $file->base_name;
        $view->form = $form;

        // Display flash message
        if (isset($_GET['flash']) && (string) $_GET['flash'] == 'ok')
        {
            $form->message('Файл сохранён успешно!');
        }

        $layout = $this->prepare_layout();
        $layout->content = $view;
        $this->request->response = $layout->render();
    }

    /**
     * Render list of files
     *
     * @return <type>
     */
    /**
     *
     * @param string $root_path Root path. NULL means that root path
     * @return <type>
     */
    public function widget_files()
    {
        try {
            $this->setup_root_path();

            $dir = new Model_File();

            $dir->relative_path = URL::decode($this->request->param('fm_path'));

            if ( ! $dir->is_dir())
            {
                throw new Filemanager_Exception('Путь "' . $dir->path . '" не является директорией!');
            }

            if ( ! $dir->is_relative())
            {
                // $dir->path is outside root path
                throw new Filemanager_Exception('Путь "' . $dir->path . '" находится вне корневой директории "' . $dir->root_path . '"!');
            }

            if ($dir->is_hidden())
            {
                // Don't allow viewing hidden dirs
                throw new Filemanager_Exception('Директория ' . $dir->relative_path . ' является скрытой!');
            }

            // ----- Obtain sorted list of files in directory
            $files = $dir->get_files();
            if ($files === NULL)
            {
                // An error occured while retrieving files in directory
                throw new Filemanager_Exception($dir->get_last_error());
            }

            // ----- Create thumbnails for images (silently ignore all errors)
            $dir->create_preview_thumbs($files);

            // Select appropriate view script to render list of files
            $list_style = $this->request->param('fm_style');
            switch ($list_style)
            {
                case 'thumbs': case 'list':
                    break;

                default:
                    $list_style = 'list';
            }

            $view = new View('backend/files');

            $view->file_upload = $this->widget_file_upload();

            $view->dir = $dir;
            $view->files = $files;

            $view->list_style = $list_style;

            $view->in_tinymce = $this->request->param('fm_tinymce');
            $view->root       = $this->request->param('fm_root');

            return $view;
        }
        catch (Filemanager_Exception $e)
        {
            return $this->_widget_error($e->getMessage());
        }
    }

    /**
     * Render file upload form, handle uploads
     *
     * @return string
     */
    public function widget_file_upload()
    {
        try {
            $file = new Model_File();

            // Upload to current directory
            $file->relative_dir_name = URL::decode($this->request->param('fm_path'));

            $file->uploaded_file = 'uploaded_file';

            //$form = new Form_Backend_ImageUpload();
            $form = new Form_Backend_FileUpload();

            if ($form->is_submitted())
            {
                $data = $form->get_values();

                // Validate form and file
                if ($form->validate() && $file->validate_upload($data))
                {
                    // Upload file
                    if ($file->do_upload($data))
                    {
                        // Uploading succeeded
                        $this->request->redirect(Request::current()->uri . '?flash=ok');
                    }
                }

                // Add model errors to form
                $form->errors($file->errors());
            }
        }
        catch (Filemanager_Exception $e)
        {
            return $this->_widget_error($e->getMessage());
        }

        if (isset($_GET['flash']) && $_GET['flash'] == 'ok')
        {
            $form->message('Файл загружен успешно!');
        }

        return $form;
    }

    /**
     * Set up layout for actions
     *
     * @return View
     */
    public function prepare_layout($layout_script = NULL)
    {
        $in_tinymce = $this->request->param('fm_tinymce');
        if ($in_tinymce)
        {
            // Filebrowser is opened in tinyMCE popup window
            $layout_script = 'layouts/backend/filemanager_tinymce';
        }

        $layout = parent::prepare_layout($layout_script);
        $layout->add_style(Modules::uri('filemanager') . '/public/css/backend/filemanager.css');

        return $layout;
    }

    /**
     * Set up file root path for action
     */
    public function setup_root_path()
    {
        switch ($this->request->param('fm_root'))
        {
            case 'css':
                Model_File::set_root_path(File::normalize_path(Kohana::config('filemanager.css_root_path')));
                break;

            case 'js':
                Model_File::set_root_path(File::normalize_path(Kohana::config('filemanager.js_root_path')));
                break;

            case 'templates':
                Model_File::set_root_path(File::normalize_path(Kohana::config('filemanager.templates_root_path')));
                break;

            default:
                Model_File::set_root_path(File::normalize_path(Kohana::config('filemanager.files_root_path')));
        }
    }
}
