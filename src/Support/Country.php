<?php

namespace Codification\Common\Support
{
	use Codification\Common\Exceptions\LocaleException;
	use Illuminate\Support\Facades\App;
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
		 * @return string
		 * @throws \Codification\Common\Exceptions\LocaleException
		 */
		public static function get(string $country = null) : string
		{
			$country = sanitize($country);

			if ($country === null)
			{
				$country = App::getLocale();
			}

			if ($country === null || !static::isValid($country))
			{
				throw new LocaleException();
			}

			return $country;
		}
	}
}