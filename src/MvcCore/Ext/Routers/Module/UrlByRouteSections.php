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

trait UrlByRouteSections {

	/**
	 * Complete semi-finished result URL as two section strings and system 
	 * params array. First section as base section with scheme, domain and base 
	 * path, second section as application requested path and query string and 
	 * third section as system params like `media_version` and/or `localization`.
	 * Those params could be inserted between first two sections as system 
	 * params in result URL by media and localization router behaviour and 
	 * default values.
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
	 *	Output:
	 *		`[
	 *			"/application/base/bath", 
	 *			"/products-list/cool-product-name/blue?variant[]=L&amp;variant[]=XL", 
	 *			["media_version" => "m", "localization" => "en-US"]
	 *		]`
	 * @param \MvcCore\Route &$route
	 * @param array $params
	 * @param string $urlParamRouteName
	 * @return array `string $urlBaseSection, string $urlPathWithQuerySection, array $systemParams`
	 */
	protected function urlByRouteSections (\MvcCore\IRoute $route, array & $params = [], $urlParamRouteName = NULL) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		$defaultParams = array_merge([], $this->GetDefaultParams() ?: []);
		if ($urlParamRouteName == 'self') 
			$params = array_merge($this->requestedParams ?: [], $params);
		
		// complete by given route base URL address part and part with path and query string
		list($urlBaseSection, $urlPathWithQuerySection) = $route->Url(
			$this->request, $params, $defaultParams, $this->getQueryStringParamsSepatator(), TRUE
		);
		
		return [
			$urlBaseSection, 
			$urlPathWithQuerySection, 
			[]
		];
	}
}
