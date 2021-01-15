<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
 */

namespace MvcCore\Ext\Routers\Module;

trait RewriteRoutingChecks {

	/**
	 * Return `TRUE` if there is possible by additional info array records 
	 * to route request by given route as first argument. For example if route
	 * object has defined http method and request has the same method or not 
	 * or if route is allowed in currently routed module.
	 * @param \MvcCore\Route $route 
	 * @param array $additionalInfo 
	 * @return bool
	 */
	protected function rewriteRoutingCheckRoute (\MvcCore\IRoute $route, array $additionalInfo) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		list ($requestMethod) = $additionalInfo;

		$routeMethod = $route->GetMethod();
		if ($routeMethod !== NULL && $routeMethod !== $requestMethod) return TRUE;

		$modules = $route->GetAdvancedConfigProperty(\MvcCore\Ext\Routers\Modules\IRoute::CONFIG_ALLOWED_MODULES);
		if (is_array($modules) && !in_array($this->currentModule, $modules)) return TRUE;

		return FALSE;
	}
}
