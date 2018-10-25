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
	protected function routeDetectStrategy () {
		list($requestCtrlName, $requestActionName) = parent::routeDetectStrategy();
		if (!$this->internalRequest) $this->domainRouting();
		return [$requestCtrlName, $requestActionName];
	}

	protected function routeSetUpDefaultForHomeIfNoMatch () {
		$domainRouteNamespace = NULL;
		if ($this->currentDomainRoute !== NULL) {
			$domainRouteNamespace = $this->currentDomainRoute->GetNamespace();
		}
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
				unset($requestParams['controller'], $requestParams['action']);
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
