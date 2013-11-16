<?php defined('SYSPATH') or die('No direct script access.'); // как обычно - защита от прямого доступа

class Kohana_Comments { // поехали!
	
	public static function factory(array $config = array()) // здесь происходит создание объекта
	{
		return new Comments($config); // создаем новый объект Comments с нашим конфигом
	}
	
	public function __construct(array $config = array()) // конструктор класса
	{
		$this->config = Kohana::$config->load('comments')->as_array(); // заносим в $this->config конфиг из папки с модулем и объединяем его с конфигом пользователя, елси он есть
	}
	
	public function render() // функция рисования комментариев
	{	
		$view = View::factory('comments/comments')->set('cfg', $this->config); // создаем переменную с нашим видом, в который передаем конфиг
		return $view->render(); // как результат вызова функции - возвращаем отрендеренный вид
	}
}