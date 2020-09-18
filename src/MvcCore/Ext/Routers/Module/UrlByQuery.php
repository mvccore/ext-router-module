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

trait UrlByQuery
{
	/**
	 * Complete relative (or absolute) URL with all params in query string. If 
	 * there is defined any target module in `$params`, absolute URL is returned.
	 * Example: `"/application/base-bath/index.php?controller=ctrlName&amp;action=actionName&amp;name=cool-product-name&amp;color=blue"`
	 * @param string $controllerActionOrRouteName
	 * @param array  $params
	 * @param string $givenRouteName
	 * @return string
	 */
	public function UrlByQueryString ($controllerActionOrRouteName = 'Index:Index', array & $params = [], $givenRouteName = NULL) {
		/** @var $this \MvcCore\Ext\Routers\Module */
		if ($givenRouteName == 'self') {
			$params = array_merge($this->requestedParams ?: [], $params);
			if ($controllerActionOrRouteName === static::DEFAULT_ROUTE_NAME && isset($params[static::URL_PARAM_PATH]))
				unset($params[static::URL_PARAM_PATH]);
		}

		list($targetModule, $targetDomainRoute, $domainParamsDefault) = $this->urlGetDomainRouteAndDefaultDomainParams(
			$params, isset($params[static::URL_PARAM_MODULE]), $this->currentDomainRoute !== NULL
		);

		if ($targetModule !== NULL) {

			$domainUrlBaseSection = $this->urlGetDomainUrlAndClasifyParamsAndDomainParams(
				$params, $domainParamsDefault, $targetDomainRoute
			);

			list($ctrlPc, $actionPc) = $this->urlByQueryStringCompleteCtrlAction(
				$controllerActionOrRouteName, $params
			);

			$absolute = $this->urlGetAbsoluteParam($params);

			$result = $this->urlByQueryStringCompleteResult(
				$ctrlPc, $actionPc, $params
			);

			if ($domainUrlBaseSection !== NULL) {
				$result = $domainUrlBaseSection . $result;
			} else {
				$result = $this->request->GetBasePath() . $result;
				if ($absolute) 
					$result = $this->request->GetDomainUrl() . $result;
			}

			return $result;
		} else {
			return parent::UrlByQueryString($controllerActionOrRouteName, $params, $givenRouteName);
		}		
	}
}
