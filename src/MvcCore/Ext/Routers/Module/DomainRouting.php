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

trait DomainRouting
{
	
	protected function domainRouting () {
		$request = & $this->request;
		if ($this->routeGetRequestsOnly) {
			trigger_error("[".__CLASS__."] Routing only GET requests with special media "
			."site version or localization conditions is not allowed in module router.", 
			E_USER_WARNING);
			$this->routeGetRequestsOnly = FALSE;
		}
		/** @var $route \MvcCore\Ext\Routers\Modules\Route */
		$allMatchedParams = [];
		foreach ($this->domainRoutes as & $route) {
			$allMatchedParams = $route->Matches($request);
			if ($allMatchedParams !== NULL) {
				$this->currentDomainRoute = clone $route;
				$this->currentModule = $this->currentDomainRoute->GetModule();
				$this->currentDomainRoute->SetMatchedParams($allMatchedParams);
				$this->domainRoutingSetRequestedAndDefaultParams($allMatchedParams);
				$break = $this->domainRoutingFilterParams($allMatchedParams);
				$this->domainRoutingSetUpRouterByDomainRoute();
				if ($break) break;
			}
		}
	}

	protected function domainRoutingSetRequestedAndDefaultParams (array & $allMatchedParams) {
		/** @var $currentRoute \MvcCore\Route */
		$currentRoute = & $this->currentDomainRoute;
		$allMatchedParams[static::URL_PARAM_MODULE] = $currentRoute->GetModule();
		$this->defaultParams = array_merge(
			$currentRoute->GetDefaults(), $allMatchedParams
		);
		$this->requestedDomainParams = array_merge([], $allMatchedParams);
	}

	protected function domainRoutingFilterParams (array & $allMatchedParams) {
		$request = & $this->request;
		$defaultParamsBefore = array_merge([], $this->defaultParams);
		$requestParams = array_merge([], $this->defaultParams);
		// filter request params
		list($success, $requestParamsFiltered) = $this->currentDomainRoute->Filter(
			$requestParams, $this->defaultParams, \MvcCore\IRoute::CONFIG_FILTER_IN
		);
		if ($success === FALSE) {
			$this->defaultParams = $defaultParamsBefore;
			$this->requestedDomainParams = [];
			$allMatchedParams = [];
			$this->currentDomainRoute = NULL;
			return FALSE;
		}
		$requestParams = array_merge($this->request->GetParams(FALSE), $requestParamsFiltered);
		$this->request->SetParams($requestParams);
		return TRUE;
	}
}
