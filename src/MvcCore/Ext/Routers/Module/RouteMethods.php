<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flídr (https://github.com/mvccore/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/4.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Routers\Module;

trait RouteMethods
{
	/**
	 * Clear all possible previously configured module domain routes and set new 
	 * given routes again collection again. If there is no module property 
	 * configured in given route item in array configuration, route module is set
	 * by given `$routes` array key, if key is not numeric.
	 *
	 * Routes could be defined in various forms:
	 * Example:
	 * `\MvcCore\Router::GetInstance()->SetDomainRoutes([
	 *		"blog"	=> [
	 *			"pattern"				=> "//blog.%sld%.%tld%",
	 *			"namespace"				=> "Blog",
	 *			"defaults"				=> ["page" => 1],
	 *			"constraints"			=> ["page" => "\d+"],
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		],
	 *		"main"	=> [
	 *			"pattern"				=> "//%domain%",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		]
	 * ]);`
	 * or:
	 * `\MvcCore\Router::GetInstance()->SetDomainRoutes([
	 *		new \MvcCore\Ext\Routers\Modules\Route(
	 *			"//blog.%sld%.%tld%",	// pattern
	 *			"blog",					// module
	 *			"Blog",					// namespace
	 *			["page" => 1],			// defaults
	 *			["page" => "\d+"],		// constraints
	 *			[						// advanced configuration
	 *				"allowedLocalizations"	=> ["en-US"],
	 *				"allowedMediaVersions"	=> ["full" => ""]
	 *			]
	 *		),
	 *		new \MvcCore\Ext\Routers\Modules\Route([
	 *			"pattern"				=> "//%domain%",
	 *			"module"				=> "main",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		])
	 * ]);`
	 * @param \MvcCore\Ext\Routers\Modules\Route[]|\MvcCore\Ext\Routers\Modules\IRoute[]|array|array[] $routes 
	 * @param bool $autoInitialize 
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Routers\Module|\MvcCore\Ext\Routers\IModule
	 */
	public function SetDomainRoutes ($routes = [], $autoInitialize = TRUE) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		if ($autoInitialize) {
			$this->domainRoutes = [];
			$this->AddDomainRoutes($routes);
		} else {
			$newRoutes = [];
			foreach ($routes as $route) 
				$newRoutes[$route->GetModule()] = $route;
			$this->domainRoutes = $newRoutes;
		}
		return $this;
	}

	/**
	 * Append or prepend new module domain routes. If there is no module property 
	 * configured in given route item in array configuration, route module is set
	 * by given `$routes` array key, if key is not numeric.
	 *
	 * Routes could be defined in various forms:
	 * Example:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoutes([
	 *		"blog"	=> [
	 *			"pattern"				=> "//blog.%sld%.%tld%",
	 *			"namespace"				=> "Blog",
	 *			"defaults"				=> ["page" => 1],
	 *			"constraints"			=> ["page" => "\d+"],
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		],
	 *		"main"	=> [
	 *			"pattern"				=> "//%domain%",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		]
	 * ]);`
	 * or:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoutes([
	 *		new \MvcCore\Ext\Routers\Modules\Route(
	 *			"//blog.%sld%.%tld%",	// pattern
	 *			"blog",					// module
	 *			"Blog",					// namespace
	 *			["page" => 1],			// defaults
	 *			["page" => "\d+"],		// constraints
	 *			[						// advanced configuration
	 *				"allowedLocalizations"	=> ["en-US"],
	 *				"allowedMediaVersions"	=> ["full" => ""]
	 *			]
	 *		),
	 *		new \MvcCore\Ext\Routers\Modules\Route([
	 *			"pattern"				=> "//%domain%",
	 *			"module"				=> "main",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		])
	 * ]);`
	 * @param \MvcCore\Ext\Routers\Modules\Route[]|\MvcCore\Ext\Routers\Modules\IRoute[]|array|array[] $routes 
	 * @param bool $prepend 
	 * @param bool $throwExceptionForDuplication 
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Routers\Module|\MvcCore\Ext\Routers\IModule
	 */
	public function AddDomainRoutes ($routes, $prepend = FALSE, $throwExceptionForDuplication = TRUE) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		if ($prepend) $routes = array_reverse($routes);
		$routeClass = static::$routeClass;
		foreach ($routes as $key => $route) {
			$numericKey = is_numeric($key);
			if ($route instanceof \MvcCore\Ext\Routers\Modules\IRoute) {
				if ($numericKey) {
					$routeModule = $route->GetModule();
					if ($routeModule === NULL) 
						throw new \InvalidArgumentException(
							"[".get_class()."] Domain route cannot be configured without module "
							."record or without alphanumeric key in given routes collection."
						);
				}
				$this->AddDomainRoute(
					$route, $prepend, $throwExceptionForDuplication
				);
			} else {
				if ($numericKey) 
					throw new \InvalidArgumentException(
						"[".get_class()."] Domain route cannot be configured without "
						."alphanumeric key (representing module) in given routes collection."
					);
				if (is_array($route)) {
					if (!isset($route['module'])) $route['module'] = $key;
					$this->AddDomainRoute(
						$this->getRouteDomainInstance($route), 
						$prepend, $throwExceptionForDuplication
					);
				} else if (is_string($route)) {
					$routeCfgData = [
						'pattern'	=> $route,
						'module'	=> $key,
					];
					$this->AddDomainRoute(
						$routeClass::CreateInstance($routeCfgData), 
						$prepend, $throwExceptionForDuplication
					);
				} else {
					throw new \InvalidArgumentException (
						"[".get_class()."] Route is not possible to assign "
						."(key: \"{$key}\", value: " . serialize($route) . ")."
					);
				}
			}
		}
		return $this;
	}

	/**
	 * Append or prepend new module domain route.
	 * Example:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoute([
	 *		"blog"	=> [
	 *			"pattern"				=> "//blog.%sld%.%tld%",
	 *			"namespace"				=> "Blog",
	 *			"defaults"				=> ["page" => 1],
	 *			"constraints"			=> ["page" => "\d+"],
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		],
	 *		"main"	=> [
	 *			"pattern"				=> "//%domain%",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		]
	 * ]);`
	 * or:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoute([
	 *		new \MvcCore\Ext\Routers\Modules\Route(
	 *			"//blog.%sld%.%tld%",	// pattern
	 *			"blog",					// module
	 *			"Blog",					// namespace
	 *			["page" => 1],			// defaults
	 *			["page" => "\d+"],		// constraints
	 *			[						// advanced configuration
	 *				"allowedLocalizations"	=> ["en-US"],
	 *				"allowedMediaVersions"	=> ["full" => ""]
	 *			]
	 *		),
	 *		new \MvcCore\Ext\Routers\Modules\Route([
	 *			"pattern"				=> "//%domain%",
	 *			"module"				=> "main",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		])
	 * ]);`
	 * @param \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute|array $routeCfgOrRoute 
	 * @param bool $prepend 
	 * @param bool $throwExceptionForDuplication 
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Routers\Module|\MvcCore\Ext\Routers\IModule
	 */
	public function AddDomainRoute ($routeCfgOrRoute, $prepend = FALSE, $throwExceptionForDuplication = TRUE) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		$instance = $this->getRouteDomainInstance($routeCfgOrRoute);
		$routeModule = $instance->GetModule();
		if (isset($this->domainRoutes[$routeModule]) && $throwExceptionForDuplication) 
			throw new \InvalidArgumentException(
				"[".get_class()."] Route with module name `.$routeModule.` "
				."has already been defined between router domain routes."
			);
		if ($prepend) {
			$newItem = [$routeModule => $instance];
			$this->domainRoutes = $newItem + $this->routes;
		} else {
			$this->domainRoutes[$routeModule] = $instance;
		}
		return $this;
	}

	/**
	 * Get always route instance from given route configuration data or return
	 * already created given instance.
	 * @param \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute|array $routeCfgOrRoute 
	 *		  Route instance or route config array.
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	protected function getRouteDomainInstance (& $routeCfgOrRoute) {
		if ($routeCfgOrRoute instanceof \MvcCore\Ext\Routers\Modules\IRoute) 
			return $routeCfgOrRoute->SetRouter($this);
		$routeClass = self::$routeDomainClass;
		return $routeClass::CreateInstance($routeCfgOrRoute)->SetRouter($this);
	}
}
