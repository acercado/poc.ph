<?php
/**
 * System configuration
 */
return new \Phalcon\Config(array(
    'system' => array(
        'debug' => false,
        'services' => array(
        	'cache' => array(
                'data' => array(
                    'adapter' => '\Phalcon\Cache\Backend\APC',
                    'options' => array(
                        'lifetime' => strtotime("+24 hours")
                    )
                ),
                'view' => array(
                    'adapter' => '\Phalcon\Cache\Backend\APC',
                    'options' => array(
                        'lifetime' => strtotime("+2 hours")
                    )
                )
            ),
        	'annotations' => array(
                'adapter' => 'Apc'
            ),
        	'models' => array(
        		'metadata' => array(
                    'adapter' => 'Apc'
                )
        	)
        ),
        'security' => array(
            'salt' => '249b111e4e5b85659b3d8c20714f8eb6a69fdb93',
        )
    )
));