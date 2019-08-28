<?php

namespace Codification\Common\Country
{
	use League\ISO3166\ISO3166;

	final class Country
	{
		/**
		 * @param null|string $country_code
		 *
		 * @return bool
		 */
		public static function isValid(?string $country_code) : bool
		{
			try
			{
				(new ISO3166())->alpha2($country_code);
			}
			catch (\Exception $e)
			{
				return false;
			}

			return true;
		}

		/**
		 * @param string|null $country_code
		 *
		 * @return void
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public static function ensureValid(?string $country_code) : void
		{
			if (static::isValid($country_code))
			{
				return;
			}

			throw new Exceptions\CountryCodeException($country_code);
		}
	}
}