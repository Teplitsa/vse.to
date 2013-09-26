<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Nested set models tree
 *
 * @package    Eresus
 * @author     Skryabin Ivan (skr@mail.mipt.ru)
 */
class ModelsTree_NestedSet extends ModelsTree
{
    /**
     * Indicates whether properties array have been rebuilt to the tree structure
     * @var boolean
     */
    public $tree_is_built = FALSE;

    /**
     * Return tree as structured list of models
     *
     * @return Models
     */
    public function as_list()
    {
        // Reorder list of properties
        $list = array();
        $this->_build_list($list);
        return new Models($this->_model_class, $list, $this->_pk);
    }

    /**
     * Get direct children of the parent node
     * If $parent == NULL returns top level items
     *
     * @param  Model $parent
     * @param  boolean as_array
     * @return Models | array
     */
    public function children(Model $parent = NULL, $as_array = FALSE)
    {   
        // convert properties to tree (if not already converted)
        $this->build_tree();

        if ($parent === NULL)
        {
            // top-level items
            $parent_pk = 0;
        }
        else
        {
            $parent_pk = $parent[$this->_pk];
        }

        $children = array();
        if ( ! empty($this->_properties_array[$parent_pk]['child_ids']))
        {
            foreach ($this->_properties_array[$parent_pk]['child_ids'] as $child_pk)
            {
                $children[$child_pk] = $this->_properties_array[$child_pk];
            }
        }

        if ($as_array)
        {
            return $children;
        }
        else
        {
            return new Models($this->_model_class, $children, $this->_pk);
        }
    }

    /**
     * Get parents for the model
     * 
     * @param  Model $child
     * @param  boolean $as_array
     * @param  boolean $sort_asc Sort by level from top to bottom
     * @return Models | array
     */
    public function parents(Model $child, $as_array = FALSE, $sort_asc = TRUE)
    {
        // convert properties to tree (if not already converted)
        $this->build_tree();
        
        $parents = array();

        $child_pk = $child[$this->_pk];
        $loop_prevention = 1000;
        while ( ! empty($this->_properties_array[$child_pk]['parent_id']) && $loop_prevention > 0)
        {
             $child_pk = $this->_properties_array[$child_pk]['parent_id'];
             $parents[$child_pk] = $this->_properties_array[$child_pk];

             $loop_prevention--;
        }

        if ($loop_prevention == 0)
            throw new Kohana_Exception ('Possible infinite loop in :method', array(':method' => __METHOD__));

        if ($sort_asc)
        {
            $parents = array_reverse($parents, TRUE);
        }

        if ($as_array)
        {
            return $parents;
        }
        else
        {
            return new Models($this->_model_class, $parents, $this->_pk);
        }
    }

    /**
     * Get the ancestor for descendant
     * 
     * @param  Model|integer $descendant
     * @param  integer $level
     * @param  boolean $as_array
     * @return Model
     */
    public function ancestor($descendant, $level = -1, $as_array = FALSE)
    {
        $this->build_tree();
        
        $pk = $this->_pk;
        if ($descendant instanceof Model)
        {
            $lev = $descendant->level;
            $id  = $descendant->$pk;
        }
        else
        {
            $id = $descendant;
            if ( ! isset($this->_properties_array[$id]))
                return NULL;

            $lev = $this->_properties_array[$id]['level'];
        }
        
        if ($level <= 0)
        {
            $level = $lev + $level;
        }

        if ($level > $lev)
            return NULL;

        if ($level < $lev)
        {
            // traverse the tree up, starting from $descendant
            do {
                if ( ! isset($this->_properties_array[$id]))
                    return NULL; // $descendant not in tree / broken tree structure

                $id = $this->_properties_array[$id]['parent_id'];
            }
            while($this->_properties_array[$id]['level'] > $level);
        }

        if ($as_array)
        {
            return $this->_properties_array[$id];
        }
        else
        {
            return $this[$id];
        }
    }

