<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Country\Country;
	use libphonenumber\NumberParseException;
	use libphonenumber\PhoneNumberType;
	use libphonenumber\PhoneNumberUtil;

	final class Phone implements \JsonSerializable
	{
		/** @var \libphonenumber\PhoneNumber */
		private $instance;

		/**
		 * @param string|null $number
		 * @param string      $country
		 *
		 * @throws \libphonenumber\NumberParseException
		 */
		private function __construct(?string $number, string $country)
		{
			$number  = sanitize($number);
			$country = $this->getCountry($country);

			$this->instance = PhoneNumberUtil::getInstance()->parse($number, $country);
		}

		/**
		 * @param string|null $country
		 *
		 * @return string
		 */
		private function getCountry(?string $country) : string
		{
			return strtoupper(Country::get($country));
		}

		/**
		 * @param string|null $locale
		 *
		 * @return string
		 */
		public function format(string $locale = null) : string
		{
			$util   = PhoneNumberUtil::getInstance();
			$locale = $this->getCountry($locale);

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
		 */
		public function isValid(string $country = null, PhoneType $type = null) : bool
		{
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

			return PhoneNumberUtil::getInstance()->isValidNumberForRegion($this->instance, $this->getCountry($country));
		}

		/**
		 * @param null|string                               $number
		 * @param string                                    $country
		 * @param \Codification\Common\Phone\PhoneType|null $type = null
		 *
		 * @return bool
		 */
		public static function validate(?string $number, string $country, PhoneType $type = null) : bool
		{
			$phone = static::make($number, $country);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($country, $type);
		}

		/**
		 * @param string|null                                     $number
		 * @param string                                          $country
		 * @param \Codification\Common\Phone\ParseErrorType|null &$parse_error_type = null
		 *
		 * @return \Codification\Common\Phone\Phone|null
		 */
		public static function make(?string $number, string $country, ParseErrorType &$parse_error_type = null) : ?Phone
		{
			try
			{
				return new static($number, $country);
			}
			catch (NumberParseException $e)
			{
				if ($parse_error_type !== null)
				{
					$parse_error_type = ParseErrorType::make($e->getErrorType());
				}
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