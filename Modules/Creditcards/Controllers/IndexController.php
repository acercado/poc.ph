<?php
namespace Modules\Creditcards\Controllers;
use Modules\Creditcards\Models\IndexModel;

class IndexController extends ModuleController{
    public function indexAction($alias=null){
        $this->registry->alias = $alias;
        $page = IndexModel::findByAliasFixtures($alias, $this->dispatcher->getModuleName());
        $this->checkPageData($page);
        
        $this->registry->breadcrumbs->add('vertical', ltrim($this->router->getRewriteUri(), '/'), $page['title']);


        $products = IndexModel::find( $this->dispatcher->getModuleName(), array() );

        $this->view->setVars(array(
            //commented temp, just removed sidebar
            'widget_list' => array('header', 'menu', 'placeholder', 'sidebar', 'modal', 'subfooter', 'footer'),
            'meta' => array('keywords' => $page['meta']['keywords'], 'description' => $page['meta']['description'], 'image' => $page['meta']['image']),
            
            'title' => $page['title'],
            'content' => $page['content'],
            'products' => $products
        ));
        
        $this->tag->prependTitle($page['title']);
    }

    public function resultsAction($alias=null){
        $this->registry->alias = $alias;

        //get query
        $query = $this->request->getQuery();
        unset( $query['_url'] ); 

        $page = IndexModel::findByAliasFixtures($alias, $this->dispatcher->getModuleName());
        $this->checkPageData($page);

        $this->registry->breadcrumbs->add('vertical', $this->registry->language->code.':credit-card', $page['title']);

        $products = IndexModel::find( $this->dispatcher->getModuleName(), array() );

        $this->view->setVars(array(
            'widget_list' => array('header', 'menu', 'modal', 'subfooter', 'footer'),
            'meta' => array('keywords' => $page['meta']['keywords'], 'description' => $page['meta']['description'], 'image' => $page['meta']['image']),
            
            'title' => $page['title'],
            'content' => $page['content'],
            'products' => $products
        ));
        
        $this->tag->prependTitle($page['title']);
    }
}
