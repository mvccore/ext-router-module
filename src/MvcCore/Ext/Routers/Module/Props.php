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

trait Props {

	/**
	 * Module domain routes store. Keys are string with every route `module`
	 * property value (opposite value for classic route `name` property) and
	 * values are module domain route instances.
	 * @var \MvcCore\Ext\Routers\Modules\Route[]|array
	 */
	protected $domainRoutes = [];

	/**
	 * Currently matched module domain route instance 
	 * or `NULL` if no module route is matched. 
	 * @var \MvcCore\Ext\Routers\Modules\Route|NULL
	 */
	protected $currentDomainRoute = NULL;

	/**
	 * Currently matched module name by currently matched module domain route.
	 * If no module domain route is matched, this value is `NULL`.
	 * @var string|NULL
	 */
	protected $currentModule = NULL;

	/**
	 * Reference to `\MvcCore\Application::GetInstance()->GetRouteClass();`.
	 * @var string|NULL
	 */
	protected static $routeDomainClass = '\MvcCore\Ext\Routers\Modules\Route';

}
