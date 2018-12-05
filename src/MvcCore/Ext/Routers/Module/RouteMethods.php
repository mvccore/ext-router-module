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
	public function & SetDomainRoutes ($routes = [], $autoInitialize = TRUE) {
		if ($autoInitialize) {
			$this->domainRoutes = [];
			$this->AddDomainRoutes($routes);
		} else {
			$newRoutes = [];
			foreach ($routes as $route) 
				$newRoutes[$route->GetModule()] = $route;
			$this->domainRoutes = $newRoutes;
			//$this->anyRoutesConfigured =  count($routes) > 0;
		}
		return $this;
	}

	public function & AddDomainRoutes ($routes, $prepend = FALSE, $throwExceptionForDuplication = TRUE) {
		if ($prepend) $routes = array_reverse($routes);
		$routeClass = static::$routeClass;
		foreach ($routes as $key => $route) {
			$numericKey = is_numeric($key);
			if ($route instanceof \MvcCore\Ext\Routers\Modules\IRoute) {
				if ($numericKey) {
					$routeModule = $route->GetModule();
					if ($routeModule === NULL) {
						$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
						throw new \InvalidArgumentException(
							"[".$selfClass."] Domain route cannot be configured without module "
							."record or without alphanumeric key in given routes collection."
						);
					}
				}
				$this->AddDomainRoute(
					$route, $prepend, $throwExceptionForDuplication
				);
			} else {
				if ($numericKey) {
					$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
					throw new \InvalidArgumentException(
						"[".$selfClass."] Domain route cannot be configured without "
						."alphanumeric key (representing module) in given routes collection."
					);
				}
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
					$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
					throw new \InvalidArgumentException (
						"[".$selfClass."] Route is not possible to assign "
						."(key: \"$key\", value: " . serialize($route) . ")."
					);
				}
			}
		}
		//$this->anyRoutesConfigured = count($routes) > 0;
		return $this;
	}

	public function & AddDomainRoute ($routeCfgOrRoute, $prepend = FALSE, $throwExceptionForDuplication = TRUE) {
		$instance = & $this->getRouteDomainInstance($routeCfgOrRoute);
		$routeModule = $instance->GetModule();
		if (isset($this->domainRoutes[$routeModule]) && $throwExceptionForDuplication) {
			$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
			throw new \InvalidArgumentException(
				"[".$selfClass."] Route with module name `.$routeModule.` "
				."has already been defined between router domain routes."
			);
		}
		if ($prepend) {
			$newItem = [$routeModule => $instance];
			$this->domainRoutes = $newItem + $this->routes;
		} else {
			$this->domainRoutes[$routeModule] = $instance;
		}
		//$this->anyRoutesConfigured = TRUE;
		return $this;
	}

	/**
	 * Get always route instance from given route configuration data or return
	 * already created given instance.
	 * @param \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute|array $routeCfgOrRoute Route instance or
	 *																		   route config array.
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	protected function & getRouteDomainInstance (& $routeCfgOrRoute) {
		if ($routeCfgOrRoute instanceof \MvcCore\Ext\Routers\Modules\IRoute) 
			return $routeCfgOrRoute->SetRouter($this);
		$routeClass = self::$routeDomainClass;
		return $routeClass::CreateInstance($routeCfgOrRoute)->SetRouter($this);
	}
}
