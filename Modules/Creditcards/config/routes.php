<?php
return array(
    '/credit-card' => array(
        'module'    => 'Creditcards',
        'controller'=> 'Index',
        'action'    => 'index',
        'alias'     => 'credit-card',
        'language'  => 'en',
        'for'       => 'en:credit-card'
    ),

    '/credit-card/{provider:([a-zA-Z0-9_-]+)}' => array(
        'module'    => 'Creditcards',
        'controller'=> 'Products',
        'action'    => 'provider',
        'alias'     => 'credit-card-provider',
        'provider'  =>  null,
        'language'  => 'en',
        'for'       => 'en:credit-card-provider'
    ),

    '/credit-card/{provider:([a-zA-Z0-9_-]+)}/{product:([a-zA-Z0-9_-]+)}' => array(
        'module'    => 'Creditcards',
        'controller'=> 'Products',
        'action'    => 'product',
        'alias'     => 'credit-card-product',
        'provider'  =>  null,
        'product'   =>  null,
        'language'  => 'en',
        'for'       => 'en:credit-card-product'
    ),

    '/credit-card/results?{params:([a-zA-Z0-9_-]+)}' => array(
        'module'    => 'Creditcards',
        'controller'=> 'Index',
        'action'    => 'results',
        'alias'     => 'credit-card-results',
        'params'    => null,
        'language'  => 'en',
        'for'       => 'en:credit-card-results'
    )
);