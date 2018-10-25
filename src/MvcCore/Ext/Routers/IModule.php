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

namespace MvcCore\Ext\Routers;

interface IModule
{
    const URL_PARAM_MODULE = 'module';

	public function & SetDomainRoutes ($routes = [], $autoInitialize = TRUE);

	public function & AddDomainRoutes ($routes, $prepend = FALSE, $throwExceptionForDuplication = TRUE);

	public function & AddDomainRoute ($routeCfgOrRoute, $prepend = FALSE, $throwExceptionForDuplication = TRUE);
}
