<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract base class for a task, which is supposed to grap info from web sites
 */
abstract class Task_Import_Web extends Task
{
    /**
     * Temporary directory to store downloaded images and other files
     * @var string
     */
    protected $_tmp_dir;

    /**
     * Base url, used by _get method
     * @var string
     */
    protected $_base_url;

    /**
     * Construct task
     *
     * @param string $base_url
     * @param string $tmp_dir
     */
    public function  __construct($base_url, $tmp_dir = NULL)
    {
        parent::__construct();

        $this->_base_url = $base_url;

        if ($tmp_dir === NULL)
        {
            // Build tmp directory from class name
            $tmp_dir = strtolower(get_class($this));
            if (strpos($tmp_dir, 'task_')   === 0) $tmp_dir = substr($tmp_dir, strlen('task_'));

            $tmp_dir = TMPPATH . '/' . $tmp_dir;
        }
        if ( ! is_dir($tmp_dir))
        {
            mkdir($tmp_dir, 0777);
        }
        $this->_tmp_dir = $tmp_dir;
    }

    /**
     * Set/get base url
     *
     * @param  string $base_url
     * @return string
     */
    public function base_url($base_url = NULL)
    {
        if ($base_url !== NULL)
        {
            $this->_base_url = trim($base_url, ' /\\');
        }
        return $this->_base_url;
    }

    /**
     * Decode html entities and trim whitespaces (including non-breakable) from string
     *
     * @param  string $encoded
     * @param  integer $maxlength
     * @return string
     */
    public function decode_trim($encoded, $maxlength = 0)
    {
        $encoded = html_entity_decode($encoded);
        $encoded = trim($encoded, " \t\n\r\0\x0B\xA0");

        if ($maxlength)
        {
            $encoded = UTF8::substr($encoded, 0, $maxlength);
        }

        return $encoded;
    }


    /**
     * GET the contencts of the specified url
     *
     * @param string $url
     * @param boolean iconv Convert the result to UTF-8
     */
    public function get($url, $iconv = TRUE)
    {
        static $ch;
        if ($ch === NULL)
        {
            $ch = curl_init();
        }

        if ($url[0] == '/')
        {
            $url = $this->_base_url . $url;
        }
        $url = str_replace(' ', '%20', $url);

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); //timeout in seconds

        /*
        curl_setopt($ch, CURLOPT_PROXY, '192.168.20.99');
        curl_setopt($ch, CURLOPT_PROXYPORT, '8080');
         */

        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check if any error occured
        if(curl_errno($ch))
        {
            throw new Kohana_Exception('cURL : ' . curl_error($ch));
        }

        //curl_close($ch);

        if (($code == 200) && ($response !== FALSE))
        {
            if ($iconv)
            {
                // Try to detect character set from <meta> header
                $encoding = '';
                if (preg_match('!<meta[^>]*charset=([\w-]+)!i', $response, $matches))
                {
                    $encoding = $matches[1];
                    $response = iconv($encoding, 'UTF-8', $response);
                }
                else
                {
                    throw new Kohana_Exception('Unable to detect response encoding ... ');
                }
            }
            return $response;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Download the file pointed by url to the specified path
     * If $local_path is not specified, than it's obtained from
     * the
     *
     * @param  string $url
     * @param  string $local_path
     * @return full path to downloaded file | FALSE on failure
     */
    public function download($url, $local_path = NULL)
    {
        $data = $this->get($url, FALSE);

        if ($data)
        {
            if ( ! isset($local_path))
            {
                $local_path = TMPPATH . '/' . basename($url);
            }

            file_put_contents($local_path, $data);
            return $local_path;
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Download file only if local file with the same name doesn't exist or is not readable
     *
     * @param  string $url
     * @param  string $local_path
     * @return full path to downloaded file | FALSE on failure
     */
    public function download_cached($url, $local_path = NULL)
    {
        if ( ! isset($local_path))
        {
            $local_path = $this->_tmp_dir . '/' . basename($url);
        }

        if (is_readable($local_path))
            return $local_path;

        return $this->download($url, $local_path);
    }
}