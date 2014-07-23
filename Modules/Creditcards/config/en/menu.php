<?php
/**
 * Menu
 */

return new \Phalcon\Config(array(
    'main' => array(
        'credit-card' => array(
            'name'          => 'Credit Cards',
            'for'           => 'en:credit-card',
            'nav_name'      => 'credit-card',
        ),

        'credit-card-cashback' => array(
            'name'          => 'Cashback Cards',
            'for'           => 'en:credit-card-results',
            'params'        => 'var=credit-card-cashback',
            'parent'        => 'credit-card'
        ),

        'credit-card-airmile' => array(
            'name'          => 'Airmile Cards',
            'for'           => 'en:credit-card-results',
            'params'        => 'var=credit-card-airmile',
            'parent'        => 'credit-card'
        ),

        'credit-card-lowbalance' => array(
            'name'          => 'Low Balance Transfer Cards',
            'for'           => 'en:credit-card-results',
            'params'        => 'var=credit-card-lowbalance',
            'parent'        => 'credit-card'
        )
    )
));
