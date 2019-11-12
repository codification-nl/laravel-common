<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static bool isValid(string|null $country_code)
	 * @method static void ensureValid(string|null $country_code)
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