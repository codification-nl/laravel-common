<?php

namespace Codification\Common\Support
{
	use League\ISO3166\ISO3166;

	final class Country
	{
		public static function isValid(?string $country)
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
	}
}