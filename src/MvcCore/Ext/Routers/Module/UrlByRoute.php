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

trait UrlByRoute
{
	/**
	 * Complete relative (or absolute) URL by route instance reverse info. If 
	 * there is defined any target module in `$params`, absolute URL is returned.
	 * Example:
	 *	Input (`\MvcCore\Route::$reverse`):
	 *		`"/products-list/<name>/<color>"`
	 *	Input ($params):
	 *		`array(
	 *			"name"			=> "cool-product-name",
	 *			"color"			=> "red",
	 *			"variant"		=> ["L", "XL"],
	 *			"media_version"	=> "mobile",
	 *			"localization"	=> "en-US",
	 *		);`
	 *	Output (relative):
	 *		`/application/base-bath/products-list/cool-product-name/blue?variant[]=L&amp;variant[]=XL"`
	 *	Output (absolute):
	 *		`https://domain.com/application/base-bath/products-list/cool-product-name/blue?variant[]=L&amp;variant[]=XL"`
	 * @param \MvcCore\Route|\MvcCore\IRoute &$route
	 * @param array $params
	 * @param string $urlParamRouteName
	 * @return string
	 */
	public function UrlByRoute (\MvcCore\IRoute $route, array & $params = [], $urlParamRouteName = NULL) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		$moduleParamName = static::URL_PARAM_MODULE;
		$moduleParamDefined = isset($params[$moduleParamName]);
		$currentDomainRouteMatched = $this->currentDomainRoute !== NULL;
		
		if (
			$route->GetAbsolute() && $moduleParamDefined && $currentDomainRouteMatched &&
			$params[$moduleParamName] !== $this->requestedDomainParams[$moduleParamName]
		) 
			throw new \InvalidArgumentException(
				"[".get_class()."] It's not possible to create URL address "
				."to different module/domain for route defined as absolute."
			);

		list ($targetModule, $targetDomainRoute, $domainParamsDefault) = $this->urlGetDomainRouteAndDefaultDomainParams(
			$params, $moduleParamDefined, $currentDomainRouteMatched
		);

		$domainUrlBaseSection = NULL;
		if ($targetModule !== NULL) 
			$domainUrlBaseSection = $this->urlGetDomainUrlAndClasifyParamsAndDomainParams(
				$params, $domainParamsDefault, $targetDomainRoute
			);
		
		// get domain with base path URL section, 
		// path with query string URL section 
		// and system params for URL prefixes
		list($urlBaseSection, $urlPathWithQuerySection, $systemParams) = $this->urlByRouteSections(
			$route, $params, $urlParamRouteName
		);

		if ($targetModule !== NULL) 
			$systemParams = array_diff_key($systemParams, $domainParamsDefault);
		
		$urlPathWithQueryIsHome = NULL;
		
		// create prefixed URL
		return $this->urlByRoutePrefixSystemParams(
			$domainUrlBaseSection ?: $urlBaseSection, 
			$urlPathWithQuerySection, 
			$systemParams, 
			$urlPathWithQueryIsHome
		);
	}
}
