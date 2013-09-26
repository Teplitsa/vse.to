<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
    'email' => array(
        'client' => array(
            'subject' => 'Ответ на Ваш вопрос на сайте "{{site}}"',
            'body' =>
'{% if user_name %}Здравствуйте, {{ user_name }}!{% else %}Здравствуйте!{% endif %}

На заданный Вами вопрос
-----------------------
{{question}}

был получен ответ:
------------------
{{answer}}'
        ),

        'admin' => array(
            'subject' => 'Новый вопрос с сайта "{{site}}"',
            'body' =>
'{% if user_name %}Имя пользователя: {{ user_name }}
{% endif %}{% if email %}E-Mail: {{ email }}
{% endif %}{% if phone %}Телефон: {{ phone }}
{% endif %}
Вопрос:
-------
{{question}}'
        )
    )
);