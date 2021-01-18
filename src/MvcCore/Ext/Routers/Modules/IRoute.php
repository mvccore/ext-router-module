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

namespace MvcCore\Ext\Routers\Modules;

/**
 * Responsibility - describing request(s) to match and reversely build URL addresses.
 * - Describing request scheme, domain and base path part and target application 
 *   module, optionally to define allowed localizations or allowed media versions.
 * - Matching request by given request object, see `\MvcCore\Route::Matches()`.
 * - Completing URL address scheme, domain and base path part by given params 
 *   array, see `\MvcCore\Route::Url()`.
 */
interface IRoute {

	/**
	 * Route advanced configuration key to define allowed module names, where standard route could be used.
	 */
	const CONFIG_ALLOWED_MODULES		= 'allowedModules';
	
	/**
	 * Route advanced configuration key to define (only) allowed localizations for target application module.
	 */
	const CONFIG_ALLOWED_LOCALIZATIONS	= 'allowedLocalizations';
	
	/**
	 * Route advanced configuration key to define (only) allowed media (device) versions for target application module.
	 */
	const CONFIG_ALLOWED_MEDIA_VERSIONS	= 'allowedMediaVersions';
}
