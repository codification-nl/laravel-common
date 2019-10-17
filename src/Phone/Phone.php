<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Support\ContainerUtils;
	use libphonenumber\NumberParseException;
	use libphonenumber\PhoneNumberFormat;
	use libphonenumber\PhoneNumberType;
	use libphonenumber\PhoneNumberUtil;

	final class Phone implements \JsonSerializable
	{
		/** @var \libphonenumber\PhoneNumber */
		private $instance;

		/**
		 * @param string|null $region_code = null
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public function format(string $region_code = null) : string
		{
			$util        = PhoneNumberUtil::getInstance();
			$region_code = ContainerUtils::resolveLocale($region_code, CASE_UPPER);

			/** @var string $result */
			$result = $util->formatOutOfCountryCallingNumber($this->instance, $region_code);
			$result = str_replace(' ', '', $result);

			return $result;
		}

		/**
		 * @return string
		 */
		public function humanize() : string
		{
			return PhoneNumberUtil::getInstance()->format($this->instance, PhoneNumberFormat::NATIONAL);
		}

		/**
		 * @param string|null                               $region_code = null
		 * @param \Codification\Common\Phone\PhoneType|null $type        = null
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public function isValid(string $region_code = null, PhoneType $type = null) : bool
		{
			$region_code = ContainerUtils::resolveLocale($region_code, CASE_UPPER);

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

			return PhoneNumberUtil::getInstance()->isValidNumberForRegion($this->instance, $region_code);
		}

		/**
		 * @param string|null                                     $number
		 * @param string|null                                     $region_code
		 * @param \Codification\Common\Phone\PhoneType|null       $type        = null
		 * @param \Codification\Common\Phone\ParseErrorType|null &$parse_error = null
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 */
		public static function validate(?string $number, ?string $region_code, PhoneType $type = null, ParseErrorType &$parse_error = null) : bool
		{
			$phone = static::make($number, $region_code, $parse_error);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($region_code, $type);
		}

		/**
		 * @param string|null                                     $number
		 * @param string|null                                     $region_code
		 * @param \Codification\Common\Phone\ParseErrorType|null &$parse_error = null
		 *
		 * @return \Codification\Common\Phone\Phone|null
		 */
		public static function make(?string $number, ?string $region_code, ParseErrorType &$parse_error = null) : ?Phone
		{
			$number      = sanitize($number);
			$region_code = sanitize(strtoupper($region_code));

			try
			{
				$phone = new static();

				$phone->instance = PhoneNumberUtil::getInstance()->parse($number, $region_code);

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
		 * @param null|string $number
		 * @param string|null $region_code
		 *
		 * @return null|string
		 */
		public static function getRegionCode(?string $number, string $region_code = null) : ?string
		{
			$util        = PhoneNumberUtil::getInstance();
			$number      = sanitize($number);
			$region_code = ContainerUtils::resolveLocale($region_code, CASE_UPPER);

			try
			{
				return $util->getRegionCodeForNumber($util->parse($number, $region_code));
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