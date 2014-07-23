<?php
namespace Modules\Common\Controllers;

use Modules\Common\Models\PageModel;

class PagesController extends ModuleController{
    public function displayAction($alias=null){
        $this->registry->alias = $alias;
        $page = PageModel::findByAliasFixtures($alias, $this->dispatcher->getModuleName());
        $this->checkPageData($page);
        
        if($alias != 'home'){
            $this->registry->breadcrumbs->add('page', ltrim($this->router->getRewriteUri(), '/'), $page['title']);
        }

        $this->view->setVars(array(
            'widget_list' => array('header', 'menu', 'seotext', 'subfooter', 'footer'),
            'meta' => array('keywords' => $page['seo']['keywords'], 'description' => $page['seo']['description'], 'image' => $page['seo']['image']),
            
            'title' => $page['title'],
            'content' => $page['content']
        ));
        
        $this->tag->prependTitle($page['title']);
    }
}
