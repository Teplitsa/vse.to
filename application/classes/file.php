<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * File helper class.
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class File extends Kohana_File {

    const SORT_BY_FILENAME = 'SORT_BY_FILENAME';
    const SORT_BY_EXT      = 'SORT_BY_EXT';

    /**
     * Concat file names
     *
     * @return string
     */
    public static function concat()
    {
        $result = '';

        $paths = func_get_args();

        foreach ($paths as $path)
        {
            if ($path === '/')
            {
                $result .= $path;
            }
            elseif ($path != '')
            {
                $result .= rtrim($path, '/\\') . '/';
            }
        }

        return File::normalize_path($result);
    }

    public static function normalize_path($path)
    {
        // Convert all backslashes to forward slashes
        $path = str_replace('\\', '/', $path);

        // Remove duplicate forward slashes
        $path = str_replace('//', '/', $path);

        if ($path !== '/')
        {
            // Trim right forward slashes
            $path = rtrim($path, '/');
        }

        return $path;
    }


    public static function real_path($path)
    {
        $path = realpath($path);
        if ($path == FALSE)
        {
            return NULL;
        }

        return File::normalize_path($path);
    }


    /**
     * Return part of $path relative to $base_path or FALSE if $path is outside $base_path
     *
     * @param string $path
     * @param string $base_path
     * @return string on success
     * @return boolean FALSE if $path is outside $base_path
     */
    public static function relative_path($path, $base_path)
    {
        $path      = File::normalize_path($path);
        $base_path = File::normalize_path($base_path);

        if ($base_path === '')
        {
            // Base path is empty
            return $path;
        }

        if (strpos($path, $base_path) !== 0)
        {
            // $path is outside $base_path
            return NULL;
        }

        $relative_path = substr($path, strlen($base_path));
        $relative_path = ltrim($relative_path, '/\\');
        return $relative_path;
    }

    /**
     * If $path is inside DOCROOT - return uri to file
     *
     * @param  string $path
     * @return string
     */
    public static function uri($path)
    {
        $relative_path = File::relative_path($path, DOCROOT);
        if ($relative_path === NULL)
        {
            return NULL;
        }

        return $relative_path;
    }

    /**
     * If $path is inside DOCROOT - return uri to file
     *
     * @param  string $path
     * @return string
     */
    public static function url($path)
    {
        $url = File::uri($path);
        if ($url !== NULL)
        {
            $url = URL::base() . $url;
        }
        return $url;
    }

    /**
     * Sort files (directories are always above files)
     *
     * @param array $files
     * @param integer $sort_by
     */
    public static function sort(array & $files, $sort_by = File::SORT_BY_FILENAME)
    {
        usort($files, array('File', '_sort_by_filename'));
    }

    /**
     * Compare files in a way to make directories be always on top
     *
     * @param array $file1
     * @param array $file2
     * @return integer 1 if $file1 > $file 2, 0 if $file1 = $file2, -1 if $file1 < $file2
     */
    private static function _sort_by_filename($file1, $file2)
    {
        if ($file1['is_dir'] && ! $file2['is_dir'])
        {
            return -1;
        }
        elseif ( ! $file1['is_dir'] && $file2['is_dir'])
        {
            return 1;
        }
        else
        {
            if ($file1['base_name'] === $file2['base_name'])
            {
                return 0;
            }
            else
            {
                return ($file1['base_name'] > $file2['base_name']) ? 1 : -1;
            }
        }
    }

    /**
     * Delete directory recursively
     *
     * @param string $dir
     * @return boolean
     */
    public static function delete($dir)
    {
        if ( ! file_exists($dir))
        {
            return TRUE;
        }

        if ( ! is_dir($dir) || is_link($dir))
        {
            // $dir is a file or a sym link
            return unlink($dir);
        }

        $files = scandir($dir);
        if ($files === FALSE)
        {
            return FALSE;
        }

        // Delete all subfiles and subfolders
        foreach ($files as $file)
        {
            if ($file == '.' || $file == '..')
            {
                // Skip pointers
                continue;
            }

            $file = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($file))
            {
                // Recursively delete subdirectory
                File::delete($file);
            }
            else
            {
                unlink($file);
            }
        }

        // Delete the dir itself
        return rmdir($dir);
    }

    /**
     * Move uploaded file to specified path or to the temporary location in application tmp dir
     * and return the new path
     *
     * @param  array $file Information about uploaded file (entry from $_FILES)
     * @return string
     */
    public static function upload(array $file, $path = NULL)
    {
        if ( ! is_writable(TMPPATH))
        {
            throw new Kohana_Exception('Temporary directory "' . TMPPATH . '" is not writable');
        }

        if ($path === NULL)
        {
            // Generate new temporary file name
            do {
                $path = TMPPATH . Text::random();
            }
            while (is_file($path));
        }

        if ( ! @move_uploaded_file($file['tmp_name'], $path))
        {
            throw new Kohana_Exception('Failed to move uploaded file!');
        }

        @chmod($path, 0666);
        
        return $path;
    }
}
