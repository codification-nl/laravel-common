<?php

namespace Codification\Common\Support\Facades
{
	use Illuminate\Support\Facades\Facade;

	/**
	 * @method static \Codification\Common\Money\Money|null make(string|float|int $value, string|\Money\Currency $currency, string|null $locale = null)
	 * @method static \Codification\Common\Money\Money|null zero(string|\Money\Currency $currency, string|null $locale = null)
	 */
	class Money extends Facade
	{
		/**
		 * @return string
		 */
		protected static function getFacadeAccessor()
		{
			return 'money';
		}
	}
}