<?php

namespace App\Controllers;

use \Phalcon\MVC\Controller;

class AppController extends Controller{
    public $flash;
    public $translations;

    public function initialize(){
        $this->flash        = $this->di->get('flash');
        $this->translations = $this->di->get('translations');
    }

    /**
     * redirect
     * 
     * redirect to destination
     * 
     * @param string|array $location
     * @param boolean $externalRedirect
     * @param int $statusCode
     * @return \Phalcon\Http\ResponseInterface
     */
    public function redirect($location=null, $externalRedirect=null, $statusCode=null){
        $this->response->redirect($location, $externalRedirect, $statusCode);
    }

    /**
     * respond
     * 
     * Respond with $flash message and redirect, or JSON response,
     * dependent on request type.
     * 
     * @param array $options
     * @return mixed
     */
    public function respond($options = array()){
        $defaults = array(
            'data' => null,
            'status' => true,
            'message' => null,
            'url' => false
        );
        $options = array_merge($defaults, $options);
        if($this->request->isAjax()) {
            unset($options['url']);
            if($options['status'] == false) {
                unset($options['data']);
            }
            $this->response->setJsonContent($options);
            $this->response->send();
        } else {
            if($options['status'] == true) {
                $this->flash->success($options['message']);
                if(isset($options['url'])) {
                    $this->redirect($options['redirect']);
                }
            } else {
                $this->flash->error($options['message']);
                if(isset($options['url'])) {
                    $this->redirect($options['redirect']);
                }
            }
        }
    }
    
    public function beforeExecuteRoute($dispatcher){
        // Executed before every found action
    }

    public function afterExecuteRoute($dispatcher){
        // Executed after every found action
    }

    public function notFoundAction(){
        $this->view->setLayout('notFound');

        $this->tag->prependTitle($this->translations->notfound_0);
        
        $this->response->setStatusCode(404, 'Not Found');
        $this->response->send();
    }

    public function checkPageData($page=null){
        if(empty($page)) {
            $this->response->setStatusCode(404, 'Not Found');
            $this->dispatcher->forward(array(
                'action' => 'notFound'
            ));
        }
    }
}