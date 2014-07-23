<?php
namespace Modules\Common;
use \Phalcon\Mvc\ModuleDefinitionInterface,
    \Phalcon\Loader;

class Module implements ModuleDefinitionInterface{
    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader;

        $loader->registerNamespaces(array(
            __NAMESPACE__ => __DIR__,
            __NAMESPACE__ . '\Controllers' => __DIR__ . '/Controllers/',
            __NAMESPACE__ . '\Models' => __DIR__ . '/Models/',
            __NAMESPACE__ . '\Router' => __DIR__ . '/Router/'
        ), true);

        $loader->register();
    }

    public static function initRoutes($di)
    {
        $router = $di->get('router');
        $routerDefaults = $router->getDefaults();
        unset($routerDefaults['params']);
        $defaults = array_replace($routerDefaults, array(
            'namespace' => __NAMESPACE__ . '\Controllers',
            'module' => str_replace('Modules\\', '', __NAMESPACE__)
        ));
        
        $moduleRoutes = include __DIR__ . '/config/routes.php';
        
        foreach ($moduleRoutes as $url => $target) {
            $route = array_merge($defaults, $target);
            $link = $router->add($url, $route);
            
            if(isset($route['for'])) {
                $link->setName($route['for']);
            }
        }
        $di->set('router', $router);
    }

    public static function initConfigs($di) {
        $language = $di->get('registry')->language->code;
        $lang_file = __DIR__ . DS . 'config'. DS .$language. DS .'lang.php';

        if(file_exists($lang_file)) {
            $di->get('app')->extendTranslation(array(
                'with' => 'lang',
                'dir' => dirname($lang_file)
            ));
        }
        
        $menu_file = __DIR__ . DS . 'config'. DS .$language. DS . 'menu.php';
        if(file_exists($menu_file)) {
            $di->get('app')->extendMenu(array(
                'with' => 'menu',
                'dir' => dirname($menu_file)
            ));
        }
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        /**
         * Read configuration
         */
        $config = $di->getShared('config');

        $moduleConfFile = __DIR__ . '/config/config.php';

        if(file_exists($moduleConfFile)) {
            /**
             * Load module configuration
             */
            $moduleConfig = require $moduleConfFile;

            /**
             * Merge module config with app config
             * if module config is valid and not empty
             */
            if(is_a($moduleConfig, "Phalcon\Config")) {
                $config->merge($moduleConfig);
            }

            /**
             * Update application wide config
             */
            $di->setShared('config', $config);
        }

        /**
         * Setting up the view component
         */
        $view = $di->get('view');
        $view->setViewsDir(__DIR__ . '/Views/');

        /**
         * Update layouts directory if this module has an override directory
         */
        $moduleLayoutDir = __DIR__ . '/Views/layouts/';
        $moduleLayoutDirExists = is_dir($moduleLayoutDir);
        $moduleLayoutDirHasFiles = false;
        if($moduleLayoutDirExists) {
            $dirContentCount = count(glob($moduleLayoutDir . '*', GLOB_NOSORT));
            if($dirContentCount > 0) {
                $moduleLayoutDirHasFiles = true;
            }
        }

        if($moduleLayoutDirHasFiles) {
            $view->setLayoutsDir('/layouts/');
        } else {
            $view->setLayoutsDir('/../../../App/Views/layouts/');
        }

        $di->set('view', $view);
    }

}