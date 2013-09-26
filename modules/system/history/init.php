<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
}
/******************************************************************************
 * Backend
 ******************************************************************************/
if (APP === 'BACKEND')
{
    Route::add('backend/history', new Route_Backend(
                'history(/<item_type>)(/p-<page>)'
              . '(/~<history>)'
            ,
            array(
                'item_type' => '\w++',
                'page' => '\d++',

                'history'   => '.++'
            )
        ))
        ->defaults(array(
            'controller' => 'history',
            'action'     => 'index',

            'item_type' => NULL,
            'page' => 0
        ));
}