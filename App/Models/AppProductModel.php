<?php

namespace App\Models;

use \Phalcon\DI,
    App\Models\AppModel,
    App\Lib\Utility\Hash;

class AppProductModel extends AppModel
{
    
    /**
     * Retrieve Product data from CompareEngine API
     * caching the results for (default) 24 hours
     * 
     * Example:
     * $creditCardList = AppProductModel::find(array(
     *     'channel' => 'credit-cards',
     *     'query' => array(
     *         'companyName' => array(
     *             '$in' => array('HSBC')
     *         ),
     *         'cardProvider' => array(
     *             '$in' => array('Mastercard')
     *         )
     *     ),
     *     'sort' => array(
     *         'lowestMonthlyFlatRate' => 1,
     *         'computedLaprAverage' => -1
     *     ),
     *     'limit' => 15,
     *     'offset' => 30
     * ),
     * array(
     *     'cache' => array(
     *         'key' => md5('123456789'),
     *         'prefix' => 'products/creditcards/',
     *         'reset' => false,
     *         'lifetime' => strtotime('+1 hour')
     *     )
     * ));
     * 
     * @param type $params
     * @return array Products Resultset
     */
    public static function find($params=null, $options=array())
    {
        $options = self::populateOptions($options, $params);
        
        $cacheKey = $options['cache']['key'];

        $client = DI::getDefault()->get('compareEngine');
        $cache = DI::getDefault()->get('modelsCache');
        
        if($options['cache']['reset'] === true) {
            $cache->delete($cacheKey);
        }
        
        if($cache->exists($cacheKey)) {
            return $cache->get($cacheKey);
        } else {
            $data = $client->getProducts($params);
            $cache->save($cacheKey, $data, $options['cache']['lifetime']);
            return $data;
        }
    }
    
    public static function findFirst($ref=null, $params=null, $options=array())
    {
        $options = self::populateOptions($options, array('id' => $ref));
        
        $cacheKey = $options['cache']['key'];

        $client = DI::getDefault()->get('compareEngine');
        $cache = DI::getDefault()->get('modelsCache');
        
        if($options['cache']['reset'] === true) {
            $cache->delete($cacheKey);
        }
        
        if($cache->exists($cacheKey)) {
            return $cache->get($cacheKey);
        } else {
            $data = $client->getProductDetail($ref, $params);
            $cache->save($cacheKey, $data, $options['cache']['lifetime']);
            return $data;
        }
    }
    
    protected static function populateOptions($options=array(), $params=array())
    {
        $options = array_replace_recursive(array(
            'cache' => array(
                'key'       => parent::createCacheKey($params),
                'lifetime'  => null,
                'reset'     => false
            )
        ), $options);
        
        if(isset($options['cache']['prefix'])) {
            $options['cache']['key'] = parent::createCacheKey($params, $options['cache']['prefix']);
        }
        return $options;
    }
}
