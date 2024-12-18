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
trait Redirect {

	/**
	 * Redirect to target media site version with path and by cloned request 
	 * object global `$_GET` collection. Return always `FALSE`.
	 * @param array $systemParams 
	 * @return bool
	 */
	protected function redirectToVersion ($systemParams) {
		/** @var \MvcCore\Request $request */
		$request = $this->request;
		
		// get domain with base path URL section, 
		// path with query string URL section 
		// system params for URL prefixes
		// and if path with query string URL section targeting `/` or `/index.php`
		$targetModule = NULL;
		$domainParamsDefault = [];
		$domainParams = [];
		if ($this->currentDomainRoute !== NULL) {
			$targetModule = $this->currentModule;
			$domainParamsDefault = $this->requestedDomainParams;
		}

		if ($targetModule !== NULL) {
			// remove domain module params and complete URL address base part by module domain
			$domainParams = array_intersect_key($systemParams, $domainParamsDefault);
			//$systemParamsClone = array_diff_key($systemParams, $domainParamsDefault);
			$defaultDomainParams = array_merge([], $this->GetDefaultParams() ?: []);
			
			if (method_exists($this, 'redirectCorrectDomainSystemParams')) 
				$this->redirectCorrectDomainSystemParams($domainParams);

			list($domainUrlBaseSection,) = $this->currentDomainRoute->Url(
				$request, $domainParams, $defaultDomainParams, FALSE, ''
			);
		} else {
			$domainUrlBaseSection = NULL;
		}
		
		list ($urlBaseSection, $urlPathWithQuerySection, $systemParams, $urlPathWithQueryIsHome) 
			= $this->redirectToVersionSections($systemParams);

		$targetUrl = $this->urlByRoutePrefixSystemParams(
			$domainUrlBaseSection ?: $urlBaseSection, 
			$urlPathWithQuerySection, 
			array_diff_key($systemParams, $domainParamsDefault), 
			$urlPathWithQueryIsHome
		);

		
		$questionMarkPos = mb_strpos($targetUrl, '?');
		if ($questionMarkPos !== FALSE) $targetUrl = mb_substr($targetUrl, 0, $questionMarkPos);

		$fullOriginalUrl = $request->GetBaseUrl() . $request->GetOriginalPath();
		
		if ($fullOriginalUrl === $targetUrl) return TRUE;

		$this->redirect(
			$targetUrl, 
			$this->redirectStatusCode, 
			'Module router redirect'
		);

		return FALSE;
	}
}
