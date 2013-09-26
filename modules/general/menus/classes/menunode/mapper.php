<?php defined('SYSPATH') or die('No direct script access.');

class MenuNode_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('menu_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('node_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('visible', array('Type' => 'boolean'));
    }

    /**
     * Find menu nodes with visibility information for given menu and root node
     *
     * @param  Model_Menu $menu
     * @param  Model_Node $root_node
     * @param  Database_Expression_Where $condition
     * @return ModelsTree_NestedSet|Models|array
     */
    public function find_all_menu_nodes(Model_Menu $menu, Model_Node $root_node, Database_Expression_Where $condition = NULL)
    {
        $node_mapper = Model_Mapper::factory('Model_Node_Mapper');
        $menunode_table = $this->table_name();

        $node_table     = $node_mapper->table_name();
        $table_prefix   = $this->get_db()->table_prefix();

        $default_visibility = (int) $menu->default_visibility;

        $query = DB::select("$node_table.*", array(DB::expr("IFNULL(".$table_prefix.$menunode_table.".visible, $default_visibility)"),  "visible"))
            ->from($node_table)
            ->join($menunode_table, 'LEFT')
                ->on("$menunode_table.node_id", '=', "$node_table.id")
                ->on("$menunode_table.menu_id", '=', DB::expr((int) $menu->id));

        // Search only nodes from the same site as the menu
        if ($condition === NULL)
        {
            $condition = DB::where("$node_table.site_id", '=', (int) $menu->site_id);
        }
        else
        {
            $condition->and_where("$node_table.site_id", '=', (int) $menu->site_id);
        }

        if (isset($root_node->id))
        {
            $condition->and_where('lft', '>', (int) $root_node->lft);
            $condition->and_where('rgt', '<', (int) $root_node->rgt);
        }

        $params = array('order_by' => 'lft', 'desc' => FALSE);

        return $node_mapper->find_all_by($root_node, $condition, $params, $query);
    }

    /**
     * Find visible menu nodes for given menu and root node
     *
     * @param Model_Menu $menu
     * @param Model_Node $root_node
     * @return array
     */
    public function find_all_visible_menu_nodes(Model_Menu $menu, Model_Node $root_node)
    {
        if ($menu->default_visibility == 1)
        {
            $condition = DB::where()
                ->and_where_open()
                    ->and_where('visible', '=', '1')
                    ->or_where(DB::expr('ISNULL(`visible`)'))
                ->and_where_close();
        }
        else
        {
            $condition = DB::where('visible', '=', '1');
        }

        if ($menu->max_level > 0)
        {
            $condition->and_where('level', '<=', $root_node->level + $menu->max_level);
        }

        return $this->find_all_menu_nodes($menu, $root_node, $condition);
    }

    /**
     * Update information about visible nodes for menu
     *
     * @param Model_Menu $menu
     * @param array $nodes_visibility
     */
    public function update_menu_nodes(Model_Menu $menu, array $nodes_visibility)
    {
        $this->delete_rows(DB::where('menu_id', '=', (int)$menu->id));

        foreach ($nodes_visibility as $node_id => $visible)
        {
            if ($visible != $menu->default_visibility)
            {
                $this->insert(array(
                    'node_id' => $node_id,
                    'menu_id' => (int)$menu->id,
                    'visible' => $visible
                ));
            }
        }
    }
}