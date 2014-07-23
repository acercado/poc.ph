<?php
/**
 * API configuration
 */
return new \Phalcon\Config(array(
    'api' => array(
        'compareEngine' => array(
            'endpoint'  => 'api.compargo.com',
            'scheme'    => 'http',
            'version'   => 'v1',
            'oauth'     => array(
                'clientId'  => '',
                'secret'    => '',
                'token'     => '8f7b3df50857dc6f9d6d673d4aede314ef9e7d0d'
            )
        ),
        'cms' => array(
            'endpoint'  => 'cms.compargo.com/api',
            'scheme'    => 'http'
        )
    )
));