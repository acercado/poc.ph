<?php
/**
 * Development configuration
 */
return new \Phalcon\Config(array(
    'system' => array(
        'debug' => true
    ),
    'api' => array(
        'compareEngine' => array(
            'endpoint' => 'apibeta.compargo.com'
        )
    )
));
