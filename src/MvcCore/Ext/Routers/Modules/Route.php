<?php

/**
 * MvcCore
 *
 * This source file is subject to the BSD 3 License
 * For the full copyright and license information, please view
 * the LICENSE.md file that are distributed with this source code.
 *
 * @copyright	Copyright (c) 2016 Tom Flidr (https://github.com/mvccore)
 * @license		https://mvccore.github.io/docs/mvccore/5.0.0/LICENCE.md
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
class		Route
extends		\MvcCore\Route
implements	\MvcCore\Ext\Routers\Modules\IRoute {

	use \MvcCore\Ext\Routers\Modules\Route\PropsGettersSetters;
	//use \MvcCore\Ext\Routers\Modules\Route\Instancing;
	use \MvcCore\Ext\Routers\Modules\Route\Matching;
	use \MvcCore\Ext\Routers\Modules\Route\UrlBuilding;

	// PHP 5.4 workaround:
	use \MvcCore\Ext\Routers\Modules\Route\Instancing {
		\MvcCore\Ext\Routers\Modules\Route\Instancing::__construct as protected __constructModules;
	}
	public function __construct() {
		call_user_func_array(
			[$this, '__constructModules'],
			func_get_args()
		);
	}
}
