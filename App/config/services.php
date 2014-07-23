<?php

/**
 * Simple service configuration
 */
return new \Phalcon\Config(array(
	'session' => array(
		'className' => '\Phalcon\Session\Adapter\Files',
        'options' => array()
	),
    
    'cache' => array(
        'data' => array(
            'className' => '\Phalcon\Cache\Backend\APC',
            'options' => array(
                'lifetime' => strtotime("+24 hours")
            )
        ),
        'view' => array(
            'className' => '\Phalcon\Cache\Backend\APC',
            'options' => array(
                'lifetime' => strtotime("+2 hours")
            )
        )
    ),
    
    'assetManager' => array()
));
