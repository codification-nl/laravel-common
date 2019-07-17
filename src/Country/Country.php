<?php

namespace Codification\Common\Country
{
	use League\ISO3166\ISO3166;

	final class Country
	{
		/**
		 * @param null|string $country
		 *
		 * @return bool
		 */
		public static function isValid(?string $country) : bool
		{
			try
			{
				(new ISO3166())->alpha2($country);
			}
			catch (\Exception $e)
			{
				return false;
			}

			return true;
		}

		/**
		 * @param string|null $country
		 *
		 * @return void
		 * @throws \Codification\Common\Country\Exceptions\InvalidCountryCodeException
		 */
		public static function ensureValid(?string $country) : void
		{
			if (static::isValid($country))
			{
				return;
			}

			throw new Exceptions\InvalidCountryCodeException();
		}
	}
}