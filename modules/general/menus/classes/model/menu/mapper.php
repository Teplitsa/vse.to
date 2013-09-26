<?php defined('SYSPATH') or die('No direct script access.');

class Model_Menu_Mapper extends Model_Mapper
{
    public function init()
    {
        $this->add_column('id',      array('Type' => 'int unsigned', 'Key' => 'PRIMARY', 'Extra' => 'auto_increment'));
        $this->add_column('site_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));

        // Имя меню (для вставки в шаблон)
        $this->add_column('name', array('Type' => 'varchar(15)', 'Key' => 'INDEX'));
        // Название меню
        $this->add_column('caption',  array('Type' => 'varchar(63)'));

        // Корневой раздел для меню
        $this->add_column('root_node_id', array('Type' => 'int unsigned'));
        // Максимальный уровень вложенности
        $this->add_column('max_level', array('Type' => 'int unsigned'));
        // Видимость для страниц по умолчанию
        $this->add_column('default_visibility', array('Type' => 'boolean'));
        // Вид (шаблон) для отрисовки меню
        $this->add_column('view', array('Type' => 'varchar(63)'));
        // Дополнительные настройки
        $this->add_column('settings', array('Type' => 'array'));

    }
}