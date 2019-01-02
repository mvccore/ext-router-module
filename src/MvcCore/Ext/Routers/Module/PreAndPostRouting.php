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

trait PreAndPostRouting
{
	/**
	 * Process normal route strategy detection from core router and than process
	 * domain routing, if request is not internal.
	 * Return array with possible query string controller name and action.
	 * @throws \LogicException Route configuration property is missing.
	 * @throws \InvalidArgumentException Wrong route pattern format.
	 * @return array
	 */
	protected function routeDetectStrategy () {
		list($requestCtrlName, $requestActionName) = parent::routeDetectStrategy();
		if (!$this->internalRequest) $this->domainRouting();
		return [$requestCtrlName, $requestActionName];
	}

	/**
	 * After routing is done, check if there is any current domain route and 
	 * remember it's possible namespace value. Then check if there is any current 
	 * route. 
	 * If there is no current route found by any strategy, there is possible to 
	 * create automatically new default route into current route property by
	 * configured default controller/action values. Then it's necessary to check 
	 * if request is targeting homepage or if router is configured to route 
	 * request to default controller and action. If those two conditions are OK, 
	 * create new route with default controller and action but in module router, 
	 * create that new route with controller in namespace by domain route if 
	 * there is any) and set this new route as current route to process default 
	 * controller and action even if there is no route for it.
	 * If there is current route defined and domain route namespace is not 
	 * `NULL`, there is necessary to prepend domain route namespace into routed
	 * controller name and redefine routed target. But prepend domain route
	 * namespace into routed controller only if routed controller definition is 
	 * defined relatively and if it not start with two slashes (`//`) or with 
	 * single backslash (`\`).
	 * @return \MvcCore\Ext\Routers\Module
	 */
	protected function routeSetUpDefaultForHomeIfNoMatch () {
		/** @var $this \MvcCore\Ext\Routers\Module */
		$domainRouteNamespace = NULL;
		if ($this->currentDomainRoute !== NULL) 
			$domainRouteNamespace = $this->currentDomainRoute->GetNamespace();
		if ($this->currentRoute === NULL) {
			$request = & $this->request;
			$requestIsHome = (
				trim($request->GetPath(), '/') == '' || 
				$request->GetPath() == $request->GetScriptName()
			);
			if ($requestIsHome || $this->routeToDefaultIfNotMatch) {
				list($dfltCtrl, $dftlAction) = $this->application->GetDefaultControllerAndActionNames();
				if ($domainRouteNamespace !== NULL) {
					if (substr($dfltCtrl, 0, 2) != '//' && substr($dfltCtrl, 0, 1) != '\\') 
						$dfltCtrl = rtrim($domainRouteNamespace, '\\') . '\\' . $dfltCtrl;
				}
				$this->SetOrCreateDefaultRouteAsCurrent(
					static::DEFAULT_ROUTE_NAME, $dfltCtrl, $dftlAction
				);
				// set up requested params from query string if there are any 
				// (and path if there is path from previous fn)
				$requestParams = array_merge([], $this->request->GetParams(FALSE));
				unset($requestParams[static::URL_PARAM_CONTROLLER], $requestParams[static::URL_PARAM_ACTION]);
				$this->requestedParams = & $requestParams;
			}
		} else if ($domainRouteNamespace !== NULL) {
			$currentRouteCtrl = $this->currentRoute->GetController();
			if (substr($currentRouteCtrl, 0, 2) != '//' && substr($currentRouteCtrl, 0, 1) != '\\') {
				$currentRouteCtrl = rtrim($domainRouteNamespace, '\\') . '\\' . $currentRouteCtrl;
				$this->RedefineRoutedTarget($currentRouteCtrl, NULL, TRUE);
			}
		}
		return $this;
	}
}
