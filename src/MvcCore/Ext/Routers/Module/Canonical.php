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
trait Canonical {

	/**
	 * If request is routed by query string strategy, check if request controller
	 * or request action is the same as default values. Then redirect to shorter
	 * canonical URL. Check also if there is routed/defined any module domain 
	 * route and if there is any, try to complete base URL domain part and also
	 * compare this part with requested URL part. If there is difference, 
	 * redirect to shorter URL version.
	 * @return bool
	 */
	protected function canonicalRedirectQueryStringStrategy () {
		/** @var \MvcCore\Request $request */
		$request = $this->request;
		$redirectToCanonicalUrl = FALSE;
		$requestGlobalGet = & $request->GetGlobalCollection('get');
		$requestedCtrlDc = isset($requestGlobalGet[static::URL_PARAM_CONTROLLER]) ? $requestGlobalGet[static::URL_PARAM_CONTROLLER] : NULL;
		$requestedActionDc = isset($requestGlobalGet[static::URL_PARAM_ACTION]) ? $requestGlobalGet[static::URL_PARAM_ACTION] : NULL;
		$toolClass = self::$toolClass;
		list($dfltCtrlPc, $dftlActionPc) = $this->application->GetDefaultControllerAndActionNames();
		$dfltCtrlDc = $toolClass::GetDashedFromPascalCase($dfltCtrlPc);
		$dftlActionDc = $toolClass::GetDashedFromPascalCase($dftlActionPc);
		$requestedParamsClone = array_merge([], $this->requestedParams);
		if ($requestedCtrlDc !== NULL && $requestedCtrlDc === $dfltCtrlDc) {
			unset($requestedParamsClone[static::URL_PARAM_CONTROLLER]);
			$redirectToCanonicalUrl = TRUE;
		}
		if ($requestedActionDc !== NULL && $requestedActionDc === $dftlActionDc) {
			unset($requestedParamsClone[static::URL_PARAM_ACTION]);
			$redirectToCanonicalUrl = TRUE;
		}
		if ($this->currentDomainRoute !== NULL) {
			$targetDomainRoute = $this->currentDomainRoute;
			$domainParams = array_intersect_key($requestedParamsClone, $this->requestedDomainParams);
			$requestedParamsClone = array_diff_key($requestedParamsClone, $this->requestedDomainParams);
			list($domainUrlBaseSection,) = $targetDomainRoute->Url(
				$this->request, $domainParams, $this->requestedDomainParams, TRUE, ''
			);
			if (mb_strpos($domainUrlBaseSection, '//') === FALSE)
				$domainUrlBaseSection = $request->GetDomainUrl() . $domainUrlBaseSection;
			if (mb_strlen($domainUrlBaseSection) > 0 && $domainUrlBaseSection !== $request->GetBaseUrl()) 
				$redirectToCanonicalUrl = TRUE;
			//x([$domainUrlBaseSection, $request->GetBaseUrl()]);
		}
		if ($redirectToCanonicalUrl) {
			$selfCanonicalUrl = $this->UrlByQueryString($this->selfRouteName, $requestedParamsClone);	
			$this->redirect($selfCanonicalUrl, \MvcCore\IResponse::MOVED_PERMANENTLY);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * If request is routed by rewrite routes strategy, try to complete canonical
	 * URL by current route. Then compare completed base URL part with requested 
	 * base URL part or completed path and query part with requested path and query
	 * part. Check also if there is routed/defined any module domain route and 
	 * if there is any, try to complete base URL domain part and also compare 
	 * this part with requested URL part. If first URL part or second URL part 
	 * is different, redirect to canonical shorter URL.
	 * @return bool
	 */
	protected function canonicalRedirectRewriteRoutesStrategy () {
		/** @var \MvcCore\Request $request */
		$request = $this->request;
		$redirectToCanonicalUrl = FALSE;
		$defaultParams =  $this->GetDefaultParams() ?: [];
		if ($this->currentDomainRoute !== NULL) {
			$targetDomainRoute = $this->currentDomainRoute;
			$domainParams = array_intersect_key($defaultParams, $this->requestedDomainParams);
			$defaultParamsClone = array_diff_key($defaultParams, $this->requestedDomainParams);
			$requestedParamsClone = array_diff_key($this->requestedParams, $this->requestedDomainParams);
			list($domainUrlBaseSection,) = $targetDomainRoute->Url(
				$this->request, $domainParams, $defaultParams, TRUE, ''
			);
			list($selfUrlDomainAndBasePart, $selfUrlPathAndQueryPart) = $this->currentRoute->Url(
				$request, $requestedParamsClone, $defaultParamsClone, TRUE
			);

			if (mb_strpos($domainUrlBaseSection, '//') === FALSE)
				$domainUrlBaseSection = $request->GetDomainUrl() . $domainUrlBaseSection;
			if (mb_strlen($domainUrlBaseSection) > 0 && $domainUrlBaseSection !== $request->GetBaseUrl()) 
				$redirectToCanonicalUrl = TRUE;
			//x([1, $domainUrlBaseSection, $request->GetBaseUrl()]);

		} else {
			$domainUrlBaseSection = NULL;
			list($selfUrlDomainAndBasePart, $selfUrlPathAndQueryPart) =  $this->currentRoute->Url(
				$request, $this->requestedParams, $defaultParams, TRUE
			);
			if (mb_strpos($selfUrlDomainAndBasePart, '//') === FALSE)
				$selfUrlDomainAndBasePart = $request->GetDomainUrl() . $selfUrlDomainAndBasePart;
			if (mb_strlen($selfUrlDomainAndBasePart) > 0 && $selfUrlDomainAndBasePart !== $request->GetBaseUrl()) 
				$redirectToCanonicalUrl = TRUE;
			//x([2, $selfUrlDomainAndBasePart, $request->GetBaseUrl()]);
		}
		
		if (mb_strlen($selfUrlPathAndQueryPart) > 0) {
			$path = $request->GetPath(FALSE);
			$requestedUrl = $path === '' ? '/' : $path ;
			if (mb_strpos($selfUrlPathAndQueryPart, '?') !== FALSE) {
				$selfUrlPathAndQueryPart = rawurldecode($selfUrlPathAndQueryPart);
				$requestedUrl .= $request->GetQuery(TRUE, FALSE);
			}
			//x([3, $selfUrlPathAndQueryPart, $requestedUrl]);
			if ($selfUrlPathAndQueryPart !== $requestedUrl) 
				$redirectToCanonicalUrl = TRUE;
		}
		if ($redirectToCanonicalUrl) {
			$selfCanonicalUrl = $this->Url($this->selfRouteName, $this->requestedParams);
			//x($selfCanonicalUrl);
			//return true;
			$this->redirect($selfCanonicalUrl, \MvcCore\IResponse::MOVED_PERMANENTLY);
			return FALSE;
		}
		return TRUE;
	}
}
