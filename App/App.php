<?php

namespace App;

use \Phalcon\DiInterface,
    \Phalcon\Loader,
    \Phalcon\Mvc\Router,
    \Phalcon\Http\ResponseInterface,
    \Phalcon\Events\Manager as EventsManager,
    \Phalcon\Registry,
    \Phalcon\Assets\Filters\Cssmin,
    \Phalcon\Assets\Filters\Jsmin,
    Illuminate\Support\Str,
    App\Lib\Network\CompareEngineClient;

/**
 * Application class for multi module applications
 * including HMVC internal requests.
 */
class App extends \Phalcon\Mvc\Application{
    /**
     * Application Constructor
     *
     * @param \Phalcon\DiInterface $di
     */
    public function __construct(DiInterface $di){
        parent::__construct($di);
        /**
         * Sets the parent DI and register the app itself as a service,
         * necessary for redirecting HMVC requests
         */
        parent::setDI($di);
        $di->set('app', $this);

        $di->setShared('registry', new Registry);
        
        include(ROOT_PATH . '/basics.php');
        
        /**
		 * The application wide configuration
		 */
		$appConfig = include CONF_PATH . DS . 'core' . DS . 'config.php';
        
        /**
         * Set application wide config
         */
        $di->setShared('config', $appConfig);
        
        /**
         * Set timezone for date/time functions
         */
        if(isset($appConfig->application->timezone)) {
            $timezone = $appConfig->application->timezone;
        } else {
            $timezone = 'Asia/Singapore';
        }
        date_default_timezone_set($timezone);
        
        // Merge system config with appConfig
        $this->extendConfig(array(
            'with' => 'system',
            'dir' => CONF_PATH . DS . 'core'
        ));
        
        // Merge country config with appConfig
        $this->extendConfig(array(
            'with' => 'country',
            'dir' => CONF_PATH . DS . 'core'
        ));
        
        // Merge API config with appConfig
        $this->extendConfig(array(
            'with' => 'api',
            'dir' => CONF_PATH . DS . 'core'
        ));
        
        // Merge project config with appConfig
        $this->extendConfig(array(
            'with' => 'project'
        ));
        
        // Merge admin config with appConfig
        if(file_exists(CONF_PATH . DS . 'core' . DS . 'admin.php')) {
            $this->extendConfig(array(
                'with' => 'admin',
                'dir' => CONF_PATH . DS . 'core'
            ));
        }
        
        // Merge environment config with appConfig
        $this->extendConfig(array(
            'with' => ENVIRONMENT,
            'dir' => CONF_PATH . DS . 'env'
        ));
        
        $di->getShared('registry')->country = reset($this->config->country);
        if (isset($this->config->admin->defaults->country)) {
            if (isset($this->config->country[$this->config->admin->defaults->country])) {
                $di->getShared('registry')->country = $this->config->country[$this->config->admin->defaults->country];
            }
        }
        
        /**
         * The application wide menu
         */
        $menus = new \Phalcon\Config(array( 'main' => array() ));

        /**
         * Set application wide config
         */
        $di->setShared('menus', $menus);

        if($appConfig->system->debug) {
            /**
             * Create new Whoops Error Handler
             */
            new \Whoops\Provider\Phalcon\WhoopsServiceProvider($di);
        }
        
        /**
         * Enable / disable error reporting based on environment
         */
        if ( $appConfig->system->debug ) {
            error_reporting( E_ALL );
            ini_set( 'display_errors', 1 );
        } else {
            error_reporting(0);
            ini_set('display_errors', 0);
        }
        
        /**
         * Register application wide accessible services
         */
        $this->registerServices();

        /**
         * Register the installed/configured modules
         */
        $this->registerModules();

        //set default registry
        $this->registry->page_name  = null;
        $this->registry->breadcrumbs= null;
        $this->registry->alias      = null;
    }

