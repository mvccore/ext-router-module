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

namespace MvcCore\Ext\Routers;

interface IModule {

	/**
	 * URL param name to build absolute URL by desired module domain route .
	 */
	const URL_PARAM_MODULE = 'module';

	/**
	 * Clear all possible previously configured module domain routes and set new 
	 * given routes again collection again. If there is no module property 
	 * configured in given route item in array configuration, route module is set
	 * by given `$routes` array key, if key is not numeric.
	 *
	 * Routes could be defined in various forms:
	 * Example:
	 * `\MvcCore\Router::GetInstance()->SetDomainRoutes([
	 *		"blog"	=> [
	 *			"pattern"				=> "//blog.%sld%.%tld%",
	 *			"namespace"				=> "Blog",
	 *			"defaults"				=> ["page" => 1],
	 *			"constraints"			=> ["page" => "\d+"],
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		],
	 *		"main"	=> [
	 *			"pattern"				=> "//%domain%",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		]
	 * ]);`
	 * or:
	 * `\MvcCore\Router::GetInstance()->SetDomainRoutes([
	 *		new \MvcCore\Ext\Routers\Modules\Route(
	 *			"//blog.%sld%.%tld%",	// pattern
	 *			"blog",					// module
	 *			"Blog",					// namespace
	 *			["page" => 1],			// defaults
	 *			["page" => "\d+"],		// constraints
	 *			[						// advanced configuration
	 *				"allowedLocalizations"	=> ["en-US"],
	 *				"allowedMediaVersions"	=> ["full" => ""]
	 *			]
	 *		),
	 *		new \MvcCore\Ext\Routers\Modules\Route([
	 *			"pattern"				=> "//%domain%",
	 *			"module"				=> "main",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		])
	 * ]);`
	 * @param \MvcCore\Ext\Routers\Modules\Route[]|array|array[] $routes 
	 * @param bool $autoInitialize 
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Routers\Module
	 */
	public function SetDomainRoutes ($routes = [], $autoInitialize = TRUE);

	/**
	 * Append or prepend new module domain routes. If there is no module property 
	 * configured in given route item in array configuration, route module is set
	 * by given `$routes` array key, if key is not numeric.
	 *
	 * Routes could be defined in various forms:
	 * Example:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoutes([
	 *		"blog"	=> [
	 *			"pattern"				=> "//blog.%sld%.%tld%",
	 *			"namespace"				=> "Blog",
	 *			"defaults"				=> ["page" => 1],
	 *			"constraints"			=> ["page" => "\d+"],
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		],
	 *		"main"	=> [
	 *			"pattern"				=> "//%domain%",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		]
	 * ]);`
	 * or:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoutes([
	 *		new \MvcCore\Ext\Routers\Modules\Route(
	 *			"//blog.%sld%.%tld%",	// pattern
	 *			"blog",					// module
	 *			"Blog",					// namespace
	 *			["page" => 1],			// defaults
	 *			["page" => "\d+"],		// constraints
	 *			[						// advanced configuration
	 *				"allowedLocalizations"	=> ["en-US"],
	 *				"allowedMediaVersions"	=> ["full" => ""]
	 *			]
	 *		),
	 *		new \MvcCore\Ext\Routers\Modules\Route([
	 *			"pattern"				=> "//%domain%",
	 *			"module"				=> "main",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		])
	 * ]);`
	 * @param \MvcCore\Ext\Routers\Modules\Route[]|array|array[] $routes 
	 * @param bool $prepend 
	 * @param bool $throwExceptionForDuplication 
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Routers\Module
	 */
	public function AddDomainRoutes ($routes, $prepend = FALSE, $throwExceptionForDuplication = TRUE);

	/**
	 * Append or prepend new module domain route.
	 * Example:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoute([
	 *		"blog"	=> [
	 *			"pattern"				=> "//blog.%sld%.%tld%",
	 *			"namespace"				=> "Blog",
	 *			"defaults"				=> ["page" => 1],
	 *			"constraints"			=> ["page" => "\d+"],
	 *			"allowedLocalizations"	=> ["en-US"],
	 *			"allowedMediaVersions"	=> ["full" => ""]
	 *		],
	 *		"main"	=> [
	 *			"pattern"				=> "//%domain%",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		]
	 * ]);`
	 * or:
	 * `\MvcCore\Router::GetInstance()->AddDomainRoute([
	 *		new \MvcCore\Ext\Routers\Modules\Route(
	 *			"//blog.%sld%.%tld%",	// pattern
	 *			"blog",					// module
	 *			"Blog",					// namespace
	 *			["page" => 1],			// defaults
	 *			["page" => "\d+"],		// constraints
	 *			[						// advanced configuration
	 *				"allowedLocalizations"	=> ["en-US"],
	 *				"allowedMediaVersions"	=> ["full" => ""]
	 *			]
	 *		),
	 *		new \MvcCore\Ext\Routers\Modules\Route([
	 *			"pattern"				=> "//%domain%",
	 *			"module"				=> "main",
	 *			"allowedLocalizations"	=> ["en-US", "de-DE"],
	 *			"allowedMediaVersions"	=> ["mobile" => "m", "full" => ""]
	 *		])
	 * ]);`
	 * @param \MvcCore\Ext\Routers\Modules\Route|array $routeCfgOrRoute 
	 * @param bool $prepend 
	 * @param bool $throwExceptionForDuplication 
	 * @throws \InvalidArgumentException 
	 * @return \MvcCore\Ext\Routers\Module
	 */
	public function AddDomainRoute ($routeCfgOrRoute, $prepend = FALSE, $throwExceptionForDuplication = TRUE);
}
