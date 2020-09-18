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
	/**
	 * Create new module domain route instance. First argument could be 
	 * configuration array with all necessary constructor values or all 
	 * separated arguments - first  is route pattern value to parse into match 
	 * and reverse values, then module name, optional target controllers 
	 * namespace, params default values and constraints.
	 * Example:
	 * `new \MvcCore\Ext\Routers\Modules\Route([
	 *		"pattern"				=> "//blog.%sld%.%tld%",
	 *		"module"				=> "blog",
	 *		"namespace"				=> "Blog",
	 *		"defaults"				=> ["page" => 1],
	 *		"constraints"			=> ["page" => "\d+"],
	 *		"allowedLocalizations"	=> ["en-US"],
	 *		"allowedMediaVersions"	=> ["full" => ""]
	 * ]);`
	 * or:
	 * `new \MvcCore\Ext\Routers\Modules\Route(
	 *		"//blog.%sld%.%tld%",
	 *		"blog",			"Blog",
	 *		["page" => 1],	["page" => "\d+"],
	 *		[
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		]
	 * );`
	 * or:
	 * `new \MvcCore\Ext\Routers\Modules\Route([
	 *		"match"					=> "#^//blog\.%sld%\.%tld%$#",
	 *		"reverse"				=> "//blog.%sld%.%tld%",
	 *		"module"				=> "blog",
	 *		"namespace"				=> "Blog",
	 *		"defaults"				=> ["page" => 1],
	 *		"constraints"			=> ["page" => "\d+"],
	 *		"allowedLocalizations"	=> ["en-US"],
	 *		"allowedMediaVersions"	=> ["full" => ""]
	 * ]);`
	 * @param string|array	$patternOrConfig
	 *						Required, configuration array or route pattern value 
	 *						to parse into match and reverse patterns.
	 * @param string		$module 
	 *						Required, application module name. Equivalent for 
	 *						classic route name.
	 * @param string		$namespace 
	 *						Optional, target controllers namespace, applied to 
	 *						routed controller by classic route if target 
	 *						controller is not defined absolutely.
	 * @param array			$defaults
	 *						Optional, default param values like: 
	 *						`["name" => "default-name", "page" => 1]`.
	 * @param array			$constraints
	 *						Optional, params regular expression constraints for
	 *						regular expression match function if no `"match"` 
	 *						property in config array as first argument defined.
	 * @param array			$advancedConfiguration
	 *						Optional, http method to only match requests by this 
	 *						method. If `NULL` (by default), request with any http 
	 *						method could be matched by this route. Given value is 
	 *						automatically converted to upper case.
	 * @return void
	 */
	public function __construct (
		$patternOrConfig = NULL,
		$module = NULL,
		$namespace = NULL,
		$defaults = [],
		$constraints = [],
		$advancedConfiguration = []
	) {
		/** @var $this \MvcCore\Ext\Routers\Modules\Route */
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

	/**
	 * If route is initialized by single array argument with all data, 
	 * initialize route application module name and optional target controllers 
	 * namespace - used only if routed controller is not defined absolutely. 
	 * Initialize both properties by setter methods.
	 * @param \stdClass $data	Object containing properties `module` and `namespace`.
	 * @return void
	 */
	protected function constructDataModuleNamespace (& $data) {
		/** @var $this \MvcCore\Ext\Routers\Modules\Route */
		if (isset($data->module)) 
			$this->SetModule($data->module);
		if (isset($data->namespace)) 
			$this->SetNamespace($data->namespace);
	}

	/**
	 * If route is initialized by each constructor function arguments, 
	 * initialize route application module name and optional target controllers 
	 * namespace - used only if routed controller is not defined absolutely. 
	 * Initialize both properties directly if values are not `NULL`.
	 * @param string|NULL $module 
	 * @param string|NULL $namespace 
	 * @return void
	 */
	protected function constructVarsModuleNamespace (& $module, & $namespace) {
		/** @var $this \MvcCore\Ext\Routers\Modules\Route */
		if ($module !== NULL) 
			$this->module = $module;
		if ($namespace !== NULL) 
			$this->namespace = $namespace;
	}
}
