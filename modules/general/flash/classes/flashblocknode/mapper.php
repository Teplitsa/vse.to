<?php defined('SYSPATH') or die('No direct script access.');

class FlashblockNode_Mapper extends Model_Mapper {

    public function init()
    {
        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('flashblock_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('node_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('visible', array('Type' => 'boolean'));
    }

    /**
     * Update information about visible nodes for block
     *
     * @param Model_Block $block
     * @param array $nodes_visibility
     */
    public function update_nodes_visibility(Model_Flashblock $flashblock, array $nodes_visibility)
    {
        $this->delete_rows(DB::where('flashblock_id', '=', (int)$flashblock->id));

        foreach ($nodes_visibility as $node_id => $visible)
        {
            if ($visible != $flashblock->default_visibility)
            {
                $this->insert(array(
                    'node_id' => $node_id,
                    'flashblock_id' => (int)$flashblock->id,
                    'visible' => $visible
                ));
            }
        }
    }
}