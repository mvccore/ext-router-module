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

trait UrlDomain
{
	protected function urlGetDomainRouteAndDefaultDomainParams (array & $params, $moduleParamDefined, $currentDomainRouteMatched) {
		$targetModule = NULL;
		$targetDomainRoute = NULL;
		$domainParamsDefault = [];
		if ($moduleParamDefined) {
			$moduleParamName = static::URL_PARAM_MODULE;
			$targetModule = $params[$moduleParamName];
			if (!isset($this->domainRoutes[$targetModule])) {
				$selfClass = version_compare(PHP_VERSION, '5.5', '>') ? self::class : __CLASS__;
				throw new \InvalidArgumentException(
					"[".$selfClass."] No domain route defined for module: `$targetModule`."
				);
			} else {
				$targetDomainRoute = $this->domainRoutes[$targetModule];
				$routeReverseParamsKeys = $targetDomainRoute->GetReverseParams();
				$routeReverseParamsDefaults = array_fill_keys($routeReverseParamsKeys, NULL);
				$targetDomainRouteDefaults = array_intersect_key($targetDomainRoute->GetDefaults(), $routeReverseParamsDefaults);
				$domainParamsDefault = array_merge($routeReverseParamsDefaults, $targetDomainRouteDefaults);
				$domainParamsDefault[$moduleParamName] = $targetModule;
			}
		} else if ($currentDomainRouteMatched) {
			$targetModule = $this->currentModule;
			$targetDomainRoute = $this->currentDomainRoute;
			$domainParamsDefault = $this->requestedDomainParams;
		}
		return [$targetModule, $targetDomainRoute, $domainParamsDefault];
	}

	protected function urlGetDomainUrlAndClasifyParamsAndDomainParams (array & $params, array & $domainParamsDefault, & $targetDomainRoute) {
		// remove domain module params and complete URL address base part by module domain
		$domainParams = array_intersect_key($params, $domainParamsDefault);
		$params = array_diff_key($params, $domainParamsDefault);
		$defaultDomainParams = array_merge([], $this->GetDefaultParams() ?: []);
		list($domainUrlBaseSection,) = $targetDomainRoute->Url(
			$this->request, $domainParams, $defaultDomainParams, ''
		);
		return $domainUrlBaseSection;
	}
}
