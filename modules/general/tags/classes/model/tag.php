<?php defined('SYSPATH') or die('No direct script access.');

class Model_Tag extends Model
{
    const TAGS_DELIMITER = ',';

    public function save_all($tags_string,$owner_type,$owner_id) {
        $tag_names = explode(self::TAGS_DELIMITER,$tags_string);
        $tag_names = array_unique($tag_names);
        $tags = Model::fly('Model_Tag')->delete_all_by(array(
            'owner_type' => $owner_type,
            'owner_id' => $owner_id));

        $i = 0;
        foreach ($tag_names as $tag_name) {
            $tag_name = trim($tag_name);
            if ($tag_name != '') {
                $tag = new Model_Tag();
                $tag->weight = ++$i;
                $tag->name = trim($tag_name);
                $tag->owner_type = $owner_type;
                $tag->owner_id = $owner_id;
                $tag->save();
            }
        }
    }
    
    /**
     * Make alias for tag from it's 'name' field
     *
     * @return string
     */
    public function make_alias()
    {
        $alias = str_replace(' ', '_', strtolower(l10n::transliterate($this->name)));
        $alias = preg_replace('/[^a-z0-9_-]/', '', $alias);
        
        return $alias;
    }
    
    public function save($force_create = FALSE) {
        if (!$this->id) $this->alias = $this->make_alias();
        parent::save($force_create);
    }
}