    /**
     * Returns TRUE if the specified parent has children
     *
     * @return boolean
     */
    public function has_children(Model $parent = NULL)
    {
        // convert properties to tree (if not already converted)
        $this->build_tree();

        if ($parent === NULL)
        {
            return ( ! empty($this->_properties_array[0]['child_ids']));
        }
        else
        {
            return ( ! empty($this->_properties_array[$parent[$this->_pk]]['child_ids']));
        }
    }

    /**
     * Get the branch of the tree with root at $root
     *
     * @param  Model $root
     * @return ModelsTree
     */
   public function branch(Model $root = NULL)
   {}

   /**
    * Rebuild list of properties to the tree structure
    */
   public function build_tree()
   {
       if ($this->tree_is_built)
            return;

       //$token = Profiler::start('models', 'build_tree');

       $tree = & $this->_properties_array;

       foreach ($tree as $child)                    // <------------------------------|
       {                                                                        //    |
           if ( ! isset($child[$this->_pk]))                                    //    |
              continue; //root item                                             //    |
                                                                                //    |
           $child_pk = $child[$this->_pk];                                      //    |
                                                                                //    |
           if ($this->root() === NULL && $this->_is_top_level($child))          //    |
           {                                                                    //    |
               // It's a top-level item                                         //    |
               $tree[0]['child_ids'][] = $child_pk;                             //    |
               $tree[$child_pk]['parent_id'] = 0;                               //    |
           }                                                                    //    |
           elseif ($this->root() !== NULL && $this->_is_child($child, $this->root()->properties())) //|
           {                                                                    //    |
               // $child is a child of $root for this tree                      //    |
               $tree[$this->root()->id]['child_ids'][] = $child_pk;             //    |
               $tree[$child_pk]['parent_id'] = $this->root()->id;               //    |
           }                                                                    //    |
           else                                                                 //    |
           {                                                                    //    |
                // Find parent for child                                        //    |
                foreach ($tree as $parent)                                      //    |
                {                                                               //    |
                    if ( ! isset($parent[$this->_pk]))                          //    |
                        continue; //root item                                   //    |
                                                                                //    |
                    $parent_pk = $parent[$this->_pk];                           //    |
                                                                                //    |
                    if ($this->_is_child($child, $parent))                      //    |
                    {                                                           //    |
                        $tree[$parent_pk]['child_ids'][] = $child_pk;           //    |
                        $tree[$child_pk]['parent_id'] = $parent_pk;             //    |
                                                                                //    |
                        continue 2; // parent found, move to the next item -----------|
                    }
                }

                // parent was not found - an orphaned child...
            }
       }

       $this->tree_is_built = TRUE;
       $this->_properties_array = $tree;
       //Profiler::stop($token);
   }

    /**
     * Recursively build ordered list of properties for as_list() method
     *
     * @param array $list
     * @param array $parent
'     */
    protected function _build_list(array & $list, array $parent = NULL)
    {
        foreach ($this->_properties_array as $item)
        {
            if ( ! isset($item[$this->_pk]))
                continue;
            
            if (
                $parent === NULL && $this->_is_top_level($item)
             || $parent !== NULL && $this->_is_child($item, $parent)
            )
            {
                $list[$item[$this->_pk]] = $item;

                $this->_build_list($list, $item);
            }
        }
    }

    /**
     * Is $child a direct child of $parent ?
     *
     * @param  array $child
     * @param  array $parent
     * @return boolean
     */
    protected function _is_child(array $child, array $parent)
    {
        return
        (
            ($child['level'] == $parent['level'] + 1)
         && ($child['lft'] > $parent['lft'])
         && ($child['rgt'] < $parent['rgt'])
        );
    }
    
    /**
     * Is item a top-level one?
     *
     * @param  array $item
     * @return boolean
     */
    protected function _is_top_level(array $item)
    {
        return ($item['level'] <= 1);
    }
    
    /**
     * If $child a descendant of $parent?
     *
     * @param  Model $child
     * @param  Model $parent
     * @return boolean
     */
    public function is_descendant(Model $child, Model $parent)
    {
        return ($child->lft > $parent->lft && $child->rgt < $parent->rgt);
    }
}
