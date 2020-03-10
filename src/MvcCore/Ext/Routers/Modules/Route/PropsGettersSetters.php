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

trait PropsGettersSetters
{
	/**
	 * Required, application module name. Any custom string to define part of
	 * application under specific domain, routed by this object.
	 * @required
	 * @var string|NULL
	 */
	protected $module = NULL;

	/**
	 * Optional, target controller namespace - used if routed controller defined
	 * by standard route is not defined absolutely. This namespace is necessary
	 * to define relatively from standard application controller namespace to
	 * target controllers directory (namespace).
	 * @var string|NULL
	 */
	protected $namespace = NULL;

	/**
	 * Optional, allowed localizations for the routed module if there is used
	 * any variant of module router with localization.
	 * @var array
	 */
	protected $allowedLocalizations = NULL;

	/**
	 * Optional, allowed localizations for the routed module if there is used
	 * any variant of module router with media (device) definition.
	 * @var array
	 */
	protected $mediaVersions = NULL;


	/**
	 * Get application module name. Any custom string to define part of
	 * application under specific domain, routed by this object.
	 * @return string|NULL
	 */
	public function GetModule () {
		return $this->module;
	}

	/**
	 * Set application module name. Any custom string to define part of
	 * application under specific domain, routed by this object.
	 * @param string|NULL $module
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetModule ($module) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		$this->module = $module;
		return $this;
	}

	/**
	 * Get target controller namespace - used if routed controller defined by
	 * standard route is not defined absolutely. This namespace is necessary to
	 * define relatively from standard application controller namespace to
	 * target controllers directory (namespace).
	 * @return string|NULL
	 */
	public function GetNamespace () {
		return $this->namespace;
	}

	/**
	 * Set target controller namespace - used if routed controller defined by
	 * standard route is not defined absolutely. This namespace is necessary to
	 * define relatively from standard application controller namespace to
	 * target controllers directory (namespace).
	 * @param string|NULL $namespace
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetNamespace ($namespace) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * Get allowed localizations for the routed module if there is used
	 * any variant of module router with localization.
	 * @return \string[]
	 */
	public function GetAllowedLocalizations () {
		return $this->allowedLocalizations
			? array_keys($this->allowedLocalizations)
			: [];
	}

	/**
	 * Set allowed localizations for the routed module if there is used
	 * any variant of module router with localization.
	 * @var \string[] $allowedLocalizations..., International lower case language
	 *											code(s) (+ optionally dash character
	 *											+ upper case international locale code(s))
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetAllowedLocalizations (/* ...$allowedLocalizations */) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		$allowedLocalizations = func_get_args();
		if (count($allowedLocalizations) === 1 && is_array($allowedLocalizations[0]))
			$allowedLocalizations = $allowedLocalizations[0];
		$this->allowedLocalizations = array_combine($allowedLocalizations, $allowedLocalizations);
		return $this;
	}

	/**
	 * Get allowed localizations for the routed module if there is used
	 * any variant of module router with media (device) definition.
	 * @return array
	 */
	public function & GetAllowedMediaVersions () {
		if (!$this->allowedMediaVersions) 
			$this->allowedMediaVersions = [];
		return $this->allowedMediaVersions;
	}

	/**
	 * Set allowed localizations for the routed module if there is used
	 * any variant of module router with media (device) definition.
	 * @param array $allowedMediaVersionsAndUrlValues
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetAllowedMediaVersions ($allowedMediaVersionsAndUrlValues = []) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		$this->allowedMediaVersions = $allowedMediaVersionsAndUrlValues;
		return $this;
	}


	/* NOT USED METHODS IN MODULE DOMAIN ROUTE CLASS: *************************/

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * Use method `GetModule()` instead.
	 * @return NULL
	 */
	public function GetName () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * Use method `SetModule($module)` instead.
	 * @param string $name
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetName ($name) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return NULL
	 */
	public function GetController () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param string $controller
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetController ($controller) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return NULL
	 */
	public function GetAction () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param string $action
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetAction ($action) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return NULL
	 */
	public function GetControllerAction () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param string $controllerAction
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetControllerAction ($controllerAction) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return NULL
	 */
	public function GetMethod () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param string $method
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetMethod ($method = NULL) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return NULL
	 */
	public function GetRedirect () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param string $redirectRouteName
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetRedirect ($redirectRouteName = NULL) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return bool
	 */
	public function GetAbsolute () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return TRUE;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param bool $absolute
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetAbsolute ($absolute = TRUE) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @return NULL
	 */
	public function GetGroupName () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	/**
	 * THIS METHOD IS NOT USED IN MODULE DOMAIN ROUTE CLASS.
	 * @param string $groupName
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function SetGroupName ($groupName) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		return $this->trriggerUnusedMethodError(__METHOD__);
	}

	/**
	 * Trigger `E_USER_WARNING` user error about not used method in this
	 * extended module domain route.
	 * @param string $method
	 * @return \MvcCore\Ext\Routers\Modules\IRoute
	 */
	protected function trriggerUnusedMethodError ($method) {
		/** @var $this \MvcCore\Ext\Routers\Modules\IRoute */
		trigger_error("[".get_class()."] The method `{$method}` is not used in this extended class.", E_USER_WARNING);
		return $this;
	}
}
