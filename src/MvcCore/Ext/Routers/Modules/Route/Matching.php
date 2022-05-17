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

namespace MvcCore\Ext\Routers\Modules\Route;

/**
 * @mixin \MvcCore\Ext\Routers\Modules\Route
 */
trait Matching {

	/**
	 * Return subject value used for `preg_match_all()` route match processing.
	 * Complete subject by route flags. Route `pattern` (or `reverse`) must 
	 * contain domain part or/and base path. Prepare those values from request 
	 * object.
	 * @param \MvcCore\Request $request 
	 * @throws \InvalidArgumentException Domain route pattern or reverse  
	 *									 must be defined as absolute.
	 * @return string
	 */
	protected function matchesGetSubject (\MvcCore\IRequest $request) {
		$subject = $this->matchesGetSubjectHostAndBase($request) ;
		if (($this->flags & static::FLAG_SCHEME_NO) != 0) 
			throw new \InvalidArgumentException(
				"[".get_class()."] Domain route pattern or reverse must be defined as "
				."absolute with `//`, `http://` or `https://` at the beginning (`//www.domain.com`)."	
			);
		return $subject;
	}

	/**
	 * Parse rewrite params from `preg_match_all()` `$matches` result array into 
	 * array, keyed by param name with parsed value.
	 * @param array $matchedValues 
	 * @param array $defaults 
	 * @return array
	 */
	protected function matchesParseRewriteParams (& $matchedValues, & $defaults) {
		$matchedParams = [];
		array_shift($matchedValues); // first item is always matched whole `$request->GetPath()` string.
		foreach ($matchedValues as $key => $matchedValueArr) {
			if (is_numeric($key)) 
				continue;
			$matchedValue = (string) current($matchedValueArr);
			if (!isset($defaults[$key])) 
				$defaults[$key] = NULL;
			$matchedEmptyString = mb_strlen($matchedValue) === 0;
			if ($matchedEmptyString)
				$matchedValue = $defaults[$key];
			// continue if there is already valid ctrl and action from route ctrl or action configuration
			if (isset($matchedParams[$key]) && $matchedEmptyString) 
				continue;
			$matchedParams[$key] = $matchedValue;
		}
		return $matchedParams;
	}
}
