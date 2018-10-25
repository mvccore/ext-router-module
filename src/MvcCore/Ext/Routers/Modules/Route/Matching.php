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

trait Matching
{
	protected function matchesGetSubject (\MvcCore\IRequest & $request) {
		$subject = $this->matchesGetSubjectHostAndBase($request) ;
		if (!$this->flags[0]) throw new \InvalidArgumentException(
			"[".__CLASS__."] Domain route pattern or reverse must be defined as "
			."absolute with `//`, `http://` or `https://` at the beginning (`//www.domain.com`)."	
		);
		return $subject;
	}

	protected function & matchesParseRewriteParams (& $matchedValues, & $defaults) {
		$matchedParams = [];
		array_shift($matchedValues); // first item is always matched whole `$request->GetPath()` string.
		foreach ($matchedValues as $key => $matchedValueArr) {
			if (is_numeric($key)) continue;
			$matchedValue = (string) current($matchedValueArr);
			if (!isset($defaults[$key])) 
				$defaults[$key] = NULL;
			$matchedEmptyString = mb_strlen($matchedValue) === 0;
			if ($matchedEmptyString)
				$matchedValue = $defaults[$key];
			// continue if there is already valid ctrl and action from route ctrl or action configuration
			if (isset($matchedParams[$key]) && $matchedEmptyString) continue;
			$matchedParams[$key] = $matchedValue;
		}
		return $matchedParams;
	}
}
