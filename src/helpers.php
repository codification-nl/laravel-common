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
	 * @param mixed                  $value
	 * @param string|\Money\Currency $currency
	 * @param string|null            $locale = null
	 *
	 * @return \Codification\Common\Money\Money|null
	 */
	function money($value, $currency, string $locale = null) : ?Codification\Common\Money\Money
	{
		return Codification\Common\Money\Money::make($value, $currency, $locale);
	}
}

if (!function_exists('phone'))
{
	/**
	 * @param string $number
	 * @param string $country
	 *
	 * @return \Codification\Common\Phone\Phone|null
	 */
	function phone(?string $number, ?string $country) : ?Codification\Common\Phone\Phone
	{
		return Codification\Common\Phone\Phone::make($number, $country);
	}
}

if (!function_exists('array_value'))
{
	/**
	 * @param array      $array
	 * @param int|string $key
	 * @param mixed      $default = null
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

if (!function_exists('array_unassoc'))
{
	/**
	 * @param string     $key
	 * @param string     $value
	 * @param array      $array
	 * @param array|null $keys = null
	 *
	 * @return mixed
	 */
	function array_unassoc(string $key, string $value, array $array, array $keys = null)
	{
		return array_map(function ($k, $v) use ($key, $value) : array
			{
				return [
					$key   => $k,
					$value => $v,
				];
			}, $keys ?: array_keys($array), $array);
	}
}

if (!function_exists('array_map_keys'))
{
	/**
	 * @param \Closure $callback
	 * @param array    $array
	 *
	 * @return array
	 */
	function array_map_keys(\Closure $callback, array $array) : array
	{
		$result = [];

		foreach ($array as $key => $value)
		{
			$mapped = $callback($value, $key);

			foreach ($mapped as $map_key => $map_value)
			{
				$result[$map_key] = $map_value;
			}
		}

		return $result;
	}
}

if (!function_exists('urlsafe_base64'))
{
	/**
	 * @param int  $length  = 16
	 * @param bool $padding = false
	 *
	 * @return string
	 */
	function urlsafe_base64(int $length = 16, bool $padding = false) : string
	{
		return \Codification\Common\Support\SecureRandom::urlsafe_base64($length, $padding);
	}
}