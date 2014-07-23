<?php
/**
 * Admin configuration
 */
return new \Phalcon\Config(array(
    'admin' => array(
        'defaults' => array(
            'country' => 'dk'
        )
    ),
    'system' => array(
        'services' => array(
        	'cache' => array(
                'data' => array(
                    'adapter' => '\Phalcon\Cache\Backend\Memory',
                    'options' => array(
                        'lifetime' => strtotime("+10 seconds")
                    )
                ),
                'view' => array(
                    'adapter' => '\Phalcon\Cache\Backend\Memory',
                    'options' => array(
                        'lifetime' => strtotime("+10 seconds")
                    )
                )
            )
        )
    )
));