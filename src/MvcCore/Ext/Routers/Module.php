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

/**
 * Responsibilities - recognize media site version and localization from URL or user agent 
 *					or session and set up request object, complete automatically rewritten 
 *					URL with remembered media site version and localization. Redirect 
 *					to proper media site version or localization by configuration.
 *					Than route request like parent class does.
 */
class		Module
extends		\MvcCore\Router
implements	\MvcCore\Ext\Routers\IModule,
			\MvcCore\Ext\Routers\IExtended {

	use \MvcCore\Ext\Routers\Extended\PropsGettersSetters;
	use \MvcCore\Ext\Routers\Extended\Preparing;
	use \MvcCore\Ext\Routers\Extended\RedirectHelpers;
	use \MvcCore\Ext\Routers\Extended\Url;
	
	use \MvcCore\Ext\Routers\Module\Canonical;
	use \MvcCore\Ext\Routers\Module\DomainRouteSetUp;
	use \MvcCore\Ext\Routers\Module\DomainRouting;
	use \MvcCore\Ext\Routers\Module\Props;
	use \MvcCore\Ext\Routers\Module\Redirect;
	use \MvcCore\Ext\Routers\Module\RewriteRoutingChecks;
	use \MvcCore\Ext\Routers\Module\RouteMethods;
	use \MvcCore\Ext\Routers\Module\PreAndPostRouting;
	use \MvcCore\Ext\Routers\Module\UrlByRoute;
	use \MvcCore\Ext\Routers\Module\UrlByQuery;
	use \MvcCore\Ext\Routers\Module\UrlByRouteSections;
	use \MvcCore\Ext\Routers\Module\UrlDomain;
	
	/**
	 * MvcCore Extension - Router - Module - version:
	 * Comparison by PHP function version_compare();
	 * @see http://php.net/manual/en/function.version-compare.php
	 */
	const VERSION = '5.3.0';

}

