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

namespace MvcCore\Ext\Routers\Modules\Route;

trait UrlBuilding
{
	public function Url (\MvcCore\IRequest & $request, array & $params = [], array & $defaultUrlParams = [], $queryStringParamsSepatator = '&') {
		// check reverse initialization
		if ($this->reverseParams === NULL) $this->initReverse();
		// complete and filter all params to build reverse pattern
		if (count($this->reverseParams) === 0) {
			$allParamsClone = array_merge([], $params);
		} else {// complete params with necessary values to build reverse pattern (and than query string)
			$emptyReverseParams = array_fill_keys(array_keys($this->reverseParams), '');
			$allMergedParams = array_merge($this->defaults, $defaultUrlParams, $params);
			// all params clone contains only keys necessary to build reverse 
			// patern for this route and all given `$params` keys, nothing more 
			// from currently requested url
			$allParamsClone = array_merge(
				$emptyReverseParams, array_intersect_key($allMergedParams, $emptyReverseParams), $params
			);
		}
		// filter params
		list(,$filteredParams) = $this->Filter($allParamsClone, $defaultUrlParams, \MvcCore\IRoute::CONFIG_FILTER_OUT);
		// convert all domain param values to lowercase
		$router = & $this->router;
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
		return $this->urlSplitResultToBaseAndPathWithQuery($request, $result, $domainPercentageParams);
	}
}
