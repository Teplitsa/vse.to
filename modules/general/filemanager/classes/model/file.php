<?php

class Model_File extends Model
{
    protected static $_root_path = SYSPATH;

    /**
     * Set root path for all file operations
     *
     * @param string $root_path
     */
    public static function set_root_path($root_path)
    {
        self::$_root_path = $root_path;
    }



    /**************************************************************************
     * File path
     **************************************************************************/

    /**
     * Full file path is constructed of two parts:
     *
     *  path = dirname . basename
     *
     *  Base name consists of file name and file extension:
     *  basename = filename . "." .ext
     */

    /**
     * Clear cached variables if path has changed
     */
    protected function _clear_path_cache()
    {
        $this->_properties['real_path'] = NULL;
        $this->_properties['directory'] = NULL;
    }

    /**
     * @param string $path
     */
    public function set_path($path)
    {
        $pathinfo = pathinfo($path);

        $this->_properties['dir_name']  = $pathinfo['dirname'];
        $this->_properties['file_name'] = $pathinfo['filename'];
        $this->_properties['ext']       = isset($pathinfo['extension']) ? $pathinfo['extension'] : NULL;

        $this->_clear_path_cache();
    }

    /**
     * @return string
     */
    public function get_path()
    {
        return File::concat($this->dir_name, $this->base_name);
    }

    /**
     * @param string $base_name
     */
    public function set_base_name($base_name)
    {
        $pathinfo = pathinfo($base_name);

        $this->_properties['file_name'] = $pathinfo['filename'];
        $this->_properties['ext']       = isset($pathinfo['extension']) ? $pathinfo['extension'] : NULL;

        $this->_clear_path_cache();
    }

    /**
     * @return string
     */
    public function get_base_name()
    {
        if ($this->ext !== NULL)
        {
            return $this->file_name . "." . $this->ext;
        }
        else
        {
            return $this->file_name;
        }
    }

    /**
     * @param string $relative_path
     */
    public function set_relative_path($relative_path)
    {
        $this->path = File::concat($this->root_path, $relative_path);
    }

    /**
     * @return string
     */
    public function get_relative_path()
    {
        return File::relative_path($this->path, $this->root_path);
    }


    /**
     * @return string
     */
    public function set_relative_dir_name($relative_dir_name)
    {
        $this->_properties['dir_name'] = File::concat($this->root_path, $relative_dir_name);

        $this->_clear_path_cache();
    }

    /**
     * @return string
     */
    public function get_relative_dir_name()
    {
        return File::relative_path($this->dir_name, $this->root_path);
    }

    /**
     * @return string
     */
    public function get_real_path()
    {
        //if ( ! isset($this->_properties['real_path']))
        {
            $this->_properties['real_path'] = realpath($this->path);
        }
        return $this->_properties['real_path'];
    }

    /**
     * @return string
     */
    public function get_root_path()
    {
        return self::$_root_path;
    }

    /**
     * Is file relative to root path?
     *
     * @return boolean
     */
    public function is_relative()
    {
        return (File::relative_path($this->real_path, $this->root_path) !== NULL);
    }

    /**
     * String representation - full file path
     *
     * @return string
     */
    public function  __toString()
    {
        return $this->path;
    }

    /**************************************************************************
     * Basic file functions
     **************************************************************************/
    /**
     * @return boolean
     */
    public function file_exists()
    {
        return file_exists($this->path);
    }

    /**
     * @return boolean
     */
    public function is_dir()
    {
        return is_dir($this->path);
    }

    /**
     * @return boolean
     */
    public function is_file()
    {
        return is_file($this->path);
    }

    /**
     * @return boolean
     */
    public function is_readable()
    {
        return is_readable($this->path);
    }

    /**
     * @return boolean
     */
    public function is_writeable()
    {
        return is_writable($this->path);
    }

    /**
     * File is . or ..
     *
     * @return boolean
     */
    public function is_dot()
    {
        return ($this->base_name == '.' || $this->base_name == '..');
    }

    /**
     * Hide image thumbs directory and . with ..
     *
     * @return boolean
     */
    public function is_hidden()
    {
        return ( ! $this->is_dot() && strpos($this->base_name, '.') === 0);
    }

