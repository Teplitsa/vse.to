<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
    'email' => array(
        'client' => array(
            'subject' => 'Восстановление пароля на сайте "{{site}}"',
            'body' =>
'По вашей учетной записи была подана заявка на восстановление пароля.
Для введения нового пароля перейдите по ссылке:
{{recovery_link}}

Если вы не подавали эту заявку, и это не первое подобное сообщение, советуем обратиться в службу клиентской поддержки портала vse.to. 
'
        )
    )
);