<?php

namespace App\Router;

use \Phalcon\Mvc\Router as Router;

/**
 * This class acts as the application router and defines global application routes.
 * Module specific routes are defined inside the Module classes.
 */
class AppRouter extends Router
{
	/**
	 * Creates a new instance of AppRouter class and defines standard application routes
	 * @param boolean $defaultRoutes
	 */
	public function __construct($defaultRoutes = false)
	{
		parent::__construct($defaultRoutes);
	}
}
