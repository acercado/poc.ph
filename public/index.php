<?php
error_reporting(E_ALL);

use \Phalcon\DI\FactoryDefault;

// Set constant for ROOT_PATH
define('DS', DIRECTORY_SEPARATOR);

// Set constant for ROOT_PATH
define('ROOT_PATH', realpath(__DIR__ . DS . '..' . DS));

// Set constant for PUBLIC_PATH
define('PUBLIC_PATH', realpath(__DIR__));

// Set constant for APP_PATH
define('APP_PATH', realpath('..' .DS . 'App' . DS));

// Set constant for ASSETS_PATH
define('ASSETS_PATH', realpath(PUBLIC_PATH . DS . 'assets' . DS));

// Set constant for STATIC_PATH
define('STATIC_PATH', realpath(PUBLIC_PATH . DS . 'static' . DS));

// Set constant for CONF_PATH
define('CONF_PATH', realpath(APP_PATH . DS . 'config' . DS));

// Set constant for MODULES_PATH
define('MODULES_PATH', realpath(ROOT_PATH . DS . 'Modules' . DS));

// Set constant for VIEWS_PATH
define('VIEWS_PATH', realpath(APP_PATH . DS . 'Views' . DS));

// Set constant for WIDGET_PATH
define('WIDGET_PATH', realpath(VIEWS_PATH . DS . 'widgets' . DS));

/**
 * Set ENVIRONMENT constant
 * local, development, staging, production
 */
define('ENVIRONMENT', 'development');

try {

    /**
     * standalone builtin php functions
     */
    require APP_PATH . '/Lib/HelpMe.php';

    /**
     * Include Composer libraries
     */
    require ROOT_PATH . DS . 'vendor' . DS . 'autoload.php';
    
    /**
     * Include Application definition
     */
    require APP_PATH . DS . 'App.php';

    /**
     * Handle the request
     */
    $app = new App\App(new FactoryDefault());

    echo $app->run();

} catch (Phalcon\Exception $e) {
    echo $e->getMessage();
} catch (PDOException $e) {
    echo $e->getMessage();
}