    /**
     * Register the services here to make them general or register in the
     * ModuleDefinition to make them module-specific
     * 
     * @return void
     */
    protected function registerServices() {
        /**
         * The application wide services
         */
        $services = include CONF_PATH . '/services.php';
        $this->di->setShared('services', $services);

        /**
         * Register namespaces for application classes
         */
        $this->di->setShared('loader', new Loader());
        $loader = $this->di->getShared('loader');
        
		$loader->registerNamespaces(array(
			'App'               => __DIR__,
			'App\Controllers'   => __DIR__ . '/Controllers/',
			'App\Models'        => __DIR__ . '/Models/',
			'App\Router'        => __DIR__ . '/Router/',
            'App\Lib\Utility'   => __DIR__ . '/Lib/Utility',
            'App\Lib\Network'   => __DIR__ . '/Lib/Network'
		), true)
        ->register();

        /**
         * Start the cookies the first time some component request the cookies service
         */
        $this->di->set('cookies', function() {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(false);
            return $cookies;
        });

        $this->di->set('flash', function() {
            $flash = new \Phalcon\Flash\Session(array(
                'error' => 'alert alert-error',
                'success' => 'alert alert-success',
                'notice' => 'alert alert-info',
                'warning' => 'alert alert-warning'
            ));
            return $flash;
        });

        /**
         * Start the session the first time some component request the session service
         */
        $this->di->set('session', function () {
            $sessionConfig = $this->di->getShared("services")->session;
            
            $session = new $sessionConfig->className();
            
            if(!$session->isStarted()) {
                $session->start();
            }
            
			return $session;
		});
        
        /**
		 * Configure dataCache service
		 */
		$this->di->setShared('modelsCache', function () {
            $cacheConfig = $this->di->getShared("services")->cache->data;
            
            // Create Cache Frontend
            $frontCacheConfig = array(
                'lifetime' => $cacheConfig->options->lifetime
            );
            $frontCache = new \Phalcon\Cache\Frontend\Data($frontCacheConfig);
            
            $backendOptions = array_replace($cacheConfig->options->toArray(), array(
                'prefix' => $this->registry->country->code . '-' . $this->di->get('registry')->language->code . '/'
            ));
            
            // Instantiate Backend Cache
            $cache = new $cacheConfig->className($frontCache, $backendOptions);
			return $cache;
		});
        
        /**
		 * Configure viewCache service
		 */
		$this->di->setShared('viewCache', function () {
            $cacheConfig = $this->di->getShared("services")->cache->view;
            
            // Create Cache Frontend
            $frontCacheConfig = array(
                'lifetime' => $cacheConfig->options->lifetime
            );
            $frontCache = new \Phalcon\Cache\Frontend\Output($frontCacheConfig);
            
            // Instantiate Backend Cache
            $cache = new $cacheConfig->className($frontCache, $cacheConfig->options->toArray());
			return $cache;
		});
        
        /**
         * Set up language detection
         */
        $this->setLanguage();

		/**
		 * Registering the application wide router with the standard routes set
		 */
        $this->di->set('router', function () {
            $router = new \App\Router\AppRouter();
            $router->setUriSource(Router::URI_SOURCE_SERVER_REQUEST_URI);
            $router->removeExtraSlashes(true);

            /**
             * Controller and action always default to 'index'
             */
            $router->setDefaults(array(
                'namespace' => 'Modules\Common\Controllers',
                'module' => 'Common',
                'controller' => 'Pages',
                'action' => 'display'
            ));

            $router->add('/', array(
                'alias' => 'home',
                'for' => 'home',
            ))->setName('home');

            $languages = $this->registry->country->languages;
            $languageCodes = array_keys($languages->toArray());
            $router->add('/lang/{lang:(' . implode('|', $languageCodes) . ')}', array(
                'namespace' => 'App\Controllers',
                'module' => null,
                'controller' => 'Language',
                'action' => 'switch'
            ))->setName('setLanguage');

            $router->notFound(array(
                'action' => 'notFound'
            ));
            
            return $router;
        });

        /* Create blank language file */
        $this->di->setShared('translations', function () {
            return new \Phalcon\Config(array());
        });

        /**
         * Register a breadcrumbs
         */
        $this->di->setShared('crumbs', function() {
            return new \App\Lib\Breadcrumbs();
        });
        
        $this->di->set('compareEngine', function () {
            $appConfig = $this->di->getShared('config');
            
            $compareEngineClient = new CompareEngineClient(array(
                'base_url' => array(
                    '{scheme}://{endpoint}/{version}/',
                    array(
                        'scheme' => $appConfig->api->compareEngine->scheme,
                        'endpoint' => $appConfig->api->compareEngine->endpoint,
                        'version' => $appConfig->api->compareEngine->version
                    )
                ),
                'locale' => array(
                    'countryCode'   => $this->registry->country->code,
                    'languageCode'  => $this->registry->language->code
                ),
                'oauth' => array(
                    'token' => $appConfig->api->compareEngine->oauth->token
                ),
                'defaults' => array(
                    'exceptions'        => $appConfig->system->debug, // Allow all exceptions if app is in debug mode
                    'timeout'           => 10,
                    'allow_redirects'   => false,
                    'headers'           => array(
                        'User-Agent' => 'CompareWeb/' . $this->registry->country->code . '-' . Str::slug($appConfig->project->name)
                    )
                )
            ));
            
            return $compareEngineClient;
        });

        $this->di->set('dispatcher', function () {
            //Create an event manager
            $eventsManager = new EventsManager();
            $eventsManager->enablePriorities(true);

            //Attach a listener for type "dispatch"
            $eventsManager->attach("dispatch", function($event, $dispatcher) {
                $matchedRoute = $this->router->getMatchedRoute();
                $matchedPaths = array();
                if($matchedRoute) {
                    $matchedPaths = $matchedRoute->getPaths();
                }
                
                // change language based on route lang
                if(isset($matchedPaths['language'])) {
                    if($matchedPaths['language'] !== $this->registry->language->code) {
                        $this->setLanguage($matchedPaths['language']);
                        $this->response->redirect(ltrim($this->request->getURI(), '/'));
                    }
                }

                $translations = new \Phalcon\Translate\Adapter\NativeArray(array(
                    'content' => $this->di->get('translations')->toArray()
                ));

                $this->registry->breadcrumbs = $this->di->getShared('crumbs');
                $this->registry->breadcrumbs->useTranslation($translations);
                $this->registry->breadcrumbs->add('home', '', 'breadcrumbs_0');
                
                $this->di->get('view')->setVar('lang', $translations);
            });

            $dispatcher = new \Phalcon\Mvc\Dispatcher();

            //Bind the eventsManager to the view component
            $dispatcher->setEventsManager($eventsManager);

            return $dispatcher;
        }, true);
        
        /**
		 * Registering the application wide URL handler
		 */
   		$this->di->set('url', function () {
            $url = new \Phalcon\Mvc\Url();

            $url->setBaseUri($this->config->project->baseUri . '/');
            //remove conflict on minifier
            //$url->setStaticBaseUri($this->config->project->baseUri . '/assets/');
            return $url;
        });

    
        /* for tagging */
        $this->di->set('tag', new \Phalcon\Tag);
        
        /**
         * Register the application wide View service
         */
        $this->di->set('view', function () {
            $view = new \Phalcon\Mvc\View();
            
            // Set views directory to App/Views, will be overridden per module
            $view->setViewsDir(VIEWS_PATH);
            
            // Set default layout
            $view->setLayout('default');

            // default variables
            $view->setVars(array(
                'widget_list' => array(),
                'meta' => array(
                    'keywords' => $this->config->project->default_keywords,
                    'description' => $this->config->project->default_descricption,
                    'image' => $this->config->project->default_image
                ),
                'title' => $this->config->project->default_title
            ));
            
            // Set application wide title
            $this->tag->setTitle($this->di->getShared('config')->project->name);
            
            // Set application wide title separator
            $this->tag->setTitleSeparator(' - ');
            
            // Load application wide CSS and JS files

            $this->assets->collection('outsideJs')
                ->addJs('http://cdnjs.cloudflare.com/ajax/libs/jquery/1.10.2/jquery.min.js', false)
                ->addJs('http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js', false)
                ->addJs('http://cdnjs.cloudflare.com/ajax/libs/json2/20130526/json2.min.js', false)
                ->addJs('http://cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.min.js', false)
                ->addJs('http://cdnjs.cloudflare.com/ajax/libs/numeral.js/1.4.5/numeral.min.js', false);

            $commonJs = $this->assets->collection('commonJs')
                ->addJs('assets/js/bootstrap-checkbox.js', true)
                ->addJs('assets/js/bootstrap-slider.js', true)
                ->addJs('assets/js/bootstrap-switch.js', true)
                ->addJs('assets/js/scrolling.js', true)
                ->addJs('assets/js/main.js', true);

            $this->assets->collection('outsideCss')
                ->addCss('http://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.min.css', false)
                ->addCss('http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css', false);
            
            $commonCss = $this->assets->collection('commonCss')
                ->addCss('assets/css/main.css', true)
                ->addCss('assets/css/product-landing.css', true)
                ->addCss('assets/css/provider-page.css', true);

            // Concatinate and minify assets for staging / production environments
            if(in_array(ENVIRONMENT, array('staging', 'production'))) {
                $gitRevision = exec('cd ' . ROOT_PATH . ' && git rev-parse --short HEAD');
                if($gitRevision) {
                    $hash = md5('Common');
                } else {
                    $hash = md5(ROOT_PATH);
                }
                
                $commonJs->setTargetPath(STATIC_PATH . '/js/common-' . $hash . '.js')
                    ->setTargetUri('static/js/common-' . $hash . '.js')
                    ->join(true)
                    ->addFilter(new Jsmin());

                $commonCss->setTargetPath(STATIC_PATH . '/css/common-' . $hash . '.css')
                    ->setTargetUri('static/css/common-' . $hash . '.css')
                    ->join(true)
                    ->addFilter(new Cssmin());
            }

            $this->addResources('js', array('main'));
            $this->addResources('css', array('main'));
            
            return $view;
        });


        /**
         * Register a widget
         */
        $this->di->set('widget', function() {
            return new \App\Lib\Widget($this->di);
        });
    }

