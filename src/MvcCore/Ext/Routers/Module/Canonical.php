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

trait Canonical
{
	protected function canonicalRedirectQueryStringStrategy () {
		/** @var $req \MvcCore\Request */
		$req = & $this->request;
		$redirectToCanonicalUrl = FALSE;
		$requestGlobalGet = & $req->GetGlobalCollection('get');
		$requestedCtrlDc = isset($requestGlobalGet['controller']) ? $requestGlobalGet['controller'] : NULL;
		$requestedActionDc = isset($requestGlobalGet['action']) ? $requestGlobalGet['action'] : NULL;
		$toolClass = self::$toolClass;
		list($dfltCtrlPc, $dftlActionPc) = $this->application->GetDefaultControllerAndActionNames();
		$dfltCtrlDc = $toolClass::GetDashedFromPascalCase($dfltCtrlPc);
		$dftlActionDc = $toolClass::GetDashedFromPascalCase($dftlActionPc);
		$requestedParamsClone = array_merge([], $this->requestedParams);
		if ($requestedCtrlDc !== NULL && $requestedCtrlDc === $dfltCtrlDc) {
			unset($requestedParamsClone['controller']);
			$redirectToCanonicalUrl = TRUE;
		}
		if ($requestedActionDc !== NULL && $requestedActionDc === $dftlActionDc) {
			unset($requestedParamsClone['action']);
			$redirectToCanonicalUrl = TRUE;
		}
		if ($this->currentDomainRoute !== NULL) {
			$targetDomainRoute = $this->currentDomainRoute;
			$domainParams = array_intersect_key($requestedParamsClone, $this->requestedDomainParams);
			$requestedParamsClone = array_diff_key($requestedParamsClone, $this->requestedDomainParams);
			list($domainUrlBaseSection,) = $targetDomainRoute->Url(
				$this->request, $domainParams, $this->requestedDomainParams, ''
			);
			if (mb_strpos($domainUrlBaseSection, '//') === FALSE)
				$domainUrlBaseSection = $req->GetDomainUrl() . $domainUrlBaseSection;
			if (mb_strlen($domainUrlBaseSection) > 0 && $domainUrlBaseSection !== $req->GetBaseUrl()) 
				$redirectToCanonicalUrl = TRUE;
			//x([$domainUrlBaseSection, $req->GetBaseUrl()]);
		}
		if ($redirectToCanonicalUrl) {
			$selfCanonicalUrl = $this->UrlByQueryString($this->selfRouteName, $requestedParamsClone);	
			$this->redirect($selfCanonicalUrl, \MvcCore\IResponse::MOVED_PERMANENTLY);
			return FALSE;
		}
		return TRUE;
	}

	protected function canonicalRedirectRewriteRoutesStrategy () {
		/** @var $req \MvcCore\Request */
		$req = & $this->request;
		$redirectToCanonicalUrl = FALSE;
		$defaultParams =  $this->GetDefaultParams() ?: [];
		if ($this->currentDomainRoute !== NULL) {
			$targetDomainRoute = $this->currentDomainRoute;
			$domainParams = array_intersect_key($defaultParams, $this->requestedDomainParams);
			$defaultParamsClone = array_diff_key($defaultParams, $this->requestedDomainParams);
			$requestedParamsClone = array_diff_key($this->requestedParams, $this->requestedDomainParams);
			list($domainUrlBaseSection,) = $targetDomainRoute->Url(
				$this->request, $domainParams, $defaultParams, ''
			);
			list($selfUrlDomainAndBasePart, $selfUrlPathAndQueryPart) = $this->currentRoute->Url(
				$req, $requestedParamsClone, $defaultParamsClone, $this->getQueryStringParamsSepatator()
			);
			if (mb_strpos($domainUrlBaseSection, '//') === FALSE)
				$domainUrlBaseSection = $req->GetDomainUrl() . $domainUrlBaseSection;
			if (mb_strlen($domainUrlBaseSection) > 0 && $domainUrlBaseSection !== $req->GetBaseUrl()) 
				$redirectToCanonicalUrl = TRUE;
			//x([1, $domainUrlBaseSection, $req->GetBaseUrl()]);

		} else {
			$domainUrlBaseSection = NULL;
			list($selfUrlDomainAndBasePart, $selfUrlPathAndQueryPart) =  $this->currentRoute->Url(
				$req, $this->requestedParams, $defaultParams, $this->getQueryStringParamsSepatator()
			);
			if (mb_strpos($selfUrlDomainAndBasePart, '//') === FALSE)
				$selfUrlDomainAndBasePart = $req->GetDomainUrl() . $selfUrlDomainAndBasePart;
			if (mb_strlen($selfUrlDomainAndBasePart) > 0 && $selfUrlDomainAndBasePart !== $req->GetBaseUrl()) 
				$redirectToCanonicalUrl = TRUE;
			//x([2, $selfUrlDomainAndBasePart, $req->GetBaseUrl()]);
		}
		
		if (mb_strlen($selfUrlPathAndQueryPart) > 0) {
			$path = $req->GetPath(TRUE);
			$requestedUrl = $path === '' ? '/' : $path ;
			if (mb_strpos($selfUrlPathAndQueryPart, '?') !== FALSE) {
				$selfUrlPathAndQueryPart = rawurldecode($selfUrlPathAndQueryPart);
				$requestedUrl .= $req->GetQuery(TRUE, TRUE);
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