    /**
     * Get file contents
     *
     * @return string
     */
    public function get_content()
    {
        if ( ! isset($this->_properties['content']))
        {
            if ($this->is_dir())
            {
                $this->_properties['content'] = NULL;
            }
            else
            {
                $this->_properties['content'] = file_get_contents($this->real_path);
            }
        }

        return $this->_properties['content'];
    }

    /**
     * Copy current file to new path
     *
     * @param  string $new_path
     * @return boolean
     */
    public function copy($new_path)
    {
        try {
            copy($this->path, (string) $new_path);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Move current file to new path
     *
     * @param  string $new_path
     * @return boolean
     */
    public function move($new_path)
    {
        try {
            rename($this->path, (string) $new_path);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Delete file or directory.
     * Directories are deleted recursively.
     *
     * @return boolean
     */
    public function delete()
    {
        try {
            File::delete($this->real_path);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Create directory from this model
     *
     * @return boolean
     */
    public function mkdir()
    {
        try {
            mkdir($this->path);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Upload file to current path from $_FILES[$file_key]
     *
     * @param  string $file_key Index to the $_FILES array
     * @return boolean
     */
    public function upload($file_key)
    {
        // Use "@" to suppress stupid open_basedir aware warnings about temporary source
        if ( ! @move_uploaded_file($_FILES[$file_key]['tmp_name'], $this->path))
        {
            $this->error('Не удалось переместить временный файл!');
            return FALSE;
        }
        return TRUE;
    }

    /***************************************************************************
     * Advanced file functions
     **************************************************************************/
    /**
     * Return the parent directory as object
     *
     * @return Model_File
     */
    public function get_directory()
    {
        if ( ! isset($this->_properties['directory']))
        {
            $this->_properties['directory'] = new Model_File();
            $this->_properties['directory']->path = $this->dir_name;
        }
        return $this->_properties['directory'];
    }

    /**
     * Get list of files in a directory
     *
     * @return array on success
     * @return null on failure
     */
    public function get_files()
    {
        if ( ! $this->is_dir())
        {
            $this->error('Файл "' . $this->relative_path . '" не является директорией!');
            return NULL;
        }

        // Try readinig files in directory
        try {
            $h = opendir($this->real_path);

            if ( ! $h)
            {
                // Failed to open directory
                $this->error('Не удалось прочитать директорию "' . $this->relative_path . '"');
                return NULL;
            }

            $files = array();
            while (($base_name = readdir($h)) !== FALSE)
            {
                $file_info = array();
                $file_info['base_name'] = $base_name;
                $file_info['path']      = File::concat($this->path, $base_name);
                $file_info['is_dir']    = is_dir($file_info['path']); // Necessary for sorting files

                $files[] = $file_info;
            }

            closedir($h);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return NULL;
        }

        // Sort files
        File::sort($files);

        return $files;
    }

    /**************************************************************************
     * Actions
     **************************************************************************/
    /**
     * Validate new directory creation
     *
     * @return boolean
     */
    public function validate_mkdir($newvalues)
    {
        if ($this->dir_name === NULL)
        {
            $this->error('Не указана родительская директория!', 'base_name');
            return FALSE;
        }

        if ( ! isset($newvalues['base_name']))
        {
            $this->error('Не указано имя директории!', 'base_name');
            return FALSE;
        }

        $new_base_name = $newvalues['base_name'];

        $tst_dir = clone $this;

        $tst_dir->base_name = $new_base_name;

        if ( ! $tst_dir->directory->is_dir())
        {
            $this->error('Путь "' . $tst_dir->relative_dir_name . '" не существует или не является директорией!', 'base_name');
            return FALSE;
        }

        if ( ! $tst_dir->directory->is_relative())
        {
            // Directory part of new name is outside root path
            $this->error('Имя директории "' . $tst_dir->path .'" находится вне корневой директории "' . $this->root_path . '"!', 'base_name');
            return FALSE;
        }

        if ($tst_dir->file_exists())
        {
            // Such file/dir already exists
            $this->error('Файл с именем "' . $tst_dir->relative_path . '" уже существует!', 'base_name');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Create new directory
     *
     * @return boolean
     */
    public function do_mkdir($newvalues)
    {
        $this->base_name = $newvalues['base_name'];

        return $this->mkdir();
    }

    /**
     * Validate renaming of file to new base name
     *
     * @param  array $newvalues
     * @return boolean
     */
    public function validate_rename($newvalues)
    {
        if ( ! isset($newvalues['base_name']))
        {
            $this->error('Вы не указали имя файла!');
            return FALSE;
        }

        if ( ! isset($newvalues['relative_dir_name']))
        {
            $this->error('Вы не указали имя директории для файла!');
            return FALSE;
        }

        $tst_file = clone $this;

        $tst_file->relative_dir_name = $newvalues['relative_dir_name'];
        $tst_file->base_name         = $newvalues['base_name'];

        if ( ! $tst_file->directory->is_dir())
        {
            $this->error('Папка "' . $tst_file->relative_dir_name . ' не существует!');
            return FALSE;
        }

        if ($tst_file->directory->is_hidden())
        {
            $this->error('Папка "' . $tst_file->relative_dir_name . ' является скрытой!');
            return FALSE;
        }

        if ( ! $tst_file->directory->is_relative())
        {
            // Directory part of new name is outside root path
            $this->error('Новое имя файла "' . $tst_file->path . '" находится вне корневой директории!');
            return FALSE;
        }

        if ($tst_file->real_path === $this->real_path)
        {
            // New path is the same as old one
            return TRUE;
        }

        if ($tst_file->file_exists())
        {
            // Such file/dir already exists
            $this->error('Файл с именем "' . $tst_file->relative_path . '" уже существует!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Rename file
     *
     * @param  srray $nevalues
     * @return boolean
     */
    public function do_rename($newvalues)
    {
        $new_file = clone $this;
        $new_file->relative_dir_name = $newvalues['relative_dir_name'];
        $new_file->base_name         = $newvalues['base_name'];

        try {
            if ($this->is_image())
            {
                if ($new_file->is_image())
                {
                    // Move thumbs together with the image
                    $this->rename_thumbs($new_file);
                }
                else
                {
                    // Delete thumbs
                    $this->delete_thumbs();
                }
            }
        }
        catch (Exception $e)
        {}

        return $this->move($new_file);
    }

    /**
     * Is it ok to delete a file?
     *
     * @param  $newvalues Additional values
     * @return boolean
     */
    public function validate_delete(array $newvalues = NULL)
    {
        if ( ! $this->is_relative())
        {
            // File is outside root path
            $this->error('Файл "' . $this->path . '" находится вне корневой директории "' . $this->root_path . '"!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Delete file or directory.
     * Directory is deleted recursively.
     *
     * @return Model_File
     */
    public function do_delete()
    {
        try {
            if ($this->is_image())
            {
                // Delete thumbs
                $this->delete_thumbs();
            }
        }
        catch (Exception $e)
        {}

        return $this->delete();
    }

    /**
     * Validate file upload
     * $this->uploaded_file property must be set to key in $_FILES array
     *
     * @return boolean
     */
    public function validate_upload(array $newvalues = NULL)
    {
        if ( ! isset($newvalues['uploaded_file']))
        {
            $this->error('Файл не был загружен', 'uploaded_file');
            return FALSE;
        }

        if ($newvalues['uploaded_file']['error'] != UPLOAD_ERR_OK)
        {
            $this->error('Произошла ошибка при загрузке файла!', 'uploaded_file');
            return FALSE;
        }

        if ($this->dir_name === NULL)
        {
            $this->error('Не указана директория для файла!', 'uploaded_file');
            return FALSE;
        }

        $tst_file = clone $this;

        $tst_file->base_name = $newvalues['uploaded_file']['name'];

        if ( ! $tst_file->directory->is_dir())
        {
            $this->error('Директория "' . $tst_file->relative_dir_name . '" не существует', 'uploaded_file');
        }

        if ( ! $tst_file->directory->is_relative())
        {
            // Directory part of new name is outside root path
            $this->error('Новое имя файла "' . $tst_file->path . '" находится вне корневой директории "' . $this->root_path . '"!', 'uploaded_file');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * @param  array $newvalues
     * @return boolean
     */
    public function do_upload($newvalues)
    {
        $this->base_name = $newvalues['uploaded_file']['name'];

        // Upload
        try {
            File::upload($newvalues['uploaded_file'], $this->path);
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage(), 'uploaded_file');
            return FALSE;
        }

        // Create thumbs & resize image if necessary
        try {
            if ($this->is_image())
            {
                if ( ! empty($newvalues['enable_popups']))
                {
                    // Create image for popup
                    $popups_config = Kohana::config('filemanager.thumbs.popups');
                    $this->create_thumb($popups_config['width'], $popups_config['height'], $popups_config['dir_base_name']);
                }

                if ( ! empty($newvalues['resize_image']))
                {
                    $this->resize_image($newvalues['width'], $newvalues['height']);
                }
            }
        }
        catch (Exception $e)
        {
            $this->error($e->getMessage());
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Validate saving file contents
     *
     * @return boolean
     */
    public function validate_save()
    {
        if ($this->is_dir())
        {
            $this->error('Указанный путь "' . $this->relative_path . '" является директорией!');
            return FALSE;
        }

        if ($this->file_exists() && ! $this->is_writeable())
        {
            $this->error('Доступ к файлу "' . $this->relative_path . '" на запись запрещён!');
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Save file contents
     */
    public function save($force_create = FALSE)
    {
        file_put_contents($this->path, $this->content);

        return TRUE;
    }


    /**************************************************************************
     * Image & thumbs functions
     **************************************************************************/
    /**
     * @return boolean
     */
    public function is_image()
    {
        switch (strtolower($this->ext))
        {
            case 'jpg': case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
                return TRUE;

            default:
                return FALSE;
        }
    }

    /**
     * Create all thumbs for current image
     */
    public function create_thumbs()
    {
        foreach (Kohana::config('filemanager.thumbs') as $config)
        {
            if ($config['enable'])
            {
                $this->create_thumb($config['width'], $config['height'], $config['dir_base_name']);
            }
        }
    }

    /**
     * Rename all thumbs for current image being renamed
     *
     * @param Model_File $new_file New name of the image
     */
    public function rename_thumbs(Model_File $new_file)
    {
        foreach (Kohana::config('filemanager.thumbs') as $config)
        {
            if ($config['enable'])
            {
                $this->rename_thumb($new_file, $config['dir_base_name']);
            }
        }
    }

    /**
     * Delete all thumbs for current image
     */
    public function delete_thumbs()
    {
        foreach (Kohana::config('filemanager.thumbs') as $config)
        {
            if ($config['enable'])
            {
                $this->delete_thumb($config['dir_base_name']);
            }
        }
    }

    /**
     * Create a thumbnail for current image.
     * Thumbnail will be placed in $dir_base_name directory (relative to current directory)
     *
     * @param  string $width
     * @param  string $height
     * @param  string|Model_File $dir_base_name
     * @param  boolean $force Create thumb even if it already exists
     * @return boolean
     */
    public function create_thumb($width, $height, $dir_base_name, $force = TRUE)
    {
        if ( ! ($dir_base_name instanceof Model_File))
        {
            $thumb_dir = new Model_File();
            $thumb_dir->dir_name = $this->dir_name;
            $thumb_dir->base_name = $dir_base_name;

            if ( ! $thumb_dir->is_dir())
            {
                $thumb_dir->mkdir();
            }
        }
        else
        {
            $thumb_dir = $dir_base_name;
        }

        $thumb_file = new Model_File();
        $thumb_file->dir_name  = $thumb_dir->path;
        // Thumb image has the same file name
        $thumb_file->base_name = $this->base_name;

        // Thumb already exists
        if (! $force && $thumb_file->is_file())
            return FALSE;

        $thumb = new Image_GD($this->path);
        $thumb->thumbnail($width, $height);
        $thumb->save($thumb_file->path);

        return TRUE;
    }

    /**
     * Rename thumb together with the current image
     *
     * @param Model_File $new_file New file for the current image
     * @param  string $dir_base_name
     * @return boolean
     */
    public function rename_thumb(Model_File $new_file, $dir_base_name)
    {
        $thumb_dir = new Model_File();
        $thumb_dir->dir_name = $this->dir_name;
        $thumb_dir->base_name = $dir_base_name;

        // Thums directory doesn't exist
        if ( ! $thumb_dir->is_dir())
            return FALSE;

        $thumb_file = new Model_File();
        $thumb_file->dir_name  = $thumb_dir->path;
        // Thumb image has the same file name
        $thumb_file->base_name = $this->base_name;

        // No such thumb
        if ( ! $thumb_file->is_file())
            return FALSE;

        // ----- Move thumb to new location

        // Create new thumb directory
        $new_thumb_dir = new Model_File();
        $new_thumb_dir->dir_name = $new_file->dir_name;
        $new_thumb_dir->base_name = $dir_base_name;
        if ( ! $new_thumb_dir->is_dir())
        {
            $new_thumb_dir->mkdir();
        }

        $new_thumb_file = new Model_File();
        $new_thumb_file->dir_name  = $new_thumb_dir->path;
        $new_thumb_file->base_name = $new_file->base_name;

        // Move thumb
        $thumb_file->move($new_thumb_file->path);
    }

    /**
     * Delete thumb for current image
     *
     * @param  string $dir_base_name
     * @return boolean
     */
    public function delete_thumb($dir_base_name)
    {
        $thumb_dir = new Model_File();
        $thumb_dir->dir_name = $this->dir_name;
        $thumb_dir->base_name = $dir_base_name;

        // Thums directory doesn't exist
        if ( ! $thumb_dir->is_dir())
            return FALSE;

        $thumb_file = new Model_File();
        $thumb_file->dir_name  = $thumb_dir->path;
        // Thumb image has the same file name
        $thumb_file->base_name = $this->base_name;

        // No such thumb
        if ( ! $thumb_file->is_file())
            return FALSE;

        return $thumb_file->delete();
    }

    public function get_thumb_path($config_name)
    {
        $config = Kohana::config('filemanager.thumbs.' . $config_name);

        $thumb_file_name = File::concat($this->dir_name, $config['dir_base_name'], $this->base_name);
        if (is_file($thumb_file_name))
        {
            return $thumb_file_name;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Resize current image
     *
     * @param integer $width
     * @param integer $height
     */
    public function resize_image($width, $height)
    {
        $image = new Image_GD($this->path);
        $image->thumbnail($width, $height);
        $image->save($this->path);
    }

    /**
     * Create preview thumbs for images among specified files
     * Silently...
     *
     * @param array $files
     */
    public function create_preview_thumbs($files)
    {
        try {
            $thumbs_config = Kohana::config('filemanager.thumbs.preview');

            $preview_dir_created = FALSE;

            $count = 0;

            $thumbs_dir = new Model_File();
            $file = new Model_File();

            foreach ($files as $properties)
            {
                $file->init();
                $file->path = $properties['path'];

                if ($file->is_image())
                {
                    // Create directory for thumbs, if not already created
                    if ( ! $preview_dir_created)
                    {
                        $thumbs_dir->dir_name = $file->dir_name;
                        $thumbs_dir->base_name = $thumbs_config['dir_base_name'];
                        if ( ! $thumbs_dir->is_dir())
                        {
                            $thumbs_dir->mkdir();
                        }

                        // Creation of thumbs directory was succesfull
                        $preview_dir_created = TRUE;
                    }

                    //@TODO: Limit number of created thumbs per request!

                    // Create thumb (ignore failures)
                    if ($file->create_thumb($thumbs_config['width'], $thumbs_config['height'], $thumbs_dir, FALSE))
                    {
                        $count++;
                    }
                }

                // Limit the number of created thumbs per one request to 100
                if ($count >= 100)
                    break;
            }
        }
        catch (Exception $e)
        {
            return FALSE;
        }

        return TRUE;
    }
}