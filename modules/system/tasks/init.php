<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Common
 ******************************************************************************/
Route::set('tasks', 'tasks(/<action>(/<task>))',
        array(
            'action' => '\w++',
            'task'   => '\w++'
        )
    )
    ->defaults(array(
        'controller' => 'tasks',
        'action'     => 'execute'
    ));

/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/tasks', new Route_Backend(
                'tasks(/<action>(/<task>))'
              . '(/~<history>)'
            ,
            array(
                'action'    => '\w++',
                'task'      => '\w++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'tasks',
            'action'     => 'ajax_status',
            'task'       => NULL,
        ));
}