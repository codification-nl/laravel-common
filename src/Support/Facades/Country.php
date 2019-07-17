<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static bool isValid(string $country)
	 * @method static void ensureValid(string $country)
	 */
	class Country extends Facade
	{
		/**
		 * @return string
		 */
		protected static function getFacadeAccessor()
		{
			return 'country';
		}
	}
}