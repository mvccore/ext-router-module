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

trait UrlDomain {

	/**
	 * Get target module, target module domain route (for possibly defined) 
	 * target `module` record in `Url()` method `$params` array) and get module 
	 * domain route default params with default values, necessary to complete 
	 * domain part. Those default params could be requested domain params or 
	 * module domain route default reverse params.
	 * @param array $params `Url()` method `$params` array, it could still contain a `module` record.
	 * @param bool  $moduleParamDefined `TRUE` if there was defined any `module` in `Url()` method `$params` array.
	 * @param bool  $currentDomainRouteMatched `TRUE` if there is matched any current domain route.
	 * @throws \InvalidArgumentException No domain route defined for given module.
	 * @return array `[string|NULL $targetModule, \MvcCore\Ext\Routers\Modules\Route|NULL $targetDomainRoute, array $domainParamsDefault]`
	 */
	protected function urlGetDomainRouteAndDefaultDomainParams (array & $params, $moduleParamDefined, $currentDomainRouteMatched) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		$targetModule = NULL;
		$targetDomainRoute = NULL;
		$domainParamsDefault = [];
		if ($moduleParamDefined) {
			$moduleParamName = static::URL_PARAM_MODULE;
			$targetModule = $params[$moduleParamName];
			if (!isset($this->domainRoutes[$targetModule])) {
				throw new \InvalidArgumentException(
					"[".get_class()."] No domain route defined for module: `{$targetModule}`."
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

	/**
	 * Complete domain URL part by given module domain route and classify params 
	 * necessary to complete URL by module route reverse string and unset those 
	 * params from params array reference. Params array will be changed.
	 * Return URL base part by module domain route.
	 * @param array $params 
	 * @param array $domainParamsDefault 
	 * @param \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute $targetDomainRoute 
	 * @return string
	 */
	protected function urlGetDomainUrlAndClasifyParamsAndDomainParams (array & $params, array & $domainParamsDefault, & $targetDomainRoute) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		// remove domain module params and complete URL address base part by module domain
		$domainParams = array_intersect_key($params, $domainParamsDefault);
		$params = array_diff_key($params, $domainParamsDefault);
		$defaultDomainParams = array_merge([], $this->GetDefaultParams() ?: []);
		list($domainUrlBaseSection,) = $targetDomainRoute->Url(
			$this->request, $domainParams, $defaultDomainParams, '', TRUE
		);
		return $domainUrlBaseSection;
	}
}
