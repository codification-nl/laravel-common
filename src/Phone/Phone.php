<?php

namespace Codification\Common\Phone
{
	use Codification\Common\Contracts\Support\Stringable;
	use Codification\Common\Support\ContainerUtils;
	use Codification\Common\Support\Contracts;
	use Codification\Common\Support\Exceptions\ShouldNotHappenException;
	use libphonenumber;

	final class Phone implements Contracts\Bindable, Stringable
	{
		/** @var \libphonenumber\PhoneNumber|null */
		private $instance = null;

		/**
		 * @param string|null $region_code = null
		 *
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function format(string $region_code = null) : string
		{
			$instance = $this->ensureInstance();
			$util     = libphonenumber\PhoneNumberUtil::getInstance();

			if ($region_code === '*')
			{
				return $util->format($instance, libphonenumber\PhoneNumberFormat::E164);
			}

			$region_code = ContainerUtils::resolveLocale($region_code, CASE_UPPER);

			try
			{
				$result = $util->formatOutOfCountryCallingNumber($instance, $region_code);
			}
			catch (\InvalidArgumentException $e)
			{
				throw new ShouldNotHappenException('Failed to format', $e);
			}

			/** @var string $result */
			$result = str_replace(' ', '', $result);

			return $result;
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function humanize() : string
		{
			return libphonenumber\PhoneNumberUtil::getInstance()->format(
				$this->ensureInstance(),
				libphonenumber\PhoneNumberFormat::NATIONAL
			);
		}

		/**
		 * @param string|null                              $region_code = null
		 * @param \Codification\Common\Phone\PhoneType|int $type
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public function isValid(string $region_code = null, $type = PhoneType::BOTH) : bool
		{
			$instance    = $this->ensureInstance();
			$region_code = ContainerUtils::resolveLocale($region_code, CASE_UPPER);

			if (!($type instanceof PhoneType))
			{
				$type = PhoneType::make($type);
			}

			switch (libphonenumber\PhoneNumberUtil::getInstance()->getNumberType($instance))
			{
				case libphonenumber\PhoneNumberType::FIXED_LINE_OR_MOBILE:
					break;

				case libphonenumber\PhoneNumberType::MOBILE:
					{
						if ($type->has(PhoneType::MOBILE()))
						{
							break;
						}

						return false;
					}

				case libphonenumber\PhoneNumberType::FIXED_LINE:
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

			try
			{
				return libphonenumber\PhoneNumberUtil::getInstance()->isValidNumberForRegion($instance, $region_code);
			}
			catch (\InvalidArgumentException $e)
			{
				throw new ShouldNotHappenException('Failed to validate', $e);
			}
		}

		/**
		 * @param string|null                                    $number
		 * @param string|null                                    $region_code
		 * @param \Codification\Common\Phone\PhoneType|int       $type            = \Codification\Common\Phone\PhoneType::BOTH
		 * @param \Codification\Common\Phone\ParseErrorType|null $out_parse_error = null
		 * @param-out \Codification\Common\Phone\ParseErrorType|null $out_parse_error = null
		 *
		 * @return bool
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 */
		public static function validate(?string $number, ?string $region_code, $type = PhoneType::BOTH, ParseErrorType &$out_parse_error = null) : bool
		{
			$phone = static::make($number, $region_code, $out_parse_error);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($region_code, $type);
		}

		/**
		 * @param string|null                                    $number
		 * @param string|null                                    $region_code
		 * @param \Codification\Common\Phone\ParseErrorType|null $out_parse_error = null
		 * @param-out \Codification\Common\Phone\ParseErrorType|null $out_parse_error = null
		 *
		 * @return \Codification\Common\Phone\Phone|null
		 * @throws \Codification\Common\Enum\Exceptions\ValueException
		 * @throws \Codification\Common\Enum\Exceptions\EnumException
		 */
		public static function make(?string $number, ?string $region_code, ParseErrorType &$out_parse_error = null) : ?Phone
		{
			$number      = sanitize($number);
			$region_code = sanitize(strtoupper($region_code ?? ''));

			try
			{
				$phone = new static();

				/** @psalm-suppress PossiblyNullArgument */
				$phone->instance = libphonenumber\PhoneNumberUtil::getInstance()->parse($number, $region_code);

				return $phone;
			}
			catch (libphonenumber\NumberParseException $e)
			{
				if ($out_parse_error !== null)
				{
					/** @var int $value */
					$value = $e->getErrorType();

					$out_parse_error = ParseErrorType::make($value);
				}
			}

			return null;
		}

		/**
		 * @param string|null $number
		 * @param string|null $region_code
		 *
		 * @return string|null
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public static function getRegionCode(?string $number, string $region_code = null) : ?string
		{
			$util        = libphonenumber\PhoneNumberUtil::getInstance();
			$number      = sanitize($number);
			$region_code = ContainerUtils::resolveLocale($region_code, CASE_UPPER);

			try
			{
				/** @psalm-suppress PossiblyNullArgument */
				$phone = $util->parse($number, $region_code);

				return $util->getRegionCodeForNumber($phone);
			}
			catch (libphonenumber\NumberParseException $e)
			{
			}

			return null;
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function __toString() : string
		{
			return $this->toString();
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function jsonSerialize() : string
		{
			return $this->format();
		}

		/**
		 * @return string
		 * @throws \Codification\Common\Country\Exceptions\CountryCodeException
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		public function toString() : string
		{
			return $this->format();
		}

		/**
		 * @return \libphonenumber\PhoneNumber
		 * @throws \Codification\Common\Support\Exceptions\ShouldNotHappenException
		 */
		private function ensureInstance() : \libphonenumber\PhoneNumber
		{
			if ($this->instance === null)
			{
				throw new ShouldNotHappenException('$this->instance === null');
			}

			return $this->instance;
		}
	}
}