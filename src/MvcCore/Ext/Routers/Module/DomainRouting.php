<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENSE.md
 */

namespace MvcCore\Ext\Routers\Module;

/**
 * @mixin \MvcCore\Ext\Routers\Module
 */
trait DomainRouting {

	/**
	 * Process routing by defined module domain routes. If any module domain 
	 * route matches the request, complete current domain route property and 
	 * current domain module property and set up requested domain params and
	 * default domain params by matched domain route params.
	 * @throws \LogicException Route configuration property is missing.
	 * @throws \InvalidArgumentException Wrong route pattern format.
	 * @return void
	 */
	protected function domainRouting () {
		if ($this->routeGetRequestsOnly) {
			trigger_error("[".get_class()."] Routing only GET requests with special media "
			."site version or localization conditions is not allowed in module router.", 
			E_USER_WARNING);
			$this->routeGetRequestsOnly = FALSE;
		}
		/** @var \MvcCore\Ext\Routers\Modules\Route $route */
		$allMatchedParams = [];
		foreach ($this->domainRoutes as $route) {
			$allMatchedParams = $route->Matches($this->request);
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

	/**
	 * If module domain route has been matched, complete requested domain params
	 * and set up default params (before normal routing) with params from 
	 * matched domain route.
	 * @param array $allMatchedParams 
	 * @return void
	 */
	protected function domainRoutingSetRequestedAndDefaultParams (array & $allMatchedParams) {
		/** @var \MvcCore\Route $currentRoute */
		$currentRoute = $this->currentDomainRoute;
		$allMatchedParams[static::URL_PARAM_MODULE] = $currentRoute->GetModule();
		$this->defaultParams = array_merge(
			$currentRoute->GetDefaults(), $allMatchedParams
		);
		$this->requestedDomainParams = array_merge([], $allMatchedParams);
	}

	/**
	 * 
	 * @param array $allMatchedParams 
	 * @return bool
	 */
	protected function domainRoutingFilterParams (array & $allMatchedParams) {
		$request = $this->request;
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
		$requestParams = array_merge(
			[], 
			$request->GetParams(
				FALSE, [], \MvcCore\IRequest::PARAM_TYPE_QUERY_STRING | \MvcCore\IRequest::PARAM_TYPE_URL_REWRITE
			), 
			$requestParamsFiltered
		);
		foreach ($requestParamsFiltered as $requestParamName => $requestParamValue) {
			$sourceType = $request->GetParamSourceType($requestParamName);
			if (!$sourceType) $sourceType = \MvcCore\IRequest::PARAM_TYPE_URL_REWRITE;
			$request->SetParam($requestParamName, $requestParamValue, $sourceType);
		}
		return TRUE;
	}
}
