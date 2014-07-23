<?php
namespace Modules\Creditcards\Controllers;
use Modules\Creditcards\Models\ProductsModel;

class ProductsController extends ModuleController{
    public function providerAction($alias=null, $provider=null){
        $this->registry->alias = $alias;
        $page = ProductsModel::findByAliasFixtures($provider, $this->dispatcher->getModuleName());
        $this->checkPageData($page);

        $this->registry->breadcrumbs->add('vertical', $this->registry->language->code.':credit-card', $this->translations->breadcrumbs_1);
        $this->registry->breadcrumbs->add('provider', ltrim($this->router->getRewriteUri(), '/'), $page['title']);

        $products = ProductsModel::find( $this->dispatcher->getModuleName(), array() );

        $this->view->setVars(array(
            'widget_list' => array('header', 'menu', 'subfooter', 'footer'),
            'meta' => array('keywords' => $page['meta']['keywords'], 'description' => $page['meta']['description'], 'image' => $page['meta']['image']),
            
            'title' => $page['title'],
            'content' => $page['content'],
            'products' => $products
        ));
        
        $this->tag->prependTitle($page['title']);
    }

    public function productAction($alias=null, $provider=null, $product=null){
        $this->registry->alias = $alias;
        $page = ProductsModel::findByProductFixtures($provider, $product, $this->dispatcher->getModuleName());
        $this->checkPageData($page);

        // bad implementation
        $this->registry->breadcrumbs->add('vertical', $this->registry->language->code.':credit-card', $this->translations->breadcrumbs_1);
        $this->registry->breadcrumbs->add('provider', rtrim(ltrim($this->router->getRewriteUri(), '/'), $page['product']), $page['title']);
        $this->registry->breadcrumbs->add('product', ltrim($this->router->getRewriteUri(), '/'), $page['product']);

        $products = ProductsModel::find( $this->dispatcher->getModuleName(), array() );

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
