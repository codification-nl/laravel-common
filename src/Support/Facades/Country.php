<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static string get(string|null $country)
	 * @method static bool isValid(string|null $country)
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