    public function registerModules($modules=array(), $merge = null)
    {
        $mod = array();

        $loader = $this->di->getShared('loader');
        // Open a known directory, and proceed to read its contents
        foreach (glob(MODULES_PATH . "/*", GLOB_NOSORT) as $file) {
            $file_path = $file . "/Module.php";
            if(file_exists($file_path)) {
                $key = str_replace(MODULES_PATH . '/', '', $file);
                $name = str_replace(MODULES_PATH . '/', 'Modules\\', $file) . '\Module';
                $mod[$key]['className'] = $name;
                $mod[$key]['path'] = $file_path;

                if(!class_exists($name, false)) {
                    $loader->registerClasses(array($name => $file_path), true)->register()->autoLoad($name);
                }

                if(method_exists($name, 'initRoutes')) {
                    $name::initRoutes($this->di);
                }

                if(method_exists($name, 'initConfigs')) {
                    $name::initConfigs($this->di);
                }
            }
        }
        parent::registerModules($mod, true);
    }

    /**
     * setLanguage
     * 
     * Determine language from session or read from defined language config
     * 
     * @return \Phalcon\Config
     */
    public function setLanguage($code = null)
    {
        $cookie = $this->di->get('cookies');
        $languages = $this->registry->country->languages;

        if(isset($code)) {
            if(isset($languages[$code])) {
                $cookie->set('language', $languages[$code]->code, time() + 15 * 86400);
                $this->registry->language = $languages[$code];
            }
        } else {
            if($cookie->has('language')) {
                $lang_cookie = $cookie->get('language');
                $this->registry->language = $languages[$lang_cookie->getValue()];
            } else {
                $this->registry->language = reset($languages);
            }
        }
        return $this->registry->language;
    }

