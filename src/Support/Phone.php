<?php

namespace Codification\Common\Support
{
	use Codification\Common\Enums\PhoneType;
	use Codification\Common\Exceptions\LocaleException;
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
		 * @throws \Codification\Common\Exceptions\LocaleException
		 */
		private function __construct(?string $number, string $country)
		{
			$number  = sanitize($number);
			$country = $this->getCountry($country);

			$this->instance = PhoneNumberUtil::getInstance()->parse($number, $country);
		}

		/**
		 * @param string $country
		 *
		 * @return string
		 * @throws \Codification\Common\Exceptions\LocaleException
		 */
		private function getCountry(string $country) : string
		{
			$country = sanitize($country);

			if ($country === null || !Country::isValid($country))
			{
				throw new LocaleException();
			}

			return strtoupper($country);
		}

		/**
		 * @param string|null $locale
		 *
		 * @return string
		 * @throws \Codification\Common\Exceptions\LocaleException
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
		 * @param string|null                               $country
		 * @param \Codification\Common\Enums\PhoneType|null $type
		 *
		 * @return bool
		 * @throws \Codification\Common\Exceptions\LocaleException
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
		 * @param string|null                               $country
		 * @param \Codification\Common\Enums\PhoneType|null $type
		 *
		 * @return bool
		 * @throws \Codification\Common\Exceptions\LocaleException
		 */
		public static function validate(?string $number, string $country = null, PhoneType $type = null) : bool
		{
			$phone = static::make($number, $country);

			if ($phone === null)
			{
				return false;
			}

			return $phone->isValid($country, $type);
		}

		/**
		 * @param string|null $number
		 * @param string      $country
		 *
		 * @return \Codification\Common\Support\Phone|null
		 * @throws \Codification\Common\Exceptions\LocaleException
		 */
		public static function make(?string $number, string $country) : ?Phone
		{
			try
			{
				return new static($number, $country);
			}
			catch (\libphonenumber\NumberParseException $e)
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
		public function jsonSerialize()
		{
			return $this->format();
		}
	}
}