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

namespace MvcCore\Ext\Routers\Modules\Route;

/**
 * @mixin \MvcCore\Ext\Routers\Modules\Route
 */
trait UrlBuilding {

	/**
	 * Complete route URL by given params array and route internal reverse 
	 * replacements pattern string. If there are more given params in first 
	 * argument than total count of replacement places in reverse pattern,
	 * then create URL with query string params after reverse pattern, 
	 * containing that extra record(s) value(s). Returned is an array with two 
	 * strings - result URL in two parts - first part as scheme, domain and base 
	 * path and second as path and query string.
	 * Example:
	 *	Input (`$params`):
	 *		`[
	 *			"name"		=> "cool-product-name",
	 *			"color"		=> "blue",
	 *			"variants"	=> ["L", "XL"],
	 *		];`
	 *	Input (`\MvcCore\Route::$reverse`):
	 *		`"/products-list/<name>/<color*>"`
	 *	Output:
	 *		`[
	 *			"https://example.com/any/app/base/path", 
	 *			"/products-list/cool-product-name/blue?variant[]=L&amp;variant[]=XL"
	 *		]`
	 * @param \MvcCore\Request	$request 
	 *							Currently requested request object.
	 * @param array				$params
	 *							URL params from application point completed 
	 *							by developer.
	 * @param array				$defaultUrlParams 
	 *							Requested URL route params and query string 
	 *							params without escaped HTML special chars: 
	 *							`< > & " ' &`.
	 * @param string			$queryStringParamsSepatator 
	 *							Query params separator, `&` by default. Always 
	 *							automatically completed by router instance.
	 * @param bool				$splitUrl
	 *							Boolean value about to split completed result URL
	 *							into two parts or not. Default is FALSE to return 
	 *							a string array with only one record - the result 
	 *							URL. If `TRUE`, result url is split into two 
	 *							parts and function return array with two items.
	 * @return \string[]		Result URL address in array. If last argument is 
	 *							`FALSE` by default, this function returns only 
	 *							single item array with result URL. If last 
	 *							argument is `TRUE`, function returns result URL 
	 *							in two parts - domain part with base path and 
	 *							path part with query string.
	 */
	public function Url (\MvcCore\IRequest $request, array & $params = [], array & $defaultUrlParams = [], $queryStringParamsSepatator = '&', $splitUrl = FALSE) {
		// check reverse initialization
		if ($this->reverseParams === NULL) $this->initReverse();
		// complete and filter all params to build reverse pattern
		if (count($this->reverseParams) === 0) {
			$allParamsClone = array_merge([], $params);
		} else {// complete params with necessary values to build reverse pattern (and than query string)
			$emptyReverseParams = array_fill_keys(array_keys($this->reverseParams), NULL);
			$allMergedParams = array_merge($this->defaults, $defaultUrlParams, $params);
			// all params clone contains only keys necessary to build reverse 
			// pattern for this route and all given `$params` keys, nothing more 
			// from currently requested URL
			$allParamsClone = array_merge(
				$emptyReverseParams, array_intersect_key($allMergedParams, $emptyReverseParams), $params
			);
		}
		// filter params
		list(,$filteredParams) = $this->Filter($allParamsClone, $defaultUrlParams, \MvcCore\IRoute::CONFIG_FILTER_OUT);
		$filteredParams = $filteredParams ?: [];
		// convert all domain param values to lower case
		$router = $this->router;
		foreach ($filteredParams as $paramName => & $paramValue) {
			if ($paramName == $router::URL_PARAM_BASEPATH) continue;
			if (is_string($paramValue)) $paramValue = mb_strtolower($paramValue);
		}
		// split params into domain params array and into path and query params array
		$domainPercentageParams = $this->urlGetAndRemoveDomainPercentageParams($filteredParams);
		// build reverse pattern
		$result = $this->urlComposeByReverseSectionsAndParams(
			$this->reverse, 
			$this->reverseSections, 
			$this->reverseParams, 
			$filteredParams, 
			$this->defaults
		);
		return $this->urlAbsPartAndSplit($request, $result, $domainPercentageParams, $splitUrl);
	}
}
