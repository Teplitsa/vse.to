<?php defined('SYSPATH') or die('No direct script access.');

/******************************************************************************
 * Frontend
 ******************************************************************************/
if (APP === 'FRONTEND')
{
    // ----- feedback
    Route::add('frontend/feedback', new Route_Frontend(
                'feedback(/<action>)'
            ,
            array(
                'action' => '\w++'
            )
        ))
        ->defaults(array(
            'controller' => 'feedback',
            'action'     => 'index',
        ));
}
/******************************************************************************
 * Backend
 ******************************************************************************/