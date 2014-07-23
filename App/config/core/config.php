<?php
/**
 * Default configuration
 * Will be extended with individual config files
 */
return new \Phalcon\Config(array(
    'project' => array(
        'baseUri' => '/',
        'timezone' => 'Asia/Manila',
        'analytics' => array(
            'google' => array(
                'enabled' => false,
                'apiKey' => 'UA-48994626-1'
            ),
            'piwik' => array(
                'enabled' => false,
                'url' => 'http://analytics.compargo.com/piwik.php'
            )
        )
    )
));
