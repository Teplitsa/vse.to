<?php defined('SYSPATH') or die('No direct script access.');

class BlockNode_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('block_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('node_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('visible', array('Type' => 'boolean'));
    }

    /**
     * Update information about visible nodes for block
     *
     * @param Model_Block $block
     * @param array $nodes_visibility
     */
    public function update_nodes_visibility(Model_Block $block, array $nodes_visibility)
    {
        $this->delete_rows(DB::where('block_id', '=', (int)$block->id));

        foreach ($nodes_visibility as $node_id => $visible)
        {
            if ($visible != $block->default_visibility)
            {
                $this->insert(array(
                    'node_id' => $node_id,
                    'block_id' => (int)$block->id,
                    'visible' => $visible
                ));
            }
        }
    }
}