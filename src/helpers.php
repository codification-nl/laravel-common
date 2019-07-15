<?php

if (!function_exists('sanitize'))
{
	/**
	 * @param mixed $value
	 *
	 * @return string|null
	 */
	function sanitize($value) : ?string
	{
		$value = trim($value);

		if (strlen($value) === 0)
		{
			return null;
		}

		return $value;
	}
}

if (!function_exists('money'))
{
	/**
	 * @param string|float|int|null  $value
	 * @param string|\Money\Currency $currency
	 * @param string|null            $locale
	 *
	 * @return \Codification\Common\Support\Money|null
	 */
	function money($value, $currency, string $locale = null) : ?Codification\Common\Support\Money
	{
		return Codification\Common\Support\Money::make($value, $currency, $locale);
	}
}

if (!function_exists('phone'))
{
	/**
	 * @param string|null $number
	 * @param string      $country
	 *
	 * @return \Codification\Common\Support\Phone|null
	 */
	function phone(?string $number, string $country) : ?Codification\Common\Support\Phone
	{
		return Codification\Common\Support\Phone::make($number, $country);
	}
}

if (!function_exists('array_value'))
{
	/**
	 * @param array      $array
	 * @param int|string $key
	 * @param mixed      $default
	 *
	 * @return mixed
	 */
	function array_value(array $array, $key, $default = null)
	{
		if (!array_key_exists($key, $array))
		{
			return $default;
		}

		return $array[$key];
	}
}