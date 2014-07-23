<?php

namespace App\Lib;

use Phalcon\Mvc\User\Component;

class Widget extends Component
{
    protected $mainMenu = array();
    protected $staticMenu = array();
    
    protected $widgetView;
    protected $widgetDir;
    protected $widgetExists;

    public function __construct($di)
    {
        $this->widgetView = new \Phalcon\Mvc\View\Simple();
        $this->widgetView->setViewsDir(WIDGET_PATH);
        $this->widgetDir = MODULES_PATH . DS . $this->dispatcher->getModuleName() . DS . 'Views' . DS . 'widgets';
        $this->widgetExists = file_exists($this->widgetDir);
        
        $translationStrings = $di->get('translations');

        $translations = new \Phalcon\Translate\Adapter\NativeArray(array(
            'content' => $translationStrings->toArray()
        ));

        if(isset($this->menus['main'])) {
            foreach ($this->menus->main->toArray() as $key => $value) { 
                if(!empty($value['parent'])) {
                    $this->mainMenu[$value['parent']]['submenu'][] = $value;
                } else {
                    $this->mainMenu[$key] = $value;
                }
            }
        }

        if(isset($this->menus['static'])) {
            $this->staticMenu = $this->menus->static->toArray();
        }

        $this->widgetView->setVars(array(
            'lang' => $translations,
            'mainMenu' => $this->mainMenu,
            'staticMenu' => $this->staticMenu
        ));
    }

    public function getheader($widget_list=array())
    {
        if( in_array('header', $widget_list) ){
            $this->_checkwidgetfile('header');
            return $this->widgetView->render('/header');
        }
    }

    public function getmenu($widget_list=array())
    {
        if(in_array('menu', $widget_list)) {
            $this->_checkwidgetfile('menu');
            return $this->widgetView->render('/menu');
        }
    }

    public function getplaceholder($widget_list=array())
    {
        if(in_array('placeholder', $widget_list)) {
            $this->_checkwidgetfile('placeholder');
            return $this->widgetView->render('/placeholder');
        }
    }


    public function getsidebar($widget_list=array())
    {
        if(in_array('sidebar', $widget_list)) {
            $this->_checkwidgetfile('sidebar');
            return $this->widgetView->render('/sidebar');
        }
    }

    public function getnewsletter($widget_list=array())
    {
        if(in_array('newsletter', $widget_list)) {
            $this->_checkwidgetfile('newsletter');
            return $this->widgetView->render('/newsletter');
        }
    }

    public function getmodal($widget_list=array())
    {
        if(in_array('modal', $widget_list)) {
            $this->_checkwidgetfile('modal');
            return $this->widgetView->render('/modal');
        }
    }

    public function getseotext($widget_list=array())
    {
        if(in_array('seotext', $widget_list)) {
            $this->_checkwidgetfile('seotext');
            return $this->widgetView->render('/seotext');
        }
    }

    public function getsubfooter($widget_list=array())
    {
        if(in_array('subfooter', $widget_list)) {
            $this->_checkwidgetfile('subfooter');
            return $this->widgetView->render('/subfooter');
        }
    }

    public function getfooter($widget_list=array())
    {
        if(in_array('footer', $widget_list)) {
            $this->_checkwidgetfile('footer');
            return $this->widgetView->render('/footer');
        }
    }

    private function _checkwidgetfile($file=null){
        if($this->widgetExists) {
            if(file_exists($this->widgetDir.'/'.$file.'.phtml')) {
                $this->widgetView->setViewsDir($this->widgetDir); 
            } else {
                $this->widgetView->setViewsDir(WIDGET_PATH);
            }
        }
    }
}