    /**
     * Handles the request and echoes its content to the output stream.
     */
    public function run()
    {
        return $this->handle()->getContent();
    }
    
    /**
     * extendConfig
     * 
     * Take the application wide configuration and merge in the chosen file
     * 
     * Call it like so:
     * 
     * $this->extendConfig(array(
     *     'with' => 'myconfig',
     *     'dir' => MODULES_PATH . DS . 'myModule' . DS . 'config'
     * ));
     * 
     * @param array $params
     */
    protected function extendConfig($params=array())
    {
        $defaults = array(
            'type' => 'config',
            'configKey' => 'config',
            'dir' => CONF_PATH,
            'with' => 'env',
            'extension' => '.php'
        );
        
        $options = array_merge($defaults, $params);
        $config = $this->di->getShared($options['configKey']);
        $extConfigFile = $options['dir'] . DS . $options['with'] . $options['extension'];
        
        if(file_exists($extConfigFile)) {
            /**
             * Load configuration
             */
            $extConfig = include $extConfigFile;

            /**
             * Merge extend config with app config
             * if environment config is valid and not empty
             */
            if(is_a($config, "Phalcon\Config") && is_a($extConfig, "Phalcon\Config")) {
                switch ($options['type']) {
                    case 'menu':
                        if(isset($extConfig['main'])) {
                            if(isset($config['main'])) {
                                $config['main']->merge($extConfig['main']);
                            } else {
                                $config['main'] = $extConfig['main'];
                            }
                        } else {
                            $config->merge($extConfig);
                        }

                        break;
                        
                    case 'language':
                        $config->merge($extConfig);
                        break;

                    default:
                        $config->merge($extConfig);
                        break;
                }
            }
        }
    }
    
