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
		$value = trim(strval($value));

		if (strlen($value) === 0)
		{
			return null;
		}

		return $value;
	}
}

if (!function_exists('url_parse'))
{
	/**
	 * @param string                                     $url
	 * @param \Codification\Common\Url\UrlParseFlags|int $flags
	 *
	 * @return \Codification\Common\Url\Url
	 * @throws \Codification\Common\Enum\Exceptions\EnumException
	 * @throws \Codification\Common\Enum\Exceptions\ValueException
	 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
	 * @throws \InvalidArgumentException
	 */
	function url_parse(string $url, $flags = \Codification\Common\Url\UrlParseFlags::ALL) : \Codification\Common\Url\Url
	{
		return Codification\Common\Url\Url::parse($url, $flags);
	}
}

if (!function_exists('money'))
{
	/**
	 * @param string|float|int|null  $value
	 * @psalm-param numeric|null     $value
	 * @param string|\Money\Currency $currency
	 * @param string|null            $locale = null
	 *
	 * @return \Codification\Common\Money\Money|null
	 * @throws \Codification\Common\Money\Exceptions\CurrencyCodeException
	 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
	 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
	 * @throws \Codification\Common\Money\Exceptions\ParseException
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
	 * @param string $region_code
	 *
	 * @return \Codification\Common\Phone\Phone|null
	 * @throws \Codification\Common\Enum\Exceptions\ValueException
	 * @throws \Codification\Common\Enum\Exceptions\EnumException
	 */
	function phone($number, $region_code) : ?Codification\Common\Phone\Phone
	{
		return Codification\Common\Phone\Phone::make($number, $region_code);
	}
}

if (!function_exists('array_value'))
{
	/**
	 * @template     T of array
	 * @template     TKey as key-of<T>
	 * @param array      $array
	 * @psalm-param  T $array
	 * @param int|string $key
	 * @psalm-param  TKey $key
	 * @param mixed      $default = null
	 *
	 * @return mixed
	 * @psalm-return T[TKey]|mixed
	 */
	function array_value(array $array, $key, $default = null)
	{
		return $array[$key] ?? $default;
	}
}

if (!function_exists('array_unassoc'))
{
	/**
	 * @param string     $key
	 * @param string     $value
	 * @param array      $array
	 * @psalm-param array<string, mixed> $array
	 * @param array|null $keys = null
	 * @psalm-param list<string>|null $keys = null
	 *
	 * @return mixed
	 */
	function array_unassoc(string $key, string $value, array $array, array $keys = null)
	{
		/** @psalm-suppress MissingClosureParamType */
		return array_map(function ($k, $v) use ($key, $value) : array
			{
				return [
					$key   => $k,
					$value => $v,
				];
			}, $keys ?? array_keys($array), $array);
	}
}

if (!function_exists('array_map_keys'))
{
	/**
	 * @template     TIn of array
	 * @template     TInValue as key-of<TIn>
	 * @template     TOut of array
	 * @template     TOutKey as key-of<TOut>
	 * @param \Closure $callback
	 * @psalm-param \Closure(TInValue, array-key):array<TOutKey, TOut[TOutKey]> $callback
	 * @param array    $array
	 * @psalm-param  TIn $array
	 *
	 * @return array
	 * @psalm-return TOut
	 */
	function array_map_keys(\Closure $callback, array $array) : array
	{
		$result = [];

		/** @psalm-var TInValue $value */
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

if (!function_exists('array_reduce_assoc'))
{
	/**
	 * @template     T
	 * @template     TIn of array
	 * @template     TInValue as value-of<TIn>
	 * @param array    $array
	 * @psalm-param  TIn $input
	 * @param \Closure $callback
	 * @psalm-param \Closure(T, TInValue, array-key):T $callback
	 * @param mixed    $initial
	 * @psalm-param  T $initial
	 *
	 * @return mixed
	 * @psalm-return T
	 */
	function array_reduce_assoc(array $array, \Closure $callback, $initial)
	{
		$result = $initial;

		/** @psalm-var TInValue $value */
		foreach ($array as $key => $value)
		{
			$result = $callback($result, $value, $key);
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
	 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
	 */
	function urlsafe_base64(int $length = 16, bool $padding = false) : string
	{
		return \Codification\Common\Support\SecureRandom::urlsafe_base64($length, $padding);
	}
}