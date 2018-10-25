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

trait Props
{
	/**
	 * @var \MvcCore\Ext\Routers\Modules\Route[]
	 */
	protected $domainRoutes = [];

	/**
	 * @var \MvcCore\Ext\Routers\Modules\Route|NULL
	 */
	protected $currentDomainRoute = NULL;

	/**
	 * @var string|NULL
	 */
	protected $currentModule = NULL;

	/**
	 * Reference to `\MvcCore\Application::GetInstance()->GetRouteClass();`.
	 * @var string|NULL
	 */
	protected static $routeDomainClass = '\MvcCore\Ext\Routers\Modules\Route';

}
