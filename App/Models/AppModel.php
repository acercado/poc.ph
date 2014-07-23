<?php

namespace App\Models;

use \Phalcon\MVC\Model;
class AppModel extends Model{
    public static function getFixtures($code=null, $module=null){
        if(!empty($code) && !empty($module)){
            $fixturesFile = file_get_contents(MODULES_PATH . DS . $module . DS .'data' . DS . 'fixtures' . DS . $code . DS . 'data.json');
            return json_decode($fixturesFile, true);
        }else{
            return false;
        }
    }
    
    public static function findFixtures($params=null, $module=null){
        if(!empty($params) && !empty($module)){
            $language = \Phalcon\DI::getDefault()->get('registry')->language;
            return self::getFixtures($language['code'], $module);
         }else{
            return false;
        }
    }
    
    public static function findByAliasFixtures($alias=null, $module=null){
        if( !empty($alias) && !empty($module) ){
            $language = \Phalcon\DI::getDefault()->get('registry')->language;
            $fixtures = self::getFixtures($language['code'], $module);
            for ($i=0; $i < count($fixtures); $i++) { 
                if ($fixtures[$i]['alias'] == $alias) {
                    return $fixtures[$i];
                }
            }
            return false;
        }else{
            return false;
        }
    }

    public static function findByProductFixtures($alias=null, $product=null, $module=null){
        if( !empty($alias) && !empty($product) && !empty($module) ){
            $language = \Phalcon\DI::getDefault()->get('registry')->language;
            $fixtures = self::getFixtures($language['code'], $module);
            for ($i=0; $i < count($fixtures); $i++) { 
                if(!empty($fixtures[$i]['product'])){
                    if ($fixtures[$i]['alias'] == $alias && $fixtures[$i]['product'] == $product) {
                        return $fixtures[$i];
                    }
                }
            }
            return false;
        }else{
            return false;
        }
    }
    
    /**
     * Implement a method that returns a string key based
     * on the query parameters
     */
    protected static function createCacheKey($parameters=array(), $prefix=null)
    {
        if(empty($parameters)) {
            return $prefix . sha1(serialize($parameters));
        }
    
        $parameters = (array)$parameters;
        $uniqueKey = array();
        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key . ':' . $value;
            } else {
                if (is_array($value)) {
                    $uniqueKey[] = $key . ':[' . self::createCacheKey($value) .']';
                }
            }
        }
        return $prefix . join(',', $uniqueKey);
    }

}
