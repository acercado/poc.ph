<?php
/**
 * Staging configuration
 */
return new \Phalcon\Config(array(
    'api' => array(
        'compareEngine' => array(
            'endpoint' => 'apibeta.compargo.com'
        )
    ),
    'system' => array(
        'debug' => false
    )
));
