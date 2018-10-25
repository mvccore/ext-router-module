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

trait Instancing
{
	public function __construct (
		$patternOrConfig = NULL,
		$module = NULL,
		$namespace = NULL,
		$defaults = [],
		$constraints = [],
		$advancedConfiguration = []
	) {
		if (count(func_get_args()) === 0) return;
		// init pattern, match, reverse, module, namespace, defaults, constraints and filters
		if (is_array($patternOrConfig)) {
			$data = (object) $patternOrConfig;
			$this->constructDataPatternsDefaultsConstraintsFilters($data);
			$this->constructDataModuleNamespace($data);
			$this->config = & $patternOrConfig;
		} else {
			$this->constructVarsPatternDefaultsConstraintsFilters(
				$patternOrConfig, $defaults, $constraints, $advancedConfiguration
			);
			$this->constructVarsModuleNamespace($module, $namespace);
			$this->config = & $advancedConfiguration;
		}
	}

	protected function constructDataModuleNamespace (& $data) {
		if (isset($data->module)) 
			$this->SetModule($data->module);
		if (isset($data->namespace)) 
			$this->SetNamespace($data->namespace);
	}

	protected function constructVarsModuleNamespace (& $module, & $namespace) {
		if ($module !== NULL) 
			$this->module = $module;
		if ($namespace !== NULL) 
			$this->namespace = $namespace;
	}
}
