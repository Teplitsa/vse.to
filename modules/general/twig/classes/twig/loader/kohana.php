<?php defined('SYSPATH') or die('No direct script access.');

/**
 * A Twig template loader with support of kohana transparent file system
 */
class Twig_Loader_Kohana implements Twig_LoaderInterface
{

    /**
     * Gets the source code of a template, given its name.
     *
     * @param  string $name string The name of the template to load
     * @return string The template source code
     */
    public function getSource($name)
    {
        return file_get_contents($this->findTemplate($name));
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param  string $name string The name of the template to load
     * @return string The cache key
     */
    public function getCacheKey($name)
    {
        return $this->findTemplate($name);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     */
    public function isFresh($name, $time)
    {
        return filemtime($this->findTemplate($name)) < $time;
    }

    /**
     * Find a template file with given name by using Kohana::find_file.
     *
     * @param  string $name
     * @return string
     */
    protected function findTemplate($name)
    {
        $path = Kohana::find_file('templates', $name, 'html');

        if ($path === FALSE)
        {
            throw new RuntimeException(sprintf('Unable to find template "%s".', "$name.html"));
        }

        return $path;
    }
}