    /**
     * extendMenu
     * 
     * Take the application wide menu and merge in the chosen file
     * 
     * Call it like so:
     * 
     * $this->extendMenu(array(
     *     'with' => 'menu',
     *     'dir' => MODULES_PATH . DS . 'myModule' . DS . 'menu'
     * ));
     * 
     * @param array $params
     */
    public function extendMenu($params = array())
    {
        $defaults = array(
            'type' => 'menu',
            'configKey' => 'menus',
            'dir' => CONF_PATH,
            'with' => 'menu',
            'extension' => '.php'
        );

        $options = array_merge($defaults, $params);

        return $this->extendConfig($options);
    }
    
    /**
     * extendTranslation
     * 
     * Take the application wide translations and merge in the chosen file
     * 
     * Call it like so:
     * 
     * $this->extendTranslation(array(
     *     'with' => 'lang',
     *     'dir' => MODULES_PATH . DS . 'myModule' . DS . 'config'
     * ));
     * 
     * @param array $params
     */
    public function extendTranslation($params = array())
    {
        $defaults = array(
            'type' => 'translation',
            'configKey' => 'translations',
            'dir' => CONF_PATH,
            'with' => 'lang',
            'extension' => '.php'
        );

        $options = array_merge($defaults, $params);

        return $this->extendConfig($options);
    }


    public function addResources($type=null, $files=array()){
        if(!empty($type) && !empty($files)){
            $include = $this->assets->collection('page'.ucfirst($type));

            if($type == 'js'){
                foreach ($files as $val) {
                    if(file_exists(PUBLIC_PATH.'/Modules/'. $this->router->getModuleName() . '/' . $type . '/'. $val .'.'. $type)){
                        $include->addJs('Modules/'. $this->router->getModuleName() . '/' . $type . '/'. $val .'.'. $type);
                    }
                }
            }else if ($type == 'css') {
                foreach ($files as $val) {
                    if(file_exists(PUBLIC_PATH.'/Modules/'. $this->router->getModuleName() . '/' . $type . '/'. $val .'.'. $type)){
                        $include->addCss('Modules/'. $this->router->getModuleName() . '/' . $type . '/'. $val .'.'. $type);
                    }
                }
            }

            // Concatinate and minify assets for staging / production environments
            if(in_array(ENVIRONMENT, array('staging', 'production'))) {
                $gitRevision = exec('cd ' . ROOT_PATH . ' && git rev-parse --short HEAD');
                if($gitRevision) {
                    $hash = md5( $this->router->getModuleName() );
                } else {
                    $hash = md5(ROOT_PATH);
                }
                
                if($type == 'js'){
                    $include->setTargetPath(STATIC_PATH . '/js/page-' . $hash . '.js')
                        ->setTargetUri('static/js/page-' . $hash . '.js')
                        ->join(true)
                        ->addFilter(new Jsmin());
                }else if ($type == 'css') {  
                    $include->setTargetPath(STATIC_PATH . '/css/page-' . $hash . '.css')
                        ->setTargetUri('static/css/page-' . $hash . '.css')
                        ->join(true)
                        ->addFilter(new Cssmin());
                }
            }
        }
    }

    /**
     * Does a HMVC request inside the application
     *
     * Inside a controller we might do
     * <code>
     * $this->app->request([ 'controller' => 'do', 'action' => 'something' ], 'param');
     * </code>
     *
     * @param array $location Array with the route information: 'namespace', 'module', 'controller', 'action', 'params'
     * @return mixed
     */
    public function request(array $location)
    {
        /** @var \Phalcon\Mvc\Dispatcher $dispatcher */
        $dispatcher = clone $this->di->get('dispatcher');

        if(isset($location['module'])) {
            $dispatcher->setModuleName($location['module']);
        }

        if(isset($location['namespace'])) {
            $dispatcher->setNamespaceName($location['namespace']);
        }

        if(!isset($location['controller'])) {
            $location['controller'] = 'index';
        }

        if(!isset($location['action'])) {
            $location['action'] = 'index';
        }

        if(!isset($location['params'])) {
            $location['params'] = array();
        }

        $dispatcher->setControllerName($location['controller']);
        $dispatcher->setActionName($location['action']);
        $dispatcher->setParams((array) $location['params']);
        $dispatcher->dispatch();

        $response = $dispatcher->getReturnedValue();

        if($response instanceof ResponseInterface) {
            return $response->getContent();
        }

        return $response;
    }

}