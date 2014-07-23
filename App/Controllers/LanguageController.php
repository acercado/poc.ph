<?php
namespace App\Controllers;

use App\Controllers\AppController;

class LanguageController extends AppController{
    public function switchAction($code=null){
        $this->di->get('app')->setLanguage($code);
        $localPath = parse_url($this->request->getHTTPReferer());
        $redir = '';
        if( !empty($localPath['path']) ){
            $allroutes = $this->router->getRoutes();
            foreach ($allroutes as $route) {
                if ($route->getPattern() === $localPath['path']) {
                    $redir = $route->getPaths();
                    if(!empty($redir['language'])){
                        $redir['language'] = $code;
                        $for = explode(':', $redir['for']);
                        $for[0] = $code;
                        $redir['for'] = implode(':', $for);
                    }
                }
            }
        }
        // redirect to root
        $this->response->redirect($redir);
    }
}

