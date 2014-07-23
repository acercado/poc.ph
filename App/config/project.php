<?php
/**
 * Project configuration
 */
 
$url = 'http://dev.ph.cms.compargo.com/api/12345/configuration';
$url = 'http://ph.cms.compargo.com/api/12345/configuration';
$cms = json_decode(file_get_contents($url));
$cms = $cms->data;

return new \Phalcon\Config(array(
    'project' => array(
        'name'                  => 'MoneyMax',
        'html_name'             => '<span class="blue-txt"><strong>Money</strong></span><span class="green-txt"><strong>Max</strong></span>',
        'url'                   => 'www.moneymax.ph',
        'people'                => 'Filipino',
        'currency'              => '₱',
        'address'               => '',
        'phone'                 => '0942-850-8823',
        'office_hours'          => 'Mon - Fri, 9am - 7pm',
        'email_address'          => 'info@moneymax.ph',
        'social'                => array(
            'twitter'               => $cms->twitter_url,
            'facebook'              => $cms->facebook_url,
            'google'                => $cms->google_plus_url,
            'youtube'               => $cms->youtube_url
        ),
        'analytics' => array(
            'google' => array(
                'apiKey' => 'UA-42077100-1'
            ),
            'piwik' => array(
                'idsite' => '1'
            )
        ),
        'appID' => array(
            'facebook' => '258222307673852'
        ),
        'widgets' => array(
            'newsletter' => array(
                'enabled' => true
            ),
            'blogfeed' => array(
                'enabled' => false
            )
        ),
        'baseUri'       => '//'.$this->request->getHttpHost(),
        'timezone'      => 'Asia/Manila',
        'mail'          => array(
            'recipentName'  => 'John Paul Onte',
            'recipentEmail' => 'johnpaul@novafabrik.com',
            'fromName'      => 'CompareWeb',
            'fromEmail'     => 'info@compare.web',
            'smtp'          => array(
                'server'        => 'smtp.mandrillapp.com',
                'port'          => 587,
                'security'      => 'ssl',
                'username'      => 'asana.enterprise@novafabrik.com',
                'password'      => 'gqnswOGUBLSrgnhYfzW4Wg'
            )
        ),
        'default_image'             => 'pose1.png',
        'default_descricption'      => 'default desc',
        'default_keywords'          => 'default keywords',
        'default_title'             => 'default title',
        'cms_url'                   => 'http://dev.ph.cms.compargo.com/api/12345/'
        //'cms_url'                   => 'http://ph.cms.compargo.com/api/12345/'
    )
));