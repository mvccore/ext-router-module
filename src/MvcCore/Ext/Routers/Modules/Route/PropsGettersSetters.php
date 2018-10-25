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
	 * @var string|NULL
	 */
	protected $module = NULL;

	/**
	 * @var string|NULL
	 */
	protected $namespace = NULL;

	/**
	 * @var array
	 */
	protected $localizations = NULL;

	/**
	 * @var array
	 */
	protected $mediaVersions = NULL;

	/**
	 * @return string|NULL
	 */
	public function GetModule () {
		return $this->module;
	}

	/**
	 * @param string|NULL $module 
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function & SetModule ($module) {
		$this->module = $module;
		return $this;
	}

	/**
	 * @return string|NULL
	 */
	public function GetNamespace () {
		return $this->namespace;
	}

	/**
	 * @param string|NULL $namespace 
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function & SetNamespace ($namespace) {
		$this->namespace = $namespace;
		return $this;
	}

	/**
	 * @return array
	 */
	public function GetAllowedLocalizations () {
		return array_keys($this->allowedLocalizations);
	}

	/**
	 * @var string $allowedLocalizations..., International lowercase language code(s) (+ optinally dash character + uppercase international locale code(s))
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function & SetAllowedLocalizations (/* ...$allowedLocalizations */) {
		$allowedLocalizations = func_get_args();
		if (count($allowedLocalizations) === 1 && is_array($allowedLocalizations[0])) 
			$allowedLocalizations = $allowedLocalizations[0];
		$this->allowedLocalizations = array_combine($allowedLocalizations, $allowedLocalizations);
		return $this;
	}

	/**
	 * @return array
	 */
	public function & GetAllowedMediaVersions () {
		return $this->allowedMediaVersions;
	}

	/**
	 * @param array $allowedMediaVersionsAndUrlValues
	 * @return \MvcCore\Ext\Routers\Modules\Route|\MvcCore\Ext\Routers\Modules\IRoute
	 */
	public function & SetAllowedMediaVersions ($allowedMediaVersionsAndUrlValues = []) {
		$this->allowedMediaVersions = $allowedMediaVersionsAndUrlValues;
		return $this;
	}


	public function GetName () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetName ($name) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetController () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetController ($controller) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetAction () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetAction ($action) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetControllerAction () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetControllerAction ($controllerAction) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetMethod () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetMethod ($method = NULL) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetRedirect () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetRedirect ($redirectRouteName = NULL) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetAbsolute () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return TRUE;
	}

	public function & SetAbsolute ($absolute = TRUE) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	public function GetGroupName () {
		$this->trriggerUnusedMethodError(__METHOD__);
		return NULL;
	}

	public function & SetGroupName ($groupName) {
		$this->trriggerUnusedMethodError(__METHOD__);
		return $this;
	}

	protected function trriggerUnusedMethodError ($method) {
		$cls = __CLASS__;
		trigger_error("[$cls] The method `$method` is not used in the extended `$cls` class.", E_USER_WARNING);
	}
}
