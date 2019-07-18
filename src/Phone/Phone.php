<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Country\Country;
	use Codification\Common\Support\ContainerUtils;
	use libphonenumber\NumberParseException;
	use libphonenumber\PhoneNumberType;
	use libphonenumber\PhoneNumberUtil;

	final class Phone implements \JsonSerializable
	{
		/** @var \libphonenumber\PhoneNumber */
		private $instance;

		/**
		 * @param string|null $locale = null
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\InvalidCountryCodeException
		 */
		public function format(string $locale = null) : string
		{
			$util   = PhoneNumberUtil::getInstance();
			$locale = static::resolveCountry($locale);

			/** @var string $result */
			$result = $util->formatOutOfCountryCallingNumber($this->instance, $locale);
			$result = str_replace(' ', '', $result);

			return $result;
		}

		/**
		 * @param string|null                               $country = null
		 * @param \Codification\Common\Phone\PhoneType|null $type    = null
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\InvalidCountryCodeException
		 */
		public function isValid(string $country = null, PhoneType $type = null) : bool
		{
			$country = static::resolveCountry($country);

			if ($type === null)
			{
				$type = PhoneType::BOTH();
			}

			switch (PhoneNumberUtil::getInstance()->getNumberType($this->instance))
			{
				case PhoneNumberType::FIXED_LINE_OR_MOBILE:
					break;

				case PhoneNumberType::MOBILE:
					{
						if ($type->has(PhoneType::MOBILE()))
						{
							break;
						}

						return false;
					}

				case PhoneNumberType::FIXED_LINE:
					{
						if ($type->has(PhoneType::FIXED()))
						{
							break;
						}

						return false;
					}

				default:
					return false;
			}

			return PhoneNumberUtil::getInstance()->isValidNumberForRegion($this->instance, $country);
		}

		/**
		 * @param null|string                                     $number
		 * @param string                                          $country
		 * @param \Codification\Common\Phone\PhoneType|null       $type        = null
		 * @param \Codification\Common\Phone\ParseErrorType|null &$parse_error = null
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\InvalidCountryCodeException
		 */
		public static function validate(?string $number, string $country, PhoneType $type = null, ParseErrorType &$parse_error = null) : bool
		{
			$phone = static::make($number, $country, $parse_error);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($country, $type);
		}

		/**
		 * @param string|null                                     $number
		 * @param string                                          $country
		 * @param \Codification\Common\Phone\ParseErrorType|null &$parse_error = null
		 *
		 * @return \Codification\Common\Phone\Phone|null
		 */
		public static function make(?string $number, string $country, ParseErrorType &$parse_error = null) : ?Phone
		{
			$number  = sanitize($number);
			$country = strtoupper(sanitize($country));

			try
			{
				$phone = new static();

				$phone->instance = PhoneNumberUtil::getInstance()->parse($number, $country);

				return $phone;
			}
			catch (NumberParseException $e)
			{
				if ($parse_error !== null)
				{
					$parse_error = ParseErrorType::make($e->getErrorType());
				}
			}

			return null;
		}

		/**
		 * @param null|string $country
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\InvalidCountryCodeException
		 */
		private static function resolveCountry(?string $country) : string
		{
			$country = sanitize($country);

			if ($country === null)
			{
				/** @var \Illuminate\Foundation\Application $app */
				$app     = ContainerUtils::resolve('app');
				$country = $app->getLocale();
			}

			Country::ensureValid($country);

			return strtoupper($country);
		}

		/**
		 * @param null|string $number
		 * @param string|null $locale
		 *
		 * @return null|string
		 */
		public static function getCountry(?string $number, string $locale = null) : ?string
		{
			$util   = PhoneNumberUtil::getInstance();
			$number = sanitize($number);
			$locale = static::resolveCountry($locale);

			try
			{
				return $util->getRegionCodeForNumber($util->parse($number, $locale));
			}
			catch (NumberParseException $e)
			{
			}

			return null;
		}

		/**
		 * @return string
		 */
		public function __toString() : string
		{
			return $this->format();
		}

		/**
		 * @return string
		 */
		public function jsonSerialize() : string
		{
			return $this->format();
		}
	}
}