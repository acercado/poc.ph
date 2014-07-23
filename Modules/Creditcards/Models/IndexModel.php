<?php

namespace Modules\Creditcards\Models;

use App\Models\AppProductModel;

class IndexModel extends AppProductModel{
    
    protected $params = array('channel' => 'credit-card');

    public static function find($params=null, $options=array()) {
        
        $fixturesFile = file_get_contents(MODULES_PATH . DS . $params . DS .'data' . DS . 'mockdata.json');
        $data = json_decode($fixturesFile, true);
        return $data['compargoGlobalApiResponse']['searchResults']['searchResultItems'];

        /*$data = parent::find($params, $options);
        return $data;*/
    }
    
    public static function findFirst($id=null, $params=null, $options=array()){
        $data = parent::find($id, $params, $options);
        return $data;
    }
    
    public static function findByAlias($ref=null, $params=null, $options=array()){
        $data = parent::find($id, $params, $options);
        return $data;
    }
    
}
