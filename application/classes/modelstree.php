<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tree of models
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
abstract class ModelsTree extends Models
{
    /**
     * @var Model
     */
    protected $_root;
    
    /**
     * Construct container from array of properties
     *
     * @param string $model_class
     * @param array  $properties_array
     * @param string $key name of the primary key field
     * @param Model  $root
     */
    public function  __construct($model_class, array $properties_array, $pk = FALSE, $root = NULL)
    {
        parent::__construct($model_class, $properties_array, $pk);
        
        $this->root($root);
    }

    /**
     * Get/set tree root
     * 
     * @param  Model $root
     * @return Model
     */
    public function root(Model $root = NULL)
    {
        if ($root !== NULL)
        {
            $this->_root = $root;
        }
        return $this->_root;
    }
    /**
     * Return tree as a structured list of models
     * 
     * @return Models
     */
    abstract public function as_list();

    /**
     * Get direct children of the parent node
     * If $parent == NULL returns top level items
     *
     * @param  Model $parent
     * @return Models
     */
    abstract public function children(Model $parent = NULL);

    /**
     * Returns TRUE if the specified parent has children
     * 
     * @return boolean
     */
    abstract public function has_children(Model $parent = NULL);

    /**
     * Get the branch of the tree with root at $root
     *
     * @param  Model $root
     * @return ModelsTree
     */
    abstract public function branch(Model $root = NULL);    